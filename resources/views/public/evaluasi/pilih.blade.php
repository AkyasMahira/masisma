<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evaluasi Pelatihan - RSUD Simpang Lima Gumul</title>
    <link rel="icon" type="image/png" href="{{ asset('icon.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        :root { --primary-color:#7c1316; --primary-light:#fcebeb; --text-dark:#2c3e50; --text-muted:#64748b; }
        body { background:#f0f2f5; font-family:'Poppins',sans-serif; color:var(--text-dark); padding:20px 14px 60px; }
        .wrap { max-width:820px; margin:0 auto; }
        .back-link { color:var(--text-muted); text-decoration:none; font-weight:600; font-size:14px; display:inline-flex; align-items:center; gap:6px; margin-bottom:14px; }
        .back-link:hover { color:var(--primary-color); text-decoration:none; }
        .head { background:#fff; border-radius:16px; border-top:5px solid var(--primary-color); box-shadow:0 10px 30px rgba(0,0,0,.08); padding:26px 28px; margin-bottom:18px; }
        .head h1 { font-size:24px; font-weight:700; color:var(--primary-color); margin:0 0 6px; }
        .head p { color:var(--text-muted); font-size:14px; margin:0; }
        .search { position:relative; margin-bottom:14px; }
        .search i { position:absolute; left:16px; top:50%; transform:translateY(-50%); color:#94a3b8; }
        .search input { width:100%; border:1px solid #cbd5e0; border-radius:12px; padding:12px 14px 12px 42px; font-size:14px; background:#fff; }
        .search input:focus { outline:none; border-color:var(--primary-color); box-shadow:0 0 0 3px rgba(124,19,22,.08); }
        .item { display:flex; align-items:center; gap:14px; background:#fff; border-radius:14px; padding:14px 18px; margin-bottom:10px; text-decoration:none; color:inherit; border:1px solid transparent; box-shadow:0 4px 6px rgba(0,0,0,.02); transition:.15s; }
        .item:hover { border-color:#e8c2c4; transform:translateY(-1px); text-decoration:none; color:inherit; box-shadow:0 8px 18px rgba(124,19,22,.08); }
        .ic { width:42px; height:42px; border-radius:12px; background:var(--primary-light); color:var(--primary-color); display:flex; align-items:center; justify-content:center; flex-shrink:0; }
        .tx { flex:1; min-width:0; }
        .tx b { display:block; font-size:14.5px; font-weight:600; }
        .tx small { color:var(--text-muted); font-size:12.5px; }
        .go { color:#cbd5e0; }
        .empty { text-align:center; color:var(--text-muted); background:#fff; border-radius:14px; padding:28px; }
        .lain { display:block; text-align:center; margin-top:16px; color:var(--text-muted); font-size:13px; }
        .lain a { color:var(--primary-color); font-weight:600; }
    </style>
</head>
<body>
<div class="wrap">
    <a href="{{ route('landing') }}" class="back-link"><i class="fas fa-arrow-left"></i> Kembali ke Beranda</a>

    <div class="head">
        <h1><i class="fas fa-clipboard-check mr-2"></i>Evaluasi Pelatihan</h1>
        <p>Pilih pelatihan yang Anda ikuti, lalu isi penilaian, kritik, dan saran. Penilaian setiap pelatihan dicatat terpisah.</p>
    </div>

    <form method="GET" class="search">
        <i class="fas fa-search"></i>
        <input type="search" name="q" value="{{ $q }}" placeholder="Cari nama pelatihan..." autofocus>
    </form>

    @forelse($kegiatan as $k)
        <a class="item" href="{{ route('evaluasi.pelatihan.form', [$k->id, $k->token_eval]) }}">
            <div class="ic"><i class="fas fa-chalkboard-teacher"></i></div>
            <div class="tx">
                <b>{{ $k->nama_kegiatan }}</b>
                <small><i class="far fa-calendar mr-1"></i>{{ $k->tanggal_mulai ? \Carbon\Carbon::parse($k->tanggal_mulai)->isoFormat('D MMM YYYY') : '-' }}@if($k->tanggal_selesai && $k->tanggal_selesai != $k->tanggal_mulai) – {{ \Carbon\Carbon::parse($k->tanggal_selesai)->isoFormat('D MMM YYYY') }}@endif</small>
            </div>
            <i class="fas fa-chevron-right go"></i>
        </a>
    @empty
        <div class="empty"><i class="fas fa-search d-block mb-2" style="font-size:22px;"></i>{{ $q !== '' ? 'Pelatihan tidak ditemukan.' : 'Belum ada pelatihan dalam 4 bulan terakhir.' }} Coba cari dengan nama pelatihan.</div>
    @endforelse

    <div class="lain">Pelatihan Anda tidak ada di daftar? <a href="{{ route('evaluasi.public.form', ['q' => ' ']) }}" onclick="document.querySelector('.search input').focus();return false;">Cari dengan nama</a> atau hubungi panitia untuk link evaluasinya.</div>
</div>
</body>
</html>
