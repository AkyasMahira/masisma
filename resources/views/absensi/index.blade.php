@extends('layouts.app')

@section('title', 'Riwayat Absensi')
@section('page-title', 'Riwayat Absensi')

@section('content')
    <style>
        :root {
            --maroon: #7c1316;
            --maroon-light: #9d2a2e;
            --text-dark: #222;
            --text-muted: #6c757d;
            --bg-light: #f8f9fa;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(15px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            animation: fadeInUp 0.5s ease-out forwards;
            opacity: 0;
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
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(124, 19, 22, 0.25);
        }

        .btn-outline-custom {
            border: 1px solid #ced4da;
            color: var(--text-dark);
            font-weight: 500;
            border-radius: 10px;
            background-color: #fff;
            transition: all 0.2s ease;
        }

        .btn-outline-custom:hover {
            background-color: var(--bg-light);
            border-color: #adb5bd;
            transform: translateY(-1px);
        }

        .table {
            border-radius: 12px;
            overflow: hidden;
            border-collapse: separate;
            border-spacing: 0;
        }

        .table thead {
            background-color: var(--maroon);
            color: #fff;
        }

        .table th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            padding-top: 1rem;
            padding-bottom: 1rem;
        }

        .table-hover tbody tr {
            transition: all 0.2s ease-out;
        }

        .table-hover tbody tr:hover {
            background-color: #fffafb;
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.07);
            position: relative;
            z-index: 10;
        }

        .filter-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 4px;
        }

        @media (max-width: 768px) {
            .filter-label {
                font-size: 0.8rem;
            }

            .btn-maroon,
            .btn-outline-custom {
                width: 100%;
                margin-top: 6px;
            }
        }
    </style>

    <div class="card">
        <div class="card-body">

            <form method="GET" class="row g-3 mb-3">
                <div class="col-md-3">
                    <label class="filter-label">Ruangan</label>
                    <select name="ruangan_id" class="form-control js-choices">
                        <option value="">Semua Ruangan</option>
                        @foreach ($ruangans as $r)
                            <option value="{{ $r->id }}" {{ request('ruangan_id') == $r->id ? 'selected' : '' }}>
                                {{ $r->nm_ruangan }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="filter-label">Tipe</label>
                    <select name="type" class="form-control js-choices">
                        <option value="">Semua</option>
                        <option value="masuk" {{ request('type') == 'masuk' ? 'selected' : '' }}>Masuk</option>
                        <option value="keluar" {{ request('type') == 'keluar' ? 'selected' : '' }}>Keluar</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="filter-label">Tanggal Mulai</label>
                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                </div>

                <div class="col-md-2">
                    <label class="filter-label">Tanggal Akhir</label>
                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                </div>

                <div class="col-md-3 d-flex align-items-end gap-2 flex-wrap">
                    <button class="btn-maroon">Filter</button>
                    <a href="{{ route('absensi.index') }}" class="btn btn-outline-custom">Reset</a>
                    <button type="button" onclick="exportAbsensi()" class="btn btn-outline-custom">Export</button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead>
                        <tr class="text-center">
                            <th>#</th>
                            <th>Waktu</th>
                            <th>Nama</th>
                            <th>Ruangan</th>
                            <th>Tipe</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($absensi as $a)
                            <tr class="text-center">
                                <td>{{ $loop->iteration + ($absensi->currentPage() - 1) * $absensi->perPage() }}</td>
                                <td>{{ $a->created_at->format('Y-m-d H:i:s') }}</td>
                                <td class="text-start">{{ $a->mahasiswa->nm_mahasiswa ?? '-' }}</td>
                                <td>{{ $a->mahasiswa->ruangan->nm_ruangan ?? ($a->mahasiswa->nm_ruangan ?? '-') }}</td>
                                <td>
                                    @if ($a->type === 'masuk')
                                        <span class="badge bg-success">Masuk</span>
                                    @else
                                        <span class="badge bg-secondary">Keluar</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">Tidak ada data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-3">
                {{ $absensi->links('pagination.custom') }}
            </div>
        </div>
    </div>
@endsection
