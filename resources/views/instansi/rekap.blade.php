@extends('layouts.app')

@section('title', 'Rekap & Laporan Peserta')

@section('content')
<style>
    :root { --maroon:#7c1316; --maroon-light:#a3191d; --maroon-subtle:#fcf0f1; --radius:14px; --shadow:0 4px 20px rgba(0,0,0,.05); }
    .hero { background: linear-gradient(135deg,#7c1316,#5f0f12); color:#fff; border-radius:20px; padding:24px 28px; margin-bottom:1.25rem; box-shadow:0 14px 34px rgba(124,19,22,.28); }
    .hero h4 { font-weight:800; margin:0 0 4px; }
    .card-soft { background:#fff; border-radius:var(--radius); box-shadow:var(--shadow); overflow:hidden; }
    .table thead th { background:var(--maroon-subtle); color:var(--maroon); font-size:.76rem; text-transform:uppercase; }
    .pill { padding:3px 10px; border-radius:20px; font-size:.72rem; font-weight:700; }
    .st-selesai { background:#dcfce7; color:#15803d; } .st-jalan { background:#fef3c7; color:#b45309; }
    .btn-sertif { font-size:.72rem; font-weight:600; border-radius:7px; padding:4px 9px; text-decoration:none; display:inline-flex; align-items:center; gap:4px; }
    .b-magang { background:#fde8e8; color:#7c1316; } .b-magang:hover { background:#7c1316; color:#fff; }
    .b-orient { background:#e0f2fe; color:#0369a1; } .b-orient:hover { background:#0369a1; color:#fff; }
    .nilai-badge { font-weight:800; font-size:1rem; }
</style>

<div class="container-fluid py-3">
    <div class="hero d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <h4><i class="bi bi-clipboard2-data me-2"></i>Rekap & Laporan Peserta</h4>
            <div style="opacity:.9;font-size:.9rem;">{{ $mou->nama_instansi ?: $mou->nama_universitas }} · {{ $mahasiswas->count() }} peserta terdaftar</div>
        </div>
        <a href="{{ route('instansi.dashboard') }}" class="btn btn-light fw-semibold rounded-pill"><i class="bi bi-arrow-left me-1"></i> Dashboard</a>
    </div>

    @if(session('error'))<div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>@endif

    <div class="card-soft">
        <div class="p-3 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h6 class="mb-0 fw-bold"><i class="bi bi-people me-1"></i> Daftar Peserta Magang</h6>
            <input type="text" id="cari" class="form-control form-control-sm" style="max-width:260px;" placeholder="Cari nama/prodi...">
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="tbl">
                <thead><tr>
                    <th width="4%">No</th><th>Nama</th><th>Prodi</th><th>Periode</th>
                    <th class="text-center">Status</th><th class="text-center">Nilai Akhir</th>
                    <th class="text-center">Orientasi</th><th class="text-center" width="22%">Unduh / Detail</th>
                </tr></thead>
                <tbody>
                    @forelse($mahasiswas as $i => $m)
                    <tr>
                        <td class="text-muted">{{ $i+1 }}</td>
                        <td class="fw-semibold">{{ $m->nm_mahasiswa }}</td>
                        <td class="small">{{ $m->prodi ?: '-' }}</td>
                        <td class="small">{{ optional($m->tanggal_mulai)->format('d/m/y') }} - {{ optional($m->tanggal_berakhir)->format('d/m/y') }}</td>
                        <td class="text-center"><span class="pill {{ $m->is_selesai ? 'st-selesai' : 'st-jalan' }}">{{ $m->is_selesai ? 'Selesai' : 'Berjalan' }}</span></td>
                        <td class="text-center"><span class="nilai-badge" style="color:var(--maroon);">{{ $m->nilai_karu_final ?: '-' }}</span></td>
                        <td class="text-center">
                            @if($m->orientasi && $m->orientasi->status === 'lulus_orientasi')
                                <span class="pill st-selesai">Lulus</span>
                            @elseif($m->orientasi)
                                <span class="pill st-jalan">Proses</span>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="d-flex flex-wrap gap-1 justify-content-center">
                                <a href="{{ route('sertifikat.download', $m->share_token) }}" target="_blank" class="btn-sertif b-magang" title="Sertifikat Magang"><i class="bi bi-award"></i> Magang</a>
                                @if($m->orientasi && $m->orientasi->status === 'lulus_orientasi')
                                    <a href="{{ route('instansi.sertifikat.orientasi', $m->id) }}" target="_blank" class="btn-sertif b-orient" title="Sertifikat Orientasi"><i class="bi bi-patch-check"></i> Orientasi</a>
                                @endif
                                <button class="btn-sertif b-orient" style="background:#f1f5f9;color:#475569;" data-bs-toggle="collapse" data-bs-target="#det-{{ $m->id }}"><i class="bi bi-eye"></i> Nilai</button>
                            </div>
                        </td>
                    </tr>
                    <tr class="collapse" id="det-{{ $m->id }}">
                        <td colspan="8" class="bg-light">
                            <div class="row g-3 py-2">
                                <div class="col-md-6">
                                    <div class="fw-bold small mb-1" style="color:var(--maroon);">Nilai per Ruangan</div>
                                    @php $nr = is_array($m->nilai_ruangan_json) ? $m->nilai_ruangan_json : []; @endphp
                                    @if(count($nr))
                                        <ul class="small mb-0">
                                            @foreach($m->roomSequences->pluck('ruangan')->unique('id')->filter() as $rg)
                                                <li>{{ $rg->nm_ruangan }}: <strong>{{ $nr[$rg->id] ?? '-' }}</strong></li>
                                            @endforeach
                                        </ul>
                                    @else <span class="text-muted small">Belum ada nilai ruangan.</span> @endif
                                </div>
                                <div class="col-md-6">
                                    <div class="fw-bold small mb-1" style="color:var(--maroon);">Catatan / Evaluasi</div>
                                    @php $ev = is_array($m->nilai_evaluasi) ? $m->nilai_evaluasi : []; @endphp
                                    @if(count($ev))
                                        <ul class="small mb-0">
                                            @foreach($ev as $k => $v)
                                                <li>{{ ucwords(str_replace('_',' ',$k)) }}: <strong>{{ is_array($v) ? json_encode($v) : $v }}</strong></li>
                                            @endforeach
                                        </ul>
                                    @else <span class="text-muted small">Belum ada catatan evaluasi.</span> @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center text-muted py-5"><i class="bi bi-inbox fs-3 d-block mb-2"></i>Belum ada peserta yang jadi mahasiswa.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.getElementById('cari').addEventListener('keyup', function(){
        var q=this.value.toLowerCase();
        document.querySelectorAll('#tbl tbody tr').forEach(function(tr){
            if(tr.classList.contains('collapse')) return;
            tr.style.display = tr.innerText.toLowerCase().includes(q) ? '' : 'none';
        });
    });
</script>
@endsection
