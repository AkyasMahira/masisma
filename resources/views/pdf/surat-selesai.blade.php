<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Surat Keterangan Selesai Penelitian</title>
    <style>
        @page {
            margin: 2cm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.6;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #000;
            padding-bottom: 10px;
        }
        .header h2 {
            margin: 5px 0;
            font-size: 18pt;
        }
        .header p {
            margin: 3px 0;
            font-size: 10pt;
        }
        .title {
            text-align: center;
            margin: 30px 0;
            text-decoration: underline;
            font-weight: bold;
            font-size: 14pt;
        }
        .content {
            text-align: justify;
            margin: 20px 0;
        }
        .content p {
            margin: 10px 0;
        }
        .data-mahasiswa {
            margin-left: 50px;
        }
        .signature {
            margin-top: 50px;
            text-align: right;
        }
        .signature-box {
            display: inline-block;
            text-align: center;
            min-width: 200px;
        }
        .signature-line {
            margin-top: 80px;
            border-top: 1px solid #000;
            padding-top: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>RUMAH SAKIT [NAMA RUMAH SAKIT]</h2>
        <p>Jl. Alamat Rumah Sakit, Kota, Provinsi</p>
        <p>Telp: (021) 1234567 | Email: info@rumahsakit.com</p>
    </div>

    <div class="title">
        SURAT KETERANGAN SELESAI PENELITIAN<br>
        Nomor: {{ date('Y') }}/SK-PENELITIAN/{{ date('m') }}/{{ $presentasi->id }}
    </div>

    <div class="content">
        <p>Yang bertanda tangan di bawah ini menerangkan bahwa:</p>

        <div class="data-mahasiswa">
            <table style="margin: 20px 0;">
                <tr>
                    <td width="150">Nama</td>
                    <td>: <strong>{{ $nama_penerima ?? $presentasi->user->name }}</strong></td>
                </tr>
                <tr>
                    <td>Email</td>
                    <td>: {{ $presentasi->user->email }}</td>
                </tr>
                <tr>
                    <td>Universitas</td>
                    <td>: {{ $presentasi->praPenelitian->mou->nama_universitas ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Program Studi</td>
                    <td>: {{ $presentasi->praPenelitian->prodi }}</td>
                </tr>
            </table>
        </div>

        <p>Telah menyelesaikan kegiatan pra-penelitian dengan judul:</p>
        <p style="margin-left: 50px; font-weight: bold; font-style: italic;">
            "{{ $presentasi->praPenelitian->judul }}"
        </p>

        <p>Kegiatan penelitian dilaksanakan pada:</p>
        <div class="data-mahasiswa">
            <table>
                <tr>
                    <td width="150">Tanggal Mulai</td>
                    <td>: {{ \Carbon\Carbon::parse($presentasi->praPenelitian->tanggal_mulai)->format('d F Y') }}</td>
                </tr>
                <tr>
                    <td>Tanggal Presentasi</td>
                    <td>: {{ $presentasi->tanggal_presentasi->format('d F Y') }}</td>
                </tr>
                <tr>
                    <td>Pembimbing (CI)</td>
                    <td>: {{ $presentasi->pengajuan->ci_nama }}</td>
                </tr>
                <tr>
                    <td>Nilai Akhir</td>
                    <td>: <strong>{{ $presentasi->nilai }}</strong></td>
                </tr>
            </table>
        </div>

        <p>Demikian surat keterangan ini dibuat untuk dapat dipergunakan sebagaimana mestinya.</p>

        <p style="margin-top: 30px;"><strong>Catatan Penting:</strong></p>
        <ul style="margin-left: 30px;">
            <li>Seluruh data dan hasil penelitian menjadi aset Rumah Sakit.</li>
            <li>Mahasiswa wajib mengambil seluruh dokumen fisik yang tertinggal.</li>
            <li>Surat ini berlaku untuk keperluan administrasi akademik.</li>
        </ul>
    </div>

    <div class="signature">
        <div class="signature-box">
            <p>{{ date('d F Y') }}</p>
            <p><strong>Kepala Bagian Penelitian</strong></p>
            <div class="signature-line">
                <strong>[Nama Kepala Bagian]</strong><br>
                NIP. XXXXXXXXXXXXXXX
            </div>
        </div>
    </div>
</body>
</html>