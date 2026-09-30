@extends('layouts.app')

@section('content')
    <style>
        :root {
            --custom-maroon: #7c1316;
            --custom-maroon-light: #a3191d;
            --custom-maroon-subtle: #fcf0f1;
            --text-dark: #2c3e50;
            --text-muted: #64748b;
            --card-radius: 12px;
            --shadow-soft: 0 4px 15px rgba(0, 0, 0, 0.03);
            --transition: 0.3s ease;
        }

        /* --- Global Cards --- */
        .custom-card {
            background: #fff;
            border-radius: var(--card-radius);
            box-shadow: var(--shadow-soft);
            border: 1px solid #f1f5f9;
            margin-bottom: 1.5rem;
            overflow: visible;
        }

        /* --- Header --- */
        .page-header {
            position: relative;
            z-index: 999;
            border-left: 5px solid var(--custom-maroon);
            padding: 1rem 1.5rem;
            background: #fff;
            border-radius: 8px;
            box-shadow: var(--shadow-soft);
        }

        .card-title-maroon {
            color: var(--custom-maroon);
            font-weight: 700;
            margin: 0;
            font-size: 1.05rem;
            display: flex;
            align-items: center;
        }

        /* --- Table Styling --- */
        .table thead th {
            background-color: var(--custom-maroon-subtle);
            color: var(--custom-maroon);
            border-bottom: 2px solid var(--custom-maroon);
            padding: 1rem;
            font-weight: 700;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            vertical-align: middle;
            white-space: nowrap;
        }
        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
            color: #475569;
            border-bottom: 1px solid #f8fafc;
            font-size: 0.9rem;
        }
        .table-hover tbody tr:hover {
            background-color: #f8fafc;
        }
        
        /* Membatasi teks panjang di tabel agar tidak merusak layout */
        .cell-truncate {
            max-width: 180px; 
            white-space: nowrap; 
            overflow: hidden; 
            text-overflow: ellipsis;
        }

        /* --- Inputs & Buttons --- */
        .custom-input {
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            padding: 0.4rem 0.75rem;
            font-size: 0.85rem;
        }
        .custom-input:focus {
            border-color: var(--custom-maroon);
            box-shadow: 0 0 0 0.2rem rgba(124, 19, 22, 0.15);
        }
        
        .btn-maroon {
            background-color: var(--custom-maroon);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 0.4rem 0.8rem;
            font-weight: 600;
            transition: var(--transition);
        }
        .btn-maroon:hover {
            background-color: var(--custom-maroon-light);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(124, 19, 22, 0.2);
        }

        /* Animation */
        .animate-up {
            animation: fadeInUp 0.5s cubic-bezier(0.4, 0, 0.2, 1) forwards;
            opacity: 0; transform: translateY(15px);
        }
        @keyframes fadeInUp { to { opacity: 1; transform: translateY(0); } }

        /* Pagination Custom */
        .page-item.active .page-link {
            background-color: var(--custom-maroon);
            border-color: var(--custom-maroon);
        }
        .page-link {
            color: var(--custom-maroon);
            font-size: 0.85rem;
            padding: 0.3rem 0.6rem;
        }
        .page-link:hover {
            color: var(--custom-maroon-light);
        }
    </style>

    <div class="container-fluid py-3">

        {{-- 1. Header Section --}}
        <div class="page-header mb-4 animate-up d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h4 class="fw-bold mb-1 text-dark">Data Evaluasi Institusi & Mahasiswa</h4>
                <small class="text-muted">Pantau rekapitulasi nilai evaluasi institusi dan detail individu secara real-time.</small>
            </div>
            
            {{-- TOMBOL LEGENDA MENGGUNAKAN BOOTSTRAP DROPDOWN AGAR TOUCH-FRIENDLY --}}
            <div class="dropdown mt-2 mt-md-0">
                <button class="btn btn-sm btn-outline-danger rounded-pill fw-bold" type="button" id="dropdownInfo" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-info-circle-fill me-1"></i> Info Perhitungan
                </button>
                <div class="dropdown-menu dropdown-menu-end p-3 shadow-lg border-0" aria-labelledby="dropdownInfo" style="width: max-content; max-width: 320px; z-index: 1050;">
                    <h6 class="border-bottom pb-2 mb-2 text-danger fw-bold"><i class="bi bi-calculator me-1"></i> Dasar Perhitungan Rata-rata</h6>
                    <div class="mb-1 fs-7"><i class="bi bi-info-circle-fill text-danger me-1"></i> <strong>Sikap (40%):</strong><br> <span class="text-muted">Kedisiplinan, Komunikasi, Kepatuhan.</span></div>
                    <div class="mb-2 fs-7"><i class="bi bi-tools text-danger me-1"></i> <strong>Keahlian (60%):</strong><br> <span class="text-muted">Pemahaman & Keterampilan teknis.</span></div>
                    <div class="p-2 bg-light rounded border border-danger border-opacity-25 fs-7 text-center">
                        <strong>Skor Akhir =</strong> <br>(Rata Sikap x 0.4) + (Rata Keahlian x 0.6)
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. Area Visualisasi Grafik --}}
        @if(count($hasilEvaluasi) > 0)
        <div class="row g-4 mb-4 animate-up" style="animation-delay: 0.2s;">
            <div class="col-lg-8">
                <div class="custom-card h-100 p-4">
                    <h6 class="card-title-maroon mb-4">
                        <i class="bi bi-bar-chart-fill me-2"></i> Grafik Rata-rata Skor Institusi
                    </h6>
                    <div style="position: relative; height: 320px; width: 100%;">
                        <canvas id="chartInstitusi"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="custom-card h-100 p-4">
                    <h6 class="card-title-maroon mb-4">
                        <i class="bi bi-radar me-2"></i> Rata-rata Kriteria (Keseluruhan)
                    </h6>
                    <div style="position: relative; height: 320px; width: 100%; display:flex; justify-content:center;">
                        <canvas id="chartRadarKriteria"></canvas>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- 3. Area Tabel Side-by-Side (Sampingan) --}}
        <div class="row g-4 animate-up" style="animation-delay: 0.3s;">
            
            {{-- TABEL INSTITUSI --}}
            <div class="col-xl-6">
                <div class="custom-card h-100 d-flex flex-column">
                    <div class="p-3 px-4 bg-white border-bottom">
                        <h6 class="card-title-maroon"><i class="bi bi-building-check me-2"></i>Rekap Evaluasi Institusi</h6>
                    </div>
                    
                    {{-- Filter Card Institusi --}}
                    <div class="p-3 bg-light border-bottom d-flex gap-2 flex-wrap">
                        <input type="text" id="searchInstitusi" class="form-control custom-input flex-grow-1" placeholder="Cari Kampus / Prodi..." style="max-width: 250px;">
                        <select id="sortInstitusi" class="form-select custom-input" style="max-width: 150px;">
                            <option value="desc">Tertinggi</option>
                            <option value="asc">Terendah</option>
                        </select>
                    </div>

                    <div class="table-responsive flex-grow-1">
                        <table class="table table-hover mb-0 text-nowrap align-middle w-100">
                            <thead>
                                <tr>
                                    <th style="width: 50%;">Institusi & Prodi</th>
                                    <th class="text-center">Mhs</th>
                                    <th class="text-center">Skor Akhir <i id="icon-sort-inst" class="bi bi-sort-numeric-down text-danger ms-1"></i></th>
                                </tr>
                            </thead>
                            <tbody id="tbody-institusi">
                                <!-- Render via JS -->
                            </tbody>
                        </table>
                    </div>
                    
                    {{-- Pagination Institusi --}}
                    <div class="p-3 border-top d-flex justify-content-between align-items-center bg-white" style="border-radius: 0 0 var(--card-radius) var(--card-radius);">
                        <small class="text-muted" id="info-institusi"></small>
                        <ul class="pagination pagination-sm mb-0" id="pagination-institusi"></ul>
                    </div>
                </div>
            </div>

            {{-- TABEL MAHASISWA --}}
            <div class="col-xl-6">
                <div class="custom-card h-100 d-flex flex-column">
                    <div class="p-3 px-4 bg-white border-bottom">
                        <h6 class="card-title-maroon"><i class="bi bi-person-lines-fill me-2"></i>Rincian Individu Mahasiswa</h6>
                    </div>
                    
                    {{-- Filter Card Mahasiswa --}}
                    <div class="p-3 bg-light border-bottom d-flex gap-2 flex-wrap">
                        <input type="text" id="searchMahasiswa" class="form-control custom-input flex-grow-1" placeholder="Cari Nama / Kampus..." style="max-width: 250px;">
                        <select id="sortMahasiswa" class="form-select custom-input" style="max-width: 150px;">
                            <option value="desc">Tertinggi</option>
                            <option value="asc">Terendah</option>
                        </select>
                    </div>

                    <div class="table-responsive flex-grow-1">
                        <table class="table table-hover mb-0 align-middle w-100">
                            <thead>
                                <tr>
                                    <th style="width: 55%;">Mahasiswa</th>
                                    <th class="text-center">Skor <i id="icon-sort-mhs" class="bi bi-sort-numeric-down text-danger ms-1"></i></th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tbody-mahasiswa">
                                <!-- Render via JS -->
                            </tbody>
                        </table>
                    </div>
                    
                    {{-- Pagination Mahasiswa --}}
                    <div class="p-3 border-top d-flex justify-content-between align-items-center bg-white" style="border-radius: 0 0 var(--card-radius) var(--card-radius);">
                        <small class="text-muted" id="info-mahasiswa"></small>
                        <ul class="pagination pagination-sm mb-0" id="pagination-mahasiswa"></ul>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- ========================================== --}}
    {{-- AREA MODAL DETAIL MAHASISWA --}}
    {{-- ========================================== --}}
    @foreach($detailMahasiswa as $mhs)
    <div class="modal fade" id="modalDetail{{ $mhs['id'] }}" tabindex="-1" aria-labelledby="modalLabel{{ $mhs['id'] }}" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg" style="border-radius: var(--card-radius);">
                <div class="modal-header bg-light border-bottom-0 py-3 px-4">
                    <h5 class="modal-title fw-bold" id="modalLabel{{ $mhs['id'] }}" style="color: var(--custom-maroon);">
                        <i class="bi bi-clipboard-data me-2"></i>Rincian Evaluasi: {{ $mhs['nama'] }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 bg-white">
                    @if(count($mhs['rincian']) > 0)
                        <div class="row g-4">
                            @foreach($mhs['rincian'] as $r)
                                <div class="col-md-6">
                                    <div class="card h-100 shadow-sm border-0" style="border-radius: 12px; background: #f8fafc; border: 1px solid #e2e8f0 !important;">
                                        <div class="card-header bg-white fw-bold py-3 px-3 border-bottom d-flex align-items-center justify-content-between" style="border-radius: 12px 12px 0 0;">
                                            <div class="text-dark">
                                                <i class="bi bi-geo-alt-fill me-2" style="color: var(--custom-maroon);"></i> {{ $r['ruangan'] }}
                                            </div>
                                            <form action="{{ route('admin.hapus_nilai_magang', ['mahasiswa_id' => $mhs['id'], 'ruangan_id' => $r['ruangan_id']]) }}" 
                                                  method="POST" class="d-inline form-delete-nilai">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-link text-danger p-0 btn-trigger-delete opacity-75 hover-opacity-100" data-mhs="{{ $mhs['nama'] }}" data-ruangan="{{ $r['ruangan'] }}" title="Hapus Nilai Ruangan Ini">
                                                    <i class="bi bi-trash3-fill fs-6"></i>
                                                </button>
                                            </form>
                                        </div>
                                        <div class="card-body p-3 fs-7 bg-white" style="border-radius: 0 0 12px 12px;">
                                            <div class="row text-muted mb-3">
                                                <div class="col-6 border-end">
                                                    <strong class="text-dark d-block border-bottom mb-2 pb-1">SIKAP ({{ $r['sikap'] }})</strong>
                                                    <div class="d-flex justify-content-between mb-1"><span>Disiplin:</span> <span class="text-dark fw-bold">{{ $r['detail']['kedisiplinan'] }}</span></div>
                                                    <div class="d-flex justify-content-between mb-1"><span>Komunikasi:</span> <span class="text-dark fw-bold">{{ $r['detail']['komunikasi'] }}</span></div>
                                                    <div class="d-flex justify-content-between"><span>Patuh:</span> <span class="text-dark fw-bold">{{ $r['detail']['kepatuhan'] }}</span></div>
                                                </div>
                                                <div class="col-6">
                                                    <strong class="text-dark d-block border-bottom mb-2 pb-1">KEAHLIAN ({{ $r['keahlian'] }})</strong>
                                                    <div class="d-flex justify-content-between mb-1"><span>Paham:</span> <span class="text-dark fw-bold">{{ $r['detail']['pemahaman'] }}</span></div>
                                                    <div class="d-flex justify-content-between"><span>Terampil:</span> <span class="text-dark fw-bold">{{ $r['detail']['keterampilan'] }}</span></div>
                                                </div>
                                            </div>
                                            <div class="bg-light p-3 rounded-3 border">
                                                <strong class="text-dark d-block mb-1 fs-7"><i class="bi bi-chat-left-text me-1"></i>Catatan Karu:</strong>
                                                <i class="text-muted fs-7">"{{ $r['detail']['catatan'] }}"</i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-warning mb-0 fs-7 border-0 shadow-sm rounded-3">
                            <i class="bi bi-exclamation-triangle me-2"></i> Belum ada detail penilaian dari ruangan untuk mahasiswa ini.
                        </div>
                    @endif
                </div>
                <div class="modal-footer border-top py-2 px-4 bg-light rounded-bottom">
                    <button type="button" class="btn btn-secondary rounded-3 px-4" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    @endforeach

@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- Tema warna Chart ---
            const maroonColor = '#7c1316';
            const maroonBg = 'rgba(124, 19, 22, 0.2)';
            Chart.defaults.font.family = "'Segoe UI', Roboto, Helvetica, Arial, sans-serif";

            // --- SweetAlert Hapus ---
            const deleteButtons = document.querySelectorAll('.btn-trigger-delete');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    const form = this.closest('form');
                    const namaMhs = this.getAttribute('data-mhs');
                    const namaRuangan = this.getAttribute('data-ruangan');

                    Swal.fire({
                        title: 'Hapus Nilai?',
                        text: `Data nilai ${namaMhs} dari ruangan ${namaRuangan} akan dihapus!`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#7c1316', 
                        cancelButtonColor: '#64748b',
                        confirmButtonText: '<i class="bi bi-trash3-fill me-1"></i> Ya, Hapus',
                        cancelButtonText: 'Batal',
                        reverseButtons: true,
                        customClass: { popup: 'rounded-4 shadow-lg' }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Memproses...',
                                allowOutsideClick: false,
                                didOpen: () => { Swal.showLoading(); }
                            });
                            form.submit();
                        }
                    });
                });
            });

            // ==========================================
            // LOGIC CLIENT-SIDE TABEL (SEARCH, SORT, PAGINATE)
            // ==========================================
            const dataInstitusi = @json($hasilEvaluasi);
            const dataMahasiswa = @json($detailMahasiswa);

            // State Institusi
            let searchInst = '';
            let sortInst = 'desc';
            let pageInst = 1;
            const perPageInst = 5;

            // State Mahasiswa
            let searchMhs = '';
            let sortMhs = 'desc';
            let pageMhs = 1;
            const perPageMhs = 5;

            // DOM Elements
            const domTbodyInst = document.getElementById('tbody-institusi');
            const domPageInst = document.getElementById('pagination-institusi');
            const domInfoInst = document.getElementById('info-institusi');
            
            const domTbodyMhs = document.getElementById('tbody-mahasiswa');
            const domPageMhs = document.getElementById('pagination-mahasiswa');
            const domInfoMhs = document.getElementById('info-mahasiswa');

            function renderInstitusi() {
                // Filter
                let filtered = dataInstitusi.filter(item => 
                    item.instansi.toLowerCase().includes(searchInst.toLowerCase()) || 
                    item.prodi.toLowerCase().includes(searchInst.toLowerCase())
                );
                
                // Sort
                filtered.sort((a, b) => {
                    return sortInst === 'desc' ? b.score_akhir - a.score_akhir : a.score_akhir - b.score_akhir;
                });

                // Update Icon Header
                document.getElementById('icon-sort-inst').className = sortInst === 'desc' ? 'bi bi-sort-numeric-down text-danger ms-1' : 'bi bi-sort-numeric-up-alt text-danger ms-1';

                // Paginate
                const totalItems = filtered.length;
                const totalPages = Math.ceil(totalItems / perPageInst);
                if (pageInst > totalPages) pageInst = totalPages || 1;
                
                const start = (pageInst - 1) * perPageInst;
                const paginated = filtered.slice(start, start + perPageInst);

                // Build Table
                domTbodyInst.innerHTML = '';
                if(paginated.length === 0) {
                    domTbodyInst.innerHTML = `<tr><td colspan="3" class="text-center py-4 text-muted">Tidak ada data institusi</td></tr>`;
                } else {
                    paginated.forEach(row => {
                        domTbodyInst.innerHTML += `
                            <tr>
                                <td class="cell-truncate">
                                    <div class="fw-bold text-dark text-truncate" title="${row.instansi}">${row.instansi}</div>
                                    <small class="text-muted text-truncate d-block" title="${row.prodi}">${row.prodi}</small>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border rounded-pill">${row.total_mahasiswa} Mhs</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge rounded-pill px-3 py-2" style="background-color: var(--custom-maroon); font-size: 0.85rem;">
                                        ${row.score_akhir}
                                    </span>
                                </td>
                            </tr>
                        `;
                    });
                }

                // Build Pagination
                domInfoInst.innerText = `Menampilkan ${start + 1} - ${Math.min(start + perPageInst, totalItems)} dari ${totalItems}`;
                buildPagination(domPageInst, totalPages, pageInst, (newPage) => {
                    pageInst = newPage;
                    renderInstitusi();
                });
            }

            function renderMahasiswa() {
                // Filter
                let filtered = dataMahasiswa.filter(item => 
                    item.nama.toLowerCase().includes(searchMhs.toLowerCase()) || 
                    item.instansi.toLowerCase().includes(searchMhs.toLowerCase()) ||
                    item.prodi.toLowerCase().includes(searchMhs.toLowerCase())
                );
                
                // Sort
                filtered.sort((a, b) => {
                    return sortMhs === 'desc' ? b.score_akhir - a.score_akhir : a.score_akhir - b.score_akhir;
                });

                // Update Icon Header
                document.getElementById('icon-sort-mhs').className = sortMhs === 'desc' ? 'bi bi-sort-numeric-down text-danger ms-1' : 'bi bi-sort-numeric-up-alt text-danger ms-1';

                // Paginate
                const totalItems = filtered.length;
                const totalPages = Math.ceil(totalItems / perPageMhs);
                if (pageMhs > totalPages) pageMhs = totalPages || 1;
                
                const start = (pageMhs - 1) * perPageMhs;
                const paginated = filtered.slice(start, start + perPageMhs);

                // Build Table
                domTbodyMhs.innerHTML = '';
                if(paginated.length === 0) {
                    domTbodyMhs.innerHTML = `<tr><td colspan="3" class="text-center py-4 text-muted">Tidak ada data mahasiswa</td></tr>`;
                } else {
                    paginated.forEach(mhs => {
                        domTbodyMhs.innerHTML += `
                            <tr>
                                <td class="cell-truncate">
                                    <div class="fw-bold text-dark text-truncate" title="${mhs.nama}">${mhs.nama}</div>
                                    <small class="text-muted text-truncate d-block" title="${mhs.instansi} - ${mhs.prodi}">${mhs.instansi} - ${mhs.prodi}</small>
                                </td>
                                <td class="text-center">
                                    <strong style="color: var(--custom-maroon); font-size: 1rem;">${mhs.score_akhir}</strong>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-maroon rounded-3 shadow-sm" type="button" data-bs-toggle="modal" data-bs-target="#modalDetail${mhs.id}">
                                        <i class="bi bi-eye-fill"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                }

                // Build Pagination
                domInfoMhs.innerText = `Menampilkan ${start + 1} - ${Math.min(start + perPageMhs, totalItems)} dari ${totalItems}`;
                buildPagination(domPageMhs, totalPages, pageMhs, (newPage) => {
                    pageMhs = newPage;
                    renderMahasiswa();
                });
            }

            function buildPagination(container, totalPages, current, callback) {
                container.innerHTML = '';
                if (totalPages <= 1) return;

                // Prev
                container.innerHTML += `<li class="page-item ${current === 1 ? 'disabled' : ''}"><a class="page-link" href="#" data-page="${current - 1}">«</a></li>`;
                
                for (let i = 1; i <= totalPages; i++) {
                    if (i === 1 || i === totalPages || (i >= current - 1 && i <= current + 1)) {
                        container.innerHTML += `<li class="page-item ${i === current ? 'active' : ''}"><a class="page-link" href="#" data-page="${i}">${i}</a></li>`;
                    } else if (i === current - 2 || i === current + 2) {
                        container.innerHTML += `<li class="page-item disabled"><a class="page-link" href="#">..</a></li>`;
                    }
                }

                // Next
                container.innerHTML += `<li class="page-item ${current === totalPages ? 'disabled' : ''}"><a class="page-link" href="#" data-page="${current + 1}">»</a></li>`;

                container.querySelectorAll('a').forEach(a => {
                    a.addEventListener('click', function(e) {
                        e.preventDefault();
                        if(this.closest('.disabled')) return;
                        callback(parseInt(this.getAttribute('data-page')));
                    });
                });
            }

            // Listeners Institusi
            document.getElementById('searchInstitusi').addEventListener('input', function(e) {
                searchInst = e.target.value; pageInst = 1; renderInstitusi();
            });
            document.getElementById('sortInstitusi').addEventListener('change', function(e) {
                sortInst = e.target.value; pageInst = 1; renderInstitusi();
            });

            // Listeners Mahasiswa
            document.getElementById('searchMahasiswa').addEventListener('input', function(e) {
                searchMhs = e.target.value; pageMhs = 1; renderMahasiswa();
            });
            document.getElementById('sortMahasiswa').addEventListener('change', function(e) {
                sortMhs = e.target.value; pageMhs = 1; renderMahasiswa();
            });

            // Initial Render
            renderInstitusi();
            renderMahasiswa();

            // ==========================================
            // CHARTS (Horizontal Bar & Radar)
            // ==========================================
            const chartCanvas = document.getElementById('chartInstitusi');
            if(chartCanvas) {
                const ctx = chartCanvas.getContext('2d');
                new Chart(ctx, {
                    type: 'bar', // Tetap menggunakan tipe 'bar'
                    data: {
                        labels: {!! json_encode($chartInstitusiLabels ?? []) !!},
                        datasets: [{
                            label: 'Skor Akhir',
                            data: {!! json_encode($chartInstitusiScores ?? []) !!},
                            backgroundColor: maroonBg,
                            borderColor: maroonColor,
                            borderWidth: 1.5,
                            borderRadius: 6
                        }]
                    },
                    options: { 
                        indexAxis: 'y', // Konfigurasi khusus untuk mengubah chart menjadi HORIZONTAL
                        responsive: true, 
                        maintainAspectRatio: false, 
                        plugins: { legend: { display: false } },
                        scales: {
                            x: {
                                max: 100, // Karena terbalik, batas 100 pindah ke X
                                min: 0,
                                grid: { color: '#f1f5f9' },
                                border: { dash: [4, 4] }
                            },
                            y: {
                                grid: { display: false }
                            }
                        }
                    }
                });
            }

            const radarCanvas = document.getElementById('chartRadarKriteria');
            if (radarCanvas && {!! json_encode(!empty($chartRadarData)) !!}) {
                const ctxRadar = radarCanvas.getContext('2d');
                new Chart(ctxRadar, {
                    type: 'radar',
                    data: {
                        labels: ['Kedisiplinan', 'Komunikasi', 'Kepatuhan', 'Pemahaman', 'Keterampilan'],
                        datasets: [{
                            label: 'Rata-rata',
                            data: {!! json_encode($chartRadarData ?? []) !!},
                            backgroundColor: 'rgba(124, 19, 22, 0.15)',
                            borderColor: maroonColor,
                            pointBackgroundColor: maroonColor,
                            borderWidth: 2
                        }]
                    },
                    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { r: { suggestedMin: 0, suggestedMax: 100 } } }
                });
            }
        });
    </script>
@endsection