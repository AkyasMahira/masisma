<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Surat Balasan - {{ $data->nama_mahasiswa }}</title>
    <style>
        /* --- Reset & Base --- */
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11pt; /* Disesuaikan: Dikecilkan sedikit (dari 12pt) agar lebih padat */
            line-height: 1.4; /* Disesuaikan: Dikecilkan sedikit agar lebih padat */
            color: #333;
            margin: 0;
            padding: 0;
        }

        /* --- Container --- */
        .container {
            width: 100%;
            padding: 0 15px;
        }

        /* --- Kop Surat --- */
        .header {
            text-align: center;
            margin-bottom: 10px; /* Disesuaikan: Dikecilkan */
            padding-top: 10px; /* Jaga jarak dari tepi atas halaman */
        }

        .header .top-org {
            margin: 0;
            font-size: 10pt;
            font-weight: normal;
            text-transform: uppercase;
        }

        .header .main-org {
            margin: 0;
            font-size: 16pt; /* Disesuaikan: Dikecilkan dari 18pt */
            text-transform: uppercase;
            font-weight: bold;
        }

        .header .contact-info {
            margin: 1px 0; /* Disesuaikan: Jarak antar baris kontak dikurangi */
            font-size: 9pt; /* Disesuaikan: Dikecilkan */
        }

        .line {
            border-bottom: 3px solid #000;
            margin-top: 5px; /* Disesuaikan: Jarak garis dikurangi */
            margin-bottom: 1px;
        }

        .line-thin {
            border-bottom: 1px solid #000;
            margin-bottom: 20px; /* Disesuaikan: Jarak garis dengan isi surat dikurangi */
        }

        /* --- Judul Surat --- */
        .title-section {
            text-align: center;
            margin-bottom: 20px; /* Disesuaikan: Jarak dikurangi */
        }

        .title-section h2 {
            text-decoration: underline;
            margin: 0;
            font-size: 13pt; /* Disesuaikan: Dikecilkan dari 14pt */
            text-transform: uppercase;
            color: #000;
        }

        .title-section p {
            margin: 3px 0; /* Disesuaikan: Jarak dikurangi */
            font-size: 10pt; /* Disesuaikan: Dikecilkan */
        }

        /* --- Tabel Data --- */
        .data-table {
            width: 100%;
            margin-bottom: 10px; /* Disesuaikan: Jarak antar tabel dikurangi */
            border-collapse: collapse;
        }

        .data-table td {
            vertical-align: top;
            padding: 1px 0; /* Disesuaikan: Padding dikurangi */
        }

        .label {
            width: 180px;
            font-weight: bold;
        }

        .separator {
            width: 20px;
            text-align: center;
        }

        /* --- Section Data Dibutuhkan (Perbaikan Pemformatan) --- */
        .box-data {
            border: 1px solid #ddd;
            padding: 10px 15px; /* Disesuaikan: Padding dikurangi */
            background-color: #fcf0f1;
            border-radius: 5px;
            margin-top: 5px; /* Disesuaikan: Jarak dikurangi */
            margin-bottom: 15px; /* Disesuaikan: Jarak dikurangi */
            font-size: 10pt; /* Disesuaikan: Dikecilkan */
        }

        .box-data ul {
            list-style-type: disc;
            margin: 0;
            padding-left: 20px;
        }

        .box-data li {
            margin-bottom: 2px;
        }

        /* --- Tanda Tangan --- */
        .signature-section {
            margin-top: 30px; /* Disesuaikan: Jarak dikurangi */
            width: 100%;
        }

        .signature-box {
            float: right;
            width: 45%;
            text-align: center;
        }
        
        /* Gaya untuk area QR Code */
        .qr-placeholder {
            height: 60px; /* Disesuaikan: Dikecilkan agar hemat ruang */
            width: 60px;
            margin: 5px auto 5px auto;
            font-size: 8pt;
            line-height: 60px;
            text-align: center;
        }

        .signature-name {
            margin-top: 5px;
            font-weight: bold;
            text-decoration: underline;
        }
        
        .signature-box p {
             margin: 0;
             line-height: 1.2;
        }

        .esig-info {
            margin-top: 5px;
            font-size: 7pt; /* Disesuaikan: Dikecilkan */
            line-height: 1.1;
            color: #555;
            padding: 0 5px;
        }

        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
    </style>
</head>

<body>

    <div class="header">
        <p class="top-org">PEMERINTAH KABUPATEN KEDIRI</p>
        <h3 class="main-org">RSUD SIMPANG LIMA GUMUL</h3>
        <p class="contact-info">Jalan Galuh Candrakirana, Tugurejo, Kec. Ngasem, Kabupaten Kediri, Jawa Timur 64182</p>
        <p class="contact-info">Telp: (0354) 2891400 | Email: rsudslg@kedirikab.go.id</p>
        <div class="line"></div>
        <div class="line-thin"></div>
    </div>

    <div class="container">
        <div class="title-section">
            <h2>SURAT KETERANGAN</h2>
            <p>
                Nomor:
                DIK/{{ date('Y') }}/{{ date('m') }}/{{ date('d') }}/{{ str_pad($data->id, 3, '0', STR_PAD_LEFT) }}
            </p>
        </div>

        <p style="margin-top: 0;">Dengan ini menerangkan bahwa mahasiswa di bawah ini:</p>

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
            @php
                // Cek apakah data_dibutuhkan adalah string JSON, jika ya, decode menjadi array
                $data_list = [];
                try {
                    $decoded_data = json_decode($data->data_dibutuhkan, true);
                    if (is_array($decoded_data)) {
                        $data_list = $decoded_data;
                    } else {
                        // Jika bukan JSON array, perlakukan sebagai string biasa
                        $data_list = explode("\n", $data->data_dibutuhkan);
                    }
                } catch (\Exception $e) {
                    // Jika decoding gagal, perlakukan sebagai string biasa
                    $data_list = explode("\n", $data->data_dibutuhkan);
                }
            @endphp

            @if (!empty($data_list))
                <ul>
                    @foreach ($data_list as $item)
                        <li>{{ trim($item) }}</li>
                    @endforeach
                </ul>
            @else
                <p>Tidak ada data/akses spesifik yang dibutuhkan.</p>
            @endif
            </div>

        <p>Demikian surat balasan ini dibuat untuk dapat dipergunakan sebagaimana mestinya.</p>

        <div class="signature-section clearfix">
            <div class="signature-box">
                <p>Kediri, {{ date('d F Y') }}</p>
                <p>Koordinator Diklat,</p>

                <div class="qr-placeholder" style="/* Tambahkan style untuk menampilkan QR code di sini */">
                    [AREA QR CODE]
                </div>

                <div class="signature-name">
                    ( HARDITYO FAJARSIWI, Kep.Ns., M.Kep. )
                </div>
                <p>NIP.19841028 200901 1 005</p>

                <p class="esig-info">
                    <small>Dokumen ini telah ditandatangani secara elektronik</small>
                </p>
            </div>
        </div>
    </div>

</body>

</html>