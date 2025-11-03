@extends('layouts.app')

@section('title', 'Ruangan')
@section('page-title', 'Data Ruangan')

@section('content')

    <style>
        :root {
            --custom-maroon: #7c1316;
            --custom-maroon-dark: #5f0f11;
            --maroon-rgba: rgba(124, 19, 22, 0.15);
            --transition-speed: 0.3s;
        }

        /* --- BUTTON STYLING --- */
        .btn-maroon {
            background-color: var(--custom-maroon);
            color: #fff;
            border-color: var(--custom-maroon);
            transition: all var(--transition-speed);
        }

        .btn-maroon:hover {
            background-color: var(--custom-maroon-dark);
            border-color: var(--custom-maroon-dark);
            color: #fff;
        }

        .btn-outline-maroon {
            color: var(--custom-maroon);
            border-color: var(--custom-maroon);
            transition: all var(--transition-speed);
        }

        .btn-outline-maroon:hover {
            background-color: var(--custom-maroon);
            color: #fff;
        }

        /* --- CARD ANIMASI & INTERAKSI --- */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card-animated {
            opacity: 0;
            animation: fadeInUp 0.6s ease-out forwards;
        }

        .room-card {
            border: 1px solid #e9ecef;
            border-left: 4px solid var(--maroon-rgba);
            transition: all var(--transition-speed);
            cursor: pointer;
            background-color: #fff;
            border-radius: 12px;
            overflow: hidden;
        }

        .room-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 1rem 1.5rem rgba(0, 0, 0, 0.12);
            border-left-color: var(--custom-maroon);
        }

        .room-card .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
            font-weight: 600;
            color: var(--custom-maroon-dark);
        }

        .room-card .card-body {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .room-card .card-text.display-6 {
            font-size: 2rem;
        }

        /* --- EMPTY STATE --- */
        .border-dashed {
            border: 2px dashed #ddd;
            border-radius: 12px;
            padding: 40px 20px;
            background-color: #fdfdfd;
            transition: all var(--transition-speed);
        }

        .border-dashed:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.08);
        }

        /* --- MODAL STYLING --- */
        #listMahasiswa .list-group-item {
            border-radius: 8px;
            margin-bottom: 8px;
            border: 1px solid #eee;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            transition: all var(--transition-speed);
        }

        #listMahasiswa .list-group-item strong {
            color: var(--custom-maroon);
        }

        /* --- PAGINATION --- */
        .pagination .page-link {
            border-radius: 8px;
            transition: all var(--transition-speed);
        }

        .pagination .page-item.active .page-link {
            background-color: var(--custom-maroon);
            border-color: var(--custom-maroon);
            color: #fff;
        }
    </style>

    <div>
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card shadow-sm">
            <div class="card-header bg-custom-maroon text-white d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Manajemen Ruangan</h4>
                <div class="d-flex align-items-center gap-2">
                    <div class="btn-group" role="group">
                        <button onclick="exportToExcel()" class="btn btn-light btn-sm"><i class="fas fa-file-export"></i>
                            Export</button>
                        <label class="btn btn-light btn-sm mb-0"> <i class="fas fa-file-import"></i> Import
                            <input type="file" id="fileImport" style="display: none" accept=".xlsx,.xls"
                                onchange="importExcel(this)">
                        </label>
                        <button onclick="downloadTemplate()" class="btn btn-light btn-sm"><i class="fas fa-download"></i>
                            Template</button>
                    </div>
                    <a href="{{ route('ruangan.create') }}" class="btn btn-maroon btn-sm"><i class="fas fa-plus"></i> Tambah
                        Ruangan</a>
                </div>
            </div>

            <div class="card-body">
                <form method="GET" class="mb-4" style="max-width: 400px;">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Cari nama ruangan..."
                            value="{{ request('search') }}">
                        <button type="submit" class="btn btn-outline-maroon"><i class="fas fa-search"></i> Cari</button>
                    </div>
                </form>

                <div class="row">
                    @forelse ($ruangan as $room)
                        @php
                            $terisi = $room->mahasiswa_count;
                            $total = $room->kuota_ruangan;
                            $tersedia = max($total - $terisi, 0);
                            $persentase = $total > 0 ? $tersedia / $total : 0;
                            $kuotaColor =
                                $persentase == 0
                                    ? 'text-danger'
                                    : ($persentase <= 0.25
                                        ? 'text-warning'
                                        : 'text-success');
                        @endphp

                        <div class="col-lg-4 col-md-6 mb-4 card-animated"
                            style="animation-delay: {{ $loop->index * 0.05 }}s;">
                            <div class="card shadow-sm h-100 room-card" style="cursor:pointer;"
                                data-nama="{{ $room->nm_ruangan }}" data-mahasiswa='@json($room->mahasiswa)'>
                                <div class="card-header">{{ $room->nm_ruangan }}</div>
                                <div class="card-body text-center">
                                    <h6 class="text-muted">KUOTA TERSEDIA</h6>
                                    <p class="card-text display-6 fw-bold {{ $kuotaColor }}">
                                        {{ $tersedia }}/{{ $room->kuota_ruangan }}</p>
                                    <small class="text-muted">tersedia dari total kuota</small>
                                    <div class="mt-3">
                                        <hr>
                                        <a href="{{ route('ruangan.edit', $room->id) }}"
                                            class="btn btn-sm btn-outline-primary me-1" onclick="event.stopPropagation()"><i
                                                class="bi bi-pencil-square"></i></a>
                                        <form action="{{ route('ruangan.destroy', $room->id) }}" method="POST"
                                            class="d-inline"
                                            onsubmit="event.stopPropagation(); return confirm('Apakah Anda yakin ingin menghapus ruangan ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger"><i
                                                    class="bi bi-trash"></i></button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="alert alert-light text-center p-5 border-dashed">
                                <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Belum Ada Data Ruangan</h5>
                                <p class="text-muted mb-3">Mulai tambahkan ruangan pertama Anda ke sistem.</p>
                                <a href="{{ route('ruangan.create') }}" class="btn btn-maroon"><i class="fas fa-plus"></i>
                                    Tambah Data Baru</a>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-center mt-3">
            {{ $ruangan->links('pagination.custom') }}
        </div>

        <!-- Modal Mahasiswa -->
        <div class="modal fade" id="modalMahasiswa" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-custom-maroon text-white">
                        <h5 class="modal-title">Mahasiswa di Ruangan</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <ul id="listMahasiswa" class="list-group list-group-flush"></ul>
                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection


