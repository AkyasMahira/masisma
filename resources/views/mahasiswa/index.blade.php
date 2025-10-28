@extends('layouts.app')

@section('title', 'Mahasiswa')
@section('page-title', 'Mahasiswa')

@section('content')
    <div class="mb-3">
        <a href="{{ route('mahasiswa.create') }}" class="btn btn-primary">Tambah Mahasiswa</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama</th>
                        <th>Universitas</th>
                        <th>Prodi</th>
                        <th>Ruangan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($mahasiswas as $m)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $m->nm_mahasiswa }}</td>
                            <td>{{ $m->univ_asal }}</td>
                            <td>{{ $m->prodi }}</td>
                            <td>{{ $m->ruangan ? $m->ruangan->nm_ruangan : $m->nm_ruangan }}</td>
                            <td>{{ $m->status }}</td>
                            <td>
                                <a href="{{ route('mahasiswa.show', $m->id) }}" class="btn btn-sm btn-info">Lihat</a>
                                <a href="{{ route('mahasiswa.edit', $m->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                <form action="{{ route('mahasiswa.destroy', $m->id) }}" method="POST"
                                    style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger"
                                        onclick="return confirm('Hapus mahasiswa?')">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('scripts')

@endsection
