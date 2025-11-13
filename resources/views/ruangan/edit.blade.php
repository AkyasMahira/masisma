@extends('layouts.app')

@section('title', 'Edit Ruangan')
@section('page-title', 'Edit Ruangan')

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

        .form-control {
            border-left: none;
            border-radius: 0 10px 10px 0;
            padding: 0.7rem 1rem;
            border-color: #dee2e6;
            box-shadow: none !important;
            transition: border-color 0.2s;
        }

        .form-control:focus {
            border-color: var(--custom-maroon-light);
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
        <div class="col-md-8 col-lg-6">
            <div class="form-card">
                <div class="card-header-custom">
                    <h4 class="mb-0 fw-bold"><i class="bi bi-pencil-square me-2"></i> Edit Ruangan</h4>
                    <p class="mb-0 small opacity-75">Perbarui informasi ruangan magang.</p>
                </div>

                <div class="card-body p-4 p-md-5">
                    @if ($errors->any())
                        <div class="alert alert-danger rounded-3 shadow-sm mb-4">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                                <strong class="mb-0">Terjadi Kesalahan!</strong>
                            </div>
                            <ul class="mb-0 small ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('ruangan.update', $ruangan->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="nm_ruangan" class="form-label">Nama Ruangan <span
                                    class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-door-closed"></i></span>
                                <input type="text" class="form-control @error('nm_ruangan') is-invalid @enderror"
                                    id="nm_ruangan" name="nm_ruangan" value="{{ old('nm_ruangan', $ruangan->nm_ruangan) }}"
                                    placeholder="Contoh: Ruang Mawar Lt. 2" required>
                            </div>
                            @error('nm_ruangan')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="kuota_ruangan" class="form-label">Kuota Maksimal <span
                                    class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-people-fill"></i></span>
                                <input type="number" class="form-control @error('kuota_ruangan') is-invalid @enderror"
                                    id="kuota_ruangan" name="kuota_ruangan"
                                    value="{{ old('kuota_ruangan', $ruangan->kuota_ruangan) }}"
                                    placeholder="Masukkan angka (misal: 10)" min="1" required>
                            </div>
                            <div class="form-text text-muted small mt-1">
                                <i class="bi bi-info-circle me-1"></i> Pastikan kuota baru cukup untuk mahasiswa yang ada.
                            </div>
                            @error('kuota_ruangan')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4 border-light">

                        <div class="d-flex justify-content-between align-items-center pt-2">
                            <a href="{{ route('ruangan.index') }}" class="btn btn-light-custom shadow-sm">
                                <i class="bi bi-arrow-left me-2"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-maroon">
                                Simpan Perubahan <i class="bi bi-check-lg ms-2"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
