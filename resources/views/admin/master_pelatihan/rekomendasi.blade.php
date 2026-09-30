@extends('layouts.app')

@section('content')
    {{--
      =====================================================
      STYLE KUSTOM (Disamakan dengan Template MOU Sindikat)
      =====================================================
    --}}
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

        /* Reusable Classes dari Template MOU */
        .page-header-wrapper { background: #fff; border-radius: var(--card-radius); padding: 1.5rem; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03); margin-bottom: 2rem; border-left: 5px solid var(--custom-maroon); position: relative; z-index: 99; overflow: visible; }
        .filter-card, .custom-table-card { background: #fff; border-radius: var(--card-radius); box-shadow: var(--shadow-soft); margin-bottom: 1.5rem; border: 1px solid #f0f0f0; }
        .filter-header { background: var(--custom-maroon-subtle); color: var(--custom-maroon); padding: 1rem 1.5rem; border-radius: var(--card-radius) var(--card-radius) 0 0; font-weight: 600; }
        .btn-maroon { background-color: var(--custom-maroon); color: #fff; border: none; border-radius: 8px; padding: 8px 16px; font-weight: 500; transition: var(--transition); }
        .btn-maroon:hover { background-color: var(--custom-maroon-light); color: #fff; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(124, 19, 22, 0.3); }
        
        .animate-up { animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards; opacity: 0; transform: translateY(20px); }
        @keyframes fadeInUp { to { opacity: 1; transform: translateY(0); } }

        /* TNA Specific Styles */
        .search-input { border-radius: 8px; border: 1px solid #e0e0e0; padding: 12px 20px 12px 45px; width: 100%; transition: var(--transition); }
        .search-input:focus { border-color: var(--custom-maroon); outline: none; box-shadow: 0 0 0 3px var(--custom-maroon-subtle); }
        .search-wrapper { position: relative; }
        .search-wrapper i { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: var(--text-muted); }
        
        .score-circle { width: 45px; height: 45px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 1.1rem; flex-shrink: 0; }
        .score-danger { background: #fee2e2; color: #dc2626; border: 1px solid #fca5a5; }
        .score-warning { background: #fef3c7; color: #d97706; border: 1px solid #fcd34d; }
        .score-success { background: #d1fae5; color: #059669; border: 1px solid #6ee7b7; }

        .module-item { background: #f8f9fa; border-left: 4px solid #198754; border-radius: 6px; padding: 10px 15px; margin-bottom: 8px; display: flex; justify-content: space-between; align-items: center; transition: var(--transition); }
        .module-item:hover { background: #e9ecef; }
        .info-icon-hover { cursor: help; color: #6c757d; }
        .info-icon-hover:hover { color: var(--custom-maroon); }
    </style>

    <!-- 1. HEADER (Sesuai Gaya MOU) -->
    <div class="d-flex justify-content-between align-items-center page-header-wrapper animate-up">
        <div>
            <h4 class="fw-bold mb-0 text-dark">Analisis Kebutuhan Pelatihan (TNA)</h4>
            <small class="text-muted">Pemetaan gap kompetensi berbasis Indeks Kepuasan Masyarakat (IKM).</small>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <button type="button" class="btn btn-outline-secondary shadow-sm d-flex align-items-center gap-2 rounded-3" data-bs-toggle="modal" data-bs-target="#modalLegenda">
                <i class="bi bi-info-circle-fill text-primary"></i> Panduan Membaca Data
            </button>
        </div>
    </div>

    <!-- 2. CHARTS VISUALIZATION -->
    <div class="row g-4 mb-4">
        <!-- Bar Chart -->
        <div class="col-lg-7">
            <div class="custom-table-card p-4 animate-up" style="animation-delay: 0.1s; height: 100%;">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h6 class="fw-bold m-0" style="color: var(--custom-maroon);">5 Area Paling Kritis (Global)</h6>
                    <i class="bi bi-question-circle info-icon-hover" data-bs-toggle="tooltip" title="Grafik ini menampilkan 5 unsur dengan nilai IKM terendah dari seluruh unit rumah sakit. Semakin rendah, semakin prioritas."></i>
                </div>
                <div style="position: relative; height: 250px;">
                    <canvas id="barChart"></canvas>
                </div>
            </div>
        </div>
        <!-- Doughnut Chart -->
        <div class="col-lg-5">
            <div class="custom-table-card p-4 animate-up" style="animation-delay: 0.2s; height: 100%;">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h6 class="fw-bold m-0" style="color: var(--custom-maroon);">Rata-rata Kinerja Unit</h6>
                    <i class="bi bi-question-circle info-icon-hover" data-bs-toggle="tooltip" title="Rata-rata skor seluruh unsur IKM di masing-masing unit layanan."></i>
                </div>
                <div style="position: relative; height: 250px;">
                    <canvas id="doughnutChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. FILTER / PENCARIAN -->
    <div class="filter-card animate-up" style="animation-delay: 0.3s;">
        <div class="filter-header d-flex justify-content-between align-items-center">
            <span><i class="bi bi-search me-2"></i> Cari Prioritas Pelatihan</span>
            <span class="badge bg-white text-maroon shadow-sm" style="color: var(--custom-maroon);">Live Search</span>
        </div>
        <div class="card-body p-3">
            <div class="search-wrapper">
                <i class="bi bi-search"></i>
                <input type="text" id="liveSearch" class="search-input text-dark" placeholder="Ketik nama ruangan (misal: Rawat Jalan) atau nama pelatihan...">
            </div>
        </div>
    </div>

    <!-- 4. DAFTAR REKOMENDASI PER UNIT -->
    <div class="row g-4" id="unitContainer">
        @forelse($rekomendasiPerUnit as $unitName => $dataPrioritas)
        <div class="col-lg-6 unit-card animate-up" style="animation-delay: 0.4s;">
            <div class="custom-table-card h-100">
                <!-- Unit Header -->
                <div class="p-3 border-bottom d-flex align-items-center" style="background: #fafafa;">
                    <div class="bg-white shadow-sm rounded p-2 me-3" style="color: var(--custom-maroon);">
                        <i class="bi bi-hospital fs-5"></i>
                    </div>
                    <h5 class="m-0 fw-bold text-dark">{{ $unitName }}</h5>
                </div>
                
                <!-- Unit Body -->
                <div class="p-4">
                    @foreach($dataPrioritas as $rek)
                        @php
                            $skor = $rek['skor_rata2'];
                            $skorClass = $skor < 2.5 ? 'score-danger' : ($skor < 3.2 ? 'score-warning' : 'score-success');
                            $statusTeks = $skor < 2.5 ? 'Sangat Kritis' : ($skor < 3.2 ? 'Perlu Perbaikan' : 'Cukup Baik');
                        @endphp
                        
                        <div class="mb-4 {{ !$loop->last ? 'border-bottom pb-4' : '' }}">
                            <div class="d-flex align-items-start mb-3">
                                <div class="score-circle {{ $skorClass }} me-3 shadow-sm" data-bs-toggle="tooltip" title="Status: {{ $statusTeks }} (Skor Max: 4.00)">
                                    {{ $skor }}
                                </div>
                                <div>
                                    <span class="badge bg-light text-secondary border mb-1">{{ $rek['kode_unsur'] }}</span>
                                    <h6 class="fw-bold text-dark mb-1" style="font-size: 0.95rem; line-height: 1.4;">{{ $rek['pertanyaan'] }}</h6>
                                </div>
                            </div>
                            
                            <div class="ps-5">
                                <p class="small text-muted fw-bold mb-2"><i class="bi bi-journal-arrow-up me-1"></i> Rekomendasi Modul:</p>
                                @forelse($rek['kebutuhan_pelatihan'] as $pl)
                                    <div class="module-item">
                                        <div>
                                            <div class="small fw-bold text-dark">{{ $pl['judul'] }}</div>
                                            <div style="font-size: 0.7rem;" class="text-muted"><i class="bi bi-tags"></i> Kategori: {{ $pl['kategori'] }}</div>
                                        </div>
                                        <span class="badge bg-success shadow-sm info-icon-hover text-white" data-bs-toggle="tooltip" data-bs-placement="left" title="Tingkat Kesesuaian Sistem. Dihitung berdasarkan kategori & kata kunci modul.">
                                            {{ $pl['skor_cocok'] }} Poin
                                        </span>
                                    </div>
                                @empty
                                    <div class="p-2 bg-light rounded text-center small text-muted fst-italic border">
                                        - Belum ada modul yang sesuai di Master Pelatihan -
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5 animate-up">
            <i class="bi bi-inbox display-4 text-muted" style="opacity: 0.5;"></i>
            <h5 class="text-muted fw-bold mt-3">Tidak ada data survei</h5>
            <p class="text-muted small">Silakan sesuaikan filter tanggal atau tunggu data survei masuk.</p>
        </div>
        @endforelse
    </div>

    <!-- MODAL LEGENDA / PANDUAN MEMBACA DATA -->
    <div class="modal fade" id="modalLegenda" tabindex="-1" aria-labelledby="modalLegendaLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content" style="border-radius: var(--card-radius); border: none;">
                <div class="modal-header" style="background: var(--custom-maroon); color: white; border-radius: var(--card-radius) var(--card-radius) 0 0;">
                    <h5 class="modal-title fw-bold" id="modalLegendaLabel"><i class="bi bi-book me-2"></i> Panduan Membaca Data TNA</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <h6 class="fw-bold border-bottom pb-2 mb-3 text-dark">1. Darimana Angka Skor Berasal?</h6>
                    <p class="small text-muted mb-4">
                        Angka skor (misal: <span class="badge bg-danger">2.45</span>) adalah <strong>Nilai Rata-rata IKM (Indeks Kepuasan Masyarakat)</strong> untuk unsur pertanyaan tertentu di sebuah ruangan. Skor maksimal adalah <strong>4.00</strong>. Sistem akan otomatis mencari 3 skor terendah di tiap ruangan untuk dijadikan prioritas pelatihan.
                    </p>

                    <h6 class="fw-bold border-bottom pb-2 mb-3 text-dark">2. Arti Warna Skor</h6>
                    <div class="d-flex align-items-center mb-2">
                        <div class="score-circle score-danger me-3" style="width: 35px; height: 35px; font-size: 0.9rem;">1.0</div>
                        <div><strong class="text-danger">Sangat Kritis (Skor 1.00 - 2.49):</strong> Wajib segera diberikan intervensi diklat.</div>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                        <div class="score-circle score-warning me-3" style="width: 35px; height: 35px; font-size: 0.9rem;">2.8</div>
                        <div><strong class="text-warning">Perlu Perbaikan (Skor 2.50 - 3.19):</strong> Disarankan untuk optimalisasi kompetensi.</div>
                    </div>
                    <div class="d-flex align-items-center mb-4">
                        <div class="score-circle score-success me-3" style="width: 35px; height: 35px; font-size: 0.9rem;">3.5</div>
                        <div><strong class="text-success">Cukup Baik (Skor 3.20 - 4.00):</strong> Pelayanan sudah memenuhi standar IKM.</div>
                    </div>

                    <h6 class="fw-bold border-bottom pb-2 mb-3 text-dark">3. Cara Sistem Mencocokkan Modul (Poin Match)</h6>
                    <p class="small text-muted mb-0">
                        Sistem AI sederhana kami mencocokkan masalah IKM dengan <strong>Master Pelatihan</strong> menggunakan pembobotan:
                        <br>✅ <strong>+5 Poin</strong> jika kategori pelatihan sesuai dengan masalah (misal masalah pelayanan -> pelatihan kategori pelayanan).
                        <br>✅ <strong>+3 Poin</strong> jika terdapat kata kunci di judul pelatihan (misal masalah antrian -> judul pelatihan ada kata "triage").
                        <br>❌ <strong>Ditolak</strong> jika materi sangat tidak relevan (misal satpam disarankan pelatihan bedah klinis).
                    </p>
                </div>
                <div class="modal-footer bg-light" style="border-radius: 0 0 var(--card-radius) var(--card-radius);">
                    <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">Tutup Panduan</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // 1. Inisialisasi Tooltip Bootstrap (Wajib untuk Hover Info)
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // 2. Fitur Live Search (Mencari di dalam Card tanpa Reload)
            const searchInput = document.getElementById('liveSearch');
            if (searchInput) {
                searchInput.addEventListener('keyup', function() {
                    let searchValue = this.value.toLowerCase();
                    let unitCards = document.querySelectorAll('.unit-card');

                    unitCards.forEach(card => {
                        let textContent = card.innerText.toLowerCase();
                        if (textContent.includes(searchValue)) {
                            card.style.display = ''; 
                        } else {
                            card.style.display = 'none'; 
                        }
                    });
                });
            }

            // 3. Render Chart 1: Bar Chart (Area Paling Kritis)
            const ctxBar = document.getElementById('barChart');
            if(ctxBar) {
                new Chart(ctxBar.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($chartLabels ?? []) !!},
                        datasets: [{
                            label: 'Skor IKM (Makin kecil makin kritis)',
                            data: {!! json_encode($chartScores ?? []) !!},
                            backgroundColor: '#7c1316',
                            borderRadius: 4
                        }]
                    },
                    options: { 
                        responsive: true,
                        maintainAspectRatio: false,
                        indexAxis: 'y', 
                        plugins: { legend: { display: false } }, 
                        scales: { x: { max: 4, grid: { display: false } }, y: { grid: { display: false } } } 
                    }
                });
            }

            // 4. Render Chart 2: Doughnut Chart (Rata-rata Unit)
            const ctxDoughnut = document.getElementById('doughnutChart');
            if(ctxDoughnut) {
                new Chart(ctxDoughnut.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: {!! json_encode($unitLabels ?? []) !!},
                        datasets: [{
                            data: {!! json_encode($unitScores ?? []) !!},
                            backgroundColor: ['#7c1316', '#a3191d', '#d97706', '#059669', '#2563eb', '#6b7280', '#475569'],
                            borderWidth: 2,
                            borderColor: '#ffffff'
                        }]
                    },
                    options: { 
                        responsive: true, 
                        maintainAspectRatio: false,
                        plugins: { legend: { position: 'right', labels: { usePointStyle: true, boxWidth: 10 } } },
                        cutout: '65%'
                    }
                });
            }
        });
    </script>
@endsection