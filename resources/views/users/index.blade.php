@extends('layouts.app')

@section('content')
<!-- Choices.js CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />

<style>
    :root {
        --maroon: #7c1316;
        --maroon-dark: #5a0e10;
        --maroon-light: rgba(124, 19, 22, 0.05);
        --white: #ffffff;
    }

    /* --- Global & Layout --- */
    .content-wrapper { padding: 1.5rem; animation: fadeIn 0.5s ease; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

    /* --- Header Section --- */
    .header-card {
        background: var(--white);
        border-radius: 15px;
        border-left: 7px solid var(--maroon);
        padding: 25px;
        margin-bottom: 25px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .data-badge {
        background: var(--maroon-light);
        color: var(--maroon);
        padding: 8px 16px;
        border-radius: 50px;
        font-weight: 800;
        font-size: 0.85rem;
    }

    /* --- Table & Card --- */
    .main-card {
        border: none;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.03);
        background: var(--white);
        overflow: hidden;
    }
    
    .table thead th {
        background-color: #fafafa;
        color: #8a8a8a;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 11px;
        letter-spacing: 1px;
        padding: 18px 25px;
        border-bottom: 1px solid #f0f0f0;
    }

    .table tbody td { padding: 18px 25px; border-bottom: 1px solid #f8f8f8; }
    .table-hover tbody tr:hover { background-color: #fff9f9; transition: 0.2s; }

    .avatar-box {
        width: 42px; height: 42px;
        background: var(--maroon);
        color: var(--white);
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: 1.1rem;
        box-shadow: 0 4px 8px rgba(124, 19, 22, 0.2);
    }

    /* --- Choices.js Premium Styling --- */
    .choices__inner {
        border-radius: 10px !important;
        border: 1px solid #e0e0e0 !important;
        background-color: var(--white) !important;
        padding: 6px 15px !important;
        font-size: 0.9rem;
    }

    .choices__list--dropdown .choices__item--selectable.is-highlighted {
        background-color: var(--white) !important;
        color: var(--maroon) !important;
        font-weight: 700;
    }

    /* --- Form Controls --- */
    .form-label-custom { font-size: 0.75rem; font-weight: 700; color: #999; text-transform: uppercase; margin-bottom: 7px; display: block; }
    .input-custom { border-radius: 10px; padding: 10px 15px; border: 1px solid #e0e0e0; }
    .input-custom:focus { border-color: var(--maroon); box-shadow: 0 0 0 0.25rem var(--maroon-light); }

    .btn-maroon {
        background: var(--maroon);
        color: var(--white);
        border: none;
        padding: 11px 25px;
        border-radius: 10px;
        font-weight: 700;
        transition: 0.3s;
    }
    .btn-maroon:hover { background: var(--maroon-dark); color: var(--white); transform: translateY(-2px); box-shadow: 0 5px 12px rgba(124, 19, 22, 0.2); }

    /* --- Pagination Styling --- */
    .pagination-container { padding: 25px; display: flex; justify-content: space-between; align-items: center; background: #fafafa; }
    .custom-pagination { display: flex; list-style: none; gap: 7px; margin: 0; padding: 0; }
    .page-link-m {
        padding: 8px 16px;
        border-radius: 8px;
        background: var(--white);
        color: var(--maroon);
        text-decoration: none;
        font-weight: 700;
        border: 1px solid #eee;
        transition: 0.2s;
    }
    .page-link-m:hover { background: var(--maroon-light); }
    .page-item-m.active .page-link-m { background: var(--maroon); color: var(--white); border-color: var(--maroon); }
</style>

<div class="content-wrapper">
    {{-- Header Section --}}
    <div class="header-card">
        <div>
            <h3 class="fw-bold text-dark mb-1">Manajemen User</h3>
            <p class="text-muted mb-0 small">Kelola data akses mahasiswa dan unit kerja secara real-time.</p>
        </div>
        <div class="text-end">
            <span class="data-badge">
                <i class="bi bi-person-check-fill me-2"></i>Loaded: {{ $allUsers->total() }} Data
            </span>
        </div>
    </div>

    {{-- Main Filter & Table Card --}}
    <div class="main-card">
        <div class="p-4 border-bottom">
            <form action="{{ url()->current() }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label-custom">Pencarian Global</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control input-custom border-start-0 ps-0" name="search" value="{{ request('search') }}" placeholder="Nama / Email / Prodi...">
                    </div>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label-custom">Filter Instansi</label>
                    <select name="mou_id" class="choice-select">
                        <option value="">Semua Instansi / MOU</option>
                        @foreach($instansis as $ins)
                            <option value="{{ $ins->id }}" {{ request('mou_id') == $ins->id ? 'selected' : '' }}>
                                {{ $ins->nama_universitas ?? $ins->nama_instansi }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label-custom">Hak Akses</label>
                    <select name="role" class="choice-select">
                        <option value="">Semua Role</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin Sistem</option>
                        <option value="ruangan" {{ request('role') == 'ruangan' ? 'selected' : '' }}>Kepala Ruangan</option>
                        <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>Mahasiswa</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label-custom">Urutkan</label>
                    <select name="sort" class="choice-select">
                        <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Tgl Terdaftar</option>
                        <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Nama (A-Z)</option>
                    </select>
                </div>

                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-maroon w-100">
                        <i class="bi bi-filter-right me-1"></i>Terapkan
                    </button>
                    <a href="{{ url()->current() }}" class="btn btn-light border p-2" title="Reset Filter">
                        <i class="bi bi-arrow-counterclockwise fs-5 text-muted"></i>
                    </a>
                </div>
            </form>
        </div>

        {{-- Alerts --}}
        @if(session('success'))
            <div class="mx-4 mt-3 alert alert-success border-0 shadow-sm rounded-3">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mx-4 mt-3 alert alert-danger border-0 shadow-sm rounded-3">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="text-center" width="70">No</th>
                        <th>Informasi Profil</th>
                        <th>Unit / Instansi Asal</th>
                        <th class="text-center">Role</th>
                        <th class="text-end" width="120">Opsi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($allUsers as $user)
                    <tr>
                        <td class="text-center text-muted fw-bold">
                            {{ $loop->iteration + ($allUsers->currentPage() - 1) * $allUsers->perPage() }}
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar-box">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                                <div>
                                    <div class="fw-bold text-dark mb-0">{{ $user->name }}</div>
                                    <small class="text-muted"><i class="bi bi-envelope me-1"></i>{{ $user->email }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="fw-bold text-dark small mb-1">{{ $user->mou ? ($user->mou->nama_universitas ?? $user->mou->nama_instansi) : 'Internal RS' }}</div>
                            <span class="badge bg-light text-dark border-0 fw-bold" style="font-size: 9px; letter-spacing: 0.5px;">
                                {{ strtoupper($user->program_studi ?? 'Official') }}
                            </span>
                        </td>
                        <td class="text-center">
                            @if($user->role == 'admin')
                                <span class="badge bg-dark rounded-pill px-3 py-2">Administrator</span>
                            @elseif($user->role == 'ruangan')
                                <span class="badge bg-info text-white rounded-pill px-3 py-2">Ka. Ruangan</span>
                            @else
                                <span class="badge bg-light text-dark border rounded-pill px-3 py-2">Mahasiswa</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-2">
                                {{-- Tombol Edit --}}
                                <button type="button" class="btn btn-sm btn-light text-primary border" 
                                    onclick="openEditModal({{ json_encode($user) }})">
                                    <i class="bi bi-pencil-square"></i>
                                </button>

                                {{-- Tombol Delete --}}
                                <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light text-danger border" onclick="return confirm('Hapus user {{ $user->name }} secara permanen?')">
                                        <i class="bi bi-trash3-fill"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="bi bi-database-exclamation display-4 mb-3 d-block opacity-25"></i>
                            <p class="mb-0">Tidak ada data user yang sesuai dengan filter pencarian.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="pagination-container">
            <div class="small text-muted fw-bold">
                Data <span class="text-maroon">{{ $allUsers->firstItem() ?? 0 }}-{{ $allUsers->lastItem() ?? 0 }}</span> dari total <span class="text-maroon">{{ $allUsers->total() }}</span> entri
            </div>
            <nav>
                <ul class="custom-pagination">
                    <li class="page-item-m {{ $allUsers->onFirstPage() ? 'disabled' : '' }}">
                        <a class="page-link-m" href="{{ $allUsers->previousPageUrl() }}"><i class="bi bi-chevron-left"></i></a>
                    </li>
                    @foreach ($allUsers->getUrlRange(max(1, $allUsers->currentPage() - 2), min($allUsers->lastPage(), $allUsers->currentPage() + 2)) as $page => $url)
                        <li class="page-item-m {{ $page == $allUsers->currentPage() ? 'active' : '' }}">
                            <a class="page-link-m" href="{{ $url }}">{{ $page }}</a>
                        </li>
                    @endforeach
                    <li class="page-item-m {{ $allUsers->hasMorePages() ? '' : 'disabled' }}">
                        <a class="page-link-m" href="{{ $allUsers->nextPageUrl() }}"><i class="bi bi-chevron-right"></i></a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>

{{-- MODAL EDIT --}}
<div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <form id="formEdit" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header border-0 p-4 pb-0">
                    <h5 class="fw-bold text-maroon"><i class="bi bi-pencil-square me-2"></i>Edit Informasi User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Lengkap</label>
                        <input type="text" name="name" id="edit_name" class="form-control rounded-3" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Email</label>
                        <input type="email" name="email" id="edit_email" class="form-control rounded-3" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Hak Akses (Role)</label>
                        <select name="role" id="edit_role" class="form-select rounded-3">
                            <option value="admin">Admin Sistem</option>
                            <option value="ruangan">Kepala Ruangan</option>
                            <option value="user">Mahasiswa</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Program Studi</label>
                        <input type="text" name="program_studi" id="edit_prodi" class="form-control rounded-3">
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-maroon rounded-pill px-4">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Choices.js JS -->
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const elements = document.querySelectorAll('.choice-select');
        elements.forEach(el => {
            new Choices(el, {
                searchEnabled: true,
                itemSelectText: '',
                shouldSort: false,
                allowHTML: true,
            });
        });
    });

    function openEditModal(user) {
        const modal = new bootstrap.Modal(document.getElementById('modalEdit'));
        
        // Update Action URL (Sesuaikan dengan route name kamu)
        document.getElementById('formEdit').action = `/users/${user.id}`;
        
        // Isi Data ke Form
        document.getElementById('edit_name').value = user.name;
        document.getElementById('edit_email').value = user.email;
        document.getElementById('edit_role').value = user.role;
        document.getElementById('edit_prodi').value = user.program_studi || '';
        
        modal.show();
    }
</script>
@endsection