@extends('layouts.app')

@section('title', 'Tambah Jadwal Rolling')
@section('page-title', 'Tambah Jadwal Rolling')

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

        .form-control, .form-select {
            border-left: none;
            border-radius: 0 10px 10px 0;
            padding: 0.7rem 1rem;
            border-color: #dee2e6;
            box-shadow: none !important;
            transition: border-color 0.2s;
            color: var(--text-dark);
        }

        .form-control:focus, .form-select:focus {
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
            opacity: 0; transform: translateY(20px);
        }
        @keyframes fadeInUp { to { opacity: 1; transform: translateY(0); } }
    </style>

    <div class="row justify-content-center animate-up">
        <div class="col-md-8 col-lg-6">
            <div class="form-card">
                <div class="card-header-custom">
                    <h4 class="mb-0 fw-bold"><i class="bi bi-calendar-plus me-2"></i> Tambah Jadwal Rolling</h4>
                    <p class="mb-0 small opacity-75">Atur perpindahan ruangan mahasiswa secara terjadwal.</p>
                </div>

                <div class="card-body p-4 p-md-5">
                    
                    {{-- Error Alert --}}
                    @if($errors->any())
                        <div class="alert alert-danger rounded-3 shadow-sm mb-4">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                                <strong class="mb-0">Periksa Inputan!</strong>
                            </div>
                            <ul class="mb-0 small ps-3">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Info Box --}}
                    <div class="alert alert-light border border-dashed d-flex align-items-center mb-4" style="background-color: var(--custom-maroon-subtle); border-color: var(--custom-maroon-light);">
                        <i class="bi bi-info-circle-fill text-custom-maroon me-3 fs-4"></i>
                        <div class="small text-muted">
                            Sistem akan otomatis memindahkan mahasiswa ke ruangan tujuan pada tanggal yang ditentukan.
                        </div>
                    </div>

                    <form action="{{ route('room_sequences.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-4">
                            <label class="form-label">Pilih Mahasiswa <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <select name="mahasiswa_id" class="form-select @error('mahasiswa_id') is-invalid @enderror">
                                    <option value="">-- Cari Nama Mahasiswa --</option>
                                    @foreach($mahasiswas as $mhs)
                                        <option value="{{ $mhs->id }}" {{ old('mahasiswa_id') == $mhs->id ? 'selected' : '' }}>
                                            {{ $mhs->nm_mahasiswa ?? $mhs->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('mahasiswa_id')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label">Tanggal Mulai (Masuk)</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-calendar-event"></i></span>
                                    <input type="date" name="start_date" 
                                        class="form-control @error('start_date') is-invalid @enderror" 
                                        value="{{ old('start_date') }}" required>
                                </div>
                                @error('start_date')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="form-label">Tanggal Selesai (Keluar)</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-calendar-check"></i></span>
                                    <input type="date" name="end_date" 
                                        class="form-control @error('end_date') is-invalid @enderror" 
                                        value="{{ old('end_date') }}" required>
                                </div>
                                @error('end_date')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Ruangan Tujuan <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-door-open"></i></span>
                                <select name="ruangan_id" class="form-select @error('ruangan_id') is-invalid @enderror">
                                    <option value="">-- Pilih Ruangan --</option>
                                    @foreach($ruangans as $room)
                                        <option value="{{ $room->id }}" {{ old('ruangan_id') == $room->id ? 'selected' : '' }}>
                                            {{ $room->nm_ruangan ?? $room->nama_ruangan }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('ruangan_id')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4 border-light">

                        <div class="d-flex justify-content-between align-items-center pt-2">
                            <a href="{{ route('room_sequences.index') }}" class="btn btn-light-custom shadow-sm">
                                <i class="bi bi-arrow-left me-2"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-maroon">
                                Simpan Jadwal <i class="bi bi-check-lg ms-2"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection