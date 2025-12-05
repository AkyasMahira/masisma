@extends('layouts.public')
@section('content')
<div class="container text-center">
    <h1>Pendaftaran Berhasil!</h1>
    <p>Terima kasih telah mendaftar pada pelatihan <strong>{{ $form->judul }}</strong>.</p>
    <a href="/" class="btn btn-success">Kembali ke Beranda</a>
</div>
@endsection
