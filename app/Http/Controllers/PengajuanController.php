<?php

namespace App\Http\Controllers;

use App\Models\Pengajuan;
use App\Models\PraPenelitian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
// Pastikan Models ini ada dan sudah di-import
use App\Models\CorporateInstructor;
use App\Models\Ruangan;
use App\Models\Mou;
  use Barryvdh\DomPDF\Facade\Pdf; //
class PengajuanController extends Controller
{
public function index()
    {
        $userId = auth()->id();

        // 1. CEK PENGAJUAN MAGANG (Ambil yang paling baru)
        $magang = Pengajuan::where('user_id', $userId)
            ->where('jenis', 'magang')
            ->latest()
            ->first();

        // 2. CEK STATUS AKTIF MAGANG 
        $isMagangActive = false;
        // Gunakan latest()->first() agar mengambil data periode terakhir
        $mahasiswaAktif = \App\Models\Mahasiswa::where('user_id', $userId)->latest()->first();

        if ($magang) {
            if ($magang->status === 'pending') {
                $isMagangActive = true; // Masih antri persetujuan
            } elseif ($magang->status === 'approved') {
                if (!$mahasiswaAktif) {
                    $isMagangActive = true; // Di-ACC tapi belum isi biodata
                } elseif ($mahasiswaAktif->status === 'aktif') {
                    $isMagangActive = true; // Sedang magang berjalan
                }
                // Jika status mahasiswanya 'nonaktif', maka $isMagangActive = false (Bisa daftar lagi)
            }
        }

        // 3. CEK PENGAJUAN PENELITIAN AKTIF
        $activePra = Pengajuan::with('presentasi')
            ->where('user_id', $userId)
            ->where('jenis', 'pra_penelitian')
            ->where(function($q) {
                $q->whereIn('status', ['pending', 'approved'])
                  ->whereDoesntHave('presentasi', function($query) {
                      $query->whereIn('status_final', ['selesai', 'ditolak']);
                  });
            })
            ->latest()
            ->first();

        // 4. AMBIL RIWAYAT PENELITIAN 
        $historyPra = \App\Models\PraPenelitian::where('user_id', $userId)
            ->whereHas('presentasi', function($query) {
                $query->whereIn('status_final', ['selesai', 'ditolak']);
            })
            ->latest()
            ->get();

        $bisaAjukanPra = $activePra ? false : true;

        return view('pengajuan.index', compact('activePra', 'historyPra', 'magang', 'bisaAjukanPra', 'isMagangActive', 'mahasiswaAktif'));
    }

    public function ajukanMagang()
    {
        // Cari pengajuan magang yang paling terakhir
        $latestMagang = Pengajuan::where('user_id', auth()->id())
            ->where('jenis', 'magang')
            ->latest()
            ->first();

        if ($latestMagang) {
            // Blokir jika masih pending
            if ($latestMagang->status === 'pending') {
                return back()->with('error', 'Anda masih memiliki pengajuan Magang yang berstatus PENDING.');
            }
            
            // Blokir jika di-ACC tapi magangnya belum dinyatakan 'nonaktif' (selesai)
            if ($latestMagang->status === 'approved') {
                $mahasiswa = \App\Models\Mahasiswa::where('user_id', auth()->id())->latest()->first();
                if (!$mahasiswa || $mahasiswa->status === 'aktif') {
                    return back()->with('error', 'Anda tidak bisa mendaftar karena masih memiliki program Magang yang SEDANG BERJALAN.');
                }
            }
        }

        // Lolos validasi = Bikin Pengajuan Baru
        Pengajuan::create([
            'user_id' => auth()->id(),
            'jenis'   => 'magang',
            'status'  => 'pending',
        ]);

        return back()->with('success', 'Pengajuan magang periode baru berhasil dikirim.');
    }
    public function ajukanPra()
    {
        $userId = auth()->id();
        
        // Cek pengajuan yang paling terakhir saja
        $latestPra = Pengajuan::with('presentasi')
            ->where('user_id', $userId)
            ->where('jenis', 'pra_penelitian')
            ->latest()
            ->first();

        if ($latestPra && in_array($latestPra->status, ['pending', 'approved'])) {
            if (!$latestPra->presentasi || !in_array($latestPra->presentasi->status_final, ['selesai', 'ditolak'])) {
                return back()->with('error', 'Anda masih memiliki pengajuan Pra-Penelitian yang sedang berjalan. Selesaikan terlebih dahulu!');
            }
        }

        // Jika lolos, buat pengajuan baru
        Pengajuan::create([
            'user_id' => $userId,
            'jenis'   => 'pra_penelitian',
            'status'  => 'pending',
        ]);

        return back()->with('success', 'Pengajuan pra-penelitian baru berhasil dikirim.');
    }


    // ========== MAHASISWA METHODS ==========

    /**
     * Upload bukti pembayaran
     */
    public function uploadBuktiPembayaran(Request $request, Pengajuan $pengajuan)
    {
        // Validasi bahwa pengajuan ini milik user yang login
        if ($pengajuan->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        // Validasi status galasan sudah dikirim
        if ($pengajuan->status_galasan !== 'sent') {
            return back()->with('error', 'Galasan belum dikirim oleh admin.');
        }

        $request->validate([
            'bukti_pembayaran' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        // Hapus file lama jika ada
        if ($pengajuan->bukti_pembayaran && Storage::exists($pengajuan->bukti_pembayaran)) {
            Storage::delete($pengajuan->bukti_pembayaran);
        }

        // Upload file baru
        $path = $request->file('bukti_pembayaran')->store('bukti_pembayaran', 'public');

        $pengajuan->update([
            'bukti_pembayaran' => $path,
            'status_pembayaran' => 'uploaded',
        ]);

        return back()->with('success', 'Bukti pembayaran berhasil diupload. Menunggu verifikasi admin.');
    }

    // ========== ADMIN METHODS ==========
public function adminIndex(Request $request)
{
    // Menggunakan eager loading untuk menghindari N+1 query problem
    $query = Pengajuan::with(['user.mou', 'user.mahasiswa']);

    // 1. Filter Pencarian (Nama atau Email User)
    if ($request->filled('search')) {
        $search = $request->search;
        $query->whereHas('user', function($q) use ($search) {
            $q->where('name', 'like', "%$search%")
              ->orWhere('email', 'like', "%$search%");
        });
    }

    // 2. Filter Universitas / Instansi (melalui relasi mou_id di tabel users)
    if ($request->filled('university_id')) {
        $query->whereHas('user', function($q) use ($request) {
            $q->where('mou_id', $request->university_id);
        });
    }

    // 3. Filter Rentang Tanggal Pengajuan
    if ($request->filled('start_date') && $request->filled('end_date')) {
        $query->whereBetween('created_at', [
            $request->start_date . ' 00:00:00', 
            $request->end_date . ' 23:59:59'
        ]);
    }

    // 4. Filter Jenis Pengajuan (magang / pra_penelitian)
    if ($request->filled('jenis')) {
        $query->where('jenis', $request->jenis);
    }

    // 5. Filter Status Pengajuan (pending / approved / rejected)
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // 6. FILTER BARU: Sortir/Filter berdasarkan Status Approval ID Card
    // Menggunakan strict check (filled) agar nilai '0' tetap terbaca sebagai filter aktif
    // if ($request->has('id_card_status') && $request->id_card_status !== null && $request->id_card_status !== '') {
    //     $idCardStatus = $request->id_card_status;
    //     $query->whereHas('user.mahasiswa', function($q) use ($idCardStatus) {
    //         $q->where('is_id_card_approved', $idCardStatus);
    //     });
    // }

    // Eksekusi query dengan pagination dan tetap membawa query string di URL
    $data = $query->latest()->paginate(10)->withQueryString();

    // Ambil data universitas untuk dropdown filter di view
    $universities = Mou::select('id', 'nama_instansi', 'nama_universitas')
        ->orderBy('nama_instansi')
        ->orderBy('nama_universitas')
        ->get();

    // Response untuk AJAX (jika menggunakan live reload table)
    if ($request->ajax()) {
        return view('admin.pengajuan._table', compact('data'))->render();
    }

    // Response view utama
    return view('admin.pengajuan.index', compact('data', 'universities'));
}

    /**
     * Handle Bulk Actions (Approve/Reject/Delete/ID Card)
     */
     
     public function cancel(Pengajuan $pengajuan)
{
    $pengajuan->update(['status' => 'canceled']);
    return back()->with('success', 'Pengajuan telah dibatalkan.');
}
    public function bulkAction(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            // PERBAIKAN DISINI: Gunakan 'pengajuan' (sesuai nama tabel database Anda)
            'ids.*' => 'exists:pengajuan,id', 
     'action' => 'required|in:approve,reject,delete,approve_id_card,cancel'
        ]);

        $ids = $request->ids;
        $action = $request->action;
        $count = count($ids);

        if ($action === 'approve') {
            Pengajuan::whereIn('id', $ids)->where('status', 'pending')->update(['status' => 'approved']);
            $message = "$count Pengajuan berhasil disetujui.";
        }elseif ($action === 'cancel') { // Tambahkan Logika ini
        Pengajuan::whereIn('id', $ids)->update(['status' => 'canceled']);
        $message = "$count Pengajuan berhasil dibatalkan.";
    } 
        
        elseif ($action === 'reject') {
            Pengajuan::whereIn('id', $ids)->where('status', 'pending')->update(['status' => 'rejected']);
            $message = "$count Pengajuan berhasil ditolak.";
        } elseif ($action === 'delete') {
            $pengajuans = Pengajuan::whereIn('id', $ids)->get();
            foreach($pengajuans as $p) {
                // Panggil destroy manual agar file terhapus (jika ada logika hapus file di method destroy)
                $this->destroy($p); 
            }
            $message = "$count Pengajuan berhasil dihapus.";
        
        } 
        
        // elseif ($action === 'approve_id_card') {
        //     // Logika ACC ID Card Massal
        //     $pengajuans = Pengajuan::whereIn('id', $ids)
        //                     ->where('jenis', 'magang')
        //                     ->where('status', 'approved')
        //                     ->with('user.mahasiswa')
        //                     ->get();
            
        //     $approvedCount = 0;

        //     foreach($pengajuans as $p) {
        //         if ($p->user && $p->user->mahasiswa) {
        //             $p->user->mahasiswa->update(['is_id_card_approved' => true]);
        //             $approvedCount++;
        //         }
        //     }
            
        //     if ($approvedCount == 0) {
        //         return back()->with('error', 'Tidak ada data Magang Approved yang dipilih untuk ACC ID Card.');
        //     }

        //     $message = "$approvedCount ID Card berhasil disetujui.";
        // }

        return back()->with('success', $message ?? 'Aksi berhasil.');
    }

    public function approve(Pengajuan $pengajuan)
    {
        $pengajuan->update(['status' => 'approved']);
        return back()->with('success', 'Pengajuan disetujui.');
    }

    public function reject(Pengajuan $pengajuan)
    {
        $pengajuan->update(['status' => 'rejected']);
        return back()->with('success', 'Pengajuan ditolak.');
    }

    /**
     * Kirim galasan (surat + invoice)
     */
    public function kirimGalasan(Request $request, Pengajuan $pengajuan)
    {
        // Cek apakah ada pra_penelitian yang sudah approved
        $praPenelitian = PraPenelitian::where('user_id', $pengajuan->user_id)
            ->where('status', 'Approved')
            ->first();

        if (!$praPenelitian) {
            return back()->with('error', 'Form pra penelitian belum di-approve.');
        }

        $request->validate([
            'surat_balasan' => 'required|file|mimes:pdf|max:2048',
            'invoice' => 'required|file|mimes:pdf|max:2048',
        ]);

        $suratPath = $request->file('surat_balasan')->store('surat_balasan', 'public');
        $invoicePath = $request->file('invoice')->store('invoice', 'public');

        $pengajuan->update([
            'surat_balasan' => $suratPath,
            'invoice' => $invoicePath,
            'status_galasan' => 'sent',
        ]);

        return back()->with('success', 'Galasan berhasil dikirim ke mahasiswa.');
    }

    public function approvePembayaran(Request $request, Pengajuan $pengajuan)
{
    $request->validate([
        'ci_id'      => 'required|exists:corporate_instructors,id',
        'ci_id_2'    => 'nullable|exists:corporate_instructors,id|different:ci_id', // Tambahkan ini
        'ruangan_id' => 'required|exists:ruangans,id',
    ]);

    $pengajuan->update([
        'status_pembayaran' => 'verified',
        'ci_id'             => $request->ci_id,
        'ci_id_2'           => $request->ci_id_2, // Tambahkan ini
        'ruangan_id'        => $request->ruangan_id,
    ]);

    return back()->with('success', 'Pembayaran diverifikasi dan CI berhasil ditugaskan.');
}
public function updateCi2(Request $request, Pengajuan $pengajuan)
{
    $request->validate([
        'ci_id_2' => 'required|exists:corporate_instructors,id|different:ci_id',
    ], [
        'ci_id_2.different' => 'Pembimbing kedua tidak boleh sama dengan pembimbing pertama.'
    ]);

    $pengajuan->update([
        'ci_id_2' => $request->ci_id_2
    ]);

    return back()->with('success', 'Pembimbing kedua berhasil ditambahkan.');
}
    /**
     * Show detail pengajuan untuk admin
     */
public function show(Pengajuan $pengajuan)
    {
        // 1. Cek Logic Redirect ke View Mahasiswa (Khusus Magang Approved)
        if (trim(strtolower($pengajuan->jenis)) == 'magang' && $pengajuan->status == 'approved') {
            
            $mahasiswa = \App\Models\Mahasiswa::where('user_id', $pengajuan->user_id)->first();
            
            if ($mahasiswa) {
                $lastStatus = \App\Models\Absensi::where('mahasiswa_id', $mahasiswa->id)
                    ->latest()
                    ->value('type');
       $today = now()->toDateString();
                $tglMulai = $mahasiswa->tanggal_mulai;
        $tglAkhir = $mahasiswa->tanggal_berakhir;
               $statusJadwal = 'Global';
                // ----------------------------------------------------------------
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
        // BUILD CALENDAR EVENTS (DETAIL LENGKAP)
        // ==========================================================
        $events = [];
        $liburDates = []; // Tracker tanggal libur

        // --- A. JADWAL SHIFT (MANUAL) ---
        foreach ($mahasiswa->shiftSchedules as $shift) {
            // Deteksi Ruangan Spesifik Tanggal Ini
            $seq = $mahasiswa->roomSequences->first(function($s) use ($shift) {
                return $shift->tanggal >= $s->start_date && $shift->tanggal <= $s->end_date;
            });
            $ruangName = $seq ? $seq->ruangan->nm_ruangan : ($mahasiswa->ruangan->nm_ruangan ?? '-');
            $isMerak = \Illuminate\Support\Str::contains(strtolower($ruangName), 'merak');

            $color = '#0d6efd'; // Biru Bootstrap
            $title = ucfirst($shift->shift_type);
          // ... (kode sebelumnya di dalam loop) ...
            $ruangName = $seq ? $seq->ruangan->nm_ruangan : ($mahasiswa->ruangan->nm_ruangan ?? '-');
            $isMerak = \Illuminate\Support\Str::contains(strtolower($ruangName), 'merak');
            // TAMBAHAN: Cek Gizi
            $isGizi = \Illuminate\Support\Str::contains(strtolower($ruangName), 'gizi');

            $color = '#0d6efd'; 
            $title = ucfirst($shift->shift_type);
            $jam    = '';

            // --- LOGIKA JAM (UPDATE INI) ---
            if ($isGizi) {
                // KHUSUS GIZI
                if ($shift->shift_type == 'Pagi') $jam = '04:30 - 12:40';
                elseif ($shift->shift_type == 'Siang') $jam = '11:20 - 19:30';
                elseif ($shift->shift_type == 'Reguler') $jam = '07:15 - 15:25';
                else $jam = 'Libur';
            } else {
                // RUANGAN LAIN (STANDARD)
                if ($shift->shift_type == 'Pagi') $jam = '07:00 - 14:00';
                elseif ($shift->shift_type == 'Siang') $jam = '14:00 - 21:00';
                elseif ($shift->shift_type == 'Malam') $jam = $isMerak ? '20:00 - 07:00' : '21:00 - 07:00';
            }

            // Handle Libur Umum
            if ($shift->shift_type == 'Libur') {
                $color = '#6c757d'; 
                $title = 'LIBUR';
                $jam    = 'Istirahat';
                $liburDates[$shift->tanggal] = true;
            }
            // ... (lanjutan kode event events[] ...)

            $events[] = [
                'title' => $title,
                'start' => $shift->tanggal,
                'color' => $color,
                'extendedProps' => ['jam' => $jam, 'ruang' => $ruangName, 'type' => 'jadwal']
            ];
        }

        // --- B. JADWAL NON-SHIFT (OTOMATIS GENERATE) ---
        // Jika mahasiswa tidak punya shift schedule manual, kita generate jadwal reguler
        // berdasarkan Sequence Ruangan (Non-Shift)
        if ($mahasiswa->shiftSchedules->isEmpty() && $mahasiswa->roomSequences->count() > 0) {
            foreach ($mahasiswa->roomSequences as $seq) {
                if (($seq->ruangan->kategori ?? '') == 'non_shift') {
                    $period = \Carbon\CarbonPeriod::create($seq->start_date, $seq->end_date);
                    foreach ($period as $dt) {
                        if ($dt->isWeekend() && !$mahasiswa->weekend_aktif) continue;
                        
                        $isJumat = $dt->dayOfWeekIso == 5;
                        $jam = $isJumat ? 'PUASA (07:00 - 11:30)' : 'PUASA (07:30 - 15:00)';
                        $title = $isJumat ? 'Jumat' : 'Reguler';
                        
                        $events[] = [
                            'title' => $title,
                            'start' => $dt->format('Y-m-d'),
                            'color' => '#6610f2', // Ungu
                            'extendedProps' => ['jam' => $jam, 'ruang' => $seq->ruangan->nm_ruangan, 'type' => 'jadwal']
                        ];
                    }
                }
            }
        }

        // --- C. REALISASI ABSENSI (HIJAU/MERAH) ---
        $absensiGrouped = $mahasiswa->absensis->groupBy(function($item) {
            return \Carbon\Carbon::parse($item->created_at)->format('Y-m-d');
        });

        foreach ($absensiGrouped as $date => $logs) {
            $hasMasuk = $logs->where('type', 'masuk')->first();
            
            // Logic cari keluar (termasuk lintas hari)
            $hasKeluar = $logs->where('type', 'keluar')->where('jam_masuk', $hasMasuk->jam_masuk ?? null)->first();
            if (!$hasKeluar && $hasMasuk) {
                // Cari keluar manual yg waktunya > masuk
                $hasKeluar = $mahasiswa->absensis->where('type', 'keluar')
                    ->where('created_at', '>', $hasMasuk->created_at)
                    ->sortBy('created_at')->first();
            }

            // Jika Libur dan cuma iseng absen, jangan tampilkan event merah
            if (isset($liburDates[$date]) && !$hasMasuk) continue;

            if ($hasMasuk && $hasKeluar) {
                $jamM = \Carbon\Carbon::parse($hasMasuk->jam_masuk)->format('H:i');
                $jamK = \Carbon\Carbon::parse($hasKeluar->jam_keluar)->format('H:i');
                $durasi = \Carbon\Carbon::parse($hasKeluar->jam_keluar)->diffInHours($hasMasuk->jam_masuk);

                $events[] = [
                    'title' => 'HADIR',
                    'start' => $date,
                    'color' => '#198754', // Hijau
                    'extendedProps' => ['jam' => "$jamM - $jamK ($durasi Jam)", 'ruang' => 'Absen Masuk', 'type' => 'absen']
                ];
            } elseif ($hasMasuk) {
                // Belum checkout
                $isToday = $date == $today;
                $events[] = [
                    'title' => $isToday ? 'SEDANG KERJA' : 'LUPA PULANG',
                    'start' => $date,
                    'color' => $isToday ? '#ffc107' : '#dc3545',
                    'extendedProps' => ['jam' => \Carbon\Carbon::parse($hasMasuk->jam_masuk)->format('H:i') . ' - ?', 'ruang' => 'Incomplete', 'type' => 'absen']
                ];
            }
        }

        // --- D. DISPENSASI ---
        $dispensasis = \App\Models\Dispensasi::where('mahasiswa_id', $mahasiswa->id)->where('status', 'approved')->get();
        foreach ($dispensasis as $dispen) {
            $events[] = [
                'title' => 'IZIN',
                'start' => $dispen->tanggal_mulai,
                'end'   => \Carbon\Carbon::parse($dispen->tanggal_selesai)->addDay()->format('Y-m-d'),
                'color' => '#fd7e14',
                'extendedProps' => ['jam' => $dispen->kategori, 'ruang' => 'Dispensasi', 'type' => 'izin']
            ];
        }

                return view('mahasiswa.show', compact('mahasiswa', 'lastStatus', 'tglAkhir','tglMulai','statusJadwal','events'));
            }
        }

        // 2. Logic Default Admin View (Jika bukan magang approved atau data mahasiswa belum ada)
        $ruangans = \App\Models\Ruangan::all(); 
        $cis = \App\Models\CorporateInstructor::orderBy('nama', 'asc')->get(); 

        $praPenelitian = null;
        if ($pengajuan->jenis === 'pra_penelitian') {
            $praPenelitian = \App\Models\PraPenelitian::where('user_id', $pengajuan->user_id)
                ->latest()
                ->first();
        }

        return view('admin.pengajuan.show', compact('pengajuan', 'ruangans', 'praPenelitian', 'cis'));
    }

    /**
     * Tampilkan detail pengajuan (untuk mahasiswa)
     */
public function detail($jenis)
    {
        $pengajuan = Pengajuan::with(['ci', 'dataRuangan']) 
            ->where('user_id', auth()->id())
            ->where('jenis', $jenis)
            ->where('status', 'approved')
            ->latest() // <--- TAMBAHKAN INI BIAR NARIK YANG PALING BARU
            ->firstOrFail();

        return view('pengajuan.detail', compact('pengajuan', 'jenis'));
    }

    public function destroy(Pengajuan $pengajuan)
    {
        $user = auth()->user();

        // authorize: owner or admin
        if (!$user || ($user->id !== $pengajuan->user_id && $user->role !== 'admin')) {
            abort(403, 'Unauthorized');
        }

        // Delete any uploaded files
        if ($pengajuan->surat_balasan && Storage::exists($pengajuan->surat_balasan)) {
            Storage::delete($pengajuan->surat_balasan);
        }
        if ($pengajuan->invoice && Storage::exists($pengajuan->invoice)) {
            Storage::delete($pengajuan->invoice);
        }
        if ($pengajuan->bukti_pembayaran && Storage::exists($pengajuan->bukti_pembayaran)) {
            Storage::delete($pengajuan->bukti_pembayaran);
        }

        // Remove related Presentasi (and its files)
        $presentasi = \App\Models\Presentasi::where('pengajuan_id', $pengajuan->id)->first();
        if ($presentasi) {
            // delete presentasi files
            if ($presentasi->file_ppt && Storage::exists($presentasi->file_ppt)) Storage::delete($presentasi->file_ppt);
            if ($presentasi->file_laporan && Storage::exists($presentasi->file_laporan)) Storage::delete($presentasi->file_laporan);
            if ($presentasi->sertifikat && Storage::exists('public/' . $presentasi->sertifikat)) Storage::delete('public/' . $presentasi->sertifikat);
            if ($presentasi->surat_selesai && Storage::exists('public/' . $presentasi->surat_selesai)) Storage::delete('public/' . $presentasi->surat_selesai);

            // delete presentasi record
            $presentasi->delete();
        }

        // Only remove PraPenelitian if this pengajuan was a Pra-Penelitian
        if ($pengajuan->jenis === 'pra_penelitian') {
            $pra = PraPenelitian::where('user_id', $pengajuan->user_id)->first();
            if ($pra) {
                // delete konsultasi
                \App\Models\Konsultasi::where('pra_penelitian_id', $pra->id)->delete();
                // delete anggota
                \App\Models\PraPenelitianAnggota::where('pra_penelitian_id', $pra->id)->delete();
                // finally delete pra penelitian
                $pra->delete();
            }
        }

        // finally delete pengajuan
        $pengajuan->delete();

        if ($user->role === 'admin') {
            return redirect()->route('admin.pengajuan.index')->with('success', 'Pengajuan berhasil dihapus.');
        }

        return redirect()->route('pengajuan.index')->with('success', 'Pengajuan berhasil dihapus. Anda bisa mengajukan kembali.');
    }
    


// Di dalam class PengajuanController
public function downloadSuratOtomatis(Pengajuan $pengajuan)
{
    $praPenelitian = PraPenelitian::where('user_id', $pengajuan->user_id)->latest()->first();

    if (!$praPenelitian) {
        return back()->with('error', 'Data penelitian tidak ditemukan.');
    }

    $data = [
        'pengajuan' => $pengajuan,
        'praPenelitian' => $praPenelitian
    ];

    $pdf = Pdf::loadView('admin.pdf.surat_selesai_data_awal', $data);
    
    // Nama file: Surat_Selesai_NamaMahasiswa.pdf
    return $pdf->download('Surat_Selesai_' . str_replace(' ', '_', $pengajuan->user->name) . '.pdf');
}
}