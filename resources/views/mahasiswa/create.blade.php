@extends('layouts.app')

@section('title', 'Tambah Mahasiswa')
@section('page-title', 'Tambah Mahasiswa')

@section('content')
    <style>
        :root {
            --custom-maroon: #7c1316;
            --custom-maroon-light: #a3191d;
            --custom-maroon-subtle: #fcf0f1;
            --text-dark: #2c3e50;
            --text-muted: #95a5a6;
            --card-radius: 16px;
            --transition: 0.3s ease;
        }

        /* --- Card Styling --- */
        .form-card {
            border: none;
            border-radius: var(--card-radius);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            background: #fff;
            overflow: hidden;
        }

        .card-header-custom {
            background-color: var(--custom-maroon);
            padding: 1.5rem;
            color: white;
            border-bottom: 4px solid var(--custom-maroon-light);
        }

        /* --- Form Styling --- */
        .form-label {
            font-weight: 600;
            color: var(--text-dark);
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .input-group-text {
            background-color: #f8f9fa;
            border-right: none;
            color: var(--custom-maroon);
            border-top-left-radius: 10px;
            border-bottom-left-radius: 10px;
        }

        .form-control,
        .form-select {
            border-left: none;
            border-radius: 0 10px 10px 0;
            padding: 0.7rem 1rem;
            border-color: #dee2e6;
            box-shadow: none !important;
            transition: border-color 0.2s;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--custom-maroon-light);
        }

        /* --- Room Info Box --- */
        .room-info-box {
            background-color: var(--custom-maroon-subtle);
            border: 1px dashed var(--custom-maroon-light);
            border-radius: 10px;
            padding: 1rem;
            margin-top: 10px;
            transition: all 0.3s ease;
        }

        .room-info-box.full {
            background-color: #fee2e2;
            border-color: #ef4444;
        }

        /* --- Buttons --- */
        .btn-maroon {
            background-color: var(--custom-maroon);
            color: white;
            border: none;
            padding: 0.8rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            transition: var(--transition);
            box-shadow: 0 4px 15px rgba(124, 19, 22, 0.2);
        }

        .btn-maroon:hover {
            background-color: var(--custom-maroon-light);
            transform: translateY(-2px);
            color: white;
        }

        .btn-maroon:disabled {
            background-color: #ccc;
            transform: none;
            box-shadow: none;
        }

        .btn-light-custom {
            background: #fff;
            border: 1px solid #dee2e6;
            color: var(--text-dark);
            border-radius: 50px;
            padding: 0.8rem 1.5rem;
            font-weight: 600;
        }

        .btn-light-custom:hover {
            background: #f8f9fa;
            color: var(--custom-maroon);
        }

        /* Animation */
        .animate-up {
            animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
            opacity: 0;
            transform: translateY(20px);
        }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>

    <div class="row justify-content-center animate-up">
        <div class="col-md-8 col-lg-7">
            <div class="form-card">
                <div class="card-header-custom">
                    <h4 class="mb-0 fw-bold"><i class="bi bi-person-plus-fill me-2"></i> Form Tambah Mahasiswa</h4>
                    <p class="mb-0 small opacity-75">Isi data lengkap mahasiswa magang di bawah ini.</p>
                </div>

                <div class="card-body p-4 p-md-5">
                    @if ($errors->any())
                        <div class="alert alert-danger rounded-3 shadow-sm mb-4">
                            <ul class="mb-0 small">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('mahasiswa.store') }}" method="POST" id="form-mahasiswa">
                        @csrf

                        <h6 class="text-muted text-uppercase fw-bold mb-3" style="font-size: 0.75rem; letter-spacing: 1px;">
                            Informasi Dasar</h6>

                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text" name="nm_mahasiswa" class="form-control"
                                    value="{{ old('nm_mahasiswa') }}" placeholder="Contoh: Budi Santoso" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Asal Universitas</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-building"></i></span>
                                    <input type="text" name="univ_asal" class="form-control"
                                        value="{{ old('univ_asal') }}" placeholder="Nama Kampus">
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Program Studi</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-book"></i></span>
                                    <input type="text" name="prodi" class="form-control" value="{{ old('prodi') }}"
                                        placeholder="Jurusan/Prodi">
                                </div>
                            </div>
                        </div>

                        <hr class="my-4 border-light">

                        <h6 class="text-muted text-uppercase fw-bold mb-3" style="font-size: 0.75rem; letter-spacing: 1px;">
                            Penempatan & Durasi</h6>

                        <div class="mb-3">
                            <label class="form-label">Pilih Ruangan (Opsional)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-door-open"></i></span>
                                <select id="ruangan_id" name="ruangan_id" class="form-select">
                                    <option value="">-- Pilih Ruangan --</option>
                                    @foreach ($ruangans as $r)
                                        <option value="{{ $r->id }}"
                                            {{ old('ruangan_id') == $r->id ? 'selected' : '' }}>
                                            {{ $r->nm_ruangan }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div id="ruangan-info" class="room-info-box" style="display: none;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="fw-bold mb-1" id="info-nama" style="color: var(--custom-maroon);"></h6>
                                        <small class="text-muted">
                                            Terisi: <span id="info-terisi" class="fw-bold"></span> / <span
                                                id="info-kuota-total"></span>
                                        </small>
                                    </div>
                                    <div class="text-end">
                                        <span id="badge-status" class="badge rounded-pill px-3 py-2"></span>
                                        <div class="small mt-1 text-muted">Sisa: <span id="info-tersedia"
                                                class="fw-bold"></span></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-calendar-plus"></i></span>
                                    <input type="date" name="tanggal_mulai" class="form-control"
                                        value="{{ old('tanggal_mulai', now()->toDateString()) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label">Tanggal Berakhir <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-calendar-check"></i></span>
                                    <input type="date" name="tanggal_berakhir" class="form-control"
                                        value="{{ old('tanggal_berakhir') }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center pt-3">
                            <a href="{{ route('mahasiswa.index') }}" class="btn btn-light-custom shadow-sm">
                                <i class="bi bi-arrow-left me-2"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-maroon" id="submit-btn">
                                Simpan Data <i class="bi bi-check-lg ms-2"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ruanganSelect = document.getElementById('ruangan_id');
            const ruanganInfo = document.getElementById('ruangan-info');
            const submitBtn = document.getElementById('submit-btn');
            let selectedRuanganFull = false;

            ruanganSelect.addEventListener('change', function() {
                const ruanganId = this.value;

                if (!ruanganId) {
                    ruanganInfo.style.display = 'none';
                    selectedRuanganFull = false;
                    submitBtn.disabled = false;
                    return;
                }

                // Fetch kuota tersedia real-time
                // Pastikan route ini ada di web.php: Route::get('/mahasiswa/ruangan-info/{id}', ...)
                fetch(`/mahasiswa/ruangan-info/${ruanganId}`)
                    .then(response => response.json())
                    .then(data => {
                        // Isi Data
                        document.getElementById('info-nama').textContent = data.nm_ruangan;
                        document.getElementById('info-kuota-total').textContent = data.kuota_total;
                        document.getElementById('info-tersedia').textContent = data.tersedia;
                        document.getElementById('info-terisi').textContent = data.terisi;

                        const badgeStatus = document.getElementById('badge-status');
                        const infoBox = document.getElementById('ruangan-info');

                        // Logika Status Visual
                        if (data.tersedia <= 0) {
                            badgeStatus.className = 'badge rounded-pill bg-danger';
                            badgeStatus.innerHTML = '<i class="bi bi-x-circle me-1"></i> Penuh';
                            infoBox.classList.add('full'); // Tambah style merah

                            selectedRuanganFull = true;
                            submitBtn.disabled = true;
                            submitBtn.innerHTML = '<i class="bi bi-lock-fill me-2"></i> Ruangan Penuh';
                        } else {
                            badgeStatus.className = 'badge rounded-pill bg-success';
                            badgeStatus.innerHTML = '<i class="bi bi-check-circle me-1"></i> Tersedia';
                            infoBox.classList.remove('full');

                            selectedRuanganFull = false;
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = 'Simpan Data <i class="bi bi-check-lg ms-2"></i>';
                        }

                        // Animasi Slide Down sederhana
                        ruanganInfo.style.display = 'block';
                        ruanganInfo.style.opacity = 0;
                        setTimeout(() => {
                            ruanganInfo.style.opacity = 1;
                        }, 50);
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        ruanganInfo.style.display = 'none';
                    });
            });

            // Double check saat submit
            document.getElementById('form-mahasiswa').addEventListener('submit', function(e) {
                if (selectedRuanganFull) {
                    e.preventDefault();
                    alert('Ruangan yang dipilih sudah penuh. Silakan pilih ruangan lain.');
                }
            });
        });
    </script>
@endsection
