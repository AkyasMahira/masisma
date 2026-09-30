@extends('layouts.app')

@section('title', 'Portal Instansi Mitra')

@section('content')
<style>
    :root { --maroon:#7c1316; --maroon-light:#a3191d; --maroon-subtle:#fcf0f1; --radius:14px; --shadow:0 4px 20px rgba(0,0,0,.05); }
    .hero { background: linear-gradient(135deg,#7c1316,#5f0f12); color:#fff; border-radius:20px; padding:26px 30px; margin-bottom:1.5rem; box-shadow:0 14px 34px rgba(124,19,22,.28); position:relative; overflow:hidden; }
    .hero::after { content:''; position:absolute; right:-40px; top:-40px; width:180px; height:180px; background:rgba(255,255,255,.06); border-radius:50%; }
    .hero h4 { font-weight:800; margin:0 0 6px; }
    .hero p { opacity:.9; margin:0; font-size:.9rem; }
    .stat { background:#fff; border-radius:var(--radius); box-shadow:var(--shadow); padding:18px; text-align:center; }
    .stat .n { font-size:1.8rem; font-weight:800; }
    .stat .l { color:#64748b; font-size:.78rem; font-weight:600; text-transform:uppercase; }
    .card-soft { background:#fff; border-radius:var(--radius); box-shadow:var(--shadow); }
    .btn-maroon { background:var(--maroon); color:#fff; border:none; border-radius:10px; padding:10px 18px; font-weight:700; }
    .btn-maroon:hover { background:var(--maroon-light); color:#fff; }
    .table thead th { background:var(--maroon-subtle); color:var(--maroon); font-size:.78rem; text-transform:uppercase; }
    .b-pending { background:#fef3c7; color:#b45309; } .b-approved { background:#dcfce7; color:#15803d; } .b-rejected { background:#fee2e2; color:#b91c1c; }
    .pill { padding:3px 10px; border-radius:20px; font-size:.72rem; font-weight:700; }
</style>

<div class="container-fluid py-3">
    <div class="hero d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <h4><i class="bi bi-buildings me-2"></i>{{ $mou->nama_instansi ?: $mou->nama_universitas }}</h4>
            <p><i class="bi bi-file-earmark-text me-1"></i> Portal Mitra RSUD Simpang Lima Gumul · MOU {{ optional($mou->tanggal_masuk)->format('d/m/Y') }} - {{ optional($mou->tanggal_keluar)->format('d/m/Y') }}</p>
        </div>
        <a href="{{ route('instansi.booking.create') }}" class="btn btn-light fw-bold rounded-pill px-4"><i class="bi bi-plus-circle me-1"></i> Booking Ruangan</a>
    </div>

    @if(session('success'))<div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>@endif
    @if(session('error'))<div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>@endif

    <div class="row g-3 mb-3">
        <div class="col-6 col-lg-3"><div class="stat"><div class="l">Menunggu</div><div class="n text-warning">{{ $stat['pending'] }}</div></div></div>
        <div class="col-6 col-lg-3"><div class="stat"><div class="l">Disetujui</div><div class="n text-success">{{ $stat['approved'] }}</div></div></div>
        <div class="col-6 col-lg-3"><div class="stat"><div class="l">Ditolak</div><div class="n text-danger">{{ $stat['rejected'] }}</div></div></div>
        <div class="col-6 col-lg-3"><div class="stat"><div class="l">Total Peserta ACC</div><div class="n" style="color:var(--maroon);">{{ $stat['peserta'] }}</div></div></div>
    </div>

    <div class="card-soft">
        <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold"><i class="bi bi-calendar-check me-1"></i> Riwayat Booking Ruangan</h6>
            <a href="{{ route('instansi.booking.create') }}" class="btn-maroon btn-sm"><i class="bi bi-plus-lg"></i> Ajukan Baru</a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead><tr><th>Ruangan</th><th class="text-center">Peserta</th><th>Periode</th><th class="text-center">Status</th><th>Catatan Admin</th></tr></thead>
                <tbody>
                    @forelse($bookings as $b)
                    <tr>
                        <td class="fw-semibold">{{ optional($b->ruangan)->nm_ruangan ?? '-' }}</td>
                        <td class="text-center">{{ $b->jumlah_peserta }}</td>
                        <td class="small">{{ optional($b->tanggal_mulai)->format('d/m/Y') }} - {{ optional($b->tanggal_selesai)->format('d/m/Y') }}</td>
                        <td class="text-center"><span class="pill b-{{ $b->status }}">{{ ['pending'=>'Menunggu','approved'=>'Disetujui','rejected'=>'Ditolak'][$b->status] ?? $b->status }}</span></td>
                        <td class="small text-muted">{{ $b->catatan_admin ?: '-' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center text-muted py-5"><i class="bi bi-inbox fs-3 d-block mb-2"></i>Belum ada booking. Klik "Booking Ruangan" untuk mengajukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
