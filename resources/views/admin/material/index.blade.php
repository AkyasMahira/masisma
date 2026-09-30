@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    :root { 
        --maroon-slg: #7c1316; 
        --bg-light: #f4f6f9;
        --filter-header: #fdf5f5;
    }

    body { background-color: var(--bg-light); }

    /* Header Section - Matching SINDIKAT Pattern */
    .header-card {
        background: white;
        border-radius: 8px;
        border-left: 5px solid var(--maroon-slg);
        padding: 20px;
        margin-bottom: 25px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    .btn-maroon {
        background-color: var(--maroon-slg);
        color: white;
        font-weight: 600;
        border-radius: 8px;
        padding: 8px 20px;
        border: none;
    }
    .btn-maroon:hover { background-color: #5a0e10; color: white; }

    /* Filter Section - Matching Screenshot SINDIKAT */
    .filter-section {
        background: white;
        border-radius: 8px;
        overflow: hidden;
        margin-bottom: 25px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    .filter-header {
        background-color: var(--filter-header);
        padding: 12px 20px;
        color: var(--maroon-slg);
        font-weight: 600;
        border-bottom: 1px solid #eee;
    }
    .filter-body { padding: 20px; }
    
    .btn-search {
        background-color: var(--maroon-slg);
        color: white;
        border-radius: 5px;
        padding: 8px 30px;
        font-weight: 600;
        width: 100%;
        border: none;
    }

    /* Table Styling - Serious & Bold */
    .table-container {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        background: white;
    }
    .table thead th {
        background-color: var(--maroon-slg) !important;
        color: white !important;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 1px;
        padding: 15px;
        border: none;
    }
    .table tbody td {
        padding: 18px 15px;
        vertical-align: middle;
        border-bottom: 1px solid #f0f0f0;
    }

    /* Manual Pagination Styling */
    .pagination-manual {
        display: flex; justify-content: center; list-style: none; padding: 0; gap: 5px;
    }
    .pagination-manual li a, .pagination-manual li span {
        padding: 8px 15px; border: 1px solid #ddd; background: white;
        color: var(--maroon-slg); text-decoration: none; border-radius: 5px; font-weight: 600;
    }
    .pagination-manual li.active span { background: var(--maroon-slg); color: white; border-color: var(--maroon-slg); }
    .pagination-manual li.disabled span { color: #ccc; cursor: not-allowed; }
    
    .badge-external {
        background-color: #fff5f5;
        color: var(--maroon-slg);
        border: 1px solid rgba(124, 19, 22, 0.2);
        font-size: 0.7rem;
        font-weight: 700;
    }
</style>

<div class="container-fluid py-4">
    <div class="header-card d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-bold mb-0 text-dark">Materi Orientasi</h4>
            <small class="text-muted">Kelola modul pembelajaran orientasi mahasiswa RSUD SLG.</small>
        </div>
        <a href="{{ route('admin.materi.create') }}" class="btn btn-maroon shadow-sm">
            <i class="fas fa-plus-circle me-1"></i> Tambah Materi
        </a>
    </div>

    <div class="filter-section">
        <div class="filter-header">
            <i class="fas fa-filter me-2"></i> Filter & Pencarian
        </div>
        <div class="filter-body">
            <form action="{{ route('admin.materi.index') }}" method="GET">
                <div class="row align-items-end g-3">
                    <div class="col-md-9">
                        <label class="small fw-bold text-muted mb-1">CARI JUDUL MATERI</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control border-start-0" placeholder="Masukkan judul materi..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-search shadow-sm">Terapkan</button>
                            <a href="{{ route('admin.materi.index') }}" class="btn btn-outline-secondary px-3 shadow-sm">
                                <i class="fas fa-sync-alt"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="table-container shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th class="text-center" width="50">NO</th>
                        <th>JUDUL MATERI</th>
                        <th>SUBTITLE & DESKRIPSI</th>
                        <th class="text-center">FILE</th>
                        <th class="text-center">ORDER</th>
                        <th class="text-center" width="150">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($materials as $index => $m)
                    <tr>
                        <td class="text-center fw-bold text-muted">{{ $materials->firstItem() + $index }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ $m->title }}</div>
                            @if($m->external_link)
                                <a href="{{ $m->external_link }}" target="_blank" class="badge badge-external text-decoration-none mt-1">
                                    <i class="fas fa-link me-1"></i> EXTERNAL LINK
                                </a>
                            @endif
                        </td>
                        <td>
                            <div class="small fw-bold text-maroon mb-1">{{ $m->subtitle ?? '-' }}</div>
                          <div class="text-muted" style="font-size: 0.75rem;">
    {!! Str::limit(strip_tags($m->description), 80) !!}
</div>
                        </td>
                        <td class="text-center">
                            <span class="badge rounded-pill bg-light text-dark border px-3 fw-bold">
                                <i class="fas fa-folder-open me-1 text-primary"></i> {{ $m->files->count() }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-light text-muted border px-3 fw-bold">#{{ $m->order }}</span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ route('admin.materi.files', $m->id) }}" class="btn btn-sm btn-light border text-primary" title="Kelola File">
                                    <i class="fas fa-file-upload"></i>
                                </a>
                                <a href="{{ route('admin.materi.edit', $m->id) }}" class="btn btn-sm btn-light border text-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.materi.destroy', $m->id) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-light border text-danger" onclick="return confirm('Hapus materi ini?')">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3 opacity-25"></i>
                            <h6 class="text-muted fw-bold">Data materi tidak ditemukan</h6>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($materials->hasPages())
    <div class="mt-4 d-flex justify-content-between align-items-center">
        <div class="small text-muted fw-bold">
            Menampilkan {{ $materials->firstItem() }} - {{ $materials->lastItem() }} dari {{ $materials->total() }} data
        </div>
        <ul class="pagination-manual">
            @if ($materials->onFirstPage())
                <li class="disabled"><span><i class="fas fa-chevron-left"></i></span></li>
            @else
                <li><a href="{{ $materials->previousPageUrl() }}"><i class="fas fa-chevron-left"></i></a></li>
            @endif

            @foreach ($materials->getUrlRange(max(1, $materials->currentPage() - 2), min($materials->lastPage(), $materials->currentPage() + 2)) as $page => $url)
                @if ($page == $materials->currentPage())
                    <li class="active"><span>{{ $page }}</span></li>
                @else
                    <li><a href="{{ $url }}">{{ $page }}</a></li>
                @endif
            @endforeach

            @if ($materials->hasMorePages())
                <li><a href="{{ $materials->nextPageUrl() }}"><i class="fas fa-chevron-right"></i></a></li>
            @else
                <li class="disabled"><span><i class="fas fa-chevron-right"></i></span></li>
            @endif
        </ul>
    </div>
    @endif
</div>
@endsection