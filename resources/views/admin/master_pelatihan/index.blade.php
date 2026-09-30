@extends('layouts.app')

@section('title', 'Data Master Pelatihan')

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

    <style>
        :root {
            --custom-maroon: #7c1316;
            --custom-maroon-light: #a3191d;
            --custom-maroon-subtle: #fcf0f1;
            --text-dark: #2c3e50;
            --text-muted: #64748b;
            --card-radius: 12px;
            --shadow-soft: 0 4px 20px rgba(0, 0, 0, 0.05);
            --transition: 0.3s ease;
        }

        .header-card {
            background: white;
            border-radius: var(--card-radius);
            border-left: 5px solid var(--custom-maroon);
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .filter-card {
            background: #fff;
            border-radius: var(--card-radius);
            box-shadow: var(--shadow-soft);
            overflow: hidden;
            border: 1px solid #f8f9fa;
            margin-bottom: 1.5rem;
        }
        .filter-header {
            background-color: var(--custom-maroon-subtle);
            padding: 12px 20px;
            border-bottom: 1px solid #f1f5f9;
            color: var(--custom-maroon);
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .filter-body { padding: 20px; }
        .filter-label { font-size: 0.75rem; color: var(--text-muted); font-weight: 600; letter-spacing: 0.5px; margin-bottom: 6px; display: block; }
        
        .search-input-group .input-group-text { background: #fff; border-right: none; border-radius: 8px 0 0 8px; color: var(--text-muted); }
        .search-input-group .form-control { border-left: none; border-radius: 0 8px 8px 0; box-shadow: none !important; border-color: #dee2e6; }

        .custom-table-card { background: #fff; border-radius: var(--card-radius); box-shadow: var(--shadow-soft); overflow: hidden; border: none; }
        .table thead th { background-color: var(--custom-maroon); color: white; border: none; padding: 1rem; font-weight: 600; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px; vertical-align: middle; white-space: nowrap; }
        .table tbody td { padding: 1rem; vertical-align: middle; color: #475569; border-bottom: 1px solid #f1f5f9; font-size: 0.95rem; }
        .table-hover tbody tr:hover { background-color: #fff5f6; }

        .btn-maroon { background-color: var(--custom-maroon); color: #fff; border: none; border-radius: 8px; padding: 0.6rem 1.2rem; font-weight: 600; transition: var(--transition); display: inline-flex; align-items: center; gap: 8px; text-decoration: none; }
        .btn-maroon:hover { background-color: var(--custom-maroon-light); color: white; }
        
        .btn-outline-custom { border: 1px solid #dee2e6; color: var(--text-dark); background: #fff; border-radius: 8px; padding: 0.6rem 1.2rem; font-weight: 600; transition: var(--transition); display: inline-flex; align-items: center; gap: 8px; cursor: pointer; }
        .btn-outline-custom:hover { background: #f8f9fa; color: var(--custom-maroon); }

        .action-btn { width: 32px; height: 32px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; border: none; }
        .btn-edit { background: #fff7ed; color: #ea580c; }
        .btn-delete { background: #fef2f2; color: #dc2626; }

        .animate-up { animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards; opacity: 0; transform: translateY(20px); }
        @keyframes fadeInUp { to { opacity: 1; transform: translateY(0); } }
    </style>

    <div class="row animate-up">
        <div class="col-12">
            {{-- Header Title --}}
            <div class="header-card d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                <div class="mb-3 mb-md-0">
                    <h4 class="fw-bold mb-0 text-dark">Data Master Pelatihan</h4>
                    <p class="mb-0 small text-muted">Daftar pelatihan untuk rekomendasi Training Needs Analysis (TNA).</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <button type="button" class="btn-outline-custom shadow-sm" onclick="downloadTemplateCSV()">
                        <i class="bi bi-download"></i> Template CSV
                    </button>
                    <button type="button" class="btn-outline-custom shadow-sm" data-bs-toggle="modal" data-bs-target="#importModal">
                        <i class="bi bi-file-earmark-excel"></i> Import
                    </button>
                    <a href="{{ route('admin.master_pelatihan.create') }}" class="btn-maroon shadow-sm">
                        <i class="bi bi-plus-lg"></i> Tambah Manual
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success shadow-sm border-0 mb-4 rounded-3">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger shadow-sm border-0 mb-4 rounded-3">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                </div>
            @endif

            {{-- Filter & Pencarian Box --}}
            <div class="filter-card">
                <div class="filter-header">
                    <i class="bi bi-funnel-fill"></i> Pencarian
                </div>
                <div class="filter-body">
                    <div class="row">
                        <div class="col-md-12">
                            <label class="filter-label">CARI JUDUL PELATIHAN</label>
                            <div class="input-group search-input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" id="searchInput" class="form-control" placeholder="Ketik judul atau kategori pelatihan...">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Table Data --}}
            <div class="custom-table-card mb-4">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="pelatihanTable">
                        <thead>
                            <tr>
                                <th class="text-center" width="5%">No</th>
                                <th>Nama Pelatihan</th>
                                <th>Kategori</th>
                                <th>Durasi</th>
                                <th>Sasaran</th>
                                <th class="text-center" width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            @forelse ($pelatihans as $index => $item)
                                <tr class="data-row">
                                    <td class="text-center text-muted fw-bold row-no">{{ $pelatihans->firstItem() + $index }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center text-secondary fw-bold" 
                                                 style="width: 35px; height: 35px;">
                                                <i class="bi bi-journal-bookmark"></i>
                                            </div>
                                            <span class="fw-bold text-dark pelatihan-name">{{ $item->nama_pelatihan }}</span>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-secondary text-white">{{ $item->kategori ?? '-' }}</span></td>
                                    <td><span class="text-muted small"><i class="bi bi-clock"></i> {{ $item->durasi ?? '-' }}</span></td>
                                    <td class="text-muted small">{{ $item->sasaran ?? '-' }}</td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="{{ route('admin.master_pelatihan.edit', $item->id) }}" class="action-btn btn-edit" title="Edit Data">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <form action="{{ route('admin.master_pelatihan.destroy', $item->id) }}" method="POST" class="d-inline delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="action-btn btn-delete btn-submit-delete" title="Hapus Data">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr id="emptyRow">
                                    <td colspan="6" class="text-center py-5">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="bi bi-folder-x display-4 text-muted mb-3 opacity-50"></i>
                                            <h5 class="text-muted fw-bold">Belum ada data pelatihan</h5>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="p-3 border-top bg-light d-flex justify-content-center">
                    {{ $pelatihans->links() }}
                </div>
            </div>
            
            <div class="d-flex justify-content-between align-items-center text-muted small mt-4 mb-5 px-2">
                <div>&copy; 2026 <strong class="text-dark">Sindikat</strong> · All Rights Reserved</div>
            </div>
        </div>
    </div>

    {{-- Modal Import Excel/CSV --}}
    <div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: var(--card-radius); overflow: hidden;">
                <div class="modal-header text-white" style="background-color: var(--custom-maroon);">
                    <h5 class="modal-title fw-bold"><i class="bi bi-file-earmark-excel me-2"></i> Import Pelatihan (CSV)</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.master_pelatihan.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="alert alert-info small border-0 shadow-sm">
                            <i class="bi bi-info-circle-fill me-1"></i> Pastikan file CSV dipisahkan oleh titik koma (;). Header baris pertama akan diabaikan.<br><br>
                            <strong>Urutan Kolom:</strong><br>
                            1. Judul Pelatihan<br>
                            2. Kategori<br>
                            3. Durasi<br>
                            4. Sasaran
                        </div>
                        <label class="form-label fw-bold">Pilih File (.csv)</label>
                        <input type="file" name="file_csv" class="form-control mb-2" accept=".csv" required>
                    </div>
                    <div class="modal-footer bg-light p-3">
                        <button type="button" class="btn btn-outline-custom" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-maroon">Proses Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // SweetAlert Confirm Delete
            document.querySelectorAll('.btn-submit-delete').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const form = this.closest('form');
                    Swal.fire({
                        title: 'Hapus Data?',
                        text: "Pelatihan yang dihapus tidak akan direkomendasikan lagi oleh sistem AI.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc2626',
                        cancelButtonColor: '#64748b',
                        confirmButtonText: 'Ya, Hapus!'
                    }).then((result) => {
                        if (result.isConfirmed) form.submit();
                    });
                });
            });

            // Live Search Sederhana
            const searchInput = document.getElementById('searchInput');
            const allRows = document.querySelectorAll('#tableBody .data-row');
            const emptyRow = document.getElementById('emptyRow');

            if(searchInput) {
                searchInput.addEventListener('input', function() {
                    let query = this.value.toLowerCase().trim();
                    let matchCount = 0;

                    allRows.forEach(row => {
                        let textToSearch = row.innerText.toLowerCase();
                        if (textToSearch.includes(query)) {
                            row.style.display = '';
                            matchCount++;
                        } else {
                            row.style.display = 'none';
                        }
                    });

                    // Tampilkan icon kosong jika tidak ada yang match (dan aslinya ada datanya)
                    if (emptyRow) {
                        emptyRow.style.display = (matchCount === 0 && allRows.length > 0) ? '' : 'none';
                    }
                });
            }
        });

        // Download Template CSV (Native Web URI, Anti-Gagal)
        function downloadTemplateCSV() {
            const csvContent = "data:text/csv;charset=utf-8,Judul Pelatihan;Kategori;Durasi;Sasaran\nPelatihan Service Excellence;Pelayanan;2 Hari;Staf Frontliner\nPelatihan BTCLS;Medis;4 Hari;Perawat";
            const encodedUri = encodeURI(csvContent);
            const link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", "Template_Import_Pelatihan.csv");
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    </script>
@endsection