<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrientasiResult;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Models\Material;
use App\Models\MaterialProgress;
use App\Models\Mahasiswa;

class OrientasiController extends Controller
{
     private function getPreTestQuestions()
    {
        return [
            1 => [
                'q' => 'Yang termasuk 5 momen cuci tangan di RS [standart WHO], kecuali:', 
                'options' => [
                    'a' => 'Sebelum bersentuhan dengan pasien', 
                    'b' => 'Sebelum melakukan tindakan aseptik', 
                    'c' => 'Sesudah melakukan tindakan administratif', 
                    'd' => 'Sesudah bersentuhan dengan pasien', 
                    'e' => 'Sesudah bersentuhan dengan lingkungan pasien'
                ], 
                'key' => 'c'
            ],
            2 => [
                'q' => 'Beberapa penyebab kejadian needle stick injury (tertusuk jarum), kecuali:', 
                'options' => [
                    'a' => 'Melakukan recapping', 
                    'b' => 'Langsung membuang spuit dan jarum ke safety box', 
                    'c' => 'Menekuk (memanipulasi) jarum', 
                    'd' => 'Melepas jarum dari syringe (spuit)', 
                    'e' => 'Membuang isi safety box jika sudah terisi penuh'
                ], 
                'key' => 'b'
            ],
            3 => [
                'q' => 'Dibawah ini jenis alat proteksi kebakaran Pasif, kecuali:', 
                'options' => [
                    'a' => 'Koridor', 
                    'b' => 'Selasar', 
                    'c' => 'Tangga Darurat', 
                    'd' => 'Ramp', 
                    'e' => 'APAR'
                ], 
                'key' => 'e'
            ],
            4 => [
                'q' => 'Berapa jumlah indikator mutu nasional?', 
                'options' => [
                    'a' => '13', 
                    'b' => '10', 
                    'c' => '11', 
                    'd' => '9', 
                    'e' => '5'
                ], 
                'key' => 'a'
            ],
            5 => [
                'q' => 'Manakah posisi KMKP di struktur organisasi?', 
                'options' => [
                    'a' => 'Monitoring mutu unit', 
                    'b' => 'Koordinasi mutu unit', 
                    'c' => 'Koordinator unit', 
                    'd' => 'Semua jawaban benar', 
                    'e' => 'Semua jawaban salah'
                ], 
                'key' => 'c'
            ],
            6 => [
                'q' => 'Yang berperan dalam keberhasilan program PPI di RS adalah:', 
                'options' => [
                    'a' => 'Direktur', 
                    'b' => 'Komite PPI', 
                    'c' => 'IPCN', 
                    'd' => 'Staf Laundry', 
                    'e' => 'Benar semua'
                ], 
                'key' => 'e'
            ],
            7 => [
                'q' => 'Kode darurat bencana RS untuk ancaman kebakaran (api/asap) adalah:', 
                'options' => [
                    'a' => 'Code blue', 
                    'b' => 'Code black', 
                    'c' => 'Code Red', 
                    'd' => 'Code green', 
                    'e' => 'Hospital disaster plan'
                ], 
                'key' => 'c'
            ],
            8 => [
                'q' => 'Tipe RSUD SLG saat ini adalah:', 
                'options' => [
                    'a' => 'Tipe B non pendidikan', 
                    'b' => 'Tipe D', 
                    'c' => 'Tipe B pendidikan', 
                    'd' => 'Tipe A', 
                    'e' => 'Tipe C'
                ], 
                'key' => 'e'
            ],
            9 => [
                'q' => 'Struktur manajemen RSUD SLG dibawah direktur adalah:', 
                'options' => [
                    'a' => 'Kepala Bagian', 
                    'b' => 'Kepala Seksi', 
                    'c' => 'Kepala instalasi', 
                    'd' => 'Kepala unit', 
                    'e' => 'Pengawas'
                ], 
                'key' => 'a'
            ],
            10 => [
                'q' => 'Pengelolaan laboratorium berada dibawah tanggung jawab bidang...', 
                'options' => [
                    'a' => 'Bidang Tata Usaha', 
                    'b' => 'Bidang kepegawaian', 
                    'c' => 'Bidang Pelayanan', 
                    'd' => 'Bidang Penunjang', 
                    'e' => 'Komite Medik'
                ], 
                'key' => 'd'
            ],
            11 => [
                'q' => 'Berapa ratio pijat dan nafas buatan pada pasien dewasa?', 
                'options' => [
                    'a' => '30 : 1', 
                    'b' => '30 : 3', 
                    'c' => '30 : 2', 
                    'd' => '15 : 1', 
                    'e' => '15 : 2'
                ], 
                'key' => 'c'
            ],
            12 => [
                'q' => 'Setiap tiupan nafas yang benar diberikan dalam waktu .... detik asal dada mengembang', 
                'options' => [
                    'a' => '1 detik', 
                    'b' => '5 detik', 
                    'c' => '4 detik', 
                    'd' => '6 detik', 
                    'e' => '10 detik'
                ], 
                'key' => 'a'
            ],
            13 => [
                'q' => 'Setiap berapa menit dilakukan evaluasi carotis pada penolong ahli?', 
                'options' => [
                    'a' => '1 menit', 
                    'b' => '1.5 menit', 
                    'c' => '2 menit', 
                    'd' => '2.5 menit', 
                    'e' => '5 menit'
                ], 
                'key' => 'c'
            ],
            14 => [
                'q' => 'Berikut ini BUKAN merupakan cara membebaskan jalan nafas yang benar pada pasien henti jantung:', 
                'options' => [
                    'a' => 'Head tilt', 
                    'b' => 'Chin lift', 
                    'c' => 'Jaw thrust', 
                    'd' => 'Neck lift', 
                    'e' => 'Pasang guedel/mayo'
                ], 
                'key' => 'd'
            ],
        ];
    }

    // --- POST TEST (SOAL VARIASI / SERUPA TAPI BEDA SUDUT PANDANG) ---
    private function getPostTestQuestions()
    {
        return [
            1 => [
                'q' => 'Salah satu dari 5 momen cuci tangan yang benar adalah:', 
                'options' => [
                    'a' => 'Sebelum masuk rumah sakit', 
                    'b' => 'Sebelum kontak dengan pasien', 
                    'c' => 'Setelah makan siang', 
                    'd' => 'Setelah menulis laporan', 
                    'e' => 'Sebelum pulang kerumah'
                ], 
                'key' => 'b' // Variasi dari soal momen cuci tangan
            ],
            2 => [
                'q' => 'Wadah berwarna kuning (Safety Box) digunakan untuk membuang limbah:', 
                'options' => [
                    'a' => 'Kertas dan plastik', 
                    'b' => 'Sisa makanan pasien', 
                    'c' => 'Benda tajam / Jarum suntik', 
                    'd' => 'Botol infus', 
                    'e' => 'Popok bayi'
                ], 
                'key' => 'c' // Variasi dari soal Needle Stick Injury
            ],
            3 => [
                'q' => 'Langkah pertama saat menggunakan APAR (Alat Pemadam Api Ringan) adalah:', 
                'options' => [
                    'a' => 'Tekan tuas', 
                    'b' => 'Arahkan ke sumber api', 
                    'c' => 'Tarik pin pengaman', 
                    'd' => 'Sapukan dari kiri ke kanan', 
                    'e' => 'Lari menjauh'
                ], 
                'key' => 'c' // Variasi materi kebakaran/APAR
            ],
            4 => [
                'q' => 'Salah satu contoh indikator mutu nasional adalah:', 
                'options' => [
                    'a' => 'Jumlah pegawai yang cuti', 
                    'b' => 'Kepatuhan penggunaan APD', 
                    'c' => 'Jumlah pendapatan parkir', 
                    'd' => 'Kecepatan wifi RS', 
                    'e' => 'Jumlah tanaman di taman'
                ], 
                'key' => 'b' // Variasi materi mutu
            ],
            5 => [
                'q' => 'Kepanjangan dari PMKP dalam standar akreditasi RS adalah:', 
                'options' => [
                    'a' => 'Pelayanan Medik dan Keperawatan Pasien', 
                    'b' => 'Peningkatan Mutu dan Keselamatan Pasien', 
                    'c' => 'Program Manajemen Kesehatan Pegawai', 
                    'd' => 'Penyelamatan Manusia Keselamatan Publik', 
                    'e' => 'Pengawasan Mutu Kinerja Perawat'
                ], 
                'key' => 'b' // Variasi materi KMKP/Mutu
            ],
            6 => [
                'q' => 'Kewaspadaan Standar dalam PPI (Pencegahan Pengendalian Infeksi) diterapkan kepada:', 
                'options' => [
                    'a' => 'Pasien dengan penyakit menular saja', 
                    'b' => 'Pasien HIV saja', 
                    'c' => 'Semua pasien tanpa terkecuali', 
                    'd' => 'Hanya pasien di ICU', 
                    'e' => 'Hanya pasien operasi'
                ], 
                'key' => 'c' // Variasi materi PPI
            ],
            7 => [
                'q' => 'Kode darurat "Code Blue" di rumah sakit menandakan adanya:', 
                'options' => [
                    'a' => 'Kebakaran', 
                    'b' => 'Penculikan bayi', 
                    'c' => 'Henti jantung / Henti napas', 
                    'd' => 'Ancaman bom', 
                    'e' => 'Bencana alam'
                ], 
                'key' => 'c' // Variasi Code Red
            ],
            8 => [
                'q' => 'Lokasi RSUD Simpang Lima Gumul (SLG) secara administratif berada di kecamatan:', 
                'options' => [
                    'a' => 'Pare', 
                    'b' => 'Ngasem', 
                    'c' => 'Gampengrejo', 
                    'd' => 'Pagu', 
                    'e' => 'Gurah'
                ], 
                'key' => 'b' // Variasi profil RS
            ],
            9 => [
                'q' => 'Siapa pimpinan tertinggi dalam struktur organisasi di RSUD SLG?', 
                'options' => [
                    'a' => 'Kepala Bagian', 
                    'b' => 'Kepala Bidang', 
                    'c' => 'Direktur', 
                    'd' => 'Bupati', 
                    'e' => 'Kepala Dinas'
                ], 
                'key' => 'c' // Variasi struktur manajemen
            ],
            10 => [
                'q' => 'Instalasi Farmasi dan Radiologi berada di bawah koordinasi bidang:', 
                'options' => [
                    'a' => 'Penunjang', 
                    'b' => 'Pelayanan', 
                    'c' => 'Keuangan', 
                    'd' => 'Umum', 
                    'e' => 'Kepegawaian'
                ], 
                'key' => 'a' // Variasi Bidang Penunjang
            ],
            11 => [
                'q' => 'Kecepatan kompresi dada yang disarankan saat melakukan RJP adalah:', 
                'options' => [
                    'a' => '60-80 x/menit', 
                    'b' => '80-100 x/menit', 
                    'c' => '100-120 x/menit', 
                    'd' => '120-140 x/menit', 
                    'e' => 'Sesuka hati penolong'
                ], 
                'key' => 'c' // Variasi RJP
            ],
            12 => [
                'q' => 'Kedalaman kompresi dada yang efektif untuk pasien dewasa adalah:', 
                'options' => [
                    'a' => '1-2 cm', 
                    'b' => '2-3 cm', 
                    'c' => '5-6 cm', 
                    'd' => '8-10 cm', 
                    'e' => 'Sedalam mungkin'
                ], 
                'key' => 'c' // Variasi RJP/Nafas
            ],
            13 => [
                'q' => 'Apa tindakan pertama yang harus dilakukan saat menemukan korban tidak sadarkan diri?', 
                'options' => [
                    'a' => 'Langsung kompresi dada', 
                    'b' => 'Cek respon (Panggil/Tepuk bahu) & Pastikan aman', 
                    'c' => 'Berikan napas buatan', 
                    'd' => 'Cari minum', 
                    'e' => 'Tinggalkan korban'
                ], 
                'key' => 'b' // Variasi Evaluasi/BHD
            ],
            14 => [
                'q' => 'Teknik "Head Tilt - Chin Lift" bertujuan untuk:', 
                'options' => [
                    'a' => 'Mengecek nadi', 
                    'b' => 'Membuka jalan napas', 
                    'c' => 'Menghentikan pendarahan', 
                    'd' => 'Memberikan obat', 
                    'e' => 'Membangunkan pasien'
                ], 
                'key' => 'b' // Variasi Jalan Nafas
            ],
        ];
    }
    // --- HELPER UNTUK MENDAPATKAN PERIODE MAGANG AKTIF ---
    private function getMahasiswaAktif()
    {
        return Mahasiswa::where('user_id', auth()->id())
            ->where('status', 'aktif')
            ->latest()
            ->first();
    }

    public function index()
    {
        $materials = Material::orderBy('order')->get();
        
        $mahasiswaAktif = $this->getMahasiswaAktif();
        $userProgress = [];
        $result = null;

        if ($mahasiswaAktif) {
            // Ambil data orientasi spesifik untuk ID Pendaftaran ini
            $userProgress = MaterialProgress::where('mahasiswa_id', $mahasiswaAktif->id)
                ->pluck('material_id')
                ->toArray();
                
            $result = OrientasiResult::where('mahasiswa_id', $mahasiswaAktif->id)->first();
        }

        $total = count($materials);
        $done = count($userProgress);
        $persen = $total > 0 ? ($done / $total) * 100 : 0;
        $allCompleted = ($total > 0 && $done >= $total);

        // Orientasi ke-berapa (urutan periode magang user ini) + tahun periode
        $orientasiKe = 1;
        $tahunOrientasi = now()->year;
        if ($mahasiswaAktif) {
            $orientasiKe = Mahasiswa::where('user_id', auth()->id())
                ->where('created_at', '<=', $mahasiswaAktif->created_at)
                ->count();
            if ($orientasiKe < 1) $orientasiKe = 1;
            $tahunOrientasi = $mahasiswaAktif->tanggal_mulai
                ? \Carbon\Carbon::parse($mahasiswaAktif->tanggal_mulai)->year
                : now()->year;
        }

        return view('orientasi.index', compact('materials', 'userProgress', 'persen', 'allCompleted', 'result', 'orientasiKe', 'tahunOrientasi'));
    }