@section('scripts')
    <script src="https://cdn.sheetjs.com/xlsx-0.20.3/package/dist/xlsx.full.min.js"></script>
    <script>
        // Export Ruangan ke Excel
        function exportToExcel() {
            const ruanganData = @json($ruangan);
            const ws_data = [
                ['Nama Ruangan', 'Kuota Ruangan']
            ];

            ruanganData.forEach(room => {
                ws_data.push([room.nm_ruangan, room.kuota_ruangan]);
            });

            const ws = XLSX.utils.aoa_to_sheet(ws_data);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "Ruangan");
            XLSX.writeFile(wb, `Data_Ruangan_${new Date().toISOString().split('T')[0]}.xlsx`);
        }

        // Download Template
        function downloadTemplate() {
            const ws_data = [
                ['Nama Ruangan', 'Kuota Ruangan'],
                ['Ruang A', '30'],
                ['Ruang B', '25']
            ];

            const ws = XLSX.utils.aoa_to_sheet(ws_data);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "Template_Ruangan");
            XLSX.writeFile(wb, "Template_Ruangan.xlsx");
        }

        // Import Excel
        function importExcel(input) {
            const file = input.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function(e) {
                const data = new Uint8Array(e.target.result);
                const workbook = XLSX.read(data, {
                    type: 'array'
                });
                const firstSheet = workbook.Sheets[workbook.SheetNames[0]];
                const jsonData = XLSX.utils.sheet_to_json(firstSheet);

                if (jsonData.length === 0) {
                    alert('File Excel kosong atau format tidak sesuai!');
                    return;
                }

                const formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('data', JSON.stringify(jsonData));

                fetch('{{ route('ruangan.store') }}', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Data berhasil diimport!');
                            location.reload();
                        } else {
                            alert('Gagal import data: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan saat import data');
                    });
            };

            reader.readAsArrayBuffer(file);
        }

        // Klik Card → Tampilkan Modal Mahasiswa
        document.addEventListener('DOMContentLoaded', () => {
            const cards = document.querySelectorAll('.room-card');
            const modalElement = document.getElementById('modalMahasiswa');
            const modal = new bootstrap.Modal(modalElement);
            const list = document.getElementById('listMahasiswa');

            cards.forEach(card => {
                card.addEventListener('click', () => {
                    const namaRuangan = card.dataset.nama;
                    const mahasiswa = JSON.parse(card.dataset.mahasiswa);

                    modalElement.querySelector('.modal-title').innerText = "Mahasiswa di " +
                        namaRuangan;
                    list.innerHTML = "";

                    if (!mahasiswa || mahasiswa.length === 0) {
                        list.innerHTML =
                            `<li class="list-group-item text-muted">Belum ada mahasiswa di ruangan ini.</li>`;
                    } else {
                        mahasiswa.forEach(m => {
                            list.innerHTML += `
                                <li class="list-group-item">
                                    <strong>${m.nm_mahasiswa}</strong><br>
                                    <small>${m.prodi ?? '-'} - ${m.univ_asal ?? '-'}</small>
                                </li>
                            `;
                        });
                    }

                    modal.show();
                });
            });
        });
    </script>
@endsection
