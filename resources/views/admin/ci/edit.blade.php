@extends('layouts.app')

@section('title', 'Edit CI')
@section('page-title', 'Edit Clinical Instructor')

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
            border-color: #dee2e6;
        }

        .form-control {
            border-left: none;
            border-radius: 0 10px 10px 0;
            padding: 0.7rem 1rem;
            border-color: #dee2e6;
            box-shadow: none !important;
            transition: border-color 0.2s;
            color: var(--text-dark);
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
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
        }
        .btn-light-custom:hover {
            background: #f8f9fa;
            color: var(--custom-maroon);
        }

        /* Animation */
        .animate-up {
            animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
            opacity: 0; transform: translateY(20px);
        }
        @keyframes fadeInUp { to { opacity: 1; transform: translateY(0); } }
    </style>

    <div class="row justify-content-center animate-up">
        <div class="col-12">
            <div class="form-card">
                <div class="card-header-custom">
                    <h4 class="mb-0 fw-bold"><i class="bi bi-pencil-square me-2"></i> Edit Data CI</h4>
                    <p class="mb-0 small opacity-75">Perbarui informasi Clinical Instructor (Pembimbing Lapangan).</p>
                </div>

                <div class="card-body p-4 p-md-5">
                    
                    <form action="{{ route('admin.ci.update', $ci->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row g-3 mb-3">
                            {{-- Nama CI --}}
                            <div class="col-md-12">
                                <label for="nama" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                                    <input type="text" name="nama" id="nama" class="form-control @error('nama') is-invalid @enderror" 
                                           value="{{ old('nama', $ci->nama) }}" placeholder="Nama Lengkap Pembimbing" required>
                                </div>
                                @error('nama') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            {{-- No HP --}}
                            <div class="col-md-6">
                                <label for="no_hp" class="form-label">No. HP / WhatsApp <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-whatsapp"></i></span>
                                    <input type="text" name="no_hp" id="no_hp" class="form-control @error('no_hp') is-invalid @enderror" 
                                           value="{{ old('no_hp', $ci->no_hp) }}" placeholder="0812xxxxxx" required>
                                </div>
                                @error('no_hp') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            {{-- Bidang --}}
                            <div class="col-md-6">
                                <label for="bidang" class="form-label">Bidang / Departemen <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-diagram-3"></i></span>
                                    <input type="text" name="bidang" id="bidang" class="form-control @error('bidang') is-invalid @enderror" 
                                           value="{{ old('bidang', $ci->bidang) }}" placeholder="Contoh: IT, HRD, Keuangan" required>
                                </div>
                                @error('bidang') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="alert alert-light border border-dashed d-flex align-items-center mb-4" style="background-color: var(--custom-maroon-subtle);">
                            <i class="bi bi-info-circle-fill text-custom-maroon me-3 fs-5"></i>
                            <div class="small text-muted">
                                Pastikan nomor HP aktif dan terhubung dengan WhatsApp untuk memudahkan komunikasi mahasiswa.
                            </div>
                        </div>

                        <hr class="my-4 border-light">

                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('admin.ci.index') }}" class="btn-light-custom shadow-sm">
                                <i class="bi bi-arrow-left"></i> Batal
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