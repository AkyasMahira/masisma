@extends('layouts.app')

@section('title', 'Tambah Ruangan')
@section('page-title', 'Tambah Ruangan Baru')

@section('content')

{{--
  1. TAMBAHKAN KODE INI
  Ini adalah elemen HTML untuk animasi background
--}}
<div class="background-animation">
    <div class="circle c1"></div>
    <div class="circle c2"></div>
    <div class="circle c3"></div>
    <div class="circle c4"></div>
    <div class="circle c5"></div>
    <div class="circle c6"></div>
</div>

{{--
  Beri style 'position: relative' dan 'z-index: 2'
  agar form ini TAMPIL DI DEPAN animasi background
--}}
<div class="row justify-content-center" style="position: relative; z-index: 2;">
    <div class="col-md-8">
        <div class="card shadow-sm card-animated">

            <div class="card-header bg-custom-maroon text-white">
                <h4 class="card-title mb-0">
                    <i class="fas fa-plus-circle me-2"></i>
                    Form Tambah Ruangan
                </h4>
            </div>

            <div class="card-body p-4">

                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">
                        <h5 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i> Terjadi Kesalahan!</h5>
                        <p>Mohon periksa kembali inputan Anda sebelum menyimpan.</p>
                        <hr>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('ruangan.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="nm_ruangan" class="form-label fw-bold">Nama Ruangan</label>
                        <div class="input-group">
                            <span class="input-group-text text-custom-maroon"><i class="fas fa-bed fa-fw"></i></span>
                            <input type="text" class="form-control @error('nm_ruangan') is-invalid @enderror"
                                   id="nm_ruangan" name="nm_ruangan"
                                   value="{{ old('nm_ruangan') }}"
                                   placeholder="Contoh: Ruang Mawar Lt. 2" required>

                            @error('nm_ruangan')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="kuota_ruangan" class="form-label fw-bold">Kuota Ruangan</label>
                        <div class="input-group">
                            <span class="input-group-text text-custom-maroon"><i class="fas fa-users fa-fw"></i></span>
                            <input type="number" class="form-control @error('kuota_ruangan') is-invalid @enderror"
                                   id="kuota_ruangan" name="kuota_ruangan"
                                   value="{{ old('kuota_ruangan') }}"
                                   placeholder="Masukkan jumlah kuota (misal: 10)" required min="1">

                            @error('kuota_ruangan')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-end">
                        <a href="{{ route('ruangan.index') }}" class="btn btn-secondary me-2">
                            <i class="fas fa-arrow-left me-1"></i>
                            Batal
                        </a>
                        <button type="submit" class="btn btn-custom-maroon">
                            <i class="fas fa-save me-1"></i>
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
{{--
  2. TAMBAHKAN/GANTI KODE CSS DI SINI
--}}
<style>
    :root {
        --custom-maroon: #7c1316;
        --custom-maroon-darker: #5f0f11;

        /* Warna untuk lingkaran (versi RGBA dari #7c1316) */
        --maroon-rgba: rgba(124, 19, 22, 0.15);
    }

    /* ... (Kode CSS .bg-custom-maroon, .btn-custom-maroon, dll. tetap sama) ... */
    .bg-custom-maroon {
        background-color: var(--custom-maroon) !important;
    }
    .text-custom-maroon {
        color: var(--custom-maroon) !important;
    }
    .btn-custom-maroon {
        background-color: var(--custom-maroon);
        border-color: var(--custom-maroon);
        color: #fff;
    }
    .btn-custom-maroon:hover {
        background-color: var(--custom-maroon-darker);
        border-color: var(--custom-maroon-darker);
        color: #fff;
    }
    .input-group-text {
        background-color: #f8f9fa;
        border-right: 0;
    }
    .form-control { border-left: 0; }
    .form-control:focus { border-left: 1px solid #ced4da; }
    .form-control.is-invalid { border-left: 1px solid #dc3545; }


    /* --- ANIMASI CARD FADE-IN --- */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    .card-animated {
        /* Kita perlambat sedikit agar lebih halus */
        animation: fadeInUp 0.8s ease-out forwards;
    }


    /* --- ANIMASI BACKGROUND LINGKARAN --- */

    .background-animation {
        position: fixed; /* Tetap di layar */
        top: 0;
        left: 0;
        width: 100%;
        height: 100vh;
        overflow: hidden;
        z-index: 1; /* Posisikan di belakang form (yang punya z-index: 2) */
    }

    .circle {
        position: absolute;
        display: block;
        list-style: none;
        background: var(--maroon-rgba); /* Warna dari CSS variabel */
        animation: animate-circles 25s linear infinite;
        bottom: -150px; /* Mulai dari bawah layar */
    }

    /* Variasi ukuran, posisi, dan kecepatan */
    .c1 { left: 25%; width: 80px; height: 80px; animation-delay: 0s; }
    .c2 { left: 10%; width: 20px; height: 20px; animation-delay: 2s; animation-duration: 12s; }
    .c3 { left: 70%; width: 20px; height: 20px; animation-delay: 4s; }
    .c4 { left: 40%; width: 60px; height: 60px; animation-delay: 0s; animation-duration: 18s; }
    .c5 { left: 65%; width: 20px; height: 20px; animation-delay: 0s; }
    .c6 { left: 85%; width: 110px; height: 110px; animation-delay: 3s; }

    @keyframes animate-circles {
        0% {
            transform: translateY(0);
            opacity: 1;
        }
        100% {
            transform: translateY(-1000px); /* Bergerak ke atas */
            opacity: 0;
        }
    }

</style>
@endsection
