@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">Edit Rencana Jadwal</div>
                
                <div class="card-body">
                    <form action="{{ route('room_sequences.update', $sequence->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Mahasiswa</label>
                            <select name="mahasiswa_id" class="form-control @error('mahasiswa_id') is-invalid @enderror">
                                @foreach($mahasiswas as $mhs)
                                    <option value="{{ $mhs->id }}" 
                                        {{ old('mahasiswa_id', $sequence->mahasiswa_id) == $mhs->id ? 'selected' : '' }}>
                                        {{-- Cek nama kolom di DB kamu, pakai fallback biar aman --}}
                                        {{ $mhs->name ?? $mhs->nm_mahasiswa }}
                                    </option>
                                @endforeach
                            </select>
                            @error('mahasiswa_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label">Tanggal Mulai (Check-in)</label>
                            <input type="date" name="start_date" 
                                   class="form-control @error('start_date') is-invalid @enderror" 
                                   value="{{ old('start_date', \Carbon\Carbon::parse($sequence->start_date)->format('Y-m-d')) }}">
                            
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label">Tanggal Selesai (Check-out)</label>
                            <input type="date" name="end_date" 
                                   class="form-control @error('end_date') is-invalid @enderror" 
                                   value="{{ old('end_date', \Carbon\Carbon::parse($sequence->end_date)->format('Y-m-d')) }}">
                            
                            @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label">Pilih Ruangan Tujuan</label>
                            <select name="ruangan_id" class="form-control @error('ruangan_id') is-invalid @enderror">
                                @foreach($ruangans as $room)
                                    <option value="{{ $room->id }}" 
                                        {{ old('ruangan_id', $sequence->ruangan_id) == $room->id ? 'selected' : '' }}>
                                        {{-- Cek nama kolom di DB kamu --}}
                                        {{ $room->nama_ruangan ?? $room->nm_ruangan }}
                                    </option>
                                @endforeach
                            </select>
                            @error('ruangan_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('room_sequences.index') }}" class="btn btn-secondary">Kembali</a>
                            <button type="submit" class="btn btn-primary">Update Jadwal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection