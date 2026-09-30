<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sindikat - Lupa Password</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('icon.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --warna-primer: #7c1316;
            --warna-sekunder: #a83236;
            --warna-tertiary: #f8eaea;
            --warna-white: #ffffff;
            --warna-text-dark: #333;
        }

        html, body { height: 100%; }

        body {
            background-color: var(--warna-primer);
            color: var(--warna-white);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            font-family: "Segoe UI", Roboto, Arial, sans-serif;
        }

        .login-wrapper {
            width: 100%;
            max-width: 400px;
            padding: 1.5rem;
            position: relative;
            z-index: 10;
            animation: fadeIn 0.8s ease-out;
        }

        .login-header { margin-bottom: 2rem; }
        .logo-img { width: 60%; margin-bottom: 1rem; }
        .login-header h2 { font-weight: 700; font-size: 1.8rem;  letter-spacing: 1px; }
        .login-header p { opacity: 0.8; font-size: 0.9rem; }

        /* --- Styling Input Group --- */
        .input-group {
            border-radius: 50rem;
            background-color: var(--warna-white);
            border: 1px solid var(--warna-white);
            padding: 0.25rem 0.5rem;
            transition: all 0.3s ease;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
        }

        .input-group:focus-within {
            transform: scale(1.02);
            box-shadow: 0 0 0 0.25rem rgba(248, 234, 234, 0.3);
        }

        .input-group .input-group-text {
            border: none;
            background-color: transparent;
            color: var(--warna-sekunder);
            padding-right: 0.5rem;
        }

        .input-group .form-control {
            border: none;
            background-color: transparent;
            color: var(--warna-text-dark);
            box-shadow: none;
            padding: 0.75rem 0.5rem;
        }

        /* --- BUTTONS --- */
        .btn-login {
            background-color: var(--warna-white);
            color: var(--warna-primer);
            border: none;
            padding: 0.85rem;
            font-weight: 700;
            border-radius: 50rem;
            transition: all 0.2s ease;
        }

        .btn-login:hover {
            background-color: var(--warna-tertiary);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .toggle-link {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.9rem;
        }

        .toggle-link a {
            color: var(--warna-white);
            text-decoration: none;
            opacity: 0.85;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .toggle-link a:hover { opacity: 1; text-decoration: underline; }

        /* --- Wave Animation --- */
        .wave-container { position: absolute; bottom: 0; left: 0; width: 100%; line-height: 0; z-index: 1; }
        .wave { position: absolute; bottom: 0; left: 0; width: 150%; }
        .wave-1 { z-index: 2; animation: waveMove 15s linear infinite; }
        .wave-2 { z-index: 3; opacity: 0.8; animation: waveMove 12s linear infinite; }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes waveMove { 0% { transform: translateX(0); } 50% { transform: translateX(-5%); } 100% { transform: translateX(0); } }

        .alert {
            border-radius: 1rem;
            font-size: 0.85rem;
            border: none;
        }
    </style>
</head>

<body>
    <div class="login-wrapper">
        <div class="login-header text-center">
            <img class="logo-img" src="{{ asset('icon.png') }}" alt="Logo">
            <h2>Lupa Password?</h2>
            <p>Jangan khawatir, masukkan email Anda dan kami akan mengirimkan link reset password.</p>
        </div>

        {{-- Status Berhasil Kirim Email --}}
        @if (session('status'))
            <div class="alert alert-success mb-4 shadow-sm">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('status') }}
            </div>
        @endif

        {{-- Error Validasi --}}
        @if ($errors->any())
            <div class="alert alert-danger mb-4 shadow-sm">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('password.email') }}" method="POST">
            @csrf
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                <input type="email" name="email" class="form-control" placeholder="Email Terdaftar" required autofocus>
            </div>

            <button type="submit" class="btn btn-login w-100">Kirim Link</button>
        </form>

        <div class="toggle-link">
            <a href="{{ route('login') }}">
                <i class="bi bi-arrow-left"></i> Kembali ke Halaman Masuk
            </a>
        </div>
    </div>

    {{-- Wave Background (Sama dengan Login) --}}
    <div class="wave-container">
        <div class="wave wave-1">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
                <path fill="#a83236" d="M0,192L48,181.3C96,171,192,149,288,160C384,171,480,213,576,208C672,203,768,149,864,138.7C960,128,1056,160,1152,176C1248,192,1344,192,1392,192L1440,192L1440,320L0,320Z"></path>
            </svg>
        </div>
        <div class="wave wave-2">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
                <path fill="#f8eaea" d="M0,256L48,245.3C96,235,192,213,288,208C384,203,480,213,576,229.3C672,245,768,267,864,256C960,245,1056,203,1152,192C1248,181,1344,203,1392,213.3L1440,224L1440,320L0,320Z"></path>
            </svg>
        </div>
    </div>
</body>
</html>