@extends('layouts.app')

@section('title', 'Kalender Booking Ruangan')

@section('content')
<style>
    :root { --maroon:#7c1316; --maroon-light:#a3191d; --maroon-subtle:#fcf0f1; --radius:14px; --shadow:0 4px 20px rgba(0,0,0,.05); }
    .hero { background:linear-gradient(135deg,#7c1316,#5f0f12); color:#fff; border-radius:20px; padding:24px 28px; margin-bottom:1.25rem; box-shadow:0 14px 34px rgba(124,19,22,.28); position:relative; overflow:hidden; }
    .hero::after { content:''; position:absolute; right:-40px; top:-40px; width:180px; height:180px; background:rgba(255,255,255,.07); border-radius:50%; pointer-events:none; }
    .hero h4 { font-weight:800; margin:0 0 4px; }
    .hero p { opacity:.9; margin:0; font-size:.88rem; }
    .hero .btn-light-h { background:#fff; color:var(--maroon); border:none; border-radius:50px; padding:9px 20px; font-weight:700; text-decoration:none; display:inline-flex; align-items:center; box-shadow:0 4px 12px rgba(0,0,0,.15); transition:.2s; }
    .hero .btn-light-h:hover { color:var(--maroon); transform:translateY(-1px); box-shadow:0 8px 18px rgba(0,0,0,.22); }
    .cal-card, .side-card { background:#fff; border-radius:var(--radius); box-shadow:var(--shadow); padding:18px; }
    .room-group { margin-bottom:18px; }
    .room-title { font-weight:700; font-size:.9rem; display:flex; align-items:center; gap:8px; padding-bottom:6px; border-bottom:2px solid #f1f5f9; margin-bottom:10px; }
    .room-dot { width:12px; height:12px; border-radius:4px; flex-shrink:0; }
    .book-item { font-size:.8rem; padding:10px 12px; border-radius:10px; background:#f8fafc; margin-bottom:8px; border-left:4px solid #cbd5e1; }
    .book-item .ins { font-weight:700; color:#1f2937; }
    .book-item .meta { color:#64748b; font-size:.72rem; margin-top:2px; line-height:1.5; }
    .b-pending-txt { color:#b45309; } .b-approved-txt { color:#15803d; }

    /* ===== FullCalendar polish ===== */
    .fc { font-size:.86rem; }
    .fc .fc-toolbar-title { font-size:1.15rem; font-weight:800; color:var(--maroon); }
    .fc .fc-button-primary { background:var(--maroon); border-color:var(--maroon); border-radius:8px; font-weight:600; text-transform:capitalize; box-shadow:none; }
    .fc .fc-button-primary:hover { background:var(--maroon-light); border-color:var(--maroon-light); }
    .fc .fc-button-primary:not(:disabled).fc-button-active,
    .fc .fc-button-primary:not(:disabled):active { background:var(--maroon-light); border-color:var(--maroon-light); }
    .fc .fc-button:focus { box-shadow:0 0 0 .2rem rgba(124,19,22,.2); }
    /* header hari */
    .fc .fc-col-header-cell { background:var(--maroon-subtle); }
    .fc .fc-col-header-cell-cushion { text-decoration:none; color:var(--maroon); font-weight:700; padding:8px 4px; text-transform:uppercase; font-size:.72rem; letter-spacing:.3px; }
    /* angka tanggal: hilangkan underline link */
    .fc .fc-daygrid-day-number { text-decoration:none; color:#475569; font-weight:600; padding:6px 8px; }
    .fc .fc-daygrid-day.fc-day-today { background:#fff7ed !important; }
    .fc .fc-day-today .fc-daygrid-day-number { color:var(--maroon); font-weight:800; }
    .fc .fc-day-sat, .fc .fc-day-sun { background:#fafafa; }
    .fc .fc-daygrid-day-frame { min-height:92px; }
    .fc-theme-standard td, .fc-theme-standard th { border-color:#eef2f7; }
    /* event jadi pill */
    .fc-daygrid-event { border:none !important; border-radius:6px !important; padding:2px 8px !important; font-size:.72rem; font-weight:600; cursor:pointer; margin:2px 4px; box-shadow:0 1px 3px rgba(0,0,0,.12); }
    .fc-daygrid-event .fc-event-title { white-space:normal; }
    .fc-list-event:hover td { background:var(--maroon-subtle); }
</style>

<div class="container-fluid py-3">
    <div class="hero d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <h4><i class="bi bi-calendar3-week me-2"></i>Kalender Booking Ruangan</h4>
            <p>Jadwal booking instansi mitra per ruangan. Warna abu = menunggu ACC.</p>
        </div>
        <a href="{{ route('admin.booking.index') }}" class="btn-light-h"><i class="bi bi-list-ul me-1"></i> Daftar Booking</a>
    </div>

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="cal-card"><div id="calendar"></div></div>
        </div>
        <div class="col-lg-4">
            <div class="side-card">
                <h6 class="fw-bold mb-3"><i class="bi bi-door-open me-1" style="color:var(--maroon);"></i> Booking per Ruangan</h6>
                @php $palette = ['#7c1316','#1d4ed8','#15803d','#b45309','#7e22ce','#0e7490','#be123c','#4d7c0f']; @endphp
                @forelse($perRuangan as $namaRuangan => $list)
                    @php $warna = $palette[($list[0]->ruangan_id ?? 0) % count($palette)]; @endphp
                    <div class="room-group">
                        <div class="room-title"><span class="room-dot" style="background:{{ $warna }};"></span>{{ $namaRuangan }} <span class="text-muted fw-normal">({{ count($list) }})</span></div>
                        @foreach($list as $b)
                            <div class="book-item" style="border-left-color:{{ $b->status==='pending' ? '#94a3b8' : $warna }};">
                                <div class="ins">{{ optional($b->mou)->nama_instansi ?? optional($b->mou)->nama_universitas ?? 'Instansi' }}</div>
                                <div class="meta">
                                    <i class="bi bi-people me-1"></i>{{ $b->jumlah_peserta }} org ·
                                    {{ optional($b->tanggal_mulai)->format('d/m/y') }}-{{ optional($b->tanggal_selesai)->format('d/m/y') }} ·
                                    <span class="{{ $b->status==='pending' ? 'b-pending-txt' : 'b-approved-txt' }} fw-semibold">{{ $b->status==='pending' ? 'Menunggu' : 'Disetujui' }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @empty
                    <div class="text-muted small text-center py-4"><i class="bi bi-inbox fs-3 d-block mb-2"></i>Belum ada booking.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var el = document.getElementById('calendar');
        if (!el) return;
        var calendar = new FullCalendar.Calendar(el, {
            initialView: 'dayGridMonth',
            locale: 'id',
            height: 'auto',
            headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,listMonth' },
            buttonText: { today: 'Hari ini', month: 'Bulan', list: 'Daftar' },
            events: @json($events),
            eventDidMount: function (info) {
                var p = info.event.extendedProps;
                info.el.title = p.instansi + ' — ' + p.ruangan + ' (' + p.peserta + ' orang, ' + (p.status === 'pending' ? 'menunggu ACC' : 'disetujui') + ')';
            }
        });
        calendar.render();
    });
</script>
@endsection
