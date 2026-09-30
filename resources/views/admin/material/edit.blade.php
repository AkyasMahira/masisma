@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    :root {
        --maroon-slg: #7c1316;
        --maroon-light: #a3191d;
        --bg-light: #f4f6f9;
    }

    body { background-color: var(--bg-light); }

    /* Header Section - Konsisten dengan modul lain */
    .header-card {
        background: white;
        border-radius: 8px;
        border-left: 5px solid var(--maroon-slg);
        padding: 20px;
        margin-bottom: 25px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    /* Card Form Styling */
    .card-custom {
        border: none;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        background: white;
        overflow: hidden;
    }

    .card-header-industrial {
        background-color: #fafbfc;
        border-bottom: 1px solid #eee;
        padding: 20px 25px;
    }

    .form-label {
        font-weight: 700;
        color: #333;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Input Styling */
    .form-control-custom {
        border-radius: 8px;
        border: 1px solid #dcdde1;
        padding: 12px 15px;
        transition: 0.3s;
        font-size: 0.95rem;
    }
    .form-control-custom:focus {
        border-color: var(--maroon-slg);
        box-shadow: 0 0 0 0.25rem rgba(124, 19, 22, 0.1);
    }

    /* Button Styling */
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
        transition: 0.2s;
    }
    .btn-outline-secondary-custom:hover {
        background: #f8f9fa;
        color: #333;
    }

    .input-group-text-custom {
        background-color: #f8f9fa;
        border-color: #dcdde1;
        color: var(--maroon-slg);
    }

    .section-divider {
        height: 1px;
        background: #eee;
        margin: 20px 0;
    }

    /* Tinggi Text Editor CKEditor */
    .ck-editor__editable_inline {
        min-height: 200px;
    }
</style>

<div class="">
    <div class="header-card d-flex align-items-center justify-content-between">
        <div>
            <h4 class="fw-bold mb-0 text-dark">Edit Informasi Materi</h4>
            <small class="text-muted">Perbarui metadata dan informasi dasar kurikulum orientasi.</small>
        </div>
        <a href="{{ route('admin.materi.index') }}" class="btn btn-outline-secondary-custom shadow-sm">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card card-custom">
                <div class="card-header-industrial">
                    <h6 class="fw-bold mb-0 text-maroon"><i class="fas fa-edit me-2"></i> Formulir Perubahan Data</h6>
                </div>
                
                <form action="{{ route('admin.materi.update', $material->id) }}" method="POST">
                    @csrf 
                    @method('PUT')
                    
                    <div class="card-body p-4 p-md-5">
                        <div class="row g-4">
                            <div class="col-md-9">
                                <label class="form-label">Judul Materi <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control form-control-custom" 
                                       value="{{ $material->title }}" required placeholder="Contoh: Etika Pelayanan Pasien">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Urutan <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text input-group-text-custom"><i class="fas fa-sort-numeric-down"></i></span>
                                    <input type="number" name="order" class="form-control form-control-custom" 
                                           value="{{ $material->order }}" required>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Sub-Judul / Label Singkat</label>
                                <input type="text" name="subtitle" class="form-control form-control-custom" 
                                       value="{{ $material->subtitle }}" placeholder="Contoh: Modul Dasar 1">
                            </div>

                            <div class="col-12">
                                <label class="form-label">Deskripsi Materi</label>
                                <textarea name="description" id="editor" class="form-control form-control-custom" rows="4" 
                                          placeholder="Tuliskan ringkasan materi...">{{ $material->description }}</textarea>
                            </div>

                            <div class="col-12">
                                <div class="p-4 rounded-4 border" style="background-color: #fafbfc;">
                                    <label class="form-label text-maroon"><i class="fas fa-link me-1"></i> Tautan Eksternal (Opsional)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-globe text-muted"></i></span>
                                        <input type="url" name="external_link" class="form-control form-control-custom border-start-0" 
                                               value="{{ $material->external_link }}" placeholder="https://youtube.com/... atau https://drive.google.com/...">
                                    </div>
                                    <div class="form-text text-muted mt-2 small">
                                        <i class="fas fa-info-circle me-1"></i> Biarkan kosong jika materi hanya berupa file unggahan internal.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-light border-0 p-4 d-flex justify-content-between align-items-center">
                        <a href="{{ route('admin.materi.index') }}" class="fw-bold text-muted text-decoration-none px-3">
                            <i class="fas fa-times me-1"></i> Batalkan Perubahan
                        </a>
                        <button type="submit" class="btn btn-maroon shadow-sm px-5">
                            <i class="fas fa-save me-2"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>

            <div class="mt-4 p-4 rounded-4 border-0 shadow-sm" style="background-color: #2c3e50; color: #ecf0f1;">
                <div class="d-flex align-items-start">
                    <i class="fas fa-exclamation-triangle fa-2x text-warning me-3 mt-1"></i>
                    <div>
                        <h6 class="fw-bold mb-1">Peringatan Modifikasi</h6>
                        <p class="small mb-0 opacity-75">
                            Mengubah informasi dasar materi tidak akan menghapus file yang sudah diunggah sebelumnya. 
                            Jika Anda ingin mengelola file (menambah/menghapus dokumen), silakan kembali ke halaman utama dan pilih ikon <strong>Kelola File</strong> pada baris materi yang bersangkutan.
                        </p>
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