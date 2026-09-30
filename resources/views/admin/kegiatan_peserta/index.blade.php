@extends('layouts.app')
@section('title', 'Kelola Peserta Kegiatan')

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />

    <style>
        :root { 
            --custom-maroon: #7c1316; 
            --custom-maroon-dark: #5c0d10;
            --custom-maroon-light: #fef1f2; 
            --card-radius: 20px; 
            --text-dark: #1e293b;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --bg-body: #f8fafc;
        }

        body { background: var(--bg-body); }

        .page-header { background: #fff; border-radius: var(--card-radius); padding: 24px 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); margin-bottom: 24px; border-left: 5px solid var(--custom-maroon); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;}
        .table-card { background: white; border-radius: var(--card-radius); padding: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); border: 1px solid var(--border-color);}
        
        /* Buttons */
        .btn-theme { background: var(--custom-maroon); color: white; border: none; border-radius: 10px; font-weight: 700; padding: 10px 20px; transition: 0.2s; display: inline-flex; align-items: center; gap: 8px; font-size: 0.9rem; text-decoration: none !important;}
        .btn-theme:hover { background: var(--custom-maroon-dark); color: white; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(124, 19, 22, 0.2);}
        .btn-outline-theme { background: white; color: var(--text-dark); border: 1px solid var(--border-color); border-radius: 10px; font-weight: 700; padding: 10px 20px; transition: 0.2s; text-decoration: none !important; display: inline-flex; align-items: center; gap: 8px; font-size: 0.9rem;}
        .btn-outline-theme:hover { border-color: var(--custom-maroon); color: var(--custom-maroon); background: var(--bg-body); }
        .btn-excel { background: #10b981; color: white; border: none; border-radius: 10px; font-weight: 700; padding: 10px 20px; transition: 0.2s; display: inline-flex; align-items: center; gap: 8px; font-size: 0.9rem; text-decoration: none !important;}
        .btn-excel:hover { background: #059669; color: white; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(16,185,129,0.2);}

        /* Table */
        .table-custom { margin-bottom: 0; }
        .table-custom thead th { background-color: var(--bg-body); color: var(--text-muted); font-weight: 800; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px; padding: 16px; border-bottom: 1px solid var(--border-color); border-top: none; white-space: nowrap;}
        .table-custom tbody td { padding: 16px; vertical-align: middle; border-bottom: 1px solid #f1f5f9; font-size: 0.9rem; font-weight: 600; color: var(--text-dark);}
        .table-custom tbody tr { transition: 0.2s; }
        .table-custom tbody tr:hover { background-color: var(--custom-maroon-light); }

        /* Badges */
        .badge-status { padding: 6px 14px; border-radius: 50px; font-weight: 700; font-size: 0.75rem; display: inline-flex; align-items: center; gap: 6px; text-transform: uppercase; letter-spacing: 0.5px;}
        .bg-pending { background: #fffbeb; color: #d97706; border: 1px solid #fde68a;}
        .bg-verified { background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe;}
        .bg-hadir { background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0;}
        .bg-belum { background: #f8fafc; color: #64748b; border: 1px solid #e2e8f0;}
        .bg-izin { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca;}

        /* Action Panel Buttons */
        .action-group { display: flex; gap: 6px; justify-content: center; flex-wrap: wrap;}
        .btn-action { width: 34px; height: 34px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; border: none; transition: 0.2s; font-size: 1rem;}
        .btn-detail { background: #e0f2fe; color: #0284c7; border: 1px solid #bae6fd;} .btn-detail:hover { background: #0284c7; color: white;}
        .btn-riwayat { background: #f0fdf4; color: #059669; border: 1px solid #bbf7d0;} .btn-riwayat:hover { background: #059669; color: white;}
        .btn-edit { background: #fffbeb; color: #d97706; border: 1px solid #fde68a;} .btn-edit:hover { background: #d97706; color: white;}
        .btn-delete { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca;} .btn-delete:hover { background: #dc2626; color: white;}
        .btn-approve { background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0;} .btn-approve:hover { background: #059669; color: white;}
        .btn-cancel-approve { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca;} .btn-cancel-approve:hover { background: #dc2626; color: white;}
        .btn-bukti { background: #f3e8ff; color: #9333ea; border: 1px solid #e9d5ff;} .btn-bukti:hover { background: #9333ea; color: white;}

        /* Certificate Buttons */
        .cert-action-group { display: flex; gap: 6px; justify-content: center; }
        .btn-cert { width: 34px; height: 34px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; border: 1px solid transparent; transition: 0.2s; text-decoration: none; font-size: 1rem; }
        .btn-cert-preview { background: #fdf4ff; color: #a21caf; border-color: #fae8ff; } .btn-cert-preview:hover { background: #a21caf; color: white; }
        .btn-cert-open { background: #ecfdf5; color: #059669; border-color: #a7f3d0; } .btn-cert-open:hover { background: #059669; color: white; }
        .btn-cert-copy { background: #eff6ff; color: #3b82f6; border-color: #bfdbfe; } .btn-cert-copy:hover { background: #3b82f6; color: white; }

        /* Form & Filter */
        .filter-label { font-size: 0.75rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px; display: block; }
        .form-control, .form-select { border-radius: 10px; border: 1px solid var(--border-color); padding: 10px 14px; font-size: 0.95rem; font-weight: 500; background: var(--bg-body); transition: 0.3s;}
        .form-control:focus, .form-select:focus { border-color: var(--custom-maroon); box-shadow: 0 0 0 4px var(--custom-maroon-light); background: #fff;}
        
        .copy-link-box { background: var(--bg-body); border: 1px solid var(--border-color); border-radius: 12px; padding: 6px 6px 6px 16px; display: inline-flex; align-items: center; gap: 10px; margin-top: 12px; flex-wrap: wrap; }
        .copy-link-box input { border: none; background: transparent; color: var(--text-dark); font-weight: 600; font-size: 0.85rem; width: 320px; outline: none; }
        .copy-link-box button { background: white; border: 1px solid var(--border-color); border-radius: 8px; padding: 6px 14px; font-size: 0.85rem; font-weight: 700; color: var(--custom-maroon); transition: 0.2s;}
        .copy-link-box button:hover { background: var(--custom-maroon); color: white;}

        .data-detail-list { padding: 0; margin: 0; list-style: none; }
        .data-detail-list li { padding: 12px 0; border-bottom: 1px dashed var(--border-color); display: flex; align-items: flex-start; }
        .data-detail-list li:last-child { border-bottom: none; }
        .data-detail-list li strong { width: 160px; flex-shrink: 0; color: var(--text-muted); font-size: 0.85rem; font-weight: 700; }
        .data-detail-list li span { font-weight: 600; color: var(--text-dark); font-size: 0.9rem;}

        /* Modal Customization */
        .modal-content { border-radius: 20px; border: none; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.1); }
        .modal-header.bg-maroon { background: linear-gradient(135deg, var(--custom-maroon), var(--custom-maroon-dark)); color: white; border-bottom: none; }
    </style>

    <div class="row">
        <div class="col-12">
            
            <div class="page-header">
                <div>
                    <h3 class="fw-bold mb-2"><i class="bi bi-people-fill me-2" style="color: var(--custom-maroon);"></i> Kelola Peserta Kegiatan</h3>
                    <p class="mb-0 fw-semibold text-muted d-flex align-items-center flex-wrap gap-2">
                        <i class="bi bi-calendar-event"></i> {{ $kegiatan->nama_kegiatan }}
                        <span class="badge {{ $kegiatan->jenis_kegiatan == 'internal' ? 'bg-primary' : 'bg-danger' }} rounded-pill px-3">{{ strtoupper($kegiatan->jenis_kegiatan) }}</span>
                    </p>
                    
                    {{-- Link Pendaftaran Publik (Hanya muncul jika Eksternal) --}}
                    @if($kegiatan->jenis_kegiatan == 'eksternal')
                        <div class="copy-link-box">
                            <span class="badge bg-danger rounded-pill px-3 py-2 text-white"><i class="bi bi-globe me-1"></i> Link Publik</span>
                            <input type="text" id="linkDaftar" value="{{ route('public.kegiatan.daftar', $kegiatan->id) }}" readonly>
                            <button type="button" onclick="copyDaftarLink()"><i class="bi bi-clipboard"></i> Salin Link</button>
                        </div>
                    @endif
                </div>
                <div class="mt-3 mt-lg-0">
                    <a href="{{ route('admin.kegiatan.index') }}" class="btn-outline-theme"><i class="bi bi-arrow-left"></i> Kembali</a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center p-3" style="background: #ecfdf5; color: #065f46;">
                    <i class="bi bi-check-circle-fill fs-3 me-3"></i> 
                    <div><strong class="d-block mb-1">Sukses!</strong><small>{{ session('success') }}</small></div>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center p-3" style="background: #fef2f2; color: #991b1b;">
                    <i class="bi bi-exclamation-triangle-fill fs-3 me-3"></i> 
                    <div><strong class="d-block mb-1">Gagal!</strong><small>{{ session('error') }}</small></div>
                </div>
            @endif

            <div class="table-card">
                
                <!-- FILTER & TOMBOL AKSI -->
                <div class="row mb-4 g-3 align-items-end">
                    
                    <!-- Form Filter (Kiri) -->
                    <div class="col-xl-5 col-lg-12">
                        <form action="{{ route('admin.kegiatan.peserta.index', $kegiatan->id) }}" method="GET" class="row g-2 align-items-end">
                            <div class="col-md-5">
                                <label class="filter-label">Cari Nama</label>
                                <div class="position-relative">
                                    <i class="bi bi-search position-absolute top-50 translate-middle-y text-muted" style="left: 14px;"></i>
                                    <input type="text" name="search" class="form-control ps-5" placeholder="Ketik..." value="{{ request('search') }}">
                                </div>
                            </div>
                            <div class="col-md-5">
                                <label class="filter-label">Filter Instansi</label>
                                <select name="instansi_id" class="form-select">
                                    <option value="">Semua Instansi</option>
                                    @foreach($instansi as $i)
                                        <option value="{{ $i->id }}" {{ request('instansi_id') == $i->id ? 'selected' : '' }}>{{ $i->nama_instansi }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 d-flex gap-2">
                                <button type="submit" class="btn-theme w-100 justify-content-center p-0" style="height: 44px;" title="Cari"><i class="bi bi-search"></i></button>
                                @if(request('search') || request('instansi_id'))
                                    <a href="{{ route('admin.kegiatan.peserta.index', $kegiatan->id) }}" class="btn-outline-theme w-100 justify-content-center p-0 border-danger text-danger" style="height: 44px; background: #fef2f2;" title="Reset Filter"><i class="bi bi-x-lg"></i></a>
                                @endif
                            </div>
                        </form>
                    </div>

                    <!-- Tombol Aksi Kanan -->
                    <div class="col-xl-7 col-lg-12 d-flex flex-wrap gap-2 justify-content-xl-end">
                        <a href="{{ route('admin.kegiatan.penilaian.teori.index', $kegiatan->id) }}" class="btn-theme" style="background: #0284c7;" title="Input Nilai Pre-Test & Post-Test">
                            <i class="bi bi-journal-text"></i> <span class="d-none d-sm-inline">Nilai Teori</span>
                        </a>
                        <a href="{{ route('admin.kegiatan.penilaian.setting', $kegiatan->id) }}" class="btn-theme" style="background: #ca8a04;" title="Setting Penilaian (Fasilitator & Checklist)">
                            <i class="bi bi-gear-fill"></i> <span class="d-none d-sm-inline">Setting Penilaian</span>
                        </a>
                        <a href="{{ route('admin.kegiatan.penilaian.rekap', $kegiatan->id) }}" class="btn-theme" style="background: #16a34a;" title="Rekap Nilai & Kelulusan">
                            <i class="bi bi-clipboard2-check-fill"></i> <span class="d-none d-sm-inline">Rekap Kelulusan</span>
                        </a>

                        <div class="dropdown">
                            <button class="btn-outline-theme dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-file-earmark-excel-fill text-success"></i> Excel
                            </button>
                            <ul class="dropdown-menu border-0 shadow-lg rounded-3">
                                <li><a class="dropdown-item fw-semibold text-success py-2" href="#" onclick="downloadTemplateImport()"><i class="bi bi-download me-2"></i>Download Template</a></li>
                                <li><a class="dropdown-item fw-semibold text-primary py-2" href="#" data-bs-toggle="modal" data-bs-target="#importModal"><i class="bi bi-upload me-2"></i>Import Data</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item fw-semibold text-dark py-2" href="#" onclick="exportPesertaXLSX()"><i class="bi bi-file-earmark-arrow-down me-2"></i>Export Peserta</a></li>
                            </ul>
                        </div>

                        <button class="btn-theme" data-bs-toggle="modal" data-bs-target="#addModal"><i class="bi bi-plus-lg"></i> <span class="d-none d-sm-inline">Tambah Peserta</span></button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-custom mb-0">
                        <thead>
                            <tr>
                                <th class="text-center" width="5%">No</th>
                                <th>Profil Peserta</th>
                                <th>Instansi & Jabatan</th>
                                
                                {{-- Kolom Verifikasi hanya untuk Eksternal --}}
                                @if($kegiatan->jenis_kegiatan == 'eksternal')
                                    <th class="text-center">Status Pendaftaran</th>
                                @endif
                                
                                <th class="text-center">Absen Hari Ini</th>
                                <th class="text-center">Sertifikat</th>
                                <th class="text-center" width="18%">Panel Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($peserta as $index => $p)
                                @php
                                    // Status Pendaftaran
                                    $statusDaftar = $p->status_pendaftaran ?? 'terverifikasi'; 
                                    $bgDaftar = $statusDaftar == 'pending' ? 'bg-pending' : 'bg-verified';
                                    $iconDaftar = $statusDaftar == 'pending' ? 'bi-hourglass-split' : 'bi-check2-circle';

                                    // Status Kehadiran & Link Sertifikat
                                    $statusAbsen = 'Belum Absen'; $bgAbsen = 'bg-belum'; $iconAbsen = 'bi-dash-circle';
                                    $linkSertifikat = null;
                                    
                                    if($p->absensiAktual) {
                                        if($p->absensiAktual->status_kehadiran == 'hadir') {
                                            $statusAbsen = 'Hadir'; $bgAbsen = 'bg-hadir'; $iconAbsen = 'bi-check-circle-fill';
                                            // Generate Link Sertifikat
                                            $hash = md5($p->id . ($secretKey ?? 'sindikat_rsud_slg_secret'));
                                            $linkSertifikat = route('public.sertifikat.peserta', [$p->id, $hash]);
                                        } else {
                                            $statusAbsen = 'Izin/Sakit'; $bgAbsen = 'bg-izin'; $iconAbsen = 'bi-file-medical-fill';
                                        }
                                    }
                                    $nomor = ($peserta instanceof \Illuminate\Pagination\LengthAwarePaginator) ? ($peserta->firstItem() + $index) : ($index + 1);
                                @endphp
                                <tr>
                                    <td class="text-center text-muted fw-bold">{{ $nomor }}</td>
                                    <td>
                                        <div class="fw-bold text-dark" style="font-size: 1rem;">{{ $p->nama_lengkap_gelar }}</div>
                                        <div class="small text-muted mt-1 fw-medium"><i class="bi bi-telephone me-1"></i>{{ $p->no_hp_peserta ?? '-' }}</div>
                                        <div class="small text-muted fw-medium"><i class="bi bi-envelope me-1"></i>{{ $p->email_plataran_sehat ?? '-' }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark" style="font-size: 0.9rem;"><i class="bi bi-building me-1 text-muted"></i> {{ $p->instansi->nama_instansi ?? 'Internal RS' }}</div>
                                        <div class="small text-muted mt-1 fw-medium"><i class="bi bi-person-badge me-1"></i> {{ $p->jabatan ?? $p->profesi ?? '-' }}</div>
                                    </td>
                                    
                                    {{-- Status Verifikasi (Hanya Eksternal) --}}
                                    @if($kegiatan->jenis_kegiatan == 'eksternal')
                                        <td class="text-center">
                                            <span class="badge-status {{ $bgDaftar }}"><i class="bi {{ $iconDaftar }}"></i> {{ $statusDaftar }}</span>
                                        </td>
                                    @endif
                                    
                                    <td class="text-center">
                                        <span class="badge-status {{ $bgAbsen }}"><i class="bi {{ $iconAbsen }}"></i> {{ $statusAbsen }}</span>
                                    </td>

                                    {{-- Kolom Sertifikat --}}
                                    <td class="text-center">
                                        @if($linkSertifikat)
                                            <div class="cert-action-group">
                                                <button type="button" class="btn-cert btn-cert-preview" onclick="previewSertifikat('{{ $linkSertifikat }}')" title="Preview Sertifikat">
                                                    <i class="bi bi-award-fill"></i>
                                                </button>
                                                <a href="{{ $linkSertifikat }}" target="_blank" class="btn-cert btn-cert-open" title="Buka di Tab Baru">
                                                    <i class="bi bi-box-arrow-up-right"></i>
                                                </a>
                                                <button type="button" class="btn-cert btn-cert-copy" onclick="copyText('{{ $linkSertifikat }}')" title="Copy Link Sertifikat">
                                                    <i class="bi bi-link-45deg"></i>
                                                </button>
                                            </div>
                                        @else
                                            <span class="badge bg-light text-muted border py-2 px-3 rounded-pill fw-bold"><i class="bi bi-lock-fill me-1"></i> Terkunci</span>
                                        @endif
                                    </td>
                                    
                                    <td class="text-center">
                                        <div class="action-group">
                                            
                                            {{-- Tombol Detail --}}
                                            <button type="button" class="btn-action btn-detail" title="Detail Lengkap" data-bs-toggle="modal" data-bs-target="#detailModal{{ $p->id }}"><i class="bi bi-person-vcard"></i></button>

                                            {{-- Tombol Riwayat Absensi --}}
                                            <button type="button" class="btn-action btn-riwayat" title="Riwayat Kehadiran Absensi" data-bs-toggle="modal" data-bs-target="#absenModal{{ $p->id }}"><i class="bi bi-clock-history"></i></button>

                                            @if($kegiatan->jenis_kegiatan == 'eksternal')
                                                {{-- Tombol Bukti Bayar --}}
                                                @if($p->bukti_bayar)
                                                    <button type="button" class="btn-action btn-bukti" title="Lihat Bukti Bayar" onclick="lihatGambar('{{ asset($p->bukti_bayar) }}', 'Bukti Pembayaran')"><i class="bi bi-receipt"></i></button>
                                                @else
                                                    <button type="button" class="btn-action bg-light text-muted border" title="Tidak Ada Bukti Bayar" disabled><i class="bi bi-receipt"></i></button>
                                                @endif

                                                {{-- Tombol Approve / Batal Approve --}}
                                                @if($statusDaftar == 'pending')
                                                    <form action="{{ route('admin.kegiatan.peserta.approve', [$kegiatan->id, $p->id]) }}" method="POST" class="d-inline m-0 form-approve">
                                                        @csrf
                                                        <button type="button" class="btn-action btn-approve btn-submit-approve" title="Verifikasi & Kirim Email"><i class="bi bi-check2-all"></i></button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('admin.kegiatan.peserta.batal_approve', [$kegiatan->id, $p->id]) }}" method="POST" class="d-inline m-0 form-batal-approve">
                                                        @csrf
                                                        <button type="button" class="btn-action btn-cancel-approve btn-submit-batal" title="Batalkan Verifikasi"><i class="bi bi-x-circle"></i></button>
                                                    </form>
                                                @endif
                                            @endif

                                            {{-- Tombol Edit --}}
                                            <button type="button" class="btn-action btn-edit" title="Edit Peserta" data-bs-toggle="modal" data-bs-target="#editModal{{ $p->id }}"><i class="bi bi-pencil-square"></i></button>
                                            
                                            {{-- Tombol Hapus --}}
                                            <form action="{{ route('admin.kegiatan.peserta.destroy', [$kegiatan->id, $p->id]) }}" method="POST" class="form-delete m-0 d-inline">
                                                @csrf @method('DELETE')
                                                <button type="button" class="btn-action btn-delete btn-delete-swal" title="Hapus Peserta"><i class="bi bi-trash"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="text-muted mb-3"><i class="bi bi-person-x fs-1 d-block opacity-25"></i></div>
                                        <h5 class="fw-bold text-dark">Belum ada data peserta</h5>
                                        <p class="text-muted mb-0">Bagikan link pendaftaran atau tambah secara manual.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 d-flex justify-content-center">
                    {{ $peserta->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>

    <!-- SEMUA MODAL -->
    @foreach($peserta as $p)
        @php
            $statusDaftar = $p->status_pendaftaran ?? 'terverifikasi'; 
            $bgDaftar = $statusDaftar == 'pending' ? 'bg-pending' : 'bg-verified';
        @endphp

        {{-- MODAL RIWAYAT ABSENSI --}}
        <div class="modal fade" id="absenModal{{ $p->id }}" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header bg-maroon">
                        <h5 class="modal-title fw-bold"><i class="bi bi-clock-history me-2"></i> Riwayat Absensi: {{ $p->nama_lengkap_gelar }}</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        @php
                            $riwayatAbsen = \App\Models\AbsensiKegiatan::where('kegiatan_peserta_id', $p->id)->orderBy('waktu_masuk', 'desc')->get();
                        @endphp
                        
                        @if($riwayatAbsen->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover align-middle">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Waktu Datang</th>
                                            <th>Waktu Pulang</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Bukti Foto</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($riwayatAbsen as $ra)
                                            <tr>
                                                <td class="fw-bold text-dark">{{ \Carbon\Carbon::parse($ra->waktu_masuk)->format('d M Y') }}</td>
                                                <td class="text-success fw-bold">{{ \Carbon\Carbon::parse($ra->waktu_masuk)->format('H:i:s') }} WIB</td>
                                                <td class="text-danger fw-bold">{{ $ra->waktu_keluar ? \Carbon\Carbon::parse($ra->waktu_keluar)->format('H:i:s') . ' WIB' : '-' }}</td>
                                                <td class="text-center">
                                                    @if($ra->status_kehadiran == 'hadir')
                                                        <span class="badge bg-success rounded-pill px-3">Hadir</span>
                                                    @else
                                                        <span class="badge bg-warning text-dark rounded-pill px-3">Izin/Sakit</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if($ra->foto_bukti)
                                                        <button class="btn btn-sm btn-outline-primary py-1 px-3 rounded-pill fw-bold" onclick="lihatGambar('{{ asset($ra->foto_bukti) }}', 'Bukti Selfie Absensi')"><i class="bi bi-image me-1"></i> Lihat</button>
                                                    @else
                                                        <span class="text-muted small">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="bi bi-calendar-x fs-1 text-muted opacity-25 d-block mb-3"></i>
                                <h6 class="fw-bold text-dark">Belum Ada Riwayat</h6>
                                <p class="text-muted mb-0">Peserta ini belum melakukan absensi.</p>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer bg-light border-top-0">
                        <button type="button" class="btn btn-secondary px-4 rounded-3 fw-bold" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- MODAL DETAIL LENGKAP --}}
        <div class="modal fade" id="detailModal{{ $p->id }}" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header bg-maroon">
                        <h5 class="modal-title fw-bold"><i class="bi bi-person-vcard me-2"></i> Detail Peserta</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="text-center mb-4 pb-3 border-bottom">
                            <h4 class="fw-bold text-dark mb-1">{{ $p->nama_lengkap_gelar }}</h4>
                            <span class="text-muted fw-semibold">{{ $p->profesi ?? '-' }}</span>
                        </div>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <h6 class="fw-bold text-primary mb-3"><i class="bi bi-person me-2"></i>Data Pribadi</h6>
                                <ul class="data-detail-list">
                                    <li><strong>NIK (KTP)</strong> <span>{{ $p->nik ?? '-' }}</span></li>
                                    <li><strong>Tempat Lahir</strong> <span>{{ $p->tempat_lahir ?? '-' }}</span></li>
                                    <li><strong>Tanggal Lahir</strong> <span>{{ $p->tanggal_lahir ? \Carbon\Carbon::parse($p->tanggal_lahir)->format('d-m-Y') : '-' }}</span></li>
                                    <li><strong>No. WhatsApp</strong> <span>{{ $p->no_hp_peserta ?? '-' }}</span></li>
                                    <li><strong>Email Pribadi</strong> <span>{{ $p->email_plataran_sehat ?? '-' }}</span></li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6 class="fw-bold text-primary mb-3"><i class="bi bi-briefcase me-2"></i>Pekerjaan & Jabatan</h6>
                                <ul class="data-detail-list">
                                    <li><strong>Profesi</strong> <span>{{ $p->profesi ?? '-' }}</span></li>
                                    <li><strong>Jabatan</strong> <span>{{ $p->jabatan ?? '-' }}</span></li>
                                    <li><strong>Status Pegawai</strong> <span>{{ $p->status_pegawai ?? '-' }}</span></li>
                                    <li><strong>Pendidikan Akhir</strong> <span>{{ $p->pendidikan_terakhir ?? '-' }}</span></li>
                                    <li><strong>NIP / Golongan</strong> <span>{{ $p->nip ?? '-' }} / {{ $p->pangkat_golongan ?? '-' }}</span></li>
                                </ul>
                            </div>
                            
                            @if($kegiatan->jenis_kegiatan == 'eksternal')
                                <div class="col-md-6">
                                    <h6 class="fw-bold text-success mb-3"><i class="bi bi-building me-2"></i>Instansi Penanggung Jawab</h6>
                                    <ul class="data-detail-list">
                                        <li><strong>Nama Instansi</strong> <span>{{ $p->instansi->nama_instansi ?? 'Internal RS' }}</span></li>
                                        <li><strong>Email Instansi</strong> <span>{{ $p->email_pj ?? '-' }}</span></li>
                                        <li><strong>No. HP Instansi</strong> <span>{{ $p->no_hp_pj ?? '-' }}</span></li>
                                        <li><strong>Alamat Lengkap</strong> <span>{{ $p->alamat_instansi ?? '-' }}</span></li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="fw-bold text-warning mb-3" style="color: #d97706 !important;"><i class="bi bi-star me-2"></i>Preferensi Pelatihan</h6>
                                    <ul class="data-detail-list">
                                        <li><strong>Metode</strong> <span>{{ $p->metode_pelatihan ?? '-' }}</span></li>
                                        <li><strong>Ukuran Kaos</strong> <span>{{ $p->ukuran_kaos ?? '-' }}</span></li>
                                        <li><strong>Akun LMS</strong> <span>
                                            @if($p->punya_akun_lms) <span class="badge bg-success rounded-pill px-3">Sudah Punya</span>
                                            @else <span class="badge bg-danger rounded-pill px-3">Belum Punya</span> @endif
                                        </span></li>
                                        <li><strong>Status Daftar</strong> <span><span class="badge-status {{ $bgDaftar }} border-0">{{ strtoupper($statusDaftar) }}</span></span></li>
                                    </ul>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top-0">
                        <button type="button" class="btn btn-secondary px-4 rounded-3 fw-bold" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- MODAL EDIT --}}
        <div class="modal fade" id="editModal{{ $p->id }}" tabindex="-1">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header bg-maroon">
                        <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i> Edit Data Peserta</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('admin.kegiatan.peserta.update', [$kegiatan->id, $p->id]) }}" method="POST">
                        @csrf @method('PUT')
                        <div class="modal-body p-4 bg-light">
                            
                            <div class="card border-0 shadow-sm rounded-4 mb-4">
                                <div class="card-body p-4">
                                    <h6 class="fw-bold border-bottom pb-3 mb-4 text-dark"><i class="bi bi-person-fill text-danger me-2"></i>Data Diri</h6>
                                    <div class="row g-3">
                                        <div class="col-md-6"><label class="filter-label">Nama Lengkap & Gelar</label><input type="text" name="nama_lengkap_gelar" class="form-control" value="{{ $p->nama_lengkap_gelar }}" required></div>
                                        <div class="col-md-6"><label class="filter-label">NIK (KTP)</label><input type="text" name="nik" class="form-control" value="{{ $p->nik }}"></div>
                                        <div class="col-md-6"><label class="filter-label">Tempat Lahir</label><input type="text" name="tempat_lahir" class="form-control" value="{{ $p->tempat_lahir }}"></div>
                                        <div class="col-md-6"><label class="filter-label">Tanggal Lahir</label><input type="date" name="tanggal_lahir" class="form-control" value="{{ $p->tanggal_lahir }}"></div>
                                        <div class="col-md-6"><label class="filter-label">No. HP (WhatsApp)</label><input type="text" name="no_hp_peserta" class="form-control" value="{{ $p->no_hp_peserta }}"></div>
                                        <div class="col-md-6"><label class="filter-label">Email Plataran Sehat</label><input type="email" name="email_plataran_sehat" class="form-control" value="{{ $p->email_plataran_sehat }}"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="card border-0 shadow-sm rounded-4 mb-4">
                                <div class="card-body p-4">
                                    <h6 class="fw-bold border-bottom pb-3 mb-4 text-dark"><i class="bi bi-briefcase-fill text-danger me-2"></i>Pekerjaan & Jabatan</h6>
                                    <div class="row g-3">
                                        <div class="col-md-4"><label class="filter-label">Profesi</label><input type="text" name="profesi" class="form-control" value="{{ $p->profesi }}" required></div>
                                        <div class="col-md-4"><label class="filter-label">Jabatan</label><input type="text" name="jabatan" class="form-control" value="{{ $p->jabatan }}"></div>
                                        <div class="col-md-4"><label class="filter-label">Status Pegawai</label>
                                            <select name="status_pegawai" class="form-select">
                                                <option value="PNS" {{ $p->status_pegawai == 'PNS' ? 'selected' : '' }}>PNS</option>
                                                <option value="PPPK" {{ $p->status_pegawai == 'PPPK' ? 'selected' : '' }}>PPPK</option>
                                                <option value="Non-ASN" {{ $p->status_pegawai == 'Non-ASN' ? 'selected' : '' }}>Non-ASN / Honorer</option>
                                                <option value="Swasta" {{ $p->status_pegawai == 'Swasta' ? 'selected' : '' }}>Swasta</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4"><label class="filter-label">Pendidikan Terakhir</label><input type="text" name="pendidikan_terakhir" class="form-control" value="{{ $p->pendidikan_terakhir }}"></div>
                                        <div class="col-md-4"><label class="filter-label">NIP (Opsional)</label><input type="text" name="nip" class="form-control" value="{{ $p->nip }}"></div>
                                        <div class="col-md-4"><label class="filter-label">Pangkat / Golongan</label><input type="text" name="pangkat_golongan" class="form-control" value="{{ $p->pangkat_golongan }}"></div>
                                    </div>
                                </div>
                            </div>

                            @if($kegiatan->jenis_kegiatan == 'eksternal')
                                <div class="card border-0 shadow-sm rounded-4">
                                    <div class="card-body p-4">
                                        <h6 class="fw-bold border-bottom pb-3 mb-4 text-dark"><i class="bi bi-building-fill text-danger me-2"></i>Data Instansi (Eksternal)</h6>
                                        <div class="row g-3">
                                            <div class="col-md-6"><label class="filter-label">Email Instansi / PJ</label><input type="email" name="email_pj" class="form-control" value="{{ $p->email_pj }}"></div>
                                            <div class="col-md-6"><label class="filter-label">No HP PJ</label><input type="text" name="no_hp_pj" class="form-control" value="{{ $p->no_hp_pj }}"></div>
                                            <div class="col-md-12"><label class="filter-label">Alamat Instansi</label><textarea name="alamat_instansi" rows="2" class="form-control">{{ $p->alamat_instansi }}</textarea></div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                        </div>
                        <div class="modal-footer bg-white border-top">
                            <button type="button" class="btn btn-secondary px-4 rounded-3 fw-bold" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn-theme px-5">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    {{-- MODAL TAMBAH MANUAL --}}
    <div class="modal fade" id="addModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-maroon">
                    <h5 class="modal-title fw-bold"><i class="bi bi-person-plus-fill me-2"></i> Tambah Peserta Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.kegiatan.peserta.store', $kegiatan->id) }}" method="POST">
                    @csrf
                    <div class="modal-body p-4 bg-light">
                        <div class="card border-0 shadow-sm rounded-4">
                            <div class="card-body p-4 row g-3">
                                <div class="col-md-6"><label class="filter-label">Nama Lengkap & Gelar <span class="text-danger">*</span></label><input type="text" name="nama_lengkap_gelar" class="form-control" placeholder="Cth: dr. Fulan, Sp.A" required></div>
                                <div class="col-md-6"><label class="filter-label">Profesi / Jabatan <span class="text-danger">*</span></label><input type="text" name="profesi" class="form-control" placeholder="Cth: Dokter Spesialis" required></div>
                                <div class="col-md-6">
                                    <label class="filter-label">Instansi Asal (Opsional)</label>
                                    <select name="instansi_id" id="choices_instansi" class="form-select">
                                        <option value="">Pilih Instansi...</option>
                                        @foreach($instansi as $i) <option value="{{ $i->id }}">{{ $i->nama_instansi }}</option> @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="filter-label">Ruangan Unit (Opsional)</label>
                                    <select name="ruangan_id" id="choices_ruangan" class="form-select">
                                        <option value="">Pilih Ruangan...</option>
                                        @foreach($ruangan as $r) <option value="{{ $r->id }}">{{ $r->instansi->nama_instansi ?? '' }} - {{ $r->nama_ruangan }}</option> @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-white border-top">
                        <button type="button" class="btn btn-secondary px-4 rounded-3 fw-bold" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn-theme px-5">Simpan Peserta</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL PREVIEW GAMBAR (GENERIC UNTUK BUKTI BAYAR DAN FOTO ABSEN) --}}
    <div class="modal fade" id="modalLihatGambar" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-light border-bottom-0">
                    <h5 class="modal-title fw-bold text-dark" id="judulModalGambar"><i class="bi bi-image me-2"></i> Bukti Berkas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0 text-center bg-dark" style="position: relative; min-height: 200px;">
                    <img id="imgViewer" src="" alt="Bukti Berkas" style="max-width: 100%; max-height: 70vh; object-fit: contain;">
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL PREVIEW SERTIFIKAT PDF (MENGGUNAKAN IFRAME) --}}
    <div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-maroon">
                    <h5 class="modal-title fw-bold"><i class="bi bi-award-fill me-2"></i> Preview Sertifikat Publik</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0" style="height: 80vh; background: #e2e8f0;">
                    <iframe id="iframeSertifikat" src="" style="width: 100%; height: 100%; border: none;"></iframe>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL IMPORT EXCEL --}}
    <div class="modal fade" id="importModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header text-white" style="background: #10b981; border: none;">
                    <h5 class="modal-title fw-bold"><i class="bi bi-file-earmark-spreadsheet-fill me-2"></i> Import dari Excel (.xlsx)</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="importForm">
                    <div class="modal-body p-4 bg-light">
                        <div class="card border-0 shadow-sm rounded-4">
                            <div class="card-body p-4">
                                <label class="filter-label">Upload File Excel</label>
                                <input type="file" id="excel_file" class="form-control mb-4" accept=".xlsx" required style="padding: 12px;">
                                
                                <div class="alert border-0 rounded-4 small mb-0" style="background:#fffbeb; color:#d97706; border-left: 4px solid #f59e0b !important;">
                                    <i class="bi bi-exclamation-circle-fill me-1"></i> <strong>Aturan Import:</strong><br>
                                    <ul class="mb-0 mt-2 ps-3">
                                        <li>Gunakan format <strong>.xlsx</strong> (Bukan .csv).</li>
                                        <li>Baris ke-1 wajib berisi header kolom yang tepat.</li>
                                        <li>Gunakan tombol <strong>"Download Template"</strong> untuk mengunduh format yang benar.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-white border-top p-3">
                        <button type="submit" class="btn-excel w-100 justify-content-center" id="btnSubmitImport">Mulai Import Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>

    <script>
        // Fungsi Preview Sertifikat (Iframe Modal)
        function previewSertifikat(url) {
            document.getElementById('iframeSertifikat').src = url;
            new bootstrap.Modal(document.getElementById('previewModal')).show();
        }

        // Fungsi Membersihkan iFrame Saat Ditutup (Mencegah Audio/Video/Memori Menggantung)
        document.getElementById('previewModal')?.addEventListener('hidden.bs.modal', function () {
            document.getElementById('iframeSertifikat').src = '';
        });

        // Copy Link Pendaftaran / Sertifikat
        function copyText(text) {
            navigator.clipboard.writeText(text).then(() => {
                Swal.fire({toast: true, position: 'top-end', icon: 'success', title: 'Tautan Disalin!', showConfirmButton: false, timer: 2000});
            });
        }

        function copyDaftarLink() {
            const copyInput = document.getElementById("linkDaftar");
            copyInput.select();
            copyInput.setSelectionRange(0, 99999);
            copyText(copyInput.value);
        }

        // Lihat Gambar (Bukti Bayar / Foto Absen)
        function lihatGambar(url, title = 'Bukti Berkas') {
            document.getElementById('imgViewer').src = url;
            document.getElementById('judulModalGambar').innerHTML = '<i class="bi bi-image me-2"></i> ' + title;
            new bootstrap.Modal(document.getElementById('modalLihatGambar')).show();
        }

        // Unduh Template Import Excel
        function downloadTemplateImport() {
            const data = [
                ["Nama Lengkap", "Profesi", "Instansi", "Ruangan", "NIK", "No HP", "Email", "Jabatan"],
                ["Dr. Budi Santoso", "Dokter Umum", "RSUD SLG", "IGD", "3506xxxxxxxxxxxx", "0812xxxxxx", "budi@email.com", "Kepala IGD"]
            ];
            const ws = XLSX.utils.aoa_to_sheet(data);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "Template_Peserta");
            XLSX.writeFile(wb, "Template_Import_Peserta.xlsx");
        }

        document.addEventListener('DOMContentLoaded', () => {
            new Choices(document.getElementById('choices_instansi'), { searchEnabled: true, itemSelectText: '' });
            new Choices(document.getElementById('choices_ruangan'), { searchEnabled: true, itemSelectText: '' });

            // Delete Swal
            document.querySelectorAll('.btn-delete-swal').forEach(button => {
                button.addEventListener('click', function() {
                    const form = this.closest('.form-delete');
                    Swal.fire({ 
                        title: 'Hapus Peserta?', text: "Data dan absen peserta ini akan terhapus juga!", icon: 'warning', 
                        showCancelButton: true, confirmButtonColor: '#dc2626', cancelButtonColor: '#64748b', confirmButtonText: 'Ya, Hapus'
                    }).then((result) => { if (result.isConfirmed) form.submit(); });
                });
            });

            // Approve Swal
            document.querySelectorAll('.btn-submit-approve').forEach(button => {
                button.addEventListener('click', function() {
                    const form = this.closest('.form-approve');
                    Swal.fire({ 
                        title: 'Verifikasi Peserta?', 
                        text: "Peserta akan disetujui dan email konfirmasi otomatis akan dikirim ke alamat email mereka.", 
                        icon: 'info', 
                        showCancelButton: true, 
                        confirmButtonColor: '#059669', 
                        cancelButtonColor: '#64748b', 
                        confirmButtonText: 'Verifikasi & Kirim Email'
                    }).then((result) => { if (result.isConfirmed) form.submit(); });
                });
            });

            // Batal Approve Swal
            document.querySelectorAll('.btn-submit-batal').forEach(button => {
                button.addEventListener('click', function() {
                    const form = this.closest('.form-batal-approve');
                    Swal.fire({ 
                        title: 'Batalkan Verifikasi?', 
                        text: "Status peserta akan dikembalikan menjadi Pending.", 
                        icon: 'warning', 
                        showCancelButton: true, 
                        confirmButtonColor: '#dc2626', 
                        cancelButtonColor: '#64748b', 
                        confirmButtonText: 'Ya, Batalkan'
                    }).then((result) => { if (result.isConfirmed) form.submit(); });
                });
            });

            // Import Handler
            document.getElementById('importForm').addEventListener('submit', function(e) {
                e.preventDefault(); 
                const fileInput = document.getElementById('excel_file');
                const file = fileInput.files[0]; 
                
                if (!file) return;
                if (!file.name.endsWith('.xlsx')) {
                    Swal.fire('Format Salah!', 'Sistem hanya menerima file Excel berekstensi .xlsx', 'error');
                    return;
                }

                const btn = document.getElementById('btnSubmitImport'); 
                btn.disabled = true; 
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Membaca File...';

                const reader = new FileReader();
                reader.onload = function(e) {
                    try {
                        const workbook = XLSX.read(new Uint8Array(e.target.result), { type: 'array' });
                        const firstSheet = workbook.SheetNames[0];
                        const jsonData = XLSX.utils.sheet_to_json(workbook.Sheets[firstSheet]);
                        
                        const formattedData = jsonData.map(row => ({ 
                            nama_lengkap: row['Nama Lengkap'], 
                            profesi: row['Profesi'] || '-', 
                            instansi: row['Instansi'] || '', 
                            ruangan: row['Ruangan'] || '',
                            nik: row['NIK'] || null,
                            no_hp: row['No HP'] || null,
                            email: row['Email'] || null,
                            jabatan: row['Jabatan'] || null
                        })).filter(row => row.nama_lengkap);

                        if (formattedData.length === 0) { 
                            Swal.fire('File Kosong', 'Data kosong atau header kolom tidak sesuai dengan template.', 'error'); 
                            btn.disabled = false; btn.innerText = 'Mulai Import Data'; 
                            return; 
                        }

                        fetch("{{ route('admin.kegiatan.peserta.import', $kegiatan->id) }}", { 
                            method: "POST", 
                            headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" }, 
                            body: JSON.stringify({ data: formattedData }) 
                        })
                        .then(r => r.json())
                        .then(r => { 
                            if (r.success) {
                                Swal.fire('Sukses!', r.message, 'success').then(() => location.reload()); 
                            } else {
                                Swal.fire('Gagal!', r.message || 'Terjadi kesalahan saat memproses data.', 'error'); 
                            }
                        })
                        .catch(() => Swal.fire('Error Server', 'Tidak dapat terhubung ke server.', 'error'))
                        .finally(() => { btn.disabled = false; btn.innerText = 'Mulai Import Data'; });

                    } catch (err) { 
                        Swal.fire('File Rusak', 'Gagal membaca file Excel.', 'error'); 
                        btn.disabled = false; btn.innerText = 'Mulai Import Data'; 
                    }
                }; 
                reader.readAsArrayBuffer(file);
            });
        });

        // Export Data
        const seluruhDataPeserta = @json($semuaPeserta ?? []);
        const secretKeyApp = "{{ $secretKey ?? 'sindikat_rsud_slg_secret' }}";

        function exportPesertaXLSX() {
            if (seluruhDataPeserta.length === 0) {
                Swal.fire('Data Kosong', 'Tidak ada data peserta di kegiatan ini untuk di-export.', 'warning');
                return;
            }

            const formattedData = seluruhDataPeserta.map((p, index) => {
                let statusKehadiran = 'Belum Absen';
                let linkSertifikat = '-';

                if (p.absensi_aktual) {
                    if (p.absensi_aktual.status_kehadiran === 'hadir') {
                        statusKehadiran = 'Hadir';
                        const stringToHash = p.id.toString() + secretKeyApp;
                        const hashMD5 = CryptoJS.MD5(stringToHash).toString();
                        linkSertifikat = window.location.origin + "/sertifikat/peserta/" + p.id + "/" + hashMD5;
                    } else {
                        statusKehadiran = 'Izin/Sakit';
                    }
                }

                return {
                    "No": index + 1,
                    "Nama Lengkap & Gelar": p.nama_lengkap_gelar,
                    "NIK": p.nik || '-',
                    "Profesi": p.profesi || '-',
                    "Jabatan": p.jabatan || '-',
                    "Instansi Asal": p.instansi ? p.instansi.nama_instansi : 'Internal RS',
                    "Ruangan": p.ruangan ? p.ruangan.nama_ruangan : '-',
                    "No HP": p.no_hp_peserta || '-',
                    "Email": p.email_plataran_sehat || p.email_pj || '-',
                    "Status Verifikasi": p.status_pendaftaran ? p.status_pendaftaran.toUpperCase() : 'TERVERIFIKASI',
                    "Status Kehadiran": statusKehadiran,
                    "Link Sertifikat": linkSertifikat
                };
            });

            const ws = XLSX.utils.json_to_sheet(formattedData);
            ws['!cols'] = [
                { wch: 5 }, { wch: 30 }, { wch: 20 }, { wch: 20 }, { wch: 20 }, 
                { wch: 30 }, { wch: 20 }, { wch: 15 }, { wch: 25 }, { wch: 18 }, { wch: 15 }, { wch: 45 }
            ];

            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "Data_Peserta");
            XLSX.writeFile(wb, "Data_Peserta_{{ Str::slug($kegiatan->nama_kegiatan) }}.xlsx");
        }
    </script>
@endsection