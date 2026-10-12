@extends('layouts.app')

@section('title', 'Kalender Ruangan')
@section('page-title', 'Kalender Jadwal Ruangan')

@section('content')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>

<style>
    :root { --maroon:#7c1316; --maroon-light:#a3191d; }
    .kal-head { background: linear-gradient(135deg,var(--maroon),var(--maroon-light)); color:#fff; border-radius:16px; padding:18px 22px; box-shadow:0 8px 24px rgba(124,19,22,.18); position:relative; overflow:hidden; }
    .kal-head::after { content:''; position:absolute; right:-40px; top:-40px; width:160px; height:160px; background:rgba(255,255,255,.07); border-radius:50%; }
    .kal-card { background:#fff; border:1px solid #eef2f7; border-radius:16px; padding:16px; box-shadow:0 2px 12px rgba(0,0,0,.04); }
    .legend-chip { display:inline-flex; align-items:center; gap:6px; font-size:.78rem; font-weight:600; color:#475569; }
    .legend-dot { width:12px; height:12px; border-radius:4px; display:inline-block; }
    #kalender .fc-event { cursor:pointer; border:none; font-size:.72rem; padding:1px 4px; }
    #kalender .fc-toolbar-title { font-size:1.1rem; font-weight:800; color:var(--maroon); }
    #kalender .fc-button-primary { background:var(--maroon); border-color:var(--maroon); }
    #kalender .fc-button-primary:hover { background:var(--maroon-light); border-color:var(--maroon-light); }
    #kalender .fc-button-primary:not(:disabled).fc-button-active { background:#5f0f12; border-color:#5f0f12; }
    .bio-foto { width:92px; height:92px; border-radius:16px; object-fit:cover; border:3px solid #fff; box-shadow:0 6px 16px rgba(0,0,0,.12); }
    .bio-ph { width:92px; height:92px; border-radius:16px; background:var(--maroon); color:#fff; display:flex; align-items:center; justify-content:center; font-size:2.2rem; font-weight:800; }
    .bio-row { display:flex; justify-content:space-between; gap:10px; padding:7px 0; border-bottom:1px solid #f1f5f9; font-size:.85rem; }
    .bio-row .lbl { color:#94a3b8; }
    .bio-row .val { font-weight:700; color:#1f2937; text-align:right; }
</style>

<div class="container-fluid pb-4">

    <div class="kal-head d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <h5 class="fw-bold mb-1"><i class="bi bi-calendar-week me-2"></i>Kalender Jadwal Ruangan</h5>
            <div class="opacity-75 small"><i class="bi bi-door-open me-1"></i>{{ $ruangan->nm_ruangan }} — klik nama mahasiswa untuk lihat biodata</div>
        </div>
        <a href="{{ route('kepala_ruangan.dashboard') }}" class="btn btn-light btn-sm rounded-pill fw-semibold"><i class="bi bi-arrow-left me-1"></i> Dashboard</a>
    </div>

    <div class="kal-card">
        <div class="d-flex flex-wrap gap-3 mb-3">
            <span class="legend-chip"><span class="legend-dot" style="background:#0d6efd;"></span> Pagi</span>
            <span class="legend-chip"><span class="legend-dot" style="background:#fd7e14;"></span> Siang</span>
            <span class="legend-chip"><span class="legend-dot" style="background:#6f42c1;"></span> Malam</span>
            <span class="legend-chip"><span class="legend-dot" style="background:#6610f2;"></span> Reguler</span>
            <span class="legend-chip"><span class="legend-dot" style="background:#20c997;"></span> Jumat</span>
        </div>
        <div id="kalender"></div>
    </div>
</div>

{{-- MODAL BIODATA --}}
<div class="modal fade" id="bioModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white" style="background:linear-gradient(135deg,#7c1316,#a3191d);">
                <h6 class="modal-title fw-bold mb-0"><i class="bi bi-person-vcard me-2"></i>Biodata Mahasiswa</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div id="bio-foto-wrap"></div>
                    <div>
                        <div id="bio-nama" class="fw-bold fs-5 text-dark"></div>
                        <div id="bio-prodi" class="text-muted small"></div>
                        <span id="bio-tipe" class="badge bg-info text-dark mt-1"></span>
                    </div>
                </div>
                <div class="bio-row"><span class="lbl">Jadwal diklik</span><span class="val" id="bio-jadwal"></span></div>
                <div class="bio-row"><span class="lbl">Asal Instansi</span><span class="val" id="bio-instansi"></span></div>
                <div class="bio-row"><span class="lbl">Periode Magang</span><span class="val" id="bio-periode"></span></div>
                <div class="bio-row"><span class="lbl">Status</span><span class="val" id="bio-status"></span></div>
                <div class="pt-2">
                    <div class="lbl mb-1" style="color:#94a3b8;font-size:.8rem;">Keahlian / Kompetensi</div>
                    <div id="bio-keahlian" class="d-flex flex-wrap gap-1"></div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <a id="bio-edit" href="#" class="btn btn-outline-secondary btn-sm fw-semibold"><i class="bi bi-pencil-square me-1"></i>Edit Biodata</a>
                <a id="bio-shift" href="#" class="btn btn-sm fw-semibold text-white" style="background:#7c1316;"><i class="bi bi-calendar-week me-1"></i>Atur Shift &amp; Rentang</a>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var BIO = {!! json_encode($bio) !!};
        var events = {!! json_encode($events) !!};
        var SHIFT_URL = "{{ route('shift_schedule.manage', '__ID__') }}";
        var el = document.getElementById('kalender');
        if (!el) return;

        var cal = new FullCalendar.Calendar(el, {
            initialView: 'dayGridMonth',
            locale: 'id',
            height: 'auto',
            headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,listMonth' },
            buttonText: { today: 'Hari Ini', month: 'Bulan', list: 'Daftar' },
            dayMaxEvents: 3,
            events: events,
            eventClick: function (info) {
                var mid = info.event.extendedProps.mid;
                var shift = info.event.extendedProps.shift || '-';
                var b = BIO[mid];
                if (!b) return;
                var wrap = document.getElementById('bio-foto-wrap');
                if (b.foto) {
                    wrap.innerHTML = '<img src="' + b.foto + '" class="bio-foto" alt="foto">';
                } else {
                    wrap.innerHTML = '<div class="bio-ph">' + (b.nama ? b.nama.charAt(0).toUpperCase() : '?') + '</div>';
                }
                document.getElementById('bio-nama').innerText = b.nama || '-';
                document.getElementById('bio-prodi').innerText = b.prodi || '-';
                document.getElementById('bio-tipe').innerText = b.tipe || 'MAGANG';
                var tgl = info.event.start ? info.event.start.toLocaleDateString('id-ID', { weekday:'long', day:'numeric', month:'long', year:'numeric' }) : '';
                document.getElementById('bio-jadwal').innerText = (shift + (tgl ? ' • ' + tgl : ''));
                document.getElementById('bio-instansi').innerText = b.instansi || '-';
                document.getElementById('bio-periode').innerText = b.periode || '-';
                document.getElementById('bio-status').innerText = b.status || '-';

                // Keahlian / kompetensi
                var kWrap = document.getElementById('bio-keahlian');
                kWrap.innerHTML = '';
                if (b.keahlian && b.keahlian.length) {
                    b.keahlian.forEach(function (k) {
                        var s = document.createElement('span');
                        s.className = 'badge';
                        s.style.cssText = 'background:#fcf0f1;color:#7c1316;border:1px solid #f0d3d5;font-weight:600;';
                        s.innerText = k;
                        kWrap.appendChild(s);
                    });
                } else {
                    kWrap.innerHTML = '<span class="text-muted small">Belum diisi</span>';
                }

                // Tombol aksi
                var btnShift = document.getElementById('bio-shift');
                if (b.seq_id) {
                    btnShift.style.display = '';
                    btnShift.href = SHIFT_URL.replace('__ID__', b.seq_id);
                } else {
                    btnShift.style.display = 'none';
                }
                document.getElementById('bio-edit').href = b.edit_url || '#';

                new bootstrap.Modal(document.getElementById('bioModal')).show();
            }
        });
        cal.render();
    });
</script>
@endsection
