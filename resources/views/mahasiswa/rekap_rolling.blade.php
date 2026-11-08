@extends('layouts.app')

@section('title', 'Rekap Rolling Ruangan')

@section('content')
@php
    $tgl = fn ($d) => $d ? $d->translatedFormat('d F Y') : '-';
    // Data untuk Excel: sheet ringkas (ruangan digabung) & rinci (per rolling)
    $xRingkas = [];
    $xRinci = [];
    $no = 0;
    foreach ($data as $i => $d) {
        $xRingkas[] = [$i + 1, $d['nama'], $d['univ'], $d['prodi'], $d['rolling']->pluck('ruangan')->implode(', ') ?: '-', $tgl($d['mulai']), $tgl($d['selesai'])];
        foreach ($d['rolling'] as $r) {
            $xRinci[] = [++$no, $d['nama'], $d['univ'], $d['prodi'], $r['ruangan'], $tgl($r['mulai']), $tgl($r['selesai'])];
        }
    }
    $jmlRolling = $no;
@endphp
<style>
    :root { --maroon:#7c1316; --maroon-subtle:#fcf0f1; }
    .hero { background: linear-gradient(135deg,#7c1316,#5f0f12); color:#fff; border-radius:20px; padding:22px 26px; margin-bottom:1.25rem; box-shadow:0 14px 34px rgba(124,19,22,.28); }
    .hero h4 { font-weight:800; margin:0 0 4px; }
    .card-soft { background:#fff; border-radius:14px; box-shadow:0 4px 20px rgba(0,0,0,.05); overflow:hidden; }
    .view-tabs .btn { border-radius:20px; font-weight:600; font-size:.85rem; }
    .view-tabs .btn.active { background:var(--maroon); color:#fff; border-color:var(--maroon); }
    .table thead th { background:var(--maroon-subtle); color:var(--maroon); font-size:.72rem; text-transform:uppercase; white-space:nowrap; position:sticky; top:0; z-index:1; }
    .table td { font-size:.86rem; vertical-align:middle; }
    .chip { display:inline-block; background:#f1f5f9; color:#334155; border-radius:12px; padding:2px 9px; font-size:.75rem; margin:1px 2px 1px 0; white-space:nowrap; }
    .chip .n { color:var(--maroon); font-weight:800; margin-right:3px; }
    .grup td { border-top:2px solid #e2e8f0; }
    .scroll-y { max-height:70vh; overflow:auto; }
</style>

<div class="container-fluid py-3">
    <div class="hero d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <h4><i class="bi bi-arrow-repeat me-2"></i>Rekap Rolling Ruangan</h4>
            <div style="opacity:.9;font-size:.9rem;">{{ $data->count() }} mahasiswa · {{ $jmlRolling }} rolling ruangan</div>
        </div>
        <button type="button" class="btn btn-light fw-semibold rounded-pill" onclick="unduhExcel()" {{ $data->count() ? '' : 'disabled' }}>
            <i class="bi bi-file-earmark-excel text-success me-1"></i> Export Excel
        </button>
    </div>

    <div class="card-soft mb-3">
        <form method="GET" class="p-3 row g-2 align-items-end">
            <input type="hidden" name="mode" value="{{ $mode }}">
            <div class="col-12 col-md-3">
                <label class="form-label small fw-bold mb-1">Cari</label>
                <input type="text" name="q" value="{{ $q }}" class="form-control form-control-sm" placeholder="Nama / prodi...">
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label small fw-bold mb-1">Universitas / Instansi</label>
                <select name="mou_id" class="form-select form-select-sm">
                    <option value="">Semua</option>
                    @foreach($mous as $m)
                        <option value="{{ $m->id }}" @selected(request('mou_id') == $m->id)>{{ $m->nama_instansi ?: $m->nama_universitas }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small fw-bold mb-1">Ruangan</label>
                <select name="ruangan_id" class="form-select form-select-sm">
                    <option value="">Semua</option>
                    @foreach($ruangans as $r)
                        <option value="{{ $r->id }}" @selected(request('ruangan_id') == $r->id)>{{ $r->nm_ruangan }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-1">
                <label class="form-label small fw-bold mb-1">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="aktif" @selected($status === 'aktif')>Aktif</option>
                    <option value="nonaktif" @selected($status === 'nonaktif')>Nonaktif</option>
                    <option value="semua" @selected($status === 'semua')>Semua</option>
                </select>
            </div>
            <div class="col-6 col-md-1">
                <label class="form-label small fw-bold mb-1">Dari</label>
                <input type="date" name="dari" value="{{ request('dari') }}" class="form-control form-control-sm">
            </div>
            <div class="col-6 col-md-1">
                <label class="form-label small fw-bold mb-1">Sampai</label>
                <input type="date" name="sampai" value="{{ request('sampai') }}" class="form-control form-control-sm">
            </div>
            <div class="col-12 col-md-1 d-grid">
                <button class="btn btn-sm text-white" style="background:var(--maroon);"><i class="bi bi-search"></i> Filter</button>
            </div>
            <div class="col-12 d-flex flex-wrap justify-content-between align-items-center gap-2 mt-2">
                <div class="view-tabs btn-group">
                    <a href="{{ request()->fullUrlWithQuery(['mode' => 'ringkas']) }}" class="btn btn-sm btn-outline-secondary {{ $mode === 'ringkas' ? 'active' : '' }}"><i class="bi bi-list me-1"></i>Per Mahasiswa</a>
                    <a href="{{ request()->fullUrlWithQuery(['mode' => 'rinci']) }}" class="btn btn-sm btn-outline-secondary {{ $mode === 'rinci' ? 'active' : '' }}"><i class="bi bi-list-ol me-1"></i>Per Rolling Ruangan</a>
                </div>
                <small class="text-muted">Filter Dari–Sampai: mahasiswa yang masa magangnya beririsan dengan rentang tersebut. Excel berisi 2 sheet (per mahasiswa & per rolling).</small>
            </div>
        </form>
    </div>

    <div class="card-soft">
        <div class="table-responsive scroll-y">
            <table class="table table-hover mb-0">
                <thead>
                    <tr><th>No</th><th>Nama</th><th>Universitas</th><th>Program Studi</th><th>Ruangan</th><th>Mulai</th><th>Selesai</th></tr>
                </thead>
                <tbody>
                    @if($mode === 'ringkas')
                        @forelse($data as $i => $d)
                            <tr>
                                <td class="text-muted">{{ $i + 1 }}</td>
                                <td class="fw-semibold">{{ $d['nama'] }}</td>
                                <td>{{ $d['univ'] }}</td>
                                <td class="small">{{ $d['prodi'] }}</td>
                                <td>
                                    @forelse($d['rolling'] as $k => $r)
                                        <span class="chip" title="{{ $tgl($r['mulai']) }} – {{ $tgl($r['selesai']) }}"><span class="n">{{ $k + 1 }}</span>{{ $r['ruangan'] }}</span>
                                    @empty
                                        <span class="text-muted">-</span>
                                    @endforelse
                                </td>
                                <td class="text-nowrap">{{ $tgl($d['mulai']) }}</td>
                                <td class="text-nowrap">{{ $tgl($d['selesai']) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-muted py-5">Tidak ada data.</td></tr>
                        @endforelse
                    @else
                        @php $n = 0; @endphp
                        @forelse($data as $d)
                            @forelse($d['rolling'] as $k => $r)
                                <tr class="{{ $k === 0 ? 'grup' : '' }}">
                                    <td class="text-muted">{{ ++$n }}</td>
                                    <td class="fw-semibold">{{ $d['nama'] }}</td>
                                    <td>{{ $d['univ'] }}</td>
                                    <td class="small">{{ $d['prodi'] }}</td>
                                    <td><span class="chip"><span class="n">{{ $k + 1 }}</span>{{ $r['ruangan'] }}</span></td>
                                    <td class="text-nowrap">{{ $tgl($r['mulai']) }}</td>
                                    <td class="text-nowrap">{{ $tgl($r['selesai']) }}</td>
                                </tr>
                            @empty
                                <tr class="grup">
                                    <td class="text-muted">{{ ++$n }}</td>
                                    <td class="fw-semibold">{{ $d['nama'] }}</td>
                                    <td>{{ $d['univ'] }}</td>
                                    <td class="small">{{ $d['prodi'] }}</td>
                                    <td class="text-muted">Belum ada rolling</td>
                                    <td class="text-nowrap">{{ $tgl($d['mulai']) }}</td>
                                    <td class="text-nowrap">{{ $tgl($d['selesai']) }}</td>
                                </tr>
                            @endforelse
                        @empty
                            <tr><td colspan="7" class="text-center text-muted py-5">Tidak ada data.</td></tr>
                        @endforelse
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.sheetjs.com/xlsx-0.20.3/package/dist/xlsx.full.min.js"></script>
<script>
    var X_RINGKAS = @json($xRingkas);
    var X_RINCI = @json($xRinci);
    function sheet(rows) {
        var ws = XLSX.utils.aoa_to_sheet([['No', 'Nama', 'Universitas', 'Program Studi', 'Ruangan', 'Mulai', 'Selesai']].concat(rows));
        ws['!cols'] = [{wch: 5}, {wch: 30}, {wch: 30}, {wch: 24}, {wch: 40}, {wch: 18}, {wch: 18}];
        return ws;
    }
    function unduhExcel() {
        if (!window.XLSX) return;
        var wb = XLSX.utils.book_new();
        // sheet yang sedang ditampilkan jadi sheet pertama
        var ringkas = ['Per Mahasiswa', sheet(X_RINGKAS)], rinci = ['Per Rolling Ruangan', sheet(X_RINCI)];
        @json($mode === 'rinci') ? [rinci, ringkas].forEach(function (s) { XLSX.utils.book_append_sheet(wb, s[1], s[0]); })
                                 : [ringkas, rinci].forEach(function (s) { XLSX.utils.book_append_sheet(wb, s[1], s[0]); });
        XLSX.writeFile(wb, 'Rekap_Rolling_Ruangan_' + new Date().toISOString().slice(0, 10) + '.xlsx');
    }
</script>
@endsection
