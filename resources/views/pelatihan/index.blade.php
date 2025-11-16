@extends('layouts.app')

@section('title', 'Pelatihan Dasar')
@section('page-title', 'Data Pelatihan Dasar')

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
        .custom-table-card {
            background: #fff;
            border-radius: var(--card-radius);
            box-shadow: var(--shadow-soft);
            overflow: hidden;
            border: none;
        }
        .table {
            margin-bottom: 0;
            border-collapse: collapse;
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

        .pelatihan-list-wrapper {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            max-width: 350px;
        }

        .pelatihan-list-item {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 6px;
            padding: 0.3rem 0.6rem;
            font-size: 0.85rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .pelatihan-list-item:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
        }
        .pelatihan-list-item:focus {
            outline: none;
            box-shadow: 0 0 0 2px var(--custom-maroon-light);
        }
        .pelatihan-list-item .nama {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            padding-right: 10px;
        }
        .pelatihan-list-item .tahun {
            font-weight: 600;
            color: var(--custom-maroon);
            background: white;
            padding: 2px 6px;
            border-radius: 4px;
            flex-shrink: 0;
        }

        .table-hover tbody tr:hover {
            background-color: #fff5f6;
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
            <h4 class="fw-bold mb-1" style="color: var(--custom-maroon);">Data Pelatihan Dasar</h4>
            <small class="text-muted">Kelola data pelatihan dasar karyawan.</small>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <div class="dropdown position-relative">
                <button class="btn btn-tool shadow-sm" type="button" id="toolsBtn" onclick="toggleTools(event)">
                    <i class="bi bi-gear-fill text-secondary"></i> Tools
                </button>
                <div id="toolsDropdownMenu" class="dropdown-menu dropdown-menu-right shadow-sm border-0"
                    style="border-radius: 12px; position: absolute; right: 0; top: 110%; z-index: 2000; display: none; min-width: 200px; background: white;">
                    <a class="dropdown-item py-2" href="javascript:void(0)" onclick="exportPelatihan(); closeTools();">
                        <i class="bi bi-file-earmark-excel text-success mr-2"></i> Export Excel
                    </a>
                    <a class="dropdown-item py-2" href="javascript:void(0)" onclick="downloadTemplatePelatihan(); closeTools();">
                        <i class="bi bi-download text-primary mr-2"></i> Template
                    </a>
                    <div class="dropdown-divider"></div>
                    <label class="dropdown-item py-2 mb-0 cursor-pointer" style="cursor: pointer;">
                        <i class="bi bi-upload text-warning mr-2"></i> Import Excel
                        <input type="file" id="fileImportPelatihan" style="display: none" accept=".xlsx,.xls"
                            onchange="closeTools(); importPelatihan(this);">
                    </label>
                </div>
            </div>
            <a href="{{ route('pelatihan.create') }}" class="btn btn-maroon shadow-sm d-flex align-items-center gap-2">
                <i class="bi bi-plus-lg"></i> Pelatihan Baru
            </a>
        </div>
    </div>
    <div class="filter-card animate-up" style="animation-delay: 0.1s;">
        <div class="filter-header">
            <i class="bi bi-funnel-fill mr-2"></i> Filter & Pencarian
        </div>
        <div class="card-body p-4">
            <form id="filterForm" method="GET" action="{{ route('pelatihan.index') }}">
                <div class="row">
                    <div class="col-md-4 mb-3 mb-md-0">
                        <label class="small text-muted font-weight-bold text-uppercase">Cari Nama Karyawan</label>
                        <div class="input-group shadow-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light border-right-0"><i class="bi bi-search"></i></span>
                            </div>
                            <input type="text" class="form-control bg-light border-left-0" name="search"
                                placeholder="Nama karyawan..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-4 mb-3 mb-md-0">
                        <label class="small text-muted font-weight-bold text-uppercase">Jabatan</label>
                        <div class="input-group shadow-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light border-right-0"><i class="bi bi-briefcase"></i></span>
                            </div>
                            <input type="text" class="form-control bg-light border-left-0" name="jabatan"
                                placeholder="Filter jabatan..." value="{{ request('jabatan') }}">
                        </div>
                    </div>
                    <div class="col-md-4 mb-3 mb-md-0">
                        <label class="small text-muted font-weight-bold text-uppercase">Unit</label>
                        <div class="input-group shadow-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light border-right-0"><i class="bi bi-building"></i></span>
                            </div>
                            <input type="text" class="form-control bg-light border-left-0" name="unit"
                                placeholder="Filter unit..." value="{{ request('unit') }}">
                        </div>
                    </div>
                    <div class="col-md-12 d-flex gap-2 mt-3">
                        <button type="submit" class="btn btn-maroon shadow-sm">Terapkan</button>
                        <a href="{{ route('pelatihan.index') }}" class="btn btn-light border shadow-sm"
                            title="Reset Filter">
                            <i class="bi bi-arrow-counterclockwise"></i> Reset
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
                        <th>Info Karyawan</th>
                        <th>Jabatan</th>
                        <th>Unit</th>
                        <th>Pelatihan Dasar (Tahun)</th>
                        <th class="text-center" width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pelatihans as $index => $pelatihan)
                        <tr>
                            <td class="text-center text-muted font-weight-bold">{{ ($pelatihans->currentPage() - 1) * $pelatihans->perPage() + $index + 1 }}</td>
                            <td>
                                <span class="font-weight-bold text-dark d-block">{{ $pelatihan->nama }}</span>
                                @if ($pelatihan->is_pns)
                                    <span class="badge badge-light border text-dark p-1" style="font-size: 0.75rem;">
                                        PNS | {{ $pelatihan->nip }} ({{ $pelatihan->golongan }})
                                    </span>
                                @else
                                    <span class="badge badge-light border text-muted p-1" style="font-size: 0.75rem;">
                                        Non-PNS
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="text-dark">{{ $pelatihan->jabatan ?? '-' }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-light border text-dark p-2">
                                    {{ $pelatihan->unit ?? '-' }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $daftarPelatihan = collect($pelatihan->pelatihan_dasar ?? [])->sortByDesc('tahun')->all();
                                @endphp

                                <div class="pelatihan-list-wrapper">
                                    @if (is_array($daftarPelatihan) && count($daftarPelatihan) > 0)
                                        @foreach ($daftarPelatihan as $item)
                                            @php
                                                $nama = is_object($item) ? ($item->nama ?? null) : ($item['nama'] ?? null);
                                                $tahun = is_object($item) ? ($item->tahun ?? null) : ($item['tahun'] ?? null);
                                            @endphp

                                            @if ($nama)
                                                <div class="pelatihan-list-item"
                                                     tabindex="0"
                                                     data-bs-toggle="popover"
                                                     data-bs-trigger="focus"
                                                     data-bs-placement="top"
                                                     title="Nama Pelatihan"
                                                     data-bs-content="{{ $nama }}">

                                                    <span class="nama">{{ Str::limit($nama, 35) }}</span>
                                                    <span class="tahun">{{ $tahun ?? '-' }}</span>
                                                </div>
                                            @endif
                                        @endforeach
                                    @else
                                        <span class="text-muted font-italic">-</span>
                                    @endif
                                </div>
                            </td>

                            <td class="text-center">
                                <a href="{{ route('pelatihan.show', $pelatihan->id) }}" class="action-btn" title="Detail">
                                    <i class="bi bi-eye-fill"></i>
                                </a>
                                <a href="{{ route('pelatihan.edit', $pelatihan->id) }}" class="action-btn" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('pelatihan.destroy', $pelatihan->id) }}" method="POST"
                                    class="d-inline delete-form">
                                    @csrf @method('DELETE')
                                    <button type="button" class="action-btn delete btn-delete" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="bi bi-folder-x display-4 text-muted mb-3" style="opacity: 0.5;"></i>
                                    <h5 class="text-muted font-weight-bold">Tidak ada data ditemukan</h5>
                                    <p class="text-muted small">Tambahkan pelatihan dasar terlebih dahulu.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="d-flex justify-content-center mt-4 animate-up" style="animation-delay: 0.3s;">
        {{ $pelatihans->links('pagination.custom') }}
    </div>

    {{-- ... (Kode SweetAlert session success/error) ... --}}
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


        function exportPelatihan() {
            try {
                const rawData = @json($pelatihans);
                const dataList = rawData.data ? rawData.data : rawData;

                if (!dataList || dataList.length === 0) {
                    showToast("Tidak ada data untuk diexport", "error");
                    return;
                }

                const data = dataList.map(p => {
                    const pelatihansList = p.pelatihan_dasar || [];

                    pelatihansList.sort((a, b) => (b.tahun || 0) - (a.tahun || 0));

                    const pelatihans = pelatihansList
                        .map(item => {
                            const nama = item.nama || item.pelatihan || 'N/A';
                            const tahun = item.tahun || '?';
                            return `${nama} (${tahun})`;
                        })
                        .join('; ');

                    return [
                        p.nama || '',
                        p.is_pns ? p.nip : 'Non-PNS',
                        p.is_pns ? p.golongan : '',
                        p.jabatan || '',
                        p.unit || '',
                        pelatihans
                    ];
                });

                const ws = XLSX.utils.aoa_to_sheet([
                    ['Nama', 'NIP (Jika PNS)', 'Golongan', 'Jabatan', 'Unit', 'Daftar Pelatihan (Tahun)']
                ].concat(data));

                const wb = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(wb, ws, 'Pelatihan');
                XLSX.writeFile(wb, `Data_Pelatihan_${new Date().toISOString().split('T')[0]}.xlsx`);

                showToast("Berhasil export Excel!", "success");
            } catch (e) {
                console.error(e);
                showToast("Gagal export: " + e.message, "error");
            }
        }

        function downloadTemplatePelatihan() {
            const ws = XLSX.utils.aoa_to_sheet([
                ['Nama', 'Jabatan', 'Unit', 'is_pns (1=PNS, 0=Non-PNS)', 'NIP', 'Golongan', 'Pelatihan1_Nama', 'Pelatihan1_Tahun', 'Pelatihan2_Nama', 'Pelatihan2_Tahun'],
                ['Budi Santoso', 'Manager', 'IT', 1, '199001012010011001', 'III/a', 'Basic Safety', 2020, 'Workshop Excel', 2021],
                ['Ana Wati', 'Staff', 'Marketing', 0, '', '', 'Digital Marketing', 2022, '', '']
            ]);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, 'Template');
            XLSX.writeFile(wb, 'Template_Pelatihan.xlsx');
            showToast("Template berhasil diunduh!", "success");
        }

        function importPelatihan(input) {
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
                        raw: false
                    });

                    if (json.length === 0) {
                        showToast('File Excel kosong atau format salah.', 'error');
                        return;
                    }

                    const fd = new FormData();
                    fd.append('_token', '{{ csrf_token() }}');
                    fd.append('data', JSON.stringify(json));

                    showToast("Sedang memproses data...", "info");

                    fetch('{{ route('pelatihan.import_excel') }}', {
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
                                showToast('Gagal: ' + (res.message || 'Import gagal'), 'error');
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            showToast('Terjadi kesalahan server.', 'error');
                        });

                } catch (error) {
                    console.error(error);
                    showToast('Gagal membaca file Excel.', 'error');
                }
            };
            reader.readAsArrayBuffer(file);
            input.value = '';
        }

        document.addEventListener('DOMContentLoaded', () => {
            const deleteButtons = document.querySelectorAll('.btn-delete');
            deleteButtons.forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const form = this.closest('form');

                    Swal.fire({
                        title: 'Yakin hapus data?',
                        text: "Data pelatihan ini akan dihapus permanen.",
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
            var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
            var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
                return new bootstrap.Popover(popoverTriggerEl, {
                    html: false,
                    sanitize: false
                })
            });
        });
    </script>
@endsection
