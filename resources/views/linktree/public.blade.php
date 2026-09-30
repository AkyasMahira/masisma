<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $package->title }} | Sindikat RSUD Simpang Lima Gumul</title>
     <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('icon.png') }}">
    <link rel="shortcut icon" href="{{ asset('icon.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* ==========================================================================
           1. ROOT & GLOBAL STYLES
           ========================================================================== */
        :root { 
            --maroon: #7c1316; 
            --maroon-light: #a91c1f;
            --dark: #121212;
            --grey-light: #f8f9fa;
            --white: #ffffff;
            --transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body { 
            background-color: #f0f2f5; 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            color: var(--dark);
            overflow-x: hidden;
            background-image: 
                radial-gradient(at 0% 0%, rgba(124, 19, 22, 0.05) 0px, transparent 50%),
                radial-gradient(at 100% 0%, rgba(124, 19, 22, 0.05) 0px, transparent 50%);
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: var(--maroon); border-radius: 10px; }

        /* ==========================================================================
           2. LAYOUT COMPONENTS
           ========================================================================== */
        .page-container {
            max-width: 650px;
            margin: 0 auto;
            min-height: 100vh;
            position: relative;
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border-left: 1px solid rgba(255, 255, 255, 0.3);
            border-right: 1px solid rgba(255, 255, 255, 0.3);
            padding-bottom: 60px;
        }

        /* Logo Pill Section */
        .navbar-pill-wrapper {
            padding: 30px 20px;
            display: flex;
            justify-content: center;
            position: relative;
            z-index: 100;
        }

        .logo-pill {
            background: var(--white);
            padding: 15px 40px;
            border-radius: 100px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            gap: 30px;
            border: 1px solid rgba(124, 19, 22, 0.1);
            transition: var(--transition);
        }

        .logo-pill:hover { transform: translateY(-3px); box-shadow: 0 20px 40px rgba(124,19,22,0.15); }

        .logo-box { height: 45px; display: flex; align-items: center; }
        .logo-box.rsud img { height: 100%; width: auto; }
        .logo-box.sindikat img { height: 170%; width: auto; } /* SINDIKAT dibuat lebih besar */

        /* Banner Section */
        .hero-banner {
            width: 100%;
            height: 250px;
            margin-top: -60px; /* Offset agar pill melayang di atas */
            position: relative;
            overflow: hidden;
        }

        .banner-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 10s ease;
        }
        .hero-banner:hover .banner-image { transform: scale(1.1); }

        .banner-overlay {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background: linear-gradient(to bottom, transparent 40%, rgba(124, 19, 22, 0.8) 100%);
        }

        /* Title & Profile Section */
        .profile-card {
            margin: -50px 25px 30px 25px;
            background: var(--white);
            border-radius: 24px;
            padding: 35px 25px;
            text-align: center;
            box-shadow: 0 20px 40px rgba(0,0,0,0.08);
            position: relative;
            z-index: 10;
            border-bottom: 5px solid var(--maroon);
        }

        .package-title {
            color: var(--maroon);
            font-weight: 800;
            font-size: 1.7rem;
            margin-bottom: 10px;
            letter-spacing: -0.5px;
        }

        .full-name {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #666;
            font-weight: 700;
            margin-bottom: 20px;
            display: block;
        }

        .hospital-tag {
            background: #fff5f5;
            color: var(--maroon);
            padding: 8px 18px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.8rem;
            display: inline-flex;
            align-items: center;
            border: 1px solid rgba(124, 19, 22, 0.1);
        }

        /* ==========================================================================
           3. LINK COMPONENTS
           ========================================================================== */
        .link-container { padding: 0 25px; }

        .group-label {
            display: flex;
            align-items: center;
            margin: 40px 0 20px 0;
            padding-left: 10px;
        }

        .group-label span {
            font-weight: 800;
            text-transform: uppercase;
            font-size: 0.85rem;
            color: var(--dark);
            letter-spacing: 2px;
            position: relative;
        }

        .group-label::after {
            content: "";
            flex: 1;
            height: 2px;
            background: linear-gradient(to right, #ddd, transparent);
            margin-left: 20px;
        }

        /* Premium Link Button */
        .link-item {
            display: flex;
            align-items: center;
            background: var(--white);
            border-radius: 18px;
            padding: 18px 24px;
            margin-bottom: 15px;
            text-decoration: none !important;
            color: var(--dark);
            border: 1px solid rgba(0,0,0,0.05);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.02);
        }

        .link-item::before {
            content: "";
            position: absolute;
            left: 0; top: 0; height: 100%; width: 0;
            background: var(--maroon);
            transition: var(--transition);
            opacity: 0.1;
        }

        .link-item:hover {
            transform: translateY(-4px) scale(1.01);
            box-shadow: 0 12px 24px rgba(124,19,22,0.1);
            border-color: var(--maroon);
            color: var(--maroon);
        }

        .link-item:hover::before { width: 100%; }

        .item-icon {
            width: 48px;
            height: 48px;
            background: #f8f9fa;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
            color: var(--maroon);
            font-size: 1.3rem;
            transition: var(--transition);
            z-index: 1;
        }

        .link-item:hover .item-icon {
            background: var(--maroon);
            color: var(--white);
            transform: rotate(-10deg);
        }

        .item-text { flex: 1; z-index: 1; }
        .item-title { display: block; font-weight: 700; font-size: 1rem; margin-bottom: 2px; }
        .item-sub { display: block; font-size: 0.75rem; color: #888; }

        .item-arrow {
            opacity: 0.2;
            transition: var(--transition);
            z-index: 1;
        }
        .link-item:hover .item-arrow { opacity: 1; transform: translateX(5px); }

        /* ==========================================================================
           4. FOOTER & MISC
           ========================================================================== */
        .footer {
            text-align: center;
            padding: 60px 20px 40px 20px;
            margin-top: 40px;
        }

        .footer-logo { height: 150px; opacity: 0.4; filter: grayscale(1); }
        .footer-credit { font-size: 0.7rem; color: #999; font-weight: 600; letter-spacing: 1px; line-height: 1.8; }
        .footer-credit strong { color: var(--maroon); }

        /* Responsiveness */
        @media (max-width: 576px) {
            .page-container { margin: 0; border: none; }
            .logo-pill { padding: 12px 25px; gap: 20px; }
            .logo-box { height: 35px; }
            .package-title { font-size: 1.4rem; }
            .hero-banner { height: 200px; }
        }
    </style>
</head>
<body>

    <div class="page-container">
        
        <div class="navbar-pill-wrapper">
            <div class="logo-pill">
                <div class="logo-box rsud">
                    <img src="https://rsudslg.kedirikab.go.id/asset_compro/img/logo/Logo.png" alt="Logo RSUD SLG">
                </div>
                <div class="logo-box sindikat">
                    <img src="/icon.png" alt="Logo SINDIKAT">
                </div>
            </div>
        </div>

        <div class="hero-banner">
            @if($package->image)
                <img src="{{ asset('storage/'.$package->image) }}" class="banner-image" alt="Banner Landscape">
            @else
                <img src="https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?auto=format&fit=crop&q=80&w=1200" class="banner-image" alt="Default Banner">
            @endif
            <div class="banner-overlay"></div>
        </div>

        <div class="profile-card">
            <h1 class="package-title">{{ $package->title }}</h1>
            <!--<span class="full-name">Sistem Informasi Pendidikan Penelitian dan Pelatihan Diklat</span>-->
            
            <div class="hospital-tag">
                <i class="fas fa-hospital-user me-2"></i> RSUD Simpang Lima Gumul Kediri
            </div>
        </div>

        <div class="link-container">
            @foreach($package->items as $item)
                <div class="group-label">
                    <span>{{ $item->title }}</span>
                </div>
                
                @foreach($item->contents as $content)
                    <a href="{{ $content->value }}" target="_blank" class="link-item">
                        <div class="item-icon">
                            @if($content->type == 'file')
                                <i class="fas fa-file-shield"></i>
                            @elseif($content->type == 'image')
                                <i class="fas fa-image"></i>
                            @else
                                <i class="fas fa-link-slash"></i>
                            @endif
                        </div>
                        <div class="item-text">
                            <span class="item-title">{{ $content->label }}</span>
                            <span class="item-sub">
                                @if($content->type == 'file')
                                    Lihat Berkas Digital
                                @elseif($content->type == 'image')
                                    Lihat Dokumentasi Gambar
                                @else
                                    Kunjungi Tautan Eksternal
                                @endif
                            </span>
                        </div>
                        <div class="item-arrow">
                            <i class="fas fa-chevron-right"></i>
                        </div>
                    </a>
                @endforeach
            @endforeach
        </div>

        <div class="footer">
            <img src="/icon.png" class="footer-logo" alt="Logo SINDIKAT Footer">
            <div class="footer-credit">
                <strong>Sindikat v1.0</strong><br>
            
                &copy; {{ date('Y') }} Diklat RSUD Simpang Lima Gumul Kediri
            </div>
        </div>

    </div>

</body>
</html>