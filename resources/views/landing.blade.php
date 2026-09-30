<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sindikat | Sistem Informasi Pendidikan dan Pelatihan Diklat</title>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('icon.png') }}">
<link rel="shortcut icon" href="{{ asset('icon.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
    /* ================== TAMBAHAN CSS CHATBOT AI ================== */
        .chat-panel { width: 380px; }
        .chat-body { max-height: 400px; padding: 20px 14px; }
        
        .chat-message.bot { background: #f8fafc; border: 1px solid #e2e8f0; color: #1e293b; border-radius: 2px 16px 16px 16px; margin-bottom: 15px;}
        .chat-message.user { background: linear-gradient(135deg, var(--primary), var(--primary-dark)); color: #fff; border-radius: 16px 2px 16px 16px; margin-bottom: 15px;}
        
        /* Quick Replies */
        .chat-suggestions { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 15px; }
        .chat-suggestion-btn { background: #fff; border: 1px solid var(--primary); color: var(--primary); padding: 6px 12px; border-radius: 50px; font-size: 12px; font-weight: 600; cursor: pointer; transition: 0.2s;}
        .chat-suggestion-btn:hover { background: var(--primary); color: #fff; }

        /* Card Rekomendasi Pelatihan */
        .chat-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; margin-bottom: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.02);}
        .chat-card-body { padding: 12px; }
        .chat-card-title { font-weight: 800; font-size: 13px; margin-bottom: 4px; color: #0f172a;}
        .chat-card-meta { font-size: 11px; color: var(--muted); margin-bottom: 8px;}
        .chat-card-skill { font-size: 10px; font-weight: 700; color: #d97706; background: #fffbeb; padding: 2px 8px; border-radius: 4px; display: inline-block; margin-bottom: 10px;}
        .chat-card-actions { display: flex; gap: 6px; border-top: 1px solid #f1f5f9; padding-top: 10px;}
        .chat-card-btn { flex: 1; padding: 6px; font-size: 11px; font-weight: 700; border-radius: 6px; text-align: center; text-decoration: none; cursor: pointer; border: none;}
        .chat-btn-detail { background: #f1f5f9; color: #475569;} .chat-btn-detail:hover { background: #e2e8f0; }
        .chat-btn-daftar { background: var(--primary); color: #fff;} .chat-btn-daftar:hover { background: var(--primary-dark); color: #fff; }
    .maskot-card {
    background: #fff;
    border: 1px solid rgba(124, 19, 22, 0.08);
    border-radius: 24px;
    box-shadow: 0 15px 35px rgba(0,0,0,0.03);
    transition: transform 0.3s ease;
    height: 100%;
}

.maskot-card:hover {
    transform: translateY(-10px);
    border-color: var(--primary);
}

.maskot-circle {
    width: 140px;
    height: 140px;
    background: radial-gradient(circle, #fff7f5 0%, #ffe8e3 100%);
    border-radius: 50%;
    margin: 0 auto;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    border: 4px solid #fff;
    box-shadow: 0 10px 20px rgba(124,19,22,0.1);
}

.maskot-circle img {
    width: 90%;
    height: auto;
    object-fit: contain;
}
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
    right: 85px; /* Geser ke kiri biar nggak nempel/nabrak tombol floating */
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
    z-index: 1070; /* Pastikan z-index lebih tinggi dari tombol-tombol di bawahnya */
}

        .chat-panel.open {
            transform: translateY(0);
            opacity: 1;
            pointer-events: auto;
            z-index: 1070;
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
        /* --- POSISI FLOATING BUTTONS (TERURUT DARI BAWAH KE ATAS) --- */

/* 1. Chatbot Launcher (Paling Bawah) */
.chat-launcher {
    position: fixed;
    bottom: 18px;
    right: 18px;
    z-index: 1060;
}

/* Panel Chat menyesuaikan posisi di atas launcher-nya saat terbuka */
.chat-panel {
    position: fixed;
    bottom: 86px; 
    right: 18px;
    z-index: 6050;
}

/* 2. Manual Book / Buku Petunjuk (Kedua dari Bawah) */
.manual-launcher {
    position: fixed;
    bottom: 86px; /* 18px + 58px + 10px spacing */
    right: 18px;
    height: 58px;
    width: 58px;
    border-radius: 50%;
    border: none;
    background: linear-gradient(130deg, #f7aa3b, #d98a1f);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 10px 25px rgba(247, 170, 59, 0.35);
    z-index: 1060;
    cursor: pointer;
    transition: all 0.5s ease;
}

/* 3. SOP Launcher (Ketiga dari Bawah) */
.sop-launcher {
    position: fixed;
    bottom: 154px; /* 86px + 58px + 10px spacing */
    right: 18px;
    height: 58px;
    width: 58px;
    border-radius: 50%;
    border: none;
    background: linear-gradient(130deg, #20c997, #0ca678); /* Hijau Teal */
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 10px 25px rgba(32, 201, 151, 0.35);
    z-index: 1060;
    cursor: pointer;
    transition: all 0.5s ease;
}

/* 4. Peraturan Launcher (Paling Atas) */
.peraturan-launcher {
    position: fixed;
    bottom: 222px; /* 154px + 58px + 10px spacing */
    right: 18px;
    height: 58px;
    width: 58px;
    border-radius: 50%;
    border: none;
    background: linear-gradient(130deg, #0d6efd, #0b5ed7); /* Biru Bootstrap */
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 10px 25px rgba(13, 110, 253, 0.35);
    z-index: 1060;
    cursor: pointer;
    transition: all 0.5s ease;
}

.manual-launcher.rotating, .sop-launcher.rotating, .peraturan-launcher.rotating { 
    transform: rotate(360deg); 
}

.manual-launcher svg, .sop-launcher svg, .peraturan-launcher svg { 
    width: 26px; 
    height: 26px; 
    fill: white; 
}

/* --- CUSTOM MODAL REUSE --- */
#manualModal .modal-dialog, #sopModal .modal-dialog, #peraturanModal .modal-dialog {
    max-width: 850px;
}
#manualModal .modal-content, #sopModal .modal-content, #peraturanModal .modal-content {
    border-radius: 20px;
    border: none;
    overflow: hidden;
}
#manualModal .modal-header, #sopModal .modal-header, #peraturanModal .modal-header {
    background: linear-gradient(to right, #7c1316 0%, #7c1316 40%, #ffffff 100%);
    border-bottom: none;
    padding: 1.2rem;
}
#manualModal .modal-title, #sopModal .modal-title, #peraturanModal .modal-title {
    color: #ffffff;
    font-weight: 800;
    font-size: 1.1rem;
}
#manualModal .btn-close, #sopModal .btn-close, #peraturanModal .btn-close {
    opacity: 0.8;
}
        /* === PERATURAN PENELITIAN === */
.rules-wrap {
    margin-top: 22px;
}

.rules-hero {
    border-radius: 22px;
    padding: 18px 18px;
    background:
        radial-gradient(circle at 15% 20%, rgba(247,170,59,0.18), transparent 45%),
        radial-gradient(circle at 85% 10%, rgba(124,19,22,0.14), transparent 40%),
        rgba(255,255,255,0.92);
    border: 1px solid rgba(124, 19, 22, 0.10);
    box-shadow: 0 20px 50px rgba(0,0,0,0.06);
}

.rules-title {
    font-weight: 900;
    font-size: clamp(20px, 2.4vw, 28px);
    margin: 0 0 6px;
}

.rules-sub {
    color: var(--muted);
    font-size: 14px;
    margin: 0;
    max-width: 860px;
}

.rules-badges {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 12px;
}

.badge-soft {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 10px;
    border-radius: 999px;
    font-weight: 800;
    font-size: 12px;
    background: rgba(124, 19, 22, 0.07);
    color: var(--primary);
    border: 1px solid rgba(124, 19, 22, 0.12);
}

.badge-warn {
    background: rgba(247, 170, 59, 0.18);
    color: #7a4a00;
    border-color: rgba(247, 170, 59, 0.35);
}

.rules-grid {
    margin-top: 16px;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 12px;
}

.rules-card {
    background: #fff;
    border: 1px solid rgba(124,19,22,0.08);
    border-radius: 18px;
    padding: 16px;
    box-shadow: 0 14px 30px rgba(0,0,0,0.04);
}

.rules-card h5 {
    font-weight: 900;
    font-size: 14px;
    margin: 0 0 8px;
}

.rules-card ul {
    margin: 0;
    padding-left: 18px;
    color: var(--muted);
    font-size: 13px;
}

.rules-note {
    margin-top: 14px;
    border-radius: 18px;
    padding: 14px 16px;
    background: #fff9f5;
    border: 1px dashed rgba(124,19,22,0.26);
    color: var(--muted);
    font-size: 13px;
}

.rules-note b {
    color: var(--primary);
}

.rules-flow {
    margin-top: 14px;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 10px;
}

.flow-step {
    border-radius: 18px;
    padding: 14px 14px;
    background: #fff;
    border: 1px solid rgba(124,19,22,0.08);
    position: relative;
    overflow: hidden;
}

.flow-step::before {
    content: "";
    position: absolute;
    inset: 0;
    background: radial-gradient(circle at 10% 10%, rgba(124,19,22,0.10), transparent 55%);
    pointer-events: none;
}

.flow-no {
    height: 28px;
    width: 28px;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: 900;
    color: #fff;
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    box-shadow: 0 12px 24px rgba(124,19,22,0.22);
    margin-bottom: 8px;
}

.flow-title {
    font-weight: 900;
    margin: 0 0 2px;
    font-size: 14px;
}

.flow-desc {
    margin: 0;
    font-size: 13px;
    color: var(--muted);
}

.rules-accordion .accordion-item {
    border: 1px solid rgba(124,19,22,0.08);
    border-radius: 16px !important;
    overflow: hidden;
    margin-bottom: 10px;
    box-shadow: 0 12px 24px rgba(0,0,0,0.03);
}

.rules-accordion .accordion-button {
    font-weight: 900;
    color: var(--ink);
    background: rgba(255,255,255,0.95);
}

.rules-accordion .accordion-button:not(.collapsed) {
    color: var(--primary);
    background: rgba(124, 19, 22, 0.06);
    box-shadow: none;
}

.rules-accordion .accordion-body {
    font-size: 13px;
    color: var(--muted);
    background: #fffdfb;
}

.ketentuan-penolakan {
    border-radius: 16px;
    padding: 14px 16px;
    background: rgba(255, 59, 59, 0.06);
    border: 1px solid rgba(255, 59, 59, 0.18);
    margin-top: 12px;
}

.ketentuan-penolakan b {
    color: #b42318;
}

.signature-box {
    margin-top: 14px;
    border-radius: 18px;
    padding: 16px;
    background: #fff;
    border: 1px solid rgba(124,19,22,0.08);
}

.signature-box .sig-top {
    font-weight: 900;
    color: var(--primary);
    margin-bottom: 6px;
}

.signature-box .sig-meta {
    font-size: 13px;
    color: var(--muted);
    margin: 0;
}

    </style>
    <link href="https://cdn.jsdelivr.net/npm/dflip@1.0.0/css/dflip.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/dflip@1.0.0/css/themify-icons.min.css" rel="stylesheet">
<style>/* --- POSISI FLOATING BUTTONS --- */
.chat-panel {
    position: fixed;
    bottom: 154px; 
    right: 18px;
    z-index: 1050;
}

.manual-launcher {
    position: fixed;
    bottom: 86px; 
    right: 18px;
    height: 58px;
    width: 58px;
    border-radius: 50%;
    border: none;
    background: linear-gradient(130deg, #f7aa3b, #d98a1f);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 10px 25px rgba(247, 170, 59, 0.35);
    z-index: 1060;
    cursor: pointer;
    transition: all 0.5s ease;
}

.manual-launcher.rotating { transform: rotate(360deg); }
.manual-launcher svg { width: 28px; height: 28px; fill: white; }

/* --- CUSTOM MODAL --- */
#manualModal .modal-dialog {
    max-width: 850px; /* Ukuran modal tidak terlalu besar */
}

#manualModal .modal-content {
    border-radius: 20px;
    border: none;
    overflow: hidden;
}

#manualModal .modal-header {
    /* Gradasi dari Merah khas SLG ke Putih */
    background: linear-gradient(to right, #7c1316 0%, #7c1316 40%, #ffffff 100%);
    border-bottom: none;
    padding: 1.2rem;
}

#manualModal .modal-title {
    color: #ffffff; /* Warna teks di area merah */
    font-weight: 800;
    font-size: 1.1rem;
}

/* Pastikan tombol close terlihat di area putih */
#manualModal .btn-close {
    opacity: 0.8;
}

#df_manual_viewer {
    background: #f8f9fa;
}
/* MINI CHART DI HERO (BARU - HORIZONTAL PROGRESS) */
.mini-stats-container {
    margin-top: 16px;
    display: flex;
    flex-direction: column;
    gap: 14px;
    background: #fffdfb;
    border-radius: 16px;
    padding: 18px;
    border: 1px dashed rgba(124, 19, 22, 0.25);
}

.mini-stats-title {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--muted);
    margin-bottom: 4px;
}

.mini-stat-group {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.mini-stat-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.mini-stat-label {
    font-size: 13px;
    font-weight: 600;
    color: var(--muted);
    display: flex;
    align-items: center;
    gap: 8px;
}

.mini-stat-icon {
    width: 26px;
    height: 26px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.mini-stat-icon svg {
    width: 14px;
    height: 14px;
}

.mini-stat-value {
    font-size: 13px;
    font-weight: 800;
    color: var(--ink);
}

.mini-stat-bar-bg {
    width: 100%;
    height: 6px;
    background: rgba(124, 19, 22, 0.08);
    border-radius: 999px;
    overflow: hidden;
}

.mini-stat-bar-fill {
    height: 100%;
    border-radius: 999px;
    width: 0;
    animation: barGrowX 1.2s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

@keyframes barGrowX {
    from { width: 0; }
    to { width: var(--bar-width); }
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
            <a href="#maskot">Tentang</a>
            <!--<a href="#maskot">Maskot & fitur</a>-->
            <!--<a href="#program">Program</a>-->
            <!--<a href="#faq">FAQ</a>-->
            <a href="#peraturan-penelitian">Peraturan Penelitian</a>
             <a  href="#peraturan-magang">Peraturan Magang/PKL</a>
              <a  href="#pelatihan-tersedia">Pelatihan Tersedia</a>
              <a href="{{ route('evaluasi.public.form') }}">Evaluasi</a>
        </nav>
        <div class="d-flex gap-2">
            <!--<a class="btn btn-ghost" href="#fitur">Fitur</a>-->
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
                        <a class="btn btn-ghost" href="{{ route('evaluasi.public.form') }}"><i class="bi bi-clipboard2-check me-1"></i> Isi Evaluasi Diklat</a>
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
                                <p class="mb-1 text-uppercase small fw-bold text-secondary">Ringkasan</p>
                                <h5 class="mb-0 fw-bold">Data program aktif</h5>
                            </div>
                            <span class="badge text-bg-warning text-dark px-3 py-2" style="border-radius: 12px;">Data Terbaru</span>
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

        <!-- MINI CHART (NEW HORIZONTAL LAYOUT) -->
                        @php
                            $mhs = $chart['mahasiswa'] ?? 0;
                            $pengajuan = $chart['pengajuan'] ?? 0;
                            $pelatihan = $chart['pelatihan'] ?? 0;

                            // Biar nggak error devided by zero
                            $maxChart = max(1, $mhs, $pengajuan, $pelatihan);

                            // Bikin persentase untuk lebar bar (minimal 5% agar bar tetap terlihat sedikit meski data kecil)
                            $wMhs        = max(5, round(($mhs        / $maxChart) * 100));
                            $wPengajuan  = max(5, round(($pengajuan / $maxChart) * 100));
                            $wPelatihan  = max(5, round(($pelatihan / $maxChart) * 100));
                        @endphp

                        <div class="mini-stats-container">
                            <div class="mini-stats-title">Ringkasan Sistem Saat Ini</div>

                            <!-- Mahasiswa Aktif -->
                            <div class="mini-stat-group">
                                <div class="mini-stat-row">
                                    <div class="mini-stat-label">
                                        <div class="mini-stat-icon" style="background: rgba(124, 19, 22, 0.1); color: var(--primary);">
                                            <svg fill="currentColor" viewBox="0 0 16 16"><path d="M8.211 2.047a.5.5 0 0 0-.422 0l-7.5 3.5a.5.5 0 0 0 .025.917l7.5 3a.5.5 0 0 0 .372 0L14 7.14V13a1 1 0 0 0-1 1v2h3v-2a2 2 0 0 0-1-1.732V6.828l.825-.35a.5.5 0 0 0 0-.917l-7.5-3.5Z"/><path d="M4.176 9.032a.5.5 0 0 0-.656.327l-.5 1.7a.5.5 0 0 0 .294.605l4.5 1.8a.5.5 0 0 0 .372 0l4.5-1.8a.5.5 0 0 0 .294-.605l-.5-1.7a.5.5 0 0 0-.656-.327L8 10.466 4.176 9.032Z"/></svg>
                                        </div>
                                        Mahasiswa Aktif
                                    </div>
                                    <div class="mini-stat-value">{{ $mhs }} Peserta</div>
                                </div>
                                <div class="mini-stat-bar-bg">
                                    <div class="mini-stat-bar-fill" style="--bar-width: {{ $wMhs }}%; background: linear-gradient(90deg, var(--primary), var(--primary-dark));"></div>
                                </div>
                            </div>

                            <!-- Kerjasama Instansi -->
                            <div class="mini-stat-group">
                                <div class="mini-stat-row">
                                    <div class="mini-stat-label">
                                        <div class="mini-stat-icon" style="background: rgba(247, 170, 59, 0.15); color: #d98a1f);">
                                            <svg fill="currentColor" viewBox="0 0 16 16"><path d="M4.887 6.2l-.964-.165A2.5 2.5 0 0 0 1.5 8.5v5h13v-5a2.5 2.5 0 0 0-2.423-2.465l-.964.165A1.5 1.5 0 0 1 9.5 7.5V6h-3v1.5a1.5 1.5 0 0 1-1.613 1.7zM3 13.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0zm10 0a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0z"/></svg>
                                        </div>
                                        Instansi Mitra
                                    </div>
                                    <div class="mini-stat-value">{{ $pengajuan }} Instansi</div>
                                </div>
                                <div class="mini-stat-bar-bg">
                                    <div class="mini-stat-bar-fill" style="--bar-width: {{ $wPengajuan }}%; background: linear-gradient(90deg, #f7aa3b, #d98a1f);"></div>
                                </div>
                            </div>

                            <!-- Pelatihan -->
                            <div class="mini-stat-group">
                                <div class="mini-stat-row">
                                    <div class="mini-stat-label">
                                        <div class="mini-stat-icon" style="background: rgba(32, 201, 151, 0.15); color: #0ca678;">
                                            <svg fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M10.5 1a.5.5 0 0 1 .5.5v4a.5.5 0 0 1-1 0V4H1v10h14V4h-2.5a.5.5 0 0 1 0-1h3a.5.5 0 0 1 .5.5v11a.5.5 0 0 1-.5.5H.5a.5.5 0 0 1-.5-.5v-11A.5.5 0 0 1 .5 3h3V1.5a.5.5 0 0 1 .5-.5h6ZM6 3h4V2H6v1Z"/></svg>
                                        </div>
                                        Pelatihan
                                    </div>
                                    <div class="mini-stat-value">{{ $pelatihan }} Kegiatan</div>
                                </div>
                                <div class="mini-stat-bar-bg">
                                    <div class="mini-stat-bar-fill" style="--bar-width: {{ $wPelatihan }}%; background: linear-gradient(90deg, #20c997, #0ca678);"></div>
                                </div>
                            </div>

                        </div>


                        <div class="mt-3 d-flex align-items-center justify-content-between">
                            <small class="text-muted">Angka di atas diambil dari Sindikat.</small>
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
<section class="section" id="sobat-sindikat">
    <div class="container">
        <div class="text-center mb-5 reveal-on-scroll">
            <div class="eyebrow">Kenalan Yuk!</div>
            <h2>Sobat Sindikat</h2>
            <p class="subtitle mx-auto">Dua kawan setia yang akan menemani perjalanan belajarmu di RSUD Simpang Lima Gumul.</p>
        </div>

        <div class="row g-4 justify-content-center">
            <div class="col-md-5 col-lg-4 reveal-on-scroll">
                <div class="maskot-card text-center p-4">
                    <div class="maskot-circle mb-3">
                        <img src="{{ asset('1.png') }}" alt="Sindi" class="img-fluid">
                    </div>
                    <h4 class="fw-bold mb-1">Sindi</h4>
                    <span class="badge text-bg-danger mb-3 px-3 rounded-pill">Si Paling Administrasi</span>
                    <p class="text-muted small">Sindi adalah ahli dalam urusan berkas. Dari verifikasi pengajuan hingga urusan sertifikat digital, Sindi memastikan datamu akurat dan prosesnya transparan.</p>
                </div>
            </div>

            <div class="col-md-5 col-lg-4 reveal-on-scroll">
                <div class="maskot-card text-center p-4">
                    <div class="maskot-circle mb-3">
                        <img src="{{ asset('2.png') }}" alt="Dika" class="img-fluid">
                    </div>
                    <h4 class="fw-bold mb-1">Dika</h4>
                    <span class="badge text-bg-warning text-dark mb-3 px-3 rounded-pill">Si Paling Disiplin</span>
                    <p class="text-muted small">Dika mewakili semangat praktik di lapangan. Dia yang memandu alur orientasi, absensi QR-Code, hingga memastikan semua peserta didik mematuhi etika di rumah sakit.</p>
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
<!-- SECTION DAFTAR PELATIHAN TERSESIA -->
    <section class="section" id="pelatihan-tersedia">
        <div class="container">
            <div class="mb-5 text-center reveal-on-scroll">
                <div class="eyebrow">Program Unggulan</div>
                <h2>Daftar Pelatihan Tersedia</h2>
                <p class="subtitle mx-auto">Tingkatkan kompetensi Anda melalui program pelatihan internal dan eksternal kami.</p>
            </div>
            
            <div class="row g-4 justify-content-center">
                @forelse($kegiatanTersedia as $k)
                    <div class="col-md-6 col-lg-4 reveal-on-scroll">
                        <div class="program-card h-100 d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <span class="program-label {{ $k->jenis_kegiatan == 'internal' ? 'bg-primary text-white' : 'bg-danger text-white' }}">{{ strtoupper($k->jenis_kegiatan) }}</span>
                                <span class="badge bg-light text-dark border"><i class="bi bi-clock-history"></i> {{ $k->jpl ?? 0 }} JPL</span>
                            </div>
                            <h5 class="fw-bold mb-2">{{ $k->nama_kegiatan }}</h5>
                            <p class="small text-muted mb-3 flex-grow-1">{{ Str::limit($k->deskripsi, 100, '...') }}</p>
                            
                            <ul class="list-unstyled small text-muted mb-4">
                                <li class="mb-1"><i class="bi bi-calendar-event me-2 text-danger"></i> {{ \Carbon\Carbon::parse($k->tanggal_mulai)->format('d M Y') }}</li>
                                <li><i class="bi bi-geo-alt-fill me-2 text-danger"></i> {{ $k->platform }}</li>
                            </ul>
                            
                            @if($k->jenis_kegiatan == 'eksternal')
                                <a href="{{ route('public.kegiatan.daftar', $k->id) }}" class="btn btn-primary-soft w-100 mt-auto text-center"><i class="bi bi-pencil-square me-1"></i> Daftar Sekarang</a>
                            @else
                                <button class="btn btn-light w-100 mt-auto text-muted border" disabled><i class="bi bi-lock-fill me-1"></i> Internal RSUD</button>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <i class="bi bi-calendar-x fs-1 text-muted opacity-50 mb-3 d-block"></i>
                        <h5 class="fw-bold text-muted">Belum Ada Pelatihan</h5>
                        <p class="small text-muted">Jadwal pelatihan belum tersedia untuk saat ini.</p>
                    </div>
                @endforelse
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

   <section class="section section-soft" id="peraturan-magang">
        <div class="container">
            <div class="mb-3 reveal-on-scroll">
                <div class="eyebrow">Peraturan & Ketentuan</div>
                <h2>Peraturan Magang & Praktik Kerja Lapangan (PKL)</h2>
                <p class="subtitle">
                    Ringkasan syarat dan ketentuan peserta didik berdasarkan dokumen resmi RSUD SLG (Kediri, 01 Desember 2025).
                    Bagian ini dibuat agar peserta magang paham <b>alur penerimaan</b> dan <b>tata tertib</b> selama bertugas.
                </p>
            </div>
    
            <div class="rules-hero reveal-on-scroll">
                <div class="rules-title">Syarat-Syarat Magang — RSUD Simpang Lima Gumul</div>
                <p class="rules-sub">
                    Fokus utama: kompetensi peserta didik, keselamatan pasien (patient safety), serta kepatuhan terhadap SOP rumah sakit.
                </p>
    
                <div class="rules-badges">
                    <span class="badge-soft">⏱ Proses: 7–14 hari kerja</span>
                    <span class="badge-soft">🧾 Wajib: MoU Institusi + Surat Tugas</span>
                    <span class="badge-soft badge-warn">🏥 Wajib Orientasi (K3 & PPI)</span>
                    <span class="badge-soft badge-warn">🩺 Dilarang tindakan medis tanpa supervisi</span>
                </div>
    
                <div class="rules-note">
                    <b>Catatan penting:</b> Institusi pendidikan wajib memiliki Perjanjian Kerjasama (PKS/MoU) yang masih berlaku dengan RSUD SLG sebelum mengirimkan peserta didik.
                </div>
            </div>
    
            <div class="rules-grid reveal-on-scroll">
                <div class="rules-card">
                    <h5>1) Persyaratan Administratif</h5>
                    <ul>
                        <li>Surat permohonan resmi dari institusi pendidikan (min. 1 bulan sebelum pelaksanaan).</li>
                        <li>Daftar nama peserta didik beserta NIM dan jurusan/kompetensi.</li>
                        <li>Salinan <b>Perjanjian Kerjasama (MoU)</b> antara Institusi dan RSUD SLG yang masih aktif.</li>
                        <li>Surat keterangan sehat / bukti vaksinasi (sesuai ketentuan unit khusus).</li>
                        <li>Pas foto dan kelengkapan biodata peserta untuk pembuatan ID Card.</li>
                        <li>Menyelesaikan administrasi biaya praktik (sesuai SK Direktur tentang Tarif Diklat).</li>
                    </ul>
                </div>
    
                <div class="rules-card">
                    <h5>2) Etik & Tata Tertib</h5>
                    <ul>
                        <li><b>Bersedia dan komitmen untuk tidak menyebarkan informasi terkait pasien atau RS selama dan setelah praktik (PKL, Penelitian atau Pelatihan) di RSUD SLG.</b></li>
                        <li>Wajib menjaga <b>kerahasiaan medis</b> pasien (Medical Secrecy). <b>Dilarang keras menyebarkan data pasien atau data rahasia rumah sakit.</b></li>
                        <li>Dilarang keras memfoto/merekam pasien atau dokumen rekam medis untuk konten pribadi/medsos.</li>
                        <li>Berpenampilan rapi, menggunakan seragam institusi, dan sepatu tertutup.</li>
                        <li>Menerapkan 5S (Senyum, Salam, Sapa, Sopan, Santun) kepada pasien dan petugas.</li>
                        <li>Dilarang merokok, makan, atau bermain HP di area pelayanan/ruang perawatan.</li>
                        <li>Wajib mengenakan tanda pengenal (ID Card) peserta didik selama di lingkungan RS.</li>
                    </ul>
                </div>
    
                <div class="rules-card">
                    <h5>3) Persyaratan Teknis</h5>
                    <ul>
                        <li>Wajib mengikuti <b>Orientasi Umum</b> (Materi K3RS, PPI, Bantuan Hidup Dasar) sebelum terjun ke ruangan.</li>
                        <li>Pelaksanaan dinas mengikuti jadwal shift yang ditentukan Kepala Ruangan/CI.</li>
                        <li>Tindakan ke pasien harus dibawah <b>supervisi</b> instruktur klinik (CI).</li>
                        <li>Wajib mengisi Logbook kegiatan harian dan diparaf oleh pembimbing.</li>
                        <li>Izin tidak masuk hanya ditoleransi dengan surat dokter/keterangan valid.</li>
                    </ul>
                </div>
    
                <div class="rules-card">
                    <h5>4) Evaluasi & Sanksi</h5>
                    <ul>
                        <li><b>Sanksi Pidana:</b> Apabila terbukti menyebarkan data pasien atau data rahasia rumah sakit, akan langsung dikeluarkan dan <b>diproses secara hukum/ditindak pidana</b> sesuai peraturan perundang-undangan yang berlaku.</li>
                        <li>Apabila merusak alat RS akibat kelalaian, wajib mengganti kerugian.</li>
                        <li>Pelanggaran etik berat (tindakan asusila, pencurian, berkelahi) akan langsung <b>dikembalikan ke institusi</b>.</li>
                        <li>Ketidakhadiran tanpa keterangan wajib mengganti dinas (utang dinas) di hari lain.</li>
                        <li>Sertifikat hanya diberikan jika nilai dan absensi memenuhi standar kelulusan RS.</li>
                    </ul>
                </div>
            </div>
    
            <div class="rules-flow reveal-on-scroll">
                <div class="flow-step">
                    <div class="flow-no">1</div>
                    <p class="flow-title">Pengajuan</p>
                    <p class="flow-desc">Kirim surat permohonan + cek kuota & status MoU.</p>
                </div>
                <div class="flow-step">
                    <div class="flow-no">2</div>
                    <p class="flow-title">Verifikasi</p>
                    <p class="flow-desc">Konfirmasi penerimaan & penyelesaian administrasi.</p>
                </div>
                <div class="flow-step">
                    <div class="flow-no">3</div>
                    <p class="flow-title">Orientasi</p>
                    <p class="flow-desc">Pembekalan materi dasar RS (Wajib Hadir).</p>
                </div>
                <div class="flow-step">
                    <div class="flow-no">4</div>
                    <p class="flow-title">Pelaksanaan</p>
                    <p class="flow-desc">Praktik di ruangan sesuai jadwal & target kompetensi.</p>
                </div>
                <div class="flow-step">
                    <div class="flow-no">5</div>
                    <p class="flow-title">Penilaian</p>
                    <p class="flow-desc">Ujian/Presentasi kasus & penyerahan nilai.</p>
                </div>
            </div>
    
            <div class="mt-4 reveal-on-scroll">
                <div class="eyebrow">Detail ketentuan</div>
                <div class="accordion rules-accordion" id="accIntern">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="hM1">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#cM1" aria-expanded="true" aria-controls="cM1">
                                Ketentuan Kehadiran
                            </button>
                        </h2>
                        <div id="cM1" class="accordion-collapse collapse show" aria-labelledby="hM1" data-bs-parent="#accIntern">
                            <div class="accordion-body">
                                <ul class="mb-0 ps-3">
                                    <li>Kehadiran peserta didik harus mencapai <b>100%</b> dari total jam praktik.</li>
                                    <li>Keterlambatan lebih dari 15 menit dianggap tidak hadir dan wajib mengganti jam dinas.</li>
                                    <li>Izin sakit wajib melampirkan surat keterangan dokter dari faskes resmi.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
    
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="hM2">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#cM2" aria-expanded="false" aria-controls="cM2">
                                Alat Pelindung Diri (APD)
                            </button>
                        </h2>
                        <div id="cM2" class="accordion-collapse collapse" aria-labelledby="hM2" data-bs-parent="#accIntern">
                            <div class="accordion-body">
                                <div class="ketentuan-apd">
                                    Peserta didik wajib membawa/menyediakan APD dasar secara mandiri, meliputi:
                                    <ul class="mb-0 ps-3 mt-2">
                                        <li>Masker medis (sesuai standar).</li>
                                        <li>Handscoon (sarung tangan) non-steril secukupnya.</li>
                                        <li>Hand sanitizer pribadi (botol kecil).</li>
                                        <li>Face shield / Goggle (jika ditempatkan di ruang isolasi/berisiko tinggi).</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
    
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="hM3">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#cM3" aria-expanded="false" aria-controls="cM3">
                                Laporan Akhir
                            </button>
                        </h2>
                        <div id="cM3" class="accordion-collapse collapse" aria-labelledby="hM3" data-bs-parent="#accIntern">
                            <div class="accordion-body">
                                Setiap kelompok/individu wajib menyerahkan <b>Laporan Kegiatan PKL</b> (Softcopy & Hardcopy) 
                                kepada Diklat RSUD SLG sebelum penarikan mahasiswa, sebagai syarat pengambilan sertifikat/nilai.
                            </div>
                        </div>
                    </div>
                </div>
    
                <div class="signature-box">
                    <div class="sig-top">Referensi dokumen</div>
                    <p class="sig-meta mb-1">
                        Pemerintah Kabupaten Kediri — Dinas Kesehatan — RSUD Simpang Lima Gumul
                    </p>
                    <p class="sig-meta mb-1">
                        Kediri, <b>01 Desember 2025</b>
                    </p>
                    <p class="sig-meta mb-0">
                        Direktur RSUD SLG Kediri: <b>dr. Tony Widyanto, Sp.OG (K)</b>
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- PERATURAN PENELITIAN -->
    <section class="section section-soft" id="peraturan-penelitian">
        <div class="container">
            <div class="mb-3 reveal-on-scroll">
                <div class="eyebrow">Peraturan & Ketentuan</div>
                <h2>Peraturan Penelitian di RSUD SLG Kediri</h2>
                <p class="subtitle">
                    Ringkasan syarat dan ketentuan penelitian berdasarkan dokumen resmi RSUD SLG (Kediri, 01 Desember 2025).
                    Bagian ini dibuat agar pemohon paham <b>apa yang wajib disiapkan</b> dan <b>apa yang dilarang</b> sebelum mengajukan.
                </p>
            </div>
    
            <div class="rules-hero reveal-on-scroll">
                <div class="rules-title">Syarat-Syarat Penelitian — RSUD Simpang Lima Gumul</div>
                <p class="rules-sub">
                    Fokus utama: kelengkapan administrasi, etika & perlindungan data pasien, serta pelaksanaan yang tidak mengganggu pelayanan.
                </p>
    
                <div class="rules-badges">
                    <span class="badge-soft">⏱ Proses: 5–14 hari kerja</span>
                    <span class="badge-soft">🧾 Wajib: surat pengantar + proposal ACC</span>
                    <span class="badge-soft badge-warn">🔒 Data pasien wajib de-identifikasi</span>
                    <span class="badge-soft badge-warn">📷 Dilarang foto/video pasien tanpa izin</span>
                </div>
    
                <div class="rules-note">
                    <b>Catatan penting:</b> penelitian tidak boleh mengganggu pelayanan. Akses data rekam medis dilakukan melalui petugas resmi sesuai SOP.
                </div>
            </div>
    
            <div class="rules-grid reveal-on-scroll">
                <div class="rules-card">
                    <h5>1) Persyaratan Administratif</h5>
                    <ul>
                        <li>Surat pengantar resmi dari institusi pendidikan (ditandatangani pejabat berwenang).</li>
                        <li>Proposal lengkap yang sudah ACC dosen pembimbing: latar belakang, rumusan masalah & tujuan, metode, jadwal, kerangka konseptual.</li>
                        <li>Mengisi link form penelitian dari Diklat RSUD SLG.</li>
                        <li>Surat pernyataan: <b>Bersedia dan komitmen untuk tidak menyebarkan informasi terkait pasien atau RS selama dan setelah praktik (PKL, Penelitian atau Pelatihan) di RSUD SLG</b>, serta siap presentasi sebelum sidang kampus.</li>
                        <li>Menerima surat balasan dari Diklat RSUD SLG.</li>
                        <li>Pembayaran sesuai ketentuan SK Direktur (Tarif Pelayanan Diklat).</li>
                        <li><b>Ethical Clearance</b> (wajib sesuai kebutuhan penelitian).</li>
                    </ul>
                </div>
    
                <div class="rules-card">
                    <h5>2) Etik & Perlindungan Data</h5>
                    <ul>
                        <li>Tidak mengganggu pelayanan rumah sakit.</li>
                        <li>Wajib menjaga kerahasiaan identitas pasien (regulasi data kesehatan). <b>Larangan keras menyebarkan data pasien atau data rahasia rumah sakit lainnya dalam bentuk apapun.</b></li>
                        <li>Data pasien harus <b>de-identifikasi</b> (tanpa nama, NIK, alamat, nomor RM, dll).</li>
                        <li><b>Sanksi Pidana:</b> Apabila peneliti terbukti menyebarkan data pasien atau data rahasia rumah sakit, akan diproses secara hukum dan <b>ditindak pidana</b> sesuai peraturan perundang-undangan.</li>
                        <li>Dilarang mengambil foto/video pasien tanpa izin tertulis.</li>
                        <li>Wajib mengikuti aturan K3 & keselamatan pasien selama berada di area RS.</li>
                        <li>Interaksi dengan pasien wajib <b>informed consent</b>.</li>
                    </ul>
                </div>
    
                <div class="rules-card">
                    <h5>3) Persyaratan Teknis</h5>
                    <ul>
                        <li>Jadwal penelitian harus disetujui Koordinator Diklat dan unit terkait.</li>
                        <li>Koordinasi awal dengan pembimbing lahan/kepala ruangan terkait kebutuhan data.</li>
                        <li>Area tertentu tidak boleh diakses tanpa izin/pendamping (mis. ICU, OK, NICU).</li>
                        <li>Pengambilan data rekam medis lewat Petugas Rekam Medis sesuai SOP.</li>
                        <li>Alur penelitian harus jelas dan tertib administrasi.</li>
                    </ul>
                </div>
    
                <div class="rules-card">
                    <h5>4) Kewajiban Mahasiswa / Peneliti</h5>
                    <ul>
                        <li>Menggunakan identitas / kartu izin penelitian selama di RS.</li>
                        <li>Menjaga sopan santun, etika profesi, dan berpakaian rapi sesuai standar.</li>
                        <li>Tidak mengganggu kegiatan pelayanan dan menjaga ketertiban.</li>
                        <li>Mematuhi seluruh aturan keamanan dan keselamatan.</li>
                    </ul>
                </div>
            </div>
    
            <div class="rules-flow reveal-on-scroll">
                <div class="flow-step">
                    <div class="flow-no">1</div>
                    <p class="flow-title">Pengajuan</p>
                    <p class="flow-desc">Submit administrasi + proposal + form diklat.</p>
                </div>
                <div class="flow-step">
                    <div class="flow-no">2</div>
                    <p class="flow-title">Verifikasi</p>
                    <p class="flow-desc">Cek kelengkapan & kesesuaian kebutuhan data.</p>
                </div>
                <div class="flow-step">
                    <div class="flow-no">3</div>
                    <p class="flow-title">Persetujuan</p>
                    <p class="flow-desc">Koordinasi unit + penetapan jadwal pelaksanaan.</p>
                </div>
                <div class="flow-step">
                    <div class="flow-no">4</div>
                    <p class="flow-title">Pelaksanaan</p>
                    <p class="flow-desc">Pengambilan data sesuai SOP + etik penelitian.</p>
                </div>
                <div class="flow-step">
                    <div class="flow-no">5</div>
                    <p class="flow-title">Pelaporan & Presentasi</p>
                    <p class="flow-desc">Serahkan softfile + presentasi untuk mutu layanan.</p>
                </div>
            </div>
    
            <div class="mt-4 reveal-on-scroll">
                <div class="eyebrow">Detail ketentuan</div>
                <div class="accordion rules-accordion" id="accRules">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="h1">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#c1" aria-expanded="true" aria-controls="c1">
                                Kewajiban Pelaporan
                            </button>
                        </h2>
                        <div id="c1" class="accordion-collapse collapse show" aria-labelledby="h1" data-bs-parent="#accRules">
                            <div class="accordion-body">
                                <ul class="mb-0 ps-3">
                                    <li>Wajib menyerahkan <b>Laporan Hasil Penelitian (Soft File)</b> kepada Diklat RSUD SLG.</li>
                                    <li>Jika penelitian dipublikasikan, wajib melakukan <b>presentasi hasil</b> untuk kepentingan pengembangan mutu layanan RSUD SLG.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
    
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="h2">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#c2" aria-expanded="false" aria-controls="c2">
                                Ketentuan Penolakan
                            </button>
                        </h2>
                        <div id="c2" class="accordion-collapse collapse" aria-labelledby="h2" data-bs-parent="#accRules">
                            <div class="accordion-body">
                                <div class="ketentuan-penolakan">
                                    <b>Permohonan dapat ditolak</b> apabila:
                                    <ul class="mb-0 ps-3 mt-2">
                                        <li>Proposal belum ACC dosen pembimbing akademik.</li>
                                        <li>Data yang diminta bersifat rahasia / tidak dapat dibuka.</li>
                                        <li>Penelitian tidak memiliki kebermanfaatan untuk RSUD SLG.</li>
                                        <li>Berpotensi mengganggu pelayanan pasien.</li>
                                        <li>Tidak memiliki <b>ethical clearance</b> (untuk penelitian yang membutuhkan).</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
    
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="h3">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#c3" aria-expanded="false" aria-controls="c3">
                                Estimasi Waktu Proses
                            </button>
                        </h2>
                        <div id="c3" class="accordion-collapse collapse" aria-labelledby="h3" data-bs-parent="#accRules">
                            <div class="accordion-body">
                                Proses verifikasi dan persetujuan umumnya membutuhkan <b>5–14 hari kerja</b>,
                                bergantung pada kompleksitas penelitian dan kebutuhan koordinasi unit.
                            </div>
                        </div>
                    </div>
                </div>
    
                <div class="signature-box">
                    <div class="sig-top">Referensi dokumen</div>
                    <p class="sig-meta mb-1">
                        Pemerintah Kabupaten Kediri — Dinas Kesehatan — RSUD Simpang Lima Gumul
                    </p>
                    <p class="sig-meta mb-1">
                        Kediri, <b>01 Desember 2025</b>
                    </p>
                    <p class="sig-meta mb-0">
                        Direktur RSUD SLG Kediri: <b>dr. Tony Widyanto, Sp.OG (K)</b>
                    </p>
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
                    <div class="stat-label">Mahasiswa / peserta</div>
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
<button class="sop-launcher" id="sop-btn" title="Dokumen SOP">
    <svg viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
        <path d="M14.5 3a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h13zm-13-1A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-13z"/>
        <path d="M7 5.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5zm-1.146.146a.5.5 0 0 1 .708 0l2 2a.5.5 0 0 1-.708.708L6 6.707l-.646.647a.5.5 0 0 1-.708-.708l1-1zm0 3.5a.5.5 0 0 1 .708 0l2 2a.5.5 0 0 1-.708.708L6 9.707l-.646.647a.5.5 0 0 1-.708-.708l1-1zM7 8.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5z"/>
    </svg>
</button>

<button class="peraturan-launcher" id="peraturan-btn" title="Dokumen Peraturan">
    <svg viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
        <path d="M8.417 5.741c1.196-.059 2.44-.277 3.583-.616a.5.5 0 0 1 .613.416l.42 2.73a.5.5 0 0 1-.392.564 16.42 16.42 0 0 1-3.111.45.5.5 0 0 1-.508-.45l-.624-3.045a.5.5 0 0 1 .419-.549zm-1.66 0a.5.5 0 0 0-.42.55l.625 3.044a.5.5 0 0 0 .507.45 16.42 16.42 0 0 0 3.112-.45.5.5 0 0 0 .391-.564l-.42-2.73a.5.5 0 0 0-.613-.416c-1.144.339-2.387.557-3.583.616zM0 13.5A.5.5 0 0 1 .5 13h15a.5.5 0 0 1 0 1H.5a.5.5 0 0 1-.5-.5z"/>
        <path d="M2.5 13h11v-1.5a.5.5 0 0 0-.5-.5H3a.5.5 0 0 0-.5.5V13zm1-3.5h9v-1H3.5v1zM4 5h8v-1H4v1zm.5-2h7v-1h-7v1z"/>
    </svg>
</button>
<button class="manual-launcher" id="manual-btn" title="Manual Book">
    <svg viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
        <path d="M1 2.828c.885-.37 2.154-.769 3.388-.893 1.33-.134 2.458.063 3.112.752v9.746c-.935-.53-2.12-.603-3.213-.493-1.18.12-2.37.461-3.287.811V2.828zm7.5.144c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492V2.972zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783z"/>
    </svg>
</button>

<div class="modal fade" id="manualModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg">
            <div class="modal-header d-flex align-items-center">
                <h5 class="modal-title m-0">
                   Buku Petunjuk Aplikasi
                </h5>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div id="df_manual_viewer" 
                     class="_df_book" 
                     source="{{ asset('book.pdf') }}" 
                     style="height: 550px;">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="sopModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg">
            <div class="modal-header d-flex align-items-center">
                <h5 class="modal-title m-0">Standar Operasional Prosedur (SOP)</h5>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div id="df_sop_viewer" class="_df_book" source="{{ asset('sop.pdf') }}" style="height: 550px;"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="peraturanModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg">
            <div class="modal-header d-flex align-items-center">
                <h5 class="modal-title m-0">Surat Keputusan</h5>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div id="df_peraturan_viewer" class="_df_book" source="{{ asset('sk.pdf') }}" style="height: 550px;"></div>
            </div>
        </div>
    </div>
</div>
<!-- CHATBOT -->
<!-- CHATBOT -->
<button class="chat-launcher" id="chat-toggle" aria-label="Buka chatbot Sindikat">
    <img src="{{ asset('icon.png') }}" alt="Chatbot Sindikat">
</button>
<div class="chat-panel" id="chat-panel">
    <div class="chat-header">
        <div class="d-flex align-items-center gap-2">
            <img src="{{ asset('icon.png') }}" alt="Maskot Sindikat" style="height:32px;width:32px;border-radius:10px;object-fit:cover;">
            <div>
                <div class="fw-bold">Sindi - Asisten AI</div>
                <small class="text-white-50" id="chat-status">Pilih Mode Interaksi</small>
            </div>
        </div>
        <button type="button" id="chat-close" class="btn btn-sm btn-light text-dark py-0" style="border-radius:10px; font-size:12px;">Tutup</button>
    </div>
    
    <div class="chat-body" id="chat-messages">
        <!-- Welcoming Mode Selection -->
        <div class="chat-message bot" id="welcome-msg">
            Halo! Saya Sindi. Ada yang bisa saya bantu hari ini?<br><br>
            Pilih layanan di bawah ini:
        </div>
        <div class="chat-suggestions" id="mode-selection">
            <button class="chat-suggestion-btn" onclick="setChatMode('general')">❓ Tanya Informasi Umum</button>
            <button class="chat-suggestion-btn" onclick="setChatMode('pelatihan')">🎯 Cari Rekomendasi Pelatihan</button>
        </div>

        <!-- Form Identitas (Hidden by default) -->
        <div id="form-identitas" style="display: none;" class="bg-light p-3 border rounded-3 mb-3">
            <p class="small fw-bold mb-2">Sebelum lanjut, mohon isi data diri Kakak:</p>
            <input type="text" id="chat-nama" class="form-control form-control-sm mb-2" placeholder="Nama Panggilan" required>
            <input type="text" id="chat-profesi" class="form-control form-control-sm mb-2" placeholder="Profesi (Contoh: Perawat)" required>
            <button type="button" class="btn btn-sm w-100 text-white fw-bold" style="background: var(--primary);" onclick="startPelatihanMode()">Mulai Konsultasi AI</button>
        </div>
    </div>
    
    <form class="chat-input" id="chat-form" style="display: none;">
        <input type="text" id="chat-input-text" placeholder="Ketik pesan..." autocomplete="off" required>
        <button type="submit"><i class="bi bi-send-fill"></i></button>
    </form>
