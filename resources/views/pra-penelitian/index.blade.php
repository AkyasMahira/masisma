@extends('layouts.app')

@section('title', 'Penelitian')
@section('page-title', 'Data Penelitian')

@section('content')
    <style>
        :root {
            --custom-maroon: #7c1316;
            --custom-maroon-light: #a3191d;
            --custom-maroon-subtle: #fcf0f1;
            --text-dark: #2c3e50;
            --text-muted: #95a5a6;
            --card-radius: 12px;
            --shadow-soft: 0 4px 20px rgba(0, 0, 0, 0.05);
            --transition: 0.3s ease;
        }

        .page-header-wrapper {
            background: #fff;
            border-radius: var(--card-radius);
            padding: 1.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            margin-bottom: 2rem;
            border-left: 5px solid var(--custom-maroon);
            position: relative;
            z-index: 99;
            overflow: visible;
        }

        .filter-card {
            background: #fff;
            border-radius: var(--card-radius);
            box-shadow: var(--shadow-soft);
            margin-bottom: 1.5rem;
            border: 1px solid #f0f0f0;
            position: relative;
            z-index: 50;
        }

        .filter-header {
            background: var(--custom-maroon-subtle);
            color: var(--custom-maroon);
            padding: 1rem 1.5rem;
            border-radius: var(--card-radius) var(--card-radius) 0 0;
            font-weight: 600;
        }

        .btn-maroon {
            background-color: var(--custom-maroon);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 8px 16px;
            font-weight: 500;
            transition: var(--transition);
        }

        .btn-maroon:hover {
            background-color: var(--custom-maroon-light);
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(124, 19, 22, 0.3);
        }

        .btn-tool {
            background: #fff;
            border: 1px solid #e0e0e0;
            color: var(--text-dark);
            border-radius: 8px;
            padding: 8px 16px;
            font-weight: 500;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-tool:hover,
        .btn-tool:focus {
            background: #f8f9fa;
            border-color: var(--custom-maroon);
            outline: none;
        }

        .custom-table-card {
            background: #fff;
            border-radius: var(--card-radius);
            box-shadow: var(--shadow-soft);
            overflow: hidden;
            border: none;
        }

        .table {
            margin-bottom: 0;
            border-collapse: collapse;
        }

        .table thead th {
            background-color: var(--custom-maroon);
            color: white;
            border: none;
            padding: 1rem;
            font-weight: 500;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }

        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
            color: #555;
            border-bottom: 1px solid #f0f0f0;
        }

        .table-hover tbody tr:hover {
            background-color: #fff5f6;
        }

        .action-btn {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
            transition: var(--transition);
            background: transparent;
            border: 1px solid transparent;
        }

        .action-btn:hover {
            background: var(--custom-maroon-subtle);
            color: var(--custom-maroon);
        }

        .action-btn.delete:hover {
            background: #fee2e2;
            color: #dc2626;
        }

        .action-btn.batal:hover {
            background: #e5e7eb;
            color: #374151;
        }

        .animate-up {
            animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
            opacity: 0;
            transform: translateY(20px);
        }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>

    {{-- 1. Header Halaman --}}
    <div class="page-header-wrapper d-flex flex-wrap justify-content-between align-items-center gap-3 animate-up">
        <div>
            <h4 class="fw-bold mb-1" style="color: var(--custom-maroon);">Daftar Penelitian</h4>
            <small class="text-muted">Kelola data pengajuan pra penelitian.</small>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            {{-- Tombol Notepad --}}
            <button class="btn btn-tool shadow-sm" type="button" data-bs-toggle="modal" data-bs-target="#notepadModal"
                title="Notepad">
                <i class="bi bi-stickies"></i> Catatan
            </button>

            {{-- Tombol Tambah --}}
            <a href="{{ route('pra-penelitian.create') }}" class="btn btn-maroon shadow-sm d-flex align-items-center gap-2">
                <i class="bi bi-plus-lg"></i> Tambah Pengajuan
            </a>
        </div>
    </div>

    {{-- 2. Filter Card (Diperbarui dengan Jenis Penelitian) --}}
    <div class="filter-card animate-up" style="animation-delay: 0.1s;">
        <div class="filter-header">
            <i class="bi bi-funnel-fill mr-2"></i> Filter & Pencarian
        </div>
        <div class="card-body p-4">
            <form id="filterForm" method="GET" action="{{ route('pra-penelitian.index') }}">
                <div class="row g-3">
                    {{-- Pencarian --}}
                    <div class="col-md-4">
                        <label class="small text-muted font-weight-bold text-uppercase">Cari</label>
                        <div class="input-group shadow-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light border-right-0"><i class="bi bi-search"></i></span>
                            </div>
                            <input type="text" class="form-control bg-light border-left-0" name="search"
                                placeholder="Cari..." value="{{ request('search') }}">
                        </div>
                    </div>
                    
                    {{-- Dropdown Kategori Mahasiswa --}}
<div class="col-md-2">
    <label class="small text-muted font-weight-bold text-uppercase">Kategori Peneliti</label>
    <div class="input-group shadow-sm">
        <div class="input-group-prepend">
            <span class="input-group-text bg-light border-right-0"><i class="bi bi-people"></i></span>
        </div>
        <select class="form-control bg-light border-left-0" name="jenis_mahasiswa" onchange="this.form.submit()">
            <option value="">Semua Kategori</option>
            <option value="Internal" {{ request('jenis_mahasiswa') == 'Internal' ? 'selected' : '' }}>Internal</option>
            <option value="Eksternal" {{ request('jenis_mahasiswa') == 'Eksternal' ? 'selected' : '' }}>Eksternal</option>
        </select>
    </div>
</div>

                    {{-- [BARU] Filter Jenis Penelitian --}}
                    <div class="col-md-2">
                        <label class="small text-muted font-weight-bold text-uppercase">Jenis Penelitian</label>
                        <div class="input-group shadow-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light border-right-0"><i class="bi bi-tags"></i></span>
                            </div>
                            <select class="form-control bg-light border-left-0" name="jenis_penelitian" onchange="this.form.submit()">
                                <option value="">Semua Jenis</option>
                                <option value="Data Awal" {{ request('jenis_penelitian') == 'Data Awal' ? 'selected' : '' }}>
                                    Data Awal</option>
                                <option value="Uji Validitas"
                                    {{ request('jenis_penelitian') == 'Uji Validitas' ? 'selected' : '' }}>Uji Validitas
                                </option>
                                <option value="Penelitian" {{ request('jenis_penelitian') == 'Penelitian' ? 'selected' : '' }}>
                                    Penelitian</option>
                            </select>
                        </div>
                    </div>

                    {{-- Filter Status --}}
                    <div class="col-md-2">
                        <label class="small text-muted font-weight-bold text-uppercase">Status</label>
                        <div class="input-group shadow-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light border-right-0"><i
                                        class="bi bi-check-circle"></i></span>
                            </div>
                            <select class="form-control bg-light border-left-0" name="status" onchange="this.form.submit()">
                                <option value="">Semua Status</option>
                                <option value="Aktif" @if (request('status') == 'Aktif') selected @endif>Aktif</option>
                                <option value="Pending" @if (request('status') == 'Pending') selected @endif>Pending</option>
                                <option value="Selesai" @if (request('status') == 'Selesai') selected @endif>Selesai</option>
                                <option value="Batal" @if (request('status') == 'Batal') selected @endif>Batal</option>
                            </select>
                        </div>
                    </div>

                    {{-- Tombol Aksi --}}
                    <div class="col-md-2 d-flex align-items-end">
                        <div class="d-flex w-100 gap-2">
                            <button type="submit" class="btn btn-maroon shadow-sm w-100" title="Terapkan Filter">
                                <i class="bi bi-funnel"></i>
                            </button>
                            <a href="{{ route('pra-penelitian.index') }}" class="btn btn-light border shadow-sm w-100"
                                title="Reset Filter">
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- 3. Tabel Kustom --}}
    <div class="custom-table-card animate-up" style="animation-delay: 0.2s;">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th class="text-center" width="5%">No</th>
                        <th>Nama</th>
                        <th>Judul Penelitian</th>
                        {{-- [BARU] Header Jenis --}}
                        <th class="text-center">Jenis</th>
                        <th>Universitas</th>
                        <th>Exp. MOU</th>
                        <th>Tgl Mulai</th>
                     <th class="text-center">Kategori Mhs</th>
                        <th class="text-center">Status</th>
                        <th class="text-center" width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($penelitian as $item)
                        <tr>
                            <td class="text-center text-muted font-weight-bold">{{ $penelitian->firstItem() + $loop->index }}</td>
                            {{-- Kolom Nama --}}
                            <td>
                                @if ($item->anggotas->isNotEmpty())
                                    <ul class="list-unstyled mb-0 small">
                                        @foreach ($item->anggotas as $anggota)
                                            <li><i class="bi bi-person text-muted me-1"></i>{{ $anggota->nama }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            {{-- Kolom Judul --}}
                            <td>
                                <span class="d-inline-block text-truncate" style="max-width: 200px;" title="{{ $item->judul }}">
                                    {{ $item->judul }}
                                </span>
                            </td>

                            {{-- [BARU] Kolom Jenis dengan Badge --}}
                            <td class="text-center">
                                @php
                                    $jenisClass = 'bg-secondary';
                                    $jenisIcon = 'bi-circle';
                                    
                                    if ($item->jenis_penelitian == 'Penelitian') {
                                        $jenisClass = 'bg-primary'; // Biru
                                        $jenisIcon = 'bi-journal-richtext';
                                    } elseif ($item->jenis_penelitian == 'Data Awal') {
                                        $jenisClass = 'bg-info text-dark'; // Biru Muda/Cyan
                                        $jenisIcon = 'bi-bar-chart-line';
                                    } elseif ($item->jenis_penelitian == 'Uji Validitas') {
                                        $jenisClass = 'bg-warning text-dark'; // Kuning
                                        $jenisIcon = 'bi-check2-square';
                                    }
                                @endphp
                                <span class="badge {{ $jenisClass }} p-2 rounded-pill shadow-sm"
                                    style="font-size: 0.7rem; font-weight: 500;">
                                    <i class="bi {{ $jenisIcon }} me-1"></i> {{ $item->jenis_penelitian }}
                                </span>
                            </td>

                            {{-- Kolom Univ --}}
                            <td>{{ $item->mou ? $item->mou->nama_instansi ?? $item->mou->nama_universitas : 'N/A' }}</td>
                            
                            {{-- Kolom Exp --}}
                            <td>{{ $item->mou->tanggal_keluar ? $item->mou->tanggal_keluar->format('d M Y') : 'N/A' }}</td>
                            
                            {{-- Kolom Mulai --}}
                            <td>{{ $item->tanggal_mulai ? $item->tanggal_mulai->format('d M Y') : 'N/A' }}</td>
                            
                            {{-- Kolom Jml --}}
                          {{-- Kolom Kategori Mahasiswa (Gantikan Jml Mhs) --}}
<td class="text-center">
    @if($item->jenis_mahasiswa)
        {{-- Jika sudah dipilih, tampilkan sebagai Badge yang bisa diklik --}}
        <button type="button" 
                class="btn btn-sm {{ $item->jenis_mahasiswa == 'Internal' ? 'btn-outline-primary' : 'btn-outline-success' }} rounded-pill px-3 shadow-sm btn-edit-jenis"
                data-bs-toggle="modal" 
                data-bs-target="#jenisMhsModal"
                data-id="{{ $item->id }}"
                data-jenis="{{ $item->jenis_mahasiswa }}"
                title="Klik untuk ubah">
            {{ $item->jenis_mahasiswa }}
        </button>
    @else
        {{-- Jika belum dipilih, tampilkan tombol Setup --}}
        <button type="button" 
                class="btn btn-sm btn-maroon rounded-pill px-3 shadow-sm btn-edit-jenis"
                data-bs-toggle="modal" 
                data-bs-target="#jenisMhsModal"
                data-id="{{ $item->id }}"
                data-jenis="">
            <i class="bi bi-gear-fill me-1"></i> Set Kategori
        </button>
    @endif
    
    {{-- Tampilkan jumlah kecil di bawahnya sebagai info tambahan --}}
    <div class="small text-muted mt-1" style="font-size: 0.7rem;">
        ({{ $item->anggotas_count }} Orang)
    </div>
</td>

                            {{-- Kolom Status --}}
                            <td class="text-center">
                                @php
                                    $statusClass = 'bg-secondary';
                                    if ($item->status == 'Aktif' || $item->status == 'Approved') {
                                        $statusClass = 'bg-success';
                                    }
                                    if ($item->status == 'Batal' || $item->status == 'Ditolak') {
                                        $statusClass = 'bg-danger';
                                    }
                                    if ($item->status == 'Pending') {
                                        $statusClass = 'bg-warning text-dark';
                                    }
                                    if ($item->status == 'Selesai') {
                                        $statusClass = 'bg-primary';
                                    }
                                @endphp
                                <span class="badge {{ $statusClass }} p-2" style="font-size: 0.75rem;">
                                    {{ $item->status }}
                                </span>
                            </td>

                            {{-- Kolom Aksi --}}
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                   
                                    <a href="{{ route('pra-penelitian.show', $item) }}" class="action-btn" title="View">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>
                                    <a href="{{ route('pra-penelitian.edit', $item) }}" class="action-btn" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>

                                    <form action="{{ route('pra-penelitian.destroy', $item) }}" method="POST"
                                        class="d-inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="action-btn delete btn-delete" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>

                                    @if ($item->status == 'Pending')
                                        <form action="{{ route('pra-penelitian.batal', $item) }}" method="POST"
                                            class="d-inline batal-form">
                                            @csrf
                                            @method('PATCH')
                                            <button type="button" class="action-btn batal btn-batal" title="Batalkan">
                                                <i class="bi bi-x-circle"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        {{-- Empty State --}}
                        <tr>
                            <td colspan="10" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="bi bi-folder-x display-4 text-muted mb-3" style="opacity: 0.5;"></i>
                                    <h5 class="text-muted font-weight-bold">Tidak ada data ditemukan</h5>
                                    <p class="text-muted small">Coba ubah filter atau tambahkan pengajuan baru.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- 6. Paginasi (Manual Appends) --}}
    <div class="d-flex justify-content-center mt-4 animate-up" style="animation-delay: 0.3s;">
        {{-- Menggunakan appends secara manual untuk mempertahankan filter --}}
        {{ $penelitian->appends([
                'search' => request('search'),
                'status' => request('status'),
                'jenis_penelitian' => request('jenis_penelitian'),
                'jenis_mahasiswa' => request('jenis_mahasiswa'),
            ])->links('pagination.custom') }}
    </div>

    {{-- 7. Notifikasi SweetAlert --}}
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 1800,
                    toast: true,
                    position: 'top-end'
                });
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian!',
                    text: '{{ session('error') }}',
                    showConfirmButton: true
                });
            });
        </script>
    @endif

    {{-- Modal Notepad CRUD Full --}}
    <div class="modal fade" id="notepadModal" tabindex="-1" aria-labelledby="notepadModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content" style="border-radius: var(--card-radius); overflow: hidden; border: none;">
                <div class="modal-header" style="background: var(--custom-maroon-subtle); color: var(--custom-maroon);">
                    <h5 class="modal-title fw-bold" id="notepadModalLabel">
                        <i class="bi bi-journal-text me-2"></i>Catatan Saya
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="row g-0" style="height: 500px;">
                        {{-- SIDEBAR: DAFTAR CATATAN --}}
                        <div class="col-md-4 border-end bg-light d-flex flex-column">
                            <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-white">
                                <span class="fw-bold text-muted small">DAFTAR CATATAN</span>
                                <button class="btn btn-sm btn-maroon" id="btn-new-note">
                                    <i class="bi bi-plus-lg"></i> Baru
                                </button>
                            </div>
                            <div class="list-group list-group-flush overflow-auto flex-grow-1" id="notes-list-container">
                                <div class="text-center p-4 text-muted small loading-indicator">
                                    <div class="spinner-border spinner-border-sm mb-2" role="status"></div>
                                    <div>Memuat...</div>
                                </div>
                            </div>
                        </div>
                        {{-- CONTENT: FORM EDITOR --}}
                        <div class="col-md-8 d-flex flex-column bg-white">
                            <div class="p-3 border-bottom">
                                <input type="text" id="note-title" class="form-control fw-bold border-0 fs-5"
                                    placeholder="Judul Catatan..." style="background: transparent;">
                            </div>
                            <div class="flex-grow-1 p-3">
                                <textarea id="note-content" class="form-control h-100 border-0"
                                    style="resize: none; box-shadow: none;" placeholder="Tulis isi catatan di sini..."></textarea>
                            </div>
                            <div class="p-3 border-top bg-light d-flex justify-content-between align-items-center">
                                <small class="text-muted fst-italic" id="status-text">Siap.</small>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-outline-danger btn-sm d-none"
                                        id="btn-delete-note">
                                        <i class="bi bi-trash"></i> Hapus
                                    </button>
                                    <button type="button" class="btn btn-maroon btn-sm px-4" id="btn-save-note">
                                        <i class="bi bi-save me-1"></i> Simpan
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- MODAL PILIH JENIS MAHASISWA --}}
<div class="modal fade" id="jenisMhsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content" style="border-radius: var(--card-radius); border: none;">
            <div class="modal-header bg-light">
                <h6 class="modal-title fw-bold">Set Kategori Peneliti</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formJenisMhs" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <p class="small text-muted mb-3">Pilih status peneliti untuk pengajuan ini:</p>
                    
                    <div class="d-grid gap-2">
                        <input type="radio" class="btn-check" name="jenis_mahasiswa" id="optionInternal" value="Internal" required>
                        <label class="btn btn-outline-primary text-start" for="optionInternal">
                            <i class="bi bi-building me-2"></i> Internal
                            <div class="small text-muted" style="font-size: 0.7rem; color:#ffff;">Staff/ Karyawan RSUD Simpang Lima Gumul</div>
                        </label>

                        <input type="radio" class="btn-check" name="jenis_mahasiswa" id="optionEksternal" value="Eksternal">
                        <label class="btn btn-outline-success text-start" for="optionEksternal">
                            <i class="bi bi-globe me-2"></i> Eksternal
                            <div class="small text-muted" style="font-size: 0.7rem; color:#ffff;">Peneliti dari luar</div>
                        </label>
                    </div>
                </div>
                <div class="modal-footer p-2 bg-light">
                    <button type="submit" class="btn btn-maroon btn-sm w-100">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>


