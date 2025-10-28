@extends('layouts.app')

@section('title', 'Mahasiswa')
@section('page-title', 'Data Mahasiswa')

@section('content')
    <style>
        :root {
            --maroon: #7c1316;
            --maroon-light: #9d2a2e;
            --text-dark: #222;
            --text-muted: #6c757d;
            --bg-light: #f8f9fa;
        }

        .btn-maroon {
            background-color: var(--maroon);
            border: none;
            color: #fff;
            font-weight: 600;
            border-radius: 10px;
            padding: 8px 14px;
            transition: all 0.2s ease;
        }

        .btn-maroon:hover {
            background-color: var(--maroon-light);
            transform: translateY(-1px);
        }

        .table {
            border-radius: 12px;
            overflow: hidden;
        }

        thead {
            background-color: var(--maroon);
            color: #fff;
        }

        th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }

        td {
            vertical-align: middle !important;
        }

        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .alert {
            border-radius: 10px;
            font-weight: 500;
        }

        .btn-sm {
            border-radius: 8px;
        }

        @media (max-width: 768px) {

            th,
            td {
                font-size: 0.85rem;
            }
        }
    </style>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold text-maroon" style="color: var(--maroon);">Daftar Mahasiswa</h4>
        <a href="{{ route('mahasiswa.create') }}" class="btn-maroon">
            <i class="bi bi-person-plus"></i> Tambah Mahasiswa
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr class="text-center">
                            <th>#</th>
                            <th>Nama</th>
                            <th>Universitas</th>
                            <th>Prodi</th>
                            <th>Ruangan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($mahasiswas as $m)
                            <tr class="text-center">
                                <td>{{ $loop->iteration }}</td>
                                <td class="text-start">{{ $m->nm_mahasiswa }}</td>
                                <td>{{ $m->univ_asal }}</td>
                                <td>{{ $m->prodi }}</td>
                                <td>{{ $m->ruangan ? $m->ruangan->nm_ruangan : $m->nm_ruangan }}</td>
                                <td>
                                    @if ($m->status == 'aktif')
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary">Nonaktif</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('mahasiswa.show', $m->id) }}" class="btn btn-sm btn-info text-white">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('mahasiswa.edit', $m->id) }}"
                                        class="btn btn-sm btn-warning text-white">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="{{ route('mahasiswa.destroy', $m->id) }}" method="POST"
                                        style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger"
                                            onclick="return confirm('Yakin ingin menghapus mahasiswa ini?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-3">Belum ada data mahasiswa.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
