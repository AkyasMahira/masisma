<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sindikat | Sistem Informasi Pendidikan dan Pelatihan Diklat</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --primary: #7c1316;
            --primary-dark: #5f0f12;
            --accent: #f7aa3b;
            --soft: #fff7f5;
            --ink: #1c1c1c;
            --muted: #5f646f;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: "Plus Jakarta Sans", system-ui, -apple-system, sans-serif;
            color: var(--ink);
            background: radial-gradient(circle at 10% 20%, #ffe8e3 0, #fff9f6 20%, #ffffff 45%);
            overflow-x: hidden;
        }

        a { color: inherit; text-decoration: none; }

        /* NAVBAR */
        .glass-nav {
            position: sticky;
            top: 0;
            z-index: 20;
            padding: 16px 0;
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.9);
            border-bottom: 1px solid rgba(124, 19, 22, 0.08);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 800;
            font-size: 20px;
            color: var(--primary);
        }

        .brand img {
            height: 42px;
            width: 42px;
            border-radius: 10px;
            object-fit: cover;
            box-shadow: 0 10px 25px rgba(124, 19, 22, 0.15);
        }

        .nav-links a {
            font-weight: 600;
            color: var(--muted);
            transition: color 0.2s ease, transform 0.2s ease;
        }

        .nav-links a:hover {
            color: var(--primary);
            transform: translateY(-2px);
        }

        .btn-primary-soft, .btn-ghost {
            border-radius: 14px;
            padding: 10px 16px;
            font-weight: 700;
            border: 1px solid transparent;
            font-size: 14px;
        }

        .btn-primary-soft {
            background: linear-gradient(120deg, var(--primary), var(--primary-dark));
            color: #fff;
            box-shadow: 0 14px 30px rgba(124, 19, 22, 0.28);
        }

        .btn-primary-soft:hover { filter: brightness(1.03); }

        .btn-ghost {
            background: #ffffff;
            border-color: rgba(124, 19, 22, 0.12);
            color: var(--ink);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.04);
        }

        /* HERO */
        .hero {
            position: relative;
            padding: 80px 0 72px;
        }

        .hero::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at 85% 20%, rgba(247, 170, 59, 0.15), transparent 35%),
                radial-gradient(circle at 10% 10%, rgba(124, 19, 22, 0.12), transparent 28%);
            pointer-events: none;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(124, 19, 22, 0.08);
            color: var(--primary);
            font-weight: 700;
            letter-spacing: 0.2px;
            margin-bottom: 14px;
            font-size: 13px;
        }

        .hero h1 {
            font-size: clamp(32px, 4vw, 48px);
            font-weight: 800;
            line-height: 1.12;
            margin-bottom: 16px;
            color: var(--ink);
        }

        .hero p.lead {
            font-size: 18px;
            color: var(--muted);
            max-width: 640px;
        }

        .hero-pills {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
            gap: 12px;
            margin-top: 20px;
        }

        .pill {
            background: #fff;
            border-radius: 14px;
            padding: 12px 14px;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.04);
            border: 1px solid rgba(124, 19, 22, 0.08);
            font-size: 13px;
        }

        .pill-dot {
            height: 10px;
            width: 10px;
            border-radius: 999px;
            background: var(--primary);
        }

        .hero-visual {
            position: relative;
            z-index: 1;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.94);
            border: 1px solid rgba(124, 19, 22, 0.08);
            border-radius: 18px;
            padding: 16px;
            box-shadow: 0 24px 50px rgba(0, 0, 0, 0.08);
        }

        /* MINI CHART DI HERO */
        .mini-chart {
            margin-top: 16px;
            padding: 10px 12px;
            border-radius: 14px;
            background: #fff9f5;
            border: 1px dashed rgba(124, 19, 22, 0.25);
        }

        .mini-chart-title {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--muted);
            margin-bottom: 6px;
        }

        .mini-chart-bars {
            display: flex;
            align-items: flex-end;
            gap: 10px;
        }

        .mini-chart-bar {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            font-size: 10px;
            color: var(--muted);
        }

        .mini-chart-bar-inner {
            width: 100%;
            border-radius: 999px;
            background: rgba(124, 19, 22, 0.12);
            overflow: hidden;
            height: 52px;
            position: relative;
        }

        .mini-chart-bar-fill {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            border-radius: 999px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            height: 0;
            animation: barGrow 1.4s ease-out forwards;
        }

        .mini-chart-bar-label {
            font-size: 11px;
            font-weight: 600;
        }

        .mini-chart-bar-value {
            font-weight: 700;
            color: var(--primary);
            font-size: 11px;
        }

        @keyframes barGrow {
            from { height: 0; }
            to { height: var(--bar-height, 60%); }
        }

        /* MASKOT & FITUR (ICON GEDE) */
        .maskot-section {
            padding: 72px 0;
        }

        .maskot-wrapper {
            display: flex;
            flex-wrap: wrap;
            gap: 32px;
            align-items: center;
        }

        .maskot-image-wrap {
            width: 260px;
            max-width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
        }

        .maskot-image {
            height: 320px;
            width: 180px;
            border-radius: 40px;
            object-fit: cover;
            /* box-shadow: 0 26px 60px rgba(124, 19, 22, 0.35); */
        }

        .maskot-label {
            font-size: 12px;
            font-weight: 700;
            color: var(--primary);
            padding: 4px 10px;
            border-radius: 999px;
            background: #fff;
            border: 1px solid rgba(124, 19, 22, 0.18);
        }

        .maskot-features {
            flex: 1;
            min-width: 260px;
        }

        .maskot-features h2 {
            font-size: clamp(26px, 3vw, 34px);
            font-weight: 800;
            margin-bottom: 6px;
        }

        .maskot-features p.subtitle {
            color: var(--muted);
            max-width: 640px;
        }

        .feature-list {
            margin-top: 18px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 10px 24px;
        }

        .feature-item {
            display: flex;
            gap: 10px;
            align-items: flex-start;
        }

        .feature-index {
            height: 26px;
            width: 26px;
            border-radius: 999px;
            background: rgba(124, 19, 22, 0.08);
            color: var(--primary);
            font-weight: 800;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-top: 2px;
        }

        .feature-item-title {
            font-weight: 700;
            font-size: 14px;
        }

        .feature-item-text {
            font-size: 13px;
            color: var(--muted);
        }

        /* SECTION GENERIC */
        .section {
            padding: 72px 0;
        }

        .section h2 {
            font-size: clamp(26px, 3vw, 36px);
            font-weight: 800;
            margin-bottom: 12px;
            color: var(--ink);
        }

        .section p.subtitle {
            color: var(--muted);
            max-width: 740px;
        }

        .section-soft {
            background: radial-gradient(circle at top, #fff3f4 0, #ffffff 55%);
        }

        /* FITUR GRID */
        .feature-card {
            height: 100%;
            border-radius: 16px;
            border: 1px solid rgba(124, 19, 22, 0.08);
            padding: 20px;
            background: #fff;
            box-shadow: 0 16px 36px rgba(0, 0, 0, 0.04);
        }

        .feature-card h5 {
            font-weight: 700;
            margin-bottom: 6px;
        }

        .feature-card p {
            font-size: 14px;
        }

        .feature-card ul {
            font-size: 13px;
        }

        /* LOGO CLOUD */
        .logo-cloud {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 18px;
            margin-top: 26px;
        }

        .logo-chip {
            background: #fff;
            border-radius: 14px;
            padding: 10px 12px;
            border: 1px solid rgba(124, 19, 22, 0.08);
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.03);
            font-size: 13px;
        }

        .logo-chip img {
            height: 30px;
            object-fit: contain;
        }

        .logo-chip span {
            font-weight: 600;
            color: var(--muted);
        }

        /* PROGRAM LIST */
        .program-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 18px;
            margin-top: 24px;
        }

        .program-card {
            border-radius: 18px;
            border: 1px solid rgba(124, 19, 22, 0.08);
            padding: 18px;
            background: #fff;
            box-shadow: 0 16px 32px rgba(0,0,0,0.04);
        }

        .program-label {
            display: inline-flex;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            background: rgba(124, 19, 22, 0.06);
            color: var(--primary);
            margin-bottom: 4px;
        }

        .program-card h5 {
            font-weight: 700;
            margin-bottom: 4px;
        }

        .program-card p {
            font-size: 13px;
            color: var(--muted);
            margin-bottom: 8px;
        }

        /* KENAPA SINDIKAT */
        .why-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
            gap: 16px;
            margin-top: 24px;
        }

        .why-card {
            padding: 18px;
            border-radius: 18px;
            background: #fff;
            border: 1px solid rgba(124,19,22,0.08);
        }

        .why-title {
            font-weight: 700;
            margin-bottom: 4px;
            font-size: 14px;
        }

        .why-text {
            font-size: 13px;
            color: var(--muted);
        }

        /* STATISTICS */
        .stats-strip {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            margin-top: 24px;
        }

        .stat-pill {
            flex: 1;
            min-width: 160px;
            background: #fff;
            border-radius: 16px;
            padding: 14px 16px;
            border: 1px solid rgba(124,19,22,0.08);
            box-shadow: 0 10px 28px rgba(0,0,0,0.03);
        }

        .stat-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--muted);
            margin-bottom: 4px;
        }

        .stat-value {
            font-size: 20px;
            font-weight: 800;
            color: var(--primary);
        }

        /* FAQ */
        .faq-list {
            margin-top: 20px;
        }

        .faq-item {
            border-radius: 14px;
            padding: 14px 16px;
            background: #fff;
            border: 1px solid rgba(124,19,22,0.08);
            margin-bottom: 8px;
        }

        .faq-q {
            font-weight: 700;
            font-size: 14px;
        }

        .faq-a {
            font-size: 13px;
            color: var(--muted);
            margin-top: 4px;
        }

        /* CTA */
        .cta {
            text-align: center;
            background: linear-gradient(135deg, rgba(124, 19, 22, 0.95), #bb3035);
            color: #fff;
            border-radius: 22px;
            padding: 46px 26px;
            box-shadow: 0 26px 45px rgba(124, 19, 22, 0.25);
        }

        .cta p { color: rgba(255, 255, 255, 0.88); }

        footer {
            padding: 24px 0 40px;
            color: var(--muted);
            font-size: 14px;
        }

        /* CHATBOT */
        .chat-launcher {
            position: fixed;
            bottom: 18px;
            right: 18px;
            height: 58px;
            width: 58px;
            border-radius: 50%;
            border: none;
            background: linear-gradient(130deg, var(--primary), var(--primary-dark));
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 20px 40px rgba(124, 19, 22, 0.35);
            z-index: 50;
            cursor: pointer;
            padding: 0;
        }

        .chat-launcher img {
            height: 35px;
            width: 35px;
            border-radius: 12px;
            object-fit: cover;
        }

        .chat-panel {
            position: fixed;
            bottom: 86px;
            right: 18px;
            width: 340px;
            max-width: 92vw;
            background: #fff;
            border-radius: 18px;
            border: 1px solid rgba(124, 19, 22, 0.15);
            box-shadow: 0 25px 55px rgba(0, 0, 0, 0.14);
            overflow: hidden;
            transform: translateY(12px);
            opacity: 0;
            pointer-events: none;
            transition: all 0.2s ease;
            z-index: 40;
        }

        .chat-panel.open {
            transform: translateY(0);
            opacity: 1;
            pointer-events: auto;
        }

        .chat-header {
            padding: 12px 14px;
            background: linear-gradient(135deg, rgba(124, 19, 22, 0.95), #bb3035);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .chat-body {
            padding: 14px;
            max-height: 320px;
            overflow-y: auto;
            background: #fffdfb;
        }

        .chat-message {
            margin-bottom: 10px;
            padding: 10px 12px;
            border-radius: 12px;
            max-width: 90%;
            box-shadow: 0 6px 16px rgba(0,0,0,0.06);
            font-size: 13px;
        }

        .chat-message.bot {
            background: #fff;
            border: 1px solid rgba(124,19,22,0.12);
        }

        .chat-message.user {
            background: var(--primary);
            color: #fff;
            margin-left: auto;
        }

        .chat-input {
            display: flex;
            gap: 8px;
            padding: 10px 12px;
            border-top: 1px solid rgba(124, 19, 22, 0.12);
            background: #fff;
        }

        .chat-input input {
            flex: 1;
            border-radius: 10px;
            border: 1px solid rgba(124, 19, 22, 0.2);
            padding: 10px 12px;
            font-size: 14px;
        }

        .chat-input button {
            border: none;
            background: var(--primary);
            color: #fff;
            border-radius: 10px;
            padding: 10px 14px;
            font-weight: 700;
            font-size: 13px;
        }

        /* ANIMATIONS */
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-8px); }
            100% { transform: translateY(0px); }
        }

        .float-icon {
            animation: float 4.5s ease-in-out infinite;
        }

        .reveal-on-scroll {
            opacity: 0;
            transform: translateY(16px);
            transition: opacity 0.4s ease, transform 0.4s ease;
        }

        .reveal-visible {
            opacity: 1;
            transform: translateY(0);
        }

        @media (max-width: 991px) {
            .nav-links { display: none; }
            .hero { padding-top: 56px; }
            .maskot-wrapper { flex-direction: column; }
            .maskot-image-wrap { margin: 0 auto 10px; }
        }
    </style>
</head>
<body>
<header class="glass-nav">
    <div class="container d-flex align-items-center justify-content-between">
        <div class="brand">
            <img src="{{ asset('icon.png') }}" alt="Maskot Sindikat">
            <span>Sindikat</span>
        </div>
        <nav class="nav-links d-none d-md-flex align-items-center gap-4">
            <a href="#fitur">Fitur</a>
            <a href="#maskot">Maskot & fitur</a>
            <a href="#program">Program</a>
            <a href="#faq">FAQ</a>
        </nav>
        <div class="d-flex gap-2">
            <a class="btn btn-ghost" href="#fitur">Fitur</a>
            <a class="btn btn-primary-soft" href="{{ route('login') }}">Masuk / Daftar</a>
        </div>
    </div>
</header>

<main>
    <!-- HERO -->
    <section class="hero" id="awal">
        <div class="container">
            <div class="row align-items-center g-4">
                <div class="col-lg-6 reveal-on-scroll">
                    <div class="eyebrow">
                        Sindikat — Sistem Informasi Pendidikan dan Pelatihan Diklat
                    </div>
                    <h1>Kelola magang, pra-penelitian, dan pelatihan tanpa tab terpisah.</h1>
                    <p class="lead mb-3">
                        Sindikat menyatukan pengajuan, absensi, sertifikat, ruangan, dan pelatihan dalam satu aplikasi yang mengikuti alur kerja tim diklat RSUD SLG.
                    </p>
                    <div class="d-flex flex-wrap align-items-center gap-3 mb-3">
                        <a class="btn btn-primary-soft" href="{{ route('login') }}">Masuk ke aplikasi</a>
                        <a class="btn btn-ghost" href="#alur">Lihat alur kerja</a>
                    </div>
                    <div class="hero-pills">
                        <div class="pill">
                            <span class="pill-dot"></span>
                            <span>Dashboard status pengajuan dan peserta</span>
                        </div>
                        <div class="pill">
                            <span class="pill-dot"></span>
                            <span>Absensi & sertifikat otomatis</span>
                        </div>
                        <div class="pill">
                            <span class="pill-dot"></span>
                            <span>Penempatan ruangan & jadwal pelatihan</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 hero-visual reveal-on-scroll">
                    <div class="glass-card">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div>
                                <p class="mb-1 text-uppercase small fw-bold text-secondary">Contoh ringkasan</p>
                                <h5 class="mb-0 fw-bold">Data program aktif</h5>
                            </div>
                            <span class="badge text-bg-warning text-dark px-3 py-2" style="border-radius: 12px;">Simulasi tampilan</span>
                        </div>
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="d-flex align-items-center justify-content-between p-3 rounded-3"
                                     style="background:#fff4f2;border:1px solid rgba(124,19,22,0.1);">
                                    <div>
                                        <div class="fw-bold text-dark">Pengajuan baru</div>
                                        <small class="text-muted">Magang & pra-penelitian masuk antrean yang sama</small>
                                    </div>
                                    <span class="badge text-bg-light text-dark rounded-pill">Tinjau di dashboard</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 rounded-3 border" style="border-color:rgba(124,19,22,0.1)!important;">
                                    <div class="text-muted small mb-1">Absensi</div>
                                    <div class="fw-bold fs-6">Check-in</div>
                                    <small class="text-muted">Token unik untuk tiap peserta</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 rounded-3 border" style="border-color:rgba(124,19,22,0.1)!important;">
                                    <div class="text-muted small mb-1">Sertifikat</div>
                                    <div class="fw-bold fs-6">Unduh instan</div>
                                    <small class="text-muted">Siap setelah rekap kehadiran</small>
                                </div>
                            </div>
                        </div>

                        <!-- MINI CHART -->
                  @php
    $mhs = $chart['mahasiswa'] ?? 0;
    $pengajuan = $chart['pengajuan'] ?? 0;
    $pelatihan = $chart['pelatihan'] ?? 0;

    // biar ga bagi 0
    $maxChart = max(1, $mhs, $pengajuan, $pelatihan);

    // minimal 10%, maksimal 100% biar tetap kelihatan
    $hMhs       = max(10, round(($mhs       / $maxChart) * 100));
    $hPengajuan = max(10, round(($pengajuan / $maxChart) * 100));
    $hPelatihan = max(10, round(($pelatihan / $maxChart) * 100));
