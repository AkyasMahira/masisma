@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    :root {
        --maroon-slg: #7c1316;
        --maroon-light: #a3191d;
        --bg-light: #f4f6f9;
        --industrial-dark: #2c3e50;
    }

    body { background-color: var(--bg-light); }

    /* Header Section */
    .header-card {
        background: white;
        border-radius: 8px;
        border-left: 5px solid var(--maroon-slg);
        padding: 20px;
        margin-bottom: 25px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    /* Table Styling */
    .table-container {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        background: white;
    }
    .table thead th {
        background-color: var(--maroon-slg) !important;
        color: white !important;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 1px;
        padding: 15px;
        border: none;
    }
    .table tbody td {
        padding: 18px 15px;
        vertical-align: middle;
        border-bottom: 1px solid #f0f0f0;
    }

    /* Button Styling */
    .btn-maroon {
        background-color: var(--maroon-slg);
        color: white;
        border-radius: 50px;
        font-weight: 700;
        padding: 10px 25px;
        border: none;
        transition: 0.3s;
    }
    .btn-maroon:hover {
        background-color: var(--maroon-light);
        color: white;
        box-shadow: 0 4px 12px rgba(124, 19, 22, 0.3);
    }

    /* Modal Styling */
    .modal-content-custom {
        border-radius: 15px;
        border: none;
        overflow: hidden;
    }
    .modal-header-maroon {
        background-color: var(--maroon-slg);
        color: white;
        padding: 20px;
    }
    .form-label-bold {
        font-weight: 700;
        color: var(--industrial-dark);
        font-size: 0.8rem;
        text-transform: uppercase;
        margin-bottom: 8px;
    }

    /* File Type Badges */
    .badge-file {
        padding: 6px 12px;
        border-radius: 6px;
        font-weight: 700;
        font-size: 0.7rem;
    }

    /* Penyesuaian Tinggi Editor CKEditor */
    .ck-editor__editable_inline {
        min-height: 150px;
    }
</style>

<div class="container-fluid py-4">
    <div class="header-card d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-bold mb-0 text-dark">Manajemen Berkas Materi</h4>
            <p class="text-muted mb-0 small">Materi Utama: <span class="text-maroon fw-bold">{{ $material->title }}</span></p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-maroon shadow-sm" data-bs-toggle="modal" data-bs-target="#uploadFileModal">
                <i class="fas fa-cloud-upload-alt me-1"></i> Unggah File Baru
            </button>
            <a href="{{ route('admin.materi.index') }}" class="btn btn-outline-secondary px-4 rounded-pill fw-bold">Selesai</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        </div>
    @endif

    <div class="table-container shadow-sm bg-white">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th class="px-4">NAMA BERKAS DIGITAL</th>
                        <th class="px-4">DESKRIPSI</th>
                        <th class="text-center" width="150">TIPE</th>
                        <th class="text-center" width="150">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($material->files as $file)
                    <tr>
                        <td class="px-4">
                            <div class="d-flex align-items-center">
                                <div class="me-3 fs-4 text-maroon">
                                    @if(in_array(strtolower($file->file_type), ['pdf', 'doc', 'docx']))
                                        <i class="fas fa-file-alt"></i>
                                    @elseif(in_array(strtolower($file->file_type), ['mp4', 'mkv', 'avi']))
                                        <i class="fas fa-video"></i>
                                    @elseif(in_array(strtolower($file->file_type), ['jpg', 'jpeg', 'png']))
                                        <i class="fas fa-image"></i>
                                    @else
                                        <i class="fas fa-file-circle-check"></i>
                                    @endif
                                </div>
                                <div>
                                    <span class="fw-bold text-dark d-block">{{ $file->file_name }}</span>
                                    <small class="text-muted" style="font-size: 0.7rem;">Diupload: {{ $file->created_at->format('d/m/Y H:i') }}</small>
                                </div>
                            </div>
                        </td>
                        <td class="px-4">
                            <div class="text-secondary small" style="max-height: 80px; overflow-y: auto;">
                                {!! $file->description ?? '<i class="text-muted">Tidak ada deskripsi</i>' !!}
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-light text-black border border-maroon badge-file">
                                {{ strtoupper($file->file_type) }}
                            </span>
                        </td>
                        <td class="text-center">
                            <form action="{{ route('admin.materi.files.destroy', $file->id) }}" method="POST" onsubmit="return confirm('Hapus berkas ini secara permanen?')">
                                @csrf 
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-light border text-danger rounded-pill px-3">
                                    <i class="fas fa-trash-alt me-1"></i> Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5">
                            <i class="fas fa-folder-open fa-3x text-muted mb-3 opacity-25"></i>
                            <h6 class="text-muted fw-bold">Belum ada berkas yang diunggah untuk materi ini.</h6>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="uploadFileModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg"> <div class="modal-content modal-content-custom shadow-lg">
            <div class="modal-header modal-header-maroon border-0">
                <h5 class="modal-title fw-bold"><i class="fas fa-file-import me-2"></i>Unggah Berkas Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.materi.files.store', $material->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label-bold">Label Nama Berkas</label>
                            <input type="text" name="file_name" class="form-control" placeholder="Contoh: Video Penjelasan Budaya RS" required>
                            <div class="form-text small">Nama ini yang akan muncul di halaman mahasiswa.</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label-bold">Pilih File Sumber</label>
                            <input type="file" name="file_upload" class="form-control" required>
                            <div class="form-text small text-maroon"><i class="fas fa-info-circle me-1"></i> Mendukung format Video, PDF, PPT, dan Gambar.</div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label-bold">Deskripsi File (Opsional)</label>
                        <textarea name="description" id="editor" class="form-control" rows="4" placeholder="Tuliskan penjelasan singkat mengenai isi file ini..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 p-4">
                    <button type="button" class="btn btn-link text-muted text-decoration-none fw-bold" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-maroon px-5 shadow-sm">
                        <i class="fas fa-upload me-1"></i> MULAI UPLOAD
                    </button>
                </div>
            </form>
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
            .catch(error => {
                console.error(error);
            });
    });
</script>
@endsection