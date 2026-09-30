@extends('layouts.app')

@section('title', 'Tambah Kompetensi Baru')

@section('content')
    <style>
        :root { --custom-maroon: #7c1316; --custom-maroon-light: #a3191d; --text-dark: #2c3e50; --card-radius: 16px; --transition: 0.3s ease; }
        .form-card { border: none; border-radius: var(--card-radius); box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08); background: #fff; overflow: hidden; }
        .card-header-custom { background-color: var(--custom-maroon); padding: 1.5rem; color: white; border-bottom: 4px solid var(--custom-maroon-light); }
        .form-label { font-weight: 600; color: var(--text-dark); font-size: 0.9rem; margin-bottom: 0.5rem; }
        .input-group-text { background-color: #f8f9fa; border-right: none; color: var(--custom-maroon); border-top-left-radius: 10px; border-bottom-left-radius: 10px; border-color: #dee2e6; }
        .form-control { border-left: none; border-radius: 0 10px 10px 0; padding: 0.7rem 1rem; border-color: #dee2e6; box-shadow: none !important; transition: border-color 0.2s; color: var(--text-dark); }
        .form-control.textarea-custom { border-left: 1px solid #dee2e6; border-radius: 10px; min-height: 120px; }
        .form-control:focus { border-color: var(--custom-maroon-light); }
        .btn-maroon { background-color: var(--custom-maroon); color: white; border: none; padding: 0.8rem 2rem; border-radius: 50px; font-weight: 600; transition: var(--transition); box-shadow: 0 4px 15px rgba(124, 19, 22, 0.2); }
        .btn-maroon:hover { background-color: var(--custom-maroon-light); transform: translateY(-2px); color: white; }
        .btn-light-custom { background: #fff; border: 1px solid #dee2e6; color: var(--text-dark); border-radius: 50px; padding: 0.8rem 1.5rem; font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem; text-decoration: none; }
        .btn-light-custom:hover { background: #f8f9fa; color: var(--custom-maroon); }
        .animate-up { animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards; opacity: 0; transform: translateY(20px); }
        @keyframes fadeInUp { to { opacity: 1; transform: translateY(0); } }
    </style>

    <div class="row animate-up">
        <div class="col-12">
            <div class="form-card">
                <div class="card-header-custom">
                    <h4 class="mb-0 fw-bold"><i class="bi bi-award me-2"></i> Tambah Kompetensi Baru</h4>
                    <p class="mb-0 small opacity-75">Masukkan data standar kompetensi untuk digunakan pada kegiatan.</p>
                </div>

                <div class="card-body p-4 p-md-5">
                    @if ($errors->any())
                        <div class="alert alert-danger rounded-3 shadow-sm mb-4">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                                <strong class="mb-0">Periksa Inputan!</strong>
                            </div>
                            <ul class="mb-0 small ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.master_kompetensi.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-4">
                            <label for="nama_kompetensi" class="form-label">Nama Kompetensi <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-star"></i></span>
                                <input type="text" name="nama_kompetensi" id="nama_kompetensi" class="form-control @error('nama_kompetensi') is-invalid @enderror" 
                                       value="{{ old('nama_kompetensi') }}" placeholder="Contoh: Mampu menerapkan Etika Komunikasi" required>
                            </div>
                            @error('nama_kompetensi') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-4">
                            <label for="deskripsi_default" class="form-label">Deskripsi / Standar (Opsional)</label>
                            <textarea name="deskripsi_default" id="deskripsi_default" class="form-control textarea-custom @error('deskripsi_default') is-invalid @enderror" 
                                      placeholder="Tambahkan detail atau penjelasan standar untuk kompetensi ini...">{{ old('deskripsi_default') }}</textarea>
                            @error('deskripsi_default') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <hr class="my-4 border-light">

                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('admin.master_kompetensi.index') }}" class="btn-light-custom shadow-sm">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-maroon">
                                Simpan Data <i class="bi bi-check-lg ms-2"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection