<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $surat->judulSurat() }} - {{ $surat->nomor_surat }}</title>
    <style>
        @page { margin: 1.1cm 2cm 2.2cm 2.2cm; }
        body { font-family: 'Times New Roman', Times, serif; font-size: 12pt; line-height: 1.45; color: #111; }

        /* ===== KOP ===== */
        .kop { width: 100%; border-collapse: collapse; }
        .kop td { vertical-align: middle; }
        .kop .logo { width: 88px; text-align: left; }
        .kop .logo img { width: 82px; }
        .kop .teks { text-align: center; padding-right: 88px; }
        .kop .pemkab { font-size: 13pt; letter-spacing: .5px; margin: 0; }
        .kop .rs { font-size: 15.5pt; font-weight: bold; color: #7c1316; margin: 1px 0 0; letter-spacing: .3px; }
        .kop .unit { font-size: 11pt; font-weight: bold; margin: 1px 0 3px; }
        .kop .alamat { font-size: 9pt; margin: 0; color: #333; line-height: 1.3; }
        .garis-kop { border-top: 3px solid #7c1316; border-bottom: 1px solid #7c1316; height: 2px; margin: 7px 0 16px; }

        /* ===== HEADER SURAT ===== */
        .meta { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        .meta td { padding: 1px 0; vertical-align: top; }
        .meta .l { width: 78px; }
        .meta .s { width: 12px; }
        .tanggal-kanan { text-align: right; vertical-align: top; white-space: nowrap; }
        .tujuan { margin: 4px 0 14px; }
        .tujuan p { margin: 0; }
        .judul { text-align: center; margin: 4px 0 2px; font-size: 13pt; font-weight: bold; text-decoration: underline; letter-spacing: .5px; }
        .nomor-judul { text-align: center; margin: 0 0 16px; }

        p { margin: 0 0 9px; text-align: justify; }
        .indent { text-indent: 36px; }

        /* ===== TABEL DATA ===== */
        .data { width: 100%; border-collapse: collapse; margin: 4px 0 12px 18px; }
        .data td { padding: 2px 0; vertical-align: top; }
        .data .l { width: 150px; }
        .data .s { width: 14px; }
        .data .v { padding-right: 18px; }

        ol.ketentuan { margin: 2px 0 12px 0; padding-left: 22px; }
        ol.ketentuan li { margin-bottom: 3px; text-align: justify; }

        /* ===== TTD ===== */
        .ttd { width: 100%; margin-top: 18px; page-break-inside: avoid; }
        .ttd td { vertical-align: top; }
        .ttd .blok { width: 270px; text-align: left; }
        .ttd .blok p { margin: 0; text-align: left; }
        .ttd .qr { margin: 6px 0 4px; }
        .ttd .qr img { width: 82px; height: 82px; }
        .ttd .nama { font-weight: bold; text-decoration: underline; }
        .tembusan { margin-top: 16px; font-size: 10.5pt; }
        .tembusan p { margin: 0; text-align: left; }

        /* ===== FOOTER ===== */
        .footer { position: fixed; bottom: -1.5cm; left: 0; right: 0; border-top: 1px solid #c9a227; padding-top: 4px; font-size: 8pt; color: #666; text-align: center; line-height: 1.3; }
        .badge-jenis { display: inline-block; border: 1px solid #7c1316; color: #7c1316; padding: 1px 8px; border-radius: 10px; font-size: 9.5pt; font-weight: bold; }
    </style>
</head>
<body>
    <div class="footer">
        Dokumen ini diterbitkan secara elektronik melalui Sistem Sindikat {{ $kop['singkat'] }}.<br>
        Keaslian surat dapat diperiksa dengan memindai QR Code atau membuka {{ $urlVerif }}
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
                <p class="alamat">Telp. {{ $kop['telp'] }} &nbsp;|&nbsp; Fax. {{ $kop['fax'] }} &nbsp;|&nbsp; Email: {{ $kop['email'] }} &nbsp;|&nbsp; {{ $kop['web'] }}</p>
            </td>
        </tr>
    </table>
    <div class="garis-kop"></div>

    @yield('isi')

    {{-- TANDA TANGAN --}}
    <table class="ttd">
        <tr>
            <td></td>
            <td class="blok">
                <p>{{ $kop['kota'] }}, {{ $surat->tanggal_surat->translatedFormat('d F Y') }}</p>
                @if(!empty($atasNama))<p>a.n. {{ config('surat.penandatangan.direktur.jabatan') }}</p>@endif
                <p><strong>{{ $ttd['jabatan'] }},</strong></p>
                <div class="qr"><img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&margin=0&data={{ urlencode($urlVerif) }}" alt="QR Verifikasi"></div>
                <p class="nama">{{ $ttd['nama'] }}</p>
                @if($ttd['pangkat'])<p>{{ $ttd['pangkat'] }}</p>@endif
                <p>NIP. {{ $ttd['nip'] }}</p>
            </td>
        </tr>
    </table>

    @hasSection('tembusan')
        <div class="tembusan">@yield('tembusan')</div>
    @endif
</body>
</html>
