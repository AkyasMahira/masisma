@extends('layouts.app')

@section('title', 'Detail Presentasi')
@section('page-title', 'Detail Presentasi')

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
            display: flex; justify-content: space-between; align-items: center;
        }

        /* --- Cards --- */
        .custom-card {
            background: #fff;
            border: none;
            border-radius: var(--card-radius);
            box-shadow: var(--shadow-soft);
            margin-bottom: 1.5rem;
            overflow: hidden;
        }

        .card-header-main {
            background-color: #fff;
            padding: 1.2rem 1.5rem;
            border-bottom: 1px solid #f1f5f9;
            font-weight: 700;
            color: var(--text-dark);
            display: flex; align-items: center; gap: 0.5rem;
        }
        
        .card-header-maroon {
            background: var(--custom-maroon);
            color: white;
            padding: 1rem 1.5rem;
        }

        /* --- Typography --- */
        .label-field {
            font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted);
            font-weight: 600; margin-bottom: 0.2rem; letter-spacing: 0.5px;
        }
        .value-field {
            font-size: 0.95rem; color: var(--text-dark); font-weight: 500; margin-bottom: 1rem;
        }

        /* --- Copy Link Box --- */
        .copy-link-container {
            background: #f8f9fa;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 0.5rem;
            display: flex;
            align-items: center;
        }
        .copy-input {
            border: none; background: transparent; width: 100%;
            font-family: monospace; color: var(--custom-maroon); font-weight: 600;
            outline: none;
        }
        .btn-copy {
            background: white; border: 1px solid #e2e8f0; color: var(--text-dark);
            border-radius: 6px; padding: 0.4rem 0.8rem; transition: var(--transition);
        }
        .btn-copy:hover { background: var(--custom-maroon); color: white; border-color: var(--custom-maroon); }

        /* --- File Cards --- */
        .file-download-card {
            display: flex; align-items: center; padding: 1rem;
            background: #fff; border: 1px solid #e2e8f0; border-radius: 10px;
            text-decoration: none; color: var(--text-dark); transition: var(--transition);
        }
        .file-download-card:hover {
            background: var(--custom-maroon-subtle); border-color: var(--custom-maroon-light); transform: translateY(-2px);
        }
        .file-icon {
            width: 40px; height: 40px; background: #fee2e2; color: #dc2626;
            border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; margin-right: 1rem;
        }

        /* --- Badges & Scores --- */
        .score-display {
            text-align: center; padding: 1.5rem; border-radius: 12px;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            margin-bottom: 1.5rem;
        }
        .score-value { font-size: 3rem; font-weight: 800; line-height: 1; }
        .score-label { font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px; font-weight: 600; }

        .text-grade-A, .text-grade-B { color: #16a34a; }
        .text-grade-C { color: #ca8a04; }
        .text-grade-D { color: #dc2626; }

        /* --- Buttons --- */
        .btn-maroon {
            background-color: var(--custom-maroon); color: #fff; border: none;
            border-radius: 8px; padding: 0.6rem 1.5rem; font-weight: 600; transition: var(--transition);
        }
        .btn-maroon:hover { background-color: var(--custom-maroon-light); color: white; transform: translateY(-2px); }

        .btn-outline-custom {
            border: 1px solid #e2e8f0; color: var(--text-dark); background: white;
            border-radius: 8px; padding: 0.5rem 1.2rem; font-weight: 500; transition: var(--transition);
        }
        .btn-outline-custom:hover { background: #f8f9fa; border-color: #cbd5e1; }

        .animate-up { animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards; opacity: 0; transform: translateY(20px); }
        @keyframes fadeInUp { to { opacity: 1; transform: translateY(0); } }
    </style>

    {{-- Header --}}
    <div class="page-header-wrapper animate-up">
        <div>
            <h4 class="fw-bold mb-1" style="color: var(--custom-maroon);">Detail Presentasi</h4>
            <small class="text-muted">Pantau progres presentasi dan penilaian mahasiswa.</small>
        </div>
        <div>
            <a href="{{ route('admin.presentasi.index') }}" class="btn btn-outline-custom shadow-sm">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row g-4">
        
        {{-- KOLOM KIRI: Info & Link --}}
        <div class="col-lg-4 animate-up" style="animation-delay: 0.1s;">
            
            {{-- Info Mahasiswa --}}
            <div class="custom-card">
                <div class="card-header-maroon">
                    <h5 class="mb-0 fs-6"><i class="bi bi-person-vcard-fill me-2"></i> Informasi Mahasiswa</h5>
                </div>
                <div class="p-4">
                    <div class="label-field">Nama Mahasiswa</div>
                    <div class="value-field fw-bold">{{ $presentasi->user->name }}</div>

                    <div class="label-field">Email</div>
                    <div class="value-field">{{ $presentasi->user->email }}</div>

                    <div class="label-field">Judul Penelitian</div>
                    <div class="value-field">{{ $presentasi->praPenelitian->judul }}</div>
                    
                    <hr class="my-3 border-light">

                    <div class="label-field">Jadwal Presentasi</div>
                    <div class="value-field">
                        <i class="bi bi-calendar-event me-1 text-muted"></i> {{ $presentasi->tanggal_presentasi->format('d F Y') }} <br>
                        <i class="bi bi-clock me-1 text-muted"></i> {{ $presentasi->waktu_mulai }} - {{ $presentasi->waktu_selesai }} WIB
                    </div>

                    <div class="label-field">Tempat</div>
                    <div class="value-field fw-bold text-danger">{{ $presentasi->tempat }}</div>
                </div>
            </div>

            {{-- Link Penilaian CI --}}
            <div class="custom-card">
                <div class="card-header-main">
                    <i class="bi bi-link-45deg fs-5 text-primary"></i> Link Penilaian CI
                </div>
                <div class="p-4">
                    <p class="small text-muted mb-2">Bagikan link ini kepada Pembimbing Lapangan (CI) untuk input nilai.</p>
                    <div class="copy-link-container">
                        <input type="text" class="copy-input" id="linkCI" value="{{ route('ci.penilaian', $presentasi->id) }}" readonly>
                        <button class="btn-copy" onclick="copyLink()" title="Salin Link">
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
                <div class="p-4">
                    @if ($presentasi->file_ppt)
                        <a href="{{ Storage::url($presentasi->file_ppt) }}" target="_blank" class="file-download-card">
                            <div class="file-icon"><i class="bi bi-file-ppt-fill"></i></div>
                            <div class="flex-grow-1">
                                <div class="fw-bold text-dark">Materi Presentasi.pptx</div>
                                <small class="text-muted">Diupload: {{ $presentasi->uploaded_at->format('d M Y H:i') }}</small>
                            </div>
                            <i class="bi bi-download text-secondary"></i>
                        </a>
                    @else
                        <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center m-0">
                            <i class="bi bi-exclamation-circle-fill fs-4 me-3"></i>
                            <div>
                                <strong>Belum Diupload</strong><br>
                                Mahasiswa belum mengupload file materi presentasi.
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
                    <div class="p-4">
                        <div class="row align-items-center">
                            <div class="col-md-4">
                                <div class="score-display">
                                    <div class="score-value text-grade-{{ $presentasi->nilai }}">{{ $presentasi->nilai }}</div>
                                    <div class="score-label text-muted">Predikat Akhir</div>
                                </div>
                            </div>
                            <div class="col-md-8">
                                @if ($presentasi->hasil_penilaian)
                                    <h6 class="fw-bold mb-3">Rincian Penilaian:</h6>
                                    <div class="list-group list-group-flush">
                                        @foreach ($presentasi->hasil_penilaian as $index => $item)
                                            <div class="list-group-item px-0 bg-transparent">
                                                <div class="d-flex justify-content-between">
                                                    <span class="text-muted small">{{ $loop->iteration }}. {{ $item['judul'] }}</span>
                                                    <span class="fw-bold text-dark">{{ $item['nilai'] ?? '-' }}</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="text-end mt-2">
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
                    <div class="p-4">
                        @if ($presentasi->file_laporan)
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <a href="{{ Storage::url($presentasi->file_laporan) }}" target="_blank" class="btn btn-outline-primary btn-sm shadow-sm">
                                    <i class="bi bi-download me-1"></i> Download Laporan
                                </a>
                                <small class="text-muted">Uploaded: {{ $presentasi->laporan_uploaded_at->format('d M Y') }}</small>
                            </div>

                            @if ($presentasi->status_laporan == 'pending')
                                <div class="bg-light p-3 rounded-3 border">
                                    <h6 class="fw-bold mb-3 text-maroon">Review Laporan Mahasiswa</h6>
                                    <form action="{{ route('admin.presentasi.review-laporan', $presentasi->id) }}" method="POST" id="reviewForm">
                                        @csrf
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <label class="form-label small fw-bold">Status</label>
                                                <select name="status" class="form-select" required>
                                                    <option value="">Pilih...</option>
                                                    <option value="approved">Setujui (Selesai)</option>
                                                    <option value="revisi">Perlu Revisi</option>
                                                </select>
                                            </div>
                                            <div class="col-md-8">
                                                <label class="form-label small fw-bold">Catatan / Keterangan</label>
                                                <textarea name="keterangan" rows="1" class="form-control" placeholder="Catatan untuk mahasiswa..." required></textarea>
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
                                            <strong>Status: {{ ucfirst($presentasi->status_laporan) }}</strong>
                                            @if ($presentasi->keterangan_review)
                                                <p class="mb-0 mt-1 small">{{ $presentasi->keterangan_review }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @else
                            <div class="text-center py-3 text-muted">
                                <i class="bi bi-hourglass-split fs-3 mb-2 d-block"></i>
                                Menunggu mahasiswa mengupload Laporan Akhir.
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- 4. Dokumen Final (Selesai) --}}
            @if ($presentasi->status_final == 'selesai')
                <div class="custom-card border-top border-4 border-success">
                    <div class="p-4 d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="fw-bold text-success mb-1"><i class="bi bi-patch-check-fill me-2"></i>Penelitian Telah Selesai</h5>
                            <p class="text-muted small mb-0">Semua proses telah diselesaikan.</p>
                        </div>
                        <div class="d-flex gap-2">
                            @if ($presentasi->surat_selesai)
                                <a href="{{ Storage::url($presentasi->surat_selesai) }}" target="_blank" class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-file-pdf me-1"></i> Surat Selesai
                                </a>
                            @endif
                            @if ($presentasi->sertifikat)
                                <a href="{{ Storage::url($presentasi->sertifikat) }}" target="_blank" class="btn btn-success btn-sm text-white">
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
                            <i class="bi bi-people-fill text-info fs-5"></i> Dokumen Anggota Penelitian
                        </div>
                        <div class="p-4">
                            <p class="small text-muted mb-3">Download sertifikat dan surat selesai untuk setiap anggota:</p>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Anggota</th>
                                            <th>Jenjang</th>
                                            <th>Dokumen</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($presentasi->praPenelitian->anggotas as $index => $anggota)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td class="fw-bold">{{ $anggota->nama }}</td>
                                                <td><span class="badge bg-light text-dark">{{ $anggota->jenjang }}</span></td>
                                                <td>
                                                    <div class="d-flex gap-2">
                                                        <a href="{{ route('presentasi.download-sertifikat', [$presentasi->id, urlencode($anggota->nama)]) }}" class="btn btn-sm btn-outline-success" title="Download Sertifikat">
                                                            <i class="bi bi-award-fill me-1"></i> Sertifikat
                                                        </a>
                                                        <a href="{{ route('presentasi.download-surat-selesai', [$presentasi->id, urlencode($anggota->nama)]) }}" class="btn btn-sm btn-outline-secondary" title="Download Surat Selesai">
                                                            <i class="bi bi-file-pdf me-1"></i> Surat
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
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // SweetAlert untuk Session Flash
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session("success") }}',
                timer: 2500,
                showConfirmButton: false
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: '{{ session("error") }}'
            });
        @endif

        // Konfirmasi Form Review
        const reviewForm = document.getElementById('reviewForm');
        if(reviewForm){
            reviewForm.addEventListener('submit', function(e){
                e.preventDefault();
                Swal.fire({
                    title: 'Kirim Review?',
                    text: "Status laporan akan diperbarui.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#7c1316',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Kirim'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            });
        }

        // Fungsi Copy Link dengan SweetAlert Toast
        function copyLink() {
            const linkInput = document.getElementById('linkCI');
            linkInput.select();
            linkInput.setSelectionRange(0, 99999); // Mobile fix
            
            navigator.clipboard.writeText(linkInput.value).then(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Link Disalin!',
                    text: 'Silakan bagikan ke Pembimbing Lapangan (CI).',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000
                });
            }).catch(err => {
                // Fallback jika HTTPS tidak aktif
                document.execCommand('copy');
                alert('Link berhasil disalin (Manual Fallback)');
            });
        }
    </script>
@endsection