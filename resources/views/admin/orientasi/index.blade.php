@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    :root { 
        --maroon-slg: #7c1316; 
        --maroon-light: #a3191d;
        --bg-light: #f4f6f9;
        --filter-header: #fdf5f5;
    }

    body { background-color: var(--bg-light); }

    /* Header Section - Konsisten dengan Modul SINDIKAT */
    .header-card {
        background: white;
        border-radius: 8px;
        border-left: 5px solid var(--maroon-slg);
        padding: 20px;
        margin-bottom: 25px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    /* Filter Section - Matching pattern "Daftar Surat Balasan" */
    .filter-section {
        background: white;
        border-radius: 8px;
        overflow: hidden;
        margin-bottom: 25px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    .filter-header {
        background-color: var(--filter-header);
        padding: 12px 20px;
        color: var(--maroon-slg);
        font-weight: 600;
        border-bottom: 1px solid #f5dada;
    }
    .filter-body { padding: 20px; }
    
    .btn-maroon {
        background-color: var(--maroon-slg);
        color: white;
        border-radius: 5px;
        padding: 8px 25px;
        font-weight: 600;
        border: none;
    }
    .btn-maroon:hover { background-color: var(--maroon-light); color: white; }

    /* Table Styling - Bold Industrial */
    .table-container {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        background: white;
    }
    .table thead th {
        background-color: var(--maroon-slg) !important;
        color: white !important;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 1px;
        padding: 15px;
        border: none;
    }
    .table tbody td {
        padding: 15px;
        vertical-align: middle;
        border-bottom: 1px solid #f0f0f0;
    }

    /* Status Badge Style */
    .badge-status {
        font-size: 0.75rem;
        padding: 6px 15px;
        border-radius: 50px;
        font-weight: 700;
        text-transform: uppercase;
    }

    /* Manual Pagination Styling */
    .pagination-manual {
        display: flex; justify-content: center; list-style: none; padding: 0; gap: 5px;
    }
    .pagination-manual li a, .pagination-manual li span {
        padding: 8px 15px; border: 1px solid #ddd; background: white;
        color: var(--maroon-slg); text-decoration: none; border-radius: 5px; font-weight: 600;
    }
    .pagination-manual li.active span { background: var(--maroon-slg); color: white; border-color: var(--maroon-slg); }
    .pagination-manual li.disabled span { color: #ccc; cursor: not-allowed; }

    .btn-outline-secondary-custom {
        border-radius: 50px;
        padding: 8px 20px;
        font-weight: 600;
        border: 1.5px solid #ddd;
        color: #666;
    }
</style>

<div class="container-fluid py-4">
    <div class="header-card d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-bold mb-0 text-dark">Monitoring Orientasi</h4>
            <small class="text-muted">Pantau hasil pre-test, post-test, dan kelulusan peserta magang RSUD SLG.</small>
        </div>
        <button class="btn btn-outline-secondary-custom shadow-sm" onclick="window.location.reload()">
            <i class="fas fa-sync-alt me-1"></i> Refresh Data
        </button>
    </div>

    <div class="filter-section">
        <div class="filter-header">
            <i class="fas fa-filter me-2"></i> Filter & Pencarian Data
        </div>
        <div class="filter-body">
            <form action="{{ route('admin.orientasi.index') }}" method="GET">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted">CARI PESERTA / INSTANSI</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control border-start-0" placeholder="Nama, Email, Kampus..." value="{{ request('search') }}">
                        </div>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label small fw-bold text-muted">GELOMBANG</label>
                        <select name="gelombang" class="form-select">
                            <option value="">Semua</option>
                            @for($i=1; $i<=10; $i++)
                                <option value="{{ $i }}" {{ request('gelombang') == $i ? 'selected' : '' }}>Gelombang {{ $i }}</option>
                            @endfor
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label small fw-bold text-muted">TAHUN</label>
                        <input type="number" name="tahun" class="form-control" placeholder="2026" value="{{ request('tahun') }}">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label small fw-bold text-muted">DARI TANGGAL</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label small fw-bold text-muted">SAMPAI TANGGAL</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>

                  <div class="col-md-1 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-maroon shadow-sm" title="Cari Data">
                        <i class="fas fa-search"></i>
                    </button>
                    
                    <button type="button" id="btnExport" onclick="exportToExcel()" class="btn btn-success shadow-sm" title="Export Excel">
                        <i class="fas fa-file-excel"></i>
                    </button>
                </div>
                </div>
            </form>
        </div>
    </div>

    <div class="table-container shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="text-center" width="5%">NO</th>
                        <th class="px-4">PESERTA & INSTANSI</th>
                        <th class="text-center">PERIODE MAGANG</th>
                        <th class="text-center">NILAI ORIENTASI</th>
                        <th class="text-center">STATUS</th>
                        <th class="text-center" width="12%">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($results as $res)
                    @php
                        // FALLBACK: Jika data lama (mahasiswa_id kosong), cari data mahasiswa terbarunya
                        $mhs = $res->mahasiswa ?? \App\Models\Mahasiswa::with('mou')->where('user_id', $res->user_id)->latest()->first();
                        
                        // Cek Instansi dari relasi Mahasiswa->Mou atau User->Mou
                        $mou = $mhs ? $mhs->mou : ($res->user ? $res->user->mou : null);
                        $univ = $mou ? ($mou->nama_instansi ?? $mou->nama_universitas) : 'Peserta Umum / Lupa Instansi';
                        $nama = $mhs ? $mhs->nm_mahasiswa : ($res->user->name ?? 'User Terhapus');
                    @endphp
                    <tr>
                        <td class="text-center fw-bold text-muted">
                            {{ ($results->currentPage() - 1) * $results->perPage() + $loop->iteration }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="d-flex align-items-center">
                                <div class="avatar me-3 bg-light text-maroon rounded-circle d-flex align-items-center justify-content-center fw-bold border" style="width: 45px; height: 45px;">
                                    {{ substr($nama, 0, 1) }}
                                </div>
                                <div>
                                    <div class="fw-bold text-dark mb-1">{{ $nama }}</div>
                                    <div class="small text-muted mb-1"><i class="fas fa-university me-1"></i> {{ $univ }}</div>
                                    <div class="small text-muted"><i class="fas fa-graduation-cap me-1"></i> {{ $mhs->prodi ?? '-' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-light text-dark border mb-2">{{ strtoupper($mhs->tipe_mahasiswa ?? 'MAGANG') }}</span><br>
                            <small class="text-muted fw-bold" style="font-size: 0.75rem;">
                                @if($mhs && $mhs->tanggal_mulai)
                                    {{ \Carbon\Carbon::parse($mhs->tanggal_mulai)->format('d/m/Y') }}<br>
                                    s/d<br>
                                    {{ \Carbon\Carbon::parse($mhs->tanggal_berakhir)->format('d/m/Y') }}
                                @else
                                    -
                                @endif
                            </small>
                        </td>
                        <td class="text-center">
                            <div class="small fw-bold mb-1">
                                PRE: 
                                <span class="{{ ($res->pre_test_score ?? 0) >= 80 ? 'text-success' : 'text-dark' }}">
                                    {{ $res->pre_test_score ?? '-' }}
                                </span>
                            </div>
                            <div class="small fw-bold">
                                POST: 
                                @if($res->post_test_score !== null)
                                    <span class="{{ $res->post_test_score >= 80 ? 'text-success' : 'text-danger' }}">
                                        {{ $res->post_test_score }}
                                    </span>
                                @else
                                    <span class="text-muted fw-normal">Belum</span>
                                @endif
                            </div>
                        </td>
                        <td class="text-center">
                            @if($res->status == 'lulus_orientasi')
                                <span class="badge-status bg-success bg-opacity-10 text-success border border-success">
                                    <i class="fas fa-check-circle me-1"></i> LULUS
                                </span>
                            @elseif($res->status == 'siap_post_test')
                                <span class="badge-status bg-warning bg-opacity-10 text-warning border border-warning">
                                    <i class="fas fa-spinner fa-spin me-1"></i> PROSES
                                </span>
                            @else
                                <span class="badge-status bg-secondary bg-opacity-10 text-secondary border border-secondary">
                                    <i class="fas fa-clock me-1"></i> AWAL
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                {{-- PERBAIKAN: Gunakan $res->id bukan $res->user_id untuk melihat sertifikat yang tepat --}}
                                @if($res->status == 'lulus_orientasi')
                                    <a href="{{ route('admin.orientasi.sertifikat_user', $res->id) }}" target="_blank" class="btn btn-sm btn-light border text-primary rounded-pill px-3 fw-bold" title="Lihat Sertifikat">
                                        <i class="fas fa-award"></i>
                                    </a>
                                @endif

                                <form action="{{ route('admin.orientasi.destroy', $res->id) }}" method="POST" onsubmit="return confirm('Reset data orientasi peserta ini? Semua nilai akan dihapus permanent.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light border text-danger rounded-pill px-3 fw-bold" title="Reset Nilai">
                                        <i class="fas fa-undo-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i class="fas fa-clipboard-list fa-3x text-muted mb-3 opacity-25"></i>
                            <h6 class="text-muted fw-bold">Data orientasi tidak ditemukan</h6>
                            <p class="small text-muted">Coba ubah filter pencarian Anda.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($results->hasPages())
    <div class="mt-4 d-flex justify-content-between align-items-center">
        <div class="small text-muted fw-bold">
            Menampilkan {{ $results->firstItem() ?? 0 }} - {{ $results->lastItem() ?? 0 }} dari {{ $results->total() }} data
        </div>
        <ul class="pagination-manual">
            @if ($results->onFirstPage())
                <li class="disabled"><span><i class="fas fa-chevron-left"></i></span></li>
            @else
                <li><a href="{{ $results->previousPageUrl() }}"><i class="fas fa-chevron-left"></i></a></li>
            @endif

            @foreach ($results->getUrlRange(max(1, $results->currentPage() - 2), min($results->lastPage(), $results->currentPage() + 2)) as $page => $url)
                @if ($page == $results->currentPage())
                    <li class="active"><span>{{ $page }}</span></li>
                @else
                    <li><a href="{{ $url }}">{{ $page }}</a></li>
                @endif
            @endforeach

            @if ($results->hasMorePages())
                <li><a href="{{ $results->nextPageUrl() }}"><i class="fas fa-chevron-right"></i></a></li>
            @else
                <li class="disabled"><span><i class="fas fa-chevron-right"></i></span></li>
            @endif
        </ul>
    </div>
    @endif
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
async function exportToExcel() {
    const btn = document.getElementById('btnExport');
    const originalText = btn.innerHTML;
    
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    btn.disabled = true;

    try {
        const urlParams = new URLSearchParams(window.location.search);
        const apiUrl = "{{ route('admin.orientasi.api') }}?" + urlParams.toString();

        const response = await fetch(apiUrl);
        const data = await response.json();

        if (data.length === 0) {
            alert('Tidak ada data untuk diexport');
            return;
        }

        const worksheet = XLSX.utils.json_to_sheet(data);
        const workbook = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(workbook, worksheet, "Data Orientasi");

        XLSX.writeFile(workbook, "Data_Orientasi_Full.xlsx");

    } catch (error) {
        console.error(error);
        alert('Gagal mendownload data');
    } finally {
        btn.innerHTML = originalText;
        btn.disabled = false;
    }
}
</script>
@endsection