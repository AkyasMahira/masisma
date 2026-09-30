@extends('layouts.app')

@section('content')
<!-- Include Choices CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />

<style>
    :root {
        --custom-maroon: #7c1316;
        --custom-maroon-light: #a3191d;
        --custom-maroon-subtle: #fcf0f1;
        --text-dark: #2c3e50;
        --text-muted: #95a5a6;
        --card-radius: 16px;
        --shadow-soft: 0 4px 20px rgba(0, 0, 0, 0.05);
        --transition: 0.3s ease;
    }

    /* --- Mini Dashboard Stats --- */
    .stat-card {
        background: white;
        border-radius: var(--card-radius);
        padding: 1.5rem;
        box-shadow: var(--shadow-soft);
        border: 1px solid #f0f0f0;
        transition: var(--transition);
        height: 100%;
    }
    .stat-card:hover { transform: translateY(-5px); }
    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 1rem;
    }

    /* --- Header & Filter --- */
    .header-box {
        background: white;
        border-radius: 12px;
        border-left: 5px solid var(--custom-maroon);
        padding: 20px;
        margin-bottom: 25px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        position: relative;
        z-index: 1050; /* Mencegah dropdown tertutup card di bawahnya */
    }

    .filter-card {
        background: #fff;
        border-radius: var(--card-radius);
        box-shadow: var(--shadow-soft);
        margin-bottom: 1.5rem;
        border: 1px solid #f0f0f0;
        overflow: hidden;
    }

    .filter-header {
        background: var(--custom-maroon-subtle);
        color: var(--custom-maroon);
        padding: 1rem 1.5rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .form-label { font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.4rem; }
    .form-control, .form-select { border-radius: 8px; border: 1px solid #e2e8f0; }

    .btn-maroon { background-color: var(--custom-maroon); color: #fff; border-radius: 8px; transition: var(--transition); border: none; padding: 0.6rem 1.5rem; }
    .btn-maroon:hover { background-color: var(--custom-maroon-light); color: white; }
    
    .btn-outline-custom { border: 1px solid #e2e8f0; color: var(--text-dark); background: white; border-radius: 8px; padding: 0.6rem 1.2rem; transition: var(--transition); }

    .custom-table-card { background: #fff; border-radius: var(--card-radius); box-shadow: var(--shadow-soft); overflow: hidden; border: none; }
    .table thead th { background-color: var(--custom-maroon); color: white; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px; padding: 1rem; }
    
    .badge-pill-soft { border-radius: 50px; padding: 5px 12px; font-weight: 600; font-size: 0.7rem; }
    .bg-soft-success { background-color: #dcfce7; color: #166534; }
    .bg-soft-secondary { background-color: #f1f5f9; color: #475569; }

    .animate-up { animation: fadeInUp 0.5s ease forwards; opacity: 0; transform: translateY(20px); }
    @keyframes fadeInUp { to { opacity: 1; transform: translateY(0); } }

    /* Choices JS Override untuk Modal */
    .choices[data-type*='select-one'] {
        margin-bottom: 0;
    }
    .choices__inner {
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        background-color: #fff;
    }
</style>

<div class="container-fluid py-4">
    <div class="header-box d-flex justify-content-between align-items-center animate-up">
        <div>
            <h4 class="fw-bold mb-0 text-dark">Riwayat Absensi</h4>
            <small class="text-muted">Pantau aktivitas masuk dan keluar mahasiswa secara real-time.</small>
        </div>
        
        <!-- DROPDOWN EXPORT -->
        <div class="dropdown">
            <button class="btn btn-outline-custom shadow-sm dropdown-toggle d-flex align-items-center gap-2" type="button" id="exportMenu" data-bs-toggle="dropdown" data-bs-display="static" data-bs-boundary="window" aria-expanded="false">
                <i class="bi bi-file-earmark-excel text-success"></i> Export Data
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="exportMenu">
                <li><a class="dropdown-item py-2" href="#" data-bs-toggle="modal" data-bs-target="#modalExportPresensi"><i class="bi bi-calendar-check text-primary me-2"></i> Rekap Presensi Kampus</a></li>
                <li><a class="dropdown-item py-2" href="#" data-bs-toggle="modal" data-bs-target="#modalExportNilai"><i class="bi bi-award text-warning me-2"></i> Rekap Nilai Akhir</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><button class="dropdown-item py-2 text-muted" onclick="exportCurrentTable()"><i class="bi bi-table me-2"></i> Export Tabel Saat Ini</button></li>
            </ul>
        </div>
    </div>

    <div class="row g-3 mb-4 animate-up" style="animation-delay: 0.1s;">
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon bg-soft-secondary"><i class="bi bi-people-fill text-secondary"></i></div>
                <h6 class="text-muted mb-1">Total Absensi Hari Ini</h6>
                <h3 class="fw-bold mb-0">{{ $stats['total_today'] }}</h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card" style="border-bottom: 4px solid #198754;">
                <div class="stat-icon bg-soft-success"><i class="bi bi-box-arrow-in-right text-success"></i></div>
                <h6 class="text-muted mb-1">Mahasiswa Masuk</h6>
                <h3 class="fw-bold mb-0 text-success">{{ $stats['masuk_today'] }}</h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card" style="border-bottom: 4px solid #6c757d;">
                <div class="stat-icon bg-soft-secondary"><i class="bi bi-box-arrow-left text-muted"></i></div>
                <h6 class="text-muted mb-1">Mahasiswa Keluar</h6>
                <h3 class="fw-bold mb-0 text-muted">{{ $stats['keluar_today'] }}</h3>
            </div>
        </div>
    </div>

    <div class="filter-card animate-up" style="animation-delay: 0.2s;">
        <div class="filter-header">
            <i class="bi bi-funnel-fill"></i> Panel Pencarian & Filter
        </div>
        <div class="card-body p-4">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Cari Mahasiswa / Kampus</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Ketik nama atau asal kampus..." value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Ruangan</label>
                    <select name="ruangan_id" class="form-select">
                        <option value="">Semua</option>
                        @foreach ($ruangans as $r)
                            <option value="{{ $r->id }}" {{ request('ruangan_id') == $r->id ? 'selected' : '' }}>{{ $r->nm_ruangan }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Tipe</label>
                    <select name="type" class="form-select">
                        <option value="">Semua</option>
                        <option value="masuk" {{ request('type') == 'masuk' ? 'selected' : '' }}>Masuk</option>
                        <option value="keluar" {{ request('type') == 'keluar' ? 'selected' : '' }}>Keluar</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Tanggal</label>
                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                </div>

                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-maroon w-100 shadow-sm">Filter</button>
                    <a href="{{ route('absensi.index') }}" class="btn btn-outline-custom shadow-sm"><i class="bi bi-arrow-counterclockwise"></i></a>
                </div>
            </form>
        </div>
    </div>

    <div class="custom-table-card animate-up" style="animation-delay: 0.3s;">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="absensiTable">
                <thead>
                    <tr>
                        <th class="text-center" width="5%">No</th>
                        <th>Waktu</th>
                        <th>Mahasiswa & Instansi</th>
                        <th>Ruangan</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($absensi as $a)
                        <tr>
                            <td class="text-center text-muted fw-bold small">
                                {{ $loop->iteration + ($absensi->currentPage() - 1) * $absensi->perPage() }}
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold text-dark">{{ $a->created_at->format('H:i') }} WIB</span>
                                    <small class="text-muted" style="font-size: 0.75rem;">{{ $a->created_at->format('d M Y') }}</small>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold text-dark">{{ $a->mahasiswa->nm_mahasiswa ?? '-' }}</span>
                                    <small class="text-maroon fw-medium" style="font-size: 0.75rem;">
                                        <i class="bi bi-building me-1"></i>{{ $a->mahasiswa->univ_asal ?? $a->mahasiswa->mou->nama_instansi ?? 'Umum' }}
                                    </small>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border px-2 py-1 fw-normal">
                                    <i class="bi bi-geo-alt me-1 text-danger"></i>{{ $a->mahasiswa->ruangan->nm_ruangan ?? '-' }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if ($a->type === 'masuk')
                                    <span class="badge badge-pill-soft bg-soft-success">
                                        <i class="bi bi-box-arrow-in-right"></i> MASUK
                                    </span>
                                @else
                                    <span class="badge badge-pill-soft bg-soft-secondary">
                                        <i class="bi bi-box-arrow-left"></i> KELUAR
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <i class="bi bi-search display-4 text-muted mb-3 opacity-25"></i>
                                <h6 class="text-muted fw-bold">Data tidak ditemukan</h6>
                                <p class="text-muted small">Coba sesuaikan kata kunci atau filter Anda.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="d-flex justify-content-center mt-4 animate-up">
        {{ $absensi->appends(request()->query())->links('pagination.custom') }}
    </div>
</div>

<!-- ================= MODAL EXPORT PRESENSI KAMPUS ================= -->
<div class="modal fade" id="modalExportPresensi" tabindex="-1" aria-labelledby="modalExportPresensiLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-soft-secondary border-0">
                <h5 class="modal-title fw-bold text-dark" id="modalExportPresensiLabel"><i class="bi bi-calendar-check text-primary me-2"></i> Export Presensi Kampus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formExportPresensi" onsubmit="handleExportPresensi(event)">
                <div class="modal-body" style="overflow: visible;">
                    <div class="mb-3">
                        <label class="form-label">Pilih Kampus/Instansi (Opsional)</label>
                        <select name="mou_id" class="form-select choices-select">
                            <option value="">-- Semua Instansi --</option>
                            @foreach($mous as $mou)
                                <option value="{{ $mou->id }}">{{ $mou->nama_instansi ?? $mou->nama_universitas }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jurusan / Prodi (Opsional)</label>
                        <select name="prodi" class="form-select choices-select">
                            <option value="">-- Semua Prodi --</option>
                            @foreach($prodis as $prodi)
                                <option value="{{ $prodi }}">{{ $prodi }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Rentang Awal <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Rentang Akhir <span class="text-danger">*</span></label>
                            <input type="date" name="end_date" class="form-control" required>
                        </div>
                    </div>
                    <small class="text-muted"><i class="bi bi-info-circle me-1"></i> Data Excel ditarik dalam format Matrix (ke samping).</small>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-file-earmark-excel me-1"></i> Generate Excel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ================= MODAL EXPORT NILAI AKHIR ================= -->
<div class="modal fade" id="modalExportNilai" tabindex="-1" aria-labelledby="modalExportNilaiLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-soft-secondary border-0">
                <h5 class="modal-title fw-bold text-dark" id="modalExportNilaiLabel"><i class="bi bi-award text-warning me-2"></i> Export Nilai Akhir</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formExportNilai" onsubmit="handleExportNilai(event)">
                <div class="modal-body" style="overflow: visible;">
                    <div class="mb-3">
                        <label class="form-label">Pilih Kampus/Instansi (Opsional)</label>
                        <select name="mou_id" class="form-select choices-select">
                            <option value="">-- Semua Instansi --</option>
                            @foreach($mous as $mou)
                                <option value="{{ $mou->id }}">{{ $mou->nama_instansi ?? $mou->nama_universitas }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jurusan / Prodi (Opsional)</label>
                        <select name="prodi" class="form-select choices-select">
                            <option value="">-- Semua Prodi --</option>
                            @foreach($prodis as $prodi)
                                <option value="{{ $prodi }}">{{ $prodi }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Periode Mulai (Opsional)</label>
                            <input type="date" name="start_date" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Periode Berakhir (Opsional)</label>
                            <input type="date" name="end_date" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning text-dark fw-bold"><i class="bi bi-file-earmark-excel me-1"></i> Generate Excel</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Include Choices JS -->
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
<!-- Include SheetJS (XLSX) -->
<script src="https://cdn.sheetjs.com/xlsx-0.20.3/package/dist/xlsx.full.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Choices.js
        const elements = document.querySelectorAll('.choices-select');
        elements.forEach(element => {
            new Choices(element, {
                searchEnabled: true,
                itemSelectText: '',
                noResultsText: 'Tidak ada hasil',
                noChoicesText: 'Tidak ada pilihan lagi',
                shouldSort: false
            });
        });
    });

    // Handle AJAX Export Presensi Kampus
    async function handleExportPresensi(e) {
        e.preventDefault();
        const form = e.target;
        const btn = form.querySelector('button[type="submit"]');
        const originalText = btn.innerHTML;
        
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';
        btn.disabled = true;

        try {
            const formData = new FormData(form);
            const params = new URLSearchParams(formData).toString();
            
            // Header Accept: application/json agar Laravel me-return pesan JSON saat validasi gagal
            const response = await fetch(`{{ route('admin.api.export.presensi') }}?${params}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                const errData = await response.json().catch(() => null);
                let errorMsg = errData?.message || `Server Error: ${response.status}`;
                
                // Jika error adalah error validasi, tampilkan detail pesannya
                if (errData?.errors) {
                    const specificErrors = Object.values(errData.errors).flat().join('\n- ');
                    errorMsg = `Gagal Validasi:\n- ${specificErrors}`;
                }
                
                throw new Error(errorMsg);
            }

            const data = await response.json();

            if (!data || data.length === 0) {
                alert('Tidak ada riwayat absensi ditemukan pada rentang tanggal tersebut.');
                return;
            }

            // Create Excel via SheetJS
            const ws = XLSX.utils.json_to_sheet(data);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "Rekap Presensi");
            
            const filename = `Presensi_Kampus_${formData.get('start_date')}_s_d_${formData.get('end_date')}.xlsx`;
            XLSX.writeFile(wb, filename);

            // Hide Modal setelah sukses
            bootstrap.Modal.getInstance(document.getElementById('modalExportPresensi')).hide();
        } catch (error) {
            console.error("Export Error:", error);
            alert(error.message);
        } finally {
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    }

    // Handle AJAX Export Nilai Akhir
    async function handleExportNilai(e) {
        e.preventDefault();
        const form = e.target;
        const btn = form.querySelector('button[type="submit"]');
        const originalText = btn.innerHTML;
        
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';
        btn.disabled = true;

        try {
            const formData = new FormData(form);
            const params = new URLSearchParams(formData).toString();
            
            // Header Accept: application/json
            const response = await fetch(`{{ route('admin.api.export.nilai') }}?${params}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                const errData = await response.json().catch(() => null);
                let errorMsg = errData?.message || `Server Error: ${response.status}`;
                
                if (errData?.errors) {
                    const specificErrors = Object.values(errData.errors).flat().join('\n- ');
                    errorMsg = `Gagal Validasi:\n- ${specificErrors}`;
                }
                
                throw new Error(errorMsg);
            }

            const data = await response.json();

            if (!data || data.length === 0) {
                alert('Tidak ada mahasiswa ditemukan dengan parameter tersebut.');
                return;
            }

            // Create Excel via SheetJS
            const ws = XLSX.utils.json_to_sheet(data);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "Rekap Nilai");
            
            XLSX.writeFile(wb, `Rekap_Nilai_Akhir_${new Date().toISOString().split('T')[0]}.xlsx`);

            // Hide Modal setelah sukses
            bootstrap.Modal.getInstance(document.getElementById('modalExportNilai')).hide();
        } catch (error) {
            console.error("Export Error:", error);
            alert(error.message);
        } finally {
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    }

    // Export Bawaan untuk Tabel UI Saat Ini
    function exportCurrentTable() {
        const table = document.getElementById("absensiTable");
        const wb = XLSX.utils.table_to_book(table, {sheet: "Tabel Saat Ini"});
        XLSX.writeFile(wb, `Tabel_Absensi_${new Date().toISOString().split('T')[0]}.xlsx`);
    }
</script>
@endsection