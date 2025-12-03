@extends('layouts.app')

@section('title', 'Tambah Pengajuan Pra Penelitian')
@section('page-title', 'Tambah Pengajuan Pra Penelitian')

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

        /* Input Group Style (Konsisten) */
        .input-group-text {
            background-color: #f8f9fa;
            border-right: none;
            color: var(--custom-maroon);
            border-top-left-radius: 10px;
            border-bottom-left-radius: 10px;
            border-color: #dee2e6;
        }

        .form-control,
        .form-select {
            border-left: none;
            border-radius: 0 10px 10px 0;
            padding: 0.7rem 1rem;
            border-color: #dee2e6;
            box-shadow: none !important;
            transition: border-color 0.2s;
            color: var(--text-dark);
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--custom-maroon-light);
        }

        /* --- Dynamic Section (Mahasiswa & Dosen) --- */
        .dynamic-section {
            background-color: #fff;
            border: 2px dashed #e9ecef;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            position: relative;
            transition: border-color 0.3s;
        }

        .dynamic-section:hover {
            border-color: var(--custom-maroon-light);
        }

        .section-badge {
            position: absolute;
            top: -12px;
            left: 20px;
            background: var(--custom-maroon);
            color: white;
            padding: 2px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
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

        .btn-add-row {
            background: #e9ecef;
            color: var(--text-dark);
            border: none;
            border-radius: 8px;
            padding: 5px 12px;
            font-size: 0.85rem;
            font-weight: 600;
            transition: var(--transition);
        }

        .btn-add-row:hover {
            background: var(--custom-maroon);
            color: white;
        }

        .btn-delete-row {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            border: 1px solid #fee2e2;
            background: #fff;
            color: #dc2626;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
        }

        .btn-delete-row:hover {
            background: #dc2626;
            color: white;
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
        <div class="col-lg-10">
            <div class="form-card">
                <div class="card-header-custom">
                    <h4 class="mb-0 fw-bold"><i class="bi bi-journal-plus me-2"></i> Tambah Pengajuan Pra Penelitian</h4>
                    <p class="mb-0 small opacity-75">Isi formulir di bawah ini dengan data yang valid.</p>
                </div>

                <div class="card-body p-4 p-md-5">

                    @if ($errors->any())
                        <div class="alert alert-danger rounded-3 shadow-sm mb-4">
                            <ul class="mb-0 small">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Info Box --}}
                    <div class="alert alert-light border-0 shadow-sm d-flex align-items-center mb-4"
                        style="background-color: var(--custom-maroon-subtle);">
                        <i class="bi bi-info-circle-fill text-custom-maroon me-3 fs-4"></i>
                        <div class="small text-muted">
                            Pastikan Anda telah membaca panduan pengajuan. Semua field bertanda <span
                                class="text-danger">*</span> wajib diisi.
                        </div>
                    </div>

                    <form action="{{ route('pra-penelitian.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        {{-- 1. DATA PENELITIAN --}}
                        <div class="row g-3 mb-4">
                            <div class="col-12">
                                <label class="form-label">Judul Pra Penelitian <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-type-h1"></i></span>
                                    <input type="text" name="judul" class="form-control" value="{{ old('judul') }}"
                                        placeholder="Masukkan judul penelitian..." required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Universitas <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-building"></i></span>
                                    <select name="mou_id" class="form-select" required>
                                        <option value="">-- Pilih Universitas --</option>
                                        @foreach ($mous as $mou)
                                            <option value="{{ $mou->id }}"
                                                {{ old('mou_id') == $mou->id ? 'selected' : '' }}>
                                                {{ $mou->nama_instansi ?? $mou->nama_universitas }} (Exp:
                                                {{ $mou->tanggal_keluar->format('d M Y') }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Jenis Penelitian <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-grid"></i></span>
                                    <select name="jenis_penelitian" class="form-select" required>
                                        <option value="Data Awal"
                                            {{ old('jenis_penelitian') == 'Data Awal' ? 'selected' : '' }}>Data Awal
                                        </option>
                                        <option value="Uji Validitas"
                                            {{ old('jenis_penelitian') == 'Uji Validitas' ? 'selected' : '' }}>Uji Validitas
                                        </option>
                                        <option value="Penelitian"
                                            {{ old('jenis_penelitian') == 'Penelitian' ? 'selected' : '' }}>Penelitian
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Program Studi</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-book"></i></span>
                                    <input type="text" name="prodi" class="form-control" value="{{ old('prodi') }}"
                                        placeholder="Contoh: Psikologi" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Mulai Penelitian</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-calendar-event"></i></span>
                                    <input type="date" name="tanggal_mulai" class="form-control"
                                        value="{{ old('tanggal_mulai') }}" required>
                                </div>
                            </div>
                        </div>

                        {{-- 2. DATA TAMBAHAN (BERKAS) --}}
                        <div class="row g-3 mb-5">
                            <div class="col-md-4">
                                <label class="form-label">Tgl. Rencana Skripsi</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-calendar-check"></i></span>
                                    <input type="date" name="tanggal_rencana_skripsi" class="form-control"
                                        value="{{ old('tanggal_rencana_skripsi') }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Upload Kerangka (PDF)</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-file-earmark-pdf"></i></span>
                                    <input type="file" name="kerangka_penelitian" class="form-control"
                                        accept="application/pdf" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Surat Pengantar (PDF)</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-envelope-paper"></i></span>
                                    <input type="file" name="surat_pengantar" class="form-control"
                                        accept="application/pdf" required>
                                </div>
                            </div>
                        </div>

                        {{-- 3. SECTION DATA MAHASISWA --}}
                        <div class="dynamic-section">
                            <span class="section-badge">Data Mahasiswa</span>

                            <div class="d-flex justify-content-end mb-3">
                                <button type="button" id="tambah-mahasiswa" class="btn-add-row">
                                    <i class="bi bi-plus-lg"></i> Tambah Anggota
                                </button>
                            </div>

                            {{-- Header Tabel (Desktop Only) --}}
                            <div class="row g-3 mb-2 d-none d-md-flex px-2 text-muted fw-bold small text-uppercase">
                                <div class="col-md-4">Nama Mahasiswa</div>
                                <div class="col-md-4">No. WhatsApp</div>
                                <div class="col-md-3">Jenjang (S1/D3)</div>
                                <div class="col-md-1 text-center">Aksi</div>
                            </div>

                            <div id="mahasiswa-list">
                                {{-- Baris Pertama (Default) --}}
                                <div class="row g-3 mb-3 align-items-center mahasiswa-row">
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                                            <input type="text" name="mahasiswas[0][nama]" class="form-control"
                                                placeholder="Nama Lengkap" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-text">+62</span>
                                            <input type="tel" name="mahasiswas[0][no_telpon]" class="form-control"
                                                placeholder="812xxxx" required>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-mortarboard"></i></span>
                                            <input type="text" name="mahasiswas[0][jenjang]" class="form-control"
                                                placeholder="S1" required>
                                        </div>
                                    </div>
                                    <div class="col-md-1 text-center">
                                        <button type="button" class="btn-delete-row hapus-mahasiswa">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- 4. SECTION DOSEN PEMBIMBING --}}
                        <div class="dynamic-section">
                            <span class="section-badge">Dosen Pembimbing</span>

                            <div class="row g-3 mt-1">
                                {{-- Dosen 1 --}}
                                <div class="col-md-6">
                                    <div class="p-3 bg-light rounded-3 border">
                                        <h6 class="fw-bold text-dark mb-3"><i
                                                class="bi bi-1-circle-fill text-secondary me-2"></i> Pembimbing 1</h6>
                                        <div class="mb-3">
                                            <label class="small text-muted fw-bold">Nama Lengkap</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                                                <input type="text" name="dosen1_nama" class="form-control" required>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="small text-muted fw-bold">Nomor HP</label>
                                            <div class="input-group">
                                                <span class="input-group-text">+62</span>
                                                <input type="tel" name="dosen1_hp" class="form-control" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Dosen 2 --}}
                                <div class="col-md-6">
                                    <div class="p-3 bg-light rounded-3 border">
                                        <h6 class="fw-bold text-dark mb-3"><i
                                                class="bi bi-2-circle-fill text-secondary me-2"></i> Pembimbing 2</h6>
                                        <div class="mb-3">
                                            <label class="small text-muted fw-bold">Nama Lengkap</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                                                <input type="text" name="dosen2_nama" class="form-control" required>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="small text-muted fw-bold">Nomor HP</label>
                                            <div class="input-group">
                                                <span class="input-group-text">+62</span>
                                                <input type="tel" name="dosen2_hp" class="form-control" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Footer Action --}}
                        <div class="d-flex justify-content-between align-items-center pt-3">
                            <a href="{{ route('dashboard') }}" class="btn btn-light-custom shadow-sm">
                                <i class="bi bi-arrow-left me-2"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-maroon">
                                Simpan Pengajuan <i class="bi bi-check-lg ms-2"></i>
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- TEMPLATE ROW JS --}}
    <template id="mahasiswa-template">
        <div class="row g-3 mb-3 align-items-center mahasiswa-row animate-up" style="animation-duration: 0.3s;">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                    <input type="text" name="mahasiswas[INDEX][nama]" class="form-control" placeholder="Nama Lengkap"
                        required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text">+62</span>
                    <input type="tel" name="mahasiswas[INDEX][no_telpon]" class="form-control" placeholder="812xxxx"
                        required>
                </div>
            </div>
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-mortarboard"></i></span>
                    <input type="text" name="mahasiswas[INDEX][jenjang]" class="form-control" placeholder="S1"
                        required>
                </div>
            </div>
            <div class="col-md-1 text-center">
                <button type="button" class="btn-delete-row hapus-mahasiswa">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>
    </template>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const list = document.getElementById('mahasiswa-list');
            const addButton = document.getElementById('tambah-mahasiswa');
            const template = document.getElementById('mahasiswa-template');
            let index = 1; // Mulai dari 1 karena 0 sudah ada di HTML statis

            // Format No HP
            function formatPhoneNumber(input) {
                let value = input.value.replace(/[^0-9]/g, '');
                if (value.startsWith('0')) value = value.substring(1);
                if (value.startsWith('62')) value = value.substring(2);
                input.value = value;
            }

            // Event Listener untuk input HP (Delegation)
            list.addEventListener('input', function(e) {
                if (e.target && e.target.name && e.target.name.includes('[no_telpon]')) {
                    formatPhoneNumber(e.target);
                }
            });

            // Tambah Baris
            addButton.addEventListener('click', function() {
                let newRowHtml = template.innerHTML.replace(/INDEX/g, index);
                // Gunakan DOMParser atau insertAdjacentHTML agar tidak merusak event listener yang ada
                list.insertAdjacentHTML('beforeend', newRowHtml);
                index++;
            });

            // Hapus Baris
            list.addEventListener('click', function(e) {
                const btn = e.target.closest('.hapus-mahasiswa');
                if (btn) {
                    if (list.querySelectorAll('.mahasiswa-row').length <= 1) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Tidak bisa menghapus',
                            text: 'Minimal harus ada 1 mahasiswa.',
                            confirmButtonColor: '#7c1316'
                        });
                        return;
                    }

                    Swal.fire({
                        title: 'Hapus baris ini?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#7c1316',
                        cancelButtonColor: 'grey',
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            btn.closest('.mahasiswa-row').remove();
                        }
                    });
                }
            });
        });
    </script>
@endsection
