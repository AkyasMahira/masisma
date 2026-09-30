<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Surat Keterangan Selesai Pengambilan Data</title>
    <style>
        @page { margin: 1.5cm 2cm; }
        body { 
            font-family: 'Times New Roman', Times, serif; 
            font-size: 12pt; 
            line-height: 1.5; 
            color: #000;
        }
        
        .kop-container {
            border-bottom: 3px double #000;
            padding-bottom: 10px;
            margin-bottom: 30px;
            text-align: center;
            position: relative;
        }
        .logo {
            position: absolute;
            left: 0;
            top: 0;
            width: 75px;
        }
        .kop-text h2 { margin: 0; font-size: 14pt; text-transform: uppercase; }
        .kop-text h1 { margin: 0; font-size: 16pt; text-transform: uppercase; font-weight: bold; }
        .kop-text p { margin: 0; font-size: 10pt; font-style: italic; }

        .judul-surat {
            text-align: center;
            font-weight: bold;
            text-decoration: underline;
            font-size: 14pt;
            margin-bottom: 40px;
            text-transform: uppercase;
        }

        .data-box {
            margin-bottom: 30px;
        }
        .data-row {
            margin-bottom: 10px;
            overflow: hidden;
        }
        .label {
            float: left;
            width: 150px;
            font-weight: normal;
        }
        .colon {
            float: left;
            width: 20px;
        }
        .value {
            float: left;
            width: 380px;
            font-weight: bold;
        }

        .content {
            text-align: justify;
            margin-bottom: 40px;
        }

        .footer-container {
            margin-top: 20px;
        }
        .ttd-wrapper {
            float: right;
            width: 300px;
            text-align: center;
        }
        .qr-code {
            margin: 10px 0;
        }
        .qr-code img {
            width: 90px;
            height: 90px;
            border: 1px solid #000;
            padding: 3px;
        }
        .ttd-name {
            font-weight: bold;
            /*text-decoration: underline;*/
            margin-bottom: 0;
        }
        .ttd-nip {
            margin-top: 0;
            font-size: 11pt;
        }
        
        .clear { clear: both; }
    </style>
</head>
<body>
    <div class="kop-container">
        <img src="https://rsudslg.kedirikab.go.id/asset_compro/img/logo/Logo.png" class="logo">
        <div class="kop-text">
            <h2>PEMERINTAH KABUPATEN KEDIRI</h2>
            <h1>RSUD SIMPANG LIMA GUMUL</h1>
            <p>Jl. Galuh Candra Kirana No. 1 Kec. Ngasem Kab. Kediri (64182)</p>
            <p>Telp. (0354) 2891400 | Email: rsudslg@kedirikab.go.id</p>
        </div>
    </div>

    <p class="judul-surat">SURAT KETERANGAN SELESAI</p>

    <div class="content">
        <p>Menerangkan bahwa identitas di bawah ini :</p>
    </div>

<table style="width: 100%; border-collapse: collapse; margin-bottom: 30px; color: #000; font-family: 'Times New Roman', Times, serif;">
    <tr>
        <td style="width: 150px; vertical-align: top; padding-bottom: 8px;">Nama</td>
        <td style="width: 20px; vertical-align: top; padding-bottom: 8px;">:</td>
        <td style=" vertical-align: top; padding-bottom: 8px; ">
            {{ $pengajuan->user->name ?? '-' }}
        </td>
    </tr>
    <tr>
        <td style="vertical-align: top; padding-bottom: 8px;">Asal Instansi</td>
        <td style="vertical-align: top; padding-bottom: 8px;">:</td>
        <td style=" vertical-align: top; padding-bottom: 8px;">
            {{ $pengajuan->user->mou->nama_universitas ?? ($pengajuan->user->mou->nama_instansi ?? '-') }}
        </td>
    </tr>
    <tr>
        <td style="vertical-align: top; padding-bottom: 8px;">Ruang Penelitian</td>
        <td style="vertical-align: top; padding-bottom: 8px;">:</td>
        <td style=" vertical-align: top; padding-bottom: 8px;">
            {{ $pengajuan->dataRuangan->nm_ruangan ?? '-' }}
        </td>
    </tr>
    <tr>
        <td style="vertical-align: top; padding-bottom: 8px;">Judul Penelitian</td>
        <td style="vertical-align: top; padding-bottom: 8px;">:</td>
        <td style=" vertical-align: top; padding-bottom: 8px;">
            "{{ $praPenelitian->judul ?? '-' }}"
        </td>
    </tr>
</table>

    <div class="content">
        <p>Telah selesai melaksanakan kegiatan pengambilan <strong>{{ $praPenelitian->jenis_penelitian }}</strong> di RSUD Simpang Lima Gumul Kabupaten Kediri pada tanggal {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}.</p>
        
        <p>Demikian surat keterangan ini dibuat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya.</p>
    </div>

    <div class="footer-container">
        <div class="ttd-wrapper">
            <p style="margin-bottom: 5px;">Kediri, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
            <p style="margin: 0;"><strong>Koordinator Unit Diklat,</strong></p>
            
            <div class="qr-code">
                {{-- QR Code berisi verifikasi data --}}
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode('Dokumen Sah RSUD SLG. Nama: ' . $pengajuan->user->name . ' | Judul: ' . $praPenelitian->judul . ' | Koordinator: Hardityo Fajarsiwi') }}" alt="QR Code TTD">
            </div>

            <p class="ttd-name">Hardityo Fajarsiwi, S.Kep.Ns.,M.Kep</p>
            <p class="ttd-nip">NIP. 198410282009011005</p>
        </div>
    </div>
    <div class="clear"></div>

</body>
</html>