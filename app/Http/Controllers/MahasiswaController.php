<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Mahasiswa;
use App\Models\Ruangan;
use App\Models\RuanganKetersediaan;
use App\Models\Mou; // Model Universitas
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Auth;
use App\Services\RoomSyncService; //
use App\Models\RoomSequence;
class MahasiswaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Create a User record for a mahasiswa and return the created user object.
     * The returned object will include a `plain_password` attribute (not saved to DB)
     * so admin can see the generated credentials immediately.
     */
    private function createUserForMahasiswa($name, $phone = null)
    {
        // Ensure unique email placeholder
        $base = Str::slug(substr($name, 0, 50)) ?: 'mahasiswa';
        $suffix = Str::random(4);
        $email = $base . '.' . $suffix . '@mahasiswa.local';

        $plain = Str::random(8);
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($plain),
            'role' => 'user',
            'is_approved' => 1,
        ]);

        // Attach plain password temporarily for admin display
        $user->plain_password = $plain;
        return $user;
    }

 
   public function index(Request $request, RoomSyncService $roomSyncService)
    {
        // [1] JALANKAN SYNC OTOMATIS
        $roomSyncService->syncRooms();

        // [2] AMBIL DATA UNTUK DROPDOWN (Ringan, hanya select kolom yang diperlukan)
        $mous = \App\Models\Mou::select('id', 'nama_instansi', 'nama_universitas')->orderBy('nama_instansi')->get();
        $ruangans = \App\Models\Ruangan::select('id', 'nm_ruangan')->orderBy('nm_ruangan')->get();
        
        // Ambil Gelombang & Tahun unik dari tabel orientasi_results
        $gelombangs = \Illuminate\Support\Facades\DB::table('orientasi_results')->whereNotNull('gelombang')->distinct()->orderBy('gelombang')->pluck('gelombang');
        $tahuns = \Illuminate\Support\Facades\DB::table('orientasi_results')->whereNotNull('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun');

        // [3] INISIASI QUERY UTAMA DENGAN EAGER LOADING
        $query = \App\Models\Mahasiswa::with(['mou', 'ruangan', 'user.mou']);

        // --- FILTER ADVANCED ---
        
        // 1. Filter Instansi/MOU
        if ($request->filled('mou_id')) {
            $query->where('mou_id', $request->mou_id);
        }

        // 2. Filter Ruangan
        if ($request->filled('ruangan_id')) {
            $query->where('ruangan_id', $request->ruangan_id);
        }

        // 3. Pencarian Nama
        if ($request->filled('search')) {
            $query->where('nm_mahasiswa', 'like', '%' . $request->search . '%');
        }

        // 4. Filter Rentang Waktu (Berdasarkan Tanggal Mulai Magang)
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tanggal_mulai', [$request->start_date, $request->end_date]);
        } elseif ($request->filled('start_date')) {
            $query->where('tanggal_mulai', '>=', $request->start_date);
        } elseif ($request->filled('end_date')) {
            $query->where('tanggal_mulai', '<=', $request->end_date);
        }

        // 5. Filter Gelombang Orientasi (Subquery agar enteng)
        if ($request->filled('gelombang')) {
            $query->whereIn('user_id', function ($q) use ($request) {
                $q->select('user_id')->from('orientasi_results')->where('gelombang', $request->gelombang);
            });
        }

        // 6. Filter Tahun Orientasi (Subquery agar enteng)
        if ($request->filled('tahun')) {
            $query->whereIn('user_id', function ($q) use ($request) {
                $q->select('user_id')->from('orientasi_results')->where('tahun', $request->tahun);
            });
        }

        // [4] HITUNG STATISTIK MINI DASHBOARD
        // Dihitung berdasarkan filter di atas, SEBELUM filter status diterapkan
        $baseQueryForStats = clone $query;
        $totalAktif = (clone $baseQueryForStats)->where('status', 'aktif')->count();
        $totalNonAktif = (clone $baseQueryForStats)->where('status', 'nonaktif')->count();
        $totalSemua = $totalAktif + $totalNonAktif;

        // [5] FILTER STATUS (Default: Aktif saat pertama kali load)
        $statusFilter = $request->input('status_filter', 'aktif');
        if ($statusFilter !== 'semua') {
            $query->where('status', $statusFilter);
        }

        // [6] EKSEKUSI PAGINATION
        $mahasiswas = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        // Trigger sisa hari
        $mahasiswas->getCollection()->transform(function ($m) {
            $m->sisa_hari; 
            return $m->fresh();
        });

        return view('mahasiswa.index', compact(
            'mahasiswas', 'ruangans', 'mous', 'gelombangs', 'tahuns',
            'totalAktif', 'totalNonAktif', 'totalSemua', 'statusFilter'
        ));
    }

    public function create()
    {
        $ruangans = Ruangan::all();
        $mous = Mou::orderBy('nama_instansi', 'asc')->get(); // Ambil data MOU (kolom baru)
        return view('mahasiswa.create', compact('ruangans', 'mous'));
    }

    public function store(Request $request)
    {
        if ($request->has('data')) {
            return $this->importMahasiswa($request);
        }
        return $this->storeSingleMahasiswa($request);
    }
    
    // ... method lain ...

    // 1. TAMPILKAN HALAMAN EDIT
    public function editRolling($id)
    {
        $mahasiswa = Mahasiswa::with(['roomSequences', 'shiftSchedules'])->findOrFail($id);
        
     $ruangans = \App\Models\Ruangan::with('roomShifts')->orderBy('nm_ruangan')->get();

        return view('mahasiswa.manage_rolling', compact('mahasiswa', 'ruangans'));
    }

    // 2. SIMPAN PERUBAHAN
  // 2. SIMPAN PERUBAHAN
    public function updateRolling(Request $request, $id)
    {
        $mahasiswa = Mahasiswa::findOrFail($id);

        // Validasi
        $request->validate([
            'sequences.*.ruangan_id' => 'required|exists:ruangans,id',
            'sequences.*.start_date' => 'required|date',
            'sequences.*.end_date'   => 'required|date|after_or_equal:sequences.*.start_date',
            'shifts.*.tanggal'       => 'required|date',
            // FIX: Ubah validasi menjadi string agar menerima nama shift custom dari database
            'shifts.*.shift_type'    => 'required|string', 
            'shifts.*.ruangan_id'    => 'required|exists:ruangans,id',
        ]);

        \Illuminate\Support\Facades\DB::transaction(function () use ($mahasiswa, $request) {
            
            // A. UPDATE ROOM SEQUENCES (Jadwal Rolling)
            $mahasiswa->roomSequences()->delete();
            
            if ($request->has('sequences')) {
                foreach ($request->sequences as $seq) {
                    $mahasiswa->roomSequences()->create([
                        'ruangan_id' => $seq['ruangan_id'],
                        'start_date' => $seq['start_date'],
                        'end_date'   => $seq['end_date'],
                    ]);
                }
            }

            // B. UPDATE SHIFT SCHEDULES (Jadwal Harian)
            $mahasiswa->shiftSchedules()->delete();

            if ($request->has('shifts')) {
                foreach ($request->shifts as $shift) {
                    $mahasiswa->shiftSchedules()->create([
                        'tanggal'    => $shift['tanggal'],
                        'shift_type' => $shift['shift_type'],
                        'ruangan_id' => $shift['ruangan_id'],
                    ]);
                }
            }
        });

        return redirect()->route('mahasiswa.show', $mahasiswa->id)
            ->with('success', 'Jadwal Rolling & Shift berhasil diperbarui!');
    }
    
    public function show($id)
    {
        // 1. DATA UTAMA
        $mahasiswa = Mahasiswa::with([
            'mou', 
            'ruangan', 
            'absensis', 
            'roomSequences.ruangan', 
            'roomSequences.ruangan.roomShifts',
            'shiftSchedules'
        ])->findOrFail($id);

        $today = now()->toDateString();
        $tglMulai = $mahasiswa->tanggal_mulai;
        $tglAkhir = $mahasiswa->tanggal_berakhir;
        $statusJadwal = 'Global';

        // Cek Jadwal Rolling Aktif
        $jadwalAktif = $mahasiswa->roomSequences()
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->first();

        if ($jadwalAktif) {
            $tglMulai = $jadwalAktif->start_date;
            $tglAkhir = $jadwalAktif->end_date;
            $statusJadwal = 'Rolling';
        }

// ==========================================================
// BUILD CALENDAR EVENTS (LOGIKA TERPADU: SHIFT & NON-SHIFT)
// ==========================================================
// $events = [];
// // Index shift manual berdasarkan tanggal agar pencarian cepat
// $manualShifts = $mahasiswa->shiftSchedules->keyBy('tanggal');

// // --- LOOP BERDASARKAN ROTASI RUANGAN (SEQUENCE) ---
// foreach ($mahasiswa->roomSequences as $seq) {
//     $period = \Carbon\CarbonPeriod::create($seq->start_date, $seq->end_date);
//     $kategoriRuangan = $seq->ruangan->kategori ?? 'non_shift';
//     $ruangName = $seq->ruangan->nm_ruangan;
    
//     // Flag Deteksi Ruangan Khusus
//     $isGizi = \Illuminate\Support\Str::contains(strtolower($ruangName), 'gizi');
//     $isMerak = \Illuminate\Support\Str::contains(strtolower($ruangName), 'merak');

//     foreach ($period as $dt) {
//         $dStr = $dt->format('Y-m-d');
        
//         // --- A. LOGIKA RUANGAN NON-SHIFT (Reguler & Jumat) ---
//         if ($kategoriRuangan === 'non_shift') {
            
//             // 1. Logika Weekend (Sabtu & Minggu) -> Tampilkan LIBUR
//             if ($dt->isWeekend()) {
//                 $events[] = [
//                     'title' => 'LIBUR',
//                     'start' => $dStr,
//                     'color' => '#6c757d', // Warna Abu-abu
//                     'extendedProps' => [
//                         'jam' => 'Sabtu/Minggu', 
//                         'ruang' => $ruangName, 
//                         'type' => 'jadwal'
//                     ]
//                 ];
//                 continue; // Lanjut ke hari berikutnya
//             }

//                  // 2. Logika Hari Kerja (Senin - Jumat)
//             $isJumat = $dt->dayOfWeekIso == 5;
// // --- A. LOGIKA RUANGAN NON-SHIFT (Reguler & Jumat) ---
// if ($kategoriRuangan === 'non_shift') {
    
//     // 1. Logika Weekend (Sabtu & Minggu) tetap LIBUR
//     if ($dt->isWeekend()) {
//         $events[] = [
//             'title' => 'LIBUR',
//             'start' => $dStr,
//             'color' => '#6c757d',
//             'extendedProps' => [
//                 'jam' => 'Sabtu/Minggu', 
//                 'ruang' => $ruangName, 
//                 'type' => 'jadwal'
//             ]
//         ];
//         continue;
//     }

//     // 2. Logika Jam Kerja Berdasarkan Hari (Senin-Kamis vs Jumat)
//     $dayOfWeek = $dt->dayOfWeekIso; // 1 (Senin) s/d 7 (Minggu)

//     if ($dayOfWeek == 5) {
//         // HARI JUMAT
//         $jam = '07:00 - 14:30';
//         $title = 'Jumat';
//     } else {
//         // SENIN - KAMIS (Reguler)
//         $jam = '07:15 - 15:30'; // Sudah diubah ke 15:00 sesuai permintaan
//         $title = 'Reguler';
//     }
    
//     $events[] = [
//         'title' => $title,
//         'start' => $dStr,
//         'color' => '#6610f2',
//         'extendedProps' => [
//             'jam' => $jam, 
//             'ruang' => $ruangName, 
//             'type' => 'jadwal'
//         ]
//     ];
    
// }

//         } 
        
//         // --- B. LOGIKA RUANGAN SHIFT (Manual Input Admin) ---
//         else {
//             // Hanya tampil jika Admin sudah mengisi di tabel shift_schedules
//             if (isset($manualShifts[$dStr])) {
//                 $shift = $manualShifts[$dStr];
//                 $type = $shift->shift_type;
//                 $jamShift = '';

//                 // Logika Jam Berdasarkan Ruangan & Tipe Shift
//                 if ($isGizi) {
//                     // Jam Khusus Instalasi Gizi
//                     if ($type == 'Pagi') $jamShift = '04:30 - 12:40';
//                     elseif ($type == 'Siang') $jamShift = '11:20 - 19:30';
//                     elseif ($type == 'Reguler') $jamShift = '07:15 - 15:25';
//                     else $jamShift = 'Libur Shift';
//                 } else {
//                     // Jam Standar Ruangan Lain
//                     if ($type == 'Pagi') $jamShift = '07:00 - 14:00';
//                     elseif ($type == 'Siang') $jamShift = '14:00 - 21:00';
//                     elseif ($type == 'Malam') {
//                         $jamShift = $isMerak ? '20:00 - 07:00' : '21:00 - 07:00';
//                     } else $jamShift = 'Libur Shift';
//                 }

//                 $events[] = [
//                     'title' => ucfirst($type),
//                     'start' => $dStr,
//                     'color' => ($type == 'Libur' ? '#6c757d' : '#0d6efd'), // Biru untuk Shift
//                     'extendedProps' => [
//                         'jam' => ($type == 'Libur' ? 'Istirahat' : $jamShift), 
//                         'ruang' => $ruangName, 
//                         'type' => 'jadwal'
//                     ]
//                 ];
//             }
//         }
//     }
// }
// ==========================================================
// BUILD CALENDAR EVENTS (LOGIKA DINAMIS DARI DATABASE)
// ==========================================================
        // Kalender: satu sumber dari model (identik dgn dashboard mahasiswa)
        $events = $mahasiswa->kalenderEvents();

        return view('mahasiswa.show', compact(
            'mahasiswa', 'tglMulai', 'tglAkhir', 'statusJadwal', 'events'
        ));
    }
    
