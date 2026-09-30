<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\MaterialFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MaterialController extends Controller
{
    public function index(Request $request)
{
    $query = Material::with('files');

    // Fitur Search berdasarkan Judul atau Subtitle
    if ($request->has('search') && $request->search != '') {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('title', 'like', "%$search%")
              ->orWhere('subtitle', 'like', "%$search%");
        });
    }

    // Pagination 10 data per halaman
    $materials = $query->orderBy('order', 'asc')->paginate(10);

    return view('admin.material.index', compact('materials'));
}

    public function create() {
        return view('admin.material.create');
    }

    public function store(Request $request) {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'order' => 'required|integer'
        ]);
        
        $material = Material::create($request->all());
        // Setelah buat materi, langsung lempar ke halaman input file
        return redirect()->route('admin.materi.files', $material->id)->with('success', 'Info materi disimpan. Sekarang upload filenya!');
    }

    public function edit($id) {
        $material = Material::findOrFail($id);
        return view('admin.material.edit', compact('material'));
    }

    public function update(Request $request, $id) {
        $request->validate(['title' => 'required', 'order' => 'required|integer']);
        $material = Material::findOrFail($id);
        $material->update($request->all());
        return redirect()->route('admin.materi.index')->with('success', 'Materi berhasil diupdate!');
    }

    public function destroy($id) {
        $material = Material::with('files')->findOrFail($id);
        foreach($material->files as $file) {
            Storage::disk('public')->delete($file->file_path);
        }
        $material->delete();
        return back()->with('success', 'Materi & file dihapus total!');
    }

    // --- HALAMAN KELOLA FILE (Langkah 2) ---
    public function manageFiles($id) {
        $material = Material::with('files')->findOrFail($id);
        // Pastikan view ini ada di resources/views/admin/material/files.blade.php
        return view('admin.material.files', compact('material'));
    }

    public function storeFile(Request $request, $id) {
        $request->validate([
            'file_name' => 'required',
            'file_upload' => 'required|file|max:102400' // Max 100MB
            
        ]);

        $file = $request->file('file_upload');
        $path = $file->store('materi', 'public');

        MaterialFile::create([
            'material_id' => $id,
            'file_name' => $request->file_name,
            'file_path' => $path,
            'description' => $request->description,
            'file_type' => $file->getClientOriginalExtension(),
        ]);

        return back()->with('success', 'File berhasil diupload!');
    }

    public function destroyFile($id) {
        $file = MaterialFile::findOrFail($id);
        Storage::disk('public')->delete($file->file_path);
        $file->delete();
        return back()->with('success', 'File berhasil dihapus!');
    }
}