@endphp

<div class="mini-chart">
    <div class="mini-chart-title">Ringkasan cepat (diambil dari data Sindikat)</div>
    <div class="mini-chart-bars">
        <div class="mini-chart-bar">
            <div class="mini-chart-bar-inner">
                <div class="mini-chart-bar-fill" style="--bar-height: {{ $hMhs }}%;"></div>
            </div>
            <div class="mini-chart-bar-label">Mahasiswa</div>
            <div class="mini-chart-bar-value">{{ $mhs }}</div>
        </div>
        <div class="mini-chart-bar">
            <div class="mini-chart-bar-inner">
                <div class="mini-chart-bar-fill" style="--bar-height: {{ $hPengajuan }}%;"></div>
            </div>
            <div class="mini-chart-bar-label">Pengajuan</div>
            <div class="mini-chart-bar-value">{{ $pengajuan }}</div>
        </div>
        <div class="mini-chart-bar">
            <div class="mini-chart-bar-inner">
                <div class="mini-chart-bar-fill" style="--bar-height: {{ $hPelatihan }}%;"></div>
            </div>
            <div class="mini-chart-bar-label">Pelatihan</div>
            <div class="mini-chart-bar-value">{{ $pelatihan }}</div>
        </div>
    </div>
</div>


                        <div class="mt-3 d-flex align-items-center justify-content-between">
                            <small class="text-muted">Angka di atas hanya contoh tampilan, bukan data riil.</small>
                            <span class="badge text-bg-light text-dark rounded-pill">Akses internal</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- MASKOT & FITUR (ICON GEDE) -->
    <section class="maskot-section section-soft" id="maskot">
        <div class="container">
            <div class="maskot-wrapper">
                <div class="maskot-image-wrap reveal-on-scroll">
                    <img src="{{ asset('icon.png') }}" alt="Maskot Sindikat" class="maskot-image float-icon">
                    {{-- <span class="maskot-label">Sindikat</span> --}}
                </div>
                <div class="maskot-features reveal-on-scroll">
                    <div class="eyebrow">Pendidikan, Pelatihan dan Penelitian</div>
                    <h2>Diklat RSUD Simpang Lima Gumul</h2>
                    <p class="subtitle">
                        Sindikat dilengkapi chatbot internal yang membantu menjawab pertanyaan umum seputar data diklat di rumah sakit.
                    </p>
                    <div class="feature-list">
                        <div class="feature-item">
                            <div class="feature-index">1</div>
                            <div>
                                <div class="feature-item-title">Ringkasan data cepat</div>
                                <div class="feature-item-text">Chatbot bisa menyajikan ringkasan jumlah mahasiswa, pengajuan, pelatihan, dan mitra langsung dari data di sistem.</div>
                            </div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-index">2</div>
                            <div>
                                <div class="feature-item-title">Pertanyaan umum dijawab otomatis</div>
                                <div class="feature-item-text">Intent seperti “status pengajuan”, “ruangan kosong”, atau “absensi hari ini” dipetakan ke jawaban yang konsisten.</div>
                            </div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-index">3</div>
                            <div>
                                <div class="feature-item-title">Fokus pada alur diklat RS</div>
                                <div class="feature-item-text">Bahasa dan alur jawaban disesuaikan dengan istilah yang biasa dipakai tim diklat dan unit pelayanan.</div>
                            </div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-index">4</div>
                            <div>
                                <div class="feature-item-title">Aman di dalam aplikasi</div>
                                <div class="feature-item-text">Chatbot berjalan di server Sindikat dan hanya mengakses data yang memang sudah ada di sistem, bukan layanan eksternal.</div>
                            </div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-index">5</div>
                            <div>
                                <div class="feature-item-title">Mengarahkan ke dashboard</div>
                                <div class="feature-item-text">Untuk hal yang sifatnya pribadi (misalnya pengajuan milik sendiri), chatbot akan mengarahkan pengguna ke dashboard setelah login.</div>
                            </div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-index">6</div>
                            <div>
                                <div class="feature-item-title">Mudah dikembangkan</div>
                                <div class="feature-item-text">Intent dan jawaban disimpan di kode yang rapih, sehingga tim pengembang bisa menambah pola kalimat baru tanpa mengubah struktur besar.</div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        {{-- <small class="text-muted">Maskot yang sama di tombol chat membantu pengguna memahami bahwa itu jalur bertanya seputar Sindikat.</small> --}}
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FITUR UTAMA -->
    <section class="section" id="fitur">
        <div class="container">
            <div class="mb-4 reveal-on-scroll">
                <div class="eyebrow">Fitur utama</div>
                <h2>Disiapkan untuk alur administrasi magang RS.</h2>
                <p class="subtitle">
                    Sindikat memusatkan pengajuan, monitoring mahasiswa, pengaturan ruangan, hingga pencetakan sertifikat tanpa perlu membuka banyak spreadsheet terpisah.
                </p>
            </div>
            <div class="row g-4">
                <div class="col-md-6 col-lg-4 reveal-on-scroll">
                    <div class="feature-card">
                        <h5 class="fw-bold">Pengajuan & verifikasi</h5>
                        <p class="text-muted mb-2">Magang dan pra-penelitian diproses dari jalur yang sama dengan status yang jelas.</p>
                        <ul class="mb-0 text-muted ps-3">
                            <li>Form mandiri plus unggah berkas</li>
                            <li>Riwayat keputusan dan catatan admin</li>
                            <li>Notifikasi status untuk pemohon</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 reveal-on-scroll">
                    <div class="feature-card">
                        <h5 class="fw-bold">Ruang & layanan</h5>
                        <p class="text-muted mb-2">Penempatan peserta disesuaikan kapasitas dan karakter layanan di rumah sakit.</p>
                        <ul class="mb-0 text-muted ps-3">
                            <li>Daftar ruangan dan kapasitas</li>
                            <li>Status tersedia / penuh</li>
                            <li>Mudah disesuaikan oleh admin</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 reveal-on-scroll">
                    <div class="feature-card">
                        <h5 class="fw-bold">Pelatihan</h5>
                        <p class="text-muted mb-2">Peserta pelatihan tercatat rapi dari undangan sampai sertifikat.</p>
                        <ul class="mb-0 text-muted ps-3">
                            <li>Import & export peserta</li>
                            <li>Perubahan data tertentu lewat link publik</li>
                            <li>Rekap kegiatan di dashboard</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 reveal-on-scroll">
                    <div class="feature-card">
                        <h5 class="fw-bold">Absensi & sertifikat</h5>
                        <p class="text-muted mb-2">Token dan sertifikat digital mengurangi pekerjaan salin nama satu per satu.</p>
                        <ul class="mb-0 text-muted ps-3">
                            <li>Kartu absensi berbasis QR</li>
                            <li>Rekap kehadiran cepat</li>
                            <li>Generate sertifikat PDF</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 reveal-on-scroll">
                    <div class="feature-card">
                        <h5 class="fw-bold">Manajemen mahasiswa</h5>
                        <p class="text-muted mb-2">Profil, status, dan laporan bisa diekspor kapan saja untuk kebutuhan laporan.</p>
                        <ul class="mb-0 text-muted ps-3">
                            <li>Import Excel & ekspor laporan</li>
                            <li>Monitoring progres individu</li>
                            <li>Akses terpisah admin & mahasiswa</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 reveal-on-scroll">
                    <div class="feature-card">
                        <h5 class="fw-bold">Dashboard & catatan</h5>
                        <p class="text-muted mb-2">Ringkasan visual membantu rapat koordinasi lebih singkat.</p>
                        <ul class="mb-0 text-muted ps-3">
                            <li>Grafik dan indikator kunci</li>
                            <li>Catatan singkat untuk staf, tersimpan di browser</li>
                            <li>Filter periode & universitas</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SIAPA YANG MENGGUNAKAN (LOGO CLOUD) -->
    <section class="section section-soft" id="pengguna">
        <div class="container">
            <div class="mb-3 reveal-on-scroll">
                <div class="eyebrow">Siapa yang terbantu</div>
                <h2>Dipakai oleh tim RS dan institusi pendidikan.</h2>
                <p class="subtitle">
                    Tujuan utama Sindikat adalah membuat tim diklat, pembimbing lapangan, dan kampus asal mahasiswa melihat data yang sama, dengan konteks yang jelas.
                </p>
            </div>
            <div class="logo-cloud">
                <div class="logo-chip reveal-on-scroll">
                    <img src="https://rsudslg.kedirikab.go.id/asset_compro/img/logo/Logo.png" alt="Logo RSUD SLG">
                    <span>RSUD Simpang Lima Gumul</span>
                </div>
                <div class="logo-chip reveal-on-scroll">
                    <img src="{{ asset('icon.png') }}" alt="Maskot Sindikat">
                    <span>Unit Diklat & SDM RS</span>
                </div>
                <div class="logo-chip reveal-on-scroll">
                    <img src="https://dummyimage.com/120x40/ffffff/7c1316&text=Universitas" alt="Universitas">
                    <span>Universitas & politeknik mitra</span>
                </div>
                <div class="logo-chip reveal-on-scroll">
                    <img src="https://dummyimage.com/120x40/ffffff/7c1316&text=Program+Studi" alt="Program Studi">
                    <span>Program studi pengirim mahasiswa</span>
                </div>
            </div>
        </div>
    </section>

    <!-- PROGRAM YANG DIDUKUNG -->
    <section class="section" id="program">
        <div class="container">
            <div class="mb-4 reveal-on-scroll">
                <div class="eyebrow">Program yang didukung</div>
                <h2>Satu sistem untuk beberapa jenis penempatan.</h2>
                <p class="subtitle">
                    Sindikat mengakomodasi alur yang sedikit berbeda antara magang, pra-penelitian, dan pelatihan, tanpa memecah sistem menjadi banyak aplikasi kecil.
                </p>
            </div>
            <div class="program-list">
                <div class="program-card reveal-on-scroll">
                    <span class="program-label">Magang / PKL</span>
                    <h5>Penempatan harian di unit layanan</h5>
                    <p>Pengajuan, jadwal, dan kehadiran mahasiswa yang ditempatkan di unit tertentu.</p>
                    <ul class="text-muted ps-3 mb-0">
                        <li>Penempatan per ruang/ruangan</li>
                        <li>Rekap hadir dan laporan akhir</li>
                    </ul>
                </div>
                <div class="program-card reveal-on-scroll">
                    <span class="program-label">Pra-penelitian</span>
                    <h5>Akses untuk pengumpulan data</h5>
                    <p>Surat pengantar kampus, izin, dan rekap kebutuhan data pendukung penelitian.</p>
                    <ul class="text-muted ps-3 mb-0">
                        <li>Dokumen persetujuan dan jadwal</li>
                        <li>Catatan penggunaan data</li>
                    </ul>
                </div>
                <div class="program-card reveal-on-scroll">
                    <span class="program-label">Pelatihan</span>
                    <h5>Workshop dan in house training</h5>
                    <p>Pencatatan kehadiran, materi, dan sertifikat peserta pelatihan internal.</p>
                    <ul class="text-muted ps-3 mb-0">
                        <li>Absensi per sesi</li>
                        <li>Generate sertifikat per peserta</li>
                    </ul>
                </div>
                <div class="program-card reveal-on-scroll">
                    <span class="program-label">Pengembangan ke depan</span>
                    <h5>Siap diperluas</h5>
                    <p>Struktur modul disiapkan agar bisa menampung jenis program lain di kemudian hari.</p>
                    <ul class="text-muted ps-3 mb-0">
                        <li>Tambah modul tanpa mengubah pondasi</li>
                        <li>Ruang integrasi dengan sistem RS lain</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- KENAPA SINDIKAT + STATISTIK -->
    <section class="section section-soft" id="alur">
        <div class="container">
            <div class="mb-3 reveal-on-scroll">
                <div class="eyebrow">Kenapa Sindikat</div>
                <h2>Dibuat mengikuti alur kerja tim diklat, bukan sebaliknya.</h2>
                <p class="subtitle">
                    Fokus pengembangan adalah mengurangi pekerjaan ganda: menulis nama, memindahkan data dari form ke spreadsheet, dan menyusun laporan akhir secara manual.
                </p>
            </div>
            <div class="why-grid">
                <div class="why-card reveal-on-scroll">
                    <div class="why-title">Mengurangi salin-tempel</div>
                    <div class="why-text">Data yang diisi peserta dipakai ulang untuk surat, absensi, dan sertifikat, sehingga risiko salah ketik berkurang.</div>
                </div>
                <div class="why-card reveal-on-scroll">
                    <div class="why-title">Transparan ke pemohon</div>
                    <div class="why-text">Mahasiswa bisa melihat status pengajuannya sendiri setelah login, tanpa harus menanyakan berulang kali ke admin.</div>
                </div>
                <div class="why-card reveal-on-scroll">
                    <div class="why-title">Terhubung dengan ruangan</div>
                    <div class="why-text">Penempatan ke ruangan mengikuti kapasitas dan catatan layanan yang sudah diinput sebelumnya.</div>
                </div>
                <div class="why-card reveal-on-scroll">
                    <div class="why-title">Konteks rumah sakit</div>
                    <div class="why-text">Istilah dan alurnya dibuat berdasarkan kebutuhan RSUD SLG, sehingga staf baru pun cepat beradaptasi.</div>
                </div>
            </div>

            <div class="stats-strip reveal-on-scroll">
                <div class="stat-pill">
                    <div class="stat-label">Mahasiswa / peserta (contoh)</div>
                    <div class="stat-value">120+</div>
                    <small class="text-muted">Dalam satu periode aktif</small>
                </div>
                <div class="stat-pill">
                    <div class="stat-label">Program dalam satu aplikasi</div>
                    <div class="stat-value">3</div>
                    <small class="text-muted">Magang, pra-penelitian, pelatihan</small>
                </div>
                <div class="stat-pill">
                    <div class="stat-label">Spreadsheet yang digantikan</div>
                    <div class="stat-value">4–6</div>
                    <small class="text-muted">Per program (perkiraan)</small>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ -->
    <section class="section" id="faq">
        <div class="container">
            <div class="mb-3 reveal-on-scroll">
                <div class="eyebrow">Pertanyaan umum</div>
                <h2>Yang sering ditanyakan sebelum memakai Sindikat.</h2>
                <p class="subtitle">
                    Beberapa hal ini biasanya muncul ketika tim mulai pindah dari spreadsheet ke aplikasi web.
                </p>
            </div>
            <div class="faq-list">
                <div class="faq-item reveal-on-scroll">
                    <div class="faq-q">Apakah data mahasiswa bisa diimpor dari Excel?</div>
                    <div class="faq-a">Bisa. Data awal bisa diimpor, lalu untuk batch berikutnya peserta bisa mengisi langsung dari formulir Sindikat.</div>
                </div>
                <div class="faq-item reveal-on-scroll">
                    <div class="faq-q">Bagaimana jika universitas belum ada di daftar mitra?</div>
                    <div class="faq-a">Admin dapat menambahkan mitra baru setelah proses MOU. Setelah itu, mahasiswa cukup memilih nama kampus dari daftar.</div>
                </div>
                <div class="faq-item reveal-on-scroll">
                    <div class="faq-q">Apakah peserta bisa mengedit datanya sendiri?</div>
                    <div class="faq-a">Beberapa data bisa diperbarui melalui link publik yang aman, selama pengajuan belum dikunci atau disetujui admin.</div>
                </div>
                <div class="faq-item reveal-on-scroll">
                    <div class="faq-q">Apakah Sindikat terhubung dengan sistem lain di RS?</div>
                    <div class="faq-a">Struktur data disiapkan untuk integrasi internal. Implementasi integrasi menyesuaikan kebijakan TI RSUD SLG.</div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA AKHIR -->
    <section class="section section-soft" id="tentang">
        <div class="container">
            <div class="cta reveal-on-scroll">
                <div class="eyebrow mb-2" style="background:rgba(255,255,255,0.16); color:#fff;">Siap digunakan</div>
                <h3 class="fw-bold mb-2">Tata kelola magang RS yang rapi tanpa spreadsheet berlapis.</h3>
                <p class="mb-3">
                    Sindikat dibuat untuk kebutuhan administrasi RS dan institusi pendidikan: cepat dioperasikan, aman, dan mudah dijelaskan ke tim baru.
                </p>
                <div class="d-flex flex-wrap justify-content-center gap-2">
                    <a class="btn btn-light fw-bold px-4" href="{{ route('login') }}">Masuk sekarang</a>
                    <a class="btn btn-ghost text-white border-0" style="background:rgba(255,255,255,0.12);" href="#fitur">Pelajari fitur</a>
                </div>
            </div>
        </div>
    </section>
