@extends('layouts.public')

@section('title', 'List MOU')

@section('content')
    <style>
        :root {
            --custom-maroon: #7c1316;
            --custom-maroon-light: #a3191d;
            --custom-maroon-subtle: #fcf0f1;
            --text-dark: #2c3e50;
            --text-muted: #95a5a6;
            --card-radius: 12px;
            --shadow-soft: 0 4px 20px rgba(0, 0, 0, 0.05);
            --transition: 0.3s ease;
        }

        .page-header-wrapper {
            background: #fff;
            border-radius: var(--card-radius);
            padding: 1.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            margin-bottom: 2rem;
            border-left: 5px solid var(--custom-maroon);
            position: relative;
            z-index: 1050;
            overflow: visible;
        }

        .filter-card {
            background: #fff;
            border-radius: var(--card-radius);
            box-shadow: var(--shadow-soft);
            margin-bottom: 1.5rem;
            border: 1px solid #f0f0f0;
            position: relative;
            z-index: 50;
        }

        .filter-header {
            background: var(--custom-maroon-subtle);
            color: var(--custom-maroon);
            padding: 1rem 1.5rem;
            border-radius: var(--card-radius) var(--card-radius) 0 0;
            font-weight: 600;
        }

        .btn-maroon {
            background-color: var(--custom-maroon);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 8px 16px;
            font-weight: 500;
            transition: var(--transition);
        }

        .btn-maroon:hover {
            background-color: var(--custom-maroon-light);
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(124, 19, 22, 0.3);
        }

        /* Hapus atau nonaktifkan style untuk btn-tool dan dropdown admin */

        .custom-table-card {
            background: #fff;
            border-radius: var(--card-radius);
            box-shadow: var(--shadow-soft);
            overflow: hidden;
            border: none;
        }

        .table {
            margin-bottom: 0;
            border-collapse: collapse;
        }

        .table thead th {
            background-color: var(--custom-maroon);
            color: white;
            border: none;
            padding: 1rem;
            font-weight: 500;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            white-space: nowrap;
        }

        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
            color: #555;
            border-bottom: 1px solid #f0f0f0;
        }

        .table-hover tbody tr:hover {
            background-color: #fff5f6;
        }
        
        /* Hapus style untuk action-btn karena tidak ada tombol aksi */

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

    {{-- 1. HEADER HALAMAN --}}
    <div class="page-header-wrapper d-flex flex-wrap justify-content-between align-items-center gap-3 animate-up">
        <div>
            <h4 class="fw-bold mb-1" style="color: var(--custom-maroon);">Daftar Memorandum of Understanding (MOU)</h4>
            <small class="text-muted">Daftar instansi yang memiliki kerja sama dengan lembaga.</small>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            {{-- Tombol Tambah Baru (Diubah ke rute public create) --}}
            <a href="{{ route('public.mou.create') }}" class="btn btn-maroon shadow-sm d-flex align-items-center gap-2">
                <i class="bi bi-file-earmark-plus"></i> Ajukan MOU Baru
            </a>
        </div>
    </div>

    {{-- 2. FILTER CARD --}}
    <div class="filter-card animate-up" style="animation-delay: 0.1s;">
        <div class="filter-header">
            <i class="bi bi-funnel-fill me-2"></i> Filter & Pencarian
        </div>
        <div class="card-body p-4">
            {{-- Route action diarahkan ke rute public index --}}
            <form id="filterForm" method="GET" action="{{ route('mou.public_index') }}">
                <div class="row">
                    <div class="col-md-4 mb-3 mb-md-0">
                        <label class="small text-muted fw-bold text-uppercase">Cari Nama Instansi</label>
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-light border-0 border-end-0"><i
                                    class="bi bi-search"></i></span>
                            <input type="text" class="form-control bg-light border-0" name="search"
                                placeholder="Nama instansi..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-4 mb-3 mb-md-0">
                        <label class="small text-muted fw-bold text-uppercase">Dari Tanggal</label>
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-light border-0 border-end-0"><i
                                    class="bi bi-calendar-plus"></i></span>
                            <input type="date" class="form-control bg-light border-0" name="tanggal_mulai"
                                value="{{ request('tanggal_mulai') }}">
                        </div>
                    </div>
                    <div class="col-md-4 mb-3 mb-md-0">
                        <label class="small text-muted fw-bold text-uppercase">Sampai Tanggal</label>
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-light border-0 border-end-0"><i
                                    class="bi bi-calendar-check"></i></span>
                            <input type="date" class="form-control bg-light border-0" name="tanggal_selesai"
                                value="{{ request('tanggal_selesai') }}">
                        </div>
                    </div>
                    <div class="col-md-12 d-flex gap-2 mt-3">
                        <button type="submit" class="btn btn-maroon shadow-sm">Terapkan</button>
                        {{-- Link reset diarahkan ke rute public index --}}
                        <a href="{{ route('mou.public_index') }}" class="btn btn-light border shadow-sm" title="Reset Filter">
                            <i class="bi bi-arrow-counterclockwise"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- 3. TABEL DATA --}}
    <div class="custom-table-card animate-up" style="animation-delay: 0.2s;">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th class="text-center" width="5%">No</th>
                        <th>Instansi</th>
                        <th style="min-width: 180px;">Nomor PKS</th>
                        <th>Jenis</th>
                        <th>Durasi</th>
                        <th>Dokumen</th>
                        <th style="min-width: 200px;">Rencana Kerja Sama</th>
                        {{-- Hapus kolom Aksi --}}
                    </tr>
                </thead>
                <tbody>
                    @forelse ($mous as $index => $mou)
                        <tr>
                            <td class="text-center text-muted fw-bold">
                                {{ $mous->currentPage() * $mous->perPage() - $mous->perPage() + $index + 1 }}
                            </td>
                            <td>
                                <span class="fw-bold text-dark d-block">{{ $mou->nama_instansi }}</span>
                                @if ($mou->alamat_instansi)
                                    <small class="d-block text-muted mt-1">{{ $mou->alamat_instansi }}</small>
                                @endif
                                @if ($mou->nama_pic_instansi)
                                    <small class="d-block text-muted text-nowrap mt-1">
                                        <i class="bi bi-person-circle me-1"></i> {{ $mou->nama_pic_instansi }}
                                        @if ($mou->nomor_kontak_pic)
                                            <br><i class="bi bi-telephone me-1"></i> {{ $mou->nomor_kontak_pic }}
                                        @endif
                                    </small>
                                @endif
                            </td>

                            {{-- KOLOM NOMOR PKS --}}
                            <td class="align-top">
                                @if ($mou->no_pks)
                                    <div class="mb-2">
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill mb-1" style="font-size: 0.65rem;">INTERNAL</span>
                                        <div class="fw-bold text-dark" style="font-size: 0.85rem;">
                                            {{ $mou->no_pks }}
                                        </div>
                                    </div>
                                @endif
                                @if ($mou->no_pks_instansi)
                                    <div class="mb-1">
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill mb-1" style="font-size: 0.65rem;">INSTANSI</span>
                                        <div class="text-muted" style="font-size: 0.85rem;">
                                            {{ $mou->no_pks_instansi }}
                                        </div>
                                    </div>
                                @endif
                                @if (!$mou->no_pks && !$mou->no_pks_instansi)
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>

                            <td>
                                <span class="small text-dark d-block text-nowrap">{{ $mou->jenis_instansi ?? '-' }}</span>
                                @if ($mou->jenis_instansi_lainnya && $mou->jenis_instansi === 'Lainnya')
                                    <small class="text-muted d-block text-nowrap">( {{ $mou->jenis_instansi_lainnya }} )</small>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex flex-column gap-1">
                                    <span class="badge bg-light text-dark border">
                                        {{ $mou->tanggal_masuk ? \Carbon\Carbon::parse($mou->tanggal_masuk)->format('d M Y') : '-' }}
                                    </span>
                                    <div class="text-center text-muted small"><i class="bi bi-arrow-down"></i></div>
                                    <span class="badge bg-light text-dark border">
                                        {{ $mou->tanggal_keluar ? \Carbon\Carbon::parse($mou->tanggal_keluar)->format('d M Y') : '-' }}
                                    </span>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-wrap gap-2" style="max-width: 120px;">
                                    {{-- Hanya tampilkan link, tanpa tombol edit/delete --}}
                                    @if ($mou->surat_permohonan)
                                        <a href="{{ asset('storage/' . $mou->surat_permohonan) }}" target="_blank"
                                            class="btn btn-sm btn-outline-dark rounded-pill px-2"
                                            title="Surat Permohonan">
                                            <i class="bi bi-file-earmark-text"></i>
                                        </a>
                                    @endif
                                    @if ($mou->sk_pengangkatan_pimpinan)
                                        <a href="{{ asset('storage/' . $mou->sk_pengangkatan_pimpinan) }}" target="_blank"
                                            class="btn btn-sm btn-outline-dark rounded-pill px-2"
                                            title="SK Pengangkatan">
                                            <i class="bi bi-journal-richtext"></i>
                                        </a>
                                    @endif
                                    @if ($mou->sertifikat_akreditasi_prodi)
                                        <a href="{{ asset('storage/' . $mou->sertifikat_akreditasi_prodi) }}" target="_blank"
                                            class="btn btn-sm btn-outline-dark rounded-pill px-2"
                                            title="Sertifikat Akreditasi">
                                            <i class="bi bi-award"></i>
                                        </a>
                                    @endif
                                    @if ($mou->draft_mou)
                                        <a href="{{ asset('storage/' . $mou->draft_mou) }}" target="_blank"
                                            class="btn btn-sm btn-outline-dark rounded-pill px-2" title="Draft MoU">
                                            <i class="bi bi-file-earmark-pdf"></i>
                                        </a>
                                    @endif
                                    @if (
                                        !$mou->surat_permohonan &&
                                        !$mou->sk_pengangkatan_pimpinan &&
                                        !$mou->sertifikat_akreditasi_prodi &&
                                        !$mou->draft_mou)
                                        <span class="text-muted small">-</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div style="max-width: 250px; white-space: normal;">
                                    {{ \Illuminate\Support\Str::limit($mou->rencana_kerja_sama ?? '', 80) }}
                                </div>
                            </td>
                            {{-- Hapus kolom aksi --}}
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="bi bi-folder-x display-4 text-muted mb-3" style="opacity: 0.5;"></i>
                                    <h5 class="text-muted fw-bold">Tidak ada data ditemukan</h5>
                                    <p class="text-muted small">
                                        @if (request()->has('search') || request()->has('tanggal_mulai'))
                                            Coba reset filter atau gunakan kata kunci lain.
                                        @else
                                            Belum ada data Memorandum of Understanding yang tercatat.
                                        @endif
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- 4. PAGINATION --}}
    <div class="d-flex justify-content-center mt-4 animate-up" style="animation-delay: 0.3s;">
        {{ $mous->links() }}
    </div>

    {{-- SWEETALERT (Dibiarkan, meskipun mungkin tidak terpakai di public view) --}}
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 1800,
                    toast: true,
                    position: 'top-end'
                });
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian!',
                    text: '{{ session('error') }}',
                    showConfirmButton: true
                });
            });
        </script>
    @endif
@endsection

@section('scripts')
    {{-- Hapus semua JS admin tools (Export, Import, Copy URL, Delete Confirmation) --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    {{-- Jika Toastify digunakan di public layout, biarkan. Jika tidak, hapus link CSS & JS nya --}}
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

    <script>
        function showToast(message, type = 'success') {
            const colors = {
                success: "#00b09b",
                error: "#ff5f6d",
                info: "#2193b0"
            };
            Toastify({
                text: message,
                duration: 3000,
                gravity: "bottom",
                position: "right",
                style: {
                    background: colors[type] || colors.info
                },
                className: "rounded shadow-lg"
            }).showToast();
        }
    </script>
@endsection