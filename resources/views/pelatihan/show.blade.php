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
            padding: 1.2rem 0;
        }

        .badge-status {
            font-size: 0.9rem;
            padding: 0.5em 1em;
            border-radius: 50px;
        }

        .badge-jpl {
            background-color: #e9ecef;
            color: #495057;
            font-weight: bold;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 0.85rem;
        }

        .total-rekap-box {
            background: var(--custom-maroon-subtle);
            border-radius: 12px;
            padding: 1.5rem;
            border: 1px solid var(--custom-maroon);
        }

        .pdf-link {
            text-decoration: none;
            color: #d32f2f;
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

        .btn-back {
            border: 1px solid #dee2e6;
            color: #495057;
            background: #fff;
            font-weight: 500;
            transition: 0.2s;
        }
        .btn-back:hover {
            background: #f1f1f1;
        }

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
                            NIK: {{ $pelatihan->nik ?? '-' }} | {{ $pelatihan->jabatan ?? 'Tidak ada jabatan' }}
                        </p>
                    </div>
                </div>

                <div class="card-body p-4 p-md-5">

                    {{-- Section 1: Data Pegawai --}}
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="info-label"><i class="bi bi-layers-fill me-1"></i> Bidang</div>
                            <div class="info-value">{{ $pelatihan->bidang ?? '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-label"><i class="bi bi-building me-1"></i> Unit</div>
                            <div class="info-value">{{ $pelatihan->unit ?? '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-label"><i class="bi bi-person-badge-fill me-1"></i> Status Pegawai</div>
                            <div class="info-value">
                                <span class="badge badge-status {{ $pelatihan->status_pegawai == 'ASN' ? 'bg-primary' : ($pelatihan->status_pegawai == 'KARYAWAN TETAP' ? 'bg-warning text-dark' : 'bg-secondary') }}">
                                    {{ $pelatihan->status_pegawai ?? '-' }}
                                </span>
                            </div>
                        </div>

                        {{-- NIP/NIRP Logic --}}
                        <div class="col-md-4">
                            @if($pelatihan->status_pegawai == 'NON ASN')
                                <div class="info-label">NIRP</div>
                                <div class="info-value">{{ $pelatihan->nirp ?? '-' }}</div>
                            @else
                                <div class="info-label">NIP</div>
                                <div class="info-value">{{ $pelatihan->nip ?? '-' }}</div>
                            @endif
                        </div>

                        {{-- LMS Akun --}}
                        <div class="col-md-8">
                            <div class="info-label"><i class="bi bi-cloud-check-fill me-1"></i> Akun LMS Kemenkes</div>
                            <div class="info-value">
                                @if($pelatihan->lms_status == 'Ada')
                                    <span class="text-success fw-bold"><i class="bi bi-check-circle-fill"></i> Terdaftar</span>
                                    <span class="text-muted ms-2">({{ $pelatihan->lms_email ?? '-' }})</span>
                                @else
                                    <span class="text-danger fw-bold"><i class="bi bi-x-circle-fill"></i> Tidak Ada</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Section Rekap JPL --}}
                    <div class="total-rekap-box mb-5">
                        <h6 class="fw-bold mb-3" style="color: var(--custom-maroon)"><i class="bi bi-bar-chart-fill me-2"></i>REKAPITULASI JPL TAHUN {{ $currentYear }}</h6>
                        <div class="d-flex align-items-center">
                            <h2 class="mb-0 fw-800 me-3 {{ $totalJpl >= 20 ? 'text-success' : 'text-danger' }}">{{ $totalJpl }}</h2>
                            <div>
                                <div class="fw-bold">Total JPL Terkumpul</div>
                                <small class="text-muted">Target minimal: 20 JPL / Tahun</small>
                            </div>
                            @if($totalJpl >= 20)
                                <div class="ms-auto">
                                    <span class="badge bg-success"><i class="bi bi-patch-check"></i> Memenuhi Syarat</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Section 2: Pelatihan Dasar --}}
                    <h5 class="section-title"><i class="bi bi-mortarboard-fill me-2"></i> Pelatihan Dasar</h5>
                    @php $dataDasar = collect($pelatihan->pelatihan_dasar ?? [])->sortByDesc('tahun'); @endphp

                    @if($dataDasar->isEmpty())
                        <div class="alert alert-light border text-center text-muted mb-4">Belum ada data pelatihan dasar.</div>
                    @else
                        <div class="list-group list-group-flush mb-4">
                            @foreach($dataDasar as $item)
                                <div class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                    <div>
                                        <h6 class="mb-1 font-weight-bold">{{ $item['nama'] ?? '-' }}</h6>
                                        <div class="d-flex gap-3 align-items-center">
                                            <small class="text-muted"><i class="bi bi-calendar-event me-1"></i> {{ $item['tahun'] ?? '-' }}</small>
                                            <span class="badge-jpl">{{ $item['jpl'] ?? 0 }} JPL</span>
                                        </div>
                                    </div>
                                    @if(!empty($item['file']))
                                        <a href="{{ asset('storage/' . $item['file']) }}" target="_blank" class="pdf-link mt-2 mt-md-0">
                                            <i class="bi bi-file-earmark-pdf-fill"></i> Sertifikat
                                        </a>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Section 3: Peningkatan Kompetensi --}}
                    <h5 class="section-title"><i class="bi bi-graph-up-arrow me-2"></i> Peningkatan Kompetensi</h5>
                    @php $dataKomp = collect($pelatihan->pelatihan_peningkatan_kompetensi ?? [])->sortByDesc('tahun'); @endphp

                    @if($dataKomp->isEmpty())
                        <div class="alert alert-light border text-center text-muted mb-4">Belum ada data peningkatan kompetensi.</div>
                    @else
                        <div class="list-group list-group-flush mb-4">
                            @foreach($dataKomp as $item)
                                <div class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                    <div>
                                        <h6 class="mb-1 font-weight-bold">{{ $item['nama'] ?? '-' }}</h6>
                                        <div class="d-flex gap-3 align-items-center">
                                            <small class="text-muted"><i class="bi bi-calendar-event me-1"></i> {{ $item['tahun'] ?? '-' }}</small>
                                            <span class="badge-jpl">{{ $item['jpl'] ?? 0 }} JPL</span>
                                        </div>
                                    </div>
                                    @if(!empty($item['file']))
                                        <a href="{{ asset('storage/' . $item['file']) }}" target="_blank" class="pdf-link mt-2 mt-md-0">
                                            <i class="bi bi-file-earmark-pdf-fill"></i> Sertifikat
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
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection