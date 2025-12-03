@extends('layouts.app')

@section('title', 'Edit Data MOU')
@section('page-title', 'Edit Data MOU') {{-- Tetap di sini jika layout Anda membutuhkannya --}}

@section('content')
    {{--
      =====================================================
      STYLE KUSTOM STANDAR (Maroon Header & Pill Button)
      =====================================================
    --}}
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

        /* Khusus untuk file input agar tidak aneh */
        .form-control[type="file"] {
            padding-top: 0.85rem;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--custom-maroon-light);
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
    {{-- ================= END STYLE ================= --}}


    {{--
      =====================================================
      STRUKTUR HTML BARU MENGIKUTI STYLE STANDAR
      =====================================================
    --}}
    <div class="row justify-content-center animate-up">
        <div class="col-md-9 col-lg-8">
            <div class="form-card">

                {{-- CARD HEADER --}}
                <div class="card-header-custom">
                    <h4 class="mb-0 fw-bold">
                        <i class="bi bi-pencil-square me-2"></i> Form Edit MOU
                    </h4>
                    <p class="mb-0 small opacity-75">Perbarui detail Memorandum of Understanding.</p>
                </div>

                {{-- CARD BODY --}}
                <div class="card-body p-4 p-md-5">

                    {{-- ERROR VALIDASI --}}
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

                    <form action="{{ route('mou.update', $mou->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT') {{-- PENTING untuk method UPDATE --}}

                        {{-- SEKSI 1 --}}
                        <h6 class="text-muted text-uppercase fw-bold mb-3" style="font-size: 0.75rem; letter-spacing: 1px;">
                            Informasi MOU
                        </h6>

                        <div class="mb-3">
                            <label class="form-label">Nama Instansi <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-building"></i></span>
                                <input type="text" class="form-control @error('nama_instansi') is-invalid @enderror"
                                    name="nama_instansi" value="{{ old('nama_instansi', $mou->nama_instansi) }}"
                                    required>
                            </div>
                            @error('nama_instansi')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Masuk <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-calendar-plus"></i></span>
                                    <input type="date" class="form-control @error('tanggal_masuk') is-invalid @enderror"
                                        name="tanggal_masuk"
                                        value="{{ old('tanggal_masuk', $mou->tanggal_masuk->format('Y-m-d')) }}" required>
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
                                        name="tanggal_keluar"
                                        value="{{ old('tanggal_keluar', $mou->tanggal_keluar->format('Y-m-d')) }}" required>
                                </div>
                                @error('tanggal_keluar')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4 border-light">
                        <div class="mb-3">
                            <label class="form-label">Rencana Kerja Sama</label>
                            <div class="input-group">
                                <span class="input-group-text align-items-start pt-3"><i class="bi bi-clipboard-data"></i></span>
                                <textarea class="form-control @error('rencana_kerja_sama') is-invalid @enderror" name="rencana_kerja_sama" rows="3" placeholder="Jelaskan singkat rencana kerja sama">{{ old('rencana_kerja_sama', $mou->rencana_kerja_sama) }}</textarea>
                            </div>
                            @error('rencana_kerja_sama')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Jenis Instansi</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-bank"></i></span>
                                    <select name="jenis_instansi" id="jenis_instansi" class="form-select @error('jenis_instansi') is-invalid @enderror">
                                        <option value="">-- Pilih Jenis Instansi --</option>
                                        <option value="Instansi Pemerintah" {{ old('jenis_instansi', $mou->jenis_instansi) == 'Instansi Pemerintah' ? 'selected' : '' }}>Instansi Pemerintah</option>
                                        <option value="Instansi Swasta" {{ old('jenis_instansi', $mou->jenis_instansi) == 'Instansi Swasta' ? 'selected' : '' }}>Instansi Swasta</option>
                                        <option value="Instansi Internasional" {{ old('jenis_instansi', $mou->jenis_instansi) == 'Instansi Internasional' ? 'selected' : '' }}>Instansi Internasional</option>
                                        <option value="Lainnya" {{ old('jenis_instansi', $mou->jenis_instansi) == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                    </select>
                                </div>
                                @error('jenis_instansi')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div id="jenisInstansiLainnyaWrapper" class="mb-3" style="display: none;">
                            <label class="form-label">Jenis Instansi (lainnya)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-buildings"></i></span>
                                <input type="text" class="form-control @error('jenis_instansi_lainnya') is-invalid @enderror" name="jenis_instansi_lainnya" value="{{ old('jenis_instansi_lainnya', $mou->jenis_instansi_lainnya) }}" placeholder="Tuliskan jenis instansi">
                            </div>
                            @error('jenis_instansi_lainnya')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Alamat Instansi</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                                <input type="text" class="form-control @error('alamat_instansi') is-invalid @enderror" name="alamat_instansi" value="{{ old('alamat_instansi', $mou->alamat_instansi) }}" placeholder="Alamat lengkap instansi">
                            </div>
                            @error('alamat_instansi')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- SEKSI 2 --}}
                        <h6 class="text-muted text-uppercase fw-bold mb-3" style="font-size: 0.75rem; letter-spacing: 1px;">
                            Upload Dokumen Kerjasama
                        </h6>

                        {{-- Download Center --}}
                        <div class="mb-3">
                            <div class="alert alert-info shadow-sm" role="alert">
                                <h6 class="fw-bold mb-2">Pusat Unduhan Template</h6>
                                <div class="d-flex flex-column">
                                    <a href="{{ asset('storage/pdfmou/draft_mou_smk.pdf') }}" target="_blank" class="file-download-box text-decoration-none mb-2">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-file-earmark-pdf-fill text-danger fs-3 me-3"></i>
                                            <div>
                                                <div class="fw-bold text-dark">Contoh Draft MoU SMK</div>
                                                <small class="text-muted">Download contoh draft untuk SMK</small>
                                            </div>
                                        </div>
                                        <i class="bi bi-download text-secondary"></i>
                                    </a>
                                    <a href="{{ asset('storage/pdfmou/draft_mou_universitas.pdf') }}" target="_blank" class="file-download-box text-decoration-none mb-2">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-file-earmark-pdf-fill text-danger fs-3 me-3"></i>
                                            <div>
                                                <div class="fw-bold text-dark">Contoh Draft MoU Universitas</div>
                                                <small class="text-muted">Download contoh draft untuk Universitas</small>
                                            </div>
                                        </div>
                                        <i class="bi bi-download text-secondary"></i>
                                    </a>
                                    <a href="{{ asset('storage/pdfmou/tata_tertib_magang.pdf') }}" target="_blank" class="file-download-box text-decoration-none">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-file-earmark-pdf-fill text-danger fs-3 me-3"></i>
                                            <div>
                                                <div class="fw-bold text-dark">Tata Tertib Pelaksanaan Magang/PKL/PKM/TPM</div>
                                                <small class="text-muted">Aturan & panduan pelaksanaan kegiatan</small>
                                            </div>
                                        </div>
                                        <i class="bi bi-download text-secondary"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Upload Surat Permohonan Kerjasama <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-file-earmark-text-fill"></i></span>
                                    <input type="file" class="form-control @error('surat_permohonan') is-invalid @enderror" name="surat_permohonan" accept=".pdf,.doc,.docx">
                                </div>
                                @if($mou->surat_permohonan)
                                <small class="form-text text-muted ms-1">File saat ini: <a href="{{ Storage::url($mou->surat_permohonan) }}" target="_blank">Lihat</a></small>
                                @endif
                                @error('surat_permohonan')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Upload SK Pengangkatan Pimpinan Instansi</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-file-earmark-fill"></i></span>
                                    <input type="file" class="form-control @error('sk_pengangkatan_pimpinan') is-invalid @enderror" name="sk_pengangkatan_pimpinan" accept=".pdf,.doc,.docx">
                                </div>
                                @if($mou->sk_pengangkatan_pimpinan)
                                <small class="form-text text-muted ms-1">File saat ini: <a href="{{ Storage::url($mou->sk_pengangkatan_pimpinan) }}" target="_blank">Lihat</a></small>
                                @endif
                                @error('sk_pengangkatan_pimpinan')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Upload Sertifikat Akreditasi Prodi</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-award-fill"></i></span>
                                    <input type="file" class="form-control @error('sertifikat_akreditasi_prodi') is-invalid @enderror" name="sertifikat_akreditasi_prodi" accept=".pdf,.jpg,.jpeg,.png">
                                </div>
                                @if($mou->sertifikat_akreditasi_prodi)
                                <small class="form-text text-muted ms-1">File saat ini: <a href="{{ Storage::url($mou->sertifikat_akreditasi_prodi) }}" target="_blank">Lihat</a></small>
                                @endif
                                @error('sertifikat_akreditasi_prodi')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Upload Draft MoU</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-file-earmark-pdf-fill"></i></span>
                                    <input type="file" class="form-control @error('draft_mou') is-invalid @enderror" name="draft_mou" accept=".pdf,.doc,.docx">
                                </div>
                                @if($mou->draft_mou)
                                <small class="form-text text-muted ms-1">File saat ini: <a href="{{ Storage::url($mou->draft_mou) }}" target="_blank">Lihat</a></small>
                                @endif
                                @error('draft_mou')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4 border-light">

                        {{-- SEKSI 3 --}}
                        <h6 class="text-muted text-uppercase fw-bold mb-3" style="font-size: 0.75rem; letter-spacing: 1px;">
                            Catatan (Opsional)
                        </h6>

                        <div class="mb-3">
                            <label class="form-label">Keterangan</label>
                            <div class="input-group">
                                <span class="input-group-text align-items-start pt-3"><i
                                        class="bi bi-pencil-square"></i></span>
                                <textarea class="form-control @error('keterangan') is-invalid @enderror" name="keterangan" rows="4"
                                    placeholder="Tambahkan catatan jika perlu...">{{ old('keterangan', $mou->keterangan) }}</textarea>
                            </div>
                            @error('keterangan')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama PIC Instansi</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                                    <input type="text" class="form-control @error('nama_pic_instansi') is-invalid @enderror" name="nama_pic_instansi" value="{{ old('nama_pic_instansi', $mou->nama_pic_instansi) }}" placeholder="Nama PIC">
                                </div>
                                @error('nama_pic_instansi')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nomor Kontak PIC</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-telephone-fill"></i></span>
                                    <input type="text" class="form-control @error('nomor_kontak_pic') is-invalid @enderror" name="nomor_kontak_pic" value="{{ old('nomor_kontak_pic', $mou->nomor_kontak_pic) }}" placeholder="Nomor telepon/WA PIC">
                                </div>
                                @error('nomor_kontak_pic')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- TOMBOL AKSI --}}
                        <div class="d-flex justify-content-between align-items-center pt-4">
                            <a href="{{ route('mou.index') }}" class="btn btn-light-custom shadow-sm">
                                <i class="bi bi-arrow-left me-2"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-maroon">
                                Simpan Perubahan <i class="bi bi-check-lg ms-2"></i>
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    {{-- Script untuk SweetAlert (dari kode Anda) --}}
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 1800,
                    toast: true,
                    position: 'top-end'
                });
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian!',
                    text: '{{ session('error') }}',
                    showConfirmButton: true
                });
            });
        </script>
    @endif
@endsection

@section('scripts')
    {{-- Pastikan SweetAlert di-load, jika belum ada di layout utama --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const jenisSelect = document.getElementById('jenis_instansi');
            const wrapper = document.getElementById('jenisInstansiLainnyaWrapper');
            function toggleLainnya() {
                if (!jenisSelect) return;
                if (jenisSelect.value === 'Lainnya') {
                    wrapper.style.display = 'block';
                } else {
                    wrapper.style.display = 'none';
                }
            }
            if (jenisSelect) {
                jenisSelect.addEventListener('change', toggleLainnya);
                toggleLainnya();
            }
        });
    </script>
@endsection
