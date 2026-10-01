@extends('layouts.app')

@section('title', 'Booking Ruangan')

@section('content')
<style>
    :root { --maroon:#7c1316; --maroon-light:#a3191d; --maroon-subtle:#fcf0f1; }
    .form-card { background:#fff; border-radius:16px; box-shadow:0 10px 30px rgba(0,0,0,.08); max-width:760px; margin:0 auto; overflow:hidden; }
    .card-head { background:var(--maroon); color:#fff; padding:1.2rem 1.5rem; border-bottom:4px solid var(--maroon-light); }
    .form-label { font-weight:600; font-size:.88rem; color:#2c3e50; }
    .form-control, .form-select { border-radius:10px; padding:.6rem .8rem; }
    .form-control:focus, .form-select:focus { border-color:var(--maroon); box-shadow:0 0 0 .2rem rgba(124,19,22,.12); }
    .btn-maroon { background:var(--maroon); color:#fff; border:none; border-radius:10px; padding:10px 20px; font-weight:700; }
    .btn-maroon:hover { background:var(--maroon-light); color:#fff; }
    .btn-light-c { background:#f1f5f9; color:#475569; border:none; border-radius:10px; padding:10px 20px; font-weight:600; text-decoration:none; }
    .room-hint { font-size:.8rem; }
</style>

<div class="container-fluid py-4">
    <div class="form-card">
        <div class="card-head"><h5 class="mb-0 fw-bold"><i class="bi bi-calendar-plus me-2"></i>Booking Ruangan untuk Anak Magang</h5></div>

        @if(session('error'))<div class="alert alert-danger m-3 mb-0">{{ session('error') }}</div>@endif
        @if($errors->any())<div class="alert alert-danger m-3 mb-0"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif

        <form action="{{ route('instansi.booking.store') }}" method="POST" class="p-4">
            @csrf
            <div class="mb-3">
                <label class="form-label">Ruangan Tujuan <span class="text-danger">*</span></label>
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

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Jumlah Peserta <span class="text-danger">*</span></label>
                    <input type="number" name="jumlah_peserta" class="form-control" min="1" value="{{ old('jumlah_peserta', 1) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal_mulai" class="form-control" value="{{ old('tanggal_mulai') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tanggal Selesai <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal_selesai" class="form-control" value="{{ old('tanggal_selesai') }}" required>
                </div>
            </div>

            <div class="mt-3">
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

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('instansi.dashboard') }}" class="btn-light-c">Batal</a>
                <button type="submit" class="btn-maroon"><i class="bi bi-send me-1"></i> Kirim Permintaan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function tambahKD(){
        var w=document.getElementById('kdWrap');
        var i=document.createElement('input');
        i.type='text'; i.name='kompetensi_dimiliki[]'; i.className='form-control mb-2'; i.placeholder='Kompetensi lain';
        w.appendChild(i);
    }
</script>
@endsection
