@extends('layouts.app')

@section('title', $pelatihan->nama)
@section('page-title', $pelatihan->nama)

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

        .header-section {
            background: linear-gradient(135deg, var(--custom-maroon), var(--custom-maroon-light));
            color: white;
            border-radius: var(--card-radius);
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-soft);
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: start;
            gap: 2rem;
            flex-wrap: wrap;
        }

        .header-title-group h1 {
            font-size: 2rem;
            font-weight: 700;
            margin: 0 0 0.5rem 0;
        }

        .header-subtitle {
            font-size: 0.95rem;
            opacity: 0.9;
        }

        .header-actions {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .btn-custom {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            border: none;
            cursor: pointer;
            font-size: 0.9rem;
        }

        .btn-edit {
            background-color: #e3f2fd;
            color: #1565c0;
        }

        .btn-edit:hover {
            background-color: #bbdefb;
        }

        .btn-back {
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .btn-back:hover {
            background-color: rgba(255, 255, 255, 0.3);
        }

        .detail-card {
            background: #fff;
            border-radius: var(--card-radius);
            box-shadow: var(--shadow-soft);
            margin-bottom: 2rem;
            overflow: hidden;
        }

        .detail-card-header {
            background-color: #f8f9fa;
            border-bottom: 2px solid var(--custom-maroon);
            padding: 1.5rem;
        }

        .detail-card-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--custom-maroon);
            margin: 0;
        }

        .detail-card-body {
            padding: 2rem;
        }

        .detail-row {
            display: grid;
            grid-template-columns: 200px 1fr;
            gap: 2rem;
            margin-bottom: 1.5rem;
            align-items: start;
        }

        @media (max-width: 768px) {
            .detail-row {
                grid-template-columns: 1fr;
                gap: 0.5rem;
            }
        }

        .detail-row:last-child {
            margin-bottom: 0;
        }

        .detail-label {
            font-weight: 600;
            color: var(--text-dark);
            font-size: 0.9rem;
        }

        .detail-value {
            color: var(--text-dark);
            font-size: 0.95rem;
            line-height: 1.5;
        }

        .detail-value.empty {
            color: var(--text-muted);
            font-style: italic;
        }

        .pelatihan-list {
            display: flex;
            flex-direction: column; /* Ubah ke kolom agar lebih rapi */
            gap: 0.75rem; /* Beri jarak antar item */
        }

        .pelatihan-badge {
            background-color: #e3f2fd;
            color: #1565c0;
            padding: 0.6rem 1rem;
            border-radius: 8px;
            font-size: 0.9rem;
            display: flex; /* Gunakan flexbox */
            justify-content: space-between; /* Nama di kiri, tahun di kanan */
            align-items: center;
        }

        /* Style untuk tahun di dalam badge */
        .pelatihan-badge-tahun {
            font-weight: 700;
            background: white;
            color: var(--custom-maroon);
            padding: 0.2rem 0.5rem;
            border-radius: 6px;
            font-size: 0.85rem;
        }

        .divider {
            border-bottom: 1px solid #e9ecef;
            margin: 1.5rem 0;
        }
    </style>

    <div class="header-section">
        <div class="header-content">
            <div class="header-title-group">
                <h1>
                    <i class="fas fa-graduation-cap"></i>
                    {{ $pelatihan->nama }}
                </h1>
                <p class="header-subtitle">Detail Data Pelatihan Dasar</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('pelatihan.edit', $pelatihan->id) }}" class="btn-custom btn-edit">
                    <i class="fas fa-edit"></i>
                    Edit
                </a>
                <a href="{{ route('pelatihan.index') }}" class="btn-custom btn-back">
                    <i class="fas fa-arrow-left"></i>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="detail-card">
        <div class="detail-card-header">
            <h2 class="detail-card-title">
                <i class="fas fa-user"></i>
                Informasi Karyawan
            </h2>
        </div>
        <div class="detail-card-body">
            <div class="detail-row">
                <div class="detail-label">Nama</div>
                <div class="detail-value">
                    <strong>{{ $pelatihan->nama }}</strong>
                </div>
            </div>

            <div class="detail-row">
                <div class="detail-label">Jabatan</div>
                <div class="detail-value {{ empty($pelatihan->jabatan) ? 'empty' : '' }}">
                    {{ $pelatihan->jabatan ?? 'Tidak diisi' }}
                </div>
            </div>

            <div class="detail-row">
                <div class="detail-label">Unit</div>
                <div class="detail-value {{ empty($pelatihan->unit) ? 'empty' : '' }}">
                    {{ $pelatihan->unit ?? 'Tidak diisi' }}
                </div>
            </div>

            <div class="divider"></div>

            <div class="detail-row">
                <div class="detail-label">Status Kepegawaian</div>
                <div class="detail-value">
                    @if ($pelatihan->is_pns)
                        <span class="pelatihan-badge" style="background-color: #d1fae5; color: #065f46; display: inline-block; width: auto;">PNS</span>
                        <div class="mt-2" style="font-size: 0.9rem; padding-top: 10px;">
                            <div><strong>NIP:</strong> {{ $pelatihan->nip ?? 'Tidak diisi' }}</div>
                            <div><strong>Golongan:</strong> {{ $pelatihan->golongan ?? 'Tidak diisi' }}</div>
                        </div>
                    @else
                        <span class="pelatihan-badge" style="background-color: #f3f4f6; color: #4b5563; display: inline-block; width: auto;">Non-PNS</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="detail-card">
        <div class="detail-card-header">
            <h2 class="detail-card-title">
                <i class="fas fa-certificate"></i>
                Pelatihan Dasar yang Dimiliki
            </h2>
        </div>
        <div class="detail-card-body">
            @php
                $daftarPelatihan = $pelatihan->pelatihan_dasar ?? [];
            @endphp

            @if (is_array($daftarPelatihan) && count($daftarPelatihan) > 0)
                <div class="pelatihan-list">

                    @foreach ($daftarPelatihan as $item)
                        @php
                            $nama = is_object($item) ? ($item->nama ?? null) : ($item['nama'] ?? null);
                            $tahun = is_object($item) ? ($item->tahun ?? null) : ($item['tahun'] ?? null);
                        @endphp

                        @if ($nama)
                            <div class="pelatihan-badge">
                                <span>{{ $nama }}</span>
                                <span class="pelatihan-badge-tahun">{{ $tahun ?? '-' }}</span>
                            </div>
                        @endif
                    @endforeach
                </div>
            @else
                <div class="detail-value empty">
                    <i class="fas fa-inbox"></i>
                    Belum ada data pelatihan dasar
                </div>
            @endif
        </div>
    </div>


    <div class="detail-card">
        <div class="detail-card-header">
            <h2 class="detail-card-title">
                <i class="fas fa-info-circle"></i>
                Informasi Lainnya
            </h2>
        </div>
        <div class="detail-card-body">
            <div class="detail-row">
                <div class="detail-label">Dibuat Pada</div>
                <div class="detail-value">
                    {{ $pelatihan->created_at->format('d M Y H:i') }}
                </div>
            </div>

            <div class="detail-row">
                <div class="detail-label">Terakhir Diperbarui</div>
                <div class="detail-value">
                    {{ $pelatihan->updated_at->format('d M Y H:i') }}
                </div>
            </div>
        </div>
    </div>
@endsection
