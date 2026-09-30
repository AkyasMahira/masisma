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

        body {
            background-color: var(--bg-light);
        }

        .page-header-wrapper {
            background: #fff;
            border-radius: var(--card-radius);
            padding: 1.5rem 2rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
            margin-bottom: 2rem;
            border-left: 5px solid var(--primary-maroon);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .info-card {
            background: #fff;
            border-radius: var(--card-radius);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
            border: 1px solid #e2e8f0;
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

        .info-header {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #f1f5f9;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .info-header.user {
            background: #f8fafc;
            color: var(--primary-blue);
        }

        .info-header.research {
            background: #fff1f2;
            color: var(--primary-maroon);
        }

        .info-body {
            padding: 1.5rem;
        }

        .detail-row {
            margin-bottom: 1rem;
        }

        .detail-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            color: #64748b;
            font-weight: 600;
            letter-spacing: 0.5px;
            margin-bottom: 0.25rem;
        }

        .detail-value {
            font-size: 0.95rem;
            color: #1e293b;
            font-weight: 500;
        }

        .file-pill {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.75rem;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background: #fff;
            text-decoration: none;
            color: #334155;
            margin-bottom: 0.5rem;
            transition: var(--transition);
        }

        .file-pill:hover {
            border-color: var(--primary-maroon);
            background: #fff9fa;
            color: var(--primary-maroon);
        }

        .timeline-container {
            position: relative;
            padding-left: 60px;
        }

        .step-card::before {
            content: '';
            position: absolute;
            left: -31px;
            top: 20px;
            bottom: -50px;
            width: 3px;
            background: #e2e8f0;
            z-index: 0;
        }

        .step-card:last-child::before {
            display: none;
        }

        .step-card {
            position: relative;
            background: #fff;
            border-radius: var(--card-radius);
            border: 1px solid #e2e8f0;
            margin-bottom: 2rem;
            transition: var(--transition);
            z-index: 1;
        }

        .step-header {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #fff;
            border-radius: var(--card-radius) var(--card-radius) 0 0;
            position: relative;
        }

        .step-title {
            font-weight: 700;
            font-size: 1rem;
            display: flex;
            align-items: center;
        }

        .step-badge {
            position: absolute;
            left: -60px;
            top: 50%;
            transform: translateY(-50%);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            font-weight: bold;
            z-index: 10;
            border: 4px solid #f8fafc;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .step-card.active .step-badge {
            background: var(--primary-maroon);
            color: white;
            border-color: #f8fafc;
        }

        .step-card.completed .step-badge {
            background: var(--success-green);
            color: white;
            border-color: #f8fafc;
        }

        .step-card.locked .step-badge {
            background: #cbd5e1;
            color: #64748b;
            border-color: #f8fafc;
        }

        .step-card.active {
            border-color: var(--primary-maroon);
            box-shadow: 0 8px 20px rgba(124, 19, 22, 0.08);
        }

        .step-card.completed {
            border-color: var(--success-green);
            background: #f0fdf4;
        }

        .step-card.completed .step-header {
            background: #dcfce7;
            border-bottom-color: #bbf7d0;
            color: #14532d;
        }

        .step-card.locked {
            opacity: 0.6;
            grayscale: 1;
            pointer-events: none;
            background: #f1f5f9;
        }

        .step-body {
            padding: 1.5rem;
        }

        .btn-custom-maroon {
            background-color: var(--primary-maroon);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
        }

        .btn-custom-maroon:hover {
            background-color: #5e0e10;
            color: white;
        }

        .animate-up {
            animation: fadeInUp 0.5s ease-out forwards;
            opacity: 0;
            transform: translateY(15px);
        }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>

    {{-- LOGIKA UTAMA: Pastikan tidak ada spasi --}}
    @php
        $jenisRaw = trim($pengajuan->jenis); // Bersihkan spasi
        $isMagang = $jenisRaw === 'magang';
    @endphp

    <div class="page-header-wrapper animate-up">
        <div>
            <h4 class="fw-bold mb-1" style="color: var(--primary-maroon);">Proses Pengajuan</h4>
            <div class="d-flex align-items-center gap-2">
                <small class="text-muted">Kelola alur persetujuan, dokumen, dan pembayaran.</small>
 
                <span class="badge bg-secondary text-white" style="font-size: 0.6rem;">
                    DETECT: {{ strtoupper($jenisRaw) }}
                </span>
            </div>
        </div>
        <div>
            <a href="{{ route('admin.pengajuan.index') }}" class="btn btn-outline-secondary btn-sm px-3 py-2 fw-bold"
                style="border-radius: 8px;">
                <i class="bi bi-arrow-left me-2"></i> Kembali
            </a>
        </div>
    </div>

    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: "{{ session('success') }}",
                timer: 3000,
                showConfirmButton: false
            });
        </script>
    @endif
    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: "{{ session('error') }}"
            });
        </script>
    @endif

    <div class="row g-4">
        {{-- KOLOM KIRI --}}
        <div class="col-lg-4 animate-up" style="animation-delay: 0.1s;">
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
                        <div class="detail-value">
                            {{ $pengajuan->user->mou ? $pengajuan->user->mou->nama_instansi ?? $pengajuan->user->mou->nama_universitas : '-' }}
                        </div>
                    </div>
                    <div class="detail-row mb-0">
                        <div class="detail-label">Jenis Pengajuan</div>
                        <span
                            class="badge bg-dark text-white fw-normal px-2 py-1">{{ ucwords(str_replace('_', ' ', $jenisRaw)) }}</span>
                    </div>
                </div>
            </div>

            {{-- HANYA TAMPIL JIKA BUKAN MAGANG --}}
            @if (!$isMagang)
                <div class="info-card">
                    <div class="info-header research"><i class="bi bi-journal-text"></i> Data Penelitian</div>
                    <div class="info-body">
                        @if (!$praPenelitian)
                            <div class="text-center py-4 text-muted"><i class="bi bi-file-earmark-x fs-1 opacity-25"></i>
                                <p class="small mt-2">Formulir belum diisi.</p>
                            </div>
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
                                @if ($praPenelitian->file_kerangka)
                                   <a href="{{ asset($praPenelitian->file_kerangka) }}" target="_blank" class="file-pill"><span><i
                                                class="bi bi-file-earmark-pdf text-danger me-2"></i>Kerangka.pdf</span><i
                                            class="bi bi-download text-muted"></i></a>
                                @endif
                                @if ($praPenelitian->file_surat_pengantar)
                                    <a href="{{ asset($praPenelitian->file_surat_pengantar) }}" target="_blank"
                                        class="file-pill"><span><i
                                                class="bi bi-envelope-paper text-primary me-2"></i>Pengantar.pdf</span><i
                                            class="bi bi-download text-muted"></i></a>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        {{-- KOLOM KANAN --}}
        <div class="col-lg-8 animate-up" style="animation-delay: 0.2s;">
            <div class="timeline-container">

                {{-- STEP 1: VERIFIKASI --}}
                @php
                    $step1State = 'active';
                    $isStep1Done = false;

                    if ($isMagang) {
                        // Logika Magang
                        if ($pengajuan->status == 'approved') {
                            $step1State = 'completed';
                            $isStep1Done = true;
                        } elseif ($pengajuan->status == 'rejected') {
                            $step1State = 'rejected';
                        }
                    } else {
                        // Logika Penelitian
                        if ($praPenelitian && $praPenelitian->status == 'Approved') {
                            $step1State = 'completed';
                            $isStep1Done = true;
                        } elseif ($praPenelitian && $praPenelitian->status == 'Rejected') {
                            $step1State = 'active';
                        }
                    }
                @endphp

                <div class="step-card {{ $step1State }}">
                    <div class="step-header">
                        <div class="step-title">
                            <div class="step-badge">1</div>
                            {{ $isMagang ? 'Verifikasi Permohonan Magang' : 'Verifikasi Data Penelitian' }}
                        </div>
                        @if ($isStep1Done)
                            <span class="badge bg-success"><i class="bi bi-check-lg"></i> Disetujui</span>
                        @endif
                    </div>
                    <div class="step-body">
                        @if ($isMagang)
                            {{-- TAMPILAN KHUSUS MAGANG --}}
                            @if ($pengajuan->status == 'pending')
                                <p class="text-muted mb-3">Cek kuota dan informasi pemohon magang.</p>
                                <div class="d-flex gap-2">
                                    <form action="{{ route('admin.pengajuan.approve', $pengajuan->id) }}" method="POST"
                                        class="flex-grow-1">
                                        @csrf
                                        <button class="btn btn-success w-100 fw-bold shadow-sm"
                                            onclick="confirmSubmit(event, 'Terima Magang?', 'Lanjut kirim surat balasan.', 'success')"><i
                                                class="bi bi-check-circle me-1"></i> Terima</button>
                                    </form>
                                    <form action="{{ route('admin.pengajuan.reject', $pengajuan->id) }}" method="POST"
                                        class="flex-grow-1">
                                        @csrf
                                        <button class="btn btn-outline-danger w-100 fw-bold"
                                            onclick="confirmSubmit(event, 'Tolak Magang?', 'Pengajuan akan ditolak.', 'error')"><i
                                                class="bi bi-x-circle me-1"></i> Tolak</button>
                                    </form>
                                </div>
                            @elseif($pengajuan->status == 'approved')
                                <div class="text-success small"><i class="bi bi-check-circle-fill me-1"></i> Permohonan
                                    magang diterima.</div>
                            @else
                                <div class="alert alert-danger m-0 border-0">Pengajuan Magang Ditolak.</div>
                            @endif
                        @else
                            {{-- TAMPILAN KHUSUS PENELITIAN --}}
                            @if (!$praPenelitian)
                                <div class="alert alert-warning">Mahasiswa belum mengisi form Pra-Penelitian.</div>
                            @elseif($praPenelitian->status == 'Pending')
                                <p class="text-muted mb-3">Tinjau data penelitian. Jika valid, setujui.</p>
                                <div class="d-flex gap-2">
                                    <form action="{{ route('pra-penelitian.approve', $praPenelitian->id) }}" method="POST"
                                        class="flex-grow-1">
                                        @csrf
                                        <button class="btn btn-success w-100 fw-bold shadow-sm"
                                            onclick="confirmSubmit(event, 'Setujui Data?', 'Lanjut ke tahap surat.', 'success')"><i
                                                class="bi bi-check-circle me-1"></i> Setujui</button>
                                    </form>
                                    <form action="{{ route('pra-penelitian.reject', $praPenelitian->id) }}" method="POST"
                                        class="flex-grow-1">
                                        @csrf
                                        <button class="btn btn-outline-danger w-100 fw-bold"
                                            onclick="confirmSubmit(event, 'Tolak Data?', 'Data dikembalikan.', 'error')"><i
                                                class="bi bi-x-circle me-1"></i> Tolak</button>
                                    </form>
                                </div>
                            @elseif($praPenelitian->status == 'Approved')
                                <div class="text-success small"><i class="bi bi-check-circle-fill me-1"></i> Data penelitian
                                    valid.</div>
                            @else
                                <div class="alert alert-danger m-0 border-0">Ditolak.</div>
                            @endif
                        @endif
                    </div>
                </div>

                {{-- STEP 2: SURAT & INVOICE --}}
                @php
                    $step2State = 'locked';
                    // Hanya aktifkan Step 2 jika Step 1 sudah SELESAI
                    if ($isStep1Done) {
                        $step2State = $pengajuan->status_galasan == 'sent' ? 'completed' : 'active';
                    }
                @endphp
                <div class="step-card {{ $step2State }}">
                    <div class="step-header">
                        <div class="step-title">
                            <div class="step-badge">2</div> Kirim Surat & Invoice
                        </div>
                        {{-- Indikator Visual Terkunci --}}
                        @if ($step2State == 'locked')
                            <span class="badge bg-secondary ms-2" style="font-size: 0.7em; opacity: 0.8;">Menunggu
                                Approval</span>
                        @endif
                    </div>
                    <div class="step-body">
                        {{-- LOGIKA PENTING: Jika Locked, JANGAN render Form sama sekali --}}
                        @if ($step2State == 'locked')
                            <div class="text-center py-3 text-muted">
                                <i class="bi bi-lock-fill display-4 mb-2 opacity-25"></i>
                                <p class="small m-0">Selesaikan verifikasi (Tahap 1) untuk membuka fitur ini.</p>
                            </div>
                        @else
                            {{-- Jika Active / Completed, tampilkan konten --}}
                            @if ($pengajuan->status_galasan === 'pending')
                                <form action="{{ route('admin.pengajuan.kirim-galasan', $pengajuan->id) }}"
                                    method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold">Surat Balasan (PDF)</label>
                                            <input type="file" name="surat_balasan" class="form-control"
                                                accept=".pdf" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold">Invoice Tagihan (PDF)</label>
                                            <input type="file" name="invoice" class="form-control" accept=".pdf"
                                                required>
                                        </div>
                                    </div>
                                    <div class="mt-3 text-end">
                                        <button class="btn btn-custom-maroon shadow-sm"
                                            onclick="confirmSubmit(event, 'Kirim Dokumen?', 'Pastikan file benar.')"><i
                                                class="bi bi-send-fill me-2"></i> Kirim Dokumen</button>
                                    </div>
                                </form>
                            @else
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="text-success small"><i class="bi bi-check-circle-fill me-1"></i> Dokumen
                                        terkirim.</div>
                                    <div class="d-flex gap-2">
                                        @if ($pengajuan->surat_balasan)
                                            <a href="{{ asset('storage/' . $pengajuan->surat_balasan) }}" target="_blank"
                                                class="badge bg-light text-dark border text-decoration-none p-2">Lihat
                                                Surat</a>
                                        @endif
                                        @if ($pengajuan->invoice)
                                            <a href="{{ asset('storage/' . $pengajuan->invoice) }}" target="_blank"
                                                class="badge bg-light text-dark border text-decoration-none p-2">Lihat
                                                Invoice</a>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>

                {{-- STEP 3: PEMBAYARAN --}}
                @php
                    $step3State = 'locked';
                    // Hanya aktifkan Step 3 jika galasan sudah dikirim
                    if ($pengajuan->status_galasan == 'sent') {
                        $step3State = $pengajuan->status_pembayaran == 'verified' ? 'completed' : 'active';
                    }
                @endphp
                <div class="step-card {{ $step3State }}">
                    <div class="step-header">
                        <div class="step-title">
                            <div class="step-badge">3</div> Verifikasi Pembayaran & CI
                        </div>
                    </div>

