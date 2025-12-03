@extends('layouts.app')
@section('title', 'Jadwal Rolling Ruangan Mahasiswa')
@section('content')
<div class="container">
    <h1>Jadwal Rolling Ruangan Mahasiswa</h1>
    <a href="{{ route('room_schedules.create') }}" class="btn btn-primary mb-3">Tambah Jadwal</a>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Mahasiswa</th>
                <th>Ruangan</th>
                <th>Periode</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($schedules as $schedule)
            <tr>
                <td>{{ $schedule->mahasiswa->nm_mahasiswa ?? '-' }}</td>
                <td>{{ $schedule->ruangan->nm_ruangan ?? '-' }}</td>
                <td>{{ $schedule->start_date }} - {{ $schedule->end_date }}</td>
                <td>{{ ucfirst($schedule->status) }}</td>
                <td>
                    <a href="{{ route('room_schedules.edit', $schedule->id) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('room_schedules.destroy', $schedule->id) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Hapus jadwal ini?')">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