public function resetDevice($id)
{
    // 1. Cari mahasiswanya
    $mahasiswa = \App\Models\Mahasiswa::findOrFail($id);

    // 2. Cari usernya secara manual menggunakan user_id yang ada di tabel mahasiswa
    $user = \App\Models\User::find($mahasiswa->user_id);

    if ($user) {
        // 3. Set device_id jadi null dan simpan
        $user->device_id = null;
        $user->save();

        return back()->with('success', 'Device ID berhasil di-reset!');
    }

    return back()->with('error', 'Akun User tidak ditemukan untuk mahasiswa ini.');
}

public function resetEdit($id)
{
    // 1. Cari mahasiswanya
    $mahasiswa = \App\Models\Mahasiswa::findOrFail($id);

    // 2. Langsung ubah nilai is_edited milik mahasiswa tersebut
    $mahasiswa->is_edited = 0; // Ubah ke 0 (karena 1 berarti sedang diedit)
    
    // 3. Simpan perubahan ke database
    $mahasiswa->save();

    return back()->with('success', 'Akses Edit berhasil di-buka (di-reset)!');
}
    /**
     * Import Excel Logic
     */
    public function importExcel(Request $request)
    {
        // Alias untuk method private di bawah agar konsisten dengan route
        return $this->importMahasiswa($request);
    }

    private function importMahasiswa(Request $request)
    {
        try {
            $rows = json_decode($request->data, true);
            $processed = 0;
            $errors = [];
            $createdCredentials = []; // collect created user credentials for admin

            foreach ($rows as $i => $row) {
                $name = $row['Nama'] ?? $row['nama'] ?? null;
                $univName = $row['Universitas'] ?? null;
                $ruanganName = $row['Ruangan'] ?? null;
                $status = isset($row['Status']) ? strtolower($row['Status']) : 'aktif';
                $tanggalMulai = $row['Tanggal Mulai'] ?? null;
                $tanggalBerakhir = $row['Tanggal Berakhir'] ?? null;

                // Validasi Dasar
                if (empty($name)) {
                    $errors[] = "Baris " . ($i + 2) . ": nama kosong";
                    continue;
                }
                if (empty($tanggalMulai) || empty($tanggalBerakhir)) {
                    $errors[] = "Baris " . ($i + 2) . ": tanggal tidak lengkap";
                    continue;
                }

                // 1. Cari ID Ruangan
                $ruanganId = null;
                $nmRuangan = null;
                if ($ruanganName) {
                    $ruanganDb = Ruangan::where('nm_ruangan', 'like', '%' . $ruanganName . '%')->first();
                    if ($ruanganDb) {
                        $ruanganId = $ruanganDb->id;
                        $nmRuangan = $ruanganDb->nm_ruangan;
                    } else {
                        $nmRuangan = $ruanganName; // Simpan string jika tidak ketemu (opsional)
                    }
                }

                // 2. Cari ID MOU (Universitas)
                $mouId = null;
                if ($univName) {
                    $mouDb = Mou::where('nama_instansi', 'like', '%' . $univName . '%')
                        ->orWhere('nama_universitas', 'like', '%' . $univName . '%')
                        ->first();
                    if ($mouDb) {
                        $mouId = $mouDb->id;
                    }
                }

                // Data Preparation
                $dataToSave = [
                    'nm_mahasiswa' => $name,
                    'mou_id' => $mouId, // Pakai ID Relasi
                    'prodi' => $row['Prodi'] ?? null,
                    'no_hp' => $row['No HP'] ?? $row['no_hp'] ?? null,
                    'tanggal_mulai' => $tanggalMulai,
                    'tanggal_berakhir' => $tanggalBerakhir,
                    'status' => in_array($status, ['aktif', 'nonaktif']) ? $status : 'aktif',
                    'ruangan_id' => $ruanganId,
                    'nm_ruangan' => $nmRuangan,
                ];

                // 3. Cek Data Lama (Untuk Update) atau Buat Baru
                // Kita cari berdasarkan nama & mou_id agar spesifik
            // 3. Cek Data Lama (Untuk Update) atau Buat Baru
                // Kita cari berdasarkan nama & mou_id agar spesifik
                $mahasiswa = Mahasiswa::where('nm_mahasiswa', $name)
                    ->when($mouId, function ($q) use ($mouId) {
                        return $q->where('mou_id', $mouId);
                    })
                    ->where('status', 'aktif') // KUNCI PERBAIKAN: Hanya timpa data jika status magangnya MASIH AKTIF
                    ->first();
                // Jika statusnya sudah nonaktif (lulus), sistem akan otomatis menganggapnya mahasiswa baru dan membuatkan row/periode baru!

                $today = now()->toDateString();

                if ($mahasiswa) {
                    // === UPDATE LOGIC (Cek Pindah Ruangan) ===
                    $oldRuanganId = $mahasiswa->ruangan_id;

                    if ($ruanganId && $ruanganId != $oldRuanganId) {
                        // Kembalikan kuota ruangan lama
                        if ($oldRuanganId) {
                            $oldSnap = RuanganKetersediaan::firstOrCreate(
                                ['ruangan_id' => $oldRuanganId, 'tanggal' => $today],
                                ['tersedia' => 0]
                            );
                            $oldSnap->increment('tersedia');
                        }

                        // Kurangi kuota ruangan baru
                        $newRuangan = Ruangan::find($ruanganId);
                        $terisi = Mahasiswa::where('ruangan_id', $ruanganId)->count();
                        $tersedia = $newRuangan->kuota_ruangan - $terisi;

                        if ($tersedia <= 0) {
                            $errors[] = "Baris " . ($i + 2) . ": Update gagal, ruangan $ruanganName penuh.";
                            continue;
                        }

                        $newSnap = RuanganKetersediaan::firstOrCreate(
                            ['ruangan_id' => $ruanganId, 'tanggal' => $today],
                            ['tersedia' => $tersedia]
                        );

                        if (!$newSnap->wasRecentlyCreated) {
                            $newSnap->decrement('tersedia');
                        }
                    }
                    $mahasiswa->update($dataToSave);
                } else {
                    // === CREATE LOGIC ===
                    if ($ruanganId) {
                        $ruangan = Ruangan::find($ruanganId);
                        $terisi = Mahasiswa::where('ruangan_id', $ruanganId)->count();
                        $tersedia = $ruangan->kuota_ruangan - $terisi;

                        if ($tersedia <= 0) {
                            $errors[] = "Baris " . ($i + 2) . ": Ruangan $ruanganName penuh.";
                            continue;
                        }

                        $snapshot = RuanganKetersediaan::firstOrCreate(
                            ['ruangan_id' => $ruanganId, 'tanggal' => $today],
                            ['tersedia' => $tersedia]
                        );

                        if (!$snapshot->wasRecentlyCreated) {
                            $snapshot->decrement('tersedia');
                        }
                    }
                    // Create a User account for the mahasiswa when importing
                    $createdUser = $this->createUserForMahasiswa($name, $row['No HP'] ?? $row['no_hp'] ?? null);
                    $dataToSave['user_id'] = $createdUser->id;
                    Mahasiswa::create($dataToSave);

                    // Record credentials to return to admin
                    $createdCredentials[] = [
                        'nama' => $createdUser->name,
                        'email' => $createdUser->email,
                        'password' => $createdUser->plain_password,
                    ];
                }
                $processed++;
            }

            return response()->json([
                'success' => true,
                'message' => "Berhasil memproses $processed data.",
                'errors' => $errors,
                'created' => $createdCredentials,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
/**
     * Helper: Pesan Validasi Bahasa Indonesia
     */
    private function getValidationMessages()
    {
        return [
            // --- 1. SOLUSI UTAMA (Target Spesifik) ---
            // Ini akan menimpa error 'validation.max.file' khusus untuk field foto
            'foto.max'   => 'Ukuran Pas Foto terlalu besar. Maksimal 2MB (2048 KB).',
            'foto.image' => 'File yang diupload harus berupa gambar.',
            'foto.mimes' => 'Format foto harus: jpg, jpeg, atau png.',

            // --- 2. Pesan Error Umum ---
            'required'       => ':attribute wajib diisi.',
            'string'         => ':attribute harus berupa teks.',
            'date'           => 'Format tanggal tidak valid.',
            'numeric'        => ':attribute harus berupa angka.',
            'exists'         => 'Data :attribute tidak ditemukan di sistem.',
            'in'             => 'Pilihan :attribute tidak valid.',
            
            // Error Tanggal
            'after'          => 'Tanggal selesai harus setelah tanggal mulai.',
            'after_or_equal' => 'Tanggal selesai harus setelah atau sama dengan tanggal mulai.',

            // --- 3. Ganti Nama Kolom Jadi Enak Dibaca ---
            'attributes' => [
                'nm_mahasiswa'     => 'Nama Lengkap',
                'tipe_mahasiswa'   => 'Tipe Mahasiswa',
                'no_hp'            => 'Nomor WhatsApp',
                'mou_id'           => 'Instansi / Universitas',
                'prodi'            => 'Program Studi',
                'tanggal_mulai'    => 'Tanggal Mulai',
                'tanggal_berakhir' => 'Tanggal Berakhir',
                'foto'             => 'Pas Foto',
                'ruangan_id'       => 'Ruangan',
                'weekend_aktif'    => 'Opsi Weekend',
            ],
        ];
    }

private function storeSingleMahasiswa(Request $request)
{
    $user = auth()->user();

    // =====================================================================
    // 1. PENCEGAHAN DUPLIKAT UNTUK USER BIASA (MAHASISWA)
    // =====================================================================
    if ($user && $user->role !== 'admin') {
        $mahasiswaLama = Mahasiswa::where('user_id', $user->id)->latest()->first();
        $pengajuanTerbaru = \App\Models\Pengajuan::where('user_id', $user->id)
                                ->where('jenis', 'magang')
                                ->latest()
                                ->first();

        // Jika user punya data magang yang masih "aktif" di database
        if ($mahasiswaLama && $mahasiswaLama->status === 'aktif') {
            
            // Pengecekan: Apakah user punya pengajuan magang BARU yang sudah di-approve admin?
            $isPengajuanBaru = ($pengajuanTerbaru && 
                                $pengajuanTerbaru->status === 'approved' && 
                                $mahasiswaLama->created_at < $pengajuanTerbaru->created_at);

            if ($isPengajuanBaru) {
                // Jika YA (Berhak bikin baru), matikan status mahasiswa lama agar tidak bentrok
                $mahasiswaLama->update(['status' => 'nonaktif']);
            } else {
                // Jika BUKAN dari pengajuan baru, blokir dan lempar kembali ke dashboard
                return redirect()->route('dashboard')
                    ->withErrors(['Akses ditolak: Anda masih memiliki periode magang yang sedang berjalan.']);
            }
        }
    }

    // =====================================================================
    // 2. VALIDASI INPUT
    // =====================================================================
    $data = $request->validate([
        'nm_mahasiswa'     => 'required|string|max:255',
        'email'            => 'nullable|email|unique:users,email', 
        'prodi'            => 'nullable|string|max:255',
        'mou_id'           => 'required|exists:mous,id',
        'tanggal_mulai'    => 'required|date',
        'tanggal_berakhir' => 'required|date|after_or_equal:tanggal_mulai',
        'weekend_aktif'    => 'nullable|boolean',
        'tipe_mahasiswa'   => 'required|in:magang,pkl',
        'no_hp'            => 'nullable|string|max:20',
        'ruangan_id'       => 'nullable|exists:ruangans,id',
        'foto'             => 'nullable|image|mimes:jpeg,png,jpg|max:2048', 
        'kompetensi'       => 'nullable|array',
        'kompetensi.*'     => 'nullable|string|max:255',
    ], $this->getValidationMessages());

    // --- LOGIKA KOMPETENSI ---
    if ($request->has('kompetensi')) {
        $kompetensiBersih = array_filter($request->kompetensi);
        $data['kompetensi_json'] = array_values($kompetensiBersih);
    } else {
        $data['kompetensi_json'] = [];
    }
    unset($data['kompetensi']);

    // =====================================================================
    // 3. LOGIC USER & PEMBUATAN AKUN (ADMIN)
    // =====================================================================
    if ($user && $user->role === 'admin') {
        $createdUser = $this->createUserForMahasiswa(
            $data['nm_mahasiswa'], 
            $data['no_hp'],
            $request->email
        );
        $data['user_id'] = $createdUser->id;
        
        session()->flash('created_mahasiswa_credentials', [
            'name' => $createdUser->name, 
            'email' => $createdUser->email, 
            'password' => $createdUser->plain_password,
        ]);
    } else {
        $data['user_id'] = $user->id;
    }

    // --- UPLOAD FOTO ---
    if ($request->hasFile('foto')) {
        $file = $request->file('foto');
        $nama_file = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
        $file->move(public_path('uploads/pas_foto'), $nama_file);
        $data['foto_path'] = 'uploads/pas_foto/' . $nama_file;
    }

    // PASTIKAN UNSET FOTO TETAP ADA DI SINI
    unset($data['foto']);

    // --- LOGIC KUOTA RUANGAN ---
    if (!empty($data['ruangan_id'])) {
        $ruangan = Ruangan::find($data['ruangan_id']);
        if ($ruangan) {
            $terisi = Mahasiswa::where('ruangan_id', $ruangan->id)->where('status', 'aktif')->count();
            $tersedia = $ruangan->kuota_ruangan - $terisi;

            if ($tersedia <= 0) {
                return back()->withErrors(['ruangan_id' => 'Mohon maaf, kuota ruangan penuh.'])->withInput();
            }

            $today = now()->toDateString();
            $snapshot = RuanganKetersediaan::firstOrCreate(
                ['ruangan_id' => $ruangan->id, 'tanggal' => $today],
                ['tersedia' => $ruangan->kuota_ruangan]
            );
            
            if (!$snapshot->wasRecentlyCreated) {
                $snapshot->decrement('tersedia');
            } else {
                $snapshot->update(['tersedia' => max(0, $tersedia - 1)]);
            }

            $data['nm_ruangan'] = $ruangan->nm_ruangan;
        }
    } else {
        $data['ruangan_id'] = null;
        $data['nm_ruangan'] = null;
    }
    
    unset($data['email']);

    // --- CREATE DATA MAHASISWA ---
    Mahasiswa::create($data);

    if ($user->role === 'admin') {
        return redirect()->route('mahasiswa.index')->with('success', 'Mahasiswa berhasil dibuat!');
    }

    return redirect()->route('mahasiswa.dashboard')->with('success', 'Biodata berhasil disimpan!');
}
    public function edit($id)
    {
        $mahasiswa = Mahasiswa::findOrFail($id);
        $ruangans = Ruangan::all();
        $mous = Mou::orderBy('nama_instansi', 'asc')->get(); // Data untuk dropdown (kolom baru)
        return view('mahasiswa.edit', compact('mahasiswa', 'ruangans', 'mous'));
    }

// =========================================================================
    // 2. UPDATE (PROSES SIMPAN, LOGIC RUANGAN, & KUNCI AKUN)
    // =========================================================================
    public function update(Request $request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            // A. AMBIL DATA & CEK SECURITY
            $mahasiswa = Mahasiswa::findOrFail($id);
            $user = Auth::user();
            $account = $mahasiswa->user; // Ambil data user terkait

            // [SECURITY CHECK] 1x Edit Limit untuk User Biasa
            if ($user->role !== 'admin' && $mahasiswa->is_edited == 1) {
                return redirect()->route('dashboard')
                    ->with('error', 'Akses Ditolak: Profil Anda sudah terkunci (Hanya bisa edit 1 kali).');
            }

            // B. VALIDASI INPUT
            $data = $request->validate([
                'nm_mahasiswa'     => 'required|string|max:255',
                'prodi'            => 'nullable|string|max:255',
                'email'            => 'nullable|email|unique:users,email,' . ($account ? $account->id : 'NULL'),
                'mou_id'           => 'nullable|exists:mous,id',
                'tanggal_mulai'    => 'nullable|date',
                'tanggal_berakhir' => 'nullable|date|after_or_equal:tanggal_mulai',
                'weekend_aktif'    => 'nullable|boolean',
                'no_hp'            => 'nullable|string|max:20',
                'status'           => 'nullable|in:aktif,nonaktif', 
                'ruangan_id'       => 'nullable|exists:ruangans,id',
                'foto'             => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'kompetensi'       => 'nullable|array',
                'kompetensi.*'     => 'nullable|string|max:255',
            ], $this->getValidationMessages());

            // Pertahankan nilai lama jika input tidak terkirim dari form (misal field di-disable)
            $data['status'] = $data['status'] ?? $mahasiswa->status;
            $data['tanggal_mulai'] = $data['tanggal_mulai'] ?? $mahasiswa->tanggal_mulai;
            $data['tanggal_berakhir'] = $data['tanggal_berakhir'] ?? $mahasiswa->tanggal_berakhir;

            // C. FORMATTING DATA
            $data['nm_mahasiswa'] = strtoupper($data['nm_mahasiswa']);
            if (!empty($data['prodi'])) {
                $data['prodi'] = strtoupper($data['prodi']);
            }
            $data['weekend_aktif'] = $request->boolean('weekend_aktif');

            // UPDATE DATA USER (Email & Nama)
            if ($account) {
                $account->update([
                    'email' => $data['email'] ?? $account->email,
                    'name'  => $data['nm_mahasiswa'],
                ]);
            }
            unset($data['email']); // Hapus email dari array data tabel mahasiswa

            // D. HANDLE UPLOAD FOTO
            if ($request->hasFile('foto')) {
                if ($mahasiswa->foto_path && File::exists(public_path($mahasiswa->foto_path))) {
                    File::delete(public_path($mahasiswa->foto_path));
                }
                $file = $request->file('foto');
                $nama_file = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
                $file->move(public_path('uploads/pas_foto'), $nama_file);
                $data['foto_path'] = 'uploads/pas_foto/' . $nama_file;
            }
            
            // Hapus atribut foto objek agar tidak error saat update query DB
            unset($data['foto']);

            // E. LOGIC MANAJEMEN RUANGAN (KUOTA)
            $oldRuanganId = $mahasiswa->ruangan_id;
            $newRuanganId = $data['ruangan_id'] ?? null;
            $today = now()->toDateString();

            // SKENARIO 1: Mahasiswa dinonaktifkan (Keluar dari ruangan)
            if ($mahasiswa->status === 'aktif' && $data['status'] === 'nonaktif' && $oldRuanganId) {
                $oldRoom = Ruangan::find($oldRuanganId);
                if ($oldRoom) {
                    $snap = RuanganKetersediaan::firstOrCreate(
                        ['ruangan_id' => $oldRoom->id, 'tanggal' => $today],
                        ['tersedia' => $oldRoom->kuota_ruangan]
                    );
                    $snap->increment('tersedia');
                }
                $data['ruangan_id'] = null;
                $data['nm_ruangan'] = null;
            }
            // SKENARIO 2: Pindah Ruangan
            elseif ($newRuanganId && $newRuanganId != $oldRuanganId) {
                // Kembalikan kuota ruangan lama
                if ($oldRuanganId) {
                    $oldRoom = Ruangan::find($oldRuanganId);
                    if ($oldRoom) {
                        $snapOld = RuanganKetersediaan::firstOrCreate(
                            ['ruangan_id' => $oldRoom->id, 'tanggal' => $today],
                            ['tersedia' => $oldRoom->kuota_ruangan]
                        );
                        $snapOld->increment('tersedia');
                    }
                }

                // Cek ketersediaan kuota ruangan baru
                $newRoom = Ruangan::findOrFail($newRuanganId);
                $terisi = Mahasiswa::where('ruangan_id', $newRoom->id)->where('status', 'aktif')->count();
                $tersedia = $newRoom->kuota_ruangan - $terisi;

                if ($tersedia <= 0) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'ruangan_id' => "Ruangan {$newRoom->nm_ruangan} penuh."
                    ]);
                }

                // Kurangi kuota ruangan baru
                $snapNew = RuanganKetersediaan::firstOrCreate(
                    ['ruangan_id' => $newRoom->id, 'tanggal' => $today],
                    ['tersedia' => $tersedia]
                );
                if (!$snapNew->wasRecentlyCreated) {
                    $snapNew->decrement('tersedia');
                } else {
                    $snapNew->update(['tersedia' => $tersedia - 1]);
                }

                $data['nm_ruangan'] = $newRoom->nm_ruangan;
            }
            // SKENARIO 3: Batal Pilih Ruangan (Dikosongkan)
            elseif (!$newRuanganId && $oldRuanganId) {
                $oldRoom = Ruangan::find($oldRuanganId);
                if ($oldRoom) {
                    $snap = RuanganKetersediaan::firstOrCreate(
                        ['ruangan_id' => $oldRoom->id, 'tanggal' => $today],
                        ['tersedia' => $oldRoom->kuota_ruangan]
                    );
                    $snap->increment('tersedia');
                }
                $data['nm_ruangan'] = null;
            }

            // HANDLE KOMPETENSI
            if ($request->has('kompetensi')) {
                $kompetensiBersih = array_filter($request->kompetensi);
                $data['kompetensi_json'] = array_values($kompetensiBersih);
            } else {
                $data['kompetensi_json'] = [];
            }
            unset($data['kompetensi']);

            // F. UPDATE DATA UTAMA
            $mahasiswa->update($data);

            // G. KUNCI AKUN (Hanya untuk Mahasiswa / Non-Admin)
            if ($user->role !== 'admin') {
                $mahasiswa->is_edited = 1;
                $mahasiswa->save();
            }

            // H. REDIRECT
            if ($user->role === 'admin') {
                return redirect()->route('mahasiswa.index')->with('success', 'Data diperbarui.');
            } else {
                return redirect()->route('mahasiswa.dashboard')->with('success', 'Profil berhasil disimpan. Data Anda kini TERKUNCI.');
            }
        });
    }
    public function destroy($id)
    {
        return DB::transaction(function () use ($id) {
            $mhs = Mahasiswa::findOrFail($id);

            // Hapus file foto fisik
            if ($mhs->foto_path && File::exists(public_path($mhs->foto_path))) {
                File::delete(public_path($mhs->foto_path));
            }

            // Kembalikan kuota ruangan
            if ($mhs->ruangan_id) {
                $ruangan = Ruangan::find($mhs->ruangan_id);
                if ($ruangan) {
                    $snap = RuanganKetersediaan::where('ruangan_id', $ruangan->id)
                        ->where('tanggal', now()->toDateString())
                        ->first();
                    if ($snap) $snap->increment('tersedia');
                }
            }

            $mhs->delete();
            return redirect()->route('mahasiswa.index')->with('success', 'Mahasiswa dihapus.');
        });
    }

    public function getRuanganInfo($id)
    {
        $ruangan = Ruangan::find($id);
        if (!$ruangan) return response()->json(['error' => 'Not Found'], 404);

        $terisi = Mahasiswa::where('ruangan_id', $ruangan->id)->count();
        $tersedia = max(0, $ruangan->kuota_ruangan - $terisi);

        return response()->json([
            'nm_ruangan' => $ruangan->nm_ruangan,
            'kuota_total' => $ruangan->kuota_ruangan,
            'tersedia' => $tersedia,
            'terisi' => $terisi,
        ]);
    }

    public function searchUniversitas(Request $request)
    {
        $search = $request->query('q', '');
        // Cari di tabel mous
        $universitas = Mou::where('nama_instansi', 'like', '%' . $search . '%')
            ->orWhere('nama_universitas', 'like', '%' . $search . '%')
            ->limit(10)
            ->get()
            ->map(function($m){ return $m->nama_instansi ?? $m->nama_universitas; });

        return response()->json($universitas);
    }

    public function copyLinks(Request $request)
    {
        $query = Mahasiswa::with('mou'); // Eager load

        // Filter relasi MOU
        if ($request->has('univ_asal') && !empty($request->univ_asal)) {
            $query->whereHas('mou', function ($q) use ($request) {
                $q->where('nama_instansi', 'like', '%' . $request->univ_asal . '%')
                  ->orWhere('nama_universitas', 'like', '%' . $request->univ_asal . '%');
            });
        }
        if ($request->has('ruangan_id') && !empty($request->ruangan_id)) {
            $query->where('ruangan_id', $request->ruangan_id);
        }
        if ($request->has('search') && !empty($request->search)) {
            $query->where('nm_mahasiswa', 'like', '%' . $request->search . '%');
        }

        $rows = $query->orderBy('created_at', 'desc')->get();

        $data = $rows->map(function ($m) {
            return [
                'nama' => $m->nm_mahasiswa,
                'link' => route('absensi.card', $m->share_token)
            ];
        });

        return response()->json($data);
    }

    // --- Sertifikat Logic ---
 public function showSertifikatSummary($id)
    {
        // 1. Ambil Data Lengkap (Termasuk relasi ke User dan Dispensasi)
        $mahasiswa = Mahasiswa::with([
            'absensis', 
            'shiftSchedules', 
            'roomSequences.ruangan', 
            'roomSequences.ruangan.roomShifts',
            'dispensasis',
            'user'
        ])->findOrFail($id);

        // 2. Data Orientasi (per periode/mahasiswa_id, bukan user_id -> orientasi reset tiap pengajuan)
        $orientasi = \App\Models\OrientasiResult::where('mahasiswa_id', $mahasiswa->id)->first();

        // 3. Setup Variabel Statistik Global
        $totalExpectedDays = 0; // Total Hari Kerja Keseluruhan
        $totalActualDays = 0;   // Masuk
        $totalIzin = 0;         // Dispen
        $totalAlpaDays = 0;     // Alfa

        if ($mahasiswa->tanggal_mulai && $mahasiswa->tanggal_berakhir) {
            $period = \Carbon\CarbonPeriod::create($mahasiswa->tanggal_mulai, $mahasiswa->tanggal_berakhir);
            
            // --- Indexing Shift ---
            $shiftMap = [];
            foreach ($mahasiswa->shiftSchedules as $shift) {
                $shiftMap[\Carbon\Carbon::parse($shift->tanggal)->format('Y-m-d')] = $shift->shift_type;
            }

            // --- Indexing Absen ---
            $absenIndexed = [];
            foreach($mahasiswa->absensis as $a) {
                if ($a->created_at && $a->type === 'masuk') {
                    $absenIndexed[\Carbon\Carbon::parse($a->created_at)->format('Y-m-d')] = $a; 
                }
            }

            // --- Indexing Dispen ---
            $dispenIndexed = [];
            foreach($mahasiswa->dispensasis->where('status', 'approved') as $d) {
                if ($d->tanggal_mulai && $d->tanggal_selesai) {
                    $dPeriod = \Carbon\CarbonPeriod::create($d->tanggal_mulai, $d->tanggal_selesai);
                    foreach($dPeriod as $dt) {
                        $dispenIndexed[$dt->format('Y-m-d')] = true;
                    }
                }
            }

            $today = now()->startOfDay();

            foreach ($period as $dateLoop) {
                $dateStr = $dateLoop->format('Y-m-d');
                
                // CEK STATUS LIBUR (Reguler / Shift)
                $shiftType = $shiftMap[$dateStr] ?? 'Reguler';
                $isHoliday = false;

                if (strtolower($shiftType) === 'libur') {
                    $isHoliday = true;
                } elseif ($shiftType === 'Reguler' && $dateLoop->isWeekend()) {
                    if (!$mahasiswa->weekend_aktif) {
                        $isHoliday = true;
                    }
                }

                // Tambah ke Target Total Keseluruhan (Jika Bukan Libur)
                if (!$isHoliday) {
                    $totalExpectedDays++;
                }

                $isDispen = isset($dispenIndexed[$dateStr]);
                $absen = $absenIndexed[$dateStr] ?? null;

                // Hitung performa AKTUAL HANYA untuk hari ini ke belakang
                if ($dateLoop->lte($today)) {
                    if ($absen) {
                        $totalActualDays++;
                    } elseif ($isHoliday) {
                        // Libur -> tidak masuk hitungan Alfa
                    } elseif ($isDispen) {
                        $totalIzin++;
                    } else {
                        $totalAlpaDays++;
                    }
                }
            }
        }
// ... (Kode sebelumnya) ...
        $instansiName = $mahasiswa->mou ? ($mahasiswa->mou->nama_instansi ?? $mahasiswa->mou->nama_universitas) : 'Umum';
        
        // 4. Persentase — SATU SUMBER dari model (bobot terlambat 90%, hari ini bukan alpha)
        $participationRate = round($mahasiswa->absensi_percentage);
        $participationRate = $participationRate > 100 ? 100 : $participationRate;

        // 5. Siapkan Data Nilai Ruangan
        $nilaiRuangan = is_string($mahasiswa->nilai_ruangan_json) 
            ? json_decode($mahasiswa->nilai_ruangan_json, true) 
            : ($mahasiswa->nilai_ruangan_json ?? []);

        return view('mahasiswa.sertifikat', compact(
            'mahasiswa',
            'totalExpectedDays',
            'totalActualDays',
            'totalIzin',
            'totalAlpaDays',
            'participationRate',
            'orientasi',
            'instansiName',
            'nilaiRuangan' // <-- Tambahkan variabel ini
        ));
    }
    public function updateNilaiRuangan(Request $request, $id)
    {
        $request->validate([
            'ruangan_id' => 'required',
            'nilai' => 'nullable|numeric|min:0|max:100',
        ]);

        $mahasiswa = Mahasiswa::findOrFail($id);
        
        // Parsing JSON lama
        $nilaiJson = is_string($mahasiswa->nilai_ruangan_json) 
            ? json_decode($mahasiswa->nilai_ruangan_json, true) 
            : ($mahasiswa->nilai_ruangan_json ?? []);

        // Cek apakah ini aksi RESET
        if ($request->has('reset_nilai') && $request->reset_nilai == '1') {
            unset($nilaiJson[$request->ruangan_id]);
            $pesan = 'Nilai berhasil di-reset. Mahasiswa kini muncul kembali di dashboard ruangan.';
        } else {
            // Aksi SIMPAN / EDIT
            $nilaiJson[$request->ruangan_id] = $request->nilai;
            $pesan = 'Nilai ruangan berhasil diperbarui.';
        }

        // Simpan kembali ke database
        $mahasiswa->nilai_ruangan_json = $nilaiJson;
        $mahasiswa->save();

        return back()->with('success', $pesan);
    }
