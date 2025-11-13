@extends('layouts.app')

@section('title', 'Riwayat Absensi')
@section('page-title', 'Riwayat Absensi')

@section('content')
    <style>
        :root {
            --custom-maroon: #7c1316;
            --custom-maroon-light: #a3191d;
            --custom-maroon-subtle: #fcf0f1;
            --text-dark: #2c3e50;
            --text-muted: #95a5a6;
            --card-radius: 16px;
            --shadow-soft: 0 4px 20px rgba(0, 0, 0, 0.05);
            --transition: 0.3s ease;
        }

        /* --- Header & Filter --- */
        .page-header-wrapper {
            background: #fff;
            border-radius: var(--card-radius);
            padding: 1.5rem;
            box-shadow: var(--shadow-soft);
            margin-bottom: 2rem;
            border-left: 5px solid var(--custom-maroon);
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
        }

        .filter-card {
            background: #fff;
            border-radius: var(--card-radius);
            box-shadow: var(--shadow-soft);
            margin-bottom: 1.5rem;
            border: 1px solid #f0f0f0;
            overflow: hidden;
        }

        .filter-header {
            background: var(--custom-maroon-subtle);
            color: var(--custom-maroon);
            padding: 1rem 1.5rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* --- Form Elements --- */
        .form-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            margin-bottom: 0.4rem;
        }

        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            padding: 0.6rem 1rem;
            font-size: 0.95rem;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--custom-maroon-light);
            box-shadow: 0 0 0 2px rgba(124, 19, 22, 0.1);
        }

        /* --- Buttons --- */
        .btn-maroon {
            background-color: var(--custom-maroon);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 0.6rem 1.5rem;
            font-weight: 500;
            transition: var(--transition);
        }
        .btn-maroon:hover {
            background-color: var(--custom-maroon-light);
            transform: translateY(-2px);
            color: white;
        }

        .btn-outline-custom {
            border: 1px solid #e2e8f0;
            color: var(--text-dark);
            background: white;
            border-radius: 8px;
            padding: 0.6rem 1.2rem;
            font-weight: 500;
            transition: var(--transition);
        }
        .btn-outline-custom:hover {
            background: #f8f9fa;
            border-color: #cbd5e1;
        }

        /* --- Table Styling --- */
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
            font-size: 0.8rem;
            letter-spacing: 0.5px;
        }

        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
            color: #475569;
            border-bottom: 1px solid #f1f5f9;
        }

        .table-hover tbody tr:hover {
            background-color: #fff5f6;
        }

        /* --- Badges --- */
        .badge-pill-soft {
            border-radius: 50px;
            padding: 6px 14px;
            font-weight: 600;
            font-size: 0.75rem;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }
        .bg-soft-success { background-color: #dcfce7; color: #166534; }
        .bg-soft-secondary { background-color: #f1f5f9; color: #475569; }

        /* Animation */
        .animate-up {
            animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
            opacity: 0; transform: translateY(20px);
        }
        @keyframes fadeInUp { to { opacity: 1; transform: translateY(0); } }
    </style>

    <div class="page-header-wrapper animate-up">
        <div>
            <h4 class="fw-bold mb-1" style="color: var(--custom-maroon);">Riwayat Absensi</h4>
            <small class="text-muted">Pantau aktivitas masuk dan keluar mahasiswa.</small>
        </div>
        
        <div>
            <button onclick="exportAbsensi()" class="btn btn-outline-custom shadow-sm d-flex align-items-center gap-2">
                <i class="bi bi-file-earmark-excel text-success"></i> Export Data
            </button>
        </div>
    </div>

    <div class="filter-card animate-up" style="animation-delay: 0.1s;">
        <div class="filter-header">
            <i class="bi bi-funnel-fill"></i> Filter Data
        </div>
        <div class="card-body p-4">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Ruangan</label>
                    <select name="ruangan_id" class="form-select">
                        <option value="">Semua Ruangan</option>
                        @foreach ($ruangans as $r)
                            <option value="{{ $r->id }}" {{ request('ruangan_id') == $r->id ? 'selected' : '' }}>
                                {{ $r->nm_ruangan }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Tipe Absen</label>
                    <select name="type" class="form-select">
                        <option value="">Semua</option>
                        <option value="masuk" {{ request('type') == 'masuk' ? 'selected' : '' }}>Masuk</option>
                        <option value="keluar" {{ request('type') == 'keluar' ? 'selected' : '' }}>Keluar</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Tanggal Mulai</label>
                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                </div>

                <div class="col-md-2">
                    <label class="form-label">Tanggal Akhir</label>
                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                </div>

                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-maroon w-100 shadow-sm">
                        <i class="bi bi-search me-1"></i> Filter
                    </button>
                    <a href="{{ route('absensi.index') }}" class="btn btn-outline-custom shadow-sm" title="Reset">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="custom-table-card animate-up" style="animation-delay: 0.2s;">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th class="text-center" width="5%">No</th>
                        <th>Waktu & Tanggal</th>
                        <th>Nama Mahasiswa</th>
                        <th>Ruangan</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($absensi as $a)
                        <tr>
                            <td class="text-center text-muted fw-bold">
                                {{ $loop->iteration + ($absensi->currentPage() - 1) * $absensi->perPage() }}
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold text-dark">{{ $a->created_at->format('H:i') }} WIB</span>
                                    <small class="text-muted">{{ $a->created_at->format('d M Y') }}</small>
                                </div>
                            </td>
                            <td>
                                <span class="fw-bold text-dark">{{ $a->mahasiswa->nm_mahasiswa ?? '-' }}</span>
                            </td>
                            <td>
                                @if($a->mahasiswa && $a->mahasiswa->ruangan)
                                    <span class="badge bg-light text-dark border px-2 py-1 fw-normal">
                                        <i class="bi bi-geo-alt me-1"></i>{{ $a->mahasiswa->ruangan->nm_ruangan }}
                                    </span>
                                @else
                                    <span class="text-muted fst-italic">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if ($a->type === 'masuk')
                                    <span class="badge badge-pill-soft bg-soft-success">
                                        <i class="bi bi-box-arrow-in-right"></i> Masuk
                                    </span>
                                @else
                                    <span class="badge badge-pill-soft bg-soft-secondary">
                                        <i class="bi bi-box-arrow-left"></i> Keluar
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="bi bi-calendar-x display-4 text-muted mb-3 opacity-50"></i>
                                    <h5 class="text-muted fw-bold">Belum ada riwayat absensi</h5>
                                    <p class="text-muted small">Data absensi akan muncul di sini setelah mahasiswa melakukan scan.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="d-flex justify-content-center mt-4 animate-up" style="animation-delay: 0.3s;">
        {{ $absensi->links('pagination.custom') }}
    </div>

@endsection

@section('scripts')
    <script src="https://cdn.sheetjs.com/xlsx-0.20.3/package/dist/xlsx.full.min.js"></script>
    <script>
        function exportAbsensi() {
            // Logic export sederhana mengambil data dari tabel HTML saat ini (Client Side)
            // Atau bisa fetch ulang semua data jika ingin export full
            const table = document.querySelector("table");
            const wb = XLSX.utils.table_to_book(table, {sheet: "Riwayat Absensi"});
            XLSX.writeFile(wb, `Riwayat_Absensi_${new Date().toISOString().split('T')[0]}.xlsx`);
        }
    </script>
@endsection