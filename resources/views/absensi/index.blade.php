@extends('layouts.app')

@section('title', 'Riwayat Absensi')
@section('page-title', 'Riwayat Absensi Hari Ini')

@section('content')
    <div class="card">
        <div class="card-body">
            <form method="GET" class="row g-3 mb-3">
                <div class="col-md-4">
                    <select name="ruangan_id" class="form-control">
                        <option value="">-- All Ruangan --</option>
                        @foreach ($ruangans as $r)
                            <option value="{{ $r->id }}" {{ request('ruangan_id') == $r->id ? 'selected' : '' }}>
                                {{ $r->nm_ruangan }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <select name="type" class="form-control">
                        <option value="">-- All Types --</option>
                        <option value="masuk" {{ request('type') == 'masuk' ? 'selected' : '' }}>Masuk</option>
                        <option value="keluar" {{ request('type') == 'keluar' ? 'selected' : '' }}>Keluar</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <button class="btn btn-primary">Filter</button>
                    <a href="{{ route('absensi.index') }}" class="btn btn-secondary">Reset</a>
                </div>
            </form>

            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Waktu</th>
                        <th>Nama</th>
                        <th>Ruangan</th>
                        <th>Tipe</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($absensis as $a)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $a->created_at->format('Y-m-d H:i:s') }}</td>
                            <td>{{ $a->mahasiswa->nm_mahasiswa }}</td>
                            <td>{{ $a->mahasiswa->ruangan ? $a->mahasiswa->ruangan->nm_ruangan : $a->mahasiswa->nm_ruangan }}
                            </td>
                            <td>{{ $a->type }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
