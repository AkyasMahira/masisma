@extends('layouts.public')

@section('title', 'Cek Data Pelatihan - RSUD SLG')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    :root {
        --maroon: #7c1316;
        --maroon-hover: #a3191d;
        --bg-light: #f8f9fa;
        --text-dark: #333333;
    }

    body {
        background-color: var(--bg-light);
        color: var(--text-dark);
        line-height: 1.6;
    }

    /* --- Hero Section --- */
    .hero-section {
        background: white;
        padding: 60px 0;
        border-bottom: 1px solid #eee;
        text-align: center;
    }

    .hero-logo {
        height: 100px;
        width: auto;
        margin-bottom: 20px;
    }

    .hero-section h2 {
        font-weight: 700;
        color: var(--maroon);
        margin-bottom: 10px;
    }

    /* --- Search Area --- */
    .search-wrapper {
        margin-top: -35px;
    }

    .search-input-group {
        background: white;
        border-radius: 50px;
        padding: 8px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        display: flex;
        align-items: center;
        border: 1px solid #ddd;
    }

    .search-input-group input {
        border: none;
        padding: 10px 25px;
        border-radius: 50px;
        flex-grow: 1;
        outline: none;
        font-size: 1rem;
    }

    .btn-search-main {
        background: var(--maroon);
        color: white;
        border: none;
        padding: 12px 30px;
        border-radius: 50px;
        font-weight: 600;
        transition: 0.3s;
    }

    .btn-search-main:hover {
        background: var(--maroon-hover);
        box-shadow: 0 4px 12px rgba(124, 19, 22, 0.2);
    }

    /* --- Result Cards --- */
    .result-card {
        background: white;
        border: 1px solid #eaeaea;
        border-radius: 16px;
        padding: 24px;
        height: 100%;
        transition: 0.3s;
    }

    .result-card:hover {
        border-color: var(--maroon);
        box-shadow: 0 8px 24px rgba(0,0,0,0.05);
    }

    .status-tag {
        font-size: 11px;
        font-weight: 700;
        padding: 4px 12px;
        border-radius: 6px;
        text-transform: uppercase;
    }

    .info-row {
        display: flex;
        align-items: start;
        gap: 12px;
        margin-bottom: 12px;
        font-size: 0.9rem;
    }

    .info-row i {
        color: var(--maroon);
        width: 16px;
        margin-top: 4px;
    }

    .btn-outline-detail {
        border: 1.5px solid var(--maroon);
        color: var(--maroon);
        background: transparent;
        width: 100%;
        padding: 10px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.85rem;
        margin-top: 15px;
        transition: 0.2s;
    }

    .btn-outline-detail:hover {
        background: var(--maroon);
        color: white;
    }

    /* --- Modal Custom --- */
    .modal-header {
        border-bottom: none;
        padding: 25px 30px 10px;
    }

    .modal-body {
        padding: 10px 30px 30px;
    }

    .training-row {
        background: #fcfcfc;
        border: 1px solid #efefef;
        padding: 15px;
        border-radius: 12px;
        margin-bottom: 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .pdf-button {
        background: #f1f1f1;
        color: #444;
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
    }

    .pdf-button:hover {
        background: var(--maroon);
        color: white;
    }
</style>

<div class="hero-section">
    <div class="container">
        <img src="/23.png" alt="Logo" class="hero-logo">
        <h2 class="mb-0">Cari Data Pelatihan</h2>
        <p class="text-muted">RSUD Simpang Lima Gumul Kediri</p>
    </div>
</div>

<div class="container">
    {{-- SEARCH BAR --}}
    <div class="row justify-content-center search-wrapper">
        <div class="col-md-8 px-4">
            <form action="{{ route('public.pelatihan.index') }}" method="GET">
                <div class="search-input-group">
                    <input type="text" name="keyword" placeholder="Cari NIP, NIRP, atau Nama Anda..." value="{{ $keyword ?? '' }}" required>
                    <button class="btn-search-main" type="submit">
                        <i class="fas fa-search me-2"></i>Cari
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- RESULTS --}}
    @if ($searchPerformed)
        <div class="mt-5 mb-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0">Hasil Pencarian</h5>
                <hr class="flex-grow-1 mx-3 d-none d-md-block" style="opacity: 0.1;">
                <span class="text-muted small">Ditemukan {{ $pelatihans->count() }} data</span>
            </div>

            @if ($pelatihans->count() > 0)
                <div class="row g-4">
                    @foreach ($pelatihans as $pelatihan)
                        <div class="col-md-6 col-lg-4">
                            <div class="result-card">
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="status-tag {{ $pelatihan->status_pegawai == 'ASN' ? 'bg-primary text-white' : 'bg-warning text-dark' }}">
                                        {{ $pelatihan->status_pegawai }}
                                    </span>
                                    <span class="text-muted small">ID: {{ $pelatihan->nip ?? $pelatihan->nirp }}</span>
                                </div>
                                
                                <h5 class="fw-bold text-dark mb-4">{{ $pelatihan->nama }}</h5>
