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

    /* FullCalendar polish */
    #bkcal { --fc-border-color:#eef1f5; --fc-today-bg-color:#fff7f7; --fc-neutral-bg-color:#fafbfc; font-size:.82rem; }
    #bkcal .fc-toolbar-title { font-size:1.12rem; font-weight:800; color:var(--maroon); }
    #bkcal .fc-button-primary { background:var(--maroon); border-color:var(--maroon); text-transform:capitalize; font-weight:600; box-shadow:none; }
    #bkcal .fc-button-primary:hover { background:var(--maroon-light); border-color:var(--maroon-light); }
    #bkcal .fc-button-primary:not(:disabled).fc-button-active { background:#5f0f12; border-color:#5f0f12; }
    #bkcal .fc-col-header-cell-cushion { color:#64748b; font-weight:700; font-size:.72rem; text-transform:uppercase; letter-spacing:.3px; padding:8px 4px; }
    #bkcal .fc-daygrid-day-number { color:#334155; font-weight:600; font-size:.78rem; }
    #bkcal .fc-event { cursor:pointer; border:none; border-radius:5px; font-size:.7rem; padding:1px 5px; font-weight:600; }
    #bkcal .fc-daygrid-event-dot { display:none; }
    #bkcal .fc-daygrid-more-link { font-size:.68rem; font-weight:700; color:var(--maroon); }
    #bkcal .fc-list-event:hover td { background:var(--maroon-subtle); }
    #bkcal .fc-list-event-dot { border-width:6px; }
    #bkcal .fc-list-day-cushion { background:var(--maroon-subtle); }
    #bkcal .fc-list-day-text, #bkcal .fc-list-day-side-text { color:var(--maroon); font-weight:800; }
    #bkcal a { text-decoration:none !important; }
    #bkcal .fc-daygrid-day-frame { min-height:78px; }

    /* heatmap ringkasan per hari */
    .heat { margin:2px 5px 5px; border-radius:8px; padding:5px 7px; cursor:pointer; line-height:1.15; transition:transform .1s, box-shadow .1s; }
    .heat:hover { transform:translateY(-1px); box-shadow:0 3px 10px rgba(124,19,22,.18); }
    .heat b { font-size:.95rem; font-weight:800; }
    .heat .u { font-size:.62rem; font-weight:700; opacity:.85; }
    .heat .o { display:block; font-size:.64rem; font-weight:600; opacity:.85; }
    .h1 { background:#fdf5f5; color:#9b2a30; } .h2 { background:#fbe4e6; color:#8a1d22; }
    .h3 { background:#f6cdd0; color:#7c1316; } .h4 { background:#eda9ae; color:#5f0f12; } .h5 { background:#d65c66; color:#fff; }
    #bkcal .fc-day-other .heat { opacity:.4; }
    @media (max-width: 576px) {
        .heat { margin:2px; padding:4px 2px; text-align:center; }
        .heat .u, .heat .o { display:none; }
        #bkcal .fc-daygrid-day-frame { min-height:58px; }
        #bkcal .fc-toolbar { flex-wrap:wrap; gap:6px; }
    }
    #bkcal.room-mode .heat { display:none; }
    .heat-scale { display:inline-flex; align-items:center; gap:3px; font-size:.72rem; color:#64748b; font-weight:600; }
    .heat-scale i { width:16px; height:10px; border-radius:3px; display:inline-block; }
    .room-filter { display:inline-flex; align-items:center; gap:6px; background:var(--maroon-subtle); color:var(--maroon); font-weight:700; font-size:.76rem; padding:4px 6px 4px 11px; border-radius:20px; }
    .room-filter button { border:none; background:#fff; color:var(--maroon); width:20px; height:20px; border-radius:50%; font-size:.7rem; line-height:1; }
    .day-item { display:flex; align-items:center; gap:9px; padding:8px 2px; border-bottom:1px solid #f1f5f9; font-size:.84rem; }
    .day-item:last-child { border-bottom:none; }
    .day-item .dd { width:10px; height:10px; border-radius:3px; flex-shrink:0; }
    .day-item .tx { flex:1; min-width:0; }
    .day-item .tx b { color:#1f2937; } .day-item .tx small { color:#94a3b8; display:block; }
    .day-item .n { font-weight:800; color:var(--maroon); white-space:nowrap; }
    .rm-card summary { user-select:none; }
    .rm-hint { font-size:.72rem; color:#94a3b8; margin-bottom:8px; }

    .legend-chip { display:inline-flex; align-items:center; gap:6px; font-size:.76rem; font-weight:600; color:#475569; }
    .legend-dot { width:12px; height:12px; border-radius:4px; display:inline-block; }
    .bio-row { display:flex; justify-content:space-between; gap:10px; padding:7px 0; border-bottom:1px solid #f1f5f9; font-size:.86rem; }
    .bio-row .lbl { color:#94a3b8; } .bio-row .val { font-weight:700; color:#1f2937; text-align:right; }

    /* per-ruangan panel (samping kalender) */
    .rm-scroll { max-height:640px; overflow:auto; padding-right:4px; }
    .rm-scroll::-webkit-scrollbar { width:7px; } .rm-scroll::-webkit-scrollbar-thumb { background:#dfe3e8; border-radius:10px; }
    .rm-card { border:1px solid #eef2f7; border-radius:12px; overflow:hidden; background:#fff; margin-bottom:8px; }
    .rm-card summary { list-style:none; cursor:pointer; display:flex; align-items:center; gap:10px; padding:10px 12px; border-left:4px solid var(--rc); }
    .rm-card summary::-webkit-details-marker { display:none; }
    .rm-card summary:hover { background:#fcfbfb; }
    .rm-dot { width:11px; height:11px; border-radius:4px; background:var(--rc); flex-shrink:0; }
    .rm-name { font-weight:700; color:#1f2937; font-size:.84rem; flex:1; min-width:0; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .rm-count { background:var(--maroon-subtle); color:var(--maroon); font-weight:800; font-size:.66rem; padding:2px 9px; border-radius:20px; }
    .rm-card[open] summary { border-bottom:1px solid #f1f5f9; }
    .rm-card[open] summary .bi-chevron-down { transform:rotate(180deg); }
    .rm-body { padding:2px 0 4px; max-height:240px; overflow:auto; }
    .rm-line { display:flex; align-items:center; gap:8px; padding:6px 12px; border-top:1px solid #f6f8fa; font-size:.78rem; }
    .rm-line:first-child { border-top:none; }
    .rm-line .ins { flex:1; min-width:0; color:#334155; }
    .rm-line .ins b { color:#1f2937; font-weight:700; } .rm-line .ins .pr { color:#94a3b8; }
    .rm-line .per { color:#64748b; white-space:nowrap; font-size:.72rem; }
    .rm-line .q { font-weight:800; color:var(--maroon); white-space:nowrap; }
    .rm-line .st { width:8px; height:8px; border-radius:50%; flex-shrink:0; }
</style>

<div class="container-fluid pb-4">

    <div class="bk-head d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <h5 class="fw-bold mb-1"><i class="bi bi-calendar-week me-2"></i>Kalender Booking Ruangan</h5>
            <div class="opacity-75 small">{{ $total }} booking · klik event untuk detail</div>
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
        <div class="d-flex flex-wrap gap-3 mb-3 align-items-center" style="min-height:28px;">
            <span id="lg-sum" class="heat-scale">Sepi <i class="h1"></i><i class="h2"></i><i class="h3"></i><i class="h4"></i><i class="h5"></i> Padat <span class="text-muted fw-normal ms-2">· angka = booking aktif per hari, klik untuk rincian</span></span>
            <span id="lg-room" class="d-none align-items-center gap-3">
                <span class="room-filter"><i class="bi bi-door-open"></i><span id="rf-name"></span><button type="button" id="rf-clear" title="Tampilkan semua ruangan">&times;</button></span>
                <span class="legend-chip"><span class="legend-dot" style="background:#94a3b8;"></span> Menunggu</span>
                <span class="legend-chip"><span class="legend-dot" id="rf-dot" style="background:#7c1316;"></span> Disetujui</span>
            </span>
        </div>
        <div id="bkcal"></div>
    </div>
    </div>

    {{-- DAFTAR PER RUANGAN (panel kanan) --}}
    @if(count($perRuangan))
    @php $pal = ['#7c1316','#1d4ed8','#15803d','#b45309','#7e22ce','#0e7490','#be123c','#4d7c0f','#0f766e','#9333ea']; @endphp
    <div class="col-xl-4 col-lg-5">
    <div class="bk-card h-100">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="fw-bold text-dark"><i class="bi bi-door-open me-2" style="color:var(--maroon);"></i>Per Ruangan</div>
            <span class="text-muted small">{{ count($perRuangan) }} ruangan</span>
        </div>
        <div class="rm-hint"><i class="bi bi-hand-index me-1"></i>Klik ruangan untuk menampilkan jadwalnya di kalender.</div>
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
                        <div class="rm-line">
                            <span class="st" style="background:{{ $b->status==='pending' ? '#f59e0b' : '#16a34a' }};"></span>
                            <span class="ins text-truncate"><b>{{ optional($b->mou)->nama_instansi ?? optional($b->mou)->nama_universitas ?? '-' }}</b>@if($b->prodi || $b->jenjang)<span class="pr"> · {{ trim(($b->jenjang? $b->jenjang.' ':'').($b->prodi ?? '')) }}</span>@endif</span>
                            <span class="per">{{ optional($b->tanggal_mulai)->format('d/m/y') }}–{{ optional($b->tanggal_selesai)->format('d/m/y') }}</span>
                            <span class="q">{{ $b->jumlah_peserta }}</span>
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

{{-- MODAL DETAIL --}}
<div class="modal fade" id="bkModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white" style="background:linear-gradient(135deg,#7c1316,#a3191d);">
                <h6 class="modal-title fw-bold mb-0"><i class="bi bi-calendar-check me-2"></i>Detail Booking</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="bio-row"><span class="lbl">Instansi</span><span class="val" id="d-instansi"></span></div>
                <div class="bio-row"><span class="lbl">Ruangan</span><span class="val" id="d-ruangan"></span></div>
                <div class="bio-row"><span class="lbl">Prodi/Jenjang</span><span class="val" id="d-prodi"></span></div>
                <div class="bio-row"><span class="lbl">Periode</span><span class="val" id="d-periode"></span></div>
                <div class="bio-row"><span class="lbl">Jumlah Peserta</span><span class="val" id="d-peserta"></span></div>
                <div class="bio-row"><span class="lbl">Status</span><span class="val" id="d-status"></span></div>
                <div id="d-peserta-wrap" class="mt-2" style="display:none;">
                    <div class="lbl mb-1" style="color:#94a3b8;font-size:.82rem;">Peserta terdaftar</div>
                    <ul id="d-peserta-list" class="small mb-0 ps-3"></ul>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL RINCIAN HARI --}}
<div class="modal fade" id="dayModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white" style="background:linear-gradient(135deg,#7c1316,#a3191d);">
                <div>
                    <h6 class="modal-title fw-bold mb-0"><i class="bi bi-calendar-day me-2"></i><span id="dm-title"></span></h6>
                    <div class="small opacity-75" id="dm-sub"></div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 py-2" id="dm-list"></div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var el = document.getElementById('bkcal');
        if (!el) return;
        var ALL = {!! json_encode($events) !!};
        var activeRoom = null;

        function pad(n) { return (n < 10 ? '0' : '') + n; }
        function key(d) { return d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate()); }
        function parse(s) { var p = s.split('-'); return new Date(+p[0], +p[1] - 1, +p[2]); }

        // Hitung booking aktif & jumlah orang per hari
        var perDay = {}, maxB = 1;
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
        var minB = Infinity;
        Object.keys(perDay).forEach(function (k) { var b = perDay[k].b; if (b > maxB) maxB = b; if (b < minB) minB = b; });
        function level(b) {
            if (maxB <= minB) return 3;
            return Math.max(1, Math.min(5, 1 + Math.floor((b - minB) / (maxB - minB + 1) * 5)));
        }

        function showDetail(p) {
            document.getElementById('d-instansi').innerText = p.instansi || '-';
                document.getElementById('d-ruangan').innerText = p.ruangan || '-';
                document.getElementById('d-prodi').innerText = p.prodi || '-';
                document.getElementById('d-periode').innerText = p.periode || '-';
                document.getElementById('d-peserta').innerText = (p.peserta || 0) + ' orang';
                var badge = p.status === 'pending' ? 'Menunggu' : 'Disetujui';
                document.getElementById('d-status').innerText = badge + (p.fase ? ' · ' + p.fase : '');
                var wrap = document.getElementById('d-peserta-wrap');
                var ul = document.getElementById('d-peserta-list');
                ul.innerHTML = '';
                if (p.pesertaList && p.pesertaList.length) {
                    p.pesertaList.forEach(function (x) {
                        var li = document.createElement('li');
                        li.innerText = (x.nama || '-') + (x.prodi ? ' (' + x.prodi + ')' : '');
                        ul.appendChild(li);
                    });
                    wrap.style.display = '';
                } else { wrap.style.display = 'none'; }
                bootstrap.Modal.getOrCreateInstance(document.getElementById('bkModal')).show();
        }

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
                row.className = 'day-item'; row.style.cursor = 'pointer';
                row.innerHTML = '<span class="dd"></span><span class="tx"><b></b><small></small></span><span class="n"></span>';
                row.querySelector('.dd').style.background = e.color;
                row.querySelector('b').innerText = p.instansi;
                row.querySelector('small').innerText = p.ruangan + (p.prodi ? ' · ' + p.prodi : '') + ' · ' + p.periode;
                row.querySelector('.n').innerText = p.peserta + ' org';
                row.addEventListener('click', function () {
                    bootstrap.Modal.getOrCreateInstance(document.getElementById('dayModal')).hide();
                    showDetail(p);
                });
                box.appendChild(row);
            });
            bootstrap.Modal.getOrCreateInstance(document.getElementById('dayModal')).show();
        }

        var cal = new FullCalendar.Calendar(el, {
            initialView: 'dayGridMonth',
            locale: 'id',
            height: 'auto',
            headerToolbar: { left:'prev,next today', center:'title', right:'dayGridMonth,listMonth' },
            buttonText: { today:'Hari Ini', month:'Bulan', list:'Daftar' },
            dayMaxEvents: 4,
            displayEventTime: false,
            noEventsText: 'Tidak ada booking pada rentang ini',
            events: function (info, ok) {
                if (activeRoom) return ok(ALL.filter(function (e) { return e.extendedProps.ruangan === activeRoom; })
                    .map(function (e) { return Object.assign({}, e, { title: e.extendedProps.instansi + ' (' + e.extendedProps.peserta + ' org)' }); }));
                ok(cal && cal.view && cal.view.type === 'listMonth' ? ALL : []);
            },
            viewDidMount: function () { if (cal) cal.refetchEvents(); },
            dayCellDidMount: function (arg) {
                var info = perDay[key(arg.date)];
                if (!info) return;
                var lvl = level(info.b);
                var h = document.createElement('div');
                h.className = 'heat h' + lvl;
                h.title = info.b + ' booking aktif · ' + info.o + ' orang — klik untuk rincian';
                h.innerHTML = '<b>' + info.b + '</b> <span class="u">booking</span><span class="o">' + info.o + ' orang</span>';
                h.addEventListener('click', function (ev) { ev.stopPropagation(); showDay(arg.date); });
                var frame = arg.el.querySelector('.fc-daygrid-day-frame') || arg.el;
                var top = frame.querySelector('.fc-daygrid-day-top');
                if (top && top.nextSibling) frame.insertBefore(h, top.nextSibling); else frame.appendChild(h);
            },
            eventClick: function (info) { showDetail(info.event.extendedProps); }
        });
        cal.render();

        // Filter ruangan dari panel kanan
        function setRoom(name, color) {
            activeRoom = name;
            el.classList.toggle('room-mode', !!name);
            document.getElementById('lg-sum').classList.toggle('d-none', !!name);
            var lr = document.getElementById('lg-room');
            lr.classList.toggle('d-none', !name); lr.classList.toggle('d-inline-flex', !!name);
            if (name) {
                document.getElementById('rf-name').innerText = name;
                document.getElementById('rf-dot').style.background = color || '#7c1316';
            }
            cal.refetchEvents();
        }
        var cards = document.querySelectorAll('.rm-card');
        cards.forEach(function (d) {
            d.addEventListener('toggle', function () {
                if (d.open) {
                    cards.forEach(function (o) { if (o !== d) o.open = false; });
                    setRoom(d.dataset.room, d.dataset.color);
                    if (window.innerWidth < 992) el.scrollIntoView({ behavior:'smooth', block:'start' });
                } else if (activeRoom === d.dataset.room) {
                    setRoom(null);
                }
            });
        });
        document.getElementById('rf-clear').addEventListener('click', function () {
            cards.forEach(function (o) { o.open = false; });
            setRoom(null);
        });
    });
</script>
@endsection
