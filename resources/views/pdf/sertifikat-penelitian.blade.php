<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Sertifikat Penelitian - RSUD SLG</title>
    <style>
        @page {
            margin: 0;
            size: A4 landscape;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            margin: 0;
            padding: 0;
            background: #ffffff;
            width: 297mm;
            height: 210mm;
            overflow: hidden;
        }
        /* Bingkai Latar Belakang */
        .border-maroon {
            position: absolute;
            top: 0; left: 0; width: 297mm; height: 210mm;
            background: #7c1316;
            z-index: 1;
        }
        /* Area Putih Utama - Dibuat simetris */
        .white-area {
            position: absolute;
            top: 10mm; left: 10mm; width: 277mm; height: 190mm;
            background: #ffffff;
            z-index: 2;
        }
        .border-gold {
            position: absolute;
            top: 12mm; left: 12mm; width: 273mm; height: 186mm;
            border: 1mm double #d4af37;
            z-index: 3;
        }
        
        /* Container Konten Pusat - Menjamin Center Alignment */
        .content-center {
            position: absolute;
            top: 10mm; left: 10mm; width: 277mm; height: 190mm;
            text-align: center;
            z-index: 10;
        }

        /* Pengaturan Posisi Vertikal Manual agar tidak meluber */
        .header-section { margin-top: 10mm; }
        .student-section { margin-top: 15mm; }
        .details-section { margin-top: 8mm; padding: 0 35mm; }
        .score-section { margin: 10mm auto 0 auto; width: 210mm; }
        
        /* Footer dipatok di kanan bawah area putih */
        .footer-section {
            position: absolute;
            bottom: 10mm; right: 15mm; width: 80mm;
            text-align: center;
        }

        /* Typography */
        .hospital-name { font-size: 14px; font-weight: bold; color: #7c1316; text-transform: uppercase; margin-bottom: 2px; }
        .main-title { font-size: 38px; font-weight: bold; margin: 0; letter-spacing: 3px; }
        .sub-title { font-size: 14px; color: #d4af37; font-style: italic; margin-top: -5px; }
        
        .label-to { font-size: 12px; color: #666; text-transform: uppercase; margin-bottom: 5px; }
        .student-name { 
            font-size: 36px; font-weight: bold; color: #7c1316; font-style: italic; 
            border-bottom: 1px solid #d4af37; padding: 0 15px; display: inline-block;
        }

        .research-title { font-weight: bold; font-style: italic; font-size: 17px; color: #000; margin: 5px 0; line-height: 1.2; }
        
        /* Table Style */
        .table-score { width: 100%; border-collapse: collapse; border: 0.5px solid #d4af37; background: #fdfdfd; }
        .table-score td { border: 0.5px solid #d4af37; padding: 10px; text-align: center; }
        .score-head { font-size: 9px; color: #888; text-transform: uppercase; display: block; margin-bottom: 3px; }
        .score-val { font-size: 16px; font-weight: bold; color: #333; }
        .score-val-final { font-size: 18px; font-weight: 800; color: #7c1316; }

        .qr-code { margin: 5px auto; width: 60px; height: 60px; }
        .director-name { font-size: 16px; font-weight: bold; text-decoration: underline; margin-top: 5px; display: block; }
        .verification { position: absolute; bottom: 8mm; left: 12mm; font-size: 8px; color: #aaa; text-align: left; }
    </style>
</head>
<body>
    @php
        $avgCI = round($presentasi->penilaianDetails->avg('skor_angka') ?? 0, 1);
        $hurufCI = \App\Models\Presentasi::getPredikat($avgCI);
        $ketCI = \App\Models\Presentasi::getKeterangan($hurufCI);
        $hurufFinal = $presentasi->nilai ?? $hurufCI;
        $ketFinal = \App\Models\Presentasi::getKeterangan($hurufFinal);
        $qrData = "Sertifikat Sah RSUD SLG\nPenerima: " . ($nama_penerima ?? $presentasi->user->name) . "\nFinal: " . $hurufFinal;
    @endphp

    <div class="border-maroon"></div>
    <div class="white-area"></div>
    <div class="border-gold"></div>

    <div class="content-center">
        <!-- Header -->
        <div class="header-section">
            <img src="https://rsudslg.kedirikab.go.id/asset_compro/img/logo/Logo.png" style="width: 55px; margin-bottom: 5px;">
            <div class="hospital-name">RSUD Simpang Lima Gumul Kabupaten Kediri</div>
            <h1 class="main-title">SERTIFIKAT PENELITIAN</h1>
            <div class="sub-title">CERTIFICATE OF RESEARCH COMPLETION</div>
        </div>

        <!-- Nama -->
        <div class="student-section">
            <div class="label-to">DIBERIKAN KEPADA:</div>
            <div class="student-name">{{ $nama_penerima ?? $presentasi->user->name }}</div>
        </div>

        <!-- Detail Penelitian -->
        <div class="details-section">
            <div style="font-size: 15px;">Telah menyelesaikan kegiatan Penelitian pada RSUD Simpang Lima Gumul Kediri dengan judul:</div>
            <div class="research-title">"{{ $presentasi->praPenelitian->judul }}"</div>
            <div style="font-size: 13px; color: #555;">Periode: {{ \Carbon\Carbon::parse($presentasi->praPenelitian->tanggal_mulai)->format('d F Y') }} s.d. {{ $presentasi->tanggal_presentasi->format('d F Y') }}</div>
        </div>

        <!-- Tabel Nilai -->
        <div class="score-section">
            <table class="table-score">
                <tr>
                    <td width="50%">
                        <span class="score-head">Rata-rata Kolektif Pembimbing (CI)</span>
                        <span class="score-val">{{ $avgCI }} | {{ $hurufCI }} ({{ $ketCI }})</span>
                    </td>
                    <td width="50%" style="background: #fcf0f1;">
                        <span class="score-head">Hasil Penilaian Akhir (Final)</span>
                        <span class="score-val-final">{{ $hurufFinal }} ({{ $ketFinal }})</span>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Tanda Tangan -->
        <div class="footer-section">
            <div style="font-size: 12px;">Kediri, {{ now()->format('d F Y') }}</div>
            <div style="font-size: 12px; font-weight: bold;">DIREKTUR RSUD SIMPANG LIMA GUMUL</div>
            <div class="qr-code">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode($qrData) }}" style="width:100%;">
            </div>
            <span class="director-name">dr. TONY WIDYANTO, Sp.OG (K)</span>
            <div style="font-size: 10px;">NIP. 19750714 200212 1 006</div>
        </div>

        <!-- Verifikasi Kiri Bawah -->
        <div class="verification">
            Verification ID: {{ strtoupper(substr(md5($presentasi->id), 0, 10)) }}<br>
            Dokumen ini sah secara elektronik melalui Sistem SINDIKAT.
        </div>
    </div>
</body>
</html>