@extends('layouts.app')

@section('title', 'Presentasi Penelitian')
@section('page-title', 'Presentasi Penelitian')

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
            --transition: all 0.3s ease;
        }

        body { background-color: var(--bg-light); }

        /* --- Global Cards --- */
        .custom-card {
            background: #fff;
            border: none;
            border-radius: var(--card-radius);
            box-shadow: var(--shadow-sm);
            margin-bottom: 1.5rem;
            overflow: hidden;
            transition: var(--transition);
        }
        .custom-card:hover { box-shadow: var(--shadow-md); transform: translateY(-2px); }

        .card-header-custom {
            padding: 1rem 1.5rem;
            font-weight: 700;
            color: white;
            display: flex; align-items: center; gap: 10px;
        }

        .bg-gradient-maroon { background: linear-gradient(135deg, #7c1316 0%, #a3191d 100%); }
        .bg-gradient-blue { background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); } /* Untuk PPT */
        .bg-gradient-purple { background: linear-gradient(135deg, #6b21a8 0%, #a855f7 100%); } /* Untuk Nilai */
        .bg-gradient-green { background: linear-gradient(135deg, #065f46 0%, #10b981 100%); } /* Untuk Laporan */

        .card-body-custom { padding: 1.5rem; }

        /* --- Info List (Left Side) --- */
        .info-item { margin-bottom: 1.25rem; }
        .info-label { font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); font-weight: 700; margin-bottom: 0.25rem; letter-spacing: 0.5px; }
        .info-value { font-size: 1rem; color: var(--text-dark); font-weight: 500; }
        .info-value i { width: 20px; text-align: center; margin-right: 5px; color: var(--primary-maroon); }

        /* --- Upload Zone --- */
        .upload-zone {
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            padding: 2rem 1.5rem;
            text-align: center;
            background-color: #f8fafc;
            transition: var(--transition);
            cursor: pointer;
            position: relative;
        }
        .upload-zone:hover { border-color: var(--primary-maroon); background-color: #fff; }
        .upload-icon { font-size: 2.5rem; color: #94a3b8; margin-bottom: 1rem; display: block; }
        
        /* --- Buttons --- */
        .btn-maroon { background-color: var(--primary-maroon); color: white; border: none; padding: 0.6rem 1.5rem; border-radius: 8px; font-weight: 600; transition: var(--transition); }
        .btn-maroon:hover { background-color: var(--primary-maroon-hover); color: white; }
        
        .btn-outline-back { border: 1px solid #e2e8f0; background: white; color: var(--text-dark); border-radius: 8px; padding: 0.5rem 1rem; font-weight: 500; text-decoration: none; transition: var(--transition); display: inline-flex; align-items: center; }
        .btn-outline-back:hover { background: #f1f5f9; border-color: #cbd5e1; color: var(--primary-maroon); }

        /* --- Grade Display --- */
        .grade-box {
            text-align: center; padding: 1.5rem; border-radius: 12px;
            background: #fff; border: 1px solid #e2e8f0;
        }
        .grade-value { font-size: 3rem; font-weight: 800; line-height: 1; }
        
        .text-success-custom { color: #059669; }
        .text-warning-custom { color: #d97706; }
        .text-danger-custom { color: #dc2626; }

        /* Animation */
        .animate-up { animation: fadeInUp 0.5s ease-out forwards; opacity: 0; transform: translateY(15px); }
        @keyframes fadeInUp { to { opacity: 1; transform: translateY(0); } }
    </style>

    <div class="container py-4">

        {{-- Alerts --}}
        @if (session('success'))
            <div class="alert alert-success border-0 shadow-sm animate-up d-flex align-items-center mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2 fs-5"></i> {{ session('success') }}
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger border-0 shadow-sm animate-up d-flex align-items-center mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i> {{ session('error') }}
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="d-flex justify-content-between align-items-center mb-4 animate-up">
            <h4 class="fw-bold mb-0 text-dark">Detail Presentasi</h4>
            <a href="{{ route('pengajuan.detail', 'pra_penelitian') }}" class="btn-outline-back">
                <i class="bi bi-arrow-left me-2"></i> Kembali
            </a>
        </div>

        <div class="row g-4">
            
            {{-- KOLOM KIRI: JADWAL & CI --}}
            <div class="col-lg-4 animate-up" style="animation-delay: 0.1s;">
                <div class="custom-card h-100">
                    <div class="card-header-custom bg-gradient-maroon">
                        <i class="bi bi-calendar-check"></i> Jadwal & Lokasi
                    </div>
                    <div class="card-body-custom">
                        <div class="info-item">
                            <div class="info-label">Tanggal</div>
                            <div class="info-value"><i class="bi bi-calendar3"></i> {{ $presentasi->tanggal_presentasi->format('d F Y') }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Waktu</div>
                            <div class="info-value"><i class="bi bi-clock"></i> {{ $presentasi->waktu_mulai }} - {{ $presentasi->waktu_selesai }} WIB</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Tempat / Ruangan</div>
                            <div class="info-value"><i class="bi bi-geo-alt"></i> {{ $presentasi->tempat }}</div>
                        </div>

                        @if ($presentasi->keterangan_admin)
                            <div class="alert alert-light border mb-4">
                                <div class="info-label text-muted mb-1"><i class="bi bi-info-circle me-1"></i> Catatan Admin</div>
                                <p class="small mb-0 text-dark">{{ $presentasi->keterangan_admin }}</p>
                            </div>
                        @endif

                        <hr class="border-light my-4">

                        <div class="info-item mb-0">
                            <div class="info-label">Pembimbing Lapangan (CI)</div>
                            <div class="d-flex align-items-center mt-2">
                                <div class="bg-light rounded-circle p-2 me-3 text-maroon">
                                    <i class="bi bi-person-badge fs-4"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark">{{ $pengajuan->ci_nama }}</div>
                                    <a href="tel:{{ $pengajuan->ci_no_hp }}" class="text-decoration-none small text-muted">
                                        <i class="bi bi-whatsapp text-success me-1"></i> {{ $pengajuan->ci_no_hp }}
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        {{-- LINK PENILAIAN DIHAPUS DARI SINI SESUAI PERMINTAAN --}}

                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN: FLOW MAHASISWA --}}
            <div class="col-lg-8 animate-up" style="animation-delay: 0.2s;">
                
                {{-- Step 1: Upload PPT --}}
                <div class="custom-card">
                    <div class="card-header-custom bg-gradient-blue">
                        <div class="d-flex w-100 justify-content-between align-items-center">
                            <span><i class="bi bi-file-earmark-slides me-2"></i>1. File Presentasi (PPT)</span>
                            @if($presentasi->file_ppt)<span class="badge bg-white text-primary rounded-pill"><i class="bi bi-check-circle-fill me-1"></i>Uploaded</span>@endif
                        </div>
                    </div>
                    <div class="card-body-custom">
                        @if (!$presentasi->file_ppt)
                            <form action="{{ route('presentasi.upload-ppt', $presentasi->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="upload-zone" onclick="document.getElementById('filePpt').click()">
                                    <i class="bi bi-cloud-arrow-up upload-icon"></i>
                                    <h6 class="fw-bold">Klik untuk upload file PPT Anda</h6>
                                    <p class="text-muted small mb-0">Format: PPT, PPTX, PDF (Max 10MB)</p>
                                    <input type="file" id="filePpt" name="file_ppt" class="d-none" accept=".ppt,.pptx,.pdf" required onchange="this.form.submit()">
                                </div>
                                <div class="text-center mt-3">
                                    <small class="text-danger fst-italic">*File hanya bisa diupload sekali, pastikan sudah final.</small>
                                </div>
                            </form>
                        @else
                            <div class="d-flex justify-content-between align-items-center p-3 border rounded bg-light">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-file-earmark-ppt-fill text-danger fs-1 me-3"></i>
                                    <div>
                                        <div class="fw-bold text-dark">File Presentasi</div>
                                        <small class="text-muted">Diupload: {{ $presentasi->uploaded_at->format('d M Y H:i') }}</small>
                                    </div>
                                </div>
                                <a href="{{ Storage::url($presentasi->file_ppt) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-download me-1"></i> Download
                                </a>
                            </div>

                            @if ($presentasi->nilai == 'C')
                                <div class="alert alert-warning mt-3 border-0 d-flex align-items-start">
                                    <i class="bi bi-exclamation-triangle-fill fs-5 me-2 mt-1"></i>
                                    <div>
                                        <strong>Revisi Diperlukan!</strong><br>
                                        Silakan upload ulang file presentasi yang sudah diperbaiki di bawah ini.
                                    </div>
                                </div>
                                <form action="{{ route('presentasi.upload-ppt', $presentasi->id) }}" method="POST" enctype="multipart/form-data" class="mt-2">
                                    @csrf
                                    <div class="input-group">
                                        <input type="file" name="file_ppt" class="form-control" accept=".ppt,.pptx,.pdf" required>
                                        <button class="btn btn-warning text-white" type="submit"><i class="bi bi-upload me-1"></i> Upload Revisi</button>
                                    </div>
                                </form>
                            @endif
                        @endif
                    </div>
                </div>

                {{-- Step 2: Hasil Penilaian --}}
                @if ($presentasi->nilai)
                    <div class="custom-card">
                        <div class="card-header-custom bg-gradient-purple">
                            <span><i class="bi bi-award me-2"></i>2. Hasil Penilaian</span>
                        </div>
                        <div class="card-body-custom">
                            <div class="row align-items-center">
                                <div class="col-md-4 mb-3 mb-md-0">
                                    <div class="grade-box">
                                        <small class="text-muted d-block mb-2 font-weight-bold text-uppercase">Nilai Akhir</small>
                                        <div class="grade-value {{ $presentasi->nilai == 'A' || $presentasi->nilai == 'B' ? 'text-success-custom' : ($presentasi->nilai == 'C' ? 'text-warning-custom' : 'text-danger-custom') }}">
                                            {{ $presentasi->nilai }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    @if ($presentasi->nilai == 'D')
                                        <div class="alert alert-danger border-0">
                                            <h6 class="fw-bold"><i class="bi bi-x-circle-fill me-2"></i>Penelitian Ditolak</h6>
                                            <p class="small mb-0">Maaf, Anda harus mengulang proses dari awal (pengajuan pra penelitian).</p>
                                        </div>
                                    @elseif ($presentasi->nilai == 'C')
                                        <div class="alert alert-warning border-0">
                                            <h6 class="fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i>Revisi Diperlukan</h6>
                                            <p class="small mb-0">Silakan perbaiki file presentasi sesuai masukan penguji.</p>
                                        </div>
                                    @else
                                        <div class="alert alert-success border-0 bg-success bg-opacity-10 text-success">
                                            <h6 class="fw-bold"><i class="bi bi-check-circle-fill me-2"></i>Lulus Presentasi!</h6>
                                            <p class="small mb-0">Selamat! Silakan lanjutkan ke tahap upload laporan akhir.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            @if ($presentasi->hasil_penilaian)
                                <div class="mt-4">
                                    <h6 class="fw-bold mb-3 text-muted text-uppercase text-xs ls-1">Detail Masukan Penguji</h6>
                                    <div class="accordion" id="accordionEvaluasi">
                                        @foreach ($presentasi->hasil_penilaian as $index => $item)
                                            <div class="accordion-item border-0 shadow-sm mb-2 rounded overflow-hidden">
                                                <h2 class="accordion-header">
                                                    <button class="accordion-button collapsed bg-light text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $index }}">
                                                        {{ $index + 1 }}. {{ $item['judul'] }}
                                                    </button>
                                                </h2>
                                                <div id="collapse{{ $index }}" class="accordion-collapse collapse" data-bs-parent="#accordionEvaluasi">
                                                    <div class="accordion-body text-muted small">
                                                        {{ $item['keterangan'] }}
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Step 3: Upload Laporan --}}
                @if (in_array($presentasi->nilai, ['A', 'B']))
                    <div class="custom-card">
                        <div class="card-header-custom bg-gradient-green">
                            <div class="d-flex w-100 justify-content-between align-items-center">
                                <span><i class="bi bi-journal-check me-2"></i>3. Laporan Akhir</span>
                                @if($presentasi->file_laporan)<span class="badge bg-white text-success rounded-pill"><i class="bi bi-check-circle-fill me-1"></i>Uploaded</span>@endif
                            </div>
                        </div>
                        <div class="card-body-custom">
                            @if (!$presentasi->file_laporan)
                                <form action="{{ route('presentasi.upload-laporan', $presentasi->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="upload-zone" onclick="document.getElementById('fileLaporan').click()">
                                        <i class="bi bi-cloud-arrow-up upload-icon text-success"></i>
                                        <h6 class="fw-bold">Upload Laporan Akhir (PDF/DOC)</h6>
                                        <p class="text-muted small mb-0">Pastikan format penulisan sudah sesuai pedoman.</p>
                                        <input type="file" id="fileLaporan" name="file_laporan" class="d-none" accept=".pdf,.doc,.docx" required onchange="this.form.submit()">
                                    </div>
                                </form>
                            @else
                                <div class="d-flex justify-content-between align-items-center p-3 border rounded bg-light mb-3">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-file-earmark-text-fill text-success fs-1 me-3"></i>
                                        <div>
                                            <div class="fw-bold text-dark">File Laporan Akhir</div>
                                            <small class="text-muted">Diupload: {{ $presentasi->laporan_uploaded_at->format('d M Y H:i') }}</small>
                                        </div>
                                    </div>
                                    <a href="{{ Storage::url($presentasi->file_laporan) }}" target="_blank" class="btn btn-sm btn-outline-success">
                                        <i class="bi bi-download me-1"></i> Download
                                    </a>
                                </div>

                                {{-- Status Laporan --}}
                                @if ($presentasi->status_laporan == 'pending')
                                    <div class="alert alert-warning border-0 d-flex align-items-center">
                                        <div class="spinner-border spinner-border-sm me-2 text-warning" role="status"></div>
                                        <div>Menunggu verifikasi admin</div>
                                    </div>
                                @elseif ($presentasi->status_laporan == 'revisi')
                                    <div class="alert alert-danger border-0">
                                        <h6 class="fw-bold"><i class="bi bi-pencil-square me-2"></i>Revisi Laporan Diperlukan</h6>
                                        <p class="small mb-2">{{ $presentasi->keterangan_review }}</p>
                                    </div>
                                    <form action="{{ route('presentasi.upload-laporan', $presentasi->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <label class="form-label small fw-bold">Upload File Revisi</label>
                                        <div class="input-group">
                                            <input type="file" name="file_laporan" class="form-control" accept=".pdf,.doc,.docx" required>
                                            <button class="btn btn-danger" type="submit"><i class="bi bi-upload me-1"></i> Upload</button>
                                        </div>
                                    </form>
                                @elseif ($presentasi->status_laporan == 'approved')
                                    <div class="alert alert-success border-0">
                                        <i class="bi bi-check-circle-fill me-2"></i> <strong>Laporan Disetujui!</strong>
                                        <p class="small mb-0 mt-1">{{ $presentasi->keterangan_review }}</p>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Step 4: Selesai --}}
                @if ($presentasi->status_final == 'selesai')
                    <div class="custom-card border-0 bg-gradient-maroon text-white animate-up">
                        <div class="card-body-custom text-center py-5">
                            <i class="bi bi-award-fill display-3 mb-3 text-white-50"></i>
                            <h3 class="fw-bold">Penelitian Selesai</h3>
                            <p class="mb-4 text-white-50">Terima kasih telah melakukan penelitian di instansi kami.</p>
                            
                            <div class="d-flex justify-content-center gap-3 flex-wrap">
                                @if ($presentasi->surat_selesai)
                                    <a href="{{ Storage::url($presentasi->surat_selesai) }}" target="_blank" class="btn btn-light shadow-sm fw-bold text-maroon">
                                        <i class="bi bi-file-earmark-check me-2"></i> Surat Keterangan Selesai
                                    </a>
                                @endif
                                @if ($presentasi->sertifikat)
                                    <a href="{{ Storage::url($presentasi->sertifikat) }}" target="_blank" class="btn btn-outline-light fw-bold">
                                        <i class="bi bi-patch-check me-2"></i> Sertifikat
                                    </a>
                                @endif
                            </div>

                            <div class="mt-4 small text-white-50 fst-italic">
                                <i class="bi bi-info-circle me-1"></i> Seluruh aset penelitian diambil / disimpan di instansi kami.
                            </div>
                        </div>
                    </div>
                @endif

                {{-- 5. Dokumen Anggota Tim (Tambahan untuk Mahasiswa) --}}
                @if ($presentasi->praPenelitian && $presentasi->praPenelitian->anggotas->count() > 0)
                    <div class="custom-card mt-4">
                        <div class="card-header-custom bg-gradient-blue">
                            <span><i class="bi bi-people-fill me-2"></i>Dokumen Anggota Tim</span>
                        </div>
                        <div class="p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0 align-middle">
                                    <thead class="bg-light text-muted small text-uppercase">
                                        <tr>
                                            <th class="ps-4 py-3" width="5%">No</th>
                                            <th class="py-3">Nama Anggota</th>
                                            <th class="py-3">Jenjang</th>
                                            <th class="pe-4 py-3 text-end">Download</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($presentasi->praPenelitian->anggotas as $index => $anggota)
                                            <tr>
                                                <td class="ps-4 text-muted">{{ $index + 1 }}</td>
                                                <td class="fw-bold text-dark">{{ $anggota->nama }}</td>
                                                <td>
                                                    <span class="badge bg-light text-dark border fw-normal">
                                                        {{ $anggota->jenjang }}
                                                    </span>
                                                </td>
                                                <td class="pe-4 text-end">
                                                    <div class="d-flex gap-2 justify-content-end">
                                                        {{-- Sertifikat --}}
                                                        <a href="{{ route('presentasi.download-sertifikat', [$presentasi->id, urlencode($anggota->nama)]) }}" 
                                                           class="btn btn-sm btn-outline-success" 
                                                           title="Download Sertifikat" 
                                                           target="_blank">
                                                            <i class="bi bi-award-fill"></i> 
                                                            <span class="d-none d-md-inline ms-1">Sertifikat</span>
                                                        </a>
                                                        
                                                        {{-- Surat Selesai --}}
                                                        <a href="{{ route('presentasi.download-surat-selesai', [$presentasi->id, urlencode($anggota->nama)]) }}" 
                                                           class="btn btn-sm btn-outline-secondary" 
                                                           title="Download Surat Selesai" 
                                                           target="_blank">
                                                            <i class="bi bi-file-pdf"></i> 
                                                            <span class="d-none d-md-inline ms-1">Surat</span>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif


            </div>
        </div>
    </div>
@endsection