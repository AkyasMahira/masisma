@extends('layouts.app') 

@section('title', 'Tambah MOU')
@section('page-title', 'Tambah Data MOU') 

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body">

                    {{-- Tampilkan error validasi --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <strong>Whoops!</strong> Ada masalah dengan input Anda.<br><br>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- FORM MULAI --}}
                    {{-- PENTING: enctype="multipart/form-data" wajib untuk upload file --}}
                    <form action="{{ route('mou.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf {{-- Wajib untuk keamanan Laravel --}}

                        <div class="form-group">
                            <label for="nama_universitas">Nama Universitas</label>
                            <input type="text" class="form-control @error('nama_universitas') is-invalid @enderror"
                                id="nama_universitas" name="nama_universitas" value="{{ old('nama_universitas') }}"
                                required>
                            @error('nama_universitas')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tanggal_masuk">Tanggal Masuk</label>
                                    <input type="date" class="form-control @error('tanggal_masuk') is-invalid @enderror"
                                        id="tanggal_masuk" name="tanggal_masuk" value="{{ old('tanggal_masuk') }}" required>
                                    @error('tanggal_masuk')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tanggal_keluar">Tanggal Keluar</label>
                                    <input type="date" class="form-control @error('tanggal_keluar') is-invalid @enderror"
                                        id="tanggal_keluar" name="tanggal_keluar" value="{{ old('tanggal_keluar') }}"
                                        required>
                                    @error('tanggal_keluar')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="file_mou">Upload File MOU (PDF/DOC, max 5MB)</label>
                            <input type="file" class="form-control @error('file_mou') is-invalid @enderror"
                                id="file_mou" name="file_mou" required>
                            @error('file_mou')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="surat_keterangan">Upload Surat Keterangan (PDF/JPG/PNG, max 5MB)</label>
                            <input type="file" class="form-control @error('surat_keterangan') is-invalid @enderror"
                                id="surat_keterangan" name="surat_keterangan" required>
                            @error('surat_keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="keterangan">Keterangan (Opsional)</label>
                            <textarea class="form-control @error('keterangan') is-invalid @enderror" id="keterangan" name="keterangan"
                                rows="3">{{ old('keterangan') }}</textarea>
                            @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr>

                        <a href="{{ route('mou.index') }}" class="btn btn-secondary">Kembali</a>
                        <button type="submit" class="btn btn-primary">Simpan Data</button>

                    </form>
                    {{-- FORM SELESAI --}}

                </div>
            </div>
        </div>
    </div>
@endsection
