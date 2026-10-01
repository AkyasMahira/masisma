@extends('layouts.app')

@section('title', 'Dashboard Magang')
@section('page-title', 'Dashboard Magang Mahasiswa')

@section('content')
{{-- CDN LIBRARY WAJIB (FullCalendar & ChartJS) --}}
{{-- CDN LIBRARY WAJIB (FullCalendar & ChartJS & SweetAlert2) --}}
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/id.js'></script> 
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<!-- TAMBAHKAN INI -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
/* --- CUSTOM CALENDAR STYLE --- */
/* --- CUSTOM CALENDAR STYLE (MODERN RESPONSIVE) --- */
#calendar {
    font-family: 'DM Sans', 'Inter', sans-serif;
    --fc-border-color: #f1f5f9;
    --fc-today-bg-color: #fff1f2;
    --fc-neutral-text-color: #64748b;
    --fc-event-border-color: transparent;
}

/* Header & Tombol */
.fc-toolbar-title {
    font-size: 1.25rem !important;
    font-weight: 800;
    color: var(--custom-maroon);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.fc-button-primary {
    background-color: #fff !important;
    border: 1px solid #e2e8f0 !important;
    color: #475569 !important;
    font-size: 0.85rem !important;
    font-weight: 600 !important;
    border-radius: 8px !important;
    text-transform: capitalize;
    box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    padding: 6px 14px !important;
    transition: 0.2s;
}
.fc-button-primary:hover, .fc-button-primary:not(:disabled).fc-button-active {
    background-color: var(--custom-maroon) !important;
    color: #fff !important;
    border-color: var(--custom-maroon) !important;
}

/* Grid & Cell */
.fc-theme-standard td, .fc-theme-standard th { border-color: var(--fc-border-color); }
.fc-col-header-cell-cushion {
    color: #64748b; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; padding: 12px 0 !important;
}
.fc-daygrid-day-number {
    color: #334155; font-size: 0.9rem; font-weight: 600; padding: 8px !important;
}

/* Base Custom Event HTML */
.custom-event-wrapper {
    display: flex; align-items: center; gap: 6px;
    padding: 4px 6px; border-radius: 6px;
    background-color: var(--event-color);
    color: #fff; cursor: pointer;
    transition: transform 0.2s;
}
.custom-event-wrapper:hover { transform: translateY(-1px); box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
.evt-dot { display: none; } /* Sembunyikan dot di PC */
.evt-text { overflow: hidden; }
.evt-title { font-size: 0.75rem; font-weight: 700; white-space: nowrap; text-overflow: ellipsis; }
.evt-time { font-size: 0.65rem; opacity: 0.9; margin-top: 1px; }

/* Hilangkan style bawaan fc-event */
.fc-h-event { background: transparent !important; border: none !important; }
.fc-daygrid-event-harness { margin-bottom: 3px !important; }

/* =====================================
   RESPONSIVE MOBILE VIEW (MAGIC-NYA DISINI) 
   ===================================== */
@media (max-width: 768px) {
    /* Rapihkan Header di HP */
    .fc-header-toolbar { flex-direction: column; gap: 12px; }
    .fc-toolbar-title { font-size: 1.1rem !important; }
    
    /* Tengahin Angka Tanggal */
    .fc-daygrid-day-top { justify-content: center; }
    
    /* Ubah Baris Event Menjadi Titik (Dots) */
    .custom-event-wrapper {
        background-color: transparent !important;
        padding: 0; margin: 0; justify-content: center;
    }
    .custom-event-wrapper:hover { transform: none; box-shadow: none; }
    
    /* Sembunyikan Teks, Munculkan Titik */
    .evt-text { display: none; }
    .evt-dot {
        display: block;
        width: 8px; height: 8px;
        border-radius: 50%;
        background-color: var(--event-color);
        margin: 2px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }

    /* Kumpulkan Titik di Tengah Bawah Tanggal */
    .fc-daygrid-day-events {
        display: flex !important;
        flex-wrap: wrap;
        justify-content: center;
        gap: 2px;
        margin-top: -5px !important;
    }
    .fc-daygrid-event-harness { margin: 0 !important; }
    .fc-daygrid-event { margin: 0 !important; }
}

/* Legend Styling */
.legend-item { display: flex; align-items: center; font-size: 0.75rem; font-weight: 600; color: #64748b; }
.legend-dot { width: 10px; height: 10px; border-radius: 50%; margin-right: 6px; display: inline-block; }
           /* THEME YELLOW (SMK Non-Kesehatan) */
.id-card-wrapper.theme-yellow .card-header-shape { background: linear-gradient(135deg, #b45309 0%, #eab308 100%) !important; }
.id-card-wrapper.theme-yellow .card-role { background-color: #fefce8 !important; color: #b45309 !important; border-color: #fef08a !important; }
.id-card-wrapper.theme-yellow .qr-text { color: #b45309 !important; }
.id-card-wrapper.theme-yellow .card-footer-shape { background: #b45309 !important; }
        /* 1. THEME BLACK (Profesi Kesehatan) */
        .id-card-wrapper.theme-black .card-header-shape { background: linear-gradient(135deg, #111827 0%, #374151 100%); }
        .id-card-wrapper.theme-black .card-role { background-color: #f1f5f9; color: #111827; border-color: #cbd5e1; }
        .id-card-wrapper.theme-black .qr-text { color: #111827; }
        .id-card-wrapper.theme-black .card-footer-shape { background: #111827; }

        /* 2. THEME BLUE (Non-Kesehatan Random) */
        .id-card-wrapper.theme-blue .card-header-shape { background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); }
        .id-card-wrapper.theme-blue .card-role { background-color: #eff6ff; color: #1e3a8a; border-color: #bfdbfe; }
        .id-card-wrapper.theme-blue .qr-text { color: #1e3a8a; }
        .id-card-wrapper.theme-blue .card-footer-shape { background: #1e3a8a; }

        /* 3. THEME GREEN (Non-Kesehatan Random) */
        .id-card-wrapper.theme-green .card-header-shape { background: linear-gradient(135deg, #14532d 0%, #22c55e 100%); }
        .id-card-wrapper.theme-green .card-role { background-color: #f0fdf4; color: #14532d; border-color: #bbf7d0; }
        .id-card-wrapper.theme-green .qr-text { color: #14532d; }
        .id-card-wrapper.theme-green .card-footer-shape { background: #14532d; }

        /* 4. THEME PURPLE (Non-Kesehatan Random) */
        .id-card-wrapper.theme-purple .card-header-shape { background: linear-gradient(135deg, #581c87 0%, #a855f7 100%); }
        .id-card-wrapper.theme-purple .card-role { background-color: #faf5ff; color: #581c87; border-color: #e9d5ff; }
        .id-card-wrapper.theme-purple .qr-text { color: #581c87; }
        .id-card-wrapper.theme-purple .card-footer-shape { background: #581c87; }

        /* 5. THEME ORANGE (Non-Kesehatan Random) */
        .id-card-wrapper.theme-orange .card-header-shape { background: linear-gradient(135deg, #9a3412 0%, #f97316 100%); }
        .id-card-wrapper.theme-orange .card-role { background-color: #fff7ed; color: #9a3412; border-color: #fed7aa; }
        .id-card-wrapper.theme-orange .qr-text { color: #9a3412; }
        .id-card-wrapper.theme-orange .card-footer-shape { background: #9a3412; }

/* 1. Header Toolbar (Bulan & Tombol) */
.fc-toolbar-title {
    font-size: 1.1rem !important;
    font-weight: 800;
    color: var(--custom-maroon); /* Sesuaikan dengan variabel warna maroon Anda */
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.fc-button {
    background-color: #fff !important;
    border: 1px solid #e2e8f0 !important;
    color: #475569 !important;
    box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    font-size: 0.8rem !important;
    font-weight: 600 !important;
    text-transform: capitalize;
    padding: 6px 12px !important;
    border-radius: 8px !important;
}

.fc-button:hover, .fc-button-active {
    background-color: var(--custom-maroon) !important;
    color: #fff !important;
    border-color: var(--custom-maroon) !important;
}

/* 2. Grid & Cell Styling */
.fc-theme-standard td, .fc-theme-standard th {
    border-color: #f1f5f9; /* Warna border lebih halus */
}

.fc-col-header-cell-cushion {
    color: #64748b;
    text-decoration: none !important;
    text-transform: uppercase;
    font-size: 0.75rem;
    font-weight: 700;
    padding: 10px 0 !important;
}

.fc-daygrid-day-number {
    color: #334155;
    text-decoration: none !important;
    font-size: 0.85rem;
    font-weight: 600;
    padding: 8px !important;
}

/* 3. Event Styling (Kotak Jadwal) */
.fc-event {
    border: none !important;
    border-radius: 6px !important;
    padding: 4px 6px !important;
    margin-bottom: 4px !important;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    font-size: 0.75rem !important;
    cursor: pointer;
    transition: transform 0.2s;
    text-decoration: none !important; /* HILANGKAN GARIS BAWAH */
}

.fc-event:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

/* Memastikan link di dalam event tidak ada garis bawah */
a.fc-event, a.fc-event:hover {
    text-decoration: none !important;
    color: inherit;
}

/* 4. Legend Dot Custom */
.legend-item {
    display: flex;
    align-items: center;
    font-size: 0.75rem;
    font-weight: 600;
    color: #64748b;
}
.legend-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    margin-right: 6px;
    display: inline-block;
}
        /* =========================================
           1. DASHBOARD STYLES
           ========================================= */
        .profile-card { background: #fff; border: none; border-radius: var(--card-radius); box-shadow: var(--shadow-soft); overflow: hidden; }
        .profile-header { background: linear-gradient(135deg, var(--custom-maroon), #5a0e10); height: 120px; position: relative; }
        .profile-avatar-container { position: absolute; bottom: -50px; left: 50%; transform: translateX(-50%); }
        .profile-avatar { width: 100px; height: 100px; border-radius: 50%; border: 4px solid #fff; box-shadow: 0 4px 10px rgba(0,0,0,0.1); background-color: #f8f9fa; overflow: hidden; display: flex; align-items: center; justify-content: center; }
        .profile-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .avatar-initial { font-size: 2.5rem; font-weight: bold; color: var(--custom-maroon); }
        .profile-body { padding: 60px 1.5rem 1.5rem; text-align: center; }
        .info-item { display: flex; justify-content: space-between; padding: 0.8rem 0; border-bottom: 1px solid #f1f5f9; text-align: left; }
        .info-item:last-child { border-bottom: none; }
        .info-label { font-size: 0.85rem; color: var(--text-muted); font-weight: 600; display: flex; align-items: center; gap: 8px; }
        .info-value { font-size: 0.9rem; color: var(--text-dark); font-weight: 600; text-align: right; max-width: 60%; }
        .stat-card { border: none; border-radius: var(--card-radius); color: white; padding: 1.5rem; position: relative; overflow: hidden; box-shadow: var(--shadow-soft); transition: transform 0.3s; height: 100%; }
        .stat-card:hover { transform: translateY(-5px); }
        .bg-gradient-blue { background: linear-gradient(135deg, #3b82f6, #2563eb); }
        .bg-gradient-green { background: linear-gradient(135deg, #10b981, #059669); }
        .bg-gradient-orange { background: linear-gradient(135deg, #f59e0b, #d97706); }
        .stat-icon-bg { position: absolute; right: -10px; bottom: -10px; font-size: 5rem; opacity: 0.2; transform: rotate(-15deg); }
        .content-card { background: #fff; border: none; border-radius: var(--card-radius); box-shadow: var(--shadow-soft); overflow: hidden; margin-bottom: 1.5rem; }
        .content-header { padding: 1.25rem 1.5rem; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; }
        .content-title { font-weight: 700; color: var(--custom-maroon); margin: 0; font-size: 1.1rem; }
        .btn-back { background: white; border: 1px solid #e2e8f0; color: var(--text-dark); padding: 0.5rem 1.2rem; border-radius: 8px; font-weight: 500; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: 0.3s; }
        .btn-back:hover { background: #f8f9fa; color: var(--custom-maroon); border-color: var(--custom-maroon); }
        .btn-qr { background: var(--custom-maroon-subtle); color: var(--custom-maroon); border: none; padding: 0.5rem 1rem; border-radius: 8px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; transition: 0.3s; }
        .btn-qr:hover { background: #edd5d5; color: #5a0e10; }
        .badge-soft-success { background: #dcfce7; color: #166534; padding: 5px 10px; border-radius: 6px; font-weight: 600; font-size: 0.75rem; }
        .badge-soft-warning { background: #fef9c3; color: #854d0e; padding: 5px 10px; border-radius: 6px; font-weight: 600; font-size: 0.75rem; }
        .badge-soft-danger { background: #fee2e2; color: #991b1b; padding: 5px 10px; border-radius: 6px; font-weight: 600; font-size: 0.75rem; }
        .animate-up { animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards; opacity: 0; transform: translateY(20px); }
        @keyframes fadeInUp { to { opacity: 1; transform: translateY(0); } }

        /* =========================================
           2. ID CARD STYLES (GENERATOR) - FIXED
           ========================================= */
        
        .id-card-wrapper {
            width: 320px;
            height: 520px;
            background: #ffffff;
            position: relative;
            overflow: hidden;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            font-family: 'Segoe UI', sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        /* Background Decoration Pattern */
        .card-bg-decoration {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            background-image: radial-gradient(#cbd5e1 1px, transparent 1px);
            background-size: 20px 20px; opacity: 0.5; z-index: 0;
        }

        /* --- LARGE BACKGROUND LOGOS (FIXED POSITIONS) --- */
        .card-big-bg-logo {
            position: absolute;
            top: 90%; /* Posisi vertikal tengah */
            transform: translateY(-50%);
            width: 300px; 
            height: 300px;
            object-fit: contain;
            opacity: 0.12; /* Transparan */
            z-index: 1; 
            pointer-events: none;
        }

        /* Geser ke KIRI */
        .bg-left {
            left: -120px; 
        }

        /* Geser ke KANAN */
        .bg-right {
            right: -120px; 
            left: auto; /* Penting: Reset left agar right berfungsi */
        }

        /* --- HEADER SHAPE --- */
        .card-header-shape {
            width: 100%; height: 140px;
            background: linear-gradient(135deg, #7c1316 0%, #a3191d 100%);
            border-bottom-left-radius: 40px; border-bottom-right-radius: 40px;
            position: relative; z-index: 2; color: white;
            display: flex; justify-content: center; align-items: flex-start;
            padding: 25px 20px 0 20px;
        }
/* THEME GREEN (Non-Kesehatan) */
.id-card-wrapper.theme-green .card-header-shape { background: linear-gradient(135deg, #065f46 0%, #10b981 100%) !important; }
.id-card-wrapper.theme-green .card-role { background-color: #ecfdf5 !important; color: #065f46 !important; border-color: #a7f3d0 !important; }
.id-card-wrapper.theme-green .qr-text { color: #065f46 !important; }
.id-card-wrapper.theme-green .card-footer-shape { background: #065f46 !important; }
        .card-title-text {
            text-align: center; color: white;
        }
        .card-title-text h6 { font-size: 11px; text-transform: uppercase; letter-spacing: 3px; margin-bottom: 4px; opacity: 0.9; }
        .card-title-text h4 { font-size: 15px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; margin: 0; line-height: 1.1; }

        /* Photo Section */
        .card-photo-container {
            margin-top: -35px; 
            z-index: 3; 
            position: relative;
        }

        .card-photo {
            width: 110px; height: 110px; border-radius: 50%;
            object-fit: cover; border: 5px solid #ffffff;
            box-shadow: 0 4px 8px rgba(0,0,0,0.15); background: #f1f5f9;
        }

        /* Content Section */
        .card-content {
            padding: 5px 25px 15px 25px; width: 100%; 
            z-index: 3;
            flex-grow: 1; display: flex; flex-direction: column; align-items: center;
        }

        .card-name { font-size: 18px; font-weight: 700; color: #1e293b; margin-top: 8px; line-height: 1.2; }
        .card-role { 
            background-color: #fef2f2; color: #7c1316; 
            font-size: 10px; font-weight: 700; 
            padding: 4px 12px; border-radius: 20px; 
            margin-top: 6px; margin-bottom: 15px;
            letter-spacing: 0.5px; border: 1px solid #fecaca; text-transform: uppercase;
        }

        /* Detail Lines */
        .detail-box { width: 100%; text-align: left; }
        .detail-row {
            display: flex; justify-content: space-between;
            border-bottom: 1px dashed #cbd5e1; padding: 6px 0;
        }
        .detail-row:last-child { border-bottom: none; }
        .detail-label { color: #64748b; font-weight: 600; font-size: 10px; text-transform: uppercase; }
        .detail-val { color: #334155; font-weight: 700; text-align: right; max-width: 65%; font-size: 11px; }

        /* QR Section */
        .card-qr-section {
            margin-top: auto; 
            margin-bottom: 25px; 
            z-index: 3;
            background: white; 
            padding: 6px; 
            border-radius: 8px;
            border: 1px solid #f1f5f9; 
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
            position: relative;
            min-width: 90px; 
        }
        
        .card-qr img { width: 65px; height: 65px; display: block; margin: 0 auto; }
        
        .qr-text { 
            font-size: 9px; font-weight: 700; color: #7c1316; margin-top: 4px; 
            letter-spacing: 1px; display: block; 
        }

        /* Footer Shape */
        .card-footer-shape {
            position: absolute; bottom: 0; left: 0; width: 100%; height: 15px;
            background: #7c1316; z-index: 2;
        }

    </style>

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
                    <p class="text-muted small mb-4">
                        @php
                            $registeredMou = $mahasiswa->user && $mahasiswa->user->mou ? $mahasiswa->user->mou : null;
                            $mahasiswaMou = $mahasiswa->mou ?? null;
                            $displayMou = $registeredMou ?? $mahasiswaMou;
                            $instansiName = $displayMou ? ($displayMou->nama_instansi ?? $displayMou->nama_universitas ?? 'Instansi Tidak Diketahui') : 'Instansi Tidak Diketahui';
                        @endphp
                        {{ $instansiName }}
                    </p>
 <a href="{{ route('absensi.card', $mahasiswa->share_token) }}" target="_blank" class="btn-qr">
                        <i class="bi bi-qr-code"></i> Kartu Absen
                    </a>
                    <div class="info-item">
                        <span class="info-label"><i class="bi bi-book"></i> Prodi</span>
                        <span class="info-value">{{ $mahasiswa->prodi }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label"><i class="bi bi-telephone"></i> No. HP</span>
                        <span class="info-value">{{ $mahasiswa->no_hp ?? '-' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label"><i class="bi bi-door-open"></i> Ruangan</span>
                        <span class="info-value text-danger">{{ $mahasiswa->nama_ruangan_saat_ini }}</span>
                    </div>
                <div class="info-item">
    <span class="info-label"><i class="bi bi-calendar-range"></i> Periode</span>
    <span class="info-value">
        {{ \Carbon\Carbon::parse($startStr)->format('d M') }} - 
        {{ \Carbon\Carbon::parse($endStr)->format('d M Y') }}
        @if($jadwalRolling)
            <br><span class="badge bg-warning text-dark" style="font-size: 0.6rem;">Jadwal Rolling</span>
        @endif
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

{{-- Action Buttons --}}
                    <div class="mt-4 d-grid gap-2">
                        
                        {{-- 1. LOGIKA ID CARD --}}
      @if($mahasiswa->share_token)
    {{-- JIKA TOKEN/LINK ABSEN SUDAH ADA, LANGSUNG BISA GENERATE ID CARD --}}
    <button type="button" class="btn btn-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#idCardModal">
        <i class="bi bi-person-badge-fill me-1"></i> Generate ID Card
    </button>
@else
    {{-- ALERT LINK ABSEN BELUM READY (DENGAN WA ADMIN) --}}
    <div class="alert alert-danger text-start p-3 shadow-sm border-0" style="background-color: #fff5f5;">
        <div class="d-flex align-items-center mb-2">
            <i class="bi bi-x-circle-fill text-danger me-2 fs-5"></i>
            <h6 class="fw-bold mb-0 text-danger" style="font-size: 0.9rem;">Link Absen Belum Ada</h6>
        </div>
        <p class="small text-muted lh-sm mb-2">
            Hubungi Admin untuk generate Link Absen/QR Code:
        </p>
        <a href="https://wa.me/6282245415977?text=Halo%20Admin,%20link%20QR/Absensi%20saya%20belum%20muncul.%0A%0ANama:%20{{ urlencode($mahasiswa->nm_mahasiswa) }}%0AKampus:%20{{ urlencode($instansiName) }}%0APeriode:%20{{ \Carbon\Carbon::parse($mahasiswa->tanggal_mulai)->format('d-m-Y') }}%20sd%20{{ \Carbon\Carbon::parse($mahasiswa->tanggal_berakhir)->format('d-m-Y') }}" 
           target="_blank" class="btn btn-outline-danger btn-sm w-100 rounded-pill" style="font-size: 0.75rem;">
            <i class="bi bi-whatsapp me-1"></i> Lapor Admin
        </a>
    </div>
    
    <button type="button" class="btn btn-secondary rounded-pill disabled" disabled>
        <i class="bi bi-lock-fill me-1"></i> ID Card Terkunci
    </button>
@endif

                        {{-- 2. LOGIKA SERTIFIKAT --}}
                        @if (now()->gt($mahasiswa->tanggal_berakhir))
                            {{-- SKENARIO A: SUDAH SELESAI --}}
                            
                            @if($mahasiswa->share_token)
                                {{-- A1: LINK PUBLIK SUDAH ADA --}}
                                {{-- [UPDATE] LANGSUNG DOWNLOAD PDF MENGGUNAKAN TOKEN --}}
                                <a href="{{ route('sertifikat.download', $mahasiswa->share_token) }}" target="_blank" class="btn btn-success rounded-pill text-white shadow-sm" style="background-color: #198754; border: none;">
                                    <i class="bi bi-file-earmark-pdf-fill me-1"></i> Download Sertifikat
                                </a>
                            @else
                                {{-- A2: SUDAH SELESAI TAPI LINK BELUM DIGENERATE ADMIN --}}
                                <div class="alert alert-warning text-start p-3 shadow-sm border-0" style="background-color: #fff3cd;">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-exclamation-triangle-fill text-warning me-2 fs-5"></i>
                                        <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.9rem;">Sertifikat Belum Rilis</h6>
                                    </div>
                                    <p class="small text-muted lh-sm mb-2">
                                        Masa magang selesai, namun Link Sertifikat belum dibuat. Hubungi Admin:
                                    </p>
                                    {{-- TOMBOL WA KE ADMIN --}}
                                    <a href="https://wa.me/6282245415977?text=Halo%20Admin,%20masa%20magang%20saya%20sudah%20selesai%20tapi%20link%20sertifikat%20belum%20bisa%20diakses.%0A%0ANama:%20{{ urlencode($mahasiswa->nm_mahasiswa) }}%0AKampus:%20{{ urlencode($instansiName) }}%0APeriode:%20{{ \Carbon\Carbon::parse($mahasiswa->tanggal_mulai)->format('d-m-Y') }}%20sd%20{{ \Carbon\Carbon::parse($mahasiswa->tanggal_berakhir)->format('d-m-Y') }}" 
                                       target="_blank" class="btn btn-success btn-sm w-100 rounded-pill" style="font-size: 0.75rem;">
                                        <i class="bi bi-whatsapp me-1"></i> Request ke Admin
                                    </a>
                                </div>
                            @endif

                        @else
                            {{-- SKENARIO B: BELUM SELESAI --}}
                            <button type="button" class="btn btn-secondary rounded-pill disabled" disabled style="background-color: #e2e6ea; color: #6c757d; border: 1px solid #ced4da;">
                                <i class="bi bi-lock-fill me-1"></i> Sertifikat (Menunggu Selesai)
                            </button>
                        @endif

                        {{-- 3. TOMBOL DISPENSASI (GROUP) --}}
                        <div class="btn-group shadow-sm" role="group">
                            <a href="{{ route('mahasiswa.dispensasi.create') }}" class="btn btn-danger" style="background-color: var(--custom-maroon); border: none;">
                                <i class="bi bi-plus-circle me-1"></i> Izin Baru
                            </a>
                            <a href="{{ route('mahasiswa.dispensasi.index') }}" class="btn btn-outline-danger" style="color: var(--custom-maroon); border-color: var(--custom-maroon);">
                                <i class="bi bi-clock-history me-1"></i> Riwayat
                            </a>
                        </div>

                        {{-- 4. TOMBOL LAINNYA --}}
                        <a href="{{ route('room_sequences.index') }}" class="btn btn-outline-secondary rounded-pill">
                            <i class="bi bi-list-task me-1"></i> Jadwal Rolling
                        </a>
                        
                        @if(auth()->user()->mahasiswa->is_edited)
    <button class="btn btn-secondary" disabled>
        <i class="bi bi-lock-fill"></i> Data Terkunci (Sudah Edit 1x)
    </button>
@else
    <a href="{{ route('mahasiswa.edit', auth()->user()->mahasiswa->id) }}" class="btn btn-warning">
        <i class="bi bi-pencil"></i> Edit Profil
    </a>
    <small class="text-danger d-block mt-1">*Hanya bisa edit 1 kali</small>
@endif
                       
                    </div>
                </div>
            </div>
        </div>

   
        <div class="col-lg-8">
            
            {{-- LOGIC PHP: DETEKSI DETAIL ALPHA (EXCLUDE IZIN) --}}
            @php
                $listAlpha = [];
                
                // 1. Ambil Tanggal Absen Masuk
                $absensiDates = $mahasiswa->absensis->filter(fn($a) => $a->type == 'masuk')
                    ->map(fn($a) => \Carbon\Carbon::parse($a->jam_masuk)->format('Y-m-d'))->toArray();
                
                // 2. Ambil Tanggal Shift Libur
                $shifts = $mahasiswa->shiftSchedules->pluck('shift_type', 'tanggal')->toArray();

                // 3. Ambil Tanggal Izin/Dispensasi (Approved)
                $izinDates = [];
                $dispensasis = \App\Models\Dispensasi::where('mahasiswa_id', $mahasiswa->id)
                    ->where('status', 'approved')->get();
                
                foreach($dispensasis as $d) {
                    $period = \Carbon\CarbonPeriod::create($d->tanggal_mulai, $d->tanggal_selesai);
                    foreach($period as $date) {
                        $izinDates[] = $date->format('Y-m-d');
                    }
                }

                // 4. Loop Periode untuk Cari Alpha
                // Batas akhir = Hari ini atau Tanggal Akhir Magang (mana yang lebih dulu)
                $batasAkhir = now()->lt(\Carbon\Carbon::parse($endStr)) ? now() : \Carbon\Carbon::parse($endStr);
                
                if($startStr) {
                    $periodeCek = \Carbon\CarbonPeriod::create($startStr, $batasAkhir);
                    foreach($periodeCek as $date) {
                        $dStr = $date->format('Y-m-d');
                        $sType = $shifts[$dStr] ?? null;

                        // Skip jika hari ini belum berlalu sepenuhnya (Opsional, tergantung kebijakan)
                        if ($date->isToday()) continue;

                        // RULE 1: Skip Libur Shift
                        if($sType === 'Libur') continue;
                        
                        // RULE 2: Skip Weekend (Jika Non-Shift & Weekend Off)
                        if(!$sType && !$mahasiswa->weekend_aktif && $date->isWeekend()) continue;

                        // RULE 3: Skip Jika Sudah Absen
                        if(in_array($dStr, $absensiDates)) continue;

                        // RULE 4: Skip Jika Izin/Dispensasi (PENTING!)
                        if(in_array($dStr, $izinDates)) continue;

                        // Jika lolos semua filter di atas, berarti ALPHA
                        $listAlpha[] = $date->isoFormat('dddd, D MMMM Y');
                    }
                }
                
                // Hitung ulang jumlah Alpha untuk tampilan kartu (agar sinkron dengan list)
                $alphaCount = count($listAlpha);
            @endphp

            <div class="row g-3 mb-4 animate-up" style="animation-delay: 0.2s;">
                
                {{-- KARTU 1: TARGET --}}
                <div class="col-md-4">
                    <div class="stat-card bg-gradient-blue">
                        <div class="position-relative z-1">
                            <h2 class="fw-bold mb-0">{{ $targetTotal }}</h2>
                            <small class="opacity-75">Target Hari Kerja</small>
                        </div>
                        <i class="bi bi-calendar-week stat-icon-bg"></i>
                    </div>
                </div>

                {{-- KARTU 2: HADIR --}}
                <div class="col-md-4">
                    <div class="stat-card bg-gradient-green">
                        <div class="position-relative z-1">
                            <h2 class="fw-bold mb-0">{{ $totalHadir }}</h2>
                            <small class="opacity-75">Total Kehadiran</small>
                        </div>
                        <i class="bi bi-person-check stat-icon-bg"></i>
                    </div>
                </div>

                {{-- KARTU 3: PERSENTASE --}}
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

            {{-- KARTU DETAIL STATISTIK --}}
            <div class="content-card animate-up" style="animation-delay: 0.4s;">
                <div class="content-header">
                    <h5 class="content-title"><i class="bi bi-pie-chart me-2"></i>Statistik Kehadiran</h5>
                </div>
                <div class="p-4">
                    <div class="row align-items-center">
                        {{-- Bagian Kiri: Angka Detail --}}
                        <div class="col-md-6 mb-3 mb-md-0">
                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="p-3 bg-light rounded-3 border text-center">
                                        <small class="text-muted d-block mb-1">Hadir</small>
                                        <h4 class="fw-bold text-success mb-0">{{ $totalHadir }}</h4>
                                        <small class="text-secondary" style="font-size: 0.7rem">Hari</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    {{-- ALPHA CLICKABLE --}}
                                    <div class="p-3 bg-light rounded-3 border text-center" 
                                         style="cursor: pointer; border-color: #fca5a5 !important; background-color: #fef2f2 !important;"
                                         data-bs-toggle="modal" data-bs-target="#alphaModal">
                                        <small class="text-muted d-block mb-1">Alpha/Bolos</small>
                                        <h4 class="fw-bold text-danger mb-0">
                                            {{ $alphaCount }} <i class="bi bi-info-circle-fill ms-1" style="font-size: 0.7rem;"></i>
                                        </h4>
                                        <small class="text-secondary" style="font-size: 0.7rem">Klik Detail</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-3 bg-light rounded-3 border text-center">
                                        <small class="text-muted d-block mb-1">Target Berjalan</small>
                                        <h4 class="fw-bold text-primary mb-0">{{ $targetBerjalan }}</h4>
                                        <small class="text-secondary" style="font-size: 0.7rem">Hari</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-3 bg-light rounded-3 border text-center">
                                        <small class="text-muted d-block mb-1">Sisa Periode</small>
                                        <h4 class="fw-bold text-warning mb-0">{{ $chartSisa }}</h4>
                                        <small class="text-secondary" style="font-size: 0.7rem">Hari Lagi</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Bagian Kanan: Grafik Donat --}}
                        <div class="col-md-6 d-flex justify-content-center">
                            <div style="width: 200px; height: 200px; position: relative;">
                                <canvas id="attendanceChart"></canvas>
                                <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center;">
                                    <span class="fw-bold text-dark" style="font-size: 1.5rem;">{{ $persentase }}%</span>
                                    <div class="small text-muted" style="font-size: 0.7rem">Kehadiran</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KALENDER AKTIVITAS (LENGKAP) --}}
<div class="content-card animate-up" style="animation-delay: 0.5s;">
    {{-- Header Card --}}
    <div class="content-header d-flex flex-wrap justify-content-between align-items-center py-3 px-4 border-bottom bg-white">
        <h5 class="content-title fw-bold mb-0 text-dark">
            <i class="bi bi-calendar-week me-2 text-danger"></i> Kalender Aktivitas
        </h5>
        
        {{-- Legend Modern (Dot Style) --}}
        <div class="d-none d-md-flex gap-3 ms-auto bg-light px-3 py-2 rounded-pill border">
            <div class="legend-item">
                <span class="legend-dot bg-primary"></span> Shift
            </div>
            <div class="legend-item">
                <span class="legend-dot bg-success"></span> Hadir
            </div>
            <div class="legend-item">
                <span class="legend-dot bg-secondary"></span> Libur
            </div>
            <div class="legend-item">
                <span class="legend-dot bg-danger"></span> Alpha
            </div>
            <div class="legend-item">
                <span class="legend-dot bg-warning"></span> Izin
            </div>
        </div>
    </div>

    {{-- Body Card --}}
    <div class="p-4">
        {{-- Container Kalender --}}
        <div id="calendar"></div>
        
        {{-- Legend Mobile (Hanya muncul di HP) --}}
        <div class="d-flex d-md-none flex-wrap gap-3 mt-3 pt-3 border-top justify-content-center">
            <div class="legend-item"><span class="legend-dot bg-primary"></span> Shift</div>
            <div class="legend-item"><span class="legend-dot bg-success"></span> Hadir</div>
            <div class="legend-item"><span class="legend-dot bg-secondary"></span> Libur</div>
            <div class="legend-item"><span class="legend-dot bg-danger"></span> Alpha</div>
            <div class="legend-item"><span class="legend-dot bg-warning"></span> Izin</div>
        </div>
    </div>
</div>
            {{-- TABEL RENCANA ROTASI (JIKA ADA) --}}
            @if(isset($mahasiswa->roomSequences) && $mahasiswa->roomSequences->count() > 0)
            <div class="content-card animate-up" style="animation-delay: 0.6s;">
                <div class="content-header">
                    <h5 class="content-title"><i class="bi bi-signpost-split me-2"></i> Rencana Rotasi Ruangan</h5>
                </div>
                <style>
                    .rotasi-timeline { position:relative; padding: 6px 10px 6px 34px; }
                    .rotasi-timeline::before { content:''; position:absolute; left:14px; top:10px; bottom:10px; width:2px; background:#e9edf2; }
                    .rotasi-item { position:relative; margin-bottom:14px; }
                    .rotasi-item:last-child { margin-bottom:0; }
                    .rotasi-item .r-dot { position:absolute; left:-27px; top:14px; width:16px; height:16px; border-radius:50%; border:3px solid #fff; }
                    .rotasi-card { background:#fff; border:1px solid #eef2f7; border-left:4px solid #cbd5e1; border-radius:12px; padding:12px 16px; box-shadow:0 2px 10px rgba(0,0,0,.04); transition:.2s; }
                    .rotasi-card:hover { box-shadow:0 8px 20px rgba(0,0,0,.08); transform:translateX(2px); }
                    .rotasi-aktif { border-left-color:#16a34a; background:linear-gradient(90deg,#f0fdf4,#fff); }
                    .rotasi-aktif .r-dot { background:#16a34a; box-shadow:0 0 0 4px rgba(22,163,74,.18); }
                    .rotasi-selesai { border-left-color:#94a3b8; opacity:.85; }
                    .rotasi-selesai .r-dot { background:#94a3b8; }
                    .rotasi-datang { border-left-color:#f59e0b; }
                    .rotasi-datang .r-dot { background:#f59e0b; }
                    .r-room { font-weight:700; color:#1f2937; font-size:.98rem; }
                    .r-pill { font-size:.68rem; font-weight:700; padding:2px 9px; border-radius:20px; }
                </style>
                <div class="p-2">
                    <div class="rotasi-timeline">
                        @foreach($mahasiswa->roomSequences->sortBy('start_date') as $seq)
                            @php
                                $today = now()->format('Y-m-d');
                                if ($today >= $seq->start_date && $today <= $seq->end_date) { $cls='rotasi-aktif'; $lbl='Aktif'; $pill='bg-success text-white'; }
                                elseif ($today > $seq->end_date) { $cls='rotasi-selesai'; $lbl='Selesai'; $pill='bg-secondary text-white'; }
                                else { $cls='rotasi-datang'; $lbl='Akan Datang'; $pill='bg-warning text-dark'; }
                                $kat = $seq->ruangan->kategori ?? 'non_shift';
                            @endphp
                            <div class="rotasi-item">
                                <span class="r-dot"></span>
                                <div class="rotasi-card {{ $cls }}">
                                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                        <div>
                                            <div class="r-room"><i class="bi bi-door-open me-1 text-muted"></i>{{ $seq->ruangan->nm_ruangan }}</div>
                                            <div class="small text-muted mt-1">
                                                <i class="bi bi-calendar3 me-1"></i>{{ \Carbon\Carbon::parse($seq->start_date)->format('d M') }} – {{ \Carbon\Carbon::parse($seq->end_date)->format('d M Y') }}
                                                <span class="r-pill {{ $kat=='shift'?'bg-info text-dark':'bg-light text-dark border' }} ms-2">{{ strtoupper($kat) }}</span>
                                            </div>
                                        </div>
                                        <span class="r-pill {{ $pill }}">{{ $lbl }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

        </div>
  

    {{-- MODAL DETAIL ALPHA --}}
    <div class="modal fade" id="alphaModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-danger text-white py-2">
                    <h6 class="modal-title fw-bold mb-0">Detail Tanggal Alpha</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0" style="max-height: 300px; overflow-y: auto;">
                    @if(count($listAlpha) > 0)
                        <ul class="list-group list-group-flush">
                            @foreach($listAlpha as $tgl)
                                <li class="list-group-item d-flex justify-content-between align-items-center small px-3">
                                    <span>{{ $tgl }}</span>
                                    <span class="badge bg-danger rounded-pill">A</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="text-center p-4">
                            <i class="bi bi-emoji-smile text-success display-4"></i>
                            <p class="small text-muted mt-2 mb-0">Hebat! Tidak ada alpha tercatat.</p>
                        </div>
                    @endif
                </div>
                <div class="modal-footer p-2 bg-light justify-content-center">
                    <small class="text-muted" style="font-size: 0.65rem;">*Alpha = Tidak Hadir & Tidak Izin di Hari Kerja.</small>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL ID CARD & SCRIPT LAINNYA TETAP DI BAWAH SINI --}}
    {{-- (Pastikan script JS Chart & Calendar ada di bawah) --}}
    </div>

    {{-- MODAL GENERATE ID CARD --}}
    <div class="modal fade" id="idCardModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-dark fs-6">Preview ID Card</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body bg-light text-center py-4">
{{-- Capture Area --}}
<div id="captureArea" class="d-inline-block">
@php
    // Bersihkan nama prodi dan jadikan huruf kapital semua agar mudah dicek
    $prodiMhs = strtoupper(trim($mahasiswa->prodi));
    $instansi = $instansiName ?? $mahasiswa->univ_asal ?? 'Instansi Tidak Diketahui';

    // 1. KATA KUNCI PENENTU KATEGORI
    // Tambahkan kata kunci jurusan kesehatan di sini jika ada yang kurang
    $kesehatanKeywords = ['KEPERAWATAN', 'FARMASI', 'KEBIDANAN', 'GIZI', 'KESEHATAN', 'MEDIK', 'RADIOLOGI', 'FISIOTERAPI', 'ANALIS', 'TLM', 'ARS', 'ELEKTROMEDIK', 'DOKTER', 'KEDOKTERAN', 'PSIKOLOGI'];
    $profesiKeywords = ['PROFESI', 'PPDS', 'KOAS', 'NERS', 'APOTEKER'];

    // 2. DETEKSI STATUS
    $isProfesi = \Illuminate\Support\Str::contains($prodiMhs, $profesiKeywords);
    $isKesehatan = \Illuminate\Support\Str::contains($prodiMhs, $kesehatanKeywords);
    // Cek apakah ada kata "SMK" di nama prodinya
    $isSMK = \Illuminate\Support\Str::startsWith($prodiMhs, 'SMK') || \Illuminate\Support\Str::contains($prodiMhs, 'SMK ');

    // 3. TERAPKAN ATURAN SESUAI KETENTUAN RSUD SLG
    $themeClass = '';
    $tingkatSupervisi = '';

    if ($isProfesi) {
        // ATURAN 1: Semua Profesi -> Merah (Bawaan), Moderat Tinggi
        $themeClass = ''; 
        $tingkatSupervisi = 'Moderat Tinggi';
    } elseif ($isKesehatan) {
        // ATURAN 2: Semua Kesehatan (termasuk SMK) KECUALI Profesi -> Hitam, Tinggi
        $themeClass = 'theme-black';
        $tingkatSupervisi = 'Tinggi';
    } else {
        // JIKA BUKAN KESEHATAN SAMA SEKALI
        if ($isSMK) {
            // ATURAN 3: SMK Non-Kesehatan -> Kuning, Moderat
            $themeClass = 'theme-yellow'; 
            $tingkatSupervisi = 'Moderat';
        } else {
            // ATURAN 4: S2, S1, D4, D3 Non-Kesehatan -> Hijau, Rendah
            $themeClass = 'theme-green';
            $tingkatSupervisi = 'Rendah';
        }
    }
@endphp
    <div class="id-card-wrapper {{ $themeClass }}">
        
        {{-- Pattern --}}
        <div class="card-bg-decoration"></div>

        {{-- BACKGROUND LOGOS (KIRI & KANAN) --}}
        <img src="{{ asset('icon.png') }}" class="card-big-bg-logo bg-left" alt="bg-icon">
        <img src="{{ asset('logors.png') }}" class="card-big-bg-logo bg-right" alt="bg-rs">

        {{-- Header --}}
        <div class="card-header-shape">
            <div class="card-title-text">
                <h6>Kartu Tanda</h6>
                <h4>Peserta Magang Rsud Simpang Lima Gumul</h4>
            </div>
        </div>

        {{-- Photo --}}
        <div class="card-photo-container">
            @if($mahasiswa->foto_path)
                <img src="{{ asset($mahasiswa->foto_path) }}" class="card-photo" alt="Foto">
            @else
                <div class="card-photo d-flex align-items-center justify-content-center text-secondary">
                    <i class="bi bi-person-fill display-4"></i>
                </div>
            @endif
        </div>

        {{-- Content --}}
        <div class="card-content">
            <div class="card-name">{{ $mahasiswa->nm_mahasiswa }}</div>
            <div class="card-role">MAGANG / INTERNSHIP</div>

            <div class="detail-box">
                <div class="detail-row">
                    <span class="detail-label">INSTANSI</span>
                    <span class="detail-val">{{ Str::limit($instansiName, 20) }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">PRODI</span>
                <span class="detail-val">{{ Str::words($mahasiswa->prodi, 3, '...') }}</span>
                </div>
                
                {{-- LOGIKA SUPERVISI --}}
                                                   {{-- LOGIKA SUPERVISI (Hilang jika Non-Kesehatan) --}}
                                   <div class="detail-row">
    <span class="detail-label">SUPERVISI</span>
    <span class="detail-val text-primary">{{ $tingkatSupervisi }}</span>
</div>

                
                <div class="detail-row">
                    <span class="detail-label">BERLAKU S/D</span>
                    <span class="detail-val text-danger">{{ \Carbon\Carbon::parse($mahasiswa->tanggal_berakhir)->format('d/m/Y') }}</span>
                </div>
            </div>
        </div>

        {{-- QR Code --}}
        <div class="card-qr-section text-center">
            <div class="card-qr">
                <img src="https://quickchart.io/qr?text={{ urlencode(route('absensi.card', $mahasiswa->share_token)) }}&size=150" alt="QR" crossorigin="anonymous">
            </div>
            <span class="qr-text">SCAN ME</span>
        </div>

        {{-- Footer --}}
        <div class="card-footer-shape"></div>
    </div>
</div>

                    {{-- Download Button --}}
                    <div class="mt-4">
                        <p class="text-muted small mb-3">Tekan tombol di bawah untuk menyimpan ID Card ini.</p>
                        <button onclick="downloadIdCard()" class="btn btn-danger rounded-pill px-4 shadow-sm" style="background-color: #7c1316; border: none;">
                            <i class="bi bi-download me-2"></i> Download ID Card (PNG)
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    
<script>
        // 1. Chart Donat
        document.addEventListener("DOMContentLoaded", function() {
            const ctx = document.getElementById('attendanceChart');
            if (ctx) {
                new Chart(ctx.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Hadir', 'Alpha', 'Sisa'],
                        datasets: [{
                            data: [{{ $totalHadir }}, {{ $alpha }}, {{ $chartSisa }}],
                            backgroundColor: ['#198754', '#dc3545', '#ffc107'], borderWidth: 0, hoverOffset: 4
                        }]
                    },
                    options: { responsive: true, cutout: '75%', plugins: { legend: { display: false } } }
                });
            }
        });

        // 2. FullCalendar (Responsif: List di HP, Grid di PC)
      // 2. FullCalendar (Responsif: Grid Modern PC & Mobile Dots)
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    if (calendarEl) {
        var calendar = new FullCalendar.Calendar(calendarEl, {
            // Paksa view month (grid) untuk semua device, karena kita sudah modif CSS-nya untuk mobile
            initialView: 'dayGridMonth', 
            locale: 'id',
            buttonText: {
        today: 'Hari Ini' 
    },
            headerToolbar: { 
                left: 'prev,next', 
                center: 'title', 
                right: 'today' // Ganti listMonth jadi today (Kembali ke Hari Ini)
            },
            contentHeight: 'auto',
            // fixedWeekCount: false, // Biar gak ada baris kosong berlebih di akhir bulan
            events: {!! json_encode($events) !!}, 
            
            // Custom Render HTML untuk Event
            eventContent: function(arg) {
                let props = arg.event.extendedProps;
                let title = arg.event.title;
                let jam   = props.jam || '';
                let ruang = props.ruang || '';
                
                // Ambil warna dari backend, kalau tidak ada set default ke maroon
                let bgColor = arg.event.backgroundColor || 'var(--custom-maroon)';

                // Template ini akan dirender secara pintar oleh CSS (di PC jadi kotak, di HP jadi titik)
                let htmlFormat = `
                    <div class="custom-event-wrapper" style="--event-color: ${bgColor}" title="${title} (${ruang})">
                        <div class="evt-dot"></div>
                        <div class="evt-text">
                            <div class="evt-title">${title}</div>
                            ${jam ? `<div class="evt-time">${jam} - ${ruang}</div>` : ''}
                        </div>
                    </div>
                `;
                return { html: htmlFormat };
            },
            
            // Saat di klik, tetap muncul SweetAlert (Nyaman banget buat user HP)
            eventClick: function(info) {
                let props = info.event.extendedProps;
                let color = info.event.backgroundColor || '#7c1316';
                
                Swal.fire({
                    title: info.event.title,
                    html: `
                        <div class="text-start mt-2">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-clock me-2" style="color: ${color};"></i> 
                                <b>Jam:</b> &nbsp; ${props.jam || '-'}
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-geo-alt me-2" style="color: ${color};"></i> 
                                <b>Ruang:</b> &nbsp; ${props.ruang || '-'}
                            </div>
                        </div>
                    `,
                    icon: 'info',
                    iconColor: color,
                    confirmButtonColor: '#7c1316',
                    confirmButtonText: 'Tutup'
                });
            }
        });
        calendar.render();
    }
});
        // 3. Download ID Card Function
        function downloadIdCard() {
            const element = document.getElementById('captureArea');
            html2canvas(element, { scale: 3, backgroundColor: null }).then(canvas => {
                const link = document.createElement('a');
                link.download = 'ID-Card-{{ \Illuminate\Support\Str::slug($mahasiswa->nm_mahasiswa) }}.png';
                link.href = canvas.toDataURL('image/png');
                link.click();
            });
        }
    
       // --- Grafik Donat ---
document.addEventListener("DOMContentLoaded", function() {
    const ctx = document.getElementById('attendanceChart');
    if (ctx) {
        new Chart(ctx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Hadir', 'Alpha', 'Sisa'],
                datasets: [{
                    data: [{{ $totalHadir }}, {{ $alpha }}, {{ $chartSisa }}],
                    backgroundColor: ['#198754', '#dc3545', '#ffc107'],
                    borderWidth: 0, 
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true, 
                maintainAspectRatio: false, 
                cutout: '75%', 
                plugins: { 
                    legend: { display: false }, 
                    tooltip: { enabled: true } 
                }
            }
        });
    }
});
        // --- Fungsi Download ID Card (Tetap Sama) ---
        function downloadIdCard() {
            const element = document.getElementById('captureArea');
            const button = document.querySelector('button[onclick="downloadIdCard()"]');
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="bi bi-hourglass-split me-2"></i> Generating...';
            button.disabled = true;

            html2canvas(element, { scale: 3, backgroundColor: null, useCORS: true }).then(canvas => {
                const link = document.createElement('a');
                link.download = 'ID-Card-Magang-{{ Str::slug($mahasiswa->nm_mahasiswa) }}.png';
                link.href = canvas.toDataURL('image/png');
                link.click();
                button.innerHTML = originalText;
                button.disabled = false;
            }).catch(err => {
                console.error(err);
                alert('Gagal meng-generate ID Card. Cek Console Log.');
                button.innerHTML = originalText;
                button.disabled = false;
            });
        }
//           document.addEventListener("DOMContentLoaded", function() {
            
//             // --- 1. NOTIFIKASI PEMUTIHAN (KODE BARU DISINI) ---
//       Swal.fire({
//     title: '⚠️ Prosedur Baru Izin & Dispensasi',
//     icon: 'warning',
//     confirmButtonColor: '#7c1316',
//     confirmButtonText: 'Saya Mengerti & Siap Mematuhi',
//     html: `
//         <div class="text-start">
//             <p class="mb-3">Halo! Mulai saat ini, seluruh pengajuan <b>Izin, Sakit, maupun Keterlambatan</b> wajib mengikuti ketentuan berikut:</p>
            
//             <div class="p-3 mb-3 rounded-3" style="background-color: #fff5f5; border: 1px dashed #7c1316;">
//                 <i class="bi bi-exclamation-triangle-fill text-danger me-1"></i> 
//                 <span class="fw-bold text-dark">Wajib Template & Hard Copy</span>
//                 <p class="mb-0 small text-muted mt-1">
//                     Pengajuan tidak lagi manual tulis tangan bebas. Gunakan <b>Template Resmi</b>, cetak (hard copy), lalu scan untuk diunggah ke sistem.
//                 </p>
//             </div>

//             <ul class="mb-3 text-muted small" style="list-style: none; padding-left: 0;">
//                 <li class="mb-2 d-flex align-items-start">
//                     <i class="bi bi-x-circle-fill text-danger me-2 mt-1"></i>
//                     <span><b>Larangan Keras:</b> Dilarang keras memalsukan tanda tangan Karu/Atasan. Pelanggaran akan dikenakan sanksi disiplin berat.</span>
//                 </li>
//                 <li class="mb-2 d-flex align-items-start">
//                     <i class="bi bi-check-circle-fill text-success me-2 mt-1"></i>
//                     <span><b>Validitas Dokumen:</b> Dokumen yang diunggah harus berupa hasil scan dari berkas fisik yang sudah ditandatangani basah.</span>
//                 </li>
//                   <li class="mb-2 d-flex align-items-start">
//                     <i class="bi bi-check-circle-fill text-success me-2 mt-1"></i>
//                     <span><b>Validitas Dokumen:</b> Dokumen yang diunggah harus berupa hasil scan dari berkas fisik yang sudah ditandatangani basah.</span>
//                 </li>
//                 <li class="mb-2 d-flex align-items-start">
//                     <i class="bi bi-clock-history text-primary me-2 mt-1"></i>
//                     <span><b>Izin Terlambat:</b> Prosedur ini juga berlaku bagi mahasiswa yang datang terlambat agar status absensi tetap tertib.</span>
//                 </li>
//             </ul>

//             <p class="mb-0 small text-muted fst-italic text-center border-top pt-2">
//                 Pastikan dokumen fisik disimpan sebagai bukti autentik jika sewaktu-waktu diminta oleh Admin.
//             </p>
//         </div>
//     `,
//     background: '#fff',
//     backdrop: `rgba(124, 19, 22, 0.4)`, // Backdrop disesuaikan ke nuansa Maroon
//     allowOutsideClick: false
// });

//             // --- 2. Grafik Donat (KODE LAMA) ---
//             const ctx = document.getElementById('attendanceChart').getContext('2d');
//             new Chart(ctx, {
//                 type: 'doughnut',
//                 data: {
//                     labels: ['Hadir', 'Alpha', 'Sisa'],
//                     datasets: [{
//                         data: [{{ $totalHadir }}, {{ $alpha }}, {{ $chartSisa }}],
//                         backgroundColor: ['#198754', '#dc3545', '#ffc107'],
//                         borderWidth: 0, hoverOffset: 4
//                     }]
//                 },
//                 options: {
//                     responsive: true, maintainAspectRatio: false, cutout: '75%',
//                     plugins: { legend: { display: false }, tooltip: { enabled: true } }
//                 }
//             });
//         });

    </script>
@endsection