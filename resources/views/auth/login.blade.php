<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sindikat - Login & Register</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('icon.png') }}">
    <link rel="shortcut icon" href="{{ asset('icon.png') }}">

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

        html, body { 
            min-height: 100%; 
        }

        body {
            background-color: var(--warna-primer);
            color: var(--warna-white);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            font-family: "Segoe UI", Roboto, Arial, sans-serif;
            padding: 2rem 1rem; /* Mencegah konten nempel di layar HP */
            overflow-y: auto; /* Mengizinkan scroll jika form melebihi tinggi layar */
        }

        .login-wrapper {
            width: 100%;
            max-width: 400px;
            /*padding: 1.5rem;*/
            position: relative;
            z-index: 10;
            animation: fadeIn 0.8s ease-out;
        }

        .login-header { margin-bottom: 2rem; }
        .image-sidebar { width: 100%; height: auto; max-height: 200px; object-fit: contain; border-radius: 8px; margin-top:100px; }
        .login-header h2 { font-weight: 700; font-size: 2.3rem; }

        /* --- Styling Input Group (RESPONSIF FIX) --- */
        .input-group {
            border-radius: 20px; /* Diubah dari 50rem agar tetap rapi saat teks memanjang */
            background-color: var(--warna-white);
            border: 1px solid var(--warna-white);
            padding: 0.5rem 0.75rem;
            transition: all 0.3s ease;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            position: relative;
            flex-wrap: nowrap; /* Mencegah icon terlempar ke bawah */
        }

        .input-group:focus-within {
            border-color: var(--warna-sekunder);
            box-shadow: 0 0 0 0.25rem rgba(248, 234, 234, 0.5);
            transform: scale(1.01);
            z-index: 50;
        }

        .input-group .input-group-text {
            border: none;
            background-color: transparent;
            color: var(--warna-sekunder);
            padding-right: 0.5rem;
            padding-left: 0.25rem;
            z-index: 2;
            display: flex;
            align-items: center;
        }

        .input-group .form-control, 
        .input-group .form-select {
            border: none;
            background-color: transparent;
            color: var(--warna-text-dark);
            box-shadow: none;
            padding: 0.25rem 0.5rem;
        }

        .input-group .form-control:focus, 
        .input-group .form-select:focus {
            background-color: transparent;
            box-shadow: none;
        }

        /* --- TOGGLE PASSWORD BUTTON --- */
        .password-toggle {
            cursor: pointer;
            color: var(--warna-sekunder);
            padding: 0 5px;
            background: transparent;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 3;
        }
        
        .password-toggle:hover { color: var(--warna-primer); }

        /* --- CUSTOM CSS CHOICES.JS (RESPONSIF & WRAP TEXT FIX) --- */
        .choices {
            flex-grow: 1;
            margin-bottom: 0;
            overflow: visible;
            min-width: 0; /* Penting untuk flexbox child agar bisa wrap */
        }

        .choices__inner {
            border: none !important;
            background-color: transparent !important;
            min-height: auto !important;
            padding: 0 !important;
            color: var(--warna-text-dark);
            font-size: 1rem;
            display: flex;
            align-items: center;
        }

        .choices__list--single {
            padding: 0 20px 0 4px !important; /* Ruang untuk tanda panah Choices.js */
            white-space: normal !important; /* Mengizinkan teks panjang turun ke bawah */
            word-break: break-word;
            color: var(--warna-text-dark);
        }

        .choices__input {
            background-color: transparent !important;
            margin-bottom: 0 !important;
            color: var(--warna-text-dark) !important;
        }

        /* Dropdown styling */
        .choices__list--dropdown {
            border: 1px solid #eee;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-top: 10px;
            z-index: 100;
            color: #333;
            background: #fff;
            max-height: 250px;
            overflow-y: auto;
        }

        .choices__list--dropdown .choices__item--selectable {
            padding: 10px 15px;
            font-size: 0.9rem;
        }

        .choices__list--dropdown .choices__item--selectable.is-highlighted {
            background-color: var(--warna-tertiary);
            color: var(--warna-primer);
        }

        .choices__placeholder { color: #6c757d; opacity: 1; }

        .is-focused .choices__inner,
        .is-open .choices__inner { border: none; box-shadow: none; }

        /* --- BUTTONS --- */
        .btn-login {
            background-color: var(--warna-white);
            color: var(--warna-primer);
            border: none;
            padding: 0.85rem;
            font-weight: 700;
            border-radius: 20px; /* Disamakan dengan input agar serasi */
            transition: all 0.2s ease;
            margin-top: 1rem;
        }
     .login-header h2 {margin-top: 100px;} 
        .btn-login:hover {
            background-color: var(--warna-tertiary);
            color: var(--warna-primer);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .btn-modal-confirm {
            background-color: var(--warna-primer);
            color: white;
            border-radius: 20px;
            padding: 0.5rem 1.5rem;
            font-weight: 600;
            border: none;
        }
        
        .btn-modal-confirm:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }

        .btn-modal-confirm:not(:disabled):hover {
            background-color: var(--warna-sekunder);
            color: white;
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
            font-weight: 600;
        }

        .toggle-link a:hover { opacity: 1; text-decoration: underline; }

        /* --- Wave Background Responsif --- */
        .wave-container { position: fixed; bottom: 0; left: 0; width: 100%; line-height: 0; z-index: 1; pointer-events: none; }
        .wave { position: absolute; bottom: 0; left: 0; width: 200%; } /* Diperlebar agar tidak patah di screen besar */
        .wave-1 { z-index: 2; animation: waveMove 15s linear infinite; }
        .wave-2 { z-index: 3; opacity: 0.8; animation: waveMove 12s linear infinite; }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes waveMove { 0% { transform: translateX(0); } 50% { transform: translateX(-25%); } 100% { transform: translateX(0); } }
        
        @media (max-width: 576px) { 
            .login-header h2 { font-size: 2rem; margin-top: 0px;} 
                .image-sidebar { margin-top: 0px; } 
            .login-wrapper { padding: 0.75rem; }
        }
        
        .mou-wrapper { position: relative; }

        .mou-badge {
            position: absolute;
            right: 16px;
            bottom: -12px;
            font-size: 0.7rem;
            padding: 2px 8px;
            border-radius: 999px;
            background: #eee;
            color: #555;
            opacity: 0;
            transform: translateY(-4px);
            transition: all 0.2s ease;
            pointer-events: none;
            z-index: 15;
        }

        .mou-badge.show { opacity: 1; transform: translateY(0); }
        .mou-badge.active { background: #e8f5e9; color: #2e7d32; }
        .mou-badge.expired { background: #fdecea; color: #b71c1c; }

        .form-check-input:checked {
            background-color: var(--warna-primer);
            border-color: var(--warna-primer);
        }
    </style>
</head>

<body>
    <div class="login-wrapper">

        {{-- Login Form --}}
        <div id="loginForm">
            <div class="login-header text-center">
                <a href="https://sindikat-rsudslg.kedirikab.go.id/">
                    <img class="image-sidebar" src="{{ asset('icon.png') }}" alt="Logo">
                </a>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger py-2 mb-3" style="border-radius: 1rem;">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST">
                @csrf
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                    <input type="email" name="email" class="form-control" placeholder="Email" required>
                </div>

                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                    <input type="password" name="password" class="form-control password-input" placeholder="Password" required>
                    <button type="button" class="password-toggle" onclick="togglePassword(this)">
                        <i class="bi bi-eye-slash"></i>
                    </button>
                </div>

                <div class="text-end mb-3">
                    <a href="{{ route('password.request') }}" style="color: white; font-size: 0.8rem; text-decoration: none; opacity: 0.8;">Lupa Password?</a>
                </div>
                <button type="submit" class="btn btn-login w-100">Masuk</button>
            </form>

            <div class="toggle-link">
                <p>Belum punya akun? <a href="#" id="showRegister">Daftar</a></p>
            </div>
        </div>

        {{-- Register Form --}}
        <div id="registerForm" style="display: none;">
            
            <div class="login-header text-center mb-4 mt-4">
                <h2 class="fw-bold">Daftar</h2>
                <p class="mb-0">Buat Akun Anda, Mulai Perjalanan Anda</p>
            </div>

            <form id="actualRegisterForm" action="{{ route('register.post') }}" method="POST">
                @csrf

                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                    <input type="text" name="name" class="form-control" placeholder="NAMA LENGKAP" required 
                           style="text-transform: uppercase;" 
                           oninput="this.value = this.value.toUpperCase()">
                </div>

                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                    <input type="email" name="email" class="form-control" placeholder="Email" required>
                </div>

                {{-- Input Universitas --}}
                <div class="input-group mou-wrapper" style="overflow: visible;">
                    <span class="input-group-text"><i class="bi bi-building-fill"></i></span>

                    <select name="mou_id" class="form-select" id="universitas-select" required>
                        <option value="" selected>Pilih Instansi</option>
                        @foreach ($universitas as $u)
                            <option 
                                value="{{ $u->id }}" 
                                data-tanggal-keluar="{{ $u->tanggal_keluar }}"
                            ):?>
                                {{ $u->nama_universitas }}
                            </option>
                        @endforeach
                    </select>

                    <span id="mou-badge" class="mou-badge"></span>
                </div>


                {{-- Program Studi (Dropdown Searchable) --}}
                <div class="input-group" style="overflow: visible;">
                    <span class="input-group-text"><i class="bi bi-mortarboard-fill"></i></span>
                    
                    <select name="program_studi" class="form-select" id="prodi-select" required>
                        <option value="" selected>PILIH PROGRAM STUDI</option>
                        <option value="S1 ILMU EKONOMI">S1 ILMU EKONOMI</option>
                        <option value="SMK REKAYASA PERANGKAT LUNAK">SMK REKAYASA PERANGKAT LUNAK</option>
                        <option value="SMK TEKNIK KOMPUTER JARINGAN">SMK TEKNIK KOMPUTER JARINGAN</option>
                        <option value="SMK MULTIMEDIA">SMK MULTIMEDIA</option>
                        <option value="S1 TEKNIK INFORMATIKA">S1 TEKNIK INFORMATIKA</option>
                        <option value="S1 SISTEM INFORMASI">S1 SISTEM INFORMASI</option>
                        <option value="S1 ILMU KOMPUTER">S1 ILMU KOMPUTER</option>
                        <option value="D3 TEKNIK ELEKTROMEDIK">D3 TEKNIK ELEKTROMEDIK</option>
                        <option value="D4 TEKNIK ELEKTROMEDIK">D4 TEKNIK ELEKTROMEDIK</option>
                        <option value="S1 TEKNIK ELEKTRO">S1 TEKNIK ELEKTRO</option>
                        <option value="S1 TEKNIK LINGKUNGAN">S1 TEKNIK LINGKUNGAN</option>
                        <option value="S2 KEBIDANAN">S2 KEBIDANAN</option>
                        <option value="SMK ASISTEN KEPERAWATAN">SMK ASISTEN KEPERAWATAN</option>
                        <option value="D3 KEPERAWATAN">D3 KEPERAWATAN</option>
                        <option value="D4 KEPERAWATAN">D4 KEPERAWATAN</option>
                        <option value="S1 KEPERAWATAN">S1 KEPERAWATAN</option>
                        <option value="PROFESI NERS">PROFESI NERS</option>
                        <option value="S2 KEPERAWATAN">S2 KEPERAWATAN</option>
                        <option value="S1 ADMINISTRASI KESEHATAN">S1 ADMINISTRASI KESEHATAN</option>
                        <option value="S2 MAGISTER MANAJEMEN">S2 MAGISTER MANAJEMEN</option>
                        <option value="D3 KEBIDANAN">D3 KEBIDANAN</option>
                        <option value="D4 KEBIDANAN">D4 KEBIDANAN</option>
                        <option value="S1 KEBIDANAN">S1 KEBIDANAN</option>
                        <option value="PROFESI BIDAN">PROFESI BIDAN</option>
                        <option value="D4 REKAM MEDIK">D4 REKAM MEDIK</option>
                        <option value="D3 REKAM MEDIK">D3 REKAM MEDIK</option>
                        <option value="SMK FARMASI">SMK FARMASI</option>
                                   <option value="=S1 TEKNIK BIOMEDIS">S1 TEKNIK BIOMEDIS</option>
                                     <option value="S2 MAGISTER KESEHATAN">S2 MAGISTER KESEHATAN</option>
                                          <option value="S2 MAGISTER KESEHATAN MASYARAKAT">S2 MAGISTER KESEHATAN MASYARAKAT</option>
                        <option value="D3 FARMASI">D3 FARMASI</option>
                        <option value="S1 FARMASI">S1 FARMASI</option>
                        <option value="PROFESI APOTEKER">PROFESI APOTEKER</option>
                        <option value="S1 KESELAMATAN DAN KESEHATAN KERJA (K3)">S1 KESELAMATAN DAN KESEHATAN KERJA (K3)</option>
                        <option value="D4 KESELAMATAN DAN KESEHATAN KERJA (K3)">D4 KESELAMATAN DAN KESEHATAN KERJA (K3)</option>
                        <option value="S1 KEDOKTERAN UMUM">S1 KEDOKTERAN UMUM</option>
                        <option value="PROFESI DOKTER (KOAS)">PROFESI DOKTER (KOAS)</option>
                        <option value="S1 KEDOKTERAN GIGI">S1 KEDOKTERAN GIGI</option>
                        <option value="PROFESI DOKTER GIGI">PROFESI DOKTER GIGI</option>
                        <option value="PPDS (SPESIALIS)">PPDS (SPESIALIS)</option>
                        <option value="S1 TEKNOLOGI LABORATORIUM MEDIK">S1 TEKNOLOGI LABORATORIUM MEDIK</option>
                        <option value="D4 TEKNOLOGI LABORATORIUM MEDIK">D4 TEKNOLOGI LABORATORIUM MEDIK</option>
                        <option value="D3 ANALIS KESEHATAN (TLM)">D3 ANALIS KESEHATAN (TLM)</option>
                        <option value="D4 ANALIS KESEHATAN (TLM)">D4 ANALIS KESEHATAN (TLM)</option>
                        <option value="D3 RADIOLOGI">D3 RADIOLOGI</option>
                        <option value="SMK KEPERAWATAN">SMK KEPERAWATAN</option>
                        <option value="SMK LABORATORIUM">SMK LABORATORIUM</option>
                        <option value="S1 FISIKA">S1 FISIKA</option>
                        <option value="D4 RADIOLOGI">D4 RADIOLOGI</option>
                        <option value="D3 FISIOTERAPI">D3 FISIOTERAPI</option>
                        <option value="S1 FISIOTERAPI">S1 FISIOTERAPI</option>
                        <option value="PROFESI FISIOTERAPI">PROFESI FISIOTERAPI</option>
                        <option value="D3 GIZI">D3 GIZI</option>
                        <option value="S1 GIZI">S1 GIZI</option>
                        <option value="PROFESI DIETISIEN">PROFESI DIETISIEN</option>
                        <option value="D3 REKAM MEDIS & INFORMASI KESEHATAN">D3 REKAM MEDIS & INFORMASI KESEHATAN</option>
                        <option value="D4 REKAM MEDIS & INFORMASI KESEHATAN">D4 REKAM MEDIS & INFORMASI KESEHATAN</option>
                        <option value="D3 KESEHATAN LINGKUNGAN (SANITASI)">D3 KESEHATAN LINGKUNGAN (SANITASI)</option>
                        <option value="S1 KESEHATAN MASYARAKAT">S1 KESEHATAN MASYARAKAT</option>
                        <option value="D4 PROMOSI KESEHATAN">D4 PROMOSI KESEHATAN</option>
                        <option value="SMK OTOMATISASI TATA KELOLA PERKANTORAN (OTKP)">SMK OTKP</option>
                        <option value="SMK AKUNTANSI">SMK AKUNTANSI</option>
                        <option value="D3 AKUNTANSI">D3 AKUNTANSI</option>
                        <option value="S1 AKUNTANSI">S1 AKUNTANSI</option>
                        <option value="S1 MANAJEMEN">S1 MANAJEMEN</option>
                        <option value="S1 HUKUM">S1 HUKUM</option>
                           <option value="S2 FISIKA">S2 FISIKA</option>
                        <option value="S1 PSIKOLOGI">S1 PSIKOLOGI</option>
                        <option value="S1 ADMINISTRASI PUBLIK">S1 ADMINISTRASI PUBLIK</option>
                        <option value="S1 ADMINISTRASI RUMAH SAKIT">S1 ADMINISTRASI RUMAH SAKIT</option>
                    </select>
                </div>

                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                    <input type="password" name="password" class="form-control password-input" placeholder="Password" required>
                    <button type="button" class="password-toggle" onclick="togglePassword(this)">
                        <i class="bi bi-eye-slash"></i>
                    </button>
                </div>

                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" name="password_confirmation" class="form-control password-input" placeholder="Konfirmasi Password" required>
                    <button type="button" class="password-toggle" onclick="togglePassword(this)">
                        <i class="bi bi-eye-slash"></i>
                    </button>
                </div>

                <button type="button" id="btnTriggerModal" class="btn btn-login w-100">REGISTER</button>
            </form>

            <div class="toggle-link">
                <p>Sudah punya akun? <a href="#" id="showLogin">Masuk</a></p>
            </div>
        </div>
    </div>

    {{-- MODAL KONFIRMASI --}}
    <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="color: #333;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="confirmationModalLabel">
                        <i class="bi bi-exclamation-circle-fill text-danger me-2"></i>Konfirmasi Pendaftaran
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-2">
                    <p class="mb-3 text-muted">Sebelum melanjutkan, pastikan data yang Anda masukkan sudah benar.</p>
                    
                    <div class="form-check p-3 bg-light rounded border">
                        <input class="form-check-input mt-1" type="checkbox" value="" id="agreeCheckbox" style="cursor: pointer;">
                        <label class="form-check-label fw-bold small" for="agreeCheckbox" style="cursor: pointer;">
                            Saya menyatakan bahwa:
                            <ul class="mb-0 ps-3 mt-1" style="font-weight: normal;">
                                <li class="mb-1">Telah membaca dan menyetujui seluruh peraturan serta ketentuan yang berlaku di <a style="text-decoration:none; font-style:italic;  font-weight:bold; font-color:#7c1316;" href="https://sindikat-rsudslg.kedirikab.go.id/">halaman utama</a>.</li>
                                <li><b>Bersedia dan komitmen untuk tidak menyebarkan informasi terkait pasien atau RS</b> selama dan setelah praktik (PKL, Penelitian atau Pelatihan) di RSUD SLG.</li>
                                <li class="text-danger">Memahami bahwa tindakan menyebarkan data pasien atau data rahasia rumah sakit adalah pelanggaran hukum dan akan <b>ditindak pidana</b> sesuai peraturan yang berlaku.</li>
                            </ul>
                        </label>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="button" id="btnFinalRegister" class="btn btn-modal-confirm" disabled>
                        Lanjutkan Daftar
                    </button>
                </div>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

    <script>
        function togglePassword(button) {
            const input = button.parentElement.querySelector('input');
            const icon = button.querySelector('i');
            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            } else {
                input.type = "password";
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            // 1. INIT DROPDOWN UNIVERSITAS
            const selectUniv = document.getElementById('universitas-select');
            const badge = document.getElementById('mou-badge');

            const choicesUniv = new Choices(selectUniv, {
                searchEnabled: true,
                itemSelectText: '',
                placeholder: true,
                placeholderValue: 'Pilih Instansi',
                searchPlaceholderValue: 'Cari instansi...',
                noResultsText: 'Tidak ditemukan',
                shouldSort: false,
            });

            // 2. INIT DROPDOWN PRODI
            const selectProdi = document.getElementById('prodi-select');
            const choicesProdi = new Choices(selectProdi, {
                searchEnabled: true,
                itemSelectText: '',
                placeholder: true,
                placeholderValue: 'PILIH PROGRAM STUDI',
                searchPlaceholderValue: 'Cari Program Studi...',
                noResultsText: 'Prodi tidak ditemukan',
                shouldSort: false,
            });

            // Logic Badge MoU
            selectUniv.addEventListener('change', function () {
                const option = selectUniv.options[selectUniv.selectedIndex];
                if (!option) return;
                
                const tanggalKeluar = option.getAttribute('data-tanggal-keluar');
                badge.className = 'mou-badge';
                badge.textContent = '';
                if (!tanggalKeluar) return;

                const today = new Date();
                today.setHours(0,0,0,0);
                const expiredDate = new Date(tanggalKeluar);
                badge.classList.add('show');

                if (expiredDate < today) {
                    badge.textContent = 'MOU EXPIRED';
                    badge.classList.add('expired');
                } else {
                    badge.textContent = `Aktif s/d ${expiredDate.toLocaleDateString('id-ID')}`;
                    badge.classList.add('active');
                }
            });

            // Toggle Form
            document.getElementById('showRegister')?.addEventListener('click', function(e) {
                e.preventDefault();
                document.getElementById('loginForm').style.display = 'none';
                document.getElementById('registerForm').style.display = 'block';
            });

            document.getElementById('showLogin')?.addEventListener('click', function(e) {
                e.preventDefault();
                document.getElementById('registerForm').style.display = 'none';
                document.getElementById('loginForm').style.display = 'block';
            });

            // Modal Confirmation Logic
            const btnTrigger = document.getElementById('btnTriggerModal');
            const btnFinal = document.getElementById('btnFinalRegister');
            const formRegister = document.getElementById('actualRegisterForm');
            const checkbox = document.getElementById('agreeCheckbox');
            const confirmModal = new bootstrap.Modal(document.getElementById('confirmationModal'));

            btnTrigger.addEventListener('click', function() {
                if (formRegister.checkValidity()) {
                    confirmModal.show();
                } else {
                    formRegister.reportValidity();
                }
            });

            checkbox.addEventListener('change', function() {
                if (this.checked) {
                    btnFinal.removeAttribute('disabled');
                } else {
                    btnFinal.setAttribute('disabled', 'disabled');
                }
            });

            btnFinal.addEventListener('click', function() {
                formRegister.submit();
            });
        });
    </script>
</body>
</html>