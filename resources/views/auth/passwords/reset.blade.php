@extends('auth.passwords._layout')

@section('title', 'Atur Ulang Password')

@section('content')
    <div class="kartu-atas">
        <div class="lencana"><i class="bi bi-shield-lock"></i></div>
        <h1>Buat Password Baru</h1>
        <p>Minimal 6 karakter. Gunakan kombinasi huruf dan angka agar lebih aman.</p>
    </div>

    @if ($errors->any())
        <div class="pesan gagal"><i class="bi bi-exclamation-triangle-fill"></i><div>{{ $errors->first() }}</div></div>
    @endif

    <form action="{{ route('password.update') }}" method="POST">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="field">
            <label>Akun</label>
            <div class="kolom terkunci">
                <i class="bi bi-person-circle"></i>
                <input type="email" name="email" value="{{ old('email', request()->email) }}" required readonly>
            </div>
        </div>

        <div class="field">
            <label for="password">Password baru</label>
            <div class="kolom">
                <i class="bi bi-lock"></i>
                <input type="password" name="password" id="password" placeholder="Minimal 6 karakter" minlength="6" required autofocus autocomplete="new-password">
                <button type="button" class="lihat" data-target="password" title="Tampilkan"><i class="bi bi-eye"></i></button>
            </div>
            <div class="kekuatan"><span id="barKekuatan"></span></div>
            <div class="bantuan" id="teksKekuatan">&nbsp;</div>
        </div>

        <div class="field">
            <label for="password_confirmation">Ulangi password baru</label>
            <div class="kolom">
                <i class="bi bi-lock-fill"></i>
                <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Ketik ulang password" minlength="6" required autocomplete="new-password">
                <button type="button" class="lihat" data-target="password_confirmation" title="Tampilkan"><i class="bi bi-eye"></i></button>
            </div>
            <div class="bantuan" id="teksCocok">&nbsp;</div>
        </div>

        <button type="submit" class="tombol"><i class="bi bi-check2-circle me-1"></i> Simpan Password Baru</button>
    </form>

    <div class="kembali"><a href="{{ route('login') }}"><i class="bi bi-arrow-left"></i> Kembali ke halaman masuk</a></div>
@endsection

@section('scripts')
<script>
    (function () {
        var p = document.getElementById('password'), k = document.getElementById('password_confirmation');
        var bar = document.getElementById('barKekuatan'), tk = document.getElementById('teksKekuatan'), tc = document.getElementById('teksCocok');
        function nilai(v) {
            var s = 0;
            if (v.length >= 6) s++; if (v.length >= 10) s++;
            if (/[a-z]/.test(v) && /[A-Z]/.test(v)) s++; if (/\d/.test(v)) s++; if (/[^A-Za-z0-9]/.test(v)) s++;
            return s;
        }
        function cek() {
            var v = p.value, s = nilai(v);
            var lv = !v ? null : (v.length < 6 ? ['Terlalu pendek', '#dc3545', 15] : s <= 2 ? ['Lemah', '#fd7e14', 40] : s <= 3 ? ['Cukup', '#ffc107', 70] : ['Kuat', '#198754', 100]);
            bar.style.width = lv ? lv[2] + '%' : '0'; bar.style.background = lv ? lv[1] : '';
            tk.textContent = lv ? 'Kekuatan: ' + lv[0] : ' ';
            if (!k.value) { tc.textContent = ' '; return; }
            var sama = k.value === v;
            tc.textContent = sama ? '✓ Password sama' : '✗ Belum sama';
            tc.style.color = sama ? '#198754' : '#dc3545';
        }
        p.addEventListener('input', cek); k.addEventListener('input', cek);
    })();
</script>
@endsection
