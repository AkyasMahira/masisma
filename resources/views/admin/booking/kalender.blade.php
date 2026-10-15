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

    .legend-chip { display:inline-flex; align-items:center; gap:6px; font-size:.76rem; font-weight:600; color:#475569; }
    .legend-dot { width:12px; height:12px; border-radius:4px; display:inline-block; }
    .bio-row { display:flex; justify-content:space-between; gap:10px; padding:7px 0; border-bottom:1px solid #f1f5f9; font-size:.86rem; }
    .bio-row .lbl { color:#94a3b8; } .bio-row .val { font-weight:700; color:#1f2937; text-align:right; }

    /* per-ruangan cards */
    .rm-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(250px,1fr)); gap:12px; }
    .rm-card { border:1px solid #eef2f7; border-radius:14px; overflow:hidden; background:#fff; }
    .rm-card summary { list-style:none; cursor:pointer; display:flex; align-items:center; gap:10px; padding:12px 14px; border-left:4px solid var(--rc); }
    .rm-card summary::-webkit-details-marker { display:none; }
    .rm-dot { width:12px; height:12px; border-radius:4px; background:var(--rc); flex-shrink:0; }
    .rm-name { font-weight:800; color:#1f2937; font-size:.9rem; flex:1; }
    .rm-count { background:var(--maroon-subtle); color:var(--maroon); font-weight:800; font-size:.68rem; padding:2px 9px; border-radius:20px; }
    .rm-card[open] summary { border-bottom:1px solid #f1f5f9; }
    .rm-body { padding:4px 0 6px; }
    .rm-line { display:flex; align-items:center; gap:8px; padding:7px 14px; border-top:1px solid #f6f8fa; font-size:.8rem; }
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

    <div class="bk-card">
        <div class="d-flex flex-wrap gap-3 mb-3 align-items-center">
            <span class="legend-chip"><span class="legend-dot" style="background:#94a3b8;"></span> Menunggu</span>
            <span class="legend-chip"><span class="legend-dot" style="background:#7c1316;"></span> Disetujui <span class="text-muted">(warna per ruangan)</span></span>
        </div>
        <div id="bkcal"></div>
    </div>

    {{-- DAFTAR PER RUANGAN --}}
    @if(count($perRuangan))
    @php $pal = ['#7c1316','#1d4ed8','#15803d','#b45309','#7e22ce','#0e7490','#be123c','#4d7c0f','#0f766e','#9333ea']; $ri = 0; @endphp
    <div class="bk-card mt-3">
        <div class="fw-bold text-dark mb-3"><i class="bi bi-grid-3x3-gap me-2" style="color:var(--maroon);"></i>Daftar per Ruangan</div>
        <div class="rm-grid">
            @foreach($perRuangan as $nmRuangan => $list)
                @php $rc = $pal[$ri % count($pal)]; $ri++; @endphp
                <details class="rm-card" style="--rc: {{ $rc }};">
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
    @endif
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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var el = document.getElementById('bkcal');
        if (!el) return;
        var cal = new FullCalendar.Calendar(el, {
            initialView: 'dayGridMonth',
            locale: 'id',
            height: 'auto',
            headerToolbar: { left:'prev,next today', center:'title', right:'dayGridMonth,listMonth' },
            buttonText: { today:'Hari Ini', month:'Bulan', list:'Daftar' },
            dayMaxEvents: 3,
            displayEventTime: false,
            noEventsText: 'Tidak ada booking pada rentang ini',
            events: {!! json_encode($events) !!},
            eventClick: function (info) {
                var p = info.event.extendedProps;
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
                new bootstrap.Modal(document.getElementById('bkModal')).show();
            }
        });
        cal.render();
    });
</script>
@endsection
