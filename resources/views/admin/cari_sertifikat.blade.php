@extends('layouts.app')

@section('title', 'Cari Sertifikat Peserta')
@section('page-title', 'Cari Sertifikat Peserta')

@section('content')
<style>
    :root { --maroon:#7c1316; --maroon-light:#a3191d; }
    .cs-head { background:linear-gradient(135deg,var(--maroon),var(--maroon-light)); color:#fff; border-radius:16px; padding:18px 22px; box-shadow:0 8px 24px rgba(124,19,22,.18); position:relative; overflow:hidden; }
    .cs-head::after { content:''; position:absolute; right:-40px; top:-40px; width:160px; height:160px; background:rgba(255,255,255,.07); border-radius:50%; }
    .cs-card { background:#fff; border:1px solid #eef2f7; border-radius:16px; padding:16px; box-shadow:0 2px 12px rgba(0,0,0,.04); }
    .filter-input { border-radius:10px; border:1.5px solid #e2e8f0; }
    .filter-input:focus { border-color:var(--maroon); box-shadow:0 0 0 .2rem rgba(124,19,22,.12); }
    .cs-table thead th { background:#fcf0f1; color:#7c1316; font-size:.74rem; text-transform:uppercase; letter-spacing:.4px; }
    .link-cell { font-size:.78rem; color:#2563eb; word-break:break-all; }
</style>

<div class="container-fluid pb-4">

    <div class="cs-head d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <h5 class="fw-bold mb-1"><i class="bi bi-search me-2"></i>Cari Sertifikat Peserta</h5>
            <div class="opacity-75 small">Menampilkan semua record peserta (lintas kegiatan) + link sertifikatnya.</div>
        </div>
        @if($hasil->count())
            <button type="button" id="btn-copy-all" class="btn btn-light btn-sm rounded-pill fw-semibold"><i class="bi bi-clipboard-check me-1"></i> Salin Semua Link</button>
        @endif
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm"><i class="bi bi-check-circle me-1"></i>{{ session('success') }}</div>
    @endif

    <form method="GET" action="{{ route('admin.cari_sertifikat') }}" class="cs-card mb-3">
        <div class="row g-2 align-items-end">
            <div class="col-md-10">
                <label class="form-label small fw-semibold text-muted mb-1">Nama Peserta</label>
                <input type="text" name="q" value="{{ $q }}" class="form-control filter-input" placeholder="mis. INTAN PURNAMA SARI" autofocus>
            </div>
            <div class="col-md-2 d-grid">
                <button type="submit" class="btn text-white fw-bold" style="background:var(--maroon);"><i class="bi bi-search me-1"></i>Cari</button>
            </div>
        </div>
    </form>

    @if($q !== '')
        @if($hasil->count())
        <div class="cs-card p-0">
            <div class="table-responsive">
                <table class="table table-hover cs-table mb-0 align-middle">
                    <thead>
                        <tr>
                            <th class="ps-3">No</th>
                            <th>Nama</th>
                            <th>Kegiatan</th>
                            <th>Tanggal</th>
                            <th>Link Sertifikat</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($hasil as $i => $p)
                        <tr>
                            <td class="ps-3 text-muted">{{ $i + 1 }}</td>
                            <td>
                                <form action="{{ route('admin.cari_sertifikat.nama', $p->id) }}" method="POST" class="d-flex gap-1">
                                    @csrf
                                    <input type="hidden" name="q" value="{{ $q }}">
                                    <input type="text" name="nama_lengkap_gelar" value="{{ $p->nama_lengkap_gelar }}" class="form-control form-control-sm filter-input" style="min-width:180px;" required>
                                    <button type="submit" class="btn btn-sm btn-outline-dark" title="Simpan nama"><i class="bi bi-check-lg"></i></button>
                                </form>
                            </td>
                            <td class="small">{{ optional($p->kegiatan)->nama_kegiatan ?? '-' }}</td>
                            <td class="small text-nowrap">{{ optional(optional($p->kegiatan)->tanggal_mulai) ? \Carbon\Carbon::parse($p->kegiatan->tanggal_mulai)->format('d/m/Y') : '-' }}</td>
                            <td class="link-cell"><a href="{{ $p->link_sertifikat }}" target="_blank" class="js-link">{{ $p->link_sertifikat }}</a></td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-outline-dark btn-copy" data-link="{{ $p->link_sertifikat }}" title="Salin link"><i class="bi bi-clipboard"></i></button>
                                <a href="{{ $p->link_sertifikat }}" target="_blank" class="btn btn-sm btn-outline-success" title="Buka"><i class="bi bi-box-arrow-up-right"></i></a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="small text-muted mt-2">{{ $hasil->count() }} record ditemukan. (Link hanya bisa dibuka bila peserta tercatat HADIR.)</div>

        {{-- teks semua link untuk salin massal --}}
        <textarea id="all-links" style="position:absolute;left:-9999px;">@foreach($hasil as $p){{ $p->nama_lengkap_gelar }} — {{ optional($p->kegiatan)->nama_kegiatan }}
{{ $p->link_sertifikat }}

@endforeach</textarea>
        @else
            <div class="cs-card text-center text-muted py-5">
                <i class="bi bi-inbox fs-1 d-block mb-2 opacity-50"></i>
                Tidak ada peserta bernama "{{ $q }}".
            </div>
        @endif
    @else
        <div class="cs-card text-center text-muted py-5">
            <i class="bi bi-search fs-1 d-block mb-2 opacity-50"></i>
            Ketik nama peserta lalu klik Cari.
        </div>
    @endif
</div>

<script>
    function copyText(t) {
        if (navigator.clipboard) { navigator.clipboard.writeText(t); return; }
        var ta = document.createElement('textarea'); ta.value = t; document.body.appendChild(ta); ta.select();
        try { document.execCommand('copy'); } catch (e) {} document.body.removeChild(ta);
    }
    document.querySelectorAll('.btn-copy').forEach(function (b) {
        b.addEventListener('click', function () {
            copyText(this.dataset.link);
            var old = this.innerHTML; this.innerHTML = '<i class="bi bi-check-lg"></i>';
            var self = this; setTimeout(function () { self.innerHTML = old; }, 1200);
        });
    });
    var all = document.getElementById('btn-copy-all');
    if (all) all.addEventListener('click', function () {
        var ta = document.getElementById('all-links');
        if (ta) { copyText(ta.value.trim()); this.innerHTML = '<i class="bi bi-check-lg me-1"></i> Tersalin!'; }
    });
</script>
@endsection
