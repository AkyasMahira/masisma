<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CorporateInstructor;
use Illuminate\Http\Request;

class CorporateInstructorController extends Controller
{
    /**
     * Menampilkan daftar semua Corporate Instructor (Read)
     */
    public function index()
    {
        $cis = CorporateInstructor::latest()->get();
        // Pastikan Anda membuat view ini: resources/views/admin/ci/index.blade.php
        return view('admin.ci.index', compact('cis'));
    }

    /**
     * Menampilkan formulir untuk membuat CI baru (Create - Form)
     */
    public function create()
    {
        // Pastikan Anda membuat view ini: resources/views/admin/ci/create.blade.php
        return view('admin.ci.create');
    }

    /**
     * Menyimpan data CI yang baru dibuat (Create - Store)
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama'    => 'required|string|max:255',
            'no_hp'   => 'nullable|string|max:20',
            'bidang'  => 'required|string|max:255',
        ]);

        CorporateInstructor::create($request->all());

        return redirect()->route('admin.ci.index')->with('success', 'Corporate Instructor berhasil ditambahkan.');
    }

    /**
     * Menampilkan formulir untuk mengedit CI (Update - Form)
     */
    public function edit(CorporateInstructor $ci)
    {
        // Pastikan Anda membuat view ini: resources/views/admin/ci/edit.blade.php
        return view('admin.ci.edit', compact('ci'));
    }

    /**
     * Memperbarui data CI (Update - Store)
     */
    public function update(Request $request, CorporateInstructor $ci)
    {
        $request->validate([
            'nama'    => 'required|string|max:255',
            'no_hp'   => 'nullable|string|max:20',
            'bidang'  => 'required|string|max:255',
        ]);

        $ci->update($request->all());

        return redirect()->route('admin.ci.index')->with('success', 'Corporate Instructor berhasil diperbarui.');
    }

    /**
     * Menghapus data CI (Delete)
     */
    public function destroy(CorporateInstructor $ci)
    {
        // Anda mungkin ingin menambahkan cek apakah CI ini sedang ditugaskan ke pengajuan aktif
        
        $ci->delete();

        return redirect()->route('admin.ci.index')->with('success', 'Corporate Instructor berhasil dihapus.');
    }
}