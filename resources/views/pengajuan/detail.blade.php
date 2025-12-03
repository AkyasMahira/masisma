@extends('layouts.app')

@section('title', 'Detail Pengajuan')
@section('page-title', 'Detail Pengajuan ' . ($jenis === 'pra_penelitian' ? 'Pra Penelitian' : 'Magang'))

@section('content')
<style>
            :root {
            --primary-maroon: #7c1316;
            --primary-maroon-hover: #5e0e10;
            --bg-light: #f8fafc;
            --text-dark: #1e293b;
            --text-muted: #64748b;
            --card-radius: 16px;
            --shadow-sm: 0 2px 4px rgba(0,0,0,0.02);
            --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.1);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body { background-color: var(--bg-light); }

        /* --- Header --- */
        .page-header-wrapper {
            background: #fff;
            border-radius: var(--card-radius);
            padding: 1.5rem 2rem;
            margin-bottom: 2rem;
            border-left: 5px solid var(--primary-maroon);
            box-shadow: var(--shadow-sm);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* --- Sidebar Status Card --- */
        .sticky-sidebar { position: sticky; top: 2rem; z-index: 10; }

        .status-card-main {
            background: #fff; border-radius: var(--card-radius); box-shadow: var(--shadow-md);
            padding: 2.5rem 1.5rem; text-align: center; border: 1px solid #e2e8f0;
        }

        .status-icon-ring {
            width: 100px; height: 100px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1.5rem; font-size: 3rem; position: relative;
        }
        .status-icon-ring::after {
            content: ''; position: absolute; top: -5px; left: -5px; right: -5px; bottom: -5px;
            border-radius: 50%; border: 2px dashed currentColor; opacity: 0.3;
            animation: spin 10s linear infinite;
        }
        @keyframes spin { 100% { transform: rotate(360deg); } }

        .st-pending { background: #fff7ed; color: #f59e0b; }
        .st-success { background: #f0fdf4; color: #10b981; }
        .st-danger { background: #fef2f2; color: #ef4444; }

        /* --- PERBAIKAN TIMELINE STEPS (AGAR GARIS TIDAK NEMPEL) --- */
        .timeline-wrapper {
            position: relative;
            padding-left: 60px; /* Memberi ruang di kiri untuk garis & nomor */
        }

        .step-card {
            background: #fff;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            margin-bottom: 2rem;
            position: relative;
            transition: var(--transition);
            /* overflow: hidden;  <-- HAPUS INI agar badge bisa keluar dari kotak */
        }

        .step-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
            border-color: #cbd5e1;
        }

        /* Connecting Line (Garis Vertikal) */
        .step-card::before {
            content: '';
            position: absolute;
            left: -37px; /* Geser ke kiri luar card */
            top: 40px;   /* Mulai dari tengah badge */
            bottom: -50px; /* Sambung ke card bawahnya */
            width: 2px;
            background: #e2e8f0;
            z-index: 0;
        }
        
        /* Hilangkan garis pada item terakhir */
        .step-card:last-child::before { display: none; }

        .step-header {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #f1f5f9;
            display: flex; align-items: center; gap: 1rem;
            background-color: #fff;
            position: relative; /* Penting untuk patokan absolute badge */
            z-index: 1;
            border-radius: 12px 12px 0 0;
        }

        .step-header.active { background-color: #fdfbfb; }
        .step-header.done { background-color: #f0fdf4; }

        /* Step Number (Badge Bulat) */
        .step-number {
            position: absolute; /* Pindah ke luar flow header */
            left: -54px; /* Geser ke kiri luar card */
            top: 50%; transform: translateY(-50%); /* Center vertikal terhadap header */
            
            width: 36px; height: 36px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 0.9rem;
            background: #f1f5f9; color: #94a3b8;
            border: 2px solid #cbd5e1;
            z-index: 2;
            box-shadow: 0 0 0 4px #f8fafc; /* Border luar warna background body agar garis terlihat terpotong rapi */
        }

        /* Warna State Badge */
        .step-header.active .step-number {
            background: var(--primary-maroon); color: white;
            border-color: var(--primary-maroon);
        }
        .step-header.done .step-number {
            background: #10b981; color: white;
            border-color: #10b981;
        }
        
        /* Warna State Card Border */
        .step-card:has(.step-header.active) { border-color: var(--primary-maroon); box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .step-card:has(.step-header.done) { border-color: #10b981; }

        .step-body { padding: 1.5rem; }

        /* --- Sisa CSS (File Card, Button, Upload) tetap sama --- */
        .file-card {
            display: flex; align-items: center; justify-content: space-between;
            padding: 1rem 1.25rem; background: #fff; border: 1px solid #e2e8f0;
            border-radius: 10px; text-decoration: none; transition: var(--transition);
        }
        .file-card:hover { border-color: var(--primary-maroon); background-color: #fff9fa; transform: translateX(5px); }
        
        .file-icon { width: 45px; height: 45px; background: #fee2e2; color: #dc2626; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; margin-right: 1rem; }
        .file-icon.invoice { background: #fef3c7; color: #d97706; }

        .btn-maroon { background-color: var(--primary-maroon); color: white; padding: 0.75rem 1.5rem; border-radius: 8px; border: none; font-weight: 500; width: 100%; transition: var(--transition); }
        .btn-maroon:hover { background-color: var(--primary-maroon-hover); color: white; transform: translateY(-1px); }

        .btn-outline-back { background: transparent; border: 1px solid #cbd5e1; color: var(--text-dark); padding: 0.5rem 1rem; border-radius: 8px; font-weight: 500; text-decoration: none; transition: var(--transition); }
        .btn-outline-back:hover { background: #e2e8f0; border-color: #94a3b8; color: var(--text-dark); }

        .upload-zone { border: 2px dashed #cbd5e1; border-radius: 10px; padding: 1.5rem; text-align: center; background: #f8fafc; transition: var(--transition); }
        .upload-zone:hover { border-color: var(--primary-maroon); background: #fff; }

        .animate-up { animation: fadeUp 0.6s ease-out forwards; opacity: 0; transform: translateY(20px); }
        @keyframes fadeUp { to { opacity: 1; transform: translateY(0); } }
    </style>

    <div class="container py-4">

        {{-- Header Page --}}
        <div class="page-header-wrapper animate-up">
            <div>
                <h4 class="fw-bold mb-1" style="color: var(--primary-maroon);">
                    <i class="bi bi-file-earmark-text me-2"></i>Detail Pengajuan
                </h4>
                <p class="text-muted mb-0 small">Lacak progres pengajuan {{ $jenis === 'pra_penelitian' ? 'Pra Penelitian' : 'Magang' }} Anda secara real-time.</p>
            </div>
            <div>
                <a href="{{ route('pengajuan.index') }}" class="btn-outline-back d-inline-flex align-items-center">
                    <i class="bi bi-arrow-left me-2"></i> Kembali
                </a>
            </div>
        </div>

        {{-- Alerts --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show animate-up shadow-sm border-0 mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bi bi-check-circle-fill fs-4 me-3 text-success"></i>
                    <div>{{ session('success') }}</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show animate-up shadow-sm border-0 mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bi bi-exclamation-triangle-fill fs-4 me-3 text-danger"></i>
                    <div>{{ session('error') }}</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- MAIN CONTENT --}}
        @if ($jenis === 'pra_penelitian')
            @php
                $praPenelitian = App\Models\PraPenelitian::where('user_id', auth()->id())->first();
                
                // Logic Visual Step Classes
                // Step 1
                $step1Class = 'active'; 
                $step1Icon = '1';
                $step1TitleColor = 'text-dark';
                if ($praPenelitian) {
                    if ($praPenelitian->status == 'Approved') { 
                        $step1Class = 'done'; 
                        $step1Icon = '<i class="bi bi-check-lg"></i>'; 
                        $step1TitleColor = 'text-success';
                    } elseif ($praPenelitian->status == 'Rejected') { 
                        $step1Class = 'active'; // Tetap active agar bisa diedit
                    }
                }

                // Step 2
                $step2Class = ''; 
                $step2Icon = '2';
                if ($praPenelitian && $praPenelitian->status == 'Approved') {
                    if ($pengajuan->status_galasan == 'sent') {
                        $step2Class = 'done';
                        $step2Icon = '<i class="bi bi-check-lg"></i>';
                    } else {
                        $step2Class = 'active';
                    }
                }

                // Step 3
                $step3Class = '';
                $step3Icon = '3';
                if ($pengajuan->status_galasan == 'sent') {
                    if ($pengajuan->status_pembayaran == 'verified') {
                        $step3Class = 'done';
                        $step3Icon = '<i class="bi bi-check-lg"></i>';
                    } else {
                        $step3Class = 'active';
                    }
                }
            @endphp

            <div class="row g-4">
                
                {{-- LEFT COLUMN: STATUS (STICKY) --}}
                <div class="col-lg-4 animate-up" style="animation-delay: 0.1s;">
                    <div class="sticky-sidebar">
                        <div class="status-card-main">
                            @if($pengajuan->status == 'approved')
                                 <div class="status-icon-ring st-success">
                                    <i class="bi bi-check-lg"></i>
                                </div>
                                <h4 class="fw-bold text-dark mb-1">Disetujui</h4>
                                <span class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-2 rounded-pill mt-2">Verified</span>
                                <p class="text-muted small mt-3">Selamat! Pengajuan Anda telah diterima dan proses administrasi selesai.</p>
                            @elseif($pengajuan->status == 'rejected')
                                <div class="status-icon-ring st-danger">
                                    <i class="bi bi-x-lg"></i>
                                </div>
                                <h4 class="fw-bold text-dark mb-1">Ditolak</h4>
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-3 py-2 rounded-pill mt-2">Rejected</span>
                                <p class="text-muted small mt-3">Mohon periksa kembali data Anda atau hubungi admin.</p>
                            @else
                                <div class="status-icon-ring st-pending">
                                    <i class="bi bi-hourglass-split"></i>
                                </div>
                                <h4 class="fw-bold text-dark mb-1">Dalam Proses</h4>
                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning px-3 py-2 rounded-pill mt-2">Pending</span>
                                <p class="text-muted small mt-3">Silakan lengkapi tahapan di sebelah kanan untuk melanjutkan.</p>
                            @endif

                            <div class="border-top mt-4 pt-4 text-start">
                                <div class="d-flex justify-content-between mb-2">
                                    <small class="text-muted">Tanggal Pengajuan</small>
                                    <small class="fw-bold text-dark">{{ $pengajuan->created_at->format('d M Y') }}</small>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <small class="text-muted">Terakhir Update</small>
                                    <small class="fw-bold text-dark">{{ $pengajuan->updated_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- RIGHT COLUMN: TIMELINE STEPS --}}
                <div class="col-lg-8 animate-up" style="animation-delay: 0.2s;">
                    <div class="timeline-wrapper">
                        
                        {{-- STEP 1: BIODATA FORM --}}
                        <div class="step-card">
                            <div class="step-header {{ $step1Class }}">
                                <div class="step-number">{!! $step1Icon !!}</div>
                                <div>
                                    <h6 class="mb-0 fw-bold {{ $step1Class == 'done' ? 'text-success' : 'text-dark' }}">Biodata Pra Penelitian</h6>
                                    @if($step1Class == 'done') <small class="text-muted text-xs">Data telah diverifikasi</small> @endif
                                </div>
                            </div>
                            <div class="step-body">
                                @if (!$praPenelitian)
                                    <div class="text-center py-3">
                                        <p class="text-muted mb-3">Langkah pertama, lengkapi data diri dan judul penelitian.</p>
                                        <a href="{{ route('pra-penelitian.create') }}" class="btn btn-maroon w-auto px-4">
                                            <i class="bi bi-pencil-square me-2"></i> Isi Formulir
                                        </a>
                                    </div>
                                @elseif ($praPenelitian->status === 'Pending')
                                    <div class="alert alert-warning border-0 d-flex align-items-center m-0">
                                        <i class="bi bi-clock-history me-3 fs-4"></i>
                                        <div>
                                            <strong>Menunggu Verifikasi</strong><br>
                                            <small>Formulir Anda sedang ditinjau oleh Admin.</small>
                                        </div>
                                    </div>
                                @elseif ($praPenelitian->status === 'Rejected')
                                    <div class="alert alert-danger border-0 mb-3">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-x-circle-fill me-3 fs-4"></i>
                                            <div>
                                                <strong>Formulir Ditolak</strong><br>
                                                <small>Ada data yang perlu diperbaiki.</small>
                                            </div>
                                        </div>
                                    </div>
                                    <a href="{{ route('pra-penelitian.edit', $praPenelitian->id) }}" class="btn btn-outline-danger w-100">
                                        <i class="bi bi-pencil-square me-2"></i> Edit Formulir
                                    </a>
                                @else
                                    <div class="d-flex align-items-start p-3 bg-light rounded border">
                                        <i class="bi bi-file-text-fill text-muted fs-4 me-3"></i>
                                        <div>
                                            <span class="d-block text-muted small text-uppercase fw-bold">Judul Penelitian</span>
                                            <span class="fw-medium text-dark">{{ $praPenelitian->judul }}</span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- STEP 2: SURAT BALASAN --}}
                        @if ($praPenelitian && $praPenelitian->status === 'Approved')
                            <div class="step-card">
                                <div class="step-header {{ $step2Class }}">
                                    <div class="step-number">{!! $step2Icon !!}</div>
                                    <h6 class="mb-0 fw-bold">Dokumen Balasan & Invoice</h6>
                                </div>
                                <div class="step-body">
                                    @if ($pengajuan->status_galasan === 'pending')
                                        <div class="text-center py-4">
                                            <div class="spinner-border text-secondary mb-3" role="status" style="width: 2rem; height: 2rem;"></div>
                                            <p class="text-muted mb-0">Sedang diproses Admin...</p>
                                        </div>
                                    @else
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                @if ($pengajuan->surat_balasan)
                                                    <a href="{{ Storage::url($pengajuan->surat_balasan) }}" target="_blank" class="file-card">
                                                        <div class="d-flex align-items-center">
                                                            <div class="file-icon"><i class="bi bi-file-pdf-fill"></i></div>
                                                            <div>
                                                                <div class="fw-bold text-dark small">Surat Balasan</div>
                                                                <div class="text-xs text-muted">Unduh PDF</div>
                                                            </div>
                                                        </div>
                                                        <i class="bi bi-download text-muted"></i>
                                                    </a>
                                                @endif
                                            </div>
                                            <div class="col-md-6">
                                                @if ($pengajuan->invoice)
                                                    <a href="{{ Storage::url($pengajuan->invoice) }}" target="_blank" class="file-card">
                                                        <div class="d-flex align-items-center">
                                                            <div class="file-icon invoice"><i class="bi bi-receipt"></i></div>
                                                            <div>
                                                                <div class="fw-bold text-dark small">Invoice Tagihan</div>
                                                                <div class="text-xs text-muted">Unduh PDF</div>
                                                            </div>
                                                        </div>
                                                        <i class="bi bi-download text-muted"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        {{-- STEP 3: PEMBAYARAN --}}
                        @if ($pengajuan->status_galasan === 'sent')
                            <div class="step-card">
                                <div class="step-header {{ $step3Class }}">
                                    <div class="step-number">{!! $step3Icon !!}</div>
                                    <h6 class="mb-0 fw-bold">Pembayaran & Penempatan</h6>
                                </div>
                                <div class="step-body">
                                    @if ($pengajuan->status_pembayaran === 'pending')
                                        <div class="upload-zone">
                                            <p class="text-muted small mb-3">Silakan lakukan pembayaran sesuai Invoice, lalu unggah buktinya di sini.</p>
                                            <form action="{{ route('pengajuan.upload-bukti', $pengajuan->id) }}" method="POST" enctype="multipart/form-data">
                                                @csrf
                                                <div class="mb-3 text-start">
                                                    <input type="file" name="bukti_pembayaran" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                                                    <div class="form-text">Format: PDF, JPG, PNG. Maks 2MB.</div>
                                                </div>
                                                <button type="submit" class="btn btn-maroon">
                                                    <i class="bi bi-cloud-upload me-2"></i> Kirim Bukti
                                                </button>
                                            </form>
                                        </div>

                                    @elseif ($pengajuan->status_pembayaran === 'uploaded')
                                        <div class="alert alert-info border-0 d-flex align-items-center">
                                            <i class="bi bi-hourglass-top me-3 fs-4"></i>
                                            <div>
                                                <strong>Bukti Terkirim</strong><br>
                                                <small>Admin sedang memverifikasi pembayaran Anda.</small>
                                            </div>
                                        </div>

                                    @elseif ($pengajuan->status_pembayaran === 'verified')
                                        <div class="alert alert-success border-0 d-flex align-items-center mb-4">
                                            <i class="bi bi-check-circle-fill me-3 fs-4"></i>
                                            <div>
                                                <strong>Pembayaran Lunas!</strong><br>
                                                <small>Proses administrasi selesai.</small>
                                            </div>
                                        </div>
                                        
                                        {{-- INFO CI --}}
                                        @if ($pengajuan->ci_nama)
                                            <div class="card border-0 bg-white shadow-sm overflow-hidden">
                                                <div class="card-header bg-white border-bottom py-3">
                                                    <h6 class="fw-bold text-dark mb-0"><i class="bi bi-person-badge me-2 text-primary"></i>Pembimbing Lapangan (CI)</h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row g-3">
                                                        <div class="col-md-6">
                                                            <small class="text-muted d-block mb-1">Nama Pembimbing</small>
                                                            <div class="fw-bold">{{ $pengajuan->ci_nama }}</div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <small class="text-muted d-block mb-1">Kontak (WhatsApp)</small>
                                                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $pengajuan->ci_no_hp) }}" target="_blank" class="btn btn-sm btn-success text-white px-3" style="border-radius: 20px;">
                                                                <i class="bi bi-whatsapp me-1"></i> {{ $pengajuan->ci_no_hp }}
                                                            </a>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <small class="text-muted d-block mb-1">Ruangan</small>
                                                            <div class="fw-bold">{{ $pengajuan->ruangan }}</div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <small class="text-muted d-block mb-1">Bidang Keahlian</small>
                                                            <div class="fw-bold">{{ $pengajuan->ci_bidang }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-footer bg-light p-3">
                                                    <a href="{{ route('konsultasi.index') }}" class="btn btn-maroon w-100">
                                                        <i class="bi bi-chat-dots-fill me-2"></i> Mulai Konsultasi Online
                                                    </a>
                                                </div>
                                            </div>
                                        @endif
                                        
                                    @endif
                                </div>
                            </div>
                        @endif

                    </div> {{-- End Timeline Wrapper --}}
                </div>
            </div>
        @endif
    </div>
@endsection