public function exportData(Request $request)
{
    // Gunakan query yang sama dengan index tapi tanpa paginate()
    $query = Mahasiswa::with(['user.mou', 'mou', 'ruangan']);

    // Terapkan Filter yang sama
    if ($request->search) {
        $query->where('nm_mahasiswa', 'like', '%' . $request->search . '%');
    }
    if ($request->univ_asal) {
        $query->where('univ_asal', $request->univ_asal);
    }
    if ($request->ruangan_id) {
        $query->where('ruangan_id', $request->ruangan_id);
    }

    $mahasiswas = $query->get();

    // Transformasi data agar sesuai dengan tampilan kolom tabel
    $data = $mahasiswas->map(function ($m) {
        // Logika Universitas (Sesuai kolom tabel)
        $registeredMou = ($m->user && $m->user->mou) ? $m->user->mou : null;
        $mahasiswaMou = $m->mou ?? null;
        $displayMou = $registeredMou ?? $mahasiswaMou;
        $namaUniv = $displayMou ? ($displayMou->nama_instansi ?? $displayMou->nama_universitas ?? '-') : '-';

        return [
            'nama' => $m->nm_mahasiswa,
            'universitas' => $namaUniv,
            'prodi' => $m->prodi ?? '-',
            'ruangan' => $m->ruangan ? $m->ruangan->nm_ruangan : '-',
            'mulai' => $m->tanggal_mulai ?? '-',
            'berakhir' => $m->tanggal_berakhir ?? '-',
            'status' => $m->status,
        ];
    });

    return response()->json($data);
}
    private function calculateExpectedDays($mahasiswa)
    {
        $days = 0;
        if ($mahasiswa->tanggal_mulai && $mahasiswa->tanggal_berakhir) {
            $period = CarbonPeriod::create($mahasiswa->tanggal_mulai, $mahasiswa->tanggal_berakhir);
            foreach ($period as $date) {
                if ($mahasiswa->weekend_aktif || !$date->isWeekend()) {
                    $days++;
                }
            }
        }
        return $days;
    }


