<?php

namespace App\Http\Controllers;

use App\Models\Pelatihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // Pastikan ini ada
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class PelatihanController extends Controller
{
    /**
     * Middleware untuk proteksi route, hanya admin.
     */
    public function __construct()
    {
        // Pengecekan middleware bisa juga dilakukan di file route (web.php)
        // $this->middleware('auth');
        // $this->middleware('admin'); // Jika Anda punya middleware 'admin'

        $this->middleware(function ($request, $next) {
            if (!auth()->check() || auth()->user()->role !== 'admin') {
                abort(403, 'Akses ditolak.');
            }
            return $next($request);
        });
    }

    /**
     * Display a listing of pelatihan
     */
    public function index(Request $request)
    {
        $query = Pelatihan::query();

        // Filter berdasarkan search (nama)
        if ($request->filled('search')) {
            $query->where('nama', 'like', '%' . $request->search . '%');
        }

        // Filter berdasarkan jabatan
        if ($request->filled('jabatan')) {
            $query->where('jabatan', 'like', '%' . $request->jabatan . '%');
        }

        // Filter berdasarkan unit
        if ($request->filled('unit')) {
            $query->where('unit', 'like', '%' . $request->unit . '%');
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
     * (LOGIKA PENGGABUNGAN DIPERBARUI)
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255|unique:pelatihans,nama',
            'jabatan' => 'nullable|string|max:255',
            'unit' => 'nullable|string|max:255',
            'is_pns' => 'required|boolean',
            'nip' => 'nullable|required_if:is_pns,1|string|max:50',
            'golongan' => 'nullable|required_if:is_pns,1|string|max:100',
            'pelatihan_dasar' => 'nullable|array',
            'pelatihan_dasar.*' => 'nullable|string',
            'pelatihan_tahun_simple' => 'nullable|array',
            'pelatihan_tahun_simple.*' => 'nullable|string', // Terima string atau number
        ]);

        // --- INI BAGIAN PENTING (Baru) ---
        // Menggabungkan array pelatihan_dasar dan pelatihan_tahun_simple
        $daftarPelatihan = [];
        if ($request->has('pelatihan_dasar')) {
            $namaPelatihan = $request->input('pelatihan_dasar');
            $tahunPelatihan = $request->input('pelatihan_tahun_simple');

            foreach ($namaPelatihan as $index => $nama) {
                // Hanya tambahkan jika namanya tidak kosong
                if (!empty($nama)) {
                    $daftarPelatihan[] = [
                        'nama'  => $nama,
                        'tahun' => $tahunPelatihan[$index] ?? null, // Ambil tahun yang sesuai
                    ];
                }
            }
        }

        // Timpa 'pelatihan_dasar' dengan array gabungan yang baru
        $data['pelatihan_dasar'] = $daftarPelatihan;

        // Hapus data array simple (karena sudah digabung)
        unset($data['pelatihan_tahun_simple']);
        // Hapus juga kolom lama jika masih ada
        unset($data['data_tahun']);

        Pelatihan::create($data);
        return redirect()->route('pelatihan.index')->with('success', 'Pelatihan berhasil ditambahkan.');
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
     * (LOGIKA PENGGABUNGAN DIPERBARUI)
     */
    public function update(Request $request, $id)
    {
        $pelatihan = Pelatihan::findOrFail($id);

        $data = $request->validate([
            'nama' => 'required|string|max:255|unique:pelatihans,nama,' . $id,
            'jabatan' => 'nullable|string|max:255',
            'unit' => 'nullable|string|max:255',
            'is_pns' => 'required|boolean',
            'nip' => 'nullable|required_if:is_pns,1|string|max:50',
            'golongan' => 'nullable|required_if:is_pns,1|string|max:100',
            'pelatihan_dasar' => 'nullable|array',
            'pelatihan_dasar.*' => 'nullable|string',
            'pelatihan_tahun_simple' => 'nullable|array',
            'pelatihan_tahun_simple.*' => 'nullable|string',
        ]);

        $daftarPelatihan = [];
        if ($request->has('pelatihan_dasar')) {
            $namaPelatihan = $request->input('pelatihan_dasar');
            $tahunPelatihan = $request->input('pelatihan_tahun_simple');

            foreach ($namaPelatihan as $index => $nama) {
                if (!empty($nama)) {
                    $daftarPelatihan[] = [
                        'nama'  => $nama,
                        'tahun' => $tahunPelatihan[$index] ?? null,
                    ];
                }
            }
        }
        $data['pelatihan_dasar'] = $daftarPelatihan;

        unset($data['pelatihan_tahun_simple']);
        unset($data['data_tahun']);

        $pelatihan->update($data);
        return redirect()->route('pelatihan.index')->with('success', 'Pelatihan berhasil diperbarui.');
    }

    /**
     * Remove the specified pelatihan from storage
     */
    public function destroy($id)
    {
        $pelatihan = Pelatihan::findOrFail($id);
        $pelatihan->delete();
        return redirect()->route('pelatihan.index')->with('success', 'Pelatihan berhasil dihapus.');
    }

    /**
     * Export pelatihan to Excel
     * (LOGIKA EXPORT DIPERBARUI)
     */
    public function export()
    {
        try {
            $pelatihans = Pelatihan::all();
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Set header (Header baru sesuai struktur data)
            $headers = ['NAMA', 'JABATAN', 'UNIT', 'STATUS', 'NIP', 'GOLONGAN', 'DAFTAR PELATIHAN (TAHUN)'];
            $sheet->fromArray([$headers], null, 'A1');

            // Style header
            $headerStyle = [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '7c1316']],
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            ];
            $sheet->getStyle('A1:G1')->applyFromArray($headerStyle);

            // Add data
            $row = 2;
            foreach ($pelatihans as $p) {

                // Format gabungan pelatihan_dasar
                $daftarPelatihanStr = '';
                if (is_array($p->pelatihan_dasar)) {
                    $items = [];
                    foreach ($p->pelatihan_dasar as $item) {
                        $nama = $item['nama'] ?? 'N/A';
                        $tahun = $item['tahun'] ?? '?';
                        $items[] = "{$nama} ({$tahun})";
                    }
                    $daftarPelatihanStr = implode('; ', $items);
                }

                $rowData = [
                    $p->nama,
                    $p->jabatan,
                    $p->unit,
                    $p->is_pns ? 'PNS' : 'Non-PNS',
                    $p->is_pns ? $p->nip : '',
                	$p->is_pns ? $p->golongan : '',
                    $daftarPelatihanStr
                ];

                $sheet->fromArray([$rowData], null, 'A' . $row);
                $row++;
            }

            // Auto-size columns
            foreach (range('A', 'G') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $writer = new Xlsx($spreadsheet);
            $fileName = 'Pelatihan_' . date('Y-m-d_His') . '.xlsx';

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $fileName . '"');
            header('Cache-Control: max-age=0');

            $writer->save('php://output');
            exit;

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengekspor data: ' . $e->getMessage());
        }
    }

    /**
     * Import pelatihan from Excel
     * (LOGIKA IMPOR DIPERBARUI UNTUK MENERIMA JSON DARI FRONTEND)
     */
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
                $rowIndex = $index + 2; // Baris 1 adalah header, data mulai dari baris 2

                try {
                    // Ambil data dasar dari template
                    $data = [
                        'nama' => $row['Nama'] ?? null,
                        'jabatan' => $row['Jabatan'] ?? null,
                        'unit' => $row['Unit'] ?? null,
                        'is_pns' => (isset($row['is_pns (1=PNS, 0=Non-PNS)']) && $row['is_pns (1=PNS, 0=Non-PNS)'] == '1'),
                        'nip' => $row['NIP'] ?? null,
                        'golongan' => $row['Golongan'] ?? null,
                    ];

                    // Validasi nama
                    if (empty($data['nama'])) {
                        $errors[] = "Baris " . $rowIndex . ": Nama tidak boleh kosong";
                        continue;
                    }

                    // Kumpulkan data pelatihan dinamis (Pelatihan1_Nama, Pelatihan1_Tahun, dst.)
                    $daftarPelatihan = [];
                    $i = 1;
                    while (true) {
                        $namaKey = "Pelatihan{$i}_Nama";
                        $tahunKey = "Pelatihan{$i}_Tahun";

                        // Berhenti jika kolom nama pelatihan tidak ada atau kosong
                        if (!isset($row[$namaKey]) || empty($row[$namaKey])) {
                            break;
                        }

                        $daftarPelatihan[] = [
                            'nama' => $row[$namaKey],
                            'tahun' => $row[$tahunKey] ?? null,
                        ];
                        $i++;
                    }
                    $data['pelatihan_dasar'] = $daftarPelatihan;

                    // Buat atau Update data berdasarkan Nama
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
