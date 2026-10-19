<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Evaluasi Narasumber</title>
    <style>
        * { font-family: DejaVu Sans, sans-serif; }
        body { color:#222; font-size:9.5px; }
        .head { text-align:center; border-bottom:3px solid #7c1316; padding-bottom:8px; margin-bottom:12px; }
        .head h1 { color:#7c1316; font-size:15px; margin:0 0 3px; }
        .head p { margin:0; font-size:9.5px; color:#555; }
        table.data { width:100%; border-collapse:collapse; margin-top:6px; }
        table.data th, table.data td { border:1px solid #ccc; padding:4px 5px; }
        table.data th { background:#fcf0f1; color:#7c1316; font-size:8.5px; }
        td.c { text-align:center; }
        h3 { color:#7c1316; font-size:11px; border-left:4px solid #7c1316; padding-left:6px; margin:14px 0 4px; }
        .foot { margin-top:16px; font-size:8px; color:#888; text-align:right; }
        .k-SangatBaik { color:#15803d; font-weight:bold; } .k-Baik { color:#1d4ed8; font-weight:bold; }
        .k-Sedang { color:#b45309; font-weight:bold; } .k-Kurang { color:#b91c1c; font-weight:bold; }
    </style>
</head>
<body>
    <div class="head">
        <h1>LAPORAN EVALUASI NARASUMBER</h1>
        <p><b>{{ $kegiatan->nama_kegiatan }}</b></p>
        <p>{{ $kegiatan->teksTanggalPelaksanaan() }} &mdash; Responden: {{ $total }} &mdash; Dicetak: {{ now()->isoFormat('D MMMM YYYY HH:mm') }}</p>
    </div>

    <h3>Nilai per Narasumber</h3>
    <table class="data">
        <thead>
            <tr>
                <th style="width:3%;">No</th><th>Narasumber</th>
                @foreach($aspekList as $i => $asp)<th title="{{ $asp }}">A{{ $i + 1 }}</th>@endforeach
                <th>Nilai Akhir</th><th>Kategori</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 0; @endphp
            @forelse($narasumber as $n)
                <tr>
                    <td class="c">{{ ++$no }}</td>
                    <td>{{ $n['nama'] }}</td>
                    @foreach($aspekList as $asp)<td class="c">{{ isset($n['aspek'][$asp]['skor']) ? number_format($n['aspek'][$asp]['skor'], 1) : '-' }}</td>@endforeach
                    <td class="c"><b>{{ $n['skor'] !== null ? number_format($n['skor'], 2) : '-' }}</b></td>
                    <td class="c k-{{ str_replace(' ', '', $n['kategori']) }}">{{ $n['kategori'] }}</td>
                </tr>
            @empty
                <tr><td colspan="{{ count($aspekList) + 4 }}" class="c">Belum ada data.</td></tr>
            @endforelse
            @if(count($narasumber))
            <tr>
                <td></td><td><b>Rata-rata aspek</b></td>
                @foreach($aspekList as $asp)<td class="c"><b>{{ $perAspek[$asp] !== null ? number_format($perAspek[$asp], 1) : '-' }}</b></td>@endforeach
                <td colspan="2"></td>
            </tr>
            @endif
        </tbody>
    </table>

    <h3>Keterangan Aspek</h3>
    <table class="data">
        <tbody>
            @foreach(array_chunk($aspekList, 2) as $pair)
                <tr>
                    @foreach($pair as $asp)
                        @php $idx = array_search($asp, $aspekList); @endphp
                        <td style="width:6%;" class="c"><b>A{{ $idx + 1 }}</b></td><td style="width:44%;">{{ $asp }}</td>
                    @endforeach
                    @if(count($pair) < 2)<td></td><td></td>@endif
                </tr>
            @endforeach
        </tbody>
    </table>

    <h3>Skala Penilaian</h3>
    <table class="data" style="width:60%;">
        <thead><tr><th>Kategori</th><th>Rentang</th><th>Nilai dipakai</th></tr></thead>
        <tbody>
            @foreach(\App\Models\EvaluasiNarasumber::SKALA as $s)
                <tr><td>{{ $s[0] }}</td><td class="c">{{ $s[1] }}</td><td class="c">{{ $s[2] }}</td></tr>
            @endforeach
        </tbody>
    </table>

    @if(count($saran))
    <h3>Kritik &amp; Saran</h3>
    <table class="data">
        <tbody>
            @foreach($saran as $i => $s)<tr><td class="c" style="width:4%;">{{ $i + 1 }}</td><td>{{ $s }}</td></tr>@endforeach
        </tbody>
    </table>
    @endif

    <div class="foot">Sumber: Sistem Informasi Diklat (Sindikat) RSUD Simpang Lima Gumul</div>
</body>
</html>
