<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penilaian Fasilitator - {{ $materi->nama_materi }} - {{ $kegiatan->nama_kegiatan }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --sindikat-maroon: #7c1316;
            --sindikat-maroon-dark: #5c0d10;
            --sindikat-bg: #f8fafc;
            --sindikat-surface: #ffffff;
            --sindikat-text: #1e293b;
            --sindikat-text-muted: #64748b;
            --sindikat-border: #e2e8f0;
        }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--sindikat-bg); color: var(--sindikat-text); min-height: 100vh; }
        
        .brand-nav { padding: 20px 0; display: flex; align-items: center; gap: 12px; }
        .brand-nav img { height: 40px; border-radius: 10px; }
        .brand-nav .brand-text { font-weight: 800; font-size: 1.35rem; color: var(--sindikat-maroon); letter-spacing: -0.5px; }

        .btn-back { color: var(--sindikat-text-muted); font-weight: 700; text-decoration: none; transition: 0.2s; display: inline-flex; align-items: center; margin-bottom: 24px; background: #fff; padding: 10px 20px; border-radius: 50px; border: 1px solid var(--sindikat-border); box-shadow: 0 2px 10px rgba(0,0,0,0.02); }
        .btn-back:hover { color: var(--sindikat-maroon); border-color: var(--sindikat-maroon); transform: translateX(-4px); }

        .header-card { background: linear-gradient(135deg, var(--sindikat-maroon) 0%, var(--sindikat-maroon-dark) 100%); color: white; padding: 32px; border-radius: 20px; box-shadow: 0 10px 30px rgba(124, 19, 22, 0.15); margin-bottom: 24px; }
        .form-card { background: var(--sindikat-surface); border-radius: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); padding: 28px; border: 1px solid var(--sindikat-border); }

        /* Search & Filter Inputs */
        .custom-input { border-radius: 12px; border: 1px solid var(--sindikat-border); font-weight: 500; background: #f8fafc; transition: 0.3s; }
        .custom-input:focus { border-color: var(--sindikat-maroon); box-shadow: 0 0 0 4px rgba(124,19,22,0.1); background: #fff; }

        /* List Peserta */
        .peserta-list { padding: 0; margin: 0; list-style: none; display: flex; flex-direction: column; gap: 12px; }
        .peserta-item { padding: 16px; border: 1px solid var(--sindikat-border); border-radius: 16px; transition: all 0.2s; display: flex; flex-direction: column; gap: 16px; background: #fff; }
        .peserta-item:hover { border-color: #cbd5e1; box-shadow: 0 8px 24px rgba(0,0,0,0.04); transform: translateY(-2px); }
        
        .peserta-info { display: flex; align-items: center; gap: 16px; width: 100%; }
        .avatar { width: 48px; height: 48px; border-radius: 12px; background: #f1f5f9; color: var(--sindikat-maroon); display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1.2rem; flex-shrink: 0; }
        .avatar.done { background: #d1fae5; color: #059669; }
        .nama-peserta { font-weight: 700; font-size: 1.05rem; margin-bottom: 4px; color: var(--sindikat-text); line-height: 1.3; }

        .badge-status { padding: 6px 14px; border-radius: 50px; font-weight: 700; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; display: inline-flex; align-items: center; gap: 4px; }
        .badge-done { background-color: #d1fae5; color: #047857; }
        .badge-pending { background-color: #fef3c7; color: #b45309; }

        .btn-sindikat { background-color: var(--sindikat-maroon); color: white; border-radius: 12px; padding: 10px 24px; font-weight: 700; font-size: 0.9rem; transition: 0.2s; text-align: center; text-decoration: none; border: 2px solid var(--sindikat-maroon); }
        .btn-sindikat:hover { background-color: var(--sindikat-maroon-dark); border-color: var(--sindikat-maroon-dark); color: white; }
        .btn-sindikat-outline { background-color: #fff; color: var(--sindikat-text); border-radius: 12px; padding: 10px 24px; font-weight: 700; font-size: 0.9rem; transition: 0.2s; text-align: center; text-decoration: none; border: 2px solid var(--sindikat-border); }
        .btn-sindikat-outline:hover { border-color: var(--sindikat-maroon); color: var(--sindikat-maroon); background: var(--sindikat-bg); }

        @media (min-width: 768px) {
            .peserta-item { flex-direction: row; align-items: center; justify-content: space-between; padding: 20px; }
            .peserta-info { width: auto; }
            .btn-action { width: 160px; }
        }
    </style>
</head>
<body>
<div class="container pb-5">
    
    <div class="brand-nav">
        <img src="https://sindikat-rsudslg.kedirikab.go.id/icon.png" alt="Logo">
        <span class="brand-text">Sindikat Assessor</span>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">

            <a href="{{ route('fasilitator.penilaian.index', $fasilitator->token_fasilitator) }}" class="btn-back">
                <i class="bi bi-arrow-left me-2"></i> Kembali ke Materi
            </a>
            
            @if(session('success'))
                <div class="alert alert-success border-0 bg-success text-white rounded-4 shadow-sm p-3 mb-4 d-flex align-items-center">
                    <i class="bi bi-check-circle-fill fs-4 me-3"></i>
                    <div class="mb-0 fw-medium">{{ session('success') }}</div>
                </div>
            @endif

            <div class="header-card">
                <div class="badge bg-white text-maroon mb-3 rounded-pill px-3 py-2 fw-bold" style="color: var(--sindikat-maroon);">Materi Penilaian</div>
                <h3 class="fw-bold mb-1">{{ $materi->nama_materi }}</h3>
                <div class="text-white-50 mt-2"><i class="bi bi-journal-text me-2"></i>{{ $kegiatan->nama_kegiatan }}</div>
            </div>

            <div class="form-card">
                <h5 class="fw-bold mb-4 d-flex align-items-center">
                    <i class="bi bi-people-fill me-2 fs-4" style="color: var(--sindikat-maroon);"></i> Daftar Peserta
                </h5>

                <!-- Search & Filter menggunakan Bootstrap Grid -->
                <div class="row g-3 mb-4">
                    <div class="col-md-8">
                        <div class="position-relative">
                            <i class="bi bi-search position-absolute top-50 translate-middle-y text-muted" style="left: 16px;"></i>
                            <input type="text" id="searchPeserta" class="form-control custom-input py-3" placeholder="Cari nama peserta..." style="padding-left: 45px;">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <select id="filterStatus" class="form-select custom-input py-3" style="font-weight: 600; color: var(--sindikat-text);">
                            <option value="all">Semua Status</option>
                            <option value="pending">Belum Dinilai</option>
                            <option value="done">Sudah Dinilai</option>
                        </select>
                    </div>
                </div>
                
                <ul class="peserta-list" id="listPeserta">
                    @forelse($daftarPeserta as $p)
                    <li class="peserta-item" data-nama="{{ strtolower($p->nama_lengkap_gelar) }}" data-status="{{ $p->sudah_dinilai ? 'done' : 'pending' }}">
                        <div class="peserta-info">
                            <div class="avatar {{ $p->sudah_dinilai ? 'done' : '' }}">
                                @if($p->sudah_dinilai)
                                    <i class="bi bi-check-lg"></i>
                                @else
                                    {{ substr($p->nama_lengkap_gelar, 0, 1) }}
                                @endif
                            </div>
                            <div>
                                <h6 class="nama-peserta">{{ $p->nama_lengkap_gelar }}</h6>
                                @if($p->sudah_dinilai)
                                    <span class="badge-status badge-done"><i class="bi bi-check-circle-fill"></i> Selesai Dinilai</span>
                                @else
                                    <span class="badge-status badge-pending"><i class="bi bi-clock-history"></i> Menunggu Nilai</span>
                                @endif
                            </div>
                        </div>
                        <a href="{{ route('fasilitator.penilaian.form', [$fasilitator->token_fasilitator, $materi->id, $p->id]) }}" 
                           class="btn-action {{ $p->sudah_dinilai ? 'btn-sindikat-outline' : 'btn-sindikat' }}">
                            {{ $p->sudah_dinilai ? 'Edit Penilaian' : 'Beri Nilai' }}
                        </a>
                    </li>
                    @empty
                    <li class="text-center text-muted py-5 border rounded-4 bg-light">
                        <i class="bi bi-people fs-1 d-block mb-3 opacity-25"></i>
                        Belum ada peserta terdaftar di kegiatan ini.
                    </li>
                    @endforelse
                </ul>
                <div id="noPeserta" class="text-center text-muted py-4 d-none">Peserta tidak ditemukan.</div>
            </div>

        </div>
    </div>
</div>

<script>
    // Pencarian dan Filter Live
    const searchInput = document.getElementById('searchPeserta');
    const filterSelect = document.getElementById('filterStatus');
    const pesertaItems = document.querySelectorAll('.peserta-item');
    const noPeserta = document.getElementById('noPeserta');

    function filterData() {
        let searchTerm = searchInput.value.toLowerCase();
        let statusFilter = filterSelect.value;
        let hasVisible = false;

        pesertaItems.forEach(item => {
            let nama = item.getAttribute('data-nama');
            let status = item.getAttribute('data-status');
            
            let matchSearch = nama.includes(searchTerm);
            let matchStatus = (statusFilter === 'all') || (status === statusFilter);

            if (matchSearch && matchStatus) {
                item.style.display = "flex";
                hasVisible = true;
            } else {
                item.style.display = "none";
            }
        });

        noPeserta.classList.toggle('d-none', hasVisible);
    }

    searchInput.addEventListener('input', filterData);
    filterSelect.addEventListener('change', filterData);
</script>
</body>
</html>