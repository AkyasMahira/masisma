@extends('layouts.app')

@section('title', 'Ruangan')
@section('page-title', 'Data Ruangan')

@section('content')

    {{-- 1. Animasi background --}}
    <div class="background-animation">
        <div class="circle c1"></div>
        <div class="circle c2"></div>
        <div class="circle c3"></div>
        <div class="circle c4"></div>
        <div class="circle c5"></div>
        <div class="circle c6"></div>
    </div>

    {{-- 2. Wrapper konten utama --}}
    <div style="position: relative; z-index: 2;">

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card shadow-sm">
            {{-- 3. Header utama TETAP berwarna marun kustom --}}
            <div class="card-header bg-custom-maroon text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Manajemen Ruangan</h4>
                    <div>
                        <button onclick="exportToExcel()" class="btn btn-light btn-sm me-2">
                            <i class="fas fa-file-export"></i> Export Excel
                        </button>
                        <label class="btn btn-light btn-sm me-2">
                            <i class="fas fa-file-import"></i> Import Excel
                            <input type="file" id="fileImport" style="display: none" accept=".xlsx,.xls"
                                onchange="importExcel(this)">
                        </label>
                        <button onclick="downloadTemplate()" class="btn btn-light btn-sm me-2">
                            <i class="fas fa-download"></i> Template
                        </button>
                        <a href="{{ route('ruangan.create') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-plus"></i> Tambah Ruangan
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">

                {{-- 4. KEMBALIKAN array warna Bootstrap --}}
                @php
                    $colors = ['primary', 'success', 'info', 'warning', 'danger', 'dark'];
                @endphp

                <div class="row">
                    @forelse ($ruangan as $room)
                        {{-- 5. Ambil warna dinamis berdasarkan index loop --}}
                        @php
                            $color = $colors[$loop->index % count($colors)];
                        @endphp

                        {{-- 6. Card item dengan animasi berurutan --}}
                        <div class="col-lg-4 col-md-6 mb-4 card-animated"
                            style="animation-delay: {{ $loop->index * 0.05 }}s;">
                            <div class="card shadow-sm h-100">

                                {{-- 7. Header card item DIUBAH KEMBALI ke warna dinamis --}}
                                <div class="card-header bg-{{ $color }} text-white">
                                    <h5 class="mb-0">
                                        <i class="fas fa-bed me-2"></i>
                                        <strong>{{ $room->nm_ruangan }}</strong>
                                    </h5>
                                </div>

                                <div class="card-body text-center d-flex flex-column">
                                    <h6 class="card-title text-muted">KUOTA TERSEDIA</h6>

                                    {{-- 8. Angka kuota DIUBAH KEMBALI ke warna dinamis --}}
                                    <p class="card-text display-4 fw-bold text-{{ $color }}">
                                        {{ $room->kuota_ruangan }}
                                    </p>

                                    <div class="mt-auto pt-3">
                                        <hr>
                                        <a href="{{ route('ruangan.edit', $room->id) }}"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>

                                        <form action="{{ route('ruangan.destroy', $room->id) }}" method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus ruangan ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash"></i> Hapus
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                    @empty
                        <div class="col-12">
                            <div class="alert alert-info text-center">
                                Belum ada data ruangan.
                                <a href="{{ route('ruangan.create') }}" class="alert-link">Tambah Data Baru</a>
                            </div>
                        </div>
                    @endforelse
                </div>

            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.sheetjs.com/xlsx-0.20.3/package/dist/xlsx.full.min.js"></script>
    <script>
        function exportToExcel() {
            const ruanganData = @json($ruangan);

            // Prepare the worksheet data
            const ws_data = [
                ['Nama Ruangan', 'Kuota Ruangan'] // Headers
            ];

            // Add data rows
            ruanganData.forEach(room => {
                ws_data.push([room.nm_ruangan, room.kuota_ruangan]);
            });

            // Create worksheet and workbook
            const ws = XLSX.utils.aoa_to_sheet(ws_data);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "Ruangan");

            // Generate Excel file
            XLSX.writeFile(wb, `Data_Ruangan_${new Date().toISOString().split('T')[0]}.xlsx`);
        }

        function downloadTemplate() {
            // Create template data
            const ws_data = [
                ['Nama Ruangan', 'Kuota Ruangan'], // Headers
                ['Ruang A', '30'], // Example data
                ['Ruang B', '25'] // Example data
            ];

            // Create worksheet and workbook
            const ws = XLSX.utils.aoa_to_sheet(ws_data);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "Template_Ruangan");

            // Generate Excel file
            XLSX.writeFile(wb, "Template_Ruangan.xlsx");
        }

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

                // Prepare form data
                const formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('data', JSON.stringify(jsonData));

                // Send to server
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
    </script>

    {{-- 9. CSS kustom TETAP ADA --}}
    <style>
        :root {
            --custom-maroon: #7c1316;
            --custom-maroon-darker: #5f0f11;
            --maroon-rgba: rgba(124, 19, 22, 0.15);
        }

        /* Class untuk warna kustom (tetap dipakai di header utama) */
        .bg-custom-maroon {
            background-color: var(--custom-maroon) !important;
        }

        /* --- ANIMASI CARD FADE-IN --- */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card-animated {
            opacity: 0;
            animation: fadeInUp 0.8s ease-out forwards;
        }

        /* --- ANIMASI BACKGROUND LINGKARAN --- */
        .background-animation {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            overflow: hidden;
            z-index: 1;
        }

        .circle {
            position: absolute;
            display: block;
            list-style: none;
            background: var(--maroon-rgba);
            animation: animate-circles 25s linear infinite;
            bottom: -150px;
        }

        .c1 {
            left: 25%;
            width: 80px;
            height: 80px;
            animation-delay: 0s;
        }

        .c2 {
            left: 10%;
            width: 20px;
            height: 20px;
            animation-delay: 2s;
            animation-duration: 12s;
        }

        .c3 {
            left: 70%;
            width: 20px;
            height: 20px;
            animation-delay: 4s;
        }

        .c4 {
            left: 40%;
            width: 60px;
            height: 60px;
            animation-delay: 0s;
            animation-duration: 18s;
        }

        .c5 {
            left: 65%;
            width: 20px;
            height: 20px;
            animation-delay: 0s;
        }

        .c6 {
            left: 85%;
            width: 110px;
            height: 110px;
            animation-delay: 3s;
        }

        @keyframes animate-circles {
            0% {
                transform: translateY(0);
                opacity: 1;
            }

            100% {
                transform: translateY(-1000px);
                opacity: 0;
            }
        }
    </style>
@endsection
