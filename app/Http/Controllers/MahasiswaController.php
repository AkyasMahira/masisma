<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\Ruangan;
use App\Models\RuanganKetersediaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MahasiswaController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->check() || auth()->user()->role !== 'admin') {
                abort(403);
            }
            return $next($request);
        });
    }

    public function index()
    {
        $mahasiswas = Mahasiswa::orderBy('created_at', 'desc')->get();
        return view('mahasiswa.index', compact('mahasiswas'));
    }

    public function create()
    {
        $ruangans = Ruangan::all();
        return view('mahasiswa.create', compact('ruangans'));
    }

    public function store(Request $request)
    {
        if ($request->has('data')) {
            return $this->importMahasiswa($request);
        }
        return $this->storeSingleMahasiswa($request);
    }

    /**
     * Import multiple mahasiswa from Excel
     */
    private function importMahasiswa(Request $request)
    {
        try {
            $rows = json_decode($request->data, true);
            $created = 0;
            $errors = [];

            foreach ($rows as $i => $row) {
                $name = $row['Nama'] ?? $row['nama'] ?? null;
                $ruanganName = $row['Ruangan'] ?? null;
                $status = $row['Status'] ?? 'aktif';

                if (empty($name)) {
                    $errors[] = "Baris " . ($i + 2) . ": nama kosong";
                    continue;
                }

                $data = [
                    'nm_mahasiswa' => $name,
                    'univ_asal' => $row['Universitas'] ?? null,
                    'prodi' => $row['Prodi'] ?? null,
                    'status' => in_array($status, ['aktif', 'nonaktif']) ? $status : 'aktif',
                ];

                if ($ruanganName) {
                    $ruangan = Ruangan::where('nm_ruangan', $ruanganName)->first();

                    if ($ruangan) {
                        $snapshot = RuanganKetersediaan::firstOrCreate(
                            ['ruangan_id' => $ruangan->id, 'tanggal' => now()->toDateString()],
                            ['tersedia' => $ruangan->kuota_ruangan] // ✅ pakai kolom kuota_ruangan
                        );

                        if ($snapshot->tersedia <= 0) {
                            $errors[] = "Baris " . ($i + 2) . ": ruangan {$ruanganName} penuh";
                            continue;
                        } else {
                            $snapshot->decrement('tersedia');
                            $data['ruangan_id'] = $ruangan->id;
                            $data['nm_ruangan'] = $ruangan->nm_ruangan;
                        }
                    } else {
                        $data['nm_ruangan'] = $ruanganName;
                    }
                }

                Mahasiswa::create($data);
                $created++;
            }

            return response()->json([
                'success' => true,
                'message' => "Berhasil mengimpor $created mahasiswa",
                'errors' => $errors
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    private function storeSingleMahasiswa(Request $request)
    {
        $data = $request->validate([
            'nm_mahasiswa' => 'required|string|max:255',
            'univ_asal' => 'nullable|string|max:255',
            'prodi' => 'nullable|string|max:255',
            'nm_ruangan' => 'nullable|string|max:255',
            'ruangan_id' => 'nullable|exists:ruangans,id',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        if (!empty($data['ruangan_id'])) {
            $ruangan = Ruangan::find($data['ruangan_id']);
            $snapshot = RuanganKetersediaan::firstOrCreate(
                ['ruangan_id' => $ruangan->id, 'tanggal' => now()->toDateString()],
                ['tersedia' => $ruangan->kuota_ruangan] // ✅ fix di sini
            );

            if ($snapshot->tersedia <= 0) {
                return back()->withErrors(['ruangan_id' => 'Kuota ruangan penuh'])->withInput();
            }

            $snapshot->decrement('tersedia');
            $data['nm_ruangan'] = $ruangan->nm_ruangan;
        }

        Mahasiswa::create($data);
        return redirect()->route('mahasiswa.index')->with('success', 'Mahasiswa berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $mahasiswa = Mahasiswa::findOrFail($id);
        $ruangans = Ruangan::all();

        return view('mahasiswa.edit', compact('mahasiswa', 'ruangans'));
    }

    public function update(Request $request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $mahasiswa = Mahasiswa::findOrFail($id);

            $data = $request->validate([
                'nm_mahasiswa' => 'required|string|max:255',
                'univ_asal' => 'nullable|string|max:255',
                'prodi' => 'nullable|string|max:255',
                'nm_ruangan' => 'nullable|string|max:255',
                'ruangan_id' => 'nullable|exists:ruangans,id',
                'status' => 'required|in:aktif,nonaktif',
            ]);

            $oldRuanganId = $mahasiswa->ruangan_id;
            $newRuanganId = $data['ruangan_id'] ?? null;

            if ($newRuanganId == $oldRuanganId) {
                $mahasiswa->update($data);
                return redirect()->route('mahasiswa.index')->with('success', 'Mahasiswa diperbarui.');
            }

            // Kembalikan ketersediaan ruangan lama
            if ($oldRuanganId) {
                $old = Ruangan::find($oldRuanganId);
                $snap = RuanganKetersediaan::firstOrCreate(
                    ['ruangan_id' => $old->id, 'tanggal' => now()->toDateString()],
                    ['tersedia' => $old->kuota_ruangan] // ✅ fix di sini
                );
                $snap->increment('tersedia');
            }

            // Kurangi ketersediaan ruangan baru
            if ($newRuanganId) {
                $new = Ruangan::find($newRuanganId);
                $snap = RuanganKetersediaan::firstOrCreate(
                    ['ruangan_id' => $new->id, 'tanggal' => now()->toDateString()],
                    ['tersedia' => $new->kuota_ruangan] // ✅ fix di sini
                );

                if ($snap->tersedia <= 0) {
                    return back()->withErrors(['ruangan_id' => 'Ruangan tujuan penuh'])->withInput();
                }
                $snap->decrement('tersedia');
                $data['nm_ruangan'] = $new->nm_ruangan;
            } else {
                $data['nm_ruangan'] = null;
            }

            $mahasiswa->update($data);
            return redirect()->route('mahasiswa.index')->with('success', 'Mahasiswa berhasil diperbarui.');
        });
    }

    public function destroy($id)
    {
        return DB::transaction(function () use ($id) {
            $mhs = Mahasiswa::findOrFail($id);

            if ($mhs->ruangan_id) {
                $ruangan = Ruangan::find($mhs->ruangan_id);
                $snap = RuanganKetersediaan::firstOrCreate(
                    ['ruangan_id' => $ruangan->id, 'tanggal' => now()->toDateString()],
                    ['tersedia' => $ruangan->kuota_ruangan] // ✅ fix di sini
                );
                $snap->increment('tersedia');
            }

            $mhs->delete();
            return redirect()->route('mahasiswa.index')->with('success', 'Mahasiswa dihapus.');
        });
    }

    public function getRuanganInfo($id)
    {
        $ruangan = Ruangan::find($id);
        if (!$ruangan) {
            return response()->json(['error' => 'Ruangan tidak ditemukan'], 404);
        }

        $snapshot = RuanganKetersediaan::where('ruangan_id', $ruangan->id)
            ->where('tanggal', now()->toDateString())
            ->first();

        $tersedia = $snapshot->tersedia ?? $ruangan->kuota_ruangan;
        $terisi = Mahasiswa::where('ruangan_id', $ruangan->id)->count();

        return response()->json([
            'nm_ruangan' => $ruangan->nm_ruangan,
            'kuota_total' => $ruangan->kuota_ruangan,
            'tersedia' => $tersedia,
            'terisi' => $terisi,
            'status' => $tersedia > 0 ? 'Tersedia' : 'Penuh'
        ]);
    }
}