</div>

<!-- MODAL DETAIL REKOMENDASI PELATIHAN AI -->
<div class="modal fade" id="modalDetailAI" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" style="border-radius: 16px; border:none;">
            <div class="modal-header bg-light border-bottom-0">
                <h5 class="modal-title fw-bold text-dark" id="aiModalTitle">Detail Pelatihan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" id="aiModalBody">
                <!-- Konten di-inject via JS -->
            </div>
            <div class="modal-footer bg-light border-top-0 p-3" id="aiModalFooter">
                <!-- Tombol daftar di-inject via JS -->
            </div>
        </div>
    </div>
</div>
<footer>
    <div class="container d-flex flex-wrap align-items-center justify-content-between gap-2">
        <span>Sindikat &mdash; Sistem Informasi Pendidikan dan Pelatihan Diklat.</span>
        <span class="text-muted">Dirancang untuk tim Rumah Sakit dan institusi pendidikan.</span>
    </div>
</footer>
<!-- Masukkan jQuery, Bootstrap JS, dan dFlip JS terlebih dahulu jika belum ada -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/dflip@1.0.0/js/dflip.min.js"></script>

<script>
// =========================================================================
// 1. SETUP MODAL DOKUMEN (SOP, MANUAL, PERATURAN)
// =========================================================================
$(document).ready(function() {
    function setupFloatingModal(btnId, modalId) {
        const btn = $(btnId);
        const modalEl = document.getElementById(modalId);

        btn.on('click', function() {
            // Animasi putar icon sebentar
            $(this).addClass('rotating');
            
            // Trigger Bootstrap Modal
            const bModal = bootstrap.Modal.getOrCreateInstance(modalEl);
            bModal.show();

            setTimeout(() => $(this).removeClass('rotating'), 600);

            // Force resize DearFlip agar render PDF presisi di dalam modal
            setTimeout(() => {
                window.dispatchEvent(new Event('resize'));
            }, 400);
        });
    }

    // Eksekusi untuk ke-3 tombol dokumen
    setupFloatingModal('#manual-btn', 'manualModal');
    setupFloatingModal('#sop-btn', 'sopModal');
    setupFloatingModal('#peraturan-btn', 'peraturanModal');
});

