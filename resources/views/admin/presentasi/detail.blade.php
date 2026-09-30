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
{{-- Daftar Nilai Masuk dari CI --}}
<div class="custom-card animate-up">
    <div class="card-header-main">
        <i class="bi bi-person-check-fill fs-5 text-success"></i> Status Penilaian Kolektif
    </div>
    <div class="card-body-custom">
        @if($presentasi->penilaianDetails->count() > 0)
            @php 
                $avgSkor = $presentasi->penilaianDetails->avg('skor_angka');
                $suggestedGrade = \App\Models\Presentasi::getPredikat($avgSkor);
            @endphp

            {{-- Form Finalisasi Admin --}}
   @if($presentasi->status_penilaian == 'pending')
    <form action="{{ route('admin.presentasi.finalisasi', $presentasi->id) }}" method="POST" id="finalisasiForm">
        @csrf
        <div class="bg-light p-3 rounded border mb-3">
            {{-- REFERENSI NILAI KOLEKTIF (ANGKA & PREDIKAT) --}}
            <div class="mb-3 pb-2 border-bottom">
                <small class="text-muted d-block fw-bold text-uppercase" style="font-size: 0.65rem;">Rerata Kolektif Pembimbing (CI)</small>
                @php
                    $avgKolektif = $presentasi->penilaianDetails->count() > 0 ? $presentasi->penilaianDetails->avg('skor_angka') : 0;
                    $hurufKolektif = \App\Models\Presentasi::getPredikat($avgKolektif);
                    $ketKolektif = \App\Models\Presentasi::getKeterangan($hurufKolektif);
                @endphp
                <div class="fw-bold text-primary">
                    {{ round($avgKolektif, 2) }} | {{ $hurufKolektif }} ({{ $ketKolektif }})
                </div>
            </div>

            {{-- INPUT NILAI AKHIR (HANYA HURUF & PREDIKAT DI SERTIFIKAT) --}}
            <label class="info-label text-maroon fw-bold">Skor Akhir (Revisi Admin)</label>
            <input readonly="" type="number" name="skor_final" class="form-control mb-2 fw-bold border-maroon" 
                   value="{{ round($avgKolektif, 2) }}" step="0.01">
            
           <label class="info-label text-maroon fw-bold">Predikat Akhir (A/B/C/D)</label>
            <select name="nilai_final" class="form-select fw-bold border-maroon">
                <option value="A" {{ $hurufKolektif == 'A' ? 'selected' : '' }}>A (Baik Sekali)</option>
                <option value="B" {{ $hurufKolektif == 'B' ? 'selected' : '' }}>B (Baik)</option>
                <option value="C" {{ $hurufKolektif == 'C' ? 'selected' : '' }}>C (Cukup)</option>
                <option value="D" {{ $hurufKolektif == 'D' ? 'selected' : '' }}>D (Kurang)</option>
            </select>
            <small class="text-muted italic">*Nilai Akhir pada sertifikat hanya akan menampilkan Huruf dan Predikat.</small>
        </div>

        <button type="submit" class="btn btn-success w-100 fw-bold">
            <i class="bi bi-check-all me-1"></i> Finalisasi & Kunci Nilai
        </button>
    </form>
@else
    {{-- TAMPILAN JIKA SUDAH DIKUNCI --}}
    <div class="alert alert-success text-center border-0 shadow-sm p-3">
        <div class="small fw-bold text-uppercase ls-1 text-muted mb-1">Hasil Penilaian Final</div>
        
        {{-- Nilai Akhir: Huruf dan Predikat --}}
        <div class="display-6 fw-bold text-maroon mb-0">{{ $presentasi->nilai }}</div>
        <div class="fw-bold text-dark mb-3">({{ \App\Models\Presentasi::getKeterangan($presentasi->nilai) }})</div>
        
        <div class="row g-0 border-top pt-2 mt-2">
            <div class="col-12">
                <small class="text-muted d-block" style="font-size: 0.65rem;">REFERENSI KOLEKTIF CI (ANGKA & PREDIKAT)</small>
                @php
                    $avgK = $presentasi->penilaianDetails->avg('skor_angka') ?? 0;
                    $hurufK = \App\Models\Presentasi::getPredikat($avgK);
                @endphp
                <div class="small fw-bold">
                    {{ round($avgK, 2) }} | {{ $hurufK }} ({{ \App\Models\Presentasi::getKeterangan($hurufK) }})
                </div>
            </div>
        </div>
    </div>
@endif

            <div class="table-responsive mt-3">
                <table class="table table-sm small table-bordered">
                    <thead class="bg-light">
                        <tr><th>Pemberi Nilai</th><th class="text-center">Skor</th></tr>
                    </thead>
                    <tbody>
                        @foreach($presentasi->penilaianDetails as $detail)
                        <tr>
                            <td>{{ $detail->nama_ci }}</td>
                            <td class="text-center">{{ $detail->skor_angka }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-3 text-muted small">Belum ada nilai CI masuk.</div>
        @endif
    </div>
</div>
        </div>

        {{-- KOLOM KANAN: File & Proses --}}
        <div class="col-lg-8 animate-up" style="animation-delay: 0.2s;">
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
            {{-- 1. File Presentasi (PPT) --}}
            <div class="custom-card">
                <div class="card-header-main">
                    <i class="bi bi-file-earmark-slides-fill text-warning fs-5"></i> File Laporan Awal
                </div>
                <div class="card-body-custom">
                    @if ($presentasi->file_ppt)
                        <a href="{{ asset('storage/' . $presentasi->file_ppt) }}" target="_blank" class="file-download-card">
                            <div class="file-icon"><i class="bi bi-file-earmark-word-fill"></i></div>
                            <div class="flex-grow-1">
                                <div class="fw-bold text-dark">Laporan Awal</div>
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
            <i class="bi bi-award-fill text-success fs-5"></i> Hasil Penilaian Akhir
        </div>
        <div class="card-body-custom">
            <div class="row align-items-center g-4">
<div class="col-md-4">
    <div class="score-box shadow-sm border-0">
        {{-- BAGIAN 1: NILAI AKHIR (ADMIN) --}}
        <div class="mb-3">
            <small class="text-muted d-block fw-bold text-uppercase mb-1" style="font-size: 0.7rem;">Nilai Akhir (Disahkan)</small>
            <div class="score-val text-grade-{{ $presentasi->nilai }} mb-0" style="line-height: 1;">
                {{ $presentasi->nilai ?? '-' }}
            </div>
            
      @php
                $statusNilai = [
                    'A' => ['bg' => 'success', 'txt' => 'Baik Sekali'],
                    'B' => ['bg' => 'success', 'txt' => 'Baik'],
                    'C' => ['bg' => 'warning', 'txt' => 'Cukup'],
                    'D' => ['bg' => 'danger', 'txt' => 'Kurang']
                ][$presentasi->nilai] ?? ['bg' => 'secondary', 'txt' => 'Belum Dinilai'];
            @endphp
            <span class="badge bg-{{ $statusNilai['bg'] }} mt-1">{{ $statusNilai['txt'] }}</span>
        </div>

        <hr class="my-3">

        {{-- BAGIAN 2: NILAI KOLEKTIF (RATA-RATA SEMUA CI) --}}
        <div class="bg-light rounded p-2 text-center">
            <small class="text-muted d-block fw-bold text-uppercase mb-1" style="font-size: 0.65rem;">Rerata Kolektif Pembimbing</small>
            
            @php
                $avgKolektif = $presentasi->penilaianDetails->count() > 0 ? $presentasi->penilaianDetails->avg('skor_angka') : 0;
                $hurufKolektif = \App\Models\Presentasi::getPredikat($avgKolektif);
                $ketKolektif = \App\Models\Presentasi::getKeterangan($hurufKolektif);
            @endphp

            <div class="fw-bold text-dark fs-4 mb-0">
                {{ round($avgKolektif, 2) }}
            </div>
            <div class="small fw-bold text-secondary">
                {{ $hurufKolektif }} — {{ $ketKolektif }}
            </div>
        </div>

        <div class="score-lbl mt-3">Predikat & Skor Keseluruhan</div>
    </div>
</div>

                {{-- Detail Catatan Kolektif --}}
                <div class="col-md-8">
                    @if ($presentasi->hasil_penilaian)
                        <h6 class="fw-bold mb-3 text-dark border-bottom pb-2">Catatan Evaluasi CI</h6>
                        <div class="list-group list-group-flush" style="max-height: 250px; overflow-y: auto;">
                            @foreach ($presentasi->hasil_penilaian as $index => $item)
                                <div class="list-group-item px-0 py-2 bg-transparent border-bottom">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <span class="text-dark small fw-bold">{{ $loop->iteration }}. {{ $item['judul'] }}</span>
                                    </div>
                                    <p class="mb-0 text-muted extra-small fst-italic">{{ $item['keterangan'] }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    
                    {{-- Info Range Nilai (Penjelasan kenapa dapat C, dll) --}}
                   {{-- Info Range Nilai (Penjelasan kenapa dapat C, dll) --}}
                    <div class="mt-3 p-2 bg-light rounded border">
                        <div class="extra-small text-muted fw-bold text-uppercase mb-1" style="font-size: 0.65rem;">Referensi Rentang Nilai:</div>
                        <div class="d-flex gap-2 flex-wrap">
                            <span class="extra-small {{ $presentasi->nilai == 'A' ? 'fw-bold text-success' : '' }}" style="font-size: 0.7rem;">A: 80-100</span>
                            <span class="extra-small {{ $presentasi->nilai == 'B' ? 'fw-bold text-success' : '' }}" style="font-size: 0.7rem;">B: 70-79</span>
                            <span class="extra-small {{ $presentasi->nilai == 'C' ? 'fw-bold text-warning' : '' }}" style="font-size: 0.7rem;">C: 60-69</span>
                            <span class="extra-small {{ $presentasi->nilai == 'D' ? 'fw-bold text-danger' : '' }}" style="font-size: 0.7rem;">D: < 60</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-end mt-3 border-top pt-2">
                <small class="text-muted fst-italic">Data difinalisasi pada: {{ $presentasi->dinilai_at->format('d M Y H:i') }}</small>
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
                                <a href="{{ asset('storage/' . $presentasi->file_laporan) }}" target="_blank" class="btn btn-outline-primary btn-sm shadow-sm bg-white">
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
                         @if ($presentasi->status_final == 'selesai')
    {{-- Tombol Surat Selesai Generate Otomatis --}}
    <a href="{{ route('presentasi.download-surat-selesai', [$presentasi->id, urlencode($presentasi->user->name)]) }}" 
       target="_blank" 
       class="btn btn-outline-secondary btn-sm fw-bold">
        <i class="bi bi-file-pdf me-1"></i> Surat Selesai
    </a>

    {{-- Tombol Sertifikat Generate Otomatis --}}
    <a href="{{ route('presentasi.download-sertifikat', [$presentasi->id, urlencode($presentasi->user->name)]) }}" 
       target="_blank" 
       class="btn btn-success btn-sm text-white fw-bold shadow-sm">
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
    const finalisasiForm = document.getElementById('finalisasiForm');
    if(finalisasiForm){
        finalisasiForm.addEventListener('submit', function(e){
            e.preventDefault();
            Swal.fire({
                title: 'Finalisasi Nilai?',
                text: "Nilai akan dikalkulasi dan form input CI akan dikunci selamanya.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#198754',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Finalisasi!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });
    }
</script>
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