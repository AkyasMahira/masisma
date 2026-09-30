@extends('layouts.app')

@section('title', 'Konsultasi Penelitian')
@section('page-title', 'Konsultasi dengan CI')

{{-- Tambahkan CSS Summernote & Custom Styles --}}
@push('styles')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
<style>
    :root {
        --custom-maroon: #7c1316;
        --custom-maroon-light: #a3191d;
        --custom-maroon-subtle: #fcf0f1;
        --text-dark: #2c3e50;
        --text-muted: #64748b;
        --card-radius: 16px;
        --transition: 0.3s ease;
        --timeline-color: #e2e8f0;
    }

    /* --- Header --- */
    .page-header-wrapper {
        background: #fff;
        border-radius: var(--card-radius);
        padding: 1.5rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        margin-bottom: 2rem;
        border-left: 5px solid var(--custom-maroon);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* --- Cards --- */
    .custom-card {
        background: #fff;
        border: none;
        border-radius: var(--card-radius);
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        overflow: hidden;
        margin-bottom: 1.5rem;
    }

    .card-header-maroon {
        background: var(--custom-maroon);
        color: white;
        padding: 1rem 1.5rem;
        font-weight: 600;
        display: flex; align-items: center; gap: 0.5rem;
    }
    
    .card-header-light {
        background: #fff;
        border-bottom: 1px solid #f1f5f9;
        padding: 1.2rem 1.5rem;
        font-weight: 700;
        color: var(--text-dark);
    }

    /* --- Typography --- */
    .info-label {
        font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted);
        font-weight: 600; margin-bottom: 0.2rem; letter-spacing: 0.5px;
    }
    .info-value {
        font-size: 0.95rem; color: var(--text-dark); font-weight: 500; margin-bottom: 1rem;
    }

    /* --- Timeline Styles (Riwayat) --- */
    .timeline {
        position: relative;
        padding-left: 20px;
        margin-top: 10px;
    }
    .timeline::before {
        content: '';
        position: absolute;
        left: 0; top: 0; bottom: 0;
        width: 2px;
        background: var(--timeline-color);
    }
    .timeline-item {
        position: relative;
        padding-left: 25px;
        margin-bottom: 2rem;
    }
    .timeline-item::after {
        content: '';
        position: absolute;
        left: -4px;
        top: 5px;
        width: 10px; height: 10px;
        border-radius: 50%;
        background: var(--custom-maroon);
        border: 2px solid white;
        box-shadow: 0 0 0 2px var(--custom-maroon-subtle);
    }
    .timeline-date {
        font-size: 0.85rem;
        color: var(--text-muted);
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .timeline-content {
        background: #f8fafc;
        border-radius: 12px;
        padding: 1.2rem;
        border: 1px solid #f1f5f9;
        font-size: 0.95rem;
        line-height: 1.6;
        color: var(--text-dark);
    }

    /* --- Summernote Tweaks --- */
    .note-editor.note-frame {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        overflow: hidden;
    }
    .note-toolbar {
        background: #f8fafc !important;
        border-bottom: 1px solid #e2e8f0 !important;
    }
    .note-btn {
        background: white !important;
        border: 1px solid #e2e8f0 !important;
        color: #64748b !important;
    }
    .note-btn:hover { background: #e2e8f0 !important; }

    /* --- Buttons --- */
    .btn-maroon {
        background-color: var(--custom-maroon); color: #fff; border: none;
        border-radius: 8px; padding: 0.6rem 1.5rem; font-weight: 600; transition: var(--transition);
    }
    .btn-maroon:hover { background-color: var(--custom-maroon-light); color: white; transform: translateY(-2px); }

    .btn-outline-custom {
        border: 1px solid #e2e8f0; color: var(--text-dark); background: white;
        border-radius: 8px; padding: 0.5rem 1.2rem; font-weight: 500; transition: var(--transition);
        text-decoration: none;
    }
    .btn-outline-custom:hover { background: #f8f9fa; border-color: #cbd5e1; }

    .btn-action-icon {
        width: 30px; height: 30px; display: inline-flex; align-items: center; justify-content: center;
        border-radius: 6px; transition: var(--transition); border: none;
    }
    .btn-edit-icon { background: #e0f2fe; color: #0284c7; }
    .btn-edit-icon:hover { background: #bae6fd; }
    .btn-del-icon { background: #fee2e2; color: #dc2626; }
    .btn-del-icon:hover { background: #fecaca; }

    .animate-up { animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards; opacity: 0; transform: translateY(20px); }
    @keyframes fadeInUp { to { opacity: 1; transform: translateY(0); } }
</style>
@endpush

@section('content')
    <div class="container py-4">
        
        {{-- Header --}}
{{-- Ganti bagian Header di Blade Anda --}}
<div class="page-header-wrapper animate-up">
    <div>
        <h4 class="fw-bold mb-1" style="color: var(--custom-maroon);">Konsultasi Penelitian</h4>
        {{-- Tampilkan judul dari record pra_penelitian terbaru --}}
        <div class="text-muted small">
            <i class="bi bi-journal-text me-1"></i> 
            Judul: <strong>{{ $praPenelitian->judul ?? 'Tidak ada judul' }}</strong>
        </div>
    </div>
    <div>
        <a href="{{ route('pengajuan.index') }}" class="btn btn-outline-custom shadow-sm">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>
</div>

        {{-- Alerts (Fallback if SweetAlert fails) --}}
        @if (session('error'))
            <div class="alert alert-danger animate-up">{{ session('error') }}</div>
        @endif

        <div class="row g-4">
            {{-- KOLOM KIRI: Info Pembimbing --}}
            <div class="col-lg-4 animate-up" style="animation-delay: 0.1s;">
                <div class="custom-card">
                    <div class="card-header-maroon">
                        <i class="bi bi-person-vcard-fill"></i> Info Pembimbing (CI)
                    </div>
                    <div class="p-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class="bg-light rounded-circle p-3 me-3 text-center" style="width: 60px; height: 60px;">
                                <i class="bi bi-person-badge fs-3 text-secondary"></i>
                            </div>
                            <div>
                                {{-- PERBAIKAN 1: Panggil relasi ->ci->nama --}}
                                <div class="fw-bold text-dark">{{ $pengajuan->ci->nama ?? 'Nama CI Tidak Muncul' }}</div>
                                {{-- Sesuaikan nama kolom bidang/jabatan di db corporate_instructors --}}
                                <small class="text-muted">{{ $pengajuan->ci->bidang ?? $pengajuan->ci->jabatan ?? '-' }}</small>
                            </div>
                        </div>
                        
                        <div class="info-label">Kontak (WhatsApp)</div>
                        <div class="info-value">
                            {{-- PERBAIKAN 2: Panggil relasi ->ci->no_hp --}}
                            @php 
                                $no_hp = $pengajuan->ci->no_hp ?? '-';
                                $wa_link = preg_replace('/[^0-9]/', '', $no_hp);
                            @endphp
                            @if($no_hp !== '-')
                                <a href="https://wa.me/{{ $wa_link }}" target="_blank" class="text-decoration-none text-success fw-bold">
                                    <i class="bi bi-whatsapp me-1"></i> {{ $no_hp }}
                                </a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </div>

                        <div class="info-label">Lokasi Ruangan</div>
<div class="info-value">
    <span class="badge bg-light text-dark border">
        <i class="bi bi-door-open me-1"></i> 
        {{-- Panggil dataRuangan (sesuai nama fungsi di model baru) --}}
        {{ $pengajuan->dataRuangan->nm_ruangan ?? 'Belum ditentukan' }}
    </span>
</div>

                        <hr class="border-light my-3">

                        {{-- Progress Konsultasi --}}
                        <div class="p-3 rounded-3" style="background-color: {{ $totalKonsul >= $minKonsul ? '#f0fdf4' : '#fff7ed' }}; border: 1px solid {{ $totalKonsul >= $minKonsul ? '#bbf7d0' : '#ffedd5' }};">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <small class="fw-bold text-uppercase {{ $totalKonsul >= $minKonsul ? 'text-success' : 'text-warning' }}">
                                    Progres Bimbingan
                                </small>
                                <span class="badge bg-white text-dark shadow-sm border">{{ $totalKonsul }} / {{ $minKonsul }}</span>
                            </div>
                            
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar {{ $totalKonsul >= $minKonsul ? 'bg-success' : 'bg-warning' }}" 
                                     role="progressbar" 
                                     style="width: {{ min(($totalKonsul / $minKonsul) * 100, 100) }}%">
                                </div>
                            </div>
                            
                            <div class="mt-2 small">
                                @if ($totalKonsul < $minKonsul)
                                    <span class="text-muted">Kurang <strong>{{ $minKonsul - $totalKonsul }}</strong> kali lagi.</span>
                                @else
                                    <span class="text-success fw-bold"><i class="bi bi-check-circle-fill me-1"></i> Target tercapai!</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN: Form & History --}}
            <div class="col-lg-8 animate-up" style="animation-delay: 0.2s;">
                
                {{-- Form Input --}}
                <div class="custom-card">
                    <div class="card-header-light">
                        <i class="bi bi-journal-plus me-2 text-primary"></i> Catat Hasil Konsultasi Baru
                    </div>
                    <div class="p-4">
                        <form action="{{ route('konsultasi.store') }}" method="POST" id="formKonsultasi">
                            @csrf
                            <div class="mb-4">
                                <label class="form-label fw-bold small text-uppercase text-muted">Tanggal Konsultasi</label>
                                <input type="date" name="tanggal_konsul" class="form-control" value="{{ old('tanggal_konsul', date('Y-m-d')) }}" required>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold small text-uppercase text-muted">Hasil Diskusi / Revisi</label>
                                <textarea name="hasil_konsul" id="summernote" class="form-control">{{ old('hasil_konsul') }}</textarea>
                                <div class="form-text">Catat poin-poin penting, revisi, atau saran dari pembimbing.</div>
                            </div>

                            <div class="text-end">
                                <button type="submit" class="btn btn-maroon">
                                    <i class="bi bi-send-fill me-2"></i> Simpan Catatan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Timeline History --}}
                <div class="custom-card">
                    <div class="card-header-light">
                        <i class="bi bi-clock-history me-2 text-info"></i> Riwayat Bimbingan
                    </div>
                    <div class="p-4">
                        @if ($konsultasi->count() > 0)
                            <div class="timeline">
                                @foreach ($konsultasi as $k)
                                    <div class="timeline-item">
                                        <div class="timeline-date">
                                            <span class="fw-bold text-dark">
                                                <i class="bi bi-calendar-event me-1"></i> {{ \Carbon\Carbon::parse($k->tanggal_konsul)->format('d F Y') }}
                                            </span>
                                            <div class="d-flex gap-2">
                                                <a href="{{ route('konsultasi.edit', $k->id) }}" class="btn-action-icon btn-edit-icon" title="Edit">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                                <form action="{{ route('konsultasi.destroy', $k->id) }}" method="POST" class="d-inline delete-form">
                                                    @csrf @method('DELETE')
                                                    <button type="button" class="btn-action-icon btn-del-icon btn-delete" title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                        <div class="timeline-content">
                                            {!! $k->hasil_konsul !!}
                                            <div class="mt-2 text-end">
                                                <small class="text-muted fst-italic" style="font-size: 0.75rem;">
                                                    Dicatat: {{ $k->created_at->diffForHumans() }}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-5">
                                <div class="mb-3">
                                    <i class="bi bi-journal-x display-4 text-muted opacity-50"></i>
                                </div>
                                <h5 class="fw-bold text-muted">Belum ada catatan</h5>
                                <p class="text-muted small">Mulai bimbingan dan catat hasilnya di formulir atas.</p>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        // Init Summernote
        $('#summernote').summernote({
            placeholder: 'Tulis hasil diskusi, saran, revisi...',
            tabsize: 2,
            height: 200,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['insert', ['link']],
                ['view', ['fullscreen', 'help']]
            ]
        });

        // SweetAlert Notifikasi
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session("success") }}',
                timer: 2000,
                showConfirmButton: false
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: '{{ session("error") }}'
            });
        @endif

        // Konfirmasi Hapus
        $('.btn-delete').on('click', function(e) {
            e.preventDefault();
            let form = $(this).closest('form');
            
            Swal.fire({
                title: 'Hapus catatan ini?',
                text: "Data yang dihapus tidak dapat dikembalikan.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });

        // Validasi Form Submit
        $('#formKonsultasi').on('submit', function(e) {
            var content = $('#summernote').summernote('code');
            // Strip tags untuk cek kosong
            var text = $("<div>").html(content).text().trim();
            
            if (text.length < 5) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Isi Kosong',
                    text: 'Mohon isi hasil konsultasi dengan lengkap.',
                    confirmButtonColor: '#7c1316'
                });
            }
        });
    });
</script>
@endpush