    public function showMaterial($id)
    {
        $material = Material::with('files')->findOrFail($id);
        $materials = Material::orderBy('order')->get();
        
        $mahasiswaAktif = $this->getMahasiswaAktif();
        $userProgress = [];
        $result = null;

        if ($mahasiswaAktif) {
            $userProgress = MaterialProgress::where('mahasiswa_id', $mahasiswaAktif->id)
                            ->where('is_completed', true)
                            ->pluck('material_id')
                            ->toArray();
                            
            $result = OrientasiResult::where('mahasiswa_id', $mahasiswaAktif->id)->first();
        }

        $total = $materials->count();
        $done = count($userProgress);
        $persen = $total > 0 ? ($done / $total) * 100 : 0;
        $allCompleted = ($total > 0 && $done >= $total);
        $isDone = in_array($id, $userProgress);

        $nextMaterial = Material::where('order', '>', $material->order)
                        ->orderBy('order', 'asc')
                        ->first();

        return view('orientasi.show', compact(
            'material', 'materials', 'userProgress', 'result', 
            'isDone', 'nextMaterial', 'persen', 'allCompleted'
        ));
    }

public function completeMaterial($id)
    {
        $mahasiswaAktif = $this->getMahasiswaAktif();
        
        if (!$mahasiswaAktif) {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki periode magang aktif.');
        }

        // PERBAIKAN: Hapus 'completed_at' => now() dari sini
        MaterialProgress::updateOrCreate(
            ['mahasiswa_id' => $mahasiswaAktif->id, 'material_id' => $id],
            ['user_id' => auth()->id(), 'is_completed' => true] 
        );

        $currentMaterial = Material::find($id);
        $next = Material::where('order', '>', $currentMaterial->order)
                ->orderBy('order', 'asc')
                ->first();

        if ($next) {
            return redirect()->route('orientasi.materi.show', $next->id)
                    ->with('success', 'Materi selesai! Lanjut ke materi berikutnya.');
        }

        return redirect()->route('orientasi.index')
                ->with('success', 'Selamat! Semua materi telah selesai dipelajari.');
    }
    public function showPreTest()
    {
        $questions = $this->getPreTestQuestions();
        $currentYear = date('Y');
        return view('orientasi.quiz', compact('questions', 'currentYear'))->with('type', 'pre');
    }

    public function startPreTest(Request $request)
    {
        $request->validate([
            'gelombang' => 'required|numeric|min:1',
            'tahun' => 'required|numeric|min:2020',
        ]);

        session([
            'orientasi_gelombang' => $request->gelombang,
            'orientasi_tahun' => $request->tahun
        ]);

        return redirect()->route('orientasi.pre');
    }

    public function submitPreTest(Request $request)
    {
        $mahasiswaAktif = $this->getMahasiswaAktif();
        if (!$mahasiswaAktif) {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki periode magang aktif.');
        }

        $questions = $this->getPreTestQuestions();
        $score = $this->calculateScore($request->answers, $questions);
        
        $gelombang = session('orientasi_gelombang');
        $tahun = session('orientasi_tahun');

        OrientasiResult::updateOrCreate(
            ['mahasiswa_id' => $mahasiswaAktif->id],
            [
                'user_id' => auth()->id(),
                'pre_test_score' => $score,
                'status' => 'siap_post_test',
                'gelombang' => $gelombang,
                'tahun' => $tahun
            ]
        );

        session()->forget(['orientasi_gelombang', 'orientasi_tahun']);

        return redirect()->route('orientasi.index')->with('success', "Data tersimpan. Silakan lanjut ke Post-Test.");
    }

    public function showPostTest()
    {
        $mahasiswaAktif = $this->getMahasiswaAktif();
        $result = null;
        
        if ($mahasiswaAktif) {
            $result = OrientasiResult::where('mahasiswa_id', $mahasiswaAktif->id)->first();
        }
        
        if (!$result || $result->pre_test_score === null) {
            return redirect()->route('orientasi.index')->with('error', 'Anda harus mengerjakan Pre-Test terlebih dahulu!');
        }

        $questions = $this->getPostTestQuestions();
        return view('orientasi.quiz', ['questions' => $questions, 'type' => 'post']);
    }

    public function submitPostTest(Request $request)
    {
        $mahasiswaAktif = $this->getMahasiswaAktif();
        if (!$mahasiswaAktif) abort(403);

        $result = OrientasiResult::where('mahasiswa_id', $mahasiswaAktif->id)->first();
        if (!$result || $result->pre_test_score === null) abort(403);

        $questions = $this->getPostTestQuestions();
        $score = $this->calculateScore($request->answers, $questions);
        
        if ($score >= 80) {
            $result->update([
                'post_test_score' => $score,
                'status' => 'lulus_orientasi'
            ]);
            return redirect()->route('orientasi.index')->with('success', "SELAMAT! Anda Lulus Post-Test (Nilai: $score). Sertifikat sudah terbit.");
        } else {
            $result->update([
                'post_test_score' => $score,
                'status' => 'siap_post_test' 
            ]);
            return redirect()->route('orientasi.index')->with('error', "Nilai Post-Test Anda: $score (Kurang dari 80). Silakan lakukan REMEDIAL (kerjakan ulang Post-Test).");
        }
    }

    private function calculateScore($answers, $questions)
    {
        $correct = 0;
        $total = count($questions);
        if(!$answers) return 0;

        foreach ($questions as $id => $q) {
            if (isset($answers[$id]) && $answers[$id] == $q['key']) {
                $correct++;
            }
        }
        return ($correct / $total) * 100;
    }
    
  public function cetakSertifikat($id) 
    {
        // Langsung tembak ke ID Hasil Orientasi yang spesifik, tidak perlu meraba pakai user_id
        $result = OrientasiResult::findOrFail($id);
        
        if ($result->status !== 'lulus_orientasi') {
            return back()->with('error', 'Mahasiswa tersebut belum lulus orientasi untuk periode ini.');
        }

        $user = \App\Models\User::findOrFail($result->user_id);

        $pdf = Pdf::loadView('orientasi.sertifikat_pdf', [
            'user' => $user,
            'date' => \Carbon\Carbon::parse($result->updated_at)->format('d F Y'),
            'pre_score' => $result->pre_test_score,
            'post_score' => $result->post_test_score
        ]);

        return $pdf->stream('Sertifikat-Orientasi-'.$user->name.'.pdf');
    }

    public function sertifikat()
    {
        $mahasiswaAktif = $this->getMahasiswaAktif();
        $result = null;
        
        if ($mahasiswaAktif) {
            $result = OrientasiResult::where('mahasiswa_id', $mahasiswaAktif->id)->first();
        }

        if (!$result || $result->status !== 'lulus_orientasi') {
            return redirect()->route('orientasi.index')->with('error', 'Anda belum lulus orientasi.');
        }

        $pdf = Pdf::loadView('orientasi.sertifikat_pdf', [
            'user' => auth()->user(),
            'date' => Carbon::parse($result->updated_at)->format('d F Y'),
            'pre_score' => $result->pre_test_score,
            'post_score' => $result->post_test_score
        ]);

        return $pdf->stream('Sertifikat-Orientasi.pdf');
    }
}