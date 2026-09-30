@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
    <style>
        :root {
            --custom-maroon: #7c1316;
            --custom-maroon-light: #a3191d;
            --custom-maroon-subtle: #fcf0f1;
            --text-dark: #2c3e50;
            --text-muted: #64748b;
            --card-radius: 16px;
            --shadow-soft: 0 4px 20px rgba(0, 0, 0, 0.05);
            --transition: 0.3s ease;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-15px); }
            100% { transform: translateY(0px); }
        }

        .animate-up {
            animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
            opacity: 0;
        }

        /* CARD STATISTIK ADMIN */
        .stat-card {
            background: #fff; border-radius: var(--card-radius);
            box-shadow: var(--shadow-soft); border: none;
            padding: 1.5rem; display: flex; align-items: center; gap: 1.25rem;
            transition: var(--transition); height: 100%;
        }
        .stat-card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(124, 19, 22, 0.1); }
        .stat-icon { width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; flex-shrink: 0; }
        .stat-icon.primary { background-color: var(--custom-maroon-subtle); color: var(--custom-maroon); }
        .stat-icon.success { background-color: #dcfce7; color: #166534; }
        .stat-icon.info { background-color: #dbeafe; color: #1e40af; }
        .stat-icon.warning { background-color: #fef3c7; color: #92400e; }
        .stat-info .stat-title { font-size: 0.85rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; margin-bottom: 0.25rem; }
        .stat-info .stat-value { font-size: 2rem; font-weight: 800; color: var(--text-dark); line-height: 1; }

        .dashboard-card-main {
            background: #fff; border-radius: var(--card-radius);
            box-shadow: var(--shadow-soft); border: none;
            padding: 1.5rem; height: 100%; display: flex; flex-direction: column;
        }
        .chart-container { position: relative; height: 300px; width: 100%; flex-grow: 1; }

        /* ROOM AVAILABILITY CARDS */
        .room-card {
            background: #fff; border: 1px solid #e2e8f0; border-radius: 12px;
            padding: 20px; transition: 0.3s; height: 100%; display: flex; flex-direction: column; justify-content: space-between;
        }
        .room-card:hover { border-color: var(--custom-maroon); box-shadow: 0 10px 20px rgba(124,19,22,0.08); transform: translateY(-3px); }
        .room-title { font-size: 1.1rem; font-weight: 800; color: var(--text-dark); margin-bottom: 5px; }
        .room-badge.tersedia { background-color: #dcfce7; color: #166534; }
        .room-badge.penuh { background-color: #fee2e2; color: #b91c1c; }
        .room-progress { height: 8px; border-radius: 10px; background-color: #e2e8f0; overflow: hidden; margin: 15px 0; }
        .room-progress-bar { height: 100%; border-radius: 10px; }

        /* FULLCALENDAR KUSTOMISASI */
        .fc { font-family: 'Plus Jakarta Sans', sans-serif; }
        .fc-toolbar-title { font-weight: 800 !important; color: var(--text-dark); font-size: 1.25rem !important; text-transform: uppercase; }
        .fc-button-primary { background-color: var(--custom-maroon) !important; border-color: var(--custom-maroon) !important; text-transform: capitalize; font-weight: 600 !important; }
        .fc-button-primary:hover { background-color: var(--custom-maroon-light) !important; }
        .fc-button-active { background-color: #5a0e10 !important; }
        .fc-event { border: none !important; border-radius: 4px; padding: 3px 6px; font-weight: 600; font-size: 0.75rem; cursor: pointer; color: white !important; margin-bottom: 2px; }

        /* WELCOME CARD USER */
        .welcome-card {
            background: linear-gradient(135deg, var(--custom-maroon), #5a0e10);
            color: white; border-radius: 20px; padding: 3rem; position: relative; overflow: hidden; box-shadow: 0 10px 30px rgba(124, 19, 22, 0.3); display: flex; align-items: center; justify-content: space-between;
        }
        .welcome-content { position: relative; z-index: 2; max-width: 60%; }
        .welcome-title { font-size: 2.5rem; font-weight: 800; margin-bottom: 0.5rem; line-height: 1.2; }
        .welcome-text { font-size: 1.1rem; opacity: 0.9; margin-bottom: 2rem; font-weight: 300; }
        .mascot-container { position: relative; z-index: 2; width: 35%; text-align: right; animation: float 4s ease-in-out infinite; }
        .mascot-img { max-width: 250px; filter: drop-shadow(0 15px 15px rgba(0,0,0,0.3)); }
        .bg-shape { position: absolute; border-radius: 50%; background: rgba(255, 255, 255, 0.05); z-index: 1; }
        .shape-1 { width: 300px; height: 300px; top: -100px; right: -50px; }
        .shape-2 { width: 200px; height: 200px; bottom: -50px; right: 200px; }
        .action-card {
            background: white; border-radius: 16px; padding: 25px; text-align: center; box-shadow: var(--shadow-soft); transition: 0.4s; text-decoration: none; color: inherit; display: block; border: 2px solid transparent;
        }
        .action-card:hover { transform: translateY(-10px); border-color: var(--custom-maroon); box-shadow: 0 15px 30px rgba(124, 19, 22, 0.15); }
        .action-icon { width: 70px; height: 70px; margin: 0 auto 15px; background: var(--custom-maroon-subtle); color: var(--custom-maroon); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2rem; transition: 0.3s; }
        .action-card:hover .action-icon { background: var(--custom-maroon); color: white; transform: scale(1.1); }
        .action-title { font-weight: 800; font-size: 1.1rem; margin-bottom: 5px; color: var(--text-dark); }
        .action-desc { font-size: 0.85rem; color: var(--text-muted); margin: 0; }

        @media (max-width: 768px) {
            .welcome-card { flex-direction: column; text-align: center; padding: 2rem; }
            .welcome-content { max-width: 100%; margin-bottom: 2rem; }
            .mascot-container { width: 100%; text-align: center; }
            .mascot-img { max-width: 180px; }
        }
    </style>
  <div class="mb-4">
        <h4 class="fw-bold"></i>Dashboard </h4>
        <p class="text-muted small">Dashboard data pendaftaran, aktivitas dan distribusi sindikat</p>
    </div>

    {{-- ========== 1. DASHBOARD ADMIN ========== --}}
    @if (auth()->check() && auth()->user()->role === 'admin')
          <div class="row g-4 mb-4">
            <div class="col-lg-3 col-md-6 animate-up" style="animation-delay: 0.1s;">
                <div class="stat-card">
                    <div class="stat-icon primary"><i class="bi bi-people-fill"></i></div>
                    <div class="stat-info">
                        <div class="stat-title">Mhs Aktif</div>
                        <div class="stat-value" id="totalMahasiswaEl">{{ $totalMahasiswa }}</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 animate-up" style="animation-delay: 0.2s;">
                <div class="stat-card">
                    <div class="stat-icon success"><i class="bi bi-door-open-fill"></i></div>
                    <div class="stat-info">
                        <div class="stat-title">Total Ruangan</div>
                        <div class="stat-value" id="totalRuanganEl">{{ $totalRuangan }}</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 animate-up" style="animation-delay: 0.3s;">
                <div class="stat-card">
                    <div class="stat-icon info"><i class="bi bi-person-badge"></i></div>
                    <div class="stat-info">
                        <div class="stat-title">Pengguna Sistem</div>
                        <div class="stat-value" id="totalUsersEl">{{ $totalUsers }}</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 animate-up" style="animation-delay: 0.4s;">
                <div class="stat-card">
                    <div class="stat-icon warning"><i class="bi bi-fingerprint"></i></div>
                    <div class="stat-info">
                        <div class="stat-title">Absen Hari Ini</div>
                        <div class="stat-value" id="todayAbsensiEl">{{ $todayAbsensi }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-lg-8 animate-up" style="animation-delay: 0.5s;">
                <div class="dashboard-card-main">
                    <h5 class="fw-bold text-dark mb-4"><i class="bi bi-graph-up text-danger me-2"></i>Tren Pendaftaran (7 Bulan)</h5>
                    <div class="chart-container"><canvas id="mahasiswaChart"></canvas></div>
                </div>
            </div>
            <div class="col-lg-4 animate-up" style="animation-delay: 0.6s;">
                <div class="dashboard-card-main">
                    <h5 class="fw-bold text-dark mb-4"><i class="bi bi-pie-chart-steps text-primary me-2"></i>Distribusi Ruangan</h5>
                    <div class="chart-container"><canvas id="ruanganChart"></canvas></div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-lg-8 animate-up" style="animation-delay: 0.7s;">
                <div class="dashboard-card-main">
                    <h5 class="fw-bold text-dark mb-4"><i class="bi bi-bar-chart-fill text-success me-2"></i>Aktivitas Absensi (7 Hari)</h5>
                    <div class="chart-container"><canvas id="absensiChart"></canvas></div>
                </div>
            </div>
            <div class="col-lg-4 animate-up" style="animation-delay: 0.8s;">
                <div class="dashboard-card-main">
                    <h5 class="fw-bold text-dark mb-4"><i class="bi bi-person-lines-fill text-warning me-2"></i>Status Mahasiswa</h5>
                    <div class="chart-container"><canvas id="statusChart"></canvas></div>
                </div>
            </div>
        </div>

      <div class="row mb-4 animate-up" style="animation-delay: 0.9s;">
            <div class="col-12">
                <div class="dashboard-card-main" style="background-color: #f8fafc;">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3 border-bottom pb-3">
                        <h5 class="fw-bold text-dark mb-0">
                            <i class="bi bi-building-check text-success me-2"></i>Status Ketersediaan Ruangan (Live)
                        </h5>
                        
                        <div class="input-group shadow-sm" style="max-width: 300px;">
                            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" id="searchRoomInput" class="form-control border-start-0" placeholder="Cari nama ruangan...">
                        </div>
                    </div>
                    
                    <div class="row g-3" id="roomCardsContainer">
                        @foreach($roomCards as $rc)
                            <div class="col-md-4 col-xl-3 room-card-wrapper">
                                <div class="room-card shadow-sm">
                                    <div>
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div class="room-title text-truncate" title="{{ $rc['nama'] }}">{{ $rc['nama'] }}</div>
                                            <span class="badge rounded-pill room-badge {{ $rc['status'] === 'Tersedia' ? 'tersedia' : 'penuh' }}">
                                                {{ $rc['status'] }}
                                            </span>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between text-muted" style="font-size: 0.85rem;">
                                            <span>Kuota: <strong>{{ $rc['kuota'] }}</strong></span>
                                            <span>Terisi: <strong>{{ $rc['terisi'] }}</strong></span>
                                        </div>

                                        @php
                                            $persen = $rc['kuota'] > 0 ? ($rc['terisi'] / $rc['kuota']) * 100 : 0;
                                            $bgColor = $persen >= 100 ? '#ef4444' : ($persen > 50 ? '#f59e0b' : '#10b981');
                                        @endphp
                                        <div class="room-progress">
                                            <div class="room-progress-bar" style="width: {{ $persen }}%; background-color: {{ $bgColor }};"></div>
                                        </div>

                                        <div class="text-muted mb-3" style="font-size: 0.8rem;">
                                            <i class="bi bi-calendar-check me-1"></i> Siap diisi tanggal:<br>
                                            <strong class="text-dark">{{ $rc['next_available'] }}</strong>
                                        </div>
                                    </div>
                                    
                                    <button type="button" class="btn btn-sm btn-outline-secondary w-100" 
                                            onclick="showOccupants('{{ $rc['nama'] }}', {{ json_encode($rc['occupants']) }})">
                                        <i class="bi bi-people-fill me-1"></i> Lihat {{ $rc['terisi'] }} Penghuni
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-12 animate-up" style="animation-delay: 1s;">
                <div class="dashboard-card-main">
                    <h5 class="fw-bold text-dark mb-4">
                        <i class="bi bi-calendar-range-fill text-primary me-2"></i> Jadwal Praktik Individual
                    </h5>
                    <div id="calendar" style="min-height: 500px;"></div>
                </div>
            </div>
        </div>



    {{-- ========== 2. DASHBOARD KASIR (BARU) ========== --}}
    @elseif (auth()->check() && auth()->user()->role === 'kasir')
        <div class="row animate-up">
            <div class="col-12 mb-4">
                <div class="welcome-card" style="padding: 2rem;">
                    <div class="welcome-content">
                        <h2 class="fw-bold">Selamat Datang, Kasir Keuangan</h2>
                        <p class="mb-0 opacity-75">Pantau tagihan invoice dan verifikasi pembayaran mahasiswa di sini.</p>
                    </div>
                    <div class="mascot-container">
                        <img src="{{ asset('23.png') }}" class="mascot-img" style="max-height: 120px;">
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-4 animate-up" style="animation-delay: 0.1s;">
                <div class="stat-card">
                    <div class="stat-icon primary"><i class="bi bi-receipt"></i></div>
                    <div class="stat-info">
                        <div class="stat-title">Perlu TTD</div>
                        <div class="stat-value text-danger">{{ \App\Models\Invoice::where('status', 'Perlu TTD')->count() }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 animate-up" style="animation-delay: 0.2s;">
                <div class="stat-card">
                    <div class="stat-icon warning"><i class="bi bi-shield-exclamation"></i></div>
                    <div class="stat-info">
                        <div class="stat-title">Perlu Verifikasi</div>
                        <div class="stat-value text-warning">{{ \App\Models\Invoice::where('status', 'Proses')->count() }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 animate-up" style="animation-delay: 0.3s;">
                <div class="stat-card">
                    <div class="stat-icon success"><i class="bi bi-cash-stack"></i></div>
                    <div class="stat-info">
                        <div class="stat-title">Lunas (Bulan Ini)</div>
                        <div class="stat-value text-success">{{ \App\Models\Invoice::where('status', 'Selesai')->whereMonth('created_at', date('m'))->count() }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 animate-up" style="animation-delay: 0.4s;">
            <div class="col-lg-6">
                <a href="{{ route('admin.invoices.index') }}" class="action-card py-4">
                    <div class="action-icon"><i class="bi bi-receipt-cutoff"></i></div>
                    <h4 class="action-title">Kelola Billing Invoice</h4>
                    <p class="action-desc">Buat, edit, dan tanda tangani invoice mahasiswa.</p>
                </a>
            </div>
            <div class="col-lg-6">
                <a href="{{ route('admin.invoices.report') }}" class="action-card py-4">
                    <div class="action-icon"><i class="bi bi-file-earmark-bar-graph"></i></div>
                    <h4 class="action-title">Laporan Keuangan</h4>
                    <p class="action-desc">Lihat rekap pendapatan dan ekspor ke Excel.</p>
                </a>
            </div>
        </div>

    {{-- ========== 3. DASHBOARD USER / MAHASISWA ========== --}}
    @else
        <div class="row justify-content-center animate-up">
            <div class="col-12 mb-4">
                <div class="welcome-card">
                    <div class="welcome-content">
                        <div class="mb-3">
                            <span class="badge bg-white text-dark px-3 py-2 rounded-pill shadow-sm">
                                <i class="bi bi-brightness-high-fill text-warning me-1"></i> {{ $greeting ?? 'Selamat Datang' }}
                            </span>
                        </div>
                        <h1 class="welcome-title">Halo, {{ auth()->user()->name }}!</h1>
                        <p class="welcome-text">Sindi dan Dika siap membantu aktivitas Anda. Pastikan Anda melakukan presensi harian dan menyelesaikan materi orientasi.</p>
                    </div>
                    <div class="mascot-container">
                        <img src="{{ asset('23.png') }}" alt="Maskot SINDIKAT" class="mascot-img">
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 animate-up" style="animation-delay: 0.3s;">
            <div class="col-lg-6 col-md-6">
                <a href="{{ route('absensi.card', auth()->user()->mahasiswa->share_token ?? 'none') }}" class="action-card">
                    <div class="action-icon"><i class="bi bi-fingerprint"></i></div>
                    <h4 class="action-title">Absensi Harian</h4>
                    <p class="action-desc">Lakukan presensi kedatangan dan kepulangan Anda di sini.</p>
                </a>
            </div>
            <div class="col-lg-6 col-md-6">
                <a href="{{ route('orientasi.index') }}" class="action-card">
                    <div class="action-icon"><i class="bi bi-journal-bookmark-fill"></i></div>
                    <h4 class="action-title">Materi Orientasi</h4>
                    <p class="action-desc">Akses dan selesaikan seluruh modul pembelajaran rumah sakit.</p>
                </a>
            </div>
            <!--<div class="col-lg-4 col-md-6">-->
            <!--    <a href="{{ route('pengajuan.index') }}" class="action-card">-->
            <!--        <div class="action-icon"><i class="bi bi-file-earmark-text"></i></div>-->
            <!--        <h4 class="action-title">Pengajuan Dokumen</h4>-->
            <!--        <p class="action-desc">Cetak sertifikat, invoice, atau unggah dokumen syarat praktik.</p>-->
            <!--    </a>-->
            <!--</div>-->
        </div>
    @endif

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            // Fungsi Pop-Up Penghuni Ruangan (Dipanggil dari tombol di dalam Card)
            function showOccupants(roomName, occupantsArray) {
                let mhsListHTML = '';
                if (occupantsArray && occupantsArray.length > 0) {
                    mhsListHTML = '<ul class="text-start ps-3 mb-0" style="max-height: 250px; overflow-y: auto;">';
                    occupantsArray.forEach(function(nama) {
                        mhsListHTML += `<li class="mb-1">${nama}</li>`;
                    });
                    mhsListHTML += '</ul>';
                } else {
                    mhsListHTML = '<p class="text-muted fst-italic mb-0">Saat ini ruangan kosong (tidak ada mahasiswa).</p>';
                }

                Swal.fire({
                    title: `Penghuni: ${roomName}`,
                    html: `<div class="bg-light p-3 rounded border text-dark text-start">${mhsListHTML}</div>`,
                    icon: 'info',
                    confirmButtonColor: '#7c1316',
                    confirmButtonText: 'Tutup',
                });
            }

            document.addEventListener("DOMContentLoaded", function() {
                
                // Animasi Counter Angka
                function animateValue(id, start, end, duration) {
                    const obj = document.getElementById(id);
                    if (!obj) return;
                    if (end === 0) { obj.innerHTML = "0"; return; }
                    let startTimestamp = null;
                    const step = (timestamp) => {
                        if (!startTimestamp) startTimestamp = timestamp;
                        const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                        obj.innerHTML = Math.floor(progress * (end - start) + start);
                        if (progress < 1) window.requestAnimationFrame(step);
                        else obj.innerHTML = end;
                    };
                    window.requestAnimationFrame(step);
                }

                animateValue("totalMahasiswaEl", 0, {{ $totalMahasiswa ?? 0 }}, 1500);
                animateValue("totalRuanganEl", 0, {{ $totalRuangan ?? 0 }}, 1500);
                animateValue("totalUsersEl", 0, {{ $totalUsers ?? 0 }}, 1500);
                animateValue("todayAbsensiEl", 0, {{ $todayAbsensi ?? 0 }}, 1500);

                // ==========================================
                // INISIALISASI KE-4 GRAFIK (CHART.JS)
                // ==========================================
                Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
                Chart.defaults.color = '#64748b';

                // 1. Line Chart (Pendaftaran)
                if (document.getElementById('mahasiswaChart')) {
                    new Chart(document.getElementById('mahasiswaChart'), {
                        type: 'line',
                        data: {
                            labels: {!! json_encode(array_reverse($months ?? [])) !!},
                            datasets: [{
                                label: 'Mahasiswa Baru',
                                data: {!! json_encode(array_reverse($mahasiswaPerMonth ?? [])) !!},
                                borderColor: '#7c1316', backgroundColor: 'rgba(124, 19, 22, 0.1)',
                                borderWidth: 3, pointBackgroundColor: '#fff', pointBorderColor: '#7c1316', fill: true
                            }]
                        },
                        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
                    });
                }

                // 2. Bar Chart (Absensi)
                if (document.getElementById('absensiChart')) {
                    new Chart(document.getElementById('absensiChart'), {
                        type: 'bar',
                        data: {
                            labels: {!! json_encode(array_reverse($last7Days ?? [])) !!},
                            datasets: [{
                                label: 'Hadir',
                                data: {!! json_encode(array_reverse($absensi7Days ?? [])) !!},
                                backgroundColor: '#10b981', borderRadius: 6
                            }]
                        },
                        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
                    });
                }

               // 3. Horizontal Bar Chart (Distribusi Ruangan)
if (document.getElementById('ruanganChart')) {
    new Chart(document.getElementById('ruanganChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($ruanganLabels ?? []) !!},
            datasets: [{
                label: 'Jumlah Mahasiswa',
                data: {!! json_encode($ruanganData ?? []) !!},
                // Bisa pakai warna-warni atau satu warna utama
                backgroundColor: ['#7c1316', '#1e40af', '#eab308', '#22c55e', '#a855f7', '#f97316'],
                borderRadius: 4
            }]
        },
        options: { 
            responsive: true, 
            maintainAspectRatio: false, 
            indexAxis: 'y', // Kunci untuk membuatnya menjadi horizontal
            plugins: { 
                legend: { display: false } // Sembunyikan legenda karena tidak perlu
            },
            scales: {
                x: { 
                    beginAtZero: true,
                    ticks: { stepSize: 1 } // Agar angkanya bulat (1, 2, 3) bukan desimal
                },
                y: { 
                    grid: { display: false } // Hilangkan garis bantu horizontal agar rapi
                }
            }
        }
    });
}

                // 4. Pie Chart (Status)
                if (document.getElementById('statusChart')) {
                    new Chart(document.getElementById('statusChart'), {
                        type: 'pie',
                        data: {
                            labels: {!! json_encode($statusLabels ?? []) !!},
                            datasets: [{
                                data: {!! json_encode($statusData ?? []) !!},
                                backgroundColor: ['#22c55e', '#eab308', '#ef4444', '#64748b'],
                                borderWidth: 0
                            }]
                        },
                        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
                    });
                }

                // ==========================================
                // INISIALISASI KALENDER JADWAL MAHASISWA
                // ==========================================
               // ==========================================
                // INISIALISASI KALENDER JADWAL MAHASISWA
                // ==========================================
                var calendarEl = document.getElementById('calendar');
                if (calendarEl) {
                    var calendar = new FullCalendar.Calendar(calendarEl, {
                        initialView: 'dayGridMonth',
                        locale: 'id',
                        headerToolbar: {
                            left: 'prev,next today',
                            center: 'title',
                            // Tambahkan multiMonthYear di sini:
                            right: 'dayGridMonth,dayGridWeek,multiMonthYear'
                        },
                        events: {!! json_encode($calendarEvents ?? []) !!},
                        eventDisplay: 'block',
                        eventClick: function(info) {
                            let startDate = info.event.start.toLocaleDateString('id-ID', {day:'numeric', month:'long', year:'numeric'});
                            let endDate = info.event.end ? new Date(info.event.end.getTime() - 86400000).toLocaleDateString('id-ID', {day:'numeric', month:'long', year:'numeric'}) : startDate;

                            Swal.fire({
                                title: info.event.title,
                                html: `
                                    <div class="text-start mt-3 px-3 py-2 bg-light rounded border">
                                        <p class="mb-2"><i class="bi bi-building me-2 text-danger"></i><strong>Ruangan:</strong> ${info.event.extendedProps.ruangan}</p>
                                        <p class="mb-2"><i class="bi bi-mortarboard-fill me-2 text-primary"></i><strong>Institusi:</strong> ${info.event.extendedProps.institusi}</p>
                                        <hr class="my-2">
                                        <p class="mb-1"><i class="bi bi-calendar-event me-2 text-success"></i><strong>Mulai:</strong> ${startDate}</p>
                                        <p class="mb-0"><i class="bi bi-calendar-check-fill me-2 text-warning"></i><strong>Selesai:</strong> ${endDate}</p>
                                    </div>
                                `,
                                icon: 'info',
                                confirmButtonColor: '#7c1316',
                                confirmButtonText: 'Tutup'
                            });
                        }
                    });
                    calendar.render();
                }

                // ==========================================
                // FITUR LIVE SEARCH RUANGAN
                // ==========================================
                const searchInput = document.getElementById('searchRoomInput');
                if(searchInput) {
                    searchInput.addEventListener('keyup', function() {
                        let filter = this.value.toLowerCase();
                        let cards = document.querySelectorAll('.room-card-wrapper');

                        cards.forEach(function(card) {
                            // Ambil teks dari nama ruangan (class room-title)
                            let title = card.querySelector('.room-title').innerText.toLowerCase();
                            
                            // Jika cocok, tampilkan. Jika tidak, sembunyikan (display none)
                            if (title.includes(filter)) {
                                card.style.display = '';
                            } else {
                                card.style.display = 'none';
                            }
                        });
                    });
                }
            });
        </script>
@endsection