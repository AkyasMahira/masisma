@extends('layouts.app') 

@section('title', 'Jadwal Rolling')
@section('page-title', 'Pengaturan Jadwal Rolling')

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

        /* --- Table Card --- */
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
        .bg-soft-primary { background-color: #e0f2fe; color: #0284c7; border: 1px solid #bae6fd; }
        .bg-soft-info { background-color: #e0e7ff; color: #4338ca; border: 1px solid #c7d2fe; }

        /* --- Buttons --- */
        .btn-maroon {
            background-color: var(--custom-maroon); color: #fff; border: none;
            border-radius: 8px; padding: 0.6rem 1.5rem; font-weight: 600;
            transition: var(--transition);
            display: inline-flex; align-items: center; gap: 8px;
        }
        .btn-maroon:hover { background-color: var(--custom-maroon-light); color: white; transform: translateY(-2px); }

        .action-btn {
            width: 32px; height: 32px; border-radius: 8px;
            display: inline-flex; align-items: center; justify-content: center;
            transition: var(--transition); border: none;
        }
        .btn-edit { background: #fef3c7; color: #d97706; }
        .btn-edit:hover { background: #fde68a; }
        
        .btn-delete { background: #fee2e2; color: #dc2626; }
        .btn-delete:hover { background: #fecaca; }

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
            <h4 class="fw-bold mb-1" style="color: var(--custom-maroon);">Pengaturan Jadwal (Rolling)</h4>
            <small class="text-muted">Kelola perpindahan ruangan mahasiswa.</small>
        </div>
        <div>
            <a href="{{ route('room_sequences.create') }}" class="btn btn-maroon shadow-sm">
                <i class="bi bi-plus-lg"></i> Tambah Jadwal
            </a>
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
                        <th class="text-center" width="5%">No</th>
                        <th>Mahasiswa</th>
                        <th>Ruangan Tujuan</th>
                        <th>Periode (Mulai - Selesai)</th>
                        <th class="text-center">Durasi</th>
                        <th class="text-center" width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sequences as $seq)
                    <tr>
                        <td class="text-center text-muted fw-bold">{{ $loop->iteration }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="bg-light rounded-circle p-2 me-3 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                    <i class="bi bi-person text-secondary"></i>
                                </div>
                                <span class="fw-bold text-dark">{{ $seq->mahasiswa->name ?? $seq->mahasiswa->nm_mahasiswa ?? '-' }}</span>
                            </div>
                        </td>
                        
                        <td>
                            <span class="badge-soft bg-soft-primary">
                                <i class="bi bi-door-open me-1"></i>
                                {{ $seq->ruangan->nama_ruangan ?? $seq->ruangan->nm_ruangan ?? '-' }}
                            </span>
                        </td>

                        <td>
                            <div class="d-flex flex-column">
                                <span class="fw-bold text-dark">
                                    <i class="bi bi-calendar-range me-2 text-muted"></i>
                                    {{ \Carbon\Carbon::parse($seq->start_date)->format('d M Y') }}
                                </span>
                                <span class="small text-muted ms-4">
                                    s/d {{ \Carbon\Carbon::parse($seq->end_date)->format('d M Y') }}
                                </span>
                            </div>
                        </td>

                        <td class="text-center">
                            <span class="badge-soft bg-soft-info">
                                {{ \Carbon\Carbon::parse($seq->start_date)->diffInDays(\Carbon\Carbon::parse($seq->end_date)) }} Hari
                            </span>
                        </td>

                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ route('room_sequences.edit', $seq->id) }}" class="action-btn btn-edit" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('room_sequences.destroy', $seq->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus jadwal ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn btn-delete" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center">
                                <i class="bi bi-calendar-x display-4 text-muted mb-3 opacity-50"></i>
                                <h5 class="text-muted fw-bold">Belum ada jadwal rolling</h5>
                                <p class="text-muted small">Tambahkan jadwal baru untuk mengatur perpindahan ruangan.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection