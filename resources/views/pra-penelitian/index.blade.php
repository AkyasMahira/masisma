@extends('layouts.app')

@section('title', 'Pra Penelitian')
@section('page-title', 'Data Pra Penelitian')

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
            z-index: 1050;
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

        /* Style untuk tombol notepad */
        .btn-tool {
            background: #fff;
            border: 1px solid #e0e0e0;
            color: var(--text-dark);
            border-radius: 8px;
            padding: 8px 12px;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
            /* Disesuaikan agar mirip .btn-maroon */
            padding: 8px 16px;
            /* Disesuaikan agar mirip .btn-maroon */
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
            /* Light gray */
            color: #374151;
            /* Dark gray */
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

    {{-- 1. Header Halaman (Diambil dari style Pelatihan) --}}
    <div class="page-header-wrapper d-flex flex-wrap justify-content-between align-items-center gap-3 animate-up">
        <div>
            <h4 class="fw-bold mb-1" style="color: var(--custom-maroon);">Daftar Pra Penelitian</h4>
            <small class="text-muted">Kelola data pengajuan pra penelitian.</small>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            {{-- [BARU] Tombol Notepad --}}
            <button class="btn btn-tool shadow-sm" type="button" data-bs-toggle="modal" data-bs-target="#notepadModal"
                title="Notepad">
                <i class="bi bi-stickies"></i> Catatan
            </button>

            {{-- Tombol 'Tambah' disesuaikan dengan style .btn-maroon --}}
            <a href="{{ route('pra-penelitian.create') }}" class="btn btn-maroon shadow-sm d-flex align-items-center gap-2">
                <i class="bi bi-plus-lg"></i> Tambah Pengajuan
            </a>
        </div>
    </div>

    {{-- 2. Filter Card (Struktur baru) --}}
    <div class="filter-card animate-up" style="animation-delay: 0.1s;">
        <div class="filter-header">
            <i class="bi bi-funnel-fill mr-2"></i> Filter & Pencarian
        </div>
        <div class="card-body p-4">
            {{-- Form filter (Anda bisa sesuaikan action dan name) --}}
            <form id="filterForm" method="GET" action="{{ route('pra-penelitian.index') }}">
                <div class="row">
                    <div class="col-md-5 mb-3 mb-md-0">
                        <label class="small text-muted font-weight-bold text-uppercase">Cari Judul / Universitas</label>
                        <div class="input-group shadow-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light border-right-0"><i class="bi bi-search"></i></span>
                            </div>
                            <input type="text" class="form-control bg-light border-left-0" name="search"
                                placeholder="Cari..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-4 mb-3 mb-md-0">
                        <label class="small text-muted font-weight-bold text-uppercase">Status</label>
                        <div class="input-group shadow-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light border-right-0"><i
                                        class="bi bi-check-circle"></i></span>
                            </div>
                            <select class="form-control bg-light border-left-0" name="status">
                                <option value="">Semua Status</option>
                                <option value="Aktif" @if (request('status') == 'Aktif') selected @endif>Aktif</option>
                                <option value="Batal" @if (request('status') == 'Batal') selected @endif>Batal</option>
                                {{-- Tambahkan status lain jika ada --}}
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-maroon shadow-sm">Terapkan</button>
                        <a href="{{ route('pra-penelitian.index') }}" class="btn btn-light border shadow-sm ms-2"
                            title="Reset Filter">
                            <i class="bi bi-arrow-counterclockwise"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- 3. Tabel Kustom (Diambil dari style Pelatihan) --}}
    <div class="custom-table-card animate-up" style="animation-delay: 0.2s;">
        <div class="table-responsive">
            {{-- Class .table-bordered diganti menjadi .table-hover --}}
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th class="text-center" width="5%">No</th>
                        <th>Judul Penelitian</th>
                        <th>Universitas</th>
                        <th>Exp. MOU</th>
                        <th>Tgl Mulai</th>
                        <th class="text-center">Jml Mhs</th>
                        <th>Status</th>
                        <th class="text-center" width="20%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($penelitian as $item)
                        <tr>
                            <td class="text-center text-muted font-weight-bold">{{ $loop->iteration }}</td>
                            <td>{{ $item->judul }}</td>
                            <td>{{ $item->mou ? ($item->mou->nama_instansi ?? $item->mou->nama_universitas) : 'N/A' }}</td>
                            {{-- Asumsi kolom exp date di tabel MOU adalah 'tanggal_keluar' --}}
                            <td>{{ $item->mou->tanggal_keluar ? $item->mou->tanggal_keluar->format('d M Y') : 'N/A' }}</td>
                            <td>{{ $item->tanggal_mulai ? $item->tanggal_mulai->format('d M Y') : 'N/A' }}</td>
                            <td class="text-center">{{ $item->anggotas_count }}</td>
                            <td>
                                {{-- Style badge disesuaikan --}}
                                @php
                                    $statusClass = 'bg-secondary'; // Default
                                    if ($item->status == 'Aktif') {
                                        $statusClass = 'bg-success';
                                    }
                                    if ($item->status == 'Batal') {
                                        $statusClass = 'bg-danger';
                                    }
                                    // Tambahkan Selesai/Lainnya jika ada
                                @endphp
                                <span class="badge {{ $statusClass }} text-white p-2" style="font-size: 0.8rem;">
                                    {{ $item->status }}
                                </span>
                            </td>
                            <td class="text-center">
                                {{-- 4. Action Button (Diambil dari style Pelatihan) --}}
                                <a href="{{ route('pra-penelitian.show', $item) }}" class="action-btn" title="View">
                                    <i class="bi bi-eye-fill"></i>
                                </a>
                                <a href="{{ route('pra-penelitian.edit', $item) }}" class="action-btn" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>

                                {{-- Form untuk Hapus (Disesuaikan) --}}
                                <form action="{{ route('pra-penelitian.destroy', $item) }}" method="POST"
                                    class="d-inline delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="action-btn delete btn-delete" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>

                                {{-- Form untuk Batal (Disesuaikan) --}}
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
                            </td>
                        </tr>
                    @empty
                        {{-- 5. Empty State (Diambil dari style Pelatihan) --}}
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="bi bi-folder-x display-4 text-muted mb-3" style="opacity: 0.5;"></i>
                                    <h5 class="text-muted font-weight-bold">Tidak ada data ditemukan</h5>
                                    <p class="text-muted small">Tambahkan pengajuan pra penelitian terlebih dahulu.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- 6. Paginasi (Diambil dari style Pelatihan) --}}
    <div class="d-flex justify-content-center mt-4 animate-up" style="animation-delay: 0.3s;">
        {{-- Pastikan Anda punya file 'pagination.custom' atau ganti ke default --}}
        {{-- {{ $penelitian->links() }} <-- Ganti ke ini jika 'pagination.custom' tidak ada --}}
        {{ $penelitian->links('pagination.custom') }}
    </div>

    {{-- 7. Notifikasi SweetAlert (Diambil dari style Pelatihan) --}}
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
        <div class="modal-dialog modal-lg modal-dialog-centered"> {{-- Ukuran modal diperbesar (modal-lg) --}}
            <div class="modal-content" style="border-radius: var(--card-radius); overflow: hidden; border: none;">
                <div class="modal-header" style="background: var(--custom-maroon-subtle); color: var(--custom-maroon);">
                    <h5 class="modal-title fw-bold" id="notepadModalLabel">
                        <i class="bi bi-journal-text me-2"></i>Catatan Saya
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="row g-0" style="height: 500px;"> {{-- Tinggi Tetap --}}

                        {{-- SIDEBAR: DAFTAR CATATAN --}}
                        <div class="col-md-4 border-end bg-light d-flex flex-column">
                            <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-white">
                                <span class="fw-bold text-muted small">DAFTAR CATATAN</span>
                                <button class="btn btn-sm btn-maroon" id="btn-new-note">
                                    <i class="bi bi-plus-lg"></i> Baru
                                </button>
                            </div>
                            {{-- List Container --}}
                            <div class="list-group list-group-flush overflow-auto flex-grow-1" id="notes-list-container">
                                {{-- Item catatan akan di-render di sini via JS --}}
                                <div class="text-center p-4 text-muted small loading-indicator">
                                    <div class="spinner-border spinner-border-sm mb-2" role="status"></div>
                                    <div>Memuat...</div>
                                </div>
                            </div>
                        </div>

                        {{-- CONTENT: FORM EDITOR --}}
                        <div class="col-md-8 d-flex flex-column bg-white">
                            {{-- Form Header (Judul) --}}
                            <div class="p-3 border-bottom">
                                <input type="text" id="note-title" class="form-control fw-bold border-0 fs-5"
                                    placeholder="Judul Catatan..." style="background: transparent;">
                            </div>

                            {{-- Form Body (Isi) --}}
                            <div class="flex-grow-1 p-3">
                                <textarea id="note-content" class="form-control h-100 border-0" style="resize: none; box-shadow: none;"
                                    placeholder="Tulis isi catatan di sini..."></textarea>
                            </div>

                            {{-- Form Footer (Tombol Aksi) --}}
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
@endsection

