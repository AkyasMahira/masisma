@extends('layouts.app')

@php
    $judulJenis = ['pelatihan' => 'Evaluasi Pelatihan', 'pendidikan' => 'Evaluasi Pendidikan', 'penelitian' => 'Evaluasi Penelitian'][$jenis];
    $grupLabel  = ['pelatihan' => 'Pelatihan', 'pendidikan' => 'Kampus / Instansi', 'penelitian' => 'Penelitian'][$jenis];
    $grupParam  = ['pelatihan' => 'kegiatan_id', 'pendidikan' => 'mou_id', 'penelitian' => 'pra_penelitian_id'][$jenis];
    $ikonJenis  = ['pelatihan' => 'bi-easel2', 'pendidikan' => 'bi-mortarboard', 'penelitian' => 'bi-journal-text'][$jenis];
    $qs = request()->except('page');
    $mutuWarna = ['A' => '#15803d', 'B' => '#1d4ed8', 'C' => '#b45309', 'D' => '#b91c1c', '-' => '#94a3b8'];
@endphp

@section('title', $judulJenis . ' & IKM')

@section('content')
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
    .tabs-j { display:flex; gap:6px; flex-wrap:wrap; margin:14px 0; }
    .tabs-j a { text-decoration:none; font-weight:700; font-size:.85rem; color:#475569; background:#fff; border:1px solid #e5e7eb; border-radius:50px; padding:7px 16px; display:inline-flex; align-items:center; gap:6px; }
    .tabs-j a.on { background:var(--maroon); color:#fff; border-color:var(--maroon); }
    .card-x { background:#fff; border:1px solid #eef2f7; border-radius:16px; box-shadow:0 4px 20px rgba(0,0,0,.05); }
    .filter-sel { border-radius:10px; border:1.5px solid #e2e8f0; font-size:.84rem; }
    .filter-sel:focus { border-color:var(--maroon); box-shadow:0 0 0 .2rem rgba(124,19,22,.1); }
    .ikm-hero { background:linear-gradient(135deg,#7c1316,#5f0f12); color:#fff; border-radius:16px; padding:22px; height:100%; }
    .ikm-score { font-size:2.8rem; font-weight:800; line-height:1; }
    .ikm-grade { width:62px; height:62px; border-radius:50%; background:rgba(255,255,255,.18); font-size:1.9rem; font-weight:800; display:flex; align-items:center; justify-content:center; }
    .mini { background:#f8fafc; border:1px solid #eef2f7; border-radius:12px; padding:10px 14px; }
    .mini .l { font-size:.66rem; font-weight:800; color:#94a3b8; text-transform:uppercase; letter-spacing:.3px; }
    .mini .v { font-size:1.35rem; font-weight:800; color:#1f2937; }
    .sec-h { font-weight:800; color:#1f2937; font-size:.95rem; display:flex; align-items:center; gap:8px; }
    .t-x thead th { background:var(--maroon-subtle); color:var(--maroon); font-size:.7rem; text-transform:uppercase; letter-spacing:.4px; border:none; padding:10px 12px; }
    .t-x td { padding:10px 12px; vertical-align:middle; border-color:#f1f3f5; font-size:.86rem; }
    .mutu { display:inline-flex; width:28px; height:28px; border-radius:8px; color:#fff; font-weight:800; align-items:center; justify-content:center; font-size:.8rem; }
    .bar { height:6px; background:#eef2f7; border-radius:6px; overflow:hidden; min-width:70px; }
    .bar span { display:block; height:100%; }
    .chip-k { font-size:.6rem; font-weight:800; background:#eef2ff; color:#4338ca; border-radius:4px; padding:1px 5px; margin-left:4px; }
    .ic-btn { width:30px; height:30px; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; border:1px solid #e5e7eb; background:#fff; color:#64748b; text-decoration:none; }
    .ic-btn:hover { background:var(--maroon-subtle); color:var(--maroon); border-color:#ecd3d5; }
    .grup-row { cursor:pointer; }
    .grup-row:hover td { background:#fffafa; }
    .grup-row.on td { background:var(--maroon-subtle); }
    .filter-note { font-size:.8rem; color:#64748b; }
    .filter-note b { color:var(--maroon); }
</style>

<div class="container-fluid py-3">
    <div class="bk-head d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <h4><i class="bi {{ $ikonJenis }} me-2"></i>{{ $judulJenis }} &amp; IKM</h4>
            <p>Indeks Kepuasan Masyarakat (Permenpan RB No. 14 Tahun 2017) — dihitung terpisah per jenis layanan.</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.master_evaluasi.index', ['jenis' => $jenis]) }}" class="btn-ghost-h"><i class="bi bi-sliders me-1"></i> Pertanyaan</a>
            <a href="{{ route('admin.evaluasi.export.csv', $qs) }}" class="btn-ghost-h"><i class="bi bi-filetype-csv me-1"></i> CSV</a>
            <a href="{{ route('admin.evaluasi.export.pdf', $qs) }}" class="btn-light-h"><i class="bi bi-file-earmark-pdf me-1"></i> Generate Laporan IKM</a>
        </div>
    </div>

    <div class="tabs-j">
        <a href="{{ route('admin.evaluasi.index', ['jenis' => 'pelatihan']) }}" class="{{ $jenis === 'pelatihan' ? 'on' : '' }}"><i class="bi bi-easel2"></i> Pelatihan</a>
        <a href="{{ route('admin.evaluasi.index', ['jenis' => 'pendidikan']) }}" class="{{ $jenis === 'pendidikan' ? 'on' : '' }}"><i class="bi bi-mortarboard"></i> Pendidikan</a>
        <a href="{{ route('admin.evaluasi.index', ['jenis' => 'penelitian']) }}" class="{{ $jenis === 'penelitian' ? 'on' : '' }}"><i class="bi bi-journal-text"></i> Penelitian</a>
    </div>

    @if(session('success'))<div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>@endif

    {{-- FILTER --}}
    <form method="GET" class="card-x p-3 mb-3">
        <input type="hidden" name="jenis" value="{{ $jenis }}">
        <div class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="small fw-semibold text-muted mb-1">{{ $grupLabel }}</label>
                <select name="{{ $grupParam }}" class="form-select form-select-sm filter-sel">
                    <option value="">Semua {{ strtolower($grupLabel) }} (keseluruhan)</option>
                    @foreach($opsi as $id => $nm)
                        <option value="{{ $id }}" {{ (string) request($grupParam) === (string) $id ? 'selected' : '' }}>{{ \Illuminate\Support\Str::limit($nm, 90) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="small fw-semibold text-muted mb-1">Per bulan</label>
                <input type="month" name="bulan" value="{{ request('bulan') }}" class="form-control form-control-sm filter-sel">
            </div>
            <div class="col-6 col-md-2">
                <label class="small fw-semibold text-muted mb-1">Per tahun</label>
                <select name="tahun" class="form-select form-select-sm filter-sel">
                    <option value="">Semua tahun</option>
                    @foreach($tahunList as $y)
                        <option value="{{ $y }}" {{ (string) request('tahun') === (string) $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="small fw-semibold text-muted mb-1">Cari responden</label>
                <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm filter-sel" placeholder="Nama / instansi">
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button class="btn btn-sm text-white fw-semibold flex-fill" style="background:var(--maroon);"><i class="bi bi-funnel me-1"></i>Terapkan</button>
                @if(request($grupParam) || request('bulan') || request('tahun') || request('q'))
                    <a href="{{ route('admin.evaluasi.index', ['jenis' => $jenis]) }}" class="btn btn-sm btn-light border">Reset</a>
                @endif
            </div>
        </div>
        <div class="filter-note mt-2"><i class="bi bi-info-circle me-1"></i>Menampilkan: <b>{{ $labelFilter }}</b>. Bulan diutamakan bila bulan & tahun sama-sama diisi.</div>
    </form>

    {{-- RINGKASAN IKM --}}
    <div class="row g-3 mb-3">
        <div class="col-lg-4">
            <div class="ikm-hero d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-uppercase small fw-semibold" style="opacity:.85;">Nilai IKM</div>
                    <div class="ikm-score">{{ number_format($ikm['nilai'], 2) }}</div>
                    <div class="mt-1" style="opacity:.9;">{{ $ikm['total_responden'] ? $ikm['kategori'] : 'Belum ada tanggapan' }}</div>
                </div>
                <div class="text-center">
                    <div class="ikm-grade">{{ $ikm['total_responden'] ? $ikm['mutu'] : '-' }}</div>
                    <div class="small mt-2" style="opacity:.85;">Mutu</div>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card-x p-3 h-100">
                <div class="d-flex gap-2 mb-3 flex-wrap">
                    <div class="mini flex-fill"><div class="l">Responden</div><div class="v">{{ $ikm['total_responden'] }}</div></div>
                    <div class="mini flex-fill"><div class="l">Unsur dinilai</div><div class="v">{{ $ikm['jumlah_unsur'] }}</div></div>
                    <div class="mini flex-fill"><div class="l">{{ $grupLabel }}</div><div class="v">{{ count($grup) }}</div></div>
                </div>
                <div class="sec-h mb-2" style="font-size:.82rem;"><i class="bi bi-bar-chart" style="color:var(--maroon);"></i>Nilai per unsur (0–100)</div>
                <div style="max-height:190px; overflow:auto;">
                    <table class="table table-sm mb-0">
                        <tbody>
                        @forelse($ikm['per_unsur'] as $u)
                            <tr>
                                <td class="fw-bold" style="width:46px;color:var(--maroon);">{{ $u['kode'] }}</td>
                                <td class="small text-muted">{{ \Illuminate\Support\Str::limit($u['pertanyaan'], 70) }}@if($u['khusus'])<span class="chip-k" title="Pertanyaan khusus pelatihan">KHUSUS</span>@endif</td>
                                <td style="width:90px;"><div class="bar"><span style="width:{{ min(100, $u['nilai']) }}%;background:{{ $mutuWarna[$u['mutu']] }}"></span></div></td>
                                <td class="text-end fw-bold" style="width:60px;">{{ number_format($u['nilai'], 2) }}</td>
                                <td class="text-center" style="width:40px;"><span class="mutu" style="background:{{ $mutuWarna[$u['mutu']] }};width:24px;height:24px;font-size:.72rem;">{{ $u['mutu'] }}</span></td>
                            </tr>
                        @empty
                            <tr><td class="text-center text-muted py-3">Belum ada penilaian untuk filter ini.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- REKAP PER GRUP --}}
    <div class="card-x mb-3">
        <div class="p-3 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div class="sec-h"><i class="bi bi-grid-3x3-gap" style="color:var(--maroon);"></i>IKM per {{ strtolower($grupLabel) }}</div>
            <span class="small text-muted">Klik baris untuk melihat IKM {{ strtolower($grupLabel) }} itu saja</span>
        </div>
        <div class="table-responsive">
            <table class="table t-x mb-0">
                <thead><tr><th>{{ $grupLabel }}</th><th class="text-center">Responden</th><th>IKM</th><th class="text-center">Mutu</th><th>Terakhir</th><th class="text-end">Aksi</th></tr></thead>
                <tbody>
                @forelse($grup as $g)
                    @php
                        $aktif = $g['id'] && (string) request($grupParam) === (string) $g['id'];
                        $url = $g['id'] ? route('admin.evaluasi.index', array_merge($qs, [$grupParam => $g['id'], 'page' => null])) : null;
                    @endphp
                    <tr class="grup-row {{ $aktif ? 'on' : '' }}" @if($url) onclick="location.href='{{ $url }}'" @endif>
                        <td style="max-width:420px;"><div class="fw-semibold text-truncate">{{ $g['nama'] }}</div></td>
                        <td class="text-center fw-bold">{{ $g['responden'] }}</td>
                        <td style="min-width:150px;">
                            @if($g['ikm'] !== null)
                                <div class="d-flex align-items-center gap-2"><div class="bar flex-fill"><span style="width:{{ min(100, $g['ikm']) }}%;background:{{ $mutuWarna[$g['mutu']] }}"></span></div><b>{{ number_format($g['ikm'], 2) }}</b></div>
                            @else <span class="text-muted">-</span> @endif
                        </td>
                        <td class="text-center"><span class="mutu" style="background:{{ $mutuWarna[$g['mutu']] }}">{{ $g['mutu'] }}</span></td>
                        <td class="small text-muted text-nowrap">{{ $g['terakhir'] ? \Carbon\Carbon::parse($g['terakhir'])->isoFormat('D MMM YYYY') : '-' }}</td>
                        <td class="text-end text-nowrap" onclick="event.stopPropagation()">
                            @if($jenis === 'pelatihan' && $g['id'])
                                @php $link = route('evaluasi.pelatihan.form', [$g['id'], $g['token']]); @endphp
                                <button type="button" class="ic-btn" title="Salin link form evaluasi" onclick="salinLink('{{ $link }}', this)"><i class="bi bi-link-45deg"></i></button>
                                <a href="{{ route('admin.master_evaluasi.index', ['jenis' => 'pelatihan', 'kegiatan_id' => $g['id']]) }}" class="ic-btn" title="Pertanyaan khusus pelatihan ini"><i class="bi bi-sliders"></i></a>
                            @elseif($jenis === 'penelitian' && $g['id'])
                                @php $link = route('evaluasi.penelitian.form', [$g['id'], $g['token']]); @endphp
                                <button type="button" class="ic-btn" title="Salin link form evaluasi" onclick="salinLink('{{ $link }}', this)"><i class="bi bi-link-45deg"></i></button>
                            @endif
                            @if($g['id'])<a href="{{ route('admin.evaluasi.export.pdf', array_merge($qs, [$grupParam => $g['id']])) }}" class="ic-btn" title="Laporan IKM {{ strtolower($grupLabel) }} ini (PDF)"><i class="bi bi-file-earmark-pdf"></i></a>@endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4"><i class="bi bi-inbox d-block fs-4 mb-1"></i>Belum ada tanggapan.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if($jenis === 'pelatihan')
            <div class="px-3 py-2 small text-muted border-top"><i class="bi bi-info-circle me-1"></i>Link evaluasi untuk pelatihan yang belum ada tanggapan bisa diambil dari menu <a href="{{ route('admin.master_evaluasi.index', ['jenis' => 'pelatihan']) }}">Pertanyaan</a> → pilih pelatihan. Form publik: <a href="{{ route('evaluasi.public.form') }}" target="_blank">{{ route('evaluasi.public.form') }}</a></div>
        @elseif($jenis === 'penelitian')
            <div class="px-3 py-2 small text-muted border-top"><i class="bi bi-info-circle me-1"></i>Link evaluasi tiap penelitian juga tampil di halaman detail penelitian dan di halaman presentasi peneliti.</div>
        @else
            <div class="px-3 py-2 small text-muted border-top"><i class="bi bi-info-circle me-1"></i>Evaluasi pendidikan diisi mahasiswa magang sebelum mengunduh sertifikat.</div>
        @endif
    </div>

    {{-- DAFTAR TANGGAPAN --}}
    <div class="card-x">
        <div class="p-3 border-bottom sec-h"><i class="bi bi-list-ul" style="color:var(--maroon);"></i>Daftar tanggapan <span class="small text-muted fw-normal">({{ $evaluasi->total() }})</span></div>
        <div class="table-responsive">
            <table class="table t-x table-hover mb-0">
                <thead><tr><th>Tanggal</th><th>Responden</th><th>{{ $grupLabel }}</th><th class="text-center">IKM</th><th class="text-end">Aksi</th></tr></thead>
                <tbody>
                @forelse($evaluasi as $ev)
                    @php
                        $g = $jenis === 'pelatihan' ? (optional($ev->kegiatan)->nama_kegiatan ?: $ev->nama_kegiatan)
                           : ($jenis === 'pendidikan' ? (optional($ev->mou)->nama_instansi ?: $ev->instansi) : optional($ev->praPenelitian)->judul);
                    @endphp
                    <tr>
                        <td class="small text-nowrap">{{ optional($ev->created_at)->isoFormat('D MMM YYYY') }}<div class="text-muted" style="font-size:.7rem;">{{ optional($ev->created_at)->format('H:i') }}</div></td>
                        <td><div class="fw-semibold">{{ $ev->nama ?: '(anonim)' }}</div><div class="small text-muted">{{ $ev->instansi ?: '-' }}</div></td>
                        <td class="small" style="max-width:340px;"><div class="text-truncate">{{ $g ?: '-' }}</div></td>
                        <td class="text-center fw-bold">{{ $ev->nilai_ikm !== null ? number_format($ev->nilai_ikm, 2) : '-' }}</td>
                        <td class="text-end text-nowrap">
                            <a href="{{ route('admin.evaluasi.show', $ev->id) }}" class="ic-btn" title="Lihat"><i class="bi bi-eye"></i></a>
                            <form action="{{ route('admin.evaluasi.destroy', $ev->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus tanggapan ini?');">
                                @csrf @method('DELETE')
                                <button class="ic-btn" title="Hapus"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">Belum ada tanggapan.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $evaluasi->links() }}</div>
    </div>
</div>

<script>
    function salinLink(url, btn) {
        var done = function () { var i = btn.querySelector('i'); i.className = 'bi bi-check2'; setTimeout(function () { i.className = 'bi bi-link-45deg'; }, 1500); };
        if (navigator.clipboard && window.isSecureContext) { navigator.clipboard.writeText(url).then(done, function () { prompt('Salin link:', url); }); }
        else { prompt('Salin link:', url); }
    }
</script>
@endsection
