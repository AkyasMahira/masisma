@extends('layouts.app')

@section('title', 'Detail Mahasiswa')
@section('page-title', 'Profil Lengkap Mahasiswa')

@section('content')
    {{-- Libraries --}}
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/id.js'></script> 

   {{-- LOGIC PHP: HITUNG STATISTIK (SAMA DENGAN DASHBOARD) --}}
   {{-- LOGIC PHP: HITUNG STATISTIK (SAMA DENGAN DASHBOARD) --}}
    @php
        // 1. Setup Tanggal
        $start = \Carbon\Carbon::parse($tglMulai);
        $end   = \Carbon\Carbon::parse($tglAkhir);
        
        // 2. Data Shift & Izin
        $shiftMap = $mahasiswa->shiftSchedules->pluck('shift_type', 'tanggal')->toArray();
        $izinDates = [];
        // Ambil data dispensasi langsung agar akurat
        $dispensasis = \App\Models\Dispensasi::where('mahasiswa_id', $mahasiswa->id)
            ->where('status', 'approved')->get();
        
        foreach($dispensasis as $d) {
            $p = \Carbon\CarbonPeriod::create($d->tanggal_mulai, $d->tanggal_selesai);
            foreach($p as $dt) $izinDates[] = $dt->format('Y-m-d');
        }
        
        // 3. Ambil Absen Fisik
        $absensiDates = $mahasiswa->absensis->filter(fn($a) => $a->type == 'masuk')
            ->map(fn($a) => \Carbon\Carbon::parse($a->jam_masuk)->format('Y-m-d'))->toArray();

        // 4. HITUNG TARGET & REALISASI
        $targetTotal = 0;
        $targetBerjalan = 0;
        $totalHadirReal = 0; // Fisik + Izin
        $today = now();
        $listAlpha = []; // Untuk Modal

        $period = \Carbon\CarbonPeriod::create($start, $end);
        
        foreach($period as $dt) {
            $dStr = $dt->format('Y-m-d');
            $sType = $shiftMap[$dStr] ?? null;
            
            // Cek Libur
            $isHariKerja = true;
            if($sType === 'Libur') $isHariKerja = false;
            if(!$sType && !$mahasiswa->weekend_aktif && $dt->isWeekend()) $isHariKerja = false;

            if($isHariKerja) {
                $targetTotal++; // Tambah Target Total
                
                // Tambah Target Berjalan (Jika tanggal <= hari ini)
                if($dt->lte($today)) {
                    $targetBerjalan++; 
                    
                    // Cek Hadir (Fisik ATAU Izin)
                    if(in_array($dStr, $absensiDates) || in_array($dStr, $izinDates)) {
                        $totalHadirReal++;
                    } else {
                        // Jika tidak hadir & tidak izin = Alpha
                        // Simpan ke list alpha untuk modal
                        $listAlpha[] = $dt->isoFormat('dddd, D MMMM Y');
                    }
                }
            }
        }

        // 5. Hitung Final — SATU SUMBER dari model (konsisten dgn dashboard & sertifikat)
        $stM = $mahasiswa->statistik;
        $targetTotal    = $stM->target_total;
        $targetBerjalan = $stM->target_sekarang;
        $totalHadirReal = $stM->hadir_fisik + $stM->dispensasi_biasa + $stM->dispensasi_terlambat;
        $alphaCount     = $stM->alpha;
        $lupaPulang     = $stM->lupa_pulang ?? 0;
        $sisaPeriode    = $stM->sisa_kerja;
        $persentase     = round($mahasiswa->absensi_percentage);

        // Daftar tanggal alpha untuk modal, dari kalender model (termasuk lupa absen pulang)
        $listAlpha = [];
        foreach(($mahasiswa->kalender_kehadiran ?? []) as $tgl => $info) {
            if(($info['status'] ?? '') === 'alpha') {
                $listAlpha[] = [
                    'tgl'  => \Carbon\Carbon::parse($tgl)->isoFormat('dddd, D MMMM Y'),
                    'lupa' => stripos($info['label'] ?? '', 'Lupa') !== false,
                ];
            }
        }
    @endphp

    <style>
        :root {
            --custom-maroon: #7c1316;
            --custom-maroon-light: #a3191d;
            --custom-maroon-subtle: #fcf0f1;
            --text-dark: #2c3e50;
            --text-muted: #64748b;
            --card-radius: 16px;
            text-decoration: none;
        }

        /* --- Cards --- */
        .profile-card {
            border: none;
            border-radius: var(--card-radius);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            background: #fff;
            overflow: hidden;
            height: 100%;
            transition: 0.3s;
        }

        .stat-card {
            background-color: #fff;
            border: 1px solid #eef2f7;
            border-radius: 12px;
            padding: 1rem;
            text-align: center;
            box-shadow: 0 2px 6px rgba(0,0,0,0.02);
            height: 100%;
        }
        
        /* Alpha Clickable */
        .stat-card.alpha-box { cursor: pointer; border-color: #fca5a5; background-color: #fef2f2; transition: 0.2s; }
        .stat-card.alpha-box:hover { transform: translateY(-3px); box-shadow: 0 5px 15px rgba(220, 38, 38, 0.1); }

        .section-title {
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--custom-maroon);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 1.2rem;
            border-bottom: 2px solid var(--custom-maroon-subtle);
            padding-bottom: 0.5rem;
        }

        /* --- Avatar --- */
        .profile-header-bg {
            background: linear-gradient(135deg, var(--custom-maroon), var(--custom-maroon-light));
            height: 120px;
        }
        .avatar-container { margin-top: -60px; text-align: center; position: relative; }
        .profile-avatar {
            width: 120px; height: 120px; border-radius: 50%;
            border: 4px solid #fff; background: #fff;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
            object-fit: cover; cursor: zoom-in; transition: transform 0.2s;
        }
        .profile-avatar:hover { transform: scale(1.05); }
        .avatar-placeholder {
            width: 120px; height: 120px; border-radius: 50%;
            border: 4px solid #fff; background: var(--custom-maroon-subtle);
            color: var(--custom-maroon); font-size: 3rem;
            display: inline-flex; align-items: center; justify-content: center;
            font-weight: bold; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
        }

        /* --- Details --- */
        .detail-item { margin-bottom: 1.25rem; }
        .detail-label { font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.2rem; text-transform: uppercase; letter-spacing: 0.5px; }
        .detail-value { font-size: 1rem; font-weight: 600; color: var(--text-dark); }

        /* --- Calendar Styling --- */
        #calendar { font-family: 'Segoe UI', sans-serif; font-size: 0.85rem; }
        .fc-toolbar-title { font-size: 1.1rem !important; font-weight: 700; color: var(--custom-maroon); }
        .fc-button-primary { background-color: var(--custom-maroon) !important; border-color: var(--custom-maroon) !important; }
        .fc-event { cursor: pointer; white-space: normal !important; border: none; padding: 2px; margin-bottom: 2px !important; }
        .evt-content { display: flex; flex-direction: column; }
        .evt-title { font-weight: 700; font-size: 0.75rem; line-height: 1.1; }
        .evt-time { font-size: 0.7rem; opacity: 0.9; }
        .evt-badge { font-size: 0.65rem; background: rgba(0,0,0,0.1); padding: 1px 4px; border-radius: 3px; margin-top: 2px; align-self: flex-start; border: 1px solid rgba(0,0,0,0.1); }

        /* --- Image Modal --- */
        .img-modal { display: none; position: fixed; z-index: 9999; padding-top: 50px; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.9); }
        .img-modal-content { margin: auto; display: block; width: 80%; max-width: 500px; border-radius: 8px; animation: zoom 0.3s; }
        @keyframes zoom { from {transform: scale(0)} to {transform: scale(1)} }
        .close-modal { position: absolute; top: 20px; right: 35px; color: #f1f1f1; font-size: 40px; font-weight: bold; transition: 0.3s; cursor: pointer; }
        .close-modal:hover { color: #bbb; }

        /* --- Animation --- */
        .animate-up { animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards; opacity: 0; transform: translateY(20px); }
        @keyframes fadeInUp { to { opacity: 1; transform: translateY(0); } }
    </style>

    <div class="row animate-up">
        
        {{-- KOLOM KIRI: PROFIL & LINK --}}
        <div class="col-lg-4 mb-4">
            <div class="profile-card pb-4">
                <div class="profile-header-bg"></div>

                <div class="avatar-container">
                    @if ($mahasiswa->foto_path)
                        <img src="{{ asset($mahasiswa->foto_path) }}" alt="Foto Profil" class="profile-avatar" onclick="openImageModal(this.src)">
                        <div class="small text-muted mt-2"><i class="bi bi-search"></i> Klik untuk perbesar</div>
                    @else
                        <div class="avatar-placeholder">{{ strtoupper(substr($mahasiswa->nm_mahasiswa, 0, 1)) }}</div>
                    @endif
                </div>

                <div class="text-center px-4 mt-3">
                    <h5 class="fw-bold text-dark mb-1">{{ $mahasiswa->nm_mahasiswa }}</h5>
                    <p class="text-muted small mb-2">{{ $mahasiswa->prodi }}</p>

                    <div class="d-flex justify-content-center flex-wrap gap-2 mb-3">
        {{-- Badge Status --}}
        <span class="badge {{ $mahasiswa->status == 'aktif' ? 'bg-success' : 'bg-secondary' }} rounded-pill px-3">
            {{ ucfirst($mahasiswa->status) }}
        </span>
        
        {{-- Badge TIPE MAHASISWA (Baru) --}}
        <span class="badge bg-info text-dark rounded-pill px-3" style="font-weight: 600;">
            <i class="bi bi-person-workspace"></i> {{ strtoupper($mahasiswa->tipe_mahasiswa ?? 'MAGANG') }}
        </span>

        {{-- Badge ID Card --}}
        @if($mahasiswa->is_id_card_approved)
             <span class="badge bg-primary rounded-pill px-3"><i class="bi bi-person-badge"></i> ID OK</span>
        @endif
    </div>

                    {{-- WA Button --}}
                    @if ($mahasiswa->no_hp)
                        @php
                            $hp = preg_replace('/[^0-9]/', '', $mahasiswa->no_hp);
                            if (substr($hp, 0, 1) == '0') $hp = '62' . substr($hp, 1);
                        @endphp
                        <a href="https://wa.me/{{ $hp }}" target="_blank" class="btn btn-success w-100 rounded-pill mb-2 shadow-sm d-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-whatsapp"></i> Chat WhatsApp
                        </a>
                    @else
                        <button disabled class="btn btn-light w-100 rounded-pill mb-2 text-muted border"><i class="bi bi-telephone-x"></i> No HP -</button>
                    @endif
                    <a href="{{ route('mahasiswa.edit', $mahasiswa->id) }}" class="btn btn-outline-dark w-100 rounded-pill">Edit Profil</a>
                    
      <a href="{{ route('mahasiswa.rolling.edit', $mahasiswa->id) }}" class="btn btn-warning w-100 rounded-pill mt-2">Rolling Jadwal</a>

{{-- Ambil data user secara manual untuk cek device_id --}}
@php
    $userAkun = \App\Models\User::find($mahasiswa->user_id);
@endphp

@if($userAkun && $userAkun->device_id)
    {{-- Jika device_id MASIH ADA --}}
    <form action="{{ route('mahasiswa.reset-device', $mahasiswa->id) }}" method="POST" id="formResetDevice" class="mt-2">
        @csrf
        <button type="button" onclick="confirmResetDevice()" class="btn btn-outline-danger w-100 rounded-pill d-flex align-items-center justify-content-center gap-2">
            <i class="bi bi-phone-flip"></i> Reset Device ID
        </button>
    </form>
@else
    {{-- Jika device_id SUDAH NULL --}}
    <div class="mt-2">
        <button disabled class="btn btn-light border w-100 rounded-pill d-flex align-items-center justify-content-center gap-2 text-muted">
            <i class="bi bi-check-circle-fill text-success"></i> Device Sudah Reset
        </button>
    </div>
@endif
@if($mahasiswa->is_edited == 1 || $mahasiswa->is_edited === '1')
    
    <form action="{{ route('mahasiswa.reset-edit', $mahasiswa->id) }}" method="POST" class="mt-2">
        @csrf
        <button type="button" onclick="confirmResetEdit(this)" class="btn btn-outline-danger w-100 rounded-pill d-flex align-items-center justify-content-center gap-2">
            <i class="bi bi-phone-flip"></i> Reset Edit
        </button>
    </form>

@else

    <div class="mt-2">
        <button disabled class="btn btn-light border w-100 rounded-pill d-flex align-items-center justify-content-center gap-2 text-muted">
            <i class="bi bi-check-circle-fill text-success"></i> Edit Sudah Reset
        </button>
    </div>

@endif
      
                </div>
                
                <hr class="mx-4 my-4">

                <div class="px-4">
                    <h6 class="small fw-bold text-muted mb-2">LINK ABSENSI</h6>
                    <div class="input-group">
                        <input type="text" class="form-control bg-light fs-6" style="font-size: 0.8rem;" value="{{ route('absensi.card', $mahasiswa->share_token) }}" id="linkAbsen" readonly>
                        <button class="btn btn-secondary" onclick="copyLink()"><i class="bi bi-clipboard"></i></button>
                    </div>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: STATISTIK, KALENDER, DAN JADWAL --}}
        <div class="col-lg-8">
            <div class="profile-card p-4">
                
                {{-- SECTION 1: INFO UMUM --}}
              {{-- SECTION 1: INFO UMUM --}}
