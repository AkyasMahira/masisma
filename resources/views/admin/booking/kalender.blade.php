@extends('layouts.app')

@section('title', 'Kalender Booking')
@section('page-title', 'Kalender Booking Ruangan')

@section('content')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>

<style>
    :root { --maroon:#7c1316; --maroon-light:#a3191d; }
    .bk-head { background:linear-gradient(135deg,var(--maroon),var(--maroon-light)); color:#fff; border-radius:16px; padding:18px 22px; box-shadow:0 8px 24px rgba(124,19,22,.18); position:relative; overflow:hidden; }
    .bk-head::after { content:''; position:absolute; right:-40px; top:-40px; width:160px; height:160px; background:rgba(255,255,255,.07); border-radius:50%; }
    .bk-card { background:#fff; border:1px solid #eef2f7; border-radius:16px; padding:16px; box-shadow:0 2px 12px rgba(0,0,0,.04); }
    .filter-input { border-radius:10px; border:1.5px solid #e2e8f0; font-size:.88rem; }
    .filter-input:focus { border-color:var(--maroon); box-shadow:0 0 0 .2rem rgba(124,19,22,.12); }
    #bkcal .fc-toolbar-title { font-size:1.1rem; font-weight:800; color:var(--maroon); }
    #bkcal .fc-button-primary { background:var(--maroon); border-color:var(--maroon); text-transform:capitalize; }
    #bkcal .fc-button-primary:hover { background:var(--maroon-light); border-color:var(--maroon-light); }
    #bkcal .fc-button-primary:not(:disabled).fc-button-active { background:#5f0f12; border-color:#5f0f12; }
    #bkcal .fc-event { cursor:pointer; border:none; font-size:.72rem; padding:1px 4px; }
    .legend-chip { display:inline-flex; align-items:center; gap:6px; font-size:.78rem; font-weight:600; color:#475569; }
    .legend-dot { width:12px; height:12px; border-radius:4px; display:inline-block; }
    .bio-row { display:flex; justify-content:space-between; gap:10px; padding:7px 0; border-bottom:1px solid #f1f5f9; font-size:.86rem; }
    .bio-row .lbl { color:#94a3b8; } .bio-row .val { font-weight:700; color:#1f2937; text-align:right; }
    .rm-item summary { cursor:pointer; font-weight:700; color:#334155; padding:8px 4px; }
    .rm-item .list td { font-size:.82rem; }
</style>

<div class="container-fluid pb-4">

    <div class="bk-head d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <h5 class="fw-bold mb-1"><i class="bi bi-calendar-week me-2"></i>Kalender Booking Ruangan</h5>
            <div class="opacity-75 small">{{ $total }} booking ditampilkan · klik event untuk detail</div>
        </div>
        <a href="{{ route('admin.booking.index') }}" class="btn btn-light btn-sm rounded-pill fw-semibold"><i class="bi bi-list-ul me-1"></i> Daftar Booking</a>
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
                    <option value="pending"  {{ $f['fStatus']==='pending'?'selected':'' }}>Menunggu</option>
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
            <div class="col-md-1 d-grid">
                <button type="submit" class="btn btn-sm text-white fw-semibold" style="background:var(--maroon);"><i class="bi bi-funnel"></i></button>
            </div>
        </div>
        @if($f['q'] || $f['fRuangan'] || $f['fMou'] || $f['fJenjang'] || $f['fStatus'] || $f['fStart'] || $f['fEnd'])
            <div class="mt-2"><a href="{{ route('admin.booking.kalender') }}" class="small text-muted text-decoration-none"><i class="bi bi-x-circle me-1"></i>Reset filter</a></div>
        @endif
    </form>

    <div class="bk-card">
        <div class="d-flex flex-wrap gap-3 mb-3">
            <span class="legend-chip"><span class="legend-dot" style="background:#94a3b8;"></span> Menunggu (pending)</span>
            <span class="legend-chip"><span class="legend-dot" style="background:#7c1316;"></span> Disetujui (warna per ruangan)</span>
        </div>
        <div id="bkcal"></div>
    </div>

    {{-- DAFTAR PER RUANGAN --}}
    @if(count($perRuangan))
    <div class="bk-card mt-3">
        <div class="fw-bold text-dark mb-2"><i class="bi bi-list-task me-2" style="color:var(--maroon);"></i>Daftar per Ruangan</div>
        @foreach($perRuangan as $nmRuangan => $list)
            <details class="rm-item">
                <summary>{{ $nmRuangan }} <span class="badge bg-light text-dark border">{{ count($list) }} booking</span></summary>
                <div class="table-responsive">
                    <table class="table table-sm list mb-2">
                        <thead class="bg-light"><tr><th>Instansi</th><th>Prodi</th><th class="text-center">Jumlah</th><th>Periode</th><th class="text-center">Status</th></tr></thead>
                        <tbody>
                            @foreach($list as $b)
                            <tr>
                                <td>{{ optional($b->mou)->nama_instansi ?? optional($b->mou)->nama_universitas ?? '-' }}</td>
                                <td class="small">{{ trim(($b->jenjang? $b->jenjang.' ':'').($b->prodi ?? '')) ?: '-' }}</td>
                                <td class="text-center">{{ $b->jumlah_peserta }}</td>
                                <td class="small text-nowrap">{{ optional($b->tanggal_mulai)->format('d/m/y') }} - {{ optional($b->tanggal_selesai)->format('d/m/y') }}</td>
                                <td class="text-center"><span class="badge {{ $b->status==='pending'?'bg-secondary':'bg-success' }}">{{ ucfirst($b->status) }}</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </details>
        @endforeach
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
            initialView: 'listMonth',
            locale: 'id',
            height: 'auto',
            headerToolbar: { left:'prev,next today', center:'title', right:'listMonth,dayGridMonth' },
            buttonText: { today:'Hari Ini', month:'Bulan', list:'Daftar' },
            dayMaxEvents: 3,
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
