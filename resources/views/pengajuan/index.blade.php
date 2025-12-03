@extends('layouts.app')

@section('title', 'Pengajuan')
@section('page-title', 'Layanan Pengajuan')

@section('content')
    <style>
        :root {
            --custom-maroon: #7c1316;
            --custom-maroon-light: #a3191d;
            --custom-maroon-subtle: #fcf0f1;
            --text-dark: #2c3e50;
            --text-muted: #64748b;
            --card-radius: 20px;
            --transition: 0.3s ease;
        }

        /* --- Option Cards (Pilihan Menu) --- */
        .option-card {
            background: #fff;
            border: 2px solid transparent;
            border-radius: var(--card-radius);
            padding: 2rem;
            text-align: center;
            transition: var(--transition);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            cursor: default;
        }

        .option-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(124, 19, 22, 0.15);
            border-color: rgba(124, 19, 22, 0.1);
        }

        .icon-circle {
            width: 80px;
            height: 80px;
            background-color: var(--custom-maroon-subtle);
            color: var(--custom-maroon);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
            transition: var(--transition);
        }

        .option-card:hover .icon-circle {
            background-color: var(--custom-maroon);
            color: white;
            transform: scale(1.1);
        }

        .option-title {
            font-weight: 700;
            font-size: 1.25rem;
            margin-bottom: 0.5rem;
            color: var(--text-dark);
        }

        .option-desc {
            color: var(--text-muted);
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }

        /* --- Status Card --- */
        .status-card {
            background: white;
            border-radius: var(--card-radius);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            border-top: 5px solid var(--custom-maroon);
            height: 100%;
        }

        .status-header {
            padding: 2rem 2rem 1rem;
            text-align: center;
        }

        .status-icon-large {
            font-size: 4rem;
            margin-bottom: 1rem;
            display: block;
        }

        .status-body {
            padding: 0 2rem 2rem;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 0.8rem 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        /* --- Badges --- */
        .badge-lg {
            padding: 0.5rem 1.2rem;
            border-radius: 50px;
            font-size: 0.9rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .bg-pending { background-color: #fff7ed; color: #c2410c; border: 1px solid #ffedd5; }
        .bg-approved { background-color: #f0fdf4; color: #15803d; border: 1px solid #dcfce7; }
        .bg-rejected { background-color: #fef2f2; color: #b91c1c; border: 1px solid #fee2e2; }

        /* --- Buttons --- */
        .btn-maroon {
            background-color: var(--custom-maroon);
            color: white;
            border: none;
            padding: 0.7rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            transition: var(--transition);
            width: 100%;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.9rem;
        }

        .btn-maroon:hover {
            background-color: var(--custom-maroon-light);
            transform: translateY(-2px);
            color: white;
            box-shadow: 0 5px 15px rgba(124, 19, 22, 0.2);
        }

        /* Animation */
        .animate-up {
            animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
            opacity: 0;
            transform: translateY(30px);
        }

        @keyframes fadeInUp {
            to { opacity: 1; transform: translateY(0); }
        }
    </style>

<div class="container py-4">

    {{-- OPTIONAL: Jika sudah pakai SweetAlert Flash Message, blok Alert HTML ini bisa dihapus agar tidak muncul double --}}
    {{-- Alert Success --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show animate-up mb-4" role="alert"
            style="border-radius: 12px; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.1);">
            <div class="d-flex align-items-center">
                <i class="bi bi-check-circle-fill fs-4 me-3"></i>
                <div>
                    <strong>Berhasil!</strong> {{ session('success') }}
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Alert Error --}}
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show animate-up mb-4" role="alert"
            style="border-radius: 12px;">
            <div class="d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
                <div>
                    <strong>Gagal!</strong> {{ session('error') }}
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="text-center mb-5 animate-up">
        <h2 class="fw-bold" style="color: var(--text-dark);">Dashboard Pengajuan</h2>
        <p class="text-muted">Kelola pengajuan Pra-Penelitian dan Magang Anda di sini.</p>
    </div>

    <div class="row justify-content-center g-4">

        {{-- ========================== KOLOM 1: PRA PENELITIAN ========================== --}}
        <div class="col-md-6 col-lg-5 animate-up" style="animation-delay: 0.1s;">

            @if ($pra)
                {{-- JIKA SUDAH ADA DATA PRA PENELITIAN -> TAMPILKAN STATUS RINGKAS --}}
                <div class="status-card h-100">
                    <div class="status-header text-center">
                        <h5 class="fw-bold mb-3">Pra Penelitian</h5>

                        @if ($pra->status === 'pending')
                            <div class="status-icon-large text-warning mb-2"><i class="bi bi-hourglass-split"></i></div>
                            <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">Menunggu Persetujuan</span>
                        @elseif ($pra->status === 'approved')
                            <div class="status-icon-large text-success mb-2"><i class="bi bi-check-circle-fill"></i></div>
                            <span class="badge bg-success px-3 py-2 rounded-pill">Disetujui</span>
                        @elseif ($pra->status === 'rejected')
                            <div class="status-icon-large text-danger mb-2"><i class="bi bi-x-circle-fill"></i></div>
                            <span class="badge bg-danger px-3 py-2 rounded-pill">Ditolak</span>
                        @endif
                    </div>

                    <div class="status-body mt-3">
                        <div class="detail-row d-flex justify-content-between mb-2 border-bottom pb-2">
                            <span class="text-muted small">Tanggal Pengajuan</span>
                            <span class="fw-bold small">{{ $pra->created_at->format('d M Y') }}</span>
                        </div>
                        <div class="detail-row d-flex justify-content-between mb-3 border-bottom pb-2">
                            <span class="text-muted small">Update Terakhir</span>
                            <span class="fw-bold small">{{ $pra->updated_at->diffForHumans() }}</span>
                        </div>

                        @if ($pra->status === 'rejected')
                            <div class="alert alert-danger small py-2 mt-3">
                                <i class="bi bi-info-circle me-1"></i> Pengajuan Anda ditolak. Silakan ajukan ulang.
                            </div>
                            <form action="{{ route('pengajuan.pra') }}" method="POST" class="w-100 mt-3">
                                @csrf
                                {{-- PERBAIKAN TOMBOL RE-APPLY PRA --}}
                                <button type="submit" class="btn btn-outline-danger w-100 btn-sm btn-confirm"
                                    data-confirm-text="Apakah Anda yakin ingin mengajukan ulang Pra Penelitian?">
                                    <i class="bi bi-arrow-repeat me-1"></i> Ajukan Ulang
                                </button>
                            </form>
                        @elseif ($pra->status === 'approved')
                            <div class="text-center mt-3">
                                <a href="{{ route('pengajuan.detail', 'pra_penelitian') }}" class="btn btn-maroon btn-sm w-100">
                                    <i class="bi bi-eye me-1"></i> Lihat Detail & Kelola
                                </a>
                            </div>
                        @else
                            <div class="alert alert-light text-center small text-muted mb-0">
                                Mohon menunggu verifikasi admin.
                            </div>
                        @endif
                    </div>
                </div>
            @else
                {{-- JIKA BELUM ADA DATA -> TAMPILKAN TOMBOL PENGAJUAN --}}
                <div class="option-card h-100 d-flex flex-column">
                    <div class="icon-circle align-self-center mb-3">
                        <i class="bi bi-journal-richtext"></i>
                    </div>
                    <h4 class="option-title text-center">Pra Penelitian</h4>
                    <p class="option-desc text-center text-muted mb-4">
                        Ajukan permohonan untuk melakukan observasi, pengambilan data awal, atau survei pendahuluan.
                    </p>
                    <form action="{{ route('pengajuan.pra') }}" method="POST" class="w-100 mt-auto">
                        @csrf
                        {{-- PERBAIKAN TOMBOL AJUKAN PRA --}}
                        <button type="submit" class="btn btn-maroon w-100 btn-confirm"
                            data-confirm-text="Apakah Anda yakin ingin mengajukan Pra Penelitian?">
                            Pilih Layanan <i class="bi bi-arrow-right ms-2"></i>
                        </button>
                    </form>
                </div>
            @endif
        </div>

        {{-- ========================== KOLOM 2: MAGANG ========================== --}}
        <div class="col-md-6 col-lg-5 animate-up" style="animation-delay: 0.2s;">

            @if ($magang)
                {{-- JIKA SUDAH ADA DATA MAGANG -> TAMPILKAN STATUS --}}
                <div class="status-card h-100">
                    <div class="status-header text-center">
                        <h5 class="fw-bold mb-3">Magang / PKL</h5>

                        @if ($magang->status === 'pending')
                            <div class="status-icon-large text-warning mb-2"><i class="bi bi-hourglass-split"></i></div>
                            <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">Menunggu Persetujuan</span>
                        @elseif ($magang->status === 'approved')
                            <div class="status-icon-large text-success mb-2"><i class="bi bi-check-circle-fill"></i></div>
                            <span class="badge bg-success px-3 py-2 rounded-pill">Disetujui</span>
                        @elseif ($magang->status === 'rejected')
                            <div class="status-icon-large text-danger mb-2"><i class="bi bi-x-circle-fill"></i></div>
                            <span class="badge bg-danger px-3 py-2 rounded-pill">Ditolak</span>
                        @endif
                    </div>

                    <div class="status-body mt-3">
                        <div class="detail-row d-flex justify-content-between mb-2 border-bottom pb-2">
                            <span class="text-muted small">Tanggal Pengajuan</span>
                            <span class="fw-bold small">{{ $magang->created_at->format('d M Y') }}</span>
                        </div>
                        <div class="detail-row d-flex justify-content-between mb-3 border-bottom pb-2">
                            <span class="text-muted small">Update Terakhir</span>
                            <span class="fw-bold small">{{ $magang->updated_at->diffForHumans() }}</span>
                        </div>

                        @if ($magang->status === 'rejected')
                            <div class="alert alert-danger small py-2 mt-3">
                                <i class="bi bi-info-circle me-1"></i> Pengajuan Anda ditolak. Silakan ajukan ulang.
                            </div>
                            <form action="{{ route('pengajuan.magang') }}" method="POST" class="w-100 mt-3">
                                @csrf
                                {{-- PERBAIKAN TOMBOL RE-APPLY MAGANG --}}
                                <button type="submit" class="btn btn-outline-danger w-100 btn-sm btn-confirm"
                                    data-confirm-text="Apakah Anda yakin ingin mengajukan ulang Magang?">
                                    <i class="bi bi-arrow-repeat me-1"></i> Ajukan Ulang
                                </button>
                            </form>
                        @elseif ($magang->status === 'approved')
                            @php
                                $mahasiswa = App\Models\Mahasiswa::where('user_id', auth()->id())->first();
                            @endphp

                            @if (!$mahasiswa)
                                <div class="alert alert-info small py-2">
                                    <i class="bi bi-info-circle me-1"></i> Silakan lengkapi biodata magang terlebih dahulu
                                </div>
                                <div class="text-center mt-3">
                                    <a href="{{ route('mahasiswa.create') }}" class="btn btn-maroon btn-sm w-100">
                                        <i class="bi bi-pencil-square me-1"></i> Lengkapi Biodata Magang
                                    </a>
                                </div>
                            @else
                                <div class="alert alert-success small py-2">
                                    <i class="bi bi-check-circle me-1"></i> Biodata magang telah lengkap
                                </div>
                                <div class="text-center mt-3">
                                    <a href="{{ route('mahasiswa.dashboard') }}" class="btn btn-maroon btn-sm w-100">
                                        <i class="bi bi-speedometer2 me-1"></i> Lihat Dashboard Magang
                                    </a>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            @else
                {{-- JIKA BELUM ADA DATA -> TAMPILKAN TOMBOL PENGAJUAN --}}
                <div class="option-card h-100 d-flex flex-column">
                    <div class="icon-circle align-self-center mb-3">
                        <i class="bi bi-briefcase"></i>
                    </div>
                    <h4 class="option-title text-center">Magang / PKL</h4>
                    <p class="option-desc text-center text-muted mb-4">
                        Ajukan permohonan resmi untuk pelaksanaan Praktik Kerja Lapangan (PKL) atau Magang.
                    </p>
                    <form action="{{ route('pengajuan.magang') }}" method="POST" class="w-100 mt-auto">
                        @csrf
                        {{-- PERBAIKAN TOMBOL AJUKAN MAGANG --}}
                        <button type="submit" class="btn btn-maroon w-100 btn-confirm"
                            data-confirm-text="Apakah Anda yakin ingin mengajukan Magang?">
                            Pilih Layanan <i class="bi bi-arrow-right ms-2"></i>
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection