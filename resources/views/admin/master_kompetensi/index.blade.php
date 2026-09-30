@extends('layouts.app')

@section('title', 'Data Master Kompetensi')

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

    <style>
        :root { --custom-maroon: #7c1316; --custom-maroon-light: #a3191d; --custom-maroon-subtle: #fcf0f1; --text-dark: #2c3e50; --text-muted: #64748b; --card-radius: 12px; --shadow-soft: 0 4px 20px rgba(0, 0, 0, 0.05); --transition: 0.3s ease; }
        .header-card { background: white; border-radius: var(--card-radius); border-left: 5px solid var(--custom-maroon); padding: 20px; margin-bottom: 25px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .filter-card { background: #fff; border-radius: var(--card-radius); box-shadow: var(--shadow-soft); overflow: hidden; border: 1px solid #f8f9fa; margin-bottom: 1.5rem; }
        .filter-header { background-color: var(--custom-maroon-subtle); padding: 12px 20px; border-bottom: 1px solid #f1f5f9; color: var(--custom-maroon); font-weight: 700; display: flex; align-items: center; gap: 8px; }
        .filter-body { padding: 20px; }
        .filter-label { font-size: 0.75rem; color: var(--text-muted); font-weight: 600; letter-spacing: 0.5px; margin-bottom: 6px; display: block; }
        .search-input-group .input-group-text { background: #fff; border-right: none; border-radius: 8px 0 0 8px; color: var(--text-muted); }
        .search-input-group .form-control { border-left: none; border-radius: 0 8px 8px 0; box-shadow: none !important; border-color: #dee2e6; }
        .custom-table-card { background: #fff; border-radius: var(--card-radius); box-shadow: var(--shadow-soft); overflow: hidden; border: none; }
        .table thead th { background-color: var(--custom-maroon); color: white; border: none; padding: 1rem; font-weight: 600; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px; vertical-align: middle; white-space: nowrap; }
        .table tbody td { padding: 1rem; vertical-align: middle; color: #475569; border-bottom: 1px solid #f1f5f9; font-size: 0.95rem; }
        .table-hover tbody tr:hover { background-color: #fff5f6; }
        .custom-pagination-wrapper { padding: 20px; display: flex; flex-direction: column; align-items: center; border-top: 1px solid #f1f5f9; }
        .custom-pagination { display: flex; justify-content: center; align-items: center; gap: 8px; list-style: none; padding: 0; margin: 0; }
        .custom-pagination li a { text-decoration: none; color: var(--custom-maroon); font-weight: 600; display: flex; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: 10px; transition: all 0.2s; font-size: 0.95rem; }
        .custom-pagination li a:hover { background-color: var(--custom-maroon-subtle); }
        .custom-pagination li.active a { background-color: var(--custom-maroon); color: white; box-shadow: 0 4px 12px rgba(124, 19, 22, 0.4); }
        .custom-pagination li.disabled a { color: #cbd5e1; cursor: not-allowed; pointer-events: none; background: transparent; }
        .btn-maroon { background-color: var(--custom-maroon); color: #fff; border: none; border-radius: 8px; padding: 0.6rem 1.2rem; font-weight: 600; transition: var(--transition); display: inline-flex; align-items: center; gap: 8px; text-decoration: none; }
        .btn-maroon:hover { background-color: var(--custom-maroon-light); color: white; }
        .btn-outline-custom { border: 1px solid #dee2e6; color: var(--text-dark); background: #fff; border-radius: 8px; padding: 0.6rem 1.2rem; font-weight: 600; transition: var(--transition); display: inline-flex; align-items: center; gap: 8px; }
        .btn-outline-custom:hover { background: #f8f9fa; color: var(--custom-maroon); }
        .action-btn { width: 32px; height: 32px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; border: none; }
        .btn-edit { background: #fff7ed; color: #ea580c; }
        .btn-delete { background: #fef2f2; color: #dc2626; }
        .animate-up { animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards; opacity: 0; transform: translateY(20px); }
        @keyframes fadeInUp { to { opacity: 1; transform: translateY(0); } }
    </style>

    <div class="row animate-up">
        <div class="col-12">
            <div class="header-card d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-bold mb-0 text-dark">Data Master Kompetensi</h4>
                    <p class="mb-0 small text-muted">Kelola daftar standar kompetensi yang akan digunakan di berbagai kegiatan.</p>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn-outline-custom shadow-sm" onclick="downloadTemplateCSV()">
                        <i class="bi bi-download"></i> Template
                    </button>
                    <button type="button" class="btn-outline-custom shadow-sm" data-bs-toggle="modal" data-bs-target="#importModal">
                        <i class="bi bi-file-earmark-excel"></i> Import
                    </button>
                    <button type="button" class="btn-outline-custom shadow-sm" onclick="exportTableToExcel()">
                        <i class="bi bi-box-arrow-up-right"></i> Export
                    </button>
                    <a href="{{ route('admin.master_kompetensi.create') }}" class="btn-maroon shadow-sm">
                        <i class="bi bi-plus-lg"></i> Tambah Kompetensi
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success shadow-sm border-0 mb-4 rounded-3"><i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}</div>
            @endif

            <div class="filter-card">
                <div class="filter-header"><i class="bi bi-funnel-fill"></i> Filter & Pencarian</div>
                <div class="filter-body">
                    <div class="row">
                        <div class="col-md-12">
                            <label class="filter-label">CARI NAMA KOMPETENSI</label>
                            <div class="input-group search-input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" id="searchInput" class="form-control" placeholder="Ketik nama kompetensi atau deskripsi...">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="custom-table-card mb-4">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="kompetensiTable">
                        <thead>
                            <tr>
                                <th class="text-center" width="5%">No</th>
                                <th width="35%">Nama Kompetensi</th>
                                <th>Deskripsi / Standar</th>
                                <th class="text-center" width="15%" data-exclude="true">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            @forelse ($kompetensi as $index => $item)
                                <tr class="data-row">
                                    <td class="text-center text-muted fw-bold row-no">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center text-secondary fw-bold" style="width: 35px; height: 35px;"><i class="bi bi-award"></i></div>
                                            <span class="fw-bold text-dark search-target">{{ $item->nama_kompetensi }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="small text-muted search-target text-wrap" style="max-width: 400px;">
                                            {{ $item->deskripsi_default ?? 'Tidak ada deskripsi default.' }}
                                        </div>
                                    </td>
                                    <td class="text-center" data-exclude="true">
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="{{ route('admin.master_kompetensi.edit', $item->id) }}" class="action-btn btn-edit" title="Edit Data"><i class="bi bi-pencil-square"></i></a>
                                            <form action="{{ route('admin.master_kompetensi.destroy', $item->id) }}" method="POST" class="d-inline delete-form">
                                                @csrf @method('DELETE')
                                                <button type="button" class="action-btn btn-delete btn-submit-delete" title="Hapus Data"><i class="bi bi-trash"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr id="emptyRow"><td colspan="4" class="text-center py-5"><h5 class="text-muted">Belum ada data kompetensi</h5></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="custom-pagination-wrapper" id="paginationContainer"><ul class="custom-pagination" id="paginationNav"></ul></div>
            </div>
        </div>
    </div>

    {{-- Modal Import Excel --}}
    <div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: var(--card-radius); overflow: hidden;">
                <div class="modal-header text-white" style="background-color: var(--custom-maroon);">
                    <h5 class="modal-title fw-bold"><i class="bi bi-file-earmark-excel me-2"></i> Import Kompetensi</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="importForm">
                    <div class="modal-body p-4">
                        <label class="form-label fw-bold">Pilih File (.xlsx / .csv)</label>
                        <input type="file" id="excel_file" class="form-control mb-3" accept=".xlsx, .xls, .csv" required>
                        <small class="text-muted">Pastikan ada kolom header: <strong>Nama Kompetensi</strong> dan <strong>Deskripsi</strong> (opsional).</small>
                    </div>
                    <div class="modal-footer bg-light p-3">
                        <button type="button" class="btn btn-outline-custom" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-maroon" id="btnSubmitImport">Proses Import</button>
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
            // Konfirmasi Hapus
            document.querySelectorAll('.btn-submit-delete').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    Swal.fire({ title: 'Hapus Kompetensi?', text: "Data ini akan terhapus permanen!", icon: 'warning', showCancelButton: true, confirmButtonColor: '#dc2626', confirmButtonText: 'Hapus' }).then((result) => {
                        if (result.isConfirmed) this.closest('form').submit();
                    });
                });
            });

            // Live Search & Pagination
            const rowsPerPage = 10; let currentPage = 1;
            let allRows = Array.from(document.querySelectorAll('#tableBody .data-row'));
            let filteredRows = [...allRows];
            const searchInput = document.getElementById('searchInput');
            const paginationNav = document.getElementById('paginationNav');

            function updateTableDisplay() {
                let totalFiltered = filteredRows.length;
                if (totalFiltered === 0) {
                    allRows.forEach(row => row.style.display = 'none');
                    document.getElementById('emptyRow').style.display = '';
                    document.getElementById('paginationContainer').style.display = 'none';
                    return;
                } else {
                    document.getElementById('emptyRow').style.display = 'none';
                    document.getElementById('paginationContainer').style.display = 'flex';
                }

                let start = (currentPage - 1) * rowsPerPage; let end = start + rowsPerPage;
                allRows.forEach(row => row.style.display = 'none');
                filteredRows.slice(start, end).forEach((row, idx) => {
                    row.style.display = ''; row.querySelector('.row-no').innerText = start + idx + 1;
                });
                buildPaginationNav(totalFiltered);
            }

            function buildPaginationNav(totalFiltered) {
                let totalPages = Math.ceil(totalFiltered / rowsPerPage);
                paginationNav.innerHTML = '';
                if (totalPages <= 1) { document.getElementById('paginationContainer').style.display = 'none'; return; }

                let prevLi = document.createElement('li'); prevLi.className = currentPage === 1 ? 'disabled' : ''; prevLi.innerHTML = `<a href="#">&#8249;</a>`;
                prevLi.addEventListener('click', (e) => { e.preventDefault(); if(currentPage > 1) { currentPage--; updateTableDisplay(); } }); paginationNav.appendChild(prevLi);

                for (let i = 1; i <= totalPages; i++) {
                    let pageLi = document.createElement('li'); pageLi.className = currentPage === i ? 'active' : ''; pageLi.innerHTML = `<a href="#">${i}</a>`;
                    pageLi.addEventListener('click', (e) => { e.preventDefault(); currentPage = i; updateTableDisplay(); }); paginationNav.appendChild(pageLi);
                }

                let nextLi = document.createElement('li'); nextLi.className = currentPage === totalPages ? 'disabled' : ''; nextLi.innerHTML = `<a href="#">&#8250;</a>`;
                nextLi.addEventListener('click', (e) => { e.preventDefault(); if(currentPage < totalPages) { currentPage++; updateTableDisplay(); } }); paginationNav.appendChild(nextLi);
            }

            searchInput.addEventListener('input', function() {
                let query = this.value.toLowerCase().trim();
                filteredRows = allRows.filter(row => {
                    let textContext = Array.from(row.querySelectorAll('.search-target')).map(el => el.innerText.toLowerCase()).join(' ');
                    return textContext.includes(query);
                });
                currentPage = 1; updateTableDisplay();
            });

            updateTableDisplay();

            // Fitur Import JS (Mengambil Kolom)
            document.getElementById('importForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const file = document.getElementById('excel_file').files[0]; if (!file) return;
                const btnSubmit = document.getElementById('btnSubmitImport'); btnSubmit.disabled = true; btnSubmit.innerText = 'Memproses...';

                const reader = new FileReader();
                reader.onload = function(e) {
                    const workbook = XLSX.read(new Uint8Array(e.target.result), { type: 'array' });
                    const jsonData = XLSX.utils.sheet_to_json(workbook.Sheets[workbook.SheetNames[0]]);
                    
                    const formattedData = jsonData.map(row => ({
                        nama_kompetensi: row['Nama Kompetensi'] || row['nama_kompetensi'],
                        deskripsi_default: row['Deskripsi'] || row['deskripsi'] || row['deskripsi_default'] || ''
                    })).filter(row => row.nama_kompetensi);

                    if (formattedData.length === 0) {
                        Swal.fire('Format Salah', 'Pastikan ada kolom "Nama Kompetensi".', 'error');
                        btnSubmit.disabled = false; btnSubmit.innerText = 'Proses Import'; return;
                    }

                    fetch("{{ route('admin.master_kompetensi.import') }}", {
                        method: "POST", headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" },
                        body: JSON.stringify({ data: formattedData })
                    }).then(res => res.json()).then(res => {
                        if (res.success) Swal.fire('Sukses!', res.message, 'success').then(() => location.reload());
                    }).catch(err => Swal.fire('Error', 'Gagal memproses ke server.', 'error')).finally(() => { btnSubmit.disabled = false; btnSubmit.innerText = 'Proses Import'; });
                };
                reader.readAsArrayBuffer(file);
            });
        });

        function exportTableToExcel() {
            let table = document.getElementById('kompetensiTable').cloneNode(true);
            table.querySelectorAll('[data-exclude="true"]').forEach(el => el.remove());
            XLSX.writeFile(XLSX.utils.table_to_book(table), "Data_Kompetensi.xlsx");
        }

        function downloadTemplateCSV() {
            const csvContent = "data:text/csv;charset=utf-8,Nama Kompetensi,Deskripsi\nMampu menerapkan Etika Komunikasi,Bersikap sopan dan menghormati pasien.\n";
            const link = document.createElement("a");
            link.setAttribute("href", encodeURI(csvContent)); link.setAttribute("download", "Template_Kompetensi.csv");
            document.body.appendChild(link); link.click(); document.body.removeChild(link);
        }
    </script>
@endsection