<div class="section-title"><i class="bi bi-info-circle me-2"></i> Info Akademik</div>
<div class="row">
    <div class="col-md-4 detail-item">
        <div class="detail-label">Asal Instansi</div>
        <div class="detail-value text-primary">
            {{ $mahasiswa->univ_asal ?? '-' }}
        </div>
    </div>
    
    {{-- TIPE MAHASISWA (Baru) --}}
    <div class="col-md-4 detail-item">
        <div class="detail-label">Tipe Kegiatan</div>
        <div class="detail-value text-dark fw-bold">
            {{ strtoupper($mahasiswa->tipe_mahasiswa ?? 'MAGANG') }}
        </div>
    </div>

    <div class="col-md-4 detail-item">
        <div class="detail-label">Ruangan Saat Ini</div>
        <div class="detail-value d-flex align-items-center gap-2">
            <i class="bi bi-door-open text-muted"></i> {{ $mahasiswa->nama_ruangan_saat_ini }}
            @php $kat = $mahasiswa->ruangan->kategori ?? 'non'; @endphp
            <span class="badge {{ $kat=='shift'?'bg-info text-dark':'bg-secondary' }}" style="font-size:0.6rem">{{ strtoupper($kat) }}</span>
        </div>
    </div>
</div>

                {{-- SECTION 2: PERIODE --}}
                <div class="section-title mt-2">
                    <i class="bi bi-calendar-range me-2"></i> Periode Magang
                    <span class="badge {{ $statusJadwal=='Rolling'?'bg-warning text-dark':'bg-light text-muted border' }} ms-2" style="font-size: 0.65rem; vertical-align: middle;">
                        {{ $statusJadwal == 'Rolling' ? 'JADWAL ROLLING' : 'GLOBAL' }}
                    </span>
                </div>

                <div class="row">
                    <div class="col-6 detail-item">
                        <div class="detail-label">Tanggal Mulai</div>
                        <div class="detail-value">
                            {{ $mahasiswa->tanggal_mulai ? \Carbon\Carbon::parse($mahasiswa->tanggal_mulai)->format('d M Y') : '-' }}
                        </div>
                    </div>
                    <div class="col-6 detail-item">
                        <div class="detail-label">Tanggal Berakhir</div>
                        <div class="detail-value">
                            {{ $mahasiswa->tanggal_berakhir ? \Carbon\Carbon::parse($mahasiswa->tanggal_berakhir)->format('d M Y') : '-' }}
                        </div>
                    </div>
                </div>

        {{-- PROGRESS BAR --}}
