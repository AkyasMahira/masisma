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

    /* ===== Modal detail ===== */
    #modalBooking .modal-content { border-radius:20px; overflow:hidden; border:none; box-shadow:0 24px 60px rgba(0,0,0,.28); }
    #modalBooking .modal-header { background:linear-gradient(135deg,#7c1316,#5f0f12); padding:22px 26px; position:relative; align-items:flex-start; }
    #modalBooking .modal-header::after { content:''; position:absolute; right:-24px; top:-24px; width:120px; height:120px; background:rgba(255,255,255,.08); border-radius:50%; pointer-events:none; }
    #mb-instansi { font-size:1.15rem; font-weight:800; }
    #mb-status .mb-pill { background:rgba(255,255,255,.22); color:#fff; }
    /* strip info horizontal */
    .mb-strip { display:flex; gap:10px; flex-wrap:wrap; margin-bottom:18px; }
    .mb-chip { flex:1; min-width:120px; background:#f8fafc; border:1px solid #eef2f7; border-radius:14px; padding:14px 16px; text-align:center; }
    .mb-chip .ic { font-size:1.2rem; color:var(--maroon); }
    .mb-chip .lbl { font-size:.65rem; text-transform:uppercase; color:#94a3b8; font-weight:700; letter-spacing:.3px; margin-top:4px; }
    .mb-chip .val { font-weight:800; color:#1f2937; font-size:.95rem; margin-top:2px; }
    .mb-prodi { background:var(--maroon-subtle); color:var(--maroon); border-radius:12px; padding:10px 14px; font-weight:600; font-size:.85rem; margin-bottom:18px; display:flex; align-items:center; gap:8px; }
    .mb-sec { font-weight:800; color:#1f2937; font-size:.9rem; display:flex; align-items:center; gap:8px; margin:4px 0 12px; }
    .mb-sec .cnt { background:var(--maroon); color:#fff; border-radius:20px; padding:1px 11px; font-size:.72rem; }
    .ps-row { display:flex; align-items:center; gap:12px; padding:10px 4px; border-bottom:1px solid #f1f5f9; }
    .ps-row:last-child { border-bottom:none; }
    .ps-av { width:40px; height:40px; border-radius:11px; background:linear-gradient(135deg,#7c1316,#a3191d); color:#fff; font-weight:800; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .ps-name { font-weight:700; color:#1f2937; font-size:.9rem; }
    .ps-meta { color:#94a3b8; font-size:.74rem; }
    .mb-pill { padding:3px 11px; border-radius:20px; font-size:.7rem; font-weight:700; }
    .mb-empty { text-align:center; padding:28px; color:#94a3b8; }
    .mb-empty i { font-size:2rem; display:block; margin-bottom:8px; opacity:.6; }
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

{{-- Modal detail booking --}}
<div class="modal fade" id="modalBooking" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header text-white border-0">
                <div>
                    <h6 class="modal-title fw-bold mb-0"><i class="bi bi-buildings me-2"></i><span id="mb-instansi">Detail Booking</span></h6>
                    <div style="opacity:.85;font-size:.76rem;" class="mt-1"><span id="mb-status"></span></div>
                </div>
                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-strip">
                    <div class="mb-chip"><div class="ic"><i class="bi bi-door-open"></i></div><div class="lbl">Ruangan</div><div class="val" id="mb-ruangan">-</div></div>
                    <div class="mb-chip"><div class="ic"><i class="bi bi-calendar-range"></i></div><div class="lbl">Periode</div><div class="val" id="mb-periode">-</div></div>
                </div>
                <div class="mb-prodi"><i class="bi bi-mortarboard"></i> <span id="mb-prodi">-</span></div>
                <div class="mb-sec"><i class="bi bi-people-fill" style="color:var(--maroon);"></i> Daftar Anak Magang <span class="cnt" id="mb-count">0</span></div>
                <div id="mb-peserta" style="max-height:300px; overflow:auto;"></div>
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
                info.el.title = 'Klik untuk detail — ' + p.instansi + ' (' + p.ruangan + ')';
            },
            eventClick: function (info) {
                info.jsEvent.preventDefault();
                var p = info.event.extendedProps;
                document.getElementById('mb-instansi').textContent = p.instansi || 'Detail Booking';
                document.getElementById('mb-ruangan').textContent = p.ruangan || '-';
                document.getElementById('mb-periode').textContent = p.periode || '-';
                document.getElementById('mb-prodi').textContent = p.prodi || '-';
                var stTxt = { pending: 'Menunggu ACC', approved: 'Disetujui' }[p.status] || p.status;
                document.getElementById('mb-status').innerHTML = '<span class="mb-pill"><i class="bi bi-circle-fill me-1" style="font-size:.5rem;vertical-align:middle;"></i>' + stTxt + '</span>';

                var list = p.pesertaList || [];
                document.getElementById('mb-count').textContent = list.length;
                var pMap = { belum:['#fee2e2','#b91c1c','Menunggu'], sudah:['#dcfce7','#15803d','Mahasiswa'], approved:['#dcfce7','#15803d','Mahasiswa'], lulus:['#dbeafe','#1d4ed8','Lulus'] };
                var html = '';
                if (list.length === 0) {
                    html = '<div class="mb-empty"><i class="bi bi-people"></i>Belum ada peserta yang diisi instansi untuk booking ini.</div>';
                } else {
                    list.forEach(function (x) {
                        var ps = pMap[x.status] || ['#f1f5f9','#64748b', (x.status||'-')];
                        var initial = (x.nama || '?').trim().charAt(0).toUpperCase();
                        html += '<div class="ps-row"><div class="ps-av">'+initial+'</div>'
                             +  '<div class="flex-grow-1"><div class="ps-name">'+(x.nama||'-')+'</div>'
                             +  '<div class="ps-meta">'+(x.nim? 'NIM '+x.nim : 'NIM -')+(x.prodi? ' · '+x.prodi : '')+'</div></div>'
                             +  '<span class="mb-pill" style="background:'+ps[0]+';color:'+ps[1]+';">'+ps[2]+'</span></div>';
                    });
                }
                document.getElementById('mb-peserta').innerHTML = html;
                new bootstrap.Modal(document.getElementById('modalBooking')).show();
            }
        });
        calendar.render();
    });
</script>
@endsection
