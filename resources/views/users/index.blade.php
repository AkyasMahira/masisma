@extends('layouts.app')

@section('title', 'Manajemen User')
@section('page-title', 'Manajemen User')

@section('content')
<style>
    /* Style tambahan agar senada dengan maroon theme */
    .nav-tabs .nav-link {
        color: #6c757d;
        font-weight: 600;
        border: none;
        border-bottom: 3px solid transparent;
    }
    .nav-tabs .nav-link.active {
        color: #7c1316; /* Maroon */
        border-bottom: 3px solid #7c1316;
        background: transparent;
    }
    .nav-tabs .nav-link:hover {
        border-color: transparent;
        color: #7c1316;
    }
    .avatar-initial {
        width: 35px; height: 35px;
        background-color: #fcf0f1;
        color: #7c1316;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: bold; font-size: 0.9rem;
    }
</style>

<div class="card border-0 shadow-sm" style="border-radius: 16px;">
    <div class="card-header bg-white border-0 pt-4 px-4">
        <ul class="nav nav-tabs card-header-tabs" id="userTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab">
                    <i class="bi bi-hourglass-split me-1"></i> Menunggu Approval
                    @if($pendingUsers->count() > 0)
                        <span class="badge rounded-pill bg-danger ms-1">{{ $pendingUsers->count() }}</span>
                    @endif
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab">
                    <i class="bi bi-people-fill me-1"></i> Semua User
                </button>
            </li>
        </ul>
    </div>

    <div class="card-body p-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="tab-content" id="userTabContent">

            {{-- TAB 1: PENDING APPROVAL --}}
            <div class="tab-pane fade show active" id="pending" role="tabpanel">
                @if($pendingUsers->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-check2-all display-4 mb-3 d-block opacity-50"></i>
                        <p>Tidak ada permintaan akun baru saat ini.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="bg-light text-uppercase small text-muted">
                                <tr>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Universitas</th>
                                    <th>Tanggal Daftar</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingUsers as $user)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar-initial">{{ substr($user->name, 0, 1) }}</div>
                                            <span class="fw-bold text-dark">{{ $user->name }}</span>
                                        </div>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->mou ? ($user->mou->nama_instansi ?? $user->mou->nama_universitas) : '-' }}</td>
                                    <td>{{ $user->created_at->diffForHumans() }}</td>
                                    <td class="text-end">
                                        <form action="{{ route('users.approve', $user->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success rounded-pill px-3" onclick="return confirm('Setujui akun ini?')">
                                                <i class="bi bi-check-lg me-1"></i> Approve
                                            </button>
                                        </form>
                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="return confirm('Tolak dan hapus akun ini?')">
                                                <i class="bi bi-x-lg me-1"></i> Tolak
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- TAB 2: ALL USERS --}}
            <div class="tab-pane fade" id="all" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="bg-light text-uppercase small text-muted">
                            <tr>
                                <th>No</th>
                                <th>User Info</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($allUsers as $user)
                            <tr>
                                <td class="text-muted">{{ $loop->iteration }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar-initial bg-light text-secondary">{{ substr($user->name, 0, 1) }}</div>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $user->name }}</div>
                                            <small class="text-muted">{{ $user->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($user->role == 'admin')
                                        <span class="badge bg-dark">Admin</span>
                                    @else
                                        <span class="badge bg-light text-dark border">User</span>
                                    @endif
                                </td>
                                <td>
                                    @if($user->is_approved)
                                        <span class="badge rounded-pill bg-success bg-opacity-10 text-success">Approved</span>
                                    @else
                                        <span class="badge rounded-pill bg-warning bg-opacity-10 text-warning">Pending</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-link text-danger p-0" onclick="return confirm('Hapus user ini secara permanen?')">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $allUsers->links('pagination.custom') }}
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
