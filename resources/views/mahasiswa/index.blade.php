@extends('layouts.app')

@section('title', 'Mahasiswa')
@section('page-title', 'Data Mahasiswa')

@section('content')
    <style>
        :root {
            --maroon: #7c1316;
            --maroon-light: #9d2a2e;
            /* Digunakan untuk hover */
            --text-dark: #222;
            --text-muted: #6c757d;
            --bg-light: #f8f9fa;
        }

        /* --- Animasi & Efek --- */
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

        /* --- Komponen Utama --- */
        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            /* Terapkan animasi fade-in */
            animation: fadeInUp 0.5s ease-out forwards;
            opacity: 0;
            /* Mulai transparan untuk animasi */
        }

        .alert {
            border-radius: 10px;
            font-weight: 500;
            /* Terapkan animasi juga untuk alert */
            animation: fadeInUp 0.4s ease-out forwards;
        }

        /* --- Styling Tombol --- */
        .btn-maroon {
            background-color: var(--maroon);
            border: none;
            color: #fff;
            font-weight: 600;
            border-radius: 10px;
            padding: 8px 14px;
            transition: all 0.2s ease;
        }

        .btn-maroon:hover {
            background-color: var(--maroon-light);
            transform: translateY(-2px);
            /* Efek 'lift' */
            box-shadow: 0 4px 12px rgba(124, 19, 22, 0.25);
        }

        /* Tombol sekunder (Import/Export) */
        .btn-outline-custom {
            border: 1px solid #ced4da;
            color: var(--text-dark);
            font-weight: 500;
            border-radius: 10px;
            background-color: #fff;
            transition: all 0.2s ease;
            gap: 0.5rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-outline-custom:hover {
            background-color: var(--bg-light);
            border-color: #adb5bd;
            color: var(--text-dark);
            transform: translateY(-1px);
        }

        /* --- Styling & Animasi Tabel --- */
        .table {
            border-radius: 12px;
            overflow: hidden;
            /* Penting untuk border-radius */
            border-collapse: separate;
            /* Membantu shadow-box pada hover */
            border-spacing: 0;
        }

        .table thead {
            background-color: var(--maroon);
            color: #fff;
        }

        .table th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            padding-top: 1rem;
            padding-bottom: 1rem;
        }

        .table td {
            vertical-align: middle !important;
            padding: 0.9rem 0.75rem;
            /* Sedikit lebih tinggi */
        }

        /* Ini adalah animasi yang Anda minta */
        .table-hover tbody tr {
            transition: all 0.2s ease-out;
        }

        .table-hover tbody tr:hover {
            background-color: #fffafb;
            /* Warna hover yang sangat halus */
            transform: translateY(-3px);
            /* Efek 'lift' pada baris */
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.07);
            position: relative;
            /* Agar shadow terlihat di atas baris lain */
            z-index: 10;
        }

        /* --- End Animasi Tabel --- */

        .btn-sm {
            border-radius: 8px;
        }

        .text-maroon {
            color: var(--maroon);
        }

        /* Dropdown styling untuk universitas */
        .dropdown-list {
            list-style: none;
            padding: 0;
            margin: 0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .dropdown-list .dropdown-item {
            padding: 0.75rem 1rem;
            cursor: pointer;
            transition: background-color 0.2s ease;
            border-bottom: 1px solid #f0f0f0;
        }

        .dropdown-list .dropdown-item:hover {
            background-color: #f8f9fa;
            color: var(--maroon);
        }

        .dropdown-list .dropdown-item.active {
            background-color: var(--maroon);
            color: white;
        }

        @media (max-width: 768px) {
            .page-header {
                flex-direction: column;
                align-items: flex-start !important;
                gap: 1rem;
            }

            th,
            td {
                font-size: 0.85rem;
                padding: 0.75rem 0.5rem;
            }

            /* Stack tombol di mobile */
            .header-buttons {
                flex-direction: column;
                align-items: stretch !important;
                width: 100%;
                gap: 0.5rem;
            }

            .header-buttons .btn,
            .header-buttons .btn-maroon,
            .header-buttons .btn-outline-custom {
                width: 100%;
                margin: 0 !important;
                display: flex !important;
                align-items: center;
                justify-content: center;
                text-align: center;
            }

            .header-buttons i {
                margin-right: 0.5rem !important;
            }
        }
    </style>

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap page-header">
        <h4 class="fw-bold text-maroon mb-0">Daftar Mahasiswa</h4>

        <div class="d-flex flex-wrap gap-2 header-buttons">
            <button class="btn btn-outline-custom btn-sm d-inline-flex align-items-center" onclick="exportMahasiswa()">
                <i class="bi bi-file-earmark-arrow-down"></i>
                <span>Export Excel</span>
            </button>

            <label class="btn btn-outline-custom btn-sm d-inline-flex align-items-center" style="cursor: pointer;">
                <i class="bi bi-upload"></i>
                <span>Import Excel</span>
                <input type="file" id="fileImportMahasiswa" style="display:none" accept=".xlsx,.xls"
                    onchange="importMahasiswa(this)">
            </label>

            <button class="btn btn-outline-custom btn-sm d-inline-flex align-items-center"
                onclick="downloadTemplateMahasiswa()">
                <i class="bi bi-download"></i>
                <span>Template</span>
            </button>

            <button type="button" class="btn btn-outline-custom btn-sm d-inline-flex align-items-center"
                onclick="copyAllLinks()">
                <i class="bi bi-clipboard"></i>
                <span>Salin Semua Link Absensi</span>
            </button>

            <a href="{{ route('mahasiswa.create') }}" class="btn-maroon btn-sm d-inline-flex align-items-center">
                <i class="bi bi-person-plus"></i>
                <span>Tambah Mahasiswa</span>
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Filter Section -->
    <div class="card mb-4">
        <div class="card-body">
            <form id="filterForm" method="GET" action="{{ route('mahasiswa.index') }}">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="search" class="form-label">Cari Nama Mahasiswa</label>
                        <input type="text" class="form-control" id="search" name="search"
                            placeholder="Ketik nama mahasiswa..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-6">
                        <label for="univ_asal" class="form-label">Filter Universitas</label>
                        <div class="position-relative">
                            <input type="text" class="form-control" id="univ_asal" placeholder="Cari universitas..."
                                autocomplete="off">
                            <input type="hidden" id="univ_asal_hidden" name="univ_asal" value="{{ request('univ_asal') }}">
                            <div id="univDropdown" class="dropdown-list"
                                style="display: none; position: absolute; top: 100%; left: 0; right: 0; background: white; border: 1px solid #dee2e6; border-radius: 0.375rem; max-height: 300px; overflow-y: auto; z-index: 1000;">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-3 d-flex gap-2">
                    <button type="submit" class="btn btn-maroon btn-sm">
                        <i class="bi bi-search"></i> Filter
                    </button>
                    <a href="{{ route('mahasiswa.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-clockwise"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead>
                        <tr class="text-center">
                            <th>#</th>
                            <th class="text-start">Nama</th>
                            <th>Universitas</th>
                            <th>Prodi</th>
                            <th>Ruangan</th>
                            <th>Masa Aktif</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($mahasiswas as $m)
                            <tr class="text-center">
                                <td>{{ $loop->iteration }}</td>
                                <td class="text-start">{{ $m->nm_mahasiswa }}</td>
                                <td>{{ $m->univ_asal }}</td>
                                <td>{{ $m->prodi }}</td>
                                <td>{{ $m->ruangan ? $m->ruangan->nm_ruangan : $m->nm_ruangan }}</td>
                                <td>
                                    @if ($m->tanggal_berakhir)
                                        @if ($m->sisa_hari > 0)
                                            <span class="badge bg-info">{{ $m->sisa_hari }} </span>
                                        @else
                                            <span class="badge bg-danger">Habis</span>
                                        @endif
                                    @else
                                        <span class="badge bg-secondary">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($m->status == 'aktif')
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary">Nonaktif</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('mahasiswa.show', $m->id) }}" class="btn btn-sm btn-info text-white">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('mahasiswa.edit', $m->id) }}"
                                        class="btn btn-sm btn-warning text-white">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="{{ route('mahasiswa.destroy', $m->id) }}" method="POST"
                                        style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger"
                                            onclick="return confirm('Yakin ingin menghapus mahasiswa ini?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    Belum ada data mahasiswa.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="d-flex justify-content-center mt-3">
                    {{ $mahasiswas->links('pagination.custom') }}
                </div>
            </div>
        </div>
    </div>

    @section('scripts')
        <!-- SheetJS dan Toastify -->
        <script src="https://cdn.sheetjs.com/xlsx-0.20.3/package/dist/xlsx.full.min.js"></script>
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
        <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

        <script>
            // Fungsi utilitas untuk Toastify
            function showToast(message, type = 'success') {
                const colors = {
                    success: "linear-gradient(to right, #00b09b, #96c93d)",
                    error: "linear-gradient(to right, #ff5f6d, #ffc371)",
                    info: "linear-gradient(to right, #2193b0, #6dd5ed)"
                };

                Toastify({
                    text: message,
                    duration: 3000,
                    gravity: "bottom",
                    position: "right",
                    style: {
                        background: colors[type] || colors.info
                    }
                }).showToast();
            }

            // Copy semua link absensi ke clipboard for ALL mahasiswa matching current filters
            async function copyAllLinks() {
                try {
                    // fetch all filtered mahasiswa via export endpoint so it respects current filters
                    const resp = await fetch('{{ route('mahasiswa.export') }}' + window.location.search, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    if (!resp.ok) throw new Error('Gagal mengambil data mahasiswa');
                    const data = await resp.json();

                    if (!data || data.length === 0) {
                        showToast('Tidak ada mahasiswa untuk disalin.', 'info');
                        return;
                    }

                    const message = data.map(m => `Link Absensi untuk ${m.nama}:\n${m.share_link || ''}`).join('\n\n');

                    await navigator.clipboard.writeText(message);
                    showToast('Semua link absensi berhasil disalin!', 'success');
                } catch (err) {
                    console.error(err);
                    showToast('Gagal menyalin link absensi', 'error');
                }
            }

            // Export data mahasiswa ke Excel for ALL mahasiswa matching current filters
            async function exportMahasiswa() {
                try {
                    const resp = await fetch('{{ route('mahasiswa.export') }}' + window.location.search, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    if (!resp.ok) throw new Error('Gagal mengambil data untuk export');
                    const rows = await resp.json();

                    if (!rows || rows.length === 0) {
                        showToast('Tidak ada data mahasiswa untuk diexport.', 'info');
                        return;
                    }

                    const data = rows.map(r => [
                        r.nama || '',
                        r.universitas || '',
                        r.prodi || '',
                        r.ruangan || '',
                        r.tanggal_mulai || '-',
                        r.tanggal_berakhir || '-',
                        r.status || '',
                        r.share_link || '' // include attendance link
                    ]);

                    const ws_data = [
                        [
                            'Nama', 'Universitas', 'Prodi', 'Ruangan', 'Tanggal Mulai', 'Tanggal Berakhir', 'Status',
                            'Link Absensi'
                        ]
                    ].concat(data);

                    const ws = XLSX.utils.aoa_to_sheet(ws_data);
                    const wb = XLSX.utils.book_new();
                    XLSX.utils.book_append_sheet(wb, ws, 'Mahasiswa');
                    XLSX.writeFile(wb, `Data_Mahasiswa_${new Date().toISOString().split('T')[0]}.xlsx`);

                    showToast('Data mahasiswa berhasil diexport!', 'success');
                } catch (error) {
                    console.error(error);
                    showToast('Gagal export data mahasiswa', 'error');
                }
            }

            // Unduh template Excel untuk input
            function downloadTemplateMahasiswa() {
                try {
                    const ws_data = [
                        ['Nama', 'Universitas', 'Prodi', 'Ruangan', 'Tanggal Mulai', 'Tanggal Berakhir', 'Status'],
                        ['Budi Santoso', 'Universitas A', 'Teknik Informatika', 'Gelatik', '2025-08-01', '2025-12-31',
                            'aktif'
                        ]
                    ];

                    const ws = XLSX.utils.aoa_to_sheet(ws_data);
                    const wb = XLSX.utils.book_new();
                    XLSX.utils.book_append_sheet(wb, ws, 'Template_Mahasiswa');
                    XLSX.writeFile(wb, 'Template_Mahasiswa.xlsx');

                    showToast("Template Mahasiswa berhasil diunduh!", "success");
                } catch (error) {
                    console.error(error);
                    showToast("Gagal mengunduh template mahasiswa", "error");
                }
            }

            // Import file Excel ke database
            function importMahasiswa(input) {
                const file = input.files[0];
                if (!file) return;

                const reader = new FileReader();

                reader.onload = function(e) {
                    try {
                        const data = new Uint8Array(e.target.result);
                        const workbook = XLSX.read(data, {
                            type: 'array',
                            cellDates: true,
                            raw: false
                        });
                        const firstSheet = workbook.Sheets[workbook.SheetNames[0]];
                        const json = XLSX.utils.sheet_to_json(firstSheet, {
                            defval: '',
                            dateNF: 'yyyy-mm-dd'
                        });

                        if (json.length === 0) {
                            showToast('File kosong atau format tidak sesuai', 'error');
                            return;
                        }

                        // Normalisasi tanggal agar pasti format YYYY-MM-DD
                        json.forEach(row => {
                            const normalizeDate = val => {
                                if (!val) return '';
                                if (typeof val === 'number') return XLSX.SSF.format('yyyy-mm-dd', val);
                                if (val instanceof Date) return val.toISOString().split('T')[0];
                                const date = new Date(val);
                                return isNaN(date) ? '' : date.toISOString().split('T')[0];
                            };

                            row['Tanggal Mulai'] = normalizeDate(row['Tanggal Mulai']);
                            row['Tanggal Berakhir'] = normalizeDate(row['Tanggal Berakhir']);
                        });

                        const formData = new FormData();
                        formData.append('_token', '{{ csrf_token() }}');
                        formData.append('data', JSON.stringify(json));

                        showToast("Sedang mengimpor data...", "info");

                        fetch('{{ route('mahasiswa.store') }}', {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'Accept': 'application/json'
                                }
                            })
                            .then(res => {
                                if (!res.ok) throw new Error('Network response was not ok');
                                return res.json();
                            })
                            .then(res => {
                                if (res.success) {
                                    showToast(res.message || 'Import berhasil!', 'success');
                                    setTimeout(() => location.reload(), 1000);
                                } else {
                                    showToast('Import gagal: ' + (res.message || 'Unknown error'), 'error');
                                }
                            })
                            .catch(err => {
                                console.error(err);
                                showToast('Terjadi kesalahan saat import. Cek konsol untuk detail.', 'error');
                            });

                    } catch (err) {
                        console.error(err);
                        showToast('File tidak valid atau rusak.', 'error');
                    }
                };

                reader.readAsArrayBuffer(file);
                input.value = ''; // Reset input agar bisa pilih file yang sama lagi
            }
        </script>
    @endsection


@endsection
