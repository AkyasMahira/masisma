<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\MahasiswaPenelitian;
use App\Models\Mou;
use App\Models\Pelatihan;
use App\Models\Pengajuan;
use App\Models\PraPenelitian;
use App\Models\PraPenelitianAnggota;
use App\Models\Ruangan;
use App\Models\RuanganKetersediaan;
use App\Models\Absensi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PublicController extends Controller
{
  public function landing()
{
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    // DATA CHART
    $mahasiswa   = Mahasiswa::count();
    $pengajuan   = Pengajuan::count();
    $pelatihan   = Pelatihan::count();
    $ruangan     = Ruangan::count();
    $mitra       = Mou::count();

    // STATUS PENGAJUAN (untuk chart 2 atau statistik lain)
    $statusPengajuan = Pengajuan::select('status', DB::raw('count(*) as total'))
        ->groupBy('status')
        ->pluck('total', 'status');

    return view('landing', [
        'chart' => [
            'mahasiswa' => $mahasiswa,
            'pengajuan' => $pengajuan,
            'pelatihan' => $pelatihan,
            'ruangan'   => $ruangan,
            'mitra'     => $mitra,
        ],
        'statusPengajuan' => $statusPengajuan,
    ]);
}


    public function chatbot(Request $request)
    {
        $data = $request->validate([
            'message' => 'required|string|max:500',
        ]);

        // normalisasi text
        $message = mb_strtolower(trim($data['message']));

        $metrics = $this->collectMetrics();
        $intent  = $this->detectIntent($message);
        $reply   = $this->respondForIntent($intent, $message, $metrics);

        return response()->json([
            'reply'       => $reply,
            'intent'      => $intent,
            'metrics'     => $metrics,
            'suggestions' => [
                'Ringkasan data',
                'Status pengajuan',
                'Jumlah pelatihan',
                'Ruangan tersedia',
                'Absensi hari ini',
            ],
        ]);
    }

    /* -----------------------------------------------------------------
     |  METRICS
     | -----------------------------------------------------------------
     */

    protected function collectMetrics(): array
    {
        return [
            'mahasiswa'              => $this->safeCount(Mahasiswa::class),
            'pelatihan'              => $this->safeCount(Pelatihan::class),
            'ruangan'                => $this->safeCount(Ruangan::class),
            'pengajuan'              => $this->safeCount(Pengajuan::class),
            'mitra'                  => $this->safeCount(Mou::class),
            'pengajuan_status'       => $this->groupStatus(),

            // tambahan
            'mahasiswa_penelitian'   => $this->safeCount(MahasiswaPenelitian::class),
            'pra_penelitian'         => $this->safeCount(PraPenelitian::class),
            'pra_penelitian_anggota' => $this->safeCount(PraPenelitianAnggota::class),
            'user'                   => $this->safeCount(User::class),
            'ruangan_kosong'         => $this->safeCountWhere(RuanganKetersediaan::class, 'status', 'tersedia'),
            'ruangan_penuh'          => $this->safeCountWhere(RuanganKetersediaan::class, 'status', 'penuh'),
            'absensi_hari_ini'       => $this->countAbsensiToday(),
        ];
    }

    protected function baseSummary(array $metrics): string
    {
        return sprintf(
            'Ringkasan Sindikat: %s mahasiswa, %s pengajuan, %s pelatihan, %s ruangan, %s mitra.',
            $this->describeMetric($metrics['mahasiswa'] ?? null),
            $this->describeMetric($metrics['pengajuan'] ?? null),
            $this->describeMetric($metrics['pelatihan'] ?? null),
            $this->describeMetric($metrics['ruangan'] ?? null),
            $this->describeMetric($metrics['mitra'] ?? null)
        );
    }

    /* -----------------------------------------------------------------
     |  NLU OFFLINE - INTENT & KEYWORD DICTIONARY
     | -----------------------------------------------------------------
     */

    /**
     * Kamus intent -> daftar keyword.
     * Satu intent bisa cover banyak pertanyaan.
     */
    protected function intentDictionary(): array
    {
        return [
            'greeting' => [
                'hai', 'halo', 'hallo', 'assalam', 'assalamu', 'selamat pagi',
                'selamat siang', 'selamat sore', 'selamat malam'
            ],
            'help' => [
                'bisa apa', 'fitur apa', 'panduan', 'cara pakai', 'bantuan', 'help'
            ],
            'about_system' => [
                'apa itu sindikat', 'sindikat itu apa', 'tentang sindikat',
                'fungsi sindikat', 'tentang sistem'
            ],
            'mahasiswa' => [
                'jumlah mahasiswa', 'total mahasiswa', 'data mahasiswa',
                'mahasiswa berapa', 'peserta berapa', 'jumlah peserta'
            ],
            'mahasiswa_penelitian' => [
                'mahasiswa penelitian', 'peserta penelitian', 'penelitian klinik',
                'jumlah penelitian', 'data penelitian'
            ],
            'pra_penelitian' => [
                'pra penelitian', 'pra-penelitian', 'jumlah pra penelitian',
                'data pra penelitian'
            ],
            'pelatihan' => [
                'jumlah pelatihan', 'data pelatihan', 'pelatihan aktif',
                'pelatihan tersedia'
            ],
            'pelatihan_peserta' => [
                'peserta pelatihan', 'jumlah peserta pelatihan'
            ],
            'ruangan' => [
                'data ruangan', 'ruangan berapa', 'jumlah ruangan', 'ruang praktik',
                'ruang magang'
            ],
            'ruangan_ketersediaan' => [
                'ruangan kosong', 'ruangan tersedia', 'ketersediaan ruangan',
                'ruangan penuh', 'ruang penuh'
            ],
            'pengajuan_general' => [
                'status pengajuan', 'data pengajuan', 'pengajuan berapa',
                'jumlah pengajuan'
            ],
            'pengajuan_approved' => [
                'pengajuan disetujui', 'pengajuan diterima', 'approve',
                'pengajuan yang sudah disetujui'
            ],
            'pengajuan_rejected' => [
                'pengajuan ditolak', 'reject', 'pengajuan yang ditolak'
            ],
            'pengajuan_pending' => [
                'pengajuan pending', 'pengajuan menunggu', 'pengajuan belum diproses',
                'waiting'
            ],
            'pengajuan_user' => [
                'status pengajuan saya', 'pengajuan saya sampai mana', 'cek pengajuan saya'
            ],
            'mitra' => [
                'data mitra', 'jumlah mitra', 'jumlah mou', 'mou aktif',
                'universitas terdaftar', 'kampus terdaftar'
            ],
            'mitra_not_registered' => [
                'universitas saya belum ada', 'kampus saya belum ada', 'mitra belum terdaftar'
            ],
            'absensi_today' => [
                'absensi hari ini', 'absen hari ini', 'jumlah absensi',
                'data absensi', 'absen peserta'
            ],
            'user' => [
                'jumlah user', 'data user', 'pengguna terdaftar', 'akun terdaftar',
                'jumlah admin', 'data admin'
            ],
            'login_issue' => [
                'gagal login', 'tidak bisa login', 'login error', 'lupa password',
                'reset password'
            ],
            'registration' => [
                'cara daftar', 'bagaimana daftar', 'mendaftar magang',
                'pendaftaran magang', 'pendaftaran penelitian', 'cara mendaftar'
            ],
            'requirements' => [
                'syarat pengajuan', 'persyaratan', 'berkas apa saja',
                'dokumen apa saja', 'syarat apa saja'
            ],
            'edit_data' => [
                'edit data', 'ubah data', 'perbaiki data', 'koreksi data'
            ],
            'program_difference' => [
                'beda magang dan penelitian', 'magang atau penelitian',
                'magang dan pelatihan', 'perbedaan program'
            ],
            'service_hours' => [
                'jam layanan', 'jadwal layanan', 'jam operasional', 'buka jam berapa'
            ],
            'contact_admin' => [
                'kontak admin', 'hubungi admin', 'contact admin',
                'wa admin', 'no admin'
            ],
            'logout' => [
                'logout', 'keluar aplikasi', 'keluar sistem'
            ],
            'summary_request' => [
                'ringkas', 'ringkasan', 'summary', 'ringkasan data'
            ],
        ];
    }

    /**
     * Deteksi intent berdasarkan keyword yang match.
     * Skor = total panjang keyword yang ketemu. Intent skor terbesar dipakai.
     */
    protected function detectIntent(string $message): string
    {
        $dictionary = $this->intentDictionary();

        $bestIntent = 'summary_request'; // default kalau ga jelas → kasih ringkasan
        $bestScore  = 0;

        foreach ($dictionary as $intent => $keywords) {
            $score = 0;

            foreach ($keywords as $keyword) {
                if ($keyword === '') {
                    continue;
                }

                if (mb_strpos($message, $keyword) !== false) {
                    // semakin spesifik (keyword panjang), skor makin besar
                    $score += mb_strlen($keyword);
                }
            }

            if ($score > $bestScore) {
                $bestScore  = $score;
                $bestIntent = $intent;
            }
        }

        return $bestIntent;
    }

    /**
     * Bangun jawaban berdasarkan intent terdeteksi.
     */
    protected function respondForIntent(string $intent, string $message, array $metrics): string
    {
        $summary = $this->baseSummary($metrics);

        switch ($intent) {
            case 'greeting':
                return 'Halo 👋, ini asisten Sindikat. '.$summary.' Kamu bisa tanya: "status pengajuan", "jumlah pelatihan", atau "ruangan tersedia".';

            case 'help':
                return 'Aku bisa bantu ringkas data, jumlah mahasiswa/pelatihan/pengajuan, status pengajuan, ketersediaan ruangan, sampai info absensi. Contoh: "jumlah mahasiswa", "pengajuan diterima berapa", "absensi hari ini".';

            case 'about_system':
                return 'Sindikat adalah sistem manajemen magang, penelitian, pelatihan, MOU, dan ruangan di lingkungan rumah sakit. Tujuannya mempermudah peserta dan admin dalam pengajuan, pemantauan status, dan pengelolaan data.';

            case 'mahasiswa':
                return 'Total mahasiswa/peserta terdaftar: '.$this->describeMetric($metrics['mahasiswa'] ?? null).'. Detail per peserta bisa dilihat di dashboard.';

            case 'mahasiswa_penelitian':
                return 'Total mahasiswa penelitian: '.$this->describeMetric($metrics['mahasiswa_penelitian'] ?? null)
                    .'. Termasuk anggota pra-penelitian: '.$this->describeMetric($metrics['pra_penelitian_anggota'] ?? null).'.';

            case 'pra_penelitian':
                return 'Total pra-penelitian yang tercatat: '.$this->describeMetric($metrics['pra_penelitian'] ?? null).'. Detail dokumen dan status bisa dicek di menu pra-penelitian.';

            case 'pelatihan':
                return 'Data pelatihan aktif: '.$this->describeMetric($metrics['pelatihan'] ?? null).'. Peserta dapat mengedit data melalui tautan publik atau dashboard peserta (jika diizinkan).';

            case 'pelatihan_peserta':
                $totalPeserta = $this->safeCount(MahasiswaPenelitian::class);
                return 'Perkiraan total peserta pelatihan/penelitian yang tercatat: '.$this->describeMetric($totalPeserta).'.';

            case 'ruangan':
                return 'Ruang/layanan terdata di sistem: '.$this->describeMetric($metrics['ruangan'] ?? null).'. Detail penempatan dan kapasitas ada di menu ruangan.';

            case 'ruangan_ketersediaan':
                $kosong = $this->describeMetric($metrics['ruangan_kosong'] ?? null);
                $penuh  = $this->describeMetric($metrics['ruangan_penuh'] ?? null);
                return "Ketersediaan ruangan saat ini: $kosong ruangan tersedia, $penuh ruangan penuh. Jadwal dan detail bisa dicek di menu ruangan.";

            case 'pengajuan_general':
                $status = $this->formatStatus($metrics['pengajuan_status'] ?? []);
                return 'Total pengajuan: '.$this->describeMetric($metrics['pengajuan'] ?? null).'. '.$status;

            case 'pengajuan_approved':
                $approved = Pengajuan::where('status', 'approve')->count();
                return 'Pengajuan yang sudah disetujui: '.$this->describeMetric($approved).'.';

            case 'pengajuan_rejected':
                $rejected = Pengajuan::where('status', 'reject')->count();
                return 'Pengajuan yang ditolak: '.$this->describeMetric($rejected).'.';

            case 'pengajuan_pending':
                $pending = Pengajuan::where('status', 'waiting')->count();
                return 'Pengajuan berstatus menunggu/pending: '.$this->describeMetric($pending).'. Silakan cek detail di dashboard.';

            case 'pengajuan_user':
                return 'Untuk status pengajuan pribadi, silakan login lalu cek di menu pengajuan pada dashboard. Sistem akan menampilkan status detail per permohonan milikmu.';

            case 'mitra':
                return 'Mitra/Universitas terdaftar: '.$this->describeMetric($metrics['mitra'] ?? null).'. Daftar ini digunakan saat peserta memilih universitas/instansi pada formulir.';

            case 'mitra_not_registered':
                return 'Jika universitas/instansi belum ada di daftar mitra, peserta dapat menghubungi admin untuk proses penambahan MOU atau mengikuti kebijakan sementara yang ditentukan admin.';

            case 'absensi_today':
                $today = date('Y-m-d');
                $totalAbsensi = $this->describeMetric($metrics['absensi_hari_ini'] ?? null);
                return "Total absensi yang tercatat hari ini ($today): $totalAbsensi. Detail kehadiran dapat dicek di menu absensi.";

            case 'user':
                return 'Total pengguna terdaftar di sistem: '.$this->describeMetric($metrics['user'] ?? null).'. Hak akses dibedakan berdasarkan role (admin, operator, peserta, dsb).';

            case 'login_issue':
                return 'Jika tidak bisa login atau lupa password, gunakan fitur reset password (jika tersedia) atau hubungi admin Sindikat untuk bantuan reset akun. Pastikan juga NIK/email yang digunakan sudah terdaftar.';

            case 'registration':
                return 'Untuk mendaftar, buka halaman pendaftaran di landing page, pilih jenis pengajuan (magang/penelitian/pelatihan), isi data dengan lengkap, lalu unggah berkas yang diminta. Setelah tersimpan, pantau status di dashboard.';

            case 'requirements':
                return 'Syarat dan berkas pengajuan berbeda untuk tiap jenis program. Detail persyaratan terbaru biasanya tercantum di formulir pengajuan (misalnya surat pengantar kampus, proposal, KTP, dsb). Silakan buka formulir terkait untuk melihat daftar lengkapnya.';

            case 'edit_data':
                return 'Sebagian data peserta bisa diedit melalui dashboard atau tautan publik selama pengajuan belum dikunci/diapprove admin. Jika data sudah terkunci, peserta perlu menghubungi admin untuk koreksi data.';

            case 'program_difference':
                return 'Secara umum: magang fokus praktik kerja, penelitian fokus pengumpulan data ilmiah, sedangkan pelatihan fokus peningkatan kompetensi melalui program pelatihan. Di Sindikat, ketiganya diatur melalui jenis pengajuan yang berbeda di sistem.';

            case 'service_hours':
                return 'Jam layanan administrasi mengikuti jam kerja rumah sakit. Untuk jam layanan terbaru dan hari tertentu, silakan lihat pengumuman resmi atau hubungi admin Sindikat.';

            case 'contact_admin':
                return 'Silakan hubungi admin Sindikat melalui akun admin di dashboard atau kanal komunikasi resmi RS (WhatsApp/telepon) yang tercantum di pengumuman atau footer aplikasi.';

            case 'logout':
                return 'Untuk keluar dari sistem, gunakan tombol logout di pojok kanan atas setelah login. Setelah logout, sesi akun berakhir dan kamu perlu login lagi untuk mengakses data.';

            case 'summary_request':
            default:
                return $summary.' Kamu bisa lanjut tanya: "status pengajuan", "ruangan kosong", "absensi hari ini", atau "mou aktif berapa".';
        }
    }

    /* -----------------------------------------------------------------
     |  HELPER
     | -----------------------------------------------------------------
     */

    protected function describeMetric($value): string
    {
        if ($value === null) {
            return 'belum tersedia';
        }

        return (string) $value;
    }

    protected function formatStatus($statusCollection): string
    {
        if (empty($statusCollection)) {
            return 'Belum ada rincian status pengajuan.';
        }

        $statusArray = is_array($statusCollection) ? $statusCollection : $statusCollection->toArray();
        $parts = [];

        foreach ($statusArray as $status => $total) {
            $parts[] = ($status ?: 'tanpa status').': '.$total;
        }

        return 'Rincian status pengajuan - '.implode(', ', $parts).'.';
    }

    protected function safeCount(string $model): ?int
    {
        try {
            return $model::count();
        } catch (\Throwable $e) {
            return null;
        }
    }

    protected function safeCountWhere(string $model, string $column, $value): ?int
    {
        try {
            return $model::where($column, $value)->count();
        } catch (\Throwable $e) {
            return null;
        }
    }

    protected function countAbsensiToday(): ?int
    {
        try {
            $today = date('Y-m-d');
            return Absensi::whereDate('created_at', $today)->count();
        } catch (\Throwable $e) {
            return null;
        }
    }

    protected function groupStatus()
    {
        try {
            return Pengajuan::select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->pluck('total', 'status');
        } catch (\Throwable $e) {
            return [];
        }
    }
}
