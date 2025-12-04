@extends('layouts.app') 

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Pengaturan Jadwal (Rolling)</h1>
        <a href="{{ route('room_sequences.create') }}" class="btn btn-primary">Tambah Jadwal Baru</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Mahasiswa</th>
                        <th>Ruangan Tujuan</th>
                        <th>Tanggal Mulai</th>
                        <th>Tanggal Selesai</th>
                        <th>Durasi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sequences as $seq)
                    <tr>
                        <td>{{ $seq->mahasiswa->name ?? $seq->mahasiswa->nm_mahasiswa ?? '-' }}</td>
                        
                        <td>
                            <span class="badge bg-info text-dark">
                                {{ $seq->ruangan->nama_ruangan ?? $seq->ruangan->nm_ruangan ?? '-' }}
                            </span>
                        </td>

                        <td>{{ \Carbon\Carbon::parse($seq->start_date)->format('d M Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($seq->end_date)->format('d M Y') }}</td>

                        <td>
                            {{ \Carbon\Carbon::parse($seq->start_date)->diffInDays(\Carbon\Carbon::parse($seq->end_date)) }} Hari
                        </td>

                        <td>
                            <a href="{{ route('room_sequences.edit', $seq->id) }}" class="btn btn-sm btn-warning">
                                <i class="fa fa-edit"></i> Edit
                            </a>
                            <form action="{{ route('room_sequences.destroy', $seq->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="">
                                    <i class="fa fa-trash"></i> Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">Belum ada data jadwal rolling.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection