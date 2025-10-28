@extends('layouts.app')

@section('title', 'Detail Mahasiswa')
@section('page-title', 'Detail Mahasiswa')

@section('content')
    <style>
        :root {
            --maroon: #7c1316;
            --maroon-light: #9d2a2e;
            --text-dark: #222;
            --text-muted: #6c757d;
            --bg-light: #f8f9fa;
        }

        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        }

        .card-body {
            padding: 2rem;
        }

        .mahasiswa-name {
            color: var(--maroon);
            font-weight: 700;
            font-size: 1.8rem;
        }

        .info-label {
            color: var(--text-muted);
            font-weight: 600;
            width: 130px;
            display: inline-block;
        }

        .info-value {
            color: var(--text-dark);
            font-weight: 500;
        }

        hr {
            border-top: 2px solid var(--bg-light);
        }

        .share-info {
            background-color: var(--bg-light);
            border-radius: 10px;
            padding: 1rem;
            font-size: 0.9rem;
        }

        a.absensi-link {
            color: var(--maroon-light);
            font-weight: 600;
            word-break: break-all;
            text-decoration: none;
        }

        a.absensi-link:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .card-body {
                padding: 1.5rem;
            }

            .mahasiswa-name {
                font-size: 1.5rem;
            }

            .info-label {
                width: 110px;
            }
        }
    </style>

    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="mahasiswa-name mb-3">{{ $mahasiswa->nm_mahasiswa }}</h4>

                    <p><span class="info-label">Universitas:</span>
                        <span class="info-value">{{ $mahasiswa->univ_asal }}</span>
                    </p>
                    <p><span class="info-label">Prodi:</span>
                        <span class="info-value">{{ $mahasiswa->prodi }}</span>
                    </p>
                    <p><span class="info-label">Ruangan:</span>
                        <span class="info-value">
                            {{ $mahasiswa->ruangan ? $mahasiswa->ruangan->nm_ruangan : $mahasiswa->nm_ruangan }}
                        </span>
                    </p>
                    <p><span class="info-label">Status:</span>
                        <span class="info-value">{{ ucfirst($mahasiswa->status) }}</span>
                    </p>

                    <hr>

                    <div class="share-info mt-3">
                        <p class="mb-1"><strong>Share Token:</strong>
                            <span>{{ \Illuminate\Support\Str::limit($mahasiswa->share_token, 16, '...') }}</span>
                        </p>
                        <p class="mb-1"><strong>Last Attendance:</strong>
                            {{ $lastStatus ? ucfirst($lastStatus) : 'No record' }}
                        </p>
                        <p class="mb-0">
                            <strong>Link Absensi:</strong><br>
                            <a href="{{ route('absensi.card', $mahasiswa->share_token) }}" class="absensi-link"
                                target="_blank">{{ route('absensi.card', $mahasiswa->share_token) }}</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