@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
{{-- SCRIPT KHUSUS MODAL JENIS --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const jenisModal = document.getElementById('jenisMhsModal');
        if (jenisModal) {
            jenisModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const jenis = button.getAttribute('data-jenis');
                
                const form = document.getElementById('formJenisMhs');
                
             
                const baseUrl = "{{ url('pra-penelitian') }}"; 
                form.action = baseUrl + '/' + id + '/update-jenis';

                // Reset & Set Radio Button
                document.getElementById('optionInternal').checked = false;
                document.getElementById('optionEksternal').checked = false;

                if (jenis === 'Internal') {
                    document.getElementById('optionInternal').checked = true;
                } else if (jenis === 'Eksternal') {
                    document.getElementById('optionEksternal').checked = true;
                }
            });
        }
    });

        document.addEventListener('DOMContentLoaded', () => {

            // Konfirmasi Hapus
            const deleteButtons = document.querySelectorAll('.btn-delete');
            deleteButtons.forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const form = this.closest('form');

                    Swal.fire({
                        title: 'Yakin hapus data?',
                        text: "Data pra penelitian ini akan dihapus permanen.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#7c1316',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });

            // Konfirmasi Batal
            const batalButtons = document.querySelectorAll('.btn-batal');
            batalButtons.forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const form = this.closest('form');

                    Swal.fire({
                        title: 'Yakin batalkan penelitian?',
                        text: "Status penelitian akan diubah menjadi 'Batal'.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#7c1316',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, batalkan!',
                        cancelButtonText: 'Tutup'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });

            // --- LOGIKA NOTEPAD CRUD (FULL AJAX) ---
            // (Sama seperti sebelumnya, tidak ada perubahan)
            const modalNotepad = document.getElementById('notepadModal');
            const listContainer = document.getElementById('notes-list-container');
            const titleInput = document.getElementById('note-title');
            const contentInput = document.getElementById('note-content');
            const btnSave = document.getElementById('btn-save-note');
            const btnDelete = document.getElementById('btn-delete-note');
            const btnNew = document.getElementById('btn-new-note');
            const statusText = document.getElementById('status-text');

            let activeNoteId = null;

            modalNotepad.addEventListener('show.bs.modal', function() {
                fetchNotes();
                resetForm();
            });

            function fetchNotes() {
                listContainer.innerHTML = `
                <div class="text-center p-4 text-muted small">
                    <div class="spinner-border spinner-border-sm text-secondary"></div>
                </div>`;
                fetch('{{ route('notes.index') }}')
                    .then(res => res.json())
                    .then(data => {
                        renderList(data);
                    })
                    .catch(err => {
                        listContainer.innerHTML =
                            '<div class="text-center p-3 text-danger small">Gagal memuat data.</div>';
                    });
            }

            function renderList(notes) {
                listContainer.innerHTML = '';
                if (notes.length === 0) {
                    listContainer.innerHTML = `
                    <div class="text-center p-5 text-muted">
                        <i class="bi bi-journal-x display-6"></i>
                        <p class="small mt-2">Belum ada catatan.</p>
                    </div>`;
                    return;
                }

                notes.forEach(note => {
                    const date = new Date(note.updated_at).toLocaleDateString('id-ID', {
                        day: 'numeric',
                        month: 'short'
                    });
                    const item = document.createElement('a');
                    item.href = '#';
                    item.className =
                        `list-group-item list-group-item-action py-3 ${activeNoteId == note.id ? 'active-note bg-light border-start border-4 border-danger' : ''}`;
                    item.innerHTML = `
                    <div class="d-flex w-100 justify-content-between">
                        <h6 class="mb-1 text-truncate" style="max-width: 180px;">${note.title || 'Tanpa Judul'}</h6>
                        <small class="text-muted" style="font-size: 0.7rem;">${date}</small>
                    </div>
                    <p class="mb-0 text-muted text-truncate small" style="max-width: 220px;">${note.content ? note.content.substring(0, 30) : '...'}</p>
                `;
                    item.addEventListener('click', (e) => {
                        e.preventDefault();
                        loadNoteToEditor(note);
                    });
                    listContainer.appendChild(item);
                });
            }

            function loadNoteToEditor(note) {
                activeNoteId = note.id;
                titleInput.value = note.title;
                contentInput.value = note.content;
                btnDelete.classList.remove('d-none');
                statusText.textContent = `Mengedit: ${note.title}`;
                fetchNotes();
            }

            btnNew.addEventListener('click', () => {
                resetForm();
                titleInput.focus();
            });

            function resetForm() {
                activeNoteId = null;
                titleInput.value = '';
                contentInput.value = '';
                btnDelete.classList.add('d-none');
                statusText.textContent = 'Catatan Baru';
            }

            btnSave.addEventListener('click', () => {
                if (!titleInput.value.trim()) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Judul Kosong',
                        text: 'Harap isi judul catatan.',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000
                    });
                    return;
                }

                const originalBtnHtml = btnSave.innerHTML;
                btnSave.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
                btnSave.disabled = true;
                statusText.textContent = 'Menyimpan...';

                fetch('{{ route('notes.store') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .content
                        },
                        body: JSON.stringify({
                            id: activeNoteId,
                            title: titleInput.value,
                            content: contentInput.value
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'success') {
                            activeNoteId = data.data.id;
                            fetchNotes();
                            btnDelete.classList.remove('d-none');
                            Swal.fire({
                                icon: 'success',
                                title: 'Tersimpan',
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 1500
                            });
                            statusText.textContent = 'Disimpan.';
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Terjadi kesalahan server.'
                        });
                    })
                    .finally(() => {
                        btnSave.innerHTML = originalBtnHtml;
                        btnSave.disabled = false;
                    });
            });

            btnDelete.addEventListener('click', () => {
                if (!activeNoteId) return;
                Swal.fire({
                    title: 'Hapus catatan ini?',
                    text: "Tidak bisa dikembalikan.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`/notes/${activeNoteId}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector(
                                        'meta[name="csrf-token"]').content
                                }
                            })
                            .then(res => res.json())
                            .then(data => {
                                resetForm();
                                fetchNotes();
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Terhapus',
                                    toast: true,
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    timer: 1500
                                });
                            });
                    }
                });
            });
        });
    </script>
@endsection