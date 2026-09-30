@extends('layouts.app')

@section('title', 'Data Surat Balasan')
@section('page-title', 'Data Surat Balasan')

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
            z-index: 99;
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
        }

        .action-btn:hover {
            background: var(--custom-maroon-subtle);
            color: var(--custom-maroon);
        }

        .action-btn.delete:hover {
            background: #fee2e2;
            color: #dc2626;
        }
        
        .action-btn.pdf:hover {
            background: #ffecec;
            color: #e74c3c;
        }

        .action-btn.edit:hover {
            background: #fff8e1;
            color: #f39c12;
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

    {{-- 1. Header Halaman --}}
    <div class="page-header-wrapper d-flex flex-wrap justify-content-between align-items-center gap-3 animate-up">
        <div>
            <h4 class="fw-bold mb-1" style="color: var(--custom-maroon);">Daftar Surat Balasan</h4>
            <small class="text-muted">Kelola data surat balasan mahasiswa.</small>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('surat-balasan.create') }}" class="btn btn-maroon shadow-sm d-flex align-items-center gap-2">
                <i class="fas fa-plus"></i> Buat Surat Baru
            </a>
        </div>
    </div>

    {{-- 2. Filter Card --}}
    <div class="filter-card animate-up" style="animation-delay: 0.1s;">
        <div class="filter-header">
            <i class="fas fa-filter me-2"></i> Filter & Pencarian
        </div>
        <div class="card-body p-4">
            <form action="{{ route('surat-balasan.index') }}" method="GET">
                <div class="row align-items-end">
                    <div class="col-md-9 mb-3 mb-md-0">
                        <label class="small text-muted font-weight-bold text-uppercase">Cari Nama / NIM / Prodi</label>
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control bg-light border-start-0" 
                                placeholder="Ketikan kata kunci pencarian..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button class="btn btn-maroon shadow-sm w-100" type="submit">Cari Data</button>
                        <a href="{{ route('surat-balasan.index') }}" class="btn btn-light border shadow-sm" title="Reset">
                            <i class="fas fa-sync-alt"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- 3. Tabel Kustom --}}
    <div class="custom-table-card animate-up" style="animation-delay: 0.2s;">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th class="text-center" width="5%">No</th>
                        <th>Mahasiswa</th>
                        <th>Instansi / MOU</th>
                        <th>Keperluan</th>
                        {{-- Kolom Baru --}}
                        <th width="15%">Data Dibutuhkan</th>
                        <th>Lama Berlaku</th>
                        <th class="text-center" width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $item)
                        <tr>
                            <td class="text-center text-muted font-weight-bold">{{ $loop->iteration + $data->firstItem() - 1 }}</td>
                            <td>
                                <div class="fw-bold text-dark">{{ $item->nama_mahasiswa }}</div>
                                <small class="text-muted" style="font-size: 0.8rem;">
                                    {{ $item->nim }} <span class="mx-1">•</span> {{ $item->prodi }}
                                </small>
                            </td>
                            <td>
                                {{ $item->mou->nama_instansi ?? $item->mou->nama_universitas ?? '-' }}
                            </td>
                            <td>{{ $item->keperluan }}</td>
                            
                            {{-- LOGIKA KOLOM DATA DIBUTUHKAN --}}
                            <td>
                                @php
                                    $listData = $item->data_dibutuhkan;
                                    // Decode jika masih berupa string JSON
                                    if (is_string($listData)) {
                                        $listData = json_decode($listData, true);
                                    }
                                @endphp

                                @if(!empty($listData) && is_array($listData))
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach($listData as $req)
                                            <span class="badge border" 
                                                  style="background-color: var(--custom-maroon-subtle); color: var(--custom-maroon); font-weight: 500;">
                                                {{ $req }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>

                            <td>
                                <span class="badge bg-light text-dark border">
                                    {{ $item->lama_berlaku }}
                                </span>
                            </td>
                            <td class="text-center">
                                {{-- Tombol PDF --}}
                                <a href="{{ route('surat-balasan.pdf', $item->id) }}" class="action-btn pdf" title="Download PDF" target="_blank">
                                    <i class="fas fa-file-pdf"></i>
                                </a>
                                {{-- Tombol Edit --}}
                                <a href="{{ route('surat-balasan.edit', $item->id) }}" class="action-btn edit" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                {{-- Tombol Hapus --}}
                                <form action="{{ route('surat-balasan.destroy', $item->id) }}" method="POST" class="d-inline delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="action-btn delete btn-delete" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            {{-- Colspan diubah jadi 7 karena ada kolom baru --}}
                            <td colspan="7" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="fas fa-folder-open fa-3x text-muted mb-3" style="opacity: 0.5;"></i>
                                    <h5 class="text-muted font-weight-bold">Data tidak ditemukan</h5>
                                    <p class="text-muted small">Silakan tambahkan data surat balasan baru.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- 4. Pagination --}}
    <div class="d-flex justify-content-center mt-4 animate-up" style="animation-delay: 0.3s;">
        {{ $data->links() }}
    </div>

@endsection

@section('scripts')
    {{-- SweetAlert Integration --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Notifikasi Sukses/Error dari Session --}}
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

    {{-- Script Konfirmasi Hapus --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const deleteButtons = document.querySelectorAll('.btn-delete');
            deleteButtons.forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const form = this.closest('form');

                    Swal.fire({
                        title: 'Yakin hapus data?',
                        text: "Data surat balasan ini akan dihapus permanen.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#7c1316',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
@endsection