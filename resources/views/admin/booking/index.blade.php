@extends('layouts.app')

@section('title', 'Booking Ruangan Instansi')

@section('content')
<style>
    :root { --maroon:#7c1316; --maroon-light:#a3191d; --maroon-subtle:#fcf0f1; }
    .hero { background:linear-gradient(135deg,#7c1316,#5f0f12); color:#fff; border-radius:18px; padding:20px 24px; margin-bottom:1rem; box-shadow:0 12px 30px rgba(124,19,22,.26); position:relative; overflow:hidden; }
    .hero::after { content:''; position:absolute; right:-40px; top:-40px; width:170px; height:170px; background:rgba(255,255,255,.07); border-radius:50%; pointer-events:none; }
    .hero h4 { font-weight:800; margin:0 0 3px; font-size:1.3rem; }
    .hero p { opacity:.88; margin:0; font-size:.84rem; }
    .btn-light-h { background:#fff; color:var(--maroon); border:none; border-radius:50px; font-weight:700; padding:7px 16px; text-decoration:none; display:inline-flex; align-items:center; box-shadow:0 4px 12px rgba(0,0,0,.14); transition:.18s; }
    .btn-light-h:hover { color:var(--maroon); text-decoration:none; transform:translateY(-1px); }
    .wait-pill { background:rgba(255,255,255,.18); color:#fff; border-radius:50px; padding:7px 15px; font-weight:700; font-size:.8rem; }

    .panel { background:#fff; border-radius:16px; box-shadow:0 4px 20px rgba(0,0,0,.05); border:1px solid #eef2f7; }
    .filter-sel { border-radius:10px; border:1.5px solid #e2e8f0; font-weight:600; color:#475569; font-size:.84rem; }
    .filter-sel:focus { border-color:var(--maroon); box-shadow:0 0 0 .2rem rgba(124,19,22,.12); }
    .toolbar { display:flex; flex-wrap:wrap; gap:10px; align-items:center; padding:12px 16px; }
    .sort-mini { display:flex; align-items:center; gap:6px; margin-left:auto; }
    .sort-mini select { border-radius:8px; border:1.5px solid #e2e8f0; font-size:.8rem; font-weight:600; color:#475569; padding:4px 8px; }
    .ic-btn { width:32px; height:32px; display:inline-flex; align-items:center; justify-content:center; border-radius:9px; border:1px solid #e5e7eb; background:#fff; color:#64748b; transition:.12s; text-decoration:none; }
    .ic-btn:hover { background:var(--maroon-subtle); color:var(--maroon); border-color:#ecd3d5; }
    .chev { transition:transform .18s; }
    .ic-btn[aria-expanded="true"] .chev { transform:rotate(180deg); }
    .ic-btn[aria-expanded="true"] { background:var(--maroon-subtle); color:var(--maroon); }

    /* ===== Baris booking (gaya sama dgn kalender > Daftar) ===== */
    .bl-list { padding:0 16px 8px; }
    .bl-item { border:1px solid #eef2f7; border-radius:12px; margin-bottom:8px; background:#fff; overflow:hidden; transition:border-color .12s, box-shadow .12s; }
    .bl-item:hover { border-color:#e3c3c6; box-shadow:0 4px 14px rgba(124,19,22,.07); }
    .bl-row { display:grid; grid-template-columns: 4px minmax(0,1fr) 170px 130px 104px 96px 78px; gap:12px; align-items:center; padding:11px 12px 11px 0; }
    .bl-bar { align-self:stretch; margin:-11px 0; }
    .bl-ins { font-weight:700; color:#1f2937; font-size:.9rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .bl-sub { font-size:.76rem; color:#64748b; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; margin-top:2px; }
    .bl-room { display:inline-flex; align-items:center; gap:6px; font-weight:700; color:#334155; font-size:.78rem; background:#f8fafc; border:1px solid #eef0f3; border-radius:20px; padding:3px 10px; max-width:100%; overflow:hidden; white-space:nowrap; text-overflow:ellipsis; }
    .bl-room i { width:8px; height:8px; border-radius:2px; flex-shrink:0; }
    .bl-per { font-size:.76rem; color:#475569; white-space:nowrap; }
    .fase { display:inline-block; font-size:.6rem; font-weight:800; padding:1px 8px; border-radius:20px; letter-spacing:.3px; margin-top:3px; }
    .fase-berjalan { background:#dcfce7; color:#15803d; } .fase-selesai { background:#eef2f7; color:#64748b; } .fase-akan { background:#dbeafe; color:#1d4ed8; }
    .bl-ppl { text-align:right; white-space:nowrap; }
    .bl-ppl b { font-size:1rem; color:var(--maroon); font-weight:800; }
    .bl-ppl small { display:block; font-size:.66rem; color:#94a3b8; font-weight:600; }
    .bl-ppl small.ok { color:#15803d; }
    .pill { padding:3px 11px; border-radius:20px; font-size:.7rem; font-weight:700; display:inline-block; white-space:nowrap; }
    .b-pending { background:#fef3c7; color:#b45309; } .b-approved { background:#dcfce7; color:#15803d; } .b-rejected { background:#fee2e2; color:#b91c1c; }
    .imp { display:inline-block; background:#eef2ff; color:#4338ca; font-size:.56rem; font-weight:800; padding:1px 5px; border-radius:4px; vertical-align:middle; margin-left:5px; }
    .bl-aksi { display:flex; gap:5px; justify-content:flex-end; align-items:center; }
    .btn-acc { background:#16a34a; color:#fff; border:none; border-radius:9px; font-weight:700; font-size:.78rem; width:32px; height:32px; }
    .btn-acc:hover { background:#15803d; color:#fff; }

    /* drawer */
    .dw { border-top:1px solid #f1f3f5; background:#fafbfc; }
    .dw-in { padding:14px 18px 16px 20px; }
    .dw-grid { display:grid; grid-template-columns: 1fr 1fr; gap:18px; }
    .dw-lbl { font-size:.66rem; font-weight:800; letter-spacing:.4px; color:var(--maroon); text-transform:uppercase; margin-bottom:8px; display:flex; align-items:center; gap:6px; }
    .dw-lbl .c { background:var(--maroon-subtle); border-radius:20px; padding:1px 8px; }
    .dw-ket { font-size:.82rem; color:#475569; }
    .dw-box { background:#fff; border:1px solid #eef2f7; border-radius:11px; padding:12px; }
    .mini-form .form-control { font-size:.82rem; }
    .mh-row { display:flex; align-items:center; gap:10px; padding:7px 2px; border-bottom:1px solid #f1f5f9; }
    .mh-row:last-child { border-bottom:none; }
    .mh-av { width:30px; height:30px; border-radius:50%; background:var(--maroon-subtle); color:var(--maroon); font-weight:800; font-size:.68rem; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .mh-tx { flex:1; min-width:0; }
    .mh-tx b { display:block; font-size:.82rem; color:#1f2937; }
    .mh-tx small { color:#94a3b8; font-size:.7rem; }
    .mh-st { font-size:.6rem; font-weight:800; padding:2px 8px; border-radius:20px; white-space:nowrap; }
    .mh-st.aktif, .mh-st.approved { background:#dcfce7; color:#15803d; }
    .mh-st.nonaktif, .mh-st.pending { background:#eef2f7; color:#64748b; }
    .mh-st.rejected { background:#fee2e2; color:#b91c1c; }
    .md-empty { background:#fff; border:1px dashed #e2e8f0; border-radius:11px; padding:12px; text-align:center; color:#94a3b8; font-size:.78rem; }
    .ps-table { font-size:.8rem; }
    .ps-table th { background:#f1f3f5; color:#475569; font-size:.68rem; text-transform:uppercase; letter-spacing:.3px; border:none; padding:7px 10px; }
    .ps-table td { padding:7px 10px; vertical-align:middle; }

    @media (max-width: 992px) {
        .bl-row { grid-template-columns: 4px minmax(0,1fr) auto; row-gap:6px; }
        .bl-bar { grid-row:1 / span 4; }
        .bl-c-room, .bl-c-per, .bl-c-st { grid-column:2; }
        .bl-ppl { grid-column:3; grid-row:1; }
        .bl-aksi { grid-column:3; grid-row:2 / span 2; align-self:end; }
        .dw-grid { grid-template-columns: 1fr; }
    }
</style>

<div class="container-fluid py-3">
    <div class="hero d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <h4><i class="bi bi-calendar-check me-2"></i>Booking Ruangan Instansi</h4>
            <p>Permintaan kuota ruangan dari instansi mitra · {{ $bookings->total() }} booking</p>
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

    {{-- FILTER --}}
    <div class="panel p-3 mb-3">
        <form method="GET" class="row g-2 align-items-end">
            <input type="hidden" name="sort" value="{{ $sort }}"><input type="hidden" name="dir" value="{{ $dir }}">
            <div class="col-md-4">
                <label class="small fw-semibold text-muted mb-1">Cari (instansi/prodi)</label>
                <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm filter-sel" placeholder="Nama instansi / prodi...">
            </div>
            <div class="col-md-4">
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
            <div class="col-6 col-md-2">
                <label class="small fw-semibold text-muted mb-1">Dari</label>
                <input type="date" name="start" value="{{ request('start') }}" class="form-control form-control-sm filter-sel">
            </div>
            <div class="col-6 col-md-2">
                <label class="small fw-semibold text-muted mb-1">Sampai</label>
                <input type="date" name="end" value="{{ request('end') }}" class="form-control form-control-sm filter-sel">
            </div>
            <div class="col-md-8 d-flex gap-2 align-items-end">
                <button type="submit" class="btn btn-sm text-white fw-semibold px-3" style="background:#7c1316;"><i class="bi bi-funnel me-1"></i>Terapkan</button>
                @if(request('q')||request('ruangan')||request('jenjang')||request('status')||request('start')||request('end'))
                    <a href="{{ route('admin.booking.index') }}" class="btn btn-sm btn-light border">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <div class="panel">
        {{-- TOOLBAR --}}
        @php $sortOpts = ['instansi'=>'Instansi','ruangan'=>'Ruangan','kuota'=>'Kuota','periode'=>'Periode','status'=>'Status']; @endphp
        <div class="toolbar">
            <span class="text-muted small fw-semibold"><i class="bi bi-list-ul me-1"></i>{{ $bookings->firstItem() ?? 0 }}–{{ $bookings->lastItem() ?? 0 }} dari {{ $bookings->total() }} booking</span>
            <div class="sort-mini">
                <span class="text-muted small fw-semibold">Urutkan</span>
                <select onchange="location.href=this.value">
                    @foreach($sortOpts as $k => $lbl)
                        <option value="{{ request()->fullUrlWithQuery(['sort'=>$k,'dir'=>$dir,'page'=>null]) }}" {{ $sort===$k?'selected':'' }}>{{ $lbl }}</option>
                    @endforeach
                </select>
                <a href="{{ request()->fullUrlWithQuery(['sort'=>$sort,'dir'=>$dir==='asc'?'desc':'asc','page'=>null]) }}" class="ic-btn" title="Balik urutan">
                    <i class="bi {{ $dir==='asc' ? 'bi-sort-down-alt' : 'bi-sort-up-alt' }}"></i>
                </a>
            </div>
        </div>

        <div class="bl-list">
            @php $palette = ['#7c1316','#1d4ed8','#15803d','#b45309','#7e22ce','#0e7490','#be123c','#4d7c0f','#0f766e','#9333ea']; @endphp
            @forelse($bookings as $b)
            @php
                $isImpor = \Illuminate\Support\Str::contains((string)$b->keterangan, '[IMPOR-MAPPING]');
                $ketBersih = trim(str_replace('[IMPOR-MAPPING]', '', (string)$b->keterangan));
                $rclr = $b->status === 'pending' ? '#f59e0b' : ($b->status === 'rejected' ? '#dc2626' : $palette[($b->ruangan_id ?? 0) % count($palette)]);
                $pr = \App\Http\Controllers\InstansiController::prodiLabel($b);
                $m = optional($b->tanggal_mulai); $s = optional($b->tanggal_selesai); $tdy = now()->startOfDay();
                $fase = (!$b->tanggal_mulai || !$b->tanggal_selesai) ? null : ($s->lt($tdy) ? ['Selesai','selesai'] : ($m->gt($tdy) ? ['Akan Datang','akan'] : ['Berjalan','berjalan']));
                $mhs = $mhsMap[$b->id] ?? [];
                $terisi = $b->pesertas->count();
                $namaInstansi = optional($b->mou)->nama_instansi ?? optional($b->mou)->nama_universitas ?? '-';
                $namaRuangan = optional($b->ruangan)->nm_ruangan ?? '-';
            @endphp
            <div class="bl-item">
                <div class="bl-row">
                    <span class="bl-bar" style="background:{{ $rclr }};"></span>
                    <div style="min-width:0;">
                        <div class="bl-ins">{{ $namaInstansi }}@if($isImpor)<span class="imp" title="Dari impor mapping manual">IMPOR</span>@endif</div>
                        <div class="bl-sub">{{ $pr ?: '-' }}</div>
                    </div>
                    <div class="bl-c-room" style="min-width:0;">
                        <span class="bl-room"><i style="background:{{ $palette[($b->ruangan_id ?? 0) % count($palette)] }};"></i>{{ $namaRuangan }}</span>
                    </div>
                    <div class="bl-c-per">
                        <div class="bl-per">{{ $b->tanggal_mulai ? $m->format('d M') : '-' }} – {{ $b->tanggal_selesai ? $s->format('d M y') : '-' }}</div>
                        @if($fase)<span class="fase fase-{{ $fase[1] }}">{{ $fase[0] }}</span>@endif
                    </div>
                    <div class="bl-ppl">
                        <b>{{ $b->jumlah_peserta }}</b> <span class="text-muted small">org</span>
                        <small class="{{ count($mhs) ? 'ok' : '' }}">@if(count($mhs))<i class="bi bi-person-check"></i> {{ count($mhs) }} tercatat @else belum tercatat @endif</small>
                    </div>
                    <div class="bl-c-st text-lg-center"><span class="pill b-{{ $b->status }}">{{ ['pending'=>'Menunggu','approved'=>'Disetujui','rejected'=>'Ditolak'][$b->status] ?? $b->status }}</span></div>
                    <div class="bl-aksi">
                        @if($b->status === 'pending')
                            <form action="{{ route('admin.booking.approve', $b->id) }}" method="POST" onsubmit="return confirm('Setujui booking ini?');">
                                @csrf<button class="btn-acc" title="Setujui"><i class="bi bi-check-lg"></i></button>
                            </form>
                        @endif
                        <button class="ic-btn" data-bs-toggle="collapse" data-bs-target="#dw-{{ $b->id }}" aria-expanded="false" title="Detail & aksi"><i class="bi bi-chevron-down chev"></i></button>
                    </div>
                </div>

                {{-- DRAWER --}}
                <div class="collapse dw" id="dw-{{ $b->id }}">
                    <div class="dw-in">
                        <div class="dw-grid">
                            <div>
                                <div class="dw-lbl"><i class="bi bi-people-fill"></i>Mahasiswa di ruangan ini <span class="c">{{ count($mhs) }}</span></div>
                                @if(count($mhs))
                                    <div class="dw-box py-1">
                                        @foreach($mhs as $x)
                                        <div class="mh-row">
                                            <div class="mh-av">{{ strtoupper(collect(preg_split('/\s+/', trim((string)$x['nama'])))->take(2)->map(function ($w) { return mb_substr($w, 0, 1); })->implode('')) }}</div>
                                            <div class="mh-tx"><b>{{ $x['nama'] }}</b><small>{{ $x['prodi'] ?: '-' }}{{ $x['mulai'] ? ' · '.$x['mulai'].' – '.$x['selesai'] : '' }}</small></div>
                                            <span class="mh-st {{ $x['status'] }}">{{ ucfirst($x['status'] ?: '-') }}</span>
                                        </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="md-empty"><i class="bi bi-person-dash me-1"></i>Belum ada mahasiswa dari instansi ini yang tercatat di ruangan ini pada periode tersebut.</div>
                                @endif
                            </div>

                            <div>
                                <div class="dw-lbl"><i class="bi bi-info-circle"></i>Detail &amp; aksi</div>
                                <div class="dw-box mini-form">
                                    <div class="dw-ket mb-2">
                                        <div><i class="bi bi-calendar3 me-1 text-muted"></i>{{ $b->tanggal_mulai ? $m->format('d/m/Y') : '-' }} – {{ $b->tanggal_selesai ? $s->format('d/m/Y') : '-' }}</div>
                                        @if($ketBersih)<div class="mt-1 text-muted" style="font-size:.78rem;"><i class="bi bi-chat-left-text me-1"></i>{{ $ketBersih }}</div>@endif
                                        @if($b->status==='rejected' && $b->catatan_admin)<div class="mt-1 text-danger" style="font-size:.78rem;"><i class="bi bi-x-circle me-1"></i>{{ $b->catatan_admin }}</div>@endif
                                    </div>

                                    @if($b->status === 'pending')
                                        <form action="{{ route('admin.booking.approve', $b->id) }}" method="POST" class="mb-2">
                                            @csrf
                                            <label class="small fw-semibold mb-1">Setujui — batas pengisian peserta <span class="text-muted">(opsional)</span></label>
                                            <div class="input-group input-group-sm">
                                                <input type="date" name="batas_pengisian" class="form-control">
                                                <button class="btn btn-success fw-semibold"><i class="bi bi-check-lg me-1"></i>Setujui</button>
                                            </div>
                                        </form>
                                        <form action="{{ route('admin.booking.reject', $b->id) }}" method="POST" class="mb-2">
                                            @csrf
                                            <label class="small fw-semibold mb-1">Tolak — alasan</label>
                                            <div class="input-group input-group-sm">
                                                <input type="text" name="catatan_admin" class="form-control" placeholder="Alasan penolakan..." required>
                                                <button class="btn btn-outline-danger fw-semibold">Tolak</button>
                                            </div>
                                        </form>
                                    @elseif($b->status === 'approved')
                                        <form action="{{ route('admin.booking.batas', $b->id) }}" method="POST" class="mb-2">
                                            @csrf
                                            <label class="small fw-semibold mb-1">Batas pengisian peserta <span class="text-muted">(saat ini: {{ optional($b->batas_pengisian)->format('d/m/Y') ?: 'tidak dibatasi' }})</span></label>
                                            <div class="input-group input-group-sm">
                                                <input type="date" name="batas_pengisian" class="form-control" value="{{ optional($b->batas_pengisian)->format('Y-m-d') }}">
                                                <button class="btn btn-outline-dark fw-semibold">Simpan</button>
                                            </div>
                                        </form>
                                    @endif

                                    <div class="d-flex gap-2 pt-1">
                                        <a href="{{ route('admin.booking.edit', $b->id) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil me-1"></i>Edit</a>
                                        <form action="{{ route('admin.booking.destroy', $b->id) }}" method="POST" onsubmit="return confirm('Hapus booking ini beserta daftar pesertanya?');">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash me-1"></i>Hapus</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($terisi)
                        <div class="mt-3">
                            <div class="dw-lbl"><i class="bi bi-person-lines-fill"></i>Peserta didaftarkan instansi <span class="c">{{ $terisi }}</span></div>
                            <div class="table-responsive dw-box p-0">
                                <table class="table table-sm ps-table align-middle mb-0">
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
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center text-muted py-5"><i class="bi bi-inbox fs-3 d-block mb-2"></i>Belum ada booking.</div>
            @endforelse
        </div>
        <div class="px-3 pb-3">{{ $bookings->appends(request()->except('page'))->links() }}</div>
    </div>
</div>
@endsection
