@extends('layouts.app')

@section('title', 'Jadwal Penghuni')
@section('page-title', 'Jadwal Penghuni (Aktual)')

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
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            vertical-align: middle;
            white-space: nowrap;
        }

        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
            color: #475569;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.9rem;
        }

        .table-hover tbody tr:hover {
            background-color: #fff5f6;
        }

        /* Highlight Row untuk Status Aktif */
        .row-active {
            background-color: #f0fdf4; /* Hijau sangat muda */
        }
        .row-active:hover {
            background-color: #dcfce7 !important;
        }

        /* --- Badges --- */
        .badge-soft {
            padding: 5px 12px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.75rem;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .bg-soft-success { background-color: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .bg-soft-secondary { background-color: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
        .bg-soft-warning { background-color: #fef9c3; color: #854d0e; border: 1px solid #fde047; }
        .bg-soft-primary { background-color: #e0f2fe; color: #0284c7; border: 1px solid #bae6fd; }

        /* --- Buttons --- */
        .btn-system-info {
            background: #f3f4f6; color: #374151; border: 1px solid #d1d5db;
            padding: 0.5rem 1rem; border-radius: 50px; font-size: 0.85rem; font-weight: 600;
            cursor: default; display: inline-flex; align-items: center; gap: 6px;
        }

        .action-btn {
            width: 32px; height: 32px;
            border-radius: 8px;
            display: inline-flex; align-items: center; justify-content: center;
            transition: var(--transition);
            border: none;
        }

        .btn-delete {
            background: #fee2e2; color: #dc2626;
        }
        .btn-delete:hover {
            background: #fecaca;
        }

        /* Animation */
        .animate-up {
            animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
            opacity: 0; transform: translateY(20px);
        }
        @keyframes fadeInUp { to { opacity: 1; transform: translateY(0); } }
    </style>

    {{-- Header Section --}}
    <div class="page-header-wrapper animate-up">
        <div>
            <h4 class="fw-bold mb-1" style="color: var(--custom-maroon);">Jadwal Penghuni (Aktual)</h4>
            <small class="text-muted">Monitoring mahasiswa yang sedang menempati ruangan saat ini.</small>
        </div>
        <div>
            <button class="btn-system-info shadow-sm">
                <i class="bi bi-robot"></i> Diatur Otomatis oleh Sistem
            </button>
        </div>
    </div>

    {{-- Alert Success --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show animate-up shadow-sm border-0 mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-check-circle-fill fs-4 me-3"></i>
                <div><strong>Berhasil!</strong> {{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Table Section --}}
    <div class="custom-table-card animate-up" style="animation-delay: 0.1s;">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th class="text-center" width="15%">Status</th>
                        <th>Mahasiswa</th>
                        <th>Ruangan Huni</th>
                        <th>Periode Huni</th>
                        <th class="text-center" width="10%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($schedules as $schedule)
                        @php
                            $today = \Carbon\Carbon::now()->startOfDay();
                            $start = \Carbon\Carbon::parse($schedule->start_date)->startOfDay();
                            $end = \Carbon\Carbon::parse($schedule->end_date)->endOfDay();
                            $isActive = $today->between($start, $end);
                        @endphp

                        <tr class="{{ $isActive ? 'row-active' : '' }}">
                            <td class="text-center">
                                @if($isActive)
                                    <span class="badge-soft bg-soft-success">
                                        <i class="bi bi-check-circle-fill"></i> AKTIF
                                    </span>
                                @elseif($today->gt($end))
                                    <span class="badge-soft bg-soft-secondary">
                                        <i class="bi bi-archive-fill"></i> SELESAI
                                    </span>
                                @else
                                    <span class="badge-soft bg-soft-warning">
                                        <i class="bi bi-hourglass-split"></i> AKAN DATANG
                                    </span>
                                @endif
                            </td>
                            
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-light rounded-circle p-2 me-3 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                        <i class="bi bi-person text-secondary"></i>
                                    </div>
                                    <span class="fw-bold text-dark">
                                        {{ $schedule->mahasiswa->name ?? $schedule->mahasiswa->nm_mahasiswa ?? '-' }}
                                    </span>
                                </div>
                            </td>

                            <td>
                                <span class="badge-soft bg-soft-primary">
                                    <i class="bi bi-door-open me-1"></i>
                                    {{ $schedule->ruangan->nama_ruangan ?? $schedule->ruangan->nm_ruangan ?? '-' }}
                                </span>
                            </td>

                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold text-dark">
                                        <i class="bi bi-calendar-range me-2 text-muted"></i>
                                        {{ \Carbon\Carbon::parse($schedule->start_date)->format('d M Y') }}
                                    </span>
                                    <span class="small text-muted ms-4">
                                        s/d {{ \Carbon\Carbon::parse($schedule->end_date)->format('d M Y') }}
                                    </span>
                                </div>
                            </td>

                            <td class="text-center">
                                <form action="{{ route('room_schedules.destroy', $schedule->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn btn-delete" title="Hapus Manual">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="bi bi-robot display-4 text-muted mb-3 opacity-50"></i>
                                    <h5 class="text-muted fw-bold">Belum ada data jadwal aktif</h5>
                                    <p class="text-muted small">Silakan jalankan command robot scheduler atau tambahkan jadwal rolling.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection