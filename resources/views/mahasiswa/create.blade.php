@extends('layouts.app')

@section('title', 'Tambah Mahasiswa')
@section('page-title', 'Tambah Mahasiswa')

@section('content')
    <style>
        :root {
            --maroon: #7c1316;
            --maroon-light: #9d2a2e;
            --text-dark: #222;
            --text-muted: #6c757d;
            --bg-light: #f8f9fa;
        }

        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: transform 0.2s ease;
        }

        .card:hover {
            transform: translateY(-2px);
        }

        .form-label {
            font-weight: 600;
            color: var(--text-dark);
        }

        .form-control,
        .form-select {
            border-radius: 10px;
            padding: 10px 12px;
            border: 1px solid #ddd;
            transition: border-color 0.2s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--maroon-light);
            box-shadow: 0 0 0 0.15rem rgba(124, 19, 22, 0.25);
        }

        .btn-maroon {
            background-color: var(--maroon);
            border: none;
            color: #fff;
            font-weight: 600;
            border-radius: 10px;
            padding: 10px 18px;
            transition: all 0.2s ease;
        }

        .btn-maroon:hover {
            background-color: var(--maroon-light);
            transform: translateY(-1px);
        }

        .card-body {
            padding: 2rem;
        }

        @media (max-width: 768px) {
            .card-body {
                padding: 1.5rem;
            }
        }
    </style>

    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="mb-4 fw-bold" style="color: var(--maroon);">Form Tambah Mahasiswa</h4>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('mahasiswa.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Nama Mahasiswa <span class="text-danger">*</span></label>
                            <input type="text" name="nm_mahasiswa" class="form-control" value="{{ old('nm_mahasiswa') }}"
                                placeholder="Masukkan nama lengkap" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Universitas</label>
                            <input type="text" name="univ_asal" class="form-control" value="{{ old('univ_asal') }}"
                                placeholder="Masukkan universitas asal">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Program Studi</label>
                            <input type="text" name="prodi" class="form-control" value="{{ old('prodi') }}"
                                placeholder="Masukkan program studi">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Ruangan</label>
                            <select name="ruangan_id" class="form-select js-choices">
                                <option value="">-- Pilih Ruangan (Opsional) --</option>
                                @foreach ($ruangans as $r)
                                    <option value="{{ $r->id }}" {{ old('ruangan_id') == $r->id ? 'selected' : '' }}>
                                        {{ $r->nm_ruangan }} (Kuota: {{ $r->kuota_ruangan }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select js-choices">
                                <option value="aktif" {{ old('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="nonaktif" {{ old('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif
                                </option>
                            </select>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('mahasiswa.index') }}" class="btn btn-secondary">Kembali</a>
                            <button type="submit" class="btn-maroon">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
