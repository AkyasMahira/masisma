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
                    <p><strong>Ruangan:</strong>
                        {{ $mahasiswa->ruangan ? $mahasiswa->ruangan->nm_ruangan : $mahasiswa->nm_ruangan }}</p>
                    <p><strong>Status:</strong> {{ ucfirst($mahasiswa->status) }}</p>

                    <hr>
                    <p><small><strong>Share token:</strong>
                            {{ \Illuminate\Support\Str::limit($mahasiswa->share_token, 16, '...') }}</small></p>
                    <p><small><strong>Last attendance today:</strong>
                            {{ $lastStatus ? ucfirst($lastStatus) : 'No record' }}</small></p>

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
