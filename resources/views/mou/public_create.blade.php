@extends('layouts.public')

@section('title', 'Tambah MOU (Publik)')

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
            --text-muted: #95a5a6;
            --card-radius: 16px;
            --transition: 0.3s ease;
        }

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

        .form-control[type="file"] {
            padding-top: 0.85rem;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--custom-maroon-light);
        }

        /* Styling untuk Download Box */
        .file-download-box {
            display: block;
            padding: 1rem;
            border: 1px solid #e9ecef;
            border-radius: 10px;
            background: #fff;
            transition: var(--transition);
        }

        .file-download-box:hover {
            border-color: var(--custom-maroon-light);
            background: var(--custom-maroon-subtle);
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
        }

        .btn-light-custom {
            background: #fff;
            border: 1px solid #dee2e6;
            color: var(--text-dark);
            border-radius: 50px;
            padding: 0.8rem 1.5rem;
            font-weight: 600;
            transition: var(--transition);
        }

        .btn-light-custom:hover {
            background: #f8f9fa;
            color: var(--custom-maroon);
        }

        /* --- Choices.js Custom Styling --- */
        .choices__inner {
            background-color: #fff;
            border: 1px solid #dee2e6;
            border-radius: 10px;
            padding: 0.5rem;
        }

        .choices__list--single {
            padding: 0.2rem;
        }

        .choices__button {
            display: flex;
            align-items: center;
            cursor: pointer;
        }

        .choices[data-type*="select-one"] .choices__button {
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%237c1316' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
            padding-right: 1.5rem;
        }

        .choices__list--dropdown,
        .choices__list[aria-expanded] {
            border-color: #7c1316;
            box-shadow: 0 4px 8px rgba(124, 19, 22, 0.1);
        }

        .choices__item--selectable.is-highlighted {
            background-color: #fcf0f1;
            color: #7c1316;
        }

        .choices__item--selectable {
            padding: 0.5rem 1rem;
        }
    </style>

    <div class="row justify-content-center mt-5 mb-5">
        <div class="col-md-9 col-lg-8">
            <div class="form-card">

                {{-- HEADER CARD --}}
                <div class="card-header-custom">
                    <h4 class="mb-0 fw-bold">
                        <i class="bi bi-file-earmark-plus-fill me-2"></i> Form Tambah MOU (Publik)
                    </h4>
                    <p class="mb-0 small opacity-75">Isi detail Memorandum of Understanding di bawah ini.</p>
                </div>

                {{-- BODY CARD --}}
                <div class="card-body p-4 p-md-5">

                    {{-- ERROR ALERT --}}
                    @if ($errors->any())
                        <div class="alert alert-danger rounded-3 shadow-sm mb-4">
                            <h6 class="alert-heading fw-bold">Whoops! Ada masalah.</h6>
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
                        <h6 class="text-muted text-uppercase fw-bold mb-3">Informasi MOU</h6>

                        <div class="mb-3">
                            <label class="form-label">Nama Instansi <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-building"></i></span>
                                <input type="text" class="form-control @error('nama_instansi') is-invalid @enderror"
                                    name="nama_instansi" value="{{ old('nama_instansi') }}" required>
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
                            <label class="form-label">Jenis Instansi (lainnya)</label>
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
                                    name="alamat_instansi" value="{{ old('alamat_instansi') }}">
                            </div>
                            @error('alamat_instansi')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Masuk <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-calendar-plus"></i></span>
                                    <input type="date" class="form-control @error('tanggal_masuk') is-invalid @enderror"
                                        name="tanggal_masuk" value="{{ old('tanggal_masuk') }}" required>
                                </div>
                                @error('tanggal_masuk')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Keluar <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-calendar-check"></i></span>
                                    <input type="date" class="form-control @error('tanggal_keluar') is-invalid @enderror"
                                        name="tanggal_keluar" value="{{ old('tanggal_keluar') }}" required>
                                </div>
                                @error('tanggal_keluar')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Rencana Kerja Sama</label>
                            <div class="input-group">
                                <span class="input-group-text align-items-start pt-3"><i class="bi bi-clipboard-data"></i></span>
                                <textarea class="form-control @error('rencana_kerja_sama') is-invalid @enderror"
                                    name="rencana_kerja_sama" rows="3">{{ old('rencana_kerja_sama') }}</textarea>
                            </div>
                            @error('rencana_kerja_sama')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama PIC Instansi</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                                    <input type="text" class="form-control @error('nama_pic_instansi') is-invalid @enderror"
                                        name="nama_pic_instansi" value="{{ old('nama_pic_instansi') }}">
                                </div>
                                @error('nama_pic_instansi')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nomor Kontak PIC</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-telephone-fill"></i></span>
                                    <input type="text" class="form-control @error('nomor_kontak_pic') is-invalid @enderror"
                                        name="nomor_kontak_pic" value="{{ old('nomor_kontak_pic') }}">
                                </div>
                                @error('nomor_kontak_pic')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- SEKSI 2: DOWNLOAD CENTER --}}
                        <hr class="my-4 border-light">
                        <h6 class="text-muted text-uppercase fw-bold mb-3">Download Dokumen Pendukung</h6>

                        <div class="mb-4">
                            <div class="alert alert-info shadow-sm border-0" role="alert" style="background-color: #f0f7ff;">
                                <h6 class="fw-bold mb-2 text-primary"><i class="bi bi-info-circle-fill me-2"></i>Contoh & Template</h6>
                                <p class="small text-muted mb-3">Silakan unduh dokumen berikut sebagai referensi sebelum mengunggah berkas.</p>

                                <div class="d-flex flex-column gap-2">
                                    {{-- File 1 --}}
                                    <a href="{{ asset('storage/pdfmou/draft_mou_smk.pdf') }}" target="_blank"
                                        class="file-download-box text-decoration-none">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-file-earmark-pdf-fill text-danger fs-3 me-3"></i>
                                                <div>
                                                    <div class="fw-bold text-dark">Contoh Draft MoU SMK</div>
                                                    <small class="text-muted">Download contoh draft untuk SMK</small>
                                                </div>
                                            </div>
                                            <i class="bi bi-download text-secondary"></i>
                                        </div>
                                    </a>

                                    {{-- File 2 --}}
                                    <a href="{{ asset('storage/pdfmou/Draft_Mou_Universitas.pdf') }}" target="_blank"
                                        class="file-download-box text-decoration-none">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-file-earmark-pdf-fill text-danger fs-3 me-3"></i>
                                                <div>
                                                    <div class="fw-bold text-dark">Contoh Draft MoU Universitas</div>
                                                    <small class="text-muted">Download contoh draft untuk Universitas</small>
                                                </div>
                                            </div>
                                            <i class="bi bi-download text-secondary"></i>
                                        </div>
                                    </a>

                                    {{-- File 3 --}}
                                    <a href="{{ asset('storage/pdfmou/tata_tertib_magang.pdf') }}" target="_blank"
                                        class="file-download-box text-decoration-none">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-file-earmark-pdf-fill text-danger fs-3 me-3"></i>
                                                <div>
                                                    <div class="fw-bold text-dark">Tata Tertib Magang/PKL/PKM</div>
                                                    <small class="text-muted">Aturan & panduan pelaksanaan kegiatan</small>
                                                </div>
                                            </div>
                                            <i class="bi bi-download text-secondary"></i>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>

                        {{-- SEKSI 3: UPLOAD DOKUMEN --}}
                        <h6 class="text-muted text-uppercase fw-bold mb-3">Upload Dokumen Kerjasama</h6>

                        <div class="mb-3">
                            <label class="form-label">Upload Surat Permohonan <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-file-earmark-text-fill"></i></span>
                                <input type="file" class="form-control @error('surat_permohonan') is-invalid @enderror"
                                    name="surat_permohonan" accept=".pdf,.doc,.docx" required>
                            </div>
                            @error('surat_permohonan')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Upload SK Pengangkatan Pimpinan</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-file-earmark-fill"></i></span>
                                    <input type="file" class="form-control @error('sk_pengangkatan_pimpinan') is-invalid @enderror"
                                        name="sk_pengangkatan_pimpinan" accept=".pdf,.doc,.docx">
                                </div>
                                @error('sk_pengangkatan_pimpinan')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Upload Sertifikat Akreditasi Prodi</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-award-fill"></i></span>
                                    <input type="file" class="form-control @error('sertifikat_akreditasi_prodi') is-invalid @enderror"
                                        name="sertifikat_akreditasi_prodi" accept=".pdf,.jpg,.jpeg,.png">
                                </div>
                                @error('sertifikat_akreditasi_prodi')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Upload Draft MoU</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-file-earmark-pdf-fill"></i></span>
                                <input type="file" class="form-control @error('draft_mou') is-invalid @enderror"
                                    name="draft_mou" accept=".pdf,.doc,.docx">
                            </div>
                            @error('draft_mou')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- SEKSI 4: CATATAN --}}
                        <hr class="my-4 border-light">
                        <h6 class="text-muted text-uppercase fw-bold mb-3">Catatan (Opsional)</h6>

                        <div class="mb-3">
                            <label class="form-label">Keterangan</label>
                            <div class="input-group">
                                <span class="input-group-text align-items-start pt-3"><i class="bi bi-pencil-square"></i></span>
                                <textarea class="form-control @error('keterangan') is-invalid @enderror"
                                    name="keterangan" rows="4">{{ old('keterangan') }}</textarea>
                            </div>
                            @error('keterangan')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- TOMBOL AKSI --}}
                        <div class="d-flex justify-content-end align-items-center pt-4">
                            <button type="submit" class="btn btn-maroon">
                                Simpan Data <i class="bi bi-check-lg ms-2"></i>
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    {{-- SCRIPT --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Choices.js untuk Jenis Instansi
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

            // Trigger toggle on change
            jenisSelect.addEventListener('change', toggleLainnya);
            toggleLainnya(); // Run on init

            // Show SweetAlert success message if session has success
            @if (session('success'))
                Swal.fire({
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    icon: 'success',
                    confirmButtonColor: '#7c1316',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Reset form after user clicks OK
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
