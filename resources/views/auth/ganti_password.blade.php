@extends('layouts.app')

@section('title', 'Ganti Password')

@section('content')
<div class="gp-wrap">
    <div class="gp-card">
        <div class="gp-head">
            <div class="gp-ikon"><i class="bi bi-shield-lock"></i></div>
            <div>
                <h1>Ganti Password</h1>
                <p>{{ auth()->user()->name }} &middot; {{ auth()->user()->email }}</p>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success d-flex gap-2 align-items-start"><i class="bi bi-check-circle-fill"></i><div>{{ session('success') }}</div></div>
        @endif

        <form method="POST" action="{{ route('akun.password.update') }}" autocomplete="off">
            @csrf
            @method('PUT')

            @foreach ([
                ['password_lama', 'Password lama', 'current-password'],
                ['password', 'Password baru', 'new-password'],
                ['password_confirmation', 'Ulangi password baru', 'new-password'],
            ] as [$nama, $label, $ac])
                <div class="mb-3">
                    <label for="{{ $nama }}" class="form-label">{{ $label }}</label>
                    <div class="input-group">
                        <input type="password" id="{{ $nama }}" name="{{ $nama }}" autocomplete="{{ $ac }}" required
                               {{ $nama !== 'password_lama' ? 'minlength=6' : '' }}
                               class="form-control @error($nama) is-invalid @enderror">
                        <button type="button" class="btn btn-outline-secondary gp-lihat" data-target="{{ $nama }}" title="Tampilkan password"><i class="bi bi-eye"></i></button>
                        @error($nama)<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    @if ($nama === 'password')<div class="form-text">Minimal 6 karakter, berbeda dari password lama.</div>@endif
                </div>
            @endforeach

            <button type="submit" class="btn gp-simpan w-100"><i class="bi bi-check2-circle me-1"></i> Simpan Password Baru</button>
        </form>

        <p class="gp-lupa">Lupa password lama? Logout lalu pilih <b>Lupa Password</b> di halaman login, atau hubungi admin Diklat.</p>
    </div>
</div>
@endsection

@push('styles')
<style>
    .gp-wrap { max-width: 460px; margin: 24px auto; padding: 0 4px; }
    .gp-card { background: #fff; border-radius: 16px; padding: 24px; box-shadow: 0 6px 24px rgba(124, 19, 22, .08); border: 1px solid #f1e4e4; }
    .gp-head { display: flex; gap: 14px; align-items: center; margin-bottom: 20px; }
    .gp-head h1 { font-size: 1.2rem; font-weight: 700; margin: 0; color: #2b2b2b; }
    .gp-head p { margin: 2px 0 0; font-size: .85rem; color: #777; word-break: break-all; }
    .gp-ikon { width: 46px; height: 46px; flex: 0 0 46px; border-radius: 12px; background: #fcf0f1; color: #7c1316; display: grid; place-items: center; font-size: 1.35rem; }
    .gp-card .form-label { font-weight: 600; font-size: .88rem; }
    .gp-simpan { background: #7c1316; color: #fff; font-weight: 600; padding: 10px; border-radius: 10px; }
    .gp-simpan:hover { background: #a3191d; color: #fff; }
    .gp-lupa { font-size: .8rem; color: #888; margin: 16px 0 0; text-align: center; }
</style>
@endpush

@push('scripts')
<script>
    document.querySelectorAll('.gp-lihat').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var inp = document.getElementById(btn.dataset.target);
            var lihat = inp.type === 'password';
            inp.type = lihat ? 'text' : 'password';
            btn.innerHTML = lihat ? '<i class="bi bi-eye-slash"></i>' : '<i class="bi bi-eye"></i>';
        });
    });
</script>
@endpush
