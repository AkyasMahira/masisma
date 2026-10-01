@extends('layouts.app')

@section('title', 'Orientasi Pegawai')

@section('content')
<style>
    :root { --maroon:#7c1316; --maroon-light:#a3191d; --maroon-subtle:#fcf0f1; --radius:14px; --shadow:0 4px 20px rgba(0,0,0,.05); }
    .hero { background:linear-gradient(135deg,#7c1316,#5f0f12); color:#fff; border-radius:20px; padding:24px 28px; margin-bottom:1.25rem; box-shadow:0 14px 34px rgba(124,19,22,.28); position:relative; overflow:hidden; }
    .hero::after { content:''; position:absolute; right:-40px; top:-40px; width:170px; height:170px; background:rgba(255,255,255,.07); border-radius:50%; }
    .hero h4 { font-weight:800; margin:0 0 4px; }
    .hero p { opacity:.9; margin:0; font-size:.9rem; }
    .stat { background:#fff; border-radius:var(--radius); box-shadow:var(--shadow); padding:18px; display:flex; align-items:center; gap:14px; }
    .stat .ic { width:48px; height:48px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:1.3rem; }
    .stat .n { font-size:1.6rem; font-weight:800; line-height:1; }
    .stat .l { color:#64748b; font-size:.76rem; font-weight:600; text-transform:uppercase; }
    .card-soft { background:#fff; border-radius:var(--radius); box-shadow:var(--shadow); overflow:hidden; }
    .table thead th { background:var(--maroon-subtle); color:var(--maroon); font-size:.76rem; text-transform:uppercase; }
    .pill { padding:3px 10px; border-radius:20px; font-size:.72rem; font-weight:700; }
    .p-belum { background:#fee2e2; color:#b91c1c; } .p-sudah { background:#dcfce7; color:#15803d; } .p-lulus { background:#dbeafe; color:#1d4ed8; }
    .btn-maroon { background:var(--maroon); color:#fff; border:none; border-radius:8px; font-weight:600; }
    .btn-maroon:hover { background:var(--maroon-light); color:#fff; }
    .row-belum { background:#fff7f7; }
</style>

<div class="container-fluid py-3">
    <div class="hero d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <h4><i class="bi bi-person-vcard me-2"></i>Orientasi Pegawai</h4>
            <p><i class="bi bi-hdd-network me-1"></i> Data pegawai diambil langsung dari sistem SDM (IT). Pegawai yang belum orientasi ditandai.</p>
        </div>
    </div>

    @if($error)<div class="alert alert-warning"><i class="bi bi-exclamation-triangle me-1"></i>{{ $error }}</div>@endif
    @if(session('success'))<div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>@endif

    <div class="row g-3 mb-3">
        <div class="col-md-4"><div class="stat"><div class="ic" style="background:var(--maroon-subtle);color:var(--maroon);"><i class="bi bi-people-fill"></i></div><div><div class="l">Total Pegawai</div><div class="n">{{ $total }}</div></div></div></div>
        <div class="col-md-4"><div class="stat"><div class="ic" style="background:#dcfce7;color:#15803d;"><i class="bi bi-check-circle-fill"></i></div><div><div class="l">Sudah Orientasi</div><div class="n text-success">{{ $sudah }}</div></div></div></div>
        <div class="col-md-4"><div class="stat"><div class="ic" style="background:#fee2e2;color:#b91c1c;"><i class="bi bi-exclamation-circle-fill"></i></div><div><div class="l">Belum Orientasi</div><div class="n text-danger">{{ $belum }}</div></div></div></div>
    </div>

    <div class="card-soft">
        <div class="p-3 border-bottom">
            <form method="GET" class="d-flex flex-wrap gap-2">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" style="max-width:260px;" placeholder="Cari nama / NIP...">
                <select name="status" class="form-select form-select-sm" style="max-width:170px;">
                    <option value="">Semua status</option>
                    <option value="belum" {{ request('status')==='belum'?'selected':'' }}>Belum orientasi</option>
                    <option value="sudah" {{ request('status')==='sudah'?'selected':'' }}>Sudah orientasi</option>
                </select>
                <button class="btn btn-sm btn-maroon px-3">Filter</button>
                @if(request('search')||request('status'))<a href="{{ route('admin.orientasi_pegawai.index') }}" class="btn btn-sm btn-light border">Reset</a>@endif
            </form>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead><tr><th width="4%">No</th><th>Nama</th><th>NIP</th><th>Unit</th><th>Kepegawaian</th><th class="text-center">Status Orientasi</th><th class="text-center" width="14%">Aksi</th></tr></thead>
                <tbody>
                    @forelse($pegawai as $i => $p)
                    <tr class="{{ $p->sudah ? '' : 'row-belum' }}">
                        <td class="text-muted">{{ $pegawai->firstItem() + $i }}</td>
                        <td class="fw-semibold">{{ $p->nama }}</td>
                        <td>{{ $p->nip }}</td>
                        <td class="small">{{ $p->unit }}</td>
                        <td class="small text-muted">{{ $p->status_pegawai }}</td>
                        <td class="text-center">
                            @if(!$p->sudah)
                                <span class="pill p-belum"><i class="bi bi-x-circle me-1"></i>BELUM</span>
                            @elseif($p->orientasi->status === 'lulus')
                                <span class="pill p-lulus"><i class="bi bi-patch-check me-1"></i>LULUS @if($p->orientasi->post_test_score !== null)({{ $p->orientasi->post_test_score }})@endif</span>
                            @else
                                <span class="pill p-sudah"><i class="bi bi-check-circle me-1"></i>SUDAH</span>
                            @endif
                            @if($p->orientasi && $p->orientasi->tahun)<div class="text-muted" style="font-size:.65rem;">Th. {{ $p->orientasi->tahun }}</div>@endif
                        </td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-maroon btn-tandai"
                                data-nip="{{ $p->nip }}" data-nama="{{ $p->nama }}" data-unit="{{ $p->unit }}" data-id="{{ $p->id }}"
                                data-status="{{ optional($p->orientasi)->status }}" data-pre="{{ optional($p->orientasi)->pre_test_score }}"
                                data-post="{{ optional($p->orientasi)->post_test_score }}" data-tahun="{{ optional($p->orientasi)->tahun }}"
                                data-bs-toggle="modal" data-bs-target="#modalTandai">
                                <i class="bi bi-pencil-square"></i> {{ $p->sudah ? 'Edit' : 'Tandai' }}
                            </button>
                            @if($p->sudah)
                                <form action="{{ route('admin.orientasi_pegawai.destroy', $p->nip) }}" method="POST" class="d-inline" onsubmit="return confirm('Kembalikan ke BELUM?');">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" title="Reset"><i class="bi bi-arrow-counterclockwise"></i></button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-5"><i class="bi bi-inbox fs-3 d-block mb-2"></i>Tidak ada data pegawai.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $pegawai->links() }}</div>
    </div>
</div>

{{-- Modal catat orientasi --}}
<div class="modal fade" id="modalTandai" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:14px;overflow:hidden;">
            <div class="modal-header text-white" style="background:var(--maroon);">
                <h6 class="modal-title fw-bold"><i class="bi bi-person-check me-1"></i> Catat Orientasi Pegawai</h6>
                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.orientasi_pegawai.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="nip" id="f_nip">
                    <input type="hidden" name="nama" id="f_nama">
                    <input type="hidden" name="unit" id="f_unit">
                    <input type="hidden" name="pegawai_id" id="f_id">
                    <div class="mb-2"><div class="small text-muted">Pegawai</div><div class="fw-bold" id="f_label">-</div></div>
                    <div class="row g-2">
                        <div class="col-6"><label class="form-label small fw-semibold">Status</label>
                            <select name="status" id="f_status" class="form-select form-select-sm">
                                <option value="sudah">Sudah Orientasi</option>
                                <option value="lulus">Lulus (dengan nilai)</option>
                            </select>
                        </div>
                        <div class="col-6"><label class="form-label small fw-semibold">Tahun</label><input type="number" name="tahun" id="f_tahun" class="form-control form-control-sm" value="{{ date('Y') }}"></div>
                        <div class="col-6"><label class="form-label small fw-semibold">Pre-Test</label><input type="number" name="pre_test_score" id="f_pre" class="form-control form-control-sm" min="0" max="100"></div>
                        <div class="col-6"><label class="form-label small fw-semibold">Post-Test</label><input type="number" name="post_test_score" id="f_post" class="form-control form-control-sm" min="0" max="100"></div>
                        <div class="col-12"><label class="form-label small fw-semibold">Keterangan</label><textarea name="keterangan" class="form-control form-control-sm" rows="2"></textarea></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-maroon">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.querySelectorAll('.btn-tandai').forEach(function (b) {
        b.addEventListener('click', function () {
            document.getElementById('f_nip').value = this.dataset.nip || '';
            document.getElementById('f_nama').value = this.dataset.nama || '';
            document.getElementById('f_unit').value = this.dataset.unit || '';
            document.getElementById('f_id').value = this.dataset.id || '';
            document.getElementById('f_label').textContent = (this.dataset.nama || '-') + ' · ' + (this.dataset.nip || '-');
            document.getElementById('f_status').value = this.dataset.status || 'sudah';
            document.getElementById('f_pre').value = this.dataset.pre || '';
            document.getElementById('f_post').value = this.dataset.post || '';
            document.getElementById('f_tahun').value = this.dataset.tahun || {{ date('Y') }};
        });
    });
</script>
@endsection
