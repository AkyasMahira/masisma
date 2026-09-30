@extends('layouts.app')

@section('title', 'Layanan Pengajuan')
@section('page-title', 'Dashboard Layanan')

@section('content')

@php
    // =========================================================
    // FIX BUGS: OVERRIDE LOGIC MULTI-PERIODE
    // Menangani kasus di mana user punya riwayat 'nonaktif' lama,
    // tapi baru saja di-approve untuk pengajuan magang yang baru.
    // =========================================================
    $isMagangActiveReal = false;
    $butuhIsiDataBaru = false;

    if (isset($magang) && $magang) {
        if ($magang->status === 'pending') {
            $isMagangActiveReal = true;
        } elseif ($magang->status === 'approved') {
            // Jika belum punya data sama sekali ATAU data mahasiswanya lebih lawas dari pengajuan ini
            if (!isset($mahasiswaAktif) || !$mahasiswaAktif || $mahasiswaAktif->created_at < $magang->created_at) {
                $isMagangActiveReal = true;
                $butuhIsiDataBaru = true; 
            } elseif (isset($mahasiswaAktif) && $mahasiswaAktif->status === 'aktif') {
                $isMagangActiveReal = true;
            }
        }
    }
@endphp

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<style>
    /* --- CONFIGURASI WARNA & VARIABLE --- */
    :root {
        --maroon-main: #7c1316;
        --maroon-light: #a3191d;
        --maroon-gradient: linear-gradient(135deg, #7c1316 0%, #4a0a0c 100%);
        --blue-main: #0369a1;
        --blue-gradient: linear-gradient(135deg, #0369a1 0%, #075985 100%);
        --green-success: #10b981;
        --orange-warning: #f59e0b;
        --glass-bg: rgba(255, 255, 255, 0.85);
        --glass-border: rgba(255, 255, 255, 0.5);
        --font-jakarta: 'Plus Jakarta Sans', sans-serif;
    }

    /* --- GLOBAL STYLING --- */
    body {
        background-color: #f8fafc;
        font-family: var(--font-jakarta);
        color: #1e293b;
        overflow-x: hidden;
    }

    /* Dekorasi Background Bulat-Bulat (Mesh) */
    .bg-blob {
        position: fixed; width: 600px; height: 600px;
        border-radius: 50%; z-index: -1; filter: blur(90px);
    }
    .blob-1 { 
        top: -150px; left: -150px; 
        background: radial-gradient(circle, rgba(124, 19, 22, 0.08) 0%, rgba(255, 255, 255, 0) 70%); 
    }
    .blob-2 { 
        bottom: -150px; right: -150px; 
        background: radial-gradient(circle, rgba(3, 105, 161, 0.08) 0%, rgba(255, 255, 255, 0) 70%); 
    }

    /* --- HEADER SECTION --- */
    .dashboard-header {
        padding: 50px 0 80px;
        background: var(--maroon-gradient);
        border-radius: 0 0 50px 50px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 15px 35px rgba(124, 19, 22, 0.15);
    }
    .header-pattern {
        position: absolute; top: 0; left: 0; width: 100%; height: 100%;
        background-image: radial-gradient(rgba(255,255,255,0.15) 1px, transparent 1px);
        background-size: 25px 25px; opacity: 0.4;
    }
    .hero-title {
        font-family: 'Space Grotesk', sans-serif;
        font-weight: 800;
        letter-spacing: -1px;
    }
    @media (max-width: 768px) {
        .dashboard-header { padding: 40px 0 60px; border-radius: 0 0 30px 30px; }
        .hero-title { font-size: 2.5rem; }
    }

    /* --- CARD SELECTION --- */
    .card-option {
        background: var(--glass-bg);
        backdrop-filter: blur(20px);
        border: 2px solid var(--glass-border);
        border-radius: 30px;
        padding: 40px 30px;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        cursor: pointer;
        height: 100%;
        text-align: center;
        box-shadow: 0 10px 30px rgba(0,0,0,0.03);
        position: relative;
    }
    .card-option:hover {
        transform: translateY(-10px);
        background: white;
        box-shadow: 0 25px 50px rgba(0,0,0,0.08);
    }
    .card-option.active-pra { border-color: var(--maroon-main); background: #fff1f2; }
    .card-option.active-magang { border-color: var(--blue-main); background: #f0f9ff; }

    .icon-box {
        width: 80px; height: 80px; margin: 0 auto 20px;
        border-radius: 25px; display: flex; align-items: center; justify-content: center;
        font-size: 2.5rem; transition: 0.3s;
    }
    .card-option:hover .icon-box { transform: rotate(-5deg) scale(1.1); }

    /* --- HERO STATUS CARD (Active Service) --- */
    .hero-status {
        background: white; border-radius: 30px; padding: 35px;
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.08);
        border-left: 8px solid var(--maroon-main);
        position: relative; height: 100%;
        display: flex; flex-direction: column; justify-content: space-between;
    }
    .hero-magang {
        background: var(--blue-gradient); border: none; color: white;
        box-shadow: 0 20px 40px rgba(3, 105, 161, 0.2);
    }
    .hero-magang .text-muted { color: rgba(255,255,255,0.8) !important; }

    /* --- FLOATING TRAY --- */
    .action-tray {
        position: fixed; bottom: -150px; left: 50%; transform: translateX(-50%);
        width: 95%; max-width: 700px; background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(25px); border-radius: 25px; padding: 20px 30px;
        box-shadow: 0 -10px 40px rgba(0,0,0,0.1);
        transition: 0.5s cubic-bezier(0.68, -0.2, 0.32, 1.2);
        z-index: 1000; border: 1px solid rgba(0,0,0,0.05);
    }
    .action-tray.visible { bottom: 20px; }
    
    @media (max-width: 768px) {
        .action-tray { width: 90%; padding: 20px; text-align: center; }
        .action-tray .btn-group-mobile { flex-direction: column; width: 100%; gap: 10px !important; margin-top: 15px; }
        .action-tray .btn-group-mobile button { width: 100%; }
    }

    /* --- TIMELINE STYLE (Modal) --- */
    .modern-timeline { list-style: none; padding: 0; position: relative; margin-top: 20px; text-align: left; }
    .modern-timeline::before {
        content: ''; position: absolute; left: 16px; top: 0; width: 2px; height: 100%;
        background: #e2e8f0;
    }
    .timeline-step { position: relative; padding-left: 50px; margin-bottom: 30px; }
    .step-marker {
        position: absolute; left: 0; top: 0; width: 34px; height: 34px;
        border-radius: 50%; background: white; border: 3px solid #cbd5e1;
        z-index: 2; display: flex; align-items: center; justify-content: center; font-size: 0.9rem;
    }
    .step-marker.success { border-color: var(--green-success); background: var(--green-success); color: white; }
    .step-marker.warning { border-color: var(--orange-warning); background: var(--orange-warning); color: white; }

    /* --- BUTTONS --- */
    .btn-premium {
        border-radius: 50px; padding: 12px 28px; font-weight: 700;
        letter-spacing: 0.5px; transition: all 0.3s ease; border: none;
    }
    .btn-maroon { background: var(--maroon-main); color: white; }
    .btn-maroon:hover { background: var(--maroon-light); transform: translateY(-3px); color: white; box-shadow: 0 10px 20px rgba(124,19,22,0.2); }
    
    .btn-blue { background: var(--blue-main); color: white; }
    .btn-blue:hover { background: #0284c7; transform: translateY(-3px); color: white; box-shadow: 0 10px 20px rgba(3,105,161,0.2); }

    /* --- ANIMATIONS --- */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-up { animation: fadeInUp 0.7s ease-out forwards; }
</style>

<div class="bg-blob blob-1"></div>
<div class="bg-blob blob-2"></div>

<div class="dashboard-header">
    <div class="header-pattern"></div>
    <div class="container text-center text-white position-relative">
        <span class="badge bg-white text-dark rounded-pill px-3 py-2 mb-3 fw-bold shadow-sm animate-up">
            <i class="bi bi-person-circle me-2 text-maroon"></i> User
        </span>
        <h1 class="hero-title mb-2 animate-up" style="animation-delay: 0.1s;">Pusat Layanan</h1>
        <p class="lead opacity-75 animate-up" style="animation-delay: 0.2s;">Kelola pengajuan Anda atau buat permohonan baru di bawah ini.</p>
    </div>
</div>

<div class="container pb-5" style="margin-top: 30px; position: relative; z-index: 10;">
    <div class="row g-4 justify-content-center">
        
        {{-- ==============================
             KOLOM MAGANG / PKL 
             ============================== --}}
        <div class="col-12 col-lg-6 animate-up" style="animation-delay: 0.3s;">
            @if ($isMagangActiveReal)
                {{-- TAMPILAN JIKA SEDANG MAGANG ATAU PENDING --}}
                <div class="hero-status hero-magang">
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="badge bg-white text-primary rounded-pill px-3 py-2 fw-bold shadow-sm">
                                STATUS: {{ strtoupper($magang->status) }}
                            </span>
                            <i class="bi bi-briefcase-fill opacity-25" style="font-size: 3rem;"></i>
                        </div>
                        <h2 class="fw-extrabold mb-2" style="font-family: 'Space Grotesk', sans-serif;">Program Magang</h2>
                        
                        {{-- NOTIFIKASI DISESUAIKAN DENGAN STATUS TERKINI --}}
                        @if($butuhIsiDataBaru)
                            <div class="alert mt-3 mb-4 d-flex align-items-start" style="background: rgba(255, 255, 255, 0.15); border: 1px solid rgba(255, 255, 255, 0.3); border-radius: 15px; color: white; backdrop-filter: blur(10px);">
                                <i class="bi bi-info-circle-fill fs-4 me-3" style="color: #fff;"></i>
                                <div style="font-size: 0.85rem; line-height: 1.5;">
                                    <strong>Pengajuan Disetujui!</strong> Silakan lengkapi biodata Anda di bawah ini untuk memulai periode magang baru.
                                </div>
                            </div>
                        @else
                            <div class="alert mt-3 mb-4 d-flex align-items-start" style="background: rgba(255, 255, 255, 0.15); border: 1px solid rgba(255, 255, 255, 0.3); border-radius: 15px; color: white; backdrop-filter: blur(10px);">
                                <i class="bi bi-exclamation-triangle-fill fs-4 me-3" style="color: #ffd700;"></i>
                                <div style="font-size: 0.85rem; line-height: 1.5;">
                                    <strong>Pemberitahuan:</strong> Anda masih memiliki pengajuan atau status magang yang <strong>sedang aktif</strong>. Anda tidak dapat mengajukan permohonan baru hingga proses saat ini selesai atau dibatalkan.
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    <div class="d-flex flex-wrap gap-2 mt-auto">
                        @if ($butuhIsiDataBaru)
                            <a href="{{ route('mahasiswa.create') }}" class="btn btn-warning btn-premium text-dark shadow-sm flex-grow-1">
                                LENGKAPI DATA BARU <i class="bi bi-pencil-square ms-1"></i>
                            </a>
                        @elseif ($magang->status === 'pending')
                            <button class="btn btn-light btn-premium text-primary shadow-sm flex-grow-1" data-bs-toggle="modal" data-bs-target="#modalMagang">
                                <i class="bi bi-bar-chart-steps me-1"></i> LIHAT PROGRES
                            </button>
                        @else
                            <a href="{{ route('mahasiswa.dashboard') }}" class="btn btn-premium shadow-sm flex-grow-1" style="background: #10b981; color: white;">
                                BUKA DASHBOARD <i class="bi bi-chevron-right"></i>
                            </a>
                        @endif
                    </div>
                </div>
            @else
                {{-- TAMPILAN JIKA BELUM PERNAH MAGANG / MAGANG LAMA SUDAH SELESAI --}}
                <div class="card-option" onclick="selectService('magang')" id="card-magang">
                    <div class="icon-box" style="background: #e0f2fe; color: var(--blue-main);">
                        <i class="bi bi-building-up"></i>
                    </div>
                    <h4 class="fw-bold">Magang / PKL</h4>
                    @if(isset($magang) && $magang && $magang->status === 'approved')
                        <p class="text-muted small px-md-3">Masa magang Anda sebelumnya telah selesai. Klik di sini jika ingin mengajukan periode magang baru.</p>
                    @else
                        <p class="text-muted small px-md-3">Pendaftaran Praktik Kerja Lapangan resmi bagi institusi pendidikan yang bermitra.</p>
                    @endif
                    <div class="mt-4 fw-bold text-primary small bg-primary bg-opacity-10 py-2 rounded-pill d-inline-block px-4">
                        PILIH LAYANAN <i class="bi bi-arrow-right ms-1"></i>
                    </div>
                </div>
            @endif
        </div>

        {{-- ==============================
             KOLOM PENELITIAN 
             ============================== --}}
        <div class="col-12 col-lg-6 animate-up" style="animation-delay: 0.4s;">
            @if (!$bisaAjukanPra && isset($activePra) && $activePra) 
                {{-- JIKA ADA PENGAJUAN AKTIF --}}
                <div class="hero-status">
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="icon-box m-0" style="background: #fff1f2; color: var(--maroon-main); width: 60px; height: 60px; border-radius: 15px;">
                                <i class="bi bi-journal-check fs-3"></i>
                            </div>
                            <div class="text-end">
                                <span class="badge rounded-pill px-3 py-2 text-uppercase mb-1 {{ $activePra->status == 'approved' ? 'bg-success' : 'bg-warning text-dark' }}">
                                    {{ $activePra->status }}
                                </span>
                                <div class="small text-muted font-monospace">#PRA-{{ $activePra->id }}</div>
                            </div>
                        </div>
                        
                        <h3 class="fw-bold text-dark">Layanan Penelitian</h3>
                        <p class="text-muted small">Izin Anda sedang dalam tahap proses administrasi / pelaksanaan di RSUD.</p>
                    </div>
                    
                    <div class="mt-4 d-flex flex-wrap gap-2">
                        @if($activePra->status === 'approved')
                            <a href="{{ route('pengajuan.detail', 'pra_penelitian') }}" class="btn btn-premium btn-maroon flex-grow-1 shadow-sm">
                                BUKA DATA LENGKAP <i class="bi bi-arrow-right-circle ms-1"></i>
                            </a>
                        @else
                            <button class="btn btn-outline-dark btn-premium flex-grow-1" data-bs-toggle="modal" data-bs-target="#modalPra">
                                PANTAU PROGRES <i class="bi bi-clock-history ms-1"></i>
                            </button>
                        @endif
                        
                        {{-- Tombol Riwayat di mode aktif (jika ada) --}}
                        @if(isset($historyPra) && count($historyPra) > 0)
                            <button class="btn btn-light border btn-premium" data-bs-toggle="modal" data-bs-target="#modalHistoryPra" title="Riwayat Selesai">
                                <i class="bi bi-archive-fill text-muted"></i>
                            </button>
                        @endif
                    </div>
                </div>
            @else
                {{-- JIKA KOSONG / SUDAH SELESAI -> BISA AJUKAN BARU --}}
                <div class="card-option" onclick="selectService('pra')" id="card-pra">
                    {{-- Tombol Riwayat di Pojok Kanan Atas Kartu --}}
                    @if(isset($historyPra) && count($historyPra) > 0)
                        <div class="position-absolute top-0 end-0 mt-3 me-3" style="z-index: 10;">
                            <button class="btn btn-sm btn-light border rounded-pill shadow-sm text-muted fw-bold" data-bs-toggle="modal" data-bs-target="#modalHistoryPra" onclick="event.stopPropagation()">
                                <i class="bi bi-clock-history me-1 text-primary"></i> Riwayat Anda
                            </button>
                        </div>
                    @endif
                    
                    <div class="icon-box" style="background: #fff1f2; color: var(--maroon-main);">
                        <i class="bi bi-journal-text"></i>
                    </div>
                    <h4 class="fw-bold">Penelitian</h4>
                    <p class="text-muted small px-md-3">Ajukan izin survei, wawancara, atau pengambilan data riset di lingkungan RSUD.</p>
                    <div class="mt-4 fw-bold text-danger small bg-danger bg-opacity-10 py-2 rounded-pill d-inline-block px-4">
                        PILIH LAYANAN BARU <i class="bi bi-arrow-right ms-1"></i>
                    </div>
                </div>
            @endif
        </div>

    </div>
</div>

{{-- ==============================
     FLOATING TRAY (BOTTOM SHEET)
     ============================== --}}
<div class="action-tray" id="actionTray">
    <div class="row align-items-center">
        <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
            <p class="text-muted small mb-1 fw-bold text-uppercase">Konfirmasi Pengajuan</p>
            <h5 id="selectedLabel" class="fw-extrabold mb-0" style="font-family: 'Space Grotesk', sans-serif;"></h5>
        </div>
        <div class="col-md-6 d-flex gap-2 justify-content-center justify-content-md-end btn-group-mobile">
            <button class="btn btn-light rounded-pill px-4 fw-bold border" onclick="resetSelection()">BATAL</button>
            
            {{-- FORM PENELITIAN --}}
            @if($bisaAjukanPra)
                {{-- Info jika ada riwayat --}}
                @if(isset($historyPra) && count($historyPra) > 0)
                    <div class="alert alert-success m-0 p-2 text-center small fw-bold flex-grow-1" id="alert-riwayat-pra" style="display: none; border-radius: 50px;">
                        <i class="bi bi-check-circle-fill me-1"></i> Siap Ajukan Baru
                    </div>
                @endif

                <form id="form-pra" action="{{ route('pengajuan.pra') }}" method="POST" style="display: none; margin: 0; flex-grow: 1;">
                    @csrf
                    <button type="submit" class="btn btn-premium btn-maroon shadow-sm w-100">AJUKAN SEKARANG</button>
                </form>
            @endif

            {{-- FORM MAGANG --}}
            <form id="form-magang" action="{{ route('pengajuan.magang') }}" method="POST" style="display: none; margin: 0; flex-grow: 1;">
                @csrf
                <button type="submit" class="btn btn-premium btn-blue shadow-sm w-100">AJUKAN SEKARANG</button>
            </form>
        </div>
    </div>
</div>

{{-- ==============================
     MODAL PROGRES PRA PENELITIAN
     ============================== --}}
<div class="modal fade" id="modalPra" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0" style="border-radius: 30px;">
            <div class="modal-body p-4 p-md-5">
                <div class="text-center mb-4">
                    <div class="icon-box mx-auto mb-3" style="background: #fff1f2; color: var(--maroon-main); width: 70px; height: 70px;">
                        <i class="bi bi-activity fs-2"></i>
                    </div>
                    <h4 class="fw-bold">Progres Penelitian</h4>
                </div>
                <div class="modern-timeline">
                    <div class="timeline-step">
                        <div class="step-marker success"><i class="bi bi-check-lg"></i></div>
                        <h6 class="fw-bold mb-1">Pengajuan Dikirim</h6>
                        <p class="text-muted small mb-0">Data awal penelitian sudah masuk ke sistem.</p>
                    </div>
                    <div class="timeline-step">
                        <div class="step-marker {{ isset($activePra) && $activePra->status != 'pending' ? 'success' : 'warning' }}">
                            <i class="bi {{ isset($activePra) && $activePra->status != 'pending' ? 'bi-check-lg' : 'bi-hourglass-split' }}"></i>
                        </div>
                        <h6 class="fw-bold mb-1">Verifikasi Admin</h6>
                        <p class="text-muted small mb-0">Sedang dalam antrian pemeriksaan oleh Admin Diklat.</p>
                    </div>
                    <div class="timeline-step">
                        <div class="step-marker"><i class="bi bi-flag-fill"></i></div>
                        <h6 class="fw-bold mb-1">Persetujuan Akhir</h6>
                        <p class="text-muted small mb-0">Menunggu status akhir dari pengajuan Anda.</p>
                    </div>
                </div>
                <button class="btn btn-light border w-100 rounded-pill py-2 mt-4 fw-bold text-dark" data-bs-dismiss="modal">TUTUP</button>
            </div>
        </div>
    </div>
</div>

{{-- ==============================
     MODAL PROGRES MAGANG
     ============================== --}}
<div class="modal fade" id="modalMagang" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0" style="border-radius: 30px;">
            <div class="modal-body p-4 p-md-5">
                <div class="text-center mb-4">
                    <div class="icon-box mx-auto mb-3" style="background: #e0f2fe; color: var(--blue-main); width: 70px; height: 70px;">
                        <i class="bi bi-bar-chart-steps fs-2"></i>
                    </div>
                    <h4 class="fw-bold">Alur Pengajuan Magang</h4>
                </div>
                <div class="modern-timeline">
                    <div class="timeline-step">
                        <div class="step-marker success"><i class="bi bi-check-lg"></i></div>
                        <h6 class="fw-bold mb-1">Registrasi Akun</h6>
                        <p class="text-muted small mb-0">Akun Anda berhasil terdaftar di portal.</p>
                    </div>
                    <div class="timeline-step">
                        <div class="step-marker {{ isset($magang) && $magang->status != 'pending' ? 'success' : 'warning' }}">
                             <i class="bi {{ isset($magang) && $magang->status != 'pending' ? 'bi-check-lg' : 'bi-hourglass' }}"></i>
                        </div>
                        <h6 class="fw-bold mb-1">Seleksi Kuota</h6>
                        <p class="text-muted small mb-0">Penyesuaian jadwal dan kuota di unit pelayanan terkait.</p>
                    </div>
                    <div class="timeline-step">
                        <div class="step-marker"><i class="bi bi-briefcase-fill"></i></div>
                        <h6 class="fw-bold mb-1">Mulai Magang</h6>
                        <p class="text-muted small mb-0">Penerbitan surat izin dan pembekalan orientasi.</p>
                    </div>
                </div>
                <button class="btn btn-light border w-100 rounded-pill py-2 mt-4 fw-bold text-dark" data-bs-dismiss="modal">MENGERTI</button>
            </div>
        </div>
    </div>
</div>

{{-- ==============================
     MODAL RIWAYAT (HISTORY) FULL DATA
     ============================== --}}
<div class="modal fade" id="modalHistoryPra" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 25px; overflow:hidden;">
            <div class="modal-header bg-light border-bottom-0 p-4 pb-3">
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-archive-fill text-maroon me-2"></i>Riwayat Penelitian Saya</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-light pt-0">
                @if(isset($historyPra) && count($historyPra) > 0)
                    <div class="row g-4">
                        @foreach($historyPra as $hist)
                            <div class="col-12">
                                <div class="card border-0 shadow-sm" style="border-radius: 20px;">
                                    <div class="card-body p-4">
                                        
                                        {{-- Header Histori --}}
                                        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 border-bottom pb-3">
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success rounded-pill px-3 py-2 fw-bold" style="font-size: 0.8rem;">
                                                <i class="bi bi-check-circle-fill me-1"></i> Selesai
                                            </span>
                                            <small class="text-muted fw-bold">Diajukan: {{ $hist->created_at->format('d M Y') }}</small>
                                        </div>
                                        
                                        {{-- Judul --}}
                                        <h5 class="fw-extrabold text-dark mb-4" style="line-height: 1.4;">{{ $hist->judul }}</h5>
                                        
                                        {{-- Data Info Grid --}}
                                        <div class="row g-3 mb-4">
                                            <div class="col-sm-6 col-md-3">
                                                <div class="small text-muted text-uppercase fw-bold mb-1" style="font-size: 0.65rem;">Jenis Penelitian</div>
                                                <div class="fw-bold text-dark"><i class="bi bi-grid me-1 text-primary"></i> {{ $hist->jenis_penelitian }}</div>
                                            </div>
                                            <div class="col-sm-6 col-md-3">
                                                <div class="small text-muted text-uppercase fw-bold mb-1" style="font-size: 0.65rem;">Instansi/Univ</div>
                                                <div class="fw-bold text-dark text-truncate" title="{{ $hist->mou ? ($hist->mou->nama_instansi ?? $hist->mou->nama_universitas) : '-' }}">
                                                    <i class="bi bi-building me-1 text-primary"></i> {{ $hist->mou ? ($hist->mou->nama_instansi ?? $hist->mou->nama_universitas) : '-' }}
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-md-3">
                                                <div class="small text-muted text-uppercase fw-bold mb-1" style="font-size: 0.65rem;">Program Studi</div>
                                                <div class="fw-bold text-dark text-truncate" title="{{ $hist->prodi }}">
                                                    <i class="bi bi-book me-1 text-primary"></i> {{ $hist->prodi }}
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-md-3">
                                                <div class="small text-muted text-uppercase fw-bold mb-1" style="font-size: 0.65rem;">Mulai / Selesai</div>
                                                <div class="fw-bold text-dark">
                                                    <i class="bi bi-calendar-range me-1 text-primary"></i> 
                                                    {{ $hist->tanggal_mulai ? $hist->tanggal_mulai->format('M Y') : '-' }}
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Dosen Pembimbing --}}
                                        <div class="bg-light p-3 rounded-4 mb-3 border">
                                            <div class="small text-muted text-uppercase fw-bold mb-2" style="font-size: 0.65rem;">Dosen Pembimbing</div>
                                            <div class="row g-2">
                                                <div class="col-md-6">
                                                    <div class="small text-dark fw-bold"><i class="bi bi-person-badge me-1 text-secondary"></i> {{ $hist->dosen1_nama }}</div>
                                                    <div class="text-muted" style="font-size: 0.75rem; margin-left: 20px;">{{ $hist->dosen1_hp }}</div>
                                                </div>
                                                @if($hist->dosen2_nama)
                                                <div class="col-md-6">
                                                    <div class="small text-dark fw-bold"><i class="bi bi-person-badge me-1 text-secondary"></i> {{ $hist->dosen2_nama }}</div>
                                                    <div class="text-muted" style="font-size: 0.75rem; margin-left: 20px;">{{ $hist->dosen2_hp }}</div>
                                                </div>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Anggota Tim --}}
                                        @if($hist->anggotas && $hist->anggotas->count() > 0)
                                        <div class="mb-4">
                                            <div class="small text-muted text-uppercase fw-bold mb-2" style="font-size: 0.65rem;">Anggota Tim ({{ $hist->anggotas->count() }} Orang)</div>
                                            <div class="d-flex flex-wrap gap-2">
                                                @foreach($hist->anggotas as $anggota)
                                                    <span class="badge bg-white text-dark border border-secondary shadow-sm fw-medium px-3 py-2" style="border-radius: 10px;">
                                                        <i class="bi bi-person me-1 text-muted"></i> {{ $anggota->nama }} <span class="text-muted ms-1">({{ $anggota->jenjang }})</span>
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                        @endif

                                        {{-- Dokumen Hasil Akhir --}}
                                        @if($hist->presentasi && ($hist->presentasi->surat_selesai || $hist->presentasi->sertifikat))
                                        <div class="border-top pt-4 mt-auto">
                                            <div class="small text-muted text-uppercase fw-bold mb-3" style="font-size: 0.65rem;">Dokumen Kelulusan</div>
                                            <div class="d-flex flex-wrap gap-3">
                                                @if($hist->presentasi->surat_selesai)
                                                <a href="{{ asset('storage/' . $hist->presentasi->surat_selesai) }}" target="_blank" class="btn btn-outline-danger fw-bold rounded-pill px-4 shadow-sm">
                                                    <i class="bi bi-file-pdf-fill me-1"></i> Surat Selesai
                                                </a>
                                                @endif
                                                
                                                @if($hist->presentasi->sertifikat)
                                                <a href="{{ asset('storage/' . $hist->presentasi->sertifikat) }}" target="_blank" class="btn btn-success fw-bold rounded-pill px-4 shadow-sm text-white">
                                                    <i class="bi bi-award-fill me-1"></i> Sertifikat Utama
                                                </a>
                                                @endif
                                            </div>
                                        </div>
                                        @endif

                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-folder-x display-1 opacity-25"></i>
                        <p class="mt-3 mb-0 fw-medium">Belum ada riwayat pengajuan yang selesai.</p>
                    </div>
                @endif
            </div>
            <div class="modal-footer border-0 bg-light justify-content-center pb-4">
                <button type="button" class="btn btn-secondary rounded-pill px-5" data-bs-dismiss="modal">Tutup Riwayat</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function selectService(type) {
        // Reset state visual
        document.getElementById('card-pra')?.classList.remove('active-pra');
        document.getElementById('card-magang')?.classList.remove('active-magang');
        
        // Hide forms safely
        const formPra = document.getElementById('form-pra');
        const formMagang = document.getElementById('form-magang');
        const alertRiwayat = document.getElementById('alert-riwayat-pra');
        
        if(formPra) formPra.style.display = 'none';
        if(formMagang) formMagang.style.display = 'none';
        if(alertRiwayat) alertRiwayat.style.display = 'none';

        const tray = document.getElementById('actionTray');
        const label = document.getElementById('selectedLabel');
        tray.classList.add('visible');

        if (type === 'pra') {
            document.getElementById('card-pra').classList.add('active-pra');
            label.innerText = "LAYANAN PENELITIAN";
            label.style.color = "var(--maroon-main)";
            if(formPra) formPra.style.display = 'block';
            if(alertRiwayat) alertRiwayat.style.display = 'block';
        } else {
            document.getElementById('card-magang').classList.add('active-magang');
            label.innerText = "MAGANG / PKL";
            label.style.color = "var(--blue-main)";
            if(formMagang) formMagang.style.display = 'block';
        }
    }

    function resetSelection() {
        document.getElementById('actionTray').classList.remove('visible');
        document.getElementById('card-pra')?.classList.remove('active-pra');
        document.getElementById('card-magang')?.classList.remove('active-magang');
        
        const alertRiwayat = document.getElementById('alert-riwayat-pra');
        if(alertRiwayat) alertRiwayat.style.display = 'none';
    }

    // Intersep Submit Form dengan SweetAlert yang Estetik
    document.querySelectorAll('form').forEach(form => {
        // Abaikan form logout jika ada
        if(form.id !== 'logout-form') {
            form.onsubmit = function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Konfirmasi Pengajuan',
                    text: "Pastikan pilihan Anda sudah benar sebelum mengirim permohonan.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#7c1316',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'YA, KIRIM SEKARANG',
                    cancelButtonText: 'BATAL',
                    padding: '2em',
                    borderRadius: '25px'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Tampilkan loading state
                        Swal.fire({
                            title: 'Memproses...',
                            text: 'Mohon tunggu sebentar',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        this.submit();
                    }
                });
            };
        }
    });
</script>
@endsection