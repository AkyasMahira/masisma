<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penilaian Fasilitator - {{ $kegiatan->nama_kegiatan }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --sindikat-maroon: #7c1316;
            --sindikat-maroon-dark: #5c0d10;
            --sindikat-bg: #fdfaf6;
            --sindikat-surface: #ffffff;
            --sindikat-text: #1e293b;
            --sindikat-text-muted: #64748b;
            --sindikat-border: #e2e8f0;
        }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f8fafc;
            color: var(--sindikat-text);
            min-height: 100vh;
        }
        .brand-nav { padding: 20px 0; display: flex; align-items: center; gap: 12px; }
        .brand-nav img { height: 40px; border-radius: 10px; box-shadow: 0 4px 12px rgba(124,19,22,0.1); }
        .brand-nav .brand-text { font-weight: 800; font-size: 1.35rem; color: var(--sindikat-maroon); letter-spacing: -0.5px; }

        .header-card { background: linear-gradient(135deg, var(--sindikat-maroon) 0%, var(--sindikat-maroon-dark) 100%); color: white; padding: 32px; border-radius: 20px; box-shadow: 0 10px 30px rgba(124, 19, 22, 0.15); margin-bottom: 24px; position: relative; overflow: hidden; }
        .header-card::after { content: ''; position: absolute; top: -50px; right: -50px; width: 200px; height: 200px; background: rgba(255,255,255,0.05); border-radius: 50%; }
        
        .form-card { background: var(--sindikat-surface); border-radius: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); padding: 28px; border: 1px solid var(--sindikat-border); }
        
        /* Search Box */
        .search-box { position: relative; margin-bottom: 24px; }
        .search-box i { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--sindikat-text-muted); }
        .search-box input { padding-left: 45px; border-radius: 50px; border: 1px solid var(--sindikat-border); background: #f8fafc; padding-top: 12px; padding-bottom: 12px; font-weight: 500; transition: 0.3s; }
        .search-box input:focus { border-color: var(--sindikat-maroon); box-shadow: 0 0 0 4px rgba(124,19,22,0.1); background: #fff; }

        /* List Items */
        .materi-list { padding: 0; margin: 0; list-style: none; }
        .materi-item { padding: 20px; border: 1px solid var(--sindikat-border); border-radius: 16px; margin-bottom: 16px; transition: all 0.3s; background: #fff; display: flex; flex-direction: column; gap: 16px; }
        .materi-item:hover { border-color: #cbd5e1; box-shadow: 0 8px 24px rgba(0,0,0,0.04); transform: translateY(-2px); }
        
        .materi-header { display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; }
        .materi-title { font-weight: 700; font-size: 1.1rem; margin-bottom: 4px; color: var(--sindikat-text); }
        
        /* Progress Bar */
        .progress-wrapper { background: #f1f5f9; border-radius: 50px; height: 8px; width: 100%; overflow: hidden; margin-top: 8px; }
        .progress-fill { background: var(--sindikat-maroon); height: 100%; border-radius: 50px; transition: width 0.5s ease; }
        .progress-fill.done { background: #10b981; }

        .btn-sindikat { background-color: var(--sindikat-maroon); color: white; border-radius: 50px; padding: 12px 24px; font-weight: 700; font-size: 0.95rem; transition: 0.2s; border: none; text-decoration: none; display: inline-flex; justify-content: center; align-items: center; width: 100%; }
        .btn-sindikat:hover { background-color: var(--sindikat-maroon-dark); color: white; transform: translateY(-2px); }

        @media (min-width: 768px) {
            .materi-item { flex-direction: row; align-items: center; justify-content: space-between; }
            .materi-info { flex: 1; }
            .btn-sindikat { width: auto; }
        }
    </style>
</head>
<body>
<div class="container pb-5">
    
    <div class="brand-nav">
        <img src="https://sindikat-rsudslg.kedirikab.go.id/icon.png" alt="Logo Sindikat">
        <span class="brand-text">Sindikat Assessor</span>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">

            @if(session('success'))
                <div class="alert alert-success border-0 bg-success text-white rounded-4 shadow-sm p-3 mb-4 d-flex align-items-center">
                    <i class="bi bi-check-circle-fill fs-4 me-3"></i>
                    <div class="mb-0 fw-medium">{{ session('success') }}</div>
                </div>
            @endif

            <div class="header-card">
                <div class="badge bg-white mb-3 rounded-pill px-3 py-2 fw-bold shadow-sm" style="color: var(--sindikat-maroon);">Modul Penilaian</div>
                <h3 class="fw-bold mb-2">{{ $kegiatan->nama_kegiatan }}</h3>
                <div class="d-flex align-items-center mt-3 p-3 rounded-3" style="background: rgba(255,255,255,0.1); backdrop-filter: blur(5px);">
                    <div class="bg-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                        <i class="bi bi-person-fill fs-5" style="color: var(--sindikat-maroon);"></i>
                    </div>
                    <div>
                        <div class="text-white-50 small fw-semibold text-uppercase letter-spacing-1">Fasilitator Penguji</div>
                        <div class="text-white fw-bold fs-5">{{ $fasilitator->nama_fasilitator }}</div>
                    </div>
                </div>
            </div>

            <div class="form-card">
                <h5 class="fw-bold mb-4 d-flex align-items-center">
                    <i class="bi bi-collection-fill me-2 fs-4" style="color: var(--sindikat-maroon);"></i> Materi yang Anda Nilai
                </h5>

                <div class="search-box">
                    <i class="bi bi-search"></i>
                    <input type="text" id="searchMateri" class="form-control" placeholder="Cari nama materi...">
                </div>

                <ul class="materi-list" id="listMateri">
                    @forelse($materiList as $m)
                        @php 
                            $selesai = $totalPeserta > 0 && $m->sudah_dinilai >= $totalPeserta; 
                            $persen = $totalPeserta > 0 ? ($m->sudah_dinilai / $totalPeserta) * 100 : 0;
                        @endphp
                        <li class="materi-item" data-nama="{{ strtolower($m->nama_materi) }}">
                            <div class="materi-info w-100 pe-md-4">
                                <div class="materi-header">
                                    <h6 class="materi-title">{{ $m->nama_materi }}</h6>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-2 mb-1">
                                    <span class="small fw-bold {{ $selesai ? 'text-success' : 'text-muted' }}">
                                        <i class="bi {{ $selesai ? 'bi-check-circle-fill' : 'bi-people-fill' }} me-1"></i>
                                        {{ $m->sudah_dinilai }} / {{ $totalPeserta }} Peserta
                                    </span>
                                    <span class="small text-muted">{{ $m->items_count }} Indikator</span>
                                </div>
                                <div class="progress-wrapper">
                                    <div class="progress-fill {{ $selesai ? 'done' : '' }}" style="width: {{ $persen }}%"></div>
                                </div>
                            </div>
                            <a href="{{ route('fasilitator.penilaian.materi', [$fasilitator->token_fasilitator, $m->id]) }}" class="btn-sindikat flex-shrink-0">
                                Buka Materi <i class="bi bi-arrow-right ms-2"></i>
                            </a>
                        </li>
                    @empty
                        <li class="text-center text-muted py-5">
                            <i class="bi bi-inbox fs-1 d-block mb-3 opacity-25"></i>
                            <span class="fw-medium">Belum ada materi yang ditugaskan kepada Anda.<br>Hubungi Tim Diklat.</span>
                        </li>
                    @endforelse
                </ul>
                <div id="noMateri" class="text-center text-muted py-4 d-none">Materi tidak ditemukan.</div>
            </div>

        </div>
    </div>
</div>

<script>
    // Fitur Pencarian Materi Live
    document.getElementById('searchMateri').addEventListener('input', function() {
        let filter = this.value.toLowerCase();
        let items = document.querySelectorAll('.materi-item');
        let hasVisible = false;

        items.forEach(function(item) {
            if(item.getAttribute('data-nama').includes(filter)) {
                item.style.display = "flex";
                hasVisible = true;
            } else {
                item.style.display = "none";
            }
        });

        document.getElementById('noMateri').classList.toggle('d-none', hasVisible);
    });
</script>
</body>
</html>