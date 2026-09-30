<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Bukti Pendaftaran</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #333; line-height: 1.5; }
        .header { border-bottom: 2px solid #7c1316; padding-bottom: 15px; margin-bottom: 20px; }
        .logo { width: 60px; float: left; margin-right: 20px; }
        .header-text h2 { margin: 0; color: #7c1316; font-size: 20px; }
        .header-text p { margin: 0; font-size: 12px; color: #666; }
        .content { clear: both; padding-top: 10px; }
        .title { text-align: center; text-transform: uppercase; font-size: 16px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table th { text-align: left; padding: 10px; border-bottom: 1px solid #eee; background: #fcfcfc; color: #7c1316; font-size: 11px; width: 35%; }
        table td { padding: 10px; border-bottom: 1px solid #eee; font-size: 13px; }
        .footer { margin-top: 40px; text-align: center; font-size: 10px; color: #999; border-top: 1px solid #eee; padding-top: 15px; }
    </style>
</head>
<body>
    <div class="header">
        <img src="icon.png" class="logo">
        <div class="header-text">
            <h2>RSUD SIMPANG LIMA GUMUL</h2>
            <p>Sistem Informasi Pendidikan dan Pelatihan Diklat (SINDIKAT)</p>
        </div>
    </div>

    <div class="content">
        <div class="title">BUKTI PENDAFTARAN: {{ $form->title }}</div>
        <p style="text-align: center; font-size: 11px;">No. Registrasi: #SDK-{{ str_pad($response->id, 5, '0', STR_PAD_LEFT) }}</p>

        <table>
            @foreach($form->fields as $i => $field)
            <tr>
                <th>{{ $field['label'] }}</th>
                <td>
                    @php 
                        $val = $response->answers['ans_'.$i] ?? '-'; 
                    @endphp
                    {{ is_array($val) ? implode(', ', $val) : $val }}
                </td>
            </tr>
            @endforeach
            <tr>
                <th>Waktu Daftar</th>
                <td>{{ $response->created_at->format('d F Y, H:i') }} WIB</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Dokumen ini diterbitkan secara resmi oleh sistem SINDIKAT RSUD SLG Kediri.<br>
        Dicetak pada: {{ date('d/m/Y H:i') }}
    </div>
</body>
</html>