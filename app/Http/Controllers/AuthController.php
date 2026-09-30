<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Mou;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log; // Untuk mencatat error di log file
use Illuminate\Validation\ValidationException; // PENTING: Untuk menangani error validasi
use Exception; // PENTING: Untuk menangani error sistem umum
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;
class AuthController extends Controller
{
    /**
     * Menampilkan halaman login & register.
     */
     
     /**
 * Menampilkan form minta link reset password
 */
public function showLinkRequestForm()
{
    return view('auth.passwords.email');
}

/**
 * Kirim link reset ke email
 */
public function sendResetLinkEmail(Request $request)
{
    $request->validate(['email' => 'required|email']);

    $status = Password::sendResetLink($request->only('email'));

    return $status === Password::RESET_LINK_SENT
        ? back()->with('status', 'Link reset password telah dikirim ke email Anda!')
        : back()->withErrors(['email' => 'Email tidak ditemukan atau terjadi kesalahan.']);
}

/**
 * Menampilkan form input password baru
 */
public function showResetForm($token)
{
    return view('auth.passwords.reset', ['token' => $token]);
}

/**
 * Update password ke database
 */
public function resetPassword(Request $request)
{
    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|min:6|confirmed',
    ]);

    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function ($user, $password) {
            $user->forceFill([
                'password' => Hash::make($password)
            ])->setRememberToken(Str::random(60));
            $user->save();

            event(new PasswordReset($user));
        }
    );

    return $status === Password::PASSWORD_RESET
        ? redirect()->route('login')->with('status', 'Password berhasil diubah. Silakan login.')
        : back()->withErrors(['email' => 'Terjadi kesalahan saat mereset password.']);
}
    public function showLoginForm()
    {
        try {
            // Ambil data MoU yang masih aktif
            $universitas = Mou::whereDate('tanggal_keluar', '>=', now())
                ->orderBy('nama_universitas')
                ->get();

            return view('auth.login', compact('universitas'));
        } catch (Exception $e) {
            // Jika gagal load halaman (misal DB mati), tampilkan error 500
            return response()->view('errors.500', ['exception' => $e], 500);
        }
    }

    /**
     * Proses Login
     */
    public function login(Request $request)
    {
        try {
            // 1. Validasi Input
            $credentials = $request->validate([
                'email'    => 'required|email',
                'password' => 'required',
            ], [
                'email.required'    => 'Email wajib diisi.',
                'email.email'       => 'Format email tidak valid.',
                'password.required' => 'Password wajib diisi.',
            ]);

            // 2. Coba Login
            if (Auth::attempt($credentials)) {
                $request->session()->regenerate();
                
                $user = Auth::user();

                // 3. Cek Status Approval
                // Aturan: User biasa (bukan admin/ruangan) harus diapprove dulu
                // if (!in_array($user->role, ['admin', 'ruangan'])) {
                //     if (!$user->is_approved) {
                //         Auth::logout(); // Tendang keluar
                //         return back()->withErrors([
                //             'email' => 'Akun Anda sedang menunggu persetujuan Admin. Silakan hubungi Diklat.'
                //         ]);
                //     }
                // }

                // 4. Redirect Berdasarkan Role
                if ($user->role === 'admin') {
                    return redirect()->intended('/dashboard');
                } elseif ($user->role === 'ruangan') {
                    return redirect()->route('kepala_ruangan.dashboard');
                } elseif ($user->role === 'instansi') {
                    return redirect()->route('instansi.dashboard');
                } else {
                    // Pastikan route 'dashboard' ini ada di web.php
                    return redirect()->route('dashboard'); 
                }
            }

            // 5. Jika Gagal Login (Password Salah / Email tidak ditemukan)
            return back()->withErrors([
                'email' => 'Email atau password salah.',
            ])->withInput();

        } catch (ValidationException $e) {
            // Lempar error validasi agar ditangani Laravel (kembali ke form dengan pesan error input)
            throw $e;
        } catch (Exception $e) {
            // Tangkap Error Sistem (DB mati, Typo, dll)
            Log::error("Error saat Login: " . $e->getMessage());
            return back()->withErrors([
                'email' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ])->withInput();
        }
    }

    /**
     * Proses Register
     */
    public function register(Request $request)
    {
        try {
            // 1. Validasi Input (Custom Bahasa Indonesia)
            $request->validate([
                'name'          => 'required|string|max:255',
                'email'         => 'required|string|email|max:255|unique:users',
                'password'      => 'required|string|min:6|confirmed',
                'program_studi' => 'required|string|max:255',
                'mou_id'        => 'required|exists:mous,id',
            ], [
                'name.required'      => 'Nama lengkap wajib diisi.',
                'email.required'     => 'Email wajib diisi.',
                'email.email'        => 'Format email tidak valid.',
                'email.unique'       => 'Email ini sudah terdaftar. Silakan Login saja.',
                'password.required'  => 'Password wajib diisi.',
                'password.min'       => 'Password minimal 6 karakter.',
                'password.confirmed' => 'Konfirmasi password tidak cocok.',
                'program_studi.required' => 'Program studi wajib dipilih.',
                'mou_id.required'    => 'Instansi/Universitas wajib dipilih.',
            ]);

            // 2. Validasi Tambahan: Cek Keaktifan MoU Manual
            $mou = Mou::where('id', $request->mou_id)
                ->whereDate('tanggal_keluar', '>=', now())
                ->first();

            if (!$mou) {
                // Lempar error validasi manual jika MoU expired
                throw ValidationException::withMessages([
                    'mou_id' => 'MoU instansi tersebut sudah kedaluwarsa atau tidak aktif.'
                ]);
            }

$user = User::create([
    'name'          => strtoupper($request->name),
    'email'         => $request->email,
    'password'      => Hash::make($request->password),
    'role'          => 'user',
    'mou_id'        => $request->mou_id,
    'program_studi' => $request->program_studi,
    'is_approved'   => 1, // <--- UBAH DARI 0 KE 1
]);;

            // 4. Auto Login & Redirect
            Auth::login($user);
            
            // Redirect ke dashboard dengan pesan sukses
            return redirect()->route('dashboard')->with('success', 'Registrasi berhasil! Selamat datang.');

        } catch (ValidationException $e) {
            // --- KUNCI: Lempar balik error validasi ke Laravel ---
            // Ini membuat error "Email sudah terdaftar" muncul di bawah kolom input, 
            // BUKAN sebagai error "The given data was invalid" di try-catch umum.
            throw $e;
            
        } catch (Exception $e) {
            // --- FALLBACK ERROR SISTEM ---
            // Ini menangkap error kodingan, database mati, route tidak ditemukan, dll.
            Log::error("Error saat Register: " . $e->getMessage());

            return back()->withInput()->withErrors([
                'email' => 'Gagal Mendaftar (Sistem Error): ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Proses Logout
     */
    public function logout(Request $request)
    {
        try {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login');
        } catch (Exception $e) {
            return back()->withErrors(['email' => 'Gagal Logout: ' . $e->getMessage()]);
        }
    }
}