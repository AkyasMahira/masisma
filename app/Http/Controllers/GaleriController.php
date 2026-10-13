<?php

namespace App\Http\Controllers;

use App\Models\Galeri;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class GaleriController extends Controller
{
    /** Halaman publik galeri. */
    public function publik()
    {
        $galeri = Galeri::orderBy('urutan')->orderByDesc('id')->get();
        return view('public.galeri', compact('galeri'));
    }

    /** Kelola galeri (admin). */
    public function index()
    {
        $galeri = Galeri::orderBy('urutan')->orderByDesc('id')->get();
        return view('admin.galeri.index', compact('galeri'));
    }

    /** Upload foto (bisa banyak sekaligus). */
    public function store(Request $request)
    {
        $request->validate([
            'foto'   => 'required',
            'foto.*' => 'image|mimes:jpeg,png,jpg,webp|max:5120', // 5MB
            'judul'  => 'nullable|string|max:255',
        ]);

        $files = $request->file('foto');
        if (!is_array($files)) $files = [$files];

        $dir = public_path('uploads/galeri');
        if (!File::isDirectory($dir)) File::makeDirectory($dir, 0755, true);

        $urutan = (int) (Galeri::max('urutan') ?? 0);
        foreach ($files as $file) {
            $namaFile = time() . '_' . mt_rand(1000, 9999) . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
            $file->move($dir, $namaFile);
            Galeri::create([
                'path'   => 'uploads/galeri/' . $namaFile,
                'judul'  => $request->judul,
                'urutan' => ++$urutan,
            ]);
        }

        return back()->with('success', 'Foto berhasil diunggah ke galeri.');
    }

    /** Ubah judul/caption. */
    public function update(Request $request, $id)
    {
        $request->validate(['judul' => 'nullable|string|max:255']);
        $g = Galeri::findOrFail($id);
        $g->update(['judul' => $request->judul]);
        return back()->with('success', 'Judul foto diperbarui.');
    }

    /** Hapus foto. */
    public function destroy($id)
    {
        $g = Galeri::findOrFail($id);
        if ($g->path && File::exists(public_path($g->path))) {
            File::delete(public_path($g->path));
        }
        $g->delete();
        return back()->with('success', 'Foto dihapus dari galeri.');
    }
}
