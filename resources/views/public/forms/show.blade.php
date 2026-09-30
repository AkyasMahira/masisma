<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $form->title }} - Sindikat RSUD Simpang Lima Gumul</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css"/>
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

    <style>
        :root {
            --primary: #7c1316;
            --primary-light: #fff5f5;
            --bg-body: #f0f4f8;
            --text-main: #1a202c;
            --text-muted: #718096;
            --white: #ffffff;
            --radius: 24px;
            --shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.04);
        }
/* Styling Premium untuk Input File */
.file-upload-wrapper {
    position: relative;
    background: #ffffff;
    border: 2px dashed #e2e8f0;
    border-radius: 18px;
    padding: 30px 20px;
    text-align: center;
    transition: all 0.3s ease;
    cursor: pointer;
    overflow: hidden;
}

.file-upload-wrapper:hover {
    border-color: var(--primary);
    background: var(--primary-light);
}

.file-upload-wrapper i.upload-icon {
    font-size: 40px;
    color: #cbd5e0;
    margin-bottom: 15px;
    transition: 0.3s;
}

.file-upload-wrapper:hover i.upload-icon {
    color: var(--primary);
    transform: translateY(-5px);
}

.file-upload-wrapper input[type="file"] {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
}

.file-name-preview {
    margin-top: 10px;
    font-size: 13px;
    font-weight: 700;
    color: var(--primary);
    display: none; /* Muncul lewat JS nanti */
}
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }

        body {
            background-color: var(--bg-body);
            color: var(--text-main);
            line-height: 1.6;
            padding: 20px 15px 100px 15px;
            overflow-x: hidden;
        }

        /* --- WATERMARKS --- */
        .bg-watermark { position: fixed; z-index: -1; opacity: 0.03; pointer-events: none; }
        .wm-1 { top: 5%; left: -100px; width: 400px; transform: rotate(-15deg); }
        .wm-2 { bottom: 5%; right: -100px; width: 450px; transform: rotate(15deg); }

        .container { width: 100%; max-width: 800px; margin: 0 auto; position: relative; }

        /* --- CARDS --- */
        .title-card {
            background: var(--white); border-radius: var(--radius); padding: 40px;
            margin-bottom: 25px; border-top: 12px solid var(--primary); box-shadow: var(--shadow);
        }

        .logo-area { display: flex; align-items: center; gap: 15px; margin-bottom: 25px; }
        .logo-img { width: 55px; height: 55px; object-fit: contain; }
        .brand-name { font-weight: 800; font-size: 22px; color: var(--primary); letter-spacing: 1px; border-left: 2px solid #eee; padding-left: 15px; }

        .title-card h1 { font-size: 30px; font-weight: 800; margin-bottom: 12px; color: #1a1a1a; letter-spacing: -1px; }

        .q-card {
            background: var(--white); border-radius: var(--radius); padding: 35px;
            margin-bottom: 20px; box-shadow: var(--shadow); border: 1px solid rgba(0,0,0,0.02);
        }

        .q-label { font-weight: 800; font-size: 17px; margin-bottom: 10px; display: flex; align-items: center; gap: 12px; color: #2d3748; }
        .q-icon { width: 34px; height: 34px; background: var(--primary-light); color: var(--primary); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 15px; }

        .q-subtitle { font-size: 14px; color: var(--text-muted); margin-bottom: 20px; padding-left: 46px; }
        .input-wrapper { padding-left: 46px; }

        /* --- INPUTS --- */
        .form-control {
            width: 100%; border: 2px solid #edf2f7; border-radius: 12px; padding: 14px;
            font-size: 15px; outline: none; background: #f8fafc; transition: 0.3s; font-weight: 600;
        }
        .form-control:focus { border-color: var(--primary); background: #fff; box-shadow: 0 0 0 4px rgba(124, 19, 22, 0.05); }

        .opt-group { display: flex; flex-direction: column; gap: 12px; }
        .opt-item {
            border: 2px solid #f1f5f9; border-radius: 15px; padding: 15px 20px;
            cursor: pointer; transition: 0.3s; display: flex; align-items: center; gap: 12px;
        }
        .opt-item:hover { border-color: var(--primary); background: var(--primary-light); }
        .opt-item input { width: 18px; height: 18px; accent-color: var(--primary); }

        /* --- NESTED CHILD BOX --- */
        .child-questions-box {
            margin-top: 15px; padding: 20px; background: #fff9f9;
            border-left: 5px solid var(--primary); border-radius: 0 15px 15px 0;
            display: flex; flex-direction: column; gap: 20px; animation: slideDown 0.3s ease-out;
        }
        .child-label { font-size: 13px; font-weight: 800; color: #7c1316; text-transform: uppercase; margin-bottom: 8px; display: block; }
        
        .sub-opt-list { display: flex; flex-wrap: wrap; gap: 10px; }
        .sub-opt-label {
            background: white; border: 1.5px solid #eee; padding: 8px 15px;
            border-radius: 10px; font-size: 13px; font-weight: 700; cursor: pointer;
            display: flex; align-items: center; gap: 8px; transition: 0.2s;
        }
        .sub-opt-label:has(input:checked) { border-color: var(--primary); color: var(--primary); background: var(--primary-light); }

        .btn-send {
            width: 100%; max-width: 400px; background: var(--primary); color: white;
            border: none; padding: 20px; border-radius: 20px; font-weight: 800;
            font-size: 18px; cursor: pointer; box-shadow: 0 10px 20px rgba(124, 19, 22, 0.3);
            margin: 40px auto 0 auto; display: block; transition: 0.3s;
        }
        .btn-send:hover { transform: translateY(-3px); filter: brightness(1.1); }

        @keyframes slideDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }

        @media (max-width: 640px) {
            .input-wrapper, .q-subtitle { padding-left: 0; }
            .title-card { padding: 25px; }
            .child-questions-box { padding: 15px; }
        }
    </style>
</head>
<body>

    <img src="https://rsudslg.kedirikab.go.id/asset_compro/img/logo/Logo.png" class="bg-watermark wm-1">
    <img src="https://sindikat-rsudslg.kedirikab.go.id/icon.png" class="bg-watermark wm-2">

    <div class="container">
        @if(!$form->is_active)
            <div class="title-card" style="text-align: center;">
                <i class="fas fa-lock" style="font-size: 50px; color: #cbd5e0; margin-bottom: 20px;"></i>
                <h1>Form Ditutup</h1>
                <p>Formulir <strong>{{ $form->title }}</strong> sedang dinonaktifkan.</p>
                <a href="/" style="display: inline-block; margin-top: 20px; color: var(--primary); font-weight: 800;">Kembali ke Beranda</a>
            </div>
        @else
        <div class="title-card">
    <div class="logo-area">
        <img src="https://sindikat-rsudslg.kedirikab.go.id/icon.png" class="logo-img" alt="Logo">
        <div style="display: inline-block; vertical-align: middle; margin-left: 10px;">
            <span class="brand-name" style="display: block; font-weight: bold; font-size: 1.5rem; line-height: 1.2;">Sindikat</span>
            <small style="display: block; font-size: 0.85rem; color: #555; margin-top: 2px;  border-left: 2px solid #eee; padding-left: 15px;">
                Sistem Informasi Pendidikan, Penelitian dan Pelatihan Diklat
            </small>
        </div>
    </div>
    <h1 style="margin-top: 20px;">{{ $form->title }}</h1>
<div class="description-content">
    {!! $form->description ?? 'Lengkapi formulir di bawah ini dengan benar.' !!}
</div>
</div>

            @if(!empty($form->header_image))
                <div style="margin-bottom: 25px;">
                    <img src="{{ url('storage/' . $form->header_image) }}" style="width:100%; max-height:150px; border-radius: 20px; box-shadow: var(--shadow);" alt="Header">
                </div>
            @endif

    <form action="{{ route('forms.public.submit', $form->slug) }}" method="POST" id="mainForm" enctype="multipart/form-data">
    @csrf

    @foreach($form->fields as $index => $field)
    <div class="q-card">
        <label class="q-label">
            <div class="q-icon">
                {{-- Logika Ikon --}}
                @if($field['type'] == 'text') <i class="fas fa-font"></i>
                @elseif($field['type'] == 'textarea') <i class="fas fa-align-left"></i>
                @elseif($field['type'] == 'number') <i class="fas fa-hashtag"></i>
                @elseif($field['type'] == 'date') <i class="fas fa-calendar-alt"></i>
                @elseif($field['type'] == 'file') <i class="fas fa-cloud-upload-alt"></i>
                @else <i class="fas fa-check-circle"></i>
                @endif
            </div>
            {{ $field['label'] }}
            @if(!empty($field['required'])) <span style="color:var(--primary)">*</span> @endif
        </label>

        @if(!empty($field['subtitle']))
            <div class="q-subtitle">{{ $field['subtitle'] }}</div>
        @endif

        <div class="input-wrapper">
            {{-- Input Teks Pendek --}}
            @if($field['type'] == 'text')
                <input type="text" name="ans_{{ $index }}" class="form-control" placeholder="Jawaban Anda..." {{ !empty($field['required']) ? 'required' : '' }}>
            
            {{-- Input Teks Panjang --}}
            @elseif($field['type'] == 'textarea')
                <textarea name="ans_{{ $index }}" class="form-control" rows="4" placeholder="Ketik jawaban lengkap..." {{ !empty($field['required']) ? 'required' : '' }}></textarea>
            
            {{-- Input Angka --}}
            @elseif($field['type'] == 'number')
                <input type="number" name="ans_{{ $index }}" class="form-control" placeholder="0" {{ !empty($field['required']) ? 'required' : '' }}>
            
            {{-- Input Tanggal --}}
            @elseif($field['type'] == 'date')
                <input type="date" name="ans_{{ $index }}" class="form-control" {{ !empty($field['required']) ? 'required' : '' }}>

            {{-- Input File/Gambar --}}
          @elseif($field['type'] == 'file')
    <div class="file-upload-wrapper" id="wrapper_{{ $index }}">
        <i class="fas fa-cloud-upload-alt upload-icon"></i>
        <div class="upload-text">
            <p style="font-weight: 800; color: #2d3748; margin: 0;">Pilih file atau tarik ke sini</p>
            <p style="font-size: 12px; color: #718096; margin-top: 4px;">PNG, JPG, PDF (Maks. 2MB)</p>
        </div>
        
        {{-- Nama file akan muncul di sini setelah dipilih --}}
        <div class="file-name-preview" id="preview_{{ $index }}">
            <i class="fas fa-file-alt"></i> <span class="file-text"></span>
        </div>

        <input type="file" 
               name="ans_{{ $index }}" 
               accept="image/*,.pdf,.doc,.docx" 
               {{ !empty($field['required']) ? 'required' : '' }}
               onchange="handleFileSelect(this, {{ $index }})">
    </div>
            {{-- Input Select/Dropdown --}}
            @elseif($field['type'] == 'select')
                <select name="ans_{{ $index }}{{ !empty($field['multiple']) ? '[]' : '' }}" class="choices-publish" {{ !empty($field['multiple']) ? 'multiple' : '' }} {{ !empty($field['required']) ? 'required' : '' }}>
                    <option value="">Pilih opsi...</option>
                    @foreach($field['options'] as $opt)
                        <option value="{{ $opt['text'] }}">{{ $opt['text'] }}</option>
                    @endforeach
                </select>

            {{-- Input Radio / Checkbox --}}
            @elseif($field['type'] == 'radio')
                <div class="opt-group">
                    @foreach($field['options'] as $oIdx => $opt)
                        <div class="option-container">
                            <label class="opt-item">
                                <input type="{{ !empty($field['multiple']) ? 'checkbox' : 'radio' }}" 
                                       name="ans_{{ $index }}{{ !empty($field['multiple']) ? '[]' : '' }}" 
                                       value="{{ $opt['text'] }}" 
                                       {{ !empty($field['required']) && empty($field['multiple']) ? 'required' : '' }}
                                       @if(!empty($opt['children'])) onchange="toggleChildren(this, 'child_box_{{ $index }}_{{ $oIdx }}')" @endif>
                                <span style="font-weight: 700;">{{ $opt['text'] }}</span>
                            </label>

                            @if(!empty($opt['children']))
                                <div id="child_box_{{ $index }}_{{ $oIdx }}" class="child-questions-box" style="display: none; flex-direction: column; gap: 15px;">
                                    @foreach($opt['children'] as $cIdx => $child)
                                        <div class="child-unit">
                                            <label class="child-label">{{ $child['label'] }}</label>
                                            @if($child['type'] == 'text')
                                                <input type="text" name="ans_{{ $index }}_sub_{{ Str::slug($opt['text']) }}[{{ $child['label'] }}]" class="form-control" placeholder="Ketik di sini...">
                                            @else
                                                <div class="sub-opt-list">
                                                    @foreach($child['choices'] as $choice)
                                                        <label class="sub-opt-label">
                                                            <input type="radio" name="ans_{{ $index }}_sub_{{ Str::slug($opt['text']) }}[{{ $child['label'] }}]" value="{{ $choice }}" style="accent-color: var(--primary);">
                                                            {{ $choice }}
                                                        </label>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
    @endforeach

    <button type="submit" class="btn-send">
        <i class="fas fa-paper-plane" style="margin-right: 12px;"></i> Kirim Jawaban
    </button>
</form>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selects = document.querySelectorAll('.choices-publish');
            selects.forEach(el => new Choices(el, { removeItemButton: true, searchEnabled: true, itemSelectText: '' }));
        });

        function toggleChildren(input, boxId) {
            const box = document.getElementById(boxId);
            if (input.type === 'radio') {
                const parentGroup = input.closest('.opt-group');
                parentGroup.querySelectorAll('.child-questions-box').forEach(b => b.style.display = 'none');
            }
            box.style.display = input.checked ? 'flex' : 'none';
        }
function handleFileSelect(input, index) {
    const wrapper = document.getElementById('wrapper_' + index);
    const preview = document.getElementById('preview_' + index);
    const uploadText = wrapper.querySelector('.upload-text');
    const icon = wrapper.querySelector('.upload-icon');
    
    if (input.files && input.files[0]) {
        const fileName = input.files[0].name;
        
        // Sembunyikan instruksi awal
        uploadText.style.display = 'none';
        icon.style.display = 'none';
        
        // Tampilkan nama file
        preview.querySelector('.file-text').innerText = fileName;
        preview.style.display = 'block';
        
        // Ubah style wrapper jadi "active"
        wrapper.style.borderColor = 'var(--primary)';
        wrapper.style.background = 'var(--primary-light)';
    }
}
        document.getElementById('mainForm').onsubmit = function() {
            const btn = this.querySelector('.btn-send');
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
            btn.style.opacity = '0.7';
            btn.style.pointerEvents = 'none';
        };
    </script>

</body>
</html>