</main>

<!-- CHATBOT -->
<button class="chat-launcher" id="chat-toggle" aria-label="Buka chatbot Sindikat">
    <img src="{{ asset('icon.png') }}" alt="Chatbot Sindikat">
</button>
<div class="chat-panel" id="chat-panel">
    <div class="chat-header">
        <div class="d-flex align-items-center gap-2">
            <img src="{{ asset('icon.png') }}" alt="Maskot Sindikat" style="height:32px;width:32px;border-radius:10px;object-fit:cover;">
            <div>
                <div class="fw-bold">Chatbot Sindikat</div>
                <small class="text-white-50">Tanya data dan fitur Sindikat</small>
            </div>
        </div>
        <button type="button" id="chat-close" class="btn btn-sm btn-light text-dark" style="border-radius:10px;">Tutup</button>
    </div>
    <div class="chat-body" id="chat-messages">
        <div class="chat-message bot">
            Hai! Saya bisa membantu menjelaskan data di Sindikat: jumlah mahasiswa, status pengajuan, pelatihan, ruangan, dan hal lain seputar sistem. Silakan ketik pertanyaanmu.
        </div>
    </div>
    <form class="chat-input" id="chat-form">
        <input type="text" id="chat-input-text" placeholder="Tanya data, contoh: status pengajuan" autocomplete="off" required>
        <button type="submit">Kirim</button>
    </form>
