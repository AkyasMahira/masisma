@extends('layouts.app')

@section('title', 'Detail Presentasi')
@section('page-title', 'Detail Presentasi')

@section('content')
    <style>
        :root {
            --primary-maroon: #7c1316;
            --primary-light: #fcf0f1;
            --text-dark: #1e293b;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --card-radius: 12px;
            --shadow-sm: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
            --transition: all 0.2s ease-in-out;
        }

        body { background-color: #f8fafc; }

        /* --- Page Header --- */
        .page-header-wrapper {
            background: #fff;
            border-radius: var(--card-radius);
            padding: 1.5rem 2rem;
            margin-bottom: 2rem;
            border-left: 5px solid var(--primary-maroon);
            box-shadow: var(--shadow-sm);
            display: flex; justify-content: space-between; align-items: center;
        }

        /* --- Cards --- */
        .custom-card {
            background: #fff;
            border: 1px solid var(--border-color);
            border-radius: var(--card-radius);
            box-shadow: var(--shadow-sm);
            margin-bottom: 1.5rem;
            overflow: hidden;
            transition: var(--transition);
        }
        .custom-card:hover { box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1); }

        .card-header-main {
            background-color: #fff;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
            font-weight: 600;
            color: var(--text-dark);
            display: flex; align-items: center; gap: 0.75rem;
            font-size: 1rem;
        }
        
        .card-header-maroon {
            background-color: var(--primary-maroon);
            color: white;
            padding: 1rem 1.5rem;
            font-weight: 600;
            display: flex; align-items: center; gap: 0.5rem;
        }

        .card-body-custom { padding: 1.5rem; }

        /* --- Info Fields (Left Side) --- */
        .info-group { margin-bottom: 1.25rem; }
        .info-label {
            font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted);
            font-weight: 700; margin-bottom: 0.25rem; letter-spacing: 0.5px;
        }
        .info-value {
            font-size: 0.95rem; color: var(--text-dark); font-weight: 500;
            display: flex; align-items: center; gap: 0.5rem;
        }

        /* --- Copy Link Box --- */
        .copy-link-container {
            background: #f1f5f9;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 0.5rem 0.75rem;
            display: flex; align-items: center; gap: 0.5rem;
            margin-top: 0.5rem;
        }
        .copy-input {
            border: none; background: transparent; flex-grow: 1;
            font-family: 'Courier New', monospace; color: var(--primary-maroon); font-weight: 600;
            font-size: 0.85rem; outline: none;
        }
        .btn-copy {
            background: white; border: 1px solid var(--border-color); color: var(--text-dark);
            border-radius: 6px; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;
            transition: var(--transition); cursor: pointer;
        }
        .btn-copy:hover { background: var(--primary-maroon); color: white; border-color: var(--primary-maroon); }

        /* --- File Download Card --- */
        .file-download-card {
            display: flex; align-items: center; padding: 1rem 1.25rem;
            background: #fff; border: 1px solid var(--border-color); border-radius: 10px;
            text-decoration: none; color: var(--text-dark); transition: var(--transition);
        }
        .file-download-card:hover {
            border-color: var(--primary-maroon); background-color: var(--primary-light);
        }
        .file-icon {
            width: 42px; height: 42px; background: #fee2e2; color: #dc2626;
            border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; margin-right: 1rem;
        }

        /* --- Score Display --- */
        .score-box {
            background: #f8fafc; border: 1px solid var(--border-color); border-radius: 12px;
            padding: 1.5rem; text-align: center; height: 100%; display: flex; flex-direction: column; justify-content: center;
        }
        .score-val { font-size: 3.5rem; font-weight: 800; line-height: 1; margin-bottom: 0.5rem; }
        .score-lbl { font-size: 0.85rem; text-transform: uppercase; font-weight: 600; color: var(--text-muted); }

        .text-grade-A, .text-grade-B { color: #16a34a; }
        .text-grade-C { color: #ca8a04; }
        .text-grade-D { color: #dc2626; }

        /* --- Buttons --- */
        .btn-maroon {
            background-color: var(--primary-maroon); color: white; border: none;
            border-radius: 8px; padding: 0.5rem 1.25rem; font-weight: 600; font-size: 0.9rem;
        }
        .btn-maroon:hover { background-color: #5e0e10; color: white; }

        .btn-outline-custom {
            border: 1px solid var(--border-color); background: white; color: var(--text-dark);
            border-radius: 8px; padding: 0.5rem 1rem; font-weight: 500; text-decoration: none;
            display: inline-flex; align-items: center; font-size: 0.9rem; transition: var(--transition);
        }
        .btn-outline-custom:hover { background: #f1f5f9; border-color: #cbd5e1; }

        .animate-up { animation: fadeInUp 0.5s ease-out forwards; opacity: 0; transform: translateY(15px); }
        @keyframes fadeInUp { to { opacity: 1; transform: translateY(0); } }
    </style>

    {{-- Header --}}
    <div class="page-header-wrapper animate-up">
        <div>
            <h4 class="fw-bold mb-1" style="color: var(--primary-maroon);">
                <i class="bi bi-easel2-fill me-2"></i>Detail Presentasi
            </h4>
            <div class="text-muted small">Pantau progres presentasi, penilaian, dan laporan akhir mahasiswa.</div>
        </div>
        <div>
            <a href="{{ route('admin.presentasi.index') }}" class="btn-outline-custom shadow-sm">
                <i class="bi bi-arrow-left me-2"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row g-4">
        
        {{-- KOLOM KIRI: Info Detail --}}
        <div class="col-lg-4 animate-up" style="animation-delay: 0.1s;">
            
            {{-- Info Mahasiswa & Jadwal --}}
            <div class="custom-card">
                <div class="card-header-maroon">
                    <i class="bi bi-person-vcard-fill"></i> Informasi Mahasiswa
                </div>
                <div class="card-body-custom">
                    <div class="info-group">
                        <div class="info-label">Nama Mahasiswa</div>
                        <div class="info-value fw-bold">{{ $presentasi->user->name }}</div>
                    </div>

                    <div class="info-group">
                        <div class="info-label">Email</div>
                        <div class="info-value text-break">{{ $presentasi->user->email }}</div>
                    </div>

                    <div class="info-group">
                        <div class="info-label">Judul Penelitian</div>
                        <div class="info-value fst-italic">"{{ $presentasi->praPenelitian->judul }}"</div>
                    </div>
                    
                    <hr class="my-4 border-light">

                    <div class="info-group">
                        <div class="info-label">Jadwal Presentasi</div>
                        <div class="info-value mb-1"><i class="bi bi-calendar3 text-muted"></i> {{ $presentasi->tanggal_presentasi->format('d F Y') }}</div>
                        <div class="info-value"><i class="bi bi-clock text-muted"></i> {{ $presentasi->waktu_mulai }} - {{ $presentasi->waktu_selesai }} WIB</div>
                    </div>

                    <div class="info-group mb-0">
                        <div class="info-label">Tempat</div>
                        <div class="info-value text-danger"><i class="bi bi-geo-alt-fill"></i> {{ $presentasi->tempat }}</div>
                    </div>
                </div>
            </div>

            {{-- Link Penilaian CI --}}
            <div class="custom-card">
                <div class="card-header-main">
                    <i class="bi bi-link-45deg fs-5 text-primary"></i> Link Penilaian CI
                </div>
                <div class="card-body-custom">
                    <p class="small text-muted mb-2">Bagikan link ini kepada Pembimbing Lapangan (CI) untuk input nilai.</p>
                    <div class="copy-link-container">
                        <input type="text" class="copy-input" id="linkCI" value="{{ route('ci.penilaian', $presentasi->id) }}" readonly>
                        <button type="button" class="btn-copy" onclick="copyLink()" title="Salin Link">
                            <i class="bi bi-clipboard"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: File & Proses --}}
        <div class="col-lg-8 animate-up" style="animation-delay: 0.2s;">
            
            {{-- 1. File Presentasi (PPT) --}}
            <div class="custom-card">
                <div class="card-header-main">
                    <i class="bi bi-file-earmark-slides-fill text-warning fs-5"></i> File Presentasi
                </div>
                <div class="card-body-custom">
                    @if ($presentasi->file_ppt)
                        <a href="{{ Storage::url($presentasi->file_ppt) }}" target="_blank" class="file-download-card">
                            <div class="file-icon"><i class="bi bi-file-earmark-ppt-fill"></i></div>
                            <div class="flex-grow-1">
                                <div class="fw-bold text-dark">Materi Presentasi</div>
                                <div class="small text-muted">Diupload: {{ $presentasi->uploaded_at->format('d M Y H:i') }}</div>
                            </div>
                            <div class="d-flex align-items-center gap-2 text-primary small fw-bold">
                                Unduh <i class="bi bi-download"></i>
                            </div>
                        </a>
                    @else
                        <div class="alert alert-warning border-0 bg-warning bg-opacity-10 d-flex align-items-center m-0">
                            <i class="bi bi-exclamation-circle-fill fs-4 me-3 text-warning"></i>
                            <div>
                                <strong class="text-dark">Belum Diupload</strong>
                                <div class="small text-muted">Mahasiswa belum mengupload file materi presentasi.</div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- 2. Hasil Penilaian --}}
            @if ($presentasi->nilai)
                <div class="custom-card">
                    <div class="card-header-main">
                        <i class="bi bi-award-fill text-success fs-5"></i> Hasil Penilaian
                    </div>
                    <div class="card-body-custom">
                        <div class="row align-items-center g-4">
                            <div class="col-md-4">
                                <div class="score-box">
                                    <div class="score-val text-grade-{{ $presentasi->nilai }}">{{ $presentasi->nilai }}</div>
                                    <div class="score-lbl">Predikat Akhir</div>
                                </div>
                            </div>
                            <div class="col-md-8">
                                @if ($presentasi->hasil_penilaian)
                                    <h6 class="fw-bold mb-3 text-dark border-bottom pb-2">Rincian Penilaian</h6>
                                    <div class="list-group list-group-flush">
                                        @foreach ($presentasi->hasil_penilaian as $index => $item)
                                            <div class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center bg-transparent border-bottom-0">
                                                <span class="text-secondary small">{{ $loop->iteration }}. {{ $item['judul'] }}</span>
                                                <span class="fw-bold text-dark badge bg-light text-dark border">{{ $item['nilai'] ?? '-' }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="text-end mt-3 border-top pt-2">
                            <small class="text-muted fst-italic">Dinilai pada: {{ $presentasi->dinilai_at->format('d M Y H:i') }}</small>
                        </div>
                    </div>
                </div>
            @endif

            {{-- 3. Laporan Akhir & Review --}}
            @if (in_array($presentasi->nilai, ['A', 'B']))
                <div class="custom-card">
                    <div class="card-header-main">
                        <i class="bi bi-file-text-fill text-info fs-5"></i> Laporan Akhir
                    </div>
                    <div class="card-body-custom">
                        @if ($presentasi->file_laporan)
                            <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-light rounded border">
                                <div>
                                    <div class="fw-bold text-dark">File Laporan Akhir</div>
                                    <small class="text-muted">Uploaded: {{ $presentasi->laporan_uploaded_at->format('d M Y') }}</small>
                                </div>
                                <a href="{{ Storage::url($presentasi->file_laporan) }}" target="_blank" class="btn btn-outline-primary btn-sm shadow-sm bg-white">
                                    <i class="bi bi-download me-1"></i> Download
                                </a>
                            </div>

                            @if ($presentasi->status_laporan == 'pending')
                                <div class="bg-white p-4 rounded-3 border border-warning">
                                    <h6 class="fw-bold mb-3 text-dark border-bottom pb-2"><i class="bi bi-pencil-square me-2 text-warning"></i>Review Laporan</h6>
                                    <form action="{{ route('admin.presentasi.review-laporan', $presentasi->id) }}" method="POST" id="reviewForm">
                                        @csrf
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <label class="form-label small fw-bold text-muted">Keputusan</label>
                                                <select name="status" class="form-select form-select-sm" required>
                                                    <option value="">Pilih Status...</option>
                                                    <option value="approved">Setujui (Selesai)</option>
                                                    <option value="revisi">Minta Revisi</option>
                                                </select>
                                            </div>
                                            <div class="col-md-8">
                                                <label class="form-label small fw-bold text-muted">Catatan / Keterangan</label>
                                                <textarea name="keterangan" rows="1" class="form-control form-control-sm" placeholder="Tulis catatan untuk mahasiswa..." required></textarea>
                                            </div>
                                        </div>
                                        <div class="text-end mt-3">
                                            <button type="submit" class="btn btn-maroon btn-sm">
                                                <i class="bi bi-send-fill me-1"></i> Kirim Review
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            @else
                                <div class="alert alert-{{ $presentasi->status_laporan == 'approved' ? 'success' : 'warning' }} border-0 shadow-sm m-0">
                                    <div class="d-flex">
                                        <i class="bi bi-{{ $presentasi->status_laporan == 'approved' ? 'check-circle-fill' : 'exclamation-triangle-fill' }} fs-4 me-3"></i>
                                        <div>
                                            <strong class="text-uppercase ls-1" style="font-size: 0.85rem;">Status: {{ $presentasi->status_laporan }}</strong>
                                            @if ($presentasi->keterangan_review)
                                                <p class="mb-0 mt-1 small">{{ $presentasi->keterangan_review }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @else
                            <div class="text-center py-4 text-muted bg-light rounded border border-dashed">
                                <i class="bi bi-hourglass-split display-4 opacity-25 mb-2 d-block"></i>
                                <span class="fw-medium">Menunggu Upload Laporan</span>
                                <div class="small mt-1">Mahasiswa belum mengupload file laporan akhir.</div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- 4. Dokumen Final (Selesai) --}}
            @if ($presentasi->status_final == 'selesai')
                <div class="custom-card border-top border-4 border-success bg-white">
                    <div class="card-body-custom d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="fw-bold text-success mb-1"><i class="bi bi-patch-check-fill me-2"></i>Proses Selesai</h5>
                            <p class="text-muted small mb-0">Seluruh tahapan penelitian telah diselesaikan dengan baik.</p>
                        </div>
                        <div class="d-flex gap-2">
                            @if ($presentasi->surat_selesai)
                                <a href="{{ Storage::url($presentasi->surat_selesai) }}" target="_blank" class="btn btn-outline-secondary btn-sm fw-bold">
                                    <i class="bi bi-file-pdf me-1"></i> Surat Selesai
                                </a>
                            @endif
                            @if ($presentasi->sertifikat)
                                <a href="{{ Storage::url($presentasi->sertifikat) }}" target="_blank" class="btn btn-success btn-sm text-white fw-bold shadow-sm">
                                    <i class="bi bi-award-fill me-1"></i> Sertifikat
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- 5. Dokumen Anggota (Jika ada) --}}
                @if ($presentasi->praPenelitian && $presentasi->praPenelitian->anggotas->count() > 0)
                    <div class="custom-card">
                        <div class="card-header-main">
                            <i class="bi bi-people-fill text-info fs-5"></i> Dokumen Anggota Tim
                        </div>
                        <div class="p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0 align-middle">
                                    <thead class="bg-light text-muted small text-uppercase">
                                        <tr>
                                            <th class="ps-4 py-3">No</th>
                                            <th class="py-3">Nama Anggota</th>
                                            <th class="py-3">Jenjang</th>
                                            <th class="pe-4 py-3 text-end">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($presentasi->praPenelitian->anggotas as $index => $anggota)
                                            <tr>
                                                <td class="ps-4 text-muted">{{ $index + 1 }}</td>
                                                <td class="fw-bold text-dark">{{ $anggota->nama }}</td>
                                                <td><span class="badge bg-light text-dark border fw-normal">{{ $anggota->jenjang }}</span></td>
                                                <td class="pe-4 text-end">
                                                    <div class="d-flex gap-2 justify-content-end">
                                                        <a href="{{ route('presentasi.download-sertifikat', [$presentasi->id, urlencode($anggota->nama)]) }}" class="btn btn-xs btn-outline-success" title="Sertifikat">
                                                            <i class="bi bi-award-fill"></i>
                                                        </a>
                                                        <a href="{{ route('presentasi.download-surat-selesai', [$presentasi->id, urlencode($anggota->nama)]) }}" class="btn btn-xs btn-outline-secondary" title="Surat Selesai">
                                                            <i class="bi bi-file-pdf"></i>
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
            @endif

        </div>
    </div>

    {{-- Script JavaScript untuk Copy Link & SweetAlert --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // SweetAlert untuk Session Flash
        @if(session('success'))
            Swal.fire({ icon: 'success', title: 'Berhasil!', text: '{{ session("success") }}', timer: 2500, showConfirmButton: false });
        @endif
        @if(session('error'))
            Swal.fire({ icon: 'error', title: 'Gagal', text: '{{ session("error") }}' });
        @endif

        // Konfirmasi Form Review
        const reviewForm = document.getElementById('reviewForm');
        if(reviewForm){
            reviewForm.addEventListener('submit', function(e){
                e.preventDefault();
                Swal.fire({
                    title: 'Kirim Review?', text: "Status laporan akan diperbarui.", icon: 'question',
                    showCancelButton: true, confirmButtonColor: '#7c1316', cancelButtonColor: '#6c757d', confirmButtonText: 'Ya, Kirim'
                }).then((result) => { if (result.isConfirmed) { this.submit(); } });
            });
        }

        // Fungsi Copy Link (Support HTTP & HTTPS)
        function copyLink() {
            var copyText = document.getElementById("linkCI");
            copyText.select(); copyText.setSelectionRange(0, 99999);
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(copyText.value).then(function() { tampilkanToastSukses(); }, function(err) { manualCopy(); });
            } else { manualCopy(); }

            function manualCopy() {
                try { document.execCommand('copy'); tampilkanToastSukses(); } 
                catch (err) { Swal.fire({ icon: 'error', title: 'Gagal', text: 'Browser tidak mengizinkan salin otomatis.' }); }
            }

            function tampilkanToastSukses() {
                Swal.fire({
                    icon: 'success', title: 'Link Disalin!', text: 'Silakan bagikan ke CI.',
                    toast: true, position: 'top-end', showConfirmButton: false, timer: 3000,
                    timerProgressBar: true, background: '#fff', iconColor: '#198754'
                });
            }
        }
    </script>
@endsection