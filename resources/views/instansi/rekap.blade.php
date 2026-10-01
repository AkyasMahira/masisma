@extends('layouts.app')

@section('title', 'Rekap & Laporan Peserta')

@section('content')
<style>
    :root { --maroon:#7c1316; --maroon-light:#a3191d; --maroon-subtle:#fcf0f1; --radius:14px; --shadow:0 4px 20px rgba(0,0,0,.05); }
    .hero { background: linear-gradient(135deg,#7c1316,#5f0f12); color:#fff; border-radius:20px; padding:24px 28px; margin-bottom:1.25rem; box-shadow:0 14px 34px rgba(124,19,22,.28); position:relative; overflow:hidden; }
    .hero::after { content:''; position:absolute; right:-40px; top:-40px; width:170px; height:170px; background:rgba(255,255,255,.07); border-radius:50%; }
    .hero h4 { font-weight:800; margin:0 0 4px; }
    .stat { background:#fff; border-radius:var(--radius); box-shadow:var(--shadow); padding:16px; text-align:center; height:100%; }
    .stat .n { font-size:1.7rem; font-weight:800; line-height:1; }
    .stat .l { color:#64748b; font-size:.72rem; font-weight:600; text-transform:uppercase; margin-top:4px; }
    .card-soft { background:#fff; border-radius:var(--radius); box-shadow:var(--shadow); overflow:hidden; }
    .table thead th { background:var(--maroon-subtle); color:var(--maroon); font-size:.74rem; text-transform:uppercase; }
    .pill { padding:3px 10px; border-radius:20px; font-size:.72rem; font-weight:700; }
    .st-selesai { background:#dcfce7; color:#15803d; } .st-jalan { background:#fef3c7; color:#b45309; }
    .nilai-badge { font-weight:800; font-size:1rem; }
    .btn-sertif { font-size:.72rem; font-weight:600; border-radius:7px; padding:4px 9px; text-decoration:none; display:inline-flex; align-items:center; gap:4px; border:none; cursor:pointer; }
    .b-magang { background:#fde8e8; color:#7c1316; } .b-magang:hover { background:#7c1316; color:#fff; }
    .b-orient { background:#e0f2fe; color:#0369a1; } .b-orient:hover { background:#0369a1; color:#fff; }
    .b-detail { background:#f1f5f9; color:#475569; } .b-detail:hover { background:#475569; color:#fff; }
    .view-tabs .vt { border:1.5px solid #e2e8f0; background:#fff; color:#64748b; border-radius:50px; padding:6px 16px; font-weight:600; font-size:.82rem; text-decoration:none; }
    .view-tabs .vt.active { background:var(--maroon); color:#fff; border-color:var(--maroon); }
    .grp-head { background:var(--maroon-subtle); color:var(--maroon); font-weight:700; padding:10px 16px; border-radius:10px; display:flex; justify-content:space-between; align-items:center; }
</style>

<div class="container-fluid py-3">
    <div class="hero d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <h4><i class="bi bi-clipboard2-data me-2"></i>Rekap & Laporan Peserta</h4>
            <div style="opacity:.9;font-size:.9rem;">{{ $mou->nama_instansi ?: $mou->nama_universitas }}</div>
        </div>
        <a href="{{ route('instansi.dashboard') }}" class="btn btn-light fw-semibold rounded-pill"><i class="bi bi-arrow-left me-1"></i> Dashboard</a>
    </div>

    @if(session('error'))<div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>@endif

    {{-- Dashboard statistik --}}
    <div class="row g-3 mb-3">
        <div class="col-6 col-lg">  <div class="stat"><div class="n" style="color:var(--maroon);">{{ $stat['total'] }}</div><div class="l">Total Peserta</div></div></div>
        <div class="col-6 col-lg">  <div class="stat"><div class="n text-success">{{ $stat['selesai'] }}</div><div class="l">Selesai</div></div></div>
        <div class="col-6 col-lg">  <div class="stat"><div class="n text-warning">{{ $stat['berjalan'] }}</div><div class="l">Berjalan</div></div></div>
        <div class="col-6 col-lg">  <div class="stat"><div class="n text-primary">{{ $stat['lulus_orientasi'] }}</div><div class="l">Lulus Orientasi</div></div></div>
        <div class="col-12 col-lg"> <div class="stat"><div class="n" style="color:var(--maroon);">{{ $stat['rata_nilai'] }}</div><div class="l">Rata-rata Nilai</div></div></div>
    </div>

    {{-- Toggle tampilan --}}
    <div class="card-soft mb-3">
        <div class="p-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div class="view-tabs d-flex flex-wrap gap-2">
                <a href="{{ route('instansi.rekap', ['q'=>request('q')]) }}" class="vt {{ $view==='semua'?'active':'' }}"><i class="bi bi-list-ul me-1"></i>Semua</a>
                <a href="{{ route('instansi.rekap', ['view'=>'prodi','q'=>request('q')]) }}" class="vt {{ $view==='prodi'?'active':'' }}"><i class="bi bi-mortarboard me-1"></i>Per Prodi</a>
                <a href="{{ route('instansi.rekap', ['view'=>'ruangan','q'=>request('q')]) }}" class="vt {{ $view==='ruangan'?'active':'' }}"><i class="bi bi-door-open me-1"></i>Per Ruangan</a>
                <a href="{{ route('instansi.rekap', ['view'=>'periode','q'=>request('q')]) }}" class="vt {{ $view==='periode'?'active':'' }}"><i class="bi bi-calendar-range me-1"></i>Per Periode</a>
            </div>
            <form method="GET" class="d-flex gap-2">
                <input type="hidden" name="view" value="{{ $view }}">
                <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm" style="max-width:240px;" placeholder="Cari nama/prodi...">
                <button class="btn btn-sm" style="background:var(--maroon);color:#fff;"><i class="bi bi-search"></i></button>
                @if(request('q'))<a href="{{ route('instansi.rekap', ['view'=>$view]) }}" class="btn btn-sm btn-light border">Reset</a>@endif
            </form>
        </div>
    </div>

    {{-- Konten --}}
    @if($view === 'semua')
        <div class="card-soft" id="tblWrap">
            @include('instansi._rekap_table', ['list' => $pg->getCollection()])
        </div>
        <div class="mt-3">{{ $pg->links() }}</div>
    @else
        <div id="tblWrap">
            @forelse($grouped as $judul => $list)
                <div class="card-soft mb-3">
                    <div class="grp-head"><span>
                        @if($view==='prodi')<i class="bi bi-mortarboard me-1"></i>@elseif($view==='ruangan')<i class="bi bi-door-open me-1"></i>@else<i class="bi bi-calendar-range me-1"></i>@endif
                        {{ $judul }}</span><span class="pill" style="background:#fff;border:1px solid var(--maroon-subtle);color:var(--maroon);">{{ count($list) }} peserta</span>
                    </div>
                    @include('instansi._rekap_table', ['list' => $list->values()])
                </div>
            @empty
                <div class="card-soft p-5 text-center text-muted"><i class="bi bi-inbox fs-3 d-block mb-2"></i>Belum ada peserta.</div>
            @endforelse
        </div>
    @endif
</div>
@endsection
