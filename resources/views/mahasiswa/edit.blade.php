@extends('layouts.app')

@section('title', 'Edit Mahasiswa')
@section('page-title', 'Edit Mahasiswa')

@section('content')
    <style>
        :root {
            --custom-maroon: #7c1316;
            --custom-maroon-light: #a3191d;
            --custom-maroon-subtle: #fcf0f1;
            --text-dark: #2c3e50;
            --text-muted: #95a5a6;
            --card-radius: 16px;
            --transition: 0.3s ease;
        }

        /* --- Card Styling --- */
        .form-card {
            border: none;
            border-radius: var(--card-radius);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            background: #fff;
            overflow: hidden;
        }

        .card-header-custom {
            background-color: var(--custom-maroon);
            padding: 1.5rem;
            color: white;
            border-bottom: 4px solid var(--custom-maroon-light);
        }

        /* --- Form Styling --- */
        .form-label {
            font-weight: 600;
            color: var(--text-dark);
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .input-group-text {
            background-color: #f8f9fa;
            border-right: none;
            color: var(--custom-maroon);
            border-top-left-radius: 10px;
            border-bottom-left-radius: 10px;
        }

        .form-control,
        .form-select {
            border-left: none;
            border-radius: 0 10px 10px 0;
            padding: 0.7rem 1rem;
            border-color: #dee2e6;
            box-shadow: none !important;
            transition: border-color 0.2s;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--custom-maroon-light);
        }

        /* --- Room Info Box --- */
        .room-info-box {
            background-color: var(--custom-maroon-subtle);
            border: 1px dashed var(--custom-maroon-light);
            border-radius: 10px;
            padding: 1rem;
            margin-top: 10px;
            transition: all 0.3s ease;
        }

        .room-info-box.full {
            background-color: #fee2e2;
            border-color: #ef4444;
        }

        /* --- Buttons --- */
        .btn-maroon {
            background-color: var(--custom-maroon);
            color: white;
            border: none;
            padding: 0.8rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            transition: var(--transition);
            box-shadow: 0 4px 15px rgba(124, 19, 22, 0.2);
        }

        .btn-maroon:hover {
            background-color: var(--custom-maroon-light);
            transform: translateY(-2px);
            color: white;
        }

        .btn-maroon:disabled {
            background-color: #ccc;
            transform: none;
            box-shadow: none;
        }

        .btn-light-custom {
            background: #fff;
            border: 1px solid #dee2e6;
            color: var(--text-dark);
            border-radius: 50px;
            padding: 0.8rem 1.5rem;
            font-weight: 600;
        }

        .btn-light-custom:hover {
            background: #f8f9fa;
            color: var(--custom-maroon);
        }

        /* Animation */
        .animate-up {
            animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
            opacity: 0;
            transform: translateY(20px);
        }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>

<div class="row justify-content-center animate-up">
    <div class="col-md-8 col-lg-7">
        <div class="form-card">
            <div class="card-header-custom">
                <h4 class="mb-0 fw-bold"><i class="bi bi-pencil-square me-2"></i> Perbarui Data Magang</h4>
                <p class="mb-0 small opacity-75">Perbarui nomor kontak atau foto profil Anda.</p>
            </div>

            <div class="card-body p-4 p-md-5">
                {{-- Alert Success --}}
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                {{-- Alert Error --}}
                @if ($errors->any())
                    <div class="alert alert-danger rounded-3 shadow-sm mb-4">
                        <ul class="mb-0 small">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('mahasiswa.update', $mahasiswa->id) }}" method="POST" id="form-mahasiswa"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

