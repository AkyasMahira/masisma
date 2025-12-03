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

    public function index(Request $request)
    {
        $ruangans = Ruangan::orderBy('nm_ruangan', 'asc')->get();

        // Eager load 'mou', 'ruangan' dan user->mou agar query lebih ringan dan data register tersedia
        $query = Mahasiswa::with(['mou', 'ruangan', 'user.mou'])->where('status', 'aktif');

        // 1. Filter by Universitas (Via Relasi)
        if ($request->has('univ_asal') && !empty($request->univ_asal)) {
            $query->whereHas('mou', function ($q) use ($request) {
                $q->where('nama_instansi', 'like', '%' . $request->univ_asal . '%')
                  ->orWhere('nama_universitas', 'like', '%' . $request->univ_asal . '%');
            });
        }

        // 2. Filter by Ruangan
        if ($request->has('ruangan_id') && !empty($request->ruangan_id)) {
            $query->where('ruangan_id', $request->ruangan_id);
        }

        // 3. Search by Nama
        if ($request->has('search') && !empty($request->search)) {
            $query->where('nm_mahasiswa', 'like', '%' . $request->search . '%');
        }

        $mahasiswas = $query->orderBy('created_at', 'desc')
            ->paginate(10)->withQueryString();

        // Trigger auto-deactivate logic (memanggil accessor sisa_hari)
        $mahasiswas->getCollection()->transform(function ($m) {
            $m->sisa_hari;
            return $m->fresh();
        });

        return view('mahasiswa.index', compact('mahasiswas', 'ruangans'));
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

    public function show($id)
    {
        $mahasiswa = Mahasiswa::with(['mou', 'ruangan'])->findOrFail($id);
        $today = now()->toDateString();
        $last = $mahasiswa->absensis()->whereDate('created_at', $today)->latest()->first();
        $lastStatus = $last ? $last->type : null;

        return view('mahasiswa.show', compact('mahasiswa', 'lastStatus'));
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
                    'tanggal_mulai' => $tanggalMulai,
                    'tanggal_berakhir' => $tanggalBerakhir,
                    'status' => in_array($status, ['aktif', 'nonaktif']) ? $status : 'aktif',
                    'ruangan_id' => $ruanganId,
                    'nm_ruangan' => $nmRuangan,
                ];

                // 3. Cek Data Lama (Untuk Update) atau Buat Baru
                // Kita cari berdasarkan nama & mou_id agar spesifik
                $mahasiswa = Mahasiswa::where('nm_mahasiswa', $name)
                    ->when($mouId, function ($q) use ($mouId) {
                        return $q->where('mou_id', $mouId);
                    })
                    ->first();

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

    private function storeSingleMahasiswa(Request $request)
    {
        $user = auth()->user();

        // Build validation rules depending on role: non-admin users should not be
        // able to set mou_id, tanggal_mulai/tanggal_berakhir or weekend_aktif.
        $rules = [
            'nm_mahasiswa' => 'required|string|max:255',
            'prodi' => 'nullable|string|max:255',
            'nm_ruangan' => 'nullable|string|max:255',
            'ruangan_id' => 'nullable|exists:ruangans,id',
            'no_hp' => 'nullable|string|max:20',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];

        if ($user && $user->role === 'admin') {
            $rules = array_merge($rules, [
                'mou_id' => 'nullable|exists:mous,id',
                'tanggal_mulai' => 'nullable|date',
                'tanggal_berakhir' => 'nullable|date|after:tanggal_mulai',
                'weekend_aktif' => 'nullable|boolean',
            ]);
        }

        $data = $request->validate($rules);

        // Ensure defaults exist so later code can safely reference keys
        $data = array_merge([
            'mou_id' => null,
            'tanggal_mulai' => null,
            'tanggal_berakhir' => null,
            'weekend_aktif' => false,
            'ruangan_id' => null,
            'nm_ruangan' => null,
        ], $data);

        // If admin creates Mahasiswa, auto-generate a User account for them.
        if ($user && $user->role === 'admin') {
            $createdUser = $this->createUserForMahasiswa($data['nm_mahasiswa'], $request->input('no_hp'));
            $data['user_id'] = $createdUser->id;
            // Expose credentials briefly to admin via session flash
            session()->flash('created_mahasiswa_credentials', [
                'name' => $createdUser->name,
                'email' => $createdUser->email,
                'password' => $createdUser->plain_password,
            ]);
        } else {
            $data['user_id'] = auth()->id();
        }
        $data['status'] = 'aktif';
        // Only take weekend_aktif if admin; otherwise force false
        $data['weekend_aktif'] = ($user && $user->role === 'admin') ? $request->boolean('weekend_aktif') : false;

        // Upload Foto
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $nama_file = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/pas_foto'), $nama_file);
            $data['foto_path'] = 'uploads/pas_foto/' . $nama_file;
        }

        // Cek Ruangan
        if (!empty($data['ruangan_id'])) {
            $ruangan = Ruangan::find($data['ruangan_id']);
            $terisi = Mahasiswa::where('ruangan_id', $ruangan->id)->count();
            $tersedia = $ruangan->kuota_ruangan - $terisi;

            if ($tersedia <= 0) {
                return back()->withErrors(['ruangan_id' => 'Kuota ruangan penuh.'])->withInput();
            }

            // Update ketersediaan
            $today = now()->toDateString();
            $snapshot = RuanganKetersediaan::where('ruangan_id', $ruangan->id)
                ->where('tanggal', $today)->first();

            if ($snapshot) {
                $snapshot->decrement('tersedia');
            } else {
                RuanganKetersediaan::create([
                    'ruangan_id' => $ruangan->id,
                    'tanggal' => $today,
                    'tersedia' => $tersedia - 1
                ]);
            }
            $data['nm_ruangan'] = $ruangan->nm_ruangan;
        }

        Mahasiswa::create($data);
        return redirect()->route('mahasiswa.dashboard')->with('success', 'Biodata berhasil disimpan!'); // REDIRECT KE DASHBOARD
    }

    public function edit($id)
    {
        $mahasiswa = Mahasiswa::findOrFail($id);
        $ruangans = Ruangan::all();
        $mous = Mou::orderBy('nama_instansi', 'asc')->get(); // Data untuk dropdown (kolom baru)
        return view('mahasiswa.edit', compact('mahasiswa', 'ruangans', 'mous'));
    }

    public function update(Request $request, $id)
    {
        // Menggunakan return langsung dari hasil transaksi
        return DB::transaction(function () use ($request, $id) {
            $mahasiswa = Mahasiswa::findOrFail($id);

            $data = $request->validate([
                'nm_mahasiswa'   => 'required|string|max:255',
                'mou_id'         => 'nullable|exists:mous,id',
                'prodi'          => 'nullable|string|max:255',
                'nm_ruangan'     => 'nullable|string|max:255',
                'no_hp'          => 'nullable|string|max:20',
                'ruangan_id'     => 'nullable|exists:ruangans,id',
                'tanggal_mulai'  => 'required|date',
                'tanggal_berakhir' => 'required|date|after:tanggal_mulai',
                'status'         => 'required|in:aktif,nonaktif',
                'weekend_aktif'  => 'nullable|boolean',
                'foto'           => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            $data['weekend_aktif'] = $request->boolean('weekend_aktif');

            // --- Logic Ganti Foto ---
            if ($request->hasFile('foto')) {
                if ($mahasiswa->foto_path && File::exists(public_path($mahasiswa->foto_path))) {
                    File::delete(public_path($mahasiswa->foto_path));
                }
                $file = $request->file('foto');
                $nama_file = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/pas_foto'), $nama_file);
                $data['foto_path'] = 'uploads/pas_foto/' . $nama_file;
            }

            $oldRuanganId = $mahasiswa->ruangan_id;
            $newRuanganId = $data['ruangan_id'] ?? null;
            $today = now()->toDateString();

            // --- SKENARIO 1: STATUS BERUBAH JADI NONAKTIF ---
            if ($mahasiswa->status === 'aktif' && $data['status'] === 'nonaktif' && $oldRuanganId) {
                $old = Ruangan::find($oldRuanganId);
                // Gunakan lockForUpdate untuk mencegah race condition (opsional tapi disarankan)
                $snap = RuanganKetersediaan::firstOrCreate(
                    ['ruangan_id' => $old->id, 'tanggal' => $today],
                    ['tersedia' => $old->kuota_ruangan]
                );
                $snap->increment('tersedia');

                $data['ruangan_id'] = null;
                $data['nm_ruangan'] = null;

                $mahasiswa->update($data);

                // Redirect khusus jika dinonaktifkan
                return redirect()->route('mahasiswa.index')
                    ->with('success', 'Mahasiswa dinonaktifkan & keluar ruangan.');
            }

            // --- SKENARIO 2: PINDAH RUANGAN ---
            if ($newRuanganId && $newRuanganId != $oldRuanganId) {
                // Kembalikan kuota lama
                if ($oldRuanganId) {
                    $old = Ruangan::find($oldRuanganId);
                    $snap = RuanganKetersediaan::where('ruangan_id', $old->id)
                        ->where('tanggal', $today)->first();
                    if ($snap) $snap->increment('tersedia');
                }

                // Ambil kuota baru
                $new = Ruangan::find($newRuanganId);
                $terisi = Mahasiswa::where('ruangan_id', $new->id)->count();
                $tersedia = $new->kuota_ruangan - $terisi;

                if ($tersedia <= 0) {
                    // Karena di dalam transaction, kita throw exception atau return redirect with error
                    // Note: return di sini akan membatalkan commit otomatis jika tidak dihandle,
                    // tapi karena ini return response object, Laravel menganggapnya sukses tereksekusi.
                    // Sebaiknya gunakan redirect back.
                    return back()->withErrors(['ruangan_id' => 'Ruangan tujuan penuh.'])->withInput();
                }

                $snap = RuanganKetersediaan::firstOrCreate(
                    ['ruangan_id' => $new->id, 'tanggal' => $today],
                    ['tersedia' => $tersedia]
                );

                if (!$snap->wasRecentlyCreated) {
                    $snap->decrement('tersedia');
                }

                $data['nm_ruangan'] = $new->nm_ruangan;
            } elseif (!$newRuanganId) {
                // Jika ruangan dikosongkan manual (bukan karena nonaktif)
                $data['nm_ruangan'] = null;
            }

            // --- UPDATE UTAMA ---
            $mahasiswa->update($data);

            // --- LOGIKA REDIRECT BERDASARKAN ROLE ---
            // 1. Ambil user yang sedang login
            $user = auth()->user();

            // 2. Cek apakah user ada DAN role-nya admin
            if ($user && $user->role === 'admin') {
                return redirect()->route('mahasiswa.index')
                    ->with('success', 'Data berhasil diperbarui (Admin).');
            }

            // 3. Jika bukan admin (misal mahasiswa itu sendiri)
            // Redirect ke dashboard atau halaman profil, JANGAN ke index semua mahasiswa
            return redirect()->route('mahasiswa.dashboard')
                ->with('success', 'Profil Anda berhasil diperbarui.');
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
        $mahasiswa = Mahasiswa::findOrFail($id);
        $totalExpectedDays = $this->calculateExpectedDays($mahasiswa);

        $totalActualDays = $mahasiswa->absensis()
            ->select(DB::raw('DATE(created_at) as date'))
            ->distinct()->count();

        $participationRate = ($totalExpectedDays > 0)
            ? round(($totalActualDays / $totalExpectedDays) * 100, 1)
            : 0;

        $totalAlpaDays = max(0, $totalExpectedDays - $totalActualDays);

        return view('mahasiswa.sertifikat', compact(
            'mahasiswa',
            'totalExpectedDays',
            'totalActualDays',
            'totalAlpaDays',
            'participationRate'
        ));
    }

    public function generateSertifikat(Request $request, $id)
    {
        $mahasiswa = Mahasiswa::with('mou')->findOrFail($id);
        $totalActualDays = $mahasiswa->absensis()
            ->select(DB::raw('DATE(created_at) as date'))
            ->distinct()->count();

        // Cek Override Percentage
        if ($request->filled('override_percentage') && is_numeric($request->override_percentage)) {
            $percentage = (float) $request->override_percentage;
        } else {
            $totalExpectedDays = $this->calculateExpectedDays($mahasiswa);
            $percentage = ($totalExpectedDays > 0) ? round(($totalActualDays / $totalExpectedDays) * 100, 1) : 0;
        }

        // Background Image
        $bgPath = public_path('background.png');
        $bgBase64 = '';
        if (File::exists($bgPath)) {
            $bgData = File::get($bgPath);
            $bgBase64 = 'data:image/png;base64,' . base64_encode($bgData);
        }

        $pdf = Pdf::loadView('sertifikat.template', [
            'mahasiswa' => $mahasiswa,
            'percentage' => $percentage,
            'total_hadir' => $totalActualDays,
            'tanggal_terbit' => Carbon::now()->isoFormat('D MMMM YYYY'),
            'bg_base64' => $bgBase64
        ]);

        return $pdf->setPaper('a4', 'landscape')
            ->stream('Sertifikat-' . $mahasiswa->nm_mahasiswa . '.pdf');
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
        // Ambil data mahasiswa berdasarkan user_id
        $mahasiswa = Mahasiswa::where('user_id', auth()->id())->firstOrFail();

        // Hitung total hari kerja
        $tanggalMulai = Carbon::parse($mahasiswa->tanggal_mulai);
        $tanggalBerakhir = Carbon::parse($mahasiswa->tanggal_berakhir); // GANTI dari tanggal_selesai ke tanggal_berakhir

        // Hitung total hari (termasuk weekend atau tidak)
        if ($mahasiswa->weekend_aktif) {
            // Kalau weekend aktif, hitung semua hari
            $totalHari = $tanggalMulai->diffInDays($tanggalBerakhir) + 1;
        } else {
            // Kalau weekend tidak aktif, hitung hanya weekdays
            $totalHari = $tanggalMulai->diffInWeekdays($tanggalBerakhir) + 1;
        }

        // Ambil data absensi
        $absensi = Absensi::where('mahasiswa_id', $mahasiswa->id)
            ->orderBy('created_at', 'desc') // GANTI dari tanggal ke created_at
            ->limit(2)
            ->get();

        // Hitung total hadir (yang punya jam_masuk & jam_keluar)
        $totalHadir = $absensi->filter(function ($abs) {
            return !is_null($abs->jam_masuk) && !is_null($abs->jam_keluar);
        })->count();

        // Hitung persentase kehadiran
        $persentase = $totalHari > 0 ? round(($totalHadir / $totalHari) * 100, 1) : 0;

        // Data untuk chart (7 hari terakhir)
        $chartLabels = [];
        $chartData = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $chartLabels[] = $date->format('d M');

            $count = $absensi->filter(function ($abs) use ($date) {
                return Carbon::parse($abs->created_at)->format('Y-m-d') === $date->format('Y-m-d')
                    && !is_null($abs->jam_masuk)
                    && !is_null($abs->jam_keluar);
            })->count();

            $chartData[] = $count;
        }

        return view('mahasiswa.dashboard', compact(
            'mahasiswa',
            'totalHari',
            'totalHadir',
            'persentase',
            'absensi',
            'chartLabels',
            'chartData'
        ));
    }
}
