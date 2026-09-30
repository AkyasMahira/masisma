<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Ruangan;
use App\Models\Mou;
use App\Models\InvoiceItem;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
class InvoiceController extends Controller
{
    /**
     * Tampilkan daftar invoice dengan filter dan pagination.
     */
 public function index(Request $request)
    {
        // 1. Tangkap semua request
        $search  = $request->get('search');
        $status  = $request->get('status');
        $jenis   = $request->get('jenis');
        $bulan   = $request->get('bulan');
        $tahun   = $request->get('tahun');
        
        // Default sorting: created_at, descending
        $sortBy  = $request->get('sort_by', 'created_at');
        $sortDir = $request->get('sort_dir', 'desc');

        // Setup Pagination Manual
        $page    = (int) $request->get('page', 1);
        $perPage = 10;
        $offset  = ($page - 1) * $perPage;

        $query = Invoice::query();

        // 2. Terapkan Filter
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('no_invoice', 'like', "%$search%")
                  ->orWhere('instansi', 'like', "%$search%")
                  ->orWhere('prodi', 'like', "%$search%");
            });
        }
        if ($status) { $query->where('status', $status); }
        if ($jenis)  { $query->where('jenis_kegiatan', $jenis); }
        if ($bulan)  { $query->whereMonth('created_at', $bulan); }
        if ($tahun)  { $query->whereYear('created_at', $tahun); }

        // 3. Hitung Data & Eksekusi Query
        $totalData  = $query->count();
        $totalPages = ceil($totalData / $perPage);
        
        // Pastikan array valid agar tidak error SQL Injection dari parameter URL
        $allowedSorts = ['created_at', 'no_invoice', 'jumlah_dibayarkan'];
        $sortBy = in_array($sortBy, $allowedSorts) ? $sortBy : 'created_at';
        $sortDir = in_array(strtolower($sortDir), ['asc', 'desc']) ? $sortDir : 'desc';

        $invoices = $query->orderBy($sortBy, $sortDir)
                          ->offset($offset)
                          ->limit($perPage)
                          ->get();

        return view('admin.invoices.index', compact(
            'invoices', 'totalPages', 'page', 'search', 'totalData', 
            'status', 'jenis', 'bulan', 'tahun', 'sortBy', 'sortDir'
        ));
    }
    public function create()
    {
        $nextNo   = Invoice::generateNumber();
        $ruangans = Ruangan::orderBy('nm_ruangan', 'asc')->get();
        $mous     = Mou::orderBy('nama_instansi', 'asc')->get();
        
        return view('admin.invoices.create', compact('nextNo', 'ruangans', 'mous'));
    }

    /**
     * Simpan data invoice baru dengan status awal 'Perlu TTD'.
     */
public function store(Request $request)
{
    // 1. Hitung total harga dari semua item untuk memenuhi kolom 'total_harga'
    $items = collect($request->items);
    $totalHargaItems = $items->sum('subtotal');
    
    // 2. Ambil data dari baris pertama untuk memenuhi kolom jml_mhs, dsb (DB Lama)
    $firstItem = $items->first() ?? [
        'jml_mhs' => 0, 
        'jml_minggu' => 0, 
        'harga_satuan' => 0
    ];

    // 3. Simpan ke tabel Invoices
    $invoice = Invoice::create([
        'no_invoice'        => Invoice::generateNumber(),
        'payment_token'     => (string) Str::uuid(),
        'instansi'          => $request->instansi,
        'prodi'             => $request->prodi,
        'jenjang'           => $request->jenjang,
        'jenis_kegiatan'    => $request->jenis_kegiatan,
        'tgl_mulai'         => $request->tgl_mulai,
        'tgl_akhir'         => $request->tgl_akhir,
        'tgl_invoice'       => $request->tgl_invoice ?? now()->toDateString(),
        'tgl_jatuh_tempo'   => $request->tgl_jatuh_tempo,
        'penanggung_jawab'  => $request->penanggung_jawab,
        'ruangan_ci'        => is_array($request->ruangan_ci) ? implode(', ', $request->ruangan_ci) : $request->ruangan_ci,
        'biaya_konsumsi'    => $request->biaya_konsumsi ?? 0,
        'status'            => 'Perlu TTD',
        'ttd_method'        => 'manual',
        
        // --- SYARAT DATABASE LAMA ---
        'total_harga'       => $totalHargaItems,      // <--- INI YANG TADI ERROR
        'jumlah_dibayarkan' => $request->jumlah_dibayarkan,
        'jml_mhs'           => $firstItem['jml_mhs'],
        'jml_minggu'        => $firstItem['jml_minggu'],
        'harga_satuan'      => $firstItem['harga_satuan'],
    ]);

    // 4. Simpan rincian ke tabel invoice_items
    foreach ($request->items as $item) {
        $invoice->items()->create($item);
    }

    return redirect()->route('admin.invoices.index')->with('success', "Invoice berhasil diterbitkan!");
}

    public function edit(Invoice $invoice)
    {
        $ruangans = Ruangan::orderBy('nm_ruangan', 'asc')->get();
        $mous     = Mou::orderBy('nama_instansi', 'asc')->get();
        $selectedRuangan = explode(', ', $invoice->ruangan_ci);

        return view('admin.invoices.edit', compact('invoice', 'ruangans', 'mous', 'selectedRuangan'));
    }

    // Pastikan import ini ada di atas

