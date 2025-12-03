@extends('layouts.app')

@section('title', 'Approval Pengajuan')
@section('page-title', 'Data Pengajuan')

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

        /* --- Status Badges (Main) --- */
        .badge-status {
            padding: 5px 10px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.75rem;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .status-pending { background-color: #fff7ed; color: #c2410c; border: 1px solid #ffedd5; }
        .status-approved { background-color: #f0fdf4; color: #15803d; border: 1px solid #dcfce7; }
        .status-rejected { background-color: #fef2f2; color: #b91c1c; border: 1px solid #fee2e2; }

        /* --- Secondary Badges (Surat & Bayar) --- */
        .badge-pill-soft {
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            white-space: nowrap;
        }
        .bg-soft-gray { background-color: #f1f5f9; color: #64748b; }
        .bg-soft-purple { background-color: #f3e8ff; color: #7e22ce; border: 1px solid #d8b4fe; } /* Sudah Kirim */
        .bg-soft-blue { background-color: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; } /* Uploaded */
        .bg-soft-yellow { background-color: #fef9c3; color: #854d0e; border: 1px solid #fde047; } /* Menunggu */
        .bg-soft-green { background-color: #dcfce7; color: #15803d; border: 1px solid #86efac; } /* Verified */

        /* --- Action Buttons --- */
        .btn-action {
            width: 32px; height: 32px;
            border-radius: 8px;
            display: inline-flex; align-items: center; justify-content: center;
            transition: var(--transition); border: none; color: white;
        }
        .btn-approve { background-color: #10b981; }
        .btn-approve:hover { background-color: #059669; transform: translateY(-2px); }
        .btn-reject { background-color: #ef4444; }
        .btn-reject:hover { background-color: #dc2626; transform: translateY(-2px); }

        .btn-detail {
            background-color: white; border: 1px solid #e2e8f0; color: var(--text-dark);
            padding: 5px 12px; border-radius: 6px; font-size: 0.8rem; font-weight: 500;
            text-decoration: none; transition: var(--transition); display: inline-flex; align-items: center; gap: 5px;
        }
        .btn-detail:hover { background-color: #f8f9fa; border-color: var(--custom-maroon); color: var(--custom-maroon); }

        /* Animation */
        .animate-up { animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards; opacity: 0; transform: translateY(20px); }
        @keyframes fadeInUp { to { opacity: 1; transform: translateY(0); } }
    </style>

    <!-- Header Section -->
    <div class="page-header-wrapper animate-up">
        <div>
            <h4 class="fw-bold mb-1" style="color: var(--custom-maroon);">Daftar Pengajuan</h4>
            <small class="text-muted">Kelola persetujuan pengajuan penelitian & magang.</small>
        </div>
    </div>

    <!-- Alert Messages -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show animate-up" role="alert" style="border-radius: 12px;">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show animate-up" role="alert" style="border-radius: 12px;">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Table Section -->
    <div class="custom-table-card animate-up" style="animation-delay: 0.1s;">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th class="text-center" width="5%">No</th>
                        <th>Pemohon</th>
                        <th>Asal Universitas</th>
                        <th>Jenis</th>
                        <th>Tanggal</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Surat Balasan</th>
                        <th class="text-center">Pembayaran</th>
                        <th class="text-center" width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $p)
                        <tr>
                            <td class="text-center text-muted fw-bold">{{ $loop->iteration }}</td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold text-dark">{{ $p->user->name ?? 'User Terhapus' }}</span>
                                    <small class="text-muted">{{ $p->user->email ?? '-' }}</small>
                                </div>
                            </td>
                            <td>
                                <span class="text-dark">{{ $p->user->mou ? ($p->user->mou->nama_instansi ?? $p->user->mou->nama_universitas) : '-' }}</span>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    {{ ucwords(str_replace('_', ' ', $p->jenis)) }}
                                </span>
                            </td>
                            <td>
                                <small class="text-muted">
                                    {{ $p->created_at->format('d M Y') }}
                                </small>
                            </td>
                            <td class="text-center">
                                @if ($p->status === 'pending')
                                    <span class="badge-status status-pending">
                                        <i class="bi bi-hourglass-split"></i> Pending
                                    </span>
                                @elseif ($p->status === 'approved')
                                    <span class="badge-status status-approved">
                                        <i class="bi bi-check-circle-fill"></i> Disetujui
                                    </span>
                                @elseif ($p->status === 'rejected')
                                    <span class="badge-status status-rejected">
                                        <i class="bi bi-x-circle-fill"></i> Ditolak
                                    </span>
                                @endif
                            </td>

                            {{-- Status Surat Balasan --}}
                            <td class="text-center">
                                @if ($p->status === 'approved')
                                    @if ($p->status_galasan === 'pending')
                                        <span class="badge-pill-soft bg-soft-gray">Belum Kirim</span>
                                    @else
                                        <span class="badge-pill-soft bg-soft-purple">
                                            <i class="bi bi-send-fill me-1"></i> Terkirim
                                        </span>
                                    @endif
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>

                            {{-- Status Pembayaran --}}
                            <td class="text-center">
                                @if ($p->status === 'approved' && $p->status_galasan === 'sent')
                                    @if ($p->status_pembayaran === 'pending')
                                        <span class="badge-pill-soft bg-soft-yellow">Belum Upload</span>
                                    @elseif ($p->status_pembayaran === 'uploaded')
                                        <span class="badge-pill-soft bg-soft-blue">Perlu Verifikasi</span>
                                    @elseif ($p->status_pembayaran === 'verified')
                                        <span class="badge-pill-soft bg-soft-green">
                                            <i class="bi bi-cash-stack me-1"></i> Lunas
                                        </span>
                                    @endif
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>

                            <td class="text-center">
                                                                    {{-- delete button --}}
                                    <form action="{{ route('admin.pengajuan.destroy', $p->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action btn-delete" title="Hapus" onclick="return confirm('Yakin ingin menghapus pengajuan ini?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @if ($p->status === 'pending')
                                    <div class="d-flex justify-content-center gap-2">
                                        {{-- Tombol Approve --}}
                                        <form action="{{ route('admin.pengajuan.approve', $p->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn-action btn-approve" title="Setujui" onclick="return confirm('Yakin ingin menyetujui pengajuan ini?')">
                                                <i class="bi bi-check-lg fs-6"></i>
                                            </button>
                                        </form>

                                        {{-- Tombol Reject --}}
                                        <form action="{{ route('admin.pengajuan.reject', $p->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn-action btn-reject" title="Tolak" onclick="return confirm('Yakin ingin menolak pengajuan ini?')">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        </form>
                                    </div>
                                @elseif ($p->status === 'approved')
                                    <a href="{{ route('admin.pengajuan.show', $p->id) }}" class="btn-detail">
                                        <i class="bi bi-eye"></i> Detail
                                    </a>
                                @else
                                    <span class="text-muted small fst-italic">Selesai</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="bi bi-inbox display-4 text-muted mb-3 opacity-50"></i>
                                    <h5 class="text-muted fw-bold">Belum ada data pengajuan</h5>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
