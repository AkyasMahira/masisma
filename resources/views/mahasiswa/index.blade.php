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
            }
        }
    </style>

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap page-header">
        <h4 class="fw-bold text-maroon mb-0">Daftar Mahasiswa</h4>

        <div class="d-flex flex-wrap gap-2 header-buttons">
            <button class="btn btn-outline-custom btn-sm" onclick="exportMahasiswa()">
                <i class="bi bi-file-earmark-arrow-down me-1"></i> Export Excel
            </button>

            <label class="btn btn-outline-custom btn-sm" style="cursor: pointer;">
                <i class="bi bi-upload me-1"></i> Import Excel
                <input type="file" id="fileImportMahasiswa" style="display:none" accept=".xlsx,.xls"
                    onchange="importMahasiswa(this)">
            </label>

            <button class="btn btn-outline-custom btn-sm" onclick="downloadTemplateMahasiswa()">
                <i class="bi bi-download me-1"></i> Template
            </button>

            <a href="{{ route('mahasiswa.create') }}" class="btn-maroon btn-sm">
                <i class="bi bi-person-plus me-1"></i> Tambah Mahasiswa
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

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
                                <td colspan="7" class="text-center text-muted py-4">
                                    Belum ada data mahasiswa.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @section('scripts')
        {{-- Script Anda tidak berubah dan sudah bagus --}}
        <script src="https://cdn.sheetjs.com/xlsx-0.20.3/package/dist/xlsx.full.min.js"></script>
        <script>
            function exportMahasiswa() {
                const data = [
                    @foreach ($mahasiswas as $m)
                        [
                            {!! json_encode($m->nm_mahasiswa ?? '') !!},
                            {!! json_encode($m->univ_asal ?? '') !!},
                            {!! json_encode($m->prodi ?? '') !!},
                            {!! json_encode($m->ruangan ? $m->ruangan->nm_ruangan : $m->nm_ruangan ?? '') !!},
                            {!! json_encode($m->status ?? '') !!}
                        ] {{ $loop->last ? '' : ',' }}
                    @endforeach
                ];

                const ws_data = [
                    ['Nama', 'Universitas', 'Prodi', 'Ruangan', 'Status']
                ].concat(data);
                const ws = XLSX.utils.aoa_to_sheet(ws_data);
                const wb = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(wb, ws, 'Mahasiswa');
                XLSX.writeFile(wb, `Data_Mahasiswa_${new Date().toISOString().split('T')[0]}.xlsx`);
            }

            function downloadTemplateMahasiswa() {
                const ws_data = [
                    ['Nama', 'Universitas', 'Prodi', 'Ruangan', 'Status'],
                    ['Budi Santoso', 'Universitas A', 'Teknik Informatika', 'Ruang A', 'aktif']
                ];
                const ws = XLSX.utils.aoa_to_sheet(ws_data);
                const wb = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(wb, ws, 'Template_Mahasiswa');
                XLSX.writeFile(wb, 'Template_Mahasiswa.xlsx');
            }

            function importMahasiswa(input) {
                const file = input.files[0];
                if (!file) return;
                const reader = new FileReader();
                reader.onload = function(e) {
                    const data = new Uint8Array(e.target.result);
                    const workbook = XLSX.read(data, {
                        type: 'array'
                    });
                    const firstSheet = workbook.Sheets[workbook.SheetNames[0]];
                    const json = XLSX.utils.sheet_to_json(firstSheet, {
                        defval: ''
                    });

                    if (json.length === 0) {
                        alert('File kosong atau format tidak sesuai');
                        return;
                    }

                    const formData = new FormData();
                    formData.append('_token', '{{ csrf_token() }}');
                    formData.append('data', JSON.stringify(json));

                    // Tampilkan semacam loading spinner jika ada
                    // ...

                    fetch('{{ route('mahasiswa.store') }}', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'Accept': 'application/json', // Pastikan server merespon JSON
                            }
                        })
                        .then(res => {
                            // Sembunyikan loading
                            if (!res.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return res.json();
                        })
                        .then(res => {
                            if (res.success) {
                                alert(res.message || 'Import berhasil');
                                location.reload();
                            } else {
                                alert('Import gagal: ' + (res.message || 'Unknown error'));
                            }
                        })
                        .catch(err => {
                            // Sembunyikan loading
                            console.error(err);
                            alert('Terjadi kesalahan saat import. Cek konsol untuk detail.');
                        });
                };
                reader.readAsArrayBuffer(file);

                // Reset input file agar bisa import file yang sama lagi
                input.value = '';
            }
        </script>
    @endsection

@endsection
