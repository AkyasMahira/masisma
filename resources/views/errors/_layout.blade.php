<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#7c1316">
    <title>@yield('kode') · @yield('judul') — Sindikat</title>
    <link rel="icon" type="image/png" href="{{ asset('icon.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px 16px;
            background: radial-gradient(circle at 20% 10%, #9a1d21 0%, #7c1316 55%, #5c0f11 100%); color: #1f2937;
        }
        .kotak { width: 100%; max-width: 460px; background: #fff; border-radius: 22px; padding: 34px 28px 26px; text-align: center; box-shadow: 0 24px 60px rgba(40, 0, 0, .35); }
        .logo { width: 56px; height: 56px; object-fit: contain; margin-bottom: 10px; }
        .kode { font-size: 3.4rem; font-weight: 700; line-height: 1; color: #7c1316; letter-spacing: -.03em; }
        .ikon { display: inline-grid; place-items: center; width: 44px; height: 44px; border-radius: 14px; background: #fcf0f1; color: #7c1316; font-size: 1.3rem; margin: 14px 0 6px; }
        h1 { font-size: 1.25rem; margin: 6px 0 8px; }
        p { color: #6b7280; font-size: .92rem; line-height: 1.55; margin: 0 0 22px; }
        .aksi { display: flex; gap: 10px; justify-content: center; flex-wrap: wrap; }
        .tbl { display: inline-flex; align-items: center; gap: 6px; padding: 10px 18px; border-radius: 12px; font-weight: 600; font-size: .9rem; text-decoration: none; border: 0; cursor: pointer; }
        .tbl.utama { background: #7c1316; color: #fff; }
        .tbl.utama:hover { background: #a3191d; }
        .tbl.kedua { background: #fcf0f1; color: #7c1316; }
        .tbl.kedua:hover { background: #f6dfe1; }
        .kaki { margin-top: 24px; font-size: .75rem; color: #9ca3af; }
    </style>
    <link rel="stylesheet" href="{{ asset('css/sindikat.css') }}?v={{ @filemtime(public_path('css/sindikat.css')) }}">
</head>
<body>
    <div class="kotak">
        <img class="logo" src="{{ asset('icon.png') }}" alt="Sindikat">
        <div class="kode">@yield('kode')</div>
        <div class="ikon"><i class="bi @yield('ikon')"></i></div>
        <h1>@yield('judul')</h1>
        <p>@yield('pesan')</p>
        <div class="aksi">
            @hasSection('aksi')
                @yield('aksi')
            @else
                <a href="{{ auth()->check() ? url('/dashboard') : url('/') }}" class="tbl utama"><i class="bi bi-house"></i> Ke Beranda</a>
                <a href="javascript:history.back()" class="tbl kedua"><i class="bi bi-arrow-left"></i> Kembali</a>
            @endif
        </div>
        <div class="kaki">Butuh bantuan? <a href="https://wa.me/6282245415977" target="_blank" rel="noopener">Hubungi admin Diklat RSUD SLG</a></div>
    </div>
</body>
</html>
