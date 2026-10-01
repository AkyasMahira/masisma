<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Media Sosial - RSUD Simpang Lima Gumul Kediri</title>
    <link rel="icon" type="image/png" href="{{ asset('icon.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root { --maroon:#7c1316; --maroon-dark:#5f0f12; }
        * { font-family:'Plus Jakarta Sans',sans-serif; box-sizing:border-box; }
        body { margin:0; min-height:100vh; background:linear-gradient(160deg,#7c1316 0%,#5f0f12 55%,#3d090b 100%); display:flex; align-items:flex-start; justify-content:center; padding:40px 16px; }
        .wrap { width:100%; max-width:440px; text-align:center; color:#fff; }
        .logo { width:96px; height:96px; border-radius:26px; background:#fff; padding:10px; object-fit:contain; margin:0 auto 16px; box-shadow:0 12px 30px rgba(0,0,0,.3); }
        .logo-fallback { width:96px; height:96px; border-radius:26px; background:#fff; color:var(--maroon); display:flex; align-items:center; justify-content:center; font-size:2.2rem; font-weight:800; margin:0 auto 16px; box-shadow:0 12px 30px rgba(0,0,0,.3); }
        h1 { font-size:1.3rem; font-weight:800; margin:0 0 4px; }
        .sub { opacity:.85; font-size:.9rem; margin-bottom:28px; }
        .links { display:flex; flex-direction:column; gap:14px; }
        .link { display:flex; align-items:center; gap:16px; background:rgba(255,255,255,.95); color:#1f2937; text-decoration:none; padding:14px 18px; border-radius:16px; font-weight:700; font-size:.95rem; transition:.2s; box-shadow:0 6px 16px rgba(0,0,0,.15); }
        .link:hover { transform:translateY(-3px); box-shadow:0 12px 24px rgba(0,0,0,.25); background:#fff; }
        .link .ic { width:42px; height:42px; border-radius:12px; display:flex; align-items:center; justify-content:center; color:#fff; font-size:1.2rem; flex-shrink:0; }
        .link .t { flex:1; text-align:left; }
        .link .t small { display:block; font-weight:500; color:#64748b; font-size:.72rem; }
        .link .arrow { color:#cbd5e1; }
        .ig { background:linear-gradient(45deg,#f09433,#e6683c,#dc2743,#cc2366,#bc1888); }
        .fb { background:#1877f2; } .yt { background:#ff0000; } .tt { background:#000; }
        .wa { background:#25d366; } .web { background:var(--maroon); } .mail { background:#ea4335; }
        .footer { margin-top:30px; opacity:.7; font-size:.78rem; }
        .back { display:inline-block; margin-top:10px; color:#fff; opacity:.85; text-decoration:none; font-size:.82rem; }
        .back:hover { opacity:1; text-decoration:underline; }
    </style>
</head>
<body>
    <div class="wrap">
        @php $logo = file_exists(public_path('logors.png')) ? asset('logors.png') : (file_exists(public_path('icon.png')) ? asset('icon.png') : null); @endphp
        @if($logo)
            <img src="{{ $logo }}" alt="Logo RSUD SLG" class="logo">
        @else
            <div class="logo-fallback">SLG</div>
        @endif
        <h1>RSUD Simpang Lima Gumul</h1>
        <div class="sub">Kabupaten Kediri &middot; Media Sosial & Kontak Resmi</div>

        <div class="links">
            <a class="link" href="https://www.instagram.com/rsudslg" target="_blank" rel="noopener">
                <span class="ic ig"><i class="fab fa-instagram"></i></span>
                <span class="t">Instagram <small>@rsudslg</small></span><i class="fas fa-chevron-right arrow"></i>
            </a>
            <a class="link" href="https://www.facebook.com/rsudslg" target="_blank" rel="noopener">
                <span class="ic fb"><i class="fab fa-facebook-f"></i></span>
                <span class="t">Facebook <small>RSUD Simpang Lima Gumul</small></span><i class="fas fa-chevron-right arrow"></i>
            </a>
            <a class="link" href="https://www.youtube.com/@rsudslg" target="_blank" rel="noopener">
                <span class="ic yt"><i class="fab fa-youtube"></i></span>
                <span class="t">YouTube <small>Channel resmi</small></span><i class="fas fa-chevron-right arrow"></i>
            </a>
            <a class="link" href="https://www.tiktok.com/@rsudslg" target="_blank" rel="noopener">
                <span class="ic tt"><i class="fab fa-tiktok"></i></span>
                <span class="t">TikTok <small>@rsudslg</small></span><i class="fas fa-chevron-right arrow"></i>
            </a>
            <a class="link" href="https://wa.me/62" target="_blank" rel="noopener">
                <span class="ic wa"><i class="fab fa-whatsapp"></i></span>
                <span class="t">WhatsApp <small>Layanan informasi</small></span><i class="fas fa-chevron-right arrow"></i>
            </a>
            <a class="link" href="https://rsudslg.kedirikab.go.id" target="_blank" rel="noopener">
                <span class="ic web"><i class="fas fa-globe"></i></span>
                <span class="t">Website Resmi <small>rsudslg.kedirikab.go.id</small></span><i class="fas fa-chevron-right arrow"></i>
            </a>
            <a class="link" href="mailto:rsud@kedirikab.go.id">
                <span class="ic mail"><i class="fas fa-envelope"></i></span>
                <span class="t">Email <small>rsud@kedirikab.go.id</small></span><i class="fas fa-chevron-right arrow"></i>
            </a>
        </div>

        <div class="footer">&copy; {{ date('Y') }} RSUD Simpang Lima Gumul Kabupaten Kediri</div>
        <a href="{{ route('landing') }}" class="back"><i class="fas fa-arrow-left"></i> Kembali ke Beranda</a>
    </div>
</body>
</html>
