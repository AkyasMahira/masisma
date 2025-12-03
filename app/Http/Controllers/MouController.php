<?php

namespace App\Http\Controllers;

use App\Models\Mou;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator; // Tambahkan ini
use Exception; // Tambahkan ini

class MouController extends Controller
{
    /**
     * Halaman LIST (Halaman Kedua)
     * KITA TAMBAHKAN LOGIKA FILTER DI SINI
     */
    public function index(Request $request) // Tambahkan Request $request
    {
        // Mulai query
        $query = Mou::query();

        // 1. Filter Nama Universitas
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('nama_instansi', 'like', '%' . $request->search . '%')
                  ->orWhere('nama_universitas', 'like', '%' . $request->search . '%');
            });
        }

        // 2. Filter Dari Tanggal
        if ($request->filled('tanggal_mulai')) {
            $query->where('tanggal_masuk', '>=', $request->tanggal_mulai);
        }

        // 3. Filter Sampai Tanggal
        if ($request->filled('tanggal_selesai')) {
            $query->where('tanggal_keluar', '<=', $request->tanggal_selesai);
        }

        // Ambil data, urutkan, dan paginasi
        // withQueryString() penting agar filter tetap aktif saat pindah halaman
        $mous = $query->latest()->paginate(10)->withQueryString();

        return view('mou.index', compact('mous'));
    }

    /**
     * Halaman CREATE (Halaman Pertama)
     */
    public function create()
    {
        return view('mou.create');
    }

    /**
     * Logika untuk MENYIMPAN data dari form CREATE
     */
    public function store(Request $request)
    {
        $request->validate([
            // Accept new field name `nama_instansi` and fallback to old `nama_universitas`
            'nama_instansi' => 'nullable|string|max:255',
            'nama_universitas' => 'nullable|string|max:255',
            'tanggal_masuk'    => 'required|date',
            'tanggal_keluar'   => 'required|date|after_or_equal:tanggal_masuk',
            'surat_permohonan' => 'required|file|mimes:pdf,doc,docx|max:5120',
            'sk_pengangkatan_pimpinan' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'sertifikat_akreditasi_prodi' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'draft_mou' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'keterangan'       => 'nullable|string',
            'alamat_instansi'  => 'nullable|string|max:1024',
            'rencana_kerja_sama'=> 'nullable|string',
            'nama_pic_instansi'=> 'nullable|string|max:255',
            'nomor_kontak_pic' => 'nullable|string|max:50',
            'jenis_instansi' => 'nullable|string|in:Instansi Pemerintah,Instansi Swasta,Instansi Internasional,Lainnya',
            'jenis_instansi_lainnya' => 'nullable|required_if:jenis_instansi,Lainnya|string|max:255',
        ]);

        // Simpan file-file baru
        $pathSuratPermohonan = $request->file('surat_permohonan')->store('mou_documents/surat_permohonan', 'public');
        $pathSK = $request->hasFile('sk_pengangkatan_pimpinan') ? $request->file('sk_pengangkatan_pimpinan')->store('mou_documents/sk_pengangkatan_pimpinan', 'public') : null;
        $pathSertifikat = $request->hasFile('sertifikat_akreditasi_prodi') ? $request->file('sertifikat_akreditasi_prodi')->store('mou_documents/sertifikat_akreditasi_prodi', 'public') : null;
        $pathDraft = $request->hasFile('draft_mou') ? $request->file('draft_mou')->store('mou_documents/draft_mou', 'public') : null;

        Mou::create([
            // Prefer `nama_instansi`, otherwise fallback to legacy field value
            'nama_instansi' => $request->input('nama_instansi') ?? $request->input('nama_universitas'),
            'nama_universitas' => $request->input('nama_universitas') ?? $request->input('nama_instansi'),
            'tanggal_masuk'    => $request->tanggal_masuk,
            'tanggal_keluar'   => $request->tanggal_keluar,
            'keterangan'       => $request->keterangan,
            'surat_permohonan' => $pathSuratPermohonan,
            'sk_pengangkatan_pimpinan' => $pathSK,
            'sertifikat_akreditasi_prodi' => $pathSertifikat,
            'draft_mou' => $pathDraft,
            'alamat_instansi' => $request->input('alamat_instansi'),
            'rencana_kerja_sama' => $request->input('rencana_kerja_sama'),
            'nama_pic_instansi' => $request->input('nama_pic_instansi'),
            'nomor_kontak_pic' => $request->input('nomor_kontak_pic'),
            'jenis_instansi' => $request->input('jenis_instansi'),
            'jenis_instansi_lainnya' => $request->input('jenis_instansi_lainnya'),
        ]);

        return redirect()->route('mou.index')
            ->with('success', 'Data MOU berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     * FUNGSI UNTUK TOMBOL EDIT
     */
    public function edit(Mou $mou) // Gunakan Route Model Binding
    {
        // Tampilkan view 'mou.edit' dan kirim data mou yang mau diedit
        return view('mou.edit', compact('mou'));
    }

    /**
     * Update the specified resource in storage.
     * FUNGSI UNTUK MENYIMPAN PERUBAHAN DARI EDIT
     */
    public function update(Request $request, Mou $mou) // Gunakan Route Model Binding
    {
        // 1. Validasi
        // 'nullable' berarti jika tidak ada file baru, file lama tetap dipakai
        $request->validate([
            'nama_instansi' => 'nullable|string|max:255',
            'nama_universitas' => 'nullable|string|max:255',
            'tanggal_masuk'    => 'required|date',
            'tanggal_keluar'   => 'required|date|after_or_equal:tanggal_masuk',
            'surat_permohonan' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'sk_pengangkatan_pimpinan' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'sertifikat_akreditasi_prodi' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'draft_mou' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'keterangan'       => 'nullable|string',
            'alamat_instansi'  => 'nullable|string|max:1024',
            'rencana_kerja_sama'=> 'nullable|string',
            'nama_pic_instansi'=> 'nullable|string|max:255',
            'nomor_kontak_pic' => 'nullable|string|max:50',
            'jenis_instansi' => 'nullable|string|in:Instansi Pemerintah,Instansi Swasta,Instansi Internasional,Lainnya',
            'jenis_instansi_lainnya' => 'nullable|required_if:jenis_instansi,Lainnya|string|max:255',
        ]);

        // 2. Ambil semua data input
        // Build data so we don't inadvertently overwrite legacy column with null.
        $data = [];
        if ($request->filled('nama_instansi')) $data['nama_instansi'] = $request->input('nama_instansi');
        if ($request->filled('nama_universitas')) $data['nama_universitas'] = $request->input('nama_universitas');
        if ($request->filled('tanggal_masuk')) $data['tanggal_masuk'] = $request->input('tanggal_masuk');
        if ($request->filled('tanggal_keluar')) $data['tanggal_keluar'] = $request->input('tanggal_keluar');
        if ($request->has('keterangan')) $data['keterangan'] = $request->input('keterangan');
        if ($request->filled('alamat_instansi')) $data['alamat_instansi'] = $request->input('alamat_instansi');
        if ($request->has('rencana_kerja_sama')) $data['rencana_kerja_sama'] = $request->input('rencana_kerja_sama');
        if ($request->filled('nama_pic_instansi')) $data['nama_pic_instansi'] = $request->input('nama_pic_instansi');
        if ($request->filled('nomor_kontak_pic')) $data['nomor_kontak_pic'] = $request->input('nomor_kontak_pic');
        if ($request->filled('jenis_instansi')) $data['jenis_instansi'] = $request->input('jenis_instansi');
        if ($request->filled('jenis_instansi_lainnya')) $data['jenis_instansi_lainnya'] = $request->input('jenis_instansi_lainnya');

        // 3. Cek jika ada file baru dan replace
        if ($request->hasFile('surat_permohonan')) {
            if ($mou->surat_permohonan && Storage::disk('public')->exists($mou->surat_permohonan)) {
                Storage::disk('public')->delete($mou->surat_permohonan);
            }
            $data['surat_permohonan'] = $request->file('surat_permohonan')->store('mou_documents/surat_permohonan', 'public');
        }
        if ($request->hasFile('sk_pengangkatan_pimpinan')) {
            if ($mou->sk_pengangkatan_pimpinan && Storage::disk('public')->exists($mou->sk_pengangkatan_pimpinan)) {
                Storage::disk('public')->delete($mou->sk_pengangkatan_pimpinan);
            }
            $data['sk_pengangkatan_pimpinan'] = $request->file('sk_pengangkatan_pimpinan')->store('mou_documents/sk_pengangkatan_pimpinan', 'public');
        }
        if ($request->hasFile('sertifikat_akreditasi_prodi')) {
            if ($mou->sertifikat_akreditasi_prodi && Storage::disk('public')->exists($mou->sertifikat_akreditasi_prodi)) {
                Storage::disk('public')->delete($mou->sertifikat_akreditasi_prodi);
            }
            $data['sertifikat_akreditasi_prodi'] = $request->file('sertifikat_akreditasi_prodi')->store('mou_documents/sertifikat_akreditasi_prodi', 'public');
        }
        if ($request->hasFile('draft_mou')) {
            if ($mou->draft_mou && Storage::disk('public')->exists($mou->draft_mou)) {
                Storage::disk('public')->delete($mou->draft_mou);
            }
            $data['draft_mou'] = $request->file('draft_mou')->store('mou_documents/draft_mou', 'public');
        }

        // 5. Update data di database
        $mou->update($data);

        return redirect()->route('mou.index')
            ->with('success', 'Data MOU berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     * FUNGSI UNTUK TOMBOL HAPUS
     */
    public function destroy(Mou $mou) // Gunakan Route Model Binding
    {
        try {
            // 1. Hapus file lama (jika ada)
            Storage::disk('public')->delete($mou->surat_permohonan);
            Storage::disk('public')->delete($mou->sk_pengangkatan_pimpinan);
            Storage::disk('public')->delete($mou->sertifikat_akreditasi_prodi);
            Storage::disk('public')->delete($mou->draft_mou);

            // 2. Hapus data dari database
            $mou->delete();

            return redirect()->route('mou.index')
                ->with('success', 'Data MOU berhasil dihapus.');
        } catch (Exception $e) {
            return redirect()->route('mou.index')
                ->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    /**
     * FUNGSI BARU UNTUK IMPORT EXCEL
     * Ini dipanggil oleh JavaScript 'importMOU()' di view index
     */
    public function importExcel(Request $request)
    {
        // Validasi request harus ada 'data'
        $validator = Validator::make($request->all(), [
            'data' => 'required|json'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Data JSON tidak valid.'], 400);
        }

        $data = json_decode($request->data, true);
        $successCount = 0;
        $errorCount = 0;
        $errors = [];

        foreach ($data as $index => $row) {
            try {
                // Sesuaikan nama kolom dengan template Excel Anda
                // Support both new and old headers
                $namaUniversitas = $row['Nama Instansi'] ?? $row['Nama Universitas'] ?? null;
                $tglMasuk = $row['Tanggal Masuk (YYYY-MM-DD)'] ?? null;
                $tglKeluar = $row['Tanggal Keluar (YYYY-MM-DD)'] ?? null;

                // Validasi data sederhana
                if (empty($namaUniversitas) || empty($tglMasuk) || empty($tglKeluar)) {
                    throw new Exception("Data tidak lengkap di baris " . ($index + 2));
                }

                // Karena tidak ada upload file via Excel, set file uploads ke null
                    Mou::create([
                    'nama_instansi' => $namaUniversitas,
                    'nama_universitas' => $row['Nama Universitas'] ?? $namaUniversitas,
                    'tanggal_masuk'    => $tglMasuk,
                    'tanggal_keluar'   => $tglKeluar,
                    'keterangan'       => $row['Keterangan'] ?? null,
                    'surat_permohonan' => null,
                    'sk_pengangkatan_pimpinan' => null,
                    'sertifikat_akreditasi_prodi' => null,
                    'draft_mou' => null,
                ]);

                $successCount++;
            } catch (Exception $e) {
                $errorCount++;
                $errors[] = $e->getMessage();
            }
        }

        if ($errorCount > 0) {
            return response()->json([
                'success' => false,
                'message' => "Selesai: $successCount berhasil, $errorCount gagal. Error: " . implode(', ', $errors)
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => "Berhasil mengimpor $successCount data MOU."
        ]);
    }

    /**
     * PUBLIC: Search / Index for MOU (no sidebar)
     */
    public function publicIndex(Request $request)
    {
        $query = Mou::query();
        if ($request->filled('search')) {
            $query->where('nama_instansi', 'like', '%' . $request->search . '%')
                  ->orWhere('nama_universitas', 'like', '%' . $request->search . '%');
        }

        $mous = $query->orderBy('tanggal_masuk', 'desc')->paginate(10)->withQueryString();
        $searchPerformed = $request->has('search');
        return view('mou.public_index', compact('mous', 'searchPerformed'));
    }

    /**
     * PUBLIC: Show create form for MOU
     */
    public function publicCreate()
    {
        return view('mou.public_create');
    }

    /**
     * PUBLIC: Store new MOU from public form
     */
    public function publicStore(Request $request)
    {
        // Reuse admin store validation but redirect to public route
        $request->validate([
            'nama_instansi' => 'nullable|string|max:255',
            'nama_universitas' => 'nullable|string|max:255',
            'tanggal_masuk'    => 'required|date',
            'tanggal_keluar'   => 'required|date|after_or_equal:tanggal_masuk',
            'surat_permohonan' => 'required|file|mimes:pdf,doc,docx|max:5120',
            'sk_pengangkatan_pimpinan' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'sertifikat_akreditasi_prodi' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'draft_mou' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'keterangan'       => 'nullable|string',
            'alamat_instansi'  => 'nullable|string|max:1024',
            'rencana_kerja_sama'=> 'nullable|string',
            'nama_pic_instansi'=> 'nullable|string|max:255',
            'nomor_kontak_pic' => 'nullable|string|max:50',
            'jenis_instansi' => 'nullable|string|in:Instansi Pemerintah,Instansi Swasta,Instansi Internasional,Lainnya',
            'jenis_instansi_lainnya' => 'nullable|required_if:jenis_instansi,Lainnya|string|max:255',
        ]);

        $pathSuratPermohonan = $request->file('surat_permohonan')->store('mou_documents/surat_permohonan', 'public');
        $pathSK = $request->hasFile('sk_pengangkatan_pimpinan') ? $request->file('sk_pengangkatan_pimpinan')->store('mou_documents/sk_pengangkatan_pimpinan', 'public') : null;
        $pathSertifikat = $request->hasFile('sertifikat_akreditasi_prodi') ? $request->file('sertifikat_akreditasi_prodi')->store('mou_documents/sertifikat_akreditasi_prodi', 'public') : null;
        $pathDraft = $request->hasFile('draft_mou') ? $request->file('draft_mou')->store('mou_documents/draft_mou', 'public') : null;

        Mou::create([
            'nama_instansi' => $request->input('nama_instansi') ?? $request->input('nama_universitas'),
            'nama_universitas' => $request->input('nama_universitas') ?? $request->input('nama_instansi'),
            'tanggal_masuk'    => $request->tanggal_masuk,
            'tanggal_keluar'   => $request->tanggal_keluar,
            'keterangan'       => $request->keterangan,
            'surat_permohonan' => $pathSuratPermohonan,
            'sk_pengangkatan_pimpinan' => $pathSK,
            'sertifikat_akreditasi_prodi' => $pathSertifikat,
            'draft_mou' => $pathDraft,
            'alamat_instansi' => $request->input('alamat_instansi'),
            'rencana_kerja_sama' => $request->input('rencana_kerja_sama'),
            'nama_pic_instansi' => $request->input('nama_pic_instansi'),
            'nomor_kontak_pic' => $request->input('nomor_kontak_pic'),
            'jenis_instansi' => $request->input('jenis_instansi'),
            'jenis_instansi_lainnya' => $request->input('jenis_instansi_lainnya'),
        ]);

        return redirect()->route('public.mou.create')
            ->with('success', 'Berhasil mengajukan MOU. Terima kasih!');
    }

    /**
     * PUBLIC: Edit page (no auth) for MOU
     */
    public function publicEdit(Mou $mou)
    {
        return view('mou.public_edit', compact('mou'));
    }

    /**
     * PUBLIC: Update MOU from public page
     */
    public function publicUpdate(Request $request, Mou $mou)
    {
        // Reuse same validation as admin update
        $request->validate([
            'nama_instansi' => 'nullable|string|max:255',
            'nama_universitas' => 'nullable|string|max:255',
            'tanggal_masuk'    => 'required|date',
            'tanggal_keluar'   => 'required|date|after_or_equal:tanggal_masuk',
            'surat_permohonan' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'sk_pengangkatan_pimpinan' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'sertifikat_akreditasi_prodi' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'draft_mou' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'keterangan'       => 'nullable|string',
            'alamat_instansi'  => 'nullable|string|max:1024',
            'rencana_kerja_sama'=> 'nullable|string',
            'nama_pic_instansi'=> 'nullable|string|max:255',
            'nomor_kontak_pic' => 'nullable|string|max:50',
            'jenis_instansi' => 'nullable|string|in:Instansi Pemerintah,Instansi Swasta,Instansi Internasional,Lainnya',
            'jenis_instansi_lainnya' => 'nullable|required_if:jenis_instansi,Lainnya|string|max:255',
        ]);

        $data = [];
        if ($request->filled('nama_instansi')) $data['nama_instansi'] = $request->input('nama_instansi');
        if ($request->filled('nama_universitas')) $data['nama_universitas'] = $request->input('nama_universitas');
        if ($request->filled('tanggal_masuk')) $data['tanggal_masuk'] = $request->input('tanggal_masuk');
        if ($request->filled('tanggal_keluar')) $data['tanggal_keluar'] = $request->input('tanggal_keluar');
        if ($request->has('keterangan')) $data['keterangan'] = $request->input('keterangan');
        if ($request->filled('alamat_instansi')) $data['alamat_instansi'] = $request->input('alamat_instansi');
        if ($request->has('rencana_kerja_sama')) $data['rencana_kerja_sama'] = $request->input('rencana_kerja_sama');
        if ($request->filled('nama_pic_instansi')) $data['nama_pic_instansi'] = $request->input('nama_pic_instansi');
        if ($request->filled('nomor_kontak_pic')) $data['nomor_kontak_pic'] = $request->input('nomor_kontak_pic');
        if ($request->filled('jenis_instansi')) $data['jenis_instansi'] = $request->input('jenis_instansi');
        if ($request->filled('jenis_instansi_lainnya')) $data['jenis_instansi_lainnya'] = $request->input('jenis_instansi_lainnya');

        if ($request->hasFile('surat_permohonan')) {
            if ($mou->surat_permohonan && Storage::disk('public')->exists($mou->surat_permohonan)) {
                Storage::disk('public')->delete($mou->surat_permohonan);
            }
            $data['surat_permohonan'] = $request->file('surat_permohonan')->store('mou_documents/surat_permohonan', 'public');
        }
        if ($request->hasFile('sk_pengangkatan_pimpinan')) {
            if ($mou->sk_pengangkatan_pimpinan && Storage::disk('public')->exists($mou->sk_pengangkatan_pimpinan)) {
                Storage::disk('public')->delete($mou->sk_pengangkatan_pimpinan);
            }
            $data['sk_pengangkatan_pimpinan'] = $request->file('sk_pengangkatan_pimpinan')->store('mou_documents/sk_pengangkatan_pimpinan', 'public');
        }
        if ($request->hasFile('sertifikat_akreditasi_prodi')) {
            if ($mou->sertifikat_akreditasi_prodi && Storage::disk('public')->exists($mou->sertifikat_akreditasi_prodi)) {
                Storage::disk('public')->delete($mou->sertifikat_akreditasi_prodi);
            }
            $data['sertifikat_akreditasi_prodi'] = $request->file('sertifikat_akreditasi_prodi')->store('mou_documents/sertifikat_akreditasi_prodi', 'public');
        }
        if ($request->hasFile('draft_mou')) {
            if ($mou->draft_mou && Storage::disk('public')->exists($mou->draft_mou)) {
                Storage::disk('public')->delete($mou->draft_mou);
            }
            $data['draft_mou'] = $request->file('draft_mou')->store('mou_documents/draft_mou', 'public');
        }

        $mou->update($data);

        return redirect()->route('public.mou.index')
            ->with('success', 'Data MOU berhasil diperbarui.');
    }
}
