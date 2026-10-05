@extends('layouts.app')

@section('title', 'Riwayat Dispensasi')
@section('page-title', 'Daftar Riwayat Dispensasi')

@section('content')
<style>
    :root {
        --custom-maroon: #7c1316;
    }
    .text-maroon { color: var(--custom-maroon); }
    .btn-maroon { background-color: var(--custom-maroon); color: white; border: none; }
    .btn-maroon:hover { background-color: #5a0e10; color: white; }
    
    /* Styling Table agar senada dengan Card */
    .table thead th {
        background-color: #f8f9fa;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 1px;
        color: #6c757d;
        border-bottom: 2px solid #dee2e6;
    }

    .status-badge {
        padding: 6px 12px;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
    }

    .note-box {
        background: #f8f9fa;
        border-left: 3px solid var(--custom-maroon);
        padding: 10px;
        border-radius: 4px;
        font-size: 0.85rem;
    }

    .note-rejected {
        border-left-color: #dc3545;
        background: #fff5f5;
    }
</style>

<div class="row justify-content-center">
    <div class="col-md-12 animate-up">
        
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
            {{-- Header sama dengan styling Create --}}
            <div class="card-header text-white p-4" style="background: var(--custom-maroon);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0 fw-bold"><i class="bi bi-clock-history me-2"></i> Riwayat Pengajuan Anda</h5>
                        <p class="mb-0 opacity-75 small">Pantau status verifikasi dan catatan dari Admin/Karu.</p>
                    </div>
                    <a href="{{ route('mahasiswa.dispensasi.create') }}" class="btn btn-light btn-sm rounded-pill px-3 fw-bold text-maroon">
                        <i class="bi bi-plus-lg me-1"></i> Izin
                    </a>
                </div>
            </div>
            
            <div class="card-body p-0"> {{-- p-0 agar tabel menempel ke pinggir card --}}
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="px-4 py-3">Jenis & Tanggal</th>
                                <th class="px-4 py-3">Alasan</th>
                                <th class="px-4 py-3 text-center">Status</th>
                                <th class="px-4 py-3">Respon Admin (Alasan Terima/Tolak)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($riwayat as $item)
                            <tr>
                                {{-- Kolom Jenis & Tanggal --}}
                                <td class="px-4 py-4">
                                    <div class="mb-1">
                                        @if($item->kategori == 'terlambat')
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2">TERLAMBAT</span>
                                        @elseif($item->kategori == 'lupa_pulang')
                                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2">LUPA PULANG</span>
                                        @else
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2">IZIN BIASA</span>
                                        @endif
                                    </div>
                                    <span class="fw-bold text-dark">
                                        {{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d/m/Y') }} 
                                        @if($item->tanggal_mulai != $item->tanggal_selesai)
                                            - {{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d/m/Y') }}
                                        @endif
                                    </span>
                                    <br>
                                    <small class="text-muted">ID: #DIS-{{ $item->id }}</small>
                                </td>

                                {{-- Alasan Mahasiswa & Berkas --}}
                                <td class="px-4">
                                    <p class="mb-2 small text-dark">{{ Str::limit($item->keterangan, 100) }}</p>
                                    @if($item->file_path)
                                        <a href="{{ asset('storage/' . $item->file_path) }}" target="_blank" class="text-maroon fw-bold small text-decoration-none">
                                            <i class="bi bi-file-earmark-pdf-fill me-1"></i> Lihat Berkas Scan
                                        </a>
                                    @endif
                                </td>

                                {{-- Status Badge --}}
                                <td class="px-4 text-center">
                                    @if($item->status == 'pending')
                                        <span class="status-badge bg-warning text-dark">Menunggu</span>
                                    @elseif($item->status == 'approved')
                                        <span class="status-badge bg-success text-white">Disetujui</span>
                                    @else
                                        <span class="status-badge bg-danger text-white">Ditolak</span>
                                    @endif
                                </td>

                                {{-- Respon Admin (Catatan) --}}
                                <td class="px-4">
                                    @if($item->catatan_admin)
                                        <div class="note-box {{ $item->status == 'rejected' ? 'note-rejected' : '' }}">
                                            <div class="fw-bold small mb-1">Catatan Admin:</div>
                                            {{ $item->catatan_admin }}
                                        </div>
                                    @else
                                        @if($item->status == 'pending')
                                            <span class="text-muted small fst-italic">Belum ada respon...</span>
                                        @else
                                            <span class="text-muted small fst-italic">Diverifikasi tanpa catatan.</span>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <i class="bi bi-folder2-open display-4 text-muted opacity-25"></i>
                                    <p class="mt-3 text-muted">Belum ada riwayat pengajuan izin.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            {{-- Footer untuk info tambahan --}}
            <div class="card-footer bg-white p-3 border-0">
                <div class="d-flex justify-content-between align-items-center">
                    <p class="mb-0 small text-muted">Total: {{ $riwayat->count() }} Pengajuan</p>
                    <a href="{{ route('mahasiswa.dashboard') }}" class="btn btn-link btn-sm text-muted text-decoration-none">Kembali ke Dashboard</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection