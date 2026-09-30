@extends('layouts.app')

@section('title', 'Edit Booking Ruangan')

@section('content')
<style>
    :root { --maroon:#7c1316; --maroon-light:#a3191d; --radius:16px; }
    .form-card { background:#fff; border-radius:var(--radius); box-shadow:0 10px 30px rgba(0,0,0,.08); max-width:760px; margin:0 auto; overflow:hidden; }
    .card-head { background:var(--maroon); color:#fff; padding:1.2rem 1.5rem; border-bottom:4px solid var(--maroon-light); }
    .form-label { font-weight:600; font-size:.88rem; color:#2c3e50; }
    .form-control, .form-select { border-radius:10px; padding:.6rem .8rem; }
    .form-control:focus, .form-select:focus { border-color:var(--maroon); box-shadow:0 0 0 .2rem rgba(124,19,22,.12); }
    .btn-maroon { background:var(--maroon); color:#fff; border:none; border-radius:10px; padding:10px 20px; font-weight:700; }
    .btn-maroon:hover { background:var(--maroon-light); color:#fff; }
    .btn-light-c { background:#f1f5f9; color:#475569; border:none; border-radius:10px; padding:10px 20px; font-weight:600; text-decoration:none; }
</style>

<div class="container-fluid py-4">
    <div class="form-card">
        <div class="card-head"><h5 class="mb-0 fw-bold"><i class="bi bi-pencil-square me-2"></i>Edit Booking — {{ optional($booking->mou)->nama_instansi ?? optional($booking->mou)->nama_universitas }}</h5></div>

        @if(session('error'))<div class="alert alert-danger m-3 mb-0">{{ session('error') }}</div>@endif
        @if($errors->any())<div class="alert alert-danger m-3 mb-0"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif

        <form action="{{ route('admin.booking.update', $booking->id) }}" method="POST" class="p-4">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label">Ruangan <span class="text-danger">*</span></label>
                <select name="ruangan_id" class="form-select" required>
                    @foreach($ruangans as $r)
                        <option value="{{ $r->id }}" {{ (string)old('ruangan_id', $booking->ruangan_id)===(string)$r->id ? 'selected':'' }}>{{ $r->nm_ruangan }}</option>
                    @endforeach
                </select>
            </div>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Jumlah Peserta <span class="text-danger">*</span></label>
                    <input type="number" name="jumlah_peserta" class="form-control" min="1" value="{{ old('jumlah_peserta', $booking->jumlah_peserta) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal_mulai" class="form-control" value="{{ old('tanggal_mulai', optional($booking->tanggal_mulai)->format('Y-m-d')) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tanggal Selesai <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal_selesai" class="form-control" value="{{ old('tanggal_selesai', optional($booking->tanggal_selesai)->format('Y-m-d')) }}" required>
                </div>
            </div>
            <div class="row g-3 mt-0">
                <div class="col-md-6">
                    <label class="form-label">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select" required>
                        <option value="pending" {{ old('status',$booking->status)==='pending'?'selected':'' }}>Menunggu</option>
                        <option value="approved" {{ old('status',$booking->status)==='approved'?'selected':'' }}>Disetujui</option>
                        <option value="rejected" {{ old('status',$booking->status)==='rejected'?'selected':'' }}>Ditolak</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Batas Pengisian Peserta</label>
                    <input type="date" name="batas_pengisian" class="form-control" value="{{ old('batas_pengisian', optional($booking->batas_pengisian)->format('Y-m-d')) }}">
                </div>
            </div>
            <div class="mt-3">
                <label class="form-label">Keterangan Instansi</label>
                <textarea name="keterangan" class="form-control" rows="2">{{ old('keterangan', $booking->keterangan) }}</textarea>
            </div>
            <div class="mt-3">
                <label class="form-label">Catatan Admin</label>
                <textarea name="catatan_admin" class="form-control" rows="2">{{ old('catatan_admin', $booking->catatan_admin) }}</textarea>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('admin.booking.index') }}" class="btn-light-c">Batal</a>
                <button type="submit" class="btn-maroon"><i class="bi bi-check-lg me-1"></i> Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection
