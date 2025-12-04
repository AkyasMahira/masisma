@extends('layouts.app')

@section('title', 'Detail Pelatihan')
@section('page-title', 'Detail Data Pegawai')

@section('content')
    <style>
        :root {
            --custom-maroon: #7c1316;
            --custom-maroon-light: #a3191d;
            --custom-maroon-subtle: #fcf0f1;
        }

        .detail-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            background: #fff;
        }

        .detail-header {
            background-color: var(--custom-maroon);
            color: white;
            padding: 2rem;
            position: relative;
        }

        .user-avatar {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            margin-right: 1.5rem;
            border: 3px solid rgba(255, 255, 255, 0.3);
        }

        .info-label {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #6c757d;
            font-weight: 600;
            margin-bottom: 0.3rem;
        }

        .info-value {
            font-size: 1.1rem;
            color: var(--text-dark);
            font-weight: 500;
            margin-bottom: 1.5rem;
        }

        .section-title {
            color: var(--custom-maroon);
            font-weight: 700;
            border-bottom: 2px solid var(--custom-maroon-subtle);
            padding-bottom: 0.5rem;
            margin-bottom: 1.5rem;
            margin-top: 1rem;
        }

        .list-group-item {
            border: none;
            border-bottom: 1px solid #f0f0f0;
            padding: 1rem 0;
        }
        .list-group-item:last-child {
            border-bottom: none;
        }

        .badge-status {
            font-size: 0.9rem;
            padding: 0.5em 1em;
            border-radius: 50px;
        }

        .btn-back {
            background: #fff;
            color: var(--custom-maroon);
            border: 1px solid var(--custom-maroon);
            transition: 0.3s;
        }
        .btn-back:hover {
            background: var(--custom-maroon);
            color: #fff;
        }

        .pdf-link {
            text-decoration: none;
            color: #d32f2f; /* Merah PDF */
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 0.9rem;
            padding: 5px 10px;
            background: #fff5f5;
            border-radius: 6px;
            transition: 0.2s;
        }
        .pdf-link:hover {
            background: #fee2e2;
            color: #991b1b;
        }

        /* Animation */
        .animate-up {
            animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
            opacity: 0;
            transform: translateY(20px);
        }
        @keyframes fadeInUp {
            to { opacity: 1; transform: translateY(0); }
        }
    </style>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="detail-card animate-up">

                {{-- Header Profile --}}
                <div class="detail-header d-flex align-items-center">
                    <div class="user-avatar">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <div>
                        <h2 class="font-weight-bold mb-1">{{ $pelatihan->nama }}</h2>
                        <p class="mb-0 opacity-75" style="font-size: 1.1rem;">
                            {{ $pelatihan->jabatan ?? 'Tidak ada jabatan' }} | {{ $pelatihan->unit ?? 'Tidak ada unit' }}
                        </p>
                    </div>
                </div>

                <div class="card-body p-4 p-md-5">

                    {{-- Section 1: Data Pegawai --}}
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="info-label"><i class="bi bi-layers-fill me-1"></i> Bidang</div>
                            <div class="info-value">{{ $pelatihan->bidang }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-label"><i class="bi bi-person-badge-fill me-1"></i> Status Kepegawaian</div>
                            <div class="info-value">
                                @if($pelatihan->status_pegawai == 'ASN')
                                    <span class="badge badge-status bg-primary">ASN</span>
                                @elseif($pelatihan->status_pegawai == 'KARYAWAN TETAP')
                                    <span class="badge badge-status bg-warning text-dark">KARYAWAN TETAP</span>
                                @else
                                    <span class="badge badge-status bg-secondary">NON ASN</span>
                                @endif
                            </div>
                        </div>

                        {{-- Logic Tampilan Detail Status --}}
                        @if($pelatihan->status_pegawai == 'ASN')
                            <div class="col-md-4">
                                <div class="info-label">NIP</div>
                                <div class="info-value">{{ $pelatihan->nip }}</div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-label">Golongan</div>
                                <div class="info-value">{{ $pelatihan->golongan }}</div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-label">Pangkat</div>
                                <div class="info-value">{{ $pelatihan->pangkat }}</div>
                            </div>

                        @elseif($pelatihan->status_pegawai == 'KARYAWAN TETAP')
                            <div class="col-md-6">
                                <div class="info-label">NIP</div>
                                <div class="info-value">{{ $pelatihan->nip }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-label">Golongan</div>
                                <div class="info-value">{{ $pelatihan->golongan }}</div>
                            </div>

                        @elseif($pelatihan->status_pegawai == 'NON ASN')
                            <div class="col-md-12">
                                <div class="info-label">NIRP</div>
                                <div class="info-value">{{ $pelatihan->nirp ?? '-' }}</div>
                            </div>
                        @endif
                    </div>

                    {{-- Section 2: Pelatihan Dasar --}}
                    <h5 class="section-title"><i class="bi bi-mortarboard-fill me-2"></i> Pelatihan Dasar</h5>
                    @php
                        $dataDasar = collect($pelatihan->pelatihan_dasar ?? [])->sortByDesc('tahun');
                    @endphp

                    @if($dataDasar->isEmpty())
                        <div class="alert alert-light border text-center text-muted">
                            <i class="bi bi-info-circle me-1"></i> Belum ada data pelatihan dasar.
                        </div>
                    @else
                        <div class="list-group list-group-flush mb-4">
                            @foreach($dataDasar as $item)
                                @php
                                    $nama = is_object($item) ? ($item->nama ?? '-') : ($item['nama'] ?? '-');
                                    $tahun = is_object($item) ? ($item->tahun ?? '-') : ($item['tahun'] ?? '-');
                                    $file = is_object($item) ? ($item->file ?? null) : ($item['file'] ?? null);
                                @endphp
                                <div class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                    <div>
                                        <h6 class="mb-1 font-weight-bold">{{ $nama }}</h6>
                                        <small class="text-muted"><i class="bi bi-calendar-event me-1"></i> Tahun: {{ $tahun }}</small>
                                    </div>
                                    @if($file)
                                        <a href="{{ Storage::url($file) }}" target="_blank" class="pdf-link mt-2 mt-md-0">
                                            <i class="bi bi-file-earmark-pdf-fill"></i> Lihat Sertifikat
                                        </a>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Section 3: Pelatihan Peningkatan Kompetensi --}}
                    <h5 class="section-title"><i class="bi bi-graph-up-arrow me-2"></i> Peningkatan Kompetensi</h5>
                    @php
                        $dataKompetensi = collect($pelatihan->pelatihan_peningkatan_kompetensi ?? [])->sortByDesc('tahun');
                    @endphp

                    @if($dataKompetensi->isEmpty())
                        <div class="alert alert-light border text-center text-muted">
                            <i class="bi bi-info-circle me-1"></i> Belum ada data peningkatan kompetensi.
                        </div>
                    @else
                        <div class="list-group list-group-flush mb-4">
                            @foreach($dataKompetensi as $item)
                                @php
                                    $nama = is_object($item) ? ($item->nama ?? '-') : ($item['nama'] ?? '-');
                                    $tahun = is_object($item) ? ($item->tahun ?? '-') : ($item['tahun'] ?? '-');
                                    $file = is_object($item) ? ($item->file ?? null) : ($item['file'] ?? null);
                                @endphp
                                <div class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                    <div>
                                        <h6 class="mb-1 font-weight-bold">{{ $nama }}</h6>
                                        <small class="text-muted"><i class="bi bi-calendar-event me-1"></i> Tahun: {{ $tahun }}</small>
                                    </div>
                                    @if($file)
                                        <a href="{{ Storage::url($file) }}" target="_blank" class="pdf-link mt-2 mt-md-0">
                                            <i class="bi bi-file-earmark-pdf-fill"></i> Lihat Sertifikat
                                        </a>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Actions --}}
                    <div class="d-flex justify-content-end gap-2 mt-5 border-top pt-3">
                        <a href="{{ route('pelatihan.index') }}" class="btn btn-back rounded-pill px-4">
                            <i class="bi bi-arrow-left me-1"></i> Kembali
                        </a>
                        <a href="{{ route('pelatihan.edit', $pelatihan->id) }}" class="btn btn-warning text-white rounded-pill px-4">
                            <i class="bi bi-pencil-square me-1"></i> Edit Data
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
