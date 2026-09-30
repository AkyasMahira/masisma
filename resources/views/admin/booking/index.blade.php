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
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.booking.kalender') }}" class="btn btn-maroon btn-sm"><i class="bi bi-calendar3-week me-1"></i> Kalender</a>
            <span class="pill b-pending"><i class="bi bi-hourglass-split me-1"></i>{{ $jmlPending }} menunggu</span>
        </div>
    </div>

    @if(session('success'))<div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>@endif
    @if(session('error'))<div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>@endif
    @if(session('akun_mahasiswa'))
        @php $am = session('akun_mahasiswa'); @endphp
        <div class="alert alert-success border-0 shadow-sm" style="border-left:5px solid #15803d !important; border-radius:10px;">
            <h6 class="fw-bold mb-2"><i class="bi bi-person-badge me-1"></i> Akun Mahasiswa Dibuat — {{ $am['nama'] }}</h6>
            <div class="small mb-2 text-muted">Serahkan kredensial ini ke anak magang. <strong>Password hanya ditampilkan sekali.</strong></div>
            <div class="d-flex flex-wrap gap-4">
                <div><span class="text-muted small d-block">Username (login)</span><code style="font-size:.95rem;">{{ $am['username'] }}</code></div>
                <div><span class="text-muted small d-block">Password</span><code style="font-size:.95rem;">{{ $am['password'] }}</code></div>
            </div>
        </div>
    @endif

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
                <thead><tr><th>Instansi</th><th>Ruangan</th><th class="text-center">Kuota</th><th class="text-center">Anak Magang</th><th>Periode</th><th class="text-center">Status</th><th class="text-center" width="16%">Aksi</th></tr></thead>
                <tbody>
                    @forelse($bookings as $b)
                    <tr>
                        <td class="fw-semibold">{{ optional($b->mou)->nama_instansi ?? optional($b->mou)->nama_universitas ?? '-' }}</td>
                        <td>{{ optional($b->ruangan)->nm_ruangan ?? '-' }}
                            @if($b->prodi || $b->jenjang || $b->semester)
                                <div class="text-muted" style="font-size:.68rem;">{{ trim(($b->jenjang ? $b->jenjang.' ' : '').($b->prodi ?? '')) ?: '-' }}{{ $b->semester ? ' · smt '.$b->semester : '' }}</div>
                            @endif
                        </td>
                        <td class="text-center fw-bold">{{ $b->jumlah_peserta }}</td>
                        <td class="text-center">
                            @if($b->pesertas->count())
                                <button class="btn btn-sm btn-outline-dark rounded-pill" data-bs-toggle="collapse" data-bs-target="#peserta-{{ $b->id }}"><i class="bi bi-people me-1"></i>{{ $b->pesertas->count() }} lihat</button>
                            @else
                                <span class="text-muted small">0</span>
                            @endif
                        </td>
                        <td class="small">{{ optional($b->tanggal_mulai)->format('d/m/Y') }} - {{ optional($b->tanggal_selesai)->format('d/m/Y') }}
                            @if($b->keterangan)<div class="text-muted" style="font-size:.72rem;">{{ \Illuminate\Support\Str::limit($b->keterangan, 60) }}</div>@endif
                        </td>
                        <td class="text-center"><span class="pill b-{{ $b->status }}">{{ ['pending'=>'Menunggu','approved'=>'Disetujui','rejected'=>'Ditolak'][$b->status] ?? $b->status }}</span>
                            @if($b->status!=='pending' && $b->catatan_admin)<div class="text-muted mt-1" style="font-size:.68rem;">{{ $b->catatan_admin }}</div>@endif
                        </td>
                        <td class="text-center">
                            <div class="mb-2 d-flex justify-content-center gap-1">
                                <a href="{{ route('admin.booking.edit', $b->id) }}" class="btn btn-sm btn-outline-dark" title="Edit booking"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('admin.booking.destroy', $b->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus booking ini beserta daftar pesertanya?');">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" title="Hapus booking"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                            @if($b->status === 'pending')
                                <button class="btn btn-sm btn-success" data-bs-toggle="collapse" data-bs-target="#acc-{{ $b->id }}"><i class="bi bi-check-lg"></i> ACC</button>
                                <button class="btn btn-sm btn-outline-danger" data-bs-toggle="collapse" data-bs-target="#rej-{{ $b->id }}"><i class="bi bi-x-lg"></i></button>
                                <div class="collapse mt-2 text-start" id="acc-{{ $b->id }}">
                                    <form action="{{ route('admin.booking.approve', $b->id) }}" method="POST">
                                        @csrf
                                        <label class="small fw-semibold mb-1">Batas pengisian peserta <span class="text-muted">(opsional)</span></label>
                                        <input type="date" name="batas_pengisian" class="form-control form-control-sm mb-1">
                                        <button class="btn btn-sm btn-success w-100">Setujui Booking</button>
                                    </form>
                                </div>
                                <div class="collapse mt-2 text-start" id="rej-{{ $b->id }}">
                                    <form action="{{ route('admin.booking.reject', $b->id) }}" method="POST">
                                        @csrf
                                        <textarea name="catatan_admin" class="form-control form-control-sm mb-1" rows="2" placeholder="Alasan penolakan..." required></textarea>
                                        <button class="btn btn-sm btn-danger w-100">Tolak</button>
                                    </form>
                                </div>
                            @elseif($b->status === 'approved')
                                <div class="small mb-1">Batas isi: <strong>{{ optional($b->batas_pengisian)->format('d/m/Y') ?: 'tidak dibatasi' }}</strong></div>
                                <button class="btn btn-sm btn-outline-dark" data-bs-toggle="collapse" data-bs-target="#batas-{{ $b->id }}"><i class="bi bi-calendar-event"></i> Atur Batas</button>
                                <div class="collapse mt-2 text-start" id="batas-{{ $b->id }}">
                                    <form action="{{ route('admin.booking.batas', $b->id) }}" method="POST">
                                        @csrf
                                        <input type="date" name="batas_pengisian" class="form-control form-control-sm mb-1" value="{{ optional($b->batas_pengisian)->format('Y-m-d') }}">
                                        <button class="btn btn-sm btn-outline-dark w-100">Simpan Batas</button>
                                    </form>
                                </div>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                    </tr>
                    @if($b->pesertas->count())
                    <tr class="collapse" id="peserta-{{ $b->id }}">
                        <td colspan="7" class="bg-light">
                            <div class="small fw-bold mb-2" style="color:var(--maroon);"><i class="bi bi-people-fill me-1"></i>Daftar Anak Magang ({{ $b->pesertas->count() }})</div>
                            <div class="table-responsive">
                                <table class="table table-sm mb-0 bg-white align-middle">
                                    <thead><tr><th>No</th><th>Nama</th><th>NIM</th><th>Prodi</th><th>JK</th><th>No. HP</th><th class="text-center">Status</th><th class="text-center">Aksi (ACC → akun mahasiswa)</th></tr></thead>
                                    <tbody>
                                        @foreach($b->pesertas as $i => $p)
                                        <tr>
                                            <td>{{ $i+1 }}</td><td>{{ $p->nama }}</td><td>{{ $p->nim ?: '-' }}</td><td>{{ $p->prodi ?: '-' }}</td><td>{{ $p->jenis_kelamin ?: '-' }}</td><td>{{ $p->no_hp ?: '-' }}</td>
                                            <td class="text-center"><span class="pill b-{{ $p->status ?? 'pending' }}">{{ ['pending'=>'Menunggu','approved'=>'Jadi Mahasiswa','rejected'=>'Ditolak'][$p->status ?? 'pending'] ?? $p->status }}</span></td>
                                            <td class="text-center">
                                                @if(($p->status ?? 'pending') === 'pending')
                                                    <form action="{{ route('admin.booking.peserta.approve', $p->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Setujui & buat akun mahasiswa untuk {{ $p->nama }}?');">
                                                        @csrf<button class="btn btn-sm btn-success"><i class="bi bi-person-check"></i> ACC</button>
                                                    </form>
                                                    <button class="btn btn-sm btn-outline-danger" data-bs-toggle="collapse" data-bs-target="#rejp-{{ $p->id }}"><i class="bi bi-x-lg"></i></button>
                                                    <div class="collapse mt-1 text-start" id="rejp-{{ $p->id }}">
                                                        <form action="{{ route('admin.booking.peserta.reject', $p->id) }}" method="POST">
                                                            @csrf
                                                            <textarea name="catatan_admin" class="form-control form-control-sm mb-1" rows="1" placeholder="Alasan..." required></textarea>
                                                            <button class="btn btn-sm btn-danger w-100">Tolak</button>
                                                        </form>
                                                    </div>
                                                @else
                                                    <span class="text-muted small">{{ $p->catatan_admin ?: '-' }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </td>
                    </tr>
                    @endif
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-5"><i class="bi bi-inbox fs-3 d-block mb-2"></i>Belum ada booking.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $bookings->links() }}</div>
    </div>
</div>
@endsection
