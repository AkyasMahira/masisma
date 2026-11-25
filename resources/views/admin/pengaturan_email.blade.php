@extends('layouts.app')

@section('title', 'Pengaturan Email')

@section('content')
<div class="pe-wrap">
    <div class="pe-head">
        <div class="pe-ikon"><i class="bi bi-envelope-at"></i></div>
        <div>
            <h1>Pengaturan Email Pengirim</h1>
            <p>Akun Gmail yang dipakai Sindikat untuk mengirim email lupa password dan notifikasi.</p>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success d-flex gap-2"><i class="bi bi-check-circle-fill"></i><div>{{ session('success') }}</div></div>
    @endif

    <div class="pe-status">
        <div><span>Pengirim saat ini</span><b>{{ $username ?: '—' }}</b></div>
        <div><span>Nama pengirim</span><b>{{ $fromName }}</b></div>
        <div><span>Server</span><b>{{ $host }}</b></div>
        <div><span>Sumber</span><b>{{ $sumber === 'aplikasi' ? 'Diatur dari aplikasi' : 'File .env' }}</b>
            @if ($diubahAt)<small>{{ \Carbon\Carbon::parse($diubahAt)->translatedFormat('d M Y H:i') }}{{ $pengubah ? ' · ' . $pengubah : '' }}</small>@endif
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-7">
            <form method="POST" action="{{ route('admin.pengaturan_email.update') }}" class="pe-card" autocomplete="off">
                @csrf @method('PUT')
                <h2><i class="bi bi-key me-1"></i> Ganti Akun / App Password</h2>

                <div class="mb-3">
                    <label class="form-label" for="username">Email Gmail</label>
                    <input type="email" id="username" name="username" value="{{ old('username', $username) }}" class="form-control @error('username') is-invalid @enderror" required>
                    @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label" for="password">App Password Gmail</label>
                    <div class="input-group">
                        <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="abcd efgh ijkl mnop" autocomplete="new-password">
                        <button type="button" class="btn btn-outline-secondary" id="lihatPw" title="Tampilkan"><i class="bi bi-eye"></i></button>
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-text">{{ $adaPassword ? 'Kosongkan bila hanya mengganti nama pengirim.' : 'Belum ada password tersimpan.' }} Spasi boleh ikut ditempel.</div>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="from_name">Nama pengirim</label>
                    <input type="text" id="from_name" name="from_name" value="{{ old('from_name', $fromName) }}" class="form-control @error('from_name') is-invalid @enderror" required>
                    @error('from_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <button class="btn pe-btn w-100" type="submit"><i class="bi bi-shield-check me-1"></i> Cek Login &amp; Simpan</button>
                <div class="form-text text-center mt-2">Sistem mencoba login ke Gmail dulu. Bila ditolak, pengaturan lama tetap dipakai.</div>
            </form>
        </div>

        <div class="col-lg-5">
            <div class="pe-card pe-panduan">
                <h2><i class="bi bi-question-circle me-1"></i> Cara membuat App Password</h2>
                <ol>
                    <li>Login ke akun Gmail pengirim di browser.</li>
                    <li>Pastikan <b>Verifikasi 2 Langkah</b> aktif (Akun Google → Keamanan).</li>
                    <li>Buka <a href="https://myaccount.google.com/apppasswords" target="_blank" rel="noopener">myaccount.google.com/apppasswords</a>.</li>
                    <li>Isi nama aplikasi, mis. <i>Sindikat</i>, lalu klik <b>Buat</b>.</li>
                    <li>Salin 16 huruf yang muncul dan tempel di kolom App Password.</li>
                </ol>
                <p class="mb-0 small text-muted">Bila password Gmail diganti, semua App Password lama otomatis tidak berlaku dan harus dibuat ulang.</p>
            </div>

            <form method="POST" action="{{ route('admin.pengaturan_email.tes') }}" class="pe-card mt-3">
                @csrf
                <h2><i class="bi bi-send me-1"></i> Kirim Email Uji</h2>
                <div class="input-group">
                    <input type="email" name="tujuan" value="{{ old('tujuan', auth()->user()->email) }}" class="form-control @error('tujuan') is-invalid @enderror" required>
                    <button class="btn btn-outline-secondary" type="submit">Kirim</button>
                    @error('tujuan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .pe-wrap { max-width: 1000px; margin: 8px auto 24px; }
    .pe-head { display: flex; gap: 14px; align-items: center; margin-bottom: 18px; }
    .pe-head h1 { font-size: 1.3rem; font-weight: 700; margin: 0; }
    .pe-head p { margin: 2px 0 0; color: #777; font-size: .9rem; }
    .pe-ikon { width: 48px; height: 48px; flex: 0 0 48px; border-radius: 12px; background: #f8eaea; color: #7c1316; display: grid; place-items: center; font-size: 1.4rem; }
    .pe-status { display: grid; grid-template-columns: repeat(auto-fit, minmax(190px, 1fr)); gap: 10px; margin-bottom: 16px; }
    .pe-status > div { background: #fff; border: 1px solid #f1e4e4; border-radius: 12px; padding: 10px 14px; min-width: 0; }
    .pe-status span { display: block; font-size: .75rem; color: #888; }
    .pe-status b { display: block; font-size: .92rem; word-break: break-all; }
    .pe-status small { color: #999; font-size: .72rem; }
    .pe-card { background: #fff; border: 1px solid #f1e4e4; border-radius: 16px; padding: 20px; box-shadow: 0 6px 24px rgba(124, 19, 22, .06); }
    .pe-card h2 { font-size: 1rem; font-weight: 700; margin-bottom: 14px; color: #7c1316; }
    .pe-card .form-label { font-weight: 600; font-size: .88rem; }
    .pe-btn { background: #7c1316; color: #fff; font-weight: 600; padding: 10px; border-radius: 10px; }
    .pe-btn:hover { background: #a83236; color: #fff; }
    .pe-panduan ol { padding-left: 18px; font-size: .88rem; margin-bottom: 10px; }
    .pe-panduan li { margin-bottom: 4px; }
</style>
@endpush

@push('scripts')
<script>
    document.getElementById('lihatPw').addEventListener('click', function () {
        var i = document.getElementById('password'), t = i.type === 'password';
        i.type = t ? 'text' : 'password';
        this.innerHTML = t ? '<i class="bi bi-eye-slash"></i>' : '<i class="bi bi-eye"></i>';
    });
</script>
@endpush
