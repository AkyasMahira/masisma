<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terima Kasih - Evaluasi Diklat</title>
    <link rel="icon" type="image/png" href="{{ asset('icon.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        :root { --primary: #7c1316; --primary-dark: #5f0f12; }
        * { font-family: 'Plus Jakarta Sans', sans-serif; }
        body { background: #f0f4f8; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; }
        .box { background: #fff; max-width: 480px; width: 92%; border-radius: 20px; padding: 40px 32px; text-align: center; box-shadow: 0 14px 40px rgba(0,0,0,.08); }
        .ic { width: 84px; height: 84px; border-radius: 50%; background: linear-gradient(135deg, var(--primary), var(--primary-dark)); color:#fff; display:flex; align-items:center; justify-content:center; font-size: 2.4rem; margin: 0 auto 20px; }
        h1 { font-weight: 800; font-size: 1.4rem; color: #1a202c; }
        p { color: #64748b; }
        .btn-primary-soft { background: var(--primary); color:#fff; border:none; border-radius: 12px; padding: 11px 22px; font-weight: 700; text-decoration: none; display:inline-block; margin-top: 10px; }
        .btn-primary-soft:hover { background: var(--primary-dark); color:#fff; }
    </style>
</head>
<body>
    <div class="box">
        <div class="ic"><i class="bi bi-check-lg"></i></div>
        <h1>Terima Kasih!</h1>
        <p>Evaluasi Anda telah kami terima. Masukan Anda sangat berarti untuk meningkatkan kualitas layanan diklat RSUD Simpang Lima Gumul.</p>
        <a href="{{ route('landing') }}" class="btn-primary-soft"><i class="bi bi-house me-1"></i> Kembali ke Beranda</a>
    </div>
</body>
</html>
