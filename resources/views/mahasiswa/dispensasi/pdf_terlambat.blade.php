<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Dispensasi Terlambat</title>
    <style>
        body { 
            font-family: "Times New Roman", Times, serif; 
            font-size: 12pt; 
            line-height: 1.5; 
            margin: 30px; 
        }
        
        /* --- STYLING KOP SURAT --- */
        .kop-surat { width: 100%; border-collapse: collapse; }
        .kop-surat td { vertical-align: middle; }
        .kop-text { text-align: center; }
        .kop-text h1 { font-size: 16pt; margin: 0; font-weight: bold; }
        .kop-text h2 { font-size: 18pt; margin: 0; font-weight: bold; }
        .kop-text p { font-size: 11pt; margin: 0; }
        
        /* Garis ganda di bawah kop (standar instansi) */
        .garis-kop {
            border-top: 3px solid black;
            border-bottom: 1px solid black;
            height: 2px;
            margin-top: 5px;
            margin-bottom: 20px;
        }

        /* --- STYLING KONTEN --- */
        .title { text-align: center; font-weight: bold; text-decoration: underline; font-size: 14pt; margin-bottom: 30px; }
        .content-table { width: 100%; margin-bottom: 20px; }
        .content-table td { vertical-align: top; padding: 4px; }
        
        .box-keterangan {
            padding: 15px; 
            border: 1px solid #000; 
            background-color: #f9f9f9;
            margin-bottom: 20px;
            font-style: italic;
        }

        /* --- STYLING TANDA TANGAN --- */
        .signature-container { width: 100%; margin-top: 50px; }
        .signature-box { width: 45%; float: right; text-align: center; }
        .signature-box img { max-width: 140px; height: auto; margin: 10px 0; }
        
        .footer-note { 
            clear: both; 
            margin-top: 60px; 
            font-size: 9pt; 
            font-style: italic; 
            color: #555; 
            text-align: center;
        }
    </style>
</head>
<body>

    <!-- KOP SURAT -->
    <table class="kop-surat">
        <tr>
            <td width="15%" align="center">
                <img src="https://rsudslg.kedirikab.go.id/asset_compro/img/logo/Logo.png" width="90">
            </td>
            <td width="85%" class="kop-text">
                <h1>PEMERINTAH KABUPATEN KEDIRI</h1>
                <h1>DINAS KESEHATAN</h1>
                <h2>UPTD RSUD SIMPANG LIMA GUMUL</h2>
                <p>JL. Galuh Candra Kirana Ds. Tugurejo Kec. Ngasem Telp.(0354)2891400</p>
                <p>Fax.(0354)2891414 Email : <span style="color: blue; text-decoration: underline;">rsud_slg@kedirikab.go.id</span> Website : rsudslg.kedirikab.go.id</p>
                <p style="font-weight: bold; letter-spacing: 2px;">K E D I R I</p>
            </td>
        </tr>
    </table>
    <div class="garis-kop"></div>
    <!-- END KOP SURAT -->

    <div class="title">{{ $jenisSurat ?? 'SURAT DISPENSASI KETERLAMBATAN ABSENSI' }}</div>

    <p>Yang bertanda tangan di bawah ini menerangkan bahwa:</p>

    <table class="content-table">
        <tr>
            <td style="width: 25%;">Nama</td>
            <td style="width: 2%;">:</td>
            <td><strong>{{ $mahasiswa->nm_mahasiswa }}</strong></td>
        </tr>
        <tr>
            <td>Program Studi</td>
            <td>:</td>
            <td>{{ $mahasiswa->prodi ?? '-' }}</td>
        </tr>
        <tr>
            <td>Asal Institusi</td>
            <td>:</td>
            <td>{{ $mahasiswa->mou->nama_universitas ?? $mahasiswa->univ_asal }}</td>
        </tr>
        <tr>
            <td>Ruangan Praktek</td>
            <td>:</td>
            <td><strong>{{ $dataTerlambat['ruangan'] }}</strong></td>
        </tr>
    </table>

    <p>{{ $aksiText ?? 'Mengajukan dispensasi keterlambatan absensi' }} pada tanggal <strong>{{ $tanggal }}</strong> dengan alasan / kronologi sebagai berikut:</p>
    
    <div class="box-keterangan">
        "{{ $keterangan }}"
    </div>

    <p>Demikian surat dispensasi ini dibuat dengan sebenar-benarnya dan telah disetujui serta divalidasi secara elektronik oleh pihak yang berwenang di ruangan terkait.</p>

    <div class="signature-container">
        <div class="signature-box">
            <p style="margin: 0;">Kediri, {{ $tanggal }}</p>
            <p style="margin: 0; font-weight: bold;">Mengetahui & Menyetujui,</p>
            
            <!-- Render Base64 Image -->
            <img src="{{ $dataTerlambat['ttd_penyetuju'] }}" alt="Tanda Tangan Penyetuju"><br>
            
            <strong style="text-decoration: underline;">{{ $dataTerlambat['nama_penyetuju'] }}</strong><br>
            <span>{{ $dataTerlambat['jabatan_penyetuju'] }}</span>
        </div>
    </div>

    <div class="footer-note">
        * Dokumen ini di-generate otomatis secara elektronik melalui sistem Sindikat RSUD SLG.<br>
        * Pihak penyetuju (Kepala Ruangan/CI/Kepala Tim Jaga) turut bertanggung jawab penuh atas validitas alasan keterlambatan.
    </div>
</body>
</html>