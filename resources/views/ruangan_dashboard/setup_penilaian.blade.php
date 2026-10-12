@extends('layouts.app')

@section('title', 'Setup Penilaian')
@section('page-title', 'Setup Indikator Penilaian')

@section('content')
<style>
    :root { --maroon:#7c1316; --maroon-light:#a3191d; }
    .sp-head { background:linear-gradient(135deg,var(--maroon),var(--maroon-light)); color:#fff; border-radius:16px; padding:18px 22px; box-shadow:0 8px 24px rgba(124,19,22,.18); position:relative; overflow:hidden; }
    .sp-head::after { content:''; position:absolute; right:-40px; top:-40px; width:160px; height:160px; background:rgba(255,255,255,.07); border-radius:50%; }
    .sp-card { background:#fff; border:1px solid #eef2f7; border-radius:16px; padding:18px; box-shadow:0 2px 12px rgba(0,0,0,.04); }
    .ind-row { display:flex; gap:10px; align-items:center; margin-bottom:10px; }
    .ind-row input, .ind-row select { border-radius:10px; border:1.5px solid #e2e8f0; }
    .ind-row input:focus, .ind-row select:focus { border-color:var(--maroon); box-shadow:0 0 0 .2rem rgba(124,19,22,.12); }
    .ind-handle { color:#cbd5e1; font-size:1.1rem; cursor:default; }
</style>

<div class="container-fluid pb-4">

    <div class="sp-head d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <h5 class="fw-bold mb-1"><i class="bi bi-sliders me-2"></i>Setup Indikator Penilaian</h5>
            <div class="opacity-75 small"><i class="bi bi-door-open me-1"></i>{{ $ruangan->nm_ruangan }} — nilai akhir sertifikat = <b>rata-rata</b> semua indikator ini</div>
        </div>
        <a href="{{ route('kepala_ruangan.dashboard') }}" class="btn btn-light btn-sm rounded-pill fw-semibold"><i class="bi bi-arrow-left me-1"></i> Dashboard</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm"><i class="bi bi-check-circle me-1"></i>{{ session('success') }}</div>
    @endif

    <div class="sp-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="fw-bold text-dark"><i class="bi bi-list-check me-2" style="color:var(--maroon);"></i>Daftar Indikator</div>
            <button type="button" id="btn-add" class="btn btn-sm btn-outline-dark fw-semibold"><i class="bi bi-plus-lg me-1"></i>Tambah Indikator</button>
        </div>

        <form action="{{ route('kepala_ruangan.penilaian.sync') }}" method="POST" id="form-indikator"
              onsubmit="return confirm('Simpan perubahan indikator? Indikator yang dihapus akan hilang beserta nilainya di evaluasi berikutnya.');">
            @csrf

            <div class="row fw-bold small text-muted px-1 mb-1 d-none d-md-flex">
                <div class="col-md-5">Nama Indikator</div>
                <div class="col-md-5">Kategori (grup)</div>
                <div class="col-md-2"></div>
            </div>

            <div id="rows">
                @foreach($indikators as $ind)
                <div class="ind-row">
                    <input type="hidden" name="indikator[{{ $loop->index }}][id]" value="{{ $ind->id }}">
                    <div class="row g-2 flex-grow-1">
                        <div class="col-md-5">
                            <input type="text" name="indikator[{{ $loop->index }}][nama]" class="form-control" value="{{ $ind->nama }}" placeholder="mis. Keahlian Lain" required>
                        </div>
                        <div class="col-md-5">
                            <input type="text" name="indikator[{{ $loop->index }}][kategori]" class="form-control" value="{{ $ind->kategori }}" placeholder="mis. Keahlian & Kompetensi" list="kategori-list">
                        </div>
                        <div class="col-md-2 d-grid">
                            <button type="button" class="btn btn-outline-danger btn-remove"><i class="bi bi-trash"></i></button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <datalist id="kategori-list">
                <option value="Sikap & Kedisiplinan">
                <option value="Keahlian & Kompetensi">
            </datalist>

            <div class="d-flex justify-content-end gap-2 mt-3">
                <a href="{{ route('kepala_ruangan.dashboard') }}" class="btn btn-light border rounded-pill px-4">Batal</a>
                <button type="submit" class="btn text-white fw-bold rounded-pill px-4" style="background:var(--maroon);"><i class="bi bi-check-lg me-1"></i>Simpan Indikator</button>
            </div>
        </form>
    </div>
</div>

<script>
    (function () {
        var rows = document.getElementById('rows');
        var idx = {{ count($indikators) }};
        function rowHtml(i) {
            return '<div class="ind-row">'
                + '<div class="row g-2 flex-grow-1">'
                + '  <div class="col-md-5"><input type="text" name="indikator[' + i + '][nama]" class="form-control" placeholder="mis. Keahlian Lain" required></div>'
                + '  <div class="col-md-5"><input type="text" name="indikator[' + i + '][kategori]" class="form-control" placeholder="mis. Keahlian & Kompetensi" list="kategori-list"></div>'
                + '  <div class="col-md-2 d-grid"><button type="button" class="btn btn-outline-danger btn-remove"><i class="bi bi-trash"></i></button></div>'
                + '</div></div>';
        }
        document.getElementById('btn-add').addEventListener('click', function () {
            rows.insertAdjacentHTML('beforeend', rowHtml(idx++));
        });
        rows.addEventListener('click', function (e) {
            var btn = e.target.closest('.btn-remove');
            if (btn) { btn.closest('.ind-row').remove(); }
        });
    })();
</script>
@endsection
