<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $surat->judulSurat() }} - {{ $surat->nomor_surat }}</title>
    @php
        $details = $presentasi ? $presentasi->penilaianDetails : collect();
        $avgCI = round($details->avg('skor_angka') ?? 0, 2);
        $hurufCI = \App\Models\Presentasi::getPredikat($avgCI);
        $hurufFinal = optional($presentasi)->nilai ?: $hurufCI;
        $ketFinal = \App\Models\Presentasi::getKeterangan($hurufFinal);
        $penguji = $details->pluck('nama_ci')->filter()->unique()->values();
        $penerima = $namaPenerima ?: optional($pra->user)->name;
        $mulai = $pra->tanggal_mulai;
        $selesai = $presentasi && $presentasi->tanggal_presentasi ? \Carbon\Carbon::parse($presentasi->tanggal_presentasi) : $surat->tanggal_surat;
    @endphp
    <style>
        @page { margin: 0; }
        body { margin: 0; font-family: 'Times New Roman', Times, serif; color: #1f2937; }

        /* ===== BINGKAI (A4 lanskap: 297 x 210 mm) ===== */
        .bg { position: fixed; top: 0; left: 0; width: 297mm; height: 210mm; background: #fffdf8; }
        .frame-maroon { position: fixed; top: 7mm; left: 7mm; width: 283mm; height: 196mm; border: 3.2mm solid #7c1316; box-sizing: border-box; }
        .frame-gold { position: fixed; top: 12mm; left: 12mm; width: 273mm; height: 186mm; border: 0.7mm solid #c9a227; box-sizing: border-box; }
        .frame-gold2 { position: fixed; top: 13.6mm; left: 13.6mm; width: 269.8mm; height: 182.8mm; border: 0.25mm solid #c9a227; box-sizing: border-box; }
        .sudut { position: fixed; width: 16mm; height: 16mm; border-color: #c9a227; border-style: solid; }
        .s-tl { top: 16mm; left: 16mm; border-width: 1.2mm 0 0 1.2mm; }
        .s-tr { top: 16mm; right: 16mm; border-width: 1.2mm 1.2mm 0 0; }
        .s-bl { bottom: 16mm; left: 16mm; border-width: 0 0 1.2mm 1.2mm; }
        .s-br { bottom: 16mm; right: 16mm; border-width: 0 1.2mm 1.2mm 0; }
        .watermark { position: fixed; top: 52mm; left: 103.5mm; width: 90mm; opacity: 0.045; }

        /* ===== ISI ===== */
        .isi { position: fixed; top: 18mm; left: 26mm; width: 245mm; text-align: center; }
        .kop img { width: 14mm; }
        .kop .pemkab { font-size: 10pt; letter-spacing: 1.5px; margin: 1mm 0 0; color: #374151; }
        .kop .rs { font-size: 12pt; font-weight: bold; color: #7c1316; letter-spacing: 1px; margin: 0.5mm 0 0; }
        .judul { font-size: 34pt; font-weight: bold; color: #7c1316; letter-spacing: 9px; margin: 2.5mm 0 0; line-height: 1; }
        .sub { font-size: 11pt; letter-spacing: 4px; color: #a16207; margin: 1mm 0 0; text-transform: uppercase; }
        .nomor { font-size: 9pt; color: #6b7280; margin: 1mm 0 0; }
        .diberikan { font-size: 10.5pt; letter-spacing: 2px; color: #4b5563; margin: 4mm 0 0; text-transform: uppercase; }
        .nama { font-size: 25pt; font-weight: bold; font-style: italic; color: #111827; margin: 1.5mm 0 0; }
        .garis-nama { width: 150mm; margin: 1mm auto 0; border-top: 0.6mm solid #c9a227; }
        .institusi { font-size: 11pt; color: #4b5563; margin: 1.5mm 0 0; }
        .uraian { font-size: 11.5pt; margin: 3mm 18mm 0; line-height: 1.35; }
        .judul-pnl { font-size: 12pt; font-weight: bold; font-style: italic; color: #7c1316; margin: 1mm 22mm 0; line-height: 1.3; }
        .periode { font-size: 10pt; color: #4b5563; margin: 1.5mm 0 0; }
        .predikat { display: inline-block; margin-top: 2.5mm; border: 0.5mm solid #c9a227; background: #fdf6e3; border-radius: 6mm; padding: 1.3mm 7mm; font-size: 11.5pt; }
        .predikat b { color: #7c1316; font-size: 13pt; }

        /* ===== TANDA TANGAN ===== */
        .ttd { position: fixed; top: 147mm; left: 26mm; width: 245mm; border-collapse: collapse; }
        .ttd td { width: 33.3%; text-align: center; vertical-align: bottom; font-size: 10.5pt; padding: 0 4mm; }
        .ttd .jab { font-size: 9.5pt; color: #374151; margin: 0 0 1mm; line-height: 1.25; }
        .ttd .nama-ttd { font-weight: bold; border-top: 0.3mm solid #374151; display: inline-block; padding-top: 1mm; min-width: 52mm; }
        .ttd .qr img { width: 18mm; height: 18mm; }
        .ttd .nip { font-size: 9pt; color: #374151; }
        .verif { position: fixed; top: 190.5mm; left: 0; width: 297mm; text-align: center; font-size: 7.5pt; color: #9ca3af; }
    </style>
</head>
<body>
    <div class="bg"></div>
    <img class="watermark" src="{{ $logo }}" alt="">
    <div class="frame-maroon"></div>
    <div class="frame-gold"></div>
    <div class="frame-gold2"></div>
    <div class="sudut s-tl"></div><div class="sudut s-tr"></div><div class="sudut s-bl"></div><div class="sudut s-br"></div>

    <div class="isi">
        <div class="kop">
            <img src="{{ $logo }}" alt="RSUD SLG">
            <p class="pemkab">{{ $kop['pemkab'] }}</p>
            <p class="rs">{{ $kop['nama'] }}</p>
        </div>

        <p class="judul">SERTIFIKAT</p>
        <p class="sub">Penelitian &nbsp;·&nbsp; Certificate of Research Completion</p>
        <p class="nomor">Nomor: {{ $surat->nomor_surat }}</p>

        <p class="diberikan">Diberikan kepada</p>
        <p class="nama">{{ $penerima ?: '-' }}</p>
        <div class="garis-nama"></div>
        <p class="institusi">{{ $pra->prodi ? $pra->prodi . ' — ' : '' }}{{ $institusi }}</p>

        <p class="uraian">atas keberhasilannya menyelesaikan seluruh rangkaian kegiatan penelitian di {{ $kop['singkat'] }}@if($ruangan), Ruang {{ $ruangan }}@endif, dengan judul:</p>
        <p class="judul-pnl">"{{ $pra->judul }}"</p>
        <p class="periode">
            @if($mulai && $mulai->lte($selesai))
                Periode {{ $mulai->translatedFormat('d F Y') }} s.d. {{ $selesai->translatedFormat('d F Y') }}
            @else
                Diselesaikan pada {{ $selesai->translatedFormat('d F Y') }}
            @endif
        </p>
        <div class="predikat">Nilai Akhir: <b>{{ $hurufFinal }}</b> &nbsp;({{ $ketFinal }})@if($details->count()) &nbsp;·&nbsp; Rata-rata CI {{ number_format($avgCI, 2) }}@endif</div>
    </div>

    <table class="ttd">
        <tr>
            <td>
                <p class="jab">Penguji I</p>
                <span class="nama-ttd">{{ $penguji[0] ?? '..............................' }}</span>
            </td>
            <td>
                <p class="jab">{{ $kop['kota'] }}, {{ $surat->tanggal_surat->translatedFormat('d F Y') }}<br><strong>{{ $ttd['jabatan'] }}</strong></p>
                <div class="qr"><img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&margin=0&data={{ urlencode($urlVerif) }}" alt="QR Verifikasi"></div>
                <span class="nama-ttd">{{ $ttd['nama'] }}</span><br>
                <span class="nip">NIP. {{ $ttd['nip'] }}</span>
            </td>
            <td>
                <p class="jab">Penguji II</p>
                <span class="nama-ttd">{{ $penguji[1] ?? '..............................' }}</span>
            </td>
        </tr>
    </table>

    <div class="verif">Diterbitkan secara elektronik melalui Sistem Sindikat RSUD SLG · Keaslian dapat diperiksa dengan memindai QR Code</div>
</body>
</html>
