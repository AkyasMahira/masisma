@extends('layouts.app')

@section('page-title', 'Tambah Materi Baru')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    :root {
        --maroon-slg: #7c1316;
        --maroon-light: #a3191d;
        --bg-light: #f4f6f9;
    }

    body { background-color: var(--bg-light); }

    .header-card {
        background: white;
        border-radius: 8px;
        border-left: 5px solid var(--maroon-slg);
        padding: 20px;
        margin-bottom: 25px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .card-custom {
        border: none;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        background: white;
    }

    .form-label {
        font-weight: 700;
        color: #333;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .form-control-custom {
        border-radius: 8px;
        border: 1px solid #dcdde1;
        padding: 12px 15px;
        transition: 0.3s;
    }
    .form-control-custom:focus {
        border-color: var(--maroon-slg);
        box-shadow: 0 0 0 0.25rem rgba(124, 19, 22, 0.1);
    }

    .btn-maroon {
        background-color: var(--maroon-slg);
        color: white;
        border-radius: 50px;
        font-weight: 700;
        padding: 12px 30px;
        border: none;
        transition: 0.3s;
    }
    .btn-maroon:hover {
        background-color: var(--maroon-light);
        color: white;
        box-shadow: 0 4px 12px rgba(124, 19, 22, 0.3);
        transform: translateY(-2px);
    }

    .btn-outline-secondary-custom {
        border-radius: 50px;
        padding: 10px 25px;
        font-weight: 600;
        border: 1.5px solid #ddd;
        color: #666;
    }

    .alert-industrial {
        background-color: #2c3e50;
        color: #ecf0f1;
        border: none;
        border-radius: 12px;
    }
    
    .section-divider {
        height: 1px;
        background: #eee;
        margin: 30px 0;
    }

    .input-group-text-custom {
        background-color: #f8f9fa;
        border-color: #dcdde1;
        color: var(--maroon-slg);
    }

    /* Tinggi Text Editor */
    .ck-editor__editable_inline {
        min-height: 200px;
    }
</style>

<div class="">
    <div class="header-card d-flex align-items-center justify-content-between">
        <div>
            <h4 class="fw-bold mb-0 text-dark">Tambah Materi Orientasi</h4>
            <small class="text-muted">Langkah 1: Input Metadata & Informasi Dasar</small>
        </div>
        <a href="{{ route('admin.materi.index') }}" class="btn btn-outline-secondary-custom shadow-sm">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card card-custom p-4">
                <form action="{{ route('admin.materi.store') }}" method="POST">
                    @csrf
                    
                    <div class="row g-4">
                        <div class="col-md-8">
                            <label class="form-label">Judul Materi <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control form-control-custom @error('title') is-invalid @enderror" 
                                   placeholder="Contoh: Pengenalan Budaya Kerja RSUD SLG" value="{{ old('title') }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Urutan Tampil <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text input-group-text-custom"><i class="fas fa-sort-numeric-down"></i></span>
                                <input type="number" name="order" class="form-control form-control-custom @error('order') is-invalid @enderror" 
                                       value="{{ old('order', 1) }}" required>
                            </div>
                            @error('order')
                                <div class="invalid-feedback text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label">Sub-Judul / Label Singkat</label>
                            <input type="text" name="subtitle" class="form-control form-control-custom" 
                                   placeholder="Contoh: Modul 1 - Dasar Etika Pelayanan" value="{{ old('subtitle') }}">
                        </div>

                        <div class="col-12">
                            <label class="form-label">Deskripsi Lengkap</label>
                            <textarea name="description" id="editor" class="form-control form-control-custom" rows="4" 
                                      placeholder="Tuliskan ringkasan materi yang akan dipelajari...">{{ old('description') }}</textarea>
                        </div>

                        <div class="col-12">
                            <div class="p-4 rounded-4 border" style="background-color: #fafbfc;">
                                <label class="form-label text-maroon"><i class="fas fa-link me-1"></i> Link Eksternal (Opsional)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-globe text-muted"></i></span>
                                    <input type="url" name="external_link" class="form-control form-control-custom border-start-0" 
                                           placeholder="https://youtube.com/video-materi atau https://drive.google.com/..." value="{{ old('external_link') }}">
                                </div>
                                <div class="form-text text-muted mt-2">
                                    <i class="fas fa-info-circle me-1"></i> Gunakan jika materi ini merujuk pada video eksternal atau penyimpanan cloud.
                                </div>
                            </div>
                        </div>

                        <div class="col-12 text-end mt-4">
                            <div class="section-divider"></div>
                            <button type="reset" class="btn btn-light px-4 me-2 fw-bold text-muted">Batal / Reset</button>
                            <button type="submit" class="btn btn-maroon shadow-sm">
                                Simpan & Lanjut Kelola File <i class="fas fa-chevron-right ms-2"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="mt-4 alert alert-industrial shadow-sm p-4 border-0">
                <div class="d-flex">
                    <div class="me-3">
                        <i class="fas fa-lightbulb fa-2x text-warning"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1">Informasi Alur Input</h6>
                        <p class="small mb-0 opacity-75">Sistem menggunakan alur bertahap. Setelah informasi dasar (metadata) disimpan, Anda akan diarahkan ke modul <strong>File Manager</strong> untuk mengunggah dokumen (PDF, PPT, atau Video) yang akan diakses oleh mahasiswa.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        ClassicEditor
            .create(document.querySelector('#editor'), {
                toolbar: [ 'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', '|', 'undo', 'redo' ]
            })
            .catch(error => { console.error(error); });
    });
</script>
@endsection