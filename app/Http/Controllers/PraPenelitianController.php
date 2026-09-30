<?php

namespace App\Http\Controllers;

/**
 * Mendefinisikan model dan library yang dibutuhkan.
 * Pastikan model Mou, PraPenelitian, dan PraPenelitianAnggota sudah ada.
 */
use App\Models\Mou;
use App\Models\PraPenelitian;
use App\Models\PraPenelitianAnggota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PraPenelitianController extends Controller
{
    /**
     * Constructor: Memastikan hanya user yang login yang bisa akses.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

public function index(Request $request)
{
    // Gunakan withCount supaya $item->anggotas_count bisa jalan di blade
    $query = PraPenelitian::with(['mou', 'anggotas'])->withCount('anggotas');

    if (auth()->user()->role !== 'admin') {
        $query->where('user_id', auth()->id());
    }

    // --- SEARCH FILTER (Judul, Univ, Nama Anggota) ---
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('judul', 'like', "%{$search}%")
              ->orWhereHas('mou', function($subQ) use ($search) {
                  $subQ->where('nama_instansi', 'like', "%{$search}%")
                       ->orWhere('nama_universitas', 'like', "%{$search}%");
              })
              ->orWhereHas('anggotas', function($subQ) use ($search) {
                  $subQ->where('nama', 'like', "%{$search}%");
              });
        });
    }

    // --- FILTER STATUS ---
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // --- FILTER JENIS PENELITIAN ---
    if ($request->filled('jenis_penelitian')) {
        $query->where('jenis_penelitian', $request->jenis_penelitian);
    }

    // --- FILTER KATEGORI MAHASISWA (Pastikan nama kolom sesuai DB) ---
    if ($request->filled('jenis_mahasiswa')) {
        $query->where('jenis_mahasiswa', $request->jenis_mahasiswa);
    }

    $penelitian = $query->latest()->paginate(10);
    
    return view('pra-penelitian.index', compact('penelitian'));
}
    /**
     * SHOW: Menampilkan detail satu data penelitian.
     */
    public function show($id)
    {
        $praPenelitian = PraPenelitian::with(['anggotas', 'mou'])->findOrFail($id);

        // Security: Mencegah user mengintip data orang lain via URL ID
        if (auth()->user()->role !== 'admin' && $praPenelitian->user_id !== auth()->id()) {
            abort(403, 'Waduh! Kamu nggak punya izin buat liat data ini.');
        }

        return view('pra-penelitian.show', compact('praPenelitian'));
    }

    /**
     * CREATE: Menampilkan form tambah data.
     */
    public function create()
    {
        // Hanya mengambil MoU yang belum kadaluarsa
        $mous = Mou::where('tanggal_keluar', '>=', now()->toDateString())
            ->orderBy('nama_instansi')
            ->get();

        return view('pra-penelitian.create', compact('mous'));
    }

    /**
     * STORE: Proses penyimpanan data baru ke database.
     */
    public function store(Request $request)
    {
        // Cek apakah user baru saja menyelesaikan pengajuan (Jeda 24 Jam)
        // $recentFinished = PraPenelitian::where('user_id', auth()->id())
        //     ->where('status', 'Selesai')
        //     ->where('updated_at', '>=', now()->subDay())
        //     ->first();

        // if ($recentFinished) {
        //     return back()->withInput()->with('error', 'Selesaikan dulu masa tunggu 1x24 jam sebelum mengajukan kembali ya.');
        // }

        /**
         * VALIDASI DATA & FALLBACK MANUSIAWI
         * Bagian ini yang bakal kasih tahu error spesifik ke user.
         */
        $rules = [
            'judul' => 'required|string|max:255',
            'mou_id' => 'required|exists:mous,id',
            'jenis_penelitian' => 'required|in:Data Awal,Uji Validitas,Penelitian',
            'prodi' => 'required|string', // SINKRONKAN: Di Blade harus name="prodi"
            'tanggal_mulai' => 'required|date',
            'tanggal_rencana_skripsi' => 'required|date',
            'kerangka_penelitian' => 'required|mimes:pdf|max:2048',
            'surat_pengantar' => 'required|mimes:pdf|max:2048',
            'proposal' => 'required|mimes:pdf|max:2048',
            'ethical_clearance' => $request->jenis_penelitian === 'Penelitian' ? 'required|mimes:pdf|max:2048' : 'nullable',
            'dosen1_nama' => 'required|string',
            'dosen1_hp' => 'required|string',
            'dosen2_nama' => 'required|string',
            'dosen2_hp' => 'required|string',
            'mahasiswas' => 'required|array|min:1',
            'mahasiswas.*.nama' => 'required|string',
            'mahasiswas.*.no_telpon' => 'required|string',
            'mahasiswas.*.jenjang' => 'required|string',
        ];

        $messages = [
            'required' => 'Waduh! Kolom :attribute ini wajib diisi, jangan kosong ya.',
            'mimes'    => 'File :attribute mustahil dibaca kalau bukan PDF.',
            'max'      => 'File :attribute kegedean! Maksimal cuma boleh 2MB.',
            'prodi.required' => 'Kamu belum milih Program Studi lho.',
            'ethical_clearance.required' => 'Karena ini jenis Penelitian, Ethical Clearance hukumnya wajib diupload.',
        ];

        $request->validate($rules, $messages);

        // Mulai Transaksi Database (Biar kalau ada yang gagal, data nggak berantakan)
        DB::beginTransaction();

        try {
            // Proses Upload File Menggunakan Helper di bawah
            $pathKerangka = $this->uploadFile($request, 'kerangka_penelitian', 'uploads/pra_penelitian/kerangka');
            $pathSurat    = $this->uploadFile($request, 'surat_pengantar', 'uploads/pra_penelitian/surat');
            $pathProposal = $this->uploadFile($request, 'proposal', 'uploads/pra_penelitian/proposal');
            $pathEthical  = $this->uploadFile($request, 'ethical_clearance', 'uploads/pra_penelitian/ethical');

            // Simpan Data Utama
            $penelitian = PraPenelitian::create([
                'user_id' => Auth::id(),
                'judul' => $request->judul,
                'mou_id' => $request->mou_id,
                'jenis_penelitian' => $request->jenis_penelitian,
                'prodi' => $request->prodi,
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_rencana_skripsi' => $request->tanggal_rencana_skripsi,
                'file_kerangka' => $pathKerangka,
                'file_surat_pengantar' => $pathSurat,
                'file_proposal' => $pathProposal,
                'file_ethical_clearance' => $pathEthical,
                'dosen1_nama' => $request->dosen1_nama,
                'dosen1_hp' => $request->dosen1_hp,
                'dosen2_nama' => $request->dosen2_nama,
                'dosen2_hp' => $request->dosen2_hp,
                'status' => 'Pending',
            ]);

            // Simpan Data Anggota Mahasiswa (Relasi HasMany)
            foreach ($request->mahasiswas as $mhs) {
                $penelitian->anggotas()->create($mhs);
            }

            DB::commit();

            return redirect()
                ->route(auth()->user()->role === 'admin' ? 'pra-penelitian.index' : 'dashboard')
                ->with('success', 'Mantap! Pengajuan penelitian kamu berhasil dikirim.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error Simpan PraPenelitian: ' . $e->getMessage());

            // Cleanup: Hapus file yang terlanjur diupload kalau DB gagal simpan
            foreach ([$pathKerangka, $pathSurat, $pathProposal, $pathEthical] as $file) {
                if ($file) $this->deleteFile($file);
            }

            return back()->withInput()->with('error', 'Ada gangguan teknis: ' . $e->getMessage());
        }
    }

    /**
     * EDIT: Menampilkan form edit data.
     */
    public function edit($id)
    {
        $praPenelitian = PraPenelitian::with(['anggotas', 'mou'])->findOrFail($id);

        if (auth()->user()->role !== 'admin' && $praPenelitian->user_id !== auth()->id()) {
            abort(403);
        }

        $mous = Mou::where('tanggal_keluar', '>=', now()->toDateString())
            ->orderBy('nama_instansi')
            ->get();

        return view('pra-penelitian.edit', compact('praPenelitian', 'mous'));
    }

    /**
     * UPDATE: Memperbarui data yang sudah ada.
     */
    public function update(Request $request, $id)
    {
        $penelitian = PraPenelitian::findOrFail($id);

        if (auth()->user()->role !== 'admin' && $penelitian->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'judul' => 'required|string|max:255',
            'mou_id' => 'required|exists:mous,id',
            'jenis_penelitian' => 'required',
            'tanggal_mulai' => 'required|date',
            'kerangka_penelitian' => 'nullable|mimes:pdf|max:2048',
            'surat_pengantar' => 'nullable|mimes:pdf|max:2048',
            'proposal' => 'nullable|mimes:pdf|max:2048',
            'ethical_clearance' => 'nullable|mimes:pdf|max:2048',
            'mahasiswas' => 'required|array|min:1',
        ], ['required' => 'Kolom :attribute jangan sampai terlewat ya.']);

        DB::beginTransaction();

        try {
            $data = $request->except([
                'mahasiswas', 'kerangka_penelitian', 'surat_pengantar', 'proposal', 'ethical_clearance',
            ]);

            // Handle Update File (Hapus file lama kalau ganti file baru)
            $fileFields = [
                'kerangka_penelitian' => 'file_kerangka',
                'surat_pengantar'     => 'file_surat_pengantar',
                'proposal'            => 'file_proposal',
                'ethical_clearance'   => 'file_ethical_clearance',
            ];

            foreach ($fileFields as $input => $column) {
                if ($request->hasFile($input)) {
                    $this->deleteFile($penelitian->$column); // Hapus yang lama
                    $data[$column] = $this->uploadFile($request, $input, 'uploads/pra_penelitian/' . str_replace('file_', '', $column));
                }
            }

            $penelitian->update($data);

            // Sync Anggota: Hapus semua lama, masukkan yang baru
            $penelitian->anggotas()->delete();
            foreach ($request->mahasiswas as $mhs) {
                $penelitian->anggotas()->create($mhs);
            }

            DB::commit();
            return redirect()->route('pra-penelitian.index')->with('success', 'Data kamu sudah berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal update data: ' . $e->getMessage());
        }
    }

    /**
     * DESTROY: Menghapus data beserta file fisiknya.
     */
    public function destroy($id)
    {
        $penelitian = PraPenelitian::findOrFail($id);

        // Hapus file dari storage biar nggak nyampah
        $files = ['file_kerangka', 'file_surat_pengantar', 'file_proposal', 'file_ethical_clearance'];
        foreach ($files as $file) {
            $this->deleteFile($penelitian->$file);
        }

        $penelitian->delete();
        return back()->with('success', 'Data pengajuan sudah berhasil dihapus selamanya.');
    }

    /**
     * APPROVE: Mengubah status menjadi Approved (Khusus Admin).
     */
    public function approveForm($id)
    {
        $penelitian = PraPenelitian::findOrFail($id);
        $penelitian->update(['status' => 'Approved']);
        
        return back()->with('success', 'Formulir Pra Penelitian berhasil disetujui.');
    }

    /**
     * UPDATE JENIS MAHASISWA: Khusus admin untuk melabeli Internal/Eksternal.
     */
    public function updateJenisMahasiswa(Request $request, $id)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Akses ilegal! Cuma Admin yang boleh ganti jenis mahasiswa.');
        }

        $request->validate([
            'jenis_mahasiswa' => 'required|in:Internal,Eksternal',
        ], ['in' => 'Pilihannya cuma Internal atau Eksternal ya.']);

        $penelitian = PraPenelitian::findOrFail($id);
        $penelitian->update([
            'jenis_mahasiswa' => $request->jenis_mahasiswa
        ]);

        return back()->with('success', 'Status Mahasiswa sekarang menjadi: ' . $request->jenis_mahasiswa);
    }

    /**
     * PRIVATE HELPER: Menangani upload file dan pembuatan folder otomatis.
     */
    private function uploadFile($request, $inputName, $targetDir)
    {
        if ($request->hasFile($inputName)) {
            $file = $request->file($inputName);
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            
            // Cek folder, buat otomatis jika belum ada di public path
            $fullPath = public_path($targetDir);
            if (!File::isDirectory($fullPath)) {
                File::makeDirectory($fullPath, 0755, true, true);
            }

            $file->move($fullPath, $filename);
            return $targetDir . '/' . $filename;
        }
        return null;
    }

    /**
     * PRIVATE HELPER: Menangani penghapusan file fisik.
     */
    private function deleteFile($path)
    {
        if ($path && File::exists(public_path($path))) {
            File::delete(public_path($path));
        }
    }
}