<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MasterPelatihan;
use Illuminate\Support\Facades\DB;

class MasterPelatihanController extends Controller
{
    public function index()
    {
        $pelatihans = MasterPelatihan::latest()->paginate(15);
        return view('admin.master_pelatihan.index', compact('pelatihans'));
    }
    public function create()
    {
        return view('admin.master_pelatihan.create');
    }

    public function edit($id)
    {
        $pelatihan = MasterPelatihan::findOrFail($id);
        return view('admin.master_pelatihan.edit', compact('pelatihan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_pelatihan' => 'required|string|max:255',
            'kategori'       => 'nullable|string|max:100',
            'durasi'         => 'nullable|string|max:50',
            'sasaran'        => 'nullable|string|max:255',
        ]);

        MasterPelatihan::create($request->all());
        return back()->with('success', 'Data pelatihan berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_pelatihan' => 'required|string|max:255',
        ]);

        $pelatihan = MasterPelatihan::findOrFail($id);
        $pelatihan->update($request->all());

        return back()->with('success', 'Data pelatihan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        MasterPelatihan::findOrFail($id)->delete();
        return back()->with('success', 'Data pelatihan berhasil dihapus.');
    }

    public function importCsv(Request $request)
    {
        $request->validate([
            'file_csv' => 'required|mimes:csv,txt|max:2048'
        ]);

        $file = $request->file('file_csv');
        $handle = fopen($file->getPathname(), "r");

        $header = true;
        $sukses = 0;

        DB::beginTransaction();
        try {
            while (($row = fgetcsv($handle, 1000, ";")) !== false) {
                if ($header) {
                    $header = false; // Lewati baris pertama (Header)
                    continue;
                }

                if (!empty($row[0])) {
                    MasterPelatihan::create([
                        'nama_pelatihan' => $row[0],
                        'kategori'       => $row[1] ?? 'Umum',
                        'durasi'         => $row[2] ?? '-',
                        'sasaran'        => $row[3] ?? '-',
                    ]);
                    $sukses++;
                }
            }
            fclose($handle);
            DB::commit();

            return back()->with('success', "$sukses data pelatihan berhasil diimpor.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mengimpor data. Pastikan format CSV sesuai (Pemisah titik koma / ;)');
        }
    }
}