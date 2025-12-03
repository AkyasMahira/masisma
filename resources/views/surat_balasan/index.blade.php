@extends('layouts.app')

@section('title', 'Daftar Surat Balasan')
@section('page-title', 'Data Surat Balasan')

@section('content')
    <style>
        /* --- Style Seragam --- */
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
        }

        .filter-card {
            background: #fff;
            border-radius: var(--card-radius);
            box-shadow: var(--shadow-soft);
            margin-bottom: 1.5rem;
            border: 1px solid #f0f0f0;
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

        .custom-table-card {
            background: #fff;
            border-radius: var(--card-radius);
            box-shadow: var(--shadow-soft);
            overflow: hidden;
            border: none;
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

        .action-btn {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
            transition: var(--transition);
            background: transparent;
            border: 1px solid transparent;
            margin: 0 2px;
        }

        .action-btn:hover {
            background: var(--custom-maroon-subtle);
            color: var(--custom-maroon);
        }

        .action-btn.edit:hover {
            background: #e0f2fe;
            color: #0284c7;
        }

        .action-btn.pdf:hover {
            background: #fef3c7;
            color: #d97706;
        }

        .action-btn.delete:hover {
            background: #fee2e2;
            color: #dc2626;
        }

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

    {{-- Header --}}
    <div class="page-header-wrapper d-flex flex-wrap justify-content-between align-items-center gap-3 animate-up">
        <div>
            <h4 class="fw-bold mb-1" style="color: var(--custom-maroon);">Daftar Surat Balasan</h4>
            <small class="text-muted">Kelola surat balasan untuk mahasiswa magang/penelitian.</small>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('surat-balasan.create') }}" class="btn btn-maroon shadow-sm d-flex align-items-center gap-2">
                <i class="bi bi-plus-lg"></i> Tambah Surat
            </a>
        </div>
    </div>

    {{-- Filter --}}
    <div class="filter-card animate-up" style="animation-delay: 0.1s;">
        <div class="filter-header">
            <i class="bi bi-funnel-fill mr-2"></i> Pencarian
        </div>
        <div class="card-body p-4">
            <form method="GET" action="{{ route('surat-balasan.index') }}">
                <div class="row">
                    <div class="col-md-9 mb-3 mb-md-0">
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-light border-right-0">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" class="form-control bg-light border-left-0" name="search"
                                placeholder="Cari Nama Mahasiswa / NIM..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-maroon shadow-sm w-100">Cari</button>
                        <a href="{{ route('surat-balasan.index') }}" class="btn btn-light border shadow-sm" title="Reset">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- TABEL LENGKAP --}}
    <div class="custom-table-card animate-up" style="animation-delay: 0.2s;">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th class="text-center" width="5%">No</th>
                        <th>Info Mahasiswa</th>
                        <th>Universitas</th>
                        <th>Rentang Waktu</th>
                        <th>Keperluan</th>
                        <th>Data Dibutuhkan</th>
                        <th class="text-center" width="15%">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($data as $row)
                        <tr>
                            <td class="text-center fw-bold text-muted">
                                {{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}
                            </td>

                            <td>
                                <div class="fw-bold text-dark">{{ $row->nama_mahasiswa }}</div>
                                <div class="small text-muted">NIM: {{ $row->nim }}</div>
                                <div class="small text-success">
                                    <i class="bi bi-whatsapp"></i> {{ $row->wa_mahasiswa }}
                                </div>
                                <div class="small text-muted">Prodi: {{ $row->prodi }}</div>
                            </td>

                            <td>
                                <span class="badge bg-light text-dark border">
                                    {{ $row->mou ? ($row->mou->nama_instansi ?? $row->mou->nama_universitas) : '-' }}
                                </span>
                            </td>

                            <td class="fw-semibold">{{ $row->lama_berlaku }}</td>

                            <td>{{ Str::limit($row->keperluan, 40) }}</td>

                            <td>{{ Str::limit($row->data_dibutuhkan, 40) }}</td>

                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="{{ route('surat-balasan.pdf', $row->id) }}" target="_blank"
                                        class="action-btn pdf" title="Download PDF">
                                        <i class="bi bi-file-earmark-pdf-fill"></i>
                                    </a>

                                    <a href="{{ route('surat-balasan.edit', $row->id) }}"
                                        class="action-btn edit" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>

                                    <form action="{{ route('surat-balasan.destroy', $row->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="action-btn delete btn-delete" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="bi bi-inbox-fill display-4 text-muted mb-3" style="opacity: 0.5;"></i>
                                    <h5 class="text-muted fw-bold">Tidak ada data surat</h5>
                                    <p class="text-muted small">Silakan tambahkan surat balasan baru.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>
    </div>

    {{-- PAGINASI --}}
    <div class="d-flex justify-content-center mt-4 animate-up" style="animation-delay: 0.3s;">
        {{ $data->links() }}
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // Tooltip
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

            // SweetAlert Delete
            const deleteButtons = document.querySelectorAll('.btn-delete');
            deleteButtons.forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const form = this.closest('form');

                    Swal.fire({
                        title: 'Hapus Surat?',
                        text: "Data surat ini akan dihapus permanen.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc2626',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });

            // Flash Message
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: "{{ session('success') }}",
                    showConfirmButton: false,
                    timer: 1500,
                    toast: true,
                    position: 'top-end'
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: "{{ session('error') }}",
                    toast: true,
                    position: 'top-end'
                });
            @endif
        });
    </script>
@endsection
