<?php

namespace App\Http\Controllers;

use App\Models\DiklatForm;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage; // <--- WAJIB ADA INI

class DiklatFormController extends Controller
{
    public function index()
    {
        $forms = DiklatForm::latest()->get();
        return view('diklat.index', compact('forms'));
    }

    public function create()
    {
        return view('diklat.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'judul' => 'required|string',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // Validasi Gambar
            'tanggal_pelaksanaan' => 'required|date',
            'keterangan' => 'nullable|string',
            'peraturan' => 'nullable|string',
            'opsi_pelatihan' => 'required|array',
            'opsi_pelatihan.*' => 'required|string',
            'opsi_tempat' => 'required|array',
            'opsi_tempat.*' => 'required|string',
            'pertanyaan_custom' => 'nullable|array',
        ]);

        // LOGIC UPLOAD BANNER (CREATE)
        if ($request->hasFile('banner')) {
            $path = $request->file('banner')->store('banners', 'public');
            $data['banner_path'] = $path;
        }

        // Logic input dinamis
        if (!empty($data['pertanyaan_custom'])) {
            foreach ($data['pertanyaan_custom'] as $i => $q) {
                if(isset($q['pilihan']) && is_string($q['pilihan'])) {
                    $data['pertanyaan_custom'][$i]['pilihan'] = array_map('trim', explode(',', $q['pilihan']));
                }
            }
        }

        $data['public_link'] = Str::random(16);

        DiklatForm::create($data);

        return redirect()->route('diklat.index')->with('success', 'Form berhasil dibuat!');
    }

    public function show($id)
    {
        $form = DiklatForm::findOrFail($id);
        return view('diklat.show', compact('form'));
    }

    public function edit($id)
    {
        $form = DiklatForm::findOrFail($id);
        return view('diklat.edit', compact('form'));
    }

    public function update(Request $request, $id)
    {
        $form = DiklatForm::findOrFail($id);

        $data = $request->validate([
            'judul' => 'required|string',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // Validasi Gambar di Update
            'tanggal_pelaksanaan' => 'required|date',
            'keterangan' => 'nullable|string',
            'peraturan' => 'nullable|string',
            'opsi_pelatihan' => 'required|array',
            'opsi_pelatihan.*' => 'required|string',
            'opsi_tempat' => 'required|array',
            'opsi_tempat.*' => 'required|string',
            'pertanyaan_custom' => 'nullable|array',
        ]);

        // --- LOGIC UPLOAD BANNER (UPDATE) - INI YANG KEMARIN KURANG ---
        if ($request->hasFile('banner')) {
            // 1. Hapus gambar lama jika ada
            if ($form->banner_path && Storage::disk('public')->exists($form->banner_path)) {
                Storage::disk('public')->delete($form->banner_path);
            }

            // 2. Simpan gambar baru
            $path = $request->file('banner')->store('banners', 'public');
            $data['banner_path'] = $path;
        }
        // -------------------------------------------------------------

        // Logic pertanyaan custom
        if (!empty($data['pertanyaan_custom'])) {
            foreach ($data['pertanyaan_custom'] as $i => $q) {
                if(isset($q['pilihan']) && is_string($q['pilihan'])) {
                    $data['pertanyaan_custom'][$i]['pilihan'] = array_map('trim', explode(',', $q['pilihan']));
                }
            }
        }

        $form->update($data);

        return redirect()->route('diklat.index')->with('success', 'Form berhasil diupdate!');
    }

    public function destroy($id)
    {
        $form = DiklatForm::findOrFail($id);

        // Hapus banner jika ada saat form dihapus
        if ($form->banner_path && Storage::disk('public')->exists($form->banner_path)) {
            Storage::disk('public')->delete($form->banner_path);
        }

        $form->delete();
        return redirect()->route('diklat.index')->with('success', 'Form berhasil dihapus!');
    }
}
