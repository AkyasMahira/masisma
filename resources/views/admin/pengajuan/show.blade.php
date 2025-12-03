@extends('layouts.app')

@section('title', 'Proses Pengajuan')
@section('page-title', 'Detail & Proses Pengajuan')

@section('content')
    <style>
        :root {
            --primary-maroon: #7c1316;
            --primary-blue: #0f172a;
            --success-green: #059669;
            --bg-light: #f8fafc;
            --card-radius: 12px;
            --transition: all 0.3s ease;
        }

        body { background-color: var(--bg-light); }

        /* --- Header --- */
        .page-header-wrapper {
            background: #fff;
            border-radius: var(--card-radius);
            padding: 1.5rem 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.03);
            margin-bottom: 2rem;
            border-left: 5px solid var(--primary-maroon);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* --- Info Cards (Left) --- */
        .info-card {
            background: #fff;
            border-radius: var(--card-radius);
            box-shadow: 0 2px 6px rgba(0,0,0,0.02);
            border: 1px solid #e2e8f0;
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

        .info-header {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #f1f5f9;
            font-weight: 700;
            display: flex; align-items: center; gap: 0.5rem;
        }
        .info-header.user { background: #f8fafc; color: var(--primary-blue); }
        .info-header.research { background: #fff1f2; color: var(--primary-maroon); }

        .info-body { padding: 1.5rem; }

        .detail-row { margin-bottom: 1rem; }
        .detail-label { font-size: 0.75rem; text-transform: uppercase; color: #64748b; font-weight: 600; letter-spacing: 0.5px; margin-bottom: 0.25rem; }
        .detail-value { font-size: 0.95rem; color: #1e293b; font-weight: 500; }

        .file-pill {
            display: flex; align-items: center; justify-content: space-between;
            padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 8px;
            background: #fff; text-decoration: none; color: #334155; margin-bottom: 0.5rem;
            transition: var(--transition);
        }
        .file-pill:hover { border-color: var(--primary-maroon); background: #fff9fa; color: var(--primary-maroon); }

        /* --- Timeline Process (Right) --- */
        .timeline-container { position: relative; padding-left: 10px; }
        
        /* Garis Vertikal Konektor */
        .step-card::before {
            content: ''; position: absolute; left: 24px; top: 60px; bottom: -30px;
            width: 2px; background: #e2e8f0; z-index: 0;
        }
        .step-card:last-child::before { display: none; }

        .step-card {
            position: relative;
            background: #fff;
            border-radius: var(--card-radius);
            border: 1px solid #e2e8f0;
            margin-bottom: 2rem;
            transition: var(--transition);
            z-index: 1;
        }

        /* State Styles */
        .step-card.locked { opacity: 0.6; grayscale: 1; pointer-events: none; background: #f1f5f9; }
        .step-card.active { border-color: var(--primary-maroon); box-shadow: 0 8px 20px rgba(124, 19, 22, 0.08); transform: scale(1.01); }
        .step-card.completed { border-color: var(--success-green); background: #f0fdf4; }

        .step-header {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #f1f5f9;
            display: flex; align-items: center; justify-content: space-between;
            background: #fff; border-radius: var(--card-radius) var(--card-radius) 0 0;
        }
        .step-card.completed .step-header { background: #dcfce7; border-bottom-color: #bbf7d0; color: #14532d; }
        
        .step-title { font-weight: 700; font-size: 1rem; display: flex; align-items: center; gap: 10px; }
        .step-badge { width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.85rem; font-weight: bold; }
        
        /* Badge Colors */
        .step-card.active .step-badge { background: var(--primary-maroon); color: white; }
        .step-card.completed .step-badge { background: var(--success-green); color: white; }
        .step-card.locked .step-badge { background: #cbd5e1; color: #64748b; }

        .step-body { padding: 1.5rem; }

        /* Buttons & Forms */
        .btn-custom-maroon { background-color: var(--primary-maroon); color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; }
        .btn-custom-maroon:hover { background-color: #5e0e10; color: white; }
        
        .upload-box { border: 2px dashed #cbd5e1; padding: 1.5rem; text-align: center; border-radius: 8px; background: #f8fafc; transition: var(--transition); }
        .upload-box:hover { border-color: var(--primary-maroon); background: #fff; }

        .animate-up { animation: fadeInUp 0.5s ease-out forwards; opacity: 0; transform: translateY(15px); }
        @keyframes fadeInUp { to { opacity: 1; transform: translateY(0); } }
        .timeline-container { 
        position: relative; 
        padding-left: 60px; /* Memberi ruang (gutter) di kiri untuk garis */
    }
    
    /* Garis Vertikal Konektor (Dipindah ke Luar Card) */
    .step-card::before {
        content: ''; 
        position: absolute; 
        left: -31px; /* Geser ke kiri luar card */
        top: 20px;   /* Mulai dari tengah badge */
        bottom: -50px; /* Sambung ke card bawahnya */
        width: 3px; 
        background: #e2e8f0; 
        z-index: 0;
    }

    /* Hilangkan garis pada card terakhir */
    .step-card:last-child::before { display: none; }

    .step-card {
        position: relative;
        background: #fff;
        border-radius: var(--card-radius);
        border: 1px solid #e2e8f0;
        margin-bottom: 2rem; /* Jarak antar card */
        transition: var(--transition);
        z-index: 1;
    }

    /* Header Card */
    .step-header {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #f1f5f9;
        display: flex; align-items: center; justify-content: space-between;
        background: #fff; 
        border-radius: var(--card-radius) var(--card-radius) 0 0;
        position: relative; /* Penting untuk positioning badge */
    }
    
    .step-title { 
        font-weight: 700; 
        font-size: 1rem; 
        display: flex; 
        align-items: center; 
        /* Gap dihapus karena badge sekarang absolute */
    }

    /* Badge Nomor (Dipindah Absolute ke Kiri Luar) */
    .step-badge { 
        position: absolute;
        left: -60px; /* Keluar dari card ke area padding container */
        top: 50%;
        transform: translateY(-50%);
        width: 40px; height: 40px; 
        border-radius: 50%; 
        display: flex; align-items: center; justify-content: center; 
        font-size: 1rem; font-weight: bold;
        z-index: 10;
        border: 4px solid #f8fafc; /* Border tebal warna background agar garis di belakangnya terlihat 'putus' rapi */
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    /* Warna Badge berdasarkan Status */
    .step-card.active .step-badge { background: var(--primary-maroon); color: white; border-color: #f8fafc; }
    .step-card.completed .step-badge { background: var(--success-green); color: white; border-color: #f8fafc; }
    .step-card.locked .step-badge { background: #cbd5e1; color: #64748b; border-color: #f8fafc; }

    /* Style Active Card (Border Merah) */
    .step-card.active { 
        border-color: var(--primary-maroon); 
        box-shadow: 0 8px 20px rgba(124, 19, 22, 0.08); 
    }
    
    /* Style Completed Card (Hijau) */
    .step-card.completed { border-color: var(--success-green); background: #f0fdf4; }
    .step-card.completed .step-header { background: #dcfce7; border-bottom-color: #bbf7d0; color: #14532d; }
    </style>

    {{-- Header Content --}}
    <div class="page-header-wrapper animate-up">
        <div>
            <h4 class="fw-bold mb-1" style="color: var(--primary-maroon);">Proses Pengajuan</h4>
            <small class="text-muted">Kelola alur persetujuan, dokumen, pembayaran, hingga presentasi.</small>
        </div>
        <div>
            <a href="{{ route('admin.pengajuan.index') }}" class="btn btn-outline-secondary btn-sm px-3 py-2 fw-bold" style="border-radius: 8px;">
                <i class="bi bi-arrow-left me-2"></i> Kembali
            </a>
        </div>
    </div>

    {{-- Alerts --}}
    @if (session('success'))
        <script>
            Swal.fire({ icon: 'success', title: 'Berhasil', text: "{{ session('success') }}", timer: 3000, showConfirmButton: false });
        </script>
    @endif

    @if (session('error'))
        <script>
            Swal.fire({ icon: 'error', title: 'Gagal', text: "{{ session('error') }}" });
        </script>
    @endif

    @php
        $praPenelitian = App\Models\PraPenelitian::where('user_id', $pengajuan->user_id)->first();
    @endphp

    <div class="row g-4">
        {{-- KOLOM KIRI: INFO DETAIL --}}
        <div class="col-lg-4 animate-up" style="animation-delay: 0.1s;">
            {{-- 1. Info User --}}
            <div class="info-card">
                <div class="info-header user"><i class="bi bi-person-circle"></i> Informasi Pemohon</div>
                <div class="info-body">
                    <div class="detail-row">
                        <div class="detail-label">Nama Lengkap</div>
                        <div class="detail-value">{{ $pengajuan->user->name }}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Email / Kontak</div>
                        <div class="detail-value">{{ $pengajuan->user->email }}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Instansi / Universitas</div>
                        <div class="detail-value">{{ $pengajuan->user->mou ? ($pengajuan->user->mou->nama_instansi ?? $pengajuan->user->mou->nama_universitas) : '-' }}</div>
                    </div>
                    <div class="detail-row mb-0">
                        <div class="detail-label">Jenis Pengajuan</div>
                        <span class="badge bg-dark text-white fw-normal px-2 py-1">{{ ucwords(str_replace('_', ' ', $pengajuan->jenis)) }}</span>
                    </div>
                </div>
            </div>

            {{-- 2. Detail Penelitian --}}
            <div class="info-card">
                <div class="info-header research"><i class="bi bi-journal-text"></i> Data Penelitian</div>
                <div class="info-body">
                    @if (!$praPenelitian)
                        <div class="text-center py-4 text-muted"><i class="bi bi-file-earmark-x fs-1 opacity-25"></i><p class="small mt-2">Formulir belum diisi.</p></div>
                    @else
                        <div class="detail-row">
                            <div class="detail-label">Judul Penelitian</div>
                            <div class="detail-value fw-bold text-dark">{{ $praPenelitian->judul }}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Jenis Penelitian</div>
                            <div class="detail-value">{{ $praPenelitian->jenis_penelitian }}</div>
                        </div>
                        <div class="mt-4">
                            <div class="detail-label mb-2">Lampiran Dokumen</div>
                            @if($praPenelitian->file_kerangka)
                                <a href="{{ Storage::url($praPenelitian->file_kerangka) }}" target="_blank" class="file-pill"><span><i class="bi bi-file-earmark-pdf text-danger me-2"></i>Kerangka.pdf</span><i class="bi bi-download text-muted"></i></a>
                            @endif
                            @if($praPenelitian->file_surat_pengantar)
                                <a href="{{ Storage::url($praPenelitian->file_surat_pengantar) }}" target="_blank" class="file-pill"><span><i class="bi bi-envelope-paper text-primary me-2"></i>Pengantar.pdf</span><i class="bi bi-download text-muted"></i></a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: WORKFLOW ADMIN --}}
        <div class="col-lg-8 animate-up" style="animation-delay: 0.2s;">
            <div class="timeline-container">
                @if($praPenelitian)

                    {{-- STEP 1: VERIFIKASI DATA --}}
                    @php
                        $step1State = 'active'; 
                        if($praPenelitian->status == 'Approved') $step1State = 'completed';
                        if($praPenelitian->status == 'Rejected') $step1State = 'active'; 
                    @endphp
                    <div class="step-card {{ $step1State }}">
                        <div class="step-header">
                            <div class="step-title"><div class="step-badge">1</div> Verifikasi Data Penelitian</div>
                            @if($praPenelitian->status == 'Approved') <span class="badge bg-success"><i class="bi bi-check-lg"></i> Disetujui</span> @endif
                        </div>
                        <div class="step-body">
                            @if($praPenelitian->status == 'Pending')
                                <p class="text-muted mb-3">Tinjau data di panel kiri. Jika valid, setujui untuk lanjut ke tahap surat.</p>
                                <div class="d-flex gap-2">
                                    {{-- FORM APPROVE --}}
                                    <form action="{{ route('pra-penelitian.approve', $praPenelitian->id) }}" method="POST" class="flex-grow-1">
                                        @csrf
                                        {{-- MODIFIKASI ONCLICK: Approve --}}
                                        <button class="btn btn-success w-100 fw-bold shadow-sm" onclick="confirmSubmit(event, 'Setujui Data?', 'Data akan diverifikasi dan lanjut ke tahap berikutnya.', 'success')">
                                            <i class="bi bi-check-circle me-1"></i> Setujui Data
                                        </button>
                                    </form>
                                    {{-- FORM REJECT --}}
                                    <form action="{{ route('pra-penelitian.reject', $praPenelitian->id) }}" method="POST" class="flex-grow-1">
                                        @csrf
                                        {{-- MODIFIKASI ONCLICK: Reject --}}
                                        <button class="btn btn-outline-danger w-100 fw-bold" onclick="confirmSubmit(event, 'Tolak Data?', 'Data akan dikembalikan ke mahasiswa untuk revisi.', 'error')">
                                            <i class="bi bi-x-circle me-1"></i> Tolak
                                        </button>
                                    </form>
                                </div>
                            @elseif($praPenelitian->status == 'Approved')
                                <div class="text-success small"><i class="bi bi-check-circle-fill me-1"></i> Data valid. Lanjut ke langkah berikutnya.</div>
                            @else
                                <div class="alert alert-danger m-0 border-0"><i class="bi bi-x-circle-fill me-1"></i> Pengajuan Ditolak.</div>
                            @endif
                        </div>
                    </div>

                    {{-- STEP 2: KIRIM SURAT & INVOICE --}}
                    @php
                        $step2State = 'locked';
                        if($praPenelitian->status == 'Approved') {
                            $step2State = ($pengajuan->status_galasan == 'sent') ? 'completed' : 'active';
                        }
                    @endphp
                    <div class="step-card {{ $step2State }}">
                        <div class="step-header">
                            <div class="step-title"><div class="step-badge">2</div> Kirim Surat & Invoice</div>
                        </div>
                        <div class="step-body">
                            @if ($pengajuan->status_galasan === 'pending')
                                <form action="{{ route('admin.pengajuan.kirim-galasan', $pengajuan->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold">Surat Balasan (PDF)</label>
                                            <input type="file" name="surat_balasan" class="form-control" accept=".pdf" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold">Invoice Tagihan (PDF)</label>
                                            <input type="file" name="invoice" class="form-control" accept=".pdf" required>
                                        </div>
                                    </div>
                                    <div class="mt-3 text-end">
                                        {{-- MODIFIKASI ONCLICK: Kirim --}}
                                        <button class="btn btn-custom-maroon shadow-sm" onclick="confirmSubmit(event, 'Kirim Dokumen?', 'Pastikan file surat dan invoice sudah benar.')">
                                            <i class="bi bi-send-fill me-2"></i> Kirim Dokumen
                                        </button>
                                    </div>
                                </form>
                            @else
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="text-success small"><i class="bi bi-check-circle-fill me-1"></i> Dokumen terkirim.</div>
                                    <div class="d-flex gap-2">
                                        @if($pengajuan->surat_balasan) <a href="{{ Storage::url($pengajuan->surat_balasan) }}" target="_blank" class="badge bg-light text-dark border text-decoration-none p-2">Lihat Surat</a> @endif
                                        @if($pengajuan->invoice) <a href="{{ Storage::url($pengajuan->invoice) }}" target="_blank" class="badge bg-light text-dark border text-decoration-none p-2">Lihat Invoice</a> @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- STEP 3: VERIFIKASI PEMBAYARAN --}}
                    @php
                        $step3State = 'locked';
                        if($pengajuan->status_galasan == 'sent') {
                            $step3State = ($pengajuan->status_pembayaran == 'verified') ? 'completed' : 'active';
                        }
                    @endphp
                    <div class="step-card {{ $step3State }}">
                        <div class="step-header">
                            <div class="step-title"><div class="step-badge">3</div> Verifikasi Pembayaran</div>
                        </div>
                        <div class="step-body">
                            <div class="bg-light p-3 rounded mb-3 border">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="small fw-bold text-muted">Bukti Pembayaran:</span>
                                    @if($pengajuan->bukti_pembayaran)
                                        <a href="{{ Storage::url($pengajuan->bukti_pembayaran) }}" target="_blank" class="btn btn-sm btn-primary"><i class="bi bi-eye me-1"></i> Lihat Bukti</a>
                                    @else
                                        <span class="badge bg-secondary">Belum Upload</span>
                                    @endif
                                </div>
                            </div>

                            @if ($pengajuan->status_pembayaran !== 'verified')
                                <p class="text-muted small">Jika bukti valid, isi data Penempatan (CI & Ruangan) untuk menyelesaikan.</p>
                                <form action="{{ route('admin.pengajuan.approve-pembayaran', $pengajuan->id) }}" method="POST">
                                    @csrf
                                    <div class="row g-3">
                                        <div class="col-md-6"><label class="form-label small fw-bold">Nama CI</label><input type="text" name="ci_nama" class="form-control" required></div>
                                        <div class="col-md-6"><label class="form-label small fw-bold">WhatsApp CI</label><input type="text" name="ci_no_hp" class="form-control" required></div>
                                        <div class="col-md-6"><label class="form-label small fw-bold">Bidang</label><input type="text" name="ci_bidang" class="form-control" required></div>
                                        <div class="col-md-6"><label class="form-label small fw-bold">Ruangan</label><input type="text" name="ruangan" class="form-control" required></div>
                                    </div>
                                    <div class="mt-3 text-end">
                                        {{-- MODIFIKASI ONCLICK: Verify Payment --}}
                                        <button type="submit" class="btn btn-success shadow-sm"
                                                onclick="confirmSubmit(event, 'Verifikasi & Selesai?', 'Pastikan data CI dan Ruangan sudah benar.')"
                                                {{ !$pengajuan->bukti_pembayaran ? 'disabled' : '' }}>
                                            <i class="bi bi-patch-check-fill me-2"></i> Verifikasi & Selesai
                                        </button>
                                    </div>
                                </form>
                            @else
                                <div class="alert alert-success m-0 border-0 p-3">
                                    <h6 class="fw-bold mb-1"><i class="bi bi-trophy-fill me-2"></i>Selesai!</h6>
                                    <small>Mahasiswa ditempatkan di <strong>{{ $pengajuan->ruangan }}</strong>.</small>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- STEP 4: JADWAL PRESENTASI --}}
                    @php
                        $step4State = 'locked';
                        if($pengajuan->status_pembayaran == 'verified') {
                            $presentasi = App\Models\Presentasi::where('pengajuan_id', $pengajuan->id)->first();
                            $step4State = $presentasi ? 'completed' : 'active';
                        }
                    @endphp
                    <div class="step-card {{ $step4State }}">
                        <div class="step-header">
                            <div class="step-title"><div class="step-badge">4</div> Jadwal Presentasi</div>
                        </div>
                        <div class="step-body">
                             @php
                                $totalKonsul = 0;
                                $presentasiCheck = null;
                                if($pengajuan->status_pembayaran == 'verified') {
                                    $totalKonsul = App\Models\Konsultasi::where('pra_penelitian_id', $praPenelitian->id)->count();
                                    $presentasiCheck = App\Models\Presentasi::where('pengajuan_id', $pengajuan->id)->first();
                                }
                            @endphp

                            @if (!$presentasiCheck)
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <span class="text-muted small">Total Konsultasi: <strong>{{ $totalKonsul }}x</strong> (Min. 2x)</span>
                                    @if ($totalKonsul >= 2) <span class="badge bg-success">Siap Presentasi</span>
                                    @else <span class="badge bg-warning text-dark">Belum Cukup</span> @endif
                                </div>
                                @if ($totalKonsul >= 2)
                                    <a href="{{ route('admin.presentasi.create', $pengajuan->id) }}" class="btn btn-custom-maroon w-100 shadow-sm"><i class="bi bi-calendar-plus me-2"></i> Buat Jadwal Presentasi</a>
                                @else
                                    <div class="alert alert-warning small m-0 border-0"><i class="bi bi-info-circle me-1"></i> Menunggu mahasiswa konsultasi.</div>
                                @endif
                            @else
                                <div class="bg-light border rounded p-3">
                                    <h6 class="fw-bold text-dark mb-2"><i class="bi bi-calendar-check me-2"></i>Jadwal Terjadwal</h6>
                                    <div class="small text-muted mb-3">{{ $presentasiCheck->tanggal_presentasi->format('d F Y') }} <br> {{ $presentasiCheck->waktu_mulai }} - {{ $presentasiCheck->waktu_selesai }} WIB <br> Ruang: {{ $presentasiCheck->tempat }}</div>
                                    <a href="{{ route('admin.presentasi.detail', $presentasiCheck->id) }}" class="btn btn-outline-primary btn-sm w-100">Lihat Detail</a>
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="alert alert-warning shadow-sm border-0 text-center py-4"><i class="bi bi-exclamation-circle display-4 text-warning mb-3"></i><h5 class="fw-bold">Data Tidak Ditemukan</h5></div>
                @endif
            </div>
        </div>
    </div>

    {{-- SCRIPT SWEETALERT LOGIC --}}
    <script>
        function confirmSubmit(event, title, text, icon = 'warning') {
            event.preventDefault(); // Mencegah form submit langsung
            let form = event.target.closest('form'); // Ambil elemen form terdekat

            if (!form) {
                console.error("Form not found!");
                return;
            }

            // Validasi Input HTML5 (Jika ada 'required', browser tidak akan menampilkan popup validasi jika preventDefault, jadi kita cek manual)
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            Swal.fire({
                title: title,
                text: text,
                icon: icon,
                showCancelButton: true,
                confirmButtonColor: '#7c1316',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Lanjutkan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }
    </script>
@endsection