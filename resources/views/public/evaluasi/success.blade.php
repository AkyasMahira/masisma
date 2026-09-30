<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terima Kasih - Evaluasi Diklat</title>
    <link rel="icon" type="image/png" href="{{ asset('icon.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <style>
        :root { --primary-color: #7c1316; --primary-hover: #5e0e10; --bg-color: #f0f2f5; --text-dark: #2c3e50; }
        body { background-color: var(--bg-color); font-family: 'Poppins', sans-serif; color: var(--text-dark); height: 100vh; display: flex; align-items: center; justify-content: center; margin: 0; }
        .success-card { background: white; border-radius: 20px; padding: 50px 40px; max-width: 550px; width: 90%; text-align: center; box-shadow: 0 10px 40px rgba(0,0,0,0.08); border-top: 6px solid var(--primary-color); }
        .ic { width: 90px; height: 90px; border-radius: 50%; background: linear-gradient(135deg, #7c1316 0%, #a31d21 100%); color:#fff; display:flex; align-items:center; justify-content:center; font-size: 2.6rem; margin: 0 auto 24px; box-shadow: 0 8px 20px rgba(124,19,22,.3); }
        h1 { font-weight: 700; font-size: 24px; color: var(--text-dark); }
        p { color: #64748b; font-size: 15px; }
        .btn-home { background: linear-gradient(135deg, #7c1316 0%, #a31d21 100%); color:#fff; border:none; border-radius: 12px; padding: 13px 26px; font-weight: 600; text-decoration: none; display:inline-block; margin-top: 12px; box-shadow: 0 4px 15px rgba(124,19,22,.3); transition:.3s; }
        .btn-home:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(124,19,22,.4); color:#fff; }
    </style>
</head>
<body>
    <div class="success-card animate__animated animate__fadeInUp">
        <div class="ic"><i class="fas fa-check"></i></div>
        <h1>Terima Kasih!</h1>
        <p>Evaluasi Anda telah kami terima. Masukan Anda sangat berarti untuk meningkatkan kualitas layanan diklat RSUD Simpang Lima Gumul.</p>
        <a href="{{ route('landing') }}" class="btn-home"><i class="fas fa-home mr-2"></i> Kembali ke Beranda</a>
    </div>
</body>
</html>
