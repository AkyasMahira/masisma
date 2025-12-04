@extends('layouts.app')

@section('title', 'Edit Jadwal Rolling')
@section('page-title', 'Edit Jadwal Rolling')

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
                    <h4 class="mb-0 fw-bold"><i class="bi bi-pencil-square me-2"></i> Edit Rencana Jadwal</h4>
                    <p class="mb-0 small opacity-75">Perbarui data jadwal rolling mahasiswa.</p>
                </div>

                <div class="card-body p-4 p-md-5">
                    
                    <form action="{{ route('room_sequences.update', $sequence->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-4">
                            <label class="form-label">Mahasiswa <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                @php
                                    $user = auth()->user();
                                    $mahasiswa = $mahasiswas->where('id', $sequence->mahasiswa_id)->first();
                                @endphp
                                <input type="hidden" name="mahasiswa_id" value="{{ $mahasiswa ? $mahasiswa->id : '' }}">
                                <input type="text" class="form-control" value="{{ $mahasiswa ? $mahasiswa->nm_mahasiswa : '-' }}" readonly>
                            </div>
                            @error('mahasiswa_id')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label">Tanggal Mulai (Check-in)</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-calendar-event"></i></span>
                                    <input type="date" name="start_date" 
                                        class="form-control @error('start_date') is-invalid @enderror" 
                                        value="{{ old('start_date', \Carbon\Carbon::parse($sequence->start_date)->format('Y-m-d')) }}">
                                </div>
                                @error('start_date')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="form-label">Tanggal Selesai (Check-out)</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-calendar-check"></i></span>
                                    <input type="date" name="end_date" 
                                        class="form-control @error('end_date') is-invalid @enderror" 
                                        value="{{ old('end_date', \Carbon\Carbon::parse($sequence->end_date)->format('Y-m-d')) }}">
                                </div>
                                @error('end_date')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Pilih Ruangan Tujuan <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-door-open"></i></span>
                                <select name="ruangan_id" class="form-select @error('ruangan_id') is-invalid @enderror">
                                    @foreach($ruangans as $room)
                                        <option value="{{ $room->id }}" 
                                            {{ old('ruangan_id', $sequence->ruangan_id) == $room->id ? 'selected' : '' }}>
                                            {{-- Cek nama kolom di DB kamu --}}
                                            {{ $room->nama_ruangan ?? $room->nm_ruangan }}
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
                                Update Jadwal <i class="bi bi-check-lg ms-2"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection