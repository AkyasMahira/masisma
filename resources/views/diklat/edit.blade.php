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
        --border-soft: #eef2f7;
        --shadow-card: 0 5px 20px rgba(0,0,0,0.05);
    }

    body {
        background-color: #f8f9fc;
        font-family: 'Poppins', sans-serif;
    }

    .edit-container {
        max-width: 850px;
        margin: 0 auto;
        padding-bottom: 80px;
    }

    /* Header Section */
    .header-section {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }

    .page-title {
        font-size: 26px;
        font-weight: 700;
        color: var(--text-dark);
        margin: 0;
    }

    /* Ultra Card Style */
    .card-ultra {
        background: white;
        border: none;
        border-radius: 16px;
        box-shadow: var(--shadow-card);
        padding: 30px;
        margin-bottom: 25px;
        position: relative;
        overflow: hidden;
        border-top: 4px solid var(--primary-color);
        transition: transform 0.3s ease;
    }

    .card-ultra:hover {
        transform: translateY(-2px);
    }

    .card-section-title {
        font-size: 16px;
        font-weight: 700;
        color: var(--primary-color);
        margin-bottom: 20px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid var(--primary-light);
        padding-bottom: 10px;
        display: inline-block;
    }

    .label-ultra {
        font-size: 13px;
        font-weight: 600;
        color: #5e6e82;
        margin-bottom: 8px;
        display: block;
    }

    /* Modern Input Style (Box) */
    .form-control-ultra {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid #e0e0e0;
        border-radius: 10px;
        background-color: #fcfcfd;
        font-size: 14px;
        color: var(--text-dark);
        transition: all 0.3s;
    }

    .form-control-ultra:focus {
        border-color: var(--primary-color);
        background-color: white;
        box-shadow: 0 0 0 4px rgba(124, 19, 22, 0.1);
        outline: none;
    }

    /* Style Khusus Opsi Jawaban (Manusiable/Google Form Style) */
    .option-item {
        display: flex;
        align-items: center;
        margin-bottom: 8px;
        transition: all 0.2s;
    }

    .option-icon {
        color: #adb5bd;
        margin-right: 12px;
        font-size: 18px;
    }

    .form-control-option {
        border: none;
        border-bottom: 1px solid #e0e0e0;
        border-radius: 0;
        padding: 8px 0;
        background: transparent;
        width: 100%;
        color: var(--text-dark);
        transition: border-bottom-color 0.3s;
    }

    .form-control-option:focus {
        border-bottom-color: var(--primary-color);
        outline: none;
        background: #fdfbfb;
    }

    .btn-remove-option {
        background: transparent;
        border: none;
        color: #cbd5e0;
        margin-left: 10px;
        cursor: pointer;
        font-size: 16px;
        padding: 5px;
        transition: color 0.2s;
    }
    .btn-remove-option:hover { color: #ef4444; }

    .btn-add-answer {
        font-size: 13px;
        font-weight: 600;
        color: var(--primary-color);
        background: transparent;
        border: none;
        padding: 5px 0;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        margin-left: 30px;
        margin-top: 5px;
        transition: all 0.2s;
    }
    .btn-add-answer:hover { color: #a31d21; text-decoration: underline; }

    /* Upload Area Styling */
    .upload-area {
        border: 2px dashed #cbd5e0;
        background: #f8f9fa;
        border-radius: 12px;
        padding: 30px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s;
        position: relative;
        overflow: hidden;
    }

    .upload-area:hover {
        border-color: var(--primary-color);
        background: var(--primary-light);
    }

    /* Buttons */
    .btn-ultra {
        background: linear-gradient(135deg, #7c1316 0%, #a31d21 100%);
        color: white;
        border: none;
        border-radius: 12px;
        padding: 14px 40px;
        font-weight: 600;
        font-size: 15px;
        box-shadow: 0 4px 15px rgba(124, 19, 22, 0.3);
        transition: all 0.3s;
    }
    .btn-ultra:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(124, 19, 22, 0.4);
        color: white;
    }

    .btn-outline-back {
        background: white;
        border: 1px solid #d1d5db;
        color: #4b5563;
        padding: 10px 20px;
        border-radius: 10px;
        font-weight: 500;
        font-size: 14px;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .btn-outline-back:hover { background: #f3f4f6; color: #1f2937; text-decoration: none; }

    /* Helper Buttons */
    .btn-add-option {
        background: var(--primary-light);
        color: var(--primary-color);
        border: none;
        padding: 8px 15px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
    }
    .btn-delete-item {
        width: 38px;
        height: 38px;
        border-radius: 8px;
        background: #fee2e2;
        color: #ef4444;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }
    .btn-delete-item:hover { background: #ef4444; color: white; }
</style>

<div class="edit-container pt-4">

    <div class="header-section animate__animated animate__fadeInDown">
        <div>
            <h1 class="page-title">Edit Form Diklat</h1>
            <p class="text-muted mb-0" style="font-size: 14px;">Perbarui detail dan konfigurasi pelatihan.</p>
        </div>
        <a href="{{ route('diklat.index') }}" class="btn-outline-back">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <form action="{{ route('diklat.update', $form->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="card-ultra animate__animated animate__fadeInUp">
            <h5 class="card-section-title"><i class="far fa-file-alt mr-2"></i> Informasi Utama</h5>

            <div class="form-group mb-4">
                <label class="label-ultra">Banner Header</label>
                <div class="upload-area" onclick="document.getElementById('bannerInput').click()">
                    <input type="file" name="banner" id="bannerInput" class="d-none" accept="image/*" onchange="previewBanner(this)">

                    <div id="uploadPlaceholder" style="display: {{ $form->banner_path ? 'none' : 'block' }}">
                        <i class="fas fa-cloud-upload-alt" style="font-size: 30px; color: #a0aec0; margin-bottom: 10px;"></i>
                        <h6 style="font-size: 14px; margin-bottom: 5px; color: #4a5568;">Klik untuk ganti banner</h6>
                        <span class="text-muted small">Format: JPG, PNG (Max 2MB)</span>
                    </div>

                    <img id="bannerPreview"
                         src="{{ $form->banner_path ? asset('storage/' . $form->banner_path) : '#' }}"
                         alt="Preview"
                         style="display: {{ $form->banner_path ? 'block' : 'none' }}; width: 100%; height: 200px; object-fit: cover; border-radius: 8px;">
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="form-group mb-4">
                        <label class="label-ultra">Judul Pelatihan <span class="text-danger">*</span></label>
                        <input type="text" name="judul" class="form-control-ultra" value="{{ $form->judul }}" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group mb-4">
                        <label class="label-ultra">Tanggal Pelaksanaan <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_pelaksanaan" class="form-control-ultra" value="{{ $form->tanggal_pelaksanaan }}" required>
                    </div>
                </div>
            </div>

            <div class="form-group mb-0">
                <label class="label-ultra">Deskripsi Singkat</label>
                <textarea name="keterangan" class="form-control-ultra" rows="3">{{ $form->keterangan }}</textarea>
            </div>
        </div>

        <div class="card-ultra animate__animated animate__fadeInUp" style="animation-delay: 0.1s;">
            <h5 class="card-section-title"><i class="fas fa-gavel mr-2"></i> Peraturan & Komitmen</h5>
            <div class="form-group mb-0">
                <textarea name="peraturan" class="form-control-ultra" rows="4">{{ $form->peraturan }}</textarea>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card-ultra animate__animated animate__fadeInUp" style="animation-delay: 0.2s; height: 100%;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-section-title mb-0"><i class="fas fa-list-ul mr-2"></i> Opsi Pelatihan</h5>
                        <button type="button" class="btn-add-option" onclick="addSimpleOption('wrapper-pelatihan', 'opsi_pelatihan[]', 'Nama Pelatihan')">
                            + Tambah
                        </button>
                    </div>
                    <div id="wrapper-pelatihan">
                        @if($form->opsi_pelatihan)
                            @foreach($form->opsi_pelatihan as $opsi)
                            <div class="input-group mb-2 align-items-center">
                                <input type="text" name="opsi_pelatihan[]" class="form-control-ultra" value="{{ $opsi }}" style="border-right:0; border-radius: 10px 0 0 10px;" required>
                                <div class="input-group-append">
                                    <button class="btn-delete-item" type="button" onclick="this.closest('.input-group').remove()" style="border-radius: 0 10px 10px 0;">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            @endforeach
                        @else
                            <div class="input-group mb-2 align-items-center">
                                <input type="text" name="opsi_pelatihan[]" class="form-control-ultra" placeholder="Nama Pelatihan" required>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card-ultra animate__animated animate__fadeInUp" style="animation-delay: 0.2s; height: 100%;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-section-title mb-0"><i class="fas fa-map-marker-alt mr-2"></i> Opsi Tempat</h5>
                        <button type="button" class="btn-add-option" onclick="addSimpleOption('wrapper-tempat', 'opsi_tempat[]', 'Nama Ruangan')">
                            + Tambah
                        </button>
                    </div>
                    <div id="wrapper-tempat">
                        @if($form->opsi_tempat)
                            @foreach($form->opsi_tempat as $opsi)
                            <div class="input-group mb-2 align-items-center">
                                <input type="text" name="opsi_tempat[]" class="form-control-ultra" value="{{ $opsi }}" style="border-right:0; border-radius: 10px 0 0 10px;" required>
                                <div class="input-group-append">
                                    <button class="btn-delete-item" type="button" onclick="this.closest('.input-group').remove()" style="border-radius: 0 10px 10px 0;">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            @endforeach
                        @else
                            <div class="input-group mb-2 align-items-center">
                                <input type="text" name="opsi_tempat[]" class="form-control-ultra" placeholder="Nama Ruangan" required>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div id="customQuestionsArea" class="mt-3">
            @if(is_array($form->pertanyaan_custom))
                @foreach($form->pertanyaan_custom as $i => $q)
                <div class="card-ultra animate__animated animate__fadeIn" id="question-card-{{ $i }}">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="card-section-title mb-0 border-0 p-0 text-dark">
                            <i class="fas fa-question-circle text-muted mr-2"></i> Pertanyaan #{{ $i + 1 }}
                        </h5>
                        <button type="button" class="btn-delete-item" onclick="removeQuestion('question-card-{{ $i }}')">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>

                    <div class="form-group mb-4">
                        <input type="text" name="pertanyaan_custom[{{ $i }}][judul]"
                               class="form-control-ultra"
                               value="{{ $q['judul'] ?? '' }}"
                               placeholder="Tulis pertanyaan Anda di sini..."
                               style="font-size: 15px; font-weight: 500; background: #fafafa;" required>
                    </div>

                    <div class="form-group mb-2">
                        <label class="label-ultra mb-2">Pilihan Jawaban</label>
                        <div id="options-container-{{ $i }}">
                            @php
                                // Handling data lama (string) atau baru (array)
                                $pilihan = is_array($q['pilihan']) ? $q['pilihan'] : explode(',', $q['pilihan']);
                            @endphp

                            @foreach($pilihan as $idx => $opsi)
                            <div class="option-item">
                                <i class="far fa-circle option-icon"></i>
                                <input type="text" name="pertanyaan_custom[{{ $i }}][pilihan][]"
                                       class="form-control-option"
                                       value="{{ trim($opsi) }}"
                                       placeholder="Opsi {{ $idx + 1 }}" required>
                                @if(count($pilihan) > 1)
                                <button type="button" class="btn-remove-option" onclick="this.closest('.option-item').remove()">
                                    <i class="fas fa-times"></i>
                                </button>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <button type="button" class="btn-add-answer" onclick="addOption({{ $i }})">
                        <i class="fas fa-plus-circle"></i> Tambah Pilihan Lain
                    </button>
                </div>
                @endforeach
            @endif
        </div>

        <div class="d-flex justify-content-between align-items-center mt-5 mb-5 animate__animated animate__fadeInUp" style="animation-delay: 0.3s;">
            <button type="button" class="btn btn-outline-secondary" onclick="addQuestion()" style="border-radius: 12px; padding: 12px 25px;">
                <i class="fas fa-plus-circle mr-1"></i> Tambah Pertanyaan Lain
            </button>
            <button type="submit" class="btn-ultra btn-lg">
                <i class="fas fa-save mr-2"></i> Simpan Perubahan
            </button>
        </div>

    </form>
</div>

<script>
    // 1. Preview Banner Image
    function previewBanner(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById('bannerPreview');
                const placeholder = document.getElementById('uploadPlaceholder');
                img.src = e.target.result;
                img.style.display = 'block';
                placeholder.style.display = 'none';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // 2. Add Opsi Simple (Pelatihan & Tempat) - Box Style
    function addSimpleOption(wrapperId, inputName, placeholderText) {
        const wrapper = document.getElementById(wrapperId);
        const html = `
            <div class="input-group mb-2 align-items-center animate__animated animate__fadeIn">
                <input type="text" name="${inputName}" class="form-control-ultra" placeholder="${placeholderText}" style="border-right:0; border-radius: 10px 0 0 10px;" required>
                <div class="input-group-append">
                    <button class="btn-delete-item" type="button" onclick="this.closest('.input-group').remove()" style="border-radius: 0 10px 10px 0;">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>`;
        wrapper.insertAdjacentHTML('beforeend', html);
    }

    // Variable Global untuk Index Pertanyaan
    let qIndex = {{ is_array($form->pertanyaan_custom) ? count($form->pertanyaan_custom) : 0 }};

    // 3. Tambah Pertanyaan Baru (Card Style)
    function addQuestion() {
        const container = document.getElementById('customQuestionsArea');
        const uniqueId = Date.now(); // ID unik untuk DOM manipulation
        const currentIndex = qIndex; // Index untuk name array

        const html = `
        <div class="card-ultra animate__animated animate__fadeInUp" id="question-card-${uniqueId}">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-section-title mb-0 border-0 p-0 text-dark">
                    <i class="fas fa-question-circle text-muted mr-2"></i> Pertanyaan Baru
                </h5>
                <button type="button" class="btn-delete-item" onclick="removeQuestion('question-card-${uniqueId}')">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </div>

            <div class="form-group mb-4">
                <input type="text" name="pertanyaan_custom[${currentIndex}][judul]"
                       class="form-control-ultra"
                       placeholder="Contoh: Ukuran Baju?"
                       style="font-size: 15px; font-weight: 500; background: #fafafa;" required>
            </div>

            <div class="form-group mb-2">
                <label class="label-ultra mb-2">Pilihan Jawaban</label>
                <div id="options-container-${uniqueId}">
                    <div class="option-item">
                        <i class="far fa-circle option-icon"></i>
                        <input type="text" name="pertanyaan_custom[${currentIndex}][pilihan][]" class="form-control-option" placeholder="Opsi 1" required>
                    </div>
                    <div class="option-item">
                        <i class="far fa-circle option-icon"></i>
                        <input type="text" name="pertanyaan_custom[${currentIndex}][pilihan][]" class="form-control-option" placeholder="Opsi 2" required>
                        <button type="button" class="btn-remove-option" onclick="this.closest('.option-item').remove()"><i class="fas fa-times"></i></button>
                    </div>
                </div>
            </div>

            <button type="button" class="btn-add-answer" onclick="addOption('${uniqueId}', ${currentIndex})">
                <i class="fas fa-plus-circle"></i> Tambah Pilihan Lain
            </button>
        </div>`;

        container.insertAdjacentHTML('beforeend', html);
        qIndex++;

        setTimeout(() => {
            document.getElementById(`question-card-${uniqueId}`).scrollIntoView({ behavior: 'smooth', block: 'center' });
        }, 100);
    }

    // 4. Tambah Opsi Jawaban (Radio Style)
    function addOption(containerId, inputIndex) {
        // Handle ID dari PHP (integer) atau JS (timestamp string)
        const targetId = `options-container-${containerId}`;
        const wrapper = document.getElementById(targetId);

        // Fallback index
        if (inputIndex === undefined) inputIndex = containerId;

        const html = `
            <div class="option-item animate__animated animate__fadeIn">
                <i class="far fa-circle option-icon"></i>
                <input type="text" name="pertanyaan_custom[${inputIndex}][pilihan][]" class="form-control-option" placeholder="Opsi Baru" required>
                <button type="button" class="btn-remove-option" onclick="this.closest('.option-item').remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>`;

        wrapper.insertAdjacentHTML('beforeend', html);

        // Auto focus
        const newInputs = wrapper.querySelectorAll('input');
        if(newInputs.length > 0) newInputs[newInputs.length - 1].focus();
    }

    // 5. Hapus Pertanyaan
    function removeQuestion(cardId) {
        const card = document.getElementById(cardId);
        card.classList.remove('animate__fadeInUp');
        card.classList.add('animate__fadeOut');
        setTimeout(() => {
            card.remove();
        }, 300);
    }
</script>
@endsection
