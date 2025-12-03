@extends('layouts.app')

@section('title', 'Mahasiswa')
@section('page-title', 'Data Mahasiswa')

@section('content')
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
        .page-header-wrapper {
            background: #fff;
            border-radius: var(--card-radius);
            padding: 1.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            margin-bottom: 2rem;
            border-left: 5px solid var(--custom-maroon);
            position: relative;
            z-index: 1050;
            overflow: visible;
        }

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

        /* --- Buttons --- */
        .btn-maroon {
            background-color: var(--custom-maroon);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 8px 16px;
            font-weight: 500;
            transition: var(--transition);
        }

        .btn-maroon:hover {
            background-color: var(--custom-maroon-light);
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(124, 19, 22, 0.3);
        }

        .btn-tool {
            background: #fff;
            border: 1px solid #e0e0e0;
            color: var(--text-dark);
            border-radius: 8px;
            padding: 8px 12px;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-tool:hover,
        .btn-tool:focus {
            background: #f8f9fa;
            border-color: var(--custom-maroon);
            outline: none;
        }

        /* --- Table Styling --- */
        .custom-table-card {
            background: #fff;
            border-radius: var(--card-radius);
            box-shadow: var(--shadow-soft);
            overflow: hidden;
            border: none;
        }

        .table thead th {
            background-color: var(--custom-maroon);
            color: white;
            border: none;
            padding: 1rem;
            font-weight: 500;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }

        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
            color: #555;
            border-bottom: 1px solid #f0f0f0;
        }

        .table-hover tbody tr:hover {
            background-color: #fff5f6;
        }

        /* --- Dropdown List (Autocomplete) --- */
        .dropdown-list {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            z-index: 1000;
            max-height: 200px;
            overflow-y: auto;
            border: 1px solid #ddd;
            border-radius: 0 0 8px 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .dropdown-list .dropdown-item {
            padding: 10px 15px;
            cursor: pointer;
            border-bottom: 1px solid #f9f9f9;
        }

        .dropdown-list .dropdown-item:hover {
            background-color: #f8f9fa;
            color: var(--custom-maroon);
        }

        /* --- Badges & Actions --- */
        .badge-pill-soft {
            border-radius: 50px;
            padding: 6px 12px;
            font-weight: 500;
            font-size: 0.75rem;
        }

        .bg-soft-success {
            background-color: #d1fae5;
            color: #065f46;
        }

        .bg-soft-secondary {
            background-color: #f3f4f6;
            color: #4b5563;
        }

        .bg-soft-info {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .bg-soft-danger {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .action-btn {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
            transition: var(--transition);
            background: transparent;
            border: 1px solid transparent;
        }

        .action-btn:hover {
            background: var(--custom-maroon-subtle);
            color: var(--custom-maroon);
        }

        .action-btn.delete:hover {
            background: #fee2e2;
            color: #dc2626;
        }

        /* Animation */
        .animate-up {
            animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
            opacity: 0;
            transform: translateY(20px);
        }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>

    <div class="page-header-wrapper d-flex flex-wrap justify-content-between align-items-center gap-3 animate-up">
        <div>
            <h4 class="fw-bold mb-1" style="color: var(--custom-maroon);">Data Mahasiswa</h4>
            <small class="text-muted">Kelola data mahasiswa, ruangan, dan status magang.</small>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <div class="dropdown position-relative">
                <button class="btn btn-tool shadow-sm" type="button" id="toolsBtn" onclick="toggleTools(event)">
                    <i class="bi bi-gear-fill text-secondary"></i> Tools
                </button>

                <div id="toolsDropdownMenu" class="dropdown-menu dropdown-menu-right shadow-sm border-0"
                    style="border-radius: 12px; position: absolute; right: 0; top: 110%; z-index: 2000; display: none; min-width: 200px; background: white;">

                    <a class="dropdown-item py-2" href="javascript:void(0)" onclick="exportMahasiswa(); closeTools();">
                        <i class="bi bi-file-earmark-excel text-success mr-2"></i> Export Excel
                    </a>
                    <a class="dropdown-item py-2" href="javascript:void(0)"
                        onclick="downloadTemplateMahasiswa(); closeTools();">
                        <i class="bi bi-download text-primary mr-2"></i> Template
                    </a>
                    <a class="dropdown-item py-2" href="javascript:void(0)" onclick="copyAllLinks(); closeTools();">
                        <i class="bi bi-link-45deg text-info mr-2"></i> Salin Semua Link
                    </a>

                    <div class="dropdown-divider"></div>

                    <label class="dropdown-item py-2 mb-0 cursor-pointer" style="cursor: pointer;">
                        <i class="bi bi-upload text-warning mr-2"></i> Import Excel
                        <input type="file" id="fileImportMahasiswa" style="display: none" accept=".xlsx,.xls"
                            onchange="closeTools(); importMahasiswa(this);">
                    </label>
                </div>
            </div>

            <a href="{{ route('mahasiswa.create') }}" class="btn btn-maroon shadow-sm d-flex align-items-center gap-2">
                <i class="bi bi-plus-lg"></i> Mahasiswa Baru
            </a>
        </div>
    </div>

    <div class="filter-card animate-up" style="animation-delay: 0.1s;">
        <div class="filter-header">
            <i class="bi bi-funnel-fill mr-2"></i> Filter & Pencarian
        </div>
        <div class="card-body p-4">
            <form id="filterForm" method="GET" action="{{ route('mahasiswa.index') }}">
                <div class="row">
                    <div class="col-md-4 mb-3 mb-md-0">
                        <label class="small text-muted font-weight-bold text-uppercase">Cari Mahasiswa</label>
                        <div class="input-group shadow-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light border-right-0"><i class="bi bi-search"></i></span>
                            </div>
                            <input type="text" class="form-control bg-light border-left-0" name="search"
                                placeholder="Nama mahasiswa..." value="{{ request('search') }}">
                        </div>
                    </div>

                    <div class="col-md-3 mb-3 mb-md-0">
                        <label class="small text-muted font-weight-bold text-uppercase">Universitas</label>
                        <div class="position-relative">
                            <div class="input-group shadow-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-light border-right-0"><i
                                            class="bi bi-building"></i></span>
                                </div>
                                <input type="text" class="form-control bg-light border-left-0" id="univ_asal"
                                    placeholder="Semua Kampus" autocomplete="off">
                                <input type="hidden" id="univ_asal_hidden" name="univ_asal"
                                    value="{{ request('univ_asal') }}">
                            </div>
                            <div id="univDropdown" class="dropdown-list" style="display: none;"></div>
                        </div>
                    </div>

                    <div class="col-md-3 mb-3 mb-md-0">
                        <label class="small text-muted font-weight-bold text-uppercase">Ruangan</label>
                        <div class="input-group shadow-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light border-right-0"><i
                                        class="bi bi-door-open"></i></span>
                            </div>
                            <select class="form-control bg-light border-left-0" name="ruangan_id">
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

                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-maroon w-100 mr-2 shadow-sm">Terapkan</button>
                        <a href="{{ route('mahasiswa.index') }}" class="btn btn-light border shadow-sm"
                            title="Reset Filter">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="custom-table-card animate-up" style="animation-delay: 0.2s;">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th class="text-center" width="5%">No</th>
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
                            <td class="text-center text-muted font-weight-bold">{{ $loop->iteration }}</td>
                            <td>
                                <span class="font-weight-bold text-dark d-block">{{ $m->nm_mahasiswa }}</span>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="text-dark">{{ $m->mou ? ($m->mou->nama_instansi ?? $m->mou->nama_universitas) : '-' }}</span>
                                    <small class="text-muted">{{ $m->prodi }}</small>
                                </div>
                            </td>
                            <td>
                                @if ($m->ruangan)
                                    <span class="badge badge-light border text-dark p-2">
                                        <i class="bi bi-geo-alt mr-1"></i>{{ $m->ruangan->nm_ruangan }}
                                    </span>
                                @else
                                    <span class="text-muted font-italic">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if ($m->tanggal_berakhir)
                                    {{-- Logika sisa_hari dari model --}}
                                    @if ($m->sisa_hari == 'Selesai')
                                        <span class="badge badge-pill-soft bg-soft-danger">Selesai</span>
                                    @elseif (is_numeric(explode(' ', $m->sisa_hari)[0]) && explode(' ', $m->sisa_hari)[0] > 0)
                                        <span class="badge badge-pill-soft bg-soft-info">{{ $m->sisa_hari }}</span>
                                    @else
                                        {{-- Fallback jika 0 hari atau format lain --}}
                                        <span class="badge badge-pill-soft bg-soft-danger">Berakhir</span>
                                    @endif
                                @else
                                    -
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
                                <a href="{{ route('mahasiswa.show', $m->id) }}" class="action-btn" title="Detail">
                                    <i class="bi bi-eye-fill"></i>
                                </a>
                                <a href="{{ route('mahasiswa.edit', $m->id) }}" class="action-btn" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>

                                <a href="{{ route('mahasiswa.sertifikat.summary', $m->id) }}" class="action-btn"
                                    title="Ringkasan Sertifikat">
                                    <i class="bi bi-bar-chart-line-fill"></i> </a>

                                <form action="{{ route('mahasiswa.destroy', $m->id) }}" method="POST"
                                    class="d-inline delete-form" style="margin-left: 2px;">
                                    @csrf @method('DELETE')
                                    <button type="button" class="action-btn delete btn-delete" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
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

    <div class="d-flex justify-content-center mt-4 animate-up" style="animation-delay: 0.3s;">
        {{ $mahasiswas->links('pagination.custom') }}
    </div>

    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                showConfirmButton: false,
                timer: 1800,
                toast: true,
                position: 'top-end'
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian!',
                text: '{{ session('error') }}',
                showConfirmButton: true
            });
        </script>
    @endif

@endsection

@section('scripts')
    <script src="https://cdn.sheetjs.com/xlsx-0.20.3/package/dist/xlsx.full.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

    <script>
        // --- Toastify Helper ---
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

        // --- 1. MANUAL DROPDOWN SCRIPT ---
        function toggleTools(e) {
            if (e) e.stopPropagation();
            var menu = document.getElementById('toolsDropdownMenu');
            menu.style.display = (menu.style.display === 'block') ? 'none' : 'block';
        }

        function closeTools() {
            document.getElementById('toolsDropdownMenu').style.display = 'none';
        }

        window.addEventListener('click', function(e) {
            var menu = document.getElementById('toolsDropdownMenu');
            var btn = document.getElementById('toolsBtn');
            if (menu.style.display === 'block' && !menu.contains(e.target) && !btn.contains(e.target)) {
                menu.style.display = 'none';
            }
        });

        // --- 2. COPY LINKS LOGIC (DENGAN FALLBACK) ---
        function copyAllLinks() {
            const params = new URLSearchParams();
            const univ = document.getElementById('univ_asal_hidden').value;
            const search = document.querySelector('input[name="search"]').value;
            const ruangan = document.querySelector('select[name="ruangan_id"]').value;

            if (univ) params.append('univ_asal', univ);
            if (search) params.append('search', search);
            if (ruangan) params.append('ruangan_id', ruangan);

            showToast("Sedang mengambil link...", "info");

            fetch('{{ route('mahasiswa.links') }}?' + params.toString())
                .then(res => res.json())
                .then(data => {
                    if (!data || data.length === 0) {
                        showToast('Tidak ada data link untuk filter ini.', 'info');
                        return;
                    }
                    const message = data.map(m => `${m.nama}: ${m.link}`).join('\n');

                    if (navigator.clipboard && window.isSecureContext) {
                        navigator.clipboard.writeText(message).then(() => {
                            showToast(`Berhasil menyalin ${data.length} link!`, "success");
                        }).catch(err => {
                            console.error('Async Clipboard fail, trying fallback', err);
                            fallbackCopyTextToClipboard(message);
                        });
                    } else {
                        fallbackCopyTextToClipboard(message);
                    }
                })
                .catch(err => {
                    console.error(err);
                    showToast('Gagal mengambil data link.', 'error');
                });
        }

        function fallbackCopyTextToClipboard(text) {
            var textArea = document.createElement("textarea");
            textArea.value = text;
            textArea.style.top = "0";
            textArea.style.left = "0";
            textArea.style.position = "fixed";
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            try {
                var successful = document.execCommand('copy');
                if (successful) {
                    showToast("Link berhasil disalin!", "success");
                } else {
                    showToast("Gagal menyalin link (Browser memblokir).", "error");
                }
            } catch (err) {
                console.error('Fallback error', err);
                showToast("Gagal menyalin link.", "error");
            }
            document.body.removeChild(textArea);
        }

        // --- 3. IMPORT LOGIC ---
        function importMahasiswa(input) {
            const file = input.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function(e) {
                try {
                    const data = new Uint8Array(e.target.result);
                    const workbook = XLSX.read(data, {
                        type: 'array',
                        cellDates: true
                    });
                    const firstSheetName = workbook.SheetNames[0];
                    const json = XLSX.utils.sheet_to_json(workbook.Sheets[firstSheetName], {
                        defval: '',
                        dateNF: 'yyyy-mm-dd',
                        raw: false
                    });

                    if (json.length === 0) {
                        showToast('File Excel kosong atau format salah.', 'error');
                        return;
                    }

                    json.forEach(row => {
                        const norm = val => {
                            if (!val) return null;
                            if (val instanceof Date) return val.toISOString().split('T')[0];
                            return val.toString().trim();
                        };
                        row['Tanggal Mulai'] = norm(row['Tanggal Mulai'] || row['tanggal mulai']);
                        row['Tanggal Berakhir'] = norm(row['Tanggal Berakhir'] || row['tanggal berakhir']);
                    });

                    const fd = new FormData();
                    fd.append('_token', '{{ csrf_token() }}');
                    fd.append('data', JSON.stringify(json));

                    showToast("Sedang memproses data...", "info");

                    fetch('{{ route('mahasiswa.import_excel') }}', {
                            method: 'POST',
                            body: fd,
                            headers: {
                                'Accept': 'application/json'
                            }
                        })
                        .then(r => r.json())
                        .then(res => {
                            if (res.success) {
                                showToast(res.message, 'success');
                                setTimeout(() => location.reload(), 1500);
                            } else {
                                console.error(res);
                                let msg = res.message;
                                if (res.errors && Array.isArray(res.errors)) {
                                    msg += ": " + res.errors[0];
                                }
                                showToast('Gagal: ' + msg, 'error');
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            showToast('Terjadi kesalahan server (Cek Console).', 'error');
                        });
                } catch (error) {
                    console.error(error);
                    showToast('Gagal membaca file Excel.', 'error');
                }
            };
            reader.readAsArrayBuffer(file);
            input.value = '';
        }

        // --- 4. EXPORT & TEMPLATE (FIXED PAGINATION ISSUE) ---
        function exportMahasiswa() {
            try {
                const rawData = @json($mahasiswas);
                const dataList = rawData.data ? rawData.data : rawData;

                if (!dataList || dataList.length === 0) {
                    showToast("Tidak ada data untuk diexport", "error");
                    return;
                }

                const data = dataList.map(m => [
                    m.nm_mahasiswa || '',
                    m.univ_asal || '',
                    m.prodi || '',
                    m.ruangan ? m.ruangan.nm_ruangan : (m.nm_ruangan || ''),
                    m.tanggal_mulai || '-',
                    m.tanggal_berakhir || '-',
                    m.status || '',
                    m.share_token ? `{{ url('/absensi/') }}/${m.share_token}` : ''
                ]);

                const ws = XLSX.utils.aoa_to_sheet([
                    ['Nama', 'Universitas', 'Prodi', 'Ruangan', 'Tanggal Mulai', 'Tanggal Berakhir', 'Status',
                        'Link Absensi'
                    ]
                ].concat(data));

                const wb = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(wb, ws, 'Mahasiswa');
                XLSX.writeFile(wb, `Data_Mahasiswa_${new Date().toISOString().split('T')[0]}.xlsx`);

                showToast("Berhasil export Excel!", "success");
            } catch (e) {
                console.error(e);
                showToast("Gagal export: " + e.message, "error");
            }
        }

        function downloadTemplateMahasiswa() {
            const ws = XLSX.utils.aoa_to_sheet([
                ['Nama', 'Universitas', 'Prodi', 'Ruangan', 'Tanggal Mulai', 'Tanggal Berakhir', 'Status'],
                ['Budi Santoso', 'Univ Merdeka', 'Informatika', 'Ruang Mawar', '2025-01-01', '2025-06-01', 'aktif']
            ]);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, 'Template');
            XLSX.writeFile(wb, 'Template_Mahasiswa.xlsx');
        }

        // --- 5. LIVE SEARCH / AUTOCOMPLETE ---
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
        document.addEventListener('DOMContentLoaded', () => {
            const deleteButtons = document.querySelectorAll('.btn-delete');
            deleteButtons.forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const form = this.closest('form');

                    Swal.fire({
                        title: 'Yakin hapus data?',
                        text: "Data mahasiswa ini akan dihapus permanen.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#7c1316',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
@endsection
