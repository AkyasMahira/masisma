@extends('layouts.app')

@section('title', 'Mahasiswa')
@section('page-title', 'Data Mahasiswa')

@section('content')
    <style>
        :root {
            --maroon: #7c1316;
            --maroon-light: #9d2a2e;
            --text-dark: #222;
            --text-muted: #6c757d;
            --bg-light: #f8f9fa;
        }

        /* --- Animations --- */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(15px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fadeInUp 0.5s ease-out forwards;
        }

        /* --- Cards --- */
        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            background: #fff;
            transition: transform 0.2s;
        }

        /* --- CSS FIX: Agar Dropdown tidak tertimbun --- */
        .filter-card {
            background: linear-gradient(to right bottom, #ffffff, #fdfdfd);
            border-left: 5px solid var(--maroon);

            /* TAMBAHKAN 3 BARIS INI PENTING: */
            overflow: visible !important;
            /* Izinkan dropdown keluar dari batas card */
            position: relative;
            /* Buat konteks tumpukan baru */
            z-index: 50;
            /* Pastikan posisinya di atas card tabel (tabel biasanya z-index 0-10) */
        }

        /* --- Inputs --- */
        .input-group-text {
            background-color: #fff;
            border-right: none;
            color: var(--maroon);
        }

        .form-control,
        .form-select {
            border-left: none;
            box-shadow: none !important;
            padding: 0.6rem 1rem;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #ced4da;
        }

        .custom-input-group .input-group-text {
            border-top-left-radius: 10px;
            border-bottom-left-radius: 10px;
        }

        .custom-input-group .form-control,
        .custom-input-group .form-select {
            border-top-right-radius: 10px;
            border-bottom-right-radius: 10px;
        }

        /* --- Buttons --- */
        .btn-maroon {
            background-color: var(--maroon);
            border: none;
            color: #fff;
            font-weight: 600;
            border-radius: 10px;
            padding: 8px 16px;
            transition: all 0.2s ease;
        }

        .btn-maroon:hover {
            background-color: var(--maroon-light);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(124, 19, 22, 0.2);
            color: #fff;
        }

        .btn-outline-custom {
            border: 1px solid #e0e0e0;
            color: var(--text-dark);
            font-weight: 500;
            border-radius: 10px;
            background-color: #fff;
            padding: 6px 12px;
            transition: all 0.2s ease;
        }

        .btn-outline-custom:hover {
            background-color: #f8f9fa;
            border-color: #b0b0b0;
            transform: translateY(-1px);
        }

        /* --- Table --- */
        .table {
            border-collapse: separate;
            border-spacing: 0;
        }

        .table thead {
            background-color: var(--maroon);
            color: #fff;
        }

        .table thead th:first-child {
            border-top-left-radius: 12px;
        }

        .table thead th:last-child {
            border-top-right-radius: 12px;
        }

        .table th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.8rem;
            padding: 1rem;
            border: none;
        }

        .table td {
            vertical-align: middle;
            padding: 1rem;
            border-bottom: 1px solid #f0f0f0;
        }

        .table-hover tbody tr:hover {
            background-color: #fff5f6;
            transform: scale(1.005);
            transition: transform 0.2s;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            z-index: 5;
            position: relative;
        }

        /* --- Dropdown --- */
        .dropdown-list {
            list-style: none;
            padding: 0;
            margin: 0;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            border-radius: 0 0 10px 10px;
            border: 1px solid #eee;
            overflow: hidden;
        }

        .dropdown-list .dropdown-item {
            padding: 10px 15px;
            cursor: pointer;
            border-bottom: 1px solid #f9f9f9;
        }

        .dropdown-list .dropdown-item:hover {
            background-color: #f8f9fa;
            color: var(--maroon);
            font-weight: 500;
        }

        @media (max-width: 768px) {
            .header-buttons {
                flex-direction: column;
                width: 100%;
            }

            .header-buttons .btn {
                width: 100%;
                justify-content: center;
            }

            .filter-actions {
                width: 100%;
                display: flex;
                justify-content: space-between;
            }

            .filter-actions .btn {
                flex: 1;
                margin: 0 2px;
            }
        }
    </style>

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3 animate-fade-in">
        <div>
            <h4 class="fw-bold text-maroon mb-1">Data Mahasiswa</h4>
            <p class="text-muted small mb-0">Kelola data mahasiswa magang, ruangan, dan absensi.</p>
        </div>

        <div class="d-flex flex-wrap gap-2 header-buttons">
            <button class="btn btn-outline-custom btn-sm d-inline-flex align-items-center" onclick="exportMahasiswa()">
                <i class="bi bi-file-earmark-excel text-success me-2"></i> Export
            </button>
            <label class="btn btn-outline-custom btn-sm d-inline-flex align-items-center mb-0" style="cursor: pointer;">
                <i class="bi bi-upload text-primary me-2"></i> Import
                <input type="file" id="fileImportMahasiswa" style="display:none" accept=".xlsx,.xls"
                    onchange="importMahasiswa(this)">
            </label>
            <button class="btn btn-outline-custom btn-sm d-inline-flex align-items-center"
                onclick="downloadTemplateMahasiswa()">
                <i class="bi bi-download me-2"></i> Template
            </button>
            <button type="button" class="btn btn-outline-custom btn-sm d-inline-flex align-items-center"
                onclick="copyAllLinks()">
                <i class="bi bi-link-45deg text-info me-2"></i> Salin Link
            </button>
            <a href="{{ route('mahasiswa.create') }}"
                class="btn btn-maroon btn-sm d-inline-flex align-items-center shadow-sm">
                <i class="bi bi-plus-lg me-2"></i> Mahasiswa Baru
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show animate-fade-in" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card mb-4 filter-card animate-fade-in" style="animation-delay: 0.1s;">
        <div class="card-body p-4">
            <form id="filterForm" method="GET" action="{{ route('mahasiswa.index') }}">
                <div class="row g-3 align-items-end">

                    <div class="col-md-4">
                        <label class="form-label small text-muted fw-bold text-uppercase">Cari Mahasiswa</label>
                        <div class="input-group custom-input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control" name="search" placeholder="Nama mahasiswa..."
                                value="{{ request('search') }}">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small text-muted fw-bold text-uppercase">Asal Universitas</label>
                        <div class="position-relative">
                            <div class="input-group custom-input-group">
                                <span class="input-group-text"><i class="bi bi-building"></i></span>
                                <input type="text" class="form-control" id="univ_asal" placeholder="Semua Kampus"
                                    autocomplete="off">
                                <input type="hidden" id="univ_asal_hidden" name="univ_asal"
                                    value="{{ request('univ_asal') }}">
                            </div>
                            <div id="univDropdown" class="dropdown-list"
                                style="display: none; position: absolute; top: 100%; left: 0; right: 0; background: white; z-index: 1000; max-height: 200px; overflow-y: auto;">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small text-muted fw-bold text-uppercase">Filter Ruangan</label>
                        <div class="input-group custom-input-group">
                            <span class="input-group-text"><i class="bi bi-door-open"></i></span>
                            <select class="form-select" name="ruangan_id">
                                <option value="">Semua Ruangan</option>
                                @foreach ($ruangans as $r)
                                    <option value="{{ $r->id }}"
                                        {{ request('ruangan_id') == $r->id ? 'selected' : '' }}>
                                        {{ $r->nm_ruangan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="d-grid gap-2 filter-actions">
                            <button type="submit" class="btn btn-maroon">Terapkan</button>
                            <a href="{{ route('mahasiswa.index') }}" class="btn btn-light text-muted border" title="Reset">
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card mb-4 animate-fade-in" style="animation-delay: 0.2s;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr class="text-center">
                            <th width="5%">#</th>
                            <th class="text-start">Nama Lengkap</th>
                            <th class="text-start">Instansi / Kampus</th>
                            <th>Ruangan</th>
                            <th>Sisa Waktu</th>
                            <th>Status</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($mahasiswas as $m)
                            <tr class="text-center">
                                <td class="text-muted">{{ $loop->iteration }}</td>
                                <td class="text-start fw-bold text-dark">{{ $m->nm_mahasiswa }}</td>
                                <td class="text-start">
                                    <div class="d-flex flex-column">
                                        <span>{{ $m->univ_asal }}</span>
                                        <small class="text-muted" style="font-size: 0.75rem">{{ $m->prodi }}</small>
                                    </div>
                                </td>
                                <td>
                                    @if ($m->ruangan)
                                        <span class="badge bg-light text-dark border">
                                            <i class="bi bi-geo-alt me-1"></i>{{ $m->ruangan->nm_ruangan }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($m->tanggal_berakhir)
                                        @if ($m->sisa_hari > 0)
                                            <span class="badge rounded-pill bg-info text-dark">{{ $m->sisa_hari }}
                                                Hari</span>
                                        @else
                                            <span class="badge rounded-pill bg-danger">Berakhir</span>
                                        @endif
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if ($m->status == 'aktif')
                                        <span
                                            class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">Aktif</span>
                                    @else
                                        <span
                                            class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-2 rounded-pill">Nonaktif</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('mahasiswa.show', $m->id) }}"
                                            class="btn btn-sm btn-light text-primary"><i class="bi bi-eye-fill"></i></a>
                                        <a href="{{ route('mahasiswa.edit', $m->id) }}"
                                            class="btn btn-sm btn-light text-warning"><i
                                                class="bi bi-pencil-square"></i></a>
                                        <form action="{{ route('mahasiswa.destroy', $m->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-light text-danger"
                                                onclick="return confirm('Hapus data ini?')"><i
                                                    class="bi bi-trash-fill"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <img src="https://cdn-icons-png.flaticon.com/512/7486/7486754.png" alt="Empty"
                                        width="64" class="mb-3 opacity-50">
                                    <p class="text-muted fw-bold">Tidak ada data ditemukan.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-top-0 py-3 d-flex justify-content-center">
            {{ $mahasiswas->links('pagination.custom') }}
        </div>
    </div>

    @section('scripts')
        <script src="https://cdn.sheetjs.com/xlsx-0.20.3/package/dist/xlsx.full.min.js"></script>
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
        <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

        <script>
            function showToast(message, type = 'success') {
                const colors = {
                    success: "#00b09b",
                    error: "#ff5f6d",
                    info: "#2193b0"
                };
                Toastify({
                    text: message,
                    duration: 3000,
                    gravity: "bottom",
                    position: "right",
                    style: {
                        background: colors[type] || colors.info
                    },
                    className: "rounded shadow-lg"
                }).showToast();
            }

            // Live Search Logic
            (function() {
                const univInput = document.getElementById('univ_asal');
                const univHidden = document.getElementById('univ_asal_hidden');
                const dropdown = document.getElementById('univDropdown');
                let timeout;

                if (univHidden && univHidden.value) univInput.value = univHidden.value;

                univInput.addEventListener('input', function(e) {
                    clearTimeout(timeout);
                    const q = e.target.value.trim();
                    if (!q) {
                        dropdown.style.display = 'none';
                        univHidden.value = '';
                        return;
                    }
                    timeout = setTimeout(() => {
                        fetch(`{{ route('mahasiswa.search.universitas') }}?q=${encodeURIComponent(q)}`)
                            .then(r => r.json())
                            .then(list => {
                                if (!list.length) dropdown.innerHTML =
                                    '<div class="dropdown-item text-muted small">Tidak ada hasil</div>';
                                else dropdown.innerHTML = list.map(u =>
                                    `<div class="dropdown-item" data-val="${u}"><i class="bi bi-building me-2 text-muted"></i>${u}</div>`
                                    ).join('');
                                dropdown.style.display = 'block';
                            });
                    }, 300);
                });

                dropdown.addEventListener('click', function(e) {
                    const it = e.target.closest('.dropdown-item');
                    if (!it) return;
                    const val = it.dataset.val || it.textContent.trim();
                    univInput.value = val;
                    univHidden.value = val;
                    dropdown.style.display = 'none';
                });
                document.addEventListener('click', e => {
                    if (!e.target.closest('.position-relative')) dropdown.style.display = 'none';
                });
            })();

            // Updated Copy Links with Room Filter
            function copyAllLinks() {
                const params = new URLSearchParams();
                const univ = document.getElementById('univ_asal_hidden').value;
                const search = document.querySelector('input[name="search"]').value;
                const ruangan = document.querySelector('select[name="ruangan_id"]').value;

                if (univ) params.append('univ_asal', univ);
                if (search) params.append('search', search);
                if (ruangan) params.append('ruangan_id', ruangan);

                fetch('{{ route('mahasiswa.links') }}?' + params.toString())
                    .then(res => res.json())
                    .then(data => {
                        if (!data.length) {
                            showToast('Tidak ada data link.', 'info');
                            return;
                        }
                        const message = data.map(m => `${m.nama}: ${m.link}`).join('\n');
                        navigator.clipboard.writeText(message).then(() => showToast(`Disalin ${data.length} link!`,
                            "success"));
                    })
                    .catch(() => showToast('Gagal mengambil data', 'error'));
            }

            // Export Logic
            function exportMahasiswa() {
                try {
                    const data = [
                        @foreach ($mahasiswas as $m)
                            [
                                {!! json_encode($m->nm_mahasiswa ?? '') !!},
                                {!! json_encode($m->univ_asal ?? '') !!},
                                {!! json_encode($m->prodi ?? '') !!},
                                {!! json_encode($m->ruangan ? $m->ruangan->nm_ruangan : $m->nm_ruangan ?? '') !!},
                                {!! json_encode($m->tanggal_mulai ?? '-') !!},
                                {!! json_encode($m->tanggal_berakhir ?? '-') !!},
                                {!! json_encode($m->status ?? '') !!},
                                {!! json_encode($m->share_token ? url('/absensi/' . $m->share_token) : '') !!}
                            ] {{ $loop->last ? '' : ',' }}
                        @endforeach
                    ];
                    const ws = XLSX.utils.aoa_to_sheet([
                        ['Nama', 'Universitas', 'Prodi', 'Ruangan', 'Mulai', 'Selesai', 'Status', 'Link']
                    ].concat(data));
                    const wb = XLSX.utils.book_new();
                    XLSX.utils.book_append_sheet(wb, ws, 'Mahasiswa');
                    XLSX.writeFile(wb, `Data_Mahasiswa_${new Date().toISOString().split('T')[0]}.xlsx`);
                    showToast("Berhasil export!", "success");
                } catch (e) {
                    showToast("Gagal export", "error");
                }
            }

            function downloadTemplateMahasiswa() {
                const ws = XLSX.utils.aoa_to_sheet([
                    ['Nama', 'Universitas', 'Prodi', 'Ruangan', 'Tanggal Mulai', 'Tanggal Berakhir', 'Status'],
                    ['Contoh', 'Univ A', 'TI', 'Mawar', '2025-01-01', '2025-03-01', 'aktif']
                ]);
                const wb = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(wb, ws, 'Template');
                XLSX.writeFile(wb, 'Template_Mahasiswa.xlsx');
            }

            function importMahasiswa(input) {
                const file = input.files[0];
                if (!file) return;
                const reader = new FileReader();
                reader.onload = function(e) {
                    const wb = XLSX.read(e.target.result, {
                        type: 'array',
                        cellDates: true
                    });
                    const json = XLSX.utils.sheet_to_json(wb.Sheets[wb.SheetNames[0]], {
                        defval: '',
                        dateNF: 'yyyy-mm-dd'
                    });
                    json.forEach(row => {
                        const norm = val => (val instanceof Date ? val.toISOString().split('T')[0] : (typeof val ===
                            'number' ? XLSX.SSF.format('yyyy-mm-dd', val) : ''));
                        row['Tanggal Mulai'] = norm(row['Tanggal Mulai']);
                        row['Tanggal Berakhir'] = norm(row['Tanggal Berakhir']);
                    });
                    const fd = new FormData();
                    fd.append('_token', '{{ csrf_token() }}');
                    fd.append('data', JSON.stringify(json));
                    showToast("Mengimpor...", "info");
                    fetch('{{ route('mahasiswa.store') }}', {
                            method: 'POST',
                            body: fd,
                            headers: {
                                'Accept': 'application/json'
                            }
                        })
                        .then(r => r.json()).then(res => {
                            if (res.success) {
                                showToast(res.message, 'success');
                                setTimeout(() => location.reload(), 1000);
                            } else showToast('Gagal: ' + res.message, 'error');
                        }).catch(() => showToast('Error import', 'error'));
                };
                reader.readAsArrayBuffer(file);
                input.value = '';
            }
        </script>
    @endsection
@endsection
