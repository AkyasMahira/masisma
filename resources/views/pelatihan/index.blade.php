@extends('layouts.app')

@section('title', 'Data Pelatihan')
@section('page-title', 'Data Pelatihan Pegawai')

@section('content')

    <style>
        :root {
            --primary-color: #8c1515;
            --primary-light: #a31d1d;
            --primary-soft: #fcf0f1;
            --bg-body: #f8fafc;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --radius-lg: 16px;
            --radius-md: 10px;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.05);
            --shadow-md: 0 10px 25px -5px rgba(0,0,0,0.05);
            --transition: all 0.25s ease;
        }

        /* CARD & HEADER */
        .modern-card { background: #ffffff; border-radius: var(--radius-lg); border: 1px solid rgba(255, 255, 255, 0.8); box-shadow: var(--shadow-md); margin-bottom: 1.5rem; overflow: hidden; }
        .page-header { display: flex; justify-content: space-between; align-items: center; padding: 1.5rem 2rem; background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); border-bottom: 1px solid var(--border-color); }
        .search-wrapper { background: #f1f5f9; border-radius: var(--radius-md); padding: 0.25rem; border: 1px solid transparent; transition: var(--transition); }
        .search-wrapper:focus-within { background: #ffffff; border-color: var(--primary-light); box-shadow: 0 0 0 3px var(--primary-soft); }
        .search-input { background: transparent; border: none; box-shadow: none !important; font-size: 0.9rem; color: var(--text-main); width: 100%; outline: none;}

        /* BUTTONS */
        .btn-modern { border-radius: var(--radius-md); font-weight: 600; padding: 0.6rem 1.2rem; display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; transition: var(--transition); border: none; cursor: pointer;}
        .btn-primary-modern { background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%); color: white; box-shadow: 0 4px 12px rgba(140, 21, 21, 0.2); }
        .btn-primary-modern:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(140, 21, 21, 0.3); color: white; }
        .btn-outline-modern { background: white; border: 1px solid var(--border-color); color: var(--text-main); }
        .btn-outline-modern:hover { border-color: var(--primary-color); color: var(--primary-color); background: var(--primary-soft); }
        .btn-export { background-color: #10b981; color: white; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2); }
        .btn-export:hover { background-color: #059669; color: white; transform: translateY(-2px); }

        /* TABLE */
        .table-custom { width: 100%; margin-bottom: 0; border-collapse: separate; border-spacing: 0; }
        .table-custom thead th { background-color: #f8fafc; color: var(--text-muted); font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; padding: 1rem 1.5rem; border-bottom: 1px solid var(--border-color); }
        .table-custom tbody tr { transition: var(--transition); border-bottom: 1px solid #f1f5f9; }
        .table-custom tbody tr:hover { background-color: #fdfefe; box-shadow: 0 4px 15px rgba(0,0,0,0.03); transform: scale(1.001); z-index: 10; position: relative; }
        .table-custom tbody td { padding: 1.25rem 1.5rem; vertical-align: middle; color: var(--text-main); border-bottom: 1px solid var(--border-color); }

        /* BADGES & PROGRESS */
        .badge-soft { padding: 0.4rem 0.75rem; border-radius: 6px; font-weight: 600; font-size: 0.75rem; letter-spacing: 0.3px; }
        .badge-unit { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
        .badge-nip { background: #eff6ff; color: #3b82f6; }
        .progress-slim { height: 6px; border-radius: 10px; background-color: #e2e8f0; overflow: hidden; margin-top: 6px;}
        .progress-slim .progress-bar { border-radius: 10px; transition: width 1s ease-in-out; height: 100%;}
        
        /* SARAN PELATIHAN */
        .suggestion-list { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 8px; }
        .suggestion-item { background: #f8fafc; border: 1px solid var(--border-color); border-radius: 8px; padding: 8px 12px; font-size: 0.8rem; display: flex; align-items: center; }
        .suggestion-icon { color: #f59e0b; background: #fef3c7; padding: 4px; border-radius: 4px; margin-right: 8px; font-size: 0.9rem; }

        /* MODAL */
        .modal { display: none; background: rgba(0,0,0,0.5); position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 1050; overflow-y: auto; opacity: 0; transition: opacity 0.3s ease; }
        .modal.show { display: block; opacity: 1; }
        .modal-dialog { margin: 1.75rem auto; max-width: 800px; transition: transform 0.3s ease; transform: translateY(-50px); }
        .modal.show .modal-dialog { transform: translateY(0); }
        .modal-content { background: #fff; border: none; border-radius: var(--radius-lg); box-shadow: 0 20px 40px rgba(0,0,0,0.1); overflow: hidden;}
        .modal-header-clean { background: #ffffff; border-bottom: 1px solid var(--border-color); padding: 1.5rem 2rem; display: flex; justify-content: space-between; align-items: center; }
        .btn-close-modal { background: #f1f5f9; border: none; color: var(--text-muted); width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; transition: var(--transition); cursor: pointer; }
        .btn-close-modal:hover { background: #fee2e2; color: #ef4444; transform: rotate(90deg); }

        /* ACCORDION (NATIVE JS FIX) */
        .riwayat-accordion .card { border: 1px solid var(--border-color); border-radius: var(--radius-md) !important; margin-bottom: 0.75rem; overflow: hidden; }
        .riwayat-btn { padding: 1rem 1.25rem; width: 100%; text-align: left; background: #fff; border: none; font-weight: 600; color: var(--text-main); display: flex; justify-content: space-between; align-items: center; cursor: pointer;}
        .riwayat-btn:hover { background: #f8fafc; }
        .collapse-content { display: none; padding: 0 1rem 1rem 1rem; border-top: 1px solid var(--border-color); background: #f8fafc;}
        .collapse-content.show { display: block; }
        
        .riwayat-item { display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 1rem; background: #ffffff; border: 1px solid var(--border-color); border-radius: 8px; margin-top: 0.5rem; transition: var(--transition); }
        .riwayat-item:hover { border-color: var(--primary-light); box-shadow: var(--shadow-sm); }
        .action-icon-btn { width: 32px; height: 32px; border-radius: 8px; border: 1px solid var(--border-color); background: #ffffff; color: var(--text-muted); display: inline-flex; align-items: center; justify-content: center; cursor: pointer; transition: 0.2s;}
        .action-icon-btn:hover { border-color: var(--primary-color); color: var(--primary-color); background: var(--primary-soft); }
        .action-icon-btn.delete:hover { border-color: #ef4444; color: #ef4444; background: #fef2f2; }
        body.modal-open { overflow: hidden; }
    </style>

    <!-- HEADER DATA -->
    <div class="modern-card">
        <div class="page-header">
            <div>
                <h4 class="fw-bold mb-1" style="color: var(--text-main); letter-spacing: -0.5px;">Manajemen Pelatihan Pegawai</h4>
                <p class="text-muted mb-0 small">Pantau progres dan kelola target pelatihan 20 JPL tahun {{ $currentYear }}.</p>
            </div>
            <div>
                <button type="button" id="btnExportExcel" class="btn-modern btn-export" onclick="exportToExcel()">
                    <i class="bi bi-file-earmark-excel"></i> Export Laporan
                </button>
            </div>
        </div>

        <!-- FILTER SECTION -->
        <div class="p-4 bg-white border-bottom">
            <form id="filterForm" method="GET" action="{{ route('pelatihan.index') }}">
                <div class="row align-items-end">
                    <div class="col-md-5 mb-3 mb-md-0">
                        <label class="small text-muted font-weight-bold text-uppercase mb-2">Pencarian Pegawai</label>
                        <div class="search-wrapper d-flex align-items-center px-3 py-1">
                            <i class="bi bi-search text-muted mr-2"></i>
                            <input type="text" class="search-input" name="search" placeholder="Ketik nama pegawai..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-5 mb-3 mb-md-0">
                        <label class="small text-muted font-weight-bold text-uppercase mb-2">Filter Unit Kerja</label>
                        <div class="search-wrapper d-flex align-items-center px-3 py-1">
                            <i class="bi bi-building text-muted mr-2"></i>
                            <input type="text" class="search-input" name="unit" placeholder="Ketik nama ruangan/unit..." value="{{ request('unit') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn-modern btn-outline-modern w-100" style="height: 42px;">
                            <i class="bi bi-funnel"></i> Terapkan
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- TABLE SECTION -->
        <div class="table-responsive">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th class="text-center" width="5%">No</th>
                        <th width="25%">Profil Pegawai</th>
                        <th width="15%">Unit Kerja</th>
                        <th width="20%">Status {{ $currentYear }}</th>
                        <th width="25%">Rekomendasi Pelatihan</th>
                        <th class="text-center" width="10%">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pelatihans as $index => $pelatihan)
                        @php
                            $persentase = min(($pelatihan->jpl_tahun_ini / 20) * 100, 100);
                            $isComplete = $pelatihan->target_terpenuhi;
                            $barColor = $isComplete ? 'bg-success' : 'bg-danger';
                        @endphp
                        
                        <tr>
                            <td class="text-center font-weight-bold text-muted">
                                {{ ($pelatihans->currentPage() - 1) * $pelatihans->perPage() + $index + 1 }}
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="font-weight-bold mb-1" style="font-size: 0.95rem;">{{ $pelatihan->nama }}</span>
                                    <div class="d-flex align-items-center mb-1" style="gap: 8px;">
                                        <span class="badge-soft badge-nip"><i class="bi bi-person-badge mr-1"></i> {{ $pelatihan->nip }}</span>
                                    </div>
                                    <span class="small text-muted">{{ $pelatihan->email }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="badge-soft badge-unit">{{ $pelatihan->unit ?? '-' }}</span>
                            </td>
                            <td>
                                <div class="status-indicator">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="font-weight-bold {{ $isComplete ? 'text-success' : 'text-danger' }}" style="font-size: 0.9rem;">
                                            {{ $pelatihan->jpl_tahun_ini }} / 20 JPL
                                        </span>
                                        @if($isComplete)
                                            <i class="bi bi-check-circle-fill text-success"></i>
                                        @endif
                                    </div>
                                    <div class="progress-slim">
                                        <div class="bg-success" style="width: {{ $persentase }}%; height: 100%; border-radius: 10px; background-color: {{ $isComplete ? '#10b981' : '#ef4444' }} !important;"></div>
                                    </div>
                                    @if(!$isComplete)
                                        <span class="small text-danger font-weight-bold">Defisit {{ 20 - $pelatihan->jpl_tahun_ini }} JPL</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if(!$isComplete)
                                    @if(count($saranPelatihan) > 0)
                                        <ul class="suggestion-list">
                                            @foreach($saranPelatihan as $saran)
                                                <li class="suggestion-item">
                                                    <span class="suggestion-icon"><i class="bi bi-lightbulb-fill"></i></span>
                                                    <div class="d-flex flex-column" style="line-height: 1.2;">
                                                        <span class="text-truncate font-weight-bold text-dark" style="max-width: 150px;" title="{{ $saran->nama_kegiatan }}">{{ $saran->nama_kegiatan }}</span>
                                                        <span class="small text-muted">{{ $saran->jpl }} JPL</span>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <span class="small text-muted font-italic">Belum ada saran.</span>
                                    @endif
                                @else
                                    <div class="d-flex align-items-center text-success font-weight-bold" style="background: #ecfdf5; padding: 8px 12px; border-radius: 8px; border: 1px solid #a7f3d0; width: fit-content;">
                                        <i class="bi bi-shield-fill-check mr-2" style="font-size: 1.2rem;"></i> Target Selesai
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex flex-column align-items-center" style="gap: 8px;">
                                    <button type="button" class="btn-modern btn-outline-modern w-100" style="padding: 6px 10px; font-size: 0.85rem;" onclick="bukaModal('modalRiwayat-{{ $pelatihan->id }}')">
                                        <i class="bi bi-clock-history"></i> Riwayat
                                    </button>
                                    <button type="button" class="btn-modern btn-primary-modern w-100" style="padding: 6px 10px; font-size: 0.85rem;" onclick="tambahPelatihan('{{ $pelatihan->id }}', '{{ addslashes($pelatihan->nama) }}')">
                                        <i class="bi bi-plus-lg"></i> Tambah
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 bg-white">
                                <div class="py-4">
                                    <i class="bi bi-search text-muted mb-3 d-block" style="font-size: 3rem; opacity: 0.3;"></i>
                                    <h5 class="text-muted font-weight-bold">Tidak ada data pegawai</h5>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $pelatihans->links('pagination::bootstrap-4') }}
    </div>

    <!-- MODAL RIWAYAT (DIRENDER UNTUK SEMUA) -->
    @foreach ($pelatihans as $pelatihan)
        <div class="modal" id="modalRiwayat-{{ $pelatihan->id }}">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header-clean">
                        <div>
                            <h5 class="font-weight-bold m-0 text-dark">Riwayat Pelatihan</h5>
                            <small class="text-muted">{{ $pelatihan->nama }}</small>
                        </div>
                        <button type="button" class="btn-close-modal" onclick="tutupModal('modalRiwayat-{{ $pelatihan->id }}')">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                    
                    <div class="modal-body p-4 bg-light" style="max-height: 60vh; overflow-y: auto;">
                        @if(empty($pelatihan->riwayat_grouped))
                            <div class="text-center p-5 rounded bg-white" style="border: 2px dashed var(--border-color);">
                                <i class="bi bi-inbox text-muted mb-3" style="font-size: 2.5rem;"></i>
                                <p class="text-muted font-weight-bold m-0">Belum ada riwayat pelatihan.</p>
                            </div>
                        @else
                            <div class="riwayat-accordion">
                                @foreach ($pelatihan->riwayat_grouped as $tahun => $items)
                                    @php $totalJplTahun = collect($items)->sum('jpl'); @endphp
                                    <div class="card">
                                        <button class="riwayat-btn" type="button" onclick="toggleAccordion('collapse-{{ $pelatihan->id }}-{{ $loop->iteration }}')">
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-calendar-event mr-2" style="color: var(--primary-color);"></i>
                                                Tahun {{ $tahun !== '' ? $tahun : 'Tidak Diketahui' }}
                                            </div>
                                            <span class="badge-soft {{ $totalJplTahun >= 20 ? 'bg-success text-white' : 'bg-danger text-white' }}">
                                                {{ $totalJplTahun }} JPL
                                            </span>
                                        </button>
                                        <div id="collapse-{{ $pelatihan->id }}-{{ $loop->iteration }}" class="collapse-content {{ $tahun == $currentYear ? 'show' : '' }}">
                                            @foreach ($items as $item)
                                                <div class="riwayat-item">
                                                    <div class="mr-3 text-truncate">
                                                        <span class="font-weight-bold text-dark d-block text-truncate" title="{{ $item['nama'] }}">
                                                            {{ $item['nama'] }}
                                                        </span>
                                                        <small class="text-muted"><i class="bi bi-award text-warning mr-1"></i> {{ $item['jpl'] }} JPL</small>
                                                    </div>
                                                    <div class="d-flex" style="gap: 8px;">
                                                        <button type="button" class="action-icon-btn" title="Edit" 
                                                                onclick="triggerEdit('{{ $pelatihan->id }}', '{{ $item['no_index'] }}', '{{ addslashes($item['nama']) }}', '{{ $item['jpl'] }}')">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                        <form action="{{ route('pelatihan.destroyAPI', ['id' => $pelatihan->id, 'index' => $item['no_index']]) }}" method="POST" class="d-inline delete-form">
                                                            @csrf @method('DELETE')
                                                            <button type="button" class="action-icon-btn delete btn-delete" title="Hapus">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <!-- MODAL TAMBAH PELATIHAN -->
    <div class="modal" id="modalAddPelatihan">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header-clean">
                    <h5 class="font-weight-bold m-0 text-dark"><i class="bi bi-plus-circle text-success mr-2"></i>Tambah Data</h5>
                    <button type="button" class="btn-close-modal" onclick="tutupModal('modalAddPelatihan')"><i class="bi bi-x-lg"></i></button>
                </div>
                <form id="formAddPelatihan" method="POST" action="">
                    @csrf
                    <div class="modal-body p-4 bg-light">
                        <div class="alert alert-primary bg-white border-primary" style="border-radius: 8px; padding: 10px; border: 1px solid #bce8f1; margin-bottom: 1rem;">
                            <small class="text-muted d-block mb-1">Menambahkan pelatihan untuk:</small>
                            <strong id="addPegawaiName" class="text-primary"></strong>
                        </div>
                        <div class="form-group mb-3">
                            <label class="font-weight-bold small text-muted text-uppercase">Nama Pelatihan <span class="text-danger">*</span></label>
                            <input type="text" name="pelatihan" class="form-control p-2 w-100" style="border: 1px solid #ccc; border-radius: 8px;" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="font-weight-bold small text-muted text-uppercase">Jumlah JPL <span class="text-danger">*</span></label>
                            <input type="number" name="jpl" class="form-control p-2 w-100" style="border: 1px solid #ccc; border-radius: 8px;" required min="1">
                        </div>
                        <div class="row d-flex" style="gap: 10px;">
                            <div class="form-group flex-fill">
                                <label class="font-weight-bold small text-muted text-uppercase">Tgl Mulai <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_mulai" class="form-control p-2 w-100" style="border: 1px solid #ccc; border-radius: 8px;" required>
                            </div>
                            <div class="form-group flex-fill">
                                <label class="font-weight-bold small text-muted text-uppercase">Tgl Selesai</label>
                                <input type="date" name="tanggal_selesai" class="form-control p-2 w-100" style="border: 1px solid #ccc; border-radius: 8px;">
                            </div>
                        </div>
                    </div>
                    <div class="modal-header-clean bg-white" style="border-top: 1px solid var(--border-color); border-bottom: none;">
                        <button type="button" class="btn-modern btn-outline-modern" onclick="tutupModal('modalAddPelatihan')">Batal</button>
                        <button type="submit" class="btn-modern btn-primary-modern">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL EDIT PELATIHAN -->
    <div class="modal" id="modalEditPelatihan">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header-clean">
                    <h5 class="font-weight-bold m-0 text-dark"><i class="bi bi-pencil-square text-warning mr-2"></i>Edit Data</h5>
                    <button type="button" class="btn-close-modal" onclick="tutupModal('modalEditPelatihan')"><i class="bi bi-x-lg"></i></button>
                </div>
                <form id="formEditPelatihan" method="POST" action="">
                    @csrf
                    @method('PUT')
                    <div class="modal-body p-4 bg-light">
                        <div class="form-group mb-3">
                            <label class="font-weight-bold small text-muted text-uppercase">Nama Pelatihan <span class="text-danger">*</span></label>
                            <input type="text" id="editNamaPelatihan" name="pelatihan" class="form-control p-2 w-100" style="border: 1px solid #ccc; border-radius: 8px;" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="font-weight-bold small text-muted text-uppercase">Jumlah JPL <span class="text-danger">*</span></label>
                            <input type="number" id="editJpl" name="jpl" class="form-control p-2 w-100" style="border: 1px solid #ccc; border-radius: 8px;" required min="1">
                        </div>
                        <div class="row d-flex" style="gap: 10px;">
                            <div class="form-group flex-fill">
                                <label class="font-weight-bold small text-muted text-uppercase">Tgl Mulai <span class="text-danger">*</span></label>
                                <input type="date" id="editTanggalMulai" name="tanggal_mulai" class="form-control p-2 w-100" style="border: 1px solid #ccc; border-radius: 8px;" required>
                            </div>
                            <div class="form-group flex-fill">
                                <label class="font-weight-bold small text-muted text-uppercase">Tgl Selesai</label>
                                <input type="date" id="editTanggalSelesai" name="tanggal_selesai" class="form-control p-2 w-100" style="border: 1px solid #ccc; border-radius: 8px;">
                            </div>
                        </div>
                    </div>
                    <div class="modal-header-clean bg-white" style="border-top: 1px solid var(--border-color); border-bottom: none;">
                        <button type="button" class="btn-modern btn-outline-modern" onclick="tutupModal('modalEditPelatihan')">Batal</button>
                        <button type="submit" id="btnSubmitEdit" class="btn-modern btn-primary-modern">Perbarui Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- SCRIPTS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

    @if (isset($error))
        <script>Swal.fire({ icon: 'error', title: 'Gangguan API!', text: '{!! $error !!}', confirmButtonColor: '#8c1515' });</script>
    @elseif (session('error'))
        <script>Swal.fire({ icon: 'error', title: 'Gagal!', text: '{{ session('error') }}', confirmButtonColor: '#8c1515' });</script>
    @endif
    @if (session('success'))
        <script>Swal.fire({ icon: 'success', title: 'Berhasil!', text: '{{ session('success') }}', showConfirmButton: false, timer: 2000 });</script>
    @endif

    <script>
        // PURE VANILLA JS UNTUK MODAL (TIDAK PERLU JQUERY)
        function bukaModal(id) {
            const modal = document.getElementById(id);
            if (modal) {
                modal.classList.add('show');
                document.body.classList.add('modal-open');
            }
        }

        function tutupModal(id) {
            const modal = document.getElementById(id);
            if (modal) {
                modal.classList.remove('show');
                document.body.classList.remove('modal-open');
            }
        }

        // PURE VANILLA JS UNTUK ACCORDION
        function toggleAccordion(id) {
            const content = document.getElementById(id);
            if (content) {
                if (content.classList.contains('show')) {
                    content.classList.remove('show');
                } else {
                    content.classList.add('show');
                }
            }
        }

        function triggerEdit(pegawaiId, index, nama, jpl) {
            tutupModal('modalRiwayat-' + pegawaiId);
            setTimeout(() => {
                bukaModal('modalEditPelatihan');
                document.getElementById('editNamaPelatihan').value = 'Memuat...';
                document.getElementById('editJpl').value = jpl;
                document.getElementById('editTanggalMulai').value = '';
                document.getElementById('editTanggalSelesai').value = '';

                let url = `{{ url('pelatihan') }}/${pegawaiId}/${index}`;
                document.getElementById('formEditPelatihan').action = url;

                fetch(url + '/detail')
                    .then(response => response.json())
                    .then(res => {
                        if(res.status === 'success' && res.data) {
                            document.getElementById('editNamaPelatihan').value = res.data.pelatihan || nama;
                            document.getElementById('editJpl').value = res.data.jpl || jpl;
                            document.getElementById('editTanggalMulai').value = res.data.tanggal_mulai || '';
                            if(res.data.tanggal_selesai) document.getElementById('editTanggalSelesai').value = res.data.tanggal_selesai;
                        } else {
                            document.getElementById('editNamaPelatihan').value = nama;
                        }
                    })
                    .catch(() => document.getElementById('editNamaPelatihan').value = nama);
            }, 300); 
        }

        function tambahPelatihan(pegawaiId, pegawaiName) {
            document.getElementById('addPegawaiName').innerText = pegawaiName;
            document.getElementById('formAddPelatihan').action = "{{ url('pelatihan') }}/" + pegawaiId;
            bukaModal('modalAddPelatihan');
        }

        function exportToExcel() {
            const btn = document.getElementById('btnExportExcel');
            const originalHtml = btn.innerHTML;
            btn.disabled = true; btn.innerHTML = 'Menyiapkan...';

            fetch("{{ route('pelatihan.exportData') }}", { headers: { 'Accept': 'application/json' } })
            .then(async response => JSON.parse(await response.text()))
            .then(res => {
                btn.disabled = false; btn.innerHTML = originalHtml;
                if (res.status !== 'success') return Swal.fire('Gagal', res.message, 'error');
                if (!res.data || res.data.length === 0) return Swal.fire('Info', 'Tidak ada data untuk diekspor.', 'info');

                const rows = res.data.map((r, i) => ({
                    'No': i + 1, 'Nama': r.nama, 'Email': r.email, 'NIP/NIK': r.nip,
                    'Status Pegawai': r.status, 'Unit': r.unit, 'Tahun': r.tahun,
                    'Nama Pelatihan': r.pelatihan, 'JPL': r.jpl
                }));
                const ws = XLSX.utils.json_to_sheet(rows);
                ws['!cols'] = [{ wch: 5 }, { wch: 30 }, { wch: 28 }, { wch: 18 }, { wch: 16 }, { wch: 20 }, { wch: 8 }, { wch: 40 }, { wch: 8 }];
                const wb = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(wb, ws, 'Data Pelatihan');
                XLSX.writeFile(wb, `Data_Pelatihan_Pegawai_${new Date().toISOString().slice(0, 10)}.xlsx`);
            })
            .catch(err => {
                btn.disabled = false; btn.innerHTML = originalHtml;
                Swal.fire('Gagal', 'Terjadi kesalahan sistem saat ekspor data.', 'error');
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            document.body.addEventListener('click', function(e) {
                const deleteBtn = e.target.closest('.btn-delete');
                if (deleteBtn) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Hapus Pelatihan?',
                        text: "Tindakan ini akan menghapus data pelatihan secara permanen.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#94a3b8',
                        confirmButtonText: 'Ya, Hapus',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) deleteBtn.closest('form').submit();
                    });
                }
            });
        });
    </script>
@endsection