public function dashboard()
    {
        // 1. AMBIL DATA MAHASISWA & RELASI
      // Tambahkan latest() agar dashboard selalu menampilkan periode magang yang terbaru
        $mahasiswa = Mahasiswa::where('user_id', auth()->id())
            ->with(['mou', 'ruangan', 'absensis', 'roomSequences.ruangan', 'shiftSchedules'])
            ->latest()
            ->firstOrFail();

        // 2. SETUP TANGGAL & JADWAL ROLLING
        $today = now()->toDateString();
        $tglMulai = $mahasiswa->tanggal_mulai;
        $tglAkhir = $mahasiswa->tanggal_berakhir;
        $jadwalRolling = null;

        // Cek Jadwal Rolling Aktif
        $jadwalAktif = $mahasiswa->roomSequences()
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->first();

        if ($jadwalAktif) {
            $tglMulai = $jadwalAktif->start_date;
            $tglAkhir = $jadwalAktif->end_date;
            $jadwalRolling = $jadwalAktif;
        }

        $startStr = \Carbon\Carbon::parse($tglMulai)->format('Y-m-d');
        $endStr   = \Carbon\Carbon::parse($tglAkhir)->format('Y-m-d');

        // 3. SIAPKAN DATA PENDUKUNG (SHIFT & IZIN)
        $shiftMap = $mahasiswa->shiftSchedules->pluck('shift_type', 'tanggal')->toArray();
        
        // Ambil Data Dispensasi (Approved) untuk perhitungan statistik
        $dispensasis = \App\Models\Dispensasi::where('mahasiswa_id', $mahasiswa->id)
            ->where('status', 'approved')->get();
            
        $izinDates = [];
        foreach ($dispensasis as $d) {
            $period = \Carbon\CarbonPeriod::create($d->tanggal_mulai, $d->tanggal_selesai);
            foreach ($period as $dt) {
                $izinDates[] = $dt->format('Y-m-d');
            }
        }

        // 4. HITUNG TARGET & REALISASI (LOOPING HARI)
        $targetTotal = 0;
        $targetBerjalan = 0;
        $calcEnd = ($today < $endStr) ? $today : $endStr;

        // A. Hitung Target Total
        $periodTotal = \Carbon\CarbonPeriod::create($startStr, $endStr);
        foreach ($periodTotal as $dt) {
            $dStr = $dt->format('Y-m-d');
            $shiftType = $shiftMap[$dStr] ?? null;
            if ($shiftType === 'Libur') continue;
            if (!$shiftType && !$mahasiswa->weekend_aktif && $dt->isWeekend()) continue;
            $targetTotal++;
        }

        // B. Hitung Target Berjalan (Sampai Hari Ini)
        if ($today >= $startStr) {
            $periodRunning = \Carbon\CarbonPeriod::create($startStr, $calcEnd);
            foreach ($periodRunning as $dt) {
                $dStr = $dt->format('Y-m-d');
                $shiftType = $shiftMap[$dStr] ?? null;
                if ($shiftType === 'Libur') continue;
                if (!$shiftType && !$mahasiswa->weekend_aktif && $dt->isWeekend()) continue;
                $targetBerjalan++;
            }
        }

        // C. Hitung Total Hadir (Fisik)
        $absensiMasuk = $mahasiswa->absensis->where('type', 'masuk');
        $totalHadirFisik = 0;
        $checkedDates = [];
        
        foreach ($absensiMasuk as $abs) {
            $tglAbsen = \Carbon\Carbon::parse($abs->created_at)->format('Y-m-d');
            if ($tglAbsen >= $startStr && $tglAbsen <= $endStr) {
                if (!in_array($tglAbsen, $checkedDates)) {
                    $totalHadirFisik++;
                    $checkedDates[] = $tglAbsen;
                }
            }
        }

        // D. Hitung Total Izin (Yang Valid di Hari Kerja)
        // Kita harus memastikan izinnya jatuh di hari kerja, bukan hari libur/minggu
        $totalIzinValid = 0;
        foreach (array_unique($izinDates) as $tglIzin) {
            if ($tglIzin >= $startStr && $tglIzin <= $calcEnd) {
                // Cek apakah ini hari libur?
                $shiftType = $shiftMap[$tglIzin] ?? null;
                $dtIzin = \Carbon\Carbon::parse($tglIzin);
                
                // Jika Libur Shift / Weekend Non-Shift, jangan dihitung sebagai kredit kehadiran
                if ($shiftType === 'Libur') continue;
                if (!$shiftType && !$mahasiswa->weekend_aktif && $dtIzin->isWeekend()) continue;
                
                // Jika sudah absen fisik, jangan double count
                if (in_array($tglIzin, $checkedDates)) continue;

                $totalIzinValid++;
            }
        }

        // 5. FINALISASI STATISTIK (LOGIC UTAMA)
        // Total Hadir (Chart) = Hadir Fisik + Izin Valid
        $totalHadirChart = $totalHadirFisik + $totalIzinValid; 
        
        // Alpha = Target Berjalan - (Hadir + Izin)
        $alpha = max(0, $targetBerjalan - $totalHadirChart);
        
        // Sisa Hari Magang
        $chartSisa = max(0, $targetTotal - $targetBerjalan);

        // Persentase
        $persentase = ($targetBerjalan > 0) ? round(($totalHadirChart / $targetBerjalan) * 100, 0) : 0;
        if ($today < $startStr && $totalHadirChart > 0) $persentase = 100;
        if ($persentase > 100) $persentase = 100;

        // ==========================================================
        // SATU SUMBER: timpa statistik dengan hasil model agar identik
        // di semua dashboard/sertifikat (bobot terlambat 90%, hari ini bukan alpha)
        // ==========================================================
        $stM = $mahasiswa->statistik;
        $targetTotal     = $stM->target_total;
        $targetBerjalan  = $stM->target_sekarang;
        $totalHadirFisik = $stM->hadir_fisik;
        $totalIzinValid  = $stM->dispensasi_biasa + $stM->dispensasi_terlambat;
        $totalHadirChart = $totalHadirFisik + $totalIzinValid;
        $alpha           = $stM->alpha;
        $lupaPulang      = $stM->lupa_pulang ?? 0;
        $chartSisa       = $stM->sisa_kerja;
        $persentase      = round($mahasiswa->absensi_percentage);

        // 6. DATA RIWAYAT
        $absensi = \App\Models\Absensi::where('mahasiswa_id', $mahasiswa->id)
            ->orderBy('created_at', 'desc')->limit(5)->get();

        $riwayatLengkap = \App\Models\Absensi::where('mahasiswa_id', $mahasiswa->id)
            ->where('type', 'keluar')
            ->orderBy('created_at', 'desc')->take(2)->get();

        // ==========================================================
        // 7. BUILD CALENDAR EVENTS
        // ==========================================================
      
// --- 1. INISIALISASI ---
// ==========================================================
// BUILD CALENDAR EVENTS (LOGIKA TERPADU: SHIFT & NON-SHIFT)
// ==========================================================
// $events = [];
// // Index shift manual berdasarkan tanggal agar pencarian cepat
// $manualShifts = $mahasiswa->shiftSchedules->keyBy('tanggal');

// // --- LOOP BERDASARKAN ROTASI RUANGAN (SEQUENCE) ---
// foreach ($mahasiswa->roomSequences as $seq) {
//     $period = \Carbon\CarbonPeriod::create($seq->start_date, $seq->end_date);
//     $kategoriRuangan = $seq->ruangan->kategori ?? 'non_shift';
//     $ruangName = $seq->ruangan->nm_ruangan;
    
//     // Flag Deteksi Ruangan Khusus
//     $isGizi = \Illuminate\Support\Str::contains(strtolower($ruangName), 'gizi');
//     $isMerak = \Illuminate\Support\Str::contains(strtolower($ruangName), 'merak');

//     foreach ($period as $dt) {
//         $dStr = $dt->format('Y-m-d');
        
//         // --- A. LOGIKA RUANGAN NON-SHIFT (Reguler & Jumat) ---
//         if ($kategoriRuangan === 'non_shift') {
            
//             // 1. Logika Weekend (Sabtu & Minggu) -> Tampilkan LIBUR
//             if ($dt->isWeekend()) {
//                 $events[] = [
//                     'title' => 'LIBUR',
//                     'start' => $dStr,
//                     'color' => '#6c757d', // Warna Abu-abu
//                     'extendedProps' => [
//                         'jam' => 'Sabtu/Minggu', 
//                         'ruang' => $ruangName, 
//                         'type' => 'jadwal'
//                     ]
//                 ];
//                 continue; // Lanjut ke hari berikutnya
//             }

//             // 2. Logika Hari Kerja (Senin - Jumat)
//             $isJumat = $dt->dayOfWeekIso == 5;
// // --- A. LOGIKA RUANGAN NON-SHIFT (Reguler & Jumat) ---
// if ($kategoriRuangan === 'non_shift') {
    
//     // 1. Logika Weekend (Sabtu & Minggu) tetap LIBUR
//     if ($dt->isWeekend()) {
//         $events[] = [
//             'title' => 'LIBUR',
//             'start' => $dStr,
//             'color' => '#6c757d',
//             'extendedProps' => [
//                 'jam' => 'Sabtu/Minggu', 
//                 'ruang' => $ruangName, 
//                 'type' => 'jadwal'
//             ]
//         ];
//         continue;
//     }

//     // 2. Logika Jam Kerja Berdasarkan Hari (Senin-Kamis vs Jumat)
//     $dayOfWeek = $dt->dayOfWeekIso; // 1 (Senin) s/d 7 (Minggu)

//     if ($dayOfWeek == 5) {
//         // HARI JUMAT
//         $jam = '07:00 - 11:30';
//         $title = 'Jumat';
//     } else {
//         // SENIN - KAMIS (Reguler)
//         $jam = '07:30 - 15:00'; // Sudah diubah ke 15:00 sesuai permintaan
//         $title = 'Reguler';
//     }
    
//     $events[] = [
//         'title' => $title,
//         'start' => $dStr,
//         'color' => '#6610f2',
//         'extendedProps' => [
//             'jam' => $jam, 
//             'ruang' => $ruangName, 
//             'type' => 'jadwal'
//         ]
//     ];
    
// }

//         } 
        
//         // --- B. LOGIKA RUANGAN SHIFT (Manual Input Admin) ---
//         else {
//             // Hanya tampil jika Admin sudah mengisi di tabel shift_schedules
//             if (isset($manualShifts[$dStr])) {
//                 $shift = $manualShifts[$dStr];
//                 $type = $shift->shift_type;
//                 $jamShift = '';

//                 // Logika Jam Berdasarkan Ruangan & Tipe Shift
//                 if ($isGizi) {
//                     // Jam Khusus Instalasi Gizi
//                     if ($type == 'Pagi') $jamShift = '04:30 - 12:40';
//                     elseif ($type == 'Siang') $jamShift = '11:20 - 19:30';
//                     elseif ($type == 'Reguler') $jamShift = '07:15 - 15:25';
//                     else $jamShift = 'Libur Shift';
//                 } else {
//                     // Jam Standar Ruangan Lain
//                     if ($type == 'Pagi') $jamShift = '07:00 - 14:00';
//                     elseif ($type == 'Siang') $jamShift = '14:00 - 21:00';
//                     elseif ($type == 'Malam') {
//                         $jamShift = $isMerak ? '20:00 - 07:00' : '21:00 - 07:00';
//                     } else $jamShift = 'Libur Shift';
//                 }

//                 $events[] = [
//                     'title' => ucfirst($type),
//                     'start' => $dStr,
//                     'color' => ($type == 'Libur' ? '#6c757d' : '#0d6efd'), // Biru untuk Shift
//                     'extendedProps' => [
//                         'jam' => ($type == 'Libur' ? 'Istirahat' : $jamShift), 
//                         'ruang' => $ruangName, 
//                         'type' => 'jadwal'
//                     ]
//                 ];
//             }
//         }
//     }
// }
// ==========================================================
// BUILD CALENDAR EVENTS (LOGIKA DINAMIS DARI DATABASE)
// ==========================================================
        // Kalender: satu sumber dari model (identik dgn halaman admin)
        $events = $mahasiswa->kalenderEvents();

        // Kirim variable 'totalHadir' ke view menggunakan 'totalHadirChart' (Gabungan Fisik + Izin)
        // Agar di view sinkron dengan persentase.
        $totalHadir = $totalHadirChart; 

        // Riwayat semua periode magang user ini (untuk unduh sertifikat multiple)
        $riwayatSertifikat = \App\Models\Mahasiswa::where('user_id', $mahasiswa->user_id)
            ->orderBy('tanggal_mulai', 'desc')->get();

        return view('mahasiswa.dashboard', compact(
            'mahasiswa', 'targetTotal', 'targetBerjalan', 'totalHadir', 'alpha', 'lupaPulang',
            'chartSisa', 'persentase', 'absensi', 'riwayatLengkap', 'startStr', 'endStr',
            'jadwalRolling', 'events', 'riwayatSertifikat'
        ));
    }
 public function generateSertifikat(Request $request, $token)
{
    // 1. Ambil Data dengan relasi lengkap agar sinkron dengan dashboard
    $mahasiswa = Mahasiswa::where('share_token', $token)
        ->with(['mou', 'absensis', 'shiftSchedules', 'roomSequences.ruangan'])
        ->firstOrFail();
    
    // 2. Hitung Total Hadir (Realtime)
    // Menggunakan unique date dari absensi tipe 'masuk'
    $totalActualDays = $mahasiswa->absensis
        ->where('type', 'masuk')
        ->map(fn($item) => \Carbon\Carbon::parse($item->created_at)->format('Y-m-d'))
        ->unique()
        ->count();

    // 3. Hitung Target Hari (Logika Sinkron dengan show())
    $expectedDates = [];

    // --- A. Dari Jadwal Shift (Jika Ada) ---
    foreach ($mahasiswa->shiftSchedules as $shift) {
        if (strtolower($shift->shift_type) !== 'libur') {
            $expectedDates[$shift->tanggal] = true;
        }
    }

    // --- B. Dari Non-Shift (Jika shift kosong & ada sequence) ---
    if ($mahasiswa->shiftSchedules->isEmpty() && $mahasiswa->roomSequences->count() > 0) {
        foreach ($mahasiswa->roomSequences as $seq) {
            if (($seq->ruangan->kategori ?? '') == 'non_shift') {
                $period = \Carbon\CarbonPeriod::create($seq->start_date, $seq->end_date);
                foreach ($period as $dt) {
                    if ($dt->isWeekend() && !$mahasiswa->weekend_aktif) continue;
                    $expectedDates[$dt->format('Y-m-d')] = true;
                }
            }
        }
    }

    $totalExpectedDays = count($expectedDates);

    // 4. Hitung Persentase (Termasuk Izin jika ingin dianggap hadir)
    $totalIzin = \App\Models\Dispensasi::where('mahasiswa_id', $mahasiswa->id)
        ->where('status', 'approved')
        ->count();

    if ($request->filled('override_percentage') && is_numeric($request->override_percentage)) {
        $percentage = (float) $request->override_percentage;
    } else {
        // Logika: (Hadir + Izin) / Target
        $calculated = ($totalExpectedDays > 0) 
            ? round((($totalActualDays + $totalIzin) / $totalExpectedDays) * 100, 1) 
            : 0;
        $percentage = min(100, $calculated);
    }

    // 5. Asset Background & QR Code (Tetap sama seperti kode Anda)
    // ... (Logika QR Code & Background Anda tetap di sini) ...

    // 7. Load PDF
    $pdf = Pdf::loadView('sertifikat.template', [
        'mahasiswa'      => $mahasiswa,
        'percentage'     => $percentage,
        'total_hadir'    => $totalActualDays,
        'total_izin'     => $totalIzin, // Tambahkan variabel ini ke view jika perlu
        'tanggal_terbit' => now()->isoFormat('D MMMM YYYY'),
        'bg_base64'      => $bgBase64,
        'qr_base64'      => $qrBase64 
    ]);

    $pdf->setOptions(['isRemoteEnabled' => true]); 
    $pdf->setPaper('a4', 'landscape');

    $cleanName = preg_replace('/[^A-Za-z0-9\-]/', ' ', $mahasiswa->nm_mahasiswa);
    return $pdf->stream('Sertifikat-' . trim($cleanName) . '.pdf');
}

    // Helper (Private)
    private function fetchRemoteImage($url) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => 1, CURLOPT_FOLLOWLOCATION => 1,
            CURLOPT_SSL_VERIFYPEER => 0, CURLOPT_TIMEOUT => 10,
            CURLOPT_USERAGENT => 'Mozilla/5.0'
        ]);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data ? 'data:image/png;base64,' . base64_encode($data) : null;
    }
    
    public function approveIdCard($id)
{
    $mahasiswa = Mahasiswa::findOrFail($id);
    
    // Set status menjadi true (disetujui)
    $mahasiswa->update([
        'is_id_card_approved' => true
    ]);

    return back()->with('success', 'ID Card mahasiswa ini telah disetujui.');
}
}
