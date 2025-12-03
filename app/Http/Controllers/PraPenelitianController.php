<?php

namespace App\Http\Controllers;

use App\Models\Mou;
use App\Models\PraPenelitian;
// Pastikan model ini ada (dari langkah sebelumnya)
use App\Models\PraPenelitianAnggota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;

class PraPenelitianController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $query = PraPenelitian::with(['mou'])
            ->withCount('anggotas'); // ← ini yang absolut wajib ditambah

        if (auth()->user()->role !== 'admin') {
            $query->where('user_id', auth()->id());
        }

        $penelitian = $query->latest()->paginate(10);

        return view('pra-penelitian.index', compact('penelitian'));
    }

    public function create()
    {
        // Ambil MOU yang masih berlaku
        $mous = Mou::where('tanggal_keluar', '>=', now()->toDateString())
            ->orderBy('nama_instansi', 'asc')
            ->get();

        return view('pra-penelitian.create', compact('mous'));
    }

    public function store(Request $request)
    {
        $request->validate([
            // Data Utama
            'judul' => 'required|string|max:255',
            'mou_id' => 'required|exists:mous,id',
            'jenis_penelitian' => 'required|in:Data Awal,Uji Validitas,Penelitian',
            'prodi' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_rencana_skripsi' => 'required|date',

            // File Upload
            'kerangka_penelitian' => 'required|mimes:pdf|max:2048',
            'surat_pengantar' => 'required|mimes:pdf|max:2048',

            // Dosen
            'dosen1_nama' => 'required|string',
            'dosen1_hp' => 'required|string',
            'dosen2_nama' => 'required|string',
            'dosen2_hp' => 'required|string',

            // Data Mahasiswa
            'mahasiswas' => 'required|array|min:1',
            'mahasiswas.*.nama' => 'required|string',
            'mahasiswas.*.no_telpon' => 'required|string',
            'mahasiswas.*.jenjang' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            // 1. Upload File
            $pathKerangka = $this->uploadFile($request, 'kerangka_penelitian', 'uploads/pra_penelitian/kerangka');
            $pathSurat = $this->uploadFile($request, 'surat_pengantar', 'uploads/pra_penelitian/surat');

            // 2. Simpan Data Utama (Parent)
            $penelitian = PraPenelitian::create([
                'user_id' => Auth::id(), // ID User yang login
                'judul' => $request->judul,
                'mou_id' => $request->mou_id,
                'jenis_penelitian' => $request->jenis_penelitian,
                'prodi' => $request->prodi,
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_rencana_skripsi' => $request->tanggal_rencana_skripsi,

                // Path File
                'file_kerangka' => $pathKerangka,
                'file_surat_pengantar' => $pathSurat,

                // Dosen
                'dosen1_nama' => $request->dosen1_nama,
                'dosen1_hp' => $request->dosen1_hp,
                'dosen2_nama' => $request->dosen2_nama,
                'dosen2_hp' => $request->dosen2_hp,

                'status' => 'Pending',
            ]);

            // 3. Simpan Data Anggota (Children)
            foreach ($request->mahasiswas as $mhs) {
                // Menggunakan relation 'anggotas' yang didefinisikan di Model PraPenelitian
                $penelitian->anggotas()->create([
                    'nama' => $mhs['nama'],
                    'no_telpon' => $mhs['no_telpon'],
                    'jenjang' => $mhs['jenjang'],
                ]);
            }

            DB::commit();

            // Redirect sesuai role
            if (auth()->user()->role === 'admin') {
                return redirect()->route('pra-penelitian.index')->with('success', 'Pengajuan berhasil dibuat.');
            } else {
                return redirect()->route('dashboard')->with('success', 'Pengajuan berhasil dikirim. Mohon tunggu konfirmasi.');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            // Hapus file jika database gagal (cleanup)
            if (isset($pathKerangka)) File::delete(public_path($pathKerangka));
            if (isset($pathSurat)) File::delete(public_path($pathSurat));

            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $praPenelitian = PraPenelitian::with(['mou', 'anggotas', 'user'])->findOrFail($id);

        // Proteksi: User biasa hanya boleh lihat punya sendiri
        if (auth()->user()->role !== 'admin' && $praPenelitian->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        return view('pra-penelitian.show', compact('praPenelitian'));
    }

    public function edit($id)
    {
        $praPenelitian = PraPenelitian::with('anggotas')->findOrFail($id);

        if (auth()->user()->role !== 'admin' && $praPenelitian->user_id !== auth()->id()) {
            abort(403);
        }

        $mous = Mou::where('tanggal_keluar', '>=', now()->toDateString())->get();

        return view('pra-penelitian.edit', compact('praPenelitian', 'mous'));
    }

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
            // File bersifat nullable saat update
            'kerangka_penelitian' => 'nullable|mimes:pdf|max:2048',
            'surat_pengantar' => 'nullable|mimes:pdf|max:2048',
            'mahasiswas' => 'required|array|min:1',
        ]);

        try {
            DB::beginTransaction();

            // 1. Update Data Utama
            $dataToUpdate = $request->except(['mahasiswas', 'kerangka_penelitian', 'surat_pengantar']);

            // Handle File Upload (Hanya jika ada file baru)
            if ($request->hasFile('kerangka_penelitian')) {
                $this->deleteFile($penelitian->file_kerangka);
                $dataToUpdate['file_kerangka'] = $this->uploadFile($request, 'kerangka_penelitian', 'uploads/pra_penelitian/kerangka');
            }
            if ($request->hasFile('surat_pengantar')) {
                $this->deleteFile($penelitian->file_surat_pengantar);
                $dataToUpdate['file_surat_pengantar'] = $this->uploadFile($request, 'surat_pengantar', 'uploads/pra_penelitian/surat');
            }

            $penelitian->update($dataToUpdate);

            // 2. Sync Anggota (Hapus lama, buat baru - cara termudah untuk nested form)
            $penelitian->anggotas()->delete();

            foreach ($request->mahasiswas as $mhs) {
                $penelitian->anggotas()->create([
                    'nama' => $mhs['nama'],
                    'no_telpon' => $mhs['no_telpon'],
                    'jenjang' => $mhs['jenjang'],
                ]);
            }

            DB::commit();
            return redirect()->route('pra-penelitian.index')->with('success', 'Data berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $penelitian = PraPenelitian::findOrFail($id);

        if (auth()->user()->role !== 'admin' && $penelitian->user_id !== auth()->id()) {
            abort(403);
        }

        try {
            // Hapus File Fisik
            $this->deleteFile($penelitian->file_kerangka);
            $this->deleteFile($penelitian->file_surat_pengantar);

            // Hapus Record (Anggota akan terhapus otomatis karena onCascade di migration)
            $penelitian->delete();

            return redirect()->route('pra-penelitian.index')->with('success', 'Data berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus data.');
        }
    }

    public function batal($id)
    {
        $penelitian = PraPenelitian::findOrFail($id);
        // Hanya admin atau pemilik yang bisa membatalkan
        if (auth()->user()->role !== 'admin' && $penelitian->user_id !== auth()->id()) {
            abort(403);
        }

        $penelitian->update(['status' => 'Rejected']); // Atau status khusus 'Batal'
        return back()->with('success', 'Status pengajuan diubah menjadi Batal/Ditolak.');
    }

    /**
     * Helper: Upload File
     */
    private function uploadFile($request, $inputName, $targetDir)
    {
        if ($request->hasFile($inputName)) {
            $file = $request->file($inputName);
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path($targetDir), $filename);
            return $targetDir . '/' . $filename;
        }
        return null;
    }

    /**
     * Approve form pra penelitian (approve surat pengantar)
     */
    public function approveForm(PraPenelitian $praPenelitian)
    {
        $praPenelitian->update(['status' => 'Approved']);
        return back()->with('success', 'Form pra penelitian berhasil di-approve.');
    }

    /**
     * Reject form pra penelitian
     */
    public function rejectForm(PraPenelitian $praPenelitian)
    {
        $praPenelitian->update(['status' => 'Rejected']);
        return back()->with('success', 'Form pra penelitian ditolak.');
    }

    /**
     * Helper: Delete File
     */
    private function deleteFile($path)
    {
        if ($path && File::exists(public_path($path))) {
            File::delete(public_path($path));
        }
    }
}
