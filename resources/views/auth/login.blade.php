<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masisma - Login & Register</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />

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
        .image-sidebar { width: 77%; height: 157px; object-fit: cover; border-radius: 8px; }
        .login-header h2 { font-weight: 700; font-size: 2.3rem; }

        /* --- Styling Input Group (Container Utama) --- */
        .input-group {
            border-radius: 50rem;
            background-color: var(--warna-white);
            border: 1px solid var(--warna-white);
            padding: 0.25rem 0.5rem;
            transition: all 0.3s ease;
            margin-bottom: 1rem;
            display: flex;
            align-items: center; /* Center vertical */
            position: relative; /* Penting untuk z-index dropdown */
        }

        /* Efek Focus (Berlaku juga saat Choices.js aktif) */
        .input-group:focus-within {
            border-color: var(--warna-sekunder);
            box-shadow: 0 0 0 0.25rem rgba(248, 234, 234, 0.5);
            transform: scale(1.02);
            z-index: 50; /* Angkat z-index saat fokus agar dropdown di atas elemen lain */
        }

        /* Icon Styling */
        .input-group .input-group-text {
            border: none;
            background-color: transparent;
            color: var(--warna-sekunder);
            padding-right: 0.5rem;
            z-index: 2;
        }

        /* Input Biasa */
        .input-group .form-control {
            border: none;
            background-color: transparent;
            color: var(--warna-text-dark);
            box-shadow: none;
            padding-left: 0.5rem;
        }

        /* --- 2. CUSTOM CSS UNTUK CHOICES.JS --- */
        /* Reset wrapper choices agar fit di dalam input-group */
        .choices {
            flex-grow: 1;
            margin-bottom: 0;
            overflow: visible; /* Biarkan dropdown keluar */
        }

        /* Styling kotak dalam Choices agar transparan & borderless */
        .choices__inner {
            border: none !important;
            background-color: transparent !important;
            min-height: auto !important;
            padding: 0.375rem 0.75rem 0.375rem 0 !important; /* Hapus padding kiri */
            color: var(--warna-text-dark);
            font-size: 1rem;
        }

        /* Input search dalam choices */
        .choices__input {
            background-color: transparent !important;
            margin-bottom: 0 !important;
        }

        /* Dropdown List Styling */
        .choices__list--dropdown {
            border: 1px solid #eee;
            border-radius: 12px; /* Rounded dropdown */
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-top: 10px; /* Jarak dari input */
            z-index: 100;
            color: #333; /* Pastikan teks hitam */
            background: #fff;
        }

        /* Item dalam dropdown */
        .choices__list--dropdown .choices__item--selectable {
            padding: 10px 15px;
            font-size: 0.9rem;
        }

        /* Highlight saat hover item */
        .choices__list--dropdown .choices__item--selectable.is-highlighted {
            background-color: var(--warna-tertiary);
            color: var(--warna-primer);
        }

        /* Placeholder color fix */
        .choices__placeholder {
            color: #6c757d;
            opacity: 1;
        }

        /* Hapus focus outline bawaan choices (kita pakai focus-within parent) */
        .is-focused .choices__inner,
        .is-open .choices__inner {
            border: none;
            box-shadow: none;
        }
        /* --- END CUSTOM CSS CHOICES.JS --- */

        .btn-login {
            background-color: var(--warna-white);
            color: var(--warna-primer);
            border: none;
            padding: 0.85rem;
            font-weight: 700;
            border-radius: 50rem;
            transition: all 0.2s ease;
            margin-top: 1rem;
        }

        .btn-login:hover {
            background-color: var(--warna-tertiary);
            color: var(--warna-primer);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .toggle-link {
            text-align: center;
            margin-top: 1rem;
            font-size: 0.9rem;
        }

        .toggle-link a {
            color: var(--warna-white);
            text-decoration: none;
            opacity: 0.85;
        }

        .toggle-link a:hover { opacity: 1; text-decoration: underline; }

        .wave-container {
            position: absolute; bottom: 0; left: 0; width: 100%; line-height: 0; z-index: 1;
        }
        .wave { position: absolute; bottom: 0; left: 0; width: 150%; }
        .wave-1 { z-index: 2; animation: waveMove 15s linear infinite; }
        .wave-2 { z-index: 3; opacity: 0.8; animation: waveMove 12s linear infinite; }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes waveMove { 0% { transform: translateX(0); } 50% { transform: translateX(-5%); } 100% { transform: translateX(0); } }
        @media (max-width: 576px) { .login-header h2 { font-size: 2rem; } }
    </style>
</head>

<body>
    <div class="login-wrapper">

        {{-- Login Form --}}
        <div id="loginForm">
            <div class="login-header text-center">
                <img class="image-sidebar" src="{{ asset('icon.png') }}" alt="Logo">
            </div>

            @if ($errors->any())
                <div class="alert alert-danger py-2 mb-3" style="border-radius: 1rem;">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST">
                @csrf
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                    <input type="email" name="email" class="form-control" placeholder="Email" required>
                </div>

                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                </div>

                <button type="submit" class="btn btn-login w-100">LOGIN</button>
            </form>

            <div class="toggle-link">
                <p>Belum punya akun? <a href="#" id="showRegister">Register</a></p>
            </div>
        </div>

       {{-- Register Form --}}
        <div id="registerForm" style="display: none;">
            <div class="login-header text-center mb-4">
                <h2 class="fw-bold">REGISTER</h2>
                <p class="mb-0">CREATE YOUR ACCOUNT</p>
            </div>

            <form action="{{ route('register.post') }}" method="POST">
                @csrf
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                    <input type="text" name="name" class="form-control" placeholder="Nama Lengkap" required>
                </div>

                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                    <input type="email" name="email" class="form-control" placeholder="Email" required>
                </div>

                {{-- Input Universitas --}}
                <div class="input-group" style="overflow: visible;">
                    <span class="input-group-text"><i class="bi bi-building-fill"></i></span>
                    <select name="mou_id" class="form-select" id="universitas-select" required>
                        <option value="" placeholder>Pilih Instansi</option>
                        @foreach ($universitas as $u)
                            <option value="{{ $u->id }}">{{ $u->nama_instansi ?? $u->nama_universitas }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- BARU: Input Program Studi --}}
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-mortarboard-fill"></i></span>
                    <input type="text" name="program_studi" class="form-control" placeholder="Program Studi" required>
                </div>
                {{-- -------------------------- --}}

                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                </div>

                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" name="password_confirmation" class="form-control" placeholder="Konfirmasi Password" required>
                </div>

                <button type="submit" class="btn btn-login w-100">REGISTER</button>
            </form>

            <div class="toggle-link">
                <p>Sudah punya akun? <a href="#" id="showLogin">Login</a></p>
            </div>
        </div>
    </div>

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

    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

    <script>
        // Inisialisasi Choices.js
        document.addEventListener('DOMContentLoaded', function() {
            const element = document.getElementById('universitas-select');
                    const choices = new Choices(element, {
                searchEnabled: true,
                itemSelectText: '', // Menghapus teks 'Press to select'
                placeholder: true,
                        placeholderValue: 'Pilih Instansi',
                        searchPlaceholderValue: 'Cari instansi...',
                noResultsText: 'Tidak ditemukan',
                shouldSort: false,
            });
        });

        // Script Toggle Login/Register
        document.getElementById('showRegister').addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('loginForm').style.display = 'none';
            document.getElementById('registerForm').style.display = 'block';
        });

        document.getElementById('showLogin').addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('registerForm').style.display = 'none';
            document.getElementById('loginForm').style.display = 'block';
        });
    </script>
</body>

</html>
