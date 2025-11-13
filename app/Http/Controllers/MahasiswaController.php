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

    public function index(Request $request)
    {
        // 1. Ambil data ruangan untuk dropdown filter
        $ruangans = Ruangan::orderBy('nm_ruangan', 'asc')->get();

        $query = Mahasiswa::where('status', 'aktif');

        // 2. Filter Universitas
        if ($request->has('univ_asal') && !empty($request->univ_asal)) {
            $query->where('univ_asal', $request->univ_asal);
        }

        // 3. (BARU) Filter Ruangan
        if ($request->has('ruangan_id') && !empty($request->ruangan_id)) {
            $query->where('ruangan_id', $request->ruangan_id);
        }

        // 4. Search Nama
        if ($request->has('search') && !empty($request->search)) {
            $query->where('nm_mahasiswa', 'like', '%' . $request->search . '%');
        }

        $mahasiswas = $query->orderBy('created_at', 'desc')
            ->paginate(10)->withQueryString();

        // Trigger auto-deactivate logic
        $mahasiswas->getCollection()->transform(function ($m) {
            $m->sisa_hari;
            return $m->fresh();
        });

        // Pass variable $ruangans ke view
        return view('mahasiswa.index', compact('mahasiswas', 'ruangans'));
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

    public function show($id)
    {
        $mahasiswa = Mahasiswa::findOrFail($id);
        $today = now()->toDateString();
        $last = $mahasiswa->absensis()->whereDate('created_at', $today)->latest()->first();
        $lastStatus = $last ? $last->type : null;
        return view('mahasiswa.show', compact('mahasiswa', 'lastStatus'));
    }

    /**
     * Import multiple mahasiswa from Excel (Support Re-write/Update)
     */
    public function importExcel(Request $request)
    {
        try {
            $rows = json_decode($request->data, true);
            $processed = 0; // Ganti created jadi processed karena bisa update/create
            $errors = [];

            foreach ($rows as $i => $row) {
                $name = $row['Nama'] ?? $row['nama'] ?? null;
                $univ = $row['Universitas'] ?? null; // Kunci unik kedua
                $ruanganName = $row['Ruangan'] ?? null;
                $status = isset($row['Status']) ? strtolower($row['Status']) : 'aktif';
                $tanggalMulai = $row['Tanggal Mulai'] ?? null;
                $tanggalBerakhir = $row['Tanggal Berakhir'] ?? null;

                // Validasi dasar
                if (empty($name)) {
                    $errors[] = "Baris " . ($i + 2) . ": nama kosong";
                    continue;
                }
                if (empty($tanggalMulai) || empty($tanggalBerakhir)) {
                    $errors[] = "Baris " . ($i + 2) . ": tanggal tidak lengkap";
                    continue;
                }

                // 1. Cari Ruangan ID berdasarkan Nama Ruangan di Excel
                $ruanganId = null;
                $nmRuangan = null;

                if ($ruanganName) {
                    $ruanganDb = Ruangan::where('nm_ruangan', 'like', '%' . $ruanganName . '%')->first();
                    if ($ruanganDb) {
                        $ruanganId = $ruanganDb->id;
                        $nmRuangan = $ruanganDb->nm_ruangan;
                    } else {
                        // Jika ruangan di excel tidak ditemukan di DB, set null atau skip
                        $nmRuangan = $ruanganName; // Tetap simpan nama text-nya
                    }
                }

                // 2. SIAPKAN DATA
                $dataToSave = [
                    'nm_mahasiswa' => $name,
                    'univ_asal' => $univ,
                    'prodi' => $row['Prodi'] ?? null,
                    'tanggal_mulai' => $tanggalMulai,
                    'tanggal_berakhir' => $tanggalBerakhir,
                    'status' => in_array($status, ['aktif', 'nonaktif']) ? $status : 'aktif',
                    'ruangan_id' => $ruanganId,
                    'nm_ruangan' => $nmRuangan,
                ];

                // 3. CEK DATA LAMA (RE-WRITE LOGIC)
                // Kita anggap unik jika Nama + Univ sama
                $mahasiswa = Mahasiswa::where('nm_mahasiswa', $name)
                    ->where('univ_asal', $univ)
                    ->first();

                $today = now()->toDateString();

                // ==========================
                // SKENARIO A: UPDATE DATA
                // ==========================
                if ($mahasiswa) {
                    $oldRuanganId = $mahasiswa->ruangan_id;
                    $newRuanganId = $ruanganId;

                    // Cek apakah pindah ruangan?
                    if ($newRuanganId && $newRuanganId != $oldRuanganId) {
                        // 1. Kembalikan Kuota Ruangan Lama
                        if ($oldRuanganId) {
                            $oldSnap = RuanganKetersediaan::firstOrCreate(
                                ['ruangan_id' => $oldRuanganId, 'tanggal' => $today],
                                ['tersedia' => 0] // Default dummy, nanti di increment
                            );
                            // Pastikan sinkron data asli, tapi logic sederhananya increment
                            $oldSnap->increment('tersedia');
                        }

                        // 2. Kurangi Kuota Ruangan Baru (Cek Penuh gak?)
                        $newRuangan = Ruangan::find($newRuanganId);
                        $terisi = Mahasiswa::where('ruangan_id', $newRuanganId)->count();
                        $tersedia = $newRuangan->kuota_ruangan - $terisi;

                        if ($tersedia <= 0) {
                            $errors[] = "Baris " . ($i + 2) . ": Update gagal, ruangan $ruanganName penuh.";
                            continue; // Skip baris ini
                        }

                        $newSnap = RuanganKetersediaan::firstOrCreate(
                            ['ruangan_id' => $newRuanganId, 'tanggal' => $today],
                            ['tersedia' => $tersedia]
                        );

                        // Jika baru dibuat (firstOrCreate), nilainya sudah benar. 
                        // Jika sudah ada, kita decrement.
                        if (!$newSnap->wasRecentlyCreated) {
                            $newSnap->decrement('tersedia');
                        }
                    }

                    // Lakukan Update
                    $mahasiswa->update($dataToSave);
                }
                // ==========================
                // SKENARIO B: BUAT BARU (CREATE)
                // ==========================
                else {
                    // Cek Kuota Ruangan Dulu
                    if ($ruanganId) {
                        $ruangan = Ruangan::find($ruanganId);
                        $terisi = Mahasiswa::where('ruangan_id', $ruanganId)->count();
                        $tersedia = $ruangan->kuota_ruangan - $terisi;

                        if ($tersedia <= 0) {
                            $errors[] = "Baris " . ($i + 2) . ": Ruangan $ruanganName penuh.";
                            continue;
                        }

                        // Update Snapshot Ketersediaan
                        $snapshot = RuanganKetersediaan::firstOrCreate(
                            ['ruangan_id' => $ruanganId, 'tanggal' => $today],
                            ['tersedia' => $tersedia]
                        );

                        if (!$snapshot->wasRecentlyCreated) {
                            $snapshot->decrement('tersedia');
                        }
                    }

                    Mahasiswa::create($dataToSave);
                }

                $processed++;
            }

            return response()->json([
                'success' => true,
                'message' => "Berhasil memproses $processed data (Update/Create)",
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
            'tanggal_mulai' => 'required|date',
            'tanggal_berakhir' => 'required|date|after:tanggal_mulai',
        ]);

        // Set status to aktif by default
        $data['status'] = 'aktif';

        if (!empty($data['ruangan_id'])) {
            $ruangan = Ruangan::find($data['ruangan_id']);

            // Hitung tersedia dari jumlah mahasiswa aktual
            $terisi = Mahasiswa::where('ruangan_id', $ruangan->id)->count();
            $tersedia = $ruangan->kuota_ruangan - $terisi;

            if ($tersedia <= 0) {
                return back()->withErrors(['ruangan_id' => 'Kuota ruangan penuh. Tidak dapat menambahkan mahasiswa.'])->withInput();
            }

            // Update atau buat snapshot untuk tracking
            $today = now()->toDateString();
            $snapshot = RuanganKetersediaan::where('ruangan_id', $ruangan->id)
                ->where('tanggal', $today)
                ->first();

            if ($snapshot) {
                $snapshot->decrement('tersedia');
            } else {
                RuanganKetersediaan::create([
                    'ruangan_id' => $ruangan->id,
                    'tanggal' => $today,
                    'tersedia' => $tersedia - 1
                ]);
            }

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
                'tanggal_mulai' => 'required|date',
                'tanggal_berakhir' => 'required|date|after:tanggal_mulai',
                'status' => 'required|in:aktif,nonaktif',
            ]);

            $oldRuanganId = $mahasiswa->ruangan_id;
            $newRuanganId = $data['ruangan_id'] ?? null;

            /* -------------------------------
         | 1. Jika status berubah aktif → nonaktif
         |    Kembalikan kuota & keluarkan dari ruangan
         --------------------------------*/
            if ($mahasiswa->status === 'aktif' && $data['status'] === 'nonaktif' && $oldRuanganId) {
                $old = Ruangan::find($oldRuanganId);

                $snap = RuanganKetersediaan::firstOrCreate(
                    ['ruangan_id' => $old->id, 'tanggal' => now()->toDateString()],
                    ['tersedia' => $old->kuota_ruangan]
                );

                $snap->increment('tersedia'); // kembalikan kuota

                // kosongkan ruangan mahasiswa
                $data['ruangan_id'] = null;
                $data['nm_ruangan'] = null;

                $mahasiswa->update($data);
                return redirect()->route('mahasiswa.index')->with('success', 'Mahasiswa dinonaktifkan & dikeluarkan dari ruangan.');
            }

            /* -----------------------------------------
         | 2. Jika status tetap aktif dan ruangan tidak berubah
         ------------------------------------------*/
            if ($newRuanganId == $oldRuanganId) {
                $mahasiswa->update($data);
                return redirect()->route('mahasiswa.index')->with('success', 'Mahasiswa diperbarui.');
            }

            /* -----------------------------------------
         | 3. Jika pindah ruangan (aktif → aktif atau nonaktif → aktif)
         ------------------------------------------*/

            $today = now()->toDateString();

            // Kembalikan kuota ruangan lama
            if ($oldRuanganId) {
                $old = Ruangan::find($oldRuanganId);
                $snap = RuanganKetersediaan::where('ruangan_id', $old->id)
                    ->where('tanggal', $today)
                    ->first();

                if ($snap) {
                    $snap->increment('tersedia');
                }
            }

            // Kurangi kuota ruangan baru
            if ($newRuanganId) {
                $new = Ruangan::find($newRuanganId);

                // Hitung tersedia dari jumlah mahasiswa aktual
                $terisi = Mahasiswa::where('ruangan_id', $new->id)->count();
                $tersedia = $new->kuota_ruangan - $terisi;

                if ($tersedia <= 0) {
                    return back()->withErrors(['ruangan_id' => 'Ruangan tujuan penuh. Tidak dapat memindahkan mahasiswa.'])->withInput();
                }

                // Update atau buat snapshot
                $snap = RuanganKetersediaan::where('ruangan_id', $new->id)
                    ->where('tanggal', $today)
                    ->first();

                if ($snap) {
                    $snap->decrement('tersedia');
                } else {
                    RuanganKetersediaan::create([
                        'ruangan_id' => $new->id,
                        'tanggal' => $today,
                        'tersedia' => $tersedia - 1
                    ]);
                }

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
                $today = now()->toDateString();

                $snap = RuanganKetersediaan::where('ruangan_id', $ruangan->id)
                    ->where('tanggal', $today)
                    ->first();

                if ($snap) {
                    $snap->increment('tersedia');
                }
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

        // SELALU hitung dari jumlah mahasiswa aktual - snapshot hanya untuk tracking history
        $terisi = Mahasiswa::where('ruangan_id', $ruangan->id)->count();
        $tersedia = $ruangan->kuota_ruangan - $terisi;
        $tersedia = max(0, $tersedia); // Jangan negatif

        return response()->json([
            'nm_ruangan' => $ruangan->nm_ruangan,
            'kuota_total' => $ruangan->kuota_ruangan,
            'tersedia' => $tersedia,
            'terisi' => $terisi,
            'status' => $tersedia > 0 ? 'Tersedia' : 'Penuh'
        ]);
    }

    /**
     * API: Get list of universities for live search
     */
    public function searchUniversitas(Request $request)
    {
        $search = $request->query('q', '');

        $universitas = Mahasiswa::where('status', 'aktif')
            ->where('univ_asal', 'like', '%' . $search . '%')
            ->distinct('univ_asal')
            ->pluck('univ_asal')
            ->filter(fn($u) => !empty($u))
            ->values();

        return response()->json($universitas);
    }

    /**
     * API: Return mahasiswa links filtered by optional params (used by copyAllLinks)
     */
    public function copyLinks(Request $request)
    {
        $query = Mahasiswa::query();

        // Filter Universitas
        if ($request->has('univ_asal') && !empty($request->univ_asal)) {
            $query->where('univ_asal', $request->univ_asal);
        }

        // (BARU) Filter Ruangan
        if ($request->has('ruangan_id') && !empty($request->ruangan_id)) {
            $query->where('ruangan_id', $request->ruangan_id);
        }

        // Search Name
        if ($request->has('search') && !empty($request->search)) {
            $query->where('nm_mahasiswa', 'like', '%' . $request->search . '%');
        }

        $rows = $query->orderBy('created_at', 'desc')
            ->get(['nm_mahasiswa', 'share_token']);

        $data = $rows->map(function ($m) {
            return [
                'nama' => $m->nm_mahasiswa,
                'link' => route('absensi.card', $m->share_token)
            ];
        });

        return response()->json($data);
    }
}