</div>

<footer>
    <div class="container d-flex flex-wrap align-items-center justify-content-between gap-2">
        <span>Sindikat &mdash; Sistem Informasi Pendidikan dan Pelatihan Diklat.</span>
        <span class="text-muted">Dirancang untuk tim Rumah Sakit dan institusi pendidikan.</span>
    </div>
</footer>

<script>
    (function() {
        const chatToggle = document.getElementById('chat-toggle');
        const chatPanel = document.getElementById('chat-panel');
        const chatClose = document.getElementById('chat-close');
        const chatForm = document.getElementById('chat-form');
        const chatInput = document.getElementById('chat-input-text');
        const chatMessages = document.getElementById('chat-messages');
        const endpoint = "{{ route('chatbot.ask') }}";
        const token = document.querySelector('meta[name=\"csrf-token\"]').getAttribute('content');

        function toggleChat() {
            chatPanel.classList.toggle('open');
            if (chatPanel.classList.contains('open')) {
                chatInput.focus();
            }
        }

        function appendMessage(text, sender = 'bot') {
            const bubble = document.createElement('div');
            bubble.className = `chat-message ${sender}`;
            bubble.textContent = text;
            chatMessages.appendChild(bubble);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        function appendLoading() {
            const bubble = document.createElement('div');
            bubble.className = 'chat-message bot';
            bubble.dataset.loading = 'true';
            bubble.textContent = 'Sedang merangkum data...';
            chatMessages.appendChild(bubble);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        function removeLoading() {
            const loading = chatMessages.querySelector('.chat-message.bot[data-loading=\"true\"]');
            if (loading) loading.remove();
        }

        chatToggle.addEventListener('click', toggleChat);
        chatClose.addEventListener('click', toggleChat);

        chatForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const message = chatInput.value.trim();
            if (!message) return;

            appendMessage(message, 'user');
            chatInput.value = '';
            appendLoading();

            fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ message })
            })
            .then(response => response.json())
            .then(data => {
                removeLoading();
                appendMessage(data.reply || 'Jawaban belum tersedia.', 'bot');
            })
            .catch(() => {
                removeLoading();
                appendMessage('Maaf, chatbot sedang tidak dapat diakses. Coba lagi nanti.', 'bot');
            });
        });

        // reveal-on-scroll
        const reveals = document.querySelectorAll('.reveal-on-scroll');
        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('reveal-visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.15 });

            reveals.forEach(el => observer.observe(el));
        } else {
            reveals.forEach(el => el.classList.add('reveal-visible'));
        }
    })();
</script>
</body>
</html>
