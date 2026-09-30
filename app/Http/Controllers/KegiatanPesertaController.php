<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use App\Models\KegiatanPeserta;
use App\Models\MasterInstansi;
use App\Models\MasterRuangan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class KegiatanPesertaController extends Controller
{
    // Kunci Rahasia untuk Enkripsi Link Sertifikat
    private $secretKey = 'sindikat_rsud_slg_secret';

    public function index(Request $request, $kegiatan_id)
    {
        $kegiatan = Kegiatan::findOrFail($kegiatan_id);
        
        // Query Dasar dengan relasi
        $query = KegiatanPeserta::with(['instansi', 'ruangan', 'absensiAktual'])
                                ->where('kegiatan_id', $kegiatan_id);

        // Filter Pencarian untuk Tampilan Tabel Ber-paged
        if ($request->search) {
            $query->where('nama_lengkap_gelar', 'like', '%' . $request->search . '%');
        }
        if ($request->instansi_id) {
            $query->where('instansi_id', $request->instansi_id);
        }

        // Ambil data untuk pagination tabel web
        $peserta = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();
        
        // AMBIL SEMUA DATA (Tanpa Paginate) khusus untuk penampung export SheetJS
        $semuaPeserta = KegiatanPeserta::with(['instansi', 'ruangan', 'absensiAktual'])
                                        ->where('kegiatan_id', $kegiatan_id)
                                        ->orderBy('id', 'desc')
                                        ->get();
        
        $instansi = MasterInstansi::orderBy('nama_instansi', 'asc')->get();
        $ruangan = MasterRuangan::with('instansi')->orderBy('nama_ruangan', 'asc')->get();
        
        $secretKey = $this->secretKey;

        return view('admin.kegiatan_peserta.index', compact('kegiatan', 'peserta', 'instansi', 'ruangan', 'secretKey', 'semuaPeserta'));
    }

    public function store(Request $request, $kegiatan_id)
    {
        $request->validate(['nama_lengkap_gelar' => 'required', 'profesi' => 'required']);
        
        // Peserta yang ditambahkan manual oleh admin otomatis berstatus terverifikasi
        $data = array_merge($request->all(), [
            'kegiatan_id' => $kegiatan_id,
            'status_pendaftaran' => 'terverifikasi'
        ]);
        
        KegiatanPeserta::create($data);
        
        return redirect()->back()->with('success', 'Peserta manual berhasil ditambahkan dan otomatis terverifikasi.');
    }

    public function update(Request $request, $kegiatan_id, $id)
    {
        $request->validate([
            'nama_lengkap_gelar' => 'required|string',
            'profesi' => 'required|string',
        ]);
        
        $peserta = KegiatanPeserta::findOrFail($id);
        
        // Update seluruh field baru dari form edit Modal LG
        $peserta->update([
            'nama_lengkap_gelar' => $request->nama_lengkap_gelar,
            'nik' => $request->nik,
            'tempat_lahir' => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'no_hp_peserta' => $request->no_hp_peserta,
            'email_plataran_sehat' => $request->email_plataran_sehat,
            'profesi' => $request->profesi,
            'jabatan' => $request->jabatan,
            'status_pegawai' => $request->status_pegawai,
            'pendidikan_terakhir' => $request->pendidikan_terakhir,
            'nip' => $request->nip,
            'pangkat_golongan' => $request->pangkat_golongan,
            'email_pj' => $request->email_pj,
            'no_hp_pj' => $request->no_hp_pj,
            'alamat_instansi' => $request->alamat_instansi,
        ]);

        return redirect()->back()->with('success', 'Data lengkap peserta berhasil diperbarui.');
    }

    public function destroy($kegiatan_id, $id)
    {
        $peserta = KegiatanPeserta::findOrFail($id);
        
        // Hapus file bukti bayar fisik jika ada
        if ($peserta->bukti_bayar && file_exists(public_path($peserta->bukti_bayar))) {
            unlink(public_path($peserta->bukti_bayar));
        }

        $peserta->delete();
        return redirect()->back()->with('success', 'Peserta berhasil dihapus dari daftar kegiatan.');
    }

    // ==========================================
    // FUNGSI VERIFIKASI & KIRIM EMAIL
    // ==========================================
  // ==========================================
    // FUNGSI VERIFIKASI & KIRIM EMAIL
    // ==========================================
    public function approve($kegiatan_id, $id)
    {
        $peserta = KegiatanPeserta::with('kegiatan')->findOrFail($id);
        $kegiatan = $peserta->kegiatan;
        
        // Tentukan email tujuan (prioritas email plataran sehat, jika kosong pakai email PJ)
        $emailTujuan = $peserta->email_plataran_sehat ?? $peserta->email_pj;

        if ($emailTujuan) {
         try {
                // Coba kirim email terlebih dahulu
                Mail::send([], [], function ($message) use ($peserta, $kegiatan, $emailTujuan) {
                    $tanggal = \Carbon\Carbon::parse($kegiatan->tanggal_mulai)->translatedFormat('d F Y, H:i');
                    
                    $htmlContent = "
                        <div style='font-family: sans-serif; color: #333;'>
                            <h2 style='color: #7c1316;'>Pendaftaran Berhasil Diverifikasi</h2>
                            <p>Halo, <strong>{$peserta->nama_lengkap_gelar}</strong>,</p>
                            <p>Selamat! Pendaftaran Anda untuk mengikuti pelatihan <strong>{$kegiatan->nama_kegiatan}</strong> telah disetujui dan diverifikasi oleh Panitia Diklat RSUD Simpang Lima Gumul.</p>
                            
                            <div style='background: #f8fafc; padding: 15px; border-radius: 8px; border: 1px solid #e2e8f0; margin-top: 15px;'>
                                <h4 style='margin-top: 0;'>Detail Pelaksanaan:</h4>
                                <ul style='margin-bottom: 0;'>
                                    <li><strong>Waktu:</strong> {$tanggal} WIB</li>
                                    <li><strong>Lokasi/Platform:</strong> {$kegiatan->platform}</li>
                                    <li><strong>Metode:</strong> {$peserta->metode_pelatihan}</li>
                                </ul>
                            </div>
                            
                            <p style='margin-top: 20px;'>Mohon persiapkan diri Anda dan pastikan hadir tepat waktu sesuai jadwal yang telah ditentukan. Terima kasih atas partisipasinya.</p>
                            <br>
                            <p>Salam hangat,<br><strong>Panitia Diklat RSUD Simpang Lima Gumul Kediri</strong></p>
                        </div>
                    ";

                    // PERUBAHAN ADA DI BARIS INI
                    $message->to($emailTujuan)
                            ->subject('Verifikasi Pendaftaran Pelatihan: ' . $kegiatan->nama_kegiatan)
                            ->setBody($htmlContent, 'text/html'); // <-- Menggunakan setBody untuk Laravel 8/PHP 7.4
                });

                // JIKA EMAIL BERHASIL TERKIRIM, BARU UBAH STATUS
                $peserta->update(['status_pendaftaran' => 'terverifikasi']);

            } catch (\Exception $e) {
                // Tampilkan pesan error ASLI dari Laravel untuk debugging
                return redirect()->back()->with('error', 'Gagal kirim email. Pesan Server: ' . $e->getMessage());
            } 
        } else {
            // Jika tidak ada data email sama sekali di form, langsung setujui tanpa kirim email
            $peserta->update(['status_pendaftaran' => 'terverifikasi']);
            return redirect()->back()->with('success', 'Peserta berhasil diverifikasi (Tanpa notifikasi email karena email kosong).');
        }

        return redirect()->back()->with('success', 'Peserta berhasil diverifikasi & Notifikasi Email telah dikirim!');
    }

    // ==========================================
    // FUNGSI BATAL APPROVE
    // ==========================================
    public function batalApprove($kegiatan_id, $id)
    {
        $peserta = KegiatanPeserta::findOrFail($id);
        
        // Ubah status kembali menjadi pending
        $peserta->update(['status_pendaftaran' => 'pending']);
        
        return redirect()->back()->with('success', 'Verifikasi berhasil dibatalkan. Status peserta kembali menjadi Pending.');
    }

    // ==========================================
    // FUNGSI IMPORT EXCEL (Ditingkatkan)
    // ==========================================
    public function import(Request $request, $kegiatan_id)
    {
        $request->validate(['data' => 'required|array']);
        $kegiatan = Kegiatan::findOrFail($kegiatan_id);
        $inserted = 0;

        foreach ($request->data as $row) {
            $instansi_id = null;
            $ruangan_id = null;

            if (!empty($row['instansi'])) {
                $instansi = MasterInstansi::firstOrCreate(['nama_instansi' => trim($row['instansi'])]);
                $instansi_id = $instansi->id;

                if (!empty($row['ruangan'])) {
                    $ruangan = MasterRuangan::firstOrCreate([
                        'instansi_id' => $instansi->id,
                        'nama_ruangan' => trim($row['ruangan'])
                    ]);
                    $ruangan_id = $ruangan->id;
                }
            }

            // Jika diimport oleh admin, status otomatis terverifikasi
            KegiatanPeserta::create([
                'kegiatan_id' => $kegiatan->id,
                'nama_lengkap_gelar' => trim($row['nama_lengkap']),
                'profesi' => trim($row['profesi'] ?? '-'),
                'instansi_id' => $instansi_id,
                'ruangan_id' => $ruangan_id,
                
                // Jika file excel punya kolom tambahan (opsional) akan ditangkap
                'nik' => $row['nik'] ?? null,
                'no_hp_peserta' => $row['no_hp'] ?? null,
                'email_plataran_sehat' => $row['email'] ?? null,
                'jabatan' => $row['jabatan'] ?? null,
                
                'status_pendaftaran' => 'terverifikasi' 
            ]);
            $inserted++;
        }

        return response()->json(['success' => true, 'message' => "Berhasil mengimpor $inserted peserta baru secara massal."]);
    }

    // ==========================================
    // FUNGSI EXPORT CSV (Fallback Server-side)
    // ==========================================
    public function export($kegiatan_id)
    {
        $kegiatan = Kegiatan::findOrFail($kegiatan_id);
        $peserta = KegiatanPeserta::with(['instansi', 'ruangan', 'absensiAktual'])->where('kegiatan_id', $kegiatan_id)->get();

        $fileName = "Data_Peserta_" . str_replace(' ', '_', $kegiatan->nama_kegiatan) . ".csv";
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate",
            "Expires" => "0"
        );

        $columns = ['No', 'Nama Lengkap', 'Profesi', 'Instansi', 'Ruangan', 'Status Pendaftaran', 'Status Kehadiran', 'Link Sertifikat Publik'];

        $callback = function() use($peserta, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            $no = 1;
            foreach ($peserta as $p) {
                $kehadiran = 'Belum Absen';
                $linkSertifikat = '-';

                if ($p->absensiAktual) {
                    if ($p->absensiAktual->status_kehadiran == 'hadir') {
                        $kehadiran = 'Hadir';
                        $hash = md5($p->id . $this->secretKey);
                        $linkSertifikat = route('public.sertifikat.peserta', [$p->id, $hash]);
                    } else {
                        $kehadiran = 'Izin/Sakit';
                    }
                }

                fputcsv($file, [
                    $no++, 
                    $p->nama_lengkap_gelar, 
                    $p->profesi ?? '-', 
                    $p->instansi->nama_instansi ?? 'Internal RS',
                    $p->ruangan->nama_ruangan ?? '-', 
                    ucfirst($p->status_pendaftaran ?? 'terverifikasi'),
                    $kehadiran, 
                    $linkSertifikat
                ]);
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }

    // ==========================================
    // FUNGSI RESET ABSENSI
    // ==========================================
    public function resetAbsen($kegiatan_id, $id)
    {
        $absensis = \App\Models\AbsensiKegiatan::where('kegiatan_id', $kegiatan_id)
                    ->where('kegiatan_peserta_id', $id)
                    ->get();

        if ($absensis->count() > 0) {
            foreach ($absensis as $absen) {
                if ($absen->foto_bukti && file_exists(public_path($absen->foto_bukti))) {
                    unlink(public_path($absen->foto_bukti));
                }
                $absen->delete();
            }
        }

        return back()->with('success', 'Data absensi peserta berhasil direset.');
    }

    public function toggleAbsen(Request $request, $kegiatan_id)
    {
        $kegiatan = Kegiatan::findOrFail($kegiatan_id);
        $kegiatan->update(['status_absen' => $request->status_absen]);
        return redirect()->back()->with('success', 'Status Form Absensi berhasil diubah!');
    }

    public function publicSertifikat($id, $hash)
    {
        if ($hash !== md5($id . $this->secretKey)) abort(404, 'Link Sertifikat Tidak Valid');
        $peserta = KegiatanPeserta::with(['kegiatan', 'absensiAktual'])->findOrFail($id);
        if (!$peserta->absensiAktual || $peserta->absensiAktual->status_kehadiran != 'hadir') {
            abort(403, 'Sertifikat tidak tersedia karena Anda tercatat tidak hadir pada kegiatan ini.');
        }
        return view('public.kegiatan.sertifikat', compact('peserta'));
    }

    // ==========================================
    // FUNGSI PENDAFTARAN PUBLIK (EKSTERNAL)
    // ==========================================
    public function publicDaftarForm($id)
    {
        $kegiatan = Kegiatan::findOrFail($id);
        return view('public.kegiatan.daftar', compact('kegiatan'));
    }

    public function submitDaftarPublic(Request $request, $id)
    {
        $kegiatan = Kegiatan::findOrFail($id);

        $request->validate([
            'nama_instansi' => 'required|string|max:255',
            'email_pj' => 'required|email',
            'no_hp_pj' => 'required|string',
            'alamat_instansi' => 'required|string',
            
            'nama_lengkap_gelar' => 'required|string',
            'tempat_lahir' => 'required|string',
            'tanggal_lahir' => 'required|date',
            'nik' => 'required|string|min:16|max:16',
            'jabatan' => 'required|string',
            'email_plataran_sehat' => 'required|email',
            'no_hp_peserta' => 'required|string',
            'profesi' => 'required|string',
            'pendidikan_terakhir' => 'required|string',
            'status_pegawai' => 'required|string',
            'metode_pelatihan' => 'required|string',
            'ukuran_kaos' => 'required|string',
            'punya_akun_lms' => 'required',
            'komitmen' => 'required|in:ya', // Wajib disetujui
            'bukti_bayar' => 'required|image|mimes:jpeg,png,jpg|max:5120', // Maks 5MB
        ], [
            'komitmen.in' => 'Anda harus menyetujui komitmen peserta untuk mendaftar.',
            'nik.min' => 'NIK harus berjumlah 16 digit.',
            'bukti_bayar.required' => 'Bukti pembayaran wajib dilampirkan.'
        ]);

        // Cek & Buat Instansi Otomatis jika belum ada
        $instansi = MasterInstansi::firstOrCreate([
            'nama_instansi' => trim($request->nama_instansi)
        ]);

        // Upload Bukti Bayar
        $fotoPath = null;
        if ($request->hasFile('bukti_bayar')) {
            $file = $request->file('bukti_bayar');
            $filename = 'BUKTI_BAYAR_' . time() . '_' . Str::random(5) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/kegiatan_pendaftaran'), $filename);
            $fotoPath = 'uploads/kegiatan_pendaftaran/' . $filename;
        }

        // Simpan Data Peserta
        KegiatanPeserta::create([
            'kegiatan_id' => $kegiatan->id,
            'instansi_id' => $instansi->id,
            
            'email_pj' => $request->email_pj,
            'no_hp_pj' => $request->no_hp_pj,
            'alamat_instansi' => $request->alamat_instansi,
            
            'nama_lengkap_gelar' => $request->nama_lengkap_gelar,
            'tempat_lahir' => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'nik' => $request->nik,
            'jabatan' => $request->jabatan,
            'profesi' => $request->profesi,
            'email_plataran_sehat' => $request->email_plataran_sehat,
            'no_hp_peserta' => $request->no_hp_peserta,
            'pendidikan_terakhir' => $request->pendidikan_terakhir,
            'status_pegawai' => $request->status_pegawai,
            'nip' => $request->nip,
            'pangkat_golongan' => $request->pangkat_golongan,
            'metode_pelatihan' => $request->metode_pelatihan,
            'ukuran_kaos' => $request->ukuran_kaos,
            'punya_akun_lms' => $request->punya_akun_lms == 'ya' ? true : false,
            
            'bukti_bayar' => $fotoPath,
            'status_pendaftaran' => 'pending' // Default status menunggu ACC admin
        ]);

        return redirect()->back()->with('success', 'Pendaftaran berhasil dikirim! Silakan tunggu konfirmasi dari panitia RSUD Simpang Lima Gumul melalui Email atau WhatsApp Anda.');
    }
}