<div class="info-row">
    <i class="fas fa-fingerprint"></i>
    <span>NIK: {{ $pelatihan->nik ?? '-' }}</span>
</div>
                                <div class="info-row">
                                    <i class="fas fa-id-card"></i>
                                    <span>{{ $pelatihan->jabatan }}</span>
                                </div>
                                <div class="info-row">
                                    <i class="fas fa-layer-group"></i>
                                    <span>{{ $pelatihan->unit }}</span>
                                </div>

                                <button class="btn-outline-detail" data-bs-toggle="modal" data-bs-target="#detail-{{ $pelatihan->id }}">
                                    Lihat Riwayat Pelatihan <i class="fas fa-chevron-right ms-2"></i>
                                </button>
                            </div>
                        </div>

                        {{-- MODAL DETAIL --}}
                        <div class="modal fade" id="detail-{{ $pelatihan->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                                    <div class="modal-header">
                                        <h5 class="fw-bold mb-0">Detail Riwayat</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-4 p-3 bg-light rounded-3">
                                            <div class="small text-muted mb-1 text-uppercase fw-bold" style="letter-spacing: 1px;">Nama Pegawai</div>
                                            <div class="h6 fw-bold mb-0">{{ $pelatihan->nama }}</div>
                                        </div>
{{-- Masukkan di dalam modal-body, di atas h6 "Daftar Pelatihan" --}}
@php
    $currentYear = date('Y');
    $totalJpl = $pelatihan->getTotalJplByYear($currentYear);
@endphp

<div class="mb-4 p-3 border rounded-3 {{ $totalJpl >= 20 ? 'bg-success-subtle' : 'bg-danger-subtle' }}" style="border-style: dashed !important;">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <div class="small fw-bold">REKAP JPL TAHUN {{ $currentYear }}</div>
            <h4 class="mb-0 fw-bold {{ $totalJpl >= 20 ? 'text-success' : 'text-danger' }}">{{ $totalJpl }} <small class="fs-6">JPL</small></h4>
        </div>
        @if($totalJpl >= 20)
            <span class="badge bg-success text-white">Lengkap</span>
        @else
            <span class="badge bg-danger text-white">Kurang {{ 20 - $totalJpl }} JPL</span>
        @endif
    </div>
</div>

{{-- LMS INFO --}}
<div class="mb-4 small">
    <i class="fas fa-cloud-check text-primary me-1"></i> Akun LMS: 
    <strong>{{ $pelatihan->lms_status == 'Ada' ? 'Terdaftar ('.$pelatihan->lms_email.')' : 'Belum Ada' }}</strong>
</div>
                                        <h6 class="fw-bold mb-3">Daftar Pelatihan:</h6>
                                        @php
                                            $allTrainings = collect($pelatihan->pelatihan_dasar ?? [])
                                                            ->merge($pelatihan->pelatihan_peningkatan_kompetensi ?? [])
                                                            ->sortByDesc('tahun');
                                        @endphp

                                       @forelse($allTrainings as $item)
    <div class="training-row">
        <div style="max-width: 75%;">
            <div class="fw-bold small">{{ $item['nama'] }}</div>
            <small class="text-muted">Tahun {{ $item['tahun'] }} | <span class="badge bg-light text-dark border">{{ $item['jpl'] ?? 0 }} JPL</span></small>
        </div>
        {{-- Tombol PDF Tetap Sama --}}
        @if(!empty($item['file']))
            <a href="{{ asset('storage/' . $item['file']) }}" target="_blank" class="pdf-button" title="Lihat PDF">
                <i class="fas fa-file-pdf text-danger"></i>
            </a>
        @endif
    </div>
@empty
                                            <div class="text-center py-4 text-muted">Belum ada riwayat pelatihan.</div>
                                        @endforelse

                                        <div class="mt-4 pt-3 border-top">
                                            <a href="{{ route('public.pelatihan.edit', $pelatihan->id) }}" class="btn btn-light w-100 rounded-3 text-maroon fw-bold small">
                                                <i class="fas fa-edit me-2"></i>Update Data Ini
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <img src="https://illustrations.popsy.co/gray/search.svg" height="150" alt="Not Found" class="mb-4">
                    <h5 class="fw-bold">Data Tidak Ditemukan</h5>
                    <p class="text-muted small">Coba cari dengan NIP atau Nama yang berbeda.</p>
                </div>
            @endif
        </div>
    @endif
</div>
@endsection