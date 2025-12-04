@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">Tambah Jadwal Rolling Baru</div>

                <div class="card-body">
                    {{-- Alert Global (Opsional, karena sudah ada error per field) --}}
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('room_sequences.store') }}" method="POST">
                        @csrf
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Pilih Mahasiswa</label>
                            <select name="mahasiswa_id" class="form-control @error('mahasiswa_id') is-invalid @enderror">
                                <option value="">-- Pilih Mahasiswa --</option>
                                @foreach($mahasiswas as $mhs)
                                    <option value="{{ $mhs->id }}" {{ old('mahasiswa_id') == $mhs->id ? 'selected' : '' }}>
                                        {{-- Gunakan fallback agar aman --}}
                                        {{ $mhs->nm_mahasiswa ?? $mhs->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('mahasiswa_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label">Tanggal Mulai (Masuk)</label>
                            <input type="date" name="start_date" 
                                   class="form-control @error('start_date') is-invalid @enderror" 
                                   value="{{ old('start_date') }}" required>
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label">Tanggal Selesai (Keluar)</label>
                            <input type="date" name="end_date" 
                                   class="form-control @error('end_date') is-invalid @enderror" 
                                   value="{{ old('end_date') }}" required>
                            @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label">Pilih Ruangan Tujuan</label>
                            <select name="ruangan_id" class="form-control @error('ruangan_id') is-invalid @enderror">
                                <option value="">-- Pilih Ruangan --</option>
                                @foreach($ruangans as $room)
                                    <option value="{{ $room->id }}" {{ old('ruangan_id') == $room->id ? 'selected' : '' }}>
                                        {{ $room->nm_ruangan ?? $room->nama_ruangan }}
                                    </option>
                                @endforeach
                            </select>
                            @error('ruangan_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('room_sequences.index') }}" class="btn btn-secondary">Kembali</a>
                            <button type="submit" class="btn btn-success">Simpan Jadwal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection