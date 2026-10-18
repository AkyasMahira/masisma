@extends('layouts.app')

@section('title', 'Master Evaluasi')

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
    .t-x thead th { background:var(--maroon-subtle); color:var(--maroon); font-size:.7rem; text-transform:uppercase; letter-spacing:.4px; border:none; padding:10px 12px; }
    .t-x td { padding:10px 12px; vertical-align:middle; border-color:#f1f3f5; font-size:.88rem; }
    .badge-x { font-size:.68rem; font-weight:700; padding:3px 9px; border-radius:20px; }
    .tipe-rating { background:#dbeafe; color:#1d4ed8; } .tipe-text { background:#e0e7ff; color:#4338ca; }
    .st-aktif { background:#dcfce7; color:#15803d; } .st-nonaktif { background:#f1f5f9; color:#64748b; }
    .ic-btn { width:30px; height:30px; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; border:1px solid #e5e7eb; background:#fff; color:#64748b; text-decoration:none; }
    .ic-btn:hover { background:var(--maroon-subtle); color:var(--maroon); border-color:#ecd3d5; }
    .scope { border-left:4px solid var(--maroon); }
    .scope-k { font-size:.66rem; font-weight:800; letter-spacing:.4px; text-transform:uppercase; color:#94a3b8; }
    .filter-sel { border-radius:10px; border:1.5px solid #e2e8f0; font-size:.84rem; }
    .link-box { background:#f8fafc; border:1px dashed #d6dbe1; border-radius:10px; padding:8px 10px; font-size:.78rem; word-break:break-all; }
</style>

<div class="container-fluid py-3">
    <div class="bk-head d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <h4><i class="bi bi-sliders me-2"></i>Master Evaluasi</h4>
            <p>Pertanyaan evaluasi per jenis layanan. Pelatihan memakai pertanyaan default, kecuali pelatihan yang punya pertanyaan khusus.</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.evaluasi.index', array_filter(['jenis' => $jenis, 'kegiatan_id' => optional($kegiatan)->id])) }}" class="btn-ghost-h"><i class="bi bi-clipboard2-data me-1"></i> Hasil &amp; IKM</a>
            <a href="{{ route('admin.master_evaluasi.create', array_filter(['jenis' => $jenis, 'kegiatan_id' => optional($kegiatan)->id])) }}" class="btn-light-h"><i class="bi bi-plus-lg me-1"></i> Tambah Pertanyaan</a>
        </div>
    </div>

    <div class="tabs-j">
        @foreach(\App\Models\MasterEvaluasi::JENIS as $k => $lbl)
            <a href="{{ route('admin.master_evaluasi.index', ['jenis' => $k]) }}" class="{{ $jenis === $k ? 'on' : '' }}"><i class="bi {{ ['pelatihan' => 'bi-easel2', 'pendidikan' => 'bi-mortarboard', 'penelitian' => 'bi-journal-text'][$k] }}"></i> {{ $lbl }}</a>
        @endforeach
    </div>

    @if(session('success'))<div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>@endif

    @if($jenis === 'pelatihan')
    <div class="card-x p-3 mb-3 scope">
        <div class="row g-3 align-items-end">
            <div class="col-lg-6">
                <div class="scope-k mb-1">Pertanyaan untuk</div>
                <form method="GET">
                    <input type="hidden" name="jenis" value="pelatihan">
                    <select name="kegiatan_id" class="form-select filter-sel" onchange="this.form.submit()">
                        <option value="">Default — semua pelatihan (master)</option>
                        @foreach($daftarKegiatan as $k)
                            <option value="{{ $k->id }}" {{ optional($kegiatan)->id === $k->id ? 'selected' : '' }}>
                                {{ \Illuminate\Support\Str::limit($k->nama_kegiatan, 80) }}{{ $k->tanggal_mulai ? ' (' . \Carbon\Carbon::parse($k->tanggal_mulai)->format('d/m/Y') . ')' : '' }}{{ isset($kegiatanKhusus[$k->id]) ? ' — khusus' : '' }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
            <div class="col-lg-6">
                @if($kegiatan)
                    <div class="scope-k mb-1">Link form evaluasi pelatihan ini</div>
                    <div class="d-flex gap-2 align-items-center">
                        <div class="link-box flex-fill" id="linkForm">{{ $linkForm }}</div>
                        <button type="button" class="ic-btn" title="Salin" onclick="salinLink('{{ $linkForm }}', this)"><i class="bi bi-clipboard"></i></button>
                        <a href="{{ $linkForm }}" target="_blank" class="ic-btn" title="Buka"><i class="bi bi-box-arrow-up-right"></i></a>
                    </div>
                @else
                    <div class="small text-muted"><i class="bi bi-info-circle me-1"></i>Pilih pelatihan untuk membuat pertanyaan khusus dan mengambil link form evaluasinya. Pelatihan tanpa pertanyaan khusus otomatis memakai pertanyaan default di bawah.</div>
                @endif
            </div>
        </div>
        @if($kegiatan)
            <div class="mt-3 d-flex flex-wrap gap-2 align-items-center">
                @if($unsur->where('aktif', true)->count())
                    <span class="badge-x st-aktif"><i class="bi bi-check2 me-1"></i>Memakai pertanyaan khusus</span>
                    <form method="POST" action="{{ route('admin.master_evaluasi.pakai_default', $kegiatan->id) }}" onsubmit="return confirm('Kembalikan pelatihan ini ke pertanyaan default?');">
                        @csrf<button class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-counterclockwise me-1"></i>Kembali ke default</button>
                    </form>
                @else
                    <span class="badge-x st-nonaktif">Saat ini memakai pertanyaan default</span>
                    <form method="POST" action="{{ route('admin.master_evaluasi.salin_default', $kegiatan->id) }}">
                        @csrf<button class="btn btn-sm text-white fw-semibold" style="background:var(--maroon);"><i class="bi bi-files me-1"></i>Buat pertanyaan khusus (salin dari default)</button>
                    </form>
                @endif
            </div>
        @endif
    </div>
    @endif

    <div class="card-x">
        <div class="table-responsive">
            <table class="table t-x table-hover mb-0">
                <thead>
                    <tr>
                        <th class="text-center" width="7%">Urutan</th>
                        <th width="8%">Kode</th>
                        <th>Pertanyaan</th>
                        <th class="text-center" width="11%">Tipe</th>
                        <th class="text-center" width="10%">Dijawab</th>
                        <th class="text-center" width="10%">Status</th>
                        <th class="text-end" width="10%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($unsur as $u)
                    <tr class="{{ $u->aktif ? '' : 'text-muted' }}">
                        <td class="text-center fw-bold text-muted">{{ $u->urutan }}</td>
                        <td class="fw-bold" style="color:var(--maroon);">{{ $u->kode ?: '-' }}</td>
                        <td>{{ $u->pertanyaan }}</td>
                        <td class="text-center"><span class="badge-x {{ $u->tipe === 'rating' ? 'tipe-rating' : 'tipe-text' }}">{{ $u->tipe === 'rating' ? 'Rating 1-4' : 'Isian' }}</span></td>
                        <td class="text-center small">{{ $u->jawaban_count }}×</td>
                        <td class="text-center"><span class="badge-x {{ $u->aktif ? 'st-aktif' : 'st-nonaktif' }}">{{ $u->aktif ? 'Aktif' : 'Nonaktif' }}</span></td>
                        <td class="text-end text-nowrap">
                            <a href="{{ route('admin.master_evaluasi.edit', $u->id) }}" class="ic-btn" title="Edit"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('admin.master_evaluasi.destroy', $u->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ $u->jawaban_count ? 'Unsur ini sudah dijawab, jadi akan dinonaktifkan (tidak dihapus). Lanjutkan?' : 'Hapus unsur ini?' }}');">
                                @csrf @method('DELETE')
                                <button class="ic-btn" title="{{ $u->jawaban_count ? 'Nonaktifkan' : 'Hapus' }}"><i class="bi {{ $u->jawaban_count ? 'bi-eye-slash' : 'bi-trash' }}"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-5"><i class="bi bi-inbox fs-3 d-block mb-2"></i>
                        @if($kegiatan) Pelatihan ini belum punya pertanyaan khusus — memakai pertanyaan default. @else Belum ada pertanyaan. @endif
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-3 py-2 small text-muted border-top"><i class="bi bi-info-circle me-1"></i>Hanya pertanyaan bertipe <b>rating</b> yang dihitung ke IKM. Pertanyaan yang sudah dijawab tidak bisa dihapus (hanya dinonaktifkan) agar IKM lama tetap utuh.</div>
    </div>
</div>

<script>
    function salinLink(url, btn) {
        var done = function () { var i = btn.querySelector('i'); var c = i.className; i.className = 'bi bi-check2'; setTimeout(function () { i.className = c; }, 1500); };
        if (navigator.clipboard && window.isSecureContext) { navigator.clipboard.writeText(url).then(done, function () { prompt('Salin link:', url); }); }
        else { prompt('Salin link:', url); }
    }
</script>
@endsection
