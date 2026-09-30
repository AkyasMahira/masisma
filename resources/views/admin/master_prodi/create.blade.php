@extends('layouts.app')
@section('title', 'Tambah Program Studi')
@section('content')
<style>
    :root { --maroon:#7c1316; --maroon-light:#a3191d; }
    .form-card { background:#fff; border-radius:16px; box-shadow:0 10px 30px rgba(0,0,0,.08); max-width:680px; margin:0 auto; overflow:hidden; }
    .card-head { background:var(--maroon); color:#fff; padding:1.2rem 1.5rem; border-bottom:4px solid var(--maroon-light); }
    .form-label { font-weight:600; font-size:.88rem; color:#2c3e50; }
    .form-control, .form-select { border-radius:10px; }
    .form-control:focus, .form-select:focus { border-color:var(--maroon); box-shadow:0 0 0 .2rem rgba(124,19,22,.12); }
    .btn-maroon { background:var(--maroon); color:#fff; border:none; border-radius:10px; padding:10px 20px; font-weight:700; }
    .btn-maroon:hover { background:var(--maroon-light); color:#fff; }
    .btn-light-c { background:#f1f5f9; color:#475569; border:none; border-radius:10px; padding:10px 20px; font-weight:600; text-decoration:none; }
</style>
<div class="container-fluid py-4">
    <div class="form-card">
        <div class="card-head"><h5 class="mb-0 fw-bold"><i class="bi bi-plus-circle me-2"></i>Tambah Program Studi</h5></div>
        <form action="{{ route('admin.master_prodi.store') }}" method="POST" class="p-4">
            @csrf
            @include('admin.master_prodi._form', ['prodi' => null])
            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('admin.master_prodi.index') }}" class="btn-light-c">Batal</a>
                <button type="submit" class="btn-maroon"><i class="bi bi-check-lg me-1"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
