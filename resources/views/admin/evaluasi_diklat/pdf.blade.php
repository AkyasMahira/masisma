<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan IKM Diklat</title>
    <style>
        * { font-family: DejaVu Sans, sans-serif; }
        body { color:#222; font-size:11px; }
        .head { text-align:center; border-bottom:3px solid #7c1316; padding-bottom:10px; margin-bottom:16px; }
        .head h1 { color:#7c1316; font-size:16px; margin:0 0 4px; }
        .head p { margin:0; font-size:10px; color:#555; }
        .summary { width:100%; border-collapse:collapse; margin-bottom:18px; }
        .summary td { padding:0 6px; vertical-align:top; }
        .box { border:1px solid #e2e2e2; border-radius:6px; padding:12px; text-align:center; }
        .box .lbl { font-size:9px; color:#777; text-transform:uppercase; }
        .box .big { font-size:22px; font-weight:bold; color:#7c1316; }
        .grade { display:inline-block; width:40px; height:40px; line-height:40px; border-radius:50%; background:#7c1316; color:#fff; font-size:20px; font-weight:bold; }
        table.data { width:100%; border-collapse:collapse; margin-top:6px; }
        table.data th, table.data td { border:1px solid #ccc; padding:6px 8px; }
        table.data th { background:#fcf0f1; color:#7c1316; font-size:10px; text-align:left; }
        table.data td.c { text-align:center; }
        .foot { margin-top:24px; font-size:9px; color:#888; text-align:right; }
        h3 { color:#7c1316; font-size:12px; border-left:4px solid #7c1316; padding-left:8px; margin:18px 0 6px; }
    </style>
</head>
<body>
    <div class="head">
        <h1>LAPORAN INDEKS KEPUASAN MASYARAKAT (IKM)</h1>
        <p>Pelayanan Diklat &mdash; RSUD Simpang Lima Gumul Kabupaten Kediri</p>
        <p>Dicetak: {{ now()->format('d F Y H:i') }}</p>
    </div>

    <table class="summary">
        <tr>
            <td style="width:34%;">
                <div class="box">
                    <div class="lbl">Nilai IKM</div>
                    <div class="big">{{ number_format($ikm['nilai'],2) }}</div>
                    <div style="font-size:10px;color:#555;">{{ $ikm['kategori'] }}</div>
                </div>
            </td>
            <td style="width:33%;">
                <div class="box">
                    <div class="lbl">Mutu Pelayanan</div>
                    <div style="margin:6px 0;"><span class="grade">{{ $ikm['mutu'] }}</span></div>
                </div>
            </td>
            <td style="width:33%;">
                <div class="box">
                    <div class="lbl">Total Responden</div>
                    <div class="big">{{ $ikm['total_responden'] }}</div>
                    <div style="font-size:10px;color:#555;">{{ $ikm['jumlah_unsur'] }} unsur dinilai</div>
                </div>
            </td>
        </tr>
    </table>

    <h3>Nilai Rata-Rata (NRR) per Unsur Pelayanan</h3>
    <table class="data">
        <thead>
            <tr>
                <th style="width:8%;">Kode</th>
                <th>Unsur Pelayanan</th>
                <th style="width:12%;">NRR</th>
                <th style="width:14%;">Nilai (0-100)</th>
                <th style="width:8%;">Mutu</th>
                <th style="width:12%;">Responden</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ikm['per_unsur'] as $u)
            <tr>
                <td class="c">{{ $u['kode'] }}</td>
                <td>{{ $u['pertanyaan'] }}</td>
                <td class="c">{{ number_format($u['nrr'],3) }}</td>
                <td class="c">{{ number_format($u['nilai'],2) }}</td>
                <td class="c">{{ $u['mutu'] }}</td>
                <td class="c">{{ $u['responden'] }}</td>
            </tr>
            @endforeach
            @if(count($ikm['per_unsur']) === 0)
            <tr><td colspan="6" class="c">Belum ada data penilaian.</td></tr>
            @endif
        </tbody>
    </table>

    <h3>Keterangan Mutu</h3>
    <table class="data">
        <thead><tr><th>Nilai IKM</th><th>Mutu</th><th>Kinerja Unit Pelayanan</th></tr></thead>
        <tbody>
            <tr><td class="c">88,31 - 100,00</td><td class="c">A</td><td>Sangat Baik</td></tr>
            <tr><td class="c">76,61 - 88,30</td><td class="c">B</td><td>Baik</td></tr>
            <tr><td class="c">65,00 - 76,60</td><td class="c">C</td><td>Kurang Baik</td></tr>
            <tr><td class="c">25,00 - 64,99</td><td class="c">D</td><td>Tidak Baik</td></tr>
        </tbody>
    </table>

    <div class="foot">Sumber: Sistem Informasi Diklat (Sindikat) RSUD SLG &mdash; berdasarkan Permenpan RB No. 14 Tahun 2017</div>
</body>
</html>
