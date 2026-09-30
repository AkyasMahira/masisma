@extends('layouts.app')

@section('title', 'Tambah Unsur Evaluasi')

@section('content')
<style>
    :root { --custom-maroon:#7c1316; --custom-maroon-light:#a3191d; --card-radius:16px; }
    .form-card { border:none; border-radius:var(--card-radius); box-shadow:0 10px 30px rgba(0,0,0,.08); background:#fff; overflow:hidden; max-width:720px; margin:0 auto; }
    .card-header-custom { background:var(--custom-maroon); padding:1.3rem 1.5rem; color:#fff; border-bottom:4px solid var(--custom-maroon-light); }
    .form-label { font-weight:600; color:#2c3e50; font-size:.9rem; }
    .form-control, .form-select { border-radius:10px; padding:.6rem .8rem; }
    .form-control:focus, .form-select:focus { border-color:var(--custom-maroon); box-shadow:0 0 0 .2rem rgba(124,19,22,.12); }
    .btn-maroon { background:var(--custom-maroon); color:#fff; border:none; border-radius:10px; padding:10px 20px; font-weight:600; }
    .btn-maroon:hover { background:var(--custom-maroon-light); color:#fff; }
    .btn-light-custom { background:#f1f5f9; color:#475569; border:none; border-radius:10px; padding:10px 20px; font-weight:600; text-decoration:none; }
</style>

<div class="container-fluid py-4">
    <div class="form-card">
        <div class="card-header-custom">
            <h5 class="mb-0 fw-bold"><i class="bi bi-plus-circle me-2"></i>Tambah Unsur Evaluasi</h5>
        </div>
        <form action="{{ route('admin.master_evaluasi.store') }}" method="POST" class="p-4">
            @csrf
            @include('admin.master_evaluasi._form', ['unsur' => null])
            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('admin.master_evaluasi.index') }}" class="btn-light-custom">Batal</a>
                <button type="submit" class="btn-maroon"><i class="bi bi-check-lg me-1"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