// =========================================================================
// 2. CHATBOT & REKOMENDASI AI LOGIC
// =========================================================================
(function() {
    const chatToggle = document.getElementById('chat-toggle');
    const chatPanel = document.getElementById('chat-panel');
    const chatClose = document.getElementById('chat-close');
    const chatForm = document.getElementById('chat-form');
    const chatInput = document.getElementById('chat-input-text');
    const chatMessages = document.getElementById('chat-messages');
    
    let currentMode = null; // 'general' atau 'pelatihan'
    let userNama = '';
    let userProfesi = '';

    // Mengambil endpoint route dan CSRF token
    const endpoint = "{{ route('chatbot.ask') }}";
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    function toggleChat() {
        chatPanel.classList.toggle('open');
    }
    
    if(chatToggle) chatToggle.addEventListener('click', toggleChat);
    if(chatClose) chatClose.addEventListener('click', toggleChat);

    // FUNGSI GANTI MODE OLEH USER
    window.setChatMode = function(mode) {
        document.getElementById('mode-selection').style.display = 'none';
        
        if (mode === 'general') {
            currentMode = 'general';
            document.getElementById('chat-status').innerText = 'Mode: Informasi Umum';
            chatForm.style.display = 'flex';
            appendMessage("Baik, Kak! Silakan tanyakan informasi seputar pengajuan, absensi, atau peraturan di Sindikat.", 'bot');
            appendSuggestions(['Ringkasan data', 'Status pengajuan', 'Ruangan tersedia', 'Peraturan penelitian']);
        } else if (mode === 'pelatihan') {
            currentMode = 'pelatihan';
            document.getElementById('chat-status').innerText = 'Mode: Rekomendasi Pelatihan (AI)';
            document.getElementById('form-identitas').style.display = 'block';
        }
    };

    // MULAI MODE PELATIHAN SETELAH ISI NAMA
    window.startPelatihanMode = function() {
        const n = document.getElementById('chat-nama').value.trim();
        const p = document.getElementById('chat-profesi').value.trim();
        
        if(!n || !p) { 
            alert("Nama dan Profesi wajib diisi ya Kak!"); 
            return; 
        }
        
        userNama = n; 
        userProfesi = p;
        
        document.getElementById('form-identitas').style.display = 'none';
        chatForm.style.display = 'flex';
        
        // Pancing trigger pertama ke server untuk memunculkan pesan sapaan AI
        sendToServer('cari pelatihan');
    };

    function appendMessage(text, sender = 'bot') {
        const bubble = document.createElement('div');
        bubble.className = `chat-message ${sender}`;
        bubble.innerHTML = text; // Memungkinkan tag HTML (bold, italic) dari respon server
        chatMessages.appendChild(bubble);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    function appendSuggestions(suggestions) {
        if(!suggestions || suggestions.length === 0) return;
        const cont = document.createElement('div');
        cont.className = 'chat-suggestions';
        suggestions.forEach(s => {
            const btn = document.createElement('button');
            btn.className = 'chat-suggestion-btn';
            btn.textContent = s;
            btn.onclick = () => {
                chatInput.value = s;
                chatForm.dispatchEvent(new Event('submit'));
            };
            cont.appendChild(btn);
        });
        chatMessages.appendChild(cont);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    // RENDER CARD REKOMENDASI AI
    function appendRecommendationCards(cards) {
        const cont = document.createElement('div');
        cards.forEach(c => {
            // Encode tanda kutip agar tidak merusak struktur HTML onClick
            const rawData = c.raw_data.replace(/"/g, '&quot;');
            
            const card = document.createElement('div');
            card.className = 'chat-card';
            card.innerHTML = `
                <div class="chat-card-body">
                    <div class="chat-card-title">${c.nama}</div>
                    <div class="chat-card-meta"><i class="bi bi-calendar-event"></i> ${c.tanggal} &nbsp;|&nbsp; <i class="bi bi-clock"></i> ${c.jpl} JPL</div>
                    <div class="chat-card-skill"><i class="bi bi-star-fill"></i> Target: ${c.keahlian}</div>
                    <div class="chat-card-actions">
                        <button type="button" class="chat-card-btn chat-btn-detail" onclick="openDetailAI('${rawData}', '${c.url_daftar}')"><i class="bi bi-info-circle"></i> Detail</button>
                        <a href="${c.url_daftar}" class="chat-card-btn chat-btn-daftar" target="_blank"><i class="bi bi-pencil-square"></i> Daftar</a>
                    </div>
                </div>
            `;
            cont.appendChild(card);
        });
        chatMessages.appendChild(cont);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    // MENGIRIM PESAN (SUBMIT FORM CHATBOT)
    if(chatForm) {
        chatForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const message = chatInput.value.trim();
            if (!message) return;

            appendMessage(message, 'user');
            chatInput.value = '';
            
            // Buat Loading Indicator
            const loading = document.createElement('div');
            loading.className = 'chat-message bot';
            loading.id = 'chat-loading';
            loading.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menganalisa...';
            chatMessages.appendChild(loading);
            chatMessages.scrollTop = chatMessages.scrollHeight;

            sendToServer(message);
        });
    }

    function sendToServer(message) {
        fetch(endpoint, {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json', 
                'X-CSRF-TOKEN': token, 
                'Accept': 'application/json' 
            },
            body: JSON.stringify({ 
                message: message, 
                mode: currentMode, 
                nama: userNama, 
                profesi: userProfesi 
            })
        })
        .then(response => response.json())
        .then(data => {
            // Hapus animasi loading
            const loadingEl = document.getElementById('chat-loading');
            if(loadingEl) loadingEl.remove();
            
            // Render Text/Intro Response
            if(data.reply) {
                appendMessage(data.reply, 'bot');
            }
            
            // Render Cards jika tipe respon adalah rekomendasi
            if(data.type === 'recommendation' && data.cards) {
                appendRecommendationCards(data.cards);
            }
            
            // Render Suggestions / Pilihan cepat
            if(data.suggestions) {
                appendSuggestions(data.suggestions);
            }
        })
        .catch((error) => {
            console.error('Chatbot Error:', error);
            const loadingEl = document.getElementById('chat-loading');
            if(loadingEl) loadingEl.remove();
            appendMessage('Maaf, sistem AI sedang sibuk atau koneksi terputus. Coba lagi nanti.', 'bot');
        });
    }

    // FUNGSI MEMBUKA MODAL DETAIL DARI HASIL PENCARIAN CHATBOT
    window.openDetailAI = function(rawDataStr, urlDaftar) {
        // Dekode kembali string JSON
        const data = JSON.parse(rawDataStr);
        document.getElementById('aiModalTitle').innerText = data.nama;
        
        // Render List Tujuan
        let tujuanHtml = data.tujuan && data.tujuan.length > 0 
            ? '<ul class="small text-muted ps-3 mb-0">' + data.tujuan.map(t => `<li>${t}</li>`).join('') + '</ul>' 
            : '<p class="small text-muted mb-0">-</p>';

        // Render Table Kompetensi
        let kompHtml = '';
        if(data.kompetensi && data.kompetensi.length > 0) {
            kompHtml = '<table class="table table-sm table-bordered mt-2 mb-0"><thead class="bg-light"><tr><th>Kompetensi Dasar</th><th>Indikator Target</th></tr></thead><tbody>';
            data.kompetensi.forEach(k => {
                kompHtml += `<tr><td class="small fw-bold">${k.nama}</td><td class="small text-muted">${k.indikator || '-'}</td></tr>`;
            });
            kompHtml += '</tbody></table>';
        } else {
            kompHtml = '<p class="small text-muted mb-0">-</p>';
        }

        const bodyHtml = `
            <div class="row g-3">
                <div class="col-6">
                    <div class="p-3 bg-light rounded border h-100">
                        <small class="text-muted d-block text-uppercase fw-bold mb-1">Jadwal Pelaksanaan</small>
                        <span class="fw-bold text-danger">${data.tanggal}</span>
                    </div>
                </div>
                <div class="col-6">
                    <div class="p-3 bg-light rounded border h-100">
                        <small class="text-muted d-block text-uppercase fw-bold mb-1">Total Waktu</small>
                        <span class="fw-bold text-dark">${data.jpl || 0} Jam Pelajaran (JPL)</span>
                    </div>
                </div>
                <div class="col-12 mt-4">
                    <h6 class="fw-bold border-bottom pb-2">Deskripsi Pelatihan</h6>
                    <p class="small text-muted mb-0">${data.deskripsi || 'Tidak ada deskripsi spesifik.'}</p>
                </div>
                <div class="col-12 mt-4">
                    <h6 class="fw-bold border-bottom pb-2">Tujuan yang Diharapkan</h6>
                    ${tujuanHtml}
                </div>
                <div class="col-12 mt-4">
                    <h6 class="fw-bold border-bottom pb-2">Standar Kompetensi & Indikator</h6>
                    ${kompHtml}
                </div>
            </div>
        `;

        document.getElementById('aiModalBody').innerHTML = bodyHtml;
        document.getElementById('aiModalFooter').innerHTML = `
            <a href="${urlDaftar}" class="btn text-white fw-bold w-100" style="background:var(--primary);" target="_blank">
                Lanjutkan Pendaftaran <i class="bi bi-arrow-right ms-1"></i>
            </a>
        `;

        const modalAI = bootstrap.Modal.getOrCreateInstance(document.getElementById('modalDetailAI'));
        modalAI.show();
    };

// =========================================================================
// 3. REVEAL ON SCROLL (ANIMASI HALAMAN)
// =========================================================================
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
