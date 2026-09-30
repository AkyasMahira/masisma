@extends('layouts.app')

@section('content')
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<style>
    :root { --primary: #7c1316; --secondary: #1a202c; --bg: #f4f7f6; }
    body { background: var(--bg);  }
    
    .builder-wrapper { margin: 0 auto; padding: 20px; }
    .glass-card { background: white; border-radius: 24px; padding: 30px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px rgba(0,0,0,0.02); margin-bottom: 25px; }
    
    /* Layout Grids */
    .grid-2 { display: grid; grid-template-columns: 1fr 280px; gap: 20px; }
    .grid-config { display: grid; grid-template-columns: 1fr 380px; gap: 20px; align-items: flex-end; }
    
    /* Responsive Fix */
    @media (max-width: 850px) {
        .grid-2, .grid-config { grid-template-columns: 1fr; }
        .glass-card { padding: 20px; }
    }

    .btn-maroon { background: var(--primary); color: white; border: none; border-radius: 12px; font-weight: 700; cursor: pointer; transition: 0.3s; display: flex; align-items: center; gap: 8px; }
    .btn-maroon:hover { transform: translateY(-2px); box-shadow: 0 10px 15px rgba(124, 19, 22, 0.2); }
    
    input:focus, select:focus, textarea:focus { border-color: var(--primary) !important; outline: none; box-shadow: 0 0 0 4px rgba(124, 19, 22, 0.05); background: white !important; }
    
    .nested-line { border-left: 4px solid #fee2e2; padding-left: 25px; margin-left: 15px; }
    .child-box { background: #fff8f8; border: 1px solid #fecdd3; border-radius: 18px; padding: 20px; margin-top: 15px; }
</style>

<div class="builder-wrapper" x-data="formBuilder()">
    
    <form action="{{ route('admin.forms.store') }}" method="POST" enctype="multipart/form-data" id="mainForm">
        @csrf
        
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; flex-wrap: wrap; gap: 20px;">
            <div>
                <h1 style="font-size: 28px; font-weight: 800; color: #1a1a1a; margin: 0;">Buat <span style="color: var(--primary);">Formulir </span></h1>
            </div>
            <div style="display: flex; gap: 12px;">
                <a href="{{ route('admin.forms.index') }}" style="text-decoration: none; padding: 12px 25px; background: white; color: #4a5568; border-radius: 12px; font-weight: 700; font-size: 14px; border: 1px solid #e2e8f0;">Batal</a>
                <button type="submit" class="btn-maroon" style="padding: 12px 25px; font-size: 14px;">
                    <i class="fas fa-paper-plane"></i> Simpan & Publikasi
                </button>
            </div>
        </div>

        <div class="glass-card">
            <div style="margin-bottom: 30px;">
                <label style="display: block; font-size: 11px; font-weight: 800; color: var(--primary); text-transform: uppercase; margin-bottom: 12px;">Banner Header</label>
                <div style="width: 100%; height: 200px; border: 2px dashed #cbd5e0; border-radius: 20px; overflow: hidden; position: relative; background: #f8fafc; display: flex; align-items: center; justify-content: center;">
                    <template x-if="imagePreview">
                        <img :src="imagePreview" style="width: 100%; height: 100%; object-fit: cover;">
                    </template>
                    <template x-if="!imagePreview">
                        <div style="text-align: center; color: #94a3b8;"><i class="fas fa-image" style="font-size: 40px; margin-bottom: 10px;"></i><p style="font-size: 12px; font-weight: 600;">Klik tombol di bawah untuk pilih gambar</p></div>
                    </template>
                </div>
                <input type="file" name="header_image" id="header_image" accept="image/*" style="display: none;" @change="const file = $event.target.files[0]; if (file) { const reader = new FileReader(); reader.onload = (e) => { imagePreview = e.target.result; }; reader.readAsDataURL(file); }">
                <button type="button" onclick="document.getElementById('header_image').click()" style="margin-top: 15px; background: white; border: 1.5px solid var(--primary); color: var(--primary); padding: 10px 20px; border-radius: 10px; font-size: 12px; font-weight: 700; cursor: pointer;">Pilih File Banner</button>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 11px; font-weight: 800; color: var(--primary); text-transform: uppercase; margin-bottom: 8px;">Judul Formulir</label>
                <input type="text" name="title" required placeholder="Judul pendaftaran atau evaluasi..." style="width: 100%; padding: 15px; border-radius: 12px; border: 1px solid #edf2f7; background: #f8fafc; font-size: 18px; font-weight: 700;">
            </div>
           <div>
    <label style="display: block; font-size: 11px; font-weight: 800; color: var(--primary); text-transform: uppercase; margin-bottom: 8px;">Deskripsi Singkat</label>
    <textarea id="summernote" name="description" placeholder="Jelaskan tujuan formulir ini...">{{ $form->description ?? '' }}</textarea>
</div>
        </div>

   <div style="display: flex; flex-direction: column; gap: 30px;" x-init="
    Sortable.create($el, {
        handle: '.drag-handle',
        animation: 150,
        ghostClass: 'bg-gray-100',
        onEnd: (evt) => {
            // Sinkronisasi urutan array Alpine.js dengan urutan visual DOM
            const item = fields.splice(evt.oldIndex, 1)[0];
            fields.splice(evt.newIndex, 0, item);
        }
    });
">
    <template x-for="(field, fIndex) in fields" :key="field.id">
        <div class="glass-card" style="border-left: 10px solid var(--primary); position: relative; padding-top: 45px;">
            
            <div class="drag-handle" style="position: absolute; top: 15px; left: 15px; cursor: grab; color: #a0aec0; font-size: 20px; transition: 0.2s;" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='#a0aec0'">
                <i class="fas fa-grip-vertical"></i>
            </div>
            
            <button type="button" @click="fields.splice(fIndex, 1)" style="position: absolute; top: 15px; right: 15px; color: #cbd5e1; border: none; background: none; cursor: pointer; font-size: 22px;" onmouseover="this.style.color='#ef4444'" onmouseout="this.style.color='#cbd5e1'">
                <i class="fas fa-trash-alt"></i>
            </button>    
                    <div class="grid-2" style="margin-bottom: 20px;">
                        <div>
                            <label style="font-size: 11px; font-weight: 800; color: #a0aec0; text-transform: uppercase; display: block; margin-bottom: 8px;">Label Pertanyaan Utama</label>
                            <input type="text" :name="`fields[${fIndex}][label]`" x-model="field.label" required placeholder="Contoh: Pilih Kategori Penilaian" style="width: 100%; padding: 12px; border: 1px solid #f1f5f9; border-bottom: 2px solid var(--primary); outline: none; font-weight: 700; font-size: 16px;">
                        </div>
                        <div>
                            <label style="font-size: 11px; font-weight: 800; color: #a0aec0; text-transform: uppercase; display: block; margin-bottom: 8px;">Tipe Input</label>
                            <select :name="`fields[${fIndex}][type]`" x-model="field.type" style="width: 100%; padding: 12px; border-radius: 12px; background: #f8fafc; border: 1px solid #e2e8f0; font-weight: 700; color: #1a202c;">
                               <option value="text">Teks Pendek</option>
    <option value="textarea">Teks Panjang (Paragraf)</option>
    <option value="number">Hanya Angka</option>
    <option value="date">Tanggal</option>
    <option value="file">Upload File / Gambar</option> 
    <option value="radio">Pilihan Ganda (Radio)</option>
    <option value="select">Dropdown (List)</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid-config" style="margin-bottom: 30px;">
                        <div>
                            <label style="font-size: 11px; font-weight: 700; color: #a0aec0; text-transform: uppercase; margin-bottom: 8px; display: block;">Subtitle (Opsional)</label>
                            <input type="text" :name="`fields[${fIndex}][subtitle]`" x-model="field.subtitle" placeholder="Ketik petunjuk tambahan..." style="width: 100%; padding: 11px 15px; border: 1px solid #f1f5f9; border-radius: 12px; font-size: 13px; background: #fff;">
                        </div>
                        <div style="background: #f8fafc; padding: 12px 20px; border-radius: 12px; border: 1px solid #edf2f7; display: flex; justify-content: center; gap: 20px;">
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 13px; font-weight: 700; color: var(--primary);">
                                <input type="checkbox" :name="`fields[${fIndex}][required]`" x-model="field.required" style="width: 18px; height: 18px; accent-color: var(--primary);"> Wajib
                            </label>
                            <template x-if="['radio', 'select'].includes(field.type)">
                                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 13px; font-weight: 700; color: #1a202c;">
                                    <input type="checkbox" :name="`fields[${fIndex}][multiple]`" x-model="field.multiple" style="width: 18px; height: 18px; accent-color: #1a202c;"> Pilih Banyak
                                </label>
                            </template>
                        </div>
                    </div>

                    <div x-show="['radio', 'select'].includes(field.type)" style="background: #f8fafc; padding: 30px; border-radius: 24px; border: 1px solid #edf2f7;">
                        <label style="font-size: 11px; font-weight: 900; color: #4a5568; text-transform: uppercase; margin-bottom: 25px; display: block;">
                            <i class="fas fa-sitemap mr-2"></i> Konfigurasi Pilihan & Sub-Pertanyaan
                        </label>

                        <div style="display: flex; flex-direction: column; gap: 20px;">
                            <template x-for="(opt, oIndex) in field.options" :key="oIndex">
                                <div style="background: white; padding: 25px; border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
                                    
                                    <div style="display: flex; gap: 12px; align-items: center;">
                                        <div style="min-width: 35px; height: 35px; background: var(--primary); color: white; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 800;" x-text="oIndex + 1"></div>
                                        <input type="text" :name="`fields[${fIndex}][options][${oIndex}][text]`" x-model="opt.text" :required="['radio', 'select'].includes(field.type)" placeholder="Teks Pilihan..." style="flex: 1; padding: 12px; border-radius: 10px; border: 1px solid #f1f5f9; outline: none; font-weight: 800; border-bottom: 2px solid #cbd5e0;">
                                        
                                        <button type="button" @click="opt.children.push({ label: '', type: 'text', choices: [''] })" style="background: #1a202c; color: white; border: none; padding: 11px 18px; border-radius: 10px; font-size: 11px; font-weight: 800; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                                            <i class="fas fa-plus"></i> ANAK
                                        </button>

                                        <button type="button" @click="field.options.splice(oIndex, 1)" x-show="field.options.length > 1" style="color: #ef4444; background: none; border: none; font-size: 18px; cursor: pointer;"><i class="fas fa-minus-circle"></i></button>
                                    </div>

                                    <div x-show="opt.children.length > 0" class="nested-line" style="margin-top: 20px; display: flex; flex-direction: column; gap: 20px;">
                                        <template x-for="(child, cIndex) in opt.children" :key="cIndex">
                                            <div class="child-box">
                                                <div style="display: grid; grid-template-columns: 1fr 180px; gap: 15px; margin-bottom: 15px;">
                                                    <div>
                                                        <label style="font-size: 10px; font-weight: 800; color: #fca5a5; text-transform: uppercase;">Label Anak Pertanyaan</label>
                                                        <input type="text" :name="`fields[${fIndex}][options][${oIndex}][children][${cIndex}][label]`" x-model="child.label" required placeholder="Contoh: Alasan Memilih" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #f1f5f9; font-size: 13px; font-weight: 700;">
                                                    </div>
                                                    <div>
                                                        <label style="font-size: 10px; font-weight: 800; color: #fca5a5; text-transform: uppercase;">Tipe Anak</label>
                                                        <select :name="`fields[${fIndex}][options][${oIndex}][children][${cIndex}][type]`" x-model="child.type" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #fecdd3; font-size: 12px; font-weight: 800; background: white;">
                                                            <option value="text">Input Teks</option>
                                                            <option value="choice">Input Pilihan</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div x-show="child.type === 'choice'" style="margin-top: 15px; background: white; padding: 20px; border-radius: 12px; border: 1.5px dashed #fca5a5;">
                                                    <label style="font-size: 10px; font-weight: 900; color: var(--primary); text-transform: uppercase; display: block; margin-bottom: 12px;">Daftar Pilihan Jawaban Anak</label>
                                                    <div style="display: flex; flex-direction: column; gap: 10px;">
                                                        <template x-for="(cOpt, coIndex) in child.choices" :key="coIndex">
                                                            <div style="display: flex; gap: 10px; align-items: center;">
                                                                <input type="text" :name="`fields[${fIndex}][options][${oIndex}][children][${cIndex}][choices][${coIndex}]`" x-model="child.choices[coIndex]" required placeholder="Misal: Ya / Tidak" style="flex: 1; padding: 10px; border-radius: 8px; border: 1px solid #eee; font-size: 12px; font-weight: 600;">
                                                                <button type="button" @click="child.choices.push('')" style="color: var(--primary); border: none; background: none; cursor: pointer;"><i class="fas fa-plus-circle"></i></button>
                                                                <button type="button" @click="child.choices.splice(coIndex, 1)" x-show="child.choices.length > 1" style="color: #cbd5e0; border: none; background: none; cursor: pointer;"><i class="fas fa-times"></i></button>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </div>

                                                <div style="text-align: right; margin-top: 15px;">
                                                    <button type="button" @click="opt.children.splice(cIndex, 1)" style="color: #e53e3e; font-size: 11px; font-weight: 800; background: none; border: none; cursor: pointer; display: flex; align-items: center; gap: 5px; margin-left: auto;">
                                                        <i class="fas fa-trash"></i> Hapus Anak
                                                    </button>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <button type="button" @click="field.options.push({ text: '', children: [] })" style="margin-top: 25px; background: white; color: var(--primary); border: 2px dashed var(--primary); padding: 15px; border-radius: 15px; font-size: 14px; font-weight: 900; cursor: pointer; width: 100%;">
                            <i class="fas fa-plus mr-2"></i> TAMBAH PILIHAN UTAMA BARU
                        </button>
                    </div>

                    <button type="button" @click="fields.splice(fIndex, 1)" style="position: absolute; top: 15px; right: 15px; color: #cbd5e1; border: none; background: none; cursor: pointer; font-size: 22px;" onmouseover="this.style.color='#ef4444'"><i class="fas fa-trash-alt"></i></button>
                </div>
            </template>
        </div>

        <button type="button" @click="addField()" style="width: 100%; padding: 40px; border: 4px dashed #cbd5e0; border-radius: 35px; background: white; color: #718096; font-weight: 900; font-size: 18px; cursor: pointer; margin-top: 25px; transition: 0.3s;" onmouseover="this.style.borderColor=var(--primary); this.style.color=var(--primary); this.style.background='#fffafa'">
            <i class="fas fa-plus-circle mr-2"></i> TAMBAH GRUP PERTANYAAN BARU
        </button>

    </form>
</div>

<script>
    function formBuilder() {
        return {
            imagePreview: '',
            fields: [
                { 
                    label: '', 
                    type: 'text', 
                    subtitle: '', 
                    required: false, 
                    multiple: false,
                    options: [{ text: '', children: [] }] 
                }
            ],
            
            addField() {
                this.fields.push({ 
                    label: '', type: 'text', subtitle: '', required: false, multiple: false,
                    options: [{ text: '', children: [] }] 
                });
            }
        }
    }

    @if($errors->any())
        Swal.fire({ icon: 'error', title: 'Gagal!', html: '{!! implode("<br>", $errors->all()) !!}', confirmButtonColor: '#7c1316' });
    @endif
</script>
<script>
    $(document).ready(function() {
        $('#summernote').summernote({
            placeholder: 'Jelaskan tujuan formulir ini...',
            tabsize: 2,
            height: 120,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['insert', ['link']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ],
            callbacks: {
                onChange: function(contents, $editable) {
                    // Jika Anda butuh sinkronisasi manual dengan Alpine (opsional)
                    // @this.set('description', contents); 
                }
            }
        });
    });
    
    function formBuilder() {
    return {
        imagePreview: '',
        fields: [
            { 
                id: Date.now(), // Tambahkan ID Unik ini
                label: '', 
                type: 'text', 
                subtitle: '', 
                required: false, 
                multiple: false,
                options: [{ text: '', children: [] }] 
            }
        ],
        
        addField() {
            this.fields.push({ 
                id: Date.now() + Math.random(), // Tambahkan ID Unik ini juga
                label: '', type: 'text', subtitle: '', required: false, multiple: false,
                options: [{ text: '', children: [] }] 
            });
        }
    }
}
</script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
@endsection