<div class="mb-4">
    <div class="d-flex justify-content-between align-items-end mb-1">
        <span class="detail-label mb-0">Persentase Kehadiran</span>
        <span class="fw-bold {{ $persentase >= 80 ? 'text-success' : 'text-warning' }}">{{ $persentase }}%</span>
    </div>
    <div class="progress rounded-pill shadow-sm" style="height: 12px;">
        <div class="progress-bar {{ $persentase >= 80 ? 'bg-success' : ($persentase >= 50 ? 'bg-warning' : 'bg-danger') }} progress-bar-striped progress-bar-animated" 
             role="progressbar" style="width: {{ $persentase }}%"></div>
    </div>
</div>

{{-- KOLOM KANAN: Statistik & Grafik --}}
<div class="row g-3 mb-4 animate-up" style="animation-delay: 0.2s;">
    
    {{-- KARTU 1: TOTAL HADIR --}}
    <div class="col-md-3 col-6">
        <div class="stat-card bg-gradient-green" style="background: #fff; border-left: 4px solid #198754;">
            <div class="position-relative z-1">
                {{-- Data Pakai $totalHadirReal (Fisik + Izin) --}}
                <h2 class="fw-bold mb-0 text-success">{{ $totalHadirReal }}</h2>
                <small class="text-muted fw-bold" style="font-size: 0.65rem;">HADIR (+IZIN)</small>
            </div>
        </div>
    </div>

    {{-- KARTU 2: ALPHA (Bisa Diklik) --}}
    <div class="col-md-3 col-6">
        <div class="stat-card alpha-box" style="background: #fff5f5; border-left: 4px solid #dc3545; cursor: pointer;" 
             data-bs-toggle="modal" data-bs-target="#alphaModal">
            <div class="position-relative z-1">
                {{-- Data Pakai $alphaCount --}}
                <h2 class="fw-bold mb-0 text-danger">
                    {{ $alphaCount }} <i class="bi bi-search ms-1" style="font-size:0.8rem"></i>
                </h2>
                <small class="text-danger fw-bold" style="font-size: 0.65rem;">ALPHA (DETAIL)</small>
            </div>
        </div>
    </div>


    {{-- KARTU 3: TARGET BERJALAN --}}
    <div class="col-md-3 col-6">
        <div class="stat-card" style="background: #fff; border-left: 4px solid #0d6efd;">
            <div class="position-relative z-1">
                {{-- Data Pakai $targetBerjalan --}}
                <h2 class="fw-bold mb-0 text-primary">{{ $targetBerjalan }}</h2>
                <small class="text-muted fw-bold" style="font-size: 0.65rem;">TARGET BERJALAN</small>
            </div>
        </div>
    </div>

    {{-- KARTU 4: SISA PERIODE --}}
    <div class="col-md-3 col-6">
        <div class="stat-card" style="background: #fff; border-left: 4px solid #ffc107;">
            <div class="position-relative z-1">
                {{-- Data Pakai $sisaPeriode --}}
                <h2 class="fw-bold mb-0 text-warning">{{ $sisaPeriode }}</h2>
                <small class="text-muted fw-bold" style="font-size: 0.65rem;">SISA HARI</small>
            </div>
        </div>
    </div>
