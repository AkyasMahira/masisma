@extends('layouts.app')

@section('title', 'Absensi Harian Mahasiswa')

@section('content')
@php
    $STATUS = [
        'hadir'             => ['Hadir', 'st-hadir', 'bi-check-circle-fill'],
        'bertugas'          => ['Sedang Bertugas', 'st-tugas', 'bi-clock-history'],
        'tanpa_pulang'      => ['Tidak Absen Pulang', 'st-warn', 'bi-exclamation-circle'],
        'izin'              => ['Izin / Sakit', 'st-izin', 'bi-envelope-paper'],
        'libur'             => ['Libur', 'st-libur', 'bi-moon-stars'],
        'belum_absen'       => ['Belum Absen', 'st-warn', 'bi-hourglass-split'],
        'tidak_hadir'       => ['Tidak Hadir', 'st-alpha', 'bi-x-circle-fill'],
        'belum_dijadwalkan' => ['Belum Dijadwalkan', 'st-libur', 'bi-calendar-x'],
        'mendatang'         => ['Mendatang', 'st-libur', 'bi-calendar'],
    ];
    $satuHari = $dari->equalTo($sampai);
    $prev = $dari->copy()->subDay()->toDateString();
    $next = $dari->copy()->addDay()->toDateString();
@endphp
<style>
    :root { --maroon:#7c1316; --maroon-subtle:#fcf0f1; --radius:14px; --shadow:0 4px 20px rgba(0,0,0,.05); }
    .hero { background: linear-gradient(135deg,#7c1316,#5f0f12); color:#fff; border-radius:20px; padding:24px 28px; margin-bottom:1.25rem; box-shadow:0 14px 34px rgba(124,19,22,.28); position:relative; overflow:hidden; }
    .hero::after { content:''; position:absolute; right:-40px; top:-40px; width:170px; height:170px; background:rgba(255,255,255,.07); border-radius:50%; pointer-events:none; }
    .hero h4 { font-weight:800; margin:0 0 4px; }
    .card-soft { background:#fff; border-radius:var(--radius); box-shadow:var(--shadow); overflow:hidden; }
    .stat { background:#fff; border-radius:var(--radius); box-shadow:var(--shadow); padding:12px 14px; text-decoration:none; display:flex; align-items:center; gap:10px; border:2px solid transparent; height:100%; }
    .stat.active { border-color: var(--maroon); }
    .stat .n { font-size:1.35rem; font-weight:800; line-height:1; color:#0f172a; }
    .stat .l { color:#64748b; font-size:.7rem; font-weight:700; text-transform:uppercase; }
    .stat .ic { width:34px; height:34px; border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .table thead th { background:var(--maroon-subtle); color:var(--maroon); font-size:.72rem; text-transform:uppercase; white-space:nowrap; position:sticky; top:0; z-index:1; }
    .table td { font-size:.86rem; vertical-align:middle; }
    .pill { padding:3px 10px; border-radius:20px; font-size:.72rem; font-weight:700; white-space:nowrap; display:inline-flex; align-items:center; gap:4px; }
    .st-hadir { background:#dcfce7; color:#15803d; } .st-tugas { background:#dbeafe; color:#1d4ed8; }
    .st-warn { background:#fef3c7; color:#b45309; } .st-izin { background:#ede9fe; color:#6d28d9; }
    .st-libur { background:#f1f5f9; color:#64748b; } .st-alpha { background:#fee2e2; color:#b91c1c; }
    .jam { font-variant-numeric: tabular-nums; font-weight:700; }
    .tgl-row td { background:#f8fafc; font-weight:800; color:var(--maroon); font-size:.8rem; }
    .scroll-y { max-height: 68vh; overflow:auto; }
</style>

<div class="container-fluid py-3">
    <div class="hero d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <h4><i class="bi bi-calendar-check me-2"></i>Absensi Harian Mahasiswa</h4>
            <div style="opacity:.9;font-size:.9rem;">{{ $mou->nama_instansi ?: $mou->nama_universitas }} · {{ $satuHari ? $dari->translatedFormat('l, d F Y') : $dari->translatedFormat('d M Y') . ' – ' . $sampai->translatedFormat('d M Y') }}</div>
        </div>
        <a href="{{ route('instansi.rekap') }}" class="btn btn-light fw-semibold rounded-pill"><i class="bi bi-clipboard2-data me-1"></i> Rekap & Laporan</a>
    </div>

    {{-- Filter --}}
    <div class="card-soft mb-3">
        <form method="GET" class="p-3 row g-2 align-items-end">
            <div class="col-6 col-md-2">
                <label class="form-label small fw-bold mb-1">Dari</label>
                <input type="date" name="dari" value="{{ $dari->toDateString() }}" class="form-control form-control-sm">
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small fw-bold mb-1">Sampai</label>
                <input type="date" name="sampai" value="{{ $sampai->toDateString() }}" class="form-control form-control-sm">
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label small fw-bold mb-1">Cari</label>
                <input type="text" name="q" value="{{ $q }}" class="form-control form-control-sm" placeholder="Nama / prodi...">
            </div>
            <div class="col-12 col-md-5 d-flex flex-wrap gap-2">
                <input type="hidden" name="status" value="{{ $fStatus }}">
                <button class="btn btn-sm text-white" style="background:var(--maroon);"><i class="bi bi-search me-1"></i>Tampilkan</button>
                <a href="{{ route('instansi.absensi_harian', ['dari' => $prev, 'sampai' => $prev, 'q' => $q]) }}" class="btn btn-sm btn-light border" title="Hari sebelumnya"><i class="bi bi-chevron-left"></i></a>
                <a href="{{ route('instansi.absensi_harian', ['q' => $q]) }}" class="btn btn-sm btn-light border">Hari ini</a>
                <a href="{{ route('instansi.absensi_harian', ['dari' => $next, 'sampai' => $next, 'q' => $q]) }}" class="btn btn-sm btn-light border" title="Hari berikutnya"><i class="bi bi-chevron-right"></i></a>
                <button type="button" class="btn btn-sm btn-success ms-auto" onclick="unduhExcel()" {{ count($rows) ? '' : 'disabled' }}><i class="bi bi-file-earmark-excel me-1"></i>Excel</button>
            </div>
            <div class="col-12"><small class="text-muted">Rentang maksimal 31 hari. Data dari absensi QR, dispensasi yang disetujui, dan jadwal shift RS.</small></div>
        </form>
    </div>

    {{-- Ringkasan status (klik untuk filter) --}}
    <div class="row g-2 mb-3">
        @foreach(['hadir', 'bertugas', 'tanpa_pulang', 'izin', 'belum_absen', 'tidak_hadir', 'libur'] as $k)
            @php $aktif = $fStatus === $k; $cls = $STATUS[$k][1]; @endphp
            <div class="col-6 col-md-3 col-xl">
                <a href="{{ route('instansi.absensi_harian', ['dari' => $dari->toDateString(), 'sampai' => $sampai->toDateString(), 'q' => $q, 'status' => $aktif ? null : $k]) }}" class="stat {{ $aktif ? 'active' : '' }}">
                    <div class="ic {{ $cls }}"><i class="bi {{ $STATUS[$k][2] }}"></i></div>
                    <div><div class="n">{{ $hitung[$k] ?? 0 }}</div><div class="l">{{ $STATUS[$k][0] }}</div></div>
                </a>
            </div>
        @endforeach
    </div>

    <div class="card-soft">
        <div class="table-responsive scroll-y">
            <table class="table table-hover mb-0" id="tblAbsensi">
                <thead>
                    <tr>
                        @unless($satuHari)<th>Tanggal</th>@endunless
                        <th>Nama</th><th>Prodi</th><th>Ruangan</th><th>Shift</th>
                        <th class="text-center">Masuk</th><th class="text-center">Pulang</th><th>Status</th><th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @php $tglSebelum = null; @endphp
                    @forelse($rows as $r)
                        @if(!$satuHari && $tglSebelum !== $r['tanggal']->toDateString())
                            <tr class="tgl-row"><td colspan="9"><i class="bi bi-calendar3 me-1"></i>{{ $r['tanggal']->translatedFormat('l, d F Y') }}</td></tr>
                            @php $tglSebelum = $r['tanggal']->toDateString(); @endphp
                        @endif
                        <tr>
                            @unless($satuHari)<td class="text-muted small">{{ $r['tanggal']->format('d/m') }}</td>@endunless
                            <td class="fw-semibold">{{ $r['nama'] }}</td>
                            <td class="text-muted small">{{ $r['prodi'] ?: '-' }}</td>
                            <td>{{ $r['ruangan'] ?: '-' }}</td>
                            <td>{{ $r['shift'] ?: '-' }}</td>
                            <td class="text-center jam">{{ $r['masuk'] ?: '–' }}</td>
                            <td class="text-center jam">{{ $r['pulang'] ?: '–' }}@if($r['pulang_besok'])<small class="text-muted fw-normal"> (+1)</small>@endif</td>
                            <td><span class="pill {{ $STATUS[$r['status']][1] }}"><i class="bi {{ $STATUS[$r['status']][2] }}"></i>{{ $STATUS[$r['status']][0] }}</span></td>
                            <td class="small text-muted">{{ $r['ket'] }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="text-center text-muted py-5"><i class="bi bi-calendar-x d-block fs-2 mb-2"></i>Tidak ada mahasiswa yang sedang magang pada tanggal ini{{ $fStatus ? ' dengan status tersebut' : '' }}.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.sheetjs.com/xlsx-0.20.3/package/dist/xlsx.full.min.js"></script>
@php
    $dataExcel = [];
    foreach ($rows as $r) {
        $dataExcel[] = [$r['tanggal']->format('d/m/Y'), $r['nama'], $r['prodi'], $r['ruangan'], $r['shift'], $r['masuk'], $r['pulang'] . ($r['pulang_besok'] ? ' (+1 hari)' : ''), $STATUS[$r['status']][0], $r['ket']];
    }
@endphp
<script>
    var DATA_ABSENSI = @json($dataExcel);
    function unduhExcel() {
        if (!window.XLSX || !DATA_ABSENSI.length) return;
        var ws = XLSX.utils.aoa_to_sheet([['Tanggal', 'Nama', 'Prodi', 'Ruangan', 'Shift', 'Masuk', 'Pulang', 'Status', 'Keterangan']].concat(DATA_ABSENSI));
        ws['!cols'] = [{wch: 11}, {wch: 32}, {wch: 22}, {wch: 22}, {wch: 12}, {wch: 8}, {wch: 14}, {wch: 18}, {wch: 34}];
        var wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Absensi Harian');
        XLSX.writeFile(wb, 'Absensi_Harian_{{ $dari->format('Ymd') }}{{ $satuHari ? '' : '-' . $sampai->format('Ymd') }}.xlsx');
    }
</script>
@endsection
