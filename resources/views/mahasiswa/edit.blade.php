@extends('layouts.app')

@section('title', 'Edit Mahasiswa')
@section('page-title', 'Edit Mahasiswa')

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('mahasiswa.update', $mahasiswa->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label>Nama Mahasiswa</label>
                    <input type="text" name="nm_mahasiswa" class="form-control"
                        value="{{ old('nm_mahasiswa', $mahasiswa->nm_mahasiswa) }}" required>
                </div>

                <div class="form-group">
                    <label>Universitas</label>
                    <input type="text" name="univ_asal" class="form-control"
                        value="{{ old('univ_asal', $mahasiswa->univ_asal) }}">
                </div>

                <div class="form-group">
                    <label>Prodi</label>
                    <input type="text" name="prodi" class="form-control" value="{{ old('prodi', $mahasiswa->prodi) }}">
                </div>

                <div class="form-group">
                    <label>Ruangan</label>
                    <select name="ruangan_id" class="form-control">
                        <option value="">-- pilih ruangan (opsional) --</option>
                        @foreach ($ruangans as $r)
                            <option value="{{ $r->id }}"
                                {{ old('ruangan_id', $mahasiswa->ruangan_id) == $r->id ? 'selected' : '' }}>
                                {{ $r->nm_ruangan }} (kuota: {{ $r->kuota_ruangan }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="aktif" {{ $mahasiswa->status === 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ $mahasiswa->status === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>

                <button class="btn btn-primary">Simpan</button>
            </form>
        </div>
    </div>
@endsection
