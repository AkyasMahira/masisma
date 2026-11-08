@extends('layouts.app')

@section('title', 'Master Program Studi')

@section('content')
<style>
    :root { --maroon:#7c1316; --maroon-light:#a3191d; --maroon-subtle:#fcf0f1; --ink:#1f2937; --muted:#6b7280; --line:#eceef1; --radius:14px; --shadow:0 6px 24px rgba(124,19,22,.07); }

    .page-head { background:linear-gradient(135deg,var(--maroon) 0%,var(--maroon-light) 100%); color:#fff; border-radius:var(--radius); padding:22px 24px; margin-bottom:18px; box-shadow:var(--shadow); }
    .page-head h4 { font-weight:800; margin:0 0 4px; }
    .page-head p { margin:0; opacity:.85; font-size:.88rem; }
    .btn-white { background:#fff; color:var(--maroon); border:none; border-radius:10px; padding:9px 18px; font-weight:700; text-decoration:none; transition:.15s; }
    .btn-white:hover { background:var(--maroon-subtle); color:var(--maroon); transform:translateY(-1px); }

    .stat-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:14px; margin-bottom:18px; }
    .stat { background:#fff; border-radius:var(--radius); padding:14px 18px; box-shadow:var(--shadow); text-decoration:none; color:var(--ink); border:2px solid transparent; transition:.15s; display:block; }
    .stat:hover { transform:translateY(-2px); }
    .stat.on { border-color:var(--maroon); }
    .stat small { color:var(--muted); font-weight:600; text-transform:uppercase; font-size:.7rem; letter-spacing:.04em; }
    .stat strong { display:block; font-size:1.6rem; line-height:1.2; color:var(--maroon); }

    .table-card { background:#fff; border-radius:var(--radius); box-shadow:var(--shadow); overflow:hidden; }
    .filter-bar { display:flex; flex-wrap:wrap; gap:10px; align-items:center; padding:16px; border-bottom:1px solid var(--line); background:#fcfcfd; }
    .search-box { position:relative; flex:1 1 260px; min-width:220px; }
    .search-box i { position:absolute; left:13px; top:50%; transform:translateY(-50%); color:var(--muted); }
    .search-box input { width:100%; padding:9px 14px 9px 38px; border:1px solid #dfe3e8; border-radius:10px; font-size:.9rem; transition:.15s; }
    .search-box input:focus, .filter-select:focus { outline:none; border-color:var(--maroon); box-shadow:0 0 0 3px rgba(124,19,22,.12); }
    .filter-select { width:auto; min-width:140px; border-radius:10px; border-color:#dfe3e8; font-size:.88rem; padding-top:8px; padding-bottom:8px; }
    .btn-reset { color:var(--maroon); font-weight:600; font-size:.85rem; text-decoration:none; padding:8px 12px; border-radius:10px; }
    .btn-reset:hover { background:var(--maroon-subtle); color:var(--maroon); }

    .table { margin:0; }
    .table thead th { background:var(--maroon-subtle); color:var(--maroon); font-size:.72rem; text-transform:uppercase; letter-spacing:.05em; border:none; padding:13px 14px; white-space:nowrap; }
    .table tbody td { padding:13px 14px; border-color:var(--line); }
    .table tbody tr:hover { background:#fff8f8; }
    .nama { font-weight:700; color:var(--ink); }

    .pill { padding:4px 10px; border-radius:20px; font-size:.72rem; font-weight:700; display:inline-flex; align-items:center; gap:5px; }
    .pill::before { content:''; width:6px; height:6px; border-radius:50%; background:currentColor; }
    .st-on { background:#dcfce7; color:#15803d; } .st-off { background:#f1f5f9; color:#64748b; }
    .badge-jen { background:#e0e7ff; color:#4338ca; padding:3px 9px; border-radius:6px; font-size:.72rem; font-weight:700; }
    .tag-kat { background:#f3f4f6; color:#4b5563; padding:3px 9px; border-radius:6px; font-size:.75rem; font-weight:600; }

    .action-btn { width:34px; height:34px; display:inline-flex; align-items:center; justify-content:center; border-radius:9px; border:none; text-decoration:none; transition:.15s; }
    .btn-edit { background:#fef3c7; color:#b45309; } .btn-edit:hover { background:#b45309; color:#fff; }
    .btn-delete { background:#fee2e2; color:#b91c1c; } .btn-delete:hover { background:#b91c1c; color:#fff; }

    .table-foot { display:flex; flex-wrap:wrap; justify-content:space-between; align-items:center; gap:10px; padding:14px 16px; border-top:1px solid var(--line); }
    .pagination { margin:0; gap:4px; }
    .pagination .page-link { border:none; border-radius:9px !important; color:var(--maroon); font-weight:700; font-size:.85rem; padding:7px 13px; }
    .pagination .page-link:hover { background:var(--maroon-subtle); }
    .pagination .page-item.active .page-link { background:var(--maroon); color:#fff; box-shadow:0 3px 10px rgba(124,19,22,.3); }
    .pagination .page-item.disabled .page-link { color:#c3c7ce; background:transparent; }

    @media (max-width:576px) { .stat-grid { grid-template-columns:1fr; } .filter-select { flex:1 1 45%; } }
</style>

@php
    $hasFilter = $q !== '' || $kategori || $jenjang || $status !== null;
@endphp

<div class="container-fluid py-3">
    <div class="page-head d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <h4><i class="bi bi-mortarboard me-2"></i>Master Program Studi</h4>
            <p>Kelola daftar program studi. Dipakai di form mahasiswa & booking instansi.</p>
        </div>
        <a href="{{ route('admin.master_prodi.create') }}" class="btn-white"><i class="bi bi-plus-lg me-1"></i> Tambah Prodi</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

 <div class="stat-grid">
    <a href="{{ route('admin.master_prodi.index') }}" class="stat {{ $status === null ? 'on' : '' }}"><small>Total prodi</small><strong>{{ $stat['total'] }}</strong></a>
    <a href="{{ route('admin.master_prodi.index', ['status' => 1]) }}" class="stat {{ $status === '1' ? 'on' : '' }}"><small>Aktif</small><strong>{{ $stat['aktif'] }}</strong></a>
    <a href="{{ route('admin.master_prodi.index', ['status' => 0]) }}" class="stat {{ $status === '0' ? 'on' : '' }}"><small>Nonaktif</small><strong>{{ $stat['nonaktif'] }}</strong></a>
</div>

    <div class="table-card">
        <form method="GET" action="{{ route('admin.master_prodi.index') }}" id="filterForm" class="filter-bar">
            <div class="search-box">
                <i class="bi bi-search"></i>
                <input type="text" name="q" id="q" value="{{ $q }}" placeholder="Cari nama prodi atau kategori..." autocomplete="off">
            </div>

            <select name="kategori" class="form-select form-select-sm filter-select">
                <option value="">Semua kategori</option>
                @foreach($kategoris as $k)
                    <option value="{{ $k }}" {{ $kategori === $k ? 'selected' : '' }}>{{ $k }}</option>
                @endforeach
            </select>

            <select name="jenjang" class="form-select form-select-sm filter-select">
                <option value="">Semua jenjang</option>
                @foreach($jenjangs as $j)
                    <option value="{{ $j }}" {{ $jenjang === $j ? 'selected' : '' }}>{{ $j }}</option>
                @endforeach
            </select>

            <select name="status" class="form-select form-select-sm filter-select">
                <option value="">Semua status</option>
                <option value="1" {{ $status === '1' ? 'selected' : '' }}>Aktif</option>
                <option value="0" {{ $status === '0' ? 'selected' : '' }}>Nonaktif</option>
            </select>

            <select name="per_page" class="form-select form-select-sm filter-select" title="Baris per halaman">
                @foreach([10, 25, 50, 100] as $n)
                    <option value="{{ $n }}" {{ $perPage === $n ? 'selected' : '' }}>{{ $n }} / halaman</option>
                @endforeach
            </select>

            @if($hasFilter)
                <a href="{{ route('admin.master_prodi.index') }}" class="btn-reset"><i class="bi bi-x-circle me-1"></i>Reset</a>
            @endif
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th>Nama Prodi</th>
                        <th>Kategori</th>
                        <th class="text-center">Jenjang</th>
                        <th class="text-center">Status</th>
                        <th class="text-center" width="12%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($prodis as $i => $p)
                    <tr>
                        <td class="text-muted">{{ $prodis->firstItem() + $i }}</td>
                        <td class="nama">{{ $p->nama_prodi }}</td>
                        <td>@if($p->kategori)<span class="tag-kat">{{ $p->kategori }}</span>@else <span class="text-muted">-</span> @endif</td>
                        <td class="text-center">@if($p->jenjang)<span class="badge-jen">{{ $p->jenjang }}</span>@else <span class="text-muted">-</span> @endif</td>
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
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
                            <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                            {{ $hasFilter ? 'Tidak ada prodi yang cocok dengan filter.' : 'Belum ada prodi.' }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

     <div class="table-foot">
    <small class="text-muted">
        @if($prodis->total())
            Menampilkan <b>{{ $prodis->firstItem() }}–{{ $prodis->lastItem() }}</b> dari <b>{{ $prodis->total() }}</b> prodi
        @else
            Tidak ada data
        @endif
    </small>

    {{-- PAGINASI LANGSUNG DI DALAM BLADE --}}
    @if ($prodis->hasPages())
        <nav aria-label="Navigasi halaman">
            <ul class="pagination">
                {{-- Tombol Previous --}}
                @if ($prodis->onFirstPage())
                    <li class="page-item disabled"><span class="page-link"><i class="bi bi-chevron-left"></i></span></li>
                @else
                    <li class="page-item"><a class="page-link" href="{{ $prodis->previousPageUrl() }}" rel="prev"><i class="bi bi-chevron-left"></i></a></li>
                @endif

                {{-- Nomor Halaman (Menggunakan linkCollection agar aman & rapi) --}}
                @foreach ($prodis->linkCollection() as $link)
                    @if ($link['url'] === null)
                        <li class="page-item disabled"><span class="page-link">{!! $link['label'] !!}</span></li>
                    @else
                        @if ($link['active'])
                            <li class="page-item active"><span class="page-link">{{ $link['label'] }}</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $link['url'] }}">{{ $link['label'] }}</a></li>
                        @endif
                    @endif
                @endforeach

                {{-- Tombol Next --}}
                @if ($prodis->hasMorePages())
                    <li class="page-item"><a class="page-link" href="{{ $prodis->nextPageUrl() }}" rel="next"><i class="bi bi-chevron-right"></i></a></li>
                @else
                    <li class="page-item disabled"><span class="page-link"><i class="bi bi-chevron-right"></i></span></li>
                @endif
            </ul>
        </nav>
    @endif
</div>
    </div>
</div>

<script>
    (function () {
        var form = document.getElementById('filterForm');
        var q = document.getElementById('q');
        var timer;

        // Cari otomatis setelah berhenti mengetik
        q.addEventListener('input', function () {
            clearTimeout(timer);
            timer = setTimeout(function () { form.submit(); }, 450);
        });
        // Dropdown langsung menerapkan filter
        form.querySelectorAll('select').forEach(function (s) {
            s.addEventListener('change', function () { form.submit(); });
        });
        // Kembalikan fokus ke kolom cari setelah halaman dimuat ulang
        if (q.value) { q.focus(); q.setSelectionRange(q.value.length, q.value.length); }
    })();
</script>
@endsection