{{-- Helper variable biar kodingan lebih bersih --}}
                    @php
                        $isAdmin = auth()->user()->role === 'admin';
                    @endphp

                    {{-- ======================================================== --}}
                    {{-- 1. INFORMASI MAHASISWA (Admin: Edit, User: Readonly) --}}
                    {{-- ======================================================== --}}
                    <h6 class="text-muted text-uppercase fw-bold mb-3" style="font-size: 0.75rem; letter-spacing: 1px;">
                        Informasi Dasar
                    </h6>

                    {{-- NAMA LENGKAP --}}
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person-lock"></i></span>
                            {{-- Jika Admin: Tidak ada readonly. Jika User: Readonly --}}
                            <input type="text" name="nm_mahasiswa" class="form-control"
                                value="{{ old('nm_mahasiswa', $mahasiswa->nm_mahasiswa) }}"
                                {{ $isAdmin ? '' : 'readonly' }}>
                        </div>
                    </div>

                    {{-- UNIVERSITAS & PRODI --}}
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Asal Universitas</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-building-lock"></i></span>

                                @if($isAdmin)
                                    {{-- JIKA ADMIN: TAMPILKAN DROPDOWN SELECT --}}
                                    <select name="mou_id" class="form-select">
                                        <option value="">-- Pilih Universitas --</option>
                                        @foreach ($mous as $mou)
                                            <option value="{{ $mou->id }}"
                                                {{ old('mou_id', $mahasiswa->mou_id) == $mou->id ? 'selected' : '' }}>
                                                {{ $mou->nama_instansi ?? $mou->nama_universitas }}
                                            </option>
                                        @endforeach
                                    </select>
                                @else
                                    {{-- JIKA USER: TAMPILKAN INPUT READONLY + HIDDEN ID --}}
                                    <input type="text" class="form-control"
                                        value="{{ $mahasiswa->mou ? ($mahasiswa->mou->nama_instansi ?? $mahasiswa->mou->nama_universitas) : 'Tidak Ada Data' }}" readonly>
                                    <input type="hidden" name="mou_id" value="{{ $mahasiswa->mou_id }}">
                                @endif
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Program Studi</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-book"></i></span>
                                {{-- Jika Admin: Tidak ada readonly. Jika User: Readonly --}}
                                <input type="text" name="prodi" class="form-control"
                                    value="{{ old('prodi', $mahasiswa->prodi) }}"
                                    {{ $isAdmin ? '' : 'readonly' }}>
                            </div>
                        </div>
                    </div>

                    {{-- ======================================================== --}}
                    {{-- 2. INFORMASI EDITABLE (BISA DIEDIT USER) --}}
                    {{-- ======================================================== --}}

                    {{-- NOMOR HP --}}
                    <div class="mb-3">
                        <label class="form-label">Nomor WhatsApp / HP <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-whatsapp"></i></span>
                            <input type="number" name="no_hp" class="form-control" placeholder="Contoh: 081234567890"
                                value="{{ old('no_hp', $mahasiswa->no_hp) }}" required>
                        </div>
                        @error('no_hp')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr class="my-4 border-light">

                    <h6 class="text-muted text-uppercase fw-bold mb-3" style="font-size: 0.75rem; letter-spacing: 1px;">
                        Update Berkas
                    </h6>

                    {{-- FOTO --}}
                    <div class="mb-4">
                        <label class="form-label">Pas Foto 3x4</label>

                        {{-- Tampilkan Foto Lama (Jika Ada) --}}
                        @if ($mahasiswa->foto_path)
                            <div class="mb-2 d-flex align-items-center">
                                <div class="p-1 border rounded bg-light me-3">
                                    <img src="{{ asset($mahasiswa->foto_path) }}" alt="Foto Lama"
                                        style="height: 80px; width: 60px; object-fit: cover; border-radius: 4px;">
                                </div>
                                <span class="badge bg-secondary opacity-75">Foto Saat Ini</span>
                            </div>
                        @endif

                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-camera"></i></span>
                            {{-- Hapus 'required' agar user tidak wajib upload ulang jika tidak mau ganti foto --}}
                            <input type="file" name="foto" id="foto-input" class="form-control"
                                accept="image/jpeg,image/png,image/jpg">
                        </div>
                        <small class="form-text text-muted">Biarkan kosong jika tidak ingin mengubah foto. (Max: 2MB)</small>

                        {{-- Preview Foto Baru (Javascript) --}}
                        <div class="mt-3" id="preview-container" style="display: none;">
                            <div class="d-inline-block p-1 border rounded bg-light">
                                <img id="img-preview" src="#" alt="Preview Foto Baru"
                                    style="max-width: 150px; max-height: 200px; object-fit: cover; border-radius: 4px; display: block;">
                            </div>
                            <div class="small text-muted mt-1 fst-italic">Preview Foto Baru</div>
                        </div>

                        @error('foto')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

