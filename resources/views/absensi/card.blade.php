@extends('layouts.app')

@section('title', 'Absensi')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="card">
                <div class="card-body text-center">
                    <h4>{{ $mahasiswa->nm_mahasiswa }}</h4>
                    <p>{{ $mahasiswa->univ_asal }} — {{ $mahasiswa->prodi }}</p>
                    <p><strong>Ruangan:</strong> {{ $mahasiswa->nm_ruangan }}</p>
                    <p><strong>Status:</strong> {{ $mahasiswa->status }}</p>

                    <form action="{{ route('absensi.masuk', $mahasiswa->share_token) }}" method="POST"
                        style="display:inline-block; margin-right:10px;">
                        @csrf
                        <button class="btn btn-success">Absensi Masuk</button>
                    </form>

                    <form action="{{ route('absensi.keluar', $mahasiswa->share_token) }}" method="POST"
                        style="display:inline-block;">
                        @csrf
                        <button class="btn btn-danger">Absensi Keluar</button>
                    </form>

                    <hr>
                    <h6>Riwayat singkat</h6>
                    <ul class="list-unstyled text-left">
                        @foreach ($mahasiswa->absensis()->latest()->limit(10)->get() as $a)
                            <li>{{ $a->created_at->format('Y-m-d H:i:s') }} — {{ $a->type }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
