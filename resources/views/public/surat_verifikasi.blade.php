<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Surat - RSUD SLG</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --maroon: #7c1316; --muted: #64748b; --line: #e2e8f0; }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: 'Plus Jakarta Sans', sans-serif; background: #f1f5f9; color: #0f172a; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px 16px; }
        .card { background: #fff; width: 100%; max-width: 560px; border-radius: 22px; box-shadow: 0 20px 50px rgba(15,23,42,.08); overflow: hidden; border: 1px solid var(--line); }
        .head { padding: 26px 28px 20px; border-bottom: 1px solid var(--line); display: flex; gap: 14px; align-items: center; }
        .head img { width: 54px; height: 54px; object-fit: contain; }
        .head .t1 { font-size: 12px; font-weight: 700; color: var(--muted); letter-spacing: .5px; text-transform: uppercase; }
        .head .t2 { font-size: 17px; font-weight: 800; color: var(--maroon); }
        .status { margin: 22px 28px 6px; padding: 14px 16px; border-radius: 14px; display: flex; gap: 12px; align-items: center; font-weight: 700; }
        .ok { background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; }
        .bad { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }
        .ico { width: 34px; height: 34px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 18px; flex-shrink: 0; }
        .ok .ico { background: #10b981; } .bad .ico { background: #ef4444; }
        .rows { padding: 10px 28px 26px; }
        .row { display: flex; padding: 11px 0; border-bottom: 1px dashed var(--line); gap: 14px; }
        .row:last-child { border-bottom: 0; }
        .row .k { width: 140px; flex-shrink: 0; font-size: 12.5px; color: var(--muted); font-weight: 600; }
        .row .v { font-size: 14px; font-weight: 600; word-break: break-word; }
        .foot { padding: 14px 28px; background: #f8fafc; font-size: 11.5px; color: var(--muted); text-align: center; }
        @media (max-width: 480px) { .row { flex-direction: column; gap: 2px; } .row .k { width: auto; } }
    </style>
</head>
<body>
    <div class="card">
        <div class="head">
            <img src="{{ asset('logors.png') }}" alt="RSUD SLG">
            <div>
                <div class="t1">Verifikasi Dokumen</div>
                <div class="t2">RSUD Simpang Lima Gumul Kabupaten Kediri</div>
            </div>
        </div>

        @if($surat && $surat->praPenelitian)
            <div class="status ok"><div class="ico">✓</div><div>Surat ASLI dan tercatat di Sistem Sindikat RSUD SLG.</div></div>
            <div class="rows">
                <div class="row"><div class="k">Jenis Surat</div><div class="v">{{ $surat->judulSurat() }}</div></div>
                <div class="row"><div class="k">Nomor</div><div class="v">{{ $surat->nomor_surat }}</div></div>
                <div class="row"><div class="k">Tanggal Surat</div><div class="v">{{ $surat->tanggal_surat->translatedFormat('d F Y') }}</div></div>
                <div class="row"><div class="k">Kegiatan</div><div class="v">{{ $kegiatan['nama'] }}</div></div>
                <div class="row"><div class="k">Atas Nama</div><div class="v">{{ optional($surat->praPenelitian->user)->name ?? '-' }}</div></div>
                <div class="row"><div class="k">Institusi</div><div class="v">{{ optional($surat->praPenelitian->mou)->nama_instansi ?: (optional($surat->praPenelitian->mou)->nama_universitas ?: '-') }}</div></div>
                <div class="row"><div class="k">Judul</div><div class="v">{{ $surat->praPenelitian->judul }}</div></div>
            </div>
        @else
            <div class="status bad"><div class="ico">!</div><div>Surat TIDAK DITEMUKAN. Kode verifikasi tidak terdaftar atau surat telah dibatalkan.</div></div>
            <div class="rows"><p style="color:#64748b;font-size:14px">Pastikan QR Code dipindai dari surat asli. Hubungi Unit Diklat RSUD SLG bila ragu.</p></div>
        @endif
        <div class="foot">Unit Pendidikan dan Pelatihan (Diklat) · {{ config('surat.instansi.telp') }} · {{ config('surat.instansi.email') }}</div>
    </div>
</body>
</html>
