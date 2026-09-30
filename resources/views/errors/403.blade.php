<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 — Menunggu Persetujuan</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --maroon: #7c1316;
            --maroon-dark: #5f0f11;
            --white: #ffffff;
            --text-white-50: rgba(255, 255, 255, 0.7);
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--maroon);
            background-image: radial-gradient(circle at top right, #9d2a2e 0%, var(--maroon) 40%);
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            text-align: center;
            padding: 20px;
            color: var(--white);
            overflow-x: hidden; /* Mencegah scroll horizontal */
        }

        .container {
            width: 100%;
            max-width: 500px;
            position: relative;
            z-index: 2;
            animation: fadeInUp 0.8s cubic-bezier(0.2, 0.8, 0.2, 1);
        }

        /* Ilustrasi / Logo */
        .illustration-box {
            width: 100px;
            height: 100px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .illustration-box img {
            width: 60px;
            height: auto;
            filter: drop-shadow(0 4px 6px rgba(0,0,0,0.2));
        }

        h1 {
            font-size: 1.8rem; /* Menggunakan rem agar lebih fleksibel */
            font-weight: 700;
            margin-bottom: 10px;
            line-height: 1.2;
        }

        .status-badge {
            background: rgba(255, 255, 255, 0.15);
            color: #ffdfba;
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        p {
            font-size: 0.95rem;
            margin-bottom: 30px;
            color: var(--text-white-50);
            font-weight: 300;
            line-height: 1.6;
            padding: 0 10px;
        }

        /* Buttons */
        .btn-group {
            display: flex;
            flex-direction: column; /* Default stack untuk mobile */
            gap: 12px;
            width: 100%;
        }

        .btn {
            padding: 14px 24px;
            border-radius: 12px; /* Lebih modern sedikit kotak di mobile */
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-home {
            background: var(--white);
            color: var(--maroon);
            border: 2px solid var(--white);
        }

        .btn-contact {
            background: transparent;
            color: var(--white);
            border: 2px solid rgba(255,255,255,0.3);
        }

        /* Footer */
        footer {
            margin-top: 40px;
            font-size: 12px;
            color: var(--text-white-50);
        }

        /* Background Elements - Ukuran adaptif */
        .bg-circle {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.03);
            z-index: 1;
            pointer-events: none;
        }
        .c1 { width: 20vw; height: 20vw; min-width: 150px; min-height: 150px; top: -5%; left: -5%; }
        .c2 { width: 15vw; height: 15vw; min-width: 100px; min-height: 100px; bottom: 5%; right: -5%; }

        /* Media Query untuk Tablet ke Atas */
        @media (min-width: 576px) {
            h1 { font-size: 2.2rem; }
            .btn-group { flex-direction: row; justify-content: center; }
            .btn { width: auto; border-radius: 50px; }
            .illustration-box { width: 120px; height: 120px; }
            .illustration-box img { width: 70px; }
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>

<body>
    <div class="bg-circle c1"></div>
    <div class="bg-circle c2"></div>

    <div class="container">
        <div class="illustration-box">
            <img src="{{ asset('icon.png') }}" alt="Logo">
        </div>

        <div class="status-badge">
            <i class="bi bi-hourglass-split"></i> Akun Pending
        </div>

        <h1>Mohon Bersabar...</h1>
        
        <p>
            Akun Anda berhasil dibuat dan sedang dalam <strong>antrean persetujuan Admin</strong>. 
            Silakan hubungi kami jika proses memakan waktu lama.
        </p>

        <div class="btn-group">
            <a href="{{ route('login') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="btn btn-home">
                <i class="bi bi-arrow-left"></i> Kembali ke Login
            </a>
            
            <a href="https://wa.me/6281234567890" target="_blank" class="btn btn-contact">
                <i class="bi bi-whatsapp"></i> Hubungi Admin
            </a>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>

        <footer>
            &copy; {{ date('Y') }} Sindikat App. All rights reserved.
        </footer>
    </div>
</body>
</html>