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
            --card-radius: 16px;
        }
        .form-card { border: none; border-radius: var(--card-radius); box-shadow: 0 10px 30px rgba(0,0,0,0.08); background: #fff; overflow: hidden; }
        .card-header-custom { background-color: var(--custom-maroon); padding: 1.5rem; color: white; border-bottom: 4px solid var(--custom-maroon-light); }
        .form-control, .form-select { border-radius: 8px; padding: 0.7rem 1rem; border-color: #dee2e6; }
        .form-control:focus, .form-select:focus { border-color: var(--custom-maroon-light); box-shadow: 0 0 0 0.25rem rgba(124, 19, 22, 0.1); }
        .btn-maroon { background-color: var(--custom-maroon); color: white; border-radius: 50px; padding: 0.8rem 2rem; font-weight: 600; border: none; }
        .btn-maroon:hover { background-color: var(--custom-maroon-light); color: white; transform: translateY(-2px); }
    </style>

    <div class="row justify-content-center animate-up">
        <div class="col-md-8 col-lg-6">
            <div class="form-card">
                <div class="card-header-custom">
                    <h4 class="mb-0 fw-bold"><i class="bi bi-calendar-plus me-2"></i> Tambah Jadwal Rolling</h4>
                    <p class="mb-0 small opacity-75">Tentukan ruangan dan tanggal masuk Anda.</p>
                </div>

                <div class="card-body p-4 p-md-5">
                    
                    {{-- 1. ALERT ERROR DARI CONTROLLER (JIKA BENTROK / DILUAR TANGGAL) --}}
                    @if($errors->any())
                        <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-exclamation-octagon-fill fs-4 me-2"></i>
                                <strong class="fs-6">Gagal Menyimpan Jadwal!</strong>
                            </div>
                            <ul class="mb-0 small ps-3">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- 2. INFO BOX: PERIODE MAGANG MAHASISWA --}}
                    @if(isset($infoMahasiswa) && $infoMahasiswa->tanggal_mulai)
                        <div class="alert alert-info border-0 shadow-sm rounded-3 d-flex align-items-start mb-4">
                            <i class="bi bi-info-circle-fill fs-4 me-3 mt-1"></i>
                            <div>
                                <strong>Info Periode Magang Anda:</strong><br>
                                <span class="badge bg-primary">
                                    {{ \Carbon\Carbon::parse($infoMahasiswa->tanggal_mulai)->format('d M Y') }}
                                </span>
                                <span class="text-muted mx-1">s/d</span>
                                <span class="badge bg-primary">
                                    {{ \Carbon\Carbon::parse($infoMahasiswa->tanggal_berakhir)->format('d M Y') }}
                                </span>
                                <div class="small text-muted mt-1">
                                    *Anda tidak bisa memilih tanggal di luar periode ini.
                                </div>
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('room_sequences.store') }}" method="POST">
                        @csrf
                        
                        {{-- INPUT MAHASISWA --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold">Mahasiswa</label>
                            @php
                                $user = auth()->user();
                                $isAdmin = method_exists($user, 'hasRole') ? $user->hasRole('admin') : ($user->role === 'admin');
                            @endphp

                            @if($isAdmin)
                                <select name="mahasiswa_id" class="form-select @error('mahasiswa_id') is-invalid @enderror">
                                    <option value="">-- Cari Nama Mahasiswa --</option>
                                    @foreach($mahasiswas as $mhs)
                                        <option value="{{ $mhs->id }}" {{ old('mahasiswa_id') == $mhs->id ? 'selected' : '' }}>
                                            {{ $mhs->nm_mahasiswa ?? $mhs->name }}
                                        </option>
                                    @endforeach
                                </select>
                            @else
                                {{-- Jika login sebagai mahasiswa, otomatis terisi dan readonly --}}
                                @php
                                    $mhsLogin = $infoMahasiswa ?? $mahasiswas->where('user_id', $user->id)->first();
                                @endphp
                                <input type="hidden" name="mahasiswa_id" value="{{ $mhsLogin->id ?? '' }}">
                                <input type="text" class="form-control bg-light" value="{{ $mhsLogin->nm_mahasiswa ?? auth()->user()->name }}" readonly>
                            @endif
                        </div>

                        {{-- INPUT TANGGAL --}}
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold">Tanggal Mulai (Masuk)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-calendar-event"></i></span>
                                    <input type="date" name="start_date" 
                                        class="form-control border-start-0 @error('start_date') is-invalid @enderror" 
                                        value="{{ old('start_date') }}" required>
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold">Tanggal Selesai (Keluar)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-calendar-check"></i></span>
                                    <input type="date" name="end_date" 
                                        class="form-control border-start-0 @error('end_date') is-invalid @enderror" 
                                        value="{{ old('end_date') }}" required>
                                </div>
                            </div>
                        </div>

                        {{-- INPUT RUANGAN --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold">Ruangan Tujuan</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-door-open"></i></span>
                                <select name="ruangan_id" class="form-select border-start-0 @error('ruangan_id') is-invalid @enderror">
                                    <option value="">-- Pilih Ruangan --</option>
                                    @foreach($ruangans as $room)
                                        <option value="{{ $room->id }}" {{ old('ruangan_id') == $room->id ? 'selected' : '' }}>
                                            {{ $room->nm_ruangan ?? $room->nama_ruangan }} 
                                            ({{ ucfirst($room->kategori ?? 'Non-Shift') }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <hr class="my-4 border-light">

                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('room_sequences.index') }}" class="btn btn-light border shadow-sm px-4 rounded-pill">
                                <i class="bi bi-arrow-left me-2"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-maroon shadow-sm">
                                Simpan Jadwal <i class="bi bi-check-lg ms-2"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection