@extends('layouts.app') @section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    :root { 
        --maroon-slg: #7c1316; 
        --bg-light: #f4f6f9;
        --filter-header: #fdf5f5;
    }

    body { background-color: var(--bg-light); }

    /* Header Section - Matching Screenshot */
    .header-card {
        background: white;
        border-radius: 8px;
        border-left: 5px solid var(--maroon-slg);
        padding: 20px;
        margin-bottom: 25px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    .btn-create {
        background-color: var(--maroon-slg);
        color: white;
        font-weight: 600;
        border-radius: 8px;
        padding: 8px 20px;
        border: none;
    }
    .btn-create:hover { background-color: #5a0e10; color: white; }

    /* Filter Section - Matching Screenshot */
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
    .btn-refresh {
        border: 1px solid #ddd;
        background: white;
        border-radius: 5px;
        padding: 8px 15px;
        color: #666;
    }

    /* Landscape Card Styling */
    .pkg-card { 
        background: white; border-radius: 12px; overflow: hidden; 
        border: 1px solid #eee; transition: 0.3s; height: 100%;
        display: flex; flex-direction: column;
    }
    .pkg-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
    
    .img-landscape-wrapper {
        width: 100%; height: 160px; background: #333; overflow: hidden; position: relative;
    }
    .img-landscape { width: 100%; height: 100%; object-fit: cover; }
    
    .status-badge {
        position: absolute; top: 10px; right: 10px; padding: 4px 10px;
        border-radius: 20px; font-size: 10px; font-weight: bold; text-transform: uppercase;
        color: white;
    }

    /* Manual Pagination */
    .pagination-manual {
        display: flex; justify-content: center; list-style: none; padding: 0; gap: 5px;
    }
    .pagination-manual li a, .pagination-manual li span {
        padding: 8px 15px; border: 1px solid #ddd; background: white;
        color: var(--maroon-slg); text-decoration: none; border-radius: 5px; font-weight: 600;
    }
    .pagination-manual li.active span { background: var(--maroon-slg); color: white; border-color: var(--maroon-slg); }
    .pagination-manual li.disabled span { color: #ccc; cursor: not-allowed; }
</style>

<div class="container-fluid py-4">
    <div class="header-card d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-bold mb-0" style="color: #7c1316;">Paket Link</h4>
            <small class="text-muted">Kelola paket link, gambar, dan aset promosi.</small>
        </div>
        <button class="btn btn-create shadow-sm" data-bs-toggle="modal" data-bs-target="#addModal">
            <i class="fas fa-plus-circle me-1"></i> Buat Paket Baru
        </button>
    </div>

    <div class="filter-section">
        <div class="filter-header">
            <i class="fas fa-filter me-2"></i> Filter & Pencarian
        </div>
        <div class="filter-body">
            <form action="{{ route('admin.linktree.index') }}" method="GET">
                <div class="row align-items-end">
                    <div class="col-md-5">
                        <label class="small fw-bold text-muted mb-1 uppercase">CARI NAMA / SLUG</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control border-start-0" placeholder="Ketikan kata kunci pencarian..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="small fw-bold text-muted mb-1 uppercase">STATUS PAKET</label>
                        <select name="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Non-Aktif</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-search shadow-sm">Cari Data</button>
                    </div>
                    <div class="col-md-1 text-end">
                        <a href="{{ route('admin.linktree.index') }}" class="btn btn-refresh shadow-sm">
                            <i class="fas fa-sync-alt"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        @forelse($packages as $p)
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="pkg-card">
                <div class="img-landscape-wrapper">
                    @if($p->image)
                        <img src="{{ asset('storage/'.$p->image) }}" class="img-landscape">
                    @else
                        <div class="d-flex align-items-center justify-content-center h-100 bg-dark text-secondary">
                            <i class="fas fa-images fa-3x"></i>
                        </div>
                    @endif
                    <span class="status-badge {{ $p->is_active ? 'bg-success' : 'bg-danger' }}">
                        {{ $p->is_active ? 'Active' : 'Off' }}
                    </span>
                </div>
                <div class="p-4">
                    <h6 class="fw-bold text-dark mb-1">{{ Str::limit($p->title, 45) }}</h6>
                    <p class="text-muted small mb-3"><i class="fas fa-link me-1"></i> /v/{{ $p->slug }}</p>
                    
                    <div class="d-flex gap-2 mb-2">
                        <a href="{{ route('admin.linktree.edit', $p->id) }}" class="btn btn-sm btn-outline-dark flex-grow-1 fw-bold">
                            <i class="fas fa-cog me-1"></i> ATUR
                        </a>
                        <button onclick="copyToClipboard('{{ route('linktree.view', $p->slug) }}')" class="btn btn-sm btn-outline-secondary px-3">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <form action="{{ route('admin.linktree.toggle', $p->id) }}" method="POST" class="flex-grow-1">
                            @csrf
                            <button type="submit" class="btn btn-sm w-100 fw-bold {{ $p->is_active ? 'btn-danger' : 'btn-success' }}">
                                <i class="fas {{ $p->is_active ? 'fa-eye-slash' : 'fa-check-circle' }} me-1"></i>
                                {{ $p->is_active ? 'NON AKTIFKAN' : 'AKTIF' }}
                            </button>
                        </form>
                        <form action="{{ route('admin.linktree.destroy', $p->id) }}" method="POST" >
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-outline-danger px-3">
            <i class="fas fa-trash"></i>
        </button>
    </form>
                        <button onclick="generateQR('{{ route('linktree.view', $p->slug) }}', '{{ $p->title }}')" class="btn btn-sm btn-dark px-3">
                            <i class="fas fa-qrcode"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5 bg-white rounded border shadow-sm">
            <i class="fas fa-folder-open fa-4x text-muted mb-3"></i>
            <h5 class="text-muted fw-bold">Data tidak ditemukan</h5>
            <p class="text-muted">Silakan tambahkan data paket digital folder baru.</p>
        </div>
        @endforelse
    </div>

    <div class="mt-4">
        <ul class="pagination-manual">
            @if ($packages->onFirstPage())
                <li class="disabled"><span><i class="fas fa-chevron-left"></i></span></li>
            @else
                <li><a href="{{ $packages->previousPageUrl() }}"><i class="fas fa-chevron-left"></i></a></li>
            @endif

            @foreach ($packages->getUrlRange(1, $packages->lastPage()) as $page => $url)
                @if ($page == $packages->currentPage())
                    <li class="active"><span>{{ $page }}</span></li>
                @else
                    <li><a href="{{ $url }}">{{ $page }}</a></li>
                @endif
            @endforeach

            @if ($packages->hasMorePages())
                <li><a href="{{ $packages->nextPageUrl() }}"><i class="fas fa-chevron-right"></i></a></li>
            @else
                <li class="disabled"><span><i class="fas fa-chevron-right"></i></span></li>
            @endif
        </ul>
    </div>
</div>

<div class="modal fade" id="qrModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-body p-4 text-center">
                <h6 id="qrTitle" class="fw-bold text-maroon mb-3" style="color: #7c1316;"></h6>
                
                <div id="qrContainer" class="p-2 bg-light rounded mb-3">
                    </div>

                <div class="d-grid gap-2">
                    <button onclick="downloadQR()" class="btn btn-create w-100 rounded-pill py-2" style="background-color: #7c1316; color: white; border: none; font-weight: 600;">
                        <i class="fas fa-download me-2"></i> DOWNLOAD PNG
                    </button>
                    
                    <button onclick="printOnlyQR()" class="btn btn-outline-secondary w-100 rounded-pill py-2" style="font-size: 0.85rem;">
                        <i class="fas fa-print me-2"></i> Print QR Code
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('admin.linktree.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
                <div class="modal-header bg-maroon text-white p-4 border-0">
                    <h5 class="modal-title fw-bold"><i class="fas fa-plus-circle me-2"></i>Buat Link</h5>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted">JUDUL PAKET</label>
                        <input type="text" name="title" class="form-control" placeholder="Contoh: Berkas Akreditasi 2026" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted">BANNER LANDSCAPE</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer border-0 p-4">
                    <button type="submit" class="btn btn-create w-100 py-2">SIMPAN PAKET</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    // 1. Fungsi Tampilkan QR di Modal
    function generateQR(url, title) {
        const qrContainer = document.getElementById('qrContainer');
        document.getElementById('qrTitle').innerText = title;

        // Gunakan size 500x500 agar saat didownload gambarnya tidak pecah
        const qrImg = `https://api.qrserver.com/v1/create-qr-code/?size=500x500&data=${encodeURIComponent(url)}&color=7c1316`;
        
        // Render ke modal (tambahkan id qrImageTarget)
        qrContainer.innerHTML = `<img src="${qrImg}" id="qrImageTarget" class="img-fluid rounded shadow-sm" style="width: 220px;">`;
        
        new bootstrap.Modal(document.getElementById('qrModal')).show();
    }

    // 2. Fitur Download Langsung as PNG
    async function downloadQR() {
        const qrImgSrc = document.querySelector('#qrImageTarget').src;
        const title = document.getElementById('qrTitle').innerText;
        
        // Nama file: QR_Nama_Paket.png
        const fileName = `QR_SINDIKAT_${title.replace(/\s+/g, '_')}.png`;

        try {
            const response = await fetch(qrImgSrc);
            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            
            const a = document.createElement('a');
            a.href = url;
            a.download = fileName;
            document.body.appendChild(a);
            a.click();
            
            // Bersihkan memori
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
        } catch (error) {
            console.error('Gagal mendownload:', error);
            alert('Gagal mendownload QR Code. Silakan coba lagi.');
        }
    }

    // 3. Fitur Print (Hanya QR saja)
    function printOnlyQR() {
        const qrImgSrc = document.querySelector('#qrImageTarget').src;
        const title = document.getElementById('qrTitle').innerText;

        const printWindow = window.open('', '_blank', 'width=600,height=600');
        printWindow.document.write(`
            <html>
                <head>
                    <title>Cetak QR - ${title}</title>
                    <style>
                        body { margin: 0; display: flex; flex-direction: column; justify-content: center; align-items: center; height: 100vh; font-family: sans-serif; text-align: center; }
                        img { width: 400px; }
                        h2 { color: #7c1316; margin-top: 15px; }
                    </style>
                </head>
                <body>
                    <img src="${qrImgSrc}">
                    <h2>${title}</h2>
                    <script>window.onload = function() { window.print(); window.close(); };<\/script>
                </body>
            </html>
        `);
        printWindow.document.close();
    }

    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            alert('Link berhasil disalin ke clipboard!');
        });
    }
</script>
@endsection