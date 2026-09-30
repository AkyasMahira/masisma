@extends('layouts.app')

@section('title', 'Daftar Presentasi')
@section('page-title', 'Daftar Presentasi')

@section('content')
    {{-- 1. PEMBUNGKUS UTAMA (Wajib ada agar layout tidak berantakan) --}}
    <div class="container-fluid py-4">

        <style>
            :root {
                --custom-maroon: #7c1316;
                --custom-maroon-light: #a3191d;
                --custom-maroon-subtle: #fcf0f1;
                --text-dark: #2c3e50;
                --text-muted: #64748b;
                --card-radius: 16px;
                --shadow-soft: 0 4px 20px rgba(0, 0, 0, 0.05);
                --transition: 0.3s ease;
            }

            /* --- Header & Filter --- */
            .page-header-wrapper {
                background: #fff;
                border-radius: var(--card-radius);
                padding: 1.5rem;
                box-shadow: var(--shadow-soft);
                margin-bottom: 2rem;
                border-left: 5px solid var(--custom-maroon);
                display: flex;
                justify-content: space-between;
                align-items: center;
                flex-wrap: wrap;
                gap: 1rem;
            }

            .filter-card {
                background: #fff;
                border-radius: var(--card-radius);
                box-shadow: var(--shadow-soft);
                margin-bottom: 1.5rem;
                border: 1px solid #f0f0f0;
            }

            .filter-body {
                padding: 1.5rem;
            }

            /* --- Table Styling --- */
            .custom-table-card {
                background: #fff;
                border-radius: var(--card-radius);
                box-shadow: var(--shadow-soft);
                overflow: hidden;
                border: none;
            }

            .table thead th {
                background-color: var(--custom-maroon);
                color: white;
                border: none;
                padding: 1rem;
                font-weight: 600;
                text-transform: uppercase;
                font-size: 0.75rem;
                letter-spacing: 0.5px;
                vertical-align: middle;
                white-space: nowrap;
            }

            .table tbody td {
                padding: 1rem;
                vertical-align: middle;
                color: #475569;
                border-bottom: 1px solid #f1f5f9;
                font-size: 0.9rem;
            }

            .table-hover tbody tr:hover {
                background-color: #fff5f6;
            }

            /* --- Badges --- */
            .badge-soft {
                padding: 5px 10px;
                border-radius: 6px;
                font-weight: 600;
                font-size: 0.75rem;
                display: inline-flex;
                align-items: center;
                gap: 4px;
                white-space: nowrap;
            }

            .bg-soft-success { background-color: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
            .bg-soft-warning { background-color: #fef9c3; color: #854d0e; border: 1px solid #fde047; }
            .bg-soft-danger { background-color: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
            .bg-soft-info { background-color: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; }
            .bg-soft-secondary { background-color: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }

            /* --- Action Buttons --- */
            .action-btn {
                width: 32px; height: 32px; border-radius: 8px;
                display: inline-flex; align-items: center; justify-content: center;
                transition: var(--transition); border: 1px solid transparent;
                color: #64748b; background: white; border-color: #e2e8f0;
                text-decoration: none;
            }

            .action-btn:hover { background: var(--custom-maroon-subtle); color: var(--custom-maroon); border-color: var(--custom-maroon-light); }
            
            /* Specific Action Colors */
            .action-btn.ppt:hover { background: #ecfdf5; color: #059669; border-color: #10b981; }
            .action-btn.doc:hover { background: #eff6ff; color: #2563eb; border-color: #3b82f6; }

            /* --- Inputs --- */
            .input-group-text { background-color: #f8f9fa; color: var(--custom-maroon); border-right: none; }
            .form-control, .form-select { border-left: none; box-shadow: none; border-color: #e2e8f0; }
            .form-control:focus, .form-select:focus { border-color: var(--custom-maroon-light); }

            .btn-maroon {
                background-color: var(--custom-maroon); color: #fff; border: none;
                border-radius: 8px; padding: 0.6rem 1.5rem; font-weight: 600; transition: var(--transition);
            }
            .btn-maroon:hover { background-color: var(--custom-maroon-light); color: white; transform: translateY(-2px); }

            .btn-outline-custom {
                border: 1px solid #e2e8f0; color: var(--text-dark); background: white;
                border-radius: 8px; padding: 0.6rem 1.2rem; font-weight: 500; transition: var(--transition);
            }
            .btn-outline-custom:hover { background: #f8f9fa; border-color: #cbd5e1; }

            .animate-up { animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards; opacity: 0; transform: translateY(20px); }
            @keyframes fadeInUp { to { opacity: 1; transform: translateY(0); } }
        </style>

        <!-- Header Section -->
      <!-- Header Section -->
<div class="page-header-wrapper animate-up">
    <div>
        <h4 class="fw-bold mb-1" style="color: var(--custom-maroon);">Daftar Presentasi</h4>
        <small class="text-muted">Kelola jadwal, file, dan penilaian presentasi mahasiswa.</small>
    </div>
    <!-- Tambahkan Tombol Sinkronisasi Disini -->
    <div class="d-flex gap-2">
        <button type="button" onclick="syncToPerpustakaan()" id="btnSync" class="btn btn-maroon d-flex align-items-center gap-2">
            <i class="bi bi-cloud-arrow-up-fill"></i>
            <span>Sinkronisasi ke Perpustakaan</span>
        </button>
    </div>
</div>

        <!-- Alert Messages -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show animate-up shadow-sm border-0" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Filter & Search -->
        <div class="filter-card animate-up" style="animation-delay: 0.1s;">
            <div class="filter-body">
                <form method="GET" action="{{ route('admin.presentasi.index') }}">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="small text-muted fw-bold text-uppercase mb-1">Cari Mahasiswa / Judul</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" name="search" class="form-control" placeholder="Ketik pencarian..."
                                    value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="small text-muted fw-bold text-uppercase mb-1">Status Nilai</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-trophy"></i></span>
                                <select name="status_nilai" class="form-select">
                                    <option value="">Semua</option>
                                    <option value="sudah_dinilai"
                                        {{ request('status_nilai') == 'sudah_dinilai' ? 'selected' : '' }}>Sudah Dinilai
                                    </option>
                                    <option value="belum_dinilai"
                                        {{ request('status_nilai') == 'belum_dinilai' ? 'selected' : '' }}>Belum Dinilai
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="small text-muted fw-bold text-uppercase mb-1">Status Final</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-flag"></i></span>
                                <select name="status_final" class="form-select">
                                    <option value="">Semua</option>
                                    <option value="selesai" {{ request('status_final') == 'selesai' ? 'selected' : '' }}>
                                        Selesai</option>
                                    <option value="proses" {{ request('status_final') == 'proses' ? 'selected' : '' }}>
                                        Proses</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-maroon w-100">Filter</button>
                                <a href="{{ route('admin.presentasi.index') }}" class="btn btn-outline-custom"
                                    title="Reset"><i class="bi bi-arrow-counterclockwise"></i></a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Table Section -->
        <div class="custom-table-card animate-up" style="animation-delay: 0.2s;">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th class="text-center" width="5%">No</th>
                            <th>Mahasiswa & Judul</th>
                            <th>Jadwal Presentasi</th>
                            <th class="text-center">Berkas</th>
                            <th class="text-center">Nilai</th>
                            <th class="text-center">Status Laporan</th>
                            <th class="text-center">Final</th>
                            <th class="text-center" width="10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($presentasi as $p)
                            <tr>
                                <td class="text-center text-muted fw-bold">
                                    {{ $loop->iteration + ($presentasi->currentPage() - 1) * $presentasi->perPage() }}
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold text-dark">{{ $p->user->name }}</span>
                                        <small class="text-muted mb-1">{{ $p->user->email }}</small>
                                        <span class="small fst-italic text-primary text-truncate" style="max-width: 250px;"
                                            title="{{ $p->praPenelitian->judul ?? '-' }}">
                                            "{{ $p->praPenelitian->judul ?? '-' }}"
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold text-dark">
                                            <i class="bi bi-calendar-event me-1 text-muted"></i>
                                            {{ $p->tanggal_presentasi->format('d M Y') }}
                                        </span>
                                        <small class="text-muted">
                                            <i class="bi bi-clock me-1"></i>
                                            {{ $p->waktu_mulai }} - {{ $p->waktu_selesai }}
                                        </small>
                                    </div>
                                </td>
                                <td class="text-center">
                                    @if ($p->file_ppt)
                                        <span class="badge-soft bg-soft-success" title="PPT Uploaded">
                                            <i class="bi bi-file-earmark-slides"></i> PPT OK
                                        </span>
                                    @else
                                        <span class="badge-soft bg-soft-warning" title="Menunggu Upload">
                                            <i class="bi bi-hourglass"></i> Menunggu
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($p->nilai)
                                        @php
                                            $gradeColor = 'bg-soft-secondary';
                                            if (in_array($p->nilai, ['A', 'B'])) {
                                                $gradeColor = 'bg-soft-success';
                                            } elseif ($p->nilai == 'C') {
                                                $gradeColor = 'bg-soft-warning';
                                            } else {
                                                $gradeColor = 'bg-soft-danger';
                                            }
                                        @endphp
                                        <span class="badge-soft {{ $gradeColor }}"
                                            style="font-size: 0.9rem;">{{ $p->nilai }}</span>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($p->file_laporan)
                                        @if ($p->status_laporan == 'approved')
                                            <span class="badge-soft bg-soft-success">Disetujui</span>
                                        @elseif ($p->status_laporan == 'revisi')
                                            <span class="badge-soft bg-soft-warning">Revisi</span>
                                        @else
                                            <span class="badge-soft bg-soft-info">Review</span>
                                        @endif
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($p->status_final == 'selesai')
                                        <span class="badge-soft bg-soft-success"><i class="bi bi-patch-check-fill me-1"></i>
                                            Selesai</span>
                                    @elseif ($p->status_final == 'ditolak')
                                        <span class="badge-soft bg-soft-danger"><i class="bi bi-x-circle-fill me-1"></i>
                                            Ditolak</span>
                                    @else
                                        <span class="badge-soft bg-soft-secondary">Proses</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="{{ route('admin.presentasi.detail', $p->id) }}" class="action-btn"
                                            title="Detail & Penilaian">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>

                                        {{-- Shortcut Download PPT --}}
                                        @if ($p->file_ppt)
                                            <a href="{{ asset('storage/' . $p->file_ppt) }}" target="_blank"
                                                class="action-btn ppt" title="Lihat PPT">
                                                <i class="bi bi-file-earmark-slides"></i>
                                            </a>
                                        @endif

                                        {{-- Shortcut Download Laporan --}}
                                        @if ($p->file_laporan)
                                            <a href="{{ asset('storage/' . $p->file_laporan) }}" target="_blank"
                                                class="action-btn doc" title="Lihat Laporan">
                                                <i class="bi bi-file-earmark-text"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="bi bi-clipboard-x display-4 text-muted mb-3 opacity-50"></i>
                                        <h5 class="text-muted fw-bold">Belum ada data presentasi</h5>
                                        <p class="text-muted small">Data akan muncul setelah jadwal diatur.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4 animate-up" style="animation-delay: 0.3s;">
            {{ $presentasi->withQueryString()->links('pagination.custom') }}
        </div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function syncToPerpustakaan() {
    const btn = document.getElementById('btnSync');
    
    Swal.fire({
        title: 'Mulai Sinkronisasi?',
        text: "Sistem akan mengirim laporan ke Perpustakaan",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#7c1316',
        confirmButtonText: 'Ya, Jalankan'
    }).then((result) => {
        if (result.isConfirmed) {
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Memproses...';

            fetch("{{ route('admin.sync.perpustakaan') }}")
                .then(response => response.json())
                .then(data => {
                    // Susun pesan laporan dalam bentuk tabel HTML
                    let logHtml = `
                        <div style="max-height: 300px; overflow-y: auto;">
                            <table class="table table-sm small text-start">
                                <thead>
                                    <tr>
                                        <th>Judul Laporan</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;

                    data.detail.forEach(item => {
                        let badgeClass = item.status === 'Berhasil' ? 'text-success' : 'text-danger';
                        let infoText = item.info === 'Created' ? '<span class="badge bg-success">Baru</span>' : 
                                      (item.info === 'Updated' ? '<span class="badge bg-info">Update</span>' : item.info);

                        logHtml += `
                            <tr>
                                <td style="font-size: 11px;">${item.judul}</td>
                                <td><b class="${badgeClass}">${infoText}</b></td>
                            </tr>
                        `;
                    });

                    logHtml += `</tbody></table></div>`;

                    Swal.fire({
                        title: 'Sinkronisasi Selesai',
                        html: logHtml,
                        icon: 'info',
                        width: '600px',
                        confirmButtonColor: '#7c1316'
                    });
                })
                .catch(error => {
                    Swal.fire('Error', 'Gagal terhubung ke server.', 'error');
                })
                .finally(() => {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="bi bi-cloud-arrow-up-fill"></i> Sinkronisasi ke Perpustakaan';
                });
        }
    });
}
</script>

    </div> {{-- Penutup Container Utama --}}
@endsection