<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sukses Terkirim | SINDIKAT</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <style>
    /* Jangan gunakan -9999px, gunakan cara ini supaya browser tetap render datanya */
#pdf-template { 
    visibility: hidden; 
    position: absolute; 
    z-index: -100;
    top: 0;
}
.pdf-content { 
    background: white !important; 
    color: black !important; 
}
        :root { --primary: #7c1316; --bg: #f8fafc; }
        * { box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }
        body { background: var(--bg); display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; padding: 20px; }
        
        .success-card { background: white; max-width: 500px; width: 100%; border-radius: 24px; padding: 40px; text-align: center; box-shadow: 0 20px 40px rgba(0,0,0,0.05); }
        .icon-box { width: 80px; height: 80px; background: #fff5f5; color: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; font-size: 40px; }
        
        h1 { font-size: 24px; color: #1e293b; margin-bottom: 10px; }
        p { color: #64748b; font-size: 14px; margin-bottom: 30px; }

        .btn-group { display: flex; flex-direction: column; gap: 10px; }
        .btn { padding: 15px; border-radius: 12px; font-weight: 700; text-decoration: none; cursor: pointer; border: none; font-size: 14px; transition: 0.3s; }
        .btn-pdf { background: var(--primary); color: white; }
        .btn-home { background: #f1f5f9; color: #475569; }
        .btn:hover { opacity: 0.9; transform: translateY(-2px); }

        /* --- STYLING KHUSUS UNTUK PDF (HIDDEN IN BROWSER) --- */
        #pdf-template { position: absolute; left: -9999px; top: -9999px; }
        .pdf-content { width: 700px; padding: 50px; background: white; color: #333; }
        .pdf-header { border-bottom: 3px solid var(--primary); padding-bottom: 20px; margin-bottom: 30px; display: flex; align-items: center; gap: 20px; }
        .pdf-logo { width: 60px; }
        .pdf-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .pdf-table th { text-align: left; padding: 12px; border-bottom: 1px solid #eee; color: var(--primary); font-size: 12px; text-transform: uppercase; width: 35%; }
        .pdf-table td { padding: 12px; border-bottom: 1px solid #eee; font-size: 14px; font-weight: 600; }
        .pdf-footer { margin-top: 50px; font-size: 11px; color: #999; text-align: center; border-top: 1px solid #eee; padding-top: 20px; }
    </style>
</head>
<body>

<div class="success-card">
    <div class="icon-box"><i class="fas fa-check-circle"></i></div>
    <h1>Terkirim!</h1>
    <p>Data Anda telah berhasil direkam dalam sistem SINDIKAT. Silakan unduh bukti  di bawah ini.</p>

    <div class="btn-group">
        <a href="{{ route('forms.public.download_pdf', [$form->slug, $response->id]) }}" class="btn btn-pdf">
            <i class="fas fa-file-pdf"></i> UNDUH BUKTI (PDF)
        </a>
        
        <a href="https://sindikat-rsudslg.kedirikab.go.id" class="btn btn-home">
            KEMBALI KE BERANDA
        </a>
    </div>
</div>

    <div id="pdf-template">
        <div class="pdf-content">
            <div class="pdf-header">
                <img src="https://sindikat-rsudslg.kedirikab.go.id/icon.png" class="pdf-logo">
                <div>
                    <h2 style="margin:0; color: #7c1316;">RSUD SIMPANG LIMA GUMUL</h2>
                    <p style="margin:0; font-size: 12px; color: #666;">Bukti Online - SINDIKAT</p>
                </div>
            </div>

            <h3 style="text-align: center; text-transform: uppercase;">{{ $form->title }}</h3>
            <p style="text-align: center; font-size: 12px; color: #888;">Nomor Registrasi: #SDK-{{ str_pad($response->id, 5, '0', STR_PAD_LEFT) }}</p>

            <table class="pdf-table">
           @foreach($form->fields as $i => $field)
<tr>
    <th>{{ $field['label'] }}</th>
    <td>
        {{-- Guna format ans_{index} --}}
        @php $key = 'ans_' . $i; @endphp
        {{ is_array($response->answers[$key] ?? '-') ? implode(', ', $response->answers[$key]) : ($response->answers[$key] ?? '-') }}
    </td>
</tr>
@endforeach
                <tr>
                    <th>Waktu Daftar</th>
                    <td>{{ $response->created_at->format('d F Y, H:i') }} WIB</td>
                </tr>
            </table>

            <div class="pdf-footer">
                Dokumen ini dihasilkan secara otomatis oleh Sistem SINDIKAT RSUD SLG.<br>
                Harap simpan bukti ini sebagai tanda terima yang sah.
            </div>
        </div>
    </div>

    <script>
      function generatePDF() {
    const element = document.getElementById('pdf-template');
    
    // Pastikan elemen dipaparkan sekejap untuk dirakam
    element.style.visibility = 'visible';

    const options = {
        margin: 0.5,
        filename: 'Sindikat.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { 
            scale: 2, 
            useCORS: true, 
            letterRendering: true 
        },
        jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' }
    };

    html2pdf().set(options).from(element).save().then(() => {
        // Sembunyikan balik lepas siap download
        element.style.visibility = 'hidden';
    });
}
    </script>
</body>