{{-- ======================================================== --}}
{{-- 3. BAGIAN PENEMPATAN & DURASI (LOGIKA CABANG) --}}
{{-- ======================================================== --}}

@if(auth()->user()->role === 'admin')

    {{-- === TAMPILAN KHUSUS ADMIN (BISA EDIT) === --}}
    <hr class="my-4 border-light">
    <h6 class="text-muted text-uppercase fw-bold mb-3" style="font-size: 0.75rem; letter-spacing: 1px;">
        Penempatan & Durasi (Admin Control)
    </h6>

    {{-- Pilih Ruangan --}}
    <div class="mb-3">
        <label class="form-label">Pilih Ruangan</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-door-open"></i></span>
            <select id="ruangan_id" name="ruangan_id" class="form-select">
                <option value="">-- Pilih Ruangan (Opsional) --</option>
                @foreach ($ruangans as $r)
                    <option value="{{ $r->id }}"
                        {{ old('ruangan_id', $mahasiswa->ruangan_id) == $r->id ? 'selected' : '' }}>
                        {{ $r->nm_ruangan }}
                    </option>
                @endforeach
            </select>
        </div>
        {{-- Info Box Room JS (Pastikan script JS included di bawah) --}}
        <div id="ruangan-info" class="room-info-box" style="display: none;">
            </div>
    </div>

    {{-- Tanggal Mulai & Akhir --}}
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-calendar-plus"></i></span>
                <input type="date" name="tanggal_mulai" class="form-control"
                    value="{{ old('tanggal_mulai', $mahasiswa->tanggal_mulai ? $mahasiswa->tanggal_mulai->format('Y-m-d') : '') }}"
                    required>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Tanggal Berakhir <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-calendar-check"></i></span>
                <input type="date" name="tanggal_berakhir" class="form-control"
                    value="{{ old('tanggal_berakhir', $mahasiswa->tanggal_berakhir ? $mahasiswa->tanggal_berakhir->format('Y-m-d') : '') }}"
                    required>
            </div>
        </div>
    </div>

    {{-- Status Keaktifan --}}
    <div class="mb-4">
        <label class="form-label">Status Keaktifan</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-toggle-on"></i></span>
            <select name="status" class="form-select">
                <option value="aktif" {{ old('status', $mahasiswa->status) === 'aktif' ? 'selected' : '' }}>
                    🟢 Aktif
                </option>
                <option value="nonaktif" {{ old('status', $mahasiswa->status) === 'nonaktif' ? 'selected' : '' }}>
                    🔴 Nonaktif
                </option>
            </select>
        </div>
    </div>

    {{-- Weekend Switch --}}
    <div class="form-group mb-4" style="padding-top: 10px;">
        <div class="form-check form-switch" style="padding-left: 2.5em;">
            <input class="form-check-input" type="checkbox" role="switch" name="weekend_aktif"
                value="1" id="weekend_aktif"
                {{ old('weekend_aktif', $mahasiswa->weekend_aktif) ? 'checked' : '' }}
                style="height: 1.25em; width: 2.25em; cursor: pointer;">
            <label class="form-check-label" for="weekend_aktif"
                style="padding-top: 0.2em; font-weight: 600; color: var(--text-dark); cursor: pointer;">
                Aktifkan Absensi Weekend
            </label>
        </div>
        <small class="form-text text-muted" style="padding-left: 2.5em;">
            Jika dicentang, Sabtu & Minggu akan dihitung sebagai hari magang.
        </small>
    </div>

@else

    {{-- === LOGIKA KHUSUS USER BIASA (HIDDEN FIELDS) === --}}
    {{-- User biasa tidak melihat form di atas, tapi data lama tetap dikirim agar validasi Controller lolos --}}

    <input type="hidden" name="ruangan_id" value="{{ $mahasiswa->ruangan_id }}">
    <input type="hidden" name="nm_ruangan" value="{{ $mahasiswa->nm_ruangan }}">
    <input type="hidden" name="tanggal_mulai" value="{{ $mahasiswa->tanggal_mulai ? $mahasiswa->tanggal_mulai->format('Y-m-d') : '' }}">
    <input type="hidden" name="tanggal_berakhir" value="{{ $mahasiswa->tanggal_berakhir ? $mahasiswa->tanggal_berakhir->format('Y-m-d') : '' }}">
    <input type="hidden" name="status" value="{{ $mahasiswa->status }}">
    @if($mahasiswa->weekend_aktif)
        <input type="hidden" name="weekend_aktif" value="1">
    @endif

