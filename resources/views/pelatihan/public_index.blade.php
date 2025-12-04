

@extends('layouts.public')

@section('title', 'Cek Data Pelatihan')

@section('content')
    <style>
        :root { --custom-maroon: #7c1316; --card-radius: 16px; }

        /* Search Box Style */
        .search-card {
            background: #fff; border-radius: var(--card-radius);
            border-top: 5px solid var(--custom-maroon);
            box-shadow: 0 10px 30px rgba(0,0,0,0.08); padding: 3rem 2rem;
            text-align: center; margin-bottom: 2rem;
        }

        /* Buttons */
        .btn-maroon {
            background: var(--custom-maroon); color: white; border-radius: 50px;
            padding: 0.7rem 2rem; font-weight: 600; border: none; transition: 0.3s;
        }
        .btn-maroon:hover { background: #a3191d; color: white; transform: translateY(-2px); }
        .btn-outline-maroon {
            border: 2px solid var(--custom-maroon); color: var(--custom-maroon);
            border-radius: 50px; padding: 0.7rem 2rem; font-weight: 600; background: transparent;
        }
        .btn-outline-maroon:hover { background: var(--custom-maroon); color: white; }

        /* Table Styles */
        .custom-table-card { background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .table thead th { background: var(--custom-maroon); color: white; border: none; padding: 1rem; vertical-align: middle; }
        .table tbody td { vertical-align: middle; padding: 1rem; }

        /* List Item Style (Untuk File PDF) */
        .pelatihan-list-item {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 6px;
            padding: 0.5rem;
            font-size: 0.85rem;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            transition: 0.2s;
        }
        .pelatihan-list-item:hover {
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            border-color: #ddd;
        }
        .pelatihan-list-item .nama { flex-grow: 1; font-weight: 500; color: #333; padding-right: 10px; }
        .pelatihan-list-item .tahun {
            font-weight: 700; color: var(--custom-maroon);
            background: white; padding: 2px 8px; border-radius: 4px;
            font-size: 0.75rem; border: 1px solid #eee;
        }

        /* Icon PDF */
        .pdf-link {
            color: #dc3545; /* Warna Merah PDF */
            margin-right: 10px;
            font-size: 1.3rem;
            display: flex; align-items: center;
        }
        .pdf-link:hover { color: #a71d2a; transform: scale(1.1); }
        .no-file-icon { color: #ccc; margin-right: 10px; font-size: 1.3rem; }

        .category-badge {
            font-size: 0.7rem; font-weight: bold; text-transform: uppercase;
            color: #999; margin-bottom: 4px; display: block; letter-spacing: 0.5px;
        }
    </style>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">

                {{-- SEARCH BOX --}}
                <div class="search-card">
                    <h2 style="color: var(--custom-maroon); font-weight: 800; margin-bottom: 1rem;">
                        <i class="fas fa-search"></i> Cek Data Pelatihan
                    </h2>
                    <p class="text-muted mb-4">Masukkan NIP atau NIRP untuk melihat riwayat pelatihan & file PDF Anda.</p>

                    <form action="{{ route('public.pelatihan.index') }}" method="GET">
                        <div class="input-group mb-4 shadow-sm" style="max-width: 600px; margin: 0 auto;">
                            <span class="input-group-text bg-white"><i class="fas fa-id-badge"></i></span>
                            <input type="text" class="form-control border-start-0" name="keyword"
                                placeholder="Masukkan NIP / NIRP..." value="{{ $keyword ?? '' }}" required>
                            <button class="btn btn-maroon" type="submit">Cari Data</button>
                        </div>
                    </form>

                    <div class="mt-4 pt-3 border-top">
                        <p class="mb-2 text-muted">Butuh memperbarui data pelatihan? Gunakan tombol Perbarui pada hasil pencarian.</p>
                    </div>
                </div>

                {{-- HASIL PENCARIAN --}}
                @if ($searchPerformed)
                    @if (session('success'))
                        <div class="alert alert-success text-center mb-4">
                            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                        </div>
                    @endif

                    @if ($pelatihans->count() > 0)
                        <div class="custom-table-card animate-up">
                            <div class="p-3 bg-light border-bottom d-flex justify-content-between align-items-center">
                                <strong class="text-dark"><i class="fas fa-list"></i> Hasil Pencarian</strong>
                                <span class="badge bg-success">{{ $pelatihans->count() }} Data Ditemukan</span>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th width="25%">Info Pegawai</th>
                                            <th width="15%">Unit & Jabatan</th>
                                            <th width="60%">Riwayat Pelatihan & File</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($pelatihans as $pelatihan)
                                            <tr>
                                                {{-- KOLOM 1: INFO PEGAWAI --}}
                                                <td class="align-top">
                                                    <div class="fw-bold text-dark" style="font-size: 1.05rem;">{{ $pelatihan->nama }}</div>
                                                    <div class="mt-2">
                                                        @if ($pelatihan->status_pegawai === 'ASN')
                                                            <span class="badge bg-primary">ASN</span>
                                                            <div class="small text-muted mt-1">NIP: {{ $pelatihan->nip }}</div>
                                                            <div class="small text-muted">{{ $pelatihan->golongan }} / {{ $pelatihan->pangkat }}</div>
                                                        @elseif ($pelatihan->status_pegawai === 'KARYAWAN TETAP')
                                                            <span class="badge bg-warning text-dark">KARYAWAN TETAP</span>
                                                            <div class="small text-muted mt-1">NIP: {{ $pelatihan->nip }}</div>
                                                            <div class="small text-muted">{{ $pelatihan->golongan }}</div>
                                                        @else
                                                            <span class="badge bg-secondary">NON ASN</span>
                                                            <div class="small text-muted mt-1">NIRP: {{ $pelatihan->nirp }}</div>
                                                        @endif
                                                    </div>
                                                    <div class="mt-3">
                                                        <a href="{{ route('public.pelatihan.edit', $pelatihan->id) }}" class="btn btn-outline-maroon btn-sm">
                                                            <i class="fas fa-edit me-1"></i> Perbarui Pelatihan
                                                        </a>
                                                    </div>
                                                </td>

                                                {{-- KOLOM 2: UNIT & JABATAN --}}
                                                <td class="align-top">
                                                    <div class="mb-2">
                                                        <small class="text-muted d-block">Unit/Ruang:</small>
                                                        <span class="fw-bold text-dark">{{ $pelatihan->unit }}</span>
                                                    </div>
                                                    <div>
                                                        <small class="text-muted d-block">Jabatan:</small>
                                                        <span>{{ $pelatihan->jabatan }}</span>
                                                    </div>
                                                    <div class="mt-2">
                                                        <small class="text-muted d-block">Bidang:</small>
                                                        <span>{{ $pelatihan->bidang }}</span>
                                                    </div>
                                                </td>

                                                {{-- KOLOM 3: RIWAYAT PELATIHAN (DENGAN PDF) --}}
                                                <td class="align-top">
                                                    {{-- Siapkan Data Array --}}
                                                    @php
                                                        $dasar = collect($pelatihan->pelatihan_dasar ?? [])->sortByDesc('tahun');
                                                        $kompetensi = collect($pelatihan->pelatihan_peningkatan_kompetensi ?? [])->sortByDesc('tahun');
                                                    @endphp

                                                    {{-- Bagian Pelatihan Dasar --}}
                                                    @if($dasar->count() > 0)
                                                        <span class="category-badge">Pelatihan Dasar</span>
                                                        @foreach ($dasar as $item)
                                                            @if(isset($item['nama']))
                                                                <div class="pelatihan-list-item">
                                                                    {{-- LOGIKA TAMPILKAN PDF --}}
                                                                    @if(!empty($item['file']))
                                                                        <a href="{{ Storage::url($item['file']) }}" target="_blank" class="pdf-link" title="Klik untuk lihat PDF">
                                                                            <i class="fas fa-file-pdf"></i>
                                                                        </a>
                                                                    @else
                                                                        <i class="fas fa-file no-file-icon" title="Tidak ada file"></i>
                                                                    @endif

                                                                    <span class="nama">{{ $item['nama'] }}</span>
                                                                    <span class="tahun">{{ $item['tahun'] ?? '-' }}</span>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                        <div class="mb-3"></div> {{-- Spacer --}}
                                                    @endif

                                                    {{-- Bagian Pelatihan Kompetensi --}}
                                                    @if($kompetensi->count() > 0)
                                                        <span class="category-badge">Peningkatan Kompetensi</span>
                                                        @foreach ($kompetensi as $item)
                                                            @if(isset($item['nama']))
                                                                <div class="pelatihan-list-item">
                                                                    {{-- LOGIKA TAMPILKAN PDF --}}
                                                                    @if(!empty($item['file']))
                                                                        <a href="{{ Storage::url($item['file']) }}" target="_blank" class="pdf-link" title="Klik untuk lihat PDF">
                                                                            <i class="fas fa-file-pdf"></i>
                                                                        </a>
                                                                    @else
                                                                        <i class="fas fa-file no-file-icon" title="Tidak ada file"></i>
                                                                    @endif

                                                                    <span class="nama">{{ $item['nama'] }}</span>
                                                                    <span class="tahun">{{ $item['tahun'] ?? '-' }}</span>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    @endif

                                                    @if($dasar->count() == 0 && $kompetensi->count() == 0)
                                                        <div class="text-center text-muted py-3">
                                                            <i class="fas fa-folder-open mb-1"></i><br>
                                                            <small>Belum ada data pelatihan.</small>
                                                        </div>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-warning text-center">
                            <i class="fas fa-exclamation-triangle fa-lg mb-2"></i><br>
                            Data tidak ditemukan. Periksa kembali NIP / NIRP yang dimasukkan.
                        </div>
                    @endif
                @endif

            </div>
        </div>
    </div>
@endsection
