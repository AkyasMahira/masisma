<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Surat Balasan - {{ $data->nama_mahasiswa }}</title>
    <style>
        /* --- Reset & Base --- */
        body {
            font-family: Arial, Helvetica, sans-serif;
            /* Font standar surat resmi modern */
            font-size: 12pt;
            line-height: 1.5;
            color: #333;
            margin: 0;
            padding: 0;
        }

        /* --- Container --- */
        .container {
            width: 100%;
            padding: 0 15px;
        }

        /* --- Kop Surat (Opsional) --- */
        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h3 {
            margin: 0;
            font-size: 16pt;
            text-transform: uppercase;
            font-weight: bold;
        }

        .header p {
            margin: 2px 0;
            font-size: 10pt;
        }

        .line {
            border-bottom: 3px solid #7c1316;
            /* Warna Maroon Custom Anda */
            margin-top: 10px;
            margin-bottom: 2px;
        }

        .line-thin {
            border-bottom: 1px solid #7c1316;
            margin-bottom: 30px;
        }

        /* --- Judul Surat --- */
        .title-section {
            text-align: center;
            margin-bottom: 30px;
        }

        .title-section h2 {
            text-decoration: underline;
            margin: 0;
            font-size: 14pt;
            text-transform: uppercase;
            color: #000;
        }

        .title-section p {
            margin: 5px 0;
            font-size: 11pt;
        }

        /* --- Tabel Data (Agar titik dua sejajar) --- */
        .data-table {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }

        .data-table td {
            vertical-align: top;
            padding: 3px 0;
        }

        .label {
            width: 180px;
            /* Lebar kolom label */
            font-weight: bold;
        }

        .separator {
            width: 20px;
            text-align: center;
        }

        /* --- Section Data Dibutuhkan --- */
        .box-data {
            border: 1px solid #ddd;
            padding: 15px;
            background-color: #fcf0f1;
            /* Maroon subtle background */
            border-radius: 5px;
            margin-top: 10px;
            margin-bottom: 20px;
            font-size: 11pt;
        }

        /* --- Tanda Tangan --- */
        .signature-section {
            margin-top: 50px;
            width: 100%;
        }

        .signature-box {
            float: right;
            width: 40%;
            text-align: center;
        }

        .signature-name {
            margin-top: 70px;
            font-weight: bold;
            text-decoration: underline;
        }

        /* Helper untuk clear float */
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
    </style>
</head>

<body>

    <div class="header">
        <h3>RSUD SIMPANG LIMA GUMUL</h3>
        <p>Jalan Galuh Candrakirana, Tugurejo, Kec. Ngasem, Kabupaten Kediri, Jawa Timur 64182</p>
        <p>Telp: (0354) 2891400 | Email: rsudslg@kedirikab.go.id</p>
        <div class="line"></div>
        <div class="line-thin"></div>
    </div>

    <div class="container">
        <div class="title-section">
            <h2>SURAT KETERANGAN</h2>
            <p>
                Nomor:
                MAS/{{ date('Y') }}/{{ date('m') }}/{{ date('d') }}/{{ str_pad($data->id, 3, '0', STR_PAD_LEFT) }}
            </p>


        </div>

        <p>Dengan ini menerangkan bahwa mahasiswa di bawah ini:</p>

        <table class="data-table">
            <tr>
                <td class="label">Nama Mahasiswa</td>
                <td class="separator">:</td>
                <td>{{ $data->nama_mahasiswa }}</td>
            </tr>
            <tr>
                <td class="label">NIM</td>
                <td class="separator">:</td>
                <td>{{ $data->nim }}</td>
            </tr>
            <tr>
                <td class="label">Program Studi</td>
                <td class="separator">:</td>
                <td>{{ $data->prodi }}</td>
            </tr>
            <tr>
                <td class="label">Asal Universitas</td>
                <td class="separator">:</td>
                <td>{{ $data->mou ? ($data->mou->nama_instansi ?? $data->mou->nama_universitas) : '-' }}</td>
            </tr>
            <tr>
                <td class="label">No. WhatsApp</td>
                <td class="separator">:</td>
                <td>{{ $data->wa_mahasiswa }}</td>
            </tr>
        </table>

        <p>Telah kami terima pengajuannya untuk melaksanakan kegiatan dengan detail sebagai berikut:</p>

        <table class="data-table">
            <tr>
                <td class="label">Keperluan</td>
                <td class="separator">:</td>
                <td>{{ $data->keperluan }}</td>
            </tr>
            <tr>
                <td class="label">Masa Berlaku MOU</td>
                <td class="separator">:</td>
                <td>
                    {{ \Carbon\Carbon::parse($data->mou->tanggal_masuk)->format('d M Y') }}
                    s/d
                    {{ \Carbon\Carbon::parse($data->mou->tanggal_keluar)->format('d M Y') }}
                </td>
            </tr>
            <tr>
                <td class="label">Lama Berlaku Surat</td>
                <td class="separator">:</td>
                <td>{{ $data->lama_berlaku }}</td>
            </tr>
        </table>

        <p style="margin-bottom: 5px;"><strong>Data / Akses yang dibutuhkan:</strong></p>
        <div class="box-data">
            {!! nl2br(e($data->data_dibutuhkan)) !!}
        </div>

        <p>Demikian surat balasan ini dibuat untuk dapat dipergunakan sebagaimana mestinya.</p>

        <div class="signature-section clearfix">
            <div class="signature-box">
                <p>Kediri, {{ date('d F Y') }}</p>
                <p>Koordinator Diklat,</p>

                <div class="signature-name">
                    ( HARDITYO FAJARSIWI, .Kep.Ns.,M.Kep. )
                </div>
                <p>NIP.19841028 200901 1 005</p>
            </div>
        </div>

    </div>

</body>

</html>