@endif

                    {{-- TOMBOL AKSI --}}
                    <div class="d-flex justify-content-between align-items-center pt-3">
                        <a href="{{ auth()->user()->role === 'admin' ? route('mahasiswa.index') : route('dashboard') }}"
                           class="btn btn-light-custom shadow-sm">
                            <i class="bi bi-arrow-left me-2"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-maroon" id="submit-btn">
                            Simpan Perubahan <i class="bi bi-check-lg ms-2"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ruanganSelect = document.getElementById('ruangan_id');
            const ruanganInfo = document.getElementById('ruangan-info');
            const submitBtn = document.getElementById('submit-btn');

            // Menyimpan ID ruangan awal untuk pengecekan "pindah ruangan"
            const originalRuanganId = "{{ old('ruangan_id', $mahasiswa->ruangan_id) }}";
            let selectedRuanganFull = false;

            function loadRuanganInfo(ruanganId) {
                if (!ruanganId) {
                    ruanganInfo.style.display = 'none';
                    selectedRuanganFull = false;
                    submitBtn.disabled = false;
                    return;
                }

                fetch(`/api/ruangan-info/${ruanganId}`)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('info-nama').textContent = data.nm_ruangan;
                        document.getElementById('info-kuota-total').textContent = data.kuota_total;
                        document.getElementById('info-tersedia').textContent = data.tersedia;
                        document.getElementById('info-terisi').textContent = data.terisi;

                        const badgeStatus = document.getElementById('badge-status');
                        const infoBox = document.getElementById('ruangan-info');

                        // Logic: Jika ruangan penuh DAN bukan ruangan asli (sedang mencoba pindah)
                        if (data.tersedia <= 0 && ruanganId != originalRuanganId) {
                            badgeStatus.className = 'badge rounded-pill bg-danger';
                            badgeStatus.innerHTML = '<i class="bi bi-x-circle me-1"></i> Penuh';
                            infoBox.classList.add('full');

                            selectedRuanganFull = true;
                            submitBtn.disabled = true;
                            submitBtn.innerHTML = '<i class="bi bi-lock-fill me-2"></i> Ruangan Penuh';
                        } else {
                            // Jika ruangan tersedia ATAU ini adalah ruangan dia sendiri (aman)
                            badgeStatus.className = 'badge rounded-pill bg-success';
                            badgeStatus.innerHTML = '<i class="bi bi-check-circle me-1"></i> Tersedia';

                            // Tambahan teks jika ini ruangan lama
                            if (ruanganId == originalRuanganId) {
                                badgeStatus.className = 'badge rounded-pill bg-info text-dark';
                                badgeStatus.innerHTML =
                                    '<i class="bi bi-house-door me-1"></i> Ruangan Saat Ini';
                            }

                            infoBox.classList.remove('full');
                            selectedRuanganFull = false;
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = 'Simpan Perubahan <i class="bi bi-check-lg ms-2"></i>';
                        }

                        ruanganInfo.style.display = 'block';
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        ruanganInfo.style.display = 'none';
                    });
            }

            // Load info saat halaman pertama kali dibuka jika ada ruangan terpilih
            if (ruanganSelect.value) {
                loadRuanganInfo(ruanganSelect.value);
            }

            ruanganSelect.addEventListener('change', function() {
                loadRuanganInfo(this.value);
            });

            document.getElementById('form-mahasiswa').addEventListener('submit', function(e) {
                if (selectedRuanganFull) {
                    e.preventDefault();
                    alert('Ruangan tujuan sudah penuh. Silakan pilih ruangan lain.');
                }
            });
        });
        document.getElementById('foto-input').addEventListener('change', function(event) {
        const file = event.target.files[0];
        const previewContainer = document.getElementById('preview-container');
        const imgPreview = document.getElementById('img-preview');

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imgPreview.src = e.target.result;
                previewContainer.style.display = 'block';
            }
            reader.readAsDataURL(file);
        } else {
            previewContainer.style.display = 'none';
        }
    });
    </script>
@endsection
