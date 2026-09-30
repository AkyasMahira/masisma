@extends('layouts.app')

@section('title', 'Kalender Booking Ruangan')

@section('content')
<style>
    :root { --maroon:#7c1316; --maroon-light:#a3191d; --maroon-subtle:#fcf0f1; --radius:12px; --shadow:0 4px 20px rgba(0,0,0,.05); }
    .header-card { background:#fff; border-radius:var(--radius); border-left:5px solid var(--maroon); padding:20px; margin-bottom:20px; box-shadow:0 2px 4px rgba(0,0,0,.05); }
    .cal-card, .side-card { background:#fff; border-radius:var(--radius); box-shadow:var(--shadow); padding:18px; }
    .btn-outline-custom { border:1.5px solid var(--maroon); color:var(--maroon); background:#fff; border-radius:8px; padding:8px 16px; font-weight:600; text-decoration:none; }
    .btn-outline-custom:hover { background:var(--maroon-subtle); }
    .room-group { margin-bottom:16px; }
    .room-title { font-weight:700; font-size:.9rem; display:flex; align-items:center; gap:8px; padding-bottom:6px; border-bottom:2px solid #f1f5f9; margin-bottom:8px; }
    .room-dot { width:12px; height:12px; border-radius:3px; flex-shrink:0; }
    .book-item { font-size:.8rem; padding:8px 10px; border-radius:8px; background:#f8fafc; margin-bottom:6px; border-left:3px solid #cbd5e1; }
    .book-item .ins { font-weight:600; color:#2c3e50; }
    .book-item .meta { color:#64748b; font-size:.72rem; }
    .b-pending-txt { color:#b45309; } .b-approved-txt { color:#15803d; }
    /* FullCalendar tweaks */
    .fc .fc-toolbar-title { font-size:1.1rem; font-weight:700; color:var(--maroon); }
    .fc .fc-button-primary { background:var(--maroon); border-color:var(--maroon); }
    .fc .fc-button-primary:hover { background:var(--maroon-light); border-color:var(--maroon-light); }
    .fc .fc-button-primary:not(:disabled).fc-button-active { background:var(--maroon-light); border-color:var(--maroon-light); }
    .fc-event { cursor:pointer; font-size:.72rem; }
</style>

<div class="container-fluid py-3">
    <div class="header-card d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <h4 class="mb-1 fw-bold" style="color:var(--maroon);"><i class="bi bi-calendar3-week me-2"></i>Kalender Booking Ruangan</h4>
            <p class="mb-0 text-muted small">Jadwal booking instansi mitra per ruangan. Warna abu = menunggu ACC.</p>
        </div>
        <a href="{{ route('admin.booking.index') }}" class="btn-outline-custom"><i class="bi bi-list-ul me-1"></i> Daftar Booking</a>
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