public function update(Request $request, Invoice $invoice)
{
    \DB::transaction(function () use ($request, $invoice) {
        $items = collect($request->items);
        $totalHargaItems = $items->sum('subtotal');
        $firstItem = $items->first() ?? ['jml_mhs' => 0, 'jml_minggu' => 0, 'harga_satuan' => 0];

        // 1. Data utama
        $data = $request->except(['items', 'bukti_bayar', 'signature']);
        if ($request->has('ruangan_ci')) {
            $data['ruangan_ci'] = implode(', ', $request->ruangan_ci);
        }
        
        // --- SYARAT DATABASE LAMA ---
        $data['total_harga']  = $totalHargaItems;
        $data['jml_mhs']      = $firstItem['jml_mhs'];
        $data['jml_minggu']   = $firstItem['jml_minggu'];
        $data['harga_satuan'] = $firstItem['harga_satuan'];

        $invoice->update($data);

        // 2. Update rincian
        $invoice->items()->delete();
        foreach ($request->items as $item) {
            $invoice->items()->create($item);
        }
    });

    return redirect()->route('admin.invoices.index')->with('success', 'Data invoice diperbarui!');
}
public function report(Request $request)
{
    // Default rentang tanggal (awal bulan s/d hari ini)
    $startDate = $request->get('start_date', date('Y-m-01'));
    $endDate = $request->get('end_date', date('Y-m-d'));

    $query = Invoice::whereBetween('tgl_invoice', [$startDate, $endDate]);

    // Clone query untuk statistik agar tidak terpengaruh pagination jika nanti ditambah
    $statsQuery = clone $query;

    // 1. Total Billing (Semua yang tidak batal)
    $totalBilling = $statsQuery->where('status', '!=', 'Batal')->sum('jumlah_dibayarkan');

    // 2. Billing Bayar (Selesai/Lunas)
    $totalPaid = Invoice::whereBetween('tgl_invoice', [$startDate, $endDate])
                ->where('status', 'Selesai')->sum('jumlah_dibayarkan');

    // 3. Billing Belum Bayar (Perlu TTD, Menunggu, Proses)
    $totalUnpaid = Invoice::whereBetween('tgl_invoice', [$startDate, $endDate])
                ->whereIn('status', ['Perlu TTD', 'Menunggu Pembayaran', 'Proses'])
                ->sum('jumlah_dibayarkan');

    // 4. Billing Aktual (Uang yang benar-benar masuk berdasarkan tgl_pembayaran)
    $billingAktual = Invoice::whereBetween('tgl_pembayaran', [$startDate, $endDate])
                    ->where('status', 'Selesai')->sum('jumlah_dibayarkan');

    $invoices = $query->orderBy('tgl_invoice', 'desc')->get();

    return view('admin.invoices.report', compact(
        'invoices', 'totalBilling', 'totalPaid', 'totalUnpaid', 'billingAktual', 'startDate', 'endDate'
    ));
}
/**
 * Cetak PDF untuk Publik (Tanpa Login)
 */
