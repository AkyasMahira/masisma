<!DOCTYPE html>
<html lang="id">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Sertifikat Kegiatan - {{ $peserta->nama_lengkap_gelar }}</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Lora:ital,wght@0,500;0,700;1,500&family=Pinyon+Script&display=swap" rel="stylesheet">
    
    @php
        // Ambil warna tema dari database, jika kosong gunakan warna maroon default RSUD SLG
        $warnaTema = $peserta->kegiatan->warna_tema ?? '#7c1316';
        
        // Fungsi kecil untuk membuat warna gradient sedikit lebih gelap dari warna tema
        function adjustBrightness($hex, $steps) {
            $steps = max(-255, min(255, $steps));
            $hex = str_replace('#', '', $hex);
            if (strlen($hex) == 3) {
                $hex = str_repeat(substr($hex,0,1), 2) . str_repeat(substr($hex,1,1), 2) . str_repeat(substr($hex,2,1), 2);
            }
            $color_parts = str_split($hex, 2);
            $return = '#';
            foreach ($color_parts as $color) {
                $color   = hexdec($color);
                $color   = max(0, min(255, $color + $steps));
                $return .= str_pad(dechex($color), 2, '0', STR_PAD_LEFT);
            }
            return $return;
        }
        $warnaTemaGelap = adjustBrightness($warnaTema, -30); // Dibuat sedikit gelap untuk gradien

        // --- KALKULASI TANGGAL ---
        $tglMulai = \Carbon\Carbon::parse($peserta->kegiatan->tanggal_mulai);
        $tglSelesai = \Carbon\Carbon::parse($peserta->kegiatan->tanggal_selesai);
        
        // Hitung masa berlaku (3 tahun dari tanggal selesai, dikurangi 1 hari)
        $tglBerlakuAkhir = $tglSelesai->copy()->addYears(3)->subDay();

        // Format Teks Rentang Tanggal Pelaksanaan
        if($tglMulai->isSameDay($tglSelesai)) {
            $teksTanggal = $tglMulai->isoFormat('D MMMM YYYY');
        } else {
            if($tglMulai->format('m Y') == $tglSelesai->format('m Y')) {
                $teksTanggal = $tglMulai->isoFormat('D') . ' - ' . $tglSelesai->isoFormat('D MMMM YYYY');
            } elseif($tglMulai->format('Y') == $tglSelesai->format('Y')) {
                $teksTanggal = $tglMulai->isoFormat('D MMMM') . ' - ' . $tglSelesai->isoFormat('D MMMM YYYY');
            } else {
                $teksTanggal = $tglMulai->isoFormat('D MMMM YYYY') . ' - ' . $tglSelesai->isoFormat('D MMMM YYYY');
            }
        }
    @endphp

    <style>
        /* Variabel CSS Dinamis berdasarkan warna tema kegiatan */
        :root {
            --tema-utama: {{ $warnaTema }};
            --tema-gelap: {{ $warnaTemaGelap }};
            --emas: #d4af37;
        }

        /* PENGATURAN KERTAS A4 LANDSCAPE & PRINT */
        @page { size: A4 landscape; margin: 0; }
        
        body { 
            margin: 0; 
            padding: 0; 
            background-color: #f1f5f9; 
            font-family: 'Inter', sans-serif; 
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
            -webkit-print-color-adjust: exact !important; 
            print-color-adjust: exact !important;
        }
        
        /* CONTAINER SERTIFIKAT - UKURAN A4 PRESISI */
        .cert-container {
            width: 297mm;
            height: 210mm;
            background: #ffffff;
            background-image: radial-gradient(circle at center, #ffffff 40%, #fcfcfc 100%);
            position: relative;
            box-sizing: border-box;
            box-shadow: 0 20px 50px rgba(0,0,0,0.15);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            margin-bottom: 20px;
            page-break-after: always;
        }

        /* =========================================
           ORNAMEN SUDUT (MEMAKAI WARNA TEMA DINAMIS)
           ========================================= */
        .ornament-tl {
            position: absolute; top: 0; left: 0; width: 180px; height: 180px;
            background: linear-gradient(135deg, var(--tema-utama) 0%, var(--tema-gelap) 50%, transparent 50.1%);
            z-index: 1;
        }
        .ornament-tl::after {
            content: ''; position: absolute; top: 12px; left: 12px; width: 150px; height: 150px;
            border-top: 3px solid var(--emas); border-left: 3px solid var(--emas);
        }

        .ornament-br {
            position: absolute; bottom: 0; right: 0; width: 180px; height: 180px;
            background: linear-gradient(315deg, var(--tema-utama) 0%, var(--tema-gelap) 50%, transparent 50.1%);
            z-index: 1;
        }
        .ornament-br::after {
            content: ''; position: absolute; bottom: 12px; right: 12px; width: 150px; height: 150px;
            border-bottom: 3px solid var(--emas); border-right: 3px solid var(--emas);
        }

        /* WATERMARK */
        .watermark {
            position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
            width: 400px; opacity: 0.04; z-index: 0; filter: grayscale(100%);
        }

        /* KONTEN BUNGKUSAN */
        .content {
            position: relative; z-index: 10;
            padding: 30px 80px 40px 80px;
            display: flex; flex-direction: column; height: 100%;
        }

        /* =========================================
           KOP SURAT
           ========================================= */
        .kop-surat {
            display: flex; justify-content: space-between; align-items: center;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 12px; margin-bottom: 15px;
            position: relative;
        }
        .kop-surat::after {
            content: ''; position: absolute; bottom: -5px; left: 0; right: 0;
            height: 1px; background: var(--tema-utama);
        }
        .kop-logo {  height: auto; object-fit: contain; }
        .kop-text { flex-grow: 1; text-align: center; padding: 0 20px; }
        .kop-gov { font-size: 12px; font-weight: 700; color: #475569; letter-spacing: 2px; text-transform: uppercase; margin-bottom: 4px;}
        .kop-rs { font-size: 21px; font-weight: 800; color: var(--tema-utama); letter-spacing: 1.5px; margin: 0; text-transform: uppercase;}
        .kop-alamat { font-size: 10px; font-weight: 400; color: #64748b; margin-top: 5px; }

        /* =========================================
           AREA TEKS SERTIFIKAT
           ========================================= */
        .cert-header { text-align: center; margin-bottom: 20px; }
        .cert-title { 
            font-family: 'Pinyon Script', cursive; 
            font-size: 58px; 
            color: var(--emas); 
            margin: 0; 
            line-height: 1;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.05);
        }
        .cert-nomor { font-size: 11px; font-weight: 600; color: #64748b; letter-spacing: 2px; margin-top: 15px; }

        .cert-body { text-align: center; flex-grow: 1; display: flex; flex-direction: column; justify-content: center;}
        .text-pengantar { font-size: 13px; font-weight: 500; color: #475569; letter-spacing: 1px; margin-bottom: 8px; text-transform: uppercase;}
        
        .nama-peserta { 
            font-family: 'Lora', serif; 
            font-size: 38px; 
            font-weight: 700; 
            color: #0f172a; 
            margin: 0; 
        }
        .detail-peserta { font-size: 13px; font-weight: 700; color: var(--tema-utama); margin-top: 5px; letter-spacing: 1px; text-transform: uppercase;}
        .garis-emas { width: 50px; height: 3px; background: var(--emas); margin: 15px auto; border-radius: 5px;}

        .deskripsi { font-size: 14px; line-height: 1.6; color: #334155; max-width: 800px; margin: 0 auto;}
        .nama-kegiatan { 
            font-size: 17px; font-weight: 800; color: #0f172a; 
            display: inline-block; margin: 6px 0; text-transform: uppercase;
        }
        .text-berlaku { font-size: 13px; font-weight: 600; color: var(--tema-utama); margin-top: 10px; display: inline-block; padding: 5px 15px; background: #fef1f2; border-radius: 50px;}

        /* =========================================
           FOOTER (TTD & LOGO KIRI BAWAH)
           ========================================= */
        .cert-footer { display: flex; justify-content: space-between; align-items: flex-end; margin-top: 15px; }
        
        /* Logo Kiri Bawah */
        .footer-logos {
            display: flex; gap: 15px; align-items: center;
            margin-left: 20px; margin-bottom: 10px; z-index: 20; position: relative;
        }
        .footer-logos img { height: 35px; width: auto; object-fit: contain; }

        /* Tanda Tangan QR (Kanan) */
        .footer-ttd { text-align: center; width: 300px; margin-right: 20px; z-index: 20; position: relative;}
        .tgl-terbit { font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 5px;}
        .jabatan { font-size: 13px; font-weight: 800; color: #0f172a; margin-bottom: 2px;} 
        
        .qr-ttd-box { margin: 2px auto; display: inline-block; padding: 4px; border: 1px solid #e2e8f0; border-radius: 8px; background: #fff;}
        .qr-img { width: 65px; height: 65px; display: block; } /* Diperkecil agar lebih ringkas */
        
        .nama-ttd { font-family: 'Lora', serif; font-size: 15px; font-weight: 700; color: #0f172a; border-bottom: 1px solid #0f172a; display: inline-block; padding-bottom: 2px; margin-bottom: 3px; margin-top: 3px;}
        .nip-ttd { font-size: 11px; font-weight: 500; color: #475569; }

        /* =========================================
           STYLING HALAMAN 2 (TABEL MATERI)
           ========================================= */
        .page-2-header { text-align: center; margin-bottom: 10px; margin-top: 0; }
        .page-2-title { font-size: 15px; font-weight: 800; color: #0f172a; margin: 0; text-transform: uppercase; }
        .page-2-subtitle { font-size: 13px; font-weight: 600; color: #475569; margin-top: 2px; }

        .table-materi { 
            width: 100%; 
            border-collapse: collapse; 
            font-size: 11px; /* Diperkecil dari 12px agar baris muat lebih banyak */
            background: #fff;
            margin: 0 auto;
        }
        .table-materi th, .table-materi td { 
            border: 1px solid #000; 
            padding: 5px 8px; /* Padding dikurangi agar baris lebih rapat */
            vertical-align: middle;
        }
        .table-materi th { 
            background-color: var(--tema-utama);
            color: #ffffff; 
            font-weight: 700; 
            text-align: center;
        }
        .table-materi td.text-center { text-align: center; }
        .table-materi td.text-left { text-align: left; }
        .table-materi tfoot th { 
            background-color: var(--tema-utama); 
            color: #ffffff;
            font-weight: 800;
        }

        /* TOMBOL CETAK UI */
        .btn-print { position: fixed; bottom: 30px; right: 30px; background: var(--tema-utama); color: white; border: none; padding: 16px 30px; border-radius: 50px; font-family: 'Inter', sans-serif; font-weight: 700; font-size: 15px; cursor: pointer; box-shadow: 0 10px 25px rgba(0,0,0,0.3); z-index: 1000; transition: 0.3s;}
        .btn-print:hover { background: var(--tema-gelap); transform: translateY(-4px);}
        
        @media print { 
            body { background: #fff; margin: 0; } 
            .cert-container { box-shadow: none; border: none; margin-bottom: 0; } 
            .btn-print { display: none; } 
        }
    </style>
</head>

<body>
    @php
        $tahun = date('Y', strtotime($peserta->kegiatan->tanggal_selesai));
        $id_pad = str_pad($peserta->kegiatan->id, 3, '0', STR_PAD_LEFT);
        $nomorSurat = "893.3 / " . $id_pad . " / 418.48 / " . $tahun;
        
        $secretKey = 'sindikat_rsud_slg_secret';
        $hash = md5($peserta->id . $secretKey);
        $tokenValidasi = 'PGW-' . $peserta->id . '-' . $hash;
        $urlValidasi = route('sertifikat.validasi', $tokenValidasi);
        
        $qrColorStr = str_replace('#', '', $warnaTema);
        $qr_url = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($urlValidasi) . "&color=" . $qrColorStr;
    @endphp

    <button class="btn-print" onclick="window.print()">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-printer" viewBox="0 0 16 16" style="display:inline; margin-bottom:-2px; margin-right:5px;">
            <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/>
            <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4V3zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2H5zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1z"/>
        </svg>
        Cetak / Simpan PDF
    </button>

    <!-- ================= HALAMAN 1 (DEPAN) ================= -->
    <div class="cert-container">
        <div class="ornament-tl"></div>
        <div class="ornament-br"></div>
        <img src="https://rsudslg.kedirikab.go.id/asset_compro/img/logo/Logo.png" class="watermark" alt="Watermark">

        <div class="content">
            <div class="kop-surat">
                <img src="https://upload.wikimedia.org/wikipedia/commons/a/ad/Logo_Kabupaten_Kediri_%28Seal_of_Kediri_Regency%29.svg" class="kop-logo" alt="Logo Kanan" style="width: 60px;">
                <div class="kop-text">
                    <div class="kop-gov">Pemerintah Kabupaten Kediri</div>
                    <div class="kop-rs">RSUD SIMPANG LIMA GUMUL</div>
                    <div class="kop-alamat">Jl. Galuh Candra Kirana, Tugurejo, Kec. Ngasem, Kabupaten Kediri, Jawa Timur 64182<br>Telp. (0354) 2891400 | Email: rsud.slg@kedirikab.go.id</div>
                </div>
                <img src="https://rsudslg.kedirikab.go.id/asset_compro/img/logo/Logo.png" class="kop-logo" alt="Logo Kiri" style="width: 70px;">
            </div>

            <div class="cert-header">
                <h1 class="cert-title">Sertifikat</h1>
                <div class="cert-nomor">NOMOR: {{ $nomorSurat }}</div>
            </div>

            <div class="cert-body">
                <div class="text-pengantar">diberikan Kepada:</div>
                <h2 class="nama-peserta">{{ $peserta->nama_lengkap_gelar }}</h2>
                <div class="detail-peserta">{{ $peserta->profesi ?? 'Peserta' }} &bull; {{ $peserta->instansi->nama_instansi ?? 'RSUD Simpang Lima Gumul' }}</div>
                
                <div class="garis-emas"></div>

                <div class="deskripsi">
                    Telah berpartisipasi dan menyelesaikan kewajiban kehadiran secara penuh sebagai <b>PESERTA</b> dalam :<br>
                    <span class="nama-kegiatan">"{{ $peserta->kegiatan->nama_kegiatan }}"</span><br>
                    
                    Yang diselenggarakan secara resmi oleh Diklat UOBK RSUD Simpang Lima Gumul Kediri<br>
                    di Kediri pada tanggal <b>{{ $teksTanggal }}</b>
                    
                    @if($peserta->kegiatan->jpl)
                        dengan jumlah <b>{{ $peserta->kegiatan->jpl }} JPL (Jam Pelajaran)</b>.
                    @else
                        .
                    @endif
                    <br>
                    
                    <!-- KETERANGAN MASA BERLAKU -->
                    <span class="text-berlaku">
                        Sertifikat ini berlaku selama 3 (tiga) tahun terhitung sejak {{ $tglSelesai->isoFormat('D MMMM YYYY') }} s.d {{ $tglBerlakuAkhir->isoFormat('D MMMM YYYY') }}
                    </span>
                </div>
            </div>

            <div class="cert-footer">
                <div class="footer-logos">
                    <img src="https://sindikat-rsudslg.kedirikab.go.id/berakhlak.png" alt="BerAKHLAK">
                    <img src="https://sindikat-rsudslg.kedirikab.go.id/melayani.png" alt="Bangga Melayani Bangsa">
                    <img src="https://sindikat-rsudslg.kedirikab.go.id/panjalu.png" alt="Panjalu Jayati">
                </div>

                <div class="footer-ttd">
                    <div class="tgl-terbit">Kediri, {{ $tglSelesai->isoFormat('D MMMM YYYY') }}</div>
                    <div class="jabatan">Direktur RSUD Simpang Lima Gumul</div>
                    
                    <div class="qr-ttd-box">
                        <img src="{{ $qr_url }}" class="qr-img" alt="QR Tanda Tangan">
                    </div><br>
                    
                    <div class="nama-ttd">dr. Tony Widyanto, Sp.OG (K)</div>
                    <div class="nip-ttd">Pembina Tk. I (IV/B)</div>
                    <div class="nip-ttd">NIP. 19750714 200212 1 006</div>
                </div>
            </div>
        </div>
    </div>


    <!-- ================= HALAMAN 2 (BELAKANG / TABEL MATERI) ================= -->
    <div class="cert-container">
        <div class="ornament-tl"></div>
        <div class="ornament-br"></div>
        <img src="https://rsudslg.kedirikab.go.id/asset_compro/img/logo/Logo.png" class="watermark" alt="Watermark" style="opacity: 0.02;">

        <!-- Padding-top & bottom dikurangi agar tabel muat lebih banyak baris -->
        <div class="content" style="padding-top: 25px; padding-bottom: 20px;">
            
            <div class="page-2-header">
                <h3 class="page-2-title">Materi {{ $peserta->kegiatan->nama_kegiatan }}</h3>
                <div class="page-2-subtitle">Sesuai Kurikulum / Silabus Kegiatan</div>
            </div>

            <table class="table-materi">
                <thead>
                    <tr>
                        <th width="5%">No.</th>
                        <th>Materi</th>
                        <th width="10%">Teori</th>
                        <th width="10%">Praktik</th>
                        <th width="15%">Jumlah JP</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalTeori = 0;
                        $totalPraktik = 0;
                        $totalJP = 0;
                        
                        // Menarik data materi milik kegiatan yang diizinkan tampil di sertifikat
                        $materiList = \App\Models\KegiatanMateri::where('kegiatan_id', $peserta->kegiatan_id)
                                        ->where('tampil_di_sertifikat', 1)
                                        ->orderBy('urutan', 'asc')
                                        ->get();
                    @endphp
                    
                    @foreach($materiList as $mat)
                        @php
                            $teori = $mat->nilai_teori ?? 0;
                            $praktik = $mat->nilai_praktik ?? 0;
                            $jml = $teori + $praktik;
                            
                            $totalTeori += $teori;
                            $totalPraktik += $praktik;
                            $totalJP += $jml;
                        @endphp
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td class="text-left">{{ $mat->nama_materi }}</td>
                            <td class="text-center">{{ $teori > 0 ? $teori : '' }}</td>
                            <td class="text-center">{{ $praktik > 0 ? $praktik : '' }}</td>
                            <td class="text-center">{{ $jml > 0 ? $jml : '' }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="2" class="text-center">Total JP</th>
                        <th class="text-center">{{ $totalTeori }}</th>
                        <th class="text-center">{{ $totalPraktik }}</th>
                        <th class="text-center">{{ $totalJP }}</th>
                    </tr>
                </tfoot>
            </table>
            
            <!-- TANDA TANGAN HALAMAN 2 -->
            <!-- Margin top diperkecil agar tidak terdorong keluar kertas -->
            <div class="cert-footer" style="margin-top: 10px;">
                <div></div> <!-- Dikosongkan agar flex space-between jalan ke Kanan -->
                
                <div class="footer-ttd">
                    <div class="tgl-terbit">Kediri, {{ $tglSelesai->isoFormat('D MMMM YYYY') }}</div>
                    <div class="jabatan">Direktur RSUD Simpang Lima Gumul</div>
                    
                    <div class="qr-ttd-box">
                        <img src="{{ $qr_url }}" class="qr-img" alt="QR Tanda Tangan">
                    </div><br>
                    
                    <div class="nama-ttd">dr. Tony Widyanto, Sp.OG (K)</div>
                    <div class="nip-ttd">Pembina Tk. I (IV/B)</div>
                    <div class="nip-ttd">NIP. 19750714 200212 1 006</div>
                </div>
            </div>

        </div>
    </div>

</body>
</html>