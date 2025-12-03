@extends('layouts.app')

@section('title', 'Proses Pengajuan')
@section('page-title', 'Detail & Proses Pengajuan')

@section('content')
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

        /* --- Header --- */
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

        /* --- Cards --- */
        .info-card {
            background: #fff;
            border: none;
            border-radius: var(--card-radius);
            box-shadow: var(--shadow-soft);
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

        .info-header {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #f1f5f9;
            font-weight: 700;
            color: var(--text-dark);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background-color: #fff;
        }

        .info-header.bg-maroon { background-color: var(--custom-maroon); color: white; }
        .info-header.bg-blue { background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); color: white; }

        .info-body { padding: 1.5rem; }

        /* --- Labels & Values --- */
        .label-field {
            font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted);
            font-weight: 600; letter-spacing: 0.5px; margin-bottom: 0.2rem;
        }
        .value-field {
            font-size: 0.95rem; color: var(--text-dark); font-weight: 500; margin-bottom: 1rem;
        }

        /* --- Process Cards (Workflow) --- */
        .process-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: var(--card-radius);
            overflow: hidden;
            margin-bottom: 1.5rem;
            transition: var(--transition);
            opacity: 0.6; /* Default redup (disabled look) */
        }

        .process-card.active {
            opacity: 1;
            border-color: var(--custom-maroon-light);
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        }

        .process-header {
            padding: 1rem 1.5rem;
            font-weight: 700;
            font-size: 1.1rem;
            color: white;
        }

        /* Gradients untuk Tahapan */
        .process-header.step-1 { background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); } /* Indigo */
        .process-header.step-2 { background: linear-gradient(135deg, #ea580c 0%, #f97316 100%); } /* Orange */
        .process-header.step-3 { background: linear-gradient(135deg, #059669 0%, #10b981 100%); } /* Green */

        .process-body { padding: 1.5rem; }

        /* --- Forms & Buttons --- */
        .form-label { font-weight: 600; font-size: 0.9rem; }
        .form-control { border-radius: 8px; padding: 0.6rem 1rem; }

        .btn-action { padding: 0.6rem 1.5rem; border-radius: 8px; font-weight: 600; transition: var(--transition); border: none; }
        .btn-primary-custom { background-color: var(--custom-maroon); color: white; }
        .btn-primary-custom:hover { background-color: var(--custom-maroon-light); color: white; }

        .btn-success-custom { background-color: #059669; color: white; }
        .btn-success-custom:hover { background-color: #047857; color: white; }

        .btn-outline-custom { border: 1px solid #e2e8f0; color: var(--text-dark); background: white; }
        .btn-outline-custom:hover { background: #f8f9fa; border-color: #cbd5e1; }

        /* --- File Links --- */
        .file-link {
            display: inline-flex; align-items: center; gap: 8px; padding: 8px 12px;
            background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 8px;
            text-decoration: none; color: var(--text-dark); font-size: 0.9rem; font-weight: 500;
            transition: var(--transition);
        }
        .file-link:hover { background: #e2e8f0; color: var(--custom-maroon); }

        .animate-up { animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards; opacity: 0; transform: translateY(20px); }
        @keyframes fadeInUp { to { opacity: 1; transform: translateY(0); } }
    </style>

    {{-- Header --}}
    <div class="page-header-wrapper animate-up">
        <div>
            <h4 class="fw-bold mb-1" style="color: var(--custom-maroon);">Proses Pengajuan</h4>
            <small class="text-muted">Workflow: Approval Data -> Kirim Balasan -> Verifikasi Pembayaran.</small>
        </div>
        <div>
            <a href="{{ route('admin.pengajuan.index') }}" class="btn btn-outline-custom shadow-sm">
                <i class="bi bi-arrow-left me-2"></i> Kembali
            </a>
        </div>
    </div>

    {{-- Alerts --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show animate-up shadow-sm border-0" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show animate-up shadow-sm border-0" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @php
        // Ambil data pra penelitian
        $praPenelitian = App\Models\PraPenelitian::where('user_id', $pengajuan->user_id)->first();
    @endphp

    <div class="row g-4">

        {{-- KOLOM KIRI: Informasi Detail (Read Only) --}}
        <div class="col-lg-4 animate-up" style="animation-delay: 0.1s;">

            {{-- 1. Info User --}}
            <div class="info-card">
                <div class="info-header bg-maroon">
                    <i class="bi bi-person-vcard-fill"></i> Informasi Pemohon
                </div>
                <div class="info-body">
                    <div class="label-field">Nama Lengkap</div>
                    <div class="value-field fw-bold">{{ $pengajuan->user->name }}</div>

                    <div class="label-field">Email</div>
                    <div class="value-field">{{ $pengajuan->user->email }}</div>

                    <div class="label-field">Universitas</div>
                    <div class="value-field">{{ $pengajuan->user->mou ? ($pengajuan->user->mou->nama_instansi ?? $pengajuan->user->mou->nama_universitas) : '-' }}</div>

                    <div class="label-field">Jenis Pengajuan</div>
                    <div class="value-field"><span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill">{{ ucwords(str_replace('_', ' ', $pengajuan->jenis)) }}</span></div>
                </div>
            </div>

            {{-- 2. Detail Data Pra Penelitian --}}
            <div class="info-card">
                <div class="info-header bg-blue">
                    <i class="bi bi-clipboard-data-fill"></i> Detail Penelitian
                </div>
                <div class="info-body">
                    @if (!$praPenelitian)
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-file-earmark-x display-4 opacity-50"></i>
                            <p class="mt-2 small">User belum mengisi form.</p>
                        </div>
                    @else
                        <div class="label-field">Judul Penelitian</div>
                        <div class="value-field fw-bold">{{ $praPenelitian->judul }}</div>

                        <div class="label-field">Jenis</div>
                        <div class="value-field">{{ $praPenelitian->jenis_penelitian }}</div>

                        <div class="label-field">File Lampiran</div>
                        <div class="d-flex flex-column gap-2 mb-3">
                            @if($praPenelitian->file_kerangka)
                                <a href="{{ Storage::url($praPenelitian->file_kerangka) }}" target="_blank" class="file-link">
                                    <i class="bi bi-file-earmark-pdf text-danger"></i> Kerangka Penelitian
                                </a>
                            @endif
                            @if($praPenelitian->file_surat_pengantar)
                                <a href="{{ Storage::url($praPenelitian->file_surat_pengantar) }}" target="_blank" class="file-link">
                                    <i class="bi bi-envelope-paper text-primary"></i> Surat Pengantar
                                </a>
                            @endif
                        </div>

                        <div class="label-field">Dosen Pembimbing</div>
                        <div class="value-field small bg-light p-2 rounded">
                            1. {{ $praPenelitian->dosen1_nama }}<br>
                            2. {{ $praPenelitian->dosen2_nama }}
                        </div>
                    @endif
                </div>
            </div>

        </div>

        {{-- KOLOM KANAN: Workflow Admin --}}
        <div class="col-lg-8 animate-up" style="animation-delay: 0.2s;">

            {{--
               LOGIKA TAMPILAN:
               Step 1: Approval Data Pra-Penelitian
               Step 2: Kirim Galasan (Hanya jika Step 1 Approved)
               Step 3: Verifikasi Pembayaran (Hanya jika Step 2 Sent)
            --}}

            @if($praPenelitian)

                {{-- STEP 1: Approval Data Pra-Penelitian --}}
                <div class="process-card {{ $praPenelitian->status == 'Pending' ? 'active' : '' }}">
                    <div class="process-header step-1 d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-1-circle-fill me-2"></i> Tahap 1: Verifikasi Data Penelitian</span>
                        @if($praPenelitian->status == 'Approved')
                            <span class="badge bg-white text-success"><i class="bi bi-check-circle-fill me-1"></i> Disetujui</span>
                        @elseif($praPenelitian->status == 'Rejected')
                            <span class="badge bg-white text-danger"><i class="bi bi-x-circle-fill me-1"></i> Ditolak</span>
                        @endif
                    </div>

                    <div class="process-body">
                        @if($praPenelitian->status == 'Pending')
                            <p class="text-muted mb-3">Silakan periksa detail penelitian di sebelah kiri. Jika valid, setujui pengajuan ini agar bisa lanjut ke tahap pengiriman surat.</p>
                            <div class="d-flex gap-2">
                                <form action="{{ route('pra-penelitian.approve', $praPenelitian->id) }}" method="POST" class="w-100">
                                    @csrf
                                    <button class="btn btn-success w-100 py-2 fw-bold">
                                        <i class="bi bi-check-lg me-2"></i> Setujui Data
                                    </button>
                                </form>
                                <form action="{{ route('pra-penelitian.reject', $praPenelitian->id) }}" method="POST" class="w-100">
                                    @csrf
                                    <button class="btn btn-outline-danger w-100 py-2 fw-bold" onclick="return confirm('Yakin tolak data ini?')">
                                        <i class="bi bi-x-lg me-2"></i> Tolak
                                    </button>
                                </form>
                            </div>
                        @elseif($praPenelitian->status == 'Approved')
                            <div class="alert alert-success border-0 shadow-sm m-0">
                                <i class="bi bi-check-circle-fill me-2"></i> Data Pra-Penelitian telah disetujui. Lanjut ke Tahap 2.
                            </div>
                        @else
                            <div class="alert alert-danger border-0 shadow-sm m-0">
                                <i class="bi bi-x-circle-fill me-2"></i> Pengajuan ditolak. Proses berhenti.
                            </div>
                        @endif
                    </div>
                </div>

                {{-- STEP 2: Kirim Galasan (Aktif jika Step 1 Approved) --}}
                @if($praPenelitian->status == 'Approved')
                    <div class="process-card {{ $pengajuan->status_galasan == 'pending' ? 'active' : '' }}">
                        <div class="process-header step-2 d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-2-circle-fill me-2"></i> Tahap 2: Kirim Surat Balasan</span>
                            @if($pengajuan->status_galasan !== 'pending')
                                <span class="badge bg-white text-success"><i class="bi bi-check-all me-1"></i> Terkirim</span>
                            @endif
                        </div>

                        <div class="process-body">
                            @if ($pengajuan->status_galasan === 'pending')
                                <form action="{{ route('admin.pengajuan.kirim-galasan', $pengajuan->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Upload Surat Balasan (PDF)</label>
                                            <input type="file" name="surat_balasan" class="form-control" accept=".pdf" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Upload Invoice (PDF)</label>
                                            <input type="file" name="invoice" class="form-control" accept=".pdf" required>
                                        </div>
                                    </div>
                                    <div class="mt-4 text-end">
                                        <button class="btn btn-action btn-primary-custom" onclick="return confirm('Kirim galasan ke mahasiswa?')">
                                            <i class="bi bi-send-fill me-2"></i> Kirim Dokumen
                                        </button>
                                    </div>
                                </form>
                            @else
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-success fw-bold"><i class="bi bi-envelope-check me-2"></i> Surat & Invoice telah dikirim.</span>
                                    <div class="d-flex gap-2">
                                        @if($pengajuan->surat_balasan)
                                            <a href="{{ Storage::url($pengajuan->surat_balasan) }}" target="_blank" class="btn btn-sm btn-outline-secondary">Lihat Surat</a>
                                        @endif
                                        @if($pengajuan->invoice)
                                            <a href="{{ Storage::url($pengajuan->invoice) }}" target="_blank" class="btn btn-sm btn-outline-secondary">Lihat Invoice</a>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Tambahkan setelah bagian Step 2: Verifikasi Pembayaran --}}

{{-- Step 4: Set Jadwal Presentasi (Setelah pembayaran verified) --}}
@if ($pengajuan->status_pembayaran === 'verified')
    <div class="col-lg-12 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white;">
                <h5 class="mb-0">
                    <i class="bi bi-4-circle me-2"></i>Step 4: Jadwal Presentasi
                </h5>
            </div>
            <div class="card-body">
                @php
                    $praPenelitian = App\Models\PraPenelitian::where('user_id', $pengajuan->user_id)->first();
                    $totalKonsul = $praPenelitian ? App\Models\Konsultasi::where('pra_penelitian_id', $praPenelitian->id)->count() : 0;
                    $presentasi = App\Models\Presentasi::where('pengajuan_id', $pengajuan->id)->first();
                @endphp

                @if (!$presentasi)
                    @if ($totalKonsul >= 2)
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            Mahasiswa sudah melakukan konsultasi {{ $totalKonsul }}x. Siap untuk presentasi!
                        </div>
                        <a href="{{ route('admin.presentasi.create', $pengajuan->id) }}" class="btn btn-primary">
                            <i class="bi bi-calendar-plus me-1"></i> Set Jadwal Presentasi
                        </a>
                    @else
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            Mahasiswa baru konsultasi {{ $totalKonsul }}x. Minimal 2x konsultasi sebelum presentasi.
                        </div>
                    @endif
                @else
                    <div class="alert alert-info">
                        <i class="bi bi-calendar-check me-2"></i>
                        Jadwal presentasi sudah dibuat.
                    </div>
                    <div class="mb-3">
                        <strong>Tanggal:</strong> {{ $presentasi->tanggal_presentasi->format('d F Y') }}<br>
                        <strong>Waktu:</strong> {{ $presentasi->waktu_mulai }} - {{ $presentasi->waktu_selesai }}<br>
                        <strong>Tempat:</strong> {{ $presentasi->tempat }}
                    </div>
                    <a href="{{ route('admin.presentasi.detail', $presentasi->id) }}" class="btn btn-outline-primary">
                        <i class="bi bi-eye me-1"></i> Lihat Detail Presentasi
                    </a>
                @endif
            </div>
        </div>
    </div>
@endif

                {{-- STEP 3: Verifikasi Pembayaran (Aktif jika Step 2 Sent) --}}
                @if($pengajuan->status_galasan == 'sent')
                    <div class="process-card {{ $pengajuan->status_pembayaran != 'verified' ? 'active' : '' }}">
                        <div class="process-header step-3 d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-3-circle-fill me-2"></i> Tahap 3: Verifikasi Pembayaran</span>
                            @if($pengajuan->status_pembayaran == 'verified')
                                <span class="badge bg-white text-success"><i class="bi bi-check-all me-1"></i> Selesai</span>
                            @endif
                        </div>

                        <div class="process-body">
                            {{-- Bukti Bayar --}}
                            <div class="mb-4 p-3 bg-light rounded-3 border d-flex justify-content-between align-items-center">
                                <div>
                                    <label class="form-label mb-0">Bukti Pembayaran Mahasiswa:</label>
                                    @if(!$pengajuan->bukti_pembayaran)
                                        <div class="text-muted small fst-italic">Belum diupload</div>
                                    @endif
                                </div>
                                @if($pengajuan->bukti_pembayaran)
                                    <a href="{{ Storage::url($pengajuan->bukti_pembayaran) }}" target="_blank" class="btn btn-outline-success btn-sm shadow-sm">
                                        <i class="bi bi-image me-2"></i> Lihat Bukti
                                    </a>
                                @endif
                            </div>

                            @if ($pengajuan->status_pembayaran !== 'verified')
                                <p class="text-muted mb-3 small">Jika bukti pembayaran valid, isi data CI dan Ruangan untuk menyelesaikan proses.</p>
                                <form action="{{ route('admin.pengajuan.approve-pembayaran', $pengajuan->id) }}" method="POST">
                                    @csrf
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Nama Pembimbing (CI)</label>
                                            <input type="text" name="ci_nama" class="form-control" placeholder="Nama CI" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">No. HP CI</label>
                                            <input type="text" name="ci_no_hp" class="form-control" placeholder="08..." required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Bidang / Unit</label>
                                            <input type="text" name="ci_bidang" class="form-control" placeholder="Contoh: IT" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Ruangan</label>
                                            <input type="text" name="ruangan" class="form-control" placeholder="Nama Ruangan" required>
                                        </div>
                                    </div>
                                    <div class="mt-4 text-end">
                                        <button type="submit" class="btn btn-action btn-success-custom"
                                                onclick="return confirm('Verifikasi pembayaran?')"
                                                {{ !$pengajuan->bukti_pembayaran ? 'disabled' : '' }}>
                                            <i class="bi bi-patch-check-fill me-2"></i> Verifikasi & Selesai
                                        </button>
                                    </div>
                                </form>
                            @else
                                <div class="alert alert-success m-0">
                                    <strong><i class="bi bi-trophy-fill me-2"></i> Proses Selesai!</strong><br>
                                    Pembayaran terverifikasi. Mahasiswa sudah ditempatkan di ruangan <strong>{{ $pengajuan->ruangan }}</strong> dengan CI <strong>{{ $pengajuan->ci_nama }}</strong>.
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

            @else
                <div class="alert alert-warning shadow-sm">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> Data form Pra-Penelitian tidak ditemukan untuk user ini.
                </div>
            @endif

        </div>
    </div>
@endsection
