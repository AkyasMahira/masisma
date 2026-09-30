@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    /* Variabel Warna Sederhana */
    :root { 
        --primary-color: #7c1316; /* Maroon SLG */
        --primary-hover: #5a0e10;
        --bg-light: #f8f9fa;
        --border-color: #e9ecef;
    }

    body { 
        background-color: var(--bg-light); 
        padding-top: 4px; /* Untuk progress bar */
    }

    /* Progress Bar Minimalis */
    .learning-progress {
        height: 4px; width: 100%; background: #dfe4ea;
        position: fixed; top: 0; left: 0; z-index: 1030;
    }
    .progress-fill {
        height: 100%; background: var(--primary-color);
        width: {{ $persen ?? 0 }}%; transition: width 0.8s ease;
    }

    /* Card Layout Umum */
    .clean-card {
        background: #ffffff;
        border: 1px solid var(--border-color);
        border-radius: 12px;
        margin-bottom: 1.5rem;
        overflow: hidden;
    }

    /* --- RESPONSIVE MEDIA CORE --- */
    .media-container {
        width: 100%;
        background-color: #f1f3f5;
        display: flex;
        justify-content: center;
        align-items: center;
        border-bottom: 1px solid var(--border-color);
    }

    .media-container video, 
    .media-container img {
        width: 100%;
        height: auto;
        max-height: 75vh;
        object-fit: contain;
        display: block;
    }

    .media-container iframe {
        width: 100%;
        height: 70vh;
        min-height: 400px;
        border: none;
        display: block;
    }

    /* Tombol Utama */
    .btn-primary-custom {
        background-color: var(--primary-color);
        color: white;
        font-weight: 600;
        transition: 0.2s;
    }
    .btn-primary-custom:hover:not(:disabled) {
        background-color: var(--primary-hover);
        color: white;
    }

    /* Mengatur style bawaan dari Text Editor agar rapi */
    .rich-text-content p:last-child {
        margin-bottom: 0;
    }
    .rich-text-content ul, .rich-text-content ol {
        margin-bottom: 1rem;
        padding-left: 1.5rem;
    }

    /* Penyesuaian Khusus Mobile (< 768px) */
    @media (max-width: 767px) {
        .media-container iframe {
            height: 60vh;
            min-height: 350px;
        }
        .header-section h1 {
            font-size: 1.4rem;
        }
        .mobile-stack {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .mobile-stack .btn {
            width: 100%;
        }
    }
</style>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/office-viewer@0.0.1/dist/index.min.css">
<script src="https://cdn.jsdelivr.net/npm/office-viewer@0.0.1/dist/office-viewer.min.js"></script>

<style>
    .pptx-viewer-wrapper {
        width: 100%;
        height: 600px;
        overflow: auto;
        background: #f0f0f0;
        border: 1px solid #ddd;
        position: relative;
    }
    .loading-text {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
    }
</style>
<div class="learning-progress">
    <div class="progress-fill"></div>
</div>

<div class="container py-4">
    <div class="clean-card p-3 p-md-4 shadow-sm header-section" style="border-top: 4px solid var(--primary-color);">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <span class="badge bg-dark mb-2 px-3 py-2 rounded-pill">Materi #{{ $material->order }}</span>
                <h1 class="fw-bold mb-0 text-dark">{{ $material->title }}</h1>
            </div>
            <div>
                <a href="{{ route('orientasi.index') }}" class="btn btn-outline-dark fw-bold rounded-pill w-100 px-4">
                    <i class="fas fa-arrow-left me-2"></i> Kembali ke Panel
                </a>
            </div>
        </div>
    </div>

    @if($material->description)
    <div class="clean-card p-3 p-md-4 shadow-sm">
        <h6 class="fw-bold text-uppercase text-muted mb-3"><i class="fas fa-info-circle me-1"></i> Pengantar Materi</h6>
        <div class="mb-0 text-dark rich-text-content" style="font-size: 1.05rem; line-height: 1.6;">
            {!! $material->description !!}
        </div>
    </div>
    @endif

    @foreach($material->files as $index => $file)
    <div class="clean-card shadow-sm">
        <div class="media-container">
            @php $ext = strtolower($file->file_type); @endphp

            @if(in_array($ext, ['mp4', 'webm', 'ogg']))
                <video controls controlsList="nodownload" preload="metadata">
                    <source src="{{ asset('storage/'.$file->file_path) }}" type="video/{{ $ext }}">
                    Browser Anda tidak mendukung pemutar video ini.
                </video>

            @elseif($ext == 'pdf')
                <iframe src="{{ asset('storage/'.$file->file_path) }}#toolbar=0"></iframe>

       @elseif(in_array($ext, ['ppt', 'pptx']))
    <div class="media-container bg-dark" style="min-height: 600px; display: block; overflow: auto;">
        {{-- Wadah pratinjau dengan ID unik --}}
        <div id="pptx-wrapper-{{ $index }}" style="width: 100%; min-height: 580px; margin: 0 auto; background: #fff;">
            <div class="d-flex flex-column align-items-center justify-content-center" style="height: 500px;" id="loader-{{ $index }}">
                <div class="spinner-border text-danger" role="status"></div>
                <p class="mt-3 text-muted">Mengekstrak slide presentasi...</p>
            </div>
        </div>
    </div>

    {{-- Gunakan type="module" sesuai dokumentasi library --}}
    <script type="module">
        import { init } from 'https://esm.sh/pptx-preview@1.0.5';

        const container = document.getElementById('pptx-wrapper-{{ $index }}');
        const loader = document.getElementById('loader-{{ $index }}');
        const fileUrl = "{{ asset('storage/'.$file->file_path) }}";

        try {
            // 1. Inisialisasi Previewer
            const pptxPreviewer = init(container, {
                width: container.offsetWidth,
                height: 600
            });

            // 2. Ambil file dari server Laravel (CORS harus aktif)
            fetch(fileUrl)
                .then(response => {
                    if (!response.ok) throw new Error('Gagal mengambil file dari server');
                    return response.arrayBuffer();
                })
                .then(arrayBuffer => {
                    if (loader) loader.remove(); // Hapus spinner
                    // 3. Render file
                    pptxPreviewer.preview(arrayBuffer);
                })
                .catch(error => {
                    console.error('Error rendering PPTX:', error);
                    container.innerHTML = `
                        <div class="p-5 text-center">
                            <i class="fas fa-exclamation-circle fa-3x text-warning mb-3"></i>
                            <p class="text-dark">Gagal memuat pratinjau: ${error.message}</p>
                            <a href="${fileUrl}" class="btn btn-sm btn-primary-custom rounded-pill">Unduh & Buka Manual</a>
                        </div>`;
                });
        } catch (e) {
            console.error('Inisialisasi library gagal:', e);
        }
    </script>

            @elseif(in_array($ext, ['jpg', 'jpeg', 'png', 'webp']))
                <img src="{{ asset('storage/'.$file->file_path) }}" alt="{{ $file->file_name }}">

            @else
                <div class="p-5 text-center text-muted">
                    <i class="fas fa-file-download fa-3x mb-3"></i>
                    <p class="mb-2 fw-bold">Pratinjau tidak tersedia untuk format {{ strtoupper($ext) }}</p>
                    <a href="{{ asset('storage/'.$file->file_path) }}" class="btn btn-sm btn-outline-secondary">Download File</a>
                </div>
            @endif
        </div>

        <div class="p-3 p-md-4">
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center border-bottom pb-3 mb-3 gap-2">
                <h5 class="fw-bold mb-0 text-break">
                    <i class="fas fa-file-alt text-muted me-2"></i>{{ $file->file_name }}
                    <span class="badge bg-light text-dark border ms-2" style="font-size: 0.7em;">{{ strtoupper($ext) }}</span>
                </h5>
                <a href="{{ asset('storage/'.$file->file_path) }}" target="_blank" class="btn btn-sm btn-light border fw-bold text-nowrap">
                    <i class="fas fa-download me-1"></i> UNDUH
                </a>
            </div>
            
            <div class=" rich-text-content" style="font-size: 0.95rem; line-height: 1.6;">
                {!! $file->description ?? 'Tidak ada catatan tambahan untuk dokumen ini.' !!}
            </div>
        </div>
    </div>
    @endforeach

    @if($material->external_link)
    <div class="clean-card p-4 text-center shadow-sm" style="border: 2px dashed var(--primary-color);">
        <i class="fas fa-link fa-2x text-muted mb-3"></i>
        <h5 class="fw-bold mb-2">Tautan Referensi Eksternal</h5>
        <p class="text-muted small mb-3">Materi ini mengharuskan Anda untuk membuka tautan sumber daya luar.</p>
        <a href="{{ $material->external_link }}" target="_blank" class="btn btn-primary-custom rounded-pill px-4">
            Buka Tautan <i class="fas fa-external-link-alt ms-1"></i>
        </a>
    </div>
    @endif

    <div class="mt-5 py-5 text-center border-top">
        @if(!$isDone)
            <h4 class="fw-bold mb-3">Sudah selesai mempelajari materi ini?</h4>
            <p class="text-muted mb-4">Pastikan Anda telah menyimak seluruh lampiran sebelum melanjutkan.</p>
            <form action="{{ route('orientasi.materi.complete', $material->id) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-primary-custom btn-lg rounded-pill px-5 shadow-sm" {{ $material->files->count() == 0 ? 'disabled' : '' }}>
                    <i class="fas fa-check-circle me-2"></i> TANDAI SELESAI
                </button>
            </form>
        @else
            <div class="d-inline-block text-center px-4 py-4 rounded-3" style="background-color: #e8f5e9; border: 1px solid #c8e6c9;">
                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                <h4 class="fw-bold text-success mb-2">Materi Selesai!</h4>
                <p class="text-muted small mb-4">Anda telah menyelesaikan bagian ini.</p>
                <div class="mobile-stack justify-content-center">
                    <a href="{{ route('orientasi.index') }}" class="btn btn-outline-success px-4 rounded-pill">Kembali</a>
                    @if($nextMaterial)
                        <a href="{{ route('orientasi.materi.show', $nextMaterial->id) }}" class="btn btn-success px-4 rounded-pill shadow-sm">
                            Materi Selanjutnya <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
@endsection