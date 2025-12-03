@extends('layouts.app')

@section('title', 'Dashboard Magang')
@section('page-title', 'Dashboard Magang')

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

        /* --- Profile Card --- */
        .profile-card {
            background: #fff;
            border: none;
            border-radius: var(--card-radius);
            box-shadow: var(--shadow-soft);
            overflow: hidden;
        }

        .profile-header {
            background: linear-gradient(135deg, var(--custom-maroon), #5a0e10);
            height: 120px;
            position: relative;
        }

        .profile-avatar-container {
            position: absolute;
            bottom: -50px;
            left: 50%;
            transform: translateX(-50%);
        }

        .profile-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 4px solid #fff;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            background-color: #f8f9fa;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .profile-avatar img {
            width: 100%; height: 100%; object-fit: cover;
        }

        .avatar-initial {
            font-size: 2.5rem; font-weight: bold; color: var(--custom-maroon);
        }

        .profile-body {
            padding: 60px 1.5rem 1.5rem; /* Padding top besar untuk avatar */
            text-align: center;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 0.8rem 0;
            border-bottom: 1px solid #f1f5f9;
            text-align: left;
        }
        .info-item:last-child { border-bottom: none; }

        .info-label { font-size: 0.85rem; color: var(--text-muted); font-weight: 600; display: flex; align-items: center; gap: 8px; }
        .info-value { font-size: 0.9rem; color: var(--text-dark); font-weight: 600; text-align: right; max-width: 60%; }

        /* --- Stat Cards --- */
        .stat-card {
            border: none;
            border-radius: var(--card-radius);
            color: white;
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow-soft);
            transition: transform 0.3s;
            height: 100%;
        }
        .stat-card:hover { transform: translateY(-5px); }

        .bg-gradient-blue { background: linear-gradient(135deg, #3b82f6, #2563eb); }
        .bg-gradient-green { background: linear-gradient(135deg, #10b981, #059669); }
        .bg-gradient-orange { background: linear-gradient(135deg, #f59e0b, #d97706); }

        .stat-icon-bg {
            position: absolute; right: -10px; bottom: -10px;
            font-size: 5rem; opacity: 0.2; transform: rotate(-15deg);
        }

        /* --- Table & Chart Cards --- */
        .content-card {
            background: #fff;
            border: none;
            border-radius: var(--card-radius);
            box-shadow: var(--shadow-soft);
            overflow: hidden;
            margin-bottom: 1.5rem;
        }
        .content-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #f1f5f9;
            display: flex; justify-content: space-between; align-items: center;
        }
        .content-title { font-weight: 700; color: var(--custom-maroon); margin: 0; font-size: 1.1rem; }

        /* --- Buttons --- */
        .btn-back {
            background: white; border: 1px solid #e2e8f0; color: var(--text-dark);
            padding: 0.5rem 1.2rem; border-radius: 8px; font-weight: 500; text-decoration: none;
            display: inline-flex; align-items: center; gap: 8px; transition: 0.3s;
        }
        .btn-back:hover { background: #f8f9fa; color: var(--custom-maroon); border-color: var(--custom-maroon); }

        .btn-qr {
            background: var(--custom-maroon-subtle); color: var(--custom-maroon);
            border: none; padding: 0.5rem 1rem; border-radius: 8px; font-weight: 600; text-decoration: none;
            display: inline-flex; align-items: center; gap: 6px; transition: 0.3s;
        }
        .btn-qr:hover { background: #edd5d5; color: #5a0e10; }

        /* --- Badges --- */
        .badge-soft-success { background: #dcfce7; color: #166534; padding: 5px 10px; border-radius: 6px; font-weight: 600; font-size: 0.75rem; }
        .badge-soft-warning { background: #fef9c3; color: #854d0e; padding: 5px 10px; border-radius: 6px; font-weight: 600; font-size: 0.75rem; }
        .badge-soft-danger { background: #fee2e2; color: #991b1b; padding: 5px 10px; border-radius: 6px; font-weight: 600; font-size: 0.75rem; }

        .animate-up { animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards; opacity: 0; transform: translateY(20px); }
        @keyframes fadeInUp { to { opacity: 1; transform: translateY(0); } }
    </style>

    {{-- Back Button --}}
    <div class="d-flex justify-content-between align-items-center mb-4 animate-up">
        <div>
            <h4 class="fw-bold mb-0 text-dark">Dashboard Mahasiswa</h4>
            <small class="text-muted">Pantau aktivitas dan statistik magang.</small>
        </div>
        <a href="{{ route('mahasiswa.index') }}" class="btn-back">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    {{-- Alerts --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show animate-up shadow-sm border-0 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">

        {{-- KOLOM KIRI: Profil --}}
        <div class="col-lg-4 animate-up" style="animation-delay: 0.1s;">
            <div class="profile-card h-100">
                <div class="profile-header">
                    <div class="profile-avatar-container">
                        <div class="profile-avatar">
                            @if($mahasiswa->foto_path)
                                <img src="{{ asset($mahasiswa->foto_path) }}" alt="{{ $mahasiswa->nm_mahasiswa }}">
                            @else
                                <div class="avatar-initial">
                                    {{ strtoupper(substr($mahasiswa->nm_mahasiswa, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="profile-body">
                    <h5 class="fw-bold text-dark mb-1">{{ $mahasiswa->nm_mahasiswa }}</h5>
                    <p class="text-muted small mb-4">{{ $mahasiswa->mou ? ($mahasiswa->mou->nama_instansi ?? $mahasiswa->mou->nama_universitas) : 'Instansi Tidak Diketahui' }}</p>

                    <div class="info-item">
                        <span class="info-label"><i class="bi bi-book"></i> Prodi</span>
                        <span class="info-value">{{ $mahasiswa->prodi }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label"><i class="bi bi-door-open"></i> Ruangan</span>
                        <span class="info-value text-danger">{{ $mahasiswa->nm_ruangan ?? '-' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label"><i class="bi bi-calendar-range"></i> Periode</span>
                        <span class="info-value">
                            {{ \Carbon\Carbon::parse($mahasiswa->tanggal_mulai)->format('d M Y') }} -
                            {{ \Carbon\Carbon::parse($mahasiswa->tanggal_berakhir)->format('d M Y') }}
                        </span>
                    </div>

                    <div class="mt-4 text-start p-3 bg-light rounded-3">
                        <small class="text-muted fw-bold text-uppercase d-block mb-2">Pembimbing</small>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="bi bi-person-badge text-secondary"></i>
                            <span class="text-dark fw-bold">{{ $mahasiswa->dosen_pembimbing ?? '-' }}</span>
                        </div>
                        <a href="tel:{{ $mahasiswa->no_hp_dospem }}" class="text-decoration-none small text-success">
                            <i class="bi bi-whatsapp me-1"></i> Hubungi Pembimbing
                        </a>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('mahasiswa.edit', $mahasiswa->id) }}" class="btn btn-outline-secondary w-100 rounded-pill">
                            <i class="bi bi-pencil-square me-1"></i> Edit Profil
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: Statistik & Grafik --}}
        <div class="col-lg-8">

            {{-- 1. Statistik Cards --}}
            <div class="row g-3 mb-4 animate-up" style="animation-delay: 0.2s;">
                <div class="col-md-4">
                    <div class="stat-card bg-gradient-blue">
                        <div class="position-relative z-1">
                            <h2 class="fw-bold mb-0">{{ $totalHari }}</h2>
                            <small class="opacity-75">Total Hari Magang</small>
                        </div>
                        <i class="bi bi-calendar-week stat-icon-bg"></i>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card bg-gradient-green">
                        <div class="position-relative z-1">
                            <h2 class="fw-bold mb-0">{{ $totalHadir }}</h2>
                            <small class="opacity-75">Total Kehadiran</small>
                        </div>
                        <i class="bi bi-person-check stat-icon-bg"></i>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card bg-gradient-orange">
                        <div class="position-relative z-1">
                            <h2 class="fw-bold mb-0">{{ $persentase }}%</h2>
                            <small class="opacity-75">Persentase Hadir</small>
                        </div>
                        <i class="bi bi-graph-up-arrow stat-icon-bg"></i>
                    </div>
                </div>
            </div>

            {{-- 2. Tabel Riwayat Absensi --}}
            <div class="content-card animate-up" style="animation-delay: 0.3s;">
                <div class="content-header">
                    <h5 class="content-title"><i class="bi bi-clock-history me-2"></i>Riwayat Absensi Terbaru</h5>
                    <a href="{{ route('absensi.card', $mahasiswa->share_token) }}" target="_blank" class="btn-qr">
                        <i class="bi bi-qr-code"></i> Kartu Absen
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Tanggal</th>
                                <th class="text-center">Jam Masuk</th>
                                <th class="text-center">Jam Keluar</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($absensi->take(5) as $abs) {{-- Ambil 5 saja agar tidak kepanjangan --}}
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-dark">{{ \Carbon\Carbon::parse($abs->created_at)->format('d M Y') }}</div>
                                        <small class="text-muted">{{ \Carbon\Carbon::parse($abs->created_at)->format('l') }}</small>
                                    </td>
                                    <td class="text-center">
                                        @if ($abs->jam_masuk)
                                            <span class="text-success fw-bold">{{ $abs->jam_masuk }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($abs->jam_keluar)
                                            <span class="text-danger fw-bold">{{ $abs->jam_keluar }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($abs->jam_masuk && $abs->jam_keluar)
                                            <span class="badge-soft-success">Selesai</span>
                                        @elseif ($abs->jam_masuk)
                                            <span class="badge-soft-warning">Belum Keluar</span>
                                        @else
                                            <span class="badge-soft-danger">Alpha</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        <i class="bi bi-inbox mb-2 d-block fs-4 opacity-50"></i>
                                        Belum ada data absensi.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($absensi->count() > 5)
                    <div class="p-2 text-center border-top bg-light">
                        <a href="#" class="text-decoration-none small fw-bold text-muted">Lihat Semua Riwayat <i class="bi bi-arrow-right"></i></a>
                    </div>
                @endif
            </div>

            {{-- 3. Grafik Kehadiran --}}
            <div class="content-card animate-up" style="animation-delay: 0.4s;">
                <div class="content-header">
                    <h5 class="content-title"><i class="bi bi-bar-chart-line me-2"></i>Grafik Aktivitas</h5>
                </div>
                <div class="p-4">
                    <div style="height: 250px; width: 100%;">
                        <canvas id="kehadiranChart"></canvas>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const ctx = document.getElementById('kehadiranChart').getContext('2d');

            // Data dummy jika chartLabels kosong (untuk preview)
            const labels = {!! json_encode($chartLabels ?? []) !!};
            const data = {!! json_encode($chartData ?? []) !!};

            if (labels.length === 0) {
                document.getElementById('kehadiranChart').parentNode.innerHTML =
                    '<div class="text-center text-muted py-5">Belum cukup data untuk menampilkan grafik.</div>';
                return;
            }

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Durasi (Jam)',
                        data: data,
                        backgroundColor: 'rgba(124, 19, 22, 0.1)', // Maroon transparan
                        borderColor: '#7c1316', // Maroon solid
                        borderWidth: 2,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#7c1316',
                        pointRadius: 4,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.raw + ' Jam';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { borderDash: [2, 4] },
                            ticks: { stepSize: 1 }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });
        });
    </script>
@endsection
