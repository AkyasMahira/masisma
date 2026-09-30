<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ruangan;
use App\Services\RoomSyncService;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class RuanganController extends Controller
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

    
public function index(Request $request, RoomSyncService $roomSyncService)
{
    // 1. Jeda Eksekusi Sync (Throttle 10 Menit)
    \Illuminate\Support\Facades\Cache::remember('sync_rooms_cooldown', 600, function () use ($roomSyncService) {
        $roomSyncService->syncRooms();
        return true; 
    });

    // 2. QUERY SUPER RINGAN (Hapus 'univ_asal', ganti 'mou_id', lalu panggil relasi mou)
    $query = Ruangan::with([
        'user',
        'mahasiswa' => function($q) {
            $q->select('id', 'ruangan_id', 'mou_id', 'nm_mahasiswa', 'status', 'prodi')
              ->with('mou:id,nama_instansi,nama_universitas'); // Load relasi kampus dengan sangat ringan
        }
    ])->withCount([
        'mahasiswa as mahasiswa_aktif_count' => function($q) {
            $q->where('status', 'aktif'); 
        }
    ]); 

    if ($request->filled('search')) {
        $query->where('nm_ruangan', 'like', '%' . $request->search . '%');
    }
    
    $ruangan = $query->paginate(6)->appends($request->query());

    // 3. INJECT DATA KAMPUS KE DALAM JSON AGAR MODAL TIDAK ERROR
    // Kita buatkan "univ_asal" bayangan untuk dikirim ke View
    $ruangan->getCollection()->transform(function($room) {
        $room->mahasiswa->transform(function($mhs) {
            $mhs->univ_asal = $mhs->mou ? ($mhs->mou->nama_instansi ?? $mhs->mou->nama_universitas) : '-';
            return $mhs;
        });
        return $room;
    });

    return view('ruangan.index', compact('ruangan'));
}
    public function create()
    {
        return view('ruangan.create');
    }

    public function store(Request $request)
    {
        // Fitur Import Excel (Tidak berubah, hanya user creation manual jika perlu logic tambahan disana)
        if ($request->has('data')) {
            return $this->processImport($request);
        }

        $request->validate([
            'nm_ruangan' => 'required|string|max:255|unique:ruangans',
            'kuota_ruangan' => 'required|integer|min:1',
            'kategori' => 'required|in:shift,non_shift'
        ]);

        DB::transaction(function() use ($request) {
            // 1. Buat Akun User untuk Ruangan
            // Format email: nama_ruangan_random@ruangan.local
            // Password Default: 12345678
            $slug = Str::slug($request->nm_ruangan);
            $email = $slug . '.' . Str::random(4) . '@ruangan.local';
            
            $user = User::create([
                'name' => 'Kepala ' . $request->nm_ruangan,
                'email' => $email,
                'password' => Hash::make('12345678'),
                'role' => 'ruangan', // Role khusus
                'is_approved' => 1,
            ]);

            // 2. Buat Ruangan
            Ruangan::create([
                'nm_ruangan' => $request->nm_ruangan,
                'kuota_ruangan' => $request->kuota_ruangan,
                'kategori' => $request->kategori,
                'user_id' => $user->id
            ]);
        });

        return redirect()->route('ruangan.index')->with('success', 'Ruangan & Akun Login berhasil dibuat (Pass: 12345678).');
    }

    public function edit($id)
    {
        $ruangan = Ruangan::with('user')->findOrFail($id);
        return view('ruangan.edit', compact('ruangan'));
    }

    public function update(Request $request, $id)
    {
        $ruangan = Ruangan::findOrFail($id);
        
        $validated = $request->validate([
            'nm_ruangan' => 'required|string|max:255|unique:ruangans,nm_ruangan,' . $ruangan->id,
            'kuota_ruangan' => 'required|integer|min:1',
            'kategori' => 'required|in:shift,non_shift',
            // Opsi ganti password user ruangan jika mau (opsional)
            'reset_password' => 'nullable|boolean' 
        ]);

        DB::transaction(function() use ($ruangan, $request, $validated) {
            // Update Nama User jika nama ruangan berubah
            if ($ruangan->user && $request->nm_ruangan !== $ruangan->nm_ruangan) {
                $ruangan->user->update([
                    'name' => 'Kepala ' . $request->nm_ruangan
                ]);
            }

            // Jika user minta reset password ruangan
            if ($request->filled('reset_password') && $request->reset_password == 1 && $ruangan->user) {
                $ruangan->user->update([
                    'password' => Hash::make('12345678')
                ]);
            }

            $ruangan->update([
                'nm_ruangan' => $validated['nm_ruangan'],
                'kategori' => $validated['kategori'],
                'kuota_ruangan' => $validated['kuota_ruangan']
            ]);
        });

        return redirect()->route('ruangan.index')->with('success', 'Ruangan diperbarui!');
    }

    public function destroy($id)
    {
        $ruangan = Ruangan::findOrFail($id);
        
        // Hapus usernya juga jika ada
        if($ruangan->user_id) {
            User::destroy($ruangan->user_id);
        }
        
        $ruangan->delete();
        return redirect()->route('ruangan.index')->with('success', 'Ruangan & Akun berhasil dihapus!');
    }

    // --- FITUR BARU: Generate User untuk Data Lama ---
    public function generateUsers()
    {
        $ruangans = Ruangan::whereNull('user_id')->get();
        $count = 0;

        foreach($ruangans as $r) {
            $slug = Str::slug($r->nm_ruangan);
            $email = $slug . '.' . Str::random(4) . '@ruangan.local';

            $user = User::create([
                'name' => 'Kepala ' . $r->nm_ruangan,
                'email' => $email,
                'password' => Hash::make('12345678'),
                'role' => 'ruangan',
                'is_approved' => 1,
            ]);

            $r->update(['user_id' => $user->id]);
            $count++;
        }

        return redirect()->route('ruangan.index')->with('success', "Berhasil membuat $count akun untuk ruangan lama.");
    }

    // Helper untuk Import (dipisah biar rapi)
    private function processImport($request)
    {
        try {
            $data = json_decode($request->data, true);
            $success = 0;
            $errors = [];

            foreach ($data as $row) {
                if (empty($row['Nama Ruangan']) || empty($row['Kuota Ruangan'])) continue;
                
                try {
                    // Cek duplikat
                    if(Ruangan::where('nm_ruangan', $row['Nama Ruangan'])->exists()) {
                         $errors[] = "Ruangan {$row['Nama Ruangan']} sudah ada.";
                         continue;
                    }

                    // Buat User
                    $slug = Str::slug($row['Nama Ruangan']);
                    $email = $slug . '.' . Str::random(4) . '@ruangan.local';
                    
                    $user = User::create([
                        'name' => 'Kepala ' . $row['Nama Ruangan'],
                        'email' => $email,
                        'password' => Hash::make('12345678'),
                        'role' => 'ruangan',
                        'is_approved' => 1,
                    ]);

                    Ruangan::create([
                        'nm_ruangan' => $row['Nama Ruangan'],
                        'kuota_ruangan' => (int)$row['Kuota Ruangan'],
                        'user_id' => $user->id
                    ]);
                    $success++;
                } catch (\Exception $e) {
                    $errors[] = "Baris {$row['Nama Ruangan']}: " . $e->getMessage();
                }
            }
            return response()->json([
                'success' => true, 
                'message' => "Berhasil import $success data", 
                'errors' => $errors
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
}