public function publicPrint($token)
{
    // Cari invoice berdasarkan token, bukan ID (lebih aman)
    $invoice = Invoice::where('payment_token', $token)->firstOrFail();
    
    // Kita panggil fungsi print yang sudah ada supaya tidak nulis kode double
    return $this->print($invoice);
}
    public function updateStatus(Request $request, Invoice $invoice)
    {
        $invoice->update(['status' => $request->status]);
        return back()->with('success', 'Status invoice diperbarui!');
    }

    public function destroy(Invoice $invoice)
    {
        if ($invoice->bukti_bayar) {
            Storage::disk('public')->delete($invoice->bukti_bayar);
        }
        if ($invoice->signature_path) {
            Storage::disk('public')->delete($invoice->signature_path);
        }
        $invoice->delete();
        return back()->with('success', 'Invoice berhasil dihapus!');
    }

    /**
     * Cetak Invoice ke PDF.
     * REVISI 4: Load relasi 'items' agar foreach tidak error.
     */
    public function print(Invoice $invoice)
    {
        // Pastikan items dimuat (Eager Loading)
        $invoice->load('items'); 

        $terbilang = ucwords($this->penyebut($invoice->jumlah_dibayarkan)) . " Rupiah";

        // Logo Base64
        $logoPath = public_path('logors.png');
        $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));

        // Stempel Base64
        $stempelBase64 = $logoBase64; 

        // Signature Base64
        $signatureBase64 = null;
        if ($invoice->ttd_method == 'digital') {
            if ($invoice->signature_path && Storage::disk('public')->exists($invoice->signature_path)) {
                $sigData = Storage::disk('public')->get($invoice->signature_path);
                $signatureBase64 = 'data:image/png;base64,' . base64_encode($sigData);
            } else {
                $ttdStatis = public_path('img/ttd_bendahara.png');
                if(file_exists($ttdStatis)){
                    $signatureBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($ttdStatis));
                }
            }
        }

        $pdf = Pdf::loadView('admin.invoices.print', compact(
            'invoice', 'terbilang', 'logoBase64', 'stempelBase64', 'signatureBase64'
        ));
        
        return $pdf->stream('Invoice-' . $invoice->no_invoice . '.pdf');
    }

    /**
     * Verifikasi Invoice + Simpan E-Signature.
     */
    public function verify(Request $request, Invoice $invoice)
    {
        $request->validate(['ttd_method' => 'required']);

        $signaturePath = $invoice->signature_path;

        if ($request->ttd_method == 'digital' && $request->signature) {
            $image_base64 = $request->signature; 
            $image_parts = explode(";base64,", $image_base64);
            $image_base64_decode = base64_decode($image_parts[1]);

            $fileName = 'signature_' . $invoice->id . '_' . time() . '.png';
            $signaturePath = 'signatures/' . $fileName;

            Storage::disk('public')->put($signaturePath, $image_base64_decode);
        }

        // Jika diverifikasi dari status 'Perlu TTD', ubah ke 'Menunggu Pembayaran'
        // Jika diverifikasi dari status 'Proses' (bukti bayar sudah ada), ubah ke 'Selesai'
        $newStatus = ($invoice->status == 'Proses') ? 'Selesai' : 'Menunggu Pembayaran';

        $invoice->update([
            'ttd_method'     => $request->ttd_method,
            'signature_path' => $signaturePath,
            'status'         => $newStatus,
        ]);

        return back()->with('success', 'Invoice berhasil ditandatangani & diverifikasi!');
    }

    public function publicPayment($token)
    {
        $invoice = Invoice::where('payment_token', $token)->firstOrFail();
        return view('public.payment_upload', compact('invoice'));
    }

  public function submitPayment(Request $request, $token)
{
    $invoice = Invoice::where('payment_token', $token)->firstOrFail();

    // 1. TAMBAHKAN PENGECEKAN JATUH TEMPO DI SINI
    if (\Carbon\Carbon::parse($invoice->tgl_jatuh_tempo)->endOfDay()->isPast()) {
        return back()->with('error', 'Gagal upload: Batas waktu pembayaran (Jatuh Tempo) sudah lewat.');
    }

    // Lanjut ke validasi normal jika belum expired
    $request->validate([
        'bank' => 'required',
        'bukti_bayar' => 'required|image|max:1024' // Pastikan max 1024 KB (1MB) sesuai validasi JS
    ]);

    if ($request->hasFile('bukti_bayar')) {
        $path = $request->file('bukti_bayar')->store('bukti_transfer', 'public');
        
        $invoice->update([
            'bukti_bayar'    => $path,
            'bank'           => $request->bank,
            'status'         => 'Proses',
            'tgl_pembayaran' => now()
        ]);
    }

    return back()->with('success', 'Bukti berhasil diunggah! Mohon tunggu verifikasi admin.');
}

    private function penyebut($nilai) {
        $nilai = abs($nilai);
        $huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
        $temp = "";
        if ($nilai < 12) { $temp = " ". $huruf[$nilai]; } 
        else if ($nilai < 20) { $temp = $this->penyebut($nilai - 10). " belas"; } 
        else if ($nilai < 100) { $temp = $this->penyebut($nilai/10)." puluh". $this->penyebut($nilai % 10); } 
        else if ($nilai < 200) { $temp = " seratus" . $this->penyebut($nilai - 100); } 
        else if ($nilai < 1000) { $temp = $this->penyebut($nilai/100) . " ratus" . $this->penyebut($nilai % 100); } 
        else if ($nilai < 2000) { $temp = " seribu" . $this->penyebut($nilai - 1000); } 
        else if ($nilai < 1000000) { $temp = $this->penyebut($nilai/1000) . " ribu" . $this->penyebut($nilai % 1000); } 
        else if ($nilai < 1000000000) { $temp = $this->penyebut($nilai/1000000) . " juta" . $this->penyebut($nilai % 1000000); }
        return $temp;
    }
}