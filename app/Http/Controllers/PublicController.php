<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\MahasiswaPenelitian;
use App\Models\Mou;
use App\Models\Kegiatan;
use App\Models\Pengajuan;
use App\Models\PraPenelitian;
use App\Models\PraPenelitianAnggota;
use App\Models\Ruangan;
use App\Models\RuanganKetersediaan;
use App\Models\Absensi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PublicController extends Controller
{
    public function landing()
    {
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }

        $mahasiswa  = Mahasiswa::count();
        $pengajuan  = Mou::count();
        $pelatihan  = Kegiatan::count(); 
        $ruangan    = Ruangan::count();
        $mitra      = Mou::count(); 

        $statusPengajuan = Pengajuan::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        // AMBIL DATA PELATIHAN YANG SEDANG TERSEDIA (Berjalan / Akan Datang)
        $kegiatanTersedia = Kegiatan::with('penyelenggara')
            ->whereDate('tanggal_selesai', '>=', now())
            ->orderBy('tanggal_mulai', 'asc')
            ->get();

        return view('landing', [
            'chart' => [
                'mahasiswa' => $mahasiswa,
                'pengajuan' => $pengajuan,
                'pelatihan' => $pelatihan,
                'ruangan'   => $ruangan,
                'mitra'     => $mitra,
            ],
            'statusPengajuan' => $statusPengajuan,
            'kegiatanTersedia' => $kegiatanTersedia
        ]);
    }

  public function chatbot(Request $request)
    {
        $data =$request->validate([
            'message' => 'required|string|max:500',
            'mode'    => 'nullable|string',
            'nama'    => 'nullable|string',
            'profesi' => 'nullable|string',
        ]);

        $message = mb_strtolower(trim($data['message']));
        $mode =$data['mode'] ?? 'general';

        // Tetap gunakan algoritma lokal untuk pencarian/rekomendasi pelatihan agar URL dan ID akurat
        if ($mode === 'pelatihan') {
            return $this->handleRekomendasiPelatihan($message, $data['nama'] ?? '',$data['profesi'] ?? '');
        }

        // =========================================================================
        // INTEGRASI GOOGLE GEMINI API (MODE GENERAL)
        // =========================================================================
        $metrics = $this->collectMetrics();$summary = $this->baseSummary($metrics);

        // System Prompt: Memberikan kepribadian, instruksi, dan data real-time kepada AI
        $systemPrompt = "Kamu adalah Sindi, asisten virtual ramah, disiplin, dan profesional dari RSUD Simpang Lima Gumul (SLG) Kediri. Tugasmu membantu user terkait Sistem Informasi Pendidikan dan Pelatihan Diklat (Sindikat).
        Data real-time sistem saat ini: {$summary}
        Aturan penelitian: Pengajuan 5-14 hari kerja, data wajib de-identifikasi, dilarang menyebarkan data pasien.
        Aturan magang: Wajib orientasi K3 & PPI, dilarang tindakan tanpa supervisi.

        Instruksi menjawab:
        1. Jawab singkat, hangat, dan langsung pada intinya.
        2. Jangan gunakan format Markdown (seperti * atau **). Gunakan tag HTML dasar (<b>, <i>, <br>) untuk pemformatan agar sesuai dengan tampilan frontend web.
        3. Jika ditanya informasi spesifik yang tidak ada di ringkasan (seperti status pengajuan pribadi), arahkan pengguna untuk login dan mengecek di dashboard.";

   try {
            $response = \Illuminate\Support\Facades\Http::withoutVerifying()->withHeaders([
                'Content-Type' => 'application/json',
            ])->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-3.8-flash:generateContent?key=' . env('GEMINI_API_KEY'), [
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [
                            ['text' => $systemPrompt . "\n\nPertanyaan user: " . $message]
                        ]
                    ]
                ]
            ]);

            if ($response->successful()) {
                $result = $response->json();
                if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
                    $reply = str_replace(['```html', '```'], '', $result['candidates'][0]['content']['parts'][0]['text']);
                } else {
                    $reply = "Maaf, Sindi agak kesulitan memproses jawaban dari pusat. Coba tanya hal lain ya!";
                }
            } else {
                // Catat log diam-diam di background
                \Illuminate\Support\Facades\Log::error('Gemini API Reject: ' . $response->body());
                
                // Pesan ramah jika server Google sedang 503 (High Demand)
                $reply = "Maaf Kak, jalur komunikasi ke server AI pusat sedang antre penuh. Silakan coba tanyakan lagi dalam beberapa menit ya! 🙏";
            }

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gemini Connection Error: ' . $e->getMessage());
            $reply = "Koneksi ke server terputus. Silakan cek jaringan Kakak atau coba lagi nanti.";
        }

        return response()->json([
            'type'        => 'text',
            'reply'       => trim($reply),
            'intent'      => 'ai_generated',
            'suggestions' => [
                'Ringkasan data sistem',
                'Aturan de-identifikasi pasien',
                'Berapa lama proses izin penelitian?',
                'Syarat magang',
            ],
        ]);
    }

    // =========================================================================
    // MESIN PENCARI REKOMENDASI PELATIHAN (PSEUDO-AI) - AMAN UNTUK PHP 7.x
    // =========================================================================
 // =========================================================================
    // MESIN PENCARI REKOMENDASI PELATIHAN (PSEUDO-AI)
    // =========================================================================
    private function handleRekomendasiPelatihan($message, $nama, $profesi)
    {
        // 1. Ambil semua pelatihan EKSTERNAL yang masih aktif
        $pelatihanEksternal = Kegiatan::with(['tujuan', 'kompetensi'])
            ->where('jenis_kegiatan', 'eksternal')
            ->whereDate('tanggal_selesai', '>=', now())
            ->get();

        if ($pelatihanEksternal->isEmpty()) {
            return response()->json([
                'type' => 'text',
                'reply' => "Mohon maaf Kak <b>{$nama}</b>, saat ini belum ada jadwal pelatihan eksternal yang tersedia. Silakan cek secara berkala ya!",
            ]);
        }

        // 2. Jika user hanya menyapa awal di mode pelatihan (Pesan Pertama)
        if (in_array($message, ['hai', 'halo', 'mulai', 'cari pelatihan'])) {
            
            // Ekstrak semua keahlian & kompetensi dari pelatihan yang aktif untuk dijadikan suggestion
            $listKeahlian = [];
            foreach ($pelatihanEksternal as $keg) {
                // Ambil dari array JSON keahlian
                if (is_array($keg->keahlian)) {
                    $listKeahlian = array_merge($listKeahlian, $keg->keahlian);
                }
                // Ambil dari relasi tabel master kompetensi
                foreach ($keg->kompetensi as $komp) {
                    $listKeahlian[] = $komp->nama_kompetensi;
                }
            }
            
            // Bersihkan data ganda, filter yang kosong, dan acak urutannya
            $listKeahlian = array_unique(array_filter($listKeahlian));
            shuffle($listKeahlian); 
            
            // Ambil maksimal 4 keahlian untuk ditampilkan sebagai tombol
            $sampleKeahlian = array_slice($listKeahlian, 0, 4);
            $suggestions = array_map(function($k) { 
                return "Belajar " . $k; 
            }, $sampleKeahlian);
            
            // Tambahkan tombol opsi lihat semua
            $suggestions[] = 'Tampilkan semua pelatihan';

            return response()->json([
                'type'  => 'text',
                'reply' => "Halo Kak <b>{$nama}</b> ({$profesi})! ✨<br><br>Untuk memberikan rekomendasi yang paling pas, <b>silakan ketikkan keahlian, kompetensi, atau bidang spesifik yang ingin Kakak kuasai</b>.<br><br>Pilih dari topik yang sedang tersedia di bawah ini, atau ketik kata kunci Kakak sendiri:",
                'suggestions' => array_values($suggestions)
            ]);
        }

        // 3. Jika user minta semua pelatihan
        if (str_contains($message, 'semua pelatihan') || str_contains($message, 'tampilkan semua')) {
            return $this->formatRecommendationResponse($pelatihanEksternal->take(5), $nama, $profesi, "Tentu! Ini daftar seluruh pelatihan yang tersedia saat ini:");
        }

        // 4. LOGIKA SCORING (Pencocokan Kata Kunci AI)
        // Pecah pesan user menjadi kata-kata (token)
        $keywords = explode(' ', preg_replace('/[^a-z0-9]+/i', ' ', $message));
        $keywords = array_filter($keywords, function($w) {
            return strlen($w) > 2; // Abaikan kata hubung pendek (di, ke, dari)
        });

        $scoredPelatihan = [];

        foreach ($pelatihanEksternal as $keg) {
            $score = 0;
            $textToSearch = strtolower($keg->nama_kegiatan . ' ' . $keg->deskripsi);
            
            // Tambahkan data keahlian JSON ke teks pencarian
            if (is_array($keg->keahlian)) {
                $textToSearch .= ' ' . strtolower(implode(' ', $keg->keahlian));
            }

            // Tambahkan Tujuan ke teks pencarian
            foreach ($keg->tujuan as $t) {
                $textToSearch .= ' ' . strtolower($t->tujuan);
            }

            // Tambahkan Kompetensi & Indikator ke teks pencarian
            foreach ($keg->kompetensi as $k) {
                $textToSearch .= ' ' . strtolower($k->nama_kompetensi . ' ' . $k->pivot->indikator_keberhasilan);
            }

            // Hitung skor kemiripan berdasarkan kata kunci
            foreach ($keywords as $kw) {
                if (str_contains($textToSearch, $kw)) {
                    $score += 1;
                }
            }

            // Jika ada kecocokan, masukkan ke daftar rekomendasi
            if ($score > 0) {
                $keg->match_score = $score;
                $scoredPelatihan[] = $keg;
            }
        }

        // 5. Urutkan berdasarkan skor tertinggi
        usort($scoredPelatihan, function($a, $b) {
            return $b->match_score <=> $a->match_score;
        });
        
        // Ambil 5 teratas
        $topRecommendations = array_slice($scoredPelatihan, 0, 5);

        if (empty($topRecommendations)) {
            // Jika tidak ketemu, tetap berikan saran keahlian yang ada di database
            $listFallback = [];
            foreach ($pelatihanEksternal as $keg) {
                if (is_array($keg->keahlian)) {
                    $listFallback = array_merge($listFallback, $keg->keahlian);
                }
            }
            $listFallback = array_unique(array_filter($listFallback));
            shuffle($listFallback);
            
            $suggestionsFallback = array_map(function($k) { return "Belajar " . $k; }, array_slice($listFallback, 0, 3));
            $suggestionsFallback[] = 'Tampilkan semua pelatihan';

            return response()->json([
                'type' => 'text',
                'reply' => "Wah, sepertinya saya belum menemukan pelatihan yang pas dengan kata kunci <b>\"{$message}\"</b>.<br><br>Mungkin Kakak tertarik untuk mempelajari keahlian lain yang sedang tersedia?",
                'suggestions' => array_values($suggestionsFallback)
            ]);
        }

        return $this->formatRecommendationResponse(collect($topRecommendations), $nama, $profesi, "Bingo! Berdasarkan keahlian yang Kakak cari, berikut adalah top rekomendasi pelatihan untuk Kak <b>{$nama}</b>:");
    }

    private function formatRecommendationResponse($pelatihans, $nama, $profesi, $introText)
    {
        $cards = [];
        foreach ($pelatihans as $p) {
            $keahlianStr = is_array($p->keahlian) ? implode(', ', $p->keahlian) : '-';
            
            $cards[] = [
                'id' => $p->id,
                'nama' => $p->nama_kegiatan,
                'jpl' => $p->jpl ?? 0,
                'platform' => $p->platform,
                'tanggal' => \Carbon\Carbon::parse($p->tanggal_mulai)->translatedFormat('d M Y'),
                'keahlian' => $keahlianStr,
                'url_daftar' => route('public.kegiatan.daftar', $p->id) . "?nama=" . urlencode($nama) . "&profesi=" . urlencode($profesi),
                'raw_data' => json_encode([
                    'nama' => $p->nama_kegiatan,
                    'deskripsi' => $p->deskripsi,
                    'tanggal' => \Carbon\Carbon::parse($p->tanggal_mulai)->translatedFormat('d M Y H:i'),
                    'jpl' => $p->jpl,
                    'keahlian' => $keahlianStr,
                    'tujuan' => $p->tujuan->pluck('tujuan')->toArray(),
                    'kompetensi' => $p->kompetensi->map(function($k) { 
                        return ['nama' => $k->nama_kompetensi, 'indikator' => $k->pivot->indikator_keberhasilan]; 
                    })->toArray()
                ])
            ];
        }

        return response()->json([
            'type' => 'recommendation',
            'reply' => $introText,
            'cards' => $cards
        ]);
    }

    // =========================================================================
    // FUNGSI HELPER LAMA (TETAP SAMA)
    // =========================================================================
    protected function collectMetrics(): array {
        return [
            'mahasiswa'             => $this->safeCount(Mahasiswa::class),
            'pelatihan'             => $this->safeCount(Kegiatan::class),
            'ruangan'               => $this->safeCount(Ruangan::class),
            'pengajuan'             => $this->safeCount(Pengajuan::class),
            'mitra'                 => $this->safeCount(Mou::class),
            'pengajuan_status'      => $this->groupStatus(),
            'mahasiswa_penelitian'  => $this->safeCount(MahasiswaPenelitian::class),
            'pra_penelitian'        => $this->safeCount(PraPenelitian::class),
            'pra_penelitian_anggota'=> $this->safeCount(PraPenelitianAnggota::class),
            'user'                  => $this->safeCount(User::class),
            'ruangan_kosong'        => $this->safeCountWhere(RuanganKetersediaan::class, 'status', 'tersedia'),
            'ruangan_penuh'         => $this->safeCountWhere(RuanganKetersediaan::class, 'status', 'penuh'),
            'absensi_hari_ini'      => $this->countAbsensiToday(),
        ];
    }

    protected function baseSummary(array $metrics): string {
        return sprintf('Ringkasan Sindikat: %s mahasiswa, %s pengajuan, %s pelatihan, %s ruangan, %s mitra.',
            $this->describeMetric($metrics['mahasiswa'] ?? null),
            $this->describeMetric($metrics['pengajuan'] ?? null),
            $this->describeMetric($metrics['pelatihan'] ?? null),
            $this->describeMetric($metrics['ruangan'] ?? null),
            $this->describeMetric($metrics['mitra'] ?? null)
        );
    }

    protected function intentDictionary(): array {
        return [
            'greeting' => ['hai', 'halo', 'hallo', 'assalam', 'assalamu', 'selamat pagi', 'selamat siang', 'selamat sore', 'selamat malam'],
            'help' => ['bisa apa', 'fitur apa', 'panduan', 'cara pakai', 'bantuan', 'help'],
            'about_system' => ['apa itu sindikat', 'sindikat itu apa', 'tentang sindikat', 'fungsi sindikat', 'tentang sistem'],
            'mahasiswa' => ['jumlah mahasiswa', 'total mahasiswa', 'data mahasiswa', 'mahasiswa berapa', 'peserta berapa', 'jumlah peserta'],
            'mahasiswa_penelitian' => ['mahasiswa penelitian', 'peserta penelitian', 'penelitian klinik', 'jumlah penelitian', 'data penelitian'],
            'pra_penelitian' => ['pra penelitian', 'pra-penelitian', 'jumlah pra penelitian', 'data pra penelitian'],
            'pelatihan' => ['jumlah pelatihan', 'data pelatihan', 'pelatihan aktif', 'pelatihan tersedia'],
            'pelatihan_peserta' => ['peserta pelatihan', 'jumlah peserta pelatihan'],
            'ruangan' => ['data ruangan', 'ruangan berapa', 'jumlah ruangan', 'ruang praktik', 'ruang magang'],
            'ruangan_ketersediaan' => ['ruangan kosong', 'ruangan tersedia', 'ketersediaan ruangan', 'ruangan penuh', 'ruang penuh'],
            'pengajuan_general' => ['status pengajuan', 'data pengajuan', 'pengajuan berapa', 'jumlah pengajuan'],
            'pengajuan_approved' => ['pengajuan disetujui', 'pengajuan diterima', 'approve', 'pengajuan yang sudah disetujui'],
            'pengajuan_rejected' => ['pengajuan ditolak', 'reject', 'pengajuan yang ditolak'],
            'pengajuan_pending' => ['pengajuan pending', 'pengajuan menunggu', 'pengajuan belum diproses', 'waiting'],
            'pengajuan_user' => ['status pengajuan saya', 'pengajuan saya sampai mana', 'cek pengajuan saya'],
            'mitra' => ['data mitra', 'jumlah mitra', 'jumlah mou', 'mou aktif', 'universitas terdaftar', 'kampus terdaftar'],
            'mitra_not_registered' => ['universitas saya belum ada', 'kampus saya belum ada', 'mitra belum terdaftar'],
            'absensi_today' => ['absensi hari ini', 'absen hari ini', 'jumlah absensi', 'data absensi', 'absen peserta'],
            'user' => ['jumlah user', 'data user', 'pengguna terdaftar', 'akun terdaftar', 'jumlah admin', 'data admin'],
            'login_issue' => ['gagal login', 'tidak bisa login', 'login error', 'lupa password', 'reset password'],
            'registration' => ['cara daftar', 'bagaimana daftar', 'mendaftar magang', 'pendaftaran magang', 'pendaftaran penelitian', 'cara mendaftar'],
            'requirements' => ['syarat pengajuan', 'persyaratan', 'berkas apa saja', 'dokumen apa saja', 'syarat apa saja'],
            'edit_data' => ['edit data', 'ubah data', 'perbaiki data', 'koreksi data'],
            'program_difference' => ['beda magang dan penelitian', 'magang atau penelitian', 'magang dan pelatihan', 'perbedaan program'],
            'service_hours' => ['jam layanan', 'jadwal layanan', 'jam operasional', 'buka jam berapa'],
            'contact_admin' => ['kontak admin', 'hubungi admin', 'contact admin', 'wa admin', 'no admin'],
            'logout' => ['logout', 'keluar aplikasi', 'keluar sistem'],
            'summary_request' => ['ringkas', 'ringkasan', 'summary', 'ringkasan data'],
            'peraturan_penelitian_umum' => ['peraturan penelitian', 'aturan penelitian', 'ketentuan penelitian', 'penelitian rs', 'penelitian di rs', 'penelitian rsud', 'rsud slg penelitian'],
            'peraturan_penelitian_penolakan' => ['ketentuan penolakan', 'penelitian ditolak', 'alasan penolakan', 'kenapa ditolak', 'ditolak kenapa'],
            'peraturan_penelitian_administrasi' => ['syarat administrasi penelitian', 'administrasi penelitian', 'berkas penelitian', 'dokumen penelitian', 'surat pengantar', 'proposal acc', 'form penelitian', 'pembayaran penelitian'],
            'peraturan_penelitian_etik' => ['etik penelitian', 'perlindungan data', 'data pasien', 'de-identifikasi', 'informed consent', 'ethical clearance', 'foto pasien', 'video pasien', 'kerahasiaan pasien'],
            'peraturan_penelitian_teknis' => ['persyaratan teknis', 'teknis penelitian', 'akses icu', 'akses ok', 'nicu', 'petugas rekam medis', 'sop rekam medis', 'koordinasi unit', 'jadwal disetujui'],
            'peraturan_penelitian_pelaporan' => ['kewajiban pelaporan', 'laporan hasil penelitian', 'soft file', 'presentasi hasil', 'publikasi penelitian'],
            'peraturan_penelitian_waktu' => ['lama proses', 'berapa lama proses', 'izin penelitian berapa lama', 'waktu persetujuan', '5-14', '5–14'],
            'peraturan_penelitian_alur' => ['alur penelitian', 'flow penelitian', 'tahapan penelitian', 'proses penelitian', 'pengajuan verifikasi persetujuan'],
        ];
    }

    protected function detectIntent(string $message): string {
        $dictionary = $this->intentDictionary();
        $bestIntent = 'summary_request'; 
        $bestScore  = 0;
        foreach ($dictionary as $intent => $keywords) {
            $score = 0;
            foreach ($keywords as $keyword) {
                if ($keyword === '') continue;
                if (strpos($message, $keyword) !== false) {
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

    protected function respondForIntent(string $intent, string $message, array $metrics): string {
        $summary = $this->baseSummary($metrics);
        switch ($intent) {
            case 'greeting': return 'Halo 👋, ini asisten Sindikat. '.$summary.' Kamu bisa tanya: "status pengajuan", "jumlah pelatihan", "ruangan tersedia", atau "peraturan penelitian".';
            case 'help': return 'Aku bisa bantu ringkas data, jumlah mahasiswa/pelatihan/pengajuan, status pengajuan, ketersediaan ruangan, info absensi, sampai peraturan penelitian. Contoh: "jumlah mahasiswa", "pengajuan diterima berapa", "ketentuan penolakan penelitian".';
            case 'about_system': return 'Sindikat adalah sistem manajemen magang, penelitian, pelatihan, MOU, dan ruangan di lingkungan rumah sakit. Tujuannya mempermudah peserta dan admin dalam pengajuan, pemantauan status, dan pengelolaan data.';
            case 'mahasiswa': return 'Total mahasiswa/peserta terdaftar: '.$this->describeMetric($metrics['mahasiswa'] ?? null).'. Detail per peserta bisa dilihat di dashboard.';
            case 'mahasiswa_penelitian': return 'Total mahasiswa penelitian: '.$this->describeMetric($metrics['mahasiswa_penelitian'] ?? null).'. Termasuk anggota pra-penelitian: '.$this->describeMetric($metrics['pra_penelitian_anggota'] ?? null).'.';
            case 'pra_penelitian': return 'Total pra-penelitian yang tercatat: '.$this->describeMetric($metrics['pra_penelitian'] ?? null).'. Detail dokumen dan status bisa dicek di menu pra-penelitian.';
            case 'pelatihan': return 'Data pelatihan aktif: '.$this->describeMetric($metrics['pelatihan'] ?? null).'. Peserta dapat mengedit data melalui tautan publik atau dashboard peserta (jika diizinkan).';
            case 'pelatihan_peserta': 
                $totalPeserta = $this->safeCount(MahasiswaPenelitian::class);
                return 'Perkiraan total peserta pelatihan/penelitian yang tercatat: '.$this->describeMetric($totalPeserta).'.';
            case 'ruangan': return 'Ruang/layanan terdata di sistem: '.$this->describeMetric($metrics['ruangan'] ?? null).'. Detail penempatan dan kapasitas ada di menu ruangan.';
            case 'ruangan_ketersediaan':
                $kosong = $this->describeMetric($metrics['ruangan_kosong'] ?? null);
                $penuh  = $this->describeMetric($metrics['ruangan_penuh'] ?? null);
                return "Ketersediaan ruangan saat ini: $kosong ruangan tersedia, $penuh ruangan penuh. Jadwal dan detail bisa dicek di menu ruangan.";
            case 'pengajuan_general': return 'Total pengajuan: '.$this->describeMetric($metrics['pengajuan'] ?? null).'. '.$this->formatStatus($metrics['pengajuan_status'] ?? []);
            case 'pengajuan_approved': return 'Pengajuan yang sudah disetujui: '.$this->describeMetric(Pengajuan::where('status', 'approve')->count()).'.';
            case 'pengajuan_rejected': return 'Pengajuan yang ditolak: '.$this->describeMetric(Pengajuan::where('status', 'reject')->count()).'.';
            case 'pengajuan_pending': return 'Pengajuan berstatus menunggu/pending: '.$this->describeMetric(Pengajuan::where('status', 'waiting')->count()).'. Silakan cek detail di dashboard.';
            case 'pengajuan_user': return 'Untuk status pengajuan pribadi, silakan login lalu cek di menu pengajuan pada dashboard. Sistem akan menampilkan status detail per permohonan milikmu.';
            case 'mitra': return 'Mitra/Universitas terdaftar: '.$this->describeMetric($metrics['mitra'] ?? null).'. Daftar ini digunakan saat peserta memilih universitas/instansi pada formulir.';
            case 'mitra_not_registered': return 'Jika universitas/instansi belum ada di daftar mitra, peserta dapat menghubungi admin untuk proses penambahan MOU atau mengikuti kebijakan sementara yang ditentukan admin.';
            case 'absensi_today': return "Total absensi yang tercatat hari ini (".date('Y-m-d')."): ".$this->describeMetric($metrics['absensi_hari_ini'] ?? null).". Detail kehadiran dapat dicek di menu absensi.";
            case 'user': return 'Total pengguna terdaftar di sistem: '.$this->describeMetric($metrics['user'] ?? null).'. Hak akses dibedakan berdasarkan role (admin, operator, peserta, dsb).';
            case 'login_issue': return 'Jika tidak bisa login atau lupa password, gunakan fitur reset password (jika tersedia) atau hubungi admin Sindikat untuk bantuan reset akun. Pastikan juga email/NIK yang digunakan sudah terdaftar.';
            case 'registration': return 'Untuk mendaftar, buka halaman pendaftaran di landing page, pilih jenis pengajuan (magang/penelitian/pelatihan), isi data dengan lengkap, lalu unggah berkas yang diminta. Setelah tersimpan, pantau status di dashboard.';
            case 'requirements': return 'Syarat dan berkas pengajuan berbeda untuk tiap jenis program. Detail persyaratan terbaru biasanya tercantum di formulir pengajuan. Jika yang dimaksud penelitian, kamu bisa tanya: "syarat administrasi penelitian" atau "etik penelitian".';
            case 'edit_data': return 'Sebagian data peserta bisa diedit melalui dashboard atau tautan publik selama pengajuan belum dikunci/diapprove admin. Jika data sudah terkunci, peserta perlu menghubungi admin untuk koreksi data.';
            case 'program_difference': return 'Secara umum: magang fokus praktik kerja, penelitian fokus pengumpulan data ilmiah, sedangkan pelatihan fokus peningkatan kompetensi. Di Sindikat, ketiganya diatur melalui jenis pengajuan yang berbeda.';
            case 'service_hours': return 'Jam layanan administrasi mengikuti jam kerja rumah sakit. Untuk info terbaru, silakan lihat pengumuman resmi atau hubungi admin Sindikat.';
            case 'contact_admin': return 'Silakan hubungi admin Sindikat melalui kanal resmi RS yang tercantum di pengumuman internal atau kontak unit Diklat.';
            case 'logout': return 'Untuk keluar dari sistem, gunakan tombol logout setelah login. Setelah logout, sesi akun berakhir dan kamu perlu login lagi untuk mengakses data.';
            case 'peraturan_penelitian_penolakan': return implode("\n", ["❌ Ketentuan Penolakan Penelitian (RSUD SLG)", "Permohonan penelitian dapat ditolak apabila:", "1) Proposal belum ACC oleh Dosen Pembimbing Akademik", "2) Data yang diminta bersifat rahasia", "3) Penelitian tidak memiliki kebermanfaatan untuk RSUD SLG", "4) Penelitian berpotensi mengganggu pelayanan pasien", "5) Tidak memiliki Ethical Clearance (untuk penelitian yang membutuhkan)"]);
            case 'peraturan_penelitian_administrasi': return implode("\n", ["🧾 Persyaratan Administratif Penelitian (RSUD SLG)", "a) Surat Pengantar Resmi dari institusi pendidikan (pejabat berwenang)", "b) Proposal lengkap yang sudah ACC dosen pembimbing", "c) Mengisi link form penelitian dari Diklat RSUD SLG", "d) Surat pernyataan: tidak menyebarluaskan data RS tanpa izin", "e) Menunggu surat balasan dari Diklat RSUD SLG", "f) Pembayaran sesuai ketentuan SK Direktur", "g) Ethical Clearance"]);
            case 'peraturan_penelitian_etik': return implode("\n", ["🔒 Etik & Perlindungan Data Penelitian (RSUD SLG)", "a) Penelitian tidak boleh mengganggu pelayanan rumah sakit", "b) Wajib menjaga kerahasiaan identitas pasien", "c) Data pasien harus de-identifikasi", "d) Dilarang foto/video pasien tanpa izin tertulis", "e) Wajib mengikuti aturan K3 & keselamatan pasien", "f) Interaksi dengan pasien wajib informed consent"]);
            case 'peraturan_penelitian_teknis': return implode("\n", ["⚙️ Persyaratan Teknis Penelitian (RSUD SLG)", "1) Jadwal penelitian harus disetujui Koordinator Diklat dan Unit terkait", "2) Wajib koordinasi awal dengan pembimbing lahan/kepala ruangan", "3) Area tertentu tidak boleh diakses tanpa pendamping/izin (mis. ICU, OK, NICU)", "4) Pengambilan data rekam medis melalui Petugas Rekam Medis sesuai SOP"]);
            case 'peraturan_penelitian_pelaporan': return implode("\n", ["📄 Kewajiban Pelaporan Penelitian (RSUD SLG)", "1) Wajib menyerahkan Laporan Hasil Penelitian (Soft File) ke Diklat RSUD SLG", "2) Jika penelitian dipublikasikan, wajib presentasi hasil untuk pengembangan mutu layanan RSUD SLG"]);
            case 'peraturan_penelitian_waktu': return implode("\n", ["⏱ Lama Proses Persetujuan Penelitian (RSUD SLG)", "Biasanya 5–14 hari kerja, tergantung kompleksitas penelitian."]);
            case 'peraturan_penelitian_alur': return implode("\n", ["🧭 Alur Penelitian (RSUD SLG)", "Pengajuan → Verifikasi → Persetujuan → Pelaksanaan → Pelaporan → Presentasi (di hadapan manajemen/Direktur sesuai ketentuan)"]);
            case 'peraturan_penelitian_umum': return implode("\n", ["📘 Peraturan Penelitian di RSUD SLG (ringkas)", "Ada 5 bagian utama:", "1) Administrasi (surat, proposal ACC, form, pernyataan, pembayaran, ethical clearance)", "2) Etik & data (de-identifikasi, informed consent, larangan foto/video tanpa izin)", "3) Teknis (jadwal disetujui, koordinasi unit, akses area terbatas, data RM via petugas)", "4) Pelaporan (soft file + presentasi bila dipublikasikan)", "5) Waktu proses izin (5–14 hari kerja)", "", "Tanya spesifik aja: \"ketentuan penolakan\", \"syarat administrasi\", \"etik penelitian\", \"alur penelitian\"."]);
            case 'summary_request':
            default:
                return $summary.' Kamu bisa lanjut tanya: "status pengajuan", "ruangan kosong", "absensi hari ini", atau "ketentuan penolakan penelitian".';
        }
    }

    protected function describeMetric($value): string { return $value === null ? 'belum tersedia' : (string) $value; }
    protected function formatStatus($statusCollection): string {
        if (empty($statusCollection)) return 'Belum ada rincian status pengajuan.';
        $statusArray = is_array($statusCollection) ? $statusCollection : $statusCollection->toArray();
        $parts = [];
        foreach ($statusArray as $status => $total) $parts[] = ($status ?: 'tanpa status').': '.$total;
        return 'Rincian status pengajuan - '.implode(', ', $parts).'.';
    }
    protected function safeCount(string $model): ?int { try { return $model::count(); } catch (\Throwable $e) { return null; } }
    protected function safeCountWhere(string $model, string $column, $value): ?int { try { return $model::where($column, $value)->count(); } catch (\Throwable $e) { return null; } }
    protected function countAbsensiToday(): ?int { try { return Absensi::whereDate('created_at', date('Y-m-d'))->count(); } catch (\Throwable $e) { return null; } }
    protected function groupStatus() { try { return Pengajuan::select('status', DB::raw('count(*) as total'))->groupBy('status')->pluck('total', 'status'); } catch (\Throwable $e) { return []; } }
}