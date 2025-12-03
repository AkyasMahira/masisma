@extends('layouts.app')

@section('title', 'Detail Pra Penelitian')
@section('page-title', 'Detail Pra Penelitian')

@section('content')
    <style>
        :root {
            --custom-maroon: #7c1316;
            --custom-maroon-light: #a3191d;
            --custom-maroon-subtle: #fcf0f1;
            --text-dark: #2c3e50;
            --text-muted: #64748b;
            --card-radius: 16px;
            --shadow-soft: 0 4px 20px rgba(0, 0, 0, 0.05);
            --transition: 0.3s ease;
        }

        /* --- Header --- */
        .page-header-wrapper {
            background: #fff;
            border-radius: var(--card-radius);
            padding: 1.5rem;
            box-shadow: var(--shadow-soft);
            margin-bottom: 2rem;
            border-left: 5px solid var(--custom-maroon);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        /* --- Cards --- */
        .detail-card {
            background: #fff;
            border-radius: var(--card-radius);
            box-shadow: var(--shadow-soft);
            margin-bottom: 1.5rem;
            border: none;
            overflow: hidden;
        }

        .detail-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .detail-header h5 {
            margin: 0;
            font-weight: 700;
            color: var(--custom-maroon);
            font-size: 1.1rem;
        }

        .detail-body {
            padding: 1.5rem;
        }

        /* --- Typography --- */
        .label-text {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.25rem;
        }

        .value-text {
            font-size: 1rem;
            color: var(--text-dark);
            font-weight: 500;
            line-height: 1.5;
        }

        .main-title {
            font-size: 1.4rem;
            font-weight: 800;
            color: var(--text-dark);
            line-height: 1.3;
        }

        /* --- File Cards --- */
        .file-card {
            display: flex;
            align-items: center;
            padding: 1rem;
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 12px;
            margin-bottom: 1rem;
            transition: var(--transition);
            text-decoration: none;
        }

        .file-card:hover {
            background-color: var(--custom-maroon-subtle);
            border-color: var(--custom-maroon-light);
            transform: translateY(-2px);
        }

        .file-icon {
            width: 40px;
            height: 40px;
            background-color: #ffebee;
            color: #d32f2f;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            margin-right: 1rem;
        }

        .file-info {
            flex: 1;
            overflow: hidden;
        }

        .file-name {
            font-weight: 600;
            color: var(--text-dark);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            font-size: 0.9rem;
        }

        .file-action {
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        /* --- Badges --- */
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .bg-pending { background-color: #fff7ed; color: #c2410c; }
        .bg-approved { background-color: #ecfdf5; color: #047857; }
        .bg-rejected { background-color: #fef2f2; color: #b91c1c; }
        .bg-active { background-color: #eff6ff; color: #1d4ed8; }

        /* --- Buttons --- */
        .btn-edit-custom {
            background-color: var(--custom-maroon);
            color: white;
            padding: 0.6rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            border: none;
            transition: var(--transition);
            text-decoration: none;
            display: inline-flex; align-items: center; gap: 0.5rem;
        }
        .btn-edit-custom:hover { background-color: var(--custom-maroon-light); color: white; transform: translateY(-2px); }

        .btn-back-custom {
            background-color: #fff;
            color: var(--text-dark);
            border: 1px solid #e2e8f0;
            padding: 0.6rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            transition: var(--transition);
        }
        .btn-back-custom:hover { background-color: #f8f9fa; border-color: #cbd5e1; color: var(--text-dark); }

        /* --- Animation --- */
        .animate-up {
            animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
            opacity: 0; transform: translateY(20px);
        }
        @keyframes fadeInUp { to { opacity: 1; transform: translateY(0); } }
    </style>

    {{-- Header --}}
    <div class="page-header-wrapper animate-up">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge rounded-pill text-uppercase
                    {{ $praPenelitian->status == 'Pending' ? 'bg-pending' :
                      ($praPenelitian->status == 'Approved' ? 'bg-approved' :
                      ($praPenelitian->status == 'Rejected' ? 'bg-rejected' : 'bg-active')) }}">
                    {{ $praPenelitian->status }}
                </span>
                <span class="text-muted small">| {{ $praPenelitian->jenis_penelitian }}</span>
            </div>
            <h2 class="main-title mb-0">{{ $praPenelitian->judul }}</h2>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('pra-penelitian.index') }}" class="btn-back-custom">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
            {{-- Tampilkan tombol edit hanya jika status masih Pending atau user adalah admin --}}
            @if($praPenelitian->status == 'Pending' || auth()->user()->role == 'admin')
                <a href="{{ route('pra-penelitian.edit', $praPenelitian->id) }}" class="btn-edit-custom">
                    <i class="bi bi-pencil-square"></i> Edit
                </a>
            @endif
        </div>
    </div>

    <div class="row animate-up" style="animation-delay: 0.1s;">

        {{-- KOLOM KIRI: Informasi Utama & Dosen --}}
        <div class="col-lg-8">

            {{-- 1. Detail Informasi --}}
            <div class="detail-card">
                <div class="detail-header">
                    <i class="bi bi-info-circle-fill fs-5"></i>
                    <h5>Informasi Penelitian</h5>
                </div>
                <div class="detail-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="label-text">Universitas</div>
                            <div class="value-text fw-bold">
                                <i class="bi bi-building me-1 text-muted"></i>
                                {{ $praPenelitian->mou ? ($praPenelitian->mou->nama_instansi ?? $praPenelitian->mou->nama_universitas) : '-' }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="label-text">Program Studi</div>
                            <div class="value-text">{{ $praPenelitian->prodi }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. Data Dosen Pembimbing --}}
            <div class="detail-card">
                <div class="detail-header">
                    <i class="bi bi-person-badge-fill fs-5"></i>
                    <h5>Dosen Pembimbing</h5>
                </div>
                <div class="detail-body">
                    <div class="row g-4">
                        {{-- Pembimbing 1 --}}
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 border">
                                <div class="label-text mb-2 text-primary">Pembimbing 1</div>
                                <div class="value-text fw-bold mb-1">{{ $praPenelitian->dosen1_nama }}</div>
                                <div class="value-text small text-muted">
                                    <i class="bi bi-whatsapp me-1"></i> {{ $praPenelitian->dosen1_hp }}
                                </div>
                            </div>
                        </div>
                        {{-- Pembimbing 2 --}}
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 border">
                                <div class="label-text mb-2 text-primary">Pembimbing 2</div>
                                <div class="value-text fw-bold mb-1">{{ $praPenelitian->dosen2_nama }}</div>
                                <div class="value-text small text-muted">
                                    <i class="bi bi-whatsapp me-1"></i> {{ $praPenelitian->dosen2_hp }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. Data Mahasiswa --}}
            <div class="detail-card">
                <div class="detail-header d-flex justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-people-fill fs-5"></i>
                        <h5>Anggota Tim</h5>
                    </div>
                    <span class="badge bg-secondary rounded-pill">{{ $praPenelitian->anggotas->count() }} Orang</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Nama Mahasiswa</th>
                                <th>Jenjang</th>
                                <th>Kontak (WA)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($praPenelitian->anggotas as $mhs)
                            <tr>
                                <td class="ps-4 fw-bold text-dark">{{ $mhs->nama }}</td>
                                <td><span class="badge bg-light text-dark border">{{ $mhs->jenjang }}</span></td>
                                <td>
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $mhs->no_telpon) }}" target="_blank" class="text-decoration-none text-success fw-medium">
                                        <i class="bi bi-whatsapp me-1"></i> {{ $mhs->no_telpon }}
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        {{-- KOLOM KANAN: Timeline & File --}}
        <div class="col-lg-4">

            {{-- 4. Timeline --}}
            <div class="detail-card">
                <div class="detail-header">
                    <i class="bi bi-calendar-range-fill fs-5"></i>
                    <h5>Timeline</h5>
                </div>
                <div class="detail-body">
                    <div class="mb-4">
                        <div class="label-text">Mulai Penelitian</div>
                        <div class="value-text fs-5 fw-bold text-dark">
                            {{ $praPenelitian->tanggal_mulai->format('d F Y') }}
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="label-text">Rencana Skripsi</div>
                        <div class="value-text text-dark">
                            {{ $praPenelitian->tanggal_rencana_skripsi->format('d F Y') }}
                        </div>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">Diajukan pada:</small>
                        <small class="fw-bold">{{ $praPenelitian->created_at->format('d M Y') }}</small>
                    </div>
                </div>
            </div>

            {{-- 5. Berkas Lampiran --}}
            <div class="detail-card">
                <div class="detail-header">
                    <i class="bi bi-paperclip fs-5"></i>
                    <h5>Berkas Lampiran</h5>
                </div>
                <div class="detail-body">
                    {{-- File Kerangka --}}
                    @if($praPenelitian->file_kerangka)
                    <a href="{{ asset($praPenelitian->file_kerangka) }}" target="_blank" class="file-card">
                        <div class="file-icon">
                            <i class="bi bi-file-earmark-pdf-fill"></i>
                        </div>
                        <div class="file-info">
                            <div class="file-name">Kerangka Penelitian</div>
                            <div class="file-action">Klik untuk melihat</div>
                        </div>
                        <i class="bi bi-box-arrow-up-right text-muted"></i>
                    </a>
                    @else
                        <div class="text-muted small mb-3 fst-italic">Tidak ada kerangka penelitian.</div>
                    @endif

                    {{-- File Surat --}}
                    @if($praPenelitian->file_surat_pengantar)
                    <a href="{{ asset($praPenelitian->file_surat_pengantar) }}" target="_blank" class="file-card mb-0">
                        <div class="file-icon">
                            <i class="bi bi-envelope-paper-fill"></i>
                        </div>
                        <div class="file-info">
                            <div class="file-name">Surat Pengantar</div>
                            <div class="file-action">Klik untuk melihat</div>
                        </div>
                        <i class="bi bi-box-arrow-up-right text-muted"></i>
                    </a>
                    @else
                        <div class="text-muted small fst-italic">Tidak ada surat pengantar.</div>
                    @endif
                </div>
            </div>

            {{-- Tombol Aksi Tambahan (Admin Only - Optional) --}}
            @if(auth()->user()->role === 'admin' && $praPenelitian->status === 'Pending')
            <div class="d-grid gap-2">
                {{-- Jika Anda punya route untuk approve/reject di controller khusus --}}
                {{--
                <form action="{{ route('pengajuan.approve', $praPenelitian->id) }}" method="POST">
                    @csrf
                    <button class="btn btn-success w-100 fw-bold py-2">Setujui Pengajuan</button>
                </form>
                --}}
            </div>
            @endif

        </div>
    </div>

@endsection
