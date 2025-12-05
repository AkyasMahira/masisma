@extends('layouts.public')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<style>
    :root {
        --primary-color: #7c1316;
        --primary-light: #fcebeb;
        --text-dark: #2c3e50;
        --text-muted: #6c757d;
        --border-color: #e0e0e0;
    }

    body {
        background-color: #f0f2f5;
        font-family: 'Poppins', sans-serif;
        color: var(--text-dark);
        padding-top: 20px;
    }

    .form-container {
        max-width: 700px;
        margin: 0 auto;
        padding-bottom: 80px;
    }

    /* --- HEADER CARD --- */
    .header-card {
        background: white;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        margin-bottom: 25px;
        border-top: 5px solid var(--primary-color);
        position: relative;
    }

    .banner-wrapper {
        width: 100%;
        height: 220px;
        background-color: var(--primary-light);
        position: relative;
        overflow: hidden;
    }

    .banner-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .header-content {
        padding: 30px;
    }

    .form-title {
        font-size: 28px;
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 10px;
        line-height: 1.3;
    }

    .form-meta {
        font-size: 14px;
        color: var(--text-muted);
        background: #f8f9fa;
        padding: 10px 15px;
        border-radius: 8px;
        display: inline-block;
        margin-bottom: 20px;
        border: 1px solid var(--border-color);
    }

    .form-description {
        font-size: 15px;
        line-height: 1.6;
        color: #4a5568;
    }

    /* --- INPUT CARDS --- */
    .input-card {
        background: white;
        border-radius: 16px;
        padding: 25px 30px;
        margin-bottom: 20px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.02);
        border: 1px solid transparent;
        transition: all 0.3s ease;
    }

    .input-card:hover {
        box-shadow: 0 10px 20px rgba(0,0,0,0.05);
    }

    .input-card:focus-within {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 4px rgba(124, 19, 22, 0.05);
        transform: translateY(-2px);
    }

    .input-label {
        font-size: 15px;
        font-weight: 600;
        color: var(--text-dark);
        margin-bottom: 12px;
        display: block;
    }

    .input-label i {
        color: var(--primary-color);
        width: 20px;
        text-align: center;
        margin-right: 8px;
    }

    .req-star {
        color: #e53e3e;
        margin-left: 3px;
    }

    /* --- MODERN FORM CONTROLS --- */
    .form-control-ultra {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid var(--border-color);
        border-radius: 10px;
        font-size: 14px;
        color: var(--text-dark);
        background-color: #fff;
        transition: all 0.3s;
    }

    .form-control-ultra:focus {
        border-color: var(--primary-color);
        outline: none;
        background-color: #fff;
    }

    .form-control-ultra::placeholder {
        color: #cbd5e0;
    }

    /* --- CHECKBOX & RADIO MANUSIABLE --- */
    .checkbox-group {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .checkbox-item {
        position: relative;
    }

    .checkbox-item input[type="checkbox"],
    .checkbox-item input[type="radio"] {
        display: none;
    }

    .checkbox-label {
        display: flex;
        align-items: center;
        padding: 12px 15px;
        border: 1px solid var(--border-color);
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.2s;
        font-size: 14px;
        background: white;
    }

    .checkbox-label:hover {
        background-color: #fafafa;
    }

    .checkbox-item input:checked + .checkbox-label {
        border-color: var(--primary-color);
        background-color: var(--primary-light);
        color: var(--primary-color);
        font-weight: 600;
    }

    .checkbox-icon {
        width: 20px;
        height: 20px;
        border: 2px solid #cbd5e0;
        border-radius: 4px;
        margin-right: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        color: transparent;
    }

    .checkbox-item input:checked + .checkbox-label .checkbox-icon {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
        color: white;
    }

    /* --- UPLOAD ZONE --- */
    .upload-zone {
        border: 2px dashed var(--border-color);
        border-radius: 12px;
        padding: 30px;
        text-align: center;
        background: #fafafa;
        cursor: pointer;
        transition: all 0.3s;
        position: relative;
    }

    .upload-zone:hover {
        border-color: var(--primary-color);
        background: var(--primary-light);
    }

    .upload-zone input[type="file"] {
        position: absolute;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        opacity: 0;
        cursor: pointer;
    }

    /* --- BUTTON --- */
    .btn-submit-ultra {
        background: linear-gradient(135deg, #7c1316 0%, #a31d21 100%);
        color: white;
        border: none;
        border-radius: 12px;
        padding: 16px 40px;
        font-weight: 600;
        font-size: 16px;
        width: 100%;
        box-shadow: 0 4px 15px rgba(124, 19, 22, 0.3);
        transition: all 0.3s;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }

    .btn-submit-ultra:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(124, 19, 22, 0.4);
    }

    .footer-text {
        text-align: center;
        margin-top: 30px;
        font-size: 12px;
        color: #a0aec0;
    }

    .alert-peraturan {
        background: #fff8e1;
        border-left: 4px solid #ffc107;
        color: #856404;
        border-radius: 8px;
        padding: 15px;
        font-size: 14px;
        margin-bottom: 25px;
    }
</style>

<div class="form-container">

    <div class="header-card animate__animated animate__fadeInDown">
        @if($form->banner_path)
        <div class="banner-wrapper">
            <img src="{{ asset('storage/' . $form->banner_path) }}" alt="Banner" class="banner-img">
        </div>
        @endif

        <div class="header-content">
            <h1 class="form-title">{{ $form->judul }}</h1>

            <div class="form-meta">
                <i class="far fa-calendar-alt mr-2"></i>
                {{ \Carbon\Carbon::parse($form->tanggal_pelaksanaan)->isoFormat('D MMMM Y') }}
            </div>

            <div class="form-description">
                {!! nl2br(e($form->keterangan)) !!}
            </div>
        </div>
        <div style="height: 4px; background: linear-gradient(90deg, #7c1316 0%, #ff4b4b 100%);"></div>
    </div>

    @if($form->peraturan)
    <div class="animate__animated animate__fadeInUp">
        <div class="alert-peraturan shadow-sm">
            <h6 class="font-weight-bold"><i class="fas fa-exclamation-triangle mr-2"></i> Peraturan & Komitmen</h6>
            <div style="white-space: pre-line;">{{ $form->peraturan }}</div>
        </div>
    </div>
    @endif

    <form action="{{ route('diklat.public.register', $form->public_link) }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="animate__animated animate__fadeInUp" style="animation-delay: 0.1s;">

            <div class="input-card">
                <label class="input-label"><i class="fas fa-user"></i> Nama Lengkap (Beserta Gelar) <span class="req-star">*</span></label>
                <input type="text" name="nama_lengkap" class="form-control-ultra" placeholder="Contoh: Dr. Budi Santoso, Sp.PD" required>
            </div>

            <div class="input-card">
                <label class="input-label"><i class="fas fa-graduation-cap"></i> Gelar (Jika dipisah)</label>
                <input type="text" name="gelar" class="form-control-ultra" placeholder="Contoh: S.Kom, M.M.">
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="input-card h-100">
                        <label class="input-label"><i class="fas fa-map-pin"></i> Tempat Lahir <span class="req-star">*</span></label>
                        <input type="text" name="tempat_lahir" class="form-control-ultra" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="input-card h-100">
                        <label class="input-label"><i class="far fa-calendar"></i> Tanggal Lahir <span class="req-star">*</span></label>
                        <input type="date" name="tanggal_lahir" class="form-control-ultra" required>
                    </div>
                </div>
            </div>

            <div class="input-card">
                <label class="input-label"><i class="fas fa-id-card"></i> NIK <span class="req-star">*</span></label>
                <input type="number" name="nik" class="form-control-ultra" placeholder="16 digit NIK KTP" required>
            </div>

            <div class="input-card">
                <label class="input-label"><i class="fas fa-envelope"></i> Email Plataran Sehat <span class="req-star">*</span></label>
                <input type="email" name="email" class="form-control-ultra" placeholder="email@kemkes.go.id" required>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="input-card">
                        <label class="input-label"><i class="fas fa-id-badge"></i> NIP (Opsional)</label>
                        <input type="number" name="nip" class="form-control-ultra">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="input-card">
                        <label class="input-label"><i class="fas fa-medal"></i> Pangkat/Golongan</label>
                        <input type="text" name="pangkat_golongan" class="form-control-ultra">
                    </div>
                </div>
            </div>

            <div class="input-card">
                <label class="input-label"><i class="fas fa-briefcase"></i> Jabatan <span class="req-star">*</span></label>
                <input type="text" name="jabatan" class="form-control-ultra" required>
            </div>

            <div class="input-card">
                <label class="input-label"><i class="fas fa-building"></i> Instansi Bekerja <span class="req-star">*</span></label>
                <input type="text" name="instansi" class="form-control-ultra" required>
            </div>

            <div class="input-card">
                <label class="input-label"><i class="fas fa-home"></i> Alamat Lengkap <span class="req-star">*</span></label>
                <textarea name="alamat" class="form-control-ultra" rows="3" required></textarea>
            </div>

            <div class="input-card">
                <label class="input-label"><i class="fab fa-whatsapp"></i> No. HP (WhatsApp) <span class="req-star">*</span></label>
                <input type="number" name="no_hp" class="form-control-ultra" placeholder="08xxxxxxxxxx" required>
            </div>
        </div>

        <div class="input-card animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
            <label class="input-label"><i class="fas fa-list-ul"></i> Pilih Pelatihan <span class="req-star">*</span></label>
            <p class="text-muted small mb-3">Anda dapat memilih lebih dari satu pelatihan.</p>

            <div class="checkbox-group">
                @foreach($form->opsi_pelatihan as $index => $opsi)
                <div class="checkbox-item">
                    <input type="checkbox" name="pilihan_pelatihan[]" value="{{ $opsi }}" id="pelatihan-{{ $index }}">
                    <label class="checkbox-label" for="pelatihan-{{ $index }}">
                        <div class="checkbox-icon"><i class="fas fa-check" style="font-size: 10px;"></i></div>
                        {{ $opsi }}
                    </label>
                </div>
                @endforeach
            </div>
        </div>

        <div class="input-card animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
            <label class="input-label"><i class="fas fa-map-marker-alt"></i> Pilih Tempat Menginap <span class="req-star">*</span></label>

            <div class="checkbox-group">
                @foreach($form->opsi_tempat as $index => $opsi)
                <div class="checkbox-item">
                    <input type="checkbox" name="pilihan_tempat[]" value="{{ $opsi }}" id="tempat-{{ $index }}">
                    <label class="checkbox-label" for="tempat-{{ $index }}">
                        <div class="checkbox-icon"><i class="fas fa-check" style="font-size: 10px;"></i></div>
                        {{ $opsi }}
                    </label>
                </div>
                @endforeach
            </div>
        </div>

        <div class="input-card animate__animated animate__fadeInUp" style="animation-delay: 0.3s;">
            <label class="input-label"><i class="fas fa-tshirt"></i> Ukuran Kaos <span class="req-star">*</span></label>

            <div class="row">
                @foreach(['S', 'M', 'L', 'XL', 'XXL', 'XXXL'] as $size)
                <div class="col-4 col-md-2 mb-2">
                    <div class="checkbox-item">
                        <input type="radio" name="ukuran_kaos" value="{{ $size }}" id="size-{{ $size }}" required>
                        <label class="checkbox-label justify-content-center" for="size-{{ $size }}">
                            {{ $size }}
                        </label>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        @if(!empty($form->pertanyaan_custom) && is_array($form->pertanyaan_custom))
            @foreach($form->pertanyaan_custom as $index => $q)
            <div class="input-card animate__animated animate__fadeInUp" style="animation-delay: 0.3s;">
                <label class="input-label"><i class="fas fa-question-circle"></i> {{ $q['judul'] }} <span class="req-star">*</span></label>

                @if(isset($q['pilihan']) && count($q['pilihan']) > 0)
                    <div class="checkbox-group">
                        @foreach($q['pilihan'] as $idx => $opt)
                        <div class="checkbox-item">
                            <input type="checkbox" name="jawaban_custom[{{ $index }}][]" value="{{ $opt }}" id="custom-{{ $index }}-{{ $idx }}">
                            <label class="checkbox-label" for="custom-{{ $index }}-{{ $idx }}">
                                <div class="checkbox-icon"><i class="fas fa-check" style="font-size: 10px;"></i></div>
                                {{ $opt }}
                            </label>
                        </div>
                        @endforeach
                    </div>
                @else
                    <input type="text" name="jawaban_custom[{{ $index }}]" class="form-control-ultra" placeholder="Jawaban Anda" required>
                @endif
            </div>
            @endforeach
        @endif

        <div class="input-card animate__animated animate__fadeInUp" style="animation-delay: 0.4s;">
            <label class="input-label"><i class="fas fa-camera"></i> Upload Pas Foto 4x6 <span class="req-star">*</span></label>

            <div class="alert alert-light border-0 p-3 mb-3" style="background-color: #fff5f5; border-radius: 10px; font-size: 13px;">
                <strong class="text-danger d-block mb-1">Ketentuan Foto:</strong>
                <ul class="mb-0 pl-3 text-muted small">
                    <li>Ukuran rasio <strong>4x6</strong></li>
                    <li>Latar belakang (Background) <strong>Wajib Merah</strong></li>
                    <li>Wajah terlihat jelas dan berpakaian rapi</li>
                    <li>Format JPG/PNG (Max 2MB)</li>
                </ul>
            </div>

            <div class="upload-zone" id="fotoZone" style="min-height: 220px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
                <input type="file" name="pas_foto" id="fotoInput" accept="image/*" required onchange="previewPublicFoto(this)">

                <div id="fotoContent">
                    <div style="width: 80px; height: 110px; background: #ffeaea; border: 2px dashed #7c1316; margin: 0 auto 15px; display: flex; align-items: center; justify-content: center; border-radius: 4px;">
                       <span style="color: #7c1316; font-size: 12px; font-weight: bold;">4x6</span>
                    </div>
                    <h6 class="text-dark">Upload Pas Foto</h6>
                    <p class="text-muted small mb-0">Klik atau seret file ke sini</p>
                </div>

                <img id="fotoPreviewImg" src="#" alt="Preview" style="display:none; width: 120px; height: 180px; object-fit: cover; border-radius: 4px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border: 2px solid #7c1316;">
            </div>
        </div>
        <div class="input-card animate__animated animate__fadeInUp" style="animation-delay: 0.4s;">
            <label class="input-label"><i class="fas fa-cloud-upload-alt"></i> Upload Bukti Pembayaran <span class="req-star">*</span></label>

            <div class="upload-zone" id="uploadZone">
                <input type="file" name="bukti_pembayaran" id="fileInput" accept=".jpg,.jpeg,.png,.pdf" required onchange="updateFileName(this)">
                <div id="uploadContent">
                    <i class="fas fa-file-invoice-dollar text-muted mb-3" style="font-size: 32px;"></i>
                    <h6 class="text-dark">Seret file ke sini atau klik untuk upload</h6>
                    <p class="text-muted small mb-0">Format: JPG, PNG, PDF (Max 2MB)</p>
                </div>
            </div>
        </div>

        <div class="mt-5 animate__animated animate__fadeInUp" style="animation-delay: 0.5s;">
            <button type="submit" class="btn-submit-ultra">
                <i class="fas fa-paper-plane mr-2"></i> Kirim Pendaftaran
            </button>

            <div class="footer-text">
                <p>Data Anda aman bersama kami. Form ini dikelola oleh Bidang Diklat.</p>
                &copy; {{ date('Y') }} Sistem Informasi Manajemen Diklat
            </div>
        </div>

    </form>
</div>

<script>
    // 1. Script Upload Bukti Pembayaran (Dokumen/File)
    function updateFileName(input) {
        const zone = document.getElementById('uploadContent');
        if (input.files && input.files[0]) {
            const file = input.files[0];
            let iconClass = 'fa-file';

            if(file.type.includes('image')) iconClass = 'fa-file-image';
            if(file.type.includes('pdf')) iconClass = 'fa-file-pdf';

            zone.innerHTML = `
                <div class="animate__animated animate__fadeIn">
                    <i class="fas ${iconClass} text-success mb-3" style="font-size: 32px;"></i>
                    <h6 class="text-success font-weight-bold">${file.name}</h6>
                    <p class="text-muted small">Siap untuk diupload</p>
                </div>
            `;
            document.getElementById('uploadZone').style.borderColor = '#28a745';
            document.getElementById('uploadZone').style.backgroundColor = '#f0fff4';
        }
    }

    // 2. Script Khusus Preview Pas Foto (Image Only)
    function previewPublicFoto(input) {
        const zone = document.getElementById('fotoContent');
        const img = document.getElementById('fotoPreviewImg');
        const container = document.getElementById('fotoZone');

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                img.src = e.target.result;
                img.style.display = 'block';
                zone.style.display = 'none'; // Sembunyikan text placeholder

                // Ubah style container biar rapi
                container.style.borderColor = '#7c1316';
                container.style.backgroundColor = '#fff';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection
