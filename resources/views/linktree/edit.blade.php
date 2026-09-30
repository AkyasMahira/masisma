@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    :root { 
        --maroon-slg: #7c1316; 
        --bg-light: #f4f6f9;
        --text-dark: #2d3436;
    }

    body { background-color: var(--bg-light); font-family: 'Inter', sans-serif; }

    /* Header Section */
    .header-card {
        background: white;
        border-radius: 12px;
        border-left: 6px solid var(--maroon-slg);
        padding: 25px;
        margin-bottom: 30px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    }

    /* Sidebar Cards */
    .card-custom {
        border: none;
        border-radius: 15px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        overflow: hidden;
        margin-bottom: 20px;
    }
    .card-custom .card-header {
        background-color: white;
        border-bottom: 1px solid #f0f0f0;
        padding: 15px 20px;
        font-weight: 700;
        color: var(--maroon-slg);
    }

    /* Package Preview */
    .img-preview-wrapper {
        width: 100%;
        height: 120px;
        border-radius: 10px;
        overflow: hidden;
        background: #eee;
        margin-bottom: 15px;
        border: 2px dashed #ddd;
    }
    .img-preview { width: 100%; height: 100%; object-fit: cover; }

    /* Structure Area */
    .group-card {
        background: white;
        border-radius: 15px;
        border: 1px solid #e0e6ed;
        margin-bottom: 25px;
        transition: 0.3s;
    }
    .group-card:hover { border-color: var(--maroon-slg); box-shadow: 0 5px 15px rgba(124,19,22,0.1); }
    
    .group-header {
        background-color: #fafbfc;
        padding: 15px 20px;
        border-bottom: 1px solid #f0f0f0;
        border-radius: 15px 15px 0 0;
    }

    /* Content Items */
    .content-item {
        background: white;
        border: 1px solid #f0f0f0;
        border-radius: 10px;
        padding: 12px 15px;
        margin-bottom: 10px;
        transition: 0.2s;
    }
    .content-item:hover { background: #fffcfc; border-color: #ffd6d6; }

    /* Buttons */
    .btn-maroon { background-color: var(--maroon-slg); color: white; border-radius: 8px; font-weight: 600; border: none; }
    .btn-maroon:hover { background-color: #5a0e10; color: white; }
    
    .btn-outline-maroon { border: 1.5px solid var(--maroon-slg); color: var(--maroon-slg); border-radius: 8px; font-weight: 600; }
    .btn-outline-maroon:hover { background-color: var(--maroon-slg); color: white; }

    /* Form Inputs */
    .form-control-custom { border-radius: 8px; border: 1px solid #dcdde1; padding: 10px 15px; }
    .form-control-custom:focus { border-color: var(--maroon-slg); box-shadow: 0 0 0 0.2rem rgba(124,19,22,0.1); }
</style>

<div class="container-fluid py-4">
    <div class="mb-4 d-flex align-items-center justify-content-between">
        <a href="{{ route('admin.linktree.index') }}" class="btn btn-outline-dark btn-sm rounded-pill px-3">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
        </a>
        <div class="text-end">
            <span class="badge bg-light text-dark border p-2 rounded-pill">
                <i class="fas fa-link me-1 text-maroon"></i> /v/{{ $package->slug }}
            </span>
        </div>
    </div>

    <div class="header-card d-flex align-items-center">
        <div class="me-4 d-none d-md-block">
            <div class="bg-maroon text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; background-color: var(--maroon-slg);">
                <i class="fas fa-folder-open fa-2x"></i>
            </div>
        </div>
        <div>
            <h3 class="fw-bold mb-1" style="color: #1a1a1a;">Kelola Struktur Isi</h3>
            <p class="text-muted mb-0">Atur kategori, link, dan berkas untuk paket <strong>{{ $package->title }}</strong></p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <div class="card card-custom">
                <div class="card-header"><i class="fas fa-pen-to-square me-2"></i>Edit Informasi Paket</div>
                <div class="card-body">
                    <form action="{{ route('admin.linktree.update', $package->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf @method('PUT')
                        <div class="mb-4 text-center">
                            <label class="small fw-bold text-muted d-block mb-2">LANDSCAPE BANNER</label>
                            <div class="img-preview-wrapper shadow-sm">
                                @if($package->image)
                                    <img src="{{ asset('storage/'.$package->image) }}" class="img-preview">
                                @else
                                    <div class="d-flex align-items-center justify-content-center h-100 text-muted small">
                                        <i class="fas fa-image me-2"></i> Belum ada banner
                                    </div>
                                @endif
                            </div>
                            <input type="file" name="image" class="form-control form-control-sm">
                        </div>
                        <div class="mb-4">
                            <label class="small fw-bold text-muted mb-1">JUDUL PAKET DIGITAL</label>
                            <input type="text" name="title" class="form-control form-control-custom" value="{{ $package->title }}" required>
                        </div>
                        <button type="submit" class="btn btn-maroon w-100 py-2">
                            <i class="fas fa-save me-2"></i>Simpan Perubahan
                        </button>
                    </form>
                </div>
            </div>

            <div class="card card-custom">
                <div class="card-header text-dark"><i class="fas fa-folder-plus me-2 text-maroon"></i>Tambah Grup Baru</div>
                <div class="card-body">
                    <form action="{{ route('admin.linktree.item.store', $package->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <input type="text" name="title" class="form-control form-control-custom" placeholder="Misal: Berkas Pendaftaran" required>
                        </div>
                        <button type="submit" class="btn btn-outline-maroon w-100 py-2">
                            <i class="fas fa-plus me-2"></i>Buat Grup
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <h5 class="fw-bold mb-4 d-flex align-items-center">
                <i class="fas fa-sitemap me-2 text-maroon"></i> Struktur Digital Folder
            </h5>

            @forelse($package->items as $item)
                <div class="group-card shadow-sm">
                    <div class="group-header d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-dark"><i class="fas fa-folder me-2 text-warning"></i> {{ $item->title }}</span>
                        <form action="{{ route('admin.linktree.item.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus grup ini beserta isinya?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-link text-danger btn-sm text-decoration-none">
                                <i class="fas fa-trash-alt me-1"></i> Hapus Grup
                            </button>
                        </form>
                    </div>
                    
                    <div class="card-body p-4">
                        <div class="mb-4">
                            @forelse($item->contents as $content)
                                <div class="content-item d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3 text-center" style="width: 30px;">
                                            @if($content->type == 'link')
                                                <i class="fas fa-link text-primary"></i>
                                            @elseif($content->type == 'file')
                                                <i class="fas fa-file-pdf text-danger"></i>
                                            @else
                                                <i class="fas fa-image text-success"></i>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="fw-bold small">{{ $content->label }}</div>
                                            <div class="text-muted" style="font-size: 10px;">{{ Str::limit($content->value, 50) }}</div>
                                        </div>
                                    </div>
                                    <form action="{{ route('admin.linktree.content.destroy', $content->id) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light text-danger rounded-circle border">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                </div>
                            @empty
                                <div class="text-center py-3 bg-light rounded" style="border: 1px dashed #ccc;">
                                    <small class="text-muted">Grup ini masih kosong.</small>
                                </div>
                            @endforelse
                        </div>

                        <div class="bg-light p-3 rounded-4 border">
                            <p class="small fw-bold text-maroon mb-2"><i class="fas fa-plus-circle me-1"></i> Tambah Link/Berkas</p>
                            <form action="{{ route('admin.linktree.content.store', $item->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row g-2">
                                    <div class="col-md-5">
                                        <input type="text" name="label" class="form-control form-control-sm" placeholder="Nama Link/File (Contoh: Form Pendaftaran)" required>
                                    </div>
                                    <div class="col-md-3">
                                        <select name="type" class="form-select form-select-sm" onchange="toggleInput(this, 'input-{{$item->id}}')">
                                            <option value="link">🔗 Link URL</option>
                                            <option value="file">📄 Berkas/PDF</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4" id="input-{{$item->id}}">
                                        <div class="input-group input-group-sm">
                                            <input type="url" name="value" class="form-control" placeholder="https://..." id="val-{{$item->id}}">
                                            <input type="file" name="file" class="form-control d-none" id="file-{{$item->id}}">
                                            <button type="submit" class="btn btn-dark"><i class="fas fa-check"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5 bg-white rounded border shadow-sm">
                    <i class="fas fa-sitemap fa-3x text-muted mb-3 opacity-25"></i>
                    <h6 class="text-muted fw-bold">Belum Ada Struktur</h6>
                    <p class="small text-muted">Silakan tambahkan kategori/grup di kolom sebelah kiri.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<script>
    function toggleInput(select, containerId) {
        const container = document.getElementById(containerId);
        const urlInput = container.querySelector('input[name="value"]');
        const fileInput = container.querySelector('input[name="file"]');
        
        if (select.value === 'link') {
            urlInput.classList.remove('d-none');
            fileInput.classList.add('d-none');
            urlInput.setAttribute('required', 'required');
            fileInput.removeAttribute('required');
        } else {
            urlInput.classList.add('d-none');
            fileInput.classList.remove('d-none');
            fileInput.setAttribute('required', 'required');
            urlInput.removeAttribute('required');
        }
    }
</script>
@endsection