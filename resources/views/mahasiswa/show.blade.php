@extends('layouts.app')

@section('title', 'Detail Mahasiswa')
@section('page-title', 'Detail Mahasiswa')

@section('content')
    <style>
        :root {
            --custom-maroon: #7c1316;
            --custom-maroon-light: #a3191d;
            --custom-maroon-subtle: #fcf0f1;
            --text-dark: #2c3e50;
            --text-muted: #64748b;
            --card-radius: 16px;
            --transition: 0.3s ease;
        }

        /* --- Card Styling --- */
        .profile-card {
            border: none;
            border-radius: var(--card-radius);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            background: #fff;
            overflow: hidden;
        }

        .profile-header-bg {
            background: linear-gradient(135deg, var(--custom-maroon), var(--custom-maroon-light));
            height: 100px;
        }

        .profile-avatar-wrapper {
            margin-top: -50px;
            text-align: center;
            margin-bottom: 1rem;
        }

        .profile-avatar {
            width: 100px;
            height: 100px;
            background-color: white;
            border-radius: 50%;
            padding: 4px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            display: inline-block;
            position: relative; /* Added for image positioning */
            overflow: hidden; /* Ensures image stays inside circle */
        }

        /* Style khusus untuk Gambar Foto */
        .profile-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover; /* Memastikan foto 3x4 terpotong rapi dalam lingkaran */
            border-radius: 50%;
        }

        .avatar-content {
            width: 100%;
            height: 100%;
            background-color: var(--custom-maroon-subtle);
            color: var(--custom-maroon);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            font-weight: bold;
        }

        /* --- Info Groups --- */
        .info-group {
            margin-bottom: 1.2rem;
        }
        .info-label {
            font-size: 0.85rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }
        .info-value {
            font-size: 1.1rem;
            font-weight: 500;
            color: var(--text-dark);
        }

        /* --- Share Box --- */
        .share-box {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 12px;
            padding: 1.5rem;
            margin-top: 2rem;
        }

        .link-wrapper {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }

        .link-text {
            font-family: monospace;
            color: var(--custom-maroon);
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* --- Badges --- */
        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.4rem 1rem;
            border-radius: 50px;
            font-size: 0.9rem;
            font-weight: 600;
        }
        .status-active { background-color: #d1fae5; color: #065f46; }
        .status-inactive { background-color: #f3f4f6; color: #4b5563; }

        /* --- Animation --- */
        .animate-up {
            animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
            opacity: 0; transform: translateY(20px);
        }
        @keyframes fadeInUp { to { opacity: 1; transform: translateY(0); } }
    </style>

    <div class="row justify-content-center animate-up">
        <div class="col-md-8 col-lg-6">
            <div class="profile-card">
                <div class="profile-header-bg"></div>

                <div class="profile-avatar-wrapper">
                    <div class="profile-avatar">
                        {{-- LOGIKA TAMPILKAN FOTO --}}
                        @if($mahasiswa->foto_path)
                            {{-- Jika ada foto, tampilkan gambar --}}
                            <img src="{{ asset($mahasiswa->foto_path) }}" alt="Foto {{ $mahasiswa->nm_mahasiswa }}">
                        @else
                            {{-- Jika tidak ada, tampilkan inisial --}}
                            <div class="avatar-content">
                                {{ strtoupper(substr($mahasiswa->nm_mahasiswa, 0, 1)) }}
                            </div>
                        @endif
                    </div>

                    <h4 class="mt-2 mb-1 fw-bold" style="color: var(--text-dark);">{{ $mahasiswa->nm_mahasiswa }}</h4>

                    @if ($mahasiswa->status == 'aktif')
                        <span class="status-badge status-active"><i class="bi bi-check-circle-fill me-2"></i> Aktif</span>
                    @else
                        <span class="status-badge status-inactive"><i class="bi bi-x-circle-fill me-2"></i> Nonaktif</span>
                    @endif
                </div>

                <div class="card-body p-4 pt-0">
                    <div class="row mt-4">
                        <div class="col-6">
                            <div class="info-group">
                                <div class="info-label"><i class="bi bi-building me-1"></i> Universitas</div>
                                <div class="info-value">{{ $mahasiswa->mou ? ($mahasiswa->mou->nama_instansi ?? $mahasiswa->mou->nama_universitas) : '-' }}</div>
                            </div>
                            <div class="info-group">
                                <div class="info-label"><i class="bi bi-book me-1"></i> Program Studi</div>
                                <div class="info-value">{{ $mahasiswa->prodi ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="info-group">
                                <div class="info-label"><i class="bi bi-door-open me-1"></i> Ruangan</div>
                                <div class="info-value">
                                    @if($mahasiswa->ruangan)
                                        <span class="badge bg-light text-dark border">{{ $mahasiswa->ruangan->nm_ruangan }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </div>
                            </div>
                            <div class="info-group">
                                <div class="info-label"><i class="bi bi-calendar-range me-1"></i> Sisa Masa Magang</div>
                                <div class="info-value">
                                    @if ($mahasiswa->sisa_hari > 0)
                                        <span class="text-primary fw-bold">{{ $mahasiswa->sisa_hari }} </span>
                                    @else
                                        <span class="text-danger fw-bold">Berakhir</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4 border-light">

                    <div class="share-box">
                        <h6 class="fw-bold mb-3 text-dark d-flex align-items-center">
                            <i class="bi bi-qr-code me-2 text-secondary"></i> Link Absensi & Status
                        </h6>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <small class="text-muted">Terakhir Absen:</small>
                            <span class="badge {{ $lastStatus ? 'bg-success' : 'bg-secondary' }}">
                                {{ $lastStatus ? ucfirst($lastStatus) : 'Belum ada data' }}
                            </span>
                        </div>

                        <label class="small text-muted mb-1">Salin Link Absensi:</label>
                        <div class="link-wrapper">
                            <a href="{{ route('absensi.card', $mahasiswa->share_token) }}"
                               target="_blank" class="link-text flex-grow-1 text-decoration-none"
                               id="absensiLink">
                                {{ route('absensi.card', $mahasiswa->share_token) }}
                            </a>
                            <button class="btn btn-sm btn-light border" onclick="copyLink()" title="Salin Link">
                                <i class="bi bi-clipboard"></i>
                            </button>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('mahasiswa.index') }}" class="btn btn-light border rounded-pill px-4">
                            <i class="bi bi-arrow-left me-2"></i> Kembali
                        </a>
                        <a href="{{ route('mahasiswa.edit', $mahasiswa->id) }}" class="btn btn-outline-secondary rounded-pill px-4">
                            <i class="bi bi-pencil-square me-2"></i> Edit
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function copyLink() {
            const linkText = document.getElementById('absensiLink').innerText;

            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(linkText).then(() => {
                    alert("Link berhasil disalin!");
                }).catch(err => {
                    fallbackCopy(linkText);
                });
            } else {
                fallbackCopy(linkText);
            }
        }

        function fallbackCopy(text) {
            const textArea = document.createElement("textarea");
            textArea.value = text;
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            try {
                document.execCommand('copy');
                alert("Link berhasil disalin!");
            } catch (err) {
                alert("Gagal menyalin link.");
            }
            document.body.removeChild(textArea);
        }
    </script>
@endsection
