<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Galeri — Diklat RSUD Simpang Lima Gumul</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
    :root{ --maroon:#7c1316; --maroon-light:#a3191d; --soft:#fff7f5; --ink:#1c1c1c; --muted:#64748b; }
    *{ box-sizing:border-box; }
    body{ margin:0; font-family:'Segoe UI',system-ui,-apple-system,sans-serif; background:linear-gradient(180deg,#fff7f5,#ffffff); color:var(--ink); }
    .wrap{ max-width:1180px; margin:0 auto; padding:0 16px 60px; }

    .hero{ background:linear-gradient(135deg,var(--maroon),var(--maroon-light)); color:#fff; border-radius:0 0 28px 28px; padding:46px 20px 40px; text-align:center; position:relative; overflow:hidden; box-shadow:0 14px 34px rgba(124,19,22,.22); }
    .hero::after{ content:''; position:absolute; right:-60px; top:-60px; width:220px; height:220px; background:rgba(255,255,255,.07); border-radius:50%; }
    .hero h1{ margin:0 0 6px; font-weight:800; font-size:1.9rem; }
    .hero p{ margin:0; opacity:.9; font-size:.95rem; }
    .hero .btns{ margin-top:18px; display:flex; gap:10px; justify-content:center; flex-wrap:wrap; }
    .btn{ display:inline-flex; align-items:center; gap:8px; text-decoration:none; font-weight:700; border-radius:50px; padding:10px 20px; font-size:.9rem; transition:.2s; }
    .btn-ig{ background:#fff; color:var(--maroon); }
    .btn-ig:hover{ transform:translateY(-2px); }
    .btn-ghost{ background:rgba(255,255,255,.15); color:#fff; }
    .btn-ghost:hover{ background:rgba(255,255,255,.28); }

    .gallery{ margin-top:34px; column-gap:16px; columns:4 260px; }
    .card{ break-inside:avoid; margin:0 0 16px; background:#fff; border-radius:16px; overflow:hidden; box-shadow:0 6px 20px rgba(0,0,0,.07); border:1px solid #f0e3e3; transition:.2s; cursor:zoom-in; }
    .card:hover{ transform:translateY(-3px); box-shadow:0 14px 30px rgba(124,19,22,.16); }
    .card img{ width:100%; height:auto; display:block; }
    .cap{ padding:10px 14px; font-size:.85rem; color:#374151; font-weight:600; border-top:1px solid #f4eaea; }

    .empty{ text-align:center; color:var(--muted); padding:70px 20px; }
    .empty i{ font-size:3rem; color:#e2c9c9; display:block; margin-bottom:10px; }

    /* Lightbox */
    .lb{ position:fixed; inset:0; background:rgba(0,0,0,.9); display:none; align-items:center; justify-content:center; z-index:9999; padding:24px; }
    .lb.open{ display:flex; }
    .lb img{ max-width:92vw; max-height:82vh; border-radius:12px; box-shadow:0 20px 60px rgba(0,0,0,.5); }
    .lb .cap2{ position:absolute; bottom:22px; left:0; right:0; text-align:center; color:#fff; font-weight:600; padding:0 20px; text-shadow:0 1px 4px rgba(0,0,0,.6); }
    .lb .x{ position:absolute; top:18px; right:24px; color:#fff; font-size:2rem; cursor:pointer; line-height:1; }

    @media (max-width:420px){ .hero h1{ font-size:1.5rem; } }
</style>
</head>
<body>

    <div class="hero">
        <h1><i class="bi bi-images me-2"></i>Galeri Kegiatan</h1>
        <p>Dokumentasi kegiatan Diklat RSUD Simpang Lima Gumul Kediri</p>
        <div class="btns">
            <a class="btn btn-ig" href="https://www.instagram.com/diklat_rsudslg/" target="_blank" rel="noopener"><i class="bi bi-instagram"></i> @diklat_rsudslg</a>
            <a class="btn btn-ghost" href="{{ url('/') }}"><i class="bi bi-house-door"></i> Beranda</a>
        </div>
    </div>

    <div class="wrap">
        @if($galeri->count())
            <div class="gallery">
                @foreach($galeri as $g)
                    <figure class="card gi" data-src="{{ asset($g->path) }}" data-cap="{{ e($g->judul) }}" style="margin:0;">
                        <img src="{{ asset($g->path) }}" alt="{{ $g->judul ?: 'Foto galeri' }}" loading="lazy">
                        @if($g->judul)
                            <figcaption class="cap">{{ $g->judul }}</figcaption>
                        @endif
                    </figure>
                @endforeach
            </div>
        @else
            <div class="empty">
                <i class="bi bi-camera"></i>
                <div class="fw-semibold">Belum ada foto di galeri.</div>
            </div>
        @endif
    </div>

    <div class="lb" id="lb" onclick="closeLB(event)">
        <span class="x" onclick="closeLB(event)">&times;</span>
        <img id="lb-img" src="" alt="">
        <div class="cap2" id="lb-cap"></div>
    </div>

    <script>
        function openLB(src, cap) {
            document.getElementById('lb-img').src = src;
            document.getElementById('lb-cap').innerText = cap || '';
            document.getElementById('lb').classList.add('open');
            document.body.style.overflow = 'hidden';
        }
        function closeLB(e) {
            if (e && e.target && e.target.id === 'lb-img') return; // jangan tutup saat klik gambar
            document.getElementById('lb').classList.remove('open');
            document.body.style.overflow = '';
        }
        document.querySelectorAll('.gi').forEach(function (el) {
            el.addEventListener('click', function () { openLB(this.dataset.src, this.dataset.cap); });
        });
        document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeLB(); });
    </script>
</body>
</html>
