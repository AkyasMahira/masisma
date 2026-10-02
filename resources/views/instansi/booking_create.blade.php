@extends('layouts.app')

@section('title', 'Booking Ruangan')

@section('content')
<style>
    :root { --maroon:#7c1316; --maroon-light:#a3191d; --maroon-subtle:#fcf0f1; }
    .form-card { background:#fff; border-radius:18px; box-shadow:0 10px 30px rgba(0,0,0,.08); overflow:hidden; }
    .card-head { background:linear-gradient(135deg,#7c1316,#5f0f12); color:#fff; padding:1.4rem 1.6rem; position:relative; overflow:hidden; }
    .card-head::after { content:''; position:absolute; right:-30px; top:-30px; width:130px; height:130px; background:rgba(255,255,255,.08); border-radius:50%; pointer-events:none; }
    .card-head h5 { font-weight:800; }
    .card-head p { margin:2px 0 0; opacity:.85; font-size:.82rem; }
    .sec-title { display:flex; align-items:center; gap:8px; font-weight:700; font-size:.82rem; text-transform:uppercase; letter-spacing:.4px; color:var(--maroon); margin:4px 0 14px; padding-bottom:8px; border-bottom:2px solid var(--maroon-subtle); }
    .sec-title i { width:26px; height:26px; background:var(--maroon-subtle); border-radius:8px; display:inline-flex; align-items:center; justify-content:center; font-size:.9rem; }
    .sec + .sec { margin-top:22px; }
    .req { color:#dc2626; }
    .cal-card { background:#fff; border-radius:16px; box-shadow:0 10px 30px rgba(0,0,0,.08); padding:16px; }
    .legend { display:flex; gap:14px; flex-wrap:wrap; font-size:.72rem; color:#64748b; margin-top:10px; }
    .legend .dot { width:12px; height:12px; border-radius:3px; display:inline-block; margin-right:4px; vertical-align:middle; }
    .fc { font-size:.78rem; }
    .fc .fc-toolbar-title { font-size:.95rem; font-weight:800; color:var(--maroon); }
    .fc .fc-button-primary { background:var(--maroon); border-color:var(--maroon); font-size:.72rem; padding:3px 8px; }
    .fc .fc-daygrid-day-number { text-decoration:none; color:#475569; font-size:.75rem; }
    .fc .fc-col-header-cell-cushion { text-decoration:none; color:var(--maroon); font-weight:700; font-size:.68rem; }
    .fc .fc-day-today { background:#fff7ed !important; }
    .fc-daygrid-event { border:none !important; border-radius:5px !important; padding:1px 5px !important; font-size:.66rem; font-weight:600; }
    .form-label { font-weight:600; font-size:.88rem; color:#2c3e50; }
    .form-control, .form-select { border-radius:10px; padding:.6rem .8rem; }
    .form-control:focus, .form-select:focus { border-color:var(--maroon); box-shadow:0 0 0 .2rem rgba(124,19,22,.12); }
    .btn-maroon { background:var(--maroon); color:#fff; border:none; border-radius:10px; padding:10px 20px; font-weight:700; }
    .btn-maroon:hover { background:var(--maroon-light); color:#fff; }
    .btn-light-c { background:#f1f5f9; color:#475569; border:none; border-radius:10px; padding:10px 20px; font-weight:600; text-decoration:none; }
    .room-hint { font-size:.8rem; }
</style>

<div class="container-fluid py-4">
  <div class="row g-4">
    <div class="col-lg-7">
    <div class="form-card">
        <div class="card-head">
            <h5 class="mb-0"><i class="bi bi-calendar-plus me-2"></i>Pengajuan Booking Ruangan</h5>
            <p>Ajukan kuota ruangan untuk anak magang instansi Anda. Field bertanda <span class="text-warning">*</span> wajib diisi.</p>
        </div>

        @if(session('error'))<div class="alert alert-danger m-3 mb-0">{{ session('error') }}</div>@endif
        @if($errors->any())<div class="alert alert-danger m-3 mb-0"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif

        <form action="{{ route('instansi.booking.store') }}" method="POST" class="p-4">
            @csrf

            <div class="sec">
            <div class="sec-title"><i class="bi bi-door-open"></i> Ruangan & Program</div>
            <div class="mb-3">
                <label class="form-label">Ruangan Tujuan <span class="req">*</span></label>
                <select name="ruangan_id" class="form-select" required>
                    <option value="">-- Pilih ruangan --</option>
                    @foreach($ruangans as $r)
                        <option value="{{ $r->id }}" {{ (string)old('ruangan_id')===(string)$r->id ? 'selected':'' }} {{ $r->sisa_kuota <= 0 ? 'disabled' : '' }}>
                            {{ $r->nm_ruangan }} — {{ $r->sisa_kuota > 0 ? ('sisa kuota '.$r->sisa_kuota.' orang') : 'PENUH' }}
                        </option>
                    @endforeach
                </select>
                <div class="room-hint text-muted mt-1"><i class="bi bi-info-circle me-1"></i>Sisa kuota indikatif per hari ini. Kuota final dicek saat admin menyetujui.</div>
            </div>

            <div class="row g-3 mb-1">
                <div class="col-md-4">
                    <label class="form-label">Jenjang</label>
                    <select name="jenjang" class="form-select">
                        <option value="">-- Semua/umum --</option>
                        @foreach($jenjangList as $j)
                            <option value="{{ $j }}" {{ old('jenjang')===$j ? 'selected':'' }}>{{ $j }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-5">
                    <label class="form-label">Program Studi</label>
                    <select name="prodi" class="form-select">
                        <option value="">-- Pilih prodi --</option>
                        @foreach($listProdi as $grup => $items)
                            <optgroup label="{{ $grup }}">
                                @foreach($items as $p)
                                    <option value="{{ $p }}" {{ old('prodi')===$p ? 'selected':'' }}>{{ $p }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Semester</label>
                    <input type="text" name="semester" class="form-control" value="{{ old('semester') }}" placeholder="mis. 5">
                </div>
            </div>
            </div>

            <div class="sec">
            <div class="sec-title"><i class="bi bi-people"></i> Jumlah & Periode</div>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Jumlah Peserta <span class="req">*</span></label>
                    <input type="number" name="jumlah_peserta" class="form-control" min="1" value="{{ old('jumlah_peserta', 1) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tanggal Mulai <span class="req">*</span></label>
                    <input type="date" name="tanggal_mulai" class="form-control" value="{{ old('tanggal_mulai') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tanggal Selesai <span class="req">*</span></label>
                    <input type="date" name="tanggal_selesai" class="form-control" value="{{ old('tanggal_selesai') }}" required>
                </div>
            </div>
            </div>

            <div class="sec">
            <div class="sec-title"><i class="bi bi-list-check"></i> Kompetensi & Catatan</div>
            <div>
                <label class="form-label">Kompetensi yang Dimiliki Peserta</label>
                <div id="kdWrap">
                    <input type="text" name="kompetensi_dimiliki[]" class="form-control mb-2" placeholder="mis. Pemasangan infus">
                </div>
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="tambahKD()"><i class="bi bi-plus"></i> Tambah kompetensi</button>
                <div class="room-hint text-muted mt-1"><i class="bi bi-info-circle me-1"></i>Kompetensi ini otomatis terisi ke semua mahasiswa dari booking ini saat disetujui.</div>
            </div>

            <div class="mt-3">
                <label class="form-label">Keterangan</label>
                <textarea name="keterangan" class="form-control" rows="3" placeholder="mis. Program magang mahasiswa D3 Keperawatan, jumlah & kebutuhan khusus...">{{ old('keterangan') }}</textarea>
            </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('instansi.dashboard') }}" class="btn-light-c">Batal</a>
                <button type="submit" class="btn-maroon"><i class="bi bi-send me-1"></i> Kirim Permintaan</button>
            </div>
        </form>
    </div>
    </div>

    <div class="col-lg-5">
        <div class="cal-card">
            <h6 class="fw-bold mb-2" style="color:var(--maroon);"><i class="bi bi-calendar3-week me-1"></i>Kalender Ketersediaan Ruangan</h6>
            <p class="text-muted small mb-2">Lihat jadwal yang sudah ter-booking sebelum mengajukan.</p>
            <div id="calOccup"></div>
            <div class="legend">
                <span><span class="dot" style="background:#7c1316;"></span>Booking Anda</span>
                <span><span class="dot" style="background:#cbd5e1;"></span>Ruangan terisi</span>
                <span><span class="dot" style="background:#94a3b8;"></span>Menunggu ACC</span>
            </div>
        </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
<script>
    function tambahKD(){
        var w=document.getElementById('kdWrap');
        var i=document.createElement('input');
        i.type='text'; i.name='kompetensi_dimiliki[]'; i.className='form-control mb-2'; i.placeholder='Kompetensi lain';
        w.appendChild(i);
    }
    document.addEventListener('DOMContentLoaded', function () {
        var el = document.getElementById('calOccup');
        if (el && window.FullCalendar) {
            var c = new FullCalendar.Calendar(el, {
                initialView: 'dayGridMonth',
                locale: 'id',
                height: 'auto',
                headerToolbar: { left: 'prev,next', center: 'title', right: 'today' },
                buttonText: { today: 'Kini' },
                events: @json($events ?? [])
            });
            c.render();
        }
    });
</script>
@endsection
