@extends('layouts.app')

@section('title', 'Galeri')
@section('page-title', 'Kelola Galeri')

@section('content')
<style>
    :root { --maroon:#7c1316; --maroon-light:#a3191d; }
    .g-head { background:linear-gradient(135deg,var(--maroon),var(--maroon-light)); color:#fff; border-radius:16px; padding:18px 22px; box-shadow:0 8px 24px rgba(124,19,22,.18); position:relative; overflow:hidden; }
    .g-head::after { content:''; position:absolute; right:-40px; top:-40px; width:160px; height:160px; background:rgba(255,255,255,.07); border-radius:50%; }
    .g-card { background:#fff; border:1px solid #eef2f7; border-radius:16px; padding:16px; box-shadow:0 2px 12px rgba(0,0,0,.04); }
    .filter-input { border-radius:10px; border:1.5px solid #e2e8f0; }
    .filter-input:focus { border-color:var(--maroon); box-shadow:0 0 0 .2rem rgba(124,19,22,.12); }
    .g-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(210px,1fr)); gap:14px; }
    .g-item { background:#fff; border:1px solid #eef2f7; border-radius:14px; overflow:hidden; box-shadow:0 2px 10px rgba(0,0,0,.04); }
    .g-item .thumb { width:100%; aspect-ratio:1/1; object-fit:cover; display:block; background:#f8fafc; }
    .g-item .body { padding:10px; }
</style>

<div class="container-fluid pb-4">

    <div class="g-head d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <h5 class="fw-bold mb-1"><i class="bi bi-images me-2"></i>Kelola Galeri</h5>
            <div class="opacity-75 small">Foto landscape maupun potrait didukung. Judul/caption opsional.</div>
        </div>
        <a href="{{ route('galeri.publik') }}" target="_blank" class="btn btn-light btn-sm rounded-pill fw-semibold"><i class="bi bi-box-arrow-up-right me-1"></i> Lihat Galeri Publik</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm"><i class="bi bi-check-circle me-1"></i>{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm"><i class="bi bi-exclamation-triangle me-1"></i>{{ $errors->first() }}</div>
    @endif

    {{-- UPLOAD --}}
    <div class="g-card mb-3">
        <div class="fw-bold text-dark mb-2"><i class="bi bi-cloud-arrow-up me-2" style="color:var(--maroon);"></i>Unggah Foto</div>
        <form action="{{ route('admin.galeri.store') }}" method="POST" enctype="multipart/form-data" class="row g-2 align-items-end">
            @csrf
            <div class="col-md-5">
                <label class="form-label small fw-semibold text-muted mb-1">Pilih Foto (bisa banyak)</label>
                <input type="file" name="foto[]" class="form-control filter-input" accept="image/*" multiple required>
            </div>
            <div class="col-md-5">
                <label class="form-label small fw-semibold text-muted mb-1">Judul / Caption (opsional)</label>
                <input type="text" name="judul" class="form-control filter-input" placeholder="mis. Pelantikan peserta magang 2026">
            </div>
            <div class="col-md-2 d-grid">
                <button type="submit" class="btn text-white fw-bold" style="background:var(--maroon);"><i class="bi bi-upload me-1"></i>Unggah</button>
            </div>
        </form>
        <small class="text-muted d-block mt-2">Format: JPG/PNG/WEBP, maksimal 5MB per foto.</small>
    </div>

    {{-- DAFTAR --}}
    @if($galeri->count())
    <div class="g-grid">
        @foreach($galeri as $g)
        <div class="g-item">
            <a href="{{ asset($g->path) }}" target="_blank"><img src="{{ asset($g->path) }}" class="thumb" alt="foto"></a>
            <div class="body">
                <form action="{{ route('admin.galeri.update', $g->id) }}" method="POST" class="d-flex gap-1 mb-2">
                    @csrf @method('PUT')
                    <input type="text" name="judul" value="{{ $g->judul }}" class="form-control form-control-sm filter-input" placeholder="Judul (opsional)">
                    <button type="submit" class="btn btn-sm btn-outline-dark" title="Simpan judul"><i class="bi bi-check-lg"></i></button>
                </form>
                <form action="{{ route('admin.galeri.destroy', $g->id) }}" method="POST" onsubmit="return confirm('Hapus foto ini?');">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger w-100"><i class="bi bi-trash me-1"></i>Hapus</button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
    @else
        <div class="g-card text-center text-muted py-5">
            <i class="bi bi-camera fs-1 d-block mb-2 opacity-50"></i>
            Belum ada foto. Unggah foto pertama di atas.
        </div>
    @endif

</div>
@endsection
