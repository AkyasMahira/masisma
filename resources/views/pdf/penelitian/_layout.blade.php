<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $surat->judulSurat() }} - {{ $surat->nomor_surat }}</title>
    <style>
        @page { margin: 0.9cm 1.9cm 1.9cm 2.1cm; }
        body { font-family: 'Times New Roman', Times, serif; font-size: 11pt; line-height: 1.27; color: #111; }

        /* ===== KOP ===== */
        .kop { width: 100%; border-collapse: collapse; }
        .kop td { vertical-align: middle; }
        .kop .logo, .kop .imbang { width: 72px; }   /* kolom kanan kosong selebar logo -> teks kop tepat di tengah halaman */
        .kop .logo { text-align: left; }
        .kop .logo img { width: 70px; }
        .kop .teks, .kop .teks p { text-align: center; }   /* dompdf tidak mewariskan text-align dari td ke p */
        .kop .pemkab { font-size: 12.5pt; letter-spacing: .5px; margin: 0; }
        .kop .rs { font-size: 13pt; font-weight: bold; color: #7c1316; margin: 1px 0 0; white-space: nowrap; }
        .kop .unit { font-size: 10.5pt; font-weight: bold; margin: 0 0 2px; }
        .kop .alamat { font-size: 8.5pt; margin: 0; color: #333; line-height: 1.25; text-align: center; }
        .garis-kop { border-top: 3px solid #7c1316; border-bottom: 1px solid #7c1316; height: 2px; margin: 6px 0 12px; }

        /* ===== HEADER SURAT ===== */
        .meta { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        .meta td { padding: 1px 0; vertical-align: top; }
        .meta .l { width: 78px; }
        .meta .s { width: 12px; }
        .tanggal-kanan { text-align: right; vertical-align: top; white-space: nowrap; }
        .tujuan { margin: 2px 0 10px; }
        .tujuan p { margin: 0; }
        .daftar-tujuan { border-collapse: collapse; margin: 0 0 0 28px; }
        .daftar-tujuan td { padding: 0; vertical-align: top; }
        .daftar-tujuan .no { width: 18px; }
        .judul { text-align: center; margin: 4px 0 2px; font-size: 13pt; font-weight: bold; text-decoration: underline; letter-spacing: .5px; }
        .nomor-judul { text-align: center; margin: 0 0 16px; }

        p { margin: 0 0 7px; text-align: justify; }
        .indent { text-indent: 36px; }

        /* ===== TABEL DATA ===== */
        .data { width: 100%; border-collapse: collapse; margin: 2px 0 8px 18px; }
        .data td { padding: 1px 0; vertical-align: top; }
        .data .l { width: 150px; }
        .data .s { width: 14px; }
        .data .v { padding-right: 18px; }

        ol.ketentuan { margin: 2px 0 8px 0; padding-left: 22px; }
        ol.ketentuan li { margin-bottom: 1px; text-align: justify; }

        /* ===== TTD ===== */
        .ttd { width: 100%; margin-top: 8px; page-break-inside: avoid; }
        .ttd td { vertical-align: top; }
        .ttd .blok { width: 270px; text-align: left; }
        .ttd .blok p { margin: 0; text-align: left; }
        .ttd .qr { margin: 4px 0 3px; }
        .ttd .qr img { width: 70px; height: 70px; }
        .ttd .nama { font-weight: bold; text-decoration: underline; }
        .tembusan-kiri { vertical-align: bottom !important; padding-right: 20px; }
        .tembusan { font-size: 9.5pt; line-height: 1.3; }
        .tembusan p { margin: 0; text-align: left; }

        /* ===== FOOTER ===== */
        .footer { position: fixed; bottom: -1.3cm; left: 0; right: 0; border-top: 1px solid #c9a227; padding-top: 4px; font-size: 8pt; color: #666; text-align: center; line-height: 1.3; }
        .badge-jenis { display: inline-block; border: 1px solid #7c1316; color: #7c1316; padding: 1px 8px; border-radius: 10px; font-size: 9.5pt; font-weight: bold; }
    </style>
</head>
<body>
    <div class="footer">
        Dokumen ini diterbitkan secara elektronik melalui Sistem Sindikat {{ $kop['singkat'] }}.<br>
        Keaslian surat dapat diperiksa dengan memindai QR Code pada tanda tangan. Nomor: {{ $surat->nomor_surat }}
    </div>

    {{-- KOP SURAT --}}
    <table class="kop">
        <tr>
            <td class="logo"><img src="{{ $logo }}" alt="Logo RSUD SLG"></td>
            <td class="teks">
                <p class="pemkab">{{ $kop['pemkab'] }}</p>
                <p class="rs">{{ $kop['nama'] }}</p>
                <p class="unit">{{ $kop['unit'] }}</p>
                <p class="alamat">{{ $kop['alamat'] }}</p>
                <p class="alamat">Telp. {{ $kop['telp'] }} &middot; Fax. {{ $kop['fax'] }} &middot; Email: {{ $kop['email'] }}</p>
                <p class="alamat">Laman: {{ $kop['web'] }}</p>
            </td>
            <td class="imbang"></td>
        </tr>
    </table>
    <div class="garis-kop"></div>

    @yield('isi')

    {{-- TANDA TANGAN --}}
    <table class="ttd">
        <tr>
            <td class="tembusan-kiri">
                @hasSection('tembusan')
                    <div class="tembusan">@yield('tembusan')</div>
                @endif
            </td>
            <td class="blok">
                {{-- Surat bertanggal di kanan atas (balasan/pengantar) tidak perlu tanggal ulang di sini --}}
                @if(!in_array($surat->jenis_surat, ['balasan', 'pengantar']))<p>{{ $kop['kota'] }}, {{ $surat->tanggal_surat->translatedFormat('d F Y') }}</p>@endif
                @if(!empty($atasNama))<p>a.n. {{ config('surat.penandatangan.direktur.jabatan') }}</p>@endif
                <p><strong>{{ $ttd['jabatan'] }},</strong></p>
                <div class="qr"><img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&margin=0&data={{ urlencode($urlVerif) }}" alt="QR Verifikasi"></div>
                <p class="nama">{{ $ttd['nama'] }}</p>
                @if($ttd['pangkat'])<p>{{ $ttd['pangkat'] }}</p>@endif
                <p>NIP. {{ $ttd['nip'] }}</p>
            </td>
        </tr>
    </table>

</body>
</html>
