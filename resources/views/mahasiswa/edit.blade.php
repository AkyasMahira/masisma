@extends('layouts.app')

@section('title', 'Edit Data Mahasiswa')

@section('content')
    {{-- 
       =========================================================
       1. SETUP DATA (PHP SIDE)
       =========================================================
    --}}
    @php
        $listProdi = [
            'TEKNIK & INFORMATIKA' => [
                'SMK REKAYASA PERANGKAT LUNAK', 'SMK TEKNIK KOMPUTER JARINGAN', 'SMK MULTIMEDIA',
                'S1 TEKNIK INFORMATIKA', 'S1 SISTEM INFORMASI', 'S1 ILMU KOMPUTER',
                'D3 TEKNIK ELEKTROMEDIK', 'D4 TEKNIK ELEKTROMEDIK', 'S1 TEKNIK ELEKTRO', 'S1 TEKNIK LINGKUNGAN',   'S1 TEKNOLOGI LABORATORIUM MEDIS',
                  'D4 TEKNOLOGI LABORATORIUM MEDIS'
            ],
            'KEPERAWATAN' => [
                'SMK ASISTEN KEPERAWATAN', 'D3 KEPERAWATAN', 'D4 KEPERAWATAN', 'S1 KEPERAWATAN',
                'PROFESI NERS', 'S2 KEPERAWATAN', 'SMK KEPERAWATAN',
            ],
            'KEBIDANAN' => [
                'D3 KEBIDANAN', 'D4 KEBIDANAN', 'S1 KEBIDANAN', 'PROFESI BIDAN',
            ],
            'KEDOKTERAN & FARMASI' => [
                'S1 KEDOKTERAN UMUM', 'PROFESI DOKTER (KOAS)', 'S1 KEDOKTERAN GIGI', 'PROFESI DOKTER GIGI',
                'PPDS (SPESIALIS)', 'SMK FARMASI', 'D3 FARMASI', 'S1 FARMASI', 'PROFESI APOTEKER',
            ],
            'PENUNJANG MEDIS & KESEHATAN' => [
                'S1 TEKNIK BIOMEDIS',
                'D4 REKAM MEDIK', 'D3 REKAM MEDIK', 'D3 REKAM MEDIS & INFORMASI KESEHATAN',
                'D4 REKAM MEDIS & INFORMASI KESEHATAN', 'S1 TEKNOLOGI LABORATORIUM MEDIK',
                'D4 TEKNOLOGI LABORATORIUM MEDIK', 'D3 ANALIS KESEHATAN (TLM)', 'D4 ANALIS KESEHATAN (TLM)',
                'D3 RADIOLOGI', 'D4 RADIOLOGI', 'D3 FISIOTERAPI', 'S1 FISIOTERAPI', 'PROFESI FISIOTERAPI',
                'D3 GIZI', 'S1 GIZI', 'PROFESI DIETISIEN', 'D3 KESEHATAN LINGKUNGAN (SANITASI)',
                'S1 KESEHATAN MASYARAKAT', 'D4 PROMOSI KESEHATAN', 'S1 KESELAMATAN DAN KESEHATAN KERJA (K3)',
                'D4 KESELAMATAN DAN KESEHATAN KERJA (K3)',
            ],
            'MANAJEMEN & SOSIAL' => [
                'SMK OTOMATISASI TATA KELOLA PERKANTORAN (OTKP)', 'SMK AKUNTANSI', 'D3 AKUNTANSI',
                'S1 AKUNTANSI', 'S1 MANAJEMEN', 'S1 HUKUM', 'S1 PSIKOLOGI', 'S1 ADMINISTRASI PUBLIK',
                'S1 ADMINISTRASI RUMAH SAKIT','SMK FARMASI','SMK LABORATORIUM',
            ]
        ];
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

        /* --- CARD STYLE & RESPONSIVE --- */
        .custom-card {
            background: #fff;
            border-radius: 16px;
            border: 1px solid rgba(0,0,0,0.05);
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            width: 100%;
            overflow: hidden;
        }

        .card-header-hero {
            background: linear-gradient(135deg, var(--primary-maroon) 0%, #4a0d0f 100%);
            padding: 1.5rem; /* Default mobile */
            color: white;
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

        /* Buttons */
        .btn-save {
            background: var(--primary-maroon); color: white; border: none;
            padding: 12px 40px; border-radius: 50px; font-weight: 600;
            transition: 0.3s; box-shadow: 0 4px 15px rgba(124, 19, 22, 0.2);
        }
        .btn-save:hover { background: var(--primary-maroon-hover); transform: translateY(-2px); color: white; }
    </style>

    <div class="col-12 px-2 px-md-0">
        
        {{-- Alert Success --}}
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

        <form action="{{ route('mahasiswa.update', $mahasiswa->id) }}" method="POST" enctype="multipart/form-data" id="form-mahasiswa">
            @csrf
            @method('PUT')

            <div class="custom-card mb-5">
                {{-- HEADER --}}
                <div class="card-header-hero">
                    <h3 class="fw-bold mb-1"><i class="bi bi-pencil-square me-2"></i>Edit Data Magang</h3>
                    <p class="mb-0 opacity-75 small">Perbarui data diri, kontak, atau informasi akademik Anda.</p>
                </div>

                <div class="card-body p-3 card-body-custom">
                    
                    {{-- 1. DATA PRIBADI --}}
                    <div class="section-header mt-0">
                        <i class="bi bi-person-lines-fill"></i><h5>Data Pribadi & Akun</h5><div class="section-line"></div>
                    </div>

                    <div class="row g-4 mb-5">
                        {{-- KOLOM KIRI: FOTO --}}
                        <div class="col-md-3 text-center">
                            <label class="form-label d-block mb-3">Pas Foto</label>
                            
                            <div class="position-relative d-inline-block" style="cursor: pointer;">
                                {{-- Foto Utama --}}
                                <img id="img-profile-avatar" 
                                     src="{{ $mahasiswa->foto_path ? asset($mahasiswa->foto_path) : asset('assets/img/default-avatar.png') }}" 
                                     onclick="openZoomModal()"
                                     style="width: 140px; height: 140px; border-radius: 50%; object-fit: cover; border: 4px solid #fff; box-shadow: 0 5px 15px rgba(0,0,0,0.1); transition: 0.3s;">
                                
                                {{-- Tombol Ganti Foto (Diubah pakai btn-maroon) --}}
                                <button type="button" 
                                        onclick="document.getElementById('foto-input').click()"
                                        class="btn btn-sm btn-maroon position-absolute shadow" 
                                        style="bottom: 5px; right: 5px; border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border: 2px solid white; z-index: 2; transition: 0.2s;">
                                    <i class="bi bi-camera-fill"></i>
                                </button>

                                {{-- Input File Hidden --}}
                                <input type="file" name="foto" id="foto-input" accept="image/*" hidden>
                            </div>

                            <div class="mt-3">
                                <span class="badge bg-soft-secondary text-dark border" style="font-size: 0.75rem;">
                                    ID User: {{ $mahasiswa->user_id }}
                                </span>
                                <div id="file-name-display" class="small text-maroon mt-2 fw-bold" style="min-height: 15px;"></div>
                            </div>
                        </div>

                        {{-- KOLOM KANAN: INPUT FIELD --}}
                        <div class="col-md-9">
                            <div class="row g-4">
                                <div class="col-12">
                                    <label class="form-label">Nama Lengkap</label>
                                    <div class="input-group-custom">
                                        <i class="bi bi-person input-icon"></i>
                                        <input type="text" name="nm_mahasiswa" class="form-control-custom"
                                            value="{{ old('nm_mahasiswa', $mahasiswa->nm_mahasiswa) }}" required
                                            oninput="this.value = this.value.toUpperCase()">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Alamat Email <span class="text-danger">*</span></label>
                                    <div class="input-group-custom">
                                        <i class="bi bi-envelope input-icon"></i>
                                        <input type="email" name="email" class="form-control-custom"
                                            value="{{ old('email', $mahasiswa->user->email ?? '') }}" required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">No. WhatsApp <span class="text-danger">*</span></label>
                                    <div class="input-group-custom">
                                        <i class="bi bi-whatsapp input-icon"></i>
                                        <input type="number" name="no_hp" class="form-control-custom"
                                            value="{{ old('no_hp', $mahasiswa->no_hp) }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 2. DATA AKADEMIK --}}
                    <div class="section-header">
                        <i class="bi bi-mortarboard-fill"></i><h5>Data Akademik</h5><div class="section-line"></div>
                    </div>

                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Asal Instansi</label>
                            <div class="input-group-custom ps-2">
                                <i class="bi bi-building input-icon"></i>
                                <div style="flex: 1; width: 100%;">
                                    <select name="mou_id" id="univ-select" class="form-select-custom">
                                        <option value="">Pilih Instansi...</option>
                                        @foreach ($mous as $mou)
                                            <option value="{{ $mou->id }}" 
                                                {{ old('mou_id', $mahasiswa->mou_id) == $mou->id ? 'selected' : '' }}>
                                                {{ strtoupper($mou->nama_instansi ?? $mou->nama_universitas) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Program Studi</label>
                            <div class="input-group-custom ps-2">
                                <i class="bi bi-book input-icon"></i>
                                <div style="flex: 1; width: 100%;">
                                    <select name="prodi" id="prodi-select" class="form-select-custom">
                                        <option value="">Pilih Program Studi...</option>
                                        @foreach($listProdi as $group => $items)
                                            <optgroup label="{{ $group }}">
                                                @foreach($items as $itemProdi)
                                                    <option value="{{ $itemProdi }}" 
                                                        {{ old('prodi', $mahasiswa->prodi) == $itemProdi ? 'selected' : '' }}>
                                                        {{ $itemProdi }}
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <!-- ... (di dalam <div class="row g-4 mb-4"> Data Akademik) ... -->

{{-- TIPE MAHASISWA --}}
<div class="col-md-6">
    <label class="form-label">Tipe (Magang / PKL) <span class="text-danger">*</span></label>
    <div class="input-group-custom bg-white">
        <i class="bi bi-person-badge input-icon text-maroon"></i>
        <select name="tipe_mahasiswa" class="form-select-custom" required>
            <option value="">Pilih Tipe...</option>
            <option value="magang" {{ old('tipe_mahasiswa', $mahasiswa->tipe_mahasiswa) == 'magang' ? 'selected' : '' }}>Magang</option>
            <option value="pkl" {{ old('tipe_mahasiswa', $mahasiswa->tipe_mahasiswa) == 'pkl' ? 'selected' : '' }}>PKL</option>
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
                            <label class="form-label text-muted small">Tambahkan kompetensi/keahlian yang Anda kuasai (Opsional)</label>
                            
                            <div id="kompetensi-container">
                                @php
                                    $kompetensiList = old('kompetensi', $mahasiswa->kompetensi_json ?? []);
                                @endphp

                                @if(is_array($kompetensiList) && count($kompetensiList) > 0)
                                    @foreach($kompetensiList as $kompetensi)
                                        <div class="d-flex flex-column flex-md-row align-items-md-center mb-2 kompetensi-row gap-2">
                                            <div class="input-group-custom w-100">
                                                <i class="bi bi-award input-icon text-maroon"></i>
                                                <input type="text" name="kompetensi[]" class="form-control-custom" value="{{ $kompetensi }}" placeholder="Contoh: Menguasai Framework Laravel">
                                            </div>
                                            <button type="button" class="btn btn-soft-danger rounded-3" onclick="this.parentElement.remove()" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="d-flex flex-column flex-md-row align-items-md-center mb-2 kompetensi-row gap-2">
                                        <div class="input-group-custom w-100">
                                            <i class="bi bi-award input-icon text-maroon"></i>
                                            <input type="text" name="kompetensi[]" class="form-control-custom" placeholder="Contoh: Menguasai Framework Laravel">
                                        </div>
                                        <button type="button" class="btn btn-soft-danger rounded-3" onclick="this.parentElement.remove()" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                @endif
                            </div>

                            <button type="button" class="btn btn-sm btn-outline-maroon mt-2 rounded-pill px-3 fw-bold" onclick="addKompetensi()">
                                <i class="bi bi-plus-circle me-1"></i> Tambah Kompetensi Lainnya
                            </button>
                        </div>
                    </div>

                    {{-- 4. PERIODE MAGANG & PENEMPATAN --}}
                    <div class="section-header">
                        <i class="bi bi-calendar-range-fill"></i><h5>Periode</h5><div class="section-line"></div>
                    </div>

                    <div class="p-3 p-md-4 rounded-4" style="background: #fffcfc; border: 1px dashed var(--primary-maroon);">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                                <div class="input-group-custom bg-white">
                                    <i class="bi bi-calendar-plus input-icon text-maroon"></i>
                                    <input type="date" name="tanggal_mulai" class="form-control-custom" 
                                        value="{{ old('tanggal_mulai', $mahasiswa->tanggal_mulai ? $mahasiswa->tanggal_mulai->format('Y-m-d') : '') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Berakhir <span class="text-danger">*</span></label>
                                <div class="input-group-custom bg-white">
                                    <i class="bi bi-calendar-check input-icon text-maroon"></i>
                                    <input type="date" name="tanggal_berakhir" class="form-control-custom" 
                                        value="{{ old('tanggal_berakhir', $mahasiswa->tanggal_berakhir ? $mahasiswa->tanggal_berakhir->format('Y-m-d') : '') }}" required>
                                </div>
                            </div>
                            
                            {{-- Status & Weekend --}}
                            <div class="col-md-6">
                                <label class="form-label">Status Keaktifan</label>
                                <div class="input-group-custom bg-white">
                                    <i class="bi bi-toggle-on input-icon"></i>
                                    <select name="status" class="form-select-custom">
                                        <option value="aktif" {{ old('status', $mahasiswa->status) === 'aktif' ? 'selected' : '' }}>🟢 Aktif</option>
                                        <option value="nonaktif" {{ old('status', $mahasiswa->status) === 'nonaktif' ? 'selected' : '' }}>🔴 Nonaktif</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6 pt-md-4">
                                <div class="form-check form-switch d-flex align-items-center ps-0 mt-2">
                                    <input class="form-check-input ms-0 me-3" type="checkbox" name="weekend_aktif" value="1" id="weekend_aktif" 
                                        {{ old('weekend_aktif', $mahasiswa->weekend_aktif) ? 'checked' : '' }} 
                                        style="width: 3em; height: 1.5em; cursor: pointer;">
                                    <div>
                                        <label class="fw-bold mb-0 text-dark" for="weekend_aktif" style="cursor: pointer;">Aktifkan Absensi Weekend</label>
                                        <div class="small text-muted">Hitung Sabtu & Minggu sebagai hari magang.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- FOOTER --}}
                <div class="card-footer bg-white border-0 p-3 p-md-4 pt-0 d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                    <a href="{{ auth()->user()->role === 'admin' ? route('mahasiswa.index') : route('dashboard') }}" 
                       class="btn btn-light shadow-sm px-4 rounded-pill fw-bold w-100 w-md-auto text-center">
                        <i class="bi bi-arrow-left me-2"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-save w-100 w-md-auto text-center" id="submit-btn">
                        Simpan Perubahan <i class="bi bi-check-lg ms-2"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- MODAL ZOOM --}}
    <div class="modal fade" id="zoomModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 bg-transparent">
                <div class="modal-body text-center p-0">
                    <img id="img-zoom-preview" src="{{ $mahasiswa->foto_path ? asset($mahasiswa->foto_path) : '' }}" 
                         style="max-width: 100%; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
                </div>
            </div>
        </div>
    </div>

    {{-- Script JS --}}
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <script>
        // --- LOGIKA DINAMIS KOMPETENSI ---
        function addKompetensi() {
            const container = document.getElementById('kompetensi-container');
            const row = document.createElement('div');
            // Menambahkan flex-column flex-md-row dan class button danger yang baru
            row.className = 'd-flex flex-column flex-md-row align-items-md-center mb-2 kompetensi-row gap-2';
            
            row.innerHTML = `
                <div class="input-group-custom w-100">
                    <i class="bi bi-award input-icon text-maroon"></i>
                    <input type="text" name="kompetensi[]" class="form-control-custom" placeholder="Ketik kompetensi di sini..." required>
                </div>
                <button type="button" class="btn btn-soft-danger rounded-3" onclick="this.parentElement.remove()" title="Hapus">
                    <i class="bi bi-trash"></i>
                </button>
            `;
            
            container.appendChild(row);
            row.querySelector('input').focus(); 
        }

        function openZoomModal() {
            const zoomModal = new bootstrap.Modal(document.getElementById('zoomModal'));
            zoomModal.show();
        }

        document.addEventListener('DOMContentLoaded', function() {
            // 1. CONFIG CHOICES.JS
            const config = {
                searchEnabled: true,
                itemSelectText: '',
                shouldSort: false,
                classNames: { containerOuter: 'choices', containerInner: 'choices__inner', input: 'choices__input' }
            };
            new Choices('#univ-select', { ...config, placeholderValue: 'Cari Instansi...' });
            new Choices('#prodi-select', { ...config, placeholderValue: 'Cari Program Studi...' });

            // 2. LOGIC UPLOAD FOTO (ID DIPERBAIKI)
            const fotoInput = document.getElementById('foto-input');
            const imgProfileAvatar = document.getElementById('img-profile-avatar'); // Diperbaiki: menargetkan elemen avatar profil
            const imgZoomPreview = document.getElementById('img-zoom-preview'); // Agar gambar modal zoom juga ikut terganti
            const nameDisplay = document.getElementById('file-name-display');

            fotoInput.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imgProfileAvatar.src = e.target.result; // Update avatar langsung
                        imgZoomPreview.src = e.target.result;   // Update foto zoom modal juga
                        nameDisplay.textContent = 'File Dipilih: ' + file.name;
                    }
                    reader.readAsDataURL(file);
                }
            });
        });
    </script>
@endsection