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
     * Import multiple mahasiswa from Excel/SheetJS
     */
    private function importMahasiswa(Request $request)
    {
        try {
            $rows = json_decode($request->data, true);
            $created = 0;
            $errors = [];

            foreach ($rows as $i => $row) {
                $name = $row['Nama'] ?? $row['nama'] ?? $row['Nama Mahasiswa'] ?? null;
                $univ = $row['Universitas'] ?? $row['universitas'] ?? $row['univ_asal'] ?? null;
                $prodi = $row['Prodi'] ?? $row['prodi'] ?? null;
                $ruanganName = $row['Ruangan'] ?? $row['ruangan'] ?? null;
                $status = $row['Status'] ?? $row['status'] ?? 'aktif';

                if (empty($name)) {
                    $errors[] = "Baris " . ($i + 2) . ": nama kosong";
                    continue;
                }

                $mahasiswaData = [
                    'nm_mahasiswa' => $name,
                    'univ_asal' => $univ,
                    'prodi' => $prodi,
                    'status' => in_array($status, ['aktif', 'nonaktif']) ? $status : 'aktif',
                ];

                if (!empty($ruanganName)) {
                    $ruangan = Ruangan::where('nm_ruangan', $ruanganName)->first();
                    if ($ruangan) {
                        // use snapshot-aware availability
                        if ($ruangan->getKuotaTersedia() <= 0) {
                            $errors[] = "Baris " . ($i + 2) . ": ruangan {$ruanganName} kuota penuh";
                            $mahasiswaData['nm_ruangan'] = $ruanganName;
                        } else {
                            $mahasiswaData['ruangan_id'] = $ruangan->id;
                            $mahasiswaData['nm_ruangan'] = $ruangan->nm_ruangan;
                            // Update kuota column and snapshot
                            $ruangan->decrement('kuota_ruangan');
                            RuanganKetersediaan::updateOrCreate(
                                ['ruangan_id' => $ruangan->id, 'tanggal' => now()->toDateString()],
                                ['tersedia' => $ruangan->kuota_ruangan]
                            );
                        }
                    } else {
                        $mahasiswaData['nm_ruangan'] = $ruanganName;
                    }
                }

                try {
                    Mahasiswa::create($mahasiswaData);
                    $created++;
                } catch (\Exception $e) {
                    $errors[] = "Baris " . ($i + 2) . ": " . $e->getMessage();
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Berhasil mengimpor $created mahasiswa",
                'errors' => $errors
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saat import: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Store single mahasiswa - FIXED VERSION
     */
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

        // Use snapshot-aware availability and keep kuota_ruangan in sync
        if (!empty($data['ruangan_id'])) {
            $ruangan = Ruangan::find($data['ruangan_id']);
            if (!$ruangan) {
                return redirect()->back()->withErrors(['ruangan_id' => 'Ruangan tidak ditemukan'])->withInput();
            }

            if ($ruangan->getKuotaTersedia() <= 0) {
                return redirect()->back()->withErrors(['ruangan_id' => 'Kuota ruangan penuh'])->withInput();
            }

            // Kurangi kuota dan set nama ruangan, lalu snapshot
            $ruangan->decrement('kuota_ruangan');
            RuanganKetersediaan::updateOrCreate(
                ['ruangan_id' => $ruangan->id, 'tanggal' => now()->toDateString()],
                ['tersedia' => $ruangan->kuota_ruangan]
            );

            $data['nm_ruangan'] = $ruangan->nm_ruangan;
        }

        Mahasiswa::create($data);

        return redirect()->route('mahasiswa.index')->with('success', 'Mahasiswa berhasil ditambahkan.');
    }

    public function show($id)
    {
        $mahasiswa = Mahasiswa::findOrFail($id);
        $today = now()->toDateString();
        $last = $mahasiswa->absensis()->whereDate('created_at', $today)->latest()->first();
        $lastStatus = $last ? $last->type : null;
        return view('mahasiswa.show', compact('mahasiswa', 'lastStatus'));
    }

    public function edit($id)
    {
        $mahasiswa = Mahasiswa::findOrFail($id);
        $ruangans = Ruangan::all();
        return view('mahasiswa.edit', compact('mahasiswa', 'ruangans'));
    }

    /**
     * Update the specified resource in storage - FIXED VERSION
     */
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

            $newRuanganId = $data['ruangan_id'] ?? null;
            $oldRuanganId = $mahasiswa->ruangan_id;

            // Jika TIDAK ada perubahan ruangan
            if ($newRuanganId == $oldRuanganId) {
                if ($newRuanganId) {
                    $currentRuangan = Ruangan::find($newRuanganId);
                    $data['nm_ruangan'] = $currentRuangan->nm_ruangan;
                }
                $mahasiswa->update($data);
                return redirect()->route('mahasiswa.index')->with('success', 'Mahasiswa berhasil diperbarui.');
            }

            // VALIDATE availability on destination ruangan using snapshot table
            if ($newRuanganId) {
                $newRuangan = Ruangan::find($newRuanganId);
                if ($newRuangan->getKuotaTersedia() <= 0) {
                    return redirect()->back()->withErrors(['ruangan_id' => 'Kuota ruangan tujuan penuh'])->withInput();
                }
            }

            // **PROSES PEMINDAHAN:**

            // 1. Kembalikan kuota ruangan lama (jika ada)
            if ($oldRuanganId) {
                $oldRuangan = Ruangan::find($oldRuanganId);
                if ($oldRuangan) {
                    $oldRuangan->increment('kuota_ruangan');
                    RuanganKetersediaan::updateOrCreate(
                        ['ruangan_id' => $oldRuangan->id, 'tanggal' => now()->toDateString()],
                        ['tersedia' => $oldRuangan->kuota_ruangan]
                    );
                }
            }

            // 2. Kurangi kuota ruangan baru (jika ada)
            if ($newRuanganId) {
                $newRuangan = Ruangan::find($newRuanganId);
                $newRuangan->decrement('kuota_ruangan');
                RuanganKetersediaan::updateOrCreate(
                    ['ruangan_id' => $newRuangan->id, 'tanggal' => now()->toDateString()],
                    ['tersedia' => $newRuangan->kuota_ruangan]
                );
                $data['nm_ruangan'] = $newRuangan->nm_ruangan;
            } else {
                $data['nm_ruangan'] = null;
            }

            // 3. Update mahasiswa
            $mahasiswa->update($data);

            return redirect()->route('mahasiswa.index')->with('success', 'Mahasiswa berhasil diperbarui.');
        });
    }

    public function destroy($id)
    {
        return DB::transaction(function () use ($id) {
            $mahasiswa = Mahasiswa::findOrFail($id);

            if ($mahasiswa->ruangan_id) {
                $ruangan = Ruangan::find($mahasiswa->ruangan_id);
                if ($ruangan) {
                    $ruangan->increment('kuota_ruangan');
                    RuanganKetersediaan::updateOrCreate(
                        ['ruangan_id' => $ruangan->id, 'tanggal' => now()->toDateString()],
                        ['tersedia' => $ruangan->kuota_ruangan]
                    );
                }
            }

            $mahasiswa->delete();

            return redirect()->route('mahasiswa.index')->with('success', 'Mahasiswa berhasil dihapus.');
        });
    }

    /**
     * HARD RESET semua kuota - PASTI WORK
     */
    public function hardResetKuota()
    {
        DB::transaction(function () {
            $ruangans = Ruangan::all();

                foreach ($ruangans as $ruangan) {
                // Hitung ulang dari mahasiswa
                $jumlahMahasiswa = Mahasiswa::where('ruangan_id', $ruangan->id)->count();
                $kuotaSebenarnya = $ruangan->kapasitas_total - $jumlahMahasiswa;

                // Update kuota column and create/update snapshot for today
                $ruangan->kuota_ruangan = $kuotaSebenarnya;
                $ruangan->save();

                RuanganKetersediaan::updateOrCreate(
                    ['ruangan_id' => $ruangan->id, 'tanggal' => now()->toDateString()],
                    ['tersedia' => $ruangan->kuota_ruangan]
                );

                \Log::info("Reset {$ruangan->nm_ruangan}: {$jumlahMahasiswa}/{$ruangan->kapasitas_total} = Kuota: {$kuotaSebenarnya}");
            }
        });

        return redirect()->route('mahasiswa.index')
            ->with('success', 'Hard reset kuota berhasil! Semua kuota telah disesuaikan.');
    }

    /**
     * Check and fix kuota consistency
     */
    public function checkKuotaConsistency()
    {
        $ruangans = Ruangan::all();
        $fixedCount = 0;

        foreach ($ruangans as $ruangan) {
            $jumlahMahasiswa = Mahasiswa::where('ruangan_id', $ruangan->id)->count();
            $expectedKuota = $ruangan->kapasitas_total - $jumlahMahasiswa;

            if ($ruangan->kuota_ruangan != $expectedKuota) {
                $ruangan->kuota_ruangan = $expectedKuota;
                $ruangan->save();
                RuanganKetersediaan::updateOrCreate(
                    ['ruangan_id' => $ruangan->id, 'tanggal' => now()->toDateString()],
                    ['tersedia' => $ruangan->kuota_ruangan]
                );
                $fixedCount++;
            }
        }

        return redirect()->route('mahasiswa.index')
            ->with('success', "Consistency check completed. Fixed $fixedCount ruangan(s).");
    }

    public function getRuanganInfo($id)
    {
        $ruangan = Ruangan::find($id);
        if (!$ruangan) {
            return response()->json(['error' => 'Ruangan tidak ditemukan'], 404);
        }

        $jumlahMahasiswa = Mahasiswa::where('ruangan_id', $id)->count();
        $kuotaTersedia = $ruangan->getKuotaTersedia();

        return response()->json([
            'nm_ruangan' => $ruangan->nm_ruangan,
            'kapasitas_total' => $ruangan->kapasitas_total,
            'kuota_tersedia' => $kuotaTersedia,
            'jumlah_mahasiswa' => $jumlahMahasiswa,
            'status' => $kuotaTersedia > 0 ? 'Tersedia' : 'Penuh'
        ]);
    }
}
