@extends('layouts.app')
@section('title', 'Edit Jadwal Rolling Ruangan')
@section('content')
<div class="container">
    <h1>Edit Jadwal Rolling Ruangan</h1>
    <form action="{{ route('room_schedules.update', $schedule->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="mahasiswa_id">Mahasiswa</label>
            <select name="mahasiswa_id" id="mahasiswa_id" class="form-control" required>
                <option value="">-- Pilih Mahasiswa --</option>
                @foreach($mahasiswas as $m)
                    <option value="{{ $m->id }}" {{ $schedule->mahasiswa_id == $m->id ? 'selected' : '' }}>{{ $m->nm_mahasiswa }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="ruangan_id">Ruangan</label>
            <select name="ruangan_id" id="ruangan_id" class="form-control" required>
                <option value="">-- Pilih Ruangan --</option>
                @foreach($ruangans as $r)
                    <option value="{{ $r->id }}" {{ $schedule->ruangan_id == $r->id ? 'selected' : '' }}>{{ $r->nm_ruangan }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="start_date">Tanggal Mulai</label>
            <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $schedule->start_date }}" required>
        </div>
        <div class="form-group">
            <label for="end_date">Tanggal Selesai</label>
            <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $schedule->end_date }}" required>
        </div>
        <button type="submit" class="btn btn-success">Update Jadwal</button>
        <a href="{{ route('room_schedules.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection
