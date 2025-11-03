@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
    {{-- Dashboard Stats --}}
    <div class="row">
        <div class="col-md-3">
            <div class="dashboard-card fade-in" style="animation-delay: 0.1s">
                <div class="card-icon primary">
                    <i class="bi bi-people"></i>
                </div>
                <div class="stat-number" id="revenueCount">0</div>
                <div class="stat-text">Mahasiswa</div>
                <div class="progress-container">
                    <div class="progress-label">
                        <span>Target</span>
                        <span>75%</span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar bg-maroon" role="progressbar" style="width: 75%"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="dashboard-card fade-in" style="animation-delay: 0.2s">
                <div class="card-icon success">
                    <i class="bi bi-door-open"></i>
                </div>
                <div class="stat-number" id="userCount">0</div>
                <div class="stat-text">Room</div>
                <div class="progress-container">
                    <div class="progress-label">
                        <span>Target</span>
                        <span>60%</span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar bg-success" role="progressbar" style="width: 60%"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="dashboard-card fade-in" style="animation-delay: 0.3s">
                <div class="card-icon warning">
                    <i class="bi bi-person"></i>
                </div>
                <div class="stat-number" id="orderCount">0</div>
                <div class="stat-text">Users</div>
                <div class="progress-container">
                    <div class="progress-label">
                        <span>Target</span>
                        <span>85%</span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar bg-warning" role="progressbar" style="width: 85%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts --}}
    <div class="row mt-4">
        <div class="col-md-8">
            <div class="dashboard-card fade-in" style="animation-delay: 0.5s">
                <h5 class="card-title">Grafik Mahasiswa</h5>
                <div class="chart-container">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="dashboard-card fade-in" style="animation-delay: 0.6s">
                <h5 class="card-title">Ruangan</h5>
                <div class="chart-container">
                    <canvas id="trafficChart"></canvas>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        // Provide dashboard data to the global layout so counters & charts use real data
        window.dashboardData = {
            totalMahasiswa: {{ $totalMahasiswa ?? 0 }},
            totalRuangan: {{ $totalRuangan ?? 0 }},
            totalUsers: {{ $totalUsers ?? 0 }},
            todayAbsensi: {{ $todayAbsensi ?? 0 }},
            months: {!! json_encode($months ?? []) !!},
            mahasiswaPerMonth: {!! json_encode($mahasiswaPerMonth ?? []) !!},
            ruanganLabels: {!! json_encode($ruanganLabels ?? []) !!},
            ruanganData: {!! json_encode($ruanganData ?? []) !!}
        };
    </script>
@endsection
