@extends('layouts.app')

@section('title', 'Booking Ruangan Instansi')

@section('content')
<style>
    :root { --maroon:#7c1316; --maroon-light:#a3191d; --maroon-subtle:#fcf0f1; --radius:14px; --shadow:0 4px 20px rgba(0,0,0,.05); }
    .hero { background:linear-gradient(135deg,#7c1316,#5f0f12); color:#fff; border-radius:20px; padding:24px 28px; margin-bottom:1.25rem; box-shadow:0 14px 34px rgba(124,19,22,.28); position:relative; overflow:hidden; }
    .hero::after { content:''; position:absolute; right:-40px; top:-40px; width:180px; height:180px; background:rgba(255,255,255,.07); border-radius:50%; pointer-events:none; }
    .hero h4 { font-weight:800; margin:0 0 4px; }
    .hero p { opacity:.9; margin:0; font-size:.88rem; }
    .hero .btn-light-h { background:#fff; color:var(--maroon); border:none; border-radius:50px; font-weight:700; padding:8px 18px; text-decoration:none; display:inline-flex; align-items:center; box-shadow:0 4px 12px rgba(0,0,0,.15); transition:.2s; }
    .hero .btn-light-h:hover { background:#fff; color:var(--maroon); text-decoration:none; transform:translateY(-1px); box-shadow:0 8px 18px rgba(0,0,0,.2); }
    .hero .btn-light-h i { text-decoration:none; }
    .hero .wait-pill { background:rgba(255,255,255,.18); color:#fff; border-radius:50px; padding:8px 16px; font-weight:700; font-size:.82rem; }
    .table-card { background:#fff; border-radius:var(--radius); box-shadow:var(--shadow); overflow:hidden; }
    .table { margin-bottom:0; }
    .table thead th { background:var(--maroon-subtle); color:var(--maroon); font-size:.72rem; text-transform:uppercase; letter-spacing:.4px; border:none; padding:14px 16px; }
    .table tbody td { padding:16px; border-top:1px solid #f1f5f9; vertical-align:middle; }
    .table tbody tr:hover { background:#fcfbfb; }
    .pill { padding:4px 12px; border-radius:20px; font-size:.72rem; font-weight:700; display:inline-flex; align-items:center; }
    .b-pending { background:#fef3c7; color:#b45309; } .b-approved { background:#dcfce7; color:#15803d; } .b-rejected { background:#fee2e2; color:#b91c1c; }
    .btn-maroon { background:var(--maroon); color:#fff; border:none; border-radius:8px; font-weight:600; }
    .btn-maroon:hover { background:var(--maroon-light); color:#fff; }
    .filter-sel { border-radius:10px; border:1px solid #e2e8f0; font-weight:600; color:#475569; }
    .th-sort { color:var(--maroon); text-decoration:none; display:inline-flex; align-items:center; gap:5px; white-space:nowrap; }
    .th-sort:hover { color:var(--maroon-light); }
    .th-sort i { font-size:.68rem; }
    .imp-badge { display:inline-block; background:#eef2ff; color:#4338ca; font-size:.6rem; font-weight:700; padding:1px 6px; border-radius:5px; vertical-align:middle; }
    /* baris hidup */
    .b-row.s-approved td:first-child { box-shadow:inset 4px 0 0 #16a34a; }
    .b-row.s-pending  td:first-child { box-shadow:inset 4px 0 0 #f59e0b; }
    .b-row.s-rejected td:first-child { box-shadow:inset 4px 0 0 #dc2626; }
    .room-chip { display:inline-flex; align-items:center; gap:7px; font-weight:800; color:#1f2937; }
    .room-dot { width:11px; height:11px; border-radius:3px; display:inline-block; flex-shrink:0; }
    .num-badge { display:inline-flex; align-items:center; justify-content:center; min-width:36px; height:30px; border-radius:9px; font-weight:800; background:var(--maroon-subtle); color:var(--maroon); padding:0 8px; }
    .num-soft { background:#eef2f7; color:#475569; }
    .fase { font-size:.62rem; font-weight:800; padding:2px 9px; border-radius:20px; letter-spacing:.3px; }
    .fase-berjalan { background:#dcfce7; color:#15803d; } .fase-selesai { background:#eef2f7; color:#64748b; } .fase-akan { background:#dbeafe; color:#1d4ed8; }
    .peek-btn { border:1px solid #e2e8f0; background:#fff; border-radius:50px; font-weight:700; font-size:.72rem; color:#475569; padding:3px 10px; }
    .peek-btn:hover { border-color:var(--maroon); color:var(--maroon); }
    .act-ic { width:34px; height:34px; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; border:1px solid #e2e8f0; background:#fff; color:#64748b; transition:.15s; }
    .act-ic:hover { background:#f8fafc; }
    .act-edit:hover { color:#b45309; border-color:#fcd34d; }
    .act-del:hover { color:#b91c1c; border-color:#fca5a5; }
    .inst-name { font-weight:700; color:#1f2937; }
    .prodi-tag { display:inline-block; margin-top:4px; background:var(--maroon-subtle); color:var(--maroon); font-size:.66rem; font-weight:700; padding:2px 8px; border-radius:6px; }
    .ruang-name { font-weight:700; color:#1f2937; }
    .act-wrap { display:flex; flex-direction:column; align-items:center; gap:6px; }
    .act-top { display:flex; gap:6px; }
    .btn-acc { background:#16a34a; color:#fff; border:none; border-radius:8px; font-weight:700; font-size:.78rem; padding:5px 14px; }
    .btn-acc:hover { background:#15803d; color:#fff; }
    .btn-tolak { background:#fff; color:#dc2626; border:1px solid #fca5a5; border-radius:8px; font-weight:700; font-size:.78rem; padding:5px 12px; }
    .btn-tolak:hover { background:#fee2e2; color:#b91c1c; }
    .batas-info { font-size:.72rem; color:#64748b; }
    .batas-info b { color:#1f2937; }
</style>

<div class="container-fluid py-3">
    <div class="hero d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <h4><i class="bi bi-calendar-check me-2"></i>Booking Ruangan Instansi</h4>
            <p>Permintaan booking kuota ruangan dari instansi mitra untuk anak magang.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="wait-pill"><i class="bi bi-hourglass-split me-1"></i>{{ $jmlPending }} menunggu</span>
            <a href="{{ route('admin.booking.kalender') }}" class="btn-light-h"><i class="bi bi-calendar3-week me-1"></i> Kalender</a>
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
                <div><span class="text-muted small d-block">Password</span>
                    @if(!empty($am['password']))<code style="font-size:.95rem;">{{ $am['password'] }}</code>
                    @else<span class="small text-muted">akun lama — password tetap seperti sebelumnya</span>@endif
                </div>
            </div>
        </div>
    @endif

    <div class="table-card">
        <div class="p-3 border-bottom">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="small fw-semibold text-muted mb-1">Cari (instansi/prodi)</label>
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm filter-sel" placeholder="Nama instansi / prodi...">
                </div>
                <div class="col-md-3">
                    <label class="small fw-semibold text-muted mb-1">Ruangan</label>
                    <select name="ruangan" class="form-select form-select-sm filter-sel">
                        <option value="">Semua Ruangan</option>
                        @foreach($ruanganOptions as $id => $nm)
                            <option value="{{ $id }}" {{ (string)request('ruangan')===(string)$id?'selected':'' }}>{{ $nm }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="small fw-semibold text-muted mb-1">Jenjang</label>
                    <select name="jenjang" class="form-select form-select-sm filter-sel">
                        <option value="">Semua</option>
                        @foreach($jenjangOptions as $j)
                            <option value="{{ $j }}" {{ request('jenjang')===$j?'selected':'' }}>{{ $j }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="small fw-semibold text-muted mb-1">Status</label>
                    <select name="status" class="form-select form-select-sm filter-sel">
                        <option value="">Semua</option>
                        <option value="pending" {{ request('status')==='pending'?'selected':'' }}>Menunggu</option>
                        <option value="approved" {{ request('status')==='approved'?'selected':'' }}>Disetujui</option>
                        <option value="rejected" {{ request('status')==='rejected'?'selected':'' }}>Ditolak</option>
                    </select>
                </div>
                <div class="col-6 col-md-1">
                    <label class="small fw-semibold text-muted mb-1">Dari</label>
                    <input type="date" name="start" value="{{ request('start') }}" class="form-control form-control-sm filter-sel">
                </div>
                <div class="col-6 col-md-1">
                    <label class="small fw-semibold text-muted mb-1">Sampai</label>
                    <input type="date" name="end" value="{{ request('end') }}" class="form-control form-control-sm filter-sel">
                </div>
                <div class="col-md-12 d-flex gap-2 mt-1">
                    <button type="submit" class="btn btn-sm text-white fw-semibold" style="background:#7c1316;"><i class="bi bi-funnel me-1"></i>Terapkan</button>
                    @if(request('q')||request('ruangan')||request('jenjang')||request('status')||request('start')||request('end'))
                        <a href="{{ route('admin.booking.index') }}" class="btn btn-sm btn-light border">Reset</a>
                    @endif
                    <a href="{{ route('admin.booking.kalender') }}" class="btn btn-sm btn-outline-dark ms-auto"><i class="bi bi-calendar-week me-1"></i>Lihat Kalender</a>
                </div>
            </form>
        </div>
        @php
            $arrow = function ($col) use ($sort, $dir) {
                if ($sort !== $col) return '<i class="bi bi-arrow-down-up" style="opacity:.35;"></i>';
                return $dir === 'asc' ? '<i class="bi bi-caret-up-fill"></i>' : '<i class="bi bi-caret-down-fill"></i>';
            };
            $slink = function ($col) use ($sort, $dir) {
                $nd = ($sort === $col && $dir === 'asc') ? 'desc' : 'asc';
                return request()->fullUrlWithQuery(['sort' => $col, 'dir' => $nd]);
            };
        @endphp
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead><tr>
                    <th><a href="{{ $slink('instansi') }}" class="th-sort">Instansi {!! $arrow('instansi') !!}</a></th>
                    <th><a href="{{ $slink('ruangan') }}" class="th-sort">Ruangan {!! $arrow('ruangan') !!}</a></th>
                    <th class="text-center"><a href="{{ $slink('kuota') }}" class="th-sort">Kuota {!! $arrow('kuota') !!}</a></th>
                    <th class="text-center"><a href="{{ $slink('peserta') }}" class="th-sort">Anak Magang {!! $arrow('peserta') !!}</a></th>
                    <th><a href="{{ $slink('periode') }}" class="th-sort">Periode {!! $arrow('periode') !!}</a></th>
                    <th class="text-center"><a href="{{ $slink('status') }}" class="th-sort">Status {!! $arrow('status') !!}</a></th>
                    <th class="text-center" width="16%">Aksi</th>
                </tr></thead>
                <tbody>
                    @php $palette = ['#7c1316','#1d4ed8','#15803d','#b45309','#7e22ce','#0e7490','#be123c','#4d7c0f','#0f766e','#9333ea']; @endphp
                    @forelse($bookings as $b)
                    @php
                        $isImpor = \Illuminate\Support\Str::contains((string)$b->keterangan, '[IMPOR-MAPPING]');
                        $ketBersih = trim(str_replace('[IMPOR-MAPPING]', '', (string)$b->keterangan));
                        $rclr = $palette[($b->ruangan_id ?? 0) % count($palette)];
                        $pr = $b->prodi ?: $b->jenjang;
                        if ($b->jenjang && $b->prodi && stripos($b->prodi, $b->jenjang) !== 0) $pr = $b->jenjang.' '.$b->prodi;
                        $m = optional($b->tanggal_mulai); $s = optional($b->tanggal_selesai); $tdy = now()->startOfDay();
                        $fase = (!$m || !$s) ? null : ($s->lt($tdy) ? ['Selesai','selesai'] : ($m->gt($tdy) ? ['Akan Datang','akan'] : ['Berjalan','berjalan']));
                    @endphp
                    <tr class="b-row s-{{ $b->status }}">
                        <td>
                            <div class="inst-name">{{ optional($b->mou)->nama_instansi ?? optional($b->mou)->nama_universitas ?? '-' }}</div>
                            @if($isImpor)<span class="imp-badge mt-1" title="Dari impor mapping manual">IMPOR</span>@endif
                        </td>
                        <td>
                            <span class="room-chip"><span class="room-dot" style="background:{{ $rclr }};"></span>{{ optional($b->ruangan)->nm_ruangan ?? '-' }}</span>
                            @if($pr || $b->semester)
                                <div><span class="prodi-tag">{{ $pr ?: '-' }}{{ $b->semester ? ' · smt '.$b->semester : '' }}</span></div>
                            @endif
                        </td>
                        <td class="text-center"><span class="num-badge">{{ $b->jumlah_peserta }}</span></td>
                        <td class="text-center">
                            @if($b->pesertas->count())
                                <button class="peek-btn" data-bs-toggle="collapse" data-bs-target="#peserta-{{ $b->id }}"><i class="bi bi-people me-1"></i>{{ $b->pesertas->count() }}</button>
                            @else
                                <span class="num-badge num-soft">0</span>
                            @endif
                        </td>
                        <td class="text-nowrap">
                            <div class="small fw-semibold text-dark"><i class="bi bi-calendar3 me-1 text-muted"></i>{{ $m->format('d/m/Y') ?? '-' }} – {{ $s->format('d/m/Y') ?? '-' }}</div>
                            @if($fase)<span class="fase fase-{{ $fase[1] }} mt-1">{{ $fase[0] }}</span>@endif
                            @if($ketBersih)<div class="text-muted" style="font-size:.7rem;">{{ \Illuminate\Support\Str::limit($ketBersih, 50) }}</div>@endif
                        </td>
                        <td class="text-center"><span class="pill b-{{ $b->status }}">{{ ['pending'=>'Menunggu','approved'=>'Disetujui','rejected'=>'Ditolak'][$b->status] ?? $b->status }}</span>
                            @if($b->status==='rejected' && $b->catatan_admin)<div class="text-muted mt-1" style="font-size:.68rem;">{{ $b->catatan_admin }}</div>@endif
                        </td>
                        <td class="text-center">
                          <div class="act-wrap">
                            <div class="act-top">
                                <a href="{{ route('admin.booking.edit', $b->id) }}" class="act-ic act-edit" title="Edit booking"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('admin.booking.destroy', $b->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus booking ini beserta daftar pesertanya?');">
                                    @csrf @method('DELETE')
                                    <button class="act-ic act-del" title="Hapus booking"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                            @if($b->status === 'pending')
                                <div class="act-top">
                                    <button class="btn-acc" data-bs-toggle="collapse" data-bs-target="#acc-{{ $b->id }}"><i class="bi bi-check-lg me-1"></i>ACC</button>
                                    <button class="btn-tolak" data-bs-toggle="collapse" data-bs-target="#rej-{{ $b->id }}"><i class="bi bi-x-lg"></i></button>
                                </div>
                                <div class="collapse mt-1 text-start w-100" id="acc-{{ $b->id }}">
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
                                <div class="batas-info">Batas isi: <b>{{ optional($b->batas_pengisian)->format('d/m/Y') ?: 'tidak dibatasi' }}</b></div>
                                <button class="btn-tolak" style="color:var(--maroon);border-color:#f3dede;" data-bs-toggle="collapse" data-bs-target="#batas-{{ $b->id }}"><i class="bi bi-calendar-event me-1"></i>Atur Batas</button>
                                <div class="collapse mt-1 text-start w-100" id="batas-{{ $b->id }}">
                                    <form action="{{ route('admin.booking.batas', $b->id) }}" method="POST">
                                        @csrf
                                        <input type="date" name="batas_pengisian" class="form-control form-control-sm mb-1" value="{{ optional($b->batas_pengisian)->format('Y-m-d') }}">
                                        <button class="btn btn-sm btn-outline-dark w-100">Simpan Batas</button>
                                    </form>
                                </div>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                          </div>
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
