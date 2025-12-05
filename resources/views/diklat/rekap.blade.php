@extends('layouts.app')

@section('content')
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <style>
        /* ... Style Tetap Sama ... */
        :root {
            --primary-color: #7c1316;
            --primary-light: #fcebeb;
            --text-dark: #32325d;
            --text-muted: #8898aa;
            --border-color: #e9ecef;
        }

        body {
            background-color: #f8f9fc;
            font-family: 'Poppins', sans-serif;
            color: var(--text-dark);
        }

        .table-responsive::-webkit-scrollbar {
            height: 8px;
        }

        .table-responsive::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        .table-responsive::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }

        .table-responsive::-webkit-scrollbar-thumb:hover {
            background: var(--primary-color);
        }

        .sticky-col {
            position: -webkit-sticky;
            position: sticky;
            background-color: white;
            z-index: 2;
        }

        .first-col {
            left: 0;
            width: 50px;
            border-right: 1px solid #eee;
        }

        .second-col {
            left: 50px;
            width: 280px;
            box-shadow: 2px 0 5px -2px rgba(0, 0, 0, 0.1);
        }

        .table-ultra thead th.sticky-col {
            background-color: #f6f9fc;
            z-index: 3;
        }

        .truncate-cell {
            max-width: 200px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            cursor: help;
        }

        .rekap-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .page-title {
            font-size: 24px;
            font-weight: 700;
            color: var(--primary-color);
            margin: 0;
        }

        .card-ultra {
            background: white;
            border: none;
            border-radius: 15px;
            box-shadow: 0 0 2rem 0 rgba(136, 152, 170, .15);
            overflow: hidden;
            border-top: 4px solid var(--primary-color);
        }

        .table-ultra {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .table-ultra thead th {
            background-color: #f6f9fc;
            color: var(--text-muted);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 11px;
            padding: 15px;
            border-bottom: 1px solid var(--border-color);
            white-space: nowrap;
        }

        .table-ultra tbody td {
            padding: 12px 15px;
            vertical-align: middle;
            font-size: 13px;
            border-bottom: 1px solid var(--border-color);
            background: white;
        }

        .avatar-initial {
            width: 38px;
            height: 38px;
            background: var(--primary-light);
            color: var(--primary-color);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 14px;
            margin-right: 15px;
            flex-shrink: 0;
        }

        .badge-soft {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            display: inline-block;
            margin: 1px 0;
        }

        .badge-primary {
            background: #e3f2fd;
            color: #1976d2;
        }

        .badge-info {
            background: #e0f2f1;
            color: #00796b;
        }

        .btn-ultra-print {
            background: linear-gradient(135deg, #2dce89 0%, #2dcecc 100%);
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 13px;
        }

        @media screen and (max-width: 992px) {
            .sticky-col {
                position: static !important;
                box-shadow: none !important;
                border: none !important;
            }

            .table-responsive {
                overflow-x: hidden;
            }

            .table-ultra,
            thead,
            tbody,
            th,
            td,
            tr {
                display: block;
            }

            thead tr {
                position: absolute;
                top: -9999px;
                left: -9999px;
            }

            tbody tr {
                margin-bottom: 15px;
                border: 1px solid #eee;
                border-radius: 12px;
                padding: 15px;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            }

            td {
                padding: 5px 0;
                border: none;
                display: flex;
                justify-content: space-between;
                text-align: right;
                align-items: center;
            }

            td:before {
                content: attr(data-label);
                font-weight: 700;
                color: #8898aa;
                font-size: 11px;
                text-transform: uppercase;
                margin-right: 10px;
                text-align: left;
                width: 40%;
            }

            .truncate-cell {
                max-width: none;
                white-space: normal;
            }
        }
    </style>

    <div class="container-fluid py-4">

        <div class="rekap-header animate__animated animate__fadeInDown">
            <div>
                <h1 class="page-title">Rekap Peserta</h1>
                <p class="small text-muted mb-0">Total Pendaftar: <strong>{{ $pesertas->count() }}</strong> Orang</p>
            </div>
            <div>
                <a href="{{ route('diklat.index') }}" class="btn btn-light border btn-sm mr-2">Kembali</a>
                <button onclick="window.print()" class="btn-ultra-print">
                    <i class="fas fa-print mr-1"></i> Cetak PDF
                </button>
            </div>
        </div>

        <div class="card-ultra animate__animated animate__fadeInUp">
            <div class="table-responsive">
                <table class="table-ultra">
                    <thead>
                        <tr>
                            <th class="sticky-col first-col text-center">No</th>
                            <th class="sticky-col second-col">Nama Peserta</th>
                            <th>Kontak & NIK</th>
                            <th>Instansi & Jabatan</th>
                            <th>Pilihan Pelatihan</th>
                            <th>Pilihan Tempat</th>
                            <th class="text-center">Kaos</th>
                            <th class="text-center">Bukti Bayar</th>
                            <th>Alamat</th>

                            {{-- LOGIKA HEADER: Reset Key menjadi 0, 1, 2... --}}
                            @php
                                $customQuestions = [];
                                if (isset($form) && !empty($form->pertanyaan_custom)) {
                                    $rawQ = is_array($form->pertanyaan_custom)
                                        ? $form->pertanyaan_custom
                                        : json_decode($form->pertanyaan_custom, true);
                                    // array_values() mereset kunci jadi angka urut (0, 1, 2)
                                    $customQuestions = !empty($rawQ) ? array_values($rawQ) : [];
                                }
                            @endphp

                            @foreach ($customQuestions as $q)
                                <th class="text-center" style="background-color: #eef2f7; min-width: 150px;">
                                    {{ $q['judul'] ?? 'Pertanyaan' }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pesertas as $key => $peserta)
                            <tr>
                                <td data-label="No" class="text-center font-weight-bold text-muted sticky-col first-col">
                                    {{ $key + 1 }}
                                </td>

                                <td data-label="Peserta" class="sticky-col second-col">
                                    <div class="d-flex align-items-center">
                                        @if ($peserta->pas_foto)
                                            <a href="{{ asset('storage/' . $peserta->pas_foto) }}" target="_blank"
                                                class="mr-3" title="Klik untuk Zoom">
                                                <img src="{{ asset('storage/' . $peserta->pas_foto) }}" class="shadow-sm"
                                                    style="width: 38px; height: 50px; object-fit: cover; border-radius: 4px; border: 1px solid #dee2e6; background: #fff;">
                                            </a>
                                        @else
                                            @php $initial = strtoupper(substr($peserta->nama_lengkap, 0, 1)); @endphp
                                            <div class="avatar-initial">{{ $initial }}</div>
                                        @endif
                                        <div>
                                            <div class="font-weight-bold text-truncate" style="max-width: 170px;">
                                                {{ $peserta->nama_lengkap }}</div>
                                            <div class="small text-muted">{{ $peserta->gelar }}</div>
                                        </div>
                                    </div>
                                </td>

                                <td data-label="Kontak">
                                    <div class="small"><i class="fas fa-phone-alt text-success mr-1"></i>
                                        {{ $peserta->no_hp }}</div>
                                    <div class="small text-muted">{{ $peserta->nik }}</div>
                                </td>

                                <td data-label="Pekerjaan">
                                    <div class="font-weight-bold text-truncate" style="max-width: 150px;">
                                        {{ $peserta->instansi }}</div>
                                    <div class="small text-muted text-truncate" style="max-width: 150px;">
                                        {{ $peserta->jabatan }}</div>
                                </td>

                                <td data-label="Pelatihan">
                                    @foreach ((array) (is_array($peserta->pilihan_pelatihan) ? $peserta->pilihan_pelatihan : json_decode($peserta->pilihan_pelatihan, true)) as $p)
                                        <span class="badge-soft badge-primary">{{ $p }}</span><br>
                                    @endforeach
                                </td>

                                <td data-label="Tempat">
                                    @foreach ((array) (is_array($peserta->pilihan_tempat) ? $peserta->pilihan_tempat : json_decode($peserta->pilihan_tempat, true)) as $t)
                                        <span class="badge-soft badge-info">{{ $t }}</span><br>
                                    @endforeach
                                </td>

                                <td data-label="Kaos" class="text-center"><strong>{{ $peserta->ukuran_kaos }}</strong></td>

                                <td data-label="Bukti Bayar" class="text-center">
                                    @if ($peserta->bukti_pembayaran)
                                        <a href="{{ asset('storage/' . $peserta->bukti_pembayaran) }}" target="_blank"
                                            class="text-primary"><i class="fas fa-file-invoice-dollar fa-2x"></i></a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>

                                <td data-label="Alamat">
                                    <div class="truncate-cell" title="{{ $peserta->alamat }}">
                                        {{ Str::limit($peserta->alamat, 20) }}</div>
                                </td>

                                {{-- ========================================================= --}}
                                {{-- LOGIKA JAWABAN CUSTOM YANG DIPERBAIKI (RESET KEY) --}}
                                {{-- ========================================================= --}}
                                @php
                                    // 1. Ambil data mentah
                                    $rawJawaban = $peserta->jawaban_custom;

                                    // 2. Pastikan bentuknya array (jika null atau string)
                                    if (!is_array($rawJawaban)) {
                                        $rawJawaban = json_decode($rawJawaban, true);
                                    }

                                    // 3. KUNCI UTAMA: array_values() untuk reset index agar cocok dengan header (0, 1, 2...)
                                    // Ini memastikan urutan jawaban cocok dengan urutan kolom header
                                    $fixedJawaban = !empty($rawJawaban) ? array_values($rawJawaban) : [];
                                @endphp

                                @foreach ($customQuestions as $index => $q)
                                    <td data-label="{{ $q['judul'] ?? 'Tanya' }}">
                                        {{-- Cek berdasarkan INDEX ANGKA (0, 1, 2) --}}
                                        @if (isset($fixedJawaban[$index]))
                                            @php $ans = $fixedJawaban[$index]; @endphp

                                            @if (is_array($ans))
                                                {{-- Jika jawaban checkbox --}}
                                                @foreach ($ans as $a)
                                                    <span class="badge-soft badge-info mb-1">{{ $a }}</span><br>
                                                @endforeach
                                            @else
                                                {{-- Jika jawaban text --}}
                                                {{ $ans }}
                                            @endif
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                @endforeach
                                {{-- ========================================================= --}}

                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ 9 + count($customQuestions) }}" class="text-center py-5">
                                    <h6 class="text-muted">Belum ada data pendaftar.</h6>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <style media="print">
        @page {
            size: landscape;
            margin: 5mm;
        }

        body {
            background: white;
            -webkit-print-color-adjust: exact;
        }

        .btn,
        .rekap-header a,
        .rekap-header button {
            display: none;
        }

        .table-responsive {
            overflow: visible !important;
        }

        .truncate-cell {
            white-space: normal;
            max-width: none;
            overflow: visible;
        }

        .sticky-col {
            position: static !important;
            box-shadow: none !important;
            border: 1px solid #ddd !important;
        }

        .table-ultra tbody td {
            background-color: white !important;
        }

        img {
            max-width: 100% !important;
        }
    </style>
@endsection
