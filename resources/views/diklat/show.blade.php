@extends('layouts.app')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<style>
    :root {
        --primary-color: #7c1316;
        --primary-light: #fcebeb;
        --text-dark: #2c3e50;
        --border-soft: #e2e8f0;
    }

    body {
        background-color: #f8f9fc;
        font-family: 'Poppins', sans-serif;
        color: var(--text-dark);
    }

    .gf-container {
        max-width: 720px;
        margin: 0 auto;
        padding-bottom: 80px;
        padding-top: 30px;
    }

    /* --- HEADER CARD --- */
    .header-card {
        background: white;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        margin-bottom: 25px;
        position: relative;
        border-top: 5px solid var(--primary-color);
    }

    .banner-img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        background-color: var(--primary-light);
    }

    .header-content {
        padding: 30px;
    }

    .form-title {
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 10px;
        color: var(--primary-color);
    }

    .form-meta {
        font-size: 14px;
        background: #f1f5f9;
        padding: 8px 15px;
        border-radius: 8px;
        display: inline-block;
        margin-bottom: 20px;
        color: #64748b;
        font-weight: 500;
    }

    /* --- INPUT CARDS --- */
    .input-card {
        background: white;
        border-radius: 16px;
        padding: 25px;
        margin-bottom: 20px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.02);
        border: 1px solid transparent;
        transition: all 0.3s ease;
    }

    .input-card:hover {
        box-shadow: 0 10px 20px rgba(0,0,0,0.05);
        transform: translateY(-2px);
    }

    .input-card:focus-within {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 4px rgba(124, 19, 22, 0.05);
    }

    .input-label {
        font-weight: 600;
        font-size: 15px;
        margin-bottom: 12px;
        display: block;
        color: #1e293b;
    }

    .input-label i {
        color: var(--primary-color);
        width: 20px;
        text-align: center;
        margin-right: 8px;
    }

    .req-star { color: #ef4444; }

    /* --- MODERN INPUTS --- */
    .form-control-ultra {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid var(--border-soft);
        border-radius: 10px;
        font-size: 14px;
        transition: all 0.3s;
        background-color: #fff;
    }

    .form-control-ultra:focus {
        border-color: var(--primary-color);
        outline: none;
    }

    /* --- MANUSIABLE CHECKBOX & RADIO --- */
    .option-group {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .option-item {
        position: relative;
    }

    .option-item input { display: none; }

    .option-label {
        display: flex;
        align-items: center;
        padding: 12px 15px;
        border: 1px solid var(--border-soft);
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.2s;
        font-size: 14px;
        background: white;
    }

    .option-label:hover { background-color: #f8fafc; }

    .option-item input:checked + .option-label {
        border-color: var(--primary-color);
        background-color: var(--primary-light);
        color: var(--primary-color);
        font-weight: 600;
    }

    .check-icon {
        width: 20px;
        height: 20px;
        border: 2px solid #cbd5e0;
        border-radius: 4px;
        margin-right: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        transition: all 0.2s;
    }

    .option-item input:checked + .option-label .check-icon {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }

    /* --- UPLOAD ZONE --- */
    .upload-zone {
        border: 2px dashed #cbd5e0;
        border-radius: 12px;
        padding: 30px;
        text-align: center;
        background: #f8fafc;
        cursor: pointer;
        transition: all 0.3s;
        position: relative;
    }
    .upload-zone:hover {
        border-color: var(--primary-color);
        background: var(--primary-light);
    }
    .upload-zone input {
        position: absolute; width: 100%; height: 100%; top: 0; left: 0; opacity: 0; cursor: pointer;
    }

    /* --- BUTTON --- */
    .btn-submit-ultra {
        background: linear-gradient(135deg, #7c1316 0%, #a31d21 100%);
        color: white;
        border: none;
        border-radius: 12px;
        padding: 15px 40px;
        font-weight: 600;
        font-size: 16px;
        width: 100%;
        box-shadow: 0 4px 15px rgba(124, 19, 22, 0.3);
        transition: all 0.3s;
        cursor: pointer;
    }
    .btn-submit-ultra:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(124, 19, 22, 0.4);
    }
</style>

<div class="gf-container">

    <div class="header-card animate__animated animate__fadeInDown">
        @if($form->banner_path)
            <img src="{{ asset('storage/' . $form->banner_path) }}" alt="Banner" class="banner-img">
        @endif

        <div class="header-content">
            <h1 class="form-title">{{ $form->judul }}</h1>
            <div class="form-meta">
                <i class="far fa-calendar-alt mr-2"></i>
                {{ \Carbon\Carbon::parse($form->tanggal_pelaksanaan)->isoFormat('D MMMM Y') }}
            </div>
            <div style="font-size: 15px; color: #475569; line-height: 1.6;">
                {!! nl2br(e($form->keterangan)) !!}
            </div>
        </div>
        <div style="height: 4px; background: linear-gradient(90deg, #7c1316 0%, #ff4b4b 100%);"></div>
    </div>

    @if($form->peraturan)
    <div class="animate__animated animate__fadeInUp">
        <div class="alert alert-warning border-0 shadow-sm rounded-lg mb-4" style="background-color: #fffbeb; color: #92400e;">
            <div class="d-flex">
                <div class="mr-3"><i class="fas fa-exclamation-circle fa-lg"></i></div>
                <div>
                    <h6 class="font-weight-bold">Peraturan & Komitmen</h6>
                    <div style="white-space: pre-line; font-size: 14px;">{{ $form->peraturan }}</div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <form action="{{ route('diklat.public.register', $form->public_link) }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="animate__animated animate__fadeInUp" style="animation-delay: 0.1s;">
            <div class="input-card">
                <label class="input-label"><i class="fas fa-user"></i> Nama Lengkap <span class="req-star">*</span></label>
                <input type="text" name="nama_lengkap" class="form-control-ultra" placeholder="Nama sesuai identitas" required>
            </div>

            <div class="input-card">
                <label class="input-label"><i class="fas fa-graduation-cap"></i> Gelar</label>
                <input type="text" name="gelar" class="form-control-ultra" placeholder="Contoh: S.Kom">
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
                <input type="number" name="nik" class="form-control-ultra" placeholder="16 digit NIK" required>
            </div>

            <div class="input-card">
                <label class="input-label"><i class="fas fa-envelope"></i> Email Plataran Sehat <span class="req-star">*</span></label>
                <input type="email" name="email" class="form-control-ultra" placeholder="email@kemkes.go.id" required>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="input-card">
                        <label class="input-label"><i class="fas fa-id-badge"></i> NIP (Optional)</label>
                        <input type="text" name="nip" class="form-control-ultra">
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
                <input type="number" name="no_hp" class="form-control-ultra" placeholder="08xxxxxxxx" required>
            </div>
        </div>

        <div class="input-card animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
            <label class="input-label"><i class="fas fa-list-ul"></i> Pilih Pelatihan <span class="req-star">*</span></label>
            <p class="text-muted small mb-3">Klik kotak untuk memilih (bisa lebih dari satu).</p>

            <div class="option-group">
                @foreach($form->opsi_pelatihan as $index => $opsi)
                <div class="option-item">
                    <input type="checkbox" name="pilihan_pelatihan[]" value="{{ $opsi }}" id="pelatihan-{{ $index }}">
                    <label class="option-label" for="pelatihan-{{ $index }}">
                        <div class="check-icon"><i class="fas fa-check" style="font-size: 10px;"></i></div>
                        {{ $opsi }}
                    </label>
                </div>
                @endforeach
            </div>
        </div>

        <div class="input-card animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
            <label class="input-label"><i class="fas fa-map-marker-alt"></i> Pilih Tempat <span class="req-star">*</span></label>

            <div class="option-group">
                @foreach($form->opsi_tempat as $index => $opsi)
                <div class="option-item">
                    <input type="checkbox" name="pilihan_tempat[]" value="{{ $opsi }}" id="tempat-{{ $index }}">
                    <label class="option-label" for="tempat-{{ $index }}">
                        <div class="check-icon"><i class="fas fa-check" style="font-size: 10px;"></i></div>
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
                <div class="col-4 mb-2">
                    <div class="option-item">
                        <input type="radio" name="ukuran_kaos" value="{{ $size }}" id="size-{{ $size }}" required>
                        <label class="option-label justify-content-center font-weight-bold" for="size-{{ $size }}">
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

                @if(isset($q['pilihan']) && count($q['pilihan']) > 0 && $q['pilihan'][0] != "")
                    <div class="option-group">
                        @foreach($q['pilihan'] as $idx => $opt)
                        <div class="option-item">
                            <input type="checkbox" name="jawaban_custom[{{ $index }}][]" value="{{ $opt }}" id="custom-{{ $index }}-{{ $idx }}">
                            <label class="option-label" for="custom-{{ $index }}-{{ $idx }}">
                                <div class="check-icon"><i class="fas fa-check" style="font-size: 10px;"></i></div>
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
            <label class="input-label"><i class="fas fa-cloud-upload-alt"></i> Upload Bukti Pembayaran <span class="req-star">*</span></label>

            <div class="upload-zone" id="uploadZone">
                <input type="file" name="bukti_pembayaran" id="fileInput" accept=".jpg,.jpeg,.png,.pdf" required onchange="updateFile(this)">
                <div id="uploadContent">
                    <i class="fas fa-file-invoice-dollar text-muted mb-3" style="font-size: 32px;"></i>
                    <h6 class="text-dark">Klik atau Seret file ke sini</h6>
                    <p class="text-muted small mb-0">JPG, PNG, PDF (Max 2MB)</p>
                </div>
            </div>
        </div>

        <div class="mt-5 animate__animated animate__fadeInUp" style="animation-delay: 0.5s;">
            <button type="submit" class="btn-submit-ultra">
                <i class="fas fa-paper-plane mr-2"></i> KIRIM FORMULIR
            </button>
            <div class="text-center mt-3 small text-muted">
                Data Anda aman dan terenkripsi.
            </div>
        </div>
    </form>
</div>

<script>
    function updateFile(input) {
        const zone = document.getElementById('uploadContent');
        if (input.files && input.files[0]) {
            const file = input.files[0];
            zone.innerHTML = `
                <div class="animate__animated animate__fadeIn">
                    <i class="fas fa-check-circle text-success mb-2" style="font-size: 32px;"></i>
                    <h6 class="text-success font-weight-bold">${file.name}</h6>
                    <p class="text-muted small">Siap diupload</p>
                </div>
            `;
            document.getElementById('uploadZone').style.borderColor = '#28a745';
            document.getElementById('uploadZone').style.backgroundColor = '#f0fff4';
        }
    }
</script>
@endsection
