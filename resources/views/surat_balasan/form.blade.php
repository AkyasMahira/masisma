@extends('layouts.app')

@section('title', 'Form Surat Balasan')
@section('page-title', 'Form Surat Balasan')

@section('content')

    {{-- ========================= --}}
    {{--  Tambah Choices.js CDN    --}}
    {{-- ========================= --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

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
    </style>

    <div class="row justify-content-center animate-up">
        <div class="col-md-10 col-lg-9">
            <div class="form-card">
                <div class="card-header-custom">
                    <h4 class="mb-0 fw-bold"><i class="fas fa-envelope-open-text me-2"></i> Form Surat Balasan</h4>
                    <p class="mb-0 small opacity-75">Lengkapi data mahasiswa dan detail keperluan surat.</p>
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

                    <form action="{{ route('surat-balasan.store') }}" method="POST">
                        @csrf

                        <h6 class="text-muted text-uppercase fw-bold mb-3" style="font-size: 0.75rem;">Identitas Mahasiswa</h6>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Mahasiswa</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user-graduate"></i></span>
                                    <input type="text" name="nama_mahasiswa" class="form-control"
                                           value="{{ old('nama_mahasiswa') }}" required>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">NIM</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                    <input type="text" name="nim" class="form-control"
                                           value="{{ old('nim') }}" required>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">No WA</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fab fa-whatsapp"></i></span>
                                    <input type="text" name="wa_mahasiswa" class="form-control"
                                           value="{{ old('wa_mahasiswa') }}" required>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Program Studi</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-university"></i></span>
                                    <input type="text" name="prodi" class="form-control"
                                           value="{{ old('prodi') }}" required>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4 border-light">

                        <h6 class="text-muted text-uppercase fw-bold mb-3" style="font-size: 0.75rem;">Detail Keperluan</h6>

                        <div class="mb-3">
                            <label class="form-label">Keperluan</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-file-alt"></i></span>
                                <input type="text" name="keperluan" class="form-control"
                                       value="{{ old('keperluan') }}" required>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Universitas MOU</label>
                            <select name="mou_id" class="form-select" required>
                                <option value="">-- Pilih Universitas --</option>
                                @foreach ($mous as $m)
                                    <option value="{{ $m->id }}">{{ $m->nama_instansi ?? $m->nama_universitas }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Rentang Tanggal --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Mulai</label>
                                <input type="date" class="form-control" name="tanggal_mulai"
                                       value="{{ date('Y-m-d') }}" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Selesai</label>
                                <input type="date" class="form-control" name="tanggal_selesai" required>
                            </div>
                        </div>

                        {{-- Pilihan Data — PAKAI CHOICES JS --}}
                        <div class="mb-3">
                            <label class="form-label">Data Yang Dibutuhkan</label>
                            <select id="data_dibutuhkan" name="data_dibutuhkan" class="form-select" required>
                                <option value="">-- Pilih Data --</option>
                                <option value="OBAT">OBAT</option>
                                <option value="SOAP (RANAP)">SOAP (RANAP)</option>
                                <option value="RADIOLOGI">RADIOLOGI</option>
                                <option value="SOAP (RALAN)">SOAP (RALAN)</option>
                                <option value="LAB">LAB</option>
                            </select>
                        </div>

                        <div class="d-flex justify-content-between pt-3">
                            <a href="{{ route('surat-balasan.index') }}" class="btn btn-light-custom">
                                <i class="fas fa-arrow-left me-2"></i> Kembali
                            </a>

                            <button type="submit" class="btn btn-maroon">
                                Simpan Data <i class="fas fa-check-circle ms-2"></i>
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>

    {{-- ===================================== --}}
    {{-- INIT Choices.js Searchable Dropdown   --}}
    {{-- ===================================== --}}
    <script>
        new Choices('#data_dibutuhkan', {
            searchEnabled: true,
            itemSelectText: '',
            shouldSort: false
        });
    </script>

@endsection
