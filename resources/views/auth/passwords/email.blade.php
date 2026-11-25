@extends('auth.passwords._layout')

@section('title', 'Lupa Password')

@section('content')
    <div class="kartu-atas">
        <div class="lencana"><i class="bi bi-key"></i></div>
        <h1>Lupa Password?</h1>
        <p>Masukkan email akun Anda. Kami kirimkan link untuk membuat password baru.</p>
    </div>

    @if (session('status'))
        <div class="pesan ok"><i class="bi bi-check-circle-fill"></i><div>{{ session('status') }}</div></div>
    @endif
    @if ($errors->any())
        <div class="pesan gagal"><i class="bi bi-exclamation-triangle-fill"></i><div>{{ $errors->first() }}</div></div>
    @endif

    <form action="{{ route('password.email') }}" method="POST">
        @csrf
        <div class="field">
            <label for="email">Email</label>
            <div class="kolom">
                <i class="bi bi-envelope"></i>
                <input type="email" id="email" name="email" placeholder="nama@email.com" value="{{ old('email') }}" required autofocus>
            </div>
        </div>
        <button type="submit" class="tombol"><i class="bi bi-send me-1"></i> Kirim Link Reset</button>
    </form>

    <div class="catatan">
        <i class="bi bi-info-circle-fill"></i>
        <div>Link berlaku 60 menit, cek juga folder <b>Spam</b>. Akun instansi mitra (<b>@sindikat.com</b>): permintaan diteruskan ke admin Diklat dan password baru dikirim ke PIC instansi.</div>
    </div>

    <div class="kembali"><a href="{{ route('login') }}"><i class="bi bi-arrow-left"></i> Kembali ke halaman masuk</a></div>
@endsection
