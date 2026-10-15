@extends('layouts.app')

@section('title', 'Kalender Booking')
@section('page-title', 'Kalender Booking Ruangan')

@section('content')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>

<style>
    :root { --maroon:#7c1316; --maroon-light:#a3191d; --maroon-subtle:#fcf0f1; }
    .bk-head { background:linear-gradient(135deg,#7c1316,#5f0f12); color:#fff; border-radius:18px; padding:20px 24px; box-shadow:0 12px 30px rgba(124,19,22,.26); position:relative; overflow:hidden; }
    .bk-head::after { content:''; position:absolute; right:-40px; top:-40px; width:170px; height:170px; background:rgba(255,255,255,.07); border-radius:50%; }
    .btn-light-h { background:#fff; color:var(--maroon); border:none; border-radius:50px; font-weight:700; padding:7px 16px; text-decoration:none; display:inline-flex; align-items:center; box-shadow:0 4px 12px rgba(0,0,0,.14); }
    .btn-light-h:hover { color:var(--maroon); text-decoration:none; }
    .bk-card { background:#fff; border:1px solid #eef2f7; border-radius:16px; padding:16px; box-shadow:0 4px 20px rgba(0,0,0,.05); }
    .filter-input { border-radius:10px; border:1.5px solid #e2e8f0; font-size:.86rem; }
    .filter-input:focus { border-color:var(--maroon); box-shadow:0 0 0 .2rem rgba(124,19,22,.12); }

    /* FullCalendar */
    #bkcal { --fc-border-color:#eef1f5; --fc-today-bg-color:#fff7f7; --fc-neutral-bg-color:#fafbfc; font-size:.82rem; }
    #bkcal a { text-decoration:none !important; }
    #bkcal .fc-toolbar-title { font-size:1.12rem; font-weight:800; color:var(--maroon); }
    #bkcal .fc-button-primary { background:#fff; border-color:#e5e7eb; color:#475569; text-transform:capitalize; font-weight:700; box-shadow:none !important; }
    #bkcal .fc-button-primary:hover { background:#f8fafc; color:var(--maroon); border-color:#e5e7eb; }
    #bkcal .fc-button-primary:disabled { background:#fff; color:#cbd5e1; }
    #bkcal .fc-button-primary.fc-button-active, #bkcal .fc-button-primary:not(:disabled):active { background:var(--maroon) !important; border-color:var(--maroon) !important; color:#fff !important; }
    #bkcal .fc-col-header-cell-cushion { color:#64748b; font-weight:700; font-size:.72rem; text-transform:uppercase; letter-spacing:.3px; padding:8px 4px; }
    #bkcal .fc-daygrid-day-number { color:#334155; font-weight:600; font-size:.78rem; }
    #bkcal .fc-daygrid-day-frame { min-height:78px; }
    #bkcal .fc-event { cursor:pointer; border:none; border-radius:5px; font-size:.7rem; padding:1px 5px; font-weight:600; }
    #bkcal .fc-daygrid-more-link { font-size:.68rem; font-weight:700; color:var(--maroon); }
    #bkcal.list-mode .fc-view-harness { display:none; }

    /* heatmap */
    .heat { margin:2px 5px 5px; border-radius:8px; padding:5px 7px; cursor:pointer; line-height:1.15; transition:transform .1s, box-shadow .1s; }
    .heat:hover { transform:translateY(-1px); box-shadow:0 3px 10px rgba(124,19,22,.18); }
    .heat b { font-size:.95rem; font-weight:800; }
    .heat .u { font-size:.62rem; font-weight:700; opacity:.85; }
    .heat .o { display:block; font-size:.64rem; font-weight:600; opacity:.85; }
    .h1 { background:#fdf5f5; color:#9b2a30; } .h2 { background:#fbe4e6; color:#8a1d22; }
    .h3 { background:#f6cdd0; color:#7c1316; } .h4 { background:#eda9ae; color:#5f0f12; } .h5 { background:#d65c66; color:#fff; }
    #bkcal .fc-day-other .heat { opacity:.4; }
    #bkcal.room-mode .heat { display:none; }
    .heat-scale { display:inline-flex; align-items:center; gap:3px; font-size:.72rem; color:#64748b; font-weight:600; }
    .heat-scale i { width:16px; height:10px; border-radius:3px; display:inline-block; }
    .legend-chip { display:inline-flex; align-items:center; gap:6px; font-size:.76rem; font-weight:600; color:#475569; }
    .legend-dot { width:12px; height:12px; border-radius:4px; display:inline-block; }
    .room-filter { display:inline-flex; align-items:center; gap:6px; background:var(--maroon-subtle); color:var(--maroon); font-weight:700; font-size:.76rem; padding:4px 6px 4px 11px; border-radius:20px; }
    .room-filter button { border:none; background:#fff; color:var(--maroon); width:20px; height:20px; border-radius:50%; font-size:.7rem; line-height:1; }

    /* ===== Tampilan DAFTAR (custom) ===== */
    #bklist { display:none; }
    #bkcal.list-mode + #bklist { display:block; }
    .bl-sum { display:flex; flex-wrap:wrap; gap:8px; margin:4px 0 12px; }
    .bl-stat { background:#f8fafc; border:1px solid #eef2f7; border-radius:12px; padding:8px 14px; min-width:110px; }
    .bl-stat .v { font-size:1.15rem; font-weight:800; color:#1f2937; line-height:1.1; }
    .bl-stat .l { font-size:.68rem; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:.3px; }
    .bl-search { margin-left:auto; min-width:220px; }
    .bl-search input { border-radius:10px; border:1.5px solid #e2e8f0; font-size:.84rem; }
    .bl-group { font-size:.7rem; font-weight:800; color:#64748b; text-transform:uppercase; letter-spacing:.5px; padding:14px 4px 6px; display:flex; align-items:center; gap:8px; }
    .bl-group .gc { background:#f1f5f9; color:#475569; border-radius:20px; padding:1px 8px; font-size:.66rem; }
    .bl-row { display:grid; grid-template-columns: 4px minmax(0,1fr) 170px 120px 92px; gap:12px; align-items:center; padding:10px 12px 10px 0; border:1px solid #eef2f7; border-radius:12px; margin-bottom:6px; cursor:pointer; background:#fff; transition:border-color .12s, box-shadow .12s, transform .12s; }
    .bl-row:hover { border-color:#e3c3c6; box-shadow:0 4px 14px rgba(124,19,22,.08); transform:translateY(-1px); }
    .bl-bar { align-self:stretch; border-radius:12px 0 0 12px; margin:-10px 0; }
    .bl-ins { font-weight:700; color:#1f2937; font-size:.88rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .bl-sub { font-size:.76rem; color:#64748b; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; margin-top:2px; }
    .bl-room { display:inline-flex; align-items:center; gap:6px; font-weight:700; color:#334155; font-size:.78rem; background:#f8fafc; border:1px solid #eef0f3; border-radius:20px; padding:3px 10px; max-width:100%; overflow:hidden; white-space:nowrap; text-overflow:ellipsis; }
    .bl-room i { width:8px; height:8px; border-radius:2px; flex-shrink:0; }
    .bl-per { font-size:.76rem; color:#475569; white-space:nowrap; }
    .fase { display:inline-block; font-size:.6rem; font-weight:800; padding:1px 8px; border-radius:20px; letter-spacing:.3px; margin-top:3px; }
    .fase-Berjalan { background:#dcfce7; color:#15803d; } .fase-Selesai { background:#eef2f7; color:#64748b; } .fase-Akan { background:#dbeafe; color:#1d4ed8; }
    .bl-ppl { text-align:right; white-space:nowrap; }
    .bl-ppl b { font-size:1rem; color:var(--maroon); font-weight:800; }
    .bl-ppl small { display:block; font-size:.66rem; color:#94a3b8; font-weight:600; }
    .bl-ppl small.ok { color:#15803d; }
    .bl-empty { text-align:center; color:#94a3b8; padding:40px 10px; }
    .imp { display:inline-block; background:#eef2ff; color:#4338ca; font-size:.56rem; font-weight:800; padding:1px 5px; border-radius:4px; vertical-align:middle; margin-left:4px; }
    .pend { display:inline-block; background:#fef3c7; color:#b45309; font-size:.56rem; font-weight:800; padding:1px 5px; border-radius:4px; vertical-align:middle; margin-left:4px; }

    /* ===== Panel navigasi: Ruangan > Universitas > Prodi > Mahasiswa ===== */
    .rm-scroll { max-height:640px; overflow:auto; padding-right:4px; }
    .rm-scroll::-webkit-scrollbar { width:7px; }
    .rm-scroll::-webkit-scrollbar-thumb { background:#dfe3e8; border-radius:10px; }
    .nv-crumb { display:flex; flex-wrap:wrap; align-items:center; gap:4px; font-size:.74rem; margin-bottom:8px; min-height:22px; }
    .nv-crumb a { color:var(--maroon); font-weight:700; text-decoration:none; cursor:pointer; padding:2px 8px; border-radius:20px; background:var(--maroon-subtle); max-width:170px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .nv-crumb a:hover { background:#f6dfe1; }
    .nv-crumb span.cur { color:#334155; font-weight:700; padding:2px 4px; max-width:190px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .nv-crumb i { color:#cbd5e1; font-size:.65rem; }
    .nv-search { position:relative; margin-bottom:8px; }
    .nv-search i { position:absolute; left:10px; top:50%; transform:translateY(-50%); color:#94a3b8; font-size:.8rem; }
    .nv-search input { width:100%; border:1.5px solid #e2e8f0; border-radius:10px; padding:6px 10px 6px 30px; font-size:.8rem; outline:none; }
    .nv-search input:focus { border-color:var(--maroon); }
    .nv-item { display:flex; align-items:center; gap:10px; padding:10px 10px; border:1px solid #eef2f7; border-radius:11px; margin-bottom:6px; cursor:pointer; background:#fff; transition:border-color .12s, background .12s, transform .12s; text-decoration:none; color:inherit; }
    .nv-item:hover { border-color:#e3c3c6; background:#fffafa; transform:translateX(2px); color:inherit; }
    .nv-ic { width:34px; height:34px; border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0; font-weight:800; font-size:.74rem; }
    .nv-tx { flex:1; min-width:0; }
    .nv-tx b { display:block; color:#1f2937; font-size:.83rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .nv-tx small { display:block; color:#94a3b8; font-size:.7rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .nv-n { text-align:right; white-space:nowrap; line-height:1.1; }
    .nv-n b { color:var(--maroon); font-weight:800; font-size:.9rem; }
    .nv-n span { display:block; font-size:.6rem; color:#94a3b8; font-weight:700; }
    .nv-go { color:#cbd5e1; font-size:.8rem; }
    .nv-sec { font-size:.66rem; font-weight:800; color:#64748b; text-transform:uppercase; letter-spacing:.4px; margin:12px 2px 6px; }
    .nv-empty { background:#f8fafc; border:1px dashed #e2e8f0; border-radius:11px; padding:14px; text-align:center; color:#94a3b8; font-size:.78rem; }
    .nv-bk { display:flex; align-items:center; gap:8px; padding:8px 10px; border:1px solid #eef2f7; border-radius:10px; margin-bottom:5px; cursor:pointer; font-size:.76rem; }
    .nv-bk:hover { background:var(--maroon-subtle); border-color:#ecd3d5; }
    .nv-bk .per { flex:1; color:#475569; font-weight:600; }
    .nv-bk .o { color:var(--maroon); font-weight:800; }

    /* ===== Modal ===== */
    .md-head { background:linear-gradient(135deg,#7c1316,#5f0f12); color:#fff; padding:18px 22px; }
    .md-head .t { font-weight:800; font-size:1.05rem; line-height:1.25; }
    .md-head .s { opacity:.85; font-size:.8rem; margin-top:3px; }
    .md-pill { display:inline-block; font-size:.66rem; font-weight:800; padding:2px 9px; border-radius:20px; background:rgba(255,255,255,.18); color:#fff; margin-right:4px; }
    .md-stats { display:grid; grid-template-columns:repeat(4,1fr); gap:8px; }
    .md-stat { background:#f8fafc; border:1px solid #eef2f7; border-radius:11px; padding:9px 11px; }
    .md-stat .l { font-size:.62rem; font-weight:800; color:#94a3b8; text-transform:uppercase; letter-spacing:.3px; }
    .md-stat .v { font-size:.88rem; font-weight:800; color:#1f2937; margin-top:2px; }
    .md-sec { font-size:.7rem; font-weight:800; color:var(--maroon); text-transform:uppercase; letter-spacing:.4px; margin:18px 0 8px; display:flex; align-items:center; gap:6px; }
    .md-sec .c { background:var(--maroon-subtle); border-radius:20px; padding:1px 8px; }
    .mh-row { display:flex; align-items:center; gap:10px; padding:8px 4px; border-bottom:1px solid #f1f5f9; }
    .mh-row:last-child { border-bottom:none; }
    .mh-av { width:32px; height:32px; border-radius:50%; background:var(--maroon-subtle); color:var(--maroon); font-weight:800; font-size:.72rem; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .mh-tx { flex:1; min-width:0; }
    .mh-tx b { display:block; font-size:.84rem; color:#1f2937; }
    .mh-tx small { color:#94a3b8; font-size:.72rem; }
    .mh-st { font-size:.62rem; font-weight:800; padding:2px 8px; border-radius:20px; white-space:nowrap; }
    .mh-st.aktif, .mh-st.approved { background:#dcfce7; color:#15803d; }
    .mh-st.nonaktif, .mh-st.pending { background:#eef2f7; color:#64748b; }
    .mh-st.rejected { background:#fee2e2; color:#b91c1c; }
    .md-empty { background:#f8fafc; border:1px dashed #e2e8f0; border-radius:11px; padding:14px; text-align:center; color:#94a3b8; font-size:.8rem; }
    .day-item { display:flex; align-items:center; gap:10px; padding:9px 6px; border-bottom:1px solid #f1f5f9; font-size:.84rem; cursor:pointer; border-radius:8px; }
    .day-item:hover { background:var(--maroon-subtle); }
    .day-item:last-child { border-bottom:none; }
    .day-item .dd { width:10px; height:10px; border-radius:3px; flex-shrink:0; }
    .day-item .tx { flex:1; min-width:0; }
    .day-item .tx b { color:#1f2937; } .day-item .tx small { color:#94a3b8; display:block; }
    .day-item .n { font-weight:800; color:var(--maroon); white-space:nowrap; }

    @media (max-width: 768px) {
        .bl-row { grid-template-columns: 4px minmax(0,1fr) auto; row-gap:6px; }
        .bl-row .bl-roomc { grid-column:2; } .bl-row .bl-perc { grid-column:2; }
        .bl-row .bl-ppl { grid-column:3; grid-row:1; }
        .bl-bar { grid-row:1 / span 3; }
        .md-stats { grid-template-columns:repeat(2,1fr); }
    }
    @media (max-width: 576px) {
        .heat { margin:2px; padding:4px 2px; text-align:center; }
        .heat .u, .heat .o { display:none; }
        #bkcal .fc-daygrid-day-frame { min-height:58px; }
        #bkcal .fc-toolbar { flex-wrap:wrap; gap:6px; }
        .bl-search { margin-left:0; width:100%; }
    }
</style>

<div class="container-fluid pb-4">

    <div class="bk-head d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <h5 class="fw-bold mb-1"><i class="bi bi-calendar-week me-2"></i>Kalender Booking Ruangan</h5>
            <div class="opacity-75 small">{{ $total }} booking · klik tanggal / booking untuk detail</div>
        </div>
        <a href="{{ route('admin.booking.index') }}" class="btn-light-h"><i class="bi bi-list-ul me-1"></i> Daftar Booking</a>
    </div>

    {{-- FILTER --}}
    <form method="GET" action="{{ route('admin.booking.kalender') }}" class="bk-card mb-3">
        <div class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted mb-1">Cari (instansi/prodi)</label>
                <input type="text" name="q" value="{{ $f['q'] }}" class="form-control form-control-sm filter-input" placeholder="Nama instansi / prodi...">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted mb-1">Ruangan</label>
                <select name="ruangan" class="form-select form-select-sm filter-input">
                    <option value="">Semua Ruangan</option>
                    @foreach($ruanganOptions as $id => $nm)
                        <option value="{{ $id }}" {{ (string)$f['fRuangan']===(string)$id ? 'selected':'' }}>{{ $nm }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted mb-1">Instansi</label>
                <select name="instansi" class="form-select form-select-sm filter-input">
                    <option value="">Semua Instansi</option>
                    @foreach($instansiOptions as $id => $nm)
                        <option value="{{ $id }}" {{ (string)$f['fMou']===(string)$id ? 'selected':'' }}>{{ $nm }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label small fw-semibold text-muted mb-1">Jenjang</label>
                <select name="jenjang" class="form-select form-select-sm filter-input">
                    <option value="">Semua Jenjang</option>
                    @foreach($jenjangOptions as $j)
                        <option value="{{ $j }}" {{ $f['fJenjang']===$j ? 'selected':'' }}>{{ $j }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label small fw-semibold text-muted mb-1">Status</label>
                <select name="status" class="form-select form-select-sm filter-input">
                    <option value="">Semua Status</option>
                    <option value="approved" {{ $f['fStatus']==='approved'?'selected':'' }}>Disetujui</option>
                    <option value="pending" {{ $f['fStatus']==='pending'?'selected':'' }}>Menunggu</option>
                    <option value="berjalan" {{ $f['fStatus']==='berjalan'?'selected':'' }}>Berjalan</option>
                    <option value="akan"     {{ $f['fStatus']==='akan'?'selected':'' }}>Akan Datang</option>
                    <option value="selesai"  {{ $f['fStatus']==='selesai'?'selected':'' }}>Selesai</option>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small fw-semibold text-muted mb-1">Dari</label>
                <input type="date" name="start" value="{{ $f['fStart'] }}" class="form-control form-control-sm filter-input">
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small fw-semibold text-muted mb-1">Sampai</label>
                <input type="date" name="end" value="{{ $f['fEnd'] }}" class="form-control form-control-sm filter-input">
            </div>
            <div class="col-md-2 d-grid">
                <button type="submit" class="btn btn-sm text-white fw-semibold" style="background:var(--maroon);"><i class="bi bi-funnel me-1"></i>Terapkan</button>
            </div>
        </div>
        @if($f['q'] || $f['fRuangan'] || $f['fMou'] || $f['fJenjang'] || $f['fStatus'] || $f['fStart'] || $f['fEnd'])
            <div class="mt-2"><a href="{{ route('admin.booking.kalender') }}" class="small text-muted text-decoration-none"><i class="bi bi-x-circle me-1"></i>Reset filter</a></div>
        @endif
    </form>

    <div class="row g-3">
    <div class="{{ count($perRuangan) ? 'col-xl-8 col-lg-7' : 'col-12' }}">
    <div class="bk-card h-100">
        <div id="lg-row" class="d-flex flex-wrap gap-3 mb-3 align-items-center" style="min-height:28px;">
            <span id="lg-sum" class="heat-scale">Sepi <i class="h1"></i><i class="h2"></i><i class="h3"></i><i class="h4"></i><i class="h5"></i> Padat <span class="text-muted fw-normal ms-2">· angka = booking aktif per hari, klik untuk rincian</span></span>
            <span id="lg-room" class="d-none align-items-center gap-3">
                <span class="room-filter"><i class="bi bi-door-open"></i><span id="rf-name"></span><button type="button" id="rf-clear" title="Tampilkan semua ruangan">&times;</button></span>
                <span class="legend-chip"><span class="legend-dot" style="background:#94a3b8;"></span> Menunggu</span>
                <span class="legend-chip"><span class="legend-dot" id="rf-dot" style="background:#7c1316;"></span> Disetujui</span>
            </span>
        </div>
        <div id="bkcal"></div>
        <div id="bklist">
            <div class="bl-sum">
                <div class="bl-stat"><div class="v" id="bl-n">0</div><div class="l">Booking aktif</div></div>
                <div class="bl-stat"><div class="v" id="bl-o">0</div><div class="l">Kuota orang</div></div>
                <div class="bl-stat"><div class="v" id="bl-m">0</div><div class="l">Mahasiswa tercatat</div></div>
                <div class="bl-search"><input type="search" id="bl-q" class="form-control form-control-sm" placeholder="Cari instansi / ruangan / nama mahasiswa..."></div>
            </div>
            <div id="bl-body"></div>
        </div>
    </div>
    </div>

    {{-- PANEL PER RUANGAN --}}
    @if(count($perRuangan))
    <div class="col-xl-4 col-lg-5">
    <div class="bk-card h-100">
        <div class="d-flex justify-content-between align-items-center mb-2 gap-2">
            <div class="fw-bold text-dark text-truncate"><i class="bi bi-diagram-3 me-2" style="color:var(--maroon);"></i><span id="nv-title">Per Ruangan</span></div>
            <span class="text-muted small text-nowrap" id="nv-count"></span>
        </div>
        <div class="nv-crumb" id="nv-crumb"></div>
        <div class="nv-search"><i class="bi bi-search"></i><input type="search" id="nv-q" placeholder="Cari di daftar ini..."></div>
        <div class="rm-scroll" id="nv-body"></div>
    </div>
    </div>
    @endif
    </div>{{-- /row --}}
</div>

{{-- MODAL DETAIL BOOKING --}}
<div class="modal fade" id="bkModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
        <div class="modal-content border-0 shadow" style="border-radius:16px; overflow:hidden;">
            <div class="md-head d-flex justify-content-between align-items-start gap-3">
                <div class="min-w-0">
                    <div id="md-pills" class="mb-2"></div>
                    <div class="t" id="md-ins"></div>
                    <div class="s" id="md-sub"></div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="md-stats">
                    <div class="md-stat"><div class="l">Periode</div><div class="v" id="md-per"></div></div>
                    <div class="md-stat"><div class="l">Kuota</div><div class="v" id="md-kuota"></div></div>
                    <div class="md-stat"><div class="l">Mahasiswa tercatat</div><div class="v" id="md-nmhs"></div></div>
                    <div class="md-stat"><div class="l">Fase</div><div class="v" id="md-fase"></div></div>
                </div>
                <div id="md-ket" class="small text-muted mt-3" style="display:none;"></div>

                <div class="md-sec"><i class="bi bi-people-fill"></i>Mahasiswa di ruangan ini <span class="c" id="md-mc">0</span></div>
                <div id="md-mhs"></div>

                <div id="md-ps-wrap" style="display:none;">
                    <div class="md-sec"><i class="bi bi-person-lines-fill"></i>Peserta didaftarkan instansi <span class="c" id="md-pc">0</span></div>
                    <div id="md-ps"></div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <a id="md-edit" href="#" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil me-1"></i>Edit booking</a>
                <button type="button" class="btn btn-sm text-white fw-semibold" style="background:var(--maroon);" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL RINCIAN HARI --}}
<div class="modal fade" id="dayModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow" style="border-radius:16px; overflow:hidden;">
            <div class="md-head d-flex justify-content-between align-items-start">
                <div>
                    <div class="t"><i class="bi bi-calendar-day me-2"></i><span id="dm-title"></span></div>
                    <div class="s" id="dm-sub"></div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-3 py-3" id="dm-scroll">
                <div class="d-flex justify-content-between align-items-center mb-2 gap-2">
                    <div class="fw-bold text-dark text-truncate"><i class="bi bi-diagram-3 me-2" style="color:var(--maroon);"></i><span id="dm-lvl">Per Ruangan</span></div>
                    <span class="text-muted small text-nowrap" id="dm-count"></span>
                </div>
                <div class="nv-crumb" id="dm-crumb"></div>
                <div class="nv-search"><i class="bi bi-search"></i><input type="search" id="dm-q" placeholder="Cari di daftar ini..."></div>
                <div id="dm-body"></div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var el = document.getElementById('bkcal');
        if (!el) return;
        var ALL = {!! json_encode($events) !!};
        var EDIT_URL = @json(route('admin.booking.edit', '__ID__'));
        var MHS_URL = @json(route('mahasiswa.show', '__ID__'));
        var BY_ID = {};
        ALL.forEach(function (e) { BY_ID[e.extendedProps.id] = e; });
        var activeRoom = null, mode = 'month';

        function pad(n) { return (n < 10 ? '0' : '') + n; }
        function key(d) { return d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate()); }
        function parse(s) { var p = s.split('-'); return new Date(+p[0], +p[1] - 1, +p[2]); }
        function esc(s) { var d = document.createElement('div'); d.innerText = s == null ? '' : String(s); return d.innerHTML; }
        function initials(n) { return String(n || '?').trim().split(/\s+/).slice(0, 2).map(function (w) { return w.charAt(0); }).join('').toUpperCase(); }
        function nMhs(e) { return (e.extendedProps.mahasiswa || []).length; }

        // ===== hitung per hari =====
        var perDay = {}, maxB = 1, minB = Infinity;
        ALL.forEach(function (e, idx) {
            if (!e.start) return;
            var d = parse(e.start), end = e.end ? parse(e.end) : new Date(d.getTime() + 864e5), guard = 0;
            while (d < end && guard++ < 800) {
                var k = key(d);
                if (!perDay[k]) perDay[k] = { b: 0, o: 0, ids: [] };
                perDay[k].b++; perDay[k].o += (+e.extendedProps.peserta || 0); perDay[k].ids.push(idx);
                d.setDate(d.getDate() + 1);
            }
        });
        Object.keys(perDay).forEach(function (k) { var b = perDay[k].b; if (b > maxB) maxB = b; if (b < minB) minB = b; });
        function level(b) { if (maxB <= minB) return 3; return Math.max(1, Math.min(5, 1 + Math.floor((b - minB) / (maxB - minB + 1) * 5))); }

        // ===== modal detail =====
        function stBadge(s) {
            var map = { aktif:'Aktif', nonaktif:'Nonaktif', approved:'Disetujui', pending:'Menunggu', rejected:'Ditolak' };
            return '<span class="mh-st ' + esc(s) + '">' + esc(map[s] || s || '-') + '</span>';
        }
        function showDetail(e) {
            var p = e.extendedProps;
            var pills = '<span class="md-pill">' + (p.status === 'pending' ? 'Menunggu' : 'Disetujui') + '</span>'
                + (p.fase && p.fase !== '-' ? '<span class="md-pill">' + esc(p.fase) + '</span>' : '')
                + (p.impor ? '<span class="md-pill">Impor mapping</span>' : '');
            document.getElementById('md-pills').innerHTML = pills;
            document.getElementById('md-ins').innerText = p.instansi || '-';
            document.getElementById('md-sub').innerText = (p.ruangan || '-') + (p.prodi ? ' · ' + p.prodi : '');
            document.getElementById('md-per').innerText = e.start
                ? fmt(e.start) + ' – ' + (e.end ? fmt(key(new Date(parse(e.end).getTime() - 864e5))) + ' ' + parse(e.end).getFullYear() : '')
                : '-';
            document.getElementById('md-kuota').innerText = (p.peserta || 0) + ' orang';
            document.getElementById('md-nmhs').innerText = nMhs(e) + ' orang';
            document.getElementById('md-fase').innerText = p.fase || '-';
            var ket = document.getElementById('md-ket');
            if (p.ket) { ket.innerHTML = '<i class="bi bi-chat-left-text me-1"></i>' + esc(p.ket); ket.style.display = ''; } else { ket.style.display = 'none'; }

            var m = p.mahasiswa || [];
            document.getElementById('md-mc').innerText = m.length;
            document.getElementById('md-mhs').innerHTML = m.length ? m.map(function (x) {
                var nm = x.id ? '<a href="' + MHS_URL.replace('__ID__', x.id) + '" target="_blank" rel="noopener" style="color:#1f2937;text-decoration:none;">' + esc(x.nama) + ' <i class="bi bi-box-arrow-up-right" style="font-size:.65rem;color:#94a3b8;"></i></a>' : esc(x.nama);
                return '<div class="mh-row"><div class="mh-av">' + esc(initials(x.nama)) + '</div><div class="mh-tx"><b>' + nm + '</b><small>'
                    + esc(x.prodi || '-') + (x.mulai ? ' · ' + esc(x.mulai) + ' – ' + esc(x.selesai) : '') + '</small></div>' + stBadge(x.status) + '</div>';
            }).join('') : '<div class="md-empty"><i class="bi bi-person-dash me-1"></i>Belum ada mahasiswa dari instansi ini yang tercatat di ruangan ini pada periode tersebut.</div>';

            var ps = p.pesertaList || [];
            document.getElementById('md-ps-wrap').style.display = ps.length ? '' : 'none';
            document.getElementById('md-pc').innerText = ps.length;
            document.getElementById('md-ps').innerHTML = ps.map(function (x) {
                return '<div class="mh-row"><div class="mh-av">' + esc(initials(x.nama)) + '</div><div class="mh-tx"><b>' + esc(x.nama) + '</b><small>'
                    + esc(x.prodi || '-') + (x.nim ? ' · NIM ' + esc(x.nim) : '') + '</small></div>' + stBadge(x.status || 'pending') + '</div>';
            }).join('');

            document.getElementById('md-edit').href = EDIT_URL.replace('__ID__', p.id);
            bootstrap.Modal.getOrCreateInstance(document.getElementById('bkModal')).show();
        }
        function showDetailId(id) { if (BY_ID[id]) showDetail(BY_ID[id]); }

        function showDay(date) {
            var info = perDay[key(date)];
            if (!info) return;
            dayList = info.ids.map(function (i) { return ALL[i]; });
            if (activeRoom) dayList = dayList.filter(function (e) { return e.extendedProps.ruangan === activeRoom; });
            var s = stats(dayList);
            document.getElementById('dm-title').innerText = date.toLocaleDateString('id-ID', { weekday:'long', day:'numeric', month:'long', year:'numeric' });
            document.getElementById('dm-sub').innerText = s.b + ' booking aktif · ' + s.o + ' orang · ' + s.m + ' mahasiswa tercatat';
            // Bila kalender sedang difilter ke satu ruangan, langsung buka tingkat universitas
            dayNav.go(activeRoom ? [activeRoom] : []);
            bootstrap.Modal.getOrCreateInstance(document.getElementById('dayModal')).show();
        }

        // ===== tampilan daftar (custom) =====
        var MONTHS = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        function fmt(s) { var d = parse(s); return pad(d.getDate()) + ' ' + MONTHS[d.getMonth()]; }
        function renderList() {
            if (!cal) return;
            var vs = cal.view.currentStart, ve = cal.view.currentEnd;
            var q = (document.getElementById('bl-q').value || '').toLowerCase().trim();
            var rows = ALL.filter(function (e) {
                if (!e.start) return false;
                var s = parse(e.start), en = e.end ? parse(e.end) : s;
                if (!(s < ve && en > vs)) return false;
                if (activeRoom && e.extendedProps.ruangan !== activeRoom) return false;
                if (q) {
                    var p = e.extendedProps, hay = (p.instansi + ' ' + p.ruangan + ' ' + p.prodi + ' ' + (p.mahasiswa || []).map(function (x) { return x.nama; }).join(' ')).toLowerCase();
                    if (hay.indexOf(q) < 0) return false;
                }
                return true;
            }).sort(function (a, b) { return a.start < b.start ? -1 : a.start > b.start ? 1 : a.extendedProps.ruangan.localeCompare(b.extendedProps.ruangan); });

            var o = 0, mm = 0;
            rows.forEach(function (e) { o += (+e.extendedProps.peserta || 0); mm += nMhs(e); });
            document.getElementById('bl-n').innerText = rows.length;
            document.getElementById('bl-o').innerText = o;
            document.getElementById('bl-m').innerText = mm;

            var body = document.getElementById('bl-body');
            if (!rows.length) { body.innerHTML = '<div class="bl-empty"><i class="bi bi-calendar-x fs-3 d-block mb-2"></i>Tidak ada booking pada bulan ini.</div>'; return; }

            var groups = { 'Berjalan': [], 'Akan Datang': [], 'Selesai': [], '-': [] };
            rows.forEach(function (e) { (groups[e.extendedProps.fase] || groups['-']).push(e); });
            var html = '';
            ['Berjalan', 'Akan Datang', 'Selesai', '-'].forEach(function (g) {
                if (!groups[g].length) return;
                html += '<div class="bl-group">' + (g === '-' ? 'Lainnya' : g) + ' <span class="gc">' + groups[g].length + '</span></div>';
                groups[g].forEach(function (e) {
                    var p = e.extendedProps, n = nMhs(e), fc = (p.fase || '').split(' ')[0];
                    html += '<div class="bl-row" data-id="' + esc(p.id) + '">'
                        + '<span class="bl-bar" style="background:' + esc(e.color) + '"></span>'
                        + '<div class="min-w-0" style="min-width:0"><div class="bl-ins">' + esc(p.instansi)
                        + (p.status === 'pending' ? '<span class="pend">MENUNGGU</span>' : '') + (p.impor ? '<span class="imp">IMPOR</span>' : '') + '</div>'
                        + '<div class="bl-sub">' + esc(p.prodi || '-') + '</div></div>'
                        + '<div class="bl-roomc" style="min-width:0"><span class="bl-room"><i style="background:' + esc(e.color) + '"></i>' + esc(p.ruangan) + '</span></div>'
                        + '<div class="bl-perc"><div class="bl-per">' + fmt(e.start) + ' – ' + (e.end ? fmt(key(new Date(parse(e.end).getTime() - 864e5))) : '-') + '</div>'
                        + (p.fase && p.fase !== '-' ? '<span class="fase fase-' + esc(fc) + '">' + esc(p.fase) + '</span>' : '') + '</div>'
                        + '<div class="bl-ppl"><b>' + esc(p.peserta) + '</b> <span class="text-muted small">org</span>'
                        + '<small class="' + (n ? 'ok' : '') + '">' + (n ? '<i class="bi bi-person-check"></i> ' + n + ' tercatat' : 'belum tercatat') + '</small></div>'
                        + '</div>';
                });
            });
            body.innerHTML = html;
        }
        document.getElementById('bl-body').addEventListener('click', function (ev) {
            var r = ev.target.closest('.bl-row'); if (r) showDetailId(r.getAttribute('data-id'));
        });
        document.getElementById('bl-q').addEventListener('input', renderList);

        function setMode(m) {
            mode = m;
            el.classList.toggle('list-mode', m === 'list');
            var bb = el.querySelector('.fc-bulan-button'), db = el.querySelector('.fc-daftar-button');
            if (bb) bb.classList.toggle('fc-button-active', m === 'month');
            if (db) db.classList.toggle('fc-button-active', m === 'list');
            document.getElementById('lg-row').style.display = (m === 'list' && !activeRoom) ? 'none' : '';
            if (m === 'list') renderList();
        }

        var cal = new FullCalendar.Calendar(el, {
            initialView: 'dayGridMonth',
            locale: 'id',
            height: 'auto',
            headerToolbar: { left:'prev,next today', center:'title', right:'bulan,daftar' },
            customButtons: {
                bulan:  { text:'Bulan',  click: function () { setMode('month'); } },
                daftar: { text:'Daftar', click: function () { setMode('list'); } }
            },
            buttonText: { today:'Hari Ini' },
            dayMaxEvents: 4,
            displayEventTime: false,
            events: function (info, ok) {
                if (!activeRoom) return ok([]);
                ok(ALL.filter(function (e) { return e.extendedProps.ruangan === activeRoom; })
                    .map(function (e) { return Object.assign({}, e, { title: e.extendedProps.instansi + ' (' + e.extendedProps.peserta + ' org)' }); }));
            },
            datesSet: function () { if (mode === 'list') renderList(); },
            dayCellDidMount: function (arg) {
                var info = perDay[key(arg.date)];
                if (!info) return;
                var h = document.createElement('div');
                h.className = 'heat h' + level(info.b);
                h.title = info.b + ' booking aktif · ' + info.o + ' orang — klik untuk rincian';
                h.innerHTML = '<b>' + info.b + '</b> <span class="u">booking</span><span class="o">' + info.o + ' orang</span>';
                h.addEventListener('click', function (ev) { ev.stopPropagation(); showDay(arg.date); });
                var frame = arg.el.querySelector('.fc-daygrid-day-frame') || arg.el;
                var top = frame.querySelector('.fc-daygrid-day-top');
                if (top && top.nextSibling) frame.insertBefore(h, top.nextSibling); else frame.appendChild(h);
            },
            eventClick: function (info) { showDetailId(info.event.extendedProps.id); }
        });
        cal.render();
        setMode('month');

        // ===== filter ruangan dari panel =====
        function setRoom(name, color) {
            activeRoom = name;
            el.classList.toggle('room-mode', !!name);
            var lr = document.getElementById('lg-room');
            lr.classList.toggle('d-none', !name); lr.classList.toggle('d-inline-flex', !!name);
            document.getElementById('lg-sum').classList.toggle('d-none', !!name);
            if (name) {
                document.getElementById('rf-name').innerText = name;
                document.getElementById('rf-dot').style.background = color || '#7c1316';
            }
            cal.refetchEvents();
            setMode(mode);
        }

        // ===== Navigasi bertingkat: Ruangan > Universitas > Prodi > Mahasiswa =====
        // Dipakai di panel kanan (semua booking) dan di popup tanggal (booking aktif hari itu).
        var PRODI_KOSONG = '(Prodi tidak diisi)';
        var LEVEL = ['Per Ruangan', 'Universitas', 'Program Studi', 'Mahasiswa'];
        var dayList = [];

        function groupBy(list, fn) {
            var g = {};
            list.forEach(function (e) { var k = fn(e); (g[k] = g[k] || []).push(e); });
            return g;
        }
        function stats(list) {
            var o = 0, ids = {};
            list.forEach(function (e) {
                o += (+e.extendedProps.peserta || 0);
                (e.extendedProps.mahasiswa || []).forEach(function (m) { ids[m.id || m.nama] = 1; });
            });
            return { b: list.length, o: o, m: Object.keys(ids).length };
        }

        function Navigator(cfg) {
            var path = [], current = [];
            var $ = function (id) { return document.getElementById(id); };
            var body = $(cfg.body), crumb = $(cfg.crumb), search = $(cfg.search);

            function inPath(e) {
                var p = e.extendedProps;
                if (path[0] !== undefined && p.ruangan !== path[0]) return false;
                if (path[1] !== undefined && p.instansi !== path[1]) return false;
                if (path[2] !== undefined && (p.prodi || PRODI_KOSONG) !== path[2]) return false;
                return true;
            }
            function itemHtml(idx, icon, iconStyle, title, sub, nb, nl) {
                return '<div class="nv-item" data-i="' + idx + '"><div class="nv-ic" style="' + iconStyle + '">' + icon + '</div>'
                    + '<div class="nv-tx"><b>' + esc(title) + '</b><small>' + esc(sub) + '</small></div>'
                    + '<div class="nv-n"><b>' + esc(nb) + '</b><span>' + esc(nl) + '</span></div><i class="bi bi-chevron-right nv-go"></i></div>';
            }
            function renderCrumb() {
                if (!path.length) { crumb.innerHTML = '<span class="text-muted">Klik ruangan → universitas → prodi → mahasiswa</span>'; return; }
                var h = '<a data-lv="0">Semua ruangan</a>';
                path.forEach(function (p, i) {
                    h += '<i class="bi bi-chevron-right"></i>';
                    h += (i === path.length - 1) ? '<span class="cur">' + esc(p) + '</span>' : '<a data-lv="' + (i + 1) + '">' + esc(p) + '</a>';
                });
                crumb.innerHTML = h;
            }
            function go(newPath) {
                var roomBefore = path[0];
                path = newPath;
                search.value = '';
                if (cfg.onRoom && path[0] !== roomBefore) {
                    var col = '#7c1316';
                    if (path[0]) { var f = ALL.filter(function (e) { return e.extendedProps.ruangan === path[0]; })[0]; if (f) col = f.extendedProps.rc || f.color; }
                    cfg.onRoom(path[0] || null, col);
                }
                render();
                var sc = $(cfg.scroll || cfg.body); if (sc) sc.scrollTop = 0;
            }
            function render() {
                renderCrumb();
                $(cfg.title).innerText = LEVEL[path.length];
                var q = (search.value || '').toLowerCase().trim();
                var list = cfg.base().filter(inPath), html = '';
                current = [];

                if (path.length < 3) {
                    var keyFn = [
                        function (e) { return e.extendedProps.ruangan; },
                        function (e) { return e.extendedProps.instansi; },
                        function (e) { return e.extendedProps.prodi || PRODI_KOSONG; }
                    ][path.length];
                    var g = groupBy(list, keyFn);
                    var keys = Object.keys(g).sort(function (a, b) {
                        if (path.length === 1) return g[b].length - g[a].length || a.localeCompare(b);
                        return a.localeCompare(b);
                    });
                    if (q) keys = keys.filter(function (k) { return k.toLowerCase().indexOf(q) >= 0; });
                    $(cfg.count).innerText = keys.length + ' ' + ['ruangan', 'instansi', 'prodi'][path.length];
                    keys.forEach(function (k, i) {
                        var s = stats(g[k]), e0 = g[k][0].extendedProps, icon, st, sub;
                        if (path.length === 0) {
                            var nu = Object.keys(groupBy(g[k], function (e) { return e.extendedProps.instansi; })).length;
                            var rc = e0.rc || '#7c1316';
                            icon = '<i class="bi bi-door-open"></i>'; st = 'background:' + rc + '1a;color:' + rc;
                            sub = nu + ' instansi · ' + s.b + ' booking · ' + s.m + ' mhs tercatat';
                        } else if (path.length === 1) {
                            var np = Object.keys(groupBy(g[k], function (e) { return e.extendedProps.prodi || PRODI_KOSONG; })).length;
                            icon = esc(initials(k)); st = 'background:#eef2ff;color:#4338ca';
                            sub = np + ' prodi · ' + s.b + ' booking · ' + s.m + ' mhs tercatat';
                        } else {
                            icon = '<i class="bi bi-mortarboard"></i>'; st = 'background:#ecfdf5;color:#047857';
                            var per = g[k].map(function (e) { return fmt(e.start); });
                            sub = s.b + ' booking · mulai ' + per.slice(0, 2).join(', ') + (per.length > 2 ? '…' : '') + ' · ' + s.m + ' mhs';
                        }
                        current.push(k);
                        html += itemHtml(i, icon, st, k, sub, s.o, 'org');
                    });
                    if (!keys.length) html = '<div class="nv-empty">Tidak ada data.</div>';
                } else {
                    var seen = {}, mhs = [];
                    list.forEach(function (e) {
                        (e.extendedProps.mahasiswa || []).forEach(function (m) {
                            var k = m.id || m.nama;
                            if (!seen[k]) { seen[k] = 1; mhs.push(m); }
                        });
                    });
                    mhs.sort(function (a, b) { return String(a.nama).localeCompare(String(b.nama)); });
                    if (q) mhs = mhs.filter(function (m) { return String(m.nama).toLowerCase().indexOf(q) >= 0; });
                    $(cfg.count).innerText = mhs.length + ' mahasiswa';
                    if (mhs.length) {
                        mhs.forEach(function (m) {
                            var href = m.id ? MHS_URL.replace('__ID__', m.id) : null;
                            var st = { aktif: 'Aktif', nonaktif: 'Nonaktif' }[m.status] || (m.status || '-');
                            html += (href ? '<a class="nv-item" href="' + href + '" target="_blank" rel="noopener">' : '<div class="nv-item">')
                                + '<div class="nv-ic" style="background:var(--maroon-subtle);color:var(--maroon);border-radius:50%">' + esc(initials(m.nama)) + '</div>'
                                + '<div class="nv-tx"><b>' + esc(m.nama) + '</b><small>' + esc((m.prodi || '-') + (m.mulai ? ' · ' + m.mulai + ' – ' + m.selesai : '')) + '</small></div>'
                                + '<span class="mh-st ' + esc(m.status) + '">' + esc(st) + '</span>'
                                + (href ? '<i class="bi bi-box-arrow-up-right nv-go"></i></a>' : '</div>');
                        });
                    } else {
                        html += '<div class="nv-empty"><i class="bi bi-person-dash me-1"></i>Belum ada mahasiswa dari instansi ini yang tercatat di ruangan ini.</div>';
                    }
                    html += '<div class="nv-sec">Booking (' + list.length + ')</div>';
                    list.slice().sort(function (a, b) { return a.start < b.start ? -1 : 1; }).forEach(function (e) {
                        var p = e.extendedProps;
                        html += '<div class="nv-bk" data-id="' + esc(p.id) + '"><i class="bi bi-calendar3 text-muted"></i><span class="per">'
                            + fmt(e.start) + ' – ' + (e.end ? fmt(key(new Date(parse(e.end).getTime() - 864e5))) : '-')
                            + ' <span class="fase fase-' + esc((p.fase || '').split(' ')[0]) + '">' + esc(p.fase || '') + '</span></span><span class="o">' + esc(p.peserta) + ' org</span></div>';
                    });
                }
                body.innerHTML = html;
            }

            body.addEventListener('click', function (ev) {
                var bk = ev.target.closest('.nv-bk');
                if (bk) { cfg.onBooking(bk.getAttribute('data-id')); return; }
                var it = ev.target.closest('.nv-item[data-i]');
                if (it) { var k = current[+it.getAttribute('data-i')]; if (k !== undefined) go(path.concat([k])); }
            });
            crumb.addEventListener('click', function (ev) {
                var a = ev.target.closest('a[data-lv]');
                if (a) go(path.slice(0, +a.getAttribute('data-lv')));
            });
            search.addEventListener('input', render);
            return { go: go, render: render };
        }

        // Popup tanggal (klik kotak di kalender)
        var dayNav = Navigator({
            body: 'dm-body', crumb: 'dm-crumb', search: 'dm-q', title: 'dm-lvl', count: 'dm-count', scroll: 'dm-scroll',
            base: function () { return dayList; },
            onRoom: null,
            onBooking: function (id) {
                bootstrap.Modal.getOrCreateInstance(document.getElementById('dayModal')).hide();
                setTimeout(function () { showDetailId(id); }, 250);
            }
        });

        // Panel kanan
        var panelNav = null;
        if (document.getElementById('nv-body')) {
            panelNav = Navigator({
                body: 'nv-body', crumb: 'nv-crumb', search: 'nv-q', title: 'nv-title', count: 'nv-count',
                base: function () { return ALL; },
                onRoom: setRoom,
                onBooking: showDetailId
            });
            panelNav.go([]);
        }
        document.getElementById('rf-clear').addEventListener('click', function () {
            if (panelNav) panelNav.go([]); else setRoom(null);
        });
    });
</script>
@endsection
