@extends('layouts.app')

@section('title', 'Detail Mahasiswa')
@section('page-title', 'Detail Mahasiswa')

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h4>{{ $mahasiswa->nm_mahasiswa }}</h4>
                    <p><strong>Universitas:</strong> {{ $mahasiswa->univ_asal }}</p>
                    <p><strong>Prodi:</strong> {{ $mahasiswa->prodi }}</p>
                    <p><strong>Ruangan:</strong> {{ $mahasiswa->nm_ruangan }}</p>
                    <p><strong>Status:</strong> {{ $mahasiswa->status }}</p>

                    <p>
                        <strong>Link absensi publik:</strong>
                        <a href="{{ route('absensi.card', $mahasiswa->share_token) }}"
                            target="_blank">{{ route('absensi.card', $mahasiswa->share_token) }}</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
