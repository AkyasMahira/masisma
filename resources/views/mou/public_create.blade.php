@extends('layouts.public')

@section('title', 'Pengajuan MOU - Sindikat RSUD Simpang Lima Gumul Kediri')

@section('content')
    {{--
      =====================================================
      STYLE KUSTOM
      =====================================================
    --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    <style>
        :root {
            --custom-maroon: #7c1316;
            --custom-maroon-light: #a3191d;
            --custom-maroon-subtle: #fcf0f1;
            --text-dark: #2c3e50;
            --text-muted: #6c757d;
            --card-radius: 16px;
            --transition: 0.3s ease;
        }

        body {
            background-color: #f8f9fa;
        }

        /* --- Styling Card Form --- */
        .form-card {
            border: none;
            border-radius: var(--card-radius);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            background: #fff;
            overflow: hidden;
            height: 100%;
        }

        .card-header-custom {
            background-color: var(--custom-maroon);
            padding: 1.5rem;
            color: white;
            border-bottom: 4px solid var(--custom-maroon-light);
        }

        /* --- Styling Info Box (Kiri) --- */
        .info-card {
            background: transparent;
            border: none;
        }

        .alur-image-container {
            border-radius: var(--card-radius);
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border: 2px solid #fff;
            margin-bottom: 1.5rem;
        }

        .alur-image-container img {
            width: 100%;
            height: auto;
            display: block;
            transition: transform 0.3s ease;
        }

        .alur-image-container:hover img {
            transform: scale(1.02);
        }

        .contact-box {
            background-color: #fff;
            border-left: 5px solid var(--custom-maroon);
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .contact-icon {
            width: 50px;
            height: 50px;
            background-color: var(--custom-maroon-subtle);
            color: var(--custom-maroon);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        /* --- Form Elements --- */
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

        .form-control, .form-select {
            border-left: none;
            border-radius: 0 10px 10px 0;
            padding: 0.7rem 1rem;
            border-color: #dee2e6;
            box-shadow: none !important;
            transition: border-color 0.2s;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--custom-maroon-light);
        }

        .btn-maroon {
            background-color: var(--custom-maroon);
            color: white;
            border: none;
            padding: 0.8rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            transition: var(--transition);
        }

        .btn-maroon:hover {
            background-color: var(--custom-maroon-light);
            color: white;
            box-shadow: 0 4px 12px rgba(124, 19, 22, 0.3);
        }

        /* --- File Download Box --- */
        .file-download-box {
            display: block;
            padding: 1rem;
            border: 1px solid #e9ecef;
            border-radius: 10px;
            background: #fff;
            transition: var(--transition);
            margin-bottom: 0.5rem;
        }

        .file-download-box:hover {
            border-color: var(--custom-maroon-light);
            background: var(--custom-maroon-subtle);
        }

        /* --- Choices.js Override --- */
        .choices__inner {
            background-color: #fff;
            border: 1px solid #dee2e6;
            border-radius: 10px;
            padding: 0.5rem;
        }
    </style>

    <div class="container-fluid px-md-5 mt-5 mb-5">
        <div class="row g-4">
            
            {{-- KOLOM KIRI: INFO & ALUR --}}
            <div class="col-lg-5 order-1 order-lg-1">
                <div class="info-card sticky-top" style="top: 2rem; z-index: 1;">
                    
                    {{-- Teks Pengantar --}}
                    <div class="mb-4">
                        <h2 class="fw-bold text-dark mb-3">Formulir Pengajuan Nota Kesepahaman</h2>
                        <h5 class="text-muted fw-normal mb-3">Tim Diklat RSUD SLG Kediri</h5>
                        <p class="text-secondary" style="line-height: 1.6;">
                            Yth. Bapak/Ibu Calon Mitra Tim Diklat RSUD SLG Kediri. <br>
                            Sebagai bentuk kerja sama dalam bidang pendidikan dan pelatihan, kami mengundang Bapak/Ibu untuk mengisi formulir kerja sama di samping.  <strong>Bagi Institusi diluar Kabupaten Kediri, harus memiliki MOU dengan Pemerintah Kabupaten Kediri. </strong>
                        </p>
                    </div>

                    {{-- Gambar Alur --}}
                    <div class="mb-4">
                        <label class="form-label text-uppercase text-muted small fw-bold ls-1">Alur Pengajuan</label>
                        <div class="alur-image-container">
                            {{-- Pastikan file alur.png ada di folder public --}}
                            <img src="{{ asset('alur.png') }}" alt="Alur Pengajuan Kerjasama">
                        </div>
                    </div>

                    {{-- Contact Person Box --}}
                    <div class="contact-box">
                        <div class="contact-icon">
                            <i class="bi bi-whatsapp"></i>
                        </div>
                        <div>
                            <p class="mb-1 text-muted small fw-bold text-uppercase">Butuh Bantuan Koordinasi?</p>
                            <h6 class="fw-bold mb-1">Contact Person – Diklat RSUD Simpang Lima Gumul</h6>
                            <a href="https://wa.me/6282245415977" target="_blank" class="text-decoration-none text-dark stretched-link">
                                0822 4541 5977
                            </a>
                        </div>
                    </div>

                </div>
            </div>

            {{-- KOLOM KANAN: FORMULIR --}}
            <div class="col-lg-7 order-2 order-lg-2">
                <div class="form-card">
                    {{-- HEADER CARD --}}
                    <div class="card-header-custom">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-file-earmark-plus-fill me-2"></i> Form Isian Data Mitra
                        </h5>
                        <p class="mb-0 small opacity-75">Mohon lengkapi data di bawah ini dengan benar.</p>
                    </div>

                    {{-- BODY CARD --}}
                    <div class="card-body p-4 p-md-5">

                        {{-- ERROR ALERT --}}
                        @if ($errors->any())
                            <div class="alert alert-danger rounded-3 shadow-sm mb-4">
                                <h6 class="alert-heading fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i>Perhatian</h6>
                                <ul class="mb-0 small ps-3">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('public.mou.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            {{-- SEKSI 1: INFORMASI MOU --}}
                            <h6 class="text-muted text-uppercase fw-bold mb-3 border-bottom pb-2">Informasi Instansi</h6>

                            <div class="mb-3">
                                <label class="form-label">Nama Instansi <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-building"></i></span>
                                    <input type="text" class="form-control @error('nama_instansi') is-invalid @enderror"
                                        name="nama_instansi" value="{{ old('nama_instansi') }}" placeholder="Contoh: Universitas Teknologi..." required>
                                </div>
                                @error('nama_instansi')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Jenis Instansi <span class="text-danger">*</span></label>
                                    <select name="jenis_instansi" id="jenis_instansi"
                                        class="form-select @error('jenis_instansi') is-invalid @enderror" required>
                                        <option value="">-- Pilih Jenis Instansi --</option>
                                        <option value="Instansi Pemerintah" {{ old('jenis_instansi') == 'Instansi Pemerintah' ? 'selected' : '' }}>Instansi Pemerintah</option>
                                        <option value="Instansi Swasta" {{ old('jenis_instansi') == 'Instansi Swasta' ? 'selected' : '' }}>Instansi Swasta</option>
                                        <option value="Instansi Internasional" {{ old('jenis_instansi') == 'Instansi Internasional' ? 'selected' : '' }}>Instansi Internasional</option>
                                        <option value="Lainnya" {{ old('jenis_instansi') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                    </select>
                                    @error('jenis_instansi')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div id="jenisInstansiLainnyaWrapper" class="mb-3" style="display:none;">
                                <label class="form-label">Sebutkan Jenis Instansi</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-buildings"></i></span>
                                    <input type="text" class="form-control @error('jenis_instansi_lainnya') is-invalid @enderror"
                                        name="jenis_instansi_lainnya" value="{{ old('jenis_instansi_lainnya') }}">
                                </div>
                                @error('jenis_instansi_lainnya')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Alamat Instansi</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                                    <input type="text" class="form-control @error('alamat_instansi') is-invalid @enderror"
                                        name="alamat_instansi" value="{{ old('alamat_instansi') }}"
                                        placeholder="Alamat lengkap instansi">
                                </div>
                                @error('alamat_instansi')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nomor PKS (RSUD)</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-hash"></i></span>
                                        <input type="text" class="form-control @error('no_pks') is-invalid @enderror"
                                            name="no_pks" value="{{ old('no_pks') }}"
                                            placeholder="Nomor PKS Rsud">
                                    </div>
                                    @error('no_pks')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nomor PKS (Instansi)</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-card-text"></i></span>
                                        <input type="text" class="form-control @error('no_pks_instansi') is-invalid @enderror"
                                            name="no_pks_instansi" value="{{ old('no_pks_instansi') }}"
                                            placeholder="Nomor PKS Instansi">
                                    </div>
                                    @error('no_pks_instansi')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tanggal Masuk <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-calendar-plus"></i></span>
                                        <input type="date" class="form-control @error('tanggal_masuk') is-invalid @enderror"
                                            name="tanggal_masuk" value="{{ old('tanggal_masuk') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tanggal Keluar <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-calendar-check"></i></span>
                                        <input type="date" class="form-control @error('tanggal_keluar') is-invalid @enderror"
                                            name="tanggal_keluar" value="{{ old('tanggal_keluar') }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Rencana Kerja Sama</label>
                                <div class="input-group">
                                    <span class="input-group-text align-items-start pt-3"><i class="bi bi-clipboard-data"></i></span>
                                    <textarea class="form-control @error('rencana_kerja_sama') is-invalid @enderror"
                                        name="rencana_kerja_sama" rows="3" placeholder="Deskripsi singkat rencana kerja sama">{{ old('rencana_kerja_sama') }}</textarea>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nama PIC Instansi</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                                        <input type="text" class="form-control @error('nama_pic_instansi') is-invalid @enderror"
                                            name="nama_pic_instansi" value="{{ old('nama_pic_instansi') }} ">
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nomor Kontak PIC</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-telephone-fill"></i></span>
                                        <input type="text" class="form-control @error('nomor_kontak_pic') is-invalid @enderror"
                                            name="nomor_kontak_pic" value="{{ old('nomor_kontak_pic') }}">
                                    </div>
                                </div>
                            </div>

                            {{-- SEKSI 2: DOWNLOAD CENTER --}}
                            <div class="mt-4 mb-3">
                                <h6 class="text-muted text-uppercase fw-bold mb-3 border-bottom pb-2">Dokumen Pendukung</h6>
                                <div class="alert alert-info shadow-sm border-0 d-flex align-items-start" role="alert" style="background-color: #f0f7ff;">
                                    <i class="bi bi-info-circle-fill text-primary mt-1 me-3 fs-5"></i>
                                    <div>
                                        <h6 class="fw-bold text-primary mb-1">Unduh Template</h6>
                                        <p class="small text-muted mb-2">Silakan unduh dokumen berikut sebagai referensi.</p>
                                        
                                        <div class="d-flex flex-column gap-2 mt-2">
                                        <a href="{{ asset('storage/mousmks.pdf') }}" target="_blank" class="small text-decoration-none fw-bold text-dark hover-maroon">
    <i class="bi bi-file-pdf text-danger me-1"></i> Contoh Draft MoU SMK
</a>
                                        <a href="{{ asset('storage/moufakultas.pdf') }}" target="_blank" class="small text-decoration-none fw-bold text-dark hover-maroon">
    <i class="bi bi-file-pdf text-danger me-1"></i> Contoh Draft MoU Fakultas
</a>
<a href="{{ asset('storage/mouunivs.pdf') }}" target="_blank" class="small text-decoration-none fw-bold text-dark hover-maroon">
    <i class="bi bi-file-pdf text-danger me-1"></i> Contoh Draft MoU Universitas
</a>
<a href="{{ asset('storage/surat.pdf') }}" target="_blank" class="small text-decoration-none fw-bold text-dark hover-maroon">
    <i class="bi bi-file-pdf text-danger me-1"></i> Surat Edaran Keterangan Penelitian
</a>
<a href="{{ asset('storage/tatib.pdf') }}" target="_blank" class="small text-decoration-none fw-bold text-dark hover-maroon">
    <i class="bi bi-file-pdf text-danger me-1"></i> Tata Tertib Magang
</a>

                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- SEKSI 3: UPLOAD DOKUMEN --}}
                            <h6 class="text-muted text-uppercase fw-bold mb-3 border-bottom pb-2">Upload Berkas</h6>

                            <div class="mb-3">
                                <label class="form-label">Surat Permohonan <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-file-earmark-text-fill"></i></span>
                                    <input type="file" class="form-control @error('surat_permohonan') is-invalid @enderror"
                                        name="surat_permohonan" accept=".pdf,.doc,.docx" required>
                                </div>
                                <small class="text-muted d-block mt-1">Format: PDF/DOCX</small>
                                @error('surat_permohonan')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">SK Pengangkatan Pimpinan</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-file-earmark-fill"></i></span>
                                        <input type="file" class="form-control @error('sk_pengangkatan_pimpinan') is-invalid @enderror"
                                            name="sk_pengangkatan_pimpinan" accept=".pdf,.doc,.docx">
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Sertifikat Akreditasi</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-award-fill"></i></span>
                                        <input type="file" class="form-control @error('sertifikat_akreditasi_prodi') is-invalid @enderror"
                                            name="sertifikat_akreditasi_prodi" accept=".pdf,.jpg,.jpeg,.png">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Upload Draft MoU</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-file-earmark-pdf-fill"></i></span>
                                    <input type="file" class="form-control @error('draft_mou') is-invalid @enderror"
                                        name="draft_mou" accept=".pdf,.doc,.docx">
                                </div>
                            </div>

                            {{-- SEKSI 4: CATATAN --}}
                            <div class="mb-4">
                                <label class="form-label">Catatan Tambahan</label>
                                <div class="input-group">
                                    <span class="input-group-text align-items-start pt-3"><i class="bi bi-pencil-square"></i></span>
                                    <textarea class="form-control @error('keterangan') is-invalid @enderror"
                                        name="keterangan" rows="2" placeholder="Jika ada pesan tambahan">{{ old('keterangan') }}</textarea>
                                </div>
                            </div>

                            {{-- TOMBOL AKSI --}}
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-maroon btn-lg shadow">
                                    <i class="bi bi-send-fill me-2"></i> Kirim Pengajuan
                                </button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- SCRIPT --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Choices.js
            const jenisSelect = document.getElementById('jenis_instansi');
            const choicesInstance = new Choices(jenisSelect, {
                searchEnabled: true,
                itemSelectText: '',
                placeholderValue: '-- Pilih Jenis Instansi --',
                shouldSort: false,
                shouldSortItems: false
            });

            const wrapper = document.getElementById('jenisInstansiLainnyaWrapper');

            function toggleLainnya() {
                const selectedValue = jenisSelect.value;
                wrapper.style.display = (selectedValue === 'Lainnya') ? 'block' : 'none';
            }

            jenisSelect.addEventListener('change', toggleLainnya);
            toggleLainnya();

            @if (session('success'))
                Swal.fire({
                    title: 'Berhasil Terkirim!',
                    text: '{{ session('success') }}',
                    icon: 'success',
                    confirmButtonColor: '#7c1316',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.querySelector('form').reset();
                        choicesInstance.clearStore();
                        jenisSelect.value = '';
                        choicesInstance.setChoiceByValue('');
                        toggleLainnya();
                    }
                });
            @endif
        });
    </script>
@endsection