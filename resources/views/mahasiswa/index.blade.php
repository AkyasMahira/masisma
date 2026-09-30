@extends('layouts.app')

@section('content')
    {{-- Memanggil CSS Choices.js --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />

    <style>
        :root {
            --custom-maroon: #7c1316;
            --custom-maroon-light: #a3191d;
            --custom-maroon-subtle: #fcf0f1;
            --text-dark: #2c3e50;
            --text-muted: #95a5a6;
            --card-radius: 12px;
            --shadow-soft: 0 4px 20px rgba(0, 0, 0, 0.05);
            --transition: 0.3s ease;
        }

        /* --- Header Styling --- */
        /* --- Header Styling --- */
        .page-header-wrapper {
            background: #fff;
            border-radius: var(--card-radius);
            padding: 1.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            margin-bottom: 1.5rem;
            border-left: 5px solid var(--custom-maroon);
            position: relative;
            overflow: visible !important; 
            z-index: 100; /* <--- UBAH JADI 100 */
        }

        #toolsDropdownMenu { z-index: 9999 !important; }

        /* --- Filter Card --- */
        .filter-card {
            background: #fff;
            border-radius: var(--card-radius);
            box-shadow: var(--shadow-soft);
            margin-bottom: 1.5rem;
            border: 1px solid #f0f0f0;
            position: relative;
            z-index: 50;
        }

        .filter-header {
            background: var(--custom-maroon-subtle);
            color: var(--custom-maroon);
            padding: 1rem 1.5rem;
            border-radius: var(--card-radius) var(--card-radius) 0 0;
            font-weight: 600;
        }

        /* --- Mini Dashboard Stats --- */
        .stat-card {
            background: #fff;
            border-radius: 10px;
            padding: 15px;
            border: 1px solid #e0e0e0;
            display: flex;
            align-items: center;
            gap: 15px;
            transition: var(--transition);
        }
        .stat-card:hover { transform: translateY(-3px); box-shadow: var(--shadow-soft); }
        .stat-icon {
            width: 45px; height: 45px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem;
        }
        .stat-icon.all { background: #e0f2fe; color: #1e40af; }
        .stat-icon.active { background: #d1fae5; color: #065f46; }
        .stat-icon.nonactive { background: #fee2e2; color: #991b1b; }
        .stat-value { font-size: 1.5rem; font-weight: 700; line-height: 1; margin-bottom: 2px; color: var(--text-dark);}
        .stat-label { font-size: 0.8rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; }

        /* --- Buttons --- */
        .btn-maroon {
            background-color: var(--custom-maroon); color: #fff; border: none; border-radius: 8px;
            padding: 8px 16px; font-weight: 500; transition: var(--transition);
        }
        .btn-maroon:hover {
            background-color: var(--custom-maroon-light); color: #fff; transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(124, 19, 22, 0.3);
        }
        .btn-tool {
            background: #fff; border: 1px solid #e0e0e0; color: var(--text-dark);
            border-radius: 8px; padding: 8px 12px; transition: var(--transition); display: flex; align-items: center; gap: 0.5rem;
        }
        .btn-tool:hover { background: #f8f9fa; border-color: var(--custom-maroon); }

        /* --- Choices JS Overrides --- */
        .choices__inner {
            background-color: #f8f9fa !important;
            border: 1px solid #ced4da !important;
            border-radius: 8px !important;
            min-height: 42px !important;
            padding-bottom: 0px !important;
        }
        .choices__list--dropdown { z-index: 1050; border-radius: 8px; }

        /* --- Table Styling --- */
        .custom-table-card { background: #fff; border-radius: var(--card-radius); box-shadow: var(--shadow-soft); overflow: hidden; border: none; }
        .table thead th { background-color: var(--custom-maroon); color: white; border: none; padding: 1rem; font-weight: 500; text-transform: uppercase; font-size: 0.85rem; }
        .table tbody td { padding: 1rem; vertical-align: middle; color: #555; border-bottom: 1px solid #f0f0f0; }
        .table-hover tbody tr:hover { background-color: #fff5f6; }
        
        .badge-pill-soft { border-radius: 50px; padding: 6px 12px; font-weight: 500; font-size: 0.75rem; }
        .bg-soft-success { background-color: #d1fae5; color: #065f46; }
        .bg-soft-secondary { background-color: #f3f4f6; color: #4b5563; }
        .bg-soft-info { background-color: #dbeafe; color: #1e40af; }
        .bg-soft-danger { background-color: #fee2e2; color: #991b1b; }
        
        .action-btn { width: 32px; height: 32px; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center; color: #6c757d; background: transparent; border: 1px solid transparent; transition: var(--transition);}
        .action-btn:hover { background: var(--custom-maroon-subtle); color: var(--custom-maroon); }
        .action-btn.delete:hover { background: #fee2e2; color: #dc2626; }
        
        .rounded-circle { border-radius: 50% !important; }
        img.rounded-circle { object-fit: cover; object-position: center; }
        .animate-up { animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards; opacity: 0; transform: translateY(20px); }
        @keyframes fadeInUp { to { opacity: 1; transform: translateY(0); } }
    </style>

    {{-- 1. HEADER --}}
    <div class="page-header-wrapper d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-bold mb-0 text-dark">Data Mahasiswa</h4>
            <small class="text-muted">Kelola data mahasiswa, ruangan, status magang, dan orientasi.</small>
        </div>
        
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <div class="dropdown position-relative">
                <button class="btn btn-tool shadow-sm" type="button" id="toolsBtn" onclick="toggleTools(event)">
                    <i class="bi bi-gear-fill text-secondary"></i> Tools
                </button>
                <div id="toolsDropdownMenu" class="dropdown-menu dropdown-menu-right shadow-sm border-0" style="border-radius: 12px; position: absolute; right: 0; top: 110%; display: none; min-width: 200px; background: white;">
                    <a class="dropdown-item py-2" href="javascript:void(0)" onclick="exportMahasiswa(); closeTools();"><i class="bi bi-file-earmark-excel text-success mr-2"></i> Export Excel</a>
                    <a class="dropdown-item py-2" href="javascript:void(0)" onclick="downloadTemplateMahasiswa(); closeTools();"><i class="bi bi-download text-primary mr-2"></i> Template</a>
                    <a class="dropdown-item py-2" href="javascript:void(0)" onclick="copyAllLinks(); closeTools();"><i class="bi bi-link-45deg text-info mr-2"></i> Salin Semua Link</a>
                    <div class="dropdown-divider"></div>
                    <label class="dropdown-item py-2 mb-0 cursor-pointer" style="cursor: pointer;">
                        <i class="bi bi-upload text-warning mr-2"></i> Import Excel
                        <input type="file" id="fileImportMahasiswa" style="display: none" accept=".xlsx,.xls" onchange="closeTools(); importMahasiswa(this);">
                    </label>
                </div>
            </div>
            <a href="{{ route('mahasiswa.create') }}" class="btn btn-maroon shadow-sm d-flex align-items-center gap-2">
                <i class="bi bi-plus-lg"></i> Mahasiswa Baru
            </a>
        </div>
    </div>

    {{-- 2. MINI DASHBOARD STATS --}}
    <div class="row mb-4 animate-up" style="animation-delay: 0.1s;">
        <div class="col-md-4 mb-3 mb-md-0">
            <div class="stat-card">
                <div class="stat-icon all"><i class="bi bi-people-fill"></i></div>
                <div>
                    <div class="stat-value">{{ $totalSemua }}</div>
                    <div class="stat-label">Total Filtered</div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3 mb-md-0">
            <div class="stat-card">
                <div class="stat-icon active"><i class="bi bi-person-check-fill"></i></div>
                <div>
                    <div class="stat-value">{{ $totalAktif }}</div>
                    <div class="stat-label">Status Aktif</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon nonactive"><i class="bi bi-person-x-fill"></i></div>
                <div>
                    <div class="stat-value">{{ $totalNonAktif }}</div>
                    <div class="stat-label">Status Non-Aktif</div>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. FILTER CARD (CHOICES JS) --}}
    <div class="filter-card animate-up" style="animation-delay: 0.2s;">
        <div class="filter-header"><i class="bi bi-funnel-fill mr-2"></i> Filter & Pencarian</div>
        <div class="card-body p-4">
            <form id="filterForm" method="GET" action="{{ route('mahasiswa.index') }}">
                <div class="row g-3">
                    
                    {{-- Pencarian --}}
                    <div class="col-md-4">
                        <label class="small text-muted font-weight-bold text-uppercase">Cari Mahasiswa</label>
                        <div class="input-group shadow-sm">
                            <div class="input-group-prepend"><span class="input-group-text bg-light border-right-0"><i class="bi bi-search"></i></span></div>
                            <input type="text" class="form-control bg-light border-left-0" name="search" placeholder="Nama..." value="{{ request('search') }}">
                        </div>
                    </div>

                    {{-- Instansi / Universitas --}}
                    <div class="col-md-4">
                        <label class="small text-muted font-weight-bold text-uppercase">Universitas/Instansi</label>
                        <select name="mou_id" class="form-control choices-select">
                            <option value="">Semua Instansi</option>
                            @foreach ($mous as $mou)
                                <option value="{{ $mou->id }}" {{ request('mou_id') == $mou->id ? 'selected' : '' }}>
                                    {{ $mou->nama_instansi ?? $mou->nama_universitas }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Ruangan --}}
                    <div class="col-md-4">
                        <label class="small text-muted font-weight-bold text-uppercase">Ruangan</label>
                        <select name="ruangan_id" class="form-control choices-select">
                            <option value="">Semua Ruangan</option>
                            @foreach ($ruangans as $r)
                                <option value="{{ $r->id }}" {{ request('ruangan_id') == $r->id ? 'selected' : '' }}>
                                    {{ $r->nm_ruangan }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Rentang Waktu (Tanggal Mulai) --}}
                    <div class="col-md-4">
                        <label class="small text-muted font-weight-bold text-uppercase">Rentang Waktu (Tgl Mulai)</label>
                        <div class="input-group shadow-sm">
                            <input type="date" name="start_date" class="form-control bg-light" value="{{ request('start_date') }}" title="Dari Tanggal">
                            <span class="input-group-text bg-light border-left-0 border-right-0">-</span>
                            <input type="date" name="end_date" class="form-control bg-light" value="{{ request('end_date') }}" title="Sampai Tanggal">
                        </div>
                    </div>

                    {{-- Gelombang & Tahun --}}
                    <div class="col-md-2">
                        <label class="small text-muted font-weight-bold text-uppercase">Gelombang</label>
                        <select name="gelombang" class="form-control choices-select">
                            <option value="">Semua</option>
                            @foreach ($gelombangs as $g)
                                <option value="{{ $g }}" {{ request('gelombang') == $g ? 'selected' : '' }}>Gel. {{ $g }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label class="small text-muted font-weight-bold text-uppercase">Tahun</label>
                        <select name="tahun" class="form-control choices-select">
                            <option value="">Semua</option>
                            @foreach ($tahuns as $t)
                                <option value="{{ $t }}" {{ request('tahun') == $t ? 'selected' : '' }}>Tahun {{ $t }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Status Toggle (Aktif/Nonaktif) --}}
                    <div class="col-md-2">
                        <label class="small text-muted font-weight-bold text-uppercase">Tampilkan Data</label>
                        <select name="status_filter" class="form-control choices-select">
                            <option value="aktif" {{ $statusFilter == 'aktif' ? 'selected' : '' }}>Hanya Aktif</option>
                            <option value="nonaktif" {{ $statusFilter == 'nonaktif' ? 'selected' : '' }}>Hanya Non-Aktif</option>
                            <option value="semua" {{ $statusFilter == 'semua' ? 'selected' : '' }}>Semua Status</option>
                        </select>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-maroon w-100 mr-2 shadow-sm"><i class="bi bi-search me-1"></i> Terapkan</button>
                        <a href="{{ route('mahasiswa.index') }}" class="btn btn-light border shadow-sm" title="Reset Filter">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- 4. TABEL DATA --}}
    <div class="custom-table-card animate-up" style="animation-delay: 0.3s;">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th class="text-center" width="5%">No</th>
                        <th>Foto</th>
                        <th>Nama Lengkap</th>
                        <th>Instansi / Prodi</th>
                        <th>Ruangan</th>
                        <th class="text-center">Sisa Waktu</th>
                        <th class="text-center">Status</th>
                        <th class="text-center" width="20%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($mahasiswas as $m)
                        <tr>
                            <td class="text-center text-muted font-weight-bold">{{ $loop->iteration + $mahasiswas->firstItem() - 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="mr-3" style="width: 45px; height: 45px; flex-shrink: 0;">
                                        @if($m->foto_path)
                                            <img src="{{ asset($m->foto_path) }}" alt="Foto" class="rounded-circle shadow-sm border" style="width: 100%; height: 100%; object-fit: cover;">
                                        @else
                                            <div class="rounded-circle bg-soft-primary d-flex align-items-center justify-content-center border" style="width: 100%; height: 100%; background-color: #f3f4f6; color: var(--custom-maroon);">
                                                <span class="font-weight-bold" style="font-size: 1.1rem;">{{ strtoupper(substr($m->nm_mahasiswa, 0, 1)) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div><small class="text-muted" style="font-size: 0.75rem;">ID: {{ $m->user_id }}</small></div>
                                </div>
                            </td>
                            <td>
                                <span class="font-weight-bold text-dark d-block">{{ $m->nm_mahasiswa }}</span>
                                <small class="text-muted d-block">{{ $m->user->email ?? '-' }}</small>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="text-dark">
                                        @php
                                            $registeredMou = $m->user && $m->user->mou ? $m->user->mou : null;
                                            $mahasiswaMou = $m->mou ?? null;
                                            $displayMou = $registeredMou ?? $mahasiswaMou;
                                        @endphp
                                        {{ $displayMou ? ($displayMou->nama_instansi ?? $displayMou->nama_universitas ?? '-') : '-' }}
                                    </span>
                                    <small class="text-muted">{{ $m->prodi }}</small>
                                </div>
                            </td>
                            <td>
                                @if ($m->ruangan)
                                    <span class="badge badge-light border text-dark p-2"><i class="bi bi-geo-alt mr-1"></i>{{ $m->nama_ruangan_saat_ini }}</span>
                                @else
                                    <span class="text-muted font-italic">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @php
                                    $start = $m->tanggal_mulai;
                                    $end   = $m->tanggal_berakhir;
                                    if (empty($end) && $m->jadwal_aktif) {
                                        $start = $m->jadwal_aktif->start_date;
                                        $end   = $m->jadwal_aktif->end_date;
                                    }
                                    
                                    $sisaString = '-';
                                    if ($end) {
                                        $now = \Carbon\Carbon::now()->startOfDay();
                                        $endC = \Carbon\Carbon::parse($end)->startOfDay();
                                        if ($now->gt($endC)) {
                                            $sisaString = 'Selesai';
                                        } else {
                                            $diff = $now->diffInDays($endC);
                                            $sisaString = ($diff === 0) ? 'Hari Terakhir' : ($diff + 1) . ' Hari';
                                        }
                                    }

                                    $badgeClass = 'bg-soft-info';
                                    $icon = 'bi-hourglass-split';
                                    if ($sisaString === 'Selesai') {
                                        $badgeClass = 'bg-soft-danger'; $icon = 'bi-check-circle-fill';
                                    } elseif ($sisaString === 'Hari Terakhir') {
                                        $badgeClass = 'bg-soft-warning text-dark'; $icon = 'bi-exclamation-circle-fill';
                                    } elseif ($sisaString !== '-') {
                                        $days = (int) filter_var($sisaString, FILTER_SANITIZE_NUMBER_INT);
                                        if ($days <= 7) { $badgeClass = 'bg-soft-warning text-dark'; $icon = 'bi-exclamation-triangle'; }
                                    }
                                @endphp

                                @if (empty($end))
                                    <span class="text-muted font-italic">-</span>
                                @else
                                    <div class="d-flex flex-column align-items-center">
                                        <span class="badge badge-pill-soft {{ $badgeClass }} mb-1"><i class="bi {{ $icon }} me-1"></i> {{ $sisaString }}</span>
                                        <small class="text-muted" style="font-size: 0.7rem;">{{ \Carbon\Carbon::parse($start)->format('d M') }} - {{ \Carbon\Carbon::parse($end)->format('d M Y') }}</small>
                                    </div>
                                @endif
                            </td>
                            <td class="text-center">
                                @if ($m->status == 'aktif')
                                    <span class="badge badge-pill-soft bg-soft-success">Aktif</span>
                                @else
                                    <span class="badge badge-pill-soft bg-soft-secondary">Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('mahasiswa.show', $m->id) }}" class="action-btn" title="Detail"><i class="bi bi-eye-fill"></i></a>
                                <a href="{{ route('mahasiswa.edit', $m->id) }}" class="action-btn" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                <a href="{{ route('mahasiswa.sertifikat.summary', $m->id) }}" class="action-btn" title="Ringkasan Sertifikat"><i class="bi bi-bar-chart-line-fill"></i></a>
                                <form action="{{ route('mahasiswa.destroy', $m->id) }}" method="POST" class="d-inline delete-form" style="margin-left: 2px;">
                                    @csrf @method('DELETE')
                                    <button type="button" class="action-btn delete btn-delete" title="Hapus"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="bi bi-folder-x display-4 text-muted mb-3" style="opacity: 0.5;"></i>
                                    <h5 class="text-muted font-weight-bold">Tidak ada data ditemukan</h5>
                                    <p class="text-muted small">Coba ubah filter pencarian Anda.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="d-flex justify-content-center mt-4 animate-up" style="animation-delay: 0.4s;">
        {{ $mahasiswas->links('pagination.custom') }}
    </div>

@endsection

@section('scripts')
    {{-- JS Choices & External Libs --}}
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <script src="https://cdn.sheetjs.com/xlsx-0.20.3/package/dist/xlsx.full.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

    <script>
        // INISIALISASI CHOICES JS
        document.addEventListener('DOMContentLoaded', function() {
            const selects = document.querySelectorAll('.choices-select');
            selects.forEach(select => {
                new Choices(select, {
                    searchEnabled: true,
                    itemSelectText: '',
                    shouldSort: false, // Biarkan sorting bawaan PHP
                });
            });
        });

        // --- Toastify Helper ---
        function showToast(message, type = 'success') {
            const colors = { success: "#00b09b", error: "#ff5f6d", info: "#2193b0" };
            Toastify({
                text: message, duration: 3000, gravity: "bottom", position: "right",
                style: { background: colors[type] || colors.info }, className: "rounded shadow-lg"
            }).showToast();
        }

        // --- Tools Dropdown Logic ---
        function toggleTools(e) {
            if (e) e.stopPropagation();
            var menu = document.getElementById('toolsDropdownMenu');
            menu.style.display = (menu.style.display === 'block') ? 'none' : 'block';
        }
        function closeTools() { document.getElementById('toolsDropdownMenu').style.display = 'none'; }
        window.addEventListener('click', function(e) {
            var menu = document.getElementById('toolsDropdownMenu');
            var btn = document.getElementById('toolsBtn');
            if (menu.style.display === 'block' && !menu.contains(e.target) && !btn.contains(e.target)) menu.style.display = 'none';
        });

        // --- Copy Links Logic ---
        function copyAllLinks() {
            const form = new FormData(document.getElementById('filterForm'));
            const params = new URLSearchParams(form);
            showToast("Sedang mengambil link...", "info");

            fetch('{{ route('mahasiswa.links') }}?' + params.toString())
                .then(res => res.json())
                .then(data => {
                    if (!data || data.length === 0) { showToast('Tidak ada data link.', 'info'); return; }
                    const message = data.map(m => `${m.nama}: ${m.link}`).join('\n');
                    fallbackCopyTextToClipboard(message);
                })
                .catch(err => { console.error(err); showToast('Gagal mengambil data link.', 'error'); });
        }

        function fallbackCopyTextToClipboard(text) {
            var textArea = document.createElement("textarea");
            textArea.value = text; textArea.style.position = "fixed"; document.body.appendChild(textArea);
            textArea.focus(); textArea.select();
            try { document.execCommand('copy') ? showToast("Link disalin!", "success") : showToast("Gagal menyalin.", "error"); } 
            catch (err) { showToast("Gagal menyalin link.", "error"); }
            document.body.removeChild(textArea);
        }

        // --- Export Excel Logic ---
        function exportMahasiswa() {
            const form = new FormData(document.getElementById('filterForm'));
            const params = new URLSearchParams(form);
            showToast("Menyiapkan data export...", "info");

            fetch('{{ route('mahasiswa.export_all') }}?' + params.toString())
                .then(res => res.json())
                .then(allData => {
                    if (allData.length === 0) { showToast("Tidak ada data.", "error"); return; }
                    const dataForExcel = allData.map(m => [ m.nama, m.universitas, m.prodi, m.ruangan, m.mulai, m.berakhir, m.status ]);
                    const ws = XLSX.utils.aoa_to_sheet([['Nama Lengkap', 'Universitas / Instansi', 'Program Studi', 'Ruangan', 'Tgl Mulai', 'Tgl Berakhir', 'Status']].concat(dataForExcel));
                    const wb = XLSX.utils.book_new();
                    XLSX.utils.book_append_sheet(wb, ws, 'Data Mahasiswa');
                    XLSX.writeFile(wb, `Data_Mahasiswa_${new Date().toISOString().split('T')[0]}.xlsx`);
                    showToast(`Berhasil export ${allData.length} data!`, "success");
                }).catch(() => showToast("Gagal export", "error"));
        }

        function downloadTemplateMahasiswa() {
            const ws = XLSX.utils.aoa_to_sheet([
                ['Nama', 'No HP', 'Universitas', 'Prodi', 'Ruangan', 'Tanggal Mulai', 'Tanggal Berakhir', 'Status'],
                ['Budi Santoso', '08123456789', 'Univ Merdeka', 'Informatika', 'Ruang Mawar', '2025-01-01', '2025-06-01', 'aktif']
            ]);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, 'Template');
            XLSX.writeFile(wb, 'Template_Mahasiswa.xlsx');
        }

        // --- SweetAlert Delete Confirm ---
        document.addEventListener('DOMContentLoaded', () => {
            const deleteButtons = document.querySelectorAll('.btn-delete');
            deleteButtons.forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const form = this.closest('form');
                    Swal.fire({
                        title: 'Yakin hapus data?', text: "Data ini akan dihapus permanen.",
                        icon: 'warning', showCancelButton: true, confirmButtonColor: '#7c1316',
                        cancelButtonColor: '#6c757d', confirmButtonText: 'Ya, hapus!', cancelButtonText: 'Batal'
                    }).then((result) => { if (result.isConfirmed) form.submit(); });
                });
            });
        });
        
        // SweetAlert Flash Messages
        @if (session('success')) Swal.fire({ icon: 'success', title: 'Berhasil!', text: '{{ session('success') }}', showConfirmButton: false, timer: 1800, toast: true, position: 'top-end' }); @endif
        @if (session('error')) Swal.fire({ icon: 'warning', title: 'Perhatian!', text: '{{ session('error') }}', showConfirmButton: true }); @endif
    </script>
@endsection