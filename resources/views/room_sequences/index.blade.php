@extends('layouts.app') 

@section('title', 'Jadwal Rolling')
@section('page-title', 'Pengaturan Jadwal Rolling')

@section('content')
    <style>
        :root { --custom-maroon: #7c1316; --custom-maroon-light: #a3191d; --text-dark: #2c3e50; }
        
        /* Layout & Card */
        .page-header-wrapper { background: #fff; border-radius: 16px; padding: 1.5rem; border-left: 5px solid var(--custom-maroon); box-shadow: 0 4px 20px rgba(0,0,0,0.05); margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; }
        .custom-table-card { background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
        
        /* Table */
        .table thead th { background: var(--custom-maroon); color: white; padding: 1rem; border: none; vertical-align: middle; }
        .table tbody td { padding: 1rem; vertical-align: middle; }
        
        /* Buttons */
        .btn-maroon { background: var(--custom-maroon); color: white; border-radius: 8px; padding: 0.6rem 1.5rem; border:none; text-decoration: none; }
        .btn-maroon:hover { background: var(--custom-maroon-light); color: white; }
        
        /* Status Banners */
        .locked-banner { background: #d1fae5; border: 1px solid #10b981; color: #065f46; border-radius: 12px; padding: 15px; margin-bottom: 20px; display: flex; align-items: center; gap: 15px; }
        .warning-banner { background: #fffbeb; border: 1px solid #f59e0b; color: #92400e; border-radius: 12px; padding: 15px; margin-bottom: 20px; display: flex; align-items: center; gap: 15px; }
        .progress { height: 10px; border-radius: 5px; background: #e5e7eb; width: 100%; max-width: 200px; }

        /* Search Box Style */
        .search-group .form-control { border-radius: 8px 0 0 8px; border: 1px solid #e5e7eb; }
        .search-group .btn { border-radius: 0 8px 8px 0; }
    </style>

    {{-- HEADER --}}
    <div class="page-header-wrapper animate-up">
        <div class="d-flex align-items-center gap-4 flex-wrap">
            {{-- JUDUL --}}
            <div>
                <h4 class="fw-bold mb-1" style="color: var(--custom-maroon);">Rencana Jadwal Ruangan</h4>
                <small class="text-muted">Atur alur perpindahan ruangan magang Anda.</small>
            </div>

            {{-- [BARU] SEARCH FORM (KHUSUS ADMIN) --}}
            @if(auth()->user()->role === 'admin')
                <form action="{{ route('room_sequences.index') }}" method="GET" class="d-flex align-items-center gap-2">
                    <div class="input-group search-group shadow-sm" style="min-width: 250px;">
                        <input type="text" name="search" class="form-control" placeholder="Cari Mahasiswa..." value="{{ request('search') }}">
                        <button class="btn btn-maroon" type="submit"><i class="bi bi-search"></i></button>
                    </div>
                    
                    {{-- Tombol Reset jika sedang mencari --}}
                    @if(request('search'))
                        <a href="{{ route('room_sequences.index') }}" class="btn btn-sm btn-outline-danger rounded-circle" title="Reset Filter">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    @endif
                </form>
            @endif
        </div>
        
        {{-- TOMBOL TAMBAH (HILANG JIKA TERKUNCI) --}}
        @if(isset($statusKelengkapan) && !$statusKelengkapan['is_locked'])
            <div>
                <a href="{{ route('room_sequences.create') }}" class="btn btn-maroon shadow-sm">
                    <i class="bi bi-plus-lg"></i> Tambah Jadwal
                </a>
            </div>
        @endif
    </div>

    {{-- BANNER STATUS KELENGKAPAN (Hanya untuk Mahasiswa) --}}
    @if(auth()->user()->role !== 'admin' && isset($statusKelengkapan))
        <div class="animate-up">
            @if($statusKelengkapan['is_locked'])
                <div class="locked-banner shadow-sm">
                    <i class="bi bi-check-circle-fill fs-3 text-success"></i>
                    <div class="flex-grow-1">
                        <h6 class="fw-bold mb-0">Jadwal Lengkap & Terkunci!</h6>
                        <small>{{ $statusKelengkapan['message'] }}</small>
                    </div>
                    <span class="badge bg-success rounded-pill px-3 py-2">100% Terisi</span>
                </div>
            @else
                <div class="warning-banner shadow-sm">
                    <div class="spinner-grow text-warning spinner-grow-sm" role="status"></div>
                    <div class="flex-grow-1">
                        <h6 class="fw-bold mb-0">Jadwal Belum Lengkap!</h6>
                        <small>{{ $statusKelengkapan['message'] }} (Harap isi sesuai periode magang Anda)</small>
                    </div>
                    <div class="d-flex flex-column align-items-end">
                        <span class="fw-bold small mb-1">{{ $statusKelengkapan['percent'] }}%</span>
                        <div class="progress">
                            <div class="progress-bar bg-warning" style="width: {{ $statusKelengkapan['percent'] }}%"></div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endif

    {{-- ALERT SYSTEM --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i> {{ session('success') }} <button class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-exclamation-triangle me-2"></i> {{ session('error') }} <button class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    {{-- TABEL DATA --}}
    <div class="custom-table-card p-0 animate-up">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th class="text-center" width="5%">No</th>
                        @if(auth()->user()->role === 'admin') <th>Mahasiswa</th> @endif
                        <th>Ruangan</th>
                        <th>Periode</th>
                        <th class="text-center">Durasi</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sequences as $seq)
                    <tr>
                        <td class="text-center fw-bold text-muted">{{ $loop->iteration }}</td>
                        
                        @if(auth()->user()->role === 'admin')
                            <td><span class="fw-bold">{{ $seq->mahasiswa->nm_mahasiswa ?? '-' }}</span></td>
                        @endif

                        <td>
                            <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary px-3 py-2">
                                <i class="bi bi-door-open me-1"></i> {{ $seq->ruangan->nm_ruangan ?? '-' }}
                            </span>
                            @if(isset($seq->ruangan->kategori) && $seq->ruangan->kategori === 'shift')
                                <span class="badge rounded-pill bg-warning text-dark ms-1"><i class="bi bi-clock"></i> Shift</span>
                            @endif
                        </td>

                        <td>
                            <div class="fw-bold">{{ \Carbon\Carbon::parse($seq->start_date)->format('d M Y') }}</div>
                            <small class="text-muted">s/d {{ \Carbon\Carbon::parse($seq->end_date)->format('d M Y') }}</small>
                        </td>

                        <td class="text-center">
                            <span class="badge bg-info bg-opacity-10 text-info">
                                {{ \Carbon\Carbon::parse($seq->start_date)->diffInDays(\Carbon\Carbon::parse($seq->end_date)) + 1 }} Hari
                            </span>
                        </td>

                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2 align-items-center">
                                
                                {{-- LOGIKA TOMBOL SHIFT --}}
                                @if(isset($seq->ruangan->kategori) && $seq->ruangan->kategori === 'shift')
                                    @php
                                        // Cek apakah sudah locked (penuh) secara global
                                        // Jika locked, tombol jadi "Detail" (Info/Read Only)
                                        // Jika belum, tombol jadi "Isi Shift" (Primary)
                                        $isGlobalLocked = isset($statusKelengkapan) && $statusKelengkapan['is_locked'];
                                        // Untuk Admin, selalu "Isi Shift" agar bisa edit
                                        if(auth()->user()->role === 'admin') $isGlobalLocked = false;
                                    @endphp

                                    @if($isGlobalLocked)
                                        <a href="{{ route('shift_schedule.manage', $seq->id) }}" 
                                           class="btn btn-sm btn-info text-white rounded-pill px-3 fw-bold shadow-sm" 
                                           title="Lihat Detail Shift (Read Only)">
                                            <i class="bi bi-eye"></i> Detail Shift
                                        </a>
                                    @else
                                        <a href="{{ route('shift_schedule.manage', $seq->id) }}" 
                                           class="btn btn-sm btn-primary rounded-pill px-3 fw-bold shadow-sm" 
                                           title="Isi/Edit Shift Harian">
                                            <i class="bi bi-pencil-square"></i> Isi Shift
                                        </a>
                                    @endif
                                @endif

                                {{-- BUTTON EDIT/DELETE (HILANG JIKA TERKUNCI bagi Mahasiswa) --}}
                                @php
                                    $isLocked = isset($statusKelengkapan) && $statusKelengkapan['is_locked'];
                                    $isAdmin = auth()->user()->role === 'admin';
                                    $canModify = $isAdmin || !$isLocked;
                                @endphp

                                @if($canModify)
                                    <a href="{{ route('room_sequences.edit', $seq->id) }}" class="btn btn-sm btn-warning text-white rounded-circle shadow-sm" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('room_sequences.destroy', $seq->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus jadwal ini?');">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger rounded-circle shadow-sm" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @else
                                    <span class="text-muted small fst-italic ms-2"><i class="bi bi-lock-fill"></i> Locked</span>
                                @endif

                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i class="bi bi-calendar-x display-1 text-muted opacity-25"></i>
                            <p class="text-muted mt-2">Belum ada rencana jadwal.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="mt-4 d-flex justify-content-center">
        {{-- Append query string agar search tidak hilang saat pindah page --}}
        {{ $sequences->withQueryString()->links() }}
    </div>
@endsection