@extends('layouts.app')

@section('title', 'Laporan Penilaian')
@section('page-title', 'Laporan Penilaian (Lintas Ruangan)')

@section('content')
<style>
    :root { --maroon:#7c1316; --maroon-light:#a3191d; }
    .lp-head { background:linear-gradient(135deg,var(--maroon),var(--maroon-light)); color:#fff; border-radius:16px; padding:18px 22px; box-shadow:0 8px 24px rgba(124,19,22,.18); position:relative; overflow:hidden; }
    .lp-head::after { content:''; position:absolute; right:-40px; top:-40px; width:160px; height:160px; background:rgba(255,255,255,.07); border-radius:50%; }
    .lp-card { background:#fff; border:1px solid #eef2f7; border-radius:16px; padding:16px; box-shadow:0 2px 12px rgba(0,0,0,.04); }
    .filter-input { border-radius:9px; border:1.5px solid #e2e8f0; font-size:.9rem; }
    .filter-input:focus { border-color:var(--maroon); box-shadow:0 0 0 .2rem rgba(124,19,22,.12); }
    .lp-table thead th { background:#fcf0f1; color:#7c1316; font-size:.74rem; text-transform:uppercase; letter-spacing:.4px; white-space:nowrap; position:sticky; top:0; }
    .lp-table td, .lp-table th { vertical-align:middle; }
    .nilai-badge { font-weight:800; }
    .empty-state { background:#fff; border:1.5px dashed #d8dee6; border-radius:16px; padding:40px 20px; text-align:center; color:#94a3b8; }
</style>

<div class="container-fluid pb-4">

    <div class="lp-head d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <h5 class="fw-bold mb-1"><i class="bi bi-file-earmark-bar-graph me-2"></i>Laporan Penilaian Lintas Ruangan</h5>
            <div class="opacity-75 small">Nilai akhir tiap ruangan = rata-rata indikator (yang masuk sertifikat)</div>
        </div>
        <a href="{{ route('admin.laporan_penilaian', array_merge(request()->query(), ['export' => 'csv'])) }}" class="btn btn-light btn-sm rounded-pill fw-semibold"><i class="bi bi-download me-1"></i> Unduh CSV</a>
    </div>

    {{-- FILTER --}}
    <form method="GET" action="{{ route('admin.laporan_penilaian') }}" class="lp-card mb-3">
        <div class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted mb-1">Cari Mahasiswa</label>
                <input type="text" name="q" value="{{ $search }}" class="form-control form-control-sm filter-input" placeholder="Nama mahasiswa...">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted mb-1">Ruangan</label>
                <select name="ruangan" class="form-select form-select-sm filter-input">
                    <option value="">Semua Ruangan</option>
                    @foreach($ruanganOptions as $rid => $rnama)
                        <option value="{{ $rid }}" {{ (string)$ruanganId === (string)$rid ? 'selected' : '' }}>{{ $rnama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold text-muted mb-1">Program Studi</label>
                <select name="prodi" class="form-select form-select-sm filter-input">
                    <option value="">Semua Prodi</option>
                    @foreach($prodiOptions as $p)
                        <option value="{{ $p }}" {{ $prodi == $p ? 'selected' : '' }}>{{ $p }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold text-muted mb-1">Instansi</label>
                <select name="instansi" class="form-select form-select-sm filter-input">
                    <option value="">Semua</option>
                    @foreach($instansiOptions as $i)
                        <option value="{{ $i }}" {{ $instansi == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-1">
                <label class="form-label small fw-semibold text-muted mb-1">Dari</label>
                <input type="date" name="start" value="{{ $start }}" class="form-control form-control-sm filter-input">
            </div>
            <div class="col-6 col-md-1">
                <label class="form-label small fw-semibold text-muted mb-1">Sampai</label>
                <input type="date" name="end" value="{{ $end }}" class="form-control form-control-sm filter-input">
            </div>
        </div>
        <div class="d-flex justify-content-between align-items-center mt-2">
            @if($search || $prodi || $instansi || $ruanganId || $start || $end)
                <a href="{{ route('admin.laporan_penilaian') }}" class="small text-muted text-decoration-none"><i class="bi bi-x-circle me-1"></i>Reset filter</a>
            @else <span></span> @endif
            <button type="submit" class="btn btn-sm text-white fw-semibold px-4" style="background:var(--maroon);"><i class="bi bi-funnel me-1"></i>Terapkan</button>
        </div>
        @if(!$ruanganId)
            <div class="small text-muted mt-2"><i class="bi bi-info-circle me-1"></i>Pilih satu <b>Ruangan</b> untuk melihat rincian nilai per indikator.</div>
        @endif
    </form>

    {{-- TABEL --}}
    @if(count($rows))
    <div class="lp-card p-0">
        <div class="table-responsive" style="max-height:70vh;">
            <table class="table table-hover lp-table mb-0">
                <thead>
                    <tr>
                        <th class="ps-3">No</th>
                        <th>Nama</th>
                        <th>Prodi</th>
                        <th>Instansi</th>
                        <th>Periode</th>
                        <th>Ruangan</th>
                        @foreach($indikators as $ind)
                            <th class="text-center">{{ $ind->nama }}</th>
                        @endforeach
                        <th class="text-center">Nilai Akhir</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rows as $i => $r)
                    <tr>
                        <td class="ps-3 text-muted">{{ $i + 1 }}</td>
                        <td class="fw-semibold">{{ $r['mhs']->nm_mahasiswa }}</td>
                        <td class="small">{{ $r['mhs']->prodi ?: '-' }}</td>
                        <td class="small">{{ $r['instansi'] }}</td>
                        <td class="small text-nowrap">{{ $r['periode'] }}</td>
                        <td class="small"><span class="badge bg-light text-dark border">{{ $r['ruangan'] }}</span></td>
                        @foreach($indikators as $ind)
                            <td class="text-center">{{ $r['indikator'][$ind->nama] !== '' ? $r['indikator'][$ind->nama] : '-' }}</td>
                        @endforeach
                        <td class="text-center">
                            @if($r['nilai'] !== null)
                                <span class="nilai-badge" style="color:{{ $r['nilai'] >= 75 ? '#166534' : ($r['nilai'] >= 60 ? '#b45309' : '#991b1b') }};">{{ $r['nilai'] }}</span>
                            @else
                                <span class="badge bg-light text-secondary border">-</span>
                            @endif
                        </td>
                        <td class="small text-muted" style="max-width:220px;">{{ $r['catatan'] ?: '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="small text-muted mt-2">{{ count($rows) }} baris penilaian (mahasiswa × ruangan).</div>
    @else
        <div class="empty-state">
            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
            <div class="fw-semibold">Tidak ada data penilaian sesuai filter.</div>
            @if($search || $prodi || $instansi || $ruanganId || $start || $end)
                <a href="{{ route('admin.laporan_penilaian') }}" class="small">Reset filter</a>
            @endif
        </div>
    @endif

</div>
@endsection
