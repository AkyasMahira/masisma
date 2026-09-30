@extends('layouts.app')

@section('content')
<style>
    .filter-card {
        border: none;
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08) !important;
    }
    .input-group-text {
        background-color: #f8f9fa;
        border-right: none;
        color: var(--custom-maroon);
    }
    .filter-card .form-control, .filter-card .form-select {
        border-left: none;
        background-color: #f8f9fa;
    }
    .filter-card .form-control:focus, .filter-card .form-select:focus {
        background-color: #fff;
        box-shadow: none;
        border-color: #dee2e6;
    }
    .label-icon {
        font-size: 0.85rem;
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    :root {
        --custom-maroon: #7c1316;
        --card-radius: 16px;
        --shadow-soft: 0 4px 20px rgba(0, 0, 0, 0.05);
        --transition: 0.3s ease;
    }

    /* --- Filter Card --- */
    .filter-card {
        background: #fff;
        border-radius: var(--card-radius);
        padding: 1.5rem;
        box-shadow: var(--shadow-soft);
        margin-bottom: 1.5rem;
        border-top: 4px solid var(--custom-maroon);
    }

    /* --- Page Header --- */
    .page-header-wrapper {
        background: #fff;
        border-radius: var(--card-radius);
        padding: 1.5rem;
        box-shadow: var(--shadow-soft);
        margin-bottom: 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* --- Table Card --- */
    .custom-table-card {
        background: #fff;
        border-radius: var(--card-radius);
        box-shadow: var(--shadow-soft);
        overflow: hidden;
        border: none;
    }

    /* --- Action Buttons --- */
    .btn-action {
        width: 32px; height: 32px;
        border-radius: 8px;
        display: inline-flex; align-items: center; justify-content: center;
        transition: var(--transition); border: none; color: white;
        text-decoration: none; font-size: 0.9rem;
    }
    .btn-approve { background-color: #10b981; }
    .btn-reject { background-color: #ef4444; }
    .btn-delete { background-color: #94a3b8; }
    .btn-detail { background-color: #3b82f6; color: white; width: 32px; height: 32px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; }
    
    .btn-action:hover, .btn-detail:hover { 
        transform: translateY(-2px); 
        box-shadow: 0 4px 10px rgba(0,0,0,0.15); 
        color: white;
    }

    .badge-status { padding: 5px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 600; }
    
    /* Animation */
    .animate-up { animation: fadeInUp 0.5s ease forwards; opacity: 0; transform: translateY(20px); }
    @keyframes fadeInUp { to { opacity: 1; transform: translateY(0); } }
        .header-card {
        background: white;
        border-radius: 8px;
        border-left: 5px solid #7c1316;
        padding: 20px;
        margin-bottom: 25px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
</style>

<div class="header-card d-flex justify-content-between align-items-center">
    <div>
        <h4 class="fw-bold mb-0 text-dark">Data Pengajuan</h4>
        <small class="text-muted">Kelola layanan pengajuan magang dan penelitian.</small>
    </div>
</div>

<div class="filter-card animate-up" style="animation-delay: 0.05s;">
    <form method="GET" action="{{ route('admin.pengajuan.index') }}">
        <div class="row g-4">
            {{-- SEKSI 1: IDENTITAS --}}
            <div class="col-md-4">
                <label class="label-icon fw-bold text-dark">
                    <i class="bi bi-person-bounding-box text-primary"></i> Pencarian Pemohon
                </label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Cari Nama atau Email..." value="{{ request('search') }}">
                </div>
            </div>

            <div class="col-md-4">
                <label class="label-icon fw-bold text-dark">
                    <i class="bi bi-building text-info"></i> Institusi / Universitas
                </label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="bi bi-university"></i></span>
                    <select name="university_id" class="form-select">
                        <option value="">Semua Institusi</option>
                        @foreach($universities as $univ)
                            <option value="{{ $univ->id }}" {{ request('university_id') == $univ->id ? 'selected' : '' }}>
                                {{ $univ->nama_instansi ?? $univ->nama_universitas }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-4">
                <label class="label-icon fw-bold text-dark">
                    <i class="bi bi-layers text-success"></i> Kategori Data
                </label>
                <div class="d-flex gap-2">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="bi bi-tag"></i></span>
                        <select name="jenis" class="form-select">
                            <option value="">Semua Kategori</option>
                            <!-- NOTE: Label diubah jadi Pendidikan & Penelitian, tapi value tetap -->
                            <option value="magang" {{ request('jenis') == 'magang' ? 'selected' : '' }}>Pendidikan</option>
                            <option value="pra_penelitian" {{ request('jenis') == 'pra_penelitian' ? 'selected' : '' }}>Penelitian</option>
                        </select>
                    </div>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                        <select name="id_card_status" class="form-select">
                            <option value="">ID Card</option>
                            <option value="1" {{ request('id_card_status') === '1' ? 'selected' : '' }}>Sudah ACC</option>
                            <option value="0" {{ request('id_card_status') === '0' ? 'selected' : '' }}>Belum ACC</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- SEKSI 2: STATUS & TANGGAL --}}
            <div class="col-md-3">
                <label class="label-icon fw-bold text-dark">
                    <i class="bi bi-check-circle text-warning"></i> Status Approval
                </label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="bi bi-flag"></i></span>
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>⌛ Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>✅ Approved</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>❌ Rejected</option>
                        <option value="canceled" {{ request('status') == 'canceled' ? 'selected' : '' }}>🚫 Canceled</option>
                    </select>
                </div>
            </div>

            <div class="col-md-5">
                <label class="label-icon fw-bold text-dark">
                    <i class="bi bi-calendar-range text-danger"></i> Rentang Tanggal Pengajuan
                </label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text">Dari</span>
                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                    <span class="input-group-text">Hingga</span>
                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                </div>
            </div>

            {{-- TOMBOL AKSI --}}
            <div class="col-md-4 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-sm w-100 fw-bold text-white shadow-sm" style="background-color: var(--custom-maroon); height: 38px; border-radius: 8px;">
                    <i class="bi bi-filter-left"></i> Terapkan Filter
                </button>
                <a href="{{ route('admin.pengajuan.index') }}" class="btn btn-sm btn-light border w-25 d-flex align-items-center justify-content-center" style="height: 38px; border-radius: 8px;" title="Reset Filter">
                    <i class="bi bi-arrow-counterclockwise text-secondary"></i>
                </a>
            </div>
        </div>
    </form>
</div>

<div id="alertContainer">
    @if (session('success'))
        <div class="alert alert-success border-0 shadow-sm animate-up" style="border-radius: 12px;">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger border-0 shadow-sm animate-up" style="border-radius: 12px;">
            <i class="bi bi-exclamation-circle-fill me-2"></i> {{ session('error') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger border-0 shadow-sm animate-up">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>

<form id="bulkActionForm" action="{{ route('admin.pengajuan.bulk_action') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="action" id="bulkActionInput">
    <div id="bulkIdsContainer"></div> 
</form>

<div id="bulkToolbar" class="d-none bg-white p-3 rounded shadow-sm mb-3 border-start border-5 border-primary animate-up align-items-center justify-content-between">
    <div class="d-flex align-items-center gap-3">
        <div class="bg-primary bg-opacity-10 text-primary px-3 py-2 rounded fw-bold">
            <i class="bi bi-check2-square me-2"></i>
            <span id="selectedCount">0</span> Item Dipilih
        </div>
    </div>
    
    {{-- TAMBAHKAN TOMBOL-TOMBOL INI --}}
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-success btn-sm text-white fw-bold" onclick="submitBulk('approve')">
            <i class="bi bi-check-lg"></i> Setujui
        </button>
        <button type="button" class="btn btn-danger btn-sm text-white fw-bold" onclick="submitBulk('reject')">
            <i class="bi bi-x-lg"></i> Tolak
        </button>
        <button type="button" class="btn btn-outline-secondary btn-sm fw-bold" onclick="submitBulk('cancel')">
            <i class="bi bi-slash-circle"></i> Batal
        </button>
        <button type="button" class="btn btn-secondary btn-sm text-white fw-bold" onclick="submitBulk('delete')">
            <i class="bi bi-trash"></i> Hapus
        </button>
    </div>
</div>

<div class="custom-table-card animate-up" style="animation-delay: 0.1s;">
    <div class="table-responsive" id="tableContainer">
        @include('admin.pengajuan._table', ['data' => $data])
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    updateBulkToolbar();
});

function updateBulkToolbar() {
    const checkboxes = document.querySelectorAll('.select-item:checked');
    const count = checkboxes.length;
    const toolbar = document.getElementById('bulkToolbar');
    const countSpan = document.getElementById('selectedCount');
    
    if(countSpan) countSpan.innerText = count;
    
    if (toolbar) {
        if (count > 0) {
            toolbar.classList.remove('d-none');
            toolbar.classList.add('d-flex');
        } else {
            toolbar.classList.add('d-none');
            toolbar.classList.remove('d-flex');
        }
    }
}

document.addEventListener('change', function(e) {
    if (e.target.id === 'selectAll') {
        const isChecked = e.target.checked;
        document.querySelectorAll('.select-item').forEach(cb => {
            cb.checked = isChecked;
        });
        updateBulkToolbar();
    }
    
    if (e.target.classList.contains('select-item')) {
        if (!e.target.checked) {
            const selectAll = document.getElementById('selectAll');
            if(selectAll) selectAll.checked = false;
        }
        updateBulkToolbar();
    }
});

function submitBulk(actionType) {
    let confirmMsg = '';
    if (actionType === 'cancel') confirmMsg = 'Yakin ingin MEMBATALKAN pengajuan yang dipilih?';
    else if (actionType === 'approve_id_card') confirmMsg = 'Yakin ingin menyetujui ID CARD untuk data yang dipilih?';
    else if (actionType === 'approve') confirmMsg = 'Yakin ingin MENYETUJUI pengajuan yang dipilih?';
    else if (actionType === 'reject') confirmMsg = 'Yakin ingin MENOLAK pengajuan yang dipilih?';
    else if (actionType === 'delete') confirmMsg = 'Yakin ingin MENGHAPUS PERMANEN semua data yang dipilih?';

    if (!confirm(confirmMsg)) return;

    const form = document.getElementById('bulkActionForm');
    const container = document.getElementById('bulkIdsContainer');
    const actionInput = document.getElementById('bulkActionInput');
    
    actionInput.value = actionType;
    
    container.innerHTML = '';
    document.querySelectorAll('.select-item:checked').forEach(cb => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'ids[]';
        input.value = cb.value;
        container.appendChild(input);
    });

    form.submit();
}
</script>
@endsection