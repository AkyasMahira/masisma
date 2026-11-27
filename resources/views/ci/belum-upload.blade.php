<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menunggu Upload File</title>
    
    {{-- Bootstrap 5 & Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --custom-maroon: #7c1316;
            --custom-maroon-light: #a3191d;
            --custom-maroon-subtle: #fcf0f1;
            --text-dark: #1f2937;
            --text-muted: #64748b;
            --bg-surface: #f8fafc;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-surface);
            color: var(--text-dark);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        /* Background Decoration */
        .bg-pattern {
            position: absolute;
            width: 100%;
            height: 100%;
            z-index: -1;
            background-image: radial-gradient(#cbd5e1 1px, transparent 1px);
            background-size: 30px 30px;
            opacity: 0.3;
        }

        .circle-decoration {
            position: absolute;
            width: 300px; height: 300px;
            border-radius: 50%;
            background: var(--custom-maroon-light);
            filter: blur(80px);
            opacity: 0.05;
            z-index: -1;
        }
        .c1 { top: -50px; right: -50px; }
        .c2 { bottom: -50px; left: -50px; }

        /* Main Card */
        .status-card {
            background: white;
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.08);
            max-width: 500px;
            width: 90%;
            padding: 3rem 2rem;
            text-align: center;
            position: relative;
            animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }

        /* Icon Animation */
        .icon-wrapper {
            width: 100px;
            height: 100px;
            background-color: #fff7ed; /* Orange soft */
            color: #ea580c; /* Orange dark */
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3.5rem;
            margin: 0 auto 1.5rem;
            position: relative;
        }

        .icon-pulse {
            position: absolute;
            width: 100%; height: 100%;
            border-radius: 50%;
            border: 2px solid #ea580c;
            opacity: 0;
            animation: pulseRing 2s infinite;
        }

        /* Typography */
        h3 { font-weight: 800; color: var(--text-dark); letter-spacing: -0.5px; margin-bottom: 0.75rem; }
        p { font-size: 1rem; color: var(--text-muted); line-height: 1.6; margin-bottom: 2rem; }
        
        .highlight-name {
            color: var(--custom-maroon);
            font-weight: 700;
            background-color: var(--custom-maroon-subtle);
            padding: 0 5px;
            border-radius: 4px;
        }

        /* Info Box */
        .info-box {
            background: #f1f5f9;
            border-radius: 16px;
            padding: 1.25rem;
            text-align: left;
            margin-bottom: 2rem;
            border: 1px solid #e2e8f0;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }
        .info-item:last-child { margin-bottom: 0; }
        .info-label { color: var(--text-muted); font-weight: 500; }
        .info-value { color: var(--text-dark); font-weight: 700; }

        /* Button */
        .btn-refresh {
            background-color: var(--custom-maroon);
            color: white;
            border: none;
            padding: 0.8rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 15px rgba(124, 19, 22, 0.2);
        }
        .btn-refresh:hover {
            background-color: var(--custom-maroon-light);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(124, 19, 22, 0.3);
            color: white;
        }
        .btn-refresh:active { transform: translateY(0); }

        /* Animations */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes pulseRing {
            0% { transform: scale(1); opacity: 0.5; }
            100% { transform: scale(1.5); opacity: 0; }
        }
    </style>
    <link rel="stylesheet" href="{{ asset('css/sindikat.css') }}?v={{ @filemtime(public_path('css/sindikat.css')) }}">
</head>
<body>

    <div class="bg-pattern"></div>
    <div class="circle-decoration c1"></div>
    <div class="circle-decoration c2"></div>

    <div class="status-card">
        <div class="icon-wrapper">
            <div class="icon-pulse"></div>
            <i class="bi bi-hourglass-split"></i>
        </div>

        <h3>File Belum Tersedia</h3>
        <p>
            Mahasiswa atas nama <span class="highlight-name">{{ $presentasi->user->name }}</span> belum mengupload file presentasi (PPT). Silakan hubungi mahasiswa atau cek kembali nanti.
        </p>

        <div class="info-box">
            <div class="d-flex align-items-center mb-3 pb-2 border-bottom">
                <i class="bi bi-calendar-event text-secondary me-2"></i>
                <span class="fw-bold text-dark">Jadwal Presentasi</span>
            </div>
            
            <div class="info-item">
                <span class="info-label">Tanggal</span>
                <span class="info-value">{{ $presentasi->tanggal_presentasi->format('d F Y') }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Waktu</span>
                <span class="info-value">{{ $presentasi->waktu_mulai }} - {{ $presentasi->waktu_selesai }} WIB</span>
            </div>
            <div class="info-item">
                <span class="info-label">Tempat</span>
                <span class="info-value">{{ $presentasi->tempat }}</span>
            </div>
        </div>

        <button onclick="location.reload()" class="btn-refresh">
            <i class="bi bi-arrow-clockwise"></i> Refresh Halaman
        </button>
    </div>

</body>
</html>