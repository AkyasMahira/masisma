@extends('layouts.app')

@section('title', 'Dashboard Utama')
@section('page-title', 'Dashboard Interaktif')

@section('content')
    {{-- Dashboard Stats --}}
    <div class="row">
        <div class="col-md-3">
            <div class="dashboard-card fade-in" style="animation-delay: 0.1s">
                <div class="card-icon primary">
                    <i class="bi bi-graph-up-arrow"></i>
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
                    <i class="bi bi-people"></i>
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
                    <i class="bi bi-cart"></i>
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
        <div class="col-md-3">
            <div class="dashboard-card fade-in" style="animation-delay: 0.4s">
                <div class="card-icon info">
                    <i class="bi bi-chat-left-text"></i>
                </div>
                <div class="stat-number" id="feedbackCount">0</div>
                <div class="stat-text">Feedback</div>
                <div class="progress-container">
                    <div class="progress-label">
                        <span>Target</span>
                        <span>45%</span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar bg-info" role="progressbar" style="width: 45%"></div>
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
    {{-- Script tambahan khusus untuk halaman dashboard --}}
    <script>
        // Script khusus untuk halaman dashboard bisa ditambahkan di sini
    </script>
@endsection
