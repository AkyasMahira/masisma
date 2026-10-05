@extends('layouts.app')

@section('title', 'Admin - Manajemen Dispensasi')

@section('content')
<style>
    :root {
        --custom-maroon: #7c1316;
    }
    .bg-maroon { background-color: var(--custom-maroon) !important; color: white; }
    .text-maroon { color: var(--custom-maroon) !important; }
    .btn-maroon { background-color: var(--custom-maroon); color: white; border: none; }
    .btn-maroon:hover { background-color: #5a0e10; color: white; }
    .btn-outline-maroon { border: 1px solid var(--custom-maroon); color: var(--custom-maroon); }
    .btn-outline-maroon:hover { background-color: var(--custom-maroon); color: white; }

    /* Dashboard Mini Style */
    .stat-card { border: none; border-radius: 15px; transition: 0.3s; }
    .stat-icon { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; }

    /* Table Styling */
    .table thead th {
        background-color: #f8f9fa;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 1px;
        color: #6c757d;
        border-bottom: 2px solid #dee2e6;
        white-space: nowrap; 
    }
    .bg-maroon-subtle { background-color: #fff5f5; border: 1px solid #7c1316; }
    .progress-bar.bg-maroon { background-color: var(--custom-maroon) !important; }
    .status-badge { padding: 5px 12px; border-radius: 50px; font-size: 0.7rem; font-weight: 800; text-transform: uppercase; }

    /* Pagination Styling */
    .pagination .page-item.active .page-link { background-color: var(--custom-maroon); border-color: var(--custom-maroon); }
    .pagination .page-link { color: var(--custom-maroon); }
    .pagination .page-link:hover { background-color: #fff5f5; color: #5a0e10; }

    /* Action Buttons */
    .btn-action { width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; font-size: 0.85rem; }

    /* FIX SAFARI RESPONSIVE: Smooth Scrolling & Scrollbar Custom */
    .custom-scroll {
        -webkit-overflow-scrolling: touch; 
    }
    .custom-scroll::-webkit-scrollbar { width: 6px; height: 6px;}
    .custom-scroll::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
    .custom-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .custom-scroll::-webkit-scrollbar-thumb:hover { background: var(--custom-maroon); }

    /* FIX SAFARI IFRAME DALAM MODAL */
    .pdf-container {
        position: relative;
        padding-bottom: 75%; 
        height: 0;
        overflow: hidden;
        -webkit-overflow-scrolling: touch;
        border-radius: 8px;
        border: 1px solid #dee2e6;
        background: #f8f9fa;
    }
    .pdf-container iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border: none;
    }
    @media (min-width: 768px) {
        .pdf-container { padding-bottom: 56.25%; }
    }
</style>

<div class="row justify-content-center">
    <div class="col-12 animate-up">
        
        {{-- HEADER BANNER --}}
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center" 
             style="background: white; border-radius: 8px; border-left: 5px solid #7c1316; padding: 20px; margin-bottom: 25px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
            <div class="mb-2 mb-sm-0">
                <h4 class="fw-bold mb-0 text-dark">Data Dispensasi</h4>
                <small class="text-muted">Kelola dispensasi mahasiswa magang.</small>
            </div>
        </div>

        {{-- DASHBOARD MINI & TOP 10 KAMPUS --}}
        <div class="row g-3 mb-4">
            {{-- Statistik --}}
            <div class="col-12 col-lg-5">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="card stat-card shadow-sm border-start border-4 border-warning h-100">
                            <div class="card-body p-3 p-md-4">
                                <small class="text-muted d-block fw-bold small text-uppercase">Pending</small>
                                <h4 class="fw-bold mb-0 text-dark">{{ $dispensasis->where('status', 'pending')->count() }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card stat-card shadow-sm border-start border-4 border-primary h-100">
                            <div class="card-body p-3 p-md-4">
                                <small class="text-muted d-block fw-bold small text-uppercase">Total Data</small>
                                <h4 class="fw-bold mb-0 text-dark">{{ $dispensasis->total() }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card stat-card shadow-sm border-start border-4 border-success h-100">
                            <div class="card-body p-3 p-md-4">
                                <small class="text-muted d-block fw-bold small text-uppercase">Disetujui</small>
                                <h4 class="fw-bold mb-0 text-dark">{{ $dispensasis->where('status', 'approved')->count() }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card stat-card shadow-sm border-start border-4 border-danger h-100">
                            <div class="card-body p-3 p-md-4">
                                <small class="text-muted d-block fw-bold small text-uppercase">Ditolak</small>
                                <h4 class="fw-bold mb-0 text-dark">{{ $dispensasis->where('status', 'rejected')->count() }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Top 10 Kampus --}}
            <div class="col-12 col-lg-7">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white border-0 pt-3 px-3">
                        <h6 class="fw-bold mb-0 text-maroon small"><i class="bi bi-trophy-fill me-2"></i> TOP 10 KAMPUS </h6>
                    </div>
                    <div class="card-body p-3 custom-scroll overflow-auto" style="max-height: 200px;">
                        @forelse($rankings as $index => $rank)
                            <div class="d-flex align-items-center mb-2">
                                <div class="fw-bold me-3 text-muted" style="width: 20px;">{{ $index + 1 }}.</div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="small fw-bold text-dark text-truncate pe-2" style="max-width: 200px;">{{ $rank->nama_universitas }}</span>
                                        <span class="badge bg-maroon-subtle text-maroon" style="font-size: 0.7rem;">{{ $rank->total }} </span>
                                    </div>
                                    <div class="progress" style="height: 4px;">
                                        @php
                                            $max = $rankings->first()->total ?? 1;
                                            $percent = ($rank->total / $max) * 100;
                                        @endphp
                                        <div class="progress-bar bg-maroon" style="width: {{ $percent }}%"></div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-muted small py-4">Belum ada data ranking.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- MAIN TABLE CARD --}}
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
            
            {{-- Header Tabel Responsive --}}
            <div class="card-header text-white p-3 p-md-4" style="background: var(--custom-maroon);">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                    <div>
                        <h5 class="mb-0 fw-bold"><i class="bi bi-shield-lock-fill me-2"></i> Manajemen Dispensasi</h5>
                        <p class="mb-0 opacity-75 small">Verifikasi berkas asli & tanda tangan basah Karu.</p>
                    </div>
                    
                    {{-- Search Form --}}
                    <form action="{{ route('admin.dispensasi.index') }}" method="GET" class="w-100" style="max-width: 350px;">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control border-0" placeholder="Cari nama mahasiswa..." value="{{ request('search') }}" style="border-radius: 50px 0 0 50px;">
                            <button class="btn btn-light text-maroon px-3" type="submit" style="border-radius: 0 50px 50px 0;"><i class="bi bi-search"></i></button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="card-body p-0">
                <div class="table-responsive custom-scroll">
                    <table class="table table-hover align-middle mb-0" style="min-width: 800px;">
                        <thead>
                            <tr>
                                <th class="px-3 px-md-4 py-3">Mahasiswa</th>
                                <th class="px-3 px-md-4 py-3 text-center">Jenis</th>
                                <th class="px-3 px-md-4 py-3">Periode Izin</th>
                                <th class="px-3 px-md-4 py-3">Keterangan & Berkas</th>
                                <th class="px-3 px-md-4 py-3 text-center">Status</th>
                                <th class="px-3 px-md-4 py-3 text-end text-nowrap">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dispensasis as $item)
                            <tr>
                                <td class="px-3 px-md-4">
                                    <div class="fw-bold text-dark text-truncate" style="max-width: 180px;">{{ $item->mahasiswa->nm_mahasiswa }}</div>
                                    <small class="text-muted d-block text-truncate" style="max-width: 180px;">{{ $item->mahasiswa->prodi }}</small>
                                </td>
                                <td class="px-3 px-md-4 text-center">
                                    @if($item->kategori == 'terlambat')
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2">TERLAMBAT</span>
                                        {{-- DETEKSI SHIFT MALAM DI BADGE --}}
                                        @if(stripos($item->keterangan, 'malam') !== false)
                                            <span class="badge bg-dark text-white border border-dark mt-1 px-2 d-block"><i class="bi bi-moon-stars-fill text-warning"></i> SHIFT MALAM</span>
                                        @endif
                                    @elseif($item->kategori == 'lupa_pulang')
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2">LUPA PULANG</span>
                                    @else
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2">IZIN BIASA</span>
                                    @endif
                                </td>
                                <td class="px-3 px-md-4 small text-nowrap">
                                    <div class="fw-bold text-dark">{{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d/m/y') }}</div>
                                    <div class="text-muted">s/d {{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d/m/y') }}</div>
                                </td>
                                <td class="px-3 px-md-4">
                                    <p class="mb-1 small text-dark text-truncate" style="max-width: 150px;" title="{{ $item->keterangan }}">{{ $item->keterangan }}</p>
                                    @if($item->file_path)
                                        <a href="{{ asset('storage/' . $item->file_path) }}" target="_blank" class="text-maroon fw-bold small text-decoration-none text-nowrap">
                                            <i class="bi bi-file-earmark-pdf-fill me-1"></i> Lihat Berkas
                                        </a>
                                    @endif
                                </td>
                                <td class="px-3 px-md-4 text-center">
                                    @if($item->status == 'pending')
                                        <span class="status-badge bg-warning text-dark">Menunggu</span>
                                    @elseif($item->status == 'approved')
                                        <span class="status-badge bg-success text-white">Disetujui</span>
                                    @else
                                        <span class="status-badge bg-danger text-white">Ditolak</span>
                                    @endif
                                </td>
                                <td class="px-3 px-md-4 text-end">
                                    <div class="d-flex justify-content-end flex-wrap gap-1">
                                        
                                        {{-- Detail Button (TRIGGER MODAL Yg Ditaruh di Bawah) --}}
                                        <button class="btn btn-info btn-action text-white" data-bs-toggle="modal" data-bs-target="#detailModal{{ $item->id }}" title="Detail"><i class="bi bi-eye"></i></button>

                                        @if($item->status == 'pending')
                                        <form action="{{ route('admin.dispensasi.approve', $item->id) }}" method="POST" onsubmit="return confirm('Setujui Pengajuan Ini?')">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-action" title="Approve"><i class="bi bi-check-lg"></i></button>
                                        </form>
                                        <button class="btn btn-danger btn-action" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $item->id }}" title="Reject"><i class="bi bi-x-lg"></i></button>
                                        @endif

                                        <a href="{{ route('admin.dispensasi.edit', $item->id) }}" class="btn btn-outline-dark btn-action" title="Edit"><i class="bi bi-pencil"></i></a>

                                        <form action="{{ route('admin.dispensasi.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus data dispensasi ini secara permanen?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-action" title="Hapus"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox display-4 opacity-25"></i>
                                    <p class="mt-2">Belum ada data pengajuan dispensasi.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            {{-- Footer Pagination --}}
            <div class="card-footer bg-white p-3 p-md-4 border-0">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                    <small class="text-muted text-center text-md-start">
                        Menampilkan data ke-{{ $dispensasis->firstItem() ?? 0 }} s.d {{ $dispensasis->lastItem() ?? 0 }} dari total {{ $dispensasis->total() }} data
                    </small>
                    <div class="overflow-auto custom-scroll" style="max-width: 100%;">
                        {{ $dispensasis->withQueryString()->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ========================================================== --}}
{{-- KUMPULAN MODAL (HARUS DILUAR TABEL AGAR TIDAK ERROR Z-INDEX) --}}
{{-- ========================================================== --}}
@foreach($dispensasis as $item)

    {{-- Modal Detail Responsif --}}
    <div class="modal fade" id="detailModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0">
                <div class="modal-header bg-maroon text-white">
                    <h6 class="modal-title fw-bold">Detail Pengajuan #{{ $item->id }}</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-3 p-md-4 custom-scroll">
                    
                    {{-- DETEKSI SHIFT MALAM DI DALAM MODAL --}}
                    @if($item->kategori == 'terlambat' && stripos($item->keterangan, 'malam') !== false)
                    <div class="alert alert-dark border-dark text-white d-flex align-items-center mb-4">
                        <i class="bi bi-moon-stars-fill fs-3 me-3 text-warning"></i>
                        <div>
                            <strong><i class="bi bi-exclamation-triangle-fill text-warning"></i> Indikasi Shift Malam!</strong><br>
                            <small class="opacity-75">Mahasiswa menyebutkan kata "malam" di keterangannya. Approval dispen ini otomatis memproses absen lintas hari.</small>
                        </div>
                    </div>
                    @endif

                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="small text-muted d-block">Mahasiswa</label>
                            <strong class="text-break">{{ $item->mahasiswa->nm_mahasiswa }}</strong>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="small text-muted d-block">Kategori</label>
                            <strong>{{ strtoupper(str_replace('_', ' ', $item->kategori)) }}</strong>
                        </div>
                        <div class="col-12">
                            <label class="small text-muted d-block">Alasan</label>
                            <p class="mb-0 bg-light p-3 rounded text-break">{{ $item->keterangan }}</p>
                        </div>
                        @if($item->catatan_admin)
                        <div class="col-12">
                            <label class="small text-danger d-block">Catatan Admin</label>
                            <p class="mb-0 border border-danger p-3 rounded small fst-italic text-break">{{ $item->catatan_admin }}</p>
                        </div>
                        @endif
                        @if($item->file_path)
                        <div class="col-12 mt-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="small text-muted mb-0">Preview Berkas PDF</label>
                                <a href="{{ asset('storage/' . $item->file_path) }}" target="_blank" class="btn btn-sm btn-outline-maroon rounded-pill">
                                    <i class="bi bi-box-arrow-up-right me-1"></i> Buka Full
                                </a>
                            </div>
                            <div class="pdf-container">
                                <iframe src="{{ asset('storage/' . $item->file_path) }}" allowfullscreen></iframe>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Reject --}}
    <div class="modal fade" id="rejectModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0">
                <form action="{{ route('admin.dispensasi.reject', $item->id) }}" method="POST">
                    @csrf
                    <div class="modal-header bg-danger text-white">
                        <h6 class="modal-title fw-bold">Alasan Penolakan</h6>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <textarea name="catatan_admin" class="form-control custom-scroll" rows="4" placeholder="Sebutkan alasan penolakan secara singkat..." required></textarea>
                    </div>
                    <div class="modal-footer border-0 pt-0 bg-white">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">Tolak Pengajuan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endforeach

@endsection