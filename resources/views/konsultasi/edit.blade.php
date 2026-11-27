@extends('layouts.app')

@section('title', 'Edit Konsultasi')
@section('page-title', 'Edit Hasil Konsultasi')

{{-- CSS Summernote & Custom Styles --}}
@push('styles')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
<style>
    :root {
        --custom-maroon: #7c1316;
        --custom-maroon-light: #a3191d;
        --custom-maroon-subtle: #fcf0f1;
        --text-dark: #1f2937;
        --text-muted: #64748b;
        --card-radius: 16px;
        --shadow-soft: 0 4px 20px rgba(0, 0, 0, 0.05);
        --transition: 0.3s ease;
    }

    /* --- Header --- */
    .page-header-wrapper {
        background: #fff;
        border-radius: var(--card-radius);
        padding: 1.5rem;
        box-shadow: var(--shadow-soft);
        margin-bottom: 2rem;
        border-left: 5px solid var(--custom-maroon);
        display: flex; justify-content: space-between; align-items: center;
    }

    /* --- Card & Form --- */
    .custom-card {
        background: #fff;
        border: none;
        border-radius: var(--card-radius);
        box-shadow: var(--shadow-soft);
        overflow: hidden;
    }

    .card-header-custom {
        background: var(--custom-maroon);
        color: white;
        padding: 1.2rem 1.5rem;
        font-weight: 600;
        border-bottom: 4px solid var(--custom-maroon-light);
        display: flex; align-items: center; gap: 0.5rem;
    }

    .form-label { font-weight: 600; font-size: 0.9rem; color: var(--text-dark); margin-bottom: 0.5rem; }
    
    .input-group-text { background-color: #f8f9fa; color: var(--custom-maroon); border-right: none; }
    .form-control { border-left: none; border-radius: 8px; padding: 0.6rem 1rem; border-color: #e2e8f0; }
    .form-control:focus { border-color: var(--custom-maroon-light); box-shadow: 0 0 0 2px rgba(124, 19, 22, 0.1); }

    /* --- Summernote Tweaks --- */
    .note-editor.note-frame {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        box-shadow: none;
    }
    .note-toolbar {
        background: #f8fafc !important;
        border-bottom: 1px solid #e2e8f0 !important;
        border-radius: 8px 8px 0 0;
    }
    .note-btn {
        background: white !important;
        border: 1px solid #e2e8f0 !important;
        color: #64748b !important;
    }
    .note-btn:hover { background: #e2e8f0 !important; color: var(--custom-maroon) !important; }
    .note-resizebar { display: none; } /* Hilangkan bar resize bawah agar rapi */

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
    .btn-outline-custom:hover { background: #f8f9fa; border-color: #cbd5e1; color: var(--text-dark); }

    .animate-up { animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards; opacity: 0; transform: translateY(20px); }
    @keyframes fadeInUp { to { opacity: 1; transform: translateY(0); } }
</style>
@endpush

@section('content')
    <div class="container py-4">
        
        {{-- Header --}}
        <div class="page-header-wrapper animate-up mb-4">
            <div>
                <h4 class="fw-bold mb-1" style="color: var(--custom-maroon);">Edit Konsultasi</h4>
                <small class="text-muted">Perbarui catatan hasil bimbingan Anda.</small>
            </div>
            <div>
                <a href="{{ route('konsultasi.index') }}" class="btn btn-outline-custom shadow-sm">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </div>

        <div class="row justify-content-center animate-up" style="animation-delay: 0.1s;">
            <div class="col-lg-9">
                <div class="custom-card">
                    <div class="card-header-custom">
                        <i class="bi bi-pencil-square fs-5"></i> Form Edit Catatan
                    </div>
                    <div class="card-body p-4">
                        
                        @if ($errors->any())
                            <div class="alert alert-danger rounded-3 mb-4 border-0 shadow-sm">
                                <ul class="mb-0 small">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('konsultasi.update', $konsultasi->id) }}" method="POST" id="formEditKonsultasi">
                            @csrf
                            @method('PUT')

                            <div class="mb-4">
                                <label class="form-label">Tanggal Konsultasi <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-calendar-date"></i></span>
                                    <input type="date" name="tanggal_konsul" class="form-control @error('tanggal_konsul') is-invalid @enderror" 
                                           value="{{ old('tanggal_konsul', $konsultasi->tanggal_konsul->format('Y-m-d')) }}" required>
                                </div>
                                @error('tanggal_konsul')
                                    <div class="invalid-feedback d-block small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Hasil Konsultasi <span class="text-danger">*</span></label>
                                <textarea name="hasil_konsul" id="summernoteEdit" class="form-control @error('hasil_konsul') is-invalid @enderror">
                                    {{ old('hasil_konsul', $konsultasi->hasil_konsul) }}
                                </textarea>
                                <div class="form-text text-muted small mt-1">
                                    <i class="bi bi-info-circle me-1"></i> Pastikan poin-poin revisi tercatat dengan jelas.
                                </div>
                                @error('hasil_konsul')
                                    <div class="invalid-feedback d-block small">{{ $message }}</div>
                                @enderror
                            </div>

                            <hr class="my-4 border-light">

                            <div class="d-flex justify-content-between align-items-center">
                                <a href="{{ route('konsultasi.index') }}" class="btn btn-outline-custom">
                                    <i class="bi bi-x-circle me-1"></i> Batal
                                </a>
                                <button type="submit" class="btn btn-maroon shadow-sm">
                                    <i class="bi bi-check-circle-fill me-2"></i> Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

{{-- Scripts Summernote & SweetAlert --}}
@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        // Init Summernote
        $('#summernoteEdit').summernote({
            placeholder: 'Tulis hasil diskusi, saran revisi, atau poin penting...',
            tabsize: 2,
            height: 350,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['insert', ['link']],
                ['view', ['fullscreen', 'help']]
            ],
            // Callbacks untuk styling tambahan jika perlu
            callbacks: {
                onInit: function() {
                    $('.note-editor').addClass('shadow-none');
                }
            }
        });

        // Validasi & Konfirmasi sebelum Submit
        $('#formEditKonsultasi').on('submit', function(e) {
            e.preventDefault();

            // Cek isi summernote (strip tags)
            var content = $('#summernoteEdit').summernote('code');
            var text = $("<div>").html(content).text().trim();
            
            if (text.length < 10) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Konten Terlalu Pendek',
                    text: 'Mohon isi hasil konsultasi dengan lebih detail (minimal 10 karakter).',
                    confirmButtonColor: '#7c1316'
                });
                return false;
            }

            // SweetAlert Konfirmasi
            Swal.fire({
                title: 'Simpan Perubahan?',
                text: "Pastikan data yang diubah sudah benar.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#7c1316',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Simpan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit(); // Submit form secara manual
                }
            });
        });
    });
</script>
@endpush