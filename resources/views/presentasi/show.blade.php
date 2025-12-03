@extends('layouts.app')

@section('title', 'Presentasi Penelitian')
@section('page-title', 'Presentasi Penelitian')

@section('content')
<div class="container py-4">
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="mb-4">
        <a href="{{ route('pengajuan.detail', 'pra_penelitian') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <div class="row g-4">
        {{-- Info Jadwal --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-maroon text-white">
                    <h5 class="mb-0"><i class="bi bi-calendar-event me-2"></i>Jadwal Presentasi</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted d-block"><i class="bi bi-calendar3 me-1"></i>Tanggal</small>
                        <h5 class="mb-0">{{ $presentasi->tanggal_presentasi->format('d F Y') }}</h5>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block"><i class="bi bi-clock me-1"></i>Waktu</small>
                        <strong>{{ $presentasi->waktu_mulai }} - {{ $presentasi->waktu_selesai }}</strong>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block"><i class="bi bi-geo-alt me-1"></i>Tempat</small>
                        <strong>{{ $presentasi->tempat }}</strong>
                    </div>
                    @if ($presentasi->keterangan_admin)
                        <div class="mb-3">
                            <small class="text-muted d-block"><i class="bi bi-info-circle me-1"></i>Keterangan</small>
                            <p class="small mb-0">{{ $presentasi->keterangan_admin }}</p>
                        </div>
                    @endif

                    <hr>

                    <div class="mb-3">
                        <small class="text-muted d-block">Pembimbing (CI)</small>
                        <strong>{{ $pengajuan->ci_nama }}</strong><br>
                        <a href="tel:{{ $pengajuan->ci_no_hp }}" class="text-decoration-none small">
                            <i class="bi bi-telephone-fill me-1"></i>{{ $pengajuan->ci_no_hp }}
                        </a>
                    </div>

                    <div>
    <small class="text-muted d-block mb-1">Link Penilaian CI</small>
    
    <div class="input-group">
        <input type="text" class="form-control form-control-sm" value="{{ route('ci.penilaian', $presentasi->id) }}" id="inputLinkPenilaian" readonly>
        
        <button class="btn btn-sm btn-outline-primary" type="button" onclick="copyLink()">
            <i class="bi bi-clipboard" id="iconCopy"></i> <span id="textCopy">Salin</span>
        </button>
    </div>

    <small class="text-muted d-block mt-1">Klik tombol salin dan kirim ke CI Anda</small>
</div>
                </div>
            </div>
        </div>

        {{-- Upload & Status --}}
        <div class="col-lg-8">
            {{-- Step 1: Upload PPT --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <h5 class="mb-0">
                        <i class="bi bi-1-circle me-2"></i>Upload File Presentasi (PPT)
                    </h5>
                </div>
                <div class="card-body">
                    @if (!$presentasi->file_ppt)
                        {{-- Form Upload --}}
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i> 
                            Silakan upload file presentasi Anda. File hanya bisa diupload sekali.
                        </div>
                        <form action="{{ route('presentasi.upload-ppt', $presentasi->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-bold">Pilih File PPT/PDF</label>
                                <input type="file" name="file_ppt" class="form-control @error('file_ppt') is-invalid @enderror" accept=".ppt,.pptx,.pdf" required>
                                @error('file_ppt')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Format: PPT, PPTX, atau PDF (Max 10MB)</small>
                            </div>
                            <button type="submit" class="btn btn-maroon" onclick="return confirm('Upload file presentasi? File hanya bisa diupload sekali.')">
                                <i class="bi bi-upload me-1"></i> Upload File
                            </button>
                        </form>
                    @else
                        {{-- File Sudah Diupload --}}
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle-fill me-2"></i> File presentasi sudah diupload
                        </div>
                        <a href="{{ Storage::url($presentasi->file_ppt) }}" target="_blank" class="btn btn-outline-primary">
                            <i class="bi bi-download me-1"></i> Download File Anda
                        </a>
                        <small class="d-block mt-2 text-muted">
                            Diupload: {{ $presentasi->uploaded_at->format('d M Y H:i') }}
                        </small>

                        @if ($presentasi->nilai == 'C')
                            {{-- Revisi: Bisa Upload Ulang --}}
                            <hr>
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i> 
                                <strong>Revisi Diperlukan!</strong><br>
                                Silakan upload ulang file presentasi yang sudah diperbaiki.
                            </div>
                            <form action="{{ route('presentasi.upload-ppt', $presentasi->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Upload File Revisi</label>
                                    <input type="file" name="file_ppt" class="form-control" accept=".ppt,.pptx,.pdf" required>
                                    <small class="text-muted">Format: PPT, PPTX, atau PDF (Max 10MB)</small>
                                </div>
                                <button type="submit" class="btn btn-warning">
                                    <i class="bi bi-upload me-1"></i> Upload File Revisi
                                </button>
                            </form>
                        @endif
                    @endif
                </div>
            </div>

            {{-- Step 2: Hasil Penilaian --}}
            @if ($presentasi->nilai)
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
                        <h5 class="mb-0">
                            <i class="bi bi-2-circle me-2"></i>Hasil Penilaian CI
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-{{ $presentasi->nilai == 'A' || $presentasi->nilai == 'B' ? 'success' : ($presentasi->nilai == 'C' ? 'warning' : 'danger') }} mb-3">
                            <h3 class="mb-0">
                                <i class="bi bi-trophy-fill me-2"></i>Nilai: <strong>{{ $presentasi->nilai }}</strong>
                            </h3>
                        </div>

                        @if ($presentasi->nilai == 'D')
                            <div class="alert alert-danger">
                                <i class="bi bi-x-circle-fill me-2"></i>
                                <strong>Penelitian Ditolak</strong><br>
                                Anda harus mengulang dari awal (pengajuan pra penelitian).
                            </div>
                        @elseif ($presentasi->nilai == 'C')
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                <strong>Revisi Diperlukan</strong><br>
                                Silakan perbaiki file presentasi Anda dan upload ulang.
                            </div>
                        @else
                            <div class="alert alert-success">
                                <i class="bi bi-check-circle-fill me-2"></i>
                                <strong>Selamat! Anda lulus presentasi.</strong><br>
                                Lanjutkan dengan mengupload laporan akhir.
                            </div>
                        @endif

                        @if ($presentasi->hasil_penilaian)
                            <h6 class="fw-bold mb-3">Detail Penilaian:</h6>
                            @foreach ($presentasi->hasil_penilaian as $index => $item)
                                <div class="card mb-2 border-0 shadow-sm">
                                    <div class="card-body p-3">
                                        <h6 class="fw-bold mb-1 text-primary">{{ $index + 1 }}. {{ $item['judul'] }}</h6>
                                        <p class="mb-0 small">{{ $item['keterangan'] }}</p>
                                    </div>
                                </div>
                            @endforeach
                        @endif

                        <small class="text-muted d-block mt-3">
                            Dinilai oleh: {{ $pengajuan->ci_nama }}<br>
                            Waktu: {{ $presentasi->dinilai_at->format('d F Y H:i') }}
                        </small>
                    </div>
                </div>
            @endif

            {{-- Step 3: Upload Laporan (Jika A/B) --}}
            @if (in_array($presentasi->nilai, ['A', 'B']))
                <div class="card border-0 shadow-sm">
                    <div class="card-header" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white;">
                        <h5 class="mb-0">
                            <i class="bi bi-3-circle me-2"></i>Upload Laporan Akhir
                        </h5>
                    </div>
                    <div class="card-body">
                        @if (!$presentasi->file_laporan)
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i> 
                                Silakan upload laporan akhir penelitian Anda dalam format PDF atau DOCX.
                            </div>
                            <form action="{{ route('presentasi.upload-laporan', $presentasi->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Pilih File Laporan</label>
                                    <input type="file" name="file_laporan" class="form-control @error('file_laporan') is-invalid @enderror" accept=".pdf,.doc,.docx" required>
                                    @error('file_laporan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Format: PDF, DOC, atau DOCX (Max 10MB)</small>
                                </div>
                                <button type="submit" class="btn btn-success" onclick="return confirm('Upload laporan akhir?')">
                                    <i class="bi bi-upload me-1"></i> Upload Laporan
                                </button>
                            </form>
                        @else
                            <div class="alert alert-success">
                                <i class="bi bi-check-circle-fill me-2"></i> Laporan sudah diupload
                            </div>
                            <a href="{{ Storage::url($presentasi->file_laporan) }}" target="_blank" class="btn btn-outline-primary">
                                <i class="bi bi-download me-1"></i> Download Laporan Anda
                            </a>
                            <small class="d-block mt-2 text-muted">
                                Diupload: {{ $presentasi->laporan_uploaded_at->format('d M Y H:i') }}
                            </small>

                            <hr>

                            @if ($presentasi->status_laporan == 'pending')
                                <div class="alert alert-warning">
                                    <i class="bi bi-clock-history me-2"></i> Menunggu review dari admin
                                </div>
                            @elseif ($presentasi->status_laporan == 'revisi')
                                <div class="alert alert-warning">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                    <strong>Revisi Diperlukan</strong><br>
                                    {{ $presentasi->keterangan_review }}
                                </div>
                                <form action="{{ route('presentasi.upload-laporan', $presentasi->id) }}" method="POST" enctype="multipart/form-data" class="mt-3">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Upload Laporan Revisi</label>
                                        <input type="file" name="file_laporan" class="form-control" accept=".pdf,.doc,.docx" required>
                                    </div>
                                    <button type="submit" class="btn btn-warning">
                                        <i class="bi bi-upload me-1"></i> Upload Revisi
                                    </button>
                                </form>
                            @elseif ($presentasi->status_laporan == 'approved')
                                <div class="alert alert-success">
                                    <i class="bi bi-check-circle-fill me-2"></i>
                                    <strong>Laporan Disetujui!</strong>
                                    @if ($presentasi->keterangan_review)
                                        <br>{{ $presentasi->keterangan_review }}
                                    @endif
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            @endif

            {{-- Step 4: Dokumen Final (Jika Selesai) --}}
            @if ($presentasi->status_final == 'selesai')
                <div class="card border-0 shadow-sm mt-4" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                    <div class="card-body text-white text-center py-5">
                        <i class="bi bi-trophy-fill display-1 mb-3"></i>
                        <h3 class="fw-bold mb-3">Selamat! Penelitian Anda Selesai</h3>
                        <p class="mb-4">Anda telah menyelesaikan seluruh tahapan penelitian dengan baik.</p>
                        
                        <div class="d-flex gap-3 justify-content-center flex-wrap">
                            @if ($presentasi->surat_selesai)
                                <a href="{{ Storage::url($presentasi->surat_selesai) }}" target="_blank" class="btn btn-light btn-lg">
                                    <i class="bi bi-file-earmark-check me-2"></i> Download Surat Selesai
                                </a>
                            @endif
                            @if ($presentasi->sertifikat)
                                <a href="{{ Storage::url($presentasi->sertifikat) }}" target="_blank" class="btn btn-outline-light btn-lg">
                                    <i class="bi bi-award me-2"></i> Download Sertifikat
                                </a>
                            @endif
                        </div>

                        <div class="alert alert-light mt-4 text-dark">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Penting:</strong> Seluruh aset penelitian (data, dokumen, dll) menjadi milik rumah sakit. 
                            Silakan hubungi admin untuk pengambilan aset fisik.
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    function copyLink() {
        // 1. Ambil elemen input
        var copyText = document.getElementById("inputLinkPenilaian");

        // 2. Select teks di dalamnya
        copyText.select();
        copyText.setSelectionRange(0, 99999); // Untuk support mobile device

        // 3. Salin ke clipboard
        navigator.clipboard.writeText(copyText.value).then(function() {
            // 4. Beri feedback (Ubah Ikon & Teks sementara)
            var icon = document.getElementById("iconCopy");
            var text = document.getElementById("textCopy");

            icon.className = "bi bi-check-lg"; // Ganti ikon jadi centang
            text.innerText = "Tersalin!";
            
            // Kembalikan ke semula setelah 2 detik
            setTimeout(function() {
                icon.className = "bi bi-clipboard";
                text.innerText = "Salin";
            }, 2000);
        }, function(err) {
            alert('Gagal menyalin link');
        });
    }
</script>
@endsection