<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sindikat - @yield('title')</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('icon.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root { --primer: #7c1316; --sekunder: #a83236; --muda: #f8eaea; --teks: #2b2b2b; --redup: #6b6b6b; }
        * { box-sizing: border-box; }
        html, body { min-height: 100%; }
        body {
            margin: 0; min-height: 100vh; font-family: "Segoe UI", Roboto, Arial, sans-serif; color: var(--teks);
            background: radial-gradient(circle at 20% 10%, #9a1d21 0%, var(--primer) 55%, #5e0d10 100%);
            display: flex; align-items: center; justify-content: center; padding: 32px 16px 120px; position: relative; overflow-x: hidden;
        }
        .kartu {
            position: relative; z-index: 5; width: 100%; max-width: 420px; background: #fff; border-radius: 22px;
            padding: 30px 28px 24px; box-shadow: 0 24px 60px rgba(40, 0, 0, .35); animation: muncul .6s ease-out;
        }
        .kartu-atas { text-align: center; margin-bottom: 22px; }
        .lencana { width: 64px; height: 64px; border-radius: 18px; background: var(--muda); color: var(--primer); display: inline-grid; place-items: center; font-size: 1.8rem; margin-bottom: 14px; }
        .kartu-atas h1 { font-size: 1.4rem; font-weight: 700; margin: 0 0 6px; }
        .kartu-atas p { font-size: .9rem; color: var(--redup); margin: 0; line-height: 1.45; }
        .merek { display: flex; align-items: center; justify-content: center; gap: 8px; color: #fff; font-weight: 700; letter-spacing: .5px; margin-bottom: 18px; }
        .merek img { width: 34px; height: 34px; object-fit: contain; }
        .bungkus { width: 100%; max-width: 420px; position: relative; z-index: 5; }

        .field { margin-bottom: 14px; }
        .field label { display: block; font-size: .82rem; font-weight: 600; margin-bottom: 6px; }
        .kolom { display: flex; align-items: center; border: 1.5px solid #e6dada; border-radius: 12px; background: #fff; transition: border-color .2s, box-shadow .2s; }
        .kolom:focus-within { border-color: var(--sekunder); box-shadow: 0 0 0 4px rgba(168, 50, 54, .12); }
        .kolom > i { color: var(--sekunder); padding: 0 4px 0 14px; font-size: 1rem; }
        .kolom input { flex: 1; min-width: 0; border: 0; outline: 0; background: transparent; padding: 12px 12px 12px 8px; font-size: .95rem; color: var(--teks); }
        .kolom input[readonly] { color: var(--redup); }
        .kolom.terkunci { background: #faf6f6; }
        .lihat { border: 0; background: transparent; color: #9a8a8a; padding: 0 14px; height: 44px; }
        .lihat:hover { color: var(--primer); }
        .bantuan { font-size: .78rem; color: var(--redup); margin-top: 6px; }

        .tombol { width: 100%; border: 0; border-radius: 12px; padding: 13px; font-weight: 700; color: #fff; background: var(--primer); transition: background .2s, transform .15s; margin-top: 6px; }
        .tombol:hover { background: var(--sekunder); transform: translateY(-1px); }
        .catatan { display: flex; gap: 8px; font-size: .8rem; color: var(--redup); background: #faf6f6; border-radius: 10px; padding: 10px 12px; margin-top: 16px; line-height: 1.4; }
        .catatan i { color: var(--sekunder); }
        .kembali { display: flex; justify-content: center; margin-top: 18px; }
        .kembali a { color: var(--primer); font-weight: 600; font-size: .88rem; text-decoration: none; display: inline-flex; gap: 6px; align-items: center; }
        .kembali a:hover { text-decoration: underline; }
        .pesan { border-radius: 12px; font-size: .85rem; padding: 11px 13px; display: flex; gap: 8px; margin-bottom: 16px; line-height: 1.4; }
        .pesan.ok { background: #e9f7ef; color: #146c43; }
        .pesan.gagal { background: #fdecec; color: #b02a37; }
        .kekuatan { height: 5px; border-radius: 5px; background: #eee; margin-top: 8px; overflow: hidden; }
        .kekuatan span { display: block; height: 100%; width: 0; transition: width .25s, background .25s; }

        .ombak { position: fixed; left: 0; right: 0; bottom: 0; height: 140px; z-index: 1; pointer-events: none; overflow: hidden; }
        .ombak svg { position: absolute; bottom: 0; left: 0; width: 150%; height: 100%; }
        .ombak .o1 { animation: geser 15s linear infinite; }
        .ombak .o2 { opacity: .6; animation: geser 11s linear infinite reverse; }
        @keyframes geser { 0%, 100% { transform: translateX(0); } 50% { transform: translateX(-6%); } }
        @keyframes muncul { from { transform: translateY(14px); } to { transform: none; } }
        @media (prefers-reduced-motion: reduce) { .kartu, .ombak svg { animation: none; } }
        @media (max-width: 420px) { .kartu { padding: 24px 20px 20px; border-radius: 18px; } body { padding-top: 20px; } }
    </style>
</head>
<body>
    <div class="bungkus">
        <div class="merek"><img src="{{ asset('icon.png') }}" alt=""> SINDIKAT</div>
        <div class="kartu">
            @yield('content')
        </div>
    </div>

    <div class="ombak">
        <svg class="o1" viewBox="0 0 1440 320" preserveAspectRatio="none"><path fill="#a83236" fill-opacity=".55" d="M0,192L48,181.3C96,171,192,149,288,160C384,171,480,213,576,208C672,203,768,149,864,138.7C960,128,1056,160,1152,176C1248,192,1344,192,1392,192L1440,192L1440,320L0,320Z"></path></svg>
        <svg class="o2" viewBox="0 0 1440 320" preserveAspectRatio="none"><path fill="#f8eaea" fill-opacity=".25" d="M0,256L48,245.3C96,235,192,213,288,208C384,203,480,213,576,229.3C672,245,768,267,864,256C960,245,1056,203,1152,192C1248,181,1344,203,1392,213.3L1440,224L1440,320L0,320Z"></path></svg>
    </div>

    <script>
        document.querySelectorAll('.lihat').forEach(function (b) {
            b.addEventListener('click', function () {
                var i = document.getElementById(b.dataset.target), t = i.type === 'password';
                i.type = t ? 'text' : 'password';
                b.innerHTML = t ? '<i class="bi bi-eye-slash"></i>' : '<i class="bi bi-eye"></i>';
            });
        });
        document.querySelectorAll('form').forEach(function (f) {
            f.addEventListener('submit', function () {
                var b = f.querySelector('.tombol');
                if (b) { b.disabled = true; b.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...'; }
            });
        });
    </script>
    @yield('scripts')
</body>
</html>
