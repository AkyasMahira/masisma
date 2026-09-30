@extends('layouts.app')

@section('title', 'Master Program Studi')

@section('content')
<style>
    :root { --maroon:#7c1316; --maroon-light:#a3191d; --maroon-subtle:#fcf0f1; --radius:12px; --shadow:0 4px 20px rgba(0,0,0,.05); }
    .header-card { background:#fff; border-radius:var(--radius); border-left:5px solid var(--maroon); padding:20px; margin-bottom:20px; box-shadow:0 2px 4px rgba(0,0,0,.05); }
    .btn-maroon { background:var(--maroon); color:#fff; border:none; border-radius:8px; padding:8px 16px; font-weight:600; }
    .btn-maroon:hover { background:var(--maroon-light); color:#fff; }
    .table-card { background:#fff; border-radius:var(--radius); box-shadow:var(--shadow); overflow:hidden; }
    .table thead th { background:var(--maroon-subtle); color:var(--maroon); font-size:.78rem; text-transform:uppercase; }
    .action-btn { width:34px; height:34px; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; border:none; text-decoration:none; }
    .btn-edit { background:#fef3c7; color:#b45309; } .btn-edit:hover{ background:#b45309; color:#fff; }
    .btn-delete { background:#fee2e2; color:#b91c1c; } .btn-delete:hover{ background:#b91c1c; color:#fff; }
    .pill { padding:3px 9px; border-radius:20px; font-size:.72rem; font-weight:700; }
    .st-on { background:#dcfce7; color:#15803d; } .st-off { background:#f1f5f9; color:#64748b; }
    .badge-jen { background:#e0e7ff; color:#4338ca; padding:2px 8px; border-radius:6px; font-size:.7rem; font-weight:700; }
</style>

<div class="container-fluid py-3">
    <div class="header-card d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <h4 class="mb-1 fw-bold" style="color:var(--maroon);"><i class="bi bi-mortarboard me-2"></i>Master Program Studi</h4>
            <p class="mb-0 text-muted small">Kelola daftar program studi (dulu hardcode). Dipakai di form mahasiswa & booking instansi.</p>
        </div>
        <a href="{{ route('admin.master_prodi.create') }}" class="btn-maroon"><i class="bi bi-plus-lg me-1"></i> Tambah Prodi</a>
    </div>

    @if(session('success'))<div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>@endif

    <div class="table-card">
        <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold"><i class="bi bi-list-ul me-1"></i> Daftar Prodi ({{ $prodis->count() }})</h6>
            <input type="text" id="cari" class="form-control form-control-sm" style="max-width:260px;" placeholder="Cari prodi/kategori...">
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="tabelProdi">
                <thead><tr><th width="5%">No</th><th>Kategori</th><th>Nama Prodi</th><th class="text-center">Jenjang</th><th class="text-center">Status</th><th class="text-center" width="12%">Aksi</th></tr></thead>
                <tbody>
                    @forelse($prodis as $i => $p)
                    <tr>
                        <td class="text-muted">{{ $i+1 }}</td>
                        <td class="small text-muted">{{ $p->kategori ?: '-' }}</td>
                        <td class="fw-semibold">{{ $p->nama_prodi }}</td>
                        <td class="text-center">@if($p->jenjang)<span class="badge-jen">{{ $p->jenjang }}</span>@else - @endif</td>
                        <td class="text-center"><span class="pill {{ $p->aktif ? 'st-on' : 'st-off' }}">{{ $p->aktif ? 'Aktif' : 'Nonaktif' }}</span></td>
                        <td class="text-center">
                            <a href="{{ route('admin.master_prodi.edit', $p->id) }}" class="action-btn btn-edit" title="Edit"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('admin.master_prodi.destroy', $p->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus prodi ini?');">
                                @csrf @method('DELETE')
                                <button class="action-btn btn-delete" title="Hapus"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-5"><i class="bi bi-inbox fs-3 d-block mb-2"></i>Belum ada prodi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.getElementById('cari').addEventListener('keyup', function () {
        var q = this.value.toLowerCase();
        document.querySelectorAll('#tabelProdi tbody tr').forEach(function (tr) {
            tr.style.display = tr.innerText.toLowerCase().includes(q) ? '' : 'none';
        });
    });
</script>
@endsection
