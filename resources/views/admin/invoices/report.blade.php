@extends('layouts.app')

@section('content')
<style>
    :root {
        --custom-maroon: #7c1316;
        --custom-maroon-subtle: #fcf0f1;
        --card-radius: 16px;
        --shadow-soft: 0 10px 30px rgba(0, 0, 0, 0.04);
    }

    .page-header {
        background: #fff; border-radius: var(--card-radius); padding: 1.5rem 2rem;
        box-shadow: var(--shadow-soft); margin-bottom: 2rem; border-left: 6px solid var(--custom-maroon);
    }

    .stat-card {
        background: #fff; border-radius: var(--card-radius); padding: 1.5rem;
        border: none; box-shadow: var(--shadow-soft); transition: all 0.3s ease;
        height: 100%;
    }
    .stat-card:hover { transform: translateY(-5px); }
    .icon-box {
        width: 45px; height: 45px; border-radius: 12px; display: flex;
        align-items: center; justify-content: center; font-size: 1.2rem; margin-bottom: 1rem;
    }

    .custom-table-card {
        background: #fff; border-radius: var(--card-radius); box-shadow: var(--shadow-soft);
        border: none; overflow: hidden; margin-top: 2rem;
    }
    .table thead th {
        background-color: #fcfcfc; color: #8898aa; padding: 1.2rem 1rem;
        font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px;
    }

    .animate-up { animation: fadeInUp 0.6s ease forwards; opacity: 0; transform: translateY(20px); }
    @keyframes fadeInUp { to { opacity: 1; transform: translateY(0); } }
</style>

<div class="container-fluid py-4">
    
    {{-- HEADER --}}
   <div class="page-header d-flex justify-content-between align-items-center animate-up">
    <div>
        <h3 class="fw-bold mb-1" style="color: var(--custom-maroon);">Rekapitulasi Pendapatan</h3>
        <p class="text-muted small mb-0">Analisa keuangan SINDIKAT RSUD SLG</p>
    </div>
    <div class="d-flex gap-2">
        <button onclick="exportExcel()" class="btn btn-success fw-bold px-4">
            <i class="bi bi-file-earmark-excel me-2"></i> Ekspor Excel
        </button>
       
    </div>
</div>

    {{-- FILTER TANGGAL --}}
    <div class="card stat-card mb-4 animate-up" style="animation-delay: 0.1s;">
        <form method="GET" action="{{ route('admin.invoices.report') }}" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label small fw-bold">Dari Tanggal</label>
                <input type="date" name="start_date" class="form-control border-0 bg-light" value="{{ $startDate }}">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-bold">Sampai Tanggal</label>
                <input type="date" name="end_date" class="form-control border-0 bg-light" value="{{ $endDate }}">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn w-100 fw-bold" style="background: var(--custom-maroon); color:white;">
                    <i class="bi bi-filter me-2"></i> PROSES REKAP
                </button>
            </div>
        </form>
    </div>

    {{-- STATS BOX --}}
    <div class="row g-4 animate-up" style="animation-delay: 0.2s;">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="icon-box bg-primary bg-opacity-10 text-primary"><i class="bi bi-receipt"></i></div>
                <h6 class="text-muted small fw-bold text-uppercase">Total Billing Terbit</h6>
                <h4 class="fw-bold">Rp {{ number_format($totalBilling, 0, ',', '.') }}</h4>
                <small class="text-muted">Tagihan yang dikeluarkan</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="icon-box bg-success bg-opacity-10 text-success"><i class="bi bi-check-circle-fill"></i></div>
                <h6 class="text-muted small fw-bold text-uppercase">Billing Terbayar</h6>
                <h4 class="fw-bold">Rp {{ number_format($totalPaid, 0, ',', '.') }}</h4>
                <small class="text-success fw-bold">Uang aman / Lunas</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card border-start border-danger border-4">
                <div class="icon-box bg-danger bg-opacity-10 text-danger"><i class="bi bi-clock-history"></i></div>
                <h6 class="text-muted small fw-bold text-uppercase">Belum Terbayar</h6>
                <h4 class="fw-bold">Rp {{ number_format($totalUnpaid, 0, ',', '.') }}</h4>
                <small class="text-danger fw-bold">Piutang berjalan</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" style="background: var(--custom-maroon); color: white;">
                <div class="icon-box bg-white bg-opacity-20 text-white"><i class="bi bi-cash-stack"></i></div>
                <h6 class="text-white bg-opacity-75 small fw-bold text-uppercase">Billing Aktual</h6>
                <h4 class="fw-bold text-white">Rp {{ number_format($billingAktual, 0, ',', '.') }}</h4>
                <small class="text-white-50">Kas masuk periode ini</small>
            </div>
        </div>
    </div>

    {{-- DATA TABLE --}}
    <div class="custom-table-card animate-up" style="animation-delay: 0.3s;">
        <div class="p-4 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0">Rincian Transaksi</h5>
            <span class="badge bg-light text-dark border px-3 py-2">Periode: {{ date('d M Y', strtotime($startDate)) }} - {{ date('d M Y', strtotime($endDate)) }}</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="text-center">Tgl</th>
                        <th>No. Invoice</th>
                        <th>Instansi</th>
                        <th class="text-end">Jumlah Tagihan</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoices as $inv)
                    <tr>
                        <td class="text-center small">{{ date('d/m/y', strtotime($inv->tgl_invoice)) }}</td>
                        <td class="fw-bold small">{{ $inv->no_invoice }}</td>
                        <td class="small">{{ $inv->instansi }}</td>
                        <td class="text-end fw-bold">Rp {{ number_format($inv->jumlah_dibayarkan, 0, ',', '.') }}</td>
                        <td class="text-center">
                            @if($inv->status == 'Selesai')
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-3">LUNAS</span>
                            @elseif($inv->status == 'Batal')
                                <span class="badge bg-secondary-subtle text-secondary px-3">BATAL</span>
                            @else
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3">PENDING</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<script src="https://cdn.sheetjs.com/xlsx-0.20.2/package/dist/xlsx.full.min.js"></script>