</div>

{{-- GRAFIK DONAT & INFO DETAIL --}}
<div class="content-card animate-up" style="animation-delay: 0.4s;">
    <div class="content-header">
        <h5 class="content-title"><i class="bi bi-pie-chart me-2"></i>Statistik Kehadiran</h5>
    </div>
    <div class="p-4">
        <div class="row align-items-center">
            {{-- Kiri: Detail Teks --}}
            <div class="col-md-6 mb-3 mb-md-0">
                <div class="alert alert-light border">
                    <h6 class="fw-bold mb-2 text-dark"><i class="bi bi-activity me-2"></i>Ringkasan</h6>
                    <ul class="mb-0 small ps-3 text-muted">
                        <li>Total Hari Kerja: <strong>{{ $targetTotal }} Hari</strong></li>
                        <li>Sudah Berjalan: <strong>{{ $targetBerjalan }} Hari</strong></li>
                        <li>Total Masuk (Fisik/Izin): <strong class="text-success">{{ $totalHadirReal }} Hari</strong></li>
                        <li>Alpha (termasuk Lupa Absen Pulang): <strong class="text-danger">{{ $alphaCount }} Hari</strong>@if($lupaPulang > 0) <span style="color:#b45309;font-size:.9em;">(di antaranya {{ $lupaPulang }} lupa absen pulang)</span>@endif</li>
                    </ul>
                </div>
            </div>

            {{-- Kanan: Grafik --}}
            <div class="col-md-6 d-flex justify-content-center">
                <div style="width: 130px; height: 130px; position: relative;">
                    <canvas id="attendanceChart"></canvas>
                    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center;">
                        <span class="fw-bold text-dark" style="font-size: 1rem;">{{ $persentase }}%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- SCRIPT UPDATE CHART (WAJIB DIUPDATE AGAR DATANYA PAS) --}}
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('attendanceChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Hadir (+Izin)', 'Alpha', 'Sisa'],
                datasets: [{
                    // Gunakan variabel PHP hasil hitungan di atas
                    data: [{{ $totalHadirReal }}, {{ $alphaCount }}, {{ $sisaPeriode }}],
                    backgroundColor: ['#198754', '#dc3545', '#ffc107'],
                    borderWidth: 0, hoverOffset: 4
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, cutout: '75%', plugins: { legend: { display: false } } }
        });
    });
