@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

<style>
    :root {
        --custom-maroon: #7c1316;
        --custom-maroon-light: #a3191d;
        --card-radius: 12px;
        --shadow-soft: 0 4px 20px rgba(0, 0, 0, 0.05);
    }
    body { background-color: #f8f9fa; }
    .page-header {
        background: #fff; border-radius: var(--card-radius); padding: 1.5rem;
        box-shadow: var(--shadow-soft); margin-bottom: 2rem; border-left: 5px solid var(--custom-maroon);
    }
    .form-section-title {
        font-size: 0.85rem; font-weight: 700; color: var(--custom-maroon);
        text-transform: uppercase; letter-spacing: 1px; margin-bottom: 1.5rem;
        display: flex; align-items: center; gap: 10px;
    }
    .form-section-title hr { flex-grow: 1; opacity: 0.1; }
    .card-custom { border-radius: var(--card-radius); border: none; box-shadow: var(--shadow-soft); }
    .total-display-box {
        background: #fcf0f1; padding: 25px; border-radius: 10px; border: 1px dashed var(--custom-maroon);
    }
    .table-item thead th { 
        font-size: 0.75rem; text-transform: uppercase; color: #666; 
        background: #f8f9fa; border-top: none;
    }
    .animate-up { animation: fadeInUp 0.6s ease forwards; }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
</style>

<div class="container-fluid pb-5">
    {{-- HEADER --}}
    <div class="page-header d-flex justify-content-between align-items-center animate-up">
        <div>
            <h4 class="fw-bold mb-0 text-dark">Edit Data Invoice</h4>
            <small class="text-muted">Nomor Invoice: <span class="badge bg-dark">{{ $invoice->no_invoice }}</span></small>
        </div>
        <a href="{{ route('admin.invoices.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <form action="{{ route('admin.invoices.update', $invoice->id) }}" method="POST" id="invoiceForm" class="animate-up">
        @csrf
        @method('PUT')
        <div class="card card-custom p-4">
            <div class="card-body">
                
                {{-- SECTION 1: INSTANSI --}}
                <div class="form-section-title">
                    <i class="bi bi-building-fill"></i> INFORMASI INSTANSI <hr>
                </div>
                <div class="row g-4 mb-5">
                    <div class="col-md-3">
                        <label class="form-label fw-bold small">Jenis Kegiatan Utama</label>
                        <select class="form-select choices-single" name="jenis_kegiatan">
                            @foreach(['Praktek Kerja Lapangan', 'Penelitian', 'Pelatihan', 'Magang', 'Diklat','Studi Banding','Uji Kompetensi'] as $j)
                                <option value="{{ $j }}" {{ $invoice->jenis_kegiatan == $j ? 'selected' : '' }}>{{ $j }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold small">Jenjang</label>
                        <select class="form-select choices-single" name="jenjang">
                            @foreach(['DIII', 'DIV', 'S1', 'S2', 'Profesi', '-','SMK'] as $jen)
                                <option value="{{ $jen }}" {{ $invoice->jenjang == $jen ? 'selected' : '' }}>{{ $jen }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
    <label class="form-label fw-bold small">Program Studi</label>
    
    <!-- Input dengan Datalist untuk Form Edit -->
    <input type="text" 
           name="prodi" 
           id="prodi" 
           class="form-control" 
           placeholder="KETIK PROGRAM STUDI..." 
           list="prodi-list" 
           value="{{ old('prodi', $invoice->prodi) }}"
           required 
           style="text-transform: uppercase;" 
           oninput="this.value = this.value.toUpperCase()">

    <!-- Wadah Pilihan (Datalist) -->
    <datalist id="prodi-list">
        <option value="S1 FISIKA">
        <option value="S2 FARMASI">
        <option value="S1 ILMU EKONOMI">
        <option value="SMK REKAYASA PERANGKAT LUNAK">
        <option value="SMK TEKNIK KOMPUTER JARINGAN">
        <option value="SMK MULTIMEDIA">
        <option value="S1 TEKNIK INFORMATIKA">
        <option value="S1 SISTEM INFORMASI">
        <option value="S1 ILMU KOMPUTER">
        <option value="D3 TEKNIK ELEKTROMEDIK">
        <option value="D4 TEKNIK ELEKTROMEDIK">
        <option value="S1 TEKNIK ELEKTRO">
        <option value="S1 TEKNIK LINGKUNGAN">
        <option value="S2 KEBIDANAN">
        <option value="SMK ASISTEN KEPERAWATAN">
        <option value="D3 KEPERAWATAN">
        <option value="D4 KEPERAWATAN">
        <option value="S1 KEPERAWATAN">
        <option value="PROFESI NERS">
        <option value="S2 KEPERAWATAN">
        <option value="S2 MAGISTER MANAJEMEN">
        <option value="D3 KEBIDANAN">
        <option value="D4 KEBIDANAN">
        <option value="S1 KEBIDANAN">
        <option value="PROFESI BIDAN">
        <option value="D4 REKAM MEDIK">
        <option value="D3 REKAM MEDIK">
        <option value="SMK FARMASI">
        <option value="D3 FARMASI">
        <option value="S1 FARMASI">
        <option value="PROFESI APOTEKER">
        <option value="S1 KESELAMATAN DAN KESEHATAN KERJA (K3)">
        <option value="D4 KESELAMATAN DAN KESEHATAN KERJA (K3)">
        <option value="S1 KEDOKTERAN UMUM">
        <option value="PROFESI DOKTER (KOAS)">
        <option value="S1 KEDOKTERAN GIGI">
        <option value="PROFESI DOKTER GIGI">
        <option value="PPDS (SPESIALIS)">
        <option value="S1 TEKNOLOGI LABORATORIUM MEDIK">
        <option value="D4 TEKNOLOGI LABORATORIUM MEDIK">
        <option value="D3 ANALIS KESEHATAN (TLM)">
        <option value="D4 ANALIS KESEHATAN (TLM)">
        <option value="D3 RADIOLOGI">
        <option value="D4 RADIOLOGI">
        <option value="D3 FISIOTERAPI">
        <option value="S1 FISIOTERAPI">
        <option value="PROFESI FISIOTERAPI">
        <option value="D3 GIZI">
        <option value="S1 GIZI">
        <option value="PROFESI DIETISIEN">
        <option value="D3 REKAM MEDIS & INFORMASI KESEHATAN">
        <option value="D4 REKAM MEDIS & INFORMASI KESEHATAN">
        <option value="D3 KESEHATAN LINGKUNGAN (SANITASI)">
        <option value="S1 KESEHATAN MASYARAKAT">
        <option value="D4 PROMOSI KESEHATAN">
        <option value="SMK OTOMATISASI TATA KELOLA PERKANTORAN (OTKP)">
        <option value="SMK AKUNTANSI">
        <option value="D3 AKUNTANSI">
        <option value="S1 AKUNTANSI">
        <option value="S1 MANAJEMEN">
        <option value="S1 HUKUM">
        <option value="S1 PSIKOLOGI">
        <option value="S1 ADMINISTRASI PUBLIK">
        <option value="S1 ADMINISTRASI RUMAH SAKIT">
    </datalist>
</div>
                 <div class="col-md-3">
    <label class="form-label fw-bold small">Instansi (Data MoU)</label>
    
    <!-- 1. Input Teks untuk memunculkan nilai saat ini dan menampung ketikan baru -->
    <input type="text" 
           name="instansi" 
           list="daftar_instansi" 
           class="form-control" 
           value="{{ $invoice->instansi ?? '' }}" 
           placeholder="Pilih atau Ketik Instansi..." 
           required>
    
    <!-- 2. Datalist sebagai sumber "Autocomplete / Dropdown" -->
    <datalist id="daftar_instansi">
        @foreach($mous as $m)
            <option value="{{ $m->nama_instansi }}"></option>
        @endforeach
    </datalist>
</div>
                </div>

                {{-- SECTION 2: PELAKSANAAN --}}
                <div class="form-section-title">
                    <i class="bi bi-geo-alt-fill"></i> DETAIL PELAKSANAAN <hr>
                </div>
                <div class="row g-4 mb-5">
                    <div class="col-md-4">
                        @php $selectedRuangan = explode(', ', $invoice->ruangan_ci); @endphp
                        <label class="form-label fw-bold small">Ruangan / CI</label>
                        <select name="ruangan_ci[]" class="form-select choices-multiple" multiple required>
                            @foreach($ruangans as $r)
                                <option value="{{ $r->nm_ruangan }}" {{ in_array($r->nm_ruangan, $selectedRuangan) ? 'selected' : '' }}>{{ $r->nm_ruangan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">Tanggal Mulai</label>
                        <input type="date" class="form-control" name="tgl_mulai" id="tgl_mulai" value="{{ $invoice->tgl_mulai }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">Tanggal Berakhir</label>
                        <input type="date" class="form-control" name="tgl_akhir" id="tgl_akhir" value="{{ $invoice->tgl_akhir }}" required>
                    </div>
                </div>

                {{-- SECTION 3: TABEL RINCIAN KEGIATAN --}}
                <div class="form-section-title">
                    <i class="bi bi-list-stars"></i> RINCIAN ITEM TAGIHAN <hr>
                </div>
                
                <div class="table-responsive mb-4">
                    <table class="table table-item" id="itemTable">
                        <thead>
                            <tr>
                                <th>Deskripsi Layanan</th>
                                <th width="100">Mhs</th>
                                <th width="100">Minggu</th>
                                <th width="180">Harga (Rp)</th>
                                <th width="200">Subtotal</th>
                                <th width="50"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($invoice->items as $index => $item)
                            <tr>
                                <td><input type="text" name="items[{{ $index }}][deskripsi]" class="form-control" value="{{ $item->deskripsi }}" required></td>
                                <td><input type="number" name="items[{{ $index }}][jml_mhs]" class="form-control calc jml_mhs" value="{{ $item->jml_mhs }}" required></td>
                                <td><input type="number" name="items[{{ $index }}][jml_minggu]" class="form-control calc jml_minggu" value="{{ $item->jml_minggu }}" required></td>
                                <td><input type="number" name="items[{{ $index }}][harga_satuan]" class="form-control calc harga_satuan" value="{{ (int)$item->harga_satuan }}" required></td>
                                <td>
                                    <input type="number" name="items[{{ $index }}][subtotal]" class="form-control subtotal_val bg-light" value="{{ (int)$item->subtotal }}" readonly>
                                </td>
                                <td>
                                    @if($index > 0)
                                        <button type="button" class="btn btn-link text-danger removeRow p-0"><i class="bi bi-trash-fill h5"></i></button>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td><input type="text" name="items[0][deskripsi]" class="form-control" placeholder="Biaya Praktek Klinik" required></td>
                                <td><input type="number" name="items[0][jml_mhs]" class="form-control calc jml_mhs" value="1" required></td>
                                <td><input type="number" name="items[0][jml_minggu]" class="form-control calc jml_minggu" value="1" required></td>
                                <td><input type="number" name="items[0][harga_satuan]" class="form-control calc harga_satuan" placeholder="0" required></td>
                                <td><input type="number" name="items[0][subtotal]" class="form-control subtotal_val bg-light" readonly></td>
                                <td></td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-sm btn-outline-maroon fw-bold" id="addRow" style="color: var(--custom-maroon); border-color: var(--custom-maroon);">
                        <i class="bi bi-plus-circle me-1"></i> Tambah Item Kegiatan
                    </button>
                </div>

                <div class="row g-4 mb-5">
                    <div class="col-md-6">
                        <div class="p-3 rounded-3 border bg-light">
                            <label class="form-label fw-bold small">Biaya Konsumsi / Lainnya (Flat)</label>
                            <input type="number" class="form-control calc" name="biaya_konsumsi" id="biaya_konsumsi" value="{{ (int)$invoice->biaya_konsumsi }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="total-display-box h-100 d-flex flex-column justify-content-center">
                            <h6 class="text-uppercase fw-bold text-muted small mb-1">Total Penagihan Akhir</h6>
                            <h2 class="fw-bold mb-0" style="color: var(--custom-maroon);">Rp <span id="display_total">{{ number_format($invoice->jumlah_dibayarkan, 0, ',', '.') }}</span></h2>
                            <input type="hidden" name="jumlah_dibayarkan" id="hidden_total" value="{{ $invoice->jumlah_dibayarkan }}">
                        </div>
                    </div>
                </div>

                {{-- SECTION 4: ADMINISTRASI & STATUS --}}
                <div class="form-section-title">
                    <i class="bi bi-person-check-fill"></i> PENGESAHAN & STATUS <hr>
                </div>
                <div class="row g-4">
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">Jatuh Tempo</label>
                        <input type="date" class="form-control" name="tgl_jatuh_tempo" value="{{ $invoice->tgl_jatuh_tempo }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">Status Invoice</label>
                        <select name="status" class="form-select choices-single">
                            <option value="Perlu TTD" {{ $invoice->status == 'Perlu TTD' ? 'selected' : '' }}>PERLU TTD</option>
                            <option value="Menunggu Pembayaran" {{ $invoice->status == 'Menunggu Pembayaran' ? 'selected' : '' }}>MENUNGGU BAYAR</option>
                            <option value="Proses" {{ $invoice->status == 'Proses' ? 'selected' : '' }}>PROSES (VERIFY)</option>
                            <option value="Selesai" {{ $invoice->status == 'Selesai' ? 'selected' : '' }}>SELESAI / LUNAS</option>
                            <option value="Batal" {{ $invoice->status == 'Batal' ? 'selected' : '' }}>BATAL</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">Penanggung Jawab (Jabatan)</label>
                        <input type="text" class="form-control" name="penanggung_jawab" value="{{ $invoice->penanggung_jawab }}" required>
                    </div>
                </div>

                <div class="mt-5 text-end border-top pt-4">
                    <button type="submit" class="btn btn-dark px-5 py-3 fw-bold shadow-sm" style="background: var(--custom-maroon); border:none; border-radius: 10px;">
                        <i class="bi bi-save me-2"></i> SIMPAN PERUBAHAN
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Initialize Choices.js
    const singleSelects = document.querySelectorAll('.choices-single');
    singleSelects.forEach(el => new Choices(el, { searchEnabled: true, itemSelectText: '', shouldSort: false }));
    const multiSelect = document.querySelector('.choices-multiple');
    new Choices(multiSelect, { removeItemButton: true, searchEnabled: true });

    let rowIdx = {{ count($invoice->items) > 0 ? count($invoice->items) : 1 }};

    // 2. Tambah Baris Baru
    document.getElementById('addRow').addEventListener('click', function() {
        const tr = `
            <tr>
                <td><input type="text" name="items[${rowIdx}][deskripsi]" class="form-control" required></td>
                <td><input type="number" name="items[${rowIdx}][jml_mhs]" class="form-control calc jml_mhs" value="1" required></td>
                <td><input type="number" name="items[${rowIdx}][jml_minggu]" class="form-control calc jml_minggu" value="1" required></td>
                <td><input type="number" name="items[${rowIdx}][harga_satuan]" class="form-control calc harga_satuan" required></td>
                <td><input type="number" name="items[${rowIdx}][subtotal]" class="form-control subtotal_val bg-light" readonly></td>
                <td><button type="button" class="btn btn-link text-danger removeRow p-0"><i class="bi bi-trash-fill h5"></i></button></td>
            </tr>`;
        document.querySelector('#itemTable tbody').insertAdjacentHTML('beforeend', tr);
        rowIdx++;
    });

    // 3. Hapus Baris
    document.addEventListener('click', function(e) {
        if (e.target.closest('.removeRow')) {
            e.target.closest('tr').remove();
            calculateAll();
        }
    });

    // 4. Kalkulasi Per Baris & Total
    function calculateAll() {
        let grandTotal = 0;
        const rows = document.querySelectorAll('#itemTable tbody tr');
        
        rows.forEach(row => {
            const mhs = parseFloat(row.querySelector('.jml_mhs').value) || 0;
            const mng = parseFloat(row.querySelector('.jml_minggu').value) || 0;
            const harga = parseFloat(row.querySelector('.harga_satuan').value) || 0;
            const subtotal = mhs * mng * harga;
            
            row.querySelector('.subtotal_val').value = subtotal;
            grandTotal += subtotal;
        });

        const konsumsi = parseFloat(document.getElementById('biaya_konsumsi').value) || 0;
        const totalAkhir = grandTotal + konsumsi;

        document.getElementById('display_total').innerText = totalAkhir.toLocaleString('id-ID');
        document.getElementById('hidden_total').value = totalAkhir;
    }

    // Listener Input
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('calc')) {
            calculateAll();
        }
    });

    // Trigger hitungan awal saat load
    calculateAll();
});
</script>
@endsection