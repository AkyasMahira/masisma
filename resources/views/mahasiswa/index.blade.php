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
            transform: translateY(-1px);
        }

        .table {
            border-radius: 12px;
            overflow: hidden;
        }

        thead {
            background-color: var(--maroon);
            color: #fff;
        }

        th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }

        td {
            vertical-align: middle !important;
        }

        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .alert {
            border-radius: 10px;
            font-weight: 500;
        }

        .btn-sm {
            border-radius: 8px;
        }

        @media (max-width: 768px) {

            th,
            td {
                font-size: 0.85rem;
            }
        }
    </style>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold text-maroon" style="color: var(--maroon);">Daftar Mahasiswa</h4>
        <div class="btn-group">
            <button class="btn btn-light btn-sm me-2" onclick="exportMahasiswa()">
                <i class="bi bi-file-earmark-arrow-down"></i> Export Excel
            </button>

            <label class="btn btn-light btn-sm me-2">
                <i class="bi bi-upload"></i> Import Excel
                <input type="file" id="fileImportMahasiswa" style="display:none" accept=".xlsx,.xls"
                    onchange="importMahasiswa(this)">
            </label>

            <button class="btn btn-light btn-sm me-2" onclick="downloadTemplateMahasiswa()">
                <i class="bi bi-download"></i> Template
            </button>

            <a href="{{ route('mahasiswa.create') }}" class="btn-maroon">
                <i class="bi bi-person-plus"></i> Tambah Mahasiswa
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr class="text-center">
                            <th>#</th>
                            <th>Nama</th>
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
                                <td colspan="7" class="text-center text-muted py-3">Belum ada data mahasiswa.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @section('scripts')
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

                    fetch('{{ route('mahasiswa.store') }}', {
                            method: 'POST',
                            body: formData
                        })
                        .then(res => res.json())
                        .then(res => {
                            if (res.success) {
                                alert(res.message || 'Import berhasil');
                                location.reload();
                            } else {
                                alert('Import gagal: ' + (res.message || 'Unknown'));
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            alert('Terjadi kesalahan saat import');
                        });
                };
                reader.readAsArrayBuffer(file);
            }
        </script>
    @endsection

@endsection
