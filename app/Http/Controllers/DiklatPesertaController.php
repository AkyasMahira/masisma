<?php
namespace App\Http\Controllers;

use App\Models\DiklatForm;
use App\Models\DiklatPeserta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DiklatPesertaController extends Controller
{
    public function publicForm($public_link)
    {
        $form = DiklatForm::where('public_link', $public_link)->firstOrFail();
        return view('diklat.public_form', compact('form'));
    }

    public function register(Request $request, $public_link)
    {
        $form = DiklatForm::where('public_link', $public_link)->firstOrFail();

        // 1. Validasi Input (Digabungkan dengan validasi pas_foto)
        $data = $request->validate([
            'nama_lengkap'      => 'required|string',
            'gelar'             => 'nullable|string',
            'tempat_lahir'      => 'required|string',
            'tanggal_lahir'     => 'required|date',
            'nik'               => 'required|string',
            'email'             => 'required|email',
            'nip'               => 'nullable|string',
            'pangkat_golongan'  => 'nullable|string',
            'jabatan'           => 'required|string',
            'instansi'          => 'required|string',
            'alamat'            => 'required|string',
            'no_hp'             => 'required|string',
            'pilihan_pelatihan' => 'required|array',
            'pilihan_tempat'    => 'required|array',
            'ukuran_kaos'       => 'required|string',
            'jawaban_custom'    => 'nullable|array',
            // Validasi file
            'bukti_pembayaran'  => 'required|file|mimes:jpg,jpeg,png,pdf',
            'pas_foto'          => 'required|image|mimes:jpg,jpeg,png|max:2048', // Tambahan Pas Foto
        ], [
            // Pesan Error Kustom untuk Pas Foto
            'pas_foto.required' => 'Pas foto wajib diunggah.',
            'pas_foto.image'    => 'File harus berupa gambar.',
            'pas_foto.max'      => 'Ukuran foto maksimal 2MB.',
        ]);

        // 2. Persiapan Data Tambahan
        $data['diklat_form_id'] = $form->id;

        // 3. Logika Upload Pas Foto (Sesuai request: Rename & Path khusus)
        if ($request->hasFile('pas_foto')) {
            // Buat nama file unik: time_nama_pasfoto.ekstensi
            // Menggunakan str_replace untuk menghapus spasi pada nama
            $filename = time() . '_' . str_replace(' ', '_', $request->nama_lengkap) . '_foto.' . $request->pas_foto->extension();

            // Simpan ke storage/app/public/uploads/peserta/foto
            $path = $request->file('pas_foto')->storeAs('uploads/peserta/foto', $filename, 'public');

            // Masukkan path ke array data
            $data['pas_foto'] = $path;
        }

        // 4. Logika Upload Bukti Pembayaran (Logika lama tetap jalan)
        if ($request->hasFile('bukti_pembayaran')) {
            $data['bukti_pembayaran'] = $request->file('bukti_pembayaran')->store('bukti_pembayaran', 'public');
        }

        // 5. Simpan ke Database
        DiklatPeserta::create($data);

        return redirect()->route('diklat.public.success', $public_link);
    }

    public function publicSuccess($public_link)
    {
        $form = DiklatForm::where('public_link', $public_link)->firstOrFail();
        return view('diklat.public_success', compact('form'));
    }

    public function rekap($id)
    {
        $form = DiklatForm::findOrFail($id);
        $pesertas = DiklatPeserta::where('diklat_form_id', $form->id)->get();
        return view('diklat.rekap', compact('form', 'pesertas'));
    }
}
