<?php

namespace App\Http\Controllers;

use App\Support\PengaturanEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PengaturanEmailController extends Controller
{
    public function index()
    {
        $d = PengaturanEmail::baca();
        $pengubah = !empty($d['diubah_oleh']) ? optional(\App\Models\User::find($d['diubah_oleh']))->name : null;

        return view('admin.pengaturan_email', [
            'username' => config('mail.mailers.smtp.username'),
            'fromName' => config('mail.from.name'),
            'adaPassword' => (bool) config('mail.mailers.smtp.password'),
            'sumber' => $d ? 'aplikasi' : '.env',
            'diubahAt' => $d['diubah_at'] ?? null,
            'pengubah' => $pengubah,
            'host' => config('mail.mailers.smtp.host') . ':' . config('mail.mailers.smtp.port'),
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'username' => 'required|email|max:150',
            'password' => 'nullable|string|max:100',
            'from_name' => 'required|string|max:120',
        ], [
            'username.required' => 'Email Gmail wajib diisi.',
            'username.email' => 'Format email tidak valid.',
            'from_name.required' => 'Nama pengirim wajib diisi.',
        ]);

        // App Password Google ditampilkan dengan spasi (abcd efgh ...) -> buang spasinya
        $pw = preg_replace('/\s+/', '', (string) $request->password);
        $gantiAkun = $request->username !== config('mail.mailers.smtp.username');
        if ($pw === '') {
            if ($gantiAkun) return back()->withInput()->withErrors(['password' => 'Email diganti, App Password wajib diisi.']);
            $pw = (string) config('mail.mailers.smtp.password');
        }

        if ($err = PengaturanEmail::uji($request->username, $pw)) {
            return back()->withInput()->withErrors(['password' => $err]);
        }

        PengaturanEmail::simpan($request->username, $pw, $request->from_name, auth()->id());
        Log::info('Pengaturan email SMTP diganti oleh user #' . auth()->id() . ' -> ' . $request->username);

        return back()->with('success', 'Login Gmail berhasil dan pengaturan disimpan. Email (lupa password, notifikasi) sekarang dikirim dari ' . $request->username . '.');
    }

    public function tes(Request $request)
    {
        $request->validate(['tujuan' => 'required|email']);
        try {
            Mail::raw("Ini email uji dari Sindikat RSUD SLG.\nJika email ini sampai, pengiriman email (termasuk lupa password) sudah berfungsi.\n\nDikirim: " . now()->format('d/m/Y H:i'), function ($m) use ($request) {
                $m->to($request->tujuan)->subject('Tes Email Sindikat');
            });
        } catch (\Throwable $e) {
            return back()->withErrors(['tujuan' => 'Gagal mengirim: ' . mb_substr($e->getMessage(), 0, 200)]);
        }
        return back()->with('success', 'Email uji terkirim ke ' . $request->tujuan . '. Cek kotak masuk / Spam.');
    }
}
