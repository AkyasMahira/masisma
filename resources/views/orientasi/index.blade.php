@extends('layouts.app')

@section('title', 'Orientasi')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    /* ==========================================================================
       1. ROOT & GLOBAL STYLES (Industrial & Bold)
       ========================================================================== */
    :root { 
        --maroon-slg: #7c1316; 
        --maroon-dark: #5a0e10;
        --white: #ffffff;
        --dark-industrial: #2c3e50;
        --text-dark: #2d3436;
        --bg-body: #f4f6f9;
        --gold-accent: #f1c40f;
    }

    body { background-color: var(--bg-body); font-family: 'Segoe UI', Roboto, sans-serif; color: var(--text-dark); }
    .text-maroon { color: var(--maroon-slg) !important; }
    .bg-maroon { background-color: var(--maroon-slg) !important; }

    /* ==========================================================================
       2. HEADER SECTION (Bold Welcome)
       ========================================================================== */
    .welcome-panel {
        background: linear-gradient(135deg, var(--maroon-slg) 0%, var(--maroon-dark) 100%);
        border-radius: 20px;
        padding: 40px;
        color: white;
        box-shadow: 0 15px 35px rgba(124,19,22,0.2);
        position: relative;
        overflow: hidden;
        margin-bottom: 30px;
    }
    
    /* Ornamen Gradasi */
    .welcome-panel::before {
        content: ""; position: absolute; top: -50px; right: -50px; width: 200px; height: 200px;
        background: rgba(255, 255, 255, 0.05); border-radius: 50%;
    }

    .welcome-panel h1 { font-weight: 800; font-size: 2.2rem; letter-spacing: -1px; margin-bottom: 5px; }
    .kepanjangan-sindikat { font-size: 0.8rem; font-weight: 600; opacity: 0.8; text-transform: uppercase; letter-spacing: 1px; display: block; margin-bottom: 15px; }
    
    /* Progress Circle */
    .progress-circle-wrapper { position: relative; width: 100px; height: 100px; }
    .progress-circle-text { position: absolute; top: 0; left: 0; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1.5rem; color: white; }
    
    svg.progress-circle { width: 100%; height: 100%; transform: rotate(-90deg); }
    svg.progress-circle circle { fill: none; stroke-width: 10; stroke-linecap: round; }
    svg.progress-circle .bg { stroke: rgba(255,255,255,0.2); }
    svg.progress-circle .bar { stroke: white; transition: stroke-dashoffset 1s ease; }

    /* ==========================================================================
       3. MATERI SECTION (Premium Cards)
       ========================================================================== */
    .section-title { font-weight: 800; color: #333; font-size: 1.1rem; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 20px; display: flex; align-items: center; }
    .section-title::after { content: ""; flex: 1; height: 1px; background: #eee; margin-left: 15px; }

    .materi-card {
        background: white; border-radius: 16px; border: 1px solid #f0f0f0; transition: 0.3s;
        margin-bottom: 15px; position: relative; overflow: hidden;
    }
    .materi-card:hover { transform: translateX(8px); border-color: #ffd6d6; box-shadow: 0 10px 20px rgba(124,19,22,0.08); }
    
    /* Aksen Warna Kiri */
    .materi-card::before { content: ""; position: absolute; left: 0; top: 0; height: 100%; width: 5px; background: #e9ecef; }
    .materi-card.selesai::before { background: #28a745; } /* Hijau Selesai */

    .card-body-custom { padding: 20px 25px; display: flex; align-items: center; }

    /* Step Indicator */
    .step-indicator {
        width: 45px; height: 45px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        margin-right: 20px; font-weight: 800; font-size: 1.1rem; flex-shrink: 0;
    }
    .materi-card .step-indicator { background-color: #f8f9fa; color: #adb5bd; border: 1px solid #eee; }
    .materi-card.selesai .step-indicator { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }

    .materi-card .materi-title { font-weight: 700; color: #1a1a1a; margin-bottom: 2px; }
    .materi-card .materi-subtitle { font-size: 0.75rem; color: #888; text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px; }
    .materi-card .type-badge { background-color: #fdf5f5; color: var(--maroon-slg); padding: 3px 8px; border-radius: 4px; font-weight: 700; font-size: 0.65rem; margin-left: 8px; }

    /* Button "Pelajari" */
    .btn-action-materi {
        border-radius: 50px; font-weight: 700; font-size: 0.8rem; padding: 8px 25px;
        transition: 0.2s; text-transform: uppercase;
    }
    .materi-card .btn-action-materi { border: 1.5px solid var(--maroon-slg); color: var(--maroon-slg); }
    .materi-card .btn-action-materi:hover { background-color: var(--maroon-slg); color: white; }
    .materi-card.selesai .btn-action-materi { border: 1.5px solid #6c757d; color: #6c757d; }
    .materi-card.selesai .btn-action-materi:hover { background-color: #6c757d; color: white; }

    /* ==========================================================================
       4. EVALUASI SECTION (Exclusive Test Boxes)
       ========================================================================== */
    .exam-panel {
        background: white; border-radius: 20px; padding: 30px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05); border: 1px solid #eee;
    }
    
    .exam-box {
        border-radius: 16px; padding: 25px; transition: 0.3s;
        position: relative; overflow: hidden; border: 1px solid #eee;
        margin-bottom: 20px;
    }
    
    /* State: LOCKED (Industrial Gray) */
    .exam-box.locked { background-color: #fafbfc; border: 2px dashed #ddd; opacity: 0.8; filter: grayscale(1); }
    .exam-box.locked .badge-step-exam { background: #adb5bd; color: white; }
    
    /* State: ACTIVE (Serious Maroon) */
    .exam-box.active {
        background: white; border: 2px solid var(--maroon-slg);
        box-shadow: 0 10px 25px rgba(124,19,22,0.1);
    }
    .exam-box.active .badge-step-exam { background: var(--maroon-slg); color: white; }

    /* Step Badge */
    .badge-step-exam {
        padding: 5px 15px; border-radius: 5px; font-weight: 700;
        font-size: 0.7rem; text-transform: uppercase; margin-bottom: 15px; display: inline-block;
    }

    .exam-box h5 { font-weight: 800; color: #1a1a1a; margin-bottom: 8px; }
    .exam-box .exam-desc { font-size: 0.85rem; color: #666; margin-bottom: 20px; }

    /* Score Display */
    .score-display {
        background-color: #f8f9fa; border-radius: 12px; padding: 15px;
        text-align: center; border: 1px solid #eee;
    }
    .score-display .score-label { font-size: 0.75rem; color: #888; font-weight: 600; text-transform: uppercase; }
    .score-display h1 { font-weight: 800; color: var(--maroon-slg); margin: 0; }

    /* Buttons Exam */
    .btn-exam { border-radius: 50px; font-weight: 700; padding: 12px 30px; transition: 0.3s; width: 100%; border: none; }
    .btn-exam-maroon { background-color: var(--maroon-slg); color: white; }
    .btn-exam-maroon:hover:not(:disabled) { background-color: var(--maroon-dark); transform: translateY(-3px); color: white; }
    .btn-exam-maroon:disabled { background-color: #adb5bd; }

    .btn-exam-success { background-color: #28a745; color: white; }
    .btn-exam-success:hover { background-color: #218838; color: white; transform: translateY(-3px); }

    .btn-exam-outline { border: 2px solid var(--dark-industrial); color: var(--dark-industrial); background: white; }
    .btn-exam-outline:hover:not(.disabled) { background-color: var(--dark-industrial); color: white; }

    /* ==========================================================================
       5. RESPONSIVE MEDIA QUERIES
       ========================================================================== */
    @media (max-width: 768px) {
        .welcome-panel { padding: 25px; text-align: center; }
        .welcome-panel .d-flex { flex-direction: column; gap: 20px; }
        .welcome-panel h1 { font-size: 1.8rem; }
        .progress-circle-wrapper { width: 80px; height: 80px; }
        .card-body-custom { flex-direction: column; text-align: center; gap: 15px; }
        .step-indicator { margin-right: 0; }
        .materi-card .materi-title { font-size: 0.95rem; }
    }
</style>

<div class="container-fluid py-4">
    <div class="row">
        
        {{-- 1. HEADER & PROGRESS --}}
        <div class="col-12 mb-4">
            <div class="welcome-panel shadow">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="kepanjangan-sindikat"></span>
                        <h1>Dashboard Orientasi</h1>
                        <p class="mb-0 opacity-75 fw-bold">Selamat datang, {{ auth()->user()->name }}.<br>Selesaikan seluruh materi untuk membuka akses ujian.</p>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <div class="progress-circle-wrapper">
                            <svg class="progress-circle" viewBox="0 0 120 120">
                                <circle class="bg" cx="60" cy="60" r="54"></circle>
                                @php
                                    $radius = 54;
                                    $circumference = 2 * pi() * $radius;
                                    $offset = $circumference - ($circumference * round($persen) / 100);
                                @endphp
                                <circle class="bar" cx="60" cy="60" r="54" style="stroke-dasharray: {{ $circumference }}; stroke-dashoffset: {{ $offset }};"></circle>
                            </svg>
                            <div class="progress-circle-text">{{ round($persen) }}%</div>
                        </div>
                        <div class="text-white d-none d-md-block">
                            <small class="fw-bold opacity-75">Progress</small>
                            <div class="fw-bold">{{ $materials->whereIn('id', $userProgress)->count() }} / {{ $materials->count() }} Materi</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. MATERI PEMBELAJARAN (Industrial Cards) --}}
        <div class="col-lg-8 col-xl-7">
            <div class="section-title">
                <i class="fas fa-layer-group me-2 text-maroon"></i> Kurikulum Orientasi
            </div>
            
            @forelse($materials as $index => $m)
                @php $isDone = in_array($m->id, $userProgress); @endphp
                <div class="card materi-card shadow-sm {{ $isDone ? 'selesai' : '' }}">
                    <div class="card-body card-body-custom">
                        <div class="step-indicator">
                            @if($isDone)
                                <i class="fas fa-check"></i>
                            @else
                                {{ $index + 1 }}
                            @endif
                        </div>
                        
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center mb-1">
                                <h6 class="materi-title fw-bold mb-0">{{ Str::limit($m->title, 50) }}</h6>
                                <span class="type-badge text-uppercase">{{ $m->content_type ?? 'Modul' }}</span>
                            </div>
                            <small class="materi-subtitle">
                                @if($isDone)
                                    <i class="fas fa-history me-1 text-success"></i> Selesai pada: {{ \Carbon\Carbon::now()->subDays(rand(1,3))->format('d M Y') }}
                                @else
                                    <i class="fas fa-book-reader me-1"></i> {{ $m->subtitle ?? 'Materi Wajib SINDIKAT' }}
                                @endif
                            </small>
                        </div>
                        
                        <div class="ms-md-3">
                            <a href="{{ route('orientasi.materi.show', $m->id) }}" class="btn btn-action-materi shadow-sm">
                                <i class="fas {{ $isDone ? 'fa-search-plus' : 'fa-play-circle' }} me-1"></i> {{ $isDone ? 'Review' : 'Mulai' }}
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5 bg-white rounded-4 border shadow-sm">
                    <i class="fas fa-clipboard-list fa-3x text-muted mb-3 opacity-25"></i>
                    <h6 class="text-muted fw-bold">Materi belum tersedia</h6>
                    <p class="small text-muted mb-0">Silakan hubungi admin SINDIKAT RSUD SLG.</p>
                </div>
            @endforelse
        </div>

        {{-- 3. EVALUASI & UJIAN (Exclusive Panel) --}}
        <div class="col-lg-4 col-xl-5 mt-4 mt-lg-0">
            <div class="section-title">
                <i class="fas fa-file-contract me-2 text-maroon"></i> Evaluasi Akhir
            </div>
            
            <div class="exam-panel shadow-sm">
                {{-- PRE-TEST BOX --}}
                <div class="exam-box {{ $allCompleted ? 'active' : 'locked' }}">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="badge-step-exam">TAHAP 1</span>
                        @if($result && $result->pre_test_score !== null)
                            <div class="text-success fs-5"><i class="fas fa-check-circle"></i></div>
                        @elseif(!$allCompleted)
                            <div class="text-muted fs-5"><i class="fas fa-lock"></i></div>
                        @else
                            <div class="text-warning fs-5"><i class="fas fa-star"></i></div>
                        @endif
                    </div>
                    <h5>Pre-Test Orientasi</h5>
                    <p class="exam-desc">Ujian awal untuk mengukur pemahaman dasar Anda mengenai materi rumah sakit.</p>
                    
                    @if($result && $result->pre_test_score !== null)
                        <div class="score-display shadow-inner">
                            <div class="score-label">Skor Ujian Awal</div>
                            <h1>{{ $result->pre_test_score }}</h1>
                        </div>
                    @else
                        <form action="{{ route('orientasi.start') }}" method="POST">
                            @csrf
                            <div class="row g-2 mb-3">
                                <div class="col-6"><input type="number" name="gelombang" class="form-control form-control-sm" placeholder="Gelombang (Misal: 3)" required></div>
                                <div class="col-6"><input type="number" name="tahun" class="form-control form-control-sm" value="{{ date('Y') }}" required></div>
                            </div>
                            <button class="btn-exam btn-exam-maroon shadow-sm fw-bold" {{ !$allCompleted ? 'disabled' : '' }}>
                                <i class="fas fa-rocket me-1"></i> MULAI PRE-TEST
                            </button>
                        </form>
                    @endif
                </div>

                {{-- POST-TEST BOX --}}
                <div class="exam-box {{ ($result && $result->pre_test_score !== null) ? 'active' : 'locked' }}">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="badge-step-exam {{ ($result && $result->pre_test_score !== null) ? 'bg-success' : 'bg-secondary' }}">TAHAP 2</span>
                        @if($result && $result->status == 'lulus_orientasi')
                            <div class="text-primary fs-5"><i class="fas fa-award"></i></div>
                        @elseif($result && $result->post_test_score !== null)
                            <div class="text-danger fs-5"><i class="fas fa-times-circle"></i></div>
                        @elseif(!$result || $result->pre_test_score === null)
                            <div class="text-muted fs-5"><i class="fas fa-lock"></i></div>
                        @else
                            <div class="text-maroon fs-5"><i class="fas fa-key"></i></div>
                        @endif
                    </div>
                    <h5>Post-Test Kelulusan</h5>
                    <p class="exam-desc">Syarat lulus Orientasi. Minimal skor <bold>80</bold> untuk mendapatkan sertifikat.</p>
                    
                    @if($result && $result->post_test_score !== null)
                        <div class="score-display shadow-inner mb-3">
                            <div class="score-label">Skor Ujian Akhir</div>
                            <h1 class="{{ $result->post_test_score >= 80 ? 'text-success' : 'text-danger' }}">
                                {{ $result->post_test_score }}
                            </h1>
                        </div>
                    @endif

                    @if($result && $result->status == 'lulus_orientasi')
                        <a href="{{ route('orientasi.sertifikat') }}" class="btn-exam btn-exam-success fw-bold text-decoration-none">
                            <i class="fas fa-award me-2"></i> SERTIFIKAT
                        </a>
                    @else
                        <a href="{{ route('orientasi.post') }}" class="btn-exam btn-exam-outline fw-bold text-decoration-none {{ (!$result || $result->pre_test_score === null) ? 'disabled' : '' }}">
                            <i class="fas {{ ($result && $result->post_test_score !== null) ? 'fa-redo' : 'fa-graduation-cap' }} me-2"></i> 
                            {{ ($result && $result->post_test_score !== null) ? 'REMEDIAL' : 'MULAI UJIAN' }}
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection