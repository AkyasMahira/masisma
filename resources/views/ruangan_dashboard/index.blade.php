@extends('layouts.app')

@section('content')

<style>
    :root {
        --primary-maroon: #7c1316;
        --light-maroon: #fcf2f2;
        --soft-bg: #f8f9fa;
    }
    
    /* --- Hero Header --- */
    .page-hero { background: linear-gradient(135deg, #7c1316 0%, #5f0f12 100%); border-radius: 20px; padding: 26px 30px; margin-bottom: 1.75rem; color: #fff; box-shadow: 0 14px 34px rgba(124,19,22,.28); position: relative; overflow: hidden; }
    .page-hero::after { content: ''; position: absolute; right: -40px; top: -40px; width: 180px; height: 180px; background: rgba(255,255,255,.06); border-radius: 50%; pointer-events: none; }
    .page-hero h4 { font-weight: 800; margin: 0 0 6px; letter-spacing: .2px; }
    .page-hero .period { opacity: .9; font-size: .9rem; margin: 0; }
    .page-hero .btn-hero { background: rgba(255,255,255,.95); color: #7c1316; border: none; border-radius: 50px; font-weight: 700; padding: 10px 22px; transition: .25s; }
    .page-hero .btn-hero:hover { background: #fff; transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,0,0,.15); }

    /* --- Modern Cards & Layout --- */
    .dashboard-card { background: #fff; border: none; border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); overflow: hidden; margin-bottom: 1.5rem; transition: 0.3s;}
    .dashboard-card:hover { box-shadow: 0 8px 25px rgba(0,0,0,0.06); }
    .card-header-clean { background: transparent; border-bottom: 1px solid #f1f5f9; padding: 1.2rem 1.5rem; font-weight: 700; color: #2c3e50; display: flex; justify-content: space-between; align-items: center; }
    
    /* --- Summary Widgets --- */
    .summary-widget { padding: 1.5rem; display: flex; align-items: center; gap: 15px; border-radius: 16px; position: relative; overflow: hidden; border: 1px solid #f1f5f9;}
    .summary-icon { width: 55px; height: 55px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; z-index: 2;}
    .summary-info { z-index: 2; }
    .summary-info h3 { font-weight: 800; font-size: 1.8rem; margin: 0; line-height: 1; }
    .summary-info p { font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin: 5px 0 0 0; }
    
    /* Widget Colors */
    .w-primary { background: #fff; border-bottom: 4px solid var(--primary-maroon); }
    .w-primary .summary-icon { background: var(--light-maroon); color: var(--primary-maroon); }
    .w-success { background: #fff; border-bottom: 4px solid #10b981; }
    .w-success .summary-icon { background: #dcfce7; color: #10b981; }
    .w-warning { background: #fff; border-bottom: 4px solid #f59e0b; }
    .w-warning .summary-icon { background: #fef3c7; color: #f59e0b; }
    .w-danger { background: #fff; border-bottom: 4px solid #ef4444; }
    .w-danger .summary-icon { background: #fee2e2; color: #ef4444; }

    /* --- Table Styling (baris = kartu mengambang) --- */
    .table-modern { margin-bottom: 0; border-collapse: separate; border-spacing: 0 14px; }
    .table-modern thead th { background: transparent; color: #94a3b8; font-size: 0.72rem; text-transform: uppercase; font-weight: 700; padding: 4px 18px; border: none; letter-spacing: 0.6px; }
    .table-modern tbody tr { background: #fff; box-shadow: 0 2px 12px rgba(0,0,0,.045); transition: 0.2s; }
    .table-modern tbody tr:hover { box-shadow: 0 8px 22px rgba(124,19,22,.10); transform: translateY(-1px); }
    .table-modern tbody td { padding: 20px 18px; border: none; vertical-align: top; background: transparent; }
    .table-modern tbody td:first-child { border-radius: 16px 0 0 16px; }
    .table-modern tbody td:last-child { border-radius: 0 16px 16px 0; }

    /* --- Panel Penilaian & Histori --- */
    .nilai-box { background: linear-gradient(180deg,#fcf2f2,#fff); border: 1px solid #f3dede; border-radius: 12px; padding: 12px; }
    .histori-list { border: 1px solid #eef2f7; border-radius: 12px; overflow: hidden; }
    .histori-list li { border-bottom: 1px solid #f1f5f9; }
    .histori-list li:last-child { border-bottom: none; }

    /* --- Kartu dispensasi menunggu persetujuan --- */
    .dispen-card { background: #fff; border: 1px solid #fde8c4; border-left: 4px solid #f59e0b; border-radius: 14px; padding: 16px; box-shadow: 0 2px 10px rgba(0,0,0,.04); }
    
    /* --- Badges & Avatars --- */
    .avatar-initial { width: 46px; height: 46px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1.25rem; background: linear-gradient(135deg, #7c1316, #a3191d); color: #fff; flex-shrink: 0; box-shadow: 0 4px 10px rgba(124,19,22,.25); }
    .status-badge { padding: 6px 12px; border-radius: 50px; font-size: 0.75rem; font-weight: 700; display: inline-flex; align-items: center; gap: 5px; }
    .status-in { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
    .status-out { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
    .status-idle { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }

    /* --- Pagination --- */
    .pagination { margin: 0; gap: 5px; }
    .page-item .page-link { border-radius: 8px; font-weight: 600; color: var(--primary-maroon); border: 1px solid #e2e8f0; }
    .page-item.active .page-link { background: var(--primary-maroon); border-color: var(--primary-maroon); color: white; }

    /* --- Form Controls & Utils --- */
    .filter-input { border-radius: 8px; border: 1px solid #e2e8f0; box-shadow: none; font-size: 0.9rem; }
    .filter-input:focus { border-color: var(--primary-maroon); box-shadow: 0 0 0 0.2rem rgba(124, 19, 22, 0.15); }
    .input-nilai { width: 80px; text-align: center; border-radius: 8px; font-weight: bold; border: 2px solid #e2e8f0; transition: 0.2s;}
    .input-nilai:focus { border-color: var(--primary-maroon); outline: none; }
    .input-nilai.has-value { border-color: #10b981; color: #10b981; background: #f0fdf4; }
    .border-dashed { border-style: dashed !important; border-color: #cbd5e1 !important;}
</style>

<div class="container-fluid pb-4">

    <div class="page-hero d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <h4><i class="bi bi-hospital me-2"></i>Dashboard Ruangan {{ $ruangan->nm_ruangan ?? '' }}</h4>
            <p class="period"><i class="bi bi-calendar3 me-1"></i> Periode: {{ \Carbon\Carbon::parse($startDate)->isoFormat('D MMM') }} - {{ \Carbon\Carbon::parse($endDate)->isoFormat('D MMM YYYY') }}</p>
        </div>
        <div class="d-flex gap-2">
            <button onclick="exportToExcel()" class="btn btn-hero shadow-sm">
                <i class="bi bi-file-earmark-excel me-2"></i> Export Rekap
            </button>
        </div>
    </div>

    @php
        // Hitung metrik
        $todayStr = now()->format('Y-m-d');
        $mhsAktifHariIni = 0;
        $mhsAlphaHariIni = 0;
        $mhsBelumDinilai = 0;

        foreach($mahasiswas as $m) {
            $nilaiJson = is_string($m->nilai_ruangan_json) ? json_decode($m->nilai_ruangan_json, true) : ($m->nilai_ruangan_json ?? []);
            
            if(!isset($nilaiJson[$ruangan->id])) $mhsBelumDinilai++;
            if($m->status_badge == 'Masuk' || $m->status_badge == 'Selesai') $mhsAktifHariIni++;
            if($m->status_badge == 'Belum Hadir') $mhsAlphaHariIni++;
        }
    @endphp

    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="summary-widget w-primary shadow-sm">
                <div class="summary-icon"><i class="bi bi-people-fill"></i></div>
                <div class="summary-info">
                    <p class="text-muted">Total Tampil</p>
                    <h3 class="text-dark">{{ $totalMahasiswa }} <span class="fs-6 text-muted fw-normal">Orang</span></h3>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="summary-widget w-success shadow-sm">
                <div class="summary-icon"><i class="bi bi-person-check-fill"></i></div>
                <div class="summary-info">
                    <p class="text-muted">Hadir Hari Ini</p>
                    <h3 class="text-dark">{{ $mhsAktifHariIni }} <span class="fs-6 text-muted fw-normal">Orang</span></h3>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="summary-widget w-danger shadow-sm">
                <div class="summary-icon"><i class="bi bi-person-x-fill"></i></div>
                <div class="summary-info">
                    <p class="text-muted">Belum Hadir / Alpha</p>
                    <h3 class="text-dark">{{ $mhsAlphaHariIni }} <span class="fs-6 text-muted fw-normal">Orang</span></h3>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="summary-widget w-warning shadow-sm" style="{{ $mhsBelumDinilai > 0 ? 'background: #fffbeb;' : '' }}">
                <div class="summary-icon"><i class="bi bi-clipboard-data-fill"></i></div>
                <div class="summary-info">
                    <p class="text-muted">Menunggu Penilaian</p>
                    <h3 class="{{ $mhsBelumDinilai > 0 ? 'text-danger' : 'text-dark' }}">{{ $mhsBelumDinilai }} <span class="fs-6 text-muted fw-normal">Orang</span></h3>
                </div>
            </div>
        </div>
    </div>

    {{-- ============ BANNER DISPENSASI -> halaman khusus ============ --}}
    @if(isset($pendingDispensasi) && $pendingDispensasi->isNotEmpty())
    <a href="{{ route('kepala_ruangan.dispensasi.index') }}" class="text-decoration-none d-block mb-3">
        <div class="dashboard-card" style="border-left:4px solid #f59e0b; background:#fffdf7;">
            <div class="d-flex align-items-center gap-3 p-3">
                <div class="d-flex align-items-center justify-content-center" style="width:46px;height:46px;border-radius:12px;background:#fef3c7;color:#b45309;font-size:1.4rem;flex-shrink:0;">
                    <i class="bi bi-envelope-paper-heart"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="fw-bold text-dark">{{ $pendingDispensasi->count() }} dispensasi menunggu persetujuan</div>
                    <div class="small text-muted">Klik untuk meninjau, menyetujui, atau menolak di halaman Dispensasi.</div>
                </div>
                <span class="btn btn-sm text-white fw-semibold" style="background:#f59e0b;">Kelola <i class="bi bi-arrow-right ms-1"></i></span>
            </div>
        </div>
    </a>
    @endif

    <div class="dashboard-card">

        <div class="bg-white p-3 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-3">
            <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-list-check text-danger me-2"></i> Pantauan & Penilaian Mahasiswa</h5>
            
            <form action="" method="GET" class="d-flex flex-wrap gap-2 align-items-center mb-0">
                <div class="input-group input-group-sm" style="width: 250px;">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control filter-input border-start-0" placeholder="Cari nama mahasiswa..." value="{{ request('search') }}">
                </div>
                <input type="date" name="start_date" class="form-control filter-input form-control-sm" value="{{ $startDate }}" style="width: 140px;" title="Dari Tanggal">
                <span class="text-muted small">-</span>
                <input type="date" name="end_date" class="form-control filter-input form-control-sm" value="{{ $endDate }}" style="width: 140px;" title="Sampai Tanggal">
                
                <button type="submit" class="btn btn-sm btn-danger px-3 fw-bold rounded-2">Filter</button>
                @if(request('search') || request('start_date'))
                    <a href="{{ request()->url() }}" class="btn btn-sm btn-light border px-2 rounded-2 text-muted" title="Reset Filter"><i class="bi bi-arrow-counterclockwise"></i></a>
                @endif
            </form>
        </div>

        <div class="table-responsive" style="min-height: 400px; background:#fbfcfe; padding: 4px 12px 12px;">
            <table class="table table-modern w-100">
                <thead>
                    <tr>
                        <th width="25%">Profil Mahasiswa</th>
                        <th width="15%">Status Operasional</th>
                        <th width="20%">Performa Kehadiran</th>
                        <th width="30%">Penilaian Ruangan (Saat Ini & Riwayat)</th>
                        <th width="10%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mahasiswas as $mhs)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar-initial">{{ substr($mhs->nm_mahasiswa, 0, 1) }}</div>
                                <div>
                                    <div class="fw-bold text-dark mb-1" style="font-size: 0.95rem;">{{ $mhs->nm_mahasiswa }}</div>
                                    <div class="small text-muted mb-1"><i class="bi bi-building me-1"></i>{{ $mhs->univ_asal ?? 'Umum' }}</div>
                                    <div class="badge bg-light text-secondary border fw-normal" style="font-size: 0.7rem;">
                                        {{ \Carbon\Carbon::parse($mhs->tanggal_mulai)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($mhs->tanggal_berakhir)->format('d/m/Y') }}
                                    </div>
                                </div>
                            </div>
                        </td>

                        <td>
                            @if($mhs->status_badge == 'Selesai')
                                <div class="status-badge status-in"><i class="bi bi-check-all"></i> Hadir (Selesai)</div>
                            @elseif($mhs->status_badge == 'Masuk')
                                <div class="status-badge status-in text-warning bg-warning bg-opacity-10 border-warning"><i class="bi bi-clock-history"></i> Sedang Dinas</div>
                            @elseif($mhs->status_badge == 'Belum Hadir')
                                <div class="status-badge status-out"><i class="bi bi-exclamation-circle"></i> Belum Absen</div>
                            @elseif($mhs->status_badge == 'Libur')
                                <div class="status-badge status-idle"><i class="bi bi-cup-hot"></i> Jadwal Libur</div>
                            @else
                                <div class="status-badge status-idle"><i class="bi bi-calendar-minus"></i> Bukan Jadwal</div>
                            @endif
                            
                            @php
                                $sisaHari = \Carbon\Carbon::now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($mhs->tanggal_berakhir)->startOfDay(), false);
                            @endphp
                            
                            @if($sisaHari < 0)
                                <div class="mt-2 text-secondary fw-bold" style="font-size: 0.7rem;">
                                    <i class="bi bi-flag-fill me-1"></i> Selesai Magang
                                </div>
                            @elseif($sisaHari >= 0 && $sisaHari <= 3)
                                <div class="mt-2 text-danger fw-bold" style="font-size: 0.7rem;">
                                    <i class="bi bi-alarm-fill me-1"></i> Sisa Magang: {{ $sisaHari }} Hari!
                                </div>
                            @endif
                        </td>

                   <td>
                            @php
                                // Persentase dari model (satu sumber): sudah berbobot (terlambat 90%) & hari ini tak dihitung alpha
                                $persen = round($mhs->stat_persen ?? 0);
                                $color = $persen >= 100 ? 'success' : ($persen >= 75 ? 'primary' : ($persen >= 50 ? 'warning' : 'danger'));
                            @endphp
                            
                            <div class="d-flex justify-content-between small fw-bold mb-1">
                                <span class="text-muted">Performa Ruangan Ini</span>
                                <span class="text-{{ $color }}">{{ $persen }}%</span>
                            </div>
                            <div class="progress mb-2" style="height: 6px; border-radius: 10px; background-color: #f1f5f9;">
                                <div class="progress-bar bg-{{ $color }}" style="width: {{ $persen }}%"></div>
                            </div>
                            <div class="d-flex flex-wrap gap-2 small">
                                <span class="text-success"><i class="bi bi-check-circle me-1"></i>{{ $mhs->stat_hadir }} Hadir</span>
                                <span class="text-warning"><i class="bi bi-envelope-paper me-1"></i>{{ $mhs->stat_dispen }} Dispen</span>
                                @if(($mhs->stat_lupa ?? 0) > 0)
                                <span style="color:#b45309;"><i class="bi bi-clock-history me-1"></i>{{ $mhs->stat_lupa }} Lupa Pulang</span>
                                @endif
                                <span class="text-danger"><i class="bi bi-x-circle me-1"></i>{{ $mhs->stat_alfa }} Alfa</span>
                            </div>
                        </td>

                        <td class="align-middle">
                            @php 
                                // Decode JSON
                                $nilaiJson = is_string($mhs->nilai_ruangan_json) ? json_decode($mhs->nilai_ruangan_json, true) : ($mhs->nilai_ruangan_json ?? []);
                                $nilaiRuanganIni = $nilaiJson[$ruangan->id] ?? null; 
                            @endphp

                            <div class="nilai-box mb-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="small fw-bold text-dark"><i class="bi bi-pencil-square me-1" style="color:var(--primary-maroon);"></i> Nilai Ruangan Ini:</span>
                                    <!--<form action="{{ route('kepala_ruangan.simpan_nilai', $mhs->id) }}" method="POST" class="m-0">-->
                                    <!--    @csrf-->
                                    <!--    <input type="number" -->
                                    <!--           name="nilai_karu" -->
                                    <!--           placeholder="Nilai"-->
                                    <!--           value="{{ $nilaiRuanganIni }}" -->
                                    <!--           class="form-control form-control-sm input-nilai {{ $nilaiRuanganIni !== null ? 'has-value' : '' }}" -->
                                    <!--           onchange="this.form.submit()"-->
                                    <!--           min="0" max="100">-->
                                    <!--</form>-->
                                    <button type="button" class="btn btn-sm w-100 fw-bold {{ $nilaiRuanganIni !== null ? 'btn-success' : 'btn-outline-primary' }}" data-bs-toggle="modal" data-bs-target="#evaluasiModal-{{ $mhs->id }}">
    @if($nilaiRuanganIni !== null)
        <i class="bi bi-check2-circle me-1"></i> Nilai: {{ $nilaiRuanganIni }} (Edit)
    @else
        <i class="bi bi-pencil-square me-1"></i> Beri Penilaian
    @endif
</button>
                                </div>
                                @if($nilaiRuanganIni === null)
                                    <div class="text-end mt-1"><small class="text-danger fw-bold" style="font-size: 0.65rem;">*Wajib Diisi</small></div>
                                @endif
                            </div>

                            <div class="mt-2">
                                <span class="d-block text-muted fw-bold mb-1" style="font-size: 0.7rem; text-transform:uppercase;">Histori Ruangan Lain:</span>
                                @php
                                    $ruanganLain = collect();
                                    
                                    // Ambil list ruangan unik yang pernah disinggahi mhs ini (selain ruangan sekarang)
                                    if($mhs->roomSequences) {
                                        foreach($mhs->roomSequences as $seq) {
                                            if($seq->ruangan && $seq->ruangan_id != $ruangan->id) {
                                                $ruanganLain->put($seq->ruangan_id, $seq->ruangan->nm_ruangan);
                                            }
                                        }
                                    }
                                @endphp

                                @if($ruanganLain->isNotEmpty())
                                    <ul class="histori-list list-unstyled mb-0">
                                        @foreach($ruanganLain as $r_id => $r_nama)
                                            @php $nilaiLain = $nilaiJson[$r_id] ?? null; @endphp
                                            <li class="d-flex justify-content-between align-items-center px-2 py-2" style="font-size: 0.75rem;">
                                                <span class="text-secondary text-truncate pe-2" style="max-width: 150px;" title="{{ $r_nama }}"><i class="bi bi-door-open me-1"></i>{{ $r_nama }}</span>
                                                @if($nilaiLain !== null)
                                                    <span class="badge bg-success shadow-sm">{{ $nilaiLain }}</span>
                                                @else
                                                    <span class="badge bg-light text-secondary border">Kosong</span>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <div class="text-muted fst-italic px-1" style="font-size: 0.7rem;">- Tidak ada riwayat di ruangan lain -</div>
                                @endif
                            </div>
                        </td>

                        <td class="text-center align-middle">
                            <div class="d-flex justify-content-center gap-2">
                                <button type="button" class="btn btn-sm btn-outline-danger rounded-circle" style="width: 35px; height: 35px;" data-bs-toggle="modal" data-bs-target="#detailModal-{{ $mhs->id }}" title="Lihat Detail Riwayat & Izin">
                                    <i class="bi bi-journal-text"></i>
                                </button>
                                
                                <a href="{{ route('sertifikat.download', $mhs->share_token) }}" target="_blank" class="btn btn-sm btn-outline-success rounded-circle" style="width: 35px; height: 35px;" title="Cetak Sertifikat">
                                    <i class="bi bi-award-fill"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <div class="py-4">
                                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                    <i class="bi bi-clipboard-x fs-1 text-muted opacity-50"></i>
                                </div>
                                <h5 class="fw-bold text-dark">Tidak Ada Data</h5>
                                <p class="text-muted mb-0">Semua mahasiswa di ruangan ini sudah ternilai, atau tidak ada jadwal magang.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-3 border-top d-flex flex-wrap justify-content-between align-items-center bg-light gap-3">
            <div class="small fw-semibold text-muted">
                Menampilkan <span class="text-dark">{{ $mahasiswas->firstItem() ?? 0 }}</span> - <span class="text-dark">{{ $mahasiswas->lastItem() ?? 0 }}</span> dari total <span class="text-dark">{{ $mahasiswas->total() }}</span> mahasiswa
            </div>
            <div class="custom-pagination">
                {{ $mahasiswas->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

@foreach($mahasiswas as $mhs)
<!-- Modal Penilaian & Evaluasi -->
<div class="modal fade" id="evaluasiModal-{{ $mhs->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            @php 
                // Ambil data evaluasi JSON yang sudah ada (jika sedang mode edit)
                $evalJson = is_string($mhs->nilai_evaluasi) ? json_decode($mhs->nilai_evaluasi, true) : ($mhs->nilai_evaluasi ?? []);
                $evalRuanganIni = $evalJson[$ruangan->id] ?? null;
                
                // Ambil nilai utama untuk sertifikat
                $nilaiJson = is_string($mhs->nilai_ruangan_json) ? json_decode($mhs->nilai_ruangan_json, true) : ($mhs->nilai_ruangan_json ?? []);
                $nilaiRuanganIni = $nilaiJson[$ruangan->id] ?? null;
            @endphp

            <div class="modal-header text-white" style="background-color: var(--primary-maroon);">
                <h6 class="modal-title fw-bold">
                    <i class="bi bi-clipboard-check me-2"></i> 
                    @if($nilaiRuanganIni !== null)
                        Ubah/Edit Evaluasi: {{ $mhs->nm_mahasiswa }}
                    @else
                        Form Evaluasi Baru: {{ $mhs->nm_mahasiswa }}
                    @endif
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            
            <form action="{{ route('kepala_ruangan.simpan_nilai', $mhs->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4 bg-light">
                    <!-- 1. Nilai Akhir (Sertifikat) -->
                    <div class="card border-0 shadow-sm mb-4 border-start border-4 border-primary">
                        <div class="card-body">
                            <h6 class="fw-bold text-dark mb-3">Nilai Akhir Ruangan (Masuk ke Sertifikat)</h6>
                            <div class="row align-items-center">
                                <div class="col-md-8 text-muted small">
                                    Berikan nilai rata-rata keseluruhan (0-100). Nilai ini akan digabungkan dengan nilai presensi untuk dicetak di sertifikat mahasiswa.
                                </div>
                                <div class="col-md-4">
<input type="number" name="nilai_karu" class="form-control form-control-lg text-center fw-bold text-primary input-nilai-akhir" placeholder="0 - 100" value="{{ $nilaiRuanganIni }}" required min="0" max="100" readonly style="background-color: #f8f9fa;">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Detail Evaluasi -->
                    <h6 class="fw-bold text-dark mb-3 px-1"><i class="bi bi-bar-chart-steps me-2"></i>Indikator Evaluasi (Sorot ikon <i class="bi bi-info-circle"></i> untuk melihat dasar aturan)</h6>
                    
                    <div class="row g-3">
                        <!-- Kolom Soft Skill -->
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-white fw-bold text-primary border-bottom-0 pt-3">
                                    Sikap & Kedisiplinan
                                </div>
                                <div class="card-body pt-0">
                                    <div class="mb-3">
                                        <label class="form-label small text-muted d-flex justify-content-between align-items-center">
                                            Kedisiplinan Waktu (0-100)
                                            <i class="bi bi-info-circle text-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="Dasar: Standar Kualifikasi dan Pendidikan Staf (KKS) Akreditasi RS terkait kepatuhan jam shift dan absensi."></i>
                                        </label>
                                        <input type="number" name="evaluasi[sikap][kedisiplinan]" class="form-control input-indikator" value="{{ $evalRuanganIni['sikap']['kedisiplinan'] ?? '' }}" required min="0" max="100">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small text-muted d-flex justify-content-between align-items-center">
                                            Komunikasi & Etika (0-100)
                                            <i class="bi bi-info-circle text-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="Dasar: UU No. 17 Tahun 2023 tentang Kesehatan & Hak Pasien terkait etika komunikasi terapeutik PPA."></i>
                                        </label>
                                        <input type="number" name="evaluasi[sikap][komunikasi]" class="form-control input-indikator" value="{{ $evalRuanganIni['sikap']['komunikasi'] ?? '' }}" required min="0" max="100">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small text-muted d-flex justify-content-between align-items-center">
                                            Kepatuhan SOP/PPI (0-100)
                                            <i class="bi bi-info-circle text-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="Dasar: Standar Pencegahan & Pengendalian Infeksi (PPI) RS, termasuk Five Moments cuci tangan dan penggunaan APD."></i>
                                        </label>
                                        <input type="number" name="evaluasi[sikap][kepatuhan]" class="form-control input-indikator" value="{{ $evalRuanganIni['sikap']['kepatuhan'] ?? '' }}" required min="0" max="100">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Kolom Hard Skill -->
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-white fw-bold text-success border-bottom-0 pt-3">
                                    Keahlian & Kompetensi
                                </div>
                                <div class="card-body pt-0">
                                    <div class="mb-3">
                                        <label class="form-label small text-muted d-flex justify-content-between align-items-center">
                                            Pemahaman Teoritis (0-100)
                                            <i class="bi bi-info-circle text-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="Dasar: Panduan Praktik Klinis (PPK) Institusi Pendidikan yang disinkronkan dengan Standar Pelayanan Rumah Sakit."></i>
                                        </label>
                                        <input type="number" name="evaluasi[keahlian][pemahaman]" class="form-control input-indikator" value="{{ $evalRuanganIni['keahlian']['pemahaman'] ?? '' }}" required min="0" max="100">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small text-muted d-flex justify-content-between align-items-center">
                                            Keterampilan Tindakan (0-100)
                                            <i class="bi bi-info-circle text-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="Dasar: Rincian Kewenangan Klinis Dasar (SPK/RKK) yang diizinkan untuk mahasiswa praktik di lingkungan RS."></i>
                                        </label>
                                        <input type="number" name="evaluasi[keahlian][keterampilan]" class="form-control input-indikator" value="{{ $evalRuanganIni['keahlian']['keterampilan'] ?? '' }}" required min="0" max="100">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Catatan Karu -->
                        <div class="col-12 mt-3">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body">
                                    <label class="form-label small text-muted fw-bold">Catatan Khusus Kepala Ruangan</label>
                                    <textarea name="evaluasi[catatan]" class="form-control" rows="3" placeholder="Masukkan catatan evaluasi, teguran, atau apresiasi...">{{ $evalRuanganIni['catatan'] ?? '' }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-white">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4">
                        @if($nilaiRuanganIni !== null) Simpan Perubahan @else Simpan Evaluasi @endif
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="detailModal-{{ $mhs->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header text-white" style="background-color: var(--primary-maroon);">
                <h6 class="modal-title fw-bold">
                    <i class="bi bi-person-lines-fill me-2"></i> Detail: {{ $mhs->nm_mahasiswa }}
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0 bg-light">
                <ul class="nav nav-tabs nav-fill bg-white border-bottom" id="myTab-{{ $mhs->id }}" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active text-dark fw-bold border-0 py-3" id="absen-tab-{{ $mhs->id }}" data-bs-toggle="tab" data-bs-target="#absen-{{ $mhs->id }}" type="button" role="tab">
                            <i class="bi bi-box-arrow-in-right text-success me-1"></i> Riwayat Tap Absen
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link text-dark fw-bold border-0 py-3" id="izin-tab-{{ $mhs->id }}" data-bs-toggle="tab" data-bs-target="#izin-{{ $mhs->id }}" type="button" role="tab">
                            <i class="bi bi-envelope-paper text-warning me-1"></i> Izin / Dispensasi
                        </button>
                    </li>
                </ul>

                <div class="tab-content p-3" id="myTabContent-{{ $mhs->id }}">
                    <div class="tab-pane fade show active" id="absen-{{ $mhs->id }}" role="tabpanel">
                        @php
                            $groupedAbsen = collect($mhs->absensis)->sortByDesc('created_at')->groupBy(function($item) {
                                return \Carbon\Carbon::parse($item->created_at)->format('Y-m-d');
                            })->take(15);
                        @endphp
                        @forelse($groupedAbsen as $tgl => $logs)
                            @php
                                $masuk = collect($logs)->where('type', 'masuk')->first();
                                $keluar = collect($logs)->where('type', 'keluar')->first();
                                $lupaPulang = $masuk && !$keluar && \Carbon\Carbon::parse($tgl)->lt(\Carbon\Carbon::today());
                            @endphp
                            <div class="d-flex align-items-center justify-content-between p-3 mb-2 bg-white border rounded-3 shadow-sm transition-hover {{ $lupaPulang ? 'border-warning' : '' }}">
                                <div>
                                    <span class="d-block fw-bold text-dark" style="font-size: 0.85rem;">
                                        <i class="bi bi-calendar2-day text-primary me-1"></i> {{ \Carbon\Carbon::parse($tgl)->isoFormat('dddd, D MMM YYYY') }}
                                    </span>
                                    @if($lupaPulang)
                                        <span class="badge bg-warning text-dark mt-1" style="font-size:.68rem;"><i class="bi bi-exclamation-triangle-fill me-1"></i>Lupa Pulang (nilai 80%)</span>
                                    @endif
                                </div>
                                <div class="text-end d-flex flex-column gap-1">
                                    <span class="badge bg-soft-success border border-success text-success" style="font-size: 0.75rem;">
                                        IN: {{ $masuk && $masuk->jam_masuk ? \Carbon\Carbon::parse($masuk->jam_masuk)->format('H:i') : '--:--' }}
                                    </span>
                                    <span class="badge {{ $lupaPulang ? 'bg-warning text-dark border border-warning' : 'bg-soft-danger border border-danger text-danger' }}" style="font-size: 0.75rem;">
                                        OUT: {{ $keluar && $keluar->jam_keluar ? \Carbon\Carbon::parse($keluar->jam_keluar)->format('H:i') : ($lupaPulang ? 'Lupa' : '--:--') }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4 text-muted small">
                                <i class="bi bi-clock-history fs-2 d-block mb-2 opacity-50"></i> Belum ada riwayat Tap Masuk/Keluar.
                            </div>
                        @endforelse
                    </div>

                    <div class="tab-pane fade" id="izin-{{ $mhs->id }}" role="tabpanel">
                        @php
                            $dispensasis = $mhs->dispensasis ? collect($mhs->dispensasis)->sortByDesc('tanggal_mulai') : collect();
                        @endphp
                        @forelse($dispensasis as $dispen)
                            @php
                                $st = $dispen->status;
                                $stMap = [
                                    'pending'  => ['warning','Menunggu','hourglass-split','warning'],
                                    'approved' => ['success','Disetujui','check-circle','success'],
                                    'rejected' => ['danger','Ditolak','x-circle','danger'],
                                ];
                                $meta = $stMap[$st] ?? ['secondary', ucfirst($st), 'question-circle','secondary'];
                            @endphp
                            <div class="p-3 mb-2 bg-white border rounded-3 shadow-sm border-start border-4 border-{{ $meta[3] }}">
                                <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-{{ $meta[0] }} {{ $meta[0]=='warning' ? 'text-dark' : '' }} px-2 py-1"><i class="bi bi-{{ $meta[2] }} me-1"></i>{{ $meta[1] }}</span>
                                        <span class="badge bg-light text-dark border px-2 py-1"><i class="bi bi-tag-fill me-1"></i>{{ $dispen->kategori }}</span>
                                    </div>
                                    <small class="text-muted fw-bold" style="font-size: 0.75rem;">
                                        {{ \Carbon\Carbon::parse($dispen->tanggal_mulai)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($dispen->tanggal_selesai)->format('d/m/Y') }}
                                    </small>
                                </div>
                                <p class="mb-0 text-dark mt-2" style="font-size: 0.85rem; line-height: 1.5;">
                                    <strong>Ket:</strong> {{ $dispen->keterangan }}
                                </p>
                                @if($dispen->file_path)
                                    <a href="{{ asset('storage/'.$dispen->file_path) }}" target="_blank" class="d-inline-block mt-2 small text-decoration-none"><i class="bi bi-paperclip me-1"></i>Lihat berkas</a>
                                @endif
                                @if($dispen->catatan_admin && $st !== 'pending')
                                    <div class="mt-2 small text-muted"><i class="bi bi-chat-left-text me-1"></i>{{ $dispen->catatan_admin }}</div>
                                @endif
                                @if($st === 'pending')
                                    <div class="mt-2 small text-warning fw-semibold"><i class="bi bi-info-circle me-1"></i>Persetujuan ada di panel "Dispensasi Menunggu Persetujuan" pada dashboard.</div>
                                @endif
                            </div>
                        @empty
                            <div class="text-center py-4 text-muted small">
                                <i class="bi bi-emoji-smile fs-2 d-block mb-2 text-success opacity-50"></i> Mahasiswa ini tidak memiliki riwayat Izin/Dispensasi.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endforeach

<script id="export-data" type="application/json">
    {!! json_encode($exportData) !!}
</script>





<script src="https://cdn.sheetjs.com/xlsx-0.20.3/package/dist/xlsx.full.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Event delegation: mendeteksi input pada seluruh halaman
    document.addEventListener('input', function(e) {
        // Cek apakah yang diubah adalah input indikator
        if (e.target.classList.contains('input-indikator')) {
            
            // Ambil form terdekat dari input yang sedang diketik
            let form = e.target.closest('form');
            
            // Ambil semua input indikator dalam form tersebut
            let indikators = form.querySelectorAll('.input-indikator');
            let targetNilaiAkhir = form.querySelector('.input-nilai-akhir');
            
            let total = 0;
            let count = 0;
            
            // Hitung total nilai yang sudah diisi
            indikators.forEach(function(input) {
                let val = parseFloat(input.value);
                if (!isNaN(val)) {
                    total += val;
                    count++;
                }
            });
            
            // Jika kelima indikator sudah diisi, hitung rata-rata dan bulatkan
            if (count === 5) {
                let rataRata = Math.round(total / 5);
                targetNilaiAkhir.value = rataRata;
            } else if (count > 0 && count < 5) {
                // Opsional: jika ingin nilai akhir otomatis terhitung meski belum semua terisi (dibagi 5)
                let rataRataSementara = Math.round(total / 5);
                targetNilaiAkhir.value = rataRataSementara;
            } else {
                targetNilaiAkhir.value = '';
            }
        }
    });
});
    function exportToExcel() {
        try {
            const rawData = JSON.parse(document.getElementById('export-data').textContent);
            if (!rawData || rawData.length === 0) { 
                alert("Tidak ada data untuk diexport pada filter ini."); 
                return; 
            }
            const ws = XLSX.utils.json_to_sheet(rawData);
            
            const wscols = [
                {wch: 35}, {wch: 15}, {wch: 15}, {wch: 20}
            ]; 
            ws['!cols'] = wscols;
            
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "Rekap Absensi");
            
            const today = new Date().toISOString().slice(0, 10);
            XLSX.writeFile(wb, `Rekap_Absensi_Mahasiswa_${today}.xlsx`);
        } catch (e) { 
            console.error(e);
            alert("Terjadi kesalahan saat mengekspor data."); 
        }
    }
</script>
<script>
    document.addEventListener("DOMContentLoaded", function(){
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endsection