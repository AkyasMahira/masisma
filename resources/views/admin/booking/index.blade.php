@extends('layouts.app')

@section('title', 'Booking Ruangan Instansi')

@section('content')
<style>
    :root { --maroon:#7c1316; --maroon-light:#a3191d; --maroon-subtle:#fcf0f1; --radius:12px; --shadow:0 4px 20px rgba(0,0,0,.05); }
    .header-card { background:#fff; border-radius:var(--radius); border-left:5px solid var(--maroon); padding:20px; margin-bottom:20px; box-shadow:0 2px 4px rgba(0,0,0,.05); }
    .table-card { background:#fff; border-radius:var(--radius); box-shadow:var(--shadow); overflow:hidden; }
    .table thead th { background:var(--maroon-subtle); color:var(--maroon); font-size:.78rem; text-transform:uppercase; }
    .pill { padding:3px 10px; border-radius:20px; font-size:.72rem; font-weight:700; }
    .b-pending { background:#fef3c7; color:#b45309; } .b-approved { background:#dcfce7; color:#15803d; } .b-rejected { background:#fee2e2; color:#b91c1c; }
    .btn-maroon { background:var(--maroon); color:#fff; border:none; border-radius:8px; font-weight:600; }
    .btn-maroon:hover { background:var(--maroon-light); color:#fff; }
</style>

<div class="container-fluid py-3">
    <div class="header-card d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <h4 class="mb-1 fw-bold" style="color:var(--maroon);"><i class="bi bi-calendar-check me-2"></i>Booking Ruangan Instansi</h4>
            <p class="mb-0 text-muted small">Permintaan booking kuota ruangan dari instansi mitra untuk anak magang.</p>
        </div>
        <span class="pill b-pending"><i class="bi bi-hourglass-split me-1"></i>{{ $jmlPending }} menunggu</span>
    </div>

    @if(session('success'))<div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>@endif
    @if(session('error'))<div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>@endif

    <div class="table-card">
        <div class="p-3 border-bottom">
            <form method="GET" class="d-flex gap-2" style="max-width:280px;">
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Semua status</option>
                    <option value="pending" {{ request('status')==='pending'?'selected':'' }}>Menunggu</option>
                    <option value="approved" {{ request('status')==='approved'?'selected':'' }}>Disetujui</option>
                    <option value="rejected" {{ request('status')==='rejected'?'selected':'' }}>Ditolak</option>
                </select>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead><tr><th>Instansi</th><th>Ruangan</th><th class="text-center">Peserta</th><th>Periode</th><th class="text-center">Status</th><th class="text-center" width="18%">Aksi</th></tr></thead>
                <tbody>
                    @forelse($bookings as $b)
                    <tr>
                        <td class="fw-semibold">{{ optional($b->mou)->nama_instansi ?? optional($b->mou)->nama_universitas ?? '-' }}</td>
                        <td>{{ optional($b->ruangan)->nm_ruangan ?? '-' }}</td>
                        <td class="text-center fw-bold">{{ $b->jumlah_peserta }}</td>
                        <td class="small">{{ optional($b->tanggal_mulai)->format('d/m/Y') }} - {{ optional($b->tanggal_selesai)->format('d/m/Y') }}
                            @if($b->keterangan)<div class="text-muted" style="font-size:.72rem;">{{ \Illuminate\Support\Str::limit($b->keterangan, 60) }}</div>@endif
                        </td>
                        <td class="text-center"><span class="pill b-{{ $b->status }}">{{ ['pending'=>'Menunggu','approved'=>'Disetujui','rejected'=>'Ditolak'][$b->status] ?? $b->status }}</span>
                            @if($b->status!=='pending' && $b->catatan_admin)<div class="text-muted mt-1" style="font-size:.68rem;">{{ $b->catatan_admin }}</div>@endif
                        </td>
                        <td class="text-center">
                            @if($b->status === 'pending')
                                <form action="{{ route('admin.booking.approve', $b->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Setujui booking ini?');">
                                    @csrf<button class="btn btn-sm btn-success"><i class="bi bi-check-lg"></i></button>
                                </form>
                                <button class="btn btn-sm btn-outline-danger" data-bs-toggle="collapse" data-bs-target="#rej-{{ $b->id }}"><i class="bi bi-x-lg"></i></button>
                                <div class="collapse mt-2 text-start" id="rej-{{ $b->id }}">
                                    <form action="{{ route('admin.booking.reject', $b->id) }}" method="POST">
                                        @csrf
                                        <textarea name="catatan_admin" class="form-control form-control-sm mb-1" rows="2" placeholder="Alasan penolakan..." required></textarea>
                                        <button class="btn btn-sm btn-danger w-100">Tolak</button>
                                    </form>
                                </div>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-5"><i class="bi bi-inbox fs-3 d-block mb-2"></i>Belum ada booking.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $bookings->links() }}</div>
    </div>
</div>
@endsection
