<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekapitulasi Data SINDIKAT</title>
    <style>
        /* CSS internal wajib untuk DomPDF */
        body { font-family: 'Helvetica', sans-serif; font-size: 10px; color: #333; margin: 0; padding: 0; }
        .header { text-align: center; border-bottom: 2px solid #7c1316; padding-bottom: 10px; margin-bottom: 20px; position: relative; }
        .logo { width: 60px; position: absolute; left: 0; top: 0; }
        .instansi { font-size: 16px; font-weight: bold; color: #7c1316; text-transform: uppercase; margin-bottom: 5px; }
        .judul-form { font-size: 12px; font-weight: bold; color: #333; margin-bottom: 5px; }
        .meta-data { font-size: 9px; color: #666; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; table-layout: fixed; }
        th { background-color: #7c1316; color: white; padding: 10px 5px; text-align: left; border: 1px solid #7c1316; text-transform: uppercase; font-size: 9px; }
        td { padding: 8px 5px; border: 1px solid #eee; word-wrap: break-word; vertical-align: top; }
        tr:nth-child(even) { background-color: #fcfcfc; }
        
        .footer { position: fixed; bottom: -30px; left: 0; right: 0; height: 30px; text-align: center; font-size: 8px; color: #999; border-top: 1px solid #eee; padding-top: 10px; }
        .page-number:before { content: "Halaman " counter(page); }
    </style>
</head>
<body>
    <div class="header">
        @if(!empty($logoBase64))
            <img src="{{ $logoBase64 }}" class="logo">
        @else
            <img src="{{ public_path('icon.png') }}" class="logo">
        @endif
        <div class="instansi">RSUD SIMPANG LIMA GUMUL KEDIRI</div>
        <div class="judul-form">LAPORAN REKAPITULASI: {{ strtoupper($form->title) }}</div>
        <div class="meta-data">
            Dicetak melalui Sistem SINDIKAT pada: {{ date('d F Y, H:i') }} WIB | Total Respons: {{ $form->responses->count() }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="30px" style="text-align: center;">No</th>
                @foreach($form->fields as $field)
                    <th>{{ $field['label'] }}</th>
                @endforeach
                <th width="80px">Waktu Daftar</th>
            </tr>
        </thead>
        <tbody>
            @forelse($form->responses as $idx => $res)
            <tr>
                <td style="text-align: center;">{{ $idx + 1 }}</td>
                @foreach($form->fields as $i => $f)
                    <td>
                        @php $val = $res->answers['ans_'.$i] ?? '-'; @endphp
                        {{ is_array($val) ? implode(', ', $val) : $val }}
                    </td>
                @endforeach
                <td style="text-align: center;">{{ $res->created_at->format('d/m/y H:i') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="{{ count($form->fields) + 2 }}" style="text-align: center; padding: 20px;">Belum ada data pendaftar.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <span class="page-number"></span> | SINDIKAT (Sistem Informasi Pendidikan, Penelitian dan Pelatihan Diklat) - RSUD Simpang Lima Gumul Kediri
    </div>
</body>
</html>