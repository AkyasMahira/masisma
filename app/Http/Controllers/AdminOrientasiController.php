<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrientasiResult;

class AdminOrientasiController extends Controller
{
    public function index(Request $request) 
    {
        // Eager load semua relasi untuk mencegah N+1 query
        $query = OrientasiResult::with(['user.mou', 'mahasiswa.mou']);

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhereHas('mahasiswa', function($q) use ($search) {
                $q->where('nm_mahasiswa', 'like', "%{$search}%")
                  ->orWhereHas('mou', function($qMou) use ($search) {
                      $qMou->where('nama_instansi', 'like', "%{$search}%")
                           ->orWhere('nama_universitas', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('gelombang')) $query->where('gelombang', $request->gelombang);
        if ($request->filled('tahun')) $query->where('tahun', $request->tahun);
        if ($request->filled('date_from')) $query->whereDate('created_at', '>=', $request->date_from);
        if ($request->filled('date_to')) $query->whereDate('created_at', '<=', $request->date_to);

        $results = $query->latest()->paginate(10)->appends($request->all());

        return view('admin.orientasi.index', compact('results'));
    }

    public function destroy($id)
    {
        OrientasiResult::findOrFail($id)->delete();
        return back()->with('success', 'Data progres orientasi berhasil direset.');
    }
    
    public function apiData(Request $request)
    {
        $query = OrientasiResult::with(['user.mou', 'mahasiswa.mou']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhereHas('mahasiswa', function($q) use ($search) {
                $q->where('nm_mahasiswa', 'like', "%{$search}%")
                  ->orWhereHas('mou', function($qMou) use ($search) {
                      $qMou->where('nama_instansi', 'like', "%{$search}%")
                           ->orWhere('nama_universitas', 'like', "%{$search}%");
                  });
            });
        }
        if ($request->filled('gelombang')) $query->where('gelombang', $request->gelombang);
        if ($request->filled('tahun')) $query->where('tahun', $request->tahun);
        if ($request->filled('date_from')) $query->whereDate('created_at', '>=', $request->date_from);
        if ($request->filled('date_to')) $query->whereDate('created_at', '<=', $request->date_to);

        $results = $query->latest()->get()->map(function($item) {
            // FALLBACK DATA EXCEL
            $mhs = $item->mahasiswa ?? \App\Models\Mahasiswa::with('mou')->where('user_id', $item->user_id)->latest()->first();
            $mou = $mhs ? $mhs->mou : ($item->user ? $item->user->mou : null);
            $univ = $mou ? ($mou->nama_instansi ?? $mou->nama_universitas) : '-';
            
            return [
                'Nama Peserta' => $mhs ? $mhs->nm_mahasiswa : ($item->user->name ?? 'User Terhapus'),
                'Asal Instansi' => $univ,
                'Prodi' => $mhs->prodi ?? '-',
                'Tipe' => strtoupper($mhs->tipe_mahasiswa ?? '-'),
                'Pre-Test' => $item->pre_test_score ?? 0,
                'Post-Test' => $item->post_test_score ?? '-',
                'Status' => $item->status,
                'Gelombang' => $item->gelombang,
                'Tahun' => $item->tahun,
                'Tanggal Ujian' => $item->created_at->format('d-m-Y'),
            ];
        });

        return response()->json($results);
    }
}