@extends('layouts.app')

@section('title', $isAdmin ? 'Tambah Booking Ruangan' : 'Booking Ruangan')

@section('content')
@php
    $oldKomp = array_values(array_filter((array) old('kompetensi_dimiliki', [])));
    if (!count($oldKomp)) $oldKomp = [''];
    $ruangJs = $ruangans->mapWithKeys(function ($r) { return [$r->id => ['nama' => $r->nm_ruangan, 'kuota' => (int) $r->kuota_ruangan]]; });
    $palette = ['#7c1316','#1d4ed8','#15803d','#b45309','#7e22ce','#0e7490','#be123c','#4d7c0f','#0f766e','#9333ea'];
@endphp
<style>
    :root { --maroon:#7c1316; --maroon-light:#a3191d; --maroon-subtle:#fcf0f1; }
    .bk-head { background:linear-gradient(135deg,#7c1316,#5f0f12); color:#fff; border-radius:18px; padding:20px 24px; box-shadow:0 12px 30px rgba(124,19,22,.26); position:relative; overflow:hidden; }
    .bk-head::after { content:''; position:absolute; right:-40px; top:-40px; width:170px; height:170px; background:rgba(255,255,255,.07); border-radius:50%; pointer-events:none; }
    .bk-head h4 { font-weight:800; margin:0 0 3px; font-size:1.3rem; }
    .bk-head p { opacity:.88; margin:0; font-size:.84rem; }
    .btn-light-h { background:#fff; color:var(--maroon); border:none; border-radius:50px; font-weight:700; padding:7px 16px; text-decoration:none; display:inline-flex; align-items:center; box-shadow:0 4px 12px rgba(0,0,0,.14); position:relative; z-index:1; }
    .btn-light-h:hover { color:var(--maroon); text-decoration:none; }

    .card-x { background:#fff; border:1px solid #eef2f7; border-radius:16px; box-shadow:0 4px 20px rgba(0,0,0,.05); }
    .step { padding:20px 22px; }
    .step + .step { border-top:1px solid #f1f3f5; }
    .step-h { display:flex; align-items:center; gap:10px; margin-bottom:14px; }
    .step-n { width:28px; height:28px; border-radius:50%; background:var(--maroon); color:#fff; font-weight:800; font-size:.8rem; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .step-h b { color:#1f2937; font-size:.95rem; }
    .step-h small { display:block; color:#94a3b8; font-size:.74rem; font-weight:500; }
    .form-label { font-weight:700; font-size:.8rem; color:#475569; margin-bottom:5px; }
    .req { color:#dc2626; }
    .form-control, .form-select { border-radius:10px; border:1.5px solid #e2e8f0; padding:.55rem .8rem; font-size:.88rem; }
    .form-control:focus, .form-select:focus { border-color:var(--maroon); box-shadow:0 0 0 .2rem rgba(124,19,22,.1); }
    .hint { font-size:.74rem; color:#94a3b8; margin-top:5px; }
    .dur-chip { display:inline-flex; align-items:center; gap:5px; background:var(--maroon-subtle); color:var(--maroon); font-weight:700; font-size:.74rem; border-radius:20px; padding:3px 10px; margin-top:8px; }
    .komp-row { display:flex; gap:6px; margin-bottom:6px; }
    .komp-row .btn-x { border:1px solid #e5e7eb; background:#fff; color:#94a3b8; border-radius:10px; width:40px; flex-shrink:0; }
    .komp-row .btn-x:hover { color:#dc2626; border-color:#fca5a5; }
    .btn-add { border:1.5px dashed #d6dbe1; background:#fff; color:#64748b; border-radius:10px; font-weight:700; font-size:.8rem; padding:6px 12px; }
    .btn-add:hover { border-color:var(--maroon); color:var(--maroon); }
    .seg { display:flex; gap:8px; flex-wrap:wrap; }
    .seg input { display:none; }
    .seg label { flex:1; min-width:150px; border:1.5px solid #e2e8f0; border-radius:12px; padding:10px 12px; cursor:pointer; transition:.12s; }
    .seg label b { display:block; font-size:.86rem; color:#1f2937; }
    .seg label small { color:#94a3b8; font-size:.72rem; }
    .seg input:checked + label { border-color:var(--maroon); background:var(--maroon-subtle); }
    .seg input:checked + label b { color:var(--maroon); }
    .form-foot { display:flex; justify-content:flex-end; gap:8px; padding:16px 22px; border-top:1px solid #f1f3f5; background:#fcfcfd; border-radius:0 0 16px 16px; }
    .btn-maroon { background:var(--maroon); color:#fff; border:none; border-radius:10px; padding:10px 20px; font-weight:700; }
    .btn-maroon:hover { background:var(--maroon-light); color:#fff; }
    .btn-soft { background:#f1f5f9; color:#475569; border:none; border-radius:10px; padding:10px 20px; font-weight:700; text-decoration:none; }
    .btn-soft:hover { background:#e2e8f0; color:#334155; }

    /* cek kuota */
    .side-sticky { position:sticky; top:16px; }
    .quota { padding:16px 18px; }
    .quota-h { font-weight:800; color:#1f2937; font-size:.9rem; display:flex; align-items:center; gap:8px; margin-bottom:10px; }
    .quota-empty { background:#f8fafc; border:1px dashed #e2e8f0; border-radius:12px; padding:16px; text-align:center; color:#94a3b8; font-size:.8rem; }
    .q-room { font-weight:800; color:#1f2937; }
    .q-per { font-size:.76rem; color:#64748b; }
    .q-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:8px; margin:12px 0 10px; }
    .q-box { background:#f8fafc; border:1px solid #eef2f7; border-radius:11px; padding:8px 10px; text-align:center; }
    .q-box .v { font-size:1.15rem; font-weight:800; color:#1f2937; line-height:1.1; }
    .q-box .l { font-size:.62rem; font-weight:800; color:#94a3b8; text-transform:uppercase; letter-spacing:.3px; }
    .q-bar { height:8px; background:#eef2f7; border-radius:10px; overflow:hidden; display:flex; }
    .q-bar span { display:block; height:100%; }
    .q-msg { margin-top:10px; border-radius:10px; padding:8px 11px; font-size:.8rem; font-weight:600; display:flex; gap:8px; align-items:flex-start; }
    .q-ok { background:#dcfce7; color:#15803d; } .q-warn { background:#fef3c7; color:#b45309; } .q-bad { background:#fee2e2; color:#b91c1c; }

    /* kalender */
    .cal-wrap { padding:16px; }
    #calOccup { --fc-border-color:#eef1f5; --fc-today-bg-color:#fff7f7; font-size:.76rem; }
    #calOccup a { text-decoration:none !important; }
    #calOccup .fc-toolbar-title { font-size:.98rem; font-weight:800; color:var(--maroon); }
    #calOccup .fc-button-primary { background:#fff; border-color:#e5e7eb; color:#475569; font-weight:700; font-size:.72rem; padding:3px 9px; box-shadow:none !important; }
    #calOccup .fc-button-primary:hover { background:#f8fafc; color:var(--maroon); }
    #calOccup .fc-button-primary:disabled { background:#fff; color:#cbd5e1; }
    #calOccup .fc-col-header-cell-cushion { color:#64748b; font-weight:700; font-size:.66rem; text-transform:uppercase; }
    #calOccup .fc-daygrid-day-number { color:#475569; font-weight:600; font-size:.72rem; }
    #calOccup .fc-daygrid-event { border:none; border-radius:5px; padding:1px 5px; font-size:.64rem; font-weight:600; cursor:pointer; }
    #calOccup .fc-daygrid-more-link { font-size:.64rem; font-weight:700; color:var(--maroon); }
    .cal-note { font-size:.74rem; color:#64748b; margin-bottom:8px; display:flex; align-items:center; gap:6px; flex-wrap:wrap; }
    .cal-chip { display:inline-flex; align-items:center; gap:6px; background:var(--maroon-subtle); color:var(--maroon); font-weight:700; border-radius:20px; padding:2px 10px; }
    .legend { display:flex; gap:12px; flex-wrap:wrap; font-size:.7rem; color:#64748b; margin-top:10px; }
    .legend .dot { width:11px; height:11px; border-radius:3px; display:inline-block; margin-right:4px; vertical-align:middle; }
    @media (max-width: 1199px) { .side-sticky { position:static; } }
</style>

<div class="container-fluid py-3">
    <div class="bk-head d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
        <div>
            <h4><i class="bi bi-calendar-plus me-2"></i>{{ $isAdmin ? 'Tambah Booking Ruangan' : 'Pengajuan Booking Ruangan' }}</h4>
            <p>{{ $isAdmin ? 'Buat booking atas nama instansi mitra — bisa langsung disetujui.' : 'Ajukan kuota ruangan untuk anak magang instansi Anda.' }}</p>
        </div>
        <a href="{{ $cancelUrl }}" class="btn-light-h"><i class="bi bi-arrow-left me-1"></i> Kembali</a>
    </div>

    @if(session('error'))<div class="alert alert-danger border-0 shadow-sm" style="border-radius:12px;"><i class="bi bi-exclamation-triangle me-1"></i>{{ session('error') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger border-0 shadow-sm" style="border-radius:12px;"><ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif

    <div class="row g-3">
        <div class="col-xl-7">
            <form action="{{ $action }}" method="POST" class="card-x" id="bkForm">
                @csrf
                @php $n = 0; @endphp

                @if($isAdmin)
                <div class="step">
                    <div class="step-h"><span class="step-n">{{ ++$n }}</span><div><b>Instansi</b><small>Booking dibuat atas nama instansi ini</small></div></div>
                    <label class="form-label">Instansi Mitra <span class="req">*</span></label>
                    <select name="mou_id" class="form-select" required>
                        <option value="">— Pilih instansi —</option>
                        @foreach($mouOptions as $id => $nm)
                            <option value="{{ $id }}" {{ (string) old('mou_id') === (string) $id ? 'selected' : '' }}>{{ $nm }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div class="step">
                    <div class="step-h"><span class="step-n">{{ ++$n }}</span><div><b>Ruangan &amp; Program</b><small>Ruangan tujuan dan program studi peserta</small></div></div>
                    <div class="mb-3">
                        <label class="form-label">Ruangan Tujuan <span class="req">*</span></label>
                        <select name="ruangan_id" id="f-ruangan" class="form-select" required>
                            <option value="">— Pilih ruangan —</option>
                            @foreach($ruangans as $r)
                                <option value="{{ $r->id }}" {{ (string) old('ruangan_id') === (string) $r->id ? 'selected' : '' }}>{{ $r->nm_ruangan }} · kapasitas {{ (int) $r->kuota_ruangan }}</option>
                            @endforeach
                        </select>
                        <div class="hint"><i class="bi bi-info-circle me-1"></i>Sisa kuota untuk periode yang dipilih tampil di panel <b>Cek Kuota</b>.</div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Jenjang</label>
                            <select name="jenjang" class="form-select">
                                <option value="">—</option>
                                @foreach($jenjangList as $j)
                                    <option value="{{ $j }}" {{ old('jenjang') === $j ? 'selected' : '' }}>{{ $j }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Program Studi</label>
                            <select name="prodi" class="form-select">
                                <option value="">— Pilih prodi —</option>
                                @foreach($listProdi as $grup => $items)
                                    <optgroup label="{{ $grup }}">
                                        @foreach($items as $p)
                                            <option value="{{ $p }}" {{ old('prodi') === $p ? 'selected' : '' }}>{{ $p }}</option>
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

                <div class="step">
                    <div class="step-h"><span class="step-n">{{ ++$n }}</span><div><b>Jumlah &amp; Periode</b><small>Berapa orang dan kapan</small></div></div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Jumlah Peserta <span class="req">*</span></label>
                            <input type="number" name="jumlah_peserta" id="f-jumlah" class="form-control" min="1" value="{{ old('jumlah_peserta', 1) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tanggal Mulai <span class="req">*</span></label>
                            <input type="date" name="tanggal_mulai" id="f-mulai" class="form-control" value="{{ old('tanggal_mulai') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tanggal Selesai <span class="req">*</span></label>
                            <input type="date" name="tanggal_selesai" id="f-selesai" class="form-control" value="{{ old('tanggal_selesai') }}" required>
                        </div>
                    </div>
                    <span class="dur-chip" id="durChip" style="display:none;"><i class="bi bi-hourglass-split"></i><span></span></span>
                </div>

                <div class="step">
                    <div class="step-h"><span class="step-n">{{ ++$n }}</span><div><b>Kompetensi &amp; Catatan</b><small>Target yang ingin dikuasai peserta (opsional)</small></div></div>
                    <label class="form-label">Kompetensi yang Ingin Dikuasai</label>
                    <div id="kdWrap">
                        @foreach($oldKomp as $k)
                            <div class="komp-row">
                                <input type="text" name="kompetensi_dimiliki[]" class="form-control" value="{{ $k }}" placeholder="mis. Pemasangan infus">
                                <button type="button" class="btn-x" title="Hapus" onclick="hapusKD(this)"><i class="bi bi-x-lg"></i></button>
                            </div>
                        @endforeach
                    </div>
                    <button type="button" class="btn-add" onclick="tambahKD()"><i class="bi bi-plus-lg me-1"></i>Tambah kompetensi</button>
                    <div class="hint">Otomatis terisi ke semua mahasiswa dari booking ini.</div>

                    <label class="form-label mt-3">Keterangan</label>
                    <textarea name="keterangan" class="form-control" rows="3" placeholder="mis. Program magang D3 Keperawatan, kebutuhan khusus...">{{ old('keterangan') }}</textarea>
                </div>

                @if($isAdmin)
                <div class="step">
                    <div class="step-h"><span class="step-n">{{ ++$n }}</span><div><b>Status &amp; Batas Pengisian</b><small>Khusus admin</small></div></div>
                    <div class="seg mb-3">
                        <input type="radio" name="status" id="st-approved" value="approved" {{ old('status', 'approved') === 'approved' ? 'checked' : '' }}>
                        <label for="st-approved"><b><i class="bi bi-check-circle me-1"></i>Langsung disetujui</b><small>Kuota dicek; instansi bisa langsung isi peserta</small></label>
                        <input type="radio" name="status" id="st-pending" value="pending" {{ old('status') === 'pending' ? 'checked' : '' }}>
                        <label for="st-pending"><b><i class="bi bi-hourglass-split me-1"></i>Menunggu</b><small>Disimpan dulu, ACC belakangan</small></label>
                    </div>
                    <label class="form-label">Batas Pengisian Peserta <span class="text-muted fw-normal">(opsional)</span></label>
                    <input type="date" name="batas_pengisian" class="form-control" value="{{ old('batas_pengisian') }}" style="max-width:240px;">
                    <div class="hint">Kosongkan bila tidak dibatasi.</div>
                </div>
                @endif

                <div class="form-foot">
                    <a href="{{ $cancelUrl }}" class="btn-soft">Batal</a>
                    <button type="submit" class="btn-maroon"><i class="bi {{ $isAdmin ? 'bi-save' : 'bi-send' }} me-1"></i>{{ $isAdmin ? 'Simpan Booking' : 'Kirim Permintaan' }}</button>
                </div>
            </form>
        </div>

        <div class="col-xl-5">
            <div class="side-sticky">
                <div class="card-x quota mb-3">
                    <div class="quota-h"><i class="bi bi-speedometer2" style="color:var(--maroon);"></i>Cek Kuota</div>
                    <div id="quotaBody"><div class="quota-empty"><i class="bi bi-door-open d-block fs-4 mb-1"></i>Pilih ruangan dan tanggal untuk melihat sisa kuota.</div></div>
                </div>

                <div class="card-x cal-wrap">
                    <div class="fw-bold mb-1" style="color:var(--maroon);"><i class="bi bi-calendar3-week me-1"></i>Kalender Ketersediaan</div>
                    <div class="cal-note" id="calNote">Menampilkan semua ruangan. Klik tanggal atau seret rentang untuk cek ketersediaan.</div>
                    <div id="calOccup"></div>
                    <div class="legend">
                        @if($isAdmin)
                            <span><span class="dot" style="background:#7c1316;"></span>Disetujui (warna per ruangan)</span>
                        @else
                            <span><span class="dot" style="background:#7c1316;"></span>Booking Anda</span>
                            <span><span class="dot" style="background:#cbd5e1;"></span>Terisi instansi lain</span>
                        @endif
                        <span><span class="dot" style="background:#94a3b8;"></span>Menunggu ACC</span>
                        <span><span class="dot" style="background:#fbe4e6;border:1px solid #e8b4b9;"></span>Periode dipilih</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalOccup" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content" style="border-radius:14px;overflow:hidden;border:none;">
            <div class="modal-header text-white" style="background:linear-gradient(135deg,#7c1316,#5f0f12);">
                <h6 class="modal-title fw-bold"><i class="bi bi-door-open me-1"></i>Detail Booking</h6>
                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2"><div class="text-muted small">Ruangan</div><div class="fw-bold" id="co-ruangan">-</div></div>
                <div class="mb-2" id="co-ins-wrap"><div class="text-muted small">Instansi</div><div class="fw-semibold" id="co-instansi">-</div></div>
                <div class="mb-2"><div class="text-muted small">Periode</div><div class="fw-semibold" id="co-periode">-</div></div>
                <div class="row">
                    <div class="col-6 mb-2"><div class="text-muted small">Jumlah</div><div class="fw-bold" id="co-jumlah">-</div></div>
                    <div class="col-6 mb-2"><div class="text-muted small">Sisa kuota</div><div class="fw-bold text-success" id="co-sisa">-</div></div>
                </div>
                <div><div class="text-muted small">Status</div><div id="co-status">-</div></div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL KETERSEDIAAN (klik tanggal / pilih rentang di kalender) --}}
<div class="modal fade" id="modalAvail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
        <div class="modal-content" style="border-radius:16px;overflow:hidden;border:none;">
            <div class="modal-header text-white align-items-start" style="background:linear-gradient(135deg,#7c1316,#5f0f12);">
                <div>
                    <h6 class="modal-title fw-bold mb-0"><i class="bi bi-calendar-range me-1"></i>Ketersediaan Ruangan</h6>
                    <div class="small opacity-75" id="av-range"></div>
                </div>
                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
                    <div class="nv-sum" id="av-sum"></div>
                    <input type="search" id="av-q" class="form-control form-control-sm ms-auto" style="max-width:230px;" placeholder="Cari ruangan / kampus...">
                </div>
                <div id="av-list"></div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <span class="text-muted small me-auto"><i class="bi bi-info-circle me-1"></i>Terisi = jumlah booking disetujui yang beririsan dengan rentang ini.</span>
                <button type="button" class="btn-maroon" id="av-use"><i class="bi bi-check2-square me-1"></i>Pakai tanggal ini</button>
            </div>
        </div>
    </div>
</div>
<style>
    .av-room { border:1px solid #eef2f7; border-radius:12px; margin-bottom:8px; overflow:hidden; }
    .av-top { display:grid; grid-template-columns:minmax(0,1fr) 70px 70px 84px 92px; gap:10px; align-items:center; padding:10px 12px; }
    .av-name { font-weight:800; color:#1f2937; font-size:.86rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .av-name small { display:block; font-weight:600; color:#94a3b8; font-size:.7rem; }
    .av-num { text-align:center; line-height:1.1; }
    .av-num b { display:block; font-size:1rem; font-weight:800; color:#1f2937; }
    .av-num span { font-size:.58rem; font-weight:800; color:#94a3b8; text-transform:uppercase; letter-spacing:.3px; }
    .av-pick { border:1.5px solid #e2e8f0; background:#fff; border-radius:9px; font-size:.72rem; font-weight:700; color:#475569; padding:5px 8px; }
    .av-pick:hover, .av-pick.on { border-color:var(--maroon); color:var(--maroon); background:var(--maroon-subtle); }
    .av-bar { height:5px; background:#eef2f7; }
    .av-bar span { display:block; height:100%; }
    .av-kampus { padding:6px 12px 10px; background:#fcfcfd; border-top:1px solid #f4f6f8; }
    .av-k { display:flex; justify-content:space-between; gap:10px; font-size:.76rem; padding:3px 0; color:#475569; }
    .av-k b { color:#1f2937; font-weight:700; }
    .av-k .n { color:var(--maroon); font-weight:800; white-space:nowrap; }
    .av-none { font-size:.74rem; color:#94a3b8; }
    .nv-sum { font-size:.8rem; color:#475569; font-weight:600; }
    @media (max-width: 576px) { .av-top { grid-template-columns:minmax(0,1fr) 54px 54px 62px; } .av-top .av-act { grid-column:1 / -1; } }
</style>

<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
<script>
    function tambahKD() {
        var w = document.getElementById('kdWrap');
        var d = document.createElement('div');
        d.className = 'komp-row';
        d.innerHTML = '<input type="text" name="kompetensi_dimiliki[]" class="form-control" placeholder="Kompetensi lain">'
            + '<button type="button" class="btn-x" title="Hapus" onclick="hapusKD(this)"><i class="bi bi-x-lg"></i></button>';
        w.appendChild(d);
        d.querySelector('input').focus();
    }
    function hapusKD(btn) {
        var w = document.getElementById('kdWrap');
        if (w.children.length > 1) btn.parentNode.remove(); else btn.parentNode.querySelector('input').value = '';
    }

    document.addEventListener('DOMContentLoaded', function () {
        var IS_ADMIN = @json((bool) $isAdmin);
        var ROOMS = @json($ruangJs);
        var OCC = @json($occ);
        var EVENTS = @json($events);
        var $ = function (id) { return document.getElementById(id); };
        var fR = $('f-ruangan'), fJ = $('f-jumlah'), fM = $('f-mulai'), fS = $('f-selesai');

        function esc(s) { var d = document.createElement('div'); d.innerText = s == null ? '' : String(s); return d.innerHTML; }
        function parse(s) { var p = s.split('-'); return new Date(+p[0], +p[1] - 1, +p[2]); }
        function fmt(s) { return parse(s).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }); }
        function addDay(s) { var d = parse(s); d.setDate(d.getDate() + 1); return d.getFullYear() + '-' + ('0' + (d.getMonth() + 1)).slice(-2) + '-' + ('0' + d.getDate()).slice(-2); }

        // Aturan sama dengan server: kapasitas - jumlah booking approved yang periodenya beririsan
        function hitung(rid, s, e) {
            var kap = ROOMS[rid] ? ROOMS[rid].kuota : 0, used = 0;
            OCC.forEach(function (o) { if (o.r === +rid && o.s <= e && o.e >= s) used += o.n; });
            return { kap: kap, used: used, sisa: Math.max(0, kap - used) };
        }

        function statusDipilih() { var r = document.querySelector('input[name="status"]:checked'); return r ? r.value : 'pending'; }

        function updateQuota() {
            var rid = fR.value, s = fM.value, e = fS.value, j = +fJ.value || 0, box = $('quotaBody');
            var chip = $('durChip');
            if (s && e && e >= s) {
                var hari = Math.round((parse(e) - parse(s)) / 864e5) + 1;
                chip.style.display = ''; chip.querySelector('span').innerText = hari + ' hari · ' + Math.ceil(hari / 7) + ' minggu';
            } else { chip.style.display = 'none'; }

            if (!rid || !s || !e) {
                box.innerHTML = '<div class="quota-empty"><i class="bi bi-door-open d-block fs-4 mb-1"></i>Pilih ruangan dan tanggal untuk melihat sisa kuota.</div>';
                return;
            }
            if (e < s) {
                box.innerHTML = '<div class="q-msg q-bad"><i class="bi bi-x-circle"></i>Tanggal selesai harus setelah tanggal mulai.</div>';
                return;
            }
            var h = hitung(rid, s, e), kap = h.kap || 0;
            var pUsed = kap ? Math.min(100, h.used / kap * 100) : 100;
            var pNew = kap ? Math.min(100 - pUsed, j / kap * 100) : 0;
            var over = j > h.sisa;
            var msg;
            if (kap <= 0) msg = '<div class="q-msg q-warn"><i class="bi bi-exclamation-triangle"></i>Kapasitas ruangan belum diatur di master ruangan.</div>';
            else if (h.sisa <= 0) msg = '<div class="q-msg q-bad"><i class="bi bi-x-octagon"></i>Ruangan PENUH pada periode ini. Pilih ruangan atau periode lain.</div>';
            else if (over) msg = '<div class="q-msg q-bad"><i class="bi bi-exclamation-octagon"></i>Jumlah ' + j + ' orang melebihi sisa kuota (' + h.sisa + ').</div>';
            else msg = '<div class="q-msg q-ok"><i class="bi bi-check-circle"></i>Kuota cukup. Setelah booking ini sisa ' + (h.sisa - j) + ' orang.</div>';
            if (IS_ADMIN && statusDipilih() === 'pending' && (over || h.sisa <= 0)) {
                msg += '<div class="q-msg q-warn"><i class="bi bi-info-circle"></i>Status Menunggu tetap bisa disimpan; kuota dicek lagi saat di-ACC.</div>';
            }
            if (!IS_ADMIN && (over || h.sisa <= 0)) {
                msg += '<div class="q-msg q-warn"><i class="bi bi-info-circle"></i>Permintaan yang melebihi sisa kuota akan ditolak sistem.</div>';
            }
            box.innerHTML = '<div class="q-room">' + esc(ROOMS[rid] ? ROOMS[rid].nama : '-') + '</div>'
                + '<div class="q-per">' + fmt(s) + ' – ' + fmt(e) + '</div>'
                + '<div class="q-grid">'
                + '<div class="q-box"><div class="v">' + kap + '</div><div class="l">Kapasitas</div></div>'
                + '<div class="q-box"><div class="v">' + h.used + '</div><div class="l">Terpakai</div></div>'
                + '<div class="q-box"><div class="v" style="color:' + (h.sisa > 0 ? '#15803d' : '#b91c1c') + '">' + h.sisa + '</div><div class="l">Sisa</div></div>'
                + '</div>'
                + '<div class="q-bar"><span style="width:' + pUsed + '%;background:#94a3b8"></span><span style="width:' + pNew + '%;background:' + (over ? '#dc2626' : '#16a34a') + '"></span></div>'
                + '<div class="d-flex justify-content-between mt-1" style="font-size:.66rem;color:#94a3b8;font-weight:700;"><span>terpakai</span><span>+ booking ini (' + j + ')</span></div>'
                + msg;
        }

        // ===== Kalender =====
        var cal = null;
        var el = $('calOccup');
        if (el && window.FullCalendar) {
            cal = new FullCalendar.Calendar(el, {
                initialView: 'dayGridMonth',
                locale: 'id',
                height: 'auto',
                headerToolbar: { left: 'prev,next', center: 'title', right: 'today' },
                buttonText: { today: 'Hari ini' },
                dayMaxEvents: 3,
                displayEventTime: false,
                selectable: true,
                select: function (info) {
                    var en = new Date(info.end.getTime() - 864e5);
                    openAvail(info.startStr, ymd(en));
                    cal.unselect();
                },
                events: function (info, ok) {
                    var rid = fR.value;
                    var list = rid ? EVENTS.filter(function (e) { return e.extendedProps.rid === +rid; }) : EVENTS.slice();
                    if (fM.value && fS.value && fS.value >= fM.value) {
                        list.push({ start: fM.value, end: addDay(fS.value), display: 'background', color: '#e8a3a9' });
                    }
                    ok(list);
                },
                eventClick: function (info) {
                    info.jsEvent.preventDefault();
                    var p = info.event.extendedProps;
                    if (!p || !p.ruangan) return;
                    var st = info.event.startStr, en = info.event.end ? new Date(info.event.end.getTime() - 864e5) : info.event.start;
                    var enStr = en.getFullYear() + '-' + ('0' + (en.getMonth() + 1)).slice(-2) + '-' + ('0' + en.getDate()).slice(-2);
                    var h = hitung(p.rid, st, enStr);
                    $('co-ruangan').textContent = p.ruangan || '-';
                    $('co-ins-wrap').style.display = p.instansi ? '' : 'none';
                    $('co-instansi').textContent = p.instansi || '-';
                    $('co-periode').textContent = p.periode || '-';
                    $('co-jumlah').textContent = (p.jumlah || 0) + ' orang';
                    $('co-sisa').textContent = h.sisa + ' / ' + h.kap + ' orang';
                    $('co-status').textContent = (p.mine ? 'Booking Anda · ' : '') + (p.status === 'pending' ? 'Menunggu ACC' : 'Disetujui');
                    bootstrap.Modal.getOrCreateInstance($('modalOccup')).show();
                }
            });
            cal.render();
        }

        // ===== Ketersediaan per ruangan untuk tanggal/rentang yang diklik =====
        function ymd(d) { return d.getFullYear() + '-' + ('0' + (d.getMonth() + 1)).slice(-2) + '-' + ('0' + d.getDate()).slice(-2); }
        var av = { s: null, e: null, pick: null };

        function availData(s, e) {
            return Object.keys(ROOMS).map(function (rid) {
                var kampus = {}, used = 0;
                OCC.forEach(function (o) {
                    if (o.r !== +rid || o.s > e || o.e < s) return;
                    used += o.n;
                    var k = o.i || 'Instansi';
                    if (!kampus[k]) kampus[k] = { n: 0, per: [] };
                    kampus[k].n += o.n;
                    kampus[k].per.push(fmt(o.s) + ' – ' + fmt(o.e));
                });
                var kap = ROOMS[rid].kuota || 0;
                return { rid: +rid, nama: ROOMS[rid].nama, kap: kap, used: used, sisa: Math.max(0, kap - used), kampus: kampus };
            });
        }

        function renderAvail() {
            var q = ($('av-q').value || '').toLowerCase().trim();
            var data = availData(av.s, av.e);
            var sel = +fR.value || null;
            data.sort(function (a, b) {
                if (a.rid === sel) return -1; if (b.rid === sel) return 1;
                return b.sisa - a.sisa || a.nama.localeCompare(b.nama);
            });
            var adaSisa = data.filter(function (d) { return d.sisa > 0; }).length;
            $('av-sum').innerHTML = '<span style="color:#15803d">' + adaSisa + ' ruangan masih ada sisa</span> · <span style="color:#b91c1c">' + (data.length - adaSisa) + ' penuh</span>';
            if (q) data = data.filter(function (d) {
                return d.nama.toLowerCase().indexOf(q) >= 0 || Object.keys(d.kampus).some(function (k) { return k.toLowerCase().indexOf(q) >= 0; });
            });
            var html = '';
            data.forEach(function (d) {
                var pUsed = d.kap ? Math.min(100, d.used / d.kap * 100) : 100;
                var warna = d.sisa <= 0 ? '#dc2626' : (d.sisa <= Math.max(1, d.kap * 0.25) ? '#f59e0b' : '#16a34a');
                var ks = Object.keys(d.kampus).sort(function (a, b) { return d.kampus[b].n - d.kampus[a].n; });
                var kampusHtml = ks.length ? ks.map(function (k) {
                    return '<div class="av-k"><span><b>' + esc(k) + '</b> <span class="text-muted">· ' + esc(d.kampus[k].per.join(', ')) + '</span></span><span class="n">' + d.kampus[k].n + ' org</span></div>';
                }).join('') : '<div class="av-none">Belum ada kampus yang mengisi pada rentang ini.</div>';
                html += '<div class="av-room"' + (d.rid === sel ? ' style="border-color:#e3b3b8;box-shadow:0 0 0 2px #fbe4e6"' : '') + '>'
                    + '<div class="av-top"><div class="av-name">' + esc(d.nama) + (d.rid === sel ? ' <span class="badge" style="background:#fcf0f1;color:#7c1316;font-size:.6rem;">dipilih di form</span>' : '') + '<small>' + ks.length + ' kampus mengisi</small></div>'
                    + '<div class="av-num"><b>' + d.kap + '</b><span>Kuota</span></div>'
                    + '<div class="av-num"><b>' + d.used + '</b><span>Terisi</span></div>'
                    + '<div class="av-num"><b style="color:' + warna + '">' + (d.sisa > 0 ? d.sisa : 'Penuh') + '</b><span>' + (d.sisa > 0 ? 'Kurang/sisa' : '') + '</span></div>'
                    + '<div class="av-act text-end"><button type="button" class="av-pick' + (av.pick === d.rid ? ' on' : '') + '" data-rid="' + d.rid + '">' + (av.pick === d.rid ? '<i class="bi bi-check-lg"></i> Dipilih' : 'Pilih') + '</button></div></div>'
                    + '<div class="av-bar"><span style="width:' + pUsed + '%;background:' + warna + '"></span></div>'
                    + '<div class="av-kampus">' + kampusHtml + '</div></div>';
            });
            $('av-list').innerHTML = html || '<div class="quota-empty">Tidak ada ruangan yang cocok.</div>';
        }

        function openAvail(s, e) {
            av.s = s; av.e = e; av.pick = +fR.value || null;
            var hari = Math.round((parse(e) - parse(s)) / 864e5) + 1;
            $('av-range').innerText = (s === e ? fmt(s) : fmt(s) + ' – ' + fmt(e)) + ' · ' + hari + ' hari';
            $('av-q').value = '';
            renderAvail();
            bootstrap.Modal.getOrCreateInstance($('modalAvail')).show();
        }

        $('av-list').addEventListener('click', function (ev) {
            var b = ev.target.closest('.av-pick'); if (!b) return;
            var rid = +b.getAttribute('data-rid');
            av.pick = av.pick === rid ? null : rid;
            renderAvail();
        });
        $('av-q').addEventListener('input', renderAvail);
        $('av-use').addEventListener('click', function () {
            fM.value = av.s; fS.min = av.s; fS.value = av.e;
            if (av.pick) fR.value = String(av.pick);
            bootstrap.Modal.getOrCreateInstance($('modalAvail')).hide();
            updateQuota(); refreshCal(true);
            fJ.focus();
        });

        function refreshCal(jump) {
            if (!cal) return;
            var rid = fR.value;
            $('calNote').innerHTML = rid
                ? 'Jadwal ruangan: <span class="cal-chip"><i class="bi bi-door-open"></i>' + esc(ROOMS[rid] ? ROOMS[rid].nama : '') + '</span>'
                : 'Menampilkan semua ruangan. Klik tanggal atau seret rentang untuk cek ketersediaan.';
            cal.refetchEvents();
            if (jump && fM.value) cal.gotoDate(fM.value);
        }

        fR.addEventListener('change', function () { updateQuota(); refreshCal(false); });
        fJ.addEventListener('input', updateQuota);
        fM.addEventListener('change', function () {
            if (fS.value && fS.value < fM.value) fS.value = '';
            fS.min = fM.value;
            updateQuota(); refreshCal(true);
        });
        fS.addEventListener('change', function () { updateQuota(); refreshCal(false); });
        document.querySelectorAll('input[name="status"]').forEach(function (r) { r.addEventListener('change', updateQuota); });

        if (fM.value) fS.min = fM.value;
        updateQuota(); refreshCal(true);
    });
</script>
@endsection
