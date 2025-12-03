<?php

namespace App\Http\Controllers;

use App\Models\Mou;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    // Menampilkan halaman login & register (1 halaman gabung)
    public function showLoginForm()
    {
        $universitas = Mou::orderBy('nama_instansi')->get();
        return view('auth.login', compact('universitas'));
    }

    protected function authenticated(Request $request, $user)
    {
        // Jika user bukan admin DAN belum diapprove
        if ($user->role !== 'admin' && !$user->is_approved) {
            auth()->logout(); // Paksa logout
            return back()->with('error', 'Akun Anda sedang menunggu persetujuan Admin. Silakan coba lagi nanti.');
        }
        // Jika lolos, lanjut ke dashboard
        return redirect()->intended($this->redirectPath());
    }

    // Fungsi login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->withInput();
    }

    // Fungsi register
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'mou_id'   => 'required|exists:mous,id',
            'password' => 'required|string|min:6|confirmed',
            'program_studi' => 'required|string|max:255',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'user',
            'mou_id'   => $request->mou_id,
            'program_studi' => $request->program_studi,
        ]);

        Auth::login($user);

        return redirect('/dashboard')->with('success', 'Pendaftaran berhasil!');
    }

    // Fungsi logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
