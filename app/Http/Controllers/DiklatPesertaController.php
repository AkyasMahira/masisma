<?php

namespace App\Http\Controllers;

use App\Models\DiklatForm;
use App\Models\DiklatPeserta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DiklatPesertaController extends Controller
{
    // ==========================================================
    // BAGIAN PUBLIC (FORM PENDAFTARAN)
    // ==========================================================

    public function publicForm($public_link)
    {
        $form = DiklatForm::where('public_link', $public_link)->firstOrFail();
        return view('diklat.public_form', compact('form'));
    }

    public function publicSuccess($public_link)
    {
        $form = DiklatForm::where('public_link', $public_link)->firstOrFail();
        return view('diklat.public_success', compact('form'));
    }

    public function register(Request $request, $public_link)
    {
        $form = DiklatForm::where('public_link', $public_link)->firstOrFail();

        // 1. ATURAN VALIDASI (RULES)
        $rules = [
            // --- DATA GLOBAL (PENANGGUNG JAWAB) ---
            'instansi'         => 'required|string',
            'alamat'           => 'required|string',
            'email_kontak'     => 'required|email',
            'no_hp_kontak'     => 'required|numeric',
            'bukti_pembayaran' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',

            // --- DATA PESERTA (ARRAY LOOPING) ---
            'peserta'                       => 'required|array|min:1',
            'peserta.*.nama_lengkap'        => 'required|string',
            'peserta.*.nik'                 => 'required|numeric|digits:16',
            'peserta.*.email'               => 'required|email',
            'peserta.*.no_hp'               => 'required|numeric',
            'peserta.*.jabatan'             => 'required|string',
            'peserta.*.profesi'             => 'required|string',
            'peserta.*.pendidikan_terakhir' => 'required|string',
            'peserta.*.status_pegawai'      => 'required|string',
            'peserta.*.tempat_lahir'        => 'required|string',
            'peserta.*.tanggal_lahir'       => 'required|date',
            
            // UPDATE: Ukuran Kaos jadi Boleh Kosong (Nullable)
            'peserta.*.ukuran_kaos'         => 'nullable|string',
            
            // Opsi Checkbox (Array)
            'peserta.*.pilihan_pelatihan'   => 'required|array',
            'peserta.*.pilihan_tempat'      => 'required|array',
        ];

        // 2. PESAN ERROR CUSTOM
        $messages = [
            // --- Error Global ---
            'instansi.required'         => 'Nama Instansi (di bagian atas) wajib diisi.',
            'alamat.required'           => 'Alamat Instansi (di bagian atas) wajib diisi.',
            'email_kontak.required'     => 'Email Penanggung Jawab wajib diisi.',
            'email_kontak.email'        => 'Format Email Penanggung Jawab tidak valid.',
            'no_hp_kontak.required'     => 'No. HP Kontak Person wajib diisi.',
            'no_hp_kontak.numeric'      => 'No. HP Kontak Person harus berupa angka.',
            'bukti_pembayaran.required' => 'Bukti Pembayaran wajib diupload.',
            'bukti_pembayaran.mimes'    => 'Bukti Pembayaran harus berupa JPG, PNG, atau PDF.',
            'bukti_pembayaran.max'      => 'Ukuran Bukti Pembayaran maksimal 2MB.',
            'peserta.min'               => 'Minimal harus ada 1 peserta yang didaftarkan.',

            // --- Error Per Peserta ---
            'peserta.*.nama_lengkap.required'        => 'Nama Lengkap peserta wajib diisi.',
            'peserta.*.nik.required'                 => 'NIK peserta wajib diisi.',
            'peserta.*.nik.numeric'                  => 'NIK peserta harus berupa angka.',
            'peserta.*.nik.digits'                   => 'NIK peserta harus tepat 16 digit KTP.',
            'peserta.*.email.required'               => 'Email Plataran Sehat peserta wajib diisi.',
            'peserta.*.email.email'                  => 'Format Email peserta tidak valid.',
            'peserta.*.no_hp.required'               => 'Nomor WhatsApp peserta wajib diisi.',
            'peserta.*.no_hp.numeric'                => 'Nomor WhatsApp peserta harus berupa angka.',
            'peserta.*.jabatan.required'             => 'Jabatan peserta wajib diisi.',
            'peserta.*.profesi.required'             => 'Profesi peserta wajib diisi.',
            'peserta.*.pendidikan_terakhir.required' => 'Pendidikan Terakhir peserta wajib dipilih.',
            'peserta.*.status_pegawai.required'      => 'Status Pegawai peserta wajib dipilih.',
            'peserta.*.tempat_lahir.required'        => 'Tempat Lahir peserta wajib diisi.',
            'peserta.*.tanggal_lahir.required'       => 'Tanggal Lahir peserta wajib diisi.',
            
            // Pesan Error Ukuran Kaos dihapus karena sekarang optional
            
            'peserta.*.pilihan_pelatihan.required'   => 'Setiap peserta wajib memilih minimal satu Pelatihan.',
            'peserta.*.pilihan_tempat.required'      => 'Setiap peserta wajib memilih minimal satu Tempat.',
        ];

        // Jalankan Validasi
        $request->validate($rules, $messages);

        try {
            DB::beginTransaction();

            // 3. UPLOAD BUKTI BAYAR (GLOBAL)
            $buktiBayarPath = null;
            if ($request->hasFile('bukti_pembayaran')) {
                $buktiBayarPath = $request->file('bukti_pembayaran')->store('bukti_bayar', 'public');
            }

            // 4. LOOPING SIMPAN PESERTA
            foreach ($request->peserta as $index => $data) {
                
                $jawabanCustom = isset($data['jawaban_custom']) ? $data['jawaban_custom'] : [];

                DiklatPeserta::create([
                    'diklat_form_id'      => $form->id,
                    
                    // --- DATA GLOBAL ---
                    'instansi'            => $request->instansi, 
                    'alamat'              => $request->alamat,   
                    
                    // --- DATA PESERTA ---
                    'nama_lengkap'        => $data['nama_lengkap'],
                    'gelar'               => $data['gelar'] ?? null,
                    'nik'                 => $data['nik'],
                    'nip'                 => $data['nip'] ?? null,
                    'email'               => $data['email'],
                    'no_hp'               => $data['no_hp'],
                    'profesi'             => $data['profesi'],
                    'pendidikan_terakhir' => $data['pendidikan_terakhir'],
                    'status_pegawai'      => $data['status_pegawai'],
                    'jabatan'             => $data['jabatan'],
                    'pangkat_golongan'    => $data['pangkat_golongan'] ?? null,
                    'tempat_lahir'        => $data['tempat_lahir'],
                    'tanggal_lahir'       => $data['tanggal_lahir'],
                    
                    // Ukuran Kaos Optional (bisa null)
                    'ukuran_kaos'         => $data['ukuran_kaos'] ?? null,
                    
                    // File & Array
                    'bukti_pembayaran'    => $buktiBayarPath,
                    'pilihan_pelatihan'   => $data['pilihan_pelatihan'] ?? [],
                    'pilihan_tempat'      => $data['pilihan_tempat'] ?? [],
                    'jawaban_custom'      => $jawabanCustom,
                    
                    'pas_foto'            => null, 
                ]);
            }

            DB::commit();

            return redirect()->route('diklat.public.success', $form->public_link)
                             ->with('success', 'Pendaftaran berhasil dikirim! Silakan cek email Anda.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            if($buktiBayarPath && Storage::disk('public')->exists($buktiBayarPath)) {
                Storage::disk('public')->delete($buktiBayarPath);
            }

            return back()->withInput()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }
// public function register(Request $request, $public_link)
//     {
//         $form = DiklatForm::where('public_link', $public_link)->firstOrFail();

//         // 1. CEK DATA YANG DIKIRIM (DEBUGGING)
//         // Hapus tanda komentar di bawah ini jika ingin melihat isi data mentah
//         // dd($request->all()); 

//         // 2. DEFINISI RULES
//         $rules = [
//             'instansi'         => 'required|string',
//             'alamat'           => 'required|string',
//             'email_kontak'     => 'required|email',
//             'no_hp_kontak'     => 'required|numeric',
            
//             // Perhatikan: Peserta minimal 1
//             'peserta'                       => 'required|array|min:1',
//             'peserta.*.nama_lengkap'        => 'required|string',
//             'peserta.*.nik'                 => 'required|numeric|digits:16',
//             'peserta.*.email'               => 'required|email',
//             'peserta.*.no_hp'               => 'required|numeric',
//             'peserta.*.jabatan'             => 'required|string',
//             'peserta.*.profesi'             => 'required|string',
//             'peserta.*.pendidikan_terakhir' => 'required|string',
//             'peserta.*.status_pegawai'      => 'required|string',
//             'peserta.*.tempat_lahir'        => 'required|string',
//             'peserta.*.tanggal_lahir'       => 'required|date',
//             'peserta.*.ukuran_kaos'         => 'required|string',
            
//             // Checkbox Array
//             'peserta.*.pilihan_pelatihan'   => 'required|array',
//             'peserta.*.pilihan_tempat'      => 'required|array',
//         ];

//         // 3. JALANKAN VALIDATOR MANUAL (AGAR BISA DI-DUMP ERRORNYA)
//         $validator = \Validator::make($request->all(), $rules);

//         if ($validator->fails()) {
//             // === INI AKAN MENAMPILKAN ERROR DI LAYAR PUTIH ===
//             dd([
//                 'STATUS' => 'VALIDASI GAGAL',
//                 'DATA YANG DIKIRIM' => $request->all(),
//                 'ERRORNYA ADALAH' => $validator->errors()->all()
//             ]);
//             // =================================================
//         }

//         // JIKA LOLOS VALIDASI, LANJUT SIMPAN...
//         try {
//             DB::beginTransaction();

//             $buktiBayarPath = null;
//             if ($request->hasFile('bukti_pembayaran')) {
//                 $buktiBayarPath = $request->file('bukti_pembayaran')->store('bukti_bayar', 'public');
//             }

//             foreach ($request->peserta as $index => $data) {
//                 // Pastikan checkbox ada isinya, kalau tidak set array kosong
//                 $pilihanPelatihan = $data['pilihan_pelatihan'] ?? [];
//                 $pilihanTempat    = $data['pilihan_tempat'] ?? [];
//                 $jawabanCustom    = $data['jawaban_custom'] ?? [];

//                 DiklatPeserta::create([
//                     'diklat_form_id'      => $form->id,
//                     'instansi'            => $request->instansi, 
//                     'alamat'              => $request->alamat,   
//                     'nama_lengkap'        => $data['nama_lengkap'],
//                     'gelar'               => $data['gelar'] ?? null,
//                     'nik'                 => $data['nik'],
//                     'nip'                 => $data['nip'] ?? null,
//                     'email'               => $data['email'], 
//                     'no_hp'               => $data['no_hp'], 
//                     'profesi'             => $data['profesi'],
//                     'pendidikan_terakhir' => $data['pendidikan_terakhir'],
//                     'status_pegawai'      => $data['status_pegawai'],
//                     'jabatan'             => $data['jabatan'],
//                     'pangkat_golongan'    => $data['pangkat_golongan'] ?? null,
//                     'tempat_lahir'        => $data['tempat_lahir'],
//                     'tanggal_lahir'       => $data['tanggal_lahir'],
//                     'ukuran_kaos'         => $data['ukuran_kaos'],
//                     'bukti_pembayaran'    => $buktiBayarPath, 
//                     'pilihan_pelatihan'   => $pilihanPelatihan,
//                     'pilihan_tempat'      => $pilihanTempat,
//                     'jawaban_custom'      => $jawabanCustom,
//                     'pas_foto'            => null, // Pastikan di DB ini NULLABLE
//                 ]);
//             }

//             DB::commit();
//             return redirect()->route('diklat.public.success', $form->public_link);

//         } catch (\Exception $e) {
//             DB::rollBack();
//             // === TAMPILKAN ERROR SQL DI LAYAR ===
//             dd('ERROR DATABASE/SYSTEM:', $e->getMessage()); 
//         }
//     }
public function rekap(Request $request, $id)
    {
        $form = DiklatForm::findOrFail($id);

        // --- QUERY DASAR ---
        $query = DiklatPeserta::where('diklat_form_id', $id);

        // --- FILTER ---
        // 1. Ambil List Instansi (Unik)
        $listInstansi = DiklatPeserta::where('diklat_form_id', $id)
                        ->select('instansi')
                        ->distinct()
                        ->orderBy('instansi', 'asc')
                        ->pluck('instansi');

        // 2. Filter Instansi
        if ($request->filled('filter_instansi')) {
            $query->where('instansi', $request->filter_instansi);
        }

        // 3. Filter Pelatihan (JSON)
        if ($request->filled('filter_pelatihan')) {
            $query->whereJsonContains('pilihan_pelatihan', $request->filter_pelatihan);
        }

        // 4. Search Global
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('no_hp', 'like', "%{$search}%")
                  ->orWhere('instansi', 'like', "%{$search}%");
            });
        }

        // --- DATA UTAMA ---
        // A. Data Per Peserta (Paginate)
        $pesertas = $query->latest()->paginate(20)->withQueryString();

        // B. Data Per Instansi (Collection Grouping)
        // Kita clone query agar filternya tetap terbawa, tapi kita ambil semua (get) lalu di-grouping
        // Ini memungkinkan kita membawa data detail peserta ke dalam grup instansi
        $rekapInstansi = (clone $query)->orderBy('instansi')->get()->groupBy('instansi');

        return view('diklat.rekap', compact('form', 'pesertas', 'listInstansi', 'rekapInstansi'));
    }

    // Fungsi Hapus Peserta (Admin)
    public function destroyPeserta($id)
    {
        $peserta = DiklatPeserta::findOrFail($id);

        // Hapus file jika ada (jika kedepannya ada fitur upload file lagi)
        if ($peserta->pas_foto && Storage::disk('public')->exists($peserta->pas_foto)) {
            Storage::disk('public')->delete($peserta->pas_foto);
        }
        
        $peserta->delete();

        return back()->with('success', 'Data peserta berhasil dihapus!');
    }
}