<script>
function exportExcel() {
    // 1. Ambil data Statistik
    const tglAwal = "{{ $startDate }}";
    const tglAkhir = "{{ $endDate }}";
    const totalBilling = "{{ $totalBilling }}";
    const totalPaid = "{{ $totalPaid }}";
    const totalUnpaid = "{{ $totalUnpaid }}";
    const aktual = "{{ $billingAktual }}";

    // 2. Persiapkan Array Data untuk Excel
    const dataExcel = [
        ["LAPORAN REKAPITULASI PENDAPATAN SINDIKAT RSUD SLG"],
        ["Periode:", tglAwal + " s/d " + tglAkhir],
        ["Tanggal Ekspor:", new Date().toLocaleString('id-ID')],
        [""], // Baris Kosong
        ["RINGKASAN STATISTIK"],
        ["Kategori", "Jumlah (IDR)"],
        ["Total Billing Terbit", parseInt(totalBilling)],
        ["Billing Terbayar (Lunas)", parseInt(totalPaid)],
        ["Billing Belum Bayar (Piutang)", parseInt(totalUnpaid)],
        ["Billing Aktual (Uang Masuk)", parseInt(aktual)],
        [""], // Baris Kosong
        ["RINCIAN TRANSAKSI"],
        ["Tanggal", "No. Invoice", "Instansi", "Jumlah Tagihan", "Status"]
    ];

    // 3. Ambil data dari Tabel Rincian
    const tableRows = document.querySelectorAll("table tbody tr");
    tableRows.forEach(row => {
        const cols = row.querySelectorAll("td");
        if(cols.length > 0) {
            dataExcel.push([
                cols[0].innerText, // Tanggal
                cols[1].innerText, // No Invoice
                cols[2].innerText, // Instansi
                parseInt(cols[3].innerText.replace(/[^0-9]/g, '')), // Jumlah (convert ke number)
                cols[4].innerText.trim() // Status
            ]);
        }
    });

    // 4. Proses Pembuatan Workbook
    const worksheet = XLSX.utils.aoa_to_sheet(dataExcel);
    const workbook = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(workbook, worksheet, "Rekap Pendapatan");

    // Setting lebar kolom agar rapi
    worksheet['!cols'] = [
        { wch: 15 }, // Tanggal
        { wch: 25 }, // No Invoice
        { wch: 40 }, // Instansi
        { wch: 20 }, // Jumlah
        { wch: 15 }  // Status
    ];

    // 5. Download File
    const fileName = `Rekap_SINDIKAT_${tglAwal}_to_${tglAkhir}.xlsx`;
    XLSX.writeFile(workbook, fileName);
    
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: 'Laporan Excel berhasil diunduh.',
        timer: 2000,
        showConfirmButton: false
    });
}
</script>
@endsection