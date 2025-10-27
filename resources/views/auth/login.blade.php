<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Masisma</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --warna-primer: #7c1316;
            /* Merah Gelap (BG Utama) */
            --warna-sekunder: #a83236;
            /* Merah Medium (Gelombang 1) */
            --warna-tertiary: #f8eaea;
            /* Pink Sangat Muda (Gelombang 2) */
            --warna-white: #ffffff;
            --warna-text-dark: #333;
        }

        html,
        body {
            height: 100%;
        }

        body {
            background-color: var(--warna-primer);
            color: var(--warna-white);
            display: flex;
            align-items: center;
            /* Vertikal center */
            justify-content: center;
            /* Horizontal center */
            position: relative;
            overflow: hidden;
            /* Mencegah scroll aneh krn gelombang */
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }

        /* Wrapper untuk konten login */
        .login-wrapper {
            width: 100%;
            max-width: 400px;
            /* Lebar maksimum di desktop */
            padding: 1.5rem;
            position: relative;
            z-index: 10;
            /* Di atas gelombang */
            animation: fadeIn 0.8s ease-out;
            /* Animasi masuk */
        }

        .login-header {
            margin-bottom: 2.5rem;
        }

        .login-header h2 {
            font-weight: 700;
            font-size: 2.5rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            /* Bayangan teks untuk kontras */
        }

        .login-header p {
            font-size: 1.1rem;
            opacity: 0.9;
            letter-spacing: 1px;
            /* Spasi huruf sedikit lebih lebar */
        }

        /* Styling Input Group agar pill-shaped dan putih */
        .input-group {
            border-radius: 50rem;
            background-color: var(--warna-white);
            border: 1px solid var(--warna-white);
            padding: 0.25rem 0.5rem;
            /* Padding dalam agar ikon tidak mepet */
            transition: all 0.3s ease;
            /* Transisi halus untuk semua perubahan */
        }

        .input-group:focus-within {
            border-color: var(--warna-sekunder);
            box-shadow: 0 0 0 0.25rem rgba(248, 234, 234, 0.5);
            /* Shadow pakai warna tertiary */
            transform: scale(1.02);
            /* Sedikit membesar saat fokus */
        }

        .input-group .form-control {
            border: none;
            background-color: transparent;
            box-shadow: none;
            /* Menghilangkan shadow aneh dari bootstrap */
            padding-left: 0.5rem;
            color: var(--warna-text-dark);
            /* Teks di dalam input berwarna gelap */
            transition: all 0.3s ease;
            /* Transisi halus untuk input */
        }

        .input-group .form-control::placeholder {
            color: #999;
            transition: color 0.3s ease;
            /* Transisi untuk placeholder */
        }

        .input-group .form-control:focus::placeholder {
            color: #ccc;
            /* Placeholder lebih transparan saat fokus */
        }

        .input-group .input-group-text {
            border: none;
            background-color: transparent;
            color: var(--warna-sekunder);
            /* Ikon pakai warna sekunder */
            font-size: 1.1rem;
            transition: all 0.3s ease;
            /* Transisi untuk ikon */
        }

        .input-group:focus-within .input-group-text {
            color: var(--warna-primer);
            /* Ikon berubah warna saat fokus */
        }

        /* Tombol Login */
        .btn-login {
            background-color: var(--warna-white);
            color: var(--warna-primer);
            /* Teks tombol pakai warna primer */
            border: none;
            padding: 0.85rem;
            font-weight: 700;
            /* Dibuat tebal seperti di gambar */
            font-size: 1rem;
            border-radius: 50rem;
            /* Pill-shaped */
            transition: all 0.2s ease;
            letter-spacing: 0.5px;
            position: relative;
            overflow: hidden;
        }

        .btn-login:hover {
            background-color: var(--warna-tertiary);
            /* Saat di-hover, ganti ke warna pink muda */
            color: var(--warna-primer);
            transform: translateY(-2px);
            /* Efek sedikit terangkat */
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .btn-login:active {
            transform: translateY(0);
            /* Kembali ke posisi saat diklik */
        }

        .btn-login::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 5px;
            height: 5px;
            background: rgba(255, 255, 255, 0.5);
            opacity: 0;
            border-radius: 100%;
            transform: scale(1, 1) translate(-50%);
            transform-origin: 50% 50%;
        }

        .btn-login:focus:not(:active)::after {
            animation: ripple 1s ease-out;
            /* Efek ripple saat fokus */
        }

        /* Kontainer untuk gelombang SVG */
        .wave-container {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            line-height: 0;
            /* Menghilangkan spasi ekstra */
            z-index: 1;
        }

        .wave {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 150%;
            line-height: 0;
        }

        /* Gelombang Belakang (Merah Medium) */
        .wave-1 {
            z-index: 2;
            animation: waveMove 15s linear infinite;
            /* Animasi gelombang */
        }

        /* Gelombang Depan (Pink Muda) */
        .wave-2 {
            z-index: 3;
            opacity: 0.8;
            /* Sedikit transparan */
            animation: waveMove 12s linear infinite;
            /* Animasi gelombang lebih cepat */
        }

        /* Animasi untuk efek ripple pada tombol */
        @keyframes ripple {
            0% {
                transform: scale(0, 0);
                opacity: 0.5;
            }

            100% {
                transform: scale(50, 50);
                opacity: 0;
            }
        }

        /* Animasi untuk gelombang bergerak */
        @keyframes waveMove {
            0% {
                transform: translateX(0);
            }

            50% {
                transform: translateX(-5%);
            }

            100% {
                transform: translateX(0);
            }
        }

        /* Animasi untuk konten masuk */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Link lupa password */
        .forgot-password {
            text-align: center;
            margin-top: 1rem;
            font-size: 0.9rem;
        }

        .forgot-password a {
            color: var(--warna-white);
            text-decoration: none;
            opacity: 0.8;
            transition: opacity 0.3s ease;
        }

        .forgot-password a:hover {
            opacity: 1;
            text-decoration: underline;
        }

        /* Responsif untuk layar kecil */
        @media (max-width: 576px) {
            .login-wrapper {
                padding: 1rem;
            }

            .login-header h2 {
                font-size: 2rem;
            }
        }
    </style>
</head>

<body class="login-body">

    <div class="login-wrapper">
        <div class="login-header text-center">
            <h2 class="fw-bold">LOGIN</h2>
            <p>TO CONTINUE</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger py-2" style="border-radius: 1rem; z-index: 20;">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST">
            @csrf
            <div class="mb-3">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                    <input type="email" name="email" value="{{ old('email') }}" class="form-control"
                        placeholder="someone@gmail.com" required>
                </div>
            </div>
            <div class="mb-4">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                </div>
            </div>
            <button type="submit" class="btn btn-login w-100 mt-3">LOGIN</button>
        </form>

        <!-- Tambahan: Link lupa password -->
        <div class="forgot-password">
            <a href="#">Forgot Password?</a>
        </div>
    </div>


    <div class="wave-container">
        <div class="wave wave-1">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
                <path fill="#a83236" fill-opacity="1"
                    d="M0,192L48,181.3C96,171,192,149,288,160C384,171,480,213,576,208C672,203,768,149,864,138.7C960,128,1056,160,1152,176C1248,192,1344,192,1392,192L1440,192L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z">
                </path>
            </svg>
        </div>
        <div class="wave wave-2">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
                <path fill="#f8eaea" fill-opacity="1"
                    d="M0,256L48,245.3C96,235,192,213,288,208C384,203,480,213,576,229.3C672,245,768,267,864,256C960,245,1056,203,1152,192C1248,181,1344,203,1392,213.3L1440,224L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z">
                </path>
            </svg>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
