<?php

namespace App\Http\Controllers;
use App\Models\Pelatihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class PelatihanController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->check() || auth()->user()->role !== 'admin') {
                abort(403, 'Akses ditolak.');
            }
            return $next($request);
        })->except(['publicIndex', 'publicEdit', 'publicUpdate']);
        // Pastikan 'publicIndex' juga masuk except jika itu nama methodnya sekarang
    }

    /**
     * Display a listing of pelatihan
     */
    public function index(Request $request)
    {
        $query = Pelatihan::query();

        if ($request->filled('search')) {
            $query->where('nama', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('jabatan')) {
            $query->where('jabatan', 'like', '%' . $request->jabatan . '%');
        }
        if ($request->filled('unit')) {
            $query->where('unit', 'like', '%' . $request->unit . '%');
        }
        if ($request->filled('bidang')) {
            $query->where('bidang', 'like', '%' . $request->bidang . '%');
        }

        $pelatihans = $query->orderBy('nama', 'asc')->paginate(10);
        return view('pelatihan.index', compact('pelatihans'));
    }

    /**
     * Show the form for creating a new pelatihan
     */
    public function create()
    {
        return view('pelatihan.create');
    }

    /**
     * Store a newly created pelatihan in storage
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255|unique:pelatihans,nama',
            'bidang' => 'required|string|max:255',
            'jabatan' => 'nullable|string|max:255',
            'unit' => 'nullable|string|max:255',

            'status_pegawai' => 'required|in:ASN,KARYAWAN TETAP,NON ASN',

            // Validasi Kondisional ASN / KARYAWAN TETAP
            'nip' => 'nullable|required_if:status_pegawai,ASN,KARYAWAN TETAP|string|max:50',
            'golongan' => 'nullable|required_if:status_pegawai,ASN,KARYAWAN TETAP|string|max:100',

            // --- Pangkat hanya required jika ASN ---
            'pangkat' => 'nullable|required_if:status_pegawai,ASN|string|max:100',

            // Validasi Kondisional NON ASN
            'nirp' => 'nullable|required_if:status_pegawai,NON ASN|string|max:50',

            // --- PELATIHAN DASAR ---
            'pelatihan_dasar' => 'nullable|array',
            'pelatihan_dasar.*' => 'nullable|string',
            'pelatihan_tahun_dasar' => 'nullable|array',
            'pelatihan_tahun_dasar.*' => 'nullable|string',
            'pelatihan_file_dasar' => 'nullable|array',
            'pelatihan_file_dasar.*' => 'nullable|file|mimes:pdf|max:2048',

            // --- PELATIHAN PENINGKATAN KOMPETENSI ---
            'pelatihan_kompetensi' => 'nullable|array',
            'pelatihan_kompetensi.*' => 'nullable|string',
            'pelatihan_tahun_kompetensi' => 'nullable|array',
            'pelatihan_tahun_kompetensi.*' => 'nullable|string',
            'pelatihan_file_kompetensi' => 'nullable|array',
            'pelatihan_file_kompetensi.*' => 'nullable|file|mimes:pdf|max:2048',
        ]);

        // 1. PROSES PELATIHAN DASAR
        $daftarPelatihanDasar = [];
        if ($request->has('pelatihan_dasar')) {
            $namaPelatihan = $request->input('pelatihan_dasar');
            $tahunPelatihan = $request->input('pelatihan_tahun_dasar');
            $filePelatihan = $request->file('pelatihan_file_dasar');

            foreach ($namaPelatihan as $index => $nama) {
                if (!empty($nama)) {
                    $filePath = null;
                    if (isset($filePelatihan[$index]) && $filePelatihan[$index]->isValid()) {
                        $file = $filePelatihan[$index];
                        $fileName = time() . '_dasar_' . $index . '_' . $file->getClientOriginalName();
                        $filePath = $file->storeAs('pelatihan_pdf', $fileName, 'public');
                    }

                    $daftarPelatihanDasar[] = [
                        'nama'  => $nama,
                        'tahun' => $tahunPelatihan[$index] ?? null,
                        'file'  => $filePath,
                    ];
                }
            }
        }

        // 2. PROSES PELATIHAN PENINGKATAN KOMPETENSI
        $daftarPelatihanKompetensi = [];
        if ($request->has('pelatihan_kompetensi')) {
            $namaKompetensi = $request->input('pelatihan_kompetensi');
            $tahunKompetensi = $request->input('pelatihan_tahun_kompetensi');
            $fileKompetensi = $request->file('pelatihan_file_kompetensi');

            foreach ($namaKompetensi as $index => $nama) {
                if (!empty($nama)) {
                    $filePath = null;
                    if (isset($fileKompetensi[$index]) && $fileKompetensi[$index]->isValid()) {
                        $file = $fileKompetensi[$index];
                        $fileName = time() . '_komp_' . $index . '_' . $file->getClientOriginalName();
                        $filePath = $file->storeAs('pelatihan_pdf', $fileName, 'public');
                    }

                    $daftarPelatihanKompetensi[] = [
                        'nama'  => $nama,
                        'tahun' => $tahunKompetensi[$index] ?? null,
                        'file'  => $filePath,
                    ];
                }
            }
        }

        // Simpan ke array data utama
        $data['pelatihan_dasar'] = $daftarPelatihanDasar;
        $data['pelatihan_peningkatan_kompetensi'] = $daftarPelatihanKompetensi;

        // Hapus key temporary
        unset($data['pelatihan_tahun_dasar']);
        unset($data['pelatihan_file_dasar']);
        unset($data['pelatihan_kompetensi']);
        unset($data['pelatihan_tahun_kompetensi']);
        unset($data['pelatihan_file_kompetensi']);

        Pelatihan::create($data);
        return redirect()->route('pelatihan.index')->with('success', 'Data berhasil ditambahkan.');
    }

    /**
     * Display the specified pelatihan
     */
    public function show($id)
    {
        $pelatihan = Pelatihan::findOrFail($id);
        return view('pelatihan.show', compact('pelatihan'));
    }

    /**
     * Show the form for editing the specified pelatihan
     */
    public function edit($id)
    {
        $pelatihan = Pelatihan::findOrFail($id);
        return view('pelatihan.edit', compact('pelatihan'));
    }

    /**
     * Update the specified pelatihan in storage
     */
    public function update(Request $request, $id)
    {
        $pelatihan = Pelatihan::findOrFail($id);

        $data = $request->validate([
            'nama' => 'required|string|max:255|unique:pelatihans,nama,' . $id,
            'bidang' => 'required|string|max:255',
            'jabatan' => 'nullable|string|max:255',
            'unit' => 'nullable|string|max:255',

            'status_pegawai' => 'required|in:ASN,KARYAWAN TETAP,NON ASN',

            'nip' => 'nullable|required_if:status_pegawai,ASN,KARYAWAN TETAP|string|max:50',
            'golongan' => 'nullable|required_if:status_pegawai,ASN,KARYAWAN TETAP|string|max:100',

            // --- Pangkat hanya required jika ASN ---
            'pangkat' => 'nullable|required_if:status_pegawai,ASN|string|max:100',

            'nirp' => 'nullable|required_if:status_pegawai,NON ASN|string|max:50',

            // --- DASAR ---
            'pelatihan_dasar' => 'nullable|array',
            'pelatihan_dasar.*' => 'nullable|string',
            'pelatihan_tahun_dasar' => 'nullable|array',
            'pelatihan_tahun_dasar.*' => 'nullable|string',
            'pelatihan_file_dasar' => 'nullable|array',
            'pelatihan_file_dasar.*' => 'nullable|file|mimes:pdf|max:2048',
            'pelatihan_existing_file_dasar' => 'nullable|array',

            // --- KOMPETENSI ---
            'pelatihan_kompetensi' => 'nullable|array',
            'pelatihan_kompetensi.*' => 'nullable|string',
            'pelatihan_tahun_kompetensi' => 'nullable|array',
            'pelatihan_tahun_kompetensi.*' => 'nullable|string',
            'pelatihan_file_kompetensi' => 'nullable|array',
            'pelatihan_file_kompetensi.*' => 'nullable|file|mimes:pdf|max:2048',
            'pelatihan_existing_file_kompetensi' => 'nullable|array',
        ]);

        // (Logic update file dipertahankan sama seperti sebelumnya)
        // --- 1. UPDATE LOGIC PELATIHAN DASAR ---
        $daftarPelatihanDasar = [];
        if ($request->has('pelatihan_dasar')) {
            $namaPelatihan = $request->input('pelatihan_dasar');
            $tahunPelatihan = $request->input('pelatihan_tahun_dasar');
            $filePelatihanBaru = $request->file('pelatihan_file_dasar');
            $fileLama = $request->input('pelatihan_existing_file_dasar');

            foreach ($namaPelatihan as $index => $nama) {
                if (!empty($nama)) {
                    $filePath = $fileLama[$index] ?? null;
                    if (isset($filePelatihanBaru[$index]) && $filePelatihanBaru[$index]->isValid()) {
                        $file = $filePelatihanBaru[$index];
                        if ($filePath && Storage::disk('public')->exists($filePath)) {
                            Storage::disk('public')->delete($filePath);
                        }
                        $fileName = time() . '_dasar_' . $index . '_' . $file->getClientOriginalName();
                        $filePath = $file->storeAs('pelatihan_pdf', $fileName, 'public');
                    }
                    $daftarPelatihanDasar[] = [
                        'nama'  => $nama,
                        'tahun' => $tahunPelatihan[$index] ?? null,
                        'file'  => $filePath,
                    ];
                }
            }

            $submittedPaths = collect($daftarPelatihanDasar)->pluck('file')->filter();
            $originalPaths = collect($pelatihan->pelatihan_dasar)->pluck('file')->filter();
            $filesToDelete = $originalPaths->diff($submittedPaths);
            foreach ($filesToDelete as $file) {
                if ($file && Storage::disk('public')->exists($file)) {
                    Storage::disk('public')->delete($file);
                }
            }
        }

        // --- 2. UPDATE LOGIC PELATIHAN KOMPETENSI ---
        $daftarPelatihanKompetensi = [];
        if ($request->has('pelatihan_kompetensi')) {
            $namaKompetensi = $request->input('pelatihan_kompetensi');
            $tahunKompetensi = $request->input('pelatihan_tahun_kompetensi');
            $fileKompetensiBaru = $request->file('pelatihan_file_kompetensi');
            $fileLamaKompetensi = $request->input('pelatihan_existing_file_kompetensi');

            foreach ($namaKompetensi as $index => $nama) {
                if (!empty($nama)) {
                    $filePath = $fileLamaKompetensi[$index] ?? null;
                    if (isset($fileKompetensiBaru[$index]) && $fileKompetensiBaru[$index]->isValid()) {
                        $file = $fileKompetensiBaru[$index];
                        if ($filePath && Storage::disk('public')->exists($filePath)) {
                            Storage::disk('public')->delete($filePath);
                        }
                        $fileName = time() . '_komp_' . $index . '_' . $file->getClientOriginalName();
                        $filePath = $file->storeAs('pelatihan_pdf', $fileName, 'public');
                    }
                    $daftarPelatihanKompetensi[] = [
                        'nama'  => $nama,
                        'tahun' => $tahunKompetensi[$index] ?? null,
                        'file'  => $filePath,
                    ];
                }
            }

            $submittedPaths = collect($daftarPelatihanKompetensi)->pluck('file')->filter();
            $originalPaths = collect($pelatihan->pelatihan_peningkatan_kompetensi)->pluck('file')->filter();
            $filesToDelete = $originalPaths->diff($submittedPaths);
            foreach ($filesToDelete as $file) {
                if ($file && Storage::disk('public')->exists($file)) {
                    Storage::disk('public')->delete($file);
                }
            }
        }

        $data['pelatihan_dasar'] = $daftarPelatihanDasar;
        $data['pelatihan_peningkatan_kompetensi'] = $daftarPelatihanKompetensi;

        // Bersihkan Data Temporary
        unset($data['pelatihan_tahun_dasar']);
        unset($data['pelatihan_file_dasar']);
        unset($data['pelatihan_existing_file_dasar']);
        unset($data['pelatihan_kompetensi']);
        unset($data['pelatihan_tahun_kompetensi']);
        unset($data['pelatihan_file_kompetensi']);
        unset($data['pelatihan_existing_file_kompetensi']);

        $pelatihan->update($data);
        return redirect()->route('pelatihan.index')->with('success', 'Data berhasil diperbarui.');
    }

    /**
     * Remove the specified pelatihan from storage
     */
    public function destroy($id)
    {
        $pelatihan = Pelatihan::findOrFail($id);

        // Hapus file fisik jika ada
        if (is_array($pelatihan->pelatihan_dasar)) {
            foreach ($pelatihan->pelatihan_dasar as $item) {
                if (!empty($item['file']) && Storage::disk('public')->exists($item['file'])) {
                    Storage::disk('public')->delete($item['file']);
                }
            }
        }
        if (is_array($pelatihan->pelatihan_peningkatan_kompetensi)) {
            foreach ($pelatihan->pelatihan_peningkatan_kompetensi as $item) {
                if (!empty($item['file']) && Storage::disk('public')->exists($item['file'])) {
                    Storage::disk('public')->delete($item['file']);
                }
            }
        }

        $pelatihan->delete();
        return redirect()->route('pelatihan.index')->with('success', 'Data berhasil dihapus.');
    }

    /**
     * Export pelatihan to Excel
     */
    public function export()
    {
        try {
            $pelatihans = Pelatihan::all();
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Header Update
            $headers = [
                'NAMA', 'BIDANG', 'JABATAN', 'UNIT', 'STATUS', 'NIP / NIRP', 'GOLONGAN', 'PANGKAT',
                'PELATIHAN DASAR (TAHUN)', 'PELATIHAN KOMPETENSI (TAHUN)', 'FILE PDF (GABUNGAN)'
            ];
            $sheet->fromArray([$headers], null, 'A1');

            $headerStyle = [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '7c1316']],
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            ];
            $sheet->getStyle('A1:K1')->applyFromArray($headerStyle);

            $row = 2;
            foreach ($pelatihans as $p) {
                $dasarStr = '';
                $files = [];
                if (is_array($p->pelatihan_dasar)) {
                    $items = [];
                    foreach ($p->pelatihan_dasar as $item) {
                        $nama = $item['nama'] ?? 'N/A';
                        $tahun = $item['tahun'] ?? '?';
                        $items[] = "{$nama} ({$tahun})";
                        if (!empty($item['file'])) {
                            $files[] = Storage::url($item['file']);
                        }
                    }
                    $dasarStr = implode('; ', $items);
                }

                $kompStr = '';
                if (is_array($p->pelatihan_peningkatan_kompetensi)) {
                    $items = [];
                    foreach ($p->pelatihan_peningkatan_kompetensi as $item) {
                        $nama = $item['nama'] ?? 'N/A';
                        $tahun = $item['tahun'] ?? '?';
                        $items[] = "{$nama} ({$tahun})";
                        if (!empty($item['file'])) {
                            $files[] = Storage::url($item['file']);
                        }
                    }
                    $kompStr = implode('; ', $items);
                }

                $daftarFileStr = implode('; ', $files);

                $identitas = ($p->status_pegawai == 'ASN' || $p->status_pegawai == 'KARYAWAN TETAP') ? $p->nip : $p->nirp;

                $rowData = [
                    $p->nama, $p->bidang, $p->jabatan, $p->unit, $p->status_pegawai,
                    $identitas, $p->golongan, $p->pangkat, $dasarStr, $kompStr, $daftarFileStr
                ];

                $sheet->fromArray([$rowData], null, 'A' . $row);
                $row++;
            }

            foreach (range('A', 'K') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $writer = new Xlsx($spreadsheet);
            $fileName = 'Data_Pegawai_' . date('Y-m-d_His') . '.xlsx';

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $fileName . '"');
            header('Cache-Control: max-age=0');

            $writer->save('php://output');
            exit;
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengekspor data: ' . $e->getMessage());
        }
    }

    // =========================
    // BAGIAN PUBLIC METHODS
    // =========================

    public function publicIndex(Request $request)
    {
        $keyword = $request->input('keyword');
        $searchPerformed = false;
        $pelatihans = collect();
        if ($request->filled('keyword')) {
            $searchPerformed = true;
            $pelatihans = \App\Models\Pelatihan::where('nip', $keyword)
                            ->orWhere('nirp', $keyword)
                            ->get();
        }
        return view('pelatihan.public_index', compact('keyword', 'searchPerformed', 'pelatihans'));
    }

    public function publicEdit($id)
    {
        $pelatihan = Pelatihan::findOrFail($id);
        return view('pelatihan.public_edit', compact('pelatihan'));
    }

    public function publicStore(Request $request)
    {
        // Validasi sama seperti store biasa
        $data = $request->validate([
            'nama' => 'required|string|max:255|unique:pelatihans,nama',
            'bidang' => 'required|string|max:255',
            'jabatan' => 'nullable|string|max:255',
            'unit' => 'nullable|string|max:255',
            'status_pegawai' => 'required|in:ASN,KARYAWAN TETAP,NON ASN',

            'nip' => 'nullable|required_if:status_pegawai,ASN,KARYAWAN TETAP|string|max:50',
            'golongan' => 'nullable|required_if:status_pegawai,ASN,KARYAWAN TETAP|string|max:100',

            // --- Pangkat hanya required jika ASN ---
            'pangkat' => 'nullable|required_if:status_pegawai,ASN|string|max:100',

            'nirp' => 'nullable|required_if:status_pegawai,NON ASN|string|max:50',

            // Validasi Array Pelatihan
            'pelatihan_dasar' => 'nullable|array',
            'pelatihan_file_dasar.*' => 'nullable|file|mimes:pdf|max:2048',
            'pelatihan_kompetensi' => 'nullable|array',
            'pelatihan_file_kompetensi.*' => 'nullable|file|mimes:pdf|max:2048',
        ]);

        // --- LOGIKA PENYIMPANAN (Copy dari method store) ---
        // 1. Proses Pelatihan Dasar
        $daftarPelatihanDasar = [];
        if ($request->has('pelatihan_dasar')) {
            $namaPelatihan = $request->input('pelatihan_dasar');
            $tahunPelatihan = $request->input('pelatihan_tahun_dasar');
            $filePelatihan = $request->file('pelatihan_file_dasar');

            foreach ($namaPelatihan as $index => $nama) {
                if (!empty($nama)) {
                    $filePath = null;
                    if (isset($filePelatihan[$index]) && $filePelatihan[$index]->isValid()) {
                        $file = $filePelatihan[$index];
                        $fileName = time() . '_dasar_' . $index . '_' . $file->getClientOriginalName();
                        $filePath = $file->storeAs('pelatihan_pdf', $fileName, 'public');
                    }
                    $daftarPelatihanDasar[] = [
                        'nama'  => $nama,
                        'tahun' => $tahunPelatihan[$index] ?? null,
                        'file'  => $filePath,
                    ];
                }
            }
        }

        // 2. Proses Pelatihan Kompetensi
        $daftarPelatihanKompetensi = [];
        if ($request->has('pelatihan_kompetensi')) {
            $namaKompetensi = $request->input('pelatihan_kompetensi');
            $tahunKompetensi = $request->input('pelatihan_tahun_kompetensi');
            $fileKompetensi = $request->file('pelatihan_file_kompetensi');

            foreach ($namaKompetensi as $index => $nama) {
                if (!empty($nama)) {
                    $filePath = null;
                    if (isset($fileKompetensi[$index]) && $fileKompetensi[$index]->isValid()) {
                        $file = $fileKompetensi[$index];
                        $fileName = time() . '_komp_' . $index . '_' . $file->getClientOriginalName();
                        $filePath = $file->storeAs('pelatihan_pdf', $fileName, 'public');
                    }
                    $daftarPelatihanKompetensi[] = [
                        'nama'  => $nama,
                        'tahun' => $tahunKompetensi[$index] ?? null,
                        'file'  => $filePath,
                    ];
                }
            }
        }

        $data['pelatihan_dasar'] = $daftarPelatihanDasar;
        $data['pelatihan_peningkatan_kompetensi'] = $daftarPelatihanKompetensi;

        // Bersihkan data temporary
        unset($data['pelatihan_tahun_dasar'], $data['pelatihan_file_dasar']);
        unset($data['pelatihan_tahun_kompetensi'], $data['pelatihan_file_kompetensi']);

        Pelatihan::create($data);

        // Redirect ke public index dengan keyword agar user bisa langsung lihat datanya
        return redirect()->route('public.pelatihan.index', ['keyword' => $request->nip ?? $request->nirp])
            ->with('success', 'Data berhasil disimpan! Silakan cek data Anda di bawah.');
    }

    public function publicUpdate(Request $request, $id)
    {
        $pelatihan = Pelatihan::findOrFail($id);

        $data = $request->validate([
            'pelatihan_dasar' => 'nullable|array',
            'pelatihan_dasar.*' => 'nullable|string',
            'pelatihan_tahun_dasar' => 'nullable|array',
            'pelatihan_tahun_dasar.*' => 'nullable|string',
            'pelatihan_file_dasar' => 'nullable|array',
            'pelatihan_file_dasar.*' => 'nullable|file|mimes:pdf|max:2048',
            'pelatihan_existing_file_dasar' => 'nullable|array',

            'pelatihan_kompetensi' => 'nullable|array',
            'pelatihan_kompetensi.*' => 'nullable|string',
            'pelatihan_tahun_kompetensi' => 'nullable|array',
            'pelatihan_tahun_kompetensi.*' => 'nullable|string',
            'pelatihan_file_kompetensi' => 'nullable|array',
            'pelatihan_file_kompetensi.*' => 'nullable|file|mimes:pdf|max:2048',
            'pelatihan_existing_file_kompetensi' => 'nullable|array',
        ]);

        // --- PROSES PELATIHAN DASAR ---
        $daftarPelatihanDasar = [];
        if ($request->has('pelatihan_dasar')) {
            $namaPelatihan = $request->input('pelatihan_dasar');
            $tahunPelatihan = $request->input('pelatihan_tahun_dasar');
            $filePelatihanBaru = $request->file('pelatihan_file_dasar');
            $fileLama = $request->input('pelatihan_existing_file_dasar', []);

            foreach ($namaPelatihan as $index => $nama) {
                if (!empty($nama)) {
                    $filePath = $fileLama[$index] ?? null;
                    if (isset($filePelatihanBaru[$index]) && $filePelatihanBaru[$index]->isValid()) {
                        $file = $filePelatihanBaru[$index];
                        if ($filePath && Storage::disk('public')->exists($filePath)) {
                            Storage::disk('public')->delete($filePath);
                        }
                        $fileName = time() . '_dasar_' . $index . '_' . $file->getClientOriginalName();
                        $filePath = $file->storeAs('pelatihan_pdf', $fileName, 'public');
                    }

                    $daftarPelatihanDasar[] = [
                        'nama' => $nama,
                        'tahun' => $tahunPelatihan[$index] ?? null,
                        'file' => $filePath,
                    ];
                }
            }

            $submittedPaths = collect($daftarPelatihanDasar)->pluck('file')->filter();
            $originalPaths = collect($pelatihan->pelatihan_dasar)->pluck('file')->filter();
            $filesToDelete = $originalPaths->diff($submittedPaths);
            foreach ($filesToDelete as $file) {
                if ($file && Storage::disk('public')->exists($file)) {
                    Storage::disk('public')->delete($file);
                }
            }
        } else {
            $daftarPelatihanDasar = $pelatihan->pelatihan_dasar ?? [];
        }

        // --- PROSES PELATIHAN KOMPETENSI ---
        $daftarPelatihanKompetensi = [];
        if ($request->has('pelatihan_kompetensi')) {
            $namaKompetensi = $request->input('pelatihan_kompetensi');
            $tahunKompetensi = $request->input('pelatihan_tahun_kompetensi');
            $fileKompetensiBaru = $request->file('pelatihan_file_kompetensi');
            $fileLamaKompetensi = $request->input('pelatihan_existing_file_kompetensi', []);

            foreach ($namaKompetensi as $index => $nama) {
                if (!empty($nama)) {
                    $filePath = $fileLamaKompetensi[$index] ?? null;
                    if (isset($fileKompetensiBaru[$index]) && $fileKompetensiBaru[$index]->isValid()) {
                        $file = $fileKompetensiBaru[$index];
                        if ($filePath && Storage::disk('public')->exists($filePath)) {
                            Storage::disk('public')->delete($filePath);
                        }
                        $fileName = time() . '_komp_' . $index . '_' . $file->getClientOriginalName();
                        $filePath = $file->storeAs('pelatihan_pdf', $fileName, 'public');
                    }

                    $daftarPelatihanKompetensi[] = [
                        'nama' => $nama,
                        'tahun' => $tahunKompetensi[$index] ?? null,
                        'file' => $filePath,
                    ];
                }
            }

            $submittedPaths = collect($daftarPelatihanKompetensi)->pluck('file')->filter();
            $originalPaths = collect($pelatihan->pelatihan_peningkatan_kompetensi)->pluck('file')->filter();
            $filesToDelete = $originalPaths->diff($submittedPaths);
            foreach ($filesToDelete as $file) {
                if ($file && Storage::disk('public')->exists($file)) {
                    Storage::disk('public')->delete($file);
                }
            }
        } else {
            $daftarPelatihanKompetensi = $pelatihan->pelatihan_peningkatan_kompetensi ?? [];
        }

        $pelatihan->pelatihan_dasar = $daftarPelatihanDasar;
        $pelatihan->pelatihan_peningkatan_kompetensi = $daftarPelatihanKompetensi;
        $pelatihan->save();

        return redirect()->route('public.pelatihan.index', ['keyword' => $pelatihan->nip ?? $pelatihan->nirp])
            ->with('success', 'Data pelatihan berhasil diperbarui.');
    }

    public function import_excel(Request $request)
    {
        try {
            $request->validate(['data' => 'required|string']);
            $rows = json_decode($request->input('data'), true);

            if (empty($rows)) {
                return response()->json(['success' => false, 'message' => 'Tidak ada data untuk diimpor.'], 422);
            }

            $imported = 0;
            $errors = [];

            foreach ($rows as $index => $row) {
                $rowIndex = $index + 2;
                try {
                    $statusRaw = $row['Status'] ?? 'Non-PNS';
                    $data = [
                        'nama' => $row['Nama'] ?? null,
                        'bidang' => $row['Bidang'] ?? null,
                        'jabatan' => $row['Jabatan'] ?? null,
                        'unit' => $row['Unit'] ?? null,
                        'status_pegawai' => $statusRaw,
                        'nip' => ($statusRaw == 'PNS' || $statusRaw == 'P3K') ? ($row['NIP'] ?? null) : null,
                        'nirp' => ($statusRaw == 'Non-PNS') ? ($row['NIRP'] ?? null) : null,
                        'golongan' => $row['Golongan'] ?? null,
                        'pangkat' => $row['Pangkat'] ?? null,
                    ];

                    if (empty($data['nama'])) {
                        $errors[] = "Baris " . $rowIndex . ": Nama tidak boleh kosong";
                        continue;
                    }

                    // Helper lokal untuk parse pelatihan
                    $daftarPelatihanDasar = [];
                    if (!empty($row['Pelatihan_Dasar'])) {
                        $items = explode(';', $row['Pelatihan_Dasar']);
                        foreach($items as $it) {
                            $daftarPelatihanDasar[] = ['nama' => trim($it), 'tahun' => null, 'file' => null];
                        }
                    }

                    $daftarPelatihanKompetensi = [];
                    if (!empty($row['Pelatihan_Kompetensi'])) {
                        $items = explode(';', $row['Pelatihan_Kompetensi']);
                        foreach($items as $it) {
                            $daftarPelatihanKompetensi[] = ['nama' => trim($it), 'tahun' => null, 'file' => null];
                        }
                    }

                    $data['pelatihan_dasar'] = $daftarPelatihanDasar;
                    $data['pelatihan_peningkatan_kompetensi'] = $daftarPelatihanKompetensi;

                    Pelatihan::updateOrCreate(
                        ['nama' => $data['nama']],
                        $data
                    );

                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Baris " . $rowIndex . ": " . $e->getMessage();
                }
            }

            $message = "Berhasil impor/update {$imported} data.";
            if (count($errors) > 0) {
                 $message .= " Ditemukan " . count($errors) . " error.";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'errors' => $errors,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Import gagal: ' . $e->getMessage(),
            ], 422);
        }
    }
}
