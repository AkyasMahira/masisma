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

    /* ===== Panel per ruangan ===== */
    .rm-hint { font-size:.72rem; color:#94a3b8; margin-bottom:8px; }
    .rm-scroll { max-height:640px; overflow:auto; padding-right:4px; }
    .rm-scroll::-webkit-scrollbar, .rm-body::-webkit-scrollbar { width:7px; }
    .rm-scroll::-webkit-scrollbar-thumb, .rm-body::-webkit-scrollbar-thumb { background:#dfe3e8; border-radius:10px; }
    .rm-card { border:1px solid #eef2f7; border-radius:12px; overflow:hidden; background:#fff; margin-bottom:8px; }
    .rm-card summary { list-style:none; cursor:pointer; display:flex; align-items:center; gap:10px; padding:10px 12px; border-left:4px solid var(--rc); user-select:none; }
    .rm-card summary::-webkit-details-marker { display:none; }
    .rm-card summary:hover { background:#fcfbfb; }
    .rm-card[open] summary { border-bottom:1px solid #f1f5f9; background:#fcfbfb; }
    .rm-card[open] summary .bi-chevron-down { transform:rotate(180deg); }
    .rm-dot { width:11px; height:11px; border-radius:4px; background:var(--rc); flex-shrink:0; }
    .rm-name { font-weight:700; color:#1f2937; font-size:.84rem; flex:1; min-width:0; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .rm-count { background:var(--maroon-subtle); color:var(--maroon); font-weight:800; font-size:.66rem; padding:2px 9px; border-radius:20px; }
    .rm-body { padding:4px 6px 6px; max-height:300px; overflow:auto; }
    .rm-line { display:flex; align-items:center; gap:10px; padding:8px 8px; border-radius:9px; cursor:pointer; transition:background .1s; }
    .rm-line + .rm-line { border-top:1px solid #f4f6f8; }
    .rm-line:hover { background:var(--maroon-subtle); }
    .rm-line .st { width:8px; height:8px; border-radius:50%; flex-shrink:0; }
    .rm-line .tx { flex:1; min-width:0; }
    .rm-line .tx b { display:block; color:#1f2937; font-size:.8rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .rm-line .tx small { display:block; color:#94a3b8; font-size:.7rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .rm-line .q { text-align:right; white-space:nowrap; font-weight:800; color:var(--maroon); font-size:.85rem; line-height:1.1; }
    .rm-line .q span { display:block; font-size:.6rem; color:#94a3b8; font-weight:700; }

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
    @php $pal = ['#7c1316','#1d4ed8','#15803d','#b45309','#7e22ce','#0e7490','#be123c','#4d7c0f','#0f766e','#9333ea']; @endphp
    <div class="col-xl-4 col-lg-5">
    <div class="bk-card h-100">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="fw-bold text-dark"><i class="bi bi-door-open me-2" style="color:var(--maroon);"></i>Per Ruangan</div>
            <span class="text-muted small">{{ count($perRuangan) }} ruangan</span>
        </div>
        <div class="rm-hint"><i class="bi bi-hand-index me-1"></i>Klik ruangan untuk memfilter kalender · klik booking untuk detail.</div>
        <div class="rm-scroll">
            @foreach($perRuangan as $nmRuangan => $list)
                @php $rc = $pal[((int) optional(reset($list))->ruangan_id) % count($pal)]; @endphp
                <details class="rm-card" data-room="{{ $nmRuangan }}" data-color="{{ $rc }}" style="--rc: {{ $rc }};">
                    <summary>
                        <span class="rm-dot"></span>
                        <span class="rm-name">{{ $nmRuangan }}</span>
                        <span class="rm-count">{{ count($list) }}</span>
                        <i class="bi bi-chevron-down text-muted small"></i>
                    </summary>
                    <div class="rm-body">
                        @foreach($list as $b)
                        @php $lbl = \App\Http\Controllers\InstansiController::prodiLabel($b); @endphp
                        <div class="rm-line" data-id="{{ $b->id }}">
                            <span class="st" style="background:{{ $b->status==='pending' ? '#f59e0b' : '#16a34a' }};"></span>
                            <span class="tx">
                                <b>{{ optional($b->mou)->nama_instansi ?? optional($b->mou)->nama_universitas ?? '-' }}</b>
                                <small>{{ $lbl ? $lbl.' · ' : '' }}{{ optional($b->tanggal_mulai)->format('d M') }} – {{ optional($b->tanggal_selesai)->format('d M y') }}</small>
                            </span>
                            <span class="q">{{ $b->jumlah_peserta }}<span>org</span></span>
                        </div>
                        @endforeach
                    </div>
                </details>
            @endforeach
        </div>
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
            <div class="modal-body px-3 py-2" id="dm-list"></div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var el = document.getElementById('bkcal');
        if (!el) return;
        var ALL = {!! json_encode($events) !!};
        var EDIT_URL = @json(route('admin.booking.edit', '__ID__'));
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
                return '<div class="mh-row"><div class="mh-av">' + esc(initials(x.nama)) + '</div><div class="mh-tx"><b>' + esc(x.nama) + '</b><small>'
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
            var list = info.ids.map(function (i) { return ALL[i]; })
                .sort(function (a, b) { return a.extendedProps.ruangan.localeCompare(b.extendedProps.ruangan); });
            document.getElementById('dm-title').innerText = date.toLocaleDateString('id-ID', { weekday:'long', day:'numeric', month:'long', year:'numeric' });
            document.getElementById('dm-sub').innerText = info.b + ' booking aktif · ' + info.o + ' orang';
            var box = document.getElementById('dm-list');
            box.innerHTML = '';
            list.forEach(function (e) {
                var p = e.extendedProps, row = document.createElement('div');
                row.className = 'day-item';
                row.innerHTML = '<span class="dd" style="background:' + esc(e.color) + '"></span><span class="tx"><b>' + esc(p.instansi) + '</b><small>'
                    + esc(p.ruangan + (p.prodi ? ' · ' + p.prodi : '') + ' · ' + p.periode) + '</small></span><span class="n">' + esc(p.peserta) + ' org</span>';
                row.addEventListener('click', function () {
                    bootstrap.Modal.getOrCreateInstance(document.getElementById('dayModal')).hide();
                    setTimeout(function () { showDetail(e); }, 250);
                });
                box.appendChild(row);
            });
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
        var cards = document.querySelectorAll('.rm-card');
        cards.forEach(function (d) {
            d.addEventListener('toggle', function () {
                if (d.open) {
                    cards.forEach(function (o) { if (o !== d) o.open = false; });
                    setRoom(d.dataset.room, d.dataset.color);
                } else if (activeRoom === d.dataset.room) {
                    setRoom(null);
                }
            });
        });
        document.querySelectorAll('.rm-line').forEach(function (l) {
            l.addEventListener('click', function () { showDetailId(l.getAttribute('data-id')); });
        });
        document.getElementById('rf-clear').addEventListener('click', function () {
            cards.forEach(function (o) { o.open = false; });
            setRoom(null);
        });
    });
</script>
@endsection