@section('scripts')
    {{-- 8. Kebutuhan Javascript (Diambil dari style Pelatihan) --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
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
            const modalNotepad = document.getElementById('notepadModal');
            const listContainer = document.getElementById('notes-list-container');
            const titleInput = document.getElementById('note-title');
            const contentInput = document.getElementById('note-content');
            const btnSave = document.getElementById('btn-save-note');
            const btnDelete = document.getElementById('btn-delete-note');
            const btnNew = document.getElementById('btn-new-note');
            const statusText = document.getElementById('status-text');

            let activeNoteId = null; // Menyimpan ID catatan yang sedang dibuka

            // 1. Load Notes saat Modal Dibuka
            modalNotepad.addEventListener('show.bs.modal', function() {
                fetchNotes();
                resetForm();
            });

            // 2. Fungsi Fetch Notes dari Database
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

            // 3. Render List ke HTML
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
                    // Format tanggal simpel
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

                    // Klik Item List
                    item.addEventListener('click', (e) => {
                        e.preventDefault();
                        loadNoteToEditor(note);
                    });

                    listContainer.appendChild(item);
                });
            }

            // 4. Load Note ke Editor (Kanan)
            function loadNoteToEditor(note) {
                activeNoteId = note.id;
                titleInput.value = note.title;
                contentInput.value = note.content;

                btnDelete.classList.remove('d-none'); // Munculkan tombol hapus
                statusText.textContent = `Mengedit: ${note.title}`;

                // Refresh highlight di list
                fetchNotes();
            }

            // 5. Klik Tombol "Baru"
            btnNew.addEventListener('click', () => {
                resetForm();
                titleInput.focus();
            });

            function resetForm() {
                activeNoteId = null;
                titleInput.value = '';
                contentInput.value = '';
                btnDelete.classList.add('d-none'); // Sembunyikan tombol hapus
                statusText.textContent = 'Catatan Baru';

                // Hapus highlight active di list (opsional, fetchNotes akan mereset juga)
            }

            // 6. Tombol Simpan
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
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
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
                            activeNoteId = data.data.id; // Set ID aktif ke yang baru disimpan
                            fetchNotes(); // Refresh list
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

            // 7. Tombol Hapus
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


            // Inisialisasi Popover (jika Anda membutuhkannya)
            var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
            var popoverList = popoverTriggerList.map(function(popoverTriggerEl) {
                return new bootstrap.Popover(popoverTriggerEl, {
                    html: false,
                    sanitize: false
                })
            });
        });
    </script>
@endsection
