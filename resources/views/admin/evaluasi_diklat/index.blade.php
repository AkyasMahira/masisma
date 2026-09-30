@extends('layouts.app')

@section('title', 'Evaluasi Diklat & IKM')

@section('content')
<style>
    :root {
        --custom-maroon: #7c1316;
        --custom-maroon-light: #a3191d;
        --custom-maroon-subtle: #fcf0f1;
        --text-dark: #2c3e50;
        --text-muted: #64748b;
        --card-radius: 12px;
        --shadow-soft: 0 4px 20px rgba(0,0,0,.05);
    }
    .header-card { background:#fff; border-radius: var(--card-radius); border-left:5px solid var(--custom-maroon); padding:20px; margin-bottom:20px; box-shadow:0 2px 4px rgba(0,0,0,.05); }
    .btn-maroon { background: var(--custom-maroon); color:#fff; border:none; border-radius:8px; padding:8px 16px; font-weight:600; }
    .btn-maroon:hover { background: var(--custom-maroon-light); color:#fff; }
    .btn-outline-custom { border:1.5px solid var(--custom-maroon); color: var(--custom-maroon); background:#fff; border-radius:8px; padding:8px 16px; font-weight:600; }
    .btn-outline-custom:hover { background: var(--custom-maroon-subtle); }
    .ikm-hero { background: linear-gradient(135deg, var(--custom-maroon), #5f0f12); color:#fff; border-radius: var(--card-radius); padding:24px; box-shadow: var(--shadow-soft); }
    .ikm-score { font-size: 3rem; font-weight: 800; line-height:1; }
    .ikm-grade { display:inline-flex; align-items:center; justify-content:center; width:64px; height:64px; border-radius:50%; background:rgba(255,255,255,.18); font-size:2rem; font-weight:800; }
    .stat-card { background:#fff; border-radius: var(--card-radius); box-shadow: var(--shadow-soft); padding:18px; height:100%; }
    .stat-card .lbl { color: var(--text-muted); font-size:.8rem; font-weight:600; text-transform:uppercase; }
    .stat-card .val { font-size:1.6rem; font-weight:800; color: var(--text-dark); }
    .table-card { background:#fff; border-radius: var(--card-radius); box-shadow: var(--shadow-soft); overflow:hidden; }
    .table thead th { background: var(--custom-maroon-subtle); color: var(--custom-maroon); font-size:.8rem; text-transform:uppercase; }
    .action-btn { width:34px; height:34px; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; border:none; }
    .btn-view { background:#e0f2fe; color:#0369a1; } .btn-view:hover{ background:#0369a1; color:#fff; }
    .btn-delete { background:#fee2e2; color:#b91c1c; } .btn-delete:hover{ background:#b91c1c; color:#fff; }
    .badge-mutu { font-weight:700; padding:4px 10px; border-radius:20px; font-size:.85rem; }
</style>

<div class="container-fluid py-3">
    <div class="header-card d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <h4 class="mb-1 fw-bold" style="color:var(--custom-maroon);"><i class="bi bi-clipboard2-data me-2"></i>Evaluasi Diklat & IKM</h4>
            <p class="mb-0 text-muted small">Indeks Kepuasan Masyarakat (Permenpan RB No. 14 Tahun 2017) dari tanggapan publik.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.master_evaluasi.index') }}" class="btn-outline-custom"><i class="bi bi-sliders me-1"></i> Master Evaluasi</a>
            <a href="{{ route('admin.evaluasi.export.csv') }}" class="btn-outline-custom"><i class="bi bi-filetype-csv me-1"></i> Unduh CSV</a>
            <a href="{{ route('admin.evaluasi.export.pdf') }}" class="btn-maroon"><i class="bi bi-file-earmark-pdf me-1"></i> Unduh Laporan IKM (PDF)</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    {{-- Ringkasan IKM --}}
    <div class="row g-3 mb-3">
        <div class="col-lg-5">
            <div class="ikm-hero d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-uppercase small fw-semibold" style="opacity:.85;">Nilai IKM Keseluruhan</div>
                    <div class="ikm-score">{{ number_format($ikm['nilai'], 2) }}</div>
                    <div class="mt-1" style="opacity:.9;">{{ $ikm['kategori'] }}</div>
                </div>
                <div class="text-center">
                    <div class="ikm-grade">{{ $ikm['mutu'] }}</div>
                    <div class="small mt-2" style="opacity:.85;">Mutu</div>
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="row g-3 h-100">
                <div class="col-sm-6"><div class="stat-card"><div class="lbl">Total Responden</div><div class="val">{{ $ikm['total_responden'] }}</div></div></div>
                <div class="col-sm-6"><div class="stat-card"><div class="lbl">Jumlah Unsur Dinilai</div><div class="val">{{ $ikm['jumlah_unsur'] }}</div></div></div>
                <div class="col-12">
                    <div class="stat-card">
                        <div class="lbl mb-2">Nilai per Unsur (skala 0-100)</div>
                        <div class="table-responsive" style="max-height:170px; overflow:auto;">
                            <table class="table table-sm mb-0">
                                <tbody>
                                    @foreach($ikm['per_unsur'] as $u)
                                    <tr>
                                        <td class="fw-semibold" style="width:44px; color:var(--custom-maroon);">{{ $u['kode'] }}</td>
                                        <td class="small text-muted">{{ \Illuminate\Support\Str::limit($u['pertanyaan'], 55) }}</td>
                                        <td class="text-end fw-bold" style="width:70px;">{{ number_format($u['nilai'],2) }}</td>
                                        <td class="text-center" style="width:44px;"><span class="badge-mutu" style="background:var(--custom-maroon-subtle); color:var(--custom-maroon);">{{ $u['mutu'] }}</span></td>
                                    </tr>
                                    @endforeach
                                    @if(count($ikm['per_unsur']) === 0)
                                    <tr><td colspan="4" class="text-center text-muted py-3">Belum ada unsur penilaian aktif.</td></tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Daftar tanggapan --}}
    <div class="table-card">
        <div class="p-3 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h6 class="mb-0 fw-bold"><i class="bi bi-list-ul me-1"></i> Daftar Tanggapan</h6>
            <form method="GET" class="d-flex gap-2" style="max-width:320px;">
                <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm" placeholder="Cari nama / instansi / kegiatan...">
                <button class="btn btn-sm btn-maroon"><i class="bi bi-search"></i></button>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="text-center" width="5%">No</th>
                        <th>Nama</th>
                        <th>Instansi</th>
                        <th>Kegiatan</th>
                        <th class="text-center">IKM</th>
                        <th>Tanggal</th>
                        <th class="text-center" width="12%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($evaluasi as $i => $ev)
                    <tr>
                        <td class="text-center text-muted">{{ $evaluasi->firstItem() + $i }}</td>
                        <td class="fw-semibold">{{ $ev->nama ?: '(anonim)' }}</td>
                        <td>{{ $ev->instansi ?: '-' }}</td>
                        <td>{{ $ev->nama_kegiatan ?: '-' }}</td>
                        <td class="text-center fw-bold">{{ $ev->nilai_ikm !== null ? number_format($ev->nilai_ikm,2) : '-' }}</td>
                        <td class="small text-muted">{{ optional($ev->created_at)->format('d M Y H:i') }}</td>
                        <td class="text-center">
                            <a href="{{ route('admin.evaluasi.show', $ev->id) }}" class="action-btn btn-view" title="Lihat"><i class="bi bi-eye"></i></a>
                            <form action="{{ route('admin.evaluasi.destroy', $ev->id) }}" method="POST" class="d-inline delete-form">
                                @csrf @method('DELETE')
                                <button type="button" class="action-btn btn-delete btn-submit-delete" title="Hapus"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-5"><i class="bi bi-inbox fs-3 d-block mb-2"></i>Belum ada tanggapan evaluasi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $evaluasi->links() }}</div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.querySelectorAll('.btn-submit-delete').forEach(btn => {
        btn.addEventListener('click', function () {
            const form = this.closest('form');
            Swal.fire({
                title: 'Hapus tanggapan ini?',
                text: 'Data yang dihapus tidak dapat dikembalikan.',
                icon: 'warning', showCancelButton: true,
                confirmButtonColor: '#7c1316', cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, hapus', cancelButtonText: 'Batal'
            }).then(r => { if (r.isConfirmed) form.submit(); });
        });
    });
</script>
@endsection
