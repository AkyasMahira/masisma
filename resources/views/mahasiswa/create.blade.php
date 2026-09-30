@extends('layouts.app')

@section('title', 'Lengkapi Biodata Magang')

@section('content')
    {{-- 
       =========================================================
       1. SETUP DATA PRODI & LOGIKA AUTO-SELECT (PHP SIDE)
       =========================================================
    --}}
    @php
        // Ambil data prodi dari User Login (Bersihkan spasi & UpperCase)
        $userProdi = strtoupper(trim(auth()->user()->program_studi ?? ''));
        
        // Ambil old input jika sebelumnya gagal validasi
        $oldProdi = old('prodi');
        
        // Tentukan mana yang dipakai (Old input prioritas utama, lalu User Data)
        $selectedProdi = $oldProdi ? $oldProdi : $userProdi;

        // Daftar Prodi Lengkap (Sesuai Request Anda) dikelompokkan biar rapi
        // Daftar prodi kini dari Master Prodi (dulu hardcode).
        $listProdi = \App\Models\MasterProdi::grouped();
    @endphp

    {{-- Load CSS Libraries --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />

    <style>
        /* --- CUSTOM THEME --- */
        :root {
            --primary-maroon: #7c1316;
            --primary-maroon-hover: #9c1c20;
            --bg-soft: #f8f9fc;
            --input-bg: #ffffff;
            --border-color: #e2e8f0;
            --text-main: #1e293b;
            --text-sub: #64748b;
        }

        /* --- THEME HELPER CLASSES (Agar senada) --- */
        .text-maroon { color: var(--primary-maroon) !important; }
        .bg-maroon { background-color: var(--primary-maroon) !important; color: white !important; }
        .btn-maroon {
            background-color: var(--primary-maroon); color: white; border: none;
        }
        .btn-maroon:hover {
            background-color: var(--primary-maroon-hover); color: white; transform: scale(1.05);
        }
        .btn-outline-maroon {
            color: var(--primary-maroon); border: 1px solid var(--primary-maroon); background: transparent;
        }
        .btn-outline-maroon:hover {
            background-color: var(--primary-maroon); color: white;
        }
        .btn-soft-danger {
            background-color: #fee2e2; color: #ef4444; border: none; transition: 0.3s;
        }
        .btn-soft-danger:hover {
            background-color: #fca5a5; color: #b91c1c;
        }

        /* --- CARD STYLE --- */
        .custom-card {
            background: #fff;
            border-radius: 16px;
            border: 1px solid rgba(0,0,0,0.05);
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            width: 100%;
            overflow: visible; /* Penting agar dropdown tidak terpotong */
        }

        .card-header-hero {
            background: linear-gradient(135deg, var(--primary-maroon) 0%, #4a0d0f 100%);
            padding: 1.5rem; /* Mobile default padding */
            color: white;
            border-radius: 16px 16px 0 0;
            position: relative;
        }
        
        /* Mobile Adjustments */
        @media (min-width: 768px) {
            .card-header-hero { padding: 2rem 2.5rem; }
            .card-body-custom { padding: 3rem !important; }
        }

        /* --- FORM ELEMENTS --- */
        .section-header {
            display: flex; align-items: center; margin: 2rem 0 1.2rem 0;
        }
        .section-header i {
            font-size: 1.2rem; color: var(--primary-maroon); margin-right: 0.8rem;
            background: #fff4f4; padding: 8px; border-radius: 8px;
        }
        .section-header h5 {
            margin: 0; font-weight: 700; color: var(--text-main);
            text-transform: uppercase; letter-spacing: 0.5px; font-size: 0.95rem;
        }
        .section-line { flex: 1; height: 1px; background: var(--border-color); margin-left: 1rem; }

        .form-label { font-weight: 600; color: var(--text-main); font-size: 0.9rem; margin-bottom: 0.4rem; }

        .input-group-custom {
            display: flex; align-items: center; background: var(--input-bg);
            border: 1px solid var(--border-color); border-radius: 10px;
            padding: 0.4rem 0.8rem; transition: all 0.3s; width: 100%;
        }
        .input-group-custom:focus-within {
            border-color: var(--primary-maroon);
            box-shadow: 0 0 0 3px rgba(124, 19, 22, 0.1);
        }
        .input-icon { color: var(--text-sub); font-size: 1.1rem; margin-right: 0.8rem; }
        .form-control-custom, .form-select-custom {
            border: none; width: 100%; padding: 0.4rem; font-size: 0.95rem;
            background: transparent; outline: none; color: var(--text-main);
        }

        /* Choices JS Fixes */
        .choices__inner { border: none !important; background: transparent !important; min-height: auto !important; padding: 0 !important; }
        .choices__list--dropdown { border-radius: 10px; border: 1px solid var(--border-color); box-shadow: 0 10px 30px rgba(0,0,0,0.1); z-index: 1050; }

        /* --- UPLOAD ZONE MODERN --- */
        .upload-zone {
            position: relative;
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            background-color: #f8fafc;
            padding: 2rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            min-height: 220px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .upload-zone:hover {
            border-color: var(--primary-maroon);
            background-color: #fff5f5;
        }
        .upload-zone.has-image {
            padding: 0;
            border-style: solid;
            border-color: var(--border-color);
            background-color: #fff;
            overflow: hidden;
        }
        .upload-icon-box {
            width: 60px; height: 60px;
            background: #e2e8f0; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 1rem; color: var(--text-main); font-size: 1.5rem;
        }
        #img-preview {
            width: 100%; height: 100%;
            object-fit: contain;
            max-height: 300px;
            display: none;
        }
        .remove-img-btn {
            position: absolute; top: 10px; right: 10px;
            background: rgba(220, 38, 38, 0.9); color: white;
            border: none; border-radius: 50%; width: 32px; height: 32px;
            display: none; align-items: center; justify-content: center;
            cursor: pointer; z-index: 10; transition: 0.2s;
        }
        .remove-img-btn:hover { background: rgba(185, 28, 28, 1); transform: scale(1.1); }

        /* Buttons */
        .btn-save {
            background: var(--primary-maroon); color: white; border: none;
            padding: 12px 40px; border-radius: 50px; font-weight: 600;
            transition: 0.3s; box-shadow: 0 4px 15px rgba(124, 19, 22, 0.2);
        }
        .btn-save:hover { background: var(--primary-maroon-hover); transform: translateY(-2px); color: white; }
    </style>

    {{-- CONTAINER FLUID (FULL WIDTH dengan padding rapi di mobile) --}}
    <div class="col-12 px-2 px-md-0">
        
        {{-- Alert Sukses --}}
        @if (session('success'))
            <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center">
                <i class="bi bi-check-circle-fill fs-5 me-3 text-success"></i>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Alert Error --}}
        @if ($errors->any())
            <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4">
                <ul class="mb-0 ps-3 small">
                    @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('mahasiswa.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="custom-card mb-5">
                {{-- HEADER --}}
                <div class="card-header-hero">
                    <h3 class="fw-bold mb-1"><i class="bi bi-file-earmark-person me-2"></i>Lengkapi Biodata Magang</h3>
                    <p class="mb-0 opacity-75 small">Mohon isi data dengan benar. Data Nama, Instansi, dan Prodi diambil dari akun Anda.</p>
                </div>

                <div class="card-body p-3 card-body-custom">
                    
                    {{-- 1. DATA PRIBADI --}}
                    <div class="section-header mt-0">
                        <i class="bi bi-person-lines-fill"></i><h5>Data Pribadi</h5><div class="section-line"></div>
                    </div>

                    <div class="row g-4 mb-4">
                        {{-- NAMA LENGKAP --}}
                        <div class="col-md-6">
                            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <div class="input-group-custom">
                                <i class="bi bi-person input-icon"></i>
                                <input type="text" name="nm_mahasiswa" class="form-control-custom"
                                    placeholder="Nama Lengkap"
                                    value="{{ old('nm_mahasiswa', strtoupper(auth()->user()->name)) }}" 
                                    required
                                    style="text-transform: uppercase;"
                                    oninput="this.value = this.value.toUpperCase()">
                            </div>
                        </div>

                        {{-- NO WA --}}
                        <div class="col-md-6">
                            <label class="form-label">No. WhatsApp <span class="text-danger">*</span></label>
                            <div class="input-group-custom">
                                <i class="bi bi-whatsapp input-icon"></i>
                                <input type="number" name="no_hp" class="form-control-custom"
                                    placeholder="Contoh: 08123456789" value="{{ old('no_hp') }}" required>
                            </div>
                        </div>

                        {{-- UPLOAD FOTO (MODERN STYLING) --}}
                        <div class="col-12">
                            <label class="form-label mb-2">Pas Foto (Format: JPG/PNG, Maks 2MB)</label>
                            
                            {{-- Area Dropzone --}}
                            <div class="upload-zone" id="upload-zone" onclick="document.getElementById('foto-input').click()">
                                {{-- Input File Tersembunyi --}}
                                <input type="file" name="foto" id="foto-input" accept="image/*" hidden>
                                
                                {{-- Tombol Hapus (Hidden Default) --}}
                                <button type="button" class="remove-img-btn" id="remove-btn" onclick="event.stopPropagation(); removeImage();">
                                    <i class="bi bi-x-lg"></i>
                                </button>

                                {{-- Tampilan Default (Placeholder) --}}
                                <div id="upload-placeholder">
                                    <div class="upload-icon-box mx-auto">
                                        <i class="bi bi-cloud-arrow-up-fill"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1">Klik atau Drag Foto ke Sini</h6>
                                    <p class="text-muted small mb-0">Disarankan rasio 3x4, Latar Merah/Biru.</p>
                                </div>

                                {{-- Preview Image (Hidden Default) --}}
                                <img id="img-preview" src="#" alt="Preview Foto">
                            </div>
                            
                            {{-- Nama File --}}
                            <div id="file-name-display" class="small text-maroon mt-2 fw-bold text-center"></div>
                        </div>
                    </div>

                    {{-- 2. DATA AKADEMIK --}}
                    <div class="section-header">
                        <i class="bi bi-mortarboard-fill"></i><h5>Data Akademik</h5><div class="section-line"></div>
                    </div>

                    <div class="row g-4 mb-4">
                        {{-- INSTANSI --}}
                        <div class="col-md-6">
                            <label class="form-label">Asal Instansi <span class="text-danger">*</span></label>
                            <div class="input-group-custom ps-2">
                                <i class="bi bi-building input-icon"></i>
                                <div style="flex: 1; width: 100%;">
                                    <select name="mou_id" id="univ-select" class="form-select-custom" required>
                                        <option value="">Pilih Instansi...</option>
                                        @foreach ($mous as $mou)
                                            <option value="{{ $mou->id }}" 
                                                {{ (old('mou_id') == $mou->id || (empty(old('mou_id')) && auth()->user()->mou_id == $mou->id)) ? 'selected' : '' }}>
                                                {{ strtoupper($mou->nama_instansi ?? $mou->nama_universitas) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- PRODI (AUTO SELECT) --}}
                        <div class="col-md-6">
                            <label class="form-label">Program Studi <span class="text-danger">*</span></label>
                            <div class="input-group-custom ps-2">
                                <i class="bi bi-book input-icon"></i>
                                <div style="flex: 1; width: 100%;">
                                    <select name="prodi" id="prodi-select" class="form-select-custom" required>
                                        <option value="">Pilih Program Studi...</option>
                                        
                                        {{-- LOOPING PHP AGAR SELECTED VALID --}}
                                        @foreach($listProdi as $group => $items)
                                            <optgroup label="{{ $group }}">
                                                @foreach($items as $prodi)
                                                    <option value="{{ $prodi }}" {{ $selectedProdi == $prodi ? 'selected' : '' }}>
                                                        {{ $prodi }}
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach

                                    </select>
                                </div>
                            </div>
                        </div>
                        
                    

{{-- TIPE MAHASISWA --}}
<div class="col-md-6">
    <label class="form-label">Tipe (Magang / PKL) <span class="text-danger">*</span></label>
    <div class="input-group-custom bg-white">
        <i class="bi bi-person-badge input-icon text-maroon"></i>
        <select name="tipe_mahasiswa" class="form-select-custom" required>
            <option value="">Pilih Tipe...</option>
            <option value="magang" {{ old('tipe_mahasiswa') == 'magang' ? 'selected' : '' }}>Magang</option>
            <option value="pkl" {{ old('tipe_mahasiswa') == 'pkl' ? 'selected' : '' }}>PKL</option>
        </select>
    </div>
</div>
                    </div>
                    
                    {{-- 3. DATA KOMPETENSI --}}
                    <div class="section-header">
                        <i class="bi bi-list-check"></i><h5>Data Kompetensi</h5><div class="section-line"></div>
                    </div>

                    <div class="row g-4 mb-4">
                        <div class="col-12">
                            <label class="form-label text-muted small">Tambahkan kompetensi/keahlian yang Anda kuasai/ Yang ingin anda kuasai (Opsional)</label>
                            
                            <div id="kompetensi-container">
                                {{-- Input Default 1 --}}
                                <div class="d-flex flex-column flex-md-row align-items-md-center mb-2 kompetensi-row gap-2">
                                    <div class="input-group-custom w-100">
                                        <i class="bi bi-award input-icon text-maroon"></i>
                                        <input type="text" name="kompetensi[]" class="form-control-custom" placeholder="Isi Kompetensi">
                                    </div>
                                    <button type="button" class="btn btn-soft-danger rounded-3" onclick="this.parentElement.remove()" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>

                            <button type="button" class="btn btn-sm btn-outline-maroon mt-2 rounded-pill px-3 fw-bold" onclick="addKompetensi()">
                                <i class="bi bi-plus-circle me-1"></i> Tambah Kompetensi
                            </button>
                        </div>
                    </div>

                    {{-- 4. PERIODE MAGANG --}}
                    <div class="section-header">
                        <i class="bi bi-calendar-range-fill"></i><h5>Periode Magang</h5><div class="section-line"></div>
                    </div>

                    <div class="p-3 p-md-4 rounded-4" style="background: #fffcfc; border: 1px dashed var(--primary-maroon);">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                                <div class="input-group-custom bg-white">
                                    <i class="bi bi-calendar-plus input-icon text-maroon"></i>
                                    <input type="date" name="tanggal_mulai" class="form-control-custom" value="{{ old('tanggal_mulai') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Berakhir <span class="text-danger">*</span></label>
                                <div class="input-group-custom bg-white">
                                    <i class="bi bi-calendar-check input-icon text-maroon"></i>
                                    <input type="date" name="tanggal_berakhir" class="form-control-custom" value="{{ old('tanggal_berakhir') }}" required>
                                </div>
                            </div>
                            <div class="col-12 pt-2">
                                <div class="form-check form-switch d-flex align-items-center ps-0">
                                    <input class="form-check-input ms-0 me-3" type="checkbox" name="weekend_aktif" value="1" id="weekend_aktif" {{ old('weekend_aktif') ? 'checked' : '' }} style="width: 3em; height: 1.5em; cursor: pointer;">
                                    <div>
                                        <label class="fw-bold mb-0 text-dark" for="weekend_aktif" style="cursor: pointer;">Hitung Sabtu & Minggu?</label>
                                        <div class="small text-muted">Centang jika jadwal magang Anda termasuk akhir pekan.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- FOOTER --}}
                <div class="card-footer bg-white border-0 p-3 p-md-4 pt-0 d-flex justify-content-end">
                    <button type="submit" class="btn btn-save w-100 w-md-auto text-center">
                        Simpan Data <i class="bi bi-arrow-right-circle ms-2"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Script JS --}}
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <script>
        // --- LOGIKA DINAMIS KOMPETENSI ---
        function addKompetensi() {
            const container = document.getElementById('kompetensi-container');
            const row = document.createElement('div');
            
            // Tambahkan class flex responsif yang baru
            row.className = 'd-flex flex-column flex-md-row align-items-md-center mb-2 kompetensi-row gap-2';
            
            row.innerHTML = `
                <div class="input-group-custom w-100">
                    <i class="bi bi-award input-icon text-maroon"></i>
                    <input type="text" name="kompetensi[]" class="form-control-custom" placeholder="Isi Kompetensi" required>
                </div>
                <button type="button" class="btn btn-soft-danger rounded-3" onclick="this.parentElement.remove()" title="Hapus">
                    <i class="bi bi-trash"></i>
                </button>
            `;
            
            container.appendChild(row);
            row.querySelector('input').focus(); 
        }

        document.addEventListener('DOMContentLoaded', function() {
            
            // 1. CONFIG CHOICES.JS
            const config = {
                searchEnabled: true,
                itemSelectText: '',
                shouldSort: false, // JANGAN DIURUTKAN, IKUTI ARRAY PHP
                classNames: { containerOuter: 'choices', containerInner: 'choices__inner', input: 'choices__input' }
            };

            // Init Universitas & Prodi
            new Choices('#univ-select', { ...config, placeholderValue: 'Cari Instansi...' });
            new Choices('#prodi-select', { ...config, placeholderValue: 'Cari Program Studi...' });

            // 2. LOGIC UPLOAD FOTO (PREVIEW & DELETE)
            const fotoInput = document.getElementById('foto-input');
            const uploadZone = document.getElementById('upload-zone');
            const imgPreview = document.getElementById('img-preview');
            const placeholder = document.getElementById('upload-placeholder');
            const removeBtn = document.getElementById('remove-btn');
            const nameDisplay = document.getElementById('file-name-display');

            // Saat file dipilih
            fotoInput.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imgPreview.src = e.target.result;
                        imgPreview.style.display = 'block';     // Tampilkan Gambar
                        placeholder.style.display = 'none';     // Sembunyikan Teks
                        removeBtn.style.display = 'flex';       // Tampilkan Tombol Hapus
                        uploadZone.classList.add('has-image');  // Ubah Style Kotak
                        nameDisplay.textContent = 'File: ' + file.name;
                    }
                    reader.readAsDataURL(file);
                }
            });

            // Fungsi Hapus Gambar
            window.removeImage = function() {
                fotoInput.value = ''; // Reset input file
                imgPreview.src = '#';
                imgPreview.style.display = 'none';
                placeholder.style.display = 'block';
                removeBtn.style.display = 'none';
                uploadZone.classList.remove('has-image');
                nameDisplay.textContent = '';
            };
        });
    </script>
@endsection