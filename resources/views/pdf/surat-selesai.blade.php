<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Surat Keterangan Selesai Penelitian</title>

    <style>
        /* --- HALAMAN --- */
        @page {
            margin: 1cm 2cm 1.5cm 2cm;
        }
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 11pt;
            line-height: 1.3;
            color: #000;
        }

        /* --- HEADER (KOP SURAT) --- */
        .header { 
            text-align: center; 
            margin-bottom: 5px; 
            position: relative; 
            padding-top: 5px;
        }
        .header .logo-kop {
            position: absolute;
            left: 0;
            top: 0;
            width: 70px; /* Ukuran Logo */
            height: auto;
        }
        .header .top-org { 
            margin: 0; 
            font-size: 12pt; 
            text-transform: uppercase; 
            letter-spacing: 0.5px; 
        }
        .header .main-org { 
            margin: 0; 
            font-size: 16pt; 
            font-weight: bold; 
            text-transform: uppercase; 
            color: #000; 
        }
        .header .contact-info { 
            margin: 1px 0; 
            font-size: 8.5pt; 
            font-style: italic; 
        }
        .line-thick { border-bottom: 2.5px solid #000; margin-top: 5px; }
        .line-thin { border-bottom: 1px solid #000; margin-top: 1.5px; margin-bottom: 20px; }

        /* --- TITLE --- */
        .title { 
            text-align: center; 
            margin-bottom: 15px; 
            font-weight: bold; 
            font-size: 13pt; 
            text-decoration: underline; 
        }
        .subtitle {
            margin: 4px 0 0 0; 
            font-weight: normal; 
            font-size: 10pt;
            text-decoration: none;
        }

        /* --- CONTENT --- */
        .content p { margin: 8px 0; }
        .data-table { margin-left: 0.3in; margin-bottom: 10px; }
        .data-table td { padding: 1.5px 0; vertical-align: top; }

        /* --- ASSESSMENT TABLE --- */
        .assessment-table {
            width: 100%;
            border-collapse: collapse;
            margin: 12px 0;
            font-size: 9.5pt;
        }
        .assessment-table th {
            background-color: #f2f2f2;
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
            text-transform: uppercase;
        }
        .assessment-table td {
            border: 1px solid #000;
            padding: 5px;
        }

        .final-score-box {
            border: 1.5px solid #000;
            padding: 8px;
            text-align: center;
            margin: 15px 0;
            background-color: #f9f9f9;
        }

        /* --- SIGNATURE AREA --- */
        .signature-table {
            width: 100%;
            margin-top: 20px;
            page-break-inside: avoid;
        }

        .qr-placeholder {
            width: 75px;
            height: 75px;
            margin: 5px auto;
        }
    </style>
</head>
<body>

    @php
        // Perhitungan Kolektif
        $avgAngka = round($presentasi->penilaianDetails->avg('skor_angka') ?? 0, 2);
        $hurufKolektif = \App\Models\Presentasi::getPredikat($avgAngka);
        $ketKolektif = \App\Models\Presentasi::getKeterangan($hurufKolektif);

        // Nilai Akhir
        $skorAkhir = $presentasi->skor_total ?? $avgAngka;
        $hurufAkhir = $presentasi->nilai;
        $ketAkhir = \App\Models\Presentasi::getKeterangan($hurufAkhir);
        
        $qrData = "Surat Keterangan Selesai Penelitian SAH\nNama: " . ($nama_penerima ?? $presentasi->user->name) . "\nNilai Akhir: " . $skorAkhir . " (" . $hurufAkhir . ")";
    @endphp

    <!-- HEADER -->
    <div class="header">
        <!-- Logo Kop -->
        <img src="https://rsudslg.kedirikab.go.id/asset_compro/img/logo/Logo.png" class="logo-kop">
        
        <p class="top-org">PEMERINTAH KABUPATEN KEDIRI</p>
        <h2 class="main-org">RSUD SIMPANG LIMA GUMUL</h2>
        <p class="contact-info">Jl. Galuh Candrakirana, Tugurejo, Kec. Ngasem, Kabupaten Kediri, Jawa Timur 64182</p>
        <p class="contact-info">Website: rsudslg.kedirikab.go.id | Email: rsudslg@kedirikab.go.id</p>
        <div class="line-thick"></div>
        <div class="line-thin"></div>
    </div>

    <!-- TITLE -->
    <div class="title">
        SURAT KETERANGAN SELESAI PENELITIAN
        <p class="subtitle">Nomor: {{ date('Y') }}/SKP-SLG/{{ date('m') }}/{{ str_pad($presentasi->id, 4, '0', STR_PAD_LEFT) }}</p>
    </div>

    <!-- CONTENT -->
    <div class="content">
        <p>Yang bertanda tangan di bawah ini, Direktur RSUD Simpang Lima Gumul Kediri menerangkan bahwa:</p>

        <table class="data-table" width="100%">
            <tr>
                <td width="140">Nama</td>
                <td width="10">:</td>
                <td><strong>{{ $nama_penerima ?? $presentasi->user->name }}</strong></td>
            </tr>
            <tr>
                <td>Instansi/Universitas</td>
                <td>:</td>
                <td>{{ $presentasi->praPenelitian->mou ? ($presentasi->praPenelitian->mou->nama_instansi ?? $presentasi->praPenelitian->mou->nama_universitas) : '-' }}</td>
            </tr>
            <tr>
                <td>Program Studi</td>
                <td>:</td>
                <td>{{ $presentasi->praPenelitian->prodi }}</td>
            </tr>
            <tr>
                <td>Judul Penelitian</td>
                <td>:</td>
                <td style="font-weight: bold; font-style: italic;">"{{ $presentasi->praPenelitian->judul }}"</td>
            </tr>
        </table>

        <p>Telah menyelesaikan seluruh rangkaian kegiatan penelitian yang dilaksanakan pada tanggal <strong>{{ \Carbon\Carbon::parse($presentasi->praPenelitian->tanggal_mulai)->translatedFormat('d F Y') }}</strong> sampai dengan <strong>{{ $presentasi->tanggal_presentasi->translatedFormat('d F Y') }}</strong> dengan rincian penilaian sebagai berikut:</p>

        <!-- RINCIAN PENILAIAN PER CI -->
        <table class="assessment-table">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="40%">Nama Pembimbing (CI)</th>
                    <th width="15%">Skor Angka</th>
                    <th width="15%">Huruf</th>
                    <th width="25%">Predikat</th>
                </tr>
            </thead>
            <tbody>
                @foreach($presentasi->penilaianDetails as $index => $detail)
                @php 
                    $h = \App\Models\Presentasi::getPredikat($detail->skor_angka);
                @endphp
                <tr>
                    <td align="center">{{ $index + 1 }}</td>
                    <td>{{ $detail->nama_ci }}</td>
                    <td align="center">{{ number_format($detail->skor_angka, 2) }}</td>
                    <td align="center">{{ $h }}</td>
                    <td align="center">{{ \App\Models\Presentasi::getKeterangan($h) }}</td>
                </tr>
                @endforeach
                <tr style="font-weight: bold; background-color: #f9f9f9;">
                    <td colspan="2" align="right">RATA-RATA KOLEKTIF :</td>
                    <td align="center">{{ number_format($avgAngka, 2) }}</td>
                    <td align="center">{{ $hurufKolektif }}</td>
                    <td align="center">{{ $ketKolektif }}</td>
                </tr>
            </tbody>
        </table>
  <p>Setelah dilakukan revisi sehingga didapatkan nilai</strong> dengan rincian  sebagai berikut:</p>

        <!-- NILAI AKHIR DISAHKAN -->
        <div class="final-score-box">
            <span style="font-size: 9pt; text-transform: uppercase; letter-spacing: 1px;">Nilai Akhir Kelulusan (Final)</span><br>
            <span style="font-size: 16pt; font-weight: bold;"> {{ $hurufAkhir }}</span><br>
            <span style="font-size: 11pt; font-style: italic;">({{ $ketAkhir }})</span>
        </div>

        <p>Demikian surat keterangan ini dibuat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya.</p>
    </div>

    <!-- SIGNATURE -->
    <table class="signature-table">
        <tr>
            <td width="55%">
                <div style="font-size: 8pt; border: 1px solid #ccc; padding: 5px; width: 220px; color: #666; line-height: 1.2;">
                    <strong>Catatan Sistem:</strong><br>
                    ID Verifikasi: {{ strtoupper(substr(md5($presentasi->id), 0, 10)) }}<br>
                    Dokumen ini diterbitkan secara elektronik oleh Sistem SINDIKAT RSUD SLG dan dapat diverifikasi melalui QR Code.
                </div>
            </td>
            <td width="45%" align="center">
                <p style="margin: 0 0 4px 0;">Kediri, {{ now()->translatedFormat('d F Y') }}</p>
                <p style="margin: 0 0 5px 0; font-weight: bold; font-size:11pt;">Direktur RSUD Simpang Lima Gumul,</p>

                <div class="qr-placeholder">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode($qrData) }}" style="width: 100%;">
                </div>

                <p style="margin: 5px 0 0 0; font-weight: bold; text-decoration: underline;">dr. Tony Widyanto, Sp.OG (K)</p>
                <p style="margin: 0;">NIP. 19750714 200212 1 006</p>
            </td>
        </tr>
    </table>

</body>
</html>