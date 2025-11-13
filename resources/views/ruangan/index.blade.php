@extends('layouts.app')

@section('title', 'Ruangan')
@section('page-title', 'Data Ruangan')

@section('content')

    <style>
        :root {
            --custom-maroon: #7c1316;
            --custom-maroon-light: #a3191d;
            --custom-maroon-subtle: #fcf0f1;
            --text-dark: #2c3e50;
            --text-muted: #95a5a6;
            --card-radius: 16px;
            --transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* --- Header & Toolbar --- */
        .page-header-wrapper {
            background: #fff;
            border-radius: var(--card-radius);
            padding: 1.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            margin-bottom: 2rem;
            border-left: 5px solid var(--custom-maroon);

            /* FIX Z-INDEX: Agar dropdown muncul paling atas */
            position: relative;
            z-index: 1050;
            overflow: visible;
        }

        /* --- Room Cards --- */
        .room-card {
            border: none;
            border-radius: var(--card-radius);
            background: #fff;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            height: 100%;
            cursor: pointer;
        }

        .room-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 30px rgba(124, 19, 22, 0.15);
        }

        .room-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--custom-maroon);
            opacity: 0;
            transition: var(--transition);
        }

        .room-card:hover::before {
            opacity: 1;
        }

        .card-icon-bg {
            width: 50px;
            height: 50px;
            background-color: var(--custom-maroon-subtle);
            color: var(--custom-maroon);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .room-title {
            font-weight: 700;
            color: var(--text-dark);
            font-size: 1.1rem;
            margin-bottom: 0.25rem;
        }

        .capacity-text {
            font-size: 2.2rem;
            font-weight: 800;
            letter-spacing: -1px;
            line-height: 1;
        }

        /* --- Buttons --- */
        .btn-maroon {
            background-color: var(--custom-maroon);
            color: #fff;
            border: none;
            padding: 0.6rem 1.2rem;
            border-radius: 10px;
            font-weight: 500;
        }

        .btn-maroon:hover {
            background-color: var(--custom-maroon-light);
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(124, 19, 22, 0.3);
        }

        .btn-icon-soft {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
            background: #f8f9fa;
            color: var(--text-dark);
            border: 1px solid transparent;
        }

        .btn-icon-soft:hover {
            background: var(--custom-maroon);
            color: white;
        }

        .btn-icon-soft.delete:hover {
            background: #dc3545;
            color: white;
        }

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
        }

        .btn-tool:hover,
        .btn-tool:focus {
            background: #f8f9fa;
            border-color: var(--custom-maroon);
            outline: none;
        }

        /* --- Empty State --- */
        .empty-state-box {
            border: 2px dashed #e0e0e0;
            border-radius: var(--card-radius);
            background: #fafafa;
            transition: var(--transition);
        }

        .empty-state-box:hover {
            border-color: var(--custom-maroon);
            background: #fff;
        }

        /* --- Animation --- */
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

        /* --- Modal List --- */
        .student-list-item {
            border-left: 3px solid transparent;
            transition: 0.2s;
        }

        .student-list-item:hover {
            background-color: #f9f9f9;
            border-left-color: var(--custom-maroon);
        }
    </style>

    <div class="page-header-wrapper d-flex flex-wrap justify-content-between align-items-center gap-3 animate-up">
        <div>
            <h4 class="fw-bold text-dark mb-1">Manajemen Ruangan</h4>
            <p class="text-muted mb-0 small">Kelola kapasitas dan penempatan mahasiswa.</p>
        </div>

        <div class="d-flex flex-wrap gap-2 align-items-center">
            <form method="GET" class="me-2">
                <div class="input-group shadow-sm" style="width: 250px;">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-white border-right-0"
                            style="border-radius: 8px 0 0 8px; height: 100%;">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                    </div>
                    <input type="text" name="search" class="form-control border-left-0" placeholder="Cari ruangan..."
                        value="{{ request('search') }}" style="box-shadow: none; border-radius: 0 8px 8px 0;">
                </div>
            </form>

            <div class="dropdown position-relative">
                <button class="btn btn-tool shadow-sm" type="button" id="toolsBtn" onclick="toggleTools(event)">
                    <i class="bi bi-gear-fill text-secondary"></i> Tools
                </button>

                <div id="toolsDropdownMenu" class="dropdown-menu dropdown-menu-right shadow-sm border-0"
                    style="border-radius: 12px; position: absolute; right: 0; top: 110%; z-index: 2000; display: none; min-width: 200px; background: white;">

                    <a class="dropdown-item py-2" href="javascript:void(0)" onclick="exportToExcel(); closeTools();">
                        <i class="bi bi-file-earmark-excel text-success mr-2"></i> Export Excel
                    </a>
                    <a class="dropdown-item py-2" href="javascript:void(0)" onclick="downloadTemplate(); closeTools();">
                        <i class="bi bi-download text-primary mr-2"></i> Template
                    </a>

                    <div class="dropdown-divider"></div>

                    <label class="dropdown-item py-2 mb-0" style="cursor: pointer;">
                        <i class="bi bi-upload text-warning mr-2"></i> Import Excel
                        <input type="file" id="fileImport" style="display: none" accept=".xlsx,.xls"
                            onchange="importExcel(this); closeTools();">
                    </label>
                </div>
            </div>

            <a href="{{ route('ruangan.create') }}" class="btn btn-maroon shadow-sm d-flex align-items-center gap-2">
                <i class="bi bi-plus-lg"></i> Ruangan Baru
            </a>
        </div>
    </div>

    <div class="row g-4">
        @forelse ($ruangan as $room)
            @php
                $terisi = $room->mahasiswa_count;
                $total = $room->kuota_ruangan;
                $tersedia = max($total - $terisi, 0);
                $persentaseIsi = $total > 0 ? ($terisi / $total) * 100 : 0;

                if ($persentaseIsi >= 100) {
                    $statusColor = 'danger';
                    $statusText = 'Penuh';
                    $statusIcon = 'bi-x-circle-fill';
                } elseif ($persentaseIsi >= 80) {
                    $statusColor = 'warning';
                    $statusText = 'Hampir Penuh';
                    $statusIcon = 'bi-exclamation-circle-fill';
                } else {
                    $statusColor = 'success';
                    $statusText = 'Tersedia';
                    $statusIcon = 'bi-check-circle-fill';
                }
            @endphp

            <div class="col-lg-4 col-md-6 animate-up" style="animation-delay: {{ $loop->index * 0.1 }}s;">
                <div class="room-card p-4 d-flex flex-column justify-content-between" data-nama="{{ $room->nm_ruangan }}"
                    data-mahasiswa='@json($room->mahasiswa)' onclick="openModal(this)">

                    <div>
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="card-icon-bg">
                                <i class="bi bi-door-open-fill"></i>
                            </div>
                            <span class="badge badge-pill badge-light text-{{ $statusColor }} px-3 py-2 shadow-sm">
                                <i class="bi {{ $statusIcon }}"></i> {{ $statusText }}
                            </span>
                        </div>

                        <h5 class="room-title mt-3">{{ $room->nm_ruangan }}</h5>

                        <div class="d-flex align-items-baseline mt-3">
                            <span class="capacity-text text-dark">{{ $terisi }}</span>
                            <span class="text-muted ml-2" style="font-size: 1.2rem;">/ {{ $total }}</span>
                        </div>
                        <div class="text-muted small mb-2">Mahasiswa Terdaftar</div>

                        <div class="progress"
                            style="height: 10px; border-radius: 20px; background-color: #e9ecef; margin-top: 10px;">
                            <div class="progress-bar bg-{{ $statusColor }}" role="progressbar"
                                style="width: {{ $persentaseIsi }}%" aria-valuenow="{{ $persentaseIsi }}"
                                aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top border-light">
                        <small class="text-muted d-flex align-items-center gap-1">
                            <i class="bi bi-info-circle"></i> Detail
                        </small>

                        <div onclick="event.stopPropagation()">
                            <a href="{{ route('ruangan.edit', $room->id) }}" class="btn-icon-soft mr-1" title="Edit">
                                <i class="bi bi-pencil-square"></i>
                            </a>

                            <form action="{{ route('ruangan.destroy', $room->id) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-icon-soft delete" title="Hapus"
                                    onclick="return confirm('Yakin hapus ruangan ini? Data mahasiswa di dalamnya mungkin terpengaruh.')">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 animate-up">
                <div class="empty-state-box text-center p-5">
                    <div class="mb-3">
                        <i class="bi bi-box-seam display-1 text-muted opacity-50"></i>
                    </div>
                    <h5 class="text-dark fw-bold">Belum Ada Ruangan</h5>
                    <p class="text-muted">Silakan tambahkan ruangan manual atau import dari Excel.</p>
                    <a href="{{ route('ruangan.create') }}"
                        class="btn btn-maroon mt-2 d-inline-flex align-items-center gap-2">
                        <i class="bi bi-plus-lg"></i> Tambah Ruangan
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center mt-5 animate-up">
        {{ $ruangan->links('pagination.custom') }}
    </div>

    <div class="modal fade" id="modalMahasiswa" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                <div class="modal-header bg-white border-bottom-0 pb-0">
                    <div>
                        <h5 class="modal-title fw-bold text-dark" id="modalTitle">Detail Ruangan</h5>
                        <p class="text-muted small mb-0">Daftar mahasiswa yang menempati ruangan ini.</p>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light border-right-0"><i
                                        class="bi bi-search text-muted"></i></span>
                            </div>
                            <input type="text" id="searchModal" class="form-control bg-light border-left-0"
                                placeholder="Filter nama mahasiswa..." onkeyup="filterList()">
                        </div>
                    </div>

                    <div style="max-height: 400px; overflow-y: auto;">
                        <ul id="listMahasiswa" class="list-group list-group-flush">
                        </ul>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script src="https://cdn.sheetjs.com/xlsx-0.20.3/package/dist/xlsx.full.min.js"></script>
    <script>
        // --- 1. MANUAL DROPDOWN SCRIPT (Supaya Tools Pasti Jalan) ---
        function toggleTools(e) {
            if (e) e.stopPropagation();
            var menu = document.getElementById('toolsDropdownMenu');
            // Toggle logic sederhana
            if (menu.style.display === 'block') {
                menu.style.display = 'none';
            } else {
                menu.style.display = 'block';
            }
        }

        function closeTools() {
            document.getElementById('toolsDropdownMenu').style.display = 'none';
        }

        // Tutup dropdown jika klik di luar
        window.addEventListener('click', function(e) {
            var menu = document.getElementById('toolsDropdownMenu');
            var btn = document.getElementById('toolsBtn');
            if (menu.style.display === 'block' && !menu.contains(e.target) && !btn.contains(e.target)) {
                menu.style.display = 'none';
            }
        });

        // --- 2. MODAL & LIST SCRIPT ---
        // Deteksi Library Modal (Bootstrap 4 vs 5)
        function openModal(element) {
            const namaRuangan = element.dataset.nama;
            const mahasiswa = JSON.parse(element.dataset.mahasiswa);

            document.getElementById('modalTitle').innerText = `Penghuni ${namaRuangan}`;
            const listContainer = document.getElementById('listMahasiswa');
            listContainer.innerHTML = "";

            if (!mahasiswa || mahasiswa.length === 0) {
                listContainer.innerHTML = `
                    <div class="text-center py-5">
                        <i class="bi bi-person-x display-4 text-muted opacity-25 mb-3"></i>
                        <p class="text-muted">Belum ada mahasiswa di ruangan ini.</p>
                    </div>`;
            } else {
                mahasiswa.forEach(m => {
                    listContainer.innerHTML += `
                        <li class="list-group-item student-list-item d-flex align-items-center py-3 px-2 border-bottom">
                            <div class="mr-3">
                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="bi bi-person-fill text-secondary" style="font-size: 1.2rem;"></i>
                                </div>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold text-dark student-name">${m.nm_mahasiswa}</h6>
                                <small class="text-muted d-flex align-items-center gap-2">
                                    <span><i class="bi bi-building mr-1"></i> ${m.univ_asal ?? '-'}</span>
                                    <span class="mx-1">&bull;</span> 
                                    <span>${m.prodi ?? '-'}</span>
                                </small>
                            </div>
                        </li>
                    `;
                });
            }
            // Tampilkan modal (Support jQuery/BS4 standard Laravel 7)
            $('#modalMahasiswa').modal('show');
        }

        function filterList() {
            const input = document.getElementById('searchModal');
            const filter = input.value.toLowerCase();
            const li = document.getElementById('listMahasiswa').getElementsByTagName('li');

            for (let i = 0; i < li.length; i++) {
                const nameEl = li[i].getElementsByClassName('student-name')[0];
                if (nameEl) {
                    const txtValue = nameEl.textContent || nameEl.innerText;
                    li[i].style.display = txtValue.toLowerCase().indexOf(filter) > -1 ? "" : "none";
                }
            }
        }

        // --- 3. EXCEL EXPORT/IMPORT (FIXED DATA STRUCTURE) ---
        function exportToExcel() {
            try {
                // Ambil data mentah dari PHP
                const rawData = @json($ruangan);

                // FIX: Cek apakah data paginated (punya properti .data) atau array biasa
                const dataToExport = rawData.data ? rawData.data : rawData;

                if (!dataToExport || dataToExport.length === 0) {
                    alert("Tidak ada data untuk diexport.");
                    return;
                }

                const ws_data = [
                    ['Nama Ruangan', 'Kuota Ruangan']
                ];

                // Loop data yang sudah dinormalisasi
                dataToExport.forEach(r => {
                    ws_data.push([r.nm_ruangan, r.kuota_ruangan]);
                });

                const ws = XLSX.utils.aoa_to_sheet(ws_data);
                const wb = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(wb, ws, "Ruangan");
                XLSX.writeFile(wb, `Data_Ruangan_${new Date().toISOString().split('T')[0]}.xlsx`);
            } catch (error) {
                console.error(error);
                alert("Gagal melakukan export. Cek konsol browser.");
            }
        }

        function downloadTemplate() {
            const ws_data = [
                ['Nama Ruangan', 'Kuota Ruangan'],
                ['Ruang Mawar', '30'],
                ['Ruang Melati', '25']
            ];
            const ws = XLSX.utils.aoa_to_sheet(ws_data);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "Template");
            XLSX.writeFile(wb, "Template_Ruangan.xlsx");
        }

        function importExcel(input) {
            const file = input.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function(e) {
                const data = new Uint8Array(e.target.result);
                const workbook = XLSX.read(data, {
                    type: 'array'
                });
                const jsonData = XLSX.utils.sheet_to_json(workbook.Sheets[workbook.SheetNames[0]]);

                if (!jsonData.length) {
                    alert('File kosong!');
                    return;
                }

                const formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('data', JSON.stringify(jsonData));

                // Tampilkan loading sederhana (optional)
                const btn = document.getElementById('toolsBtn');
                const originalText = btn.innerHTML;
                btn.innerHTML = 'Importing...';

                fetch('{{ route('ruangan.store') }}', {
                        method: 'POST',
                        body: formData
                    })
                    .then(r => r.json())
                    .then(d => {
                        btn.innerHTML = originalText;
                        if (d.success) {
                            alert('Berhasil import!');
                            location.reload();
                        } else {
                            alert('Gagal: ' + d.message);
                        }
                    })
                    .catch(err => {
                        btn.innerHTML = originalText;
                        alert('Terjadi kesalahan koneksi.');
                    });
            };
            reader.readAsArrayBuffer(file);
            input.value = '';
        }
    </script>
@endsection
