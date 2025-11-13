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

        /* --- Animasi Fade In --- */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-up {
            animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
            opacity: 0;
        }

        /* --- Kartu Statistik --- */
        .stat-card {
            background: #fff;
            border-radius: var(--card-radius);
            box-shadow: var(--shadow-soft);
            border: none;
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1.25rem;
            transition: var(--transition);
            height: 100%;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(124, 19, 22, 0.1);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            flex-shrink: 0;
        }

        .stat-icon.primary {
            background-color: var(--custom-maroon-subtle);
            color: var(--custom-maroon);
        }

        .stat-icon.success {
            background-color: #dcfce7;
            color: #166534;
        }

        .stat-icon.info {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .stat-icon.warning {
            background-color: #fef3c7;
            color: #92400e;
        }

        .stat-info .stat-title {
            font-size: 0.85rem;
            color: var(--text-muted);
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 0.25rem;
        }

        .stat-info .stat-value {
            font-size: 2rem;
            font-weight: 800;
            color: var(--text-dark);
            line-height: 1;
        }

        /* --- Kartu Chart --- */
        .dashboard-card-main {
            background: #fff;
            border-radius: var(--card-radius);
            box-shadow: var(--shadow-soft);
            border: none;
            padding: 1.5rem;
            height: 100%;
        }

        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }
    </style>

    <div class="row g-4 mb-4">
        <div class="col-lg-3 col-md-6 animate-up" style="animation-delay: 0.1s;">
            <div class="stat-card">
                <div class="stat-icon primary"><i class="bi bi-people-fill"></i></div>
                <div class="stat-info">
                    <div class="stat-title">Total Mahasiswa</div>
                    <div class="stat-value" id="totalMahasiswaEl">0</div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 animate-up" style="animation-delay: 0.2s;">
            <div class="stat-card">
                <div class="stat-icon success"><i class="bi bi-door-open-fill"></i></div>
                <div class="stat-info">
                    <div class="stat-title">Total Ruangan</div>
                    <div class="stat-value" id="totalRuanganEl">0</div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 animate-up" style="animation-delay: 0.3s;">
            <div class="stat-card">
                <div class="stat-icon info"><i class="bi bi-person-badge"></i></div>
                <div class="stat-info">
                    <div class="stat-title">Total Pengguna</div>
                    <div class="stat-value" id="totalUsersEl">0</div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 animate-up" style="animation-delay: 0.4s;">
            <div class="stat-card">
                <div class="stat-icon warning"><i class="bi bi-calendar-check"></i></div>
                <div class="stat-info">
                    <div class="stat-title">Absen Hari Ini</div>
                    <div class="stat-value" id="todayAbsensiEl">0</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8 animate-up" style="animation-delay: 0.5s;">
            <div class="dashboard-card-main">
                <h5 class="fw-bold text-dark mb-4">Statistik Pendaftaran (7 Bulan Terakhir)</h5>
                <div class="chart-container">
                    <canvas id="mahasiswaChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4 animate-up" style="animation-delay: 0.6s;">
            <div class="dashboard-card-main">
                <h5 class="fw-bold text-dark mb-4">Distribusi Ruangan</h5>
                <div class="chart-container">
                    <canvas id="ruanganChart"></canvas>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {

            // --- 1. INJEKSI DATA DARI CONTROLLER KE JS ---
            // Pastikan Controller mengirim variable ini dengan benar
            const dbData = {
                totalMahasiswa: {{ $totalMahasiswa ?? 0 }},
                totalRuangan: {{ $totalRuangan ?? 0 }},
                totalUsers: {{ $totalUsers ?? 0 }},
                todayAbsensi: {{ $todayAbsensi ?? 0 }},

                // Data Grafik (Array)
                months: {!! json_encode($months ?? []) !!},
                mahasiswaData: {!! json_encode($mahasiswaPerMonth ?? []) !!},

                ruanganLabels: {!! json_encode($ruanganLabels ?? []) !!},
                ruanganData: {!! json_encode($ruanganData ?? []) !!}
            };

            console.log("Dashboard Data Loaded:", dbData); // Cek console browser untuk debug

            // --- 2. ANIMASI ANGKA (Count Up) ---
            function animateValue(id, start, end, duration) {
                const obj = document.getElementById(id);
                if (!obj) return;
                if (end === 0) {
                    obj.innerHTML = "0";
                    return;
                } // Handle 0 value

                let startTimestamp = null;
                const step = (timestamp) => {
                    if (!startTimestamp) startTimestamp = timestamp;
                    const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                    obj.innerHTML = Math.floor(progress * (end - start) + start);
                    if (progress < 1) {
                        window.requestAnimationFrame(step);
                    } else {
                        obj.innerHTML = end; // Pastikan angka akhir pas
                    }
                };
                window.requestAnimationFrame(step);
            }

            animateValue("totalMahasiswaEl", 0, dbData.totalMahasiswa, 1500);
            animateValue("totalRuanganEl", 0, dbData.totalRuangan, 1500);
            animateValue("totalUsersEl", 0, dbData.totalUsers, 1500);
            animateValue("todayAbsensiEl", 0, dbData.todayAbsensi, 1500);

            // --- 3. RENDER GRAFIK MAHASISWA (Line Chart) ---
            const ctxMhs = document.getElementById('mahasiswaChart');
            if (ctxMhs && dbData.months.length > 0) {
                new Chart(ctxMhs, {
                    type: 'line',
                    data: {
                        labels: dbData.months,
                        datasets: [{
                            label: 'Mahasiswa Baru',
                            data: dbData.mahasiswaData,
                            borderColor: '#7c1316', // Maroon
                            backgroundColor: 'rgba(124, 19, 22, 0.1)',
                            borderWidth: 3,
                            pointBackgroundColor: '#fff',
                            pointBorderColor: '#7c1316',
                            pointRadius: 5,
                            tension: 0.4, // Garis melengkung halus
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    borderDash: [2, 4]
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            } else if (ctxMhs) {
                // Pesan jika data kosong
                ctxMhs.parentNode.innerHTML =
                    '<div class="d-flex align-items-center justify-content-center h-100 text-muted">Belum ada data statistik.</div>';
            }

            // --- 4. RENDER GRAFIK RUANGAN (Doughnut Chart) ---
            const ctxRuangan = document.getElementById('ruanganChart');
            if (ctxRuangan && dbData.ruanganData.length > 0) {
                new Chart(ctxRuangan, {
                    type: 'doughnut',
                    data: {
                        labels: dbData.ruanganLabels,
                        datasets: [{
                            data: dbData.ruanganData,
                            backgroundColor: [
                                '#7c1316', '#a3191d', '#eab308', '#22c55e', '#3b82f6', '#64748b'
                            ],
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    usePointStyle: true,
                                    padding: 20
                                }
                            }
                        },
                        cutout: '75%' // Lubang tengah
                    }
                });
            } else if (ctxRuangan) {
                ctxRuangan.parentNode.innerHTML =
                    '<div class="d-flex align-items-center justify-content-center h-100 text-muted">Belum ada data ruangan.</div>';
            }
        });
    </script>
@endsection
