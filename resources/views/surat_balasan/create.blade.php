@extends('layouts.app')

@section('title', 'Tambah Surat Balasan')
@section('page-title', 'Tambah Surat Balasan')

@section('content')

    {{-- CSS & JS Choices --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

    <style>
        :root {
            --custom-maroon: #7c1316;
            --custom-maroon-light: #a3191d;
            --card-radius: 16px;
        }
        .form-card {
            border: none; border-radius: var(--card-radius);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08); background: #fff; overflow: hidden;
        }
        .card-header-custom {
            background-color: var(--custom-maroon); padding: 1.5rem; color: white;
            border-bottom: 4px solid var(--custom-maroon-light);
        }
        .form-label { font-weight: 600; color: #2c3e50; font-size: 0.9rem; margin-bottom: 0.5rem; }
        .btn-maroon { background-color: var(--custom-maroon); color: white; border: none; }
        .btn-maroon:hover { background-color: var(--custom-maroon-light); color: white; }
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
                                <input type="text" name="nama_mahasiswa" class="form-control" value="{{ old('nama_mahasiswa') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">NIM</label>
                                <input type="text" name="nim" class="form-control" value="{{ old('nim') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">No WA</label>
                                <input type="text" name="wa_mahasiswa" class="form-control" value="{{ old('wa_mahasiswa') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Program Studi</label>
                                <input type="text" name="prodi" class="form-control" value="{{ old('prodi') }}" required>
                            </div>
                        </div>

                        <hr class="my-4 border-light">
                        <h6 class="text-muted text-uppercase fw-bold mb-3" style="font-size: 0.75rem;">Detail Keperluan</h6>

                        <div class="mb-3">
                            <label class="form-label">Keperluan</label>
                            <input type="text" name="keperluan" class="form-control" value="{{ old('keperluan') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Universitas MOU</label>
                            <select name="mou_id" class="form-select" required>
                                <option value="">-- Pilih Universitas --</option>
                                @foreach ($mous as $m)
                                    <option value="{{ $m->id }}" {{ old('mou_id') == $m->id ? 'selected' : '' }}>
                                        {{ $m->nama_instansi ?? $m->nama_universitas }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Mulai</label>
                                <input type="date" class="form-control" name="tanggal_mulai" value="{{ old('tanggal_mulai', date('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Selesai</label>
                                <input type="date" class="form-control" name="tanggal_selesai" value="{{ old('tanggal_selesai') }}" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Data Yang Dibutuhkan</label>
                            <select id="data_dibutuhkan" name="data_dibutuhkan[]" class="form-select" multiple required>
                                <option value="OBAT">OBAT</option>
                                <option value="SOAP (RANAP)">SOAP (RANAP)</option>
                                <option value="RADIOLOGI">RADIOLOGI</option>
                                <option value="SOAP (RALAN)">SOAP (RALAN)</option>
                                <option value="LAB">LAB</option>
                            </select>
                        </div>

                        <div class="d-flex justify-content-between pt-3">
                            <a href="{{ route('surat-balasan.index') }}" class="btn btn-light-custom"><i class="fas fa-arrow-left me-2"></i> Kembali</a>
                            <button type="submit" class="btn btn-maroon">Simpan Data <i class="fas fa-check-circle ms-2"></i></button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <script>
        new Choices('#data_dibutuhkan', {
            removeItemButton: true,
            searchEnabled: true,
            shouldSort: false,
        });
    </script>
@endsection