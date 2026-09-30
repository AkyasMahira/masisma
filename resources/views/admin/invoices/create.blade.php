@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

<style>
    :root {
        --custom-maroon: #7c1316;
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
            <h4 class="fw-bold mb-0 text-dark">Terbitkan Invoice Baru</h4>
            <small class="text-muted">Nomor Invoice: <span class="badge bg-dark">{{ $nextNo }}</span></small>
        </div>
        <a href="{{ route('admin.invoices.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <form action="{{ route('admin.invoices.store') }}" method="POST" id="invoiceForm" class="animate-up">
        @csrf
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
                            <option>Praktek Kerja Lapangan</option>
                            <option>Penelitian</option>
                            <option>Magang</option>
                            <option>Pelatihan</option>
                                 <option>Uji Kompetensi</option>
                            <option>Studi Banding</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold small">Jenjang</label>
                        <select class="form-select choices-single" name="jenjang">
                            <option>DIII</option><option>DIV</option><option>S1</option>
                            <option>S2</option><option>Profesi</option><option>SMK</option>
                            <option>-</option>
                        </select>
                    </div>
                <div class="col-md-3">
    <label class="form-label fw-bold small">Program Studi</label>
    
    <!-- Input dengan Datalist -->
    <input type="text" 
           name="prodi" 
           id="prodi" 
           class="form-control" 
           placeholder="KETIK PROGRAM STUDI..." 
           list="prodi-list" 
           required 
           style="text-transform: uppercase;" 
           oninput="this.value = this.value.toUpperCase()">

    <!-- Wadah Pilihan (Datalist) -->
    <datalist id="prodi-list">
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

                    {{-- BAGIAN DATALIST INSTANSI --}}
                    <div class="col-md-3">
                        <label class="form-label fw-bold small">Instansi (Data MoU)</label>
                        <input type="text" name="instansi" list="data_mou" class="form-control" placeholder="Pilih atau ketik instansi..." required>
                        <datalist id="data_mou">
                            @foreach($mous as $m)
                                <option value="{{ $m->nama_instansi }}">
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
                        <label class="form-label fw-bold small">Ruangan / CI</label>
                        <select name="ruangan_ci[]" class="form-select choices-multiple" multiple required>
                            @foreach($ruangans as $r)
                                <option value="{{ $r->nm_ruangan }}">{{ $r->nm_ruangan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">Tanggal Mulai</label>
                        <input type="date" class="form-control" name="tgl_mulai" id="tgl_mulai" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">Tanggal Berakhir</label>
                        <input type="date" class="form-control" name="tgl_akhir" id="tgl_akhir" required>
                    </div>
                </div>

                {{-- SECTION 3: TABEL RINCIAN --}}
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
                            <tr>
                                <td><input type="text" name="items[0][deskripsi]" class="form-control" value="Biaya Praktek Klinik / Magang" required></td>
                                <td><input type="number" name="items[0][jml_mhs]" class="form-control calc jml_mhs" value="1" required></td>
                                <td><input type="number" name="items[0][jml_minggu]" class="form-control calc jml_minggu" id="default_minggu" value="1" required></td>
                                <td><input type="number" name="items[0][harga_satuan]" class="form-control calc harga_satuan" placeholder="0" required></td>
                                <td><input type="number" name="items[0][subtotal]" class="form-control subtotal_val bg-light" readonly></td>
                                <td></td>
                            </tr>
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
                            <input type="number" class="form-control calc" name="biaya_konsumsi" id="biaya_konsumsi" value="0">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="total-display-box h-100 d-flex flex-column justify-content-center">
                            <h6 class="text-uppercase fw-bold text-muted small mb-1">Total Penagihan Akhir</h6>
                            <h2 class="fw-bold mb-0" style="color: var(--custom-maroon);">Rp <span id="display_total">0</span></h2>
                            <input type="hidden" name="jumlah_dibayarkan" id="hidden_total">
                        </div>
                    </div>
                </div>

                {{-- SECTION 4: ADMINISTRASI --}}
                <div class="form-section-title">
                    <i class="bi bi-person-check-fill"></i> PENGESAHAN <hr>
                </div>
                <div class="row g-4">
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">Jatuh Tempo (+7 Hari)</label>
                        <input type="date" class="form-control" name="tgl_jatuh_tempo" value="{{ date('Y-m-d', strtotime('+7 days')) }}">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label fw-bold small">Penanggung Jawab / Jabatan</label>
                        <input type="text" class="form-control" name="penanggung_jawab" placeholder="Misal: Bendahara Penerimaan / Kasir Diklat" required>
                    </div>
                </div>

                <div class="mt-5 text-end border-top pt-4">
                    <button type="submit" class="btn btn-dark px-5 py-3 fw-bold shadow-sm" style="background: var(--custom-maroon); border:none; border-radius: 10px;">
                        <i class="bi bi-send-check me-2"></i> SIMPAN & PROSES TTD
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Initialize Choices.js (Hanya untuk class yang ditentukan)
    const singleSelects = document.querySelectorAll('.choices-single');
    singleSelects.forEach(el => new Choices(el, { searchEnabled: true, itemSelectText: '', shouldSort: false }));
    
    const multiSelect = document.querySelector('.choices-multiple');
    new Choices(multiSelect, { removeItemButton: true, searchEnabled: true });

    // Script instansi-select lama DIHAPUS karena sudah pakai datalist native

    let rowIdx = 1;

    // 2. Fungsi Hitung Minggu
    function updateDefaultWeeks() {
        const t1 = document.getElementById('tgl_mulai').value;
        const t2 = document.getElementById('tgl_akhir').value;
        if (t1 && t2) {
            const start = new Date(t1);
            const end = new Date(t2);
            if (end >= start) {
                const diffTime = Math.abs(end - start);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                const weeks = Math.ceil(diffDays / 7);
                document.querySelectorAll('.jml_minggu').forEach(el => el.value = weeks);
                calculateAll();
            }
        }
    }

    // 3. Tambah Baris
    document.getElementById('addRow').addEventListener('click', function() {
        const currentWeeks = document.getElementById('default_minggu').value;
        const tr = `
            <tr>
                <td><input type="text" name="items[${rowIdx}][deskripsi]" class="form-control" required></td>
                <td><input type="number" name="items[${rowIdx}][jml_mhs]" class="form-control calc jml_mhs" value="1" required></td>
                <td><input type="number" name="items[${rowIdx}][jml_minggu]" class="form-control calc jml_minggu" value="${currentWeeks}" required></td>
                <td><input type="number" name="items[${rowIdx}][harga_satuan]" class="form-control calc harga_satuan" required></td>
                <td><input type="number" name="items[${rowIdx}][subtotal]" class="form-control subtotal_val bg-light" readonly></td>
                <td><button type="button" class="btn btn-link text-danger removeRow p-0"><i class="bi bi-trash-fill h5"></i></button></td>
            </tr>`;
        document.querySelector('#itemTable tbody').insertAdjacentHTML('beforeend', tr);
        rowIdx++;
    });

    // 4. Hapus Baris
    document.addEventListener('click', function(e) {
        if (e.target.closest('.removeRow')) {
            e.target.closest('tr').remove();
            calculateAll();
        }
    });

    // 5. Kalkulasi
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

    document.getElementById('tgl_mulai').addEventListener('change', updateDefaultWeeks);
    document.getElementById('tgl_akhir').addEventListener('change', updateDefaultWeeks);
    
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('calc')) {
            calculateAll();
        }
    });
});
</script>
@endsection