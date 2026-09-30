@extends('layouts.app')

@section('title', 'Master Evaluasi')

@section('content')
<style>
    :root { --custom-maroon:#7c1316; --custom-maroon-light:#a3191d; --custom-maroon-subtle:#fcf0f1; --card-radius:12px; --shadow-soft:0 4px 20px rgba(0,0,0,.05); }
    .header-card { background:#fff; border-radius:var(--card-radius); border-left:5px solid var(--custom-maroon); padding:20px; margin-bottom:20px; box-shadow:0 2px 4px rgba(0,0,0,.05); }
    .btn-maroon { background:var(--custom-maroon); color:#fff; border:none; border-radius:8px; padding:8px 16px; font-weight:600; }
    .btn-maroon:hover { background:var(--custom-maroon-light); color:#fff; }
    .btn-outline-custom { border:1.5px solid var(--custom-maroon); color:var(--custom-maroon); background:#fff; border-radius:8px; padding:8px 16px; font-weight:600; }
    .table-card { background:#fff; border-radius:var(--card-radius); box-shadow:var(--shadow-soft); overflow:hidden; }
    .table thead th { background:var(--custom-maroon-subtle); color:var(--custom-maroon); font-size:.8rem; text-transform:uppercase; }
    .action-btn { width:34px; height:34px; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; border:none; text-decoration:none; }
    .btn-edit { background:#fef3c7; color:#b45309; } .btn-edit:hover{ background:#b45309; color:#fff; }
    .btn-delete { background:#fee2e2; color:#b91c1c; } .btn-delete:hover{ background:#b91c1c; color:#fff; }
    .badge-tipe { font-size:.72rem; font-weight:700; padding:3px 9px; border-radius:20px; }
    .tipe-rating { background:#dbeafe; color:#1d4ed8; } .tipe-text { background:#e0e7ff; color:#4338ca; }
    .badge-status { font-size:.72rem; font-weight:700; padding:3px 9px; border-radius:20px; }
    .st-aktif { background:#dcfce7; color:#15803d; } .st-nonaktif { background:#f1f5f9; color:#64748b; }
</style>

<div class="container-fluid py-3">
    <div class="header-card d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <h4 class="mb-1 fw-bold" style="color:var(--custom-maroon);"><i class="bi bi-sliders me-2"></i>Master Evaluasi</h4>
            <p class="mb-0 text-muted small">Kelola unsur/pertanyaan evaluasi diklat. Unsur bertipe <b>rating</b> dihitung ke dalam IKM.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.evaluasi.index') }}" class="btn-outline-custom"><i class="bi bi-clipboard2-data me-1"></i> Lihat Hasil & IKM</a>
            <a href="{{ route('admin.master_evaluasi.create') }}" class="btn-maroon"><i class="bi bi-plus-lg me-1"></i> Tambah Unsur</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    <div class="table-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="text-center" width="6%">Urutan</th>
                        <th width="8%">Kode</th>
                        <th>Pertanyaan</th>
                        <th class="text-center" width="10%">Tipe</th>
                        <th class="text-center" width="10%">Status</th>
                        <th class="text-center" width="12%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($unsur as $u)
                    <tr>
                        <td class="text-center text-muted fw-bold">{{ $u->urutan }}</td>
                        <td class="fw-bold" style="color:var(--custom-maroon);">{{ $u->kode ?: '-' }}</td>
                        <td>{{ $u->pertanyaan }}</td>
                        <td class="text-center"><span class="badge-tipe {{ $u->tipe === 'rating' ? 'tipe-rating' : 'tipe-text' }}">{{ $u->tipe === 'rating' ? 'Rating 1-4' : 'Isian' }}</span></td>
                        <td class="text-center"><span class="badge-status {{ $u->aktif ? 'st-aktif' : 'st-nonaktif' }}">{{ $u->aktif ? 'Aktif' : 'Nonaktif' }}</span></td>
                        <td class="text-center">
                            <a href="{{ route('admin.master_evaluasi.edit', $u->id) }}" class="action-btn btn-edit" title="Edit"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('admin.master_evaluasi.destroy', $u->id) }}" method="POST" class="d-inline delete-form">
                                @csrf @method('DELETE')
                                <button type="button" class="action-btn btn-delete btn-submit-delete" title="Hapus"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-5"><i class="bi bi-inbox fs-3 d-block mb-2"></i>Belum ada unsur evaluasi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
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
                title: 'Hapus unsur ini?',
                text: 'Menghapus unsur juga menghapus jawaban terkait pada tanggapan lama.',
                icon: 'warning', showCancelButton: true,
                confirmButtonColor: '#7c1316', cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, hapus', cancelButtonText: 'Batal'
            }).then(r => { if (r.isConfirmed) form.submit(); });
        });
    });
</script>
@endsection
