@extends('layouts.app')

@section('title', 'Kelola Dispensasi')
@section('page-title', 'Kelola Dispensasi Ruangan')

@section('content')
<style>
    :root { --maroon:#7c1316; --maroon-light:#a3191d; }
    .disp-head { background: linear-gradient(135deg,var(--maroon),var(--maroon-light)); color:#fff; border-radius:16px; padding:18px 22px; box-shadow:0 8px 24px rgba(124,19,22,.18); }
    .disp-tabs { display:flex; flex-wrap:wrap; gap:8px; }
    .disp-tab { display:inline-flex; align-items:center; gap:8px; padding:8px 16px; border-radius:50px; font-weight:600; font-size:.85rem; text-decoration:none; border:1.5px solid #e9edf2; background:#fff; color:#64748b; transition:.15s; }
    .disp-tab:hover { border-color:var(--maroon); color:var(--maroon); }
    .disp-tab.active { background:var(--maroon); border-color:var(--maroon); color:#fff; box-shadow:0 4px 12px rgba(124,19,22,.22); }
    .disp-tab .cnt { background:rgba(0,0,0,.08); padding:1px 9px; border-radius:50px; font-size:.72rem; }
    .disp-tab.active .cnt { background:rgba(255,255,255,.25); }
    .filter-card { background:#fff; border:1px solid #eef2f7; border-radius:14px; padding:14px 16px; }
    .filter-input { border-radius:9px; border:1px solid #e2e8f0; font-size:.9rem; }
    .filter-input:focus { border-color:var(--maroon); box-shadow:0 0 0 .2rem rgba(124,19,22,.15); }
    .dispen-card { background:#fff; border:1px solid #eef2f7; border-left:4px solid #f59e0b; border-radius:14px; padding:16px; box-shadow:0 2px 10px rgba(0,0,0,.04); height:100%; transition:.15s; }
    .dispen-card:hover { box-shadow:0 8px 22px rgba(0,0,0,.08); transform:translateY(-2px); }
    .avatar-initial { width:40px; height:40px; border-radius:11px; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:1.05rem; background:linear-gradient(135deg,var(--maroon),var(--maroon-light)); color:#fff; flex-shrink:0; }
    .min-w-0 { min-width:0; }
    .empty-state { background:#fff; border:1.5px dashed #d8dee6; border-radius:16px; padding:48px 20px; text-align:center; color:#94a3b8; }
    .custom-pagination .pagination { margin:0; gap:5px; justify-content:center; }
    .custom-pagination .page-link { border-radius:8px; font-weight:600; color:var(--maroon); border:1px solid #e2e8f0; }
    .custom-pagination .page-item.active .page-link { background:var(--maroon); border-color:var(--maroon); color:#fff; }
</style>

<div class="container-fluid pb-4">

    {{-- HEADER --}}
    <div class="disp-head d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <h5 class="fw-bold mb-1"><i class="bi bi-envelope-paper-heart me-2"></i>Kelola Dispensasi</h5>
            <div class="opacity-75 small"><i class="bi bi-door-open me-1"></i>{{ $ruangan->nm_ruangan }}</div>
        </div>
        <a href="{{ route('kepala_ruangan.dashboard') }}" class="btn btn-light btn-sm rounded-pill fw-semibold">
            <i class="bi bi-arrow-left me-1"></i> Dashboard
        </a>
    </div>

    {{-- TABS STATUS --}}
    <div class="disp-tabs mb-3">
        @php
            $tabs = [
                'pending'  => ['Menunggu',  'bi-hourglass-split', $counts['pending']],
                'approved' => ['Disetujui', 'bi-check-circle',    $counts['approved']],
                'rejected' => ['Ditolak',   'bi-x-circle',        $counts['rejected']],
                'all'      => ['Semua',     'bi-collection',       $counts['pending'] + $counts['approved'] + $counts['rejected']],
            ];
        @endphp
        @foreach($tabs as $key => $t)
            <a class="disp-tab {{ $status === $key ? 'active' : '' }}"
               href="{{ route('kepala_ruangan.dispensasi.index', array_merge(request()->except(['page','status']), ['status' => $key])) }}">
                <i class="bi {{ $t[1] }}"></i> {{ $t[0] }} <span class="cnt">{{ $t[2] }}</span>
            </a>
        @endforeach
    </div>

    {{-- FILTER BAR --}}
    <form method="GET" action="{{ route('kepala_ruangan.dispensasi.index') }}" class="filter-card mb-3">
        <input type="hidden" name="status" value="{{ $status }}">
        <div class="row g-2 align-items-end">
            <div class="col-md-5">
                <label class="form-label small fw-semibold text-muted mb-1">Cari Mahasiswa</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="q" value="{{ $q }}" class="form-control filter-input border-start-0" placeholder="Ketik nama mahasiswa...">
                </div>
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label small fw-semibold text-muted mb-1">Dari Tanggal</label>
                <input type="date" name="start" value="{{ $start }}" class="form-control form-control-sm filter-input">
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label small fw-semibold text-muted mb-1">Sampai Tanggal</label>
                <input type="date" name="end" value="{{ $end }}" class="form-control form-control-sm filter-input">
            </div>
            <div class="col-md-1 d-grid">
                <button type="submit" class="btn btn-sm text-white fw-semibold" style="background:var(--maroon);"><i class="bi bi-funnel"></i></button>
            </div>
        </div>
        @if($q || $start || $end)
            <div class="mt-2">
                <a href="{{ route('kepala_ruangan.dispensasi.index', ['status' => $status]) }}" class="small text-muted text-decoration-none"><i class="bi bi-x-circle me-1"></i>Reset filter</a>
            </div>
        @endif
    </form>

    {{-- LIST --}}
    @if($dispensasis->total() > 0)
        <div class="row g-3">
            @foreach($dispensasis as $dispen)
                @php
                    $kat = strtolower($dispen->kategori);
                    $katMap = [
                        'terlambat'   => ['Terlambat',    '#dc3545', '#fdecee', 'bi-clock-history'],
                        'lupa_pulang' => ['Lupa Pulang',  '#f59e0b', '#fef3e2', 'bi-box-arrow-right'],
                        'biasa'       => ['Izin / Sakit', '#0d6efd', '#e7f0ff', 'bi-calendar2-check'],
                    ];
                    $ki = $katMap[$kat] ?? [ucwords(str_replace('_',' ',$kat)), '#6c757d', '#f1f5f9', 'bi-tag-fill'];
                    $stMap = [
                        'pending'  => ['Menunggu',  '#b45309', '#fef3c7'],
                        'approved' => ['Disetujui', '#166534', '#dcfce7'],
                        'rejected' => ['Ditolak',   '#991b1b', '#fee2e2'],
                    ];
                    $si = $stMap[$dispen->status] ?? ['-', '#64748b', '#f1f5f9'];
                    $appr = $dispen->keterangan_terlambat;
                    if (is_string($appr)) $appr = json_decode($appr, true);
                    if (!is_array($appr)) $appr = [];
                @endphp
                <div class="col-md-6 col-xl-4">
                    <div class="dispen-card" style="border-left-color: {{ $ki[1] }};">
                        <div class="d-flex align-items-start gap-2 mb-2">
                            <div class="avatar-initial">{{ substr(optional($dispen->mahasiswa)->nm_mahasiswa ?? '?', 0, 1) }}</div>
                            <div class="flex-grow-1 min-w-0">
                                <div class="fw-bold text-dark text-truncate" style="font-size:.88rem;">{{ optional($dispen->mahasiswa)->nm_mahasiswa ?? '-' }}</div>
                                <div class="text-muted text-truncate" style="font-size:.72rem;">{{ optional($dispen->mahasiswa)->prodi }}</div>
                            </div>
                            <span class="badge" style="background:{{ $si[2] }};color:{{ $si[1] }};font-size:.66rem;white-space:nowrap;">{{ $si[0] }}</span>
                        </div>

                        <div class="d-flex flex-wrap gap-1 mb-2">
                            <span class="badge" style="background:{{ $ki[2] }};color:{{ $ki[1] }};font-size:.68rem;border:1px solid {{ $ki[1] }}40;">
                                <i class="bi {{ $ki[3] }} me-1"></i>{{ $ki[0] }}
                            </span>
                            @if($kat === 'terlambat' && stripos($dispen->keterangan, 'malam') !== false)
                                <span class="badge bg-dark text-white" style="font-size:.64rem;"><i class="bi bi-moon-stars-fill text-warning me-1"></i>Shift Malam</span>
                            @endif
                        </div>

                        @if($kat === 'lupa_pulang')
                            <div class="mb-2" style="color:#b45309;font-size:.72rem;"><i class="bi bi-info-circle me-1"></i>Bila disetujui, hari itu dihitung <strong>90%</strong>.</div>
                        @endif

                        <div class="small text-muted mb-1">
                            <i class="bi bi-calendar-range me-1"></i>{{ \Carbon\Carbon::parse($dispen->tanggal_mulai)->format('d/m/Y') }}@if($dispen->tanggal_mulai != $dispen->tanggal_selesai) &ndash; {{ \Carbon\Carbon::parse($dispen->tanggal_selesai)->format('d/m/Y') }}@endif
                        </div>
                        <p class="mb-2 text-dark" style="font-size:.82rem; line-height:1.4;"><strong>Alasan:</strong> {{ $dispen->keterangan }}</p>

                        @if(!empty($appr['nama_penyetuju']))
                            <div class="small text-muted mb-2" style="font-size:.72rem;"><i class="bi bi-person-check me-1"></i>Penyetuju: <strong>{{ $appr['nama_penyetuju'] }}</strong>{{ !empty($appr['jabatan_penyetuju']) ? ' — '.$appr['jabatan_penyetuju'] : '' }}</div>
                        @endif
                        @if($dispen->file_path)
                            <a href="{{ asset('storage/'.$dispen->file_path) }}" target="_blank" class="small text-decoration-none d-inline-block mb-2"><i class="bi bi-paperclip me-1"></i>Lihat berkas / surat</a>
                        @endif

                        @if($dispen->status === 'pending')
                            <div class="d-flex gap-2 mt-1">
                                <form action="{{ route('kepala_ruangan.dispensasi.approve', $dispen->id) }}" method="POST" class="flex-fill" onsubmit="return confirm('Setujui dispensasi {{ $ki[0] }} untuk {{ addslashes(optional($dispen->mahasiswa)->nm_mahasiswa ?? '-') }}?');">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm w-100"><i class="bi bi-check-lg me-1"></i>Setujui</button>
                                </form>
                                <button type="button" class="btn btn-outline-danger btn-sm flex-fill" data-bs-toggle="collapse" data-bs-target="#tolakDisp-{{ $dispen->id }}">
                                    <i class="bi bi-x-lg me-1"></i>Tolak
                                </button>
                            </div>
                            <div class="collapse mt-2" id="tolakDisp-{{ $dispen->id }}">
                                <form action="{{ route('kepala_ruangan.dispensasi.reject', $dispen->id) }}" method="POST">
                                    @csrf
                                    <textarea name="catatan_admin" class="form-control form-control-sm mb-2" rows="2" placeholder="Alasan penolakan (wajib)..." required></textarea>
                                    <button type="submit" class="btn btn-danger btn-sm w-100"><i class="bi bi-send me-1"></i>Kirim Penolakan</button>
                                </form>
                            </div>
                        @elseif($dispen->status === 'rejected' && $dispen->catatan_admin)
                            <div class="mt-1 small p-2 rounded" style="background:#fff1f2;color:#991b1b;font-size:.74rem;"><i class="bi bi-chat-left-text me-1"></i>{{ $dispen->catatan_admin }}</div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="custom-pagination mt-4">
            {{ $dispensasis->links() }}
        </div>
    @else
        <div class="empty-state">
            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
            <div class="fw-semibold">Tidak ada dispensasi {{ $status !== 'all' ? ($tabs[$status][0] ?? '') : '' }}.</div>
            @if($q || $start || $end)
                <a href="{{ route('kepala_ruangan.dispensasi.index', ['status' => $status]) }}" class="small">Reset filter</a>
            @endif
        </div>
    @endif

</div>
@endsection
