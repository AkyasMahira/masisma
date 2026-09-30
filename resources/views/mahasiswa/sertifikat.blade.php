@extends('layouts.app')

@section('title', 'Ringkasan Sertifikat')
@section('page-title', 'Dokumen & Penilaian Mahasiswa')

@section('content')
    <style>
        :root {
            --custom-maroon: #7c1316;
            --custom-maroon-subtle: #f9eaea;
            --text-dark: #1e293b;
            --text-muted: #64748b;
            --card-radius: 20px;
            
            --success: #10b981;
            --success-bg: #ecfdf5;
            --danger: #ef4444;
            --danger-bg: #fef2f2;
            --warning: #f59e0b;
            --warning-bg: #fffbeb;
            --info: #3b82f6;
            --info-bg: #eff6ff;
        }

        body {
            background-color: #f8fafc;
        }

        /* CARD UMUM */
        .modern-card {
            background: #ffffff;
            border: none;
            border-radius: var(--card-radius);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
        }
        
        .section-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
        }

        /* PROFIL STATS */
        .avatar-lg {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, var(--custom-maroon) 0%, #a3191d 100%);
            color: white;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.2rem;
            font-weight: 800;
            box-shadow: 0 4px 10px rgba(124, 19, 22, 0.2);
        }

        /* GRID STATISTIK */
        .stat-modern-box {
            border-radius: 16px;
            padding: 1.5rem 1.25rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(0,0,0,0.02);
        }
        .stat-modern-box .icon-bg {
            position: absolute;
            right: -10px;
            bottom: -15px;
            font-size: 5rem;
            opacity: 0.1;
            transform: rotate(-10deg);
        }
        .stat-modern-box.info { background-color: var(--info-bg); }
        .stat-modern-box.info .val { color: var(--info); }
        
        .stat-modern-box.success { background-color: var(--success-bg); }
        .stat-modern-box.success .val { color: var(--success); }
        
        .stat-modern-box.warning { background-color: var(--warning-bg); }
        .stat-modern-box.warning .val { color: var(--warning); }
        
        .stat-modern-box.danger { background-color: var(--danger-bg); }
        .stat-modern-box.danger .val { color: var(--danger); }

        .stat-modern-box .val { font-size: 2.2rem; font-weight: 800; line-height: 1.1; margin-bottom: 0.25rem; z-index: 1;}
        .stat-modern-box .lbl { font-size: 0.85rem; font-weight: 600; color: var(--text-muted); z-index: 1; text-transform: uppercase; letter-spacing: 0.5px;}

        /* PROGRESS BAR */
        .progress-modern {
            height: 28px;
            background-color: #e2e8f0;
            border-radius: 30px;
            overflow: hidden;
            position: relative;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.05);
        }
        .progress-modern-bar {
            background: linear-gradient(90deg, var(--success), #34d399);
            height: 100%;
            border-radius: 30px;
            transition: width 1s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .progress-modern-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #fff;
            font-weight: 800;
            font-size: 0.9rem;
            text-shadow: 0 1px 2px rgba(0,0,0,0.3);
        }

        /* PANELS */
        .action-panel-modern {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 1.75rem;
            height: 100%;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .action-panel-modern:hover {
            box-shadow: 0 8px 25px rgba(0,0,0,0.04);
            transform: translateY(-2px);
        }

        /* ADMIN TABLE */
        .admin-table th {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #94a3b8;
            font-weight: 700;
            background-color: #f8fafc;
            border-bottom: 2px solid #e2e8f0;
            padding: 1rem;
        }
        .admin-table td {
            vertical-align: middle;
            padding: 1.25rem 1rem;
            border-bottom: 1px dashed #e2e8f0;
        }
        .admin-table tbody tr:hover { background-color: #f1f5f9; }
        .admin-table tbody tr:last-child td { border-bottom: none; }
        
        .badge-nilai {
            font-size: 1rem;
            font-weight: 700;
            padding: 0.5em 1.2em;
            border-radius: 50px;
        }

        /* =========================================
           STYLE ID CARD (JANGAN DIUBAH AGAR RENDER AMAN)
           ========================================= */
        .id-card-wrapper.theme-black .card-header-shape { background: linear-gradient(135deg, #111827 0%, #374151 100%); }
        .id-card-wrapper.theme-black .card-role { background-color: #f1f5f9; color: #111827; border-color: #cbd5e1; }
        .id-card-wrapper.theme-black .qr-text { color: #111827; }
        .id-card-wrapper.theme-black .card-footer-shape { background: #111827; }
        .id-card-wrapper.theme-blue .card-header-shape { background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); }
        .id-card-wrapper.theme-blue .card-role { background-color: #eff6ff; color: #1e3a8a; border-color: #bfdbfe; }
        .id-card-wrapper.theme-blue .qr-text { color: #1e3a8a; }
        .id-card-wrapper.theme-blue .card-footer-shape { background: #1e3a8a; }
        .id-card-wrapper.theme-green .card-header-shape { background: linear-gradient(135deg, #14532d 0%, #22c55e 100%) !important; }
        .id-card-wrapper.theme-green .card-role { background-color: #f0fdf4 !important; color: #14532d !important; border-color: #bbf7d0 !important; }
        .id-card-wrapper.theme-green .qr-text { color: #14532d !important; }
        .id-card-wrapper.theme-green .card-footer-shape { background: #14532d !important; }
        .id-card-wrapper.theme-purple .card-header-shape { background: linear-gradient(135deg, #581c87 0%, #a855f7 100%); }
        .id-card-wrapper.theme-purple .card-role { background-color: #faf5ff; color: #581c87; border-color: #e9d5ff; }
        .id-card-wrapper.theme-purple .qr-text { color: #581c87; }
        .id-card-wrapper.theme-purple .card-footer-shape { background: #581c87; }
        .id-card-wrapper.theme-orange .card-header-shape { background: linear-gradient(135deg, #9a3412 0%, #f97316 100%); }
        .id-card-wrapper.theme-orange .card-role { background-color: #fff7ed; color: #9a3412; border-color: #fed7aa; }
        .id-card-wrapper.theme-orange .qr-text { color: #9a3412; }
        .id-card-wrapper.theme-orange .card-footer-shape { background: #9a3412; }
        /* THEME YELLOW (SMK Non-Kesehatan) */
.id-card-wrapper.theme-yellow .card-header-shape { background: linear-gradient(135deg, #b45309 0%, #eab308 100%) !important; }
.id-card-wrapper.theme-yellow .card-role { background-color: #fefce8 !important; color: #b45309 !important; border-color: #fef08a !important; }
.id-card-wrapper.theme-yellow .qr-text { color: #b45309 !important; }
.id-card-wrapper.theme-yellow .card-footer-shape { background: #b45309 !important; }
        .id-card-wrapper { width: 320px; height: 520px; background: #ffffff; position: relative; overflow: hidden; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); font-family: 'Segoe UI', sans-serif; display: flex; flex-direction: column; align-items: center; text-align: center; }
        .card-bg-decoration { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-image: radial-gradient(#cbd5e1 1px, transparent 1px); background-size: 20px 20px; opacity: 0.5; z-index: 0; }
        .card-big-bg-logo { position: absolute; top: 90%; transform: translateY(-50%); width: 300px; height: 300px; object-fit: contain; opacity: 0.12; z-index: 1; pointer-events: none; }
        .bg-left { left: -120px; } .bg-right { right: -120px; left: auto; }
        .card-header-shape { width: 100%; height: 140px; background: linear-gradient(135deg, #7c1316 0%, #a3191d 100%); border-bottom-left-radius: 40px; border-bottom-right-radius: 40px; position: relative; z-index: 2; color: white; display: flex; justify-content: center; align-items: flex-start; padding: 25px 20px 0 20px; }
        .card-title-text { text-align: center; color: white; }
        .card-title-text h6 { font-size: 11px; text-transform: uppercase; letter-spacing: 3px; margin-bottom: 4px; opacity: 0.9; }
        .card-title-text h4 { font-size: 15px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; margin: 0; line-height: 1.1; }
        .card-photo-container { margin-top: -35px; z-index: 3; position: relative; }
        .card-photo { width: 110px; height: 110px; border-radius: 50%; object-fit: cover; border: 5px solid #ffffff; box-shadow: 0 4px 8px rgba(0,0,0,0.15); background: #f1f5f9; }
        .card-content { padding: 5px 25px 15px 25px; width: 100%; z-index: 3; flex-grow: 1; display: flex; flex-direction: column; align-items: center; }
        .card-name { font-size: 15px; font-weight: 700; color: #1e293b; margin-top: 8px; line-height: 1.2; }
        .card-role { background-color: #fef2f2; color: #7c1316; font-size: 10px; font-weight: 700; padding: 4px 12px; border-radius: 20px; margin-top: 6px; margin-bottom: 15px; letter-spacing: 0.5px; border: 1px solid #fecaca; text-transform: uppercase; }
        .detail-box { width: 100%; text-align: left; }
        .detail-row { display: flex; justify-content: space-between; border-bottom: 1px dashed #cbd5e1; padding: 6px 0; }
        .detail-row:last-child { border-bottom: none; }
        .detail-label { color: #64748b; font-weight: 600; font-size: 10px; text-transform: uppercase; }
        .detail-val { color: #334155; font-weight: 700; text-align: right; max-width: 65%; font-size: 11px; }
        .card-qr-section { margin-top: auto; margin-bottom: 25px; z-index: 3; background: white; padding: 6px; border-radius: 8px; border: 1px solid #f1f5f9; box-shadow: 0 2px 6px rgba(0,0,0,0.05); position: relative; min-width: 90px; }
        .card-qr img { width: 65px; height: 65px; display: block; margin: 0 auto; }
        .qr-text { font-size: 9px; font-weight: 700; color: #7c1316; margin-top: 4px; letter-spacing: 1px; display: block; }
        .card-footer-shape { position: absolute; bottom: 0; left: 0; width: 100%; height: 15px; background: #7c1316; z-index: 2; }
    </style>

    <div class="container-fluid px-3 px-md-4 py-4">
        
        <!-- BAGIAN 1: HEADER & PROFIL -->
        <div class="modern-card p-4">
            <div class="row align-items-center">
                <div class="col-md-8 d-flex align-items-center mb-3 mb-md-0">
                    <div class="avatar-lg me-4 flex-shrink-0">
                        {{ strtoupper(substr($mahasiswa->nm_mahasiswa, 0, 1)) }}
                    </div>
                    <div>
                        <h3 class="mb-1 fw-bolder text-dark">{{ $mahasiswa->nm_mahasiswa }}</h3>
                        <div class="d-flex flex-wrap gap-2 align-items-center text-muted">
                            <span class="badge bg-light text-dark border"><i class="bi bi-building me-1"></i> {{ $mahasiswa->univ_asal ?? '-' }}</span>
                            <span class="badge bg-light text-dark border"><i class="bi bi-book me-1"></i> {{ $mahasiswa->prodi ?? '-' }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-md-end">
                    <div class="d-flex gap-2 justify-content-md-end">
                        <button type="button" class="btn btn-outline-dark fw-bold rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#idCardModal">
                            <i class="bi bi-person-badge me-2"></i> Preview ID Card
                        </button>
                        <a href="{{ route('absensi.card', $mahasiswa->share_token) }}" target="_blank" class="btn btn-danger fw-bold rounded-pill px-4" style="background-color: var(--custom-maroon); border-color: var(--custom-maroon);">
                            <i class="bi bi-upc-scan me-2"></i> Link Card
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- BAGIAN 2: STATISTIK GLOBAL -->
        <div class="modern-card p-4 p-md-5">
            <h5 class="section-title"><i class="bi bi-bar-chart-fill text-primary me-2 fs-4"></i> Statistik Kehadiran Magang</h5>
            
            <div class="row g-3 mb-5">
                <div class="col-6 col-md-3">
                    <div class="stat-modern-box info h-100">
                        <i class="bi bi-calendar-range icon-bg"></i>
                        <div class="val">{{ $totalExpectedDays }}</div>
                        <div class="lbl">Total Hari Magang</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-modern-box success h-100">
                        <i class="bi bi-check-circle icon-bg"></i>
                        <div class="val">{{ $totalActualDays }}</div>
                        <div class="lbl">Total Hadir</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-modern-box warning h-100">
                        <i class="bi bi-envelope-paper icon-bg"></i>
                        <div class="val">{{ $totalIzin }}</div>
                        <div class="lbl">Total Dispen/Izin</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-modern-box danger h-100">
                        <i class="bi bi-x-octagon icon-bg"></i>
                        <div class="val">{{ $totalAlpaDays }}</div>
                        <div class="lbl">Total Alfa</div>
                    </div>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="bg-light p-4 rounded-4 border">
                <div class="d-flex justify-content-between align-items-end mb-2">
                    <span class="fw-bold text-dark text-uppercase" style="letter-spacing: 0.5px; font-size: 0.85rem;">Tingkat Partisipasi Berjalan</span>
                    <span class="fw-bolder text-success fs-5">{{ $participationRate }}%</span>
                </div>
                <div class="progress-modern">
                    <div class="progress-modern-bar" style="width: {{ $participationRate }}%;"></div>
                    <span class="progress-modern-text">{{ $participationRate }}%</span>
                </div>
                <div class="text-muted small mt-2">
                    <i class="bi bi-info-circle me-1"></i> Dihitung berdasarkan hari magang yang telah terlewati hingga hari ini.
                </div>
            </div>
        </div>

        <!-- BAGIAN 3: DOKUMEN & SERTIFIKAT -->
        <div class="row g-4 mb-4">
            <!-- Magang -->
            <div class="col-md-6">
                <div class="action-panel-modern d-flex flex-column">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-success-bg p-3 rounded-circle me-3 text-success">
                            <i class="bi bi-award-fill fs-3"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1 text-dark">Sertifikat Kelulusan</h5>
                            <span class="text-muted small">Mencakup rekap absen & nilai dari seluruh ruangan.</span>
                        </div>
                    </div>
                    
                    <div class="mt-auto pt-3">
                        <form action="{{ route('sertifikat.download', $mahasiswa->share_token) }}" method="GET" target="_blank">
                            <div class="input-group mb-3">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-percent text-muted"></i></span>
                                <input type="number" step="0.1" min="0" max="100" class="form-control border-start-0 bg-light" name="override_percentage" placeholder="Override % Absen (Opsional, kosongkan jika default)">
                            </div>
                            
                            @if (now()->gt(\Carbon\Carbon::parse($mahasiswa->tanggal_berakhir)->startOfDay()))
                                <button type="submit" class="btn btn-success w-100 rounded-pill fw-bold py-2">
                                    <i class="bi bi-printer me-2"></i> Cetak Sertifikat Magang
                                </button>
                            @else
                                <button class="btn btn-secondary w-100 rounded-pill fw-bold py-2" disabled>
                                    <i class="bi bi-lock-fill me-2"></i> Magang Belum Selesai
                                </button>
                                <div class="text-center mt-2 small text-danger">Tersedia setelah: {{ \Carbon\Carbon::parse($mahasiswa->tanggal_berakhir)->format('d M Y') }}</div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>

            <!-- Orientasi -->
            <div class="col-md-6">
                <div class="action-panel-modern d-flex flex-column">
                    <div class="d-flex align-items-center mb-3">
                        <div class="p-3 rounded-circle me-3" style="background-color: var(--custom-maroon-subtle); color: var(--custom-maroon);">
                            <i class="bi bi-shield-check fs-3"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1 text-dark">Sertifikat Orientasi</h5>
                            <span class="text-muted small">Bukti penyelesaian Pre-Test & Post-Test RSUD SLG.</span>
                        </div>
                    </div>

                    <div class="bg-light rounded-3 p-3 mb-4 mt-2 d-flex justify-content-between align-items-center border">
                        <span class="fw-bold text-secondary small text-uppercase">Status Kelulusan</span>
                        @if($orientasi && $orientasi->status === 'lulus_orientasi')
                            <span class="badge bg-success py-2 px-3 rounded-pill"><i class="bi bi-check2-circle me-1"></i> LULUS</span>
                        @else
                            <span class="badge bg-danger py-2 px-3 rounded-pill"><i class="bi bi-x-circle me-1"></i> BELUM LULUS</span>
                        @endif
                    </div>
                    
                    <div class="mt-auto">
                        @if($orientasi && $orientasi->status === 'lulus_orientasi')
                            <a href="{{ route('admin.orientasi.sertifikat_user', $mahasiswa->user_id) }}" target="_blank" class="btn w-100 rounded-pill fw-bold py-2" style="background-color: var(--custom-maroon); color: white;">
                                <i class="bi bi-printer me-2"></i> Cetak Sertifikat Orientasi
                            </a>
                        @else
                            <button class="btn btn-secondary w-100 rounded-pill fw-bold py-2" disabled>
                                <i class="bi bi-lock-fill me-2"></i> Tidak Tersedia
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- BAGIAN 4: PANEL ADMIN (MANAJEMEN NILAI) -->
        <div class="modern-card border-top border-4 border-danger">
            <div class="card-body p-4 p-md-5">
                
                <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
                    <h5 class="fw-bold mb-0 text-dark d-flex align-items-center">
                        <i class="bi bi-journal-medical me-2 fs-3 text-danger"></i> 
                        Manajemen Penilaian Ruangan
                    </h5>
                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-2 fw-bold shadow-sm">
                        <i class="bi bi-shield-lock me-1"></i> AKSES KHUSUS ADMIN
                    </span>
                </div>

                <div class="alert alert-secondary border-0 bg-light d-flex align-items-center p-3 rounded-4 mb-4">
                    <i class="bi bi-info-circle-fill text-muted fs-3 me-3"></i>
                    <div class="small text-dark">
                        Gunakan tabel di bawah untuk memantau atau mengkoreksi nilai yang diberikan oleh Kepala Ruangan.<br>
                        <strong>Catatan:</strong> Jika Anda menekan <strong class="text-danger">Reset</strong>, nilai akan dihapus dan mahasiswa akan muncul kembali di antrean penilaian Kepala Ruangan.
                    </div>
                </div>

                <div class="table-responsive bg-white border rounded-4">
                    <table class="table admin-table mb-0 w-100" style="min-width: 700px;">
                        <thead>
                            <tr>
                                <th width="35%">Nama Ruangan</th>
                                <th width="25%">Periode Praktik</th>
                                <th width="15%" class="text-center">Skor Nilai</th>
                                <th width="25%" class="text-end pe-4">Aksi / Edit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($mahasiswa->roomSequences->unique('ruangan_id') as $seq)
                                @php
                                    $rId = $seq->ruangan_id;
                                    $adaNilai = array_key_exists($rId, $nilaiRuangan);
                                    $nilai = $nilaiRuangan[$rId] ?? null;
                                @endphp
                                <tr>
                                    <td>
                                        <!-- FIX: Menggunakan nm_ruangan (Sesuai Kolom Database) -->
                                        <div class="fw-bolder text-dark fs-6">{{ $seq->ruangan->nm_ruangan ?? 'Ruangan Terhapus' }}</div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center text-muted small fw-medium">
                                            <i class="bi bi-calendar-event me-2 text-secondary"></i>
                                            <div>
                                                {{ \Carbon\Carbon::parse($seq->start_date)->format('d M Y') }} <br>
                                                <span class="text-primary opacity-75">s/d {{ \Carbon\Carbon::parse($seq->end_date)->format('d M Y') }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @if($adaNilai && $nilai !== null)
                                            <span class="badge badge-nilai bg-success-subtle text-success border border-success-subtle">
                                                {{ $nilai }}
                                            </span>
                                        @else
                                            <span class="badge badge-nilai bg-light text-muted border fw-normal">Belum Dinilai</span>
                                        @endif
                                    </td>
                                    <td class="pe-4">
                                        <form action="{{ route('admin.mahasiswa.update_nilai_ruangan', $mahasiswa->id) }}" method="POST" class="d-flex justify-content-end gap-2 mb-0">
                                            @csrf
                                            <input type="hidden" name="ruangan_id" value="{{ $rId }}">
                                            
                                            <input type="number" step="0.1" name="nilai" class="form-control text-center fw-bold bg-light" style="width: 80px;" value="{{ $nilai }}" placeholder="0-100" min="0" max="100" required>
                                            
                                            <button type="submit" class="btn btn-primary" title="Simpan Perubahan">
                                                <i class="bi bi-floppy-fill"></i>
                                            </button>

                                            @if($adaNilai && $nilai !== null)
                                                <button type="submit" name="reset_nilai" value="1" class="btn btn-outline-danger bg-white" onclick="return confirm('Yakin ingin mereset nilai ini? Kepala Ruangan harus menilainya ulang.')" formnovalidate title="Hapus / Reset Nilai">
                                                    <i class="bi bi-arrow-counterclockwise"></i>
                                                </button>
                                            @endif
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <div class="text-muted d-flex flex-column align-items-center">
                                            <i class="bi bi-inboxes text-light" style="font-size: 3rem;"></i>
                                            <span class="mt-2 fw-medium">Belum ada riwayat plotting ruangan.</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="text-center py-4">
            <a href="{{ route('mahasiswa.index') }}" class="btn btn-white border rounded-pill px-5 py-2 text-dark fw-bold shadow-sm">
                <i class="bi bi-arrow-left me-2"></i> Kembali ke Daftar Mahasiswa
            </a>
        </div>
    </div>

    <!-- MODAL PREVIEW ID CARD -->
    <div class="modal fade" id="idCardModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-dark fs-6">Preview ID Card</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body bg-light text-center py-4" style="border-bottom-left-radius: 20px; border-bottom-right-radius: 20px;">
                    
                    {{-- Capture Area --}}
                    <div id="captureArea" class="d-inline-block shadow-lg rounded-3 mb-4">
                      @php
    // Bersihkan nama prodi dan jadikan huruf kapital semua agar mudah dicek
    $prodiMhs = strtoupper(trim($mahasiswa->prodi));
    $instansi = $instansiName ?? $mahasiswa->univ_asal ?? 'Instansi Tidak Diketahui';

    // 1. KATA KUNCI PENENTU KATEGORI
    // Tambahkan kata kunci jurusan kesehatan di sini jika ada yang kurang
    $kesehatanKeywords = ['KEPERAWATAN', 'FARMASI', 'KEBIDANAN', 'GIZI', 'KESEHATAN', 'MEDIK', 'RADIOLOGI', 'FISIOTERAPI', 'ANALIS', 'TLM', 'ARS', 'ELEKTROMEDIK', 'DOKTER', 'KEDOKTERAN', 'PSIKOLOGI'];
    $profesiKeywords = ['PROFESI', 'PPDS', 'KOAS', 'NERS', 'APOTEKER'];

    // 2. DETEKSI STATUS
    $isProfesi = \Illuminate\Support\Str::contains($prodiMhs, $profesiKeywords);
    $isKesehatan = \Illuminate\Support\Str::contains($prodiMhs, $kesehatanKeywords);
    // Cek apakah ada kata "SMK" di nama prodinya
    $isSMK = \Illuminate\Support\Str::startsWith($prodiMhs, 'SMK') || \Illuminate\Support\Str::contains($prodiMhs, 'SMK ');

    // 3. TERAPKAN ATURAN SESUAI KETENTUAN RSUD SLG
    $themeClass = '';
    $tingkatSupervisi = '';

    if ($isProfesi) {
        // ATURAN 1: Semua Profesi -> Merah (Bawaan), Moderat Tinggi
        $themeClass = ''; 
        $tingkatSupervisi = 'Moderat Tinggi';
    } elseif ($isKesehatan) {
        // ATURAN 2: Semua Kesehatan (termasuk SMK) KECUALI Profesi -> Hitam, Tinggi
        $themeClass = 'theme-black';
        $tingkatSupervisi = 'Tinggi';
    } else {
        // JIKA BUKAN KESEHATAN SAMA SEKALI
        if ($isSMK) {
            // ATURAN 3: SMK Non-Kesehatan -> Kuning, Moderat
            $themeClass = 'theme-yellow'; 
            $tingkatSupervisi = 'Moderat';
        } else {
            // ATURAN 4: S2, S1, D4, D3 Non-Kesehatan -> Hijau, Rendah
            $themeClass = 'theme-green';
            $tingkatSupervisi = 'Rendah';
        }
    }
@endphp
                        
                        <div class="id-card-wrapper {{ $themeClass }}">
                            <div class="card-bg-decoration"></div>
                            
                            <img src="{{ asset('icon.png') }}" class="card-big-bg-logo bg-left" alt="bg-icon">
                            <img src="{{ asset('logors.png') }}" class="card-big-bg-logo bg-right" alt="bg-rs">

                            <div class="card-header-shape">
                                <div class="card-title-text">
                                    <h6>Kartu Tanda</h6>
                                    <h4>Peserta Magang Rsud Simpang Lima Gumul</h4>
                                </div>
                            </div>

                            <div class="card-photo-container">
                                @if($mahasiswa->foto_path)
                                    <img src="{{ asset($mahasiswa->foto_path) }}" class="card-photo" alt="Foto">
                                @else
                                    <div class="card-photo d-flex align-items-center justify-content-center text-secondary">
                                        <i class="bi bi-person-fill display-4"></i>
                                    </div>
                                @endif
                            </div>

                            <div class="card-content">
                                <div class="card-name">{{ $mahasiswa->nm_mahasiswa }}</div>
                                <div class="card-role">MAGANG / INTERNSHIP</div>

                                <div class="detail-box">
                                    <div class="detail-row">
                                        <span class="detail-label">INSTANSI</span>
                                        <span class="detail-val">{{ Str::limit($instansi, 20) }}</span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="detail-label">PRODI</span>
                                      <span class="detail-val">{{ Str::words($mahasiswa->prodi, 3, '...') }}</span>
                                    </div>
                                   <div class="detail-row">
                                        <span class="detail-label">SUPERVISI</span>
                                        <span class="detail-val text-primary">{{ $tingkatSupervisi }}</span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="detail-label">BERLAKU S/D</span>
                                        <span class="detail-val text-danger">{{ \Carbon\Carbon::parse($mahasiswa->tanggal_berakhir)->format('d/m/Y') }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="card-qr-section text-center">
                                <div class="card-qr">
                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ route('absensi.card', $mahasiswa->share_token) }}" alt="QR">
                                </div>
                                <span class="qr-text">SCAN ME</span>
                            </div>

                            <div class="card-footer-shape"></div>
                        </div>
                    </div>

                    <div>
                        <button onclick="downloadIdCard()" class="btn btn-dark rounded-pill px-4 shadow-sm py-2 fw-bold">
                            <i class="bi bi-download me-2"></i> Download (PNG)
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Panggil html2canvas -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    
    <script>
        function downloadIdCard() {
            const element = document.getElementById('captureArea');
            html2canvas(element, { 
                scale: 3, 
                backgroundColor: null,
                useCORS: true 
            }).then(canvas => {
                const link = document.createElement('a');
                link.download = 'ID-Card-{{ \Illuminate\Support\Str::slug($mahasiswa->nm_mahasiswa) }}.png';
                link.href = canvas.toDataURL('image/png');
                link.click();
            });
        }
    </script>
@endsection