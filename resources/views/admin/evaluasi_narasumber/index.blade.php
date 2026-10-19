@extends('layouts.app')

@section('title', 'Evaluasi Narasumber')

@section('content')
@php
    $warna = function ($s) {
        if ($s === null) return ['#f1f5f9', '#94a3b8'];
        if ($s >= 86) return ['#dcfce7', '#15803d'];
        if ($s >= 76) return ['#dbeafe', '#1d4ed8'];
        if ($s >= 56) return ['#fef3c7', '#b45309'];
        return ['#fee2e2', '#b91c1c'];
    };
@endphp
<style>
    :root { --maroon:#7c1316; --maroon-light:#a3191d; --maroon-subtle:#fcf0f1; }
    .bk-head { background:linear-gradient(135deg,#7c1316,#5f0f12); color:#fff; border-radius:18px; padding:20px 24px; box-shadow:0 12px 30px rgba(124,19,22,.26); position:relative; overflow:hidden; }
    .bk-head::after { content:''; position:absolute; right:-40px; top:-40px; width:170px; height:170px; background:rgba(255,255,255,.07); border-radius:50%; pointer-events:none; }
    .bk-head h4 { font-weight:800; margin:0 0 3px; font-size:1.3rem; }
    .bk-head p { opacity:.88; margin:0; font-size:.84rem; }
    .btn-light-h { background:#fff; color:var(--maroon); border:none; border-radius:50px; font-weight:700; padding:7px 15px; text-decoration:none; display:inline-flex; align-items:center; font-size:.84rem; position:relative; z-index:1; }
    .btn-light-h:hover { color:var(--maroon); }
    .btn-ghost-h { background:rgba(255,255,255,.16); color:#fff; border:none; border-radius:50px; font-weight:700; padding:7px 15px; text-decoration:none; display:inline-flex; align-items:center; font-size:.84rem; position:relative; z-index:1; }
    .btn-ghost-h:hover { background:rgba(255,255,255,.26); color:#fff; }
    .card-x { background:#fff; border:1px solid #eef2f7; border-radius:16px; box-shadow:0 4px 20px rgba(0,0,0,.05); }
    .filter-sel { border-radius:10px; border:1.5px solid #e2e8f0; font-size:.86rem; }
    .t-x thead th { background:var(--maroon-subtle); color:var(--maroon); font-size:.68rem; text-transform:uppercase; letter-spacing:.3px; border:none; padding:10px; vertical-align:bottom; }
    .t-x td { padding:9px 10px; vertical-align:middle; border-color:#f1f3f5; font-size:.85rem; }
    .cell { display:inline-block; min-width:48px; text-align:center; font-weight:800; font-size:.8rem; border-radius:8px; padding:4px 6px; }
    .mini { background:#f8fafc; border:1px solid #eef2f7; border-radius:12px; padding:10px 14px; }
    .mini .l { font-size:.66rem; font-weight:800; color:#94a3b8; text-transform:uppercase; letter-spacing:.3px; }
    .mini .v { font-size:1.3rem; font-weight:800; color:#1f2937; }
    .link-box { background:#f8fafc; border:1px dashed #d6dbe1; border-radius:10px; padding:8px 10px; font-size:.78rem; word-break:break-all; }
    .ic-btn { width:30px; height:30px; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; border:1px solid #e5e7eb; background:#fff; color:#64748b; text-decoration:none; }
    .ic-btn:hover { background:var(--maroon-subtle); color:var(--maroon); border-color:#ecd3d5; }
    .aspek-th { writing-mode:vertical-rl; transform:rotate(180deg); white-space:nowrap; max-height:150px; font-size:.68rem; }
    .rank { width:26px; height:26px; border-radius:50%; background:var(--maroon-subtle); color:var(--maroon); font-weight:800; font-size:.75rem; display:inline-flex; align-items:center; justify-content:center; }
    .legend span { font-size:.72rem; font-weight:700; border-radius:20px; padding:2px 9px; margin-right:4px; }
    .keg-row { cursor:pointer; } .keg-row:hover td { background:#fffafa; }
</style>

<div class="container-fluid py-3">
    <div class="bk-head d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
        <div>
            <h4><i class="bi bi-person-video3 me-2"></i>Evaluasi Narasumber</h4>
            <p>Penilaian peserta terhadap narasumber (fasilitator) di akhir pelatihan.</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.master_evaluasi.index', ['jenis' => 'narasumber']) }}" class="btn-ghost-h"><i class="bi bi-sliders me-1"></i> Aspek Penilaian</a>
            @if($kegiatan)
                <a href="{{ route('admin.evaluasi_narasumber.csv', $kegiatan->id) }}" class="btn-ghost-h"><i class="bi bi-filetype-csv me-1"></i> CSV</a>
                <a href="{{ route('admin.evaluasi_narasumber.pdf', $kegiatan->id) }}" class="btn-light-h"><i class="bi bi-file-earmark-pdf me-1"></i> Laporan PDF</a>
            @endif
        </div>
    </div>

    @if(session('success'))<div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>@endif

    <form method="GET" class="card-x p-3 mb-3">
        <label class="small fw-semibold text-muted mb-1">Pelatihan</label>
        <select name="kegiatan_id" class="form-select filter-sel" onchange="this.form.submit()">
            <option value="">— Pilih pelatihan —</option>
            @foreach($daftar as $k)
                <option value="{{ $k->id }}" {{ optional($kegiatan)->id === $k->id ? 'selected' : '' }}>
                    {{ \Illuminate\Support\Str::limit($k->nama_kegiatan, 90) }}{{ $k->tanggal_mulai ? ' (' . \Carbon\Carbon::parse($k->tanggal_mulai)->format('d/m/Y') . ')' : '' }} — {{ $k->fasilitator_count }} narasumber, {{ optional($jumlah->get($k->id))->n ?? 0 }} responden
                </option>
            @endforeach
        </select>
    </form>

    @if(!$kegiatan)
        <div class="card-x">
            <div class="table-responsive">
                <table class="table t-x mb-0">
                    <thead><tr><th>Pelatihan</th><th class="text-center">Narasumber</th><th class="text-center">Responden</th><th>Terakhir diisi</th></tr></thead>
                    <tbody>
                    @foreach($daftar->filter(function ($k) { return $k->fasilitator_count > 0; }) as $k)
                        @php $j = $jumlah->get($k->id); @endphp
                        <tr class="keg-row" onclick="location.href='{{ route('admin.evaluasi_narasumber.index', ['kegiatan_id' => $k->id]) }}'">
                            <td><div class="fw-semibold">{{ $k->nama_kegiatan }}</div><div class="small text-muted">{{ $k->tanggal_mulai ? \Carbon\Carbon::parse($k->tanggal_mulai)->isoFormat('D MMM YYYY') : '-' }}</div></td>
                            <td class="text-center">{{ $k->fasilitator_count }}</td>
                            <td class="text-center fw-bold">{{ $j->n ?? 0 }}</td>
                            <td class="small text-muted">{{ $j ? \Carbon\Carbon::parse($j->terakhir)->isoFormat('D MMM YYYY HH:mm') : '-' }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-3 py-2 small text-muted border-top">Hanya pelatihan yang punya narasumber (fasilitator) yang ditampilkan. Narasumber diatur di menu Pelatihan → Setting Penilaian.</div>
        </div>
    @else
        <div class="row g-3 mb-3">
            <div class="col-lg-7">
                <div class="card-x p-3 h-100">
                    <div class="fw-bold mb-2">{{ $kegiatan->nama_kegiatan }}</div>
                    <div class="small text-muted mb-1">Link form evaluasi narasumber (bagikan ke peserta di akhir pelatihan):</div>
                    <div class="d-flex gap-2 align-items-center">
                        <div class="link-box flex-fill">{{ $data['link'] }}</div>
                        <button type="button" class="ic-btn" title="Salin" onclick="salinLink('{{ $data['link'] }}', this)"><i class="bi bi-clipboard"></i></button>
                        <a href="{{ $data['link'] }}" target="_blank" class="ic-btn" title="Buka"><i class="bi bi-box-arrow-up-right"></i></a>
                    </div>
                    <div class="small text-muted mt-2"><i class="bi bi-info-circle me-1"></i>Peserta yang mengisi evaluasi pelatihan juga otomatis diarahkan ke form ini.</div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="d-flex gap-2 h-100">
                    <div class="mini flex-fill"><div class="l">Responden</div><div class="v">{{ $data['responden']->count() }}</div></div>
                    <div class="mini flex-fill"><div class="l">Narasumber</div><div class="v">{{ $kegiatan->fasilitator->count() }}</div></div>
                    <div class="mini flex-fill"><div class="l">Aspek</div><div class="v">{{ count($data['aspekList']) }}</div></div>
                </div>
            </div>
        </div>

        <div class="card-x mb-3">
            <div class="p-3 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div class="fw-bold"><i class="bi bi-grid-3x3 me-1" style="color:var(--maroon);"></i>Nilai narasumber per aspek</div>
                <div class="legend">
                    <span style="background:#fee2e2;color:#b91c1c;">Kurang 45–55</span><span style="background:#fef3c7;color:#b45309;">Sedang 56–75</span><span style="background:#dbeafe;color:#1d4ed8;">Baik 76–85</span><span style="background:#dcfce7;color:#15803d;">Sangat Baik 86+</span>
                </div>
            </div>
            @if(count($data['narasumber']))
            <div class="table-responsive">
                <table class="table t-x mb-0">
                    <thead><tr>
                        <th style="width:36px;">#</th><th>Narasumber</th>
                        @foreach($data['aspekList'] as $asp)<th class="text-center"><div class="aspek-th" title="{{ $asp }}">{{ \Illuminate\Support\Str::limit($asp, 34) }}</div></th>@endforeach
                        <th class="text-center">Nilai Akhir</th><th class="text-center">Kategori</th>
                    </tr></thead>
                    <tbody>
                    @php $rk = 0; @endphp
                    @foreach($data['narasumber'] as $n)
                        @php $rk++; $c = $warna($n['skor']); @endphp
                        <tr>
                            <td><span class="rank">{{ $rk }}</span></td>
                            <td class="fw-semibold text-nowrap">{{ $n['nama'] }}</td>
                            @foreach($data['aspekList'] as $asp)
                                @php $s = $n['aspek'][$asp]['skor'] ?? null; $w = $warna($s); @endphp
                                <td class="text-center"><span class="cell" style="background:{{ $w[0] }};color:{{ $w[1] }};" title="{{ $asp }}">{{ $s !== null ? number_format($s, 1) : '-' }}</span></td>
                            @endforeach
                            <td class="text-center"><span class="cell" style="background:{{ $c[0] }};color:{{ $c[1] }};font-size:.92rem;">{{ $n['skor'] !== null ? number_format($n['skor'], 2) : '-' }}</span></td>
                            <td class="text-center fw-bold" style="color:{{ $c[1] }};">{{ $n['kategori'] }}</td>
                        </tr>
                    @endforeach
                    <tr style="border-top:2px solid #eef2f7;">
                        <td></td><td class="small fw-bold text-muted">Rata-rata aspek</td>
                        @foreach($data['aspekList'] as $asp)
                            @php $s = $data['perAspek'][$asp]; $w = $warna($s); @endphp
                            <td class="text-center"><span class="cell" style="background:{{ $w[0] }};color:{{ $w[1] }};">{{ $s !== null ? number_format($s, 1) : '-' }}</span></td>
                        @endforeach
                        <td colspan="2"></td>
                    </tr>
                    </tbody>
                </table>
            </div>
            @else
                <div class="text-center text-muted py-5"><i class="bi bi-inbox d-block fs-3 mb-1"></i>Belum ada peserta yang mengisi evaluasi narasumber.</div>
            @endif
            <div class="px-3 py-2 small text-muted border-top">Nilai = rata-rata titik tengah rentang pilihan (Kurang 50, Sedang 65,5, Baik 80,5, Sangat Baik 93).</div>
        </div>

        <div class="row g-3">
            <div class="col-lg-6">
                <div class="card-x h-100">
                    <div class="p-3 border-bottom fw-bold"><i class="bi bi-chat-left-text me-1" style="color:var(--maroon);"></i>Kritik & saran</div>
                    <div class="p-3" style="max-height:360px;overflow:auto;">
                        @forelse($data['responden']->filter(function ($r) { return trim((string) $r->saran) !== ''; }) as $r)
                            <div class="mb-3 pb-2 border-bottom"><div class="small text-muted mb-1">{{ $r->profesi ?: '-' }} · {{ optional($r->tanggal_pengisian)->isoFormat('D MMM YYYY') }}</div>{{ $r->saran }}</div>
                        @empty
                            <div class="text-muted small">Belum ada saran.</div>
                        @endforelse
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card-x h-100">
                    <div class="p-3 border-bottom fw-bold"><i class="bi bi-people me-1" style="color:var(--maroon);"></i>Responden</div>
                    <div class="table-responsive" style="max-height:360px;overflow:auto;">
                        <table class="table t-x mb-0">
                            <thead><tr><th>Tanggal</th><th>Profesi</th><th>Nama</th><th></th></tr></thead>
                            <tbody>
                            @forelse($data['responden'] as $r)
                                <tr>
                                    <td class="small text-nowrap">{{ optional($r->tanggal_pengisian)->isoFormat('D MMM YYYY') }}</td>
                                    <td class="small">{{ $r->profesi ?: '-' }}</td>
                                    <td class="small">{{ $r->nama ?: '(anonim)' }}</td>
                                    <td class="text-end">
                                        <form method="POST" action="{{ route('admin.evaluasi_narasumber.destroy', $r->id) }}" onsubmit="return confirm('Hapus tanggapan ini?');">
                                            @csrf @method('DELETE')<button class="ic-btn" title="Hapus"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted py-3">Belum ada responden.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
    function salinLink(url, btn) {
        var done = function () { var i = btn.querySelector('i'); var c = i.className; i.className = 'bi bi-check2'; setTimeout(function () { i.className = c; }, 1500); };
        if (navigator.clipboard && window.isSecureContext) { navigator.clipboard.writeText(url).then(done, function () { prompt('Salin link:', url); }); }
        else { prompt('Salin link:', url); }
    }
</script>
@endsection
