<?php

namespace App\Http\Controllers; 

use App\Models\Mou; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MouController extends Controller
{
    /**
     * Halaman LIST (Halaman Kedua)
     */
    public function index()
    {
        // Ambil semua data MOU, diurutkan dari yang terbaru
        $mous = Mou::orderBy('created_at', 'desc')->get();

        // Tampilkan view 'mou.index' dan kirim data 'mous'
        return view('mou.index', compact('mous'));
    }

    /**
     * Halaman CREATE (Halaman Pertama)
     */
    public function create()
    {
        // Hanya tampilkan view form
        return view('mou.create');
    }

    /**
     * Logika untuk MENYIMPAN data dari form CREATE
     */
    public function store(Request $request)
    {
        // 1. Validasi input
        $request->validate([
            'nama_universitas' => 'required|string|max:255',
            'tanggal_masuk'    => 'required|date',
            'tanggal_keluar'   => 'required|date|after_or_equal:tanggal_masuk',
            'file_mou'         => 'required|file|mimes:pdf,doc,docx|max:5120', // Max 5MB
            'surat_keterangan' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // Max 5MB
            'keterangan'       => 'nullable|string',
        ]);

        // 2. Handle File Upload
        // Penting: Pastikan Anda sudah menjalankan `php artisan storage:link`

        // Simpan 'file_mou' di 'storage/app/public/file_mou'
        $pathMou = $request->file('file_mou')->store('public/file_mou');

        // Simpan 'surat_keterangan' di 'storage/app/public/surat_keterangan'
        $pathSurat = $request->file('surat_keterangan')->store('public/surat_keterangan');

        // 3. Simpan data ke database
        Mou::create([
            'nama_universitas' => $request->nama_universitas,
            'tanggal_masuk'    => $request->tanggal_masuk,
            'tanggal_keluar'   => $request->tanggal_keluar,
            'keterangan'       => $request->keterangan,
            'file_mou'         => $pathMou,
            'surat_keterangan' => $pathSurat,
        ]);

        // 4. Redirect kembali ke halaman index (list) dengan pesan sukses
        return redirect()->route('mou.index')
            ->with('success', 'Data MOU berhasil ditambahkan.');
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
