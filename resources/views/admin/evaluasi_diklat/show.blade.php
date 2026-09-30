@extends('layouts.app')

@section('title', 'Detail Tanggapan Evaluasi')

@section('content')
<style>
    :root { --custom-maroon:#7c1316; --custom-maroon-subtle:#fcf0f1; --card-radius:12px; --shadow-soft:0 4px 20px rgba(0,0,0,.05); }
    .card-soft { background:#fff; border-radius:var(--card-radius); box-shadow:var(--shadow-soft); padding:22px; margin-bottom:18px; }
    .card-soft h5 { color:var(--custom-maroon); font-weight:700; font-size:1rem; }
    .info-item { padding:10px 0; border-bottom:1px dashed #e2e8f0; }
    .info-item .lbl { color:#64748b; font-size:.8rem; font-weight:600; }
    .info-item .val { font-weight:600; color:#2c3e50; }
    .btn-outline-custom { border:1.5px solid var(--custom-maroon); color:var(--custom-maroon); background:#fff; border-radius:8px; padding:8px 16px; font-weight:600; }
    .btn-outline-custom:hover { background:var(--custom-maroon-subtle); }
    .nilai-pill { display:inline-block; min-width:34px; text-align:center; font-weight:700; padding:3px 10px; border-radius:20px; background:var(--custom-maroon-subtle); color:var(--custom-maroon); }
    .table thead th { background:var(--custom-maroon-subtle); color:var(--custom-maroon); font-size:.8rem; }
</style>

<div class="container-fluid py-3">
    <a href="{{ route('admin.evaluasi.index') }}" class="btn-outline-custom mb-3 d-inline-block"><i class="bi bi-arrow-left me-1"></i> Kembali</a>

    @php $labels = [1=>'Tidak Baik',2=>'Kurang Baik',3=>'Baik',4=>'Sangat Baik']; @endphp

    <div class="row g-3">
        <div class="col-lg-4">
            <div class="card-soft">
                <h5 class="mb-3"><i class="bi bi-person-badge me-1"></i> Data Responden</h5>
                <div class="info-item"><div class="lbl">Nama</div><div class="val">{{ $evaluasi->nama ?: '(anonim)' }}</div></div>
                <div class="info-item"><div class="lbl">Instansi</div><div class="val">{{ $evaluasi->instansi ?: '-' }}</div></div>
                <div class="info-item"><div class="lbl">Kontak</div><div class="val">{{ $evaluasi->kontak ?: '-' }}</div></div>
                <div class="info-item"><div class="lbl">Jenis Kelamin</div><div class="val">{{ $evaluasi->jenis_kelamin === 'L' ? 'Laki-laki' : ($evaluasi->jenis_kelamin === 'P' ? 'Perempuan' : '-') }}</div></div>
                <div class="info-item"><div class="lbl">Pendidikan</div><div class="val">{{ $evaluasi->pendidikan ?: '-' }}</div></div>
                <div class="info-item"><div class="lbl">Umur</div><div class="val">{{ $evaluasi->umur ?: '-' }}</div></div>
                <div class="info-item"><div class="lbl">Kegiatan / Diklat</div><div class="val">{{ $evaluasi->nama_kegiatan ?: '-' }}</div></div>
                <div class="info-item"><div class="lbl">IKM Responden</div><div class="val"><span class="nilai-pill">{{ $evaluasi->nilai_ikm !== null ? number_format($evaluasi->nilai_ikm,2) : '-' }}</span></div></div>
                <div class="info-item border-0"><div class="lbl">Tanggal Mengisi</div><div class="val">{{ optional($evaluasi->created_at)->format('d M Y H:i') }}</div></div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card-soft">
                <h5 class="mb-3"><i class="bi bi-clipboard-check me-1"></i> Penilaian Unsur</h5>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead><tr><th width="8%">Kode</th><th>Unsur</th><th class="text-center" width="22%">Nilai</th></tr></thead>
                        <tbody>
                            @foreach($evaluasi->jawaban as $j)
                                @if(optional($j->unsur)->tipe === 'rating')
                                <tr>
                                    <td class="fw-bold" style="color:var(--custom-maroon);">{{ optional($j->unsur)->kode }}</td>
                                    <td class="small">{{ optional($j->unsur)->pertanyaan }}</td>
                                    <td class="text-center"><span class="nilai-pill">{{ $j->nilai }}</span> <span class="small text-muted d-block">{{ $labels[$j->nilai] ?? '' }}</span></td>
                                </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @php $textJawaban = $evaluasi->jawaban->filter(fn($j) => optional($j->unsur)->tipe === 'text' && $j->jawaban_text); @endphp
                @if($textJawaban->count())
                    <h6 class="fw-bold mt-2 mb-2">Isian Tambahan</h6>
                    @foreach($textJawaban as $j)
                        <div class="info-item"><div class="lbl">{{ optional($j->unsur)->pertanyaan }}</div><div class="val">{{ $j->jawaban_text }}</div></div>
                    @endforeach
                @endif
            </div>

            <div class="card-soft">
                <h5 class="mb-3"><i class="bi bi-chat-left-quote me-1"></i> Kritik & Saran</h5>
                <div class="info-item"><div class="lbl">Kritik</div><div class="val">{{ $evaluasi->kritik ?: '-' }}</div></div>
                <div class="info-item border-0"><div class="lbl">Saran</div><div class="val">{{ $evaluasi->saran ?: '-' }}</div></div>
            </div>
        </div>
    </div>
</div>
@endsection
