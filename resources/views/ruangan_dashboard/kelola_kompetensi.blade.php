@extends('layouts.app')

@section('title', 'Kelola Kompetensi')
@section('page-title', 'Kelola Kompetensi Mahasiswa')

@section('content')
<style>
    :root { --maroon:#7c1316; --maroon-light:#a3191d; }
    .kk-head { background:linear-gradient(135deg,var(--maroon),var(--maroon-light)); color:#fff; border-radius:16px; padding:18px 22px; box-shadow:0 8px 24px rgba(124,19,22,.18); position:relative; overflow:hidden; }
    .kk-head::after { content:''; position:absolute; right:-40px; top:-40px; width:160px; height:160px; background:rgba(255,255,255,.07); border-radius:50%; }
    .kk-card { background:#fff; border:1px solid #eef2f7; border-radius:16px; padding:18px; box-shadow:0 2px 12px rgba(0,0,0,.04); max-width:720px; }
    .komp-row { display:flex; gap:10px; align-items:center; margin-bottom:10px; }
    .komp-row input { border-radius:10px; border:1.5px solid #e2e8f0; }
    .komp-row input:focus { border-color:var(--maroon); box-shadow:0 0 0 .2rem rgba(124,19,22,.12); }
</style>

<div class="container-fluid pb-4">

    <div class="kk-head d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <h5 class="fw-bold mb-1"><i class="bi bi-stars me-2"></i>Kelola Kompetensi</h5>
            <div class="opacity-75 small"><i class="bi bi-person me-1"></i>{{ $mahasiswa->nm_mahasiswa }} — {{ $mahasiswa->prodi ?: '-' }}</div>
        </div>
        <a href="{{ route('kepala_ruangan.dashboard') }}" class="btn btn-light btn-sm rounded-pill fw-semibold"><i class="bi bi-arrow-left me-1"></i> Dashboard</a>
    </div>

    <div class="kk-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="fw-bold text-dark"><i class="bi bi-list-check me-2" style="color:var(--maroon);"></i>Daftar Kompetensi / Keahlian</div>
            <button type="button" id="btn-add" class="btn btn-sm btn-outline-dark fw-semibold"><i class="bi bi-plus-lg me-1"></i>Tambah</button>
        </div>

        <form action="{{ route('kepala_ruangan.mahasiswa.kompetensi.update', $mahasiswa->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div id="rows">
                @forelse($kompetensi as $k)
                <div class="komp-row">
                    <input type="text" name="kompetensi[]" class="form-control" value="{{ $k }}" placeholder="mis. Pemasangan infus">
                    <button type="button" class="btn btn-outline-danger btn-remove"><i class="bi bi-trash"></i></button>
                </div>
                @empty
                <div class="komp-row">
                    <input type="text" name="kompetensi[]" class="form-control" placeholder="mis. Pemasangan infus">
                    <button type="button" class="btn btn-outline-danger btn-remove"><i class="bi bi-trash"></i></button>
                </div>
                @endforelse
            </div>

            <div class="d-flex justify-content-end gap-2 mt-3">
                <a href="{{ route('kepala_ruangan.dashboard') }}" class="btn btn-light border rounded-pill px-4">Batal</a>
                <button type="submit" class="btn text-white fw-bold rounded-pill px-4" style="background:var(--maroon);"><i class="bi bi-check-lg me-1"></i>Simpan Kompetensi</button>
            </div>
        </form>
    </div>
</div>

<script>
    (function () {
        var rows = document.getElementById('rows');
        var addBtn = document.getElementById('btn-add');
        if (!rows || !addBtn) return;
        function rowHtml() {
            return '<div class="komp-row">'
                + '<input type="text" name="kompetensi[]" class="form-control" placeholder="mis. Pemasangan infus">'
                + '<button type="button" class="btn btn-outline-danger btn-remove"><i class="bi bi-trash"></i></button>'
                + '</div>';
        }
        addBtn.addEventListener('click', function () { rows.insertAdjacentHTML('beforeend', rowHtml()); });
        rows.addEventListener('click', function (e) {
            var btn = e.target.closest('.btn-remove');
            if (btn && rows.querySelectorAll('.komp-row').length > 1) { btn.closest('.komp-row').remove(); }
            else if (btn) { btn.closest('.komp-row').querySelector('input').value = ''; }
        });
    })();
</script>
@endsection