<div class="step-body">
    @if ($step3State == 'locked')
        <div class="text-center py-3 text-muted">
            <i class="bi bi-lock-fill display-4 mb-2 opacity-25"></i>
            <p class="small m-0">Kirim surat balasan (Tahap 2) terlebih dahulu.</p>
        </div>
    @else
        <div class="bg-light p-3 rounded mb-3 border">
            <div class="d-flex justify-content-between align-items-center">
                <span class="small fw-bold text-muted">Bukti Pembayaran:</span>
                @if ($pengajuan->bukti_pembayaran)
                    <a href="{{ asset('storage/' . $pengajuan->bukti_pembayaran) }}" target="_blank"
                        class="btn btn-sm btn-primary"><i class="bi bi-eye me-1"></i> Lihat Bukti</a>
                @else
                    <span class="badge bg-secondary">Belum Upload</span>
                @endif
            </div>
        </div>

        @if ($pengajuan->status_pembayaran !== 'verified')
            {{-- FORM VERIFIKASI AWAL (Sama seperti sebelumnya) --}}
            <form action="{{ route('admin.pengajuan.approve-pembayaran', $pengajuan->id) }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Corporate Instructor 1 (Utama)</label>
                        <select name="ci_id" class="form-select" required>
                            <option value="" disabled selected>-- Pilih CI 1 --</option>
                            @foreach ($cis as $ci)
                                <option value="{{ $ci->id }}">{{ $ci->nama }} ({{ $ci->bidang }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Corporate Instructor 2 (Opsional)</label>
                        <select name="ci_id_2" class="form-select">
                            <option value="" selected>-- Tanpa CI Kedua --</option>
                            @foreach ($cis as $ci)
                                <option value="{{ $ci->id }}">{{ $ci->nama }} ({{ $ci->bidang }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label small fw-bold">Pilih Ruangan</label>
                        <select name="ruangan_id" class="form-select" required>
                            <option value="" disabled selected>-- Pilih Ruangan --</option>
                            @foreach ($ruangans as $ruangan)
                                <option value="{{ $ruangan->id }}">{{ $ruangan->nm_ruangan }} (Sisa: {{ $ruangan->kuota_ruangan }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="mt-3 text-end">
                    <button type="submit" class="btn btn-success shadow-sm" onclick="confirmSubmit(event, 'Verifikasi & Selesai?', 'Pastikan data sudah benar.')">
                        <i class="bi bi-patch-check-fill me-2"></i> Verifikasi & Selesai
                    </button>
                </div>
            </form>
        @else
            {{-- TAMPILAN JIKA SUDAH VERIFIED --}}
            <div class="alert alert-success m-0 border-0 p-3 mb-3">
                <h6 class="fw-bold mb-1"><i class="bi bi-trophy-fill me-2"></i>Selesai!</h6>
                <small>
                    Mahasiswa ditempatkan di <strong>{{ $pengajuan->dataRuangan->nm_ruangan ?? '-' }}</strong>.<br>
                    Pembimbing terdaftar:<br>
                    1. <strong>{{ $pengajuan->ci->nama ?? '-' }}</strong>
                    @if($pengajuan->ci2)
                        <br>2. <strong>{{ $pengajuan->ci2->nama }}</strong>
                    @endif
                </small>
            </div>

            {{-- FORM TAMBAH CI KE-2 (Jika CI 2 masih kosong) --}}
            @if(!$pengajuan->ci_id_2)
                <div class="mt-3 p-3 border rounded bg-white shadow-sm" style="border-left: 4px solid #f59e0b !important;">
                    <label class="form-label small fw-bold text-dark"><i class="bi bi-plus-circle-fill me-1 text-warning"></i> Tambah Pembimbing Ke-2</label>
                    <p class="text-muted" style="font-size: 0.75rem;">Saat ini hanya ada 1 pembimbing. Admin bisa menambahkan pembimbing pendamping di sini jika diperlukan.</p>
                    
                    <form action="{{ route('admin.pengajuan.update-ci2', $pengajuan->id) }}" method="POST" class="d-flex gap-2">
                        @csrf
                        <select name="ci_id_2" class="form-select form-select-sm" required>
                            <option value="" disabled selected>-- Pilih CI Tambahan --</option>
                            @foreach ($cis as $ci)
                                @if($ci->id != $pengajuan->ci_id) {{-- Hindari pilih orang yang sama --}}
                                    <option value="{{ $ci->id }}">{{ $ci->nama }} ({{ $ci->bidang }})</option>
                                @endif
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-sm btn-primary px-3">Simpan</button>
                    </form>
                </div>
            @endif
        @endif
    @endif
</div>
                </div>

            {{-- STEP 4: PRESENTASI / PENYELESAIAN AKHIR --}}
                @if (!$isMagang && $praPenelitian)
                    @php
                        $step4State = 'locked';
                        if ($pengajuan->status_pembayaran == 'verified') {
                            $presentasiCheck = App\Models\Presentasi::where('pengajuan_id', $pengajuan->id)->first();
                            $step4State = $presentasiCheck ? 'completed' : 'active';
                        }
                        
                        // Cek apakah ini tipe yang BYPASS presentasi
                        $isBypassPresentasi = in_array($praPenelitian->jenis_penelitian, ['Uji Validitas', 'Data Awal']);
                        
                        // Hitung jumlah konsultasi
                        $totalKonsul = App\Models\Konsultasi::where('pra_penelitian_id', $praPenelitian->id)->count();
                    @endphp
                    <div class="step-card {{ $step4State }}">
                        <div class="step-header">
                            <div class="step-title">
                                <div class="step-badge">4</div> 
                                {{ $isBypassPresentasi ? 'Penyelesaian & Surat Akhir' : 'Jadwal Presentasi & Laporan' }}
                            </div>
                        </div>
                        <div class="step-body">
                            @if ($step4State == 'locked')
                                <div class="text-center py-3 text-muted">
                                    <i class="bi bi-lock-fill display-4 mb-2 opacity-25"></i>
                                    <p class="small m-0">Selesaikan pembayaran & penempatan CI (Tahap 3) terlebih dahulu.</p>
                                </div>
                            @else
                                {{-- JIKA JENISNYA UJI VALIDITAS / DATA AWAL (Bypass Presentasi) --}}
                               {{-- JIKA JENISNYA UJI VALIDITAS / DATA AWAL (Bypass Presentasi) --}}
                                @if($isBypassPresentasi)
                                    {{-- Jika BELUM di-generate (belum ada data presentasi) --}}
                                    @if(!$presentasiCheck)
                                        <div class="alert alert-info border-0 d-flex align-items-center mb-3">
                                            <i class="bi bi-info-circle-fill fs-4 me-3"></i>
                                            <div>
                                                <strong>Penelitian: {{ $praPenelitian->jenis_penelitian }}</strong><br>
                                                <small>Mahasiswa ini tidak perlu presentasi. Admin dapat mengakhiri proses dan mengirim Surat Selesai jika bimbingan sudah cukup.</small>
                                            </div>
                                        </div>
                                        
                                        <div class="d-flex align-items-center justify-content-between mb-3 p-3 bg-light rounded border">
                                            <span class="text-muted small">Total Konsultasi: <strong>{{ $totalKonsul }}x</strong> (Min. 2x)</span>
                                            @if ($totalKonsul >= 2)
                                                <span class="badge bg-success">Siap Diakhiri</span>
                                            @else
                                                <span class="badge bg-warning text-dark">Belum Cukup</span>
                                            @endif
                                        </div>

                                        @if ($totalKonsul >= 2)
                                            <form action="{{ route('admin.presentasi.store', $pengajuan->id) }}" method="POST" enctype="multipart/form-data">
                                                @csrf
                                                <input type="hidden" name="tanggal_presentasi" value="{{ now()->format('Y-m-d') }}">
                                                <input type="hidden" name="waktu_mulai" value="08:00">
                                                <input type="hidden" name="waktu_selesai" value="09:00">
                                                <input type="hidden" name="tempat" value="Tanpa Presentasi ({{ $praPenelitian->jenis_penelitian }})">
                                                <input type="hidden" name="keterangan_admin" value="Diselesaikan otomatis karena tipe {{ $praPenelitian->jenis_penelitian }}">
                       @if($praPenelitian->jenis_penelitian === 'Data Awal')
    <div class="mb-3">
        <a href="{{ route('admin.pengajuan.download-surat-otomatis', $pengajuan->id) }}" class="btn btn-primary btn-sm w-100 mb-2">
            <i class="bi bi-file-earmark-pdf-fill me-2"></i> Surat Selesai Data Awal
        </a>
        <p class="text-muted" style="font-size: 0.7rem;">*Surat akan terisi otomatis dengan data mahasiswa & judul penelitian.</p>
    </div>
@endif
                                                <div class="mb-3 text-start bg-white p-3 border rounded shadow-sm">
                                                    <label class="form-label small fw-bold text-dark"><i class="bi bi-file-pdf text-danger me-1"></i> Upload Surat Selesai (PDF) <span class="text-danger">*</span></label>
                                                    <input type="file" name="surat_selesai_manual" class="form-control form-control-sm" accept=".pdf" required>
                                                </div>

                                                <button type="submit" class="btn btn-success w-100 fw-bold shadow-sm" onclick="confirmSubmit(event, 'Akhiri & Kirim Surat?', 'Pengajuan akan diselesaikan dan surat akan dikirim ke mahasiswa.', 'success')">
                                                    <i class="bi bi-send-fill me-2"></i> Akhiri & Kirim Surat Selesai
                                                </button>
                                            </form>
                                        @else
                                            <button class="btn btn-secondary w-100 fw-bold" disabled>
                                                <i class="bi bi-lock me-2"></i> Menunggu Konsultasi Terpenuhi
                                            </button>
                                        @endif
                                    
                                    {{-- Jika SUDAH di-generate --}}
                                    @else
                                        <div class="alert alert-success border-0 d-flex align-items-center mb-3">
                                            <i class="bi bi-check-circle-fill fs-4 me-3"></i>
                                            <div>
                                                <strong>Proses Selesai!</strong><br>
                                                <small>Surat Selesai sudah berhasil dikirim ke mahasiswa.</small>
                                            </div>
                                        </div>
                                        @if($presentasiCheck->surat_selesai)
                                            <a href="{{ asset('storage/' . $presentasiCheck->surat_selesai) }}" target="_blank" class="btn btn-outline-success w-100 fw-bold">
                                                <i class="bi bi-download me-2"></i> Lihat Surat yang Dikirim
                                            </a>
                                        @endif
                                    @endif
                             
                                
                                {{-- JIKA JENISNYA PENELITIAN BIASA (Alur Normal) --}}
                                @else
                                    @if (!$presentasiCheck)
                                        <div class="d-flex align-items-center justify-content-between mb-3 p-3 bg-light rounded border">
                                            <span class="text-muted small">Total Konsultasi: <strong>{{ $totalKonsul }}x</strong> (Min. 2x)</span>
                                            @if ($totalKonsul >= 2)
                                                <span class="badge bg-success">Siap Presentasi</span>
                                            @else
                                                <span class="badge bg-warning text-dark">Belum Cukup</span>
                                            @endif
                                        </div>
                                        @if ($totalKonsul >= 2)
                                            <a href="{{ route('admin.presentasi.create', $pengajuan->id) }}" class="btn btn-custom-maroon w-100 shadow-sm">
                                                <i class="bi bi-calendar-plus me-2"></i> Buat Jadwal Presentasi
                                            </a>
                                        @else
                                            <div class="alert alert-warning small m-0 border-0"><i class="bi bi-info-circle me-1"></i> Menunggu mahasiswa menyelesaikan minimal 2x konsultasi.</div>
                                        @endif
                                    @else
                                        <div class="bg-light border rounded p-3">
                                            <h6 class="fw-bold text-dark mb-2"><i class="bi bi-calendar-check me-2"></i>Status Presentasi</h6>
                                            <div class="small text-muted mb-3">
                                                Tanggal: {{ $presentasiCheck->tanggal_presentasi->format('d F Y') }} <br>
                                                Pukul: {{ $presentasiCheck->waktu_mulai }} - {{ $presentasiCheck->waktu_selesai }} WIB <br>
                                                Ruang: {{ $presentasiCheck->tempat }}
                                            </div>
                                            <a href="{{ route('admin.presentasi.detail', $presentasiCheck->id) }}" class="btn btn-outline-primary btn-sm w-100">Kelola Nilai & Laporan</a>
                                        </div>
                                    @endif
                                @endif
                            @endif
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>

    <script>
        function confirmSubmit(event, title, text, icon = 'warning') {
            event.preventDefault();
            let form = event.target.closest('form');
            if (!form) return;
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
                if (result.isConfirmed) form.submit();
            });
        }
    </script>
@endsection