</script>

                {{-- SECTION 5: KALENDER AKTIVITAS (RESPONSIF) --}}
                <div class="section-title mt-4"><i class="bi bi-calendar-check me-2"></i> Kalender Aktivitas</div>
                
                {{-- Legend --}}
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <span class="badge bg-success">Hadir</span>
                    <span class="badge bg-primary">Jadwal Shift</span>
                    <span class="badge bg-secondary">Libur</span>
                    <span class="badge bg-danger">Alpha</span>
                    <span class="badge" style="background:#6610f2">Non-Shift</span>
                </div>

                <div id="calendar" class="mb-4"></div>

                {{-- SECTION 6: TABEL RENCANA RUANGAN (JIKA ADA) --}}
                @if($mahasiswa->roomSequences->count() > 0)
                <div class="section-title mt-4"><i class="bi bi-arrow-right-circle me-2"></i> Rencana & Riwayat Ruangan</div>
                <div class="table-responsive">
                    <table class="table table-hover table-sm align-middle mb-0" style="font-size: 0.85rem;">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-3 py-2">Ruangan</th>
                                <th>Kategori</th>
                                <th>Periode</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($mahasiswa->roomSequences->sortBy('start_date') as $seq)
                                @php
                                    $today = now()->format('Y-m-d');
                                    $badge = 'bg-secondary';
                                    $textStatus = 'Akan Datang';
                                    if ($today >= $seq->start_date && $today <= $seq->end_date) {
                                        $badge = 'bg-success'; $textStatus = 'Aktif';
                                    } elseif ($today > $seq->end_date) {
                                        $badge = 'bg-light text-muted border'; $textStatus = 'Selesai';
                                    }
                                    $kat = $seq->ruangan->kategori ?? 'non_shift';
                                @endphp
                                <tr>
                                    <td class="ps-3 fw-bold text-dark">{{ $seq->ruangan->nm_ruangan }}</td>
                                    <td><span class="badge bg-light text-dark border">{{ strtoupper($kat) }}</span></td>
                                    <td>{{ \Carbon\Carbon::parse($seq->start_date)->format('d M') }} - {{ \Carbon\Carbon::parse($seq->end_date)->format('d M Y') }}</td>
                                    <td class="text-center"><span class="badge {{ $badge }} rounded-pill" style="font-size: 0.65rem;">{{ $textStatus }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif

                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('mahasiswa.index') }}" class="btn btn-light border px-4">Kembali</a>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL DETAIL ALPHA --}}
    <div class="modal fade" id="alphaModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-danger text-white py-2">
                    <h6 class="modal-title fw-bold mb-0">Detail Tanggal Alpha</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0" style="max-height: 300px; overflow-y: auto;">
                    @if(count($listAlpha) > 0)
                        <ul class="list-group list-group-flush">
                            @foreach($listAlpha as $a)
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span style="font-size: 0.85rem;">{{ $a['tgl'] }}</span>
                                        <span class="badge {{ $a['lupa'] ? 'bg-warning text-dark' : 'bg-danger' }} rounded-pill" style="font-size: 0.65rem;">{{ $a['lupa'] ? 'LP' : 'A' }}</span>
                                    </div>
                                    @if($a['lupa'])
                                        <div style="font-size:.7rem;color:#b45309;"><i class="bi bi-box-arrow-right me-1"></i>Lupa Absen Pulang (dihitung alfa)</div>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="text-center p-4">
                            <i class="bi bi-emoji-smile text-success display-4"></i>
                            <p class="small text-muted mt-2 mb-0">Hebat! Tidak ada alpha.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div id="imageModal" class="img-modal">
        <span class="close-modal" onclick="closeImageModal()">&times;</span>
        <img class="img-modal-content" id="img01">
    </div>

    {{-- SCRIPTS --}}
    <script>
   

        // 2. Kalender Responsif (Grid vs List)
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var isMobile = window.innerWidth < 768; // Deteksi HP

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: isMobile ? 'listMonth' : 'dayGridMonth',
                locale: 'id',
                headerToolbar: {
                    left: 'prev,next',
                    center: 'title',
                    right: isMobile ? 'listMonth' : 'dayGridMonth'
                },
                contentHeight: 'auto',
                events: {!! json_encode($events) !!}, 
                
                // RENDER CUSTOM ISI EVENT
                eventContent: function(arg) {
                    let props = arg.event.extendedProps;
                    let title = arg.event.title;
                    let jam   = props.jam || '';
                    let ruang = props.ruang || '';

                    // Mode List (HP)
                    if (arg.view.type === 'listMonth') {
                        return { 
                            html: `
                                <div class="d-flex justify-content-between align-items-center w-100">
                                    <div>
                                        <div class="fw-bold text-dark">${title}</div>
                                        <div class="small text-muted">${jam}</div>
                                    </div>
                                    ${ruang ? `<span class="badge bg-light text-dark border">${ruang}</span>` : ''}
                                </div>
                            ` 
                        };
                    } 
                    
                    // Mode Grid (Laptop)
                    else {
                        return { 
                            html: `
                                <div class="evt-content">
                                    <span class="evt-title">${title}</span>
                                    ${jam ? `<span class="evt-time">${jam}</span>` : ''}
                                    ${ruang ? `<span class="evt-badge">${ruang}</span>` : ''}
                                </div>
                            ` 
                        };
                    }
                },

                // Pop-up Detail
                eventClick: function(info) {
                    let p = info.event.extendedProps;
                    Swal.fire({
                        title: info.event.title,
                        html: `<b>Jam:</b> ${p.jam}<br><b>Lokasi:</b> ${p.ruang}`,
                        icon: 'info',
                        confirmButtonColor: '#7c1316'
                    });
                },

                windowResize: function(view) {
                    if (window.innerWidth < 768) calendar.changeView('listMonth');
                    else calendar.changeView('dayGridMonth');
                }
            });
            calendar.render();
        });

        // --- Copy Link ---
        function copyLink() {
            const linkInput = document.getElementById('linkAbsen');
            linkInput.select();
            linkInput.setSelectionRange(0, 99999);
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(linkInput.value).then(() => toast('Link Disalin!'));
            } else {
                document.execCommand('copy');
                toast('Link Disalin!');
            }
        }
        function toast(msg) { Swal.fire({ icon: 'success', title: msg, toast: true, position: 'top-end', showConfirmButton: false, timer: 1500 }); }

        // --- Zoom Image ---
        function openImageModal(src) { document.getElementById("imageModal").style.display = "block"; document.getElementById("img01").src = src; }
        function closeImageModal() { document.getElementById("imageModal").style.display = "none"; }
        window.onclick = function(e) { if(e.target == document.getElementById("imageModal")) closeImageModal(); }
        
        
  function confirmResetDevice() {
    Swal.fire({
        title: 'Reset Perangkat?',
        text: "Setelah di-reset, mahasiswa harus login ulang untuk mendaftarkan HP barunya.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545', // Warna merah (danger)
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Reset Sekarang!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('formResetDevice').submit();
        }
    })
}

// Terima parameter 'button' dari HTML
function confirmResetEdit(button) {
    Swal.fire({
        title: 'Beri Akses Edit?',
        text: "Setelah di-reset, mahasiswa bisa edit datanya kembali.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545', 
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Reset Sekarang!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // button.closest('form') akan mencari form terdekat dari tombol yang diklik
            // Ini sangat aman meskipun ada 100 tombol di satu halaman tabel
            button.closest('form').submit();
        }
    });
}
    </script>
@endsection