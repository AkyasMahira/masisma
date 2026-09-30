<?php

namespace App\Http\Controllers;

use App\Models\DiklatForm;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\DiklatPeserta;
use Illuminate\Support\Facades\Storage;

class DiklatFormController extends Controller
{
    public function index(Request $request)
    {
        $query = DiklatForm::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        $forms = $query->latest()->paginate(10);
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
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'tanggal_pelaksanaan' => 'required|date',
            'keterangan' => 'nullable|string',
            'peraturan' => 'nullable|string',
            'opsi_pelatihan' => 'required|array',
            'opsi_pelatihan.*' => 'required|string',
            'opsi_tempat' => 'required|array',
            'opsi_tempat.*' => 'required|string',
            'pertanyaan_custom' => 'nullable|array',
        ]);

        if ($request->hasFile('banner')) {
            $path = $request->file('banner')->store('banners', 'public');
            $data['banner_path'] = $path;
        }

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
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'tanggal_pelaksanaan' => 'required|date',
            'keterangan' => 'nullable|string',
            'peraturan' => 'nullable|string',
            'opsi_pelatihan' => 'required|array',
            'opsi_pelatihan.*' => 'required|string',
            'opsi_tempat' => 'required|array',
            'opsi_tempat.*' => 'required|string',
            'pertanyaan_custom' => 'nullable|array',
        ]);

        if ($request->hasFile('banner')) {
            if ($form->banner_path && Storage::disk('public')->exists($form->banner_path)) {
                Storage::disk('public')->delete($form->banner_path);
            }
            $path = $request->file('banner')->store('banners', 'public');
            $data['banner_path'] = $path;
        }

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

        if ($form->banner_path && Storage::disk('public')->exists($form->banner_path)) {
            Storage::disk('public')->delete($form->banner_path);
        }

        $form->delete();
        return redirect()->route('diklat.index')->with('success', 'Form berhasil dihapus!');
    }

    public function destroyPeserta($id)
    {
        $peserta = DiklatPeserta::findOrFail($id);

        if ($peserta->pas_foto && Storage::disk('public')->exists($peserta->pas_foto)) {
            Storage::disk('public')->delete($peserta->pas_foto);
        }

        if ($peserta->bukti_pembayaran && Storage::disk('public')->exists($peserta->bukti_pembayaran)) {
            Storage::disk('public')->delete($peserta->bukti_pembayaran);
        }

        $peserta->delete();

        return back()->with('success', 'Data peserta berhasil dihapus!');
    }
    
    public function rekap(Request $request, $id)
    {
        $form = DiklatForm::findOrFail($id);
        $query = DiklatPeserta::where('diklat_form_id', $id);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('instansi', 'like', "%{$search}%")
                  ->orWhere('no_hp', 'like', "%{$search}%");
            });
        }

        // FIX: Menggunakan paginate(20) agar sesuai dengan View
        $pesertas = $query->latest()->paginate(20);
        
        return view('diklat.rekap', compact('form', 'pesertas'));
    }
}