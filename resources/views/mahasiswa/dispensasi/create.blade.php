@extends('layouts.app')

@section('title', 'Ajukan Dispensasi')
@section('page-title', 'Form Pengajuan Dispensasi')

@section('content')
<style>
    :root {
        --custom-maroon: #7c1316;
    }
    .btn-maroon { background-color: var(--custom-maroon); color: white; border: none; }
    .btn-maroon:hover { background-color: #5a0e10; color: white; }
    
    /* Styling Step */
    .card-info { border-radius: 12px; border: none; transition: 0.3s; height: 100%; }
    .step-number {
        width: 30px; height: 30px; background: var(--custom-maroon);
        color: white; border-radius: 50%; display: flex;
        align-items: center; justify-content: center; font-weight: bold;
        margin-bottom: 10px;
    }
    .instruction-box {
        border-left: 4px solid var(--custom-maroon);
        background: #f8f9fa;
        padding: 15px;
        border-radius: 0 8px 8px 0;
    }

    /* Styling TTD Terlambat */
    #signature-pad { border: 1px solid #ccc; border-radius: 8px; width: 100%; height: 200px; background: #fff; cursor: crosshair;}
</style>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />

<div class="row justify-content-center">
    <div class="col-md-12 animate-up">
        
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
            <div class="card-header text-white p-4" style="background: var(--custom-maroon);">
                <h5 class="mb-0 fw-bold" id="form-title"><i class="bi bi-file-earmark-text-fill me-2"></i> Form Pengajuan Dispensasi</h5>
                <p class="mb-0 opacity-75 small" id="form-subtitle">Selesaikan pengisian form di bawah ini.</p>
            </div>
            
            <div class="card-body p-4">
                
                <form action="{{ route('mahasiswa.dispensasi.store') }}" method="POST" enctype="multipart/form-data" id="formDispensasi">
                    @csrf
                    <input type="hidden" name="kategori" id="input-kategori" value="{{ old('kategori') }}">

                    <div id="form-biasa" style="display: none;">
                        
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <div class="card-info border p-3 shadow-sm">
                                    <div class="step-number">1</div>
                                    <h6 class="fw-bold">Unduh & Isi Template</h6>
                                    <p class="small text-muted">Gunakan template resmi untuk Izin Biasa. Wajib diprint (Hard Copy).</p>
                                    <a href="{{ route('mahasiswa.dispensasi.template') }}" class="btn btn-sm btn-outline-dark rounded-pill w-100">
                                        <i class="bi bi-download me-1"></i> Unduh Template
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card-info border p-3 shadow-sm">
                                    <div class="step-number">2</div>
                                    <h6 class="fw-bold">Tanda Tangan Asli</h6>
                                    <p class="small text-muted">Wajib Tanda Tangan Basah (Mahasiswa & Karu). <strong>Dilarang scan TTD atau TTD Palsu</strong>.</p>
                                    <span class="badge bg-danger">Sanksi: Status Alpa Permanen</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card-info border p-3 shadow-sm">
                                    <div class="step-number">3</div>
                                    <h6 class="fw-bold">Scan & Gabung PDF</h6>
                                    <p class="small text-muted">Scan hasil TTD dan gabungkan dengan foto absensi ruangan dalam 1 file PDF.</p>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4 opacity-25">

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Tanggal Mulai</label>
                                <input type="date" name="tanggal_mulai" id="tgl_mulai_biasa" class="form-control" value="{{ old('tanggal_mulai') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Tanggal Selesai</label>
                                <input type="date" name="tanggal_selesai" id="tgl_selesai_biasa" class="form-control" value="{{ old('tanggal_selesai') }}">
                            </div>
                        </div>

                       <div class="mb-3">
    <label class="form-label fw-bold small text-muted">Keterangan / Alasan</label>
    <select name="keterangan" id="ket_biasa" class="form-select" required>
        <option value="" disabled selected>-- Pilih alasan --</option>
        <option value="Sakit" {{ old('keterangan') == 'Sakit' ? 'selected' : '' }}>Sakit</option>
        <option value="Izin" {{ old('keterangan') == 'Izin' ? 'Pilih alasan' : '' }}>Izin</option>
        {{-- Anda bisa menambahkan opsi lain di sini jika diperlukan --}}
    </select>
</div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small text-muted">Upload Berkas Gabungan (PDF - Maks 1MB)</label>
                            <input type="file" name="file_surat" id="file_surat" class="form-control" accept=".pdf">
                            
                            <div class="instruction-box mt-3">
                                <h6 class="fw-bold small mb-2 text-dark"><i class="bi bi-paperclip me-2"></i>DOKUMEN YANG WAJIB ADA DI DALAM PDF:</h6>
                                <ol class="small mb-0 text-muted">
                                    <li><strong>Scan Surat Izin:</strong> Dari template, TTD Basah Anda & Karu.</li>
                                    <li><strong>Foto Absensi Ruangan:</strong> Foto fisik daftar hadir di ruangan.</li>
                                    <li><strong>Foto Surat Sakit:</strong> Foto Surat Sakit TTD Dokter (jika sakit).</li>
                                </ol>
                            </div>
                        </div>
                    </div>

                    <div id="form-terlambat" style="display: none;">
                        <div class="alert alert-danger shadow-sm border-0 mb-4" id="alert-note-terlambat">
                            <h6 class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-2"></i> PERHATIAN PENTING!</h6>
                            <p class="small mb-0" id="note-terlambat-text">Dispensasi terlambat <b>HANYA</b> berlaku untuk absensi hari ini (<b>{{ \Carbon\Carbon::now()->isoFormat('D MMMM YYYY') }}</b>). Sistem mengunci tanggal otomatis.</p>
                        </div>

                        {{-- Tanggal khusus Lupa Pulang (maks 2 hari ke belakang) --}}
                        <div class="row mb-3" id="wrap-tgl-lupa" style="display: none;">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Tanggal Lupa Absen Pulang</label>
                                <input type="date" name="tanggal_mulai" id="tgl_lupa" class="form-control" disabled
                                       min="{{ \Carbon\Carbon::now()->subDays(2)->toDateString() }}"
                                       max="{{ \Carbon\Carbon::now()->toDateString() }}"
                                       value="{{ old('tanggal_mulai') }}">
                                <small class="text-muted">Hanya bisa memilih maksimal 2 hari ke belakang.</small>
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <div class="card-info border p-3 shadow-sm">
                                    <div class="step-number">1</div>
                                    <h6 class="fw-bold">Lokasi & Kronologi</h6>
                                    <p class="small text-muted">Pilih ruangan tempat Anda magang saat ini dan jelaskan secara singkat kronologi keterlambatan Anda.</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card-info border p-3 shadow-sm">
                                    <div class="step-number">2</div>
                                    <h6 class="fw-bold">Identitas Penyetuju</h6>
                                    <p class="small text-muted">Isi nama lengkap dan jabatan (Karu/CI) yang akan memberikan otorisasi keterlambatan Anda hari ini.</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card-info border p-3 shadow-sm">
                                    <div class="step-number">3</div>
                                    <h6 class="fw-bold">Tanda Tangan Layar</h6>
                                    <p class="small text-muted">Minta pihak penyetuju (Karu/CI) untuk langsung menandatangani form secara digital di layar smartphone Anda.</p>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4 opacity-25">

                        <div class="row mb-3 mt-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Program Studi</label>
                                <input type="text" class="form-control bg-light" value="{{ $mahasiswa->prodi }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Asal Kampus</label>
                                <input type="text" class="form-control bg-light" value="{{ $mahasiswa->mou->nama_universitas ?? $mahasiswa->univ_asal }}" readonly>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Pilih Ruangan Saat Ini</label>
                            <select name="ruangan_id" id="ruangan_id" class="form-select">
                                <option value="">Cari dan pilih ruangan...</option>
                                @foreach($ruangans as $ruangan)
                                    <option value="{{ $ruangan->id }}" {{ old('ruangan_id') == $ruangan->id ? 'selected' : '' }}>{{ $ruangan->nm_ruangan }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted" id="label-ket-terlambat">Kronologi / Alasan Terlambat</label>
                            <textarea name="keterangan_terlambat_input" id="ket_terlambat" class="form-control" rows="3" placeholder="Jelaskan alasan detail kenapa Anda terlambat / lupa absen pulang...">{{ old('keterangan_terlambat_input') }}</textarea>
                        </div>

                        <div class="card bg-light border-0 mb-4 p-3 shadow-sm">
                            <h6 class="fw-bold text-maroon mb-3"><i class="bi bi-person-check-fill me-2"></i>Otorisasi Pihak Penyetuju</h6>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted">Ketik Nama Penyetuju (Karu/CI?Katim Jaga)</label>
                                    <input type="text" name="nama_penyetuju" class="form-control" placeholder="Contoh: Ns. Budi Santoso, S.Kep" value="{{ old('nama_penyetuju') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted">Ketik Jabatan Penyetuju</label>
                                    <input type="text" name="jabatan_penyetuju" class="form-control" placeholder="Contoh: Kepala Ruangan Mawar" value="{{ old('jabatan_penyetuju') }}">
                                </div>
                            </div>
                            
                            <button type="button" class="btn btn-outline-dark fw-bold w-100 mb-2" data-bs-toggle="modal" data-bs-target="#signatureModal">
                                <i class="bi bi-pen me-2"></i> Buka Canvas Tanda Tangan
                            </button>
                            
                            <input type="hidden" name="ttd_penyetuju" id="ttd_penyetuju" value="{{ old('ttd_penyetuju') }}">
                            
                            <div id="ttd-preview-container" class="text-center mt-3" style="display:none;">
                                <span class="badge bg-success mb-2"><i class="bi bi-check-circle me-1"></i> Tanda Tangan Terekam</span><br>
                                <img id="ttd-preview" src="" style="max-height: 120px; border: 1px solid #ccc; border-radius: 5px; background: white; padding: 5px;">
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2 mt-2" id="btn-submit-container" style="display:none;">
                        <button type="submit" class="btn btn-maroon py-3 rounded-pill fw-bold shadow">
                            <i class="bi bi-send-check-fill me-2"></i> KIRIM PENGAJUAN
                        </button>
                        <a href="{{ route('mahasiswa.dispensasi.index') }}" class="btn btn-link text-muted text-decoration-none small">Kembali ke Riwayat</a>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="signatureModal" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title fw-bold"><i class="bi bi-shield-lock me-2"></i>Otorisasi Tanda Tangan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body bg-light">
                <div class="alert alert-warning small border-warning">
                    <strong>TANGGUNG JAWAB PENYETUJU:</strong><br>
                    Dengan membubuhkan tanda tangan, Anda menyatakan telah memvalidasi alasan dan memberikan izin masuk.
                </div>
                <p class="text-center fw-bold text-dark mb-2">Tanda Tangan Di Bawah Ini:</p>
                <canvas id="signature-pad" class="shadow-sm"></canvas>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary btn-sm fw-bold" id="btn-clear-sig"><i class="bi bi-eraser me-1"></i>Ulangi</button>
                <button type="button" class="btn btn-success btn-sm fw-bold" id="btn-save-sig"><i class="bi bi-check-lg me-1"></i>Simpan</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // NOTIFIKASI ERROR LARAVEL (Ini yang menyelesaikan masalah blank page saat gagal upload)
    @if ($errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Pengajuan Gagal',
            html: `
                <div class="text-start">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            `,
            confirmButtonColor: '#7c1316'
        });
    @endif

    document.addEventListener('DOMContentLoaded', function() {
        
        // Pengecekan jika terjadi error sebelumnya, langsung tampilkan form terakhir yang dipilih
        let oldKategori = document.getElementById('input-kategori').value;
        if (oldKategori) {
            setupForm(oldKategori);
            
            // Restore TTD image if it exists from previous failed request
            let oldTtd = document.getElementById('ttd_penyetuju').value;
            if(oldTtd) {
                document.getElementById('ttd-preview').src = oldTtd;
                document.getElementById('ttd-preview-container').style.display = 'block';
            }
        } else {
            // Tampilkan Pop-up Pemilihan saat Halaman Dimuat pertama kali
            Swal.fire({
                title: 'Pilih Jenis Dispensasi',
                showConfirmButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                width: 440,
                customClass: { popup: 'dsp-popup' },
                html: `
                    <style>
                        .dsp-popup{border-radius:20px !important}
                        .dsp-popup .swal2-title{font-size:1.15rem;color:#7c1316;padding-top:4px}
                        .dsp-sub{font-size:.82rem;color:#8a8a8a;margin:-4px 0 14px}
                        .dsp-list{display:flex;flex-direction:column;gap:10px;text-align:left}
                        .dsp-opt{display:flex;align-items:center;gap:12px;width:100%;border:1.5px solid #eee;background:#fff;
                                 border-radius:14px;padding:12px 14px;cursor:pointer;transition:.15s;font-family:inherit}
                        .dsp-opt:hover{border-color:var(--c);box-shadow:0 8px 20px rgba(0,0,0,.09);transform:translateY(-1px)}
                        .dsp-opt .ic{flex:0 0 44px;height:44px;border-radius:12px;display:flex;align-items:center;
                                     justify-content:center;background:var(--cb);color:var(--c);font-size:1.3rem}
                        .dsp-opt .tx{display:flex;flex-direction:column;line-height:1.25;flex:1;min-width:0}
                        .dsp-opt .tx b{color:#222;font-size:.95rem}
                        .dsp-opt .tx small{color:#8a8a8a;font-size:.76rem;margin-top:2px}
                        .dsp-opt .chev{color:#d0d0d0;font-size:1rem}
                        .dsp-back{margin-top:16px;background:none;border:none;color:#9a9a9a;font-size:.85rem;cursor:pointer}
                        .dsp-back:hover{color:#7c1316;text-decoration:underline}
                    </style>
                    <p class="dsp-sub">Pilih jenis pengajuan sesuai kondisi Anda saat ini.</p>
                    <div class="dsp-list">
                        <button type="button" class="dsp-opt" style="--c:#dc3545;--cb:#fdecee" onclick="window.__pilihDispen('terlambat')">
                            <span class="ic"><i class="bi bi-clock-history"></i></span>
                            <span class="tx"><b>Dispensasi Terlambat</b><small>Telat absen masuk — berlaku hari ini</small></span>
                            <i class="bi bi-chevron-right chev"></i>
                        </button>
                        <button type="button" class="dsp-opt" style="--c:#f59e0b;--cb:#fef3e2" onclick="window.__pilihDispen('lupa_pulang')">
                            <span class="ic"><i class="bi bi-box-arrow-right"></i></span>
                            <span class="tx"><b>Dispensasi Lupa Pulang</b><small>Lupa tap pulang — maksimal 2 hari lalu</small></span>
                            <i class="bi bi-chevron-right chev"></i>
                        </button>
                        <button type="button" class="dsp-opt" style="--c:#0d6efd;--cb:#e7f0ff" onclick="window.__pilihDispen('biasa')">
                            <span class="ic"><i class="bi bi-calendar2-check"></i></span>
                            <span class="tx"><b>Izin Biasa / Sakit</b><small>Pakai surat — hard copy &amp; upload PDF</small></span>
                            <i class="bi bi-chevron-right chev"></i>
                        </button>
                    </div>
                    <button type="button" class="dsp-back" onclick="window.location.href='{{ route('mahasiswa.dispensasi.index') }}'">&larr; Kembali ke Riwayat</button>
                `
            });
            window.__pilihDispen = function (k) { Swal.close(); setupForm(k); };
        }

        // Inisialisasi Choices JS
        new Choices('#ruangan_id', {
            searchEnabled: true,
            itemSelectText: '',
            shouldSort: false
        });
    });

    function setupForm(kategori) {
        document.getElementById('input-kategori').value = kategori;
        document.getElementById('btn-submit-container').style.display = 'block';

        const reqElements = document.querySelectorAll('#formDispensasi [required]');
        reqElements.forEach(el => el.removeAttribute('required'));

        const tglMulaiBiasa = document.getElementById('tgl_mulai_biasa');
        const tglSelesaiBiasa = document.getElementById('tgl_selesai_biasa');
        const fileSurat = document.getElementById('file_surat');
        const tglLupa = document.getElementById('tgl_lupa');
        const wrapTglLupa = document.getElementById('wrap-tgl-lupa');
        const noteEl = document.getElementById('note-terlambat-text');
        const alertEl = document.getElementById('alert-note-terlambat');
        const ketLabel = document.getElementById('label-ket-terlambat');

        if (kategori === 'biasa') {
            document.getElementById('form-title').innerHTML = '<i class="bi bi-calendar2-check me-2"></i> Pengajuan Izin Biasa / Sakit';
            document.getElementById('form-subtitle').innerText = 'Ikuti prosedur hard-copy dan upload PDF.';

            document.getElementById('form-biasa').style.display = 'block';
            document.getElementById('form-terlambat').style.display = 'none';

            // Aktifkan input biasa, matikan input lupa pulang (agar tidak ikut terkirim)
            tglMulaiBiasa.disabled = false; tglSelesaiBiasa.disabled = false; fileSurat.disabled = false;
            tglLupa.disabled = true; tglLupa.removeAttribute('required');

            tglMulaiBiasa.setAttribute('required', 'true');
            tglSelesaiBiasa.setAttribute('required', 'true');
            document.getElementById('ket_biasa').setAttribute('required', 'true');
            fileSurat.setAttribute('required', 'true');

            // Copy value textarea (workaround for Laravel validation switching)
            document.getElementById('ket_biasa').name = 'keterangan';
            document.getElementById('ket_terlambat').name = 'keterangan_terlambat_input';

        } else if (kategori === 'terlambat' || kategori === 'lupa_pulang') {
            document.getElementById('form-biasa').style.display = 'none';
            document.getElementById('form-terlambat').style.display = 'block';

            // Matikan input biasa (hidden tapi masih punya name tanggal_mulai -> cegah bentrok)
            tglMulaiBiasa.disabled = true; tglSelesaiBiasa.disabled = true; fileSurat.disabled = true;
            tglMulaiBiasa.removeAttribute('required'); tglSelesaiBiasa.removeAttribute('required');

            document.getElementById('ruangan_id').setAttribute('required', 'true');
            document.getElementById('ket_terlambat').setAttribute('required', 'true');
            document.querySelector('input[name="nama_penyetuju"]').setAttribute('required', 'true');
            document.querySelector('input[name="jabatan_penyetuju"]').setAttribute('required', 'true');

            // Switch name attribute so backend receives the correct "keterangan"
            document.getElementById('ket_terlambat').name = 'keterangan';
            document.getElementById('ket_biasa').name = 'keterangan_biasa_input';

            if (kategori === 'terlambat') {
                document.getElementById('form-title').innerHTML = '<i class="bi bi-clock-history me-2"></i> Pengajuan Dispensasi Terlambat';
                document.getElementById('form-subtitle').innerText = 'Isi data dan minta validasi langsung kepada penyetuju di ruangan.';
                wrapTglLupa.style.display = 'none';
                tglLupa.disabled = true; tglLupa.removeAttribute('required');
                alertEl.className = 'alert alert-danger shadow-sm border-0 mb-4';
                noteEl.innerHTML = 'Dispensasi terlambat <b>HANYA</b> berlaku untuk absensi hari ini. Sistem mengunci tanggal otomatis.';
                if (ketLabel) ketLabel.innerText = 'Kronologi / Alasan Terlambat';
            } else {
                document.getElementById('form-title').innerHTML = '<i class="bi bi-box-arrow-right me-2"></i> Pengajuan Dispensasi Lupa Pulang';
                document.getElementById('form-subtitle').innerText = 'Untuk hari Anda lupa tap pulang (maks 2 hari lalu). Perlu validasi Karu/CI.';
                wrapTglLupa.style.display = 'flex';
                tglLupa.disabled = false; tglLupa.setAttribute('required', 'true');
                alertEl.className = 'alert alert-warning shadow-sm border-0 mb-4';
                noteEl.innerHTML = 'Lupa absen pulang dihitung <b>90%</b> bila disetujui. Pilih tanggal saat Anda lupa checkout (maksimal 2 hari ke belakang).';
                if (ketLabel) ketLabel.innerText = 'Kronologi / Alasan Lupa Absen Pulang';
            }
        }
    }

    // --- SETUP SIGNATURE PAD ---
    const canvas = document.getElementById('signature-pad');
    const signaturePad = new SignaturePad(canvas, {
        backgroundColor: 'rgb(255, 255, 255)',
        penColor: 'rgb(0, 0, 0)'
    });

    function resizeCanvas() {
        const ratio =  Math.max(window.devicePixelRatio || 1, 1);
        canvas.width = canvas.offsetWidth * ratio;
        canvas.height = canvas.offsetHeight * ratio;
        canvas.getContext("2d").scale(ratio, ratio);
        signaturePad.clear(); 
    }
    
    const sigModal = document.getElementById('signatureModal');
    sigModal.addEventListener('shown.bs.modal', function () {
        resizeCanvas();
    });

    document.getElementById('btn-clear-sig').addEventListener('click', function() {
        signaturePad.clear();
    });

    document.getElementById('btn-save-sig').addEventListener('click', function() {
        if (signaturePad.isEmpty()) {
            Swal.fire({icon: 'error', title: 'TTD Kosong', text: 'Tanda tangan tidak boleh kosong.'});
            return;
        }
        const dataURL = signaturePad.toDataURL();
        document.getElementById('ttd_penyetuju').value = dataURL;
        document.getElementById('ttd-preview').src = dataURL;
        document.getElementById('ttd-preview-container').style.display = 'block';
        const modal = bootstrap.Modal.getInstance(sigModal);
        modal.hide();
    });

    // Handle Form Submit (Validasi Javascript)
    document.getElementById('formDispensasi').addEventListener('submit', function(e) {
        const kategori = document.getElementById('input-kategori').value;
        
        if(kategori === 'biasa') {
            const fileInput = document.getElementById('file_surat');
            if (fileInput.files.length > 0) {
                const fileSize = fileInput.files[0].size / 1024 / 1024; // MB
                if (fileSize > 1) {
                    Swal.fire({ icon: 'error', title: 'File Terlalu Besar', text: 'Ukuran file PDF maksimal adalah 1 MB!' });
                    e.preventDefault();
                    return false;
                }
                
                if (fileInput.files[0].type !== 'application/pdf') {
                    Swal.fire({ icon: 'error', title: 'Format Salah', text: 'File harus berformat PDF!' });
                    e.preventDefault();
                    return false;
                }
            }
        }

        if(kategori === 'terlambat' || kategori === 'lupa_pulang') {
            if(document.getElementById('ttd_penyetuju').value === "") {
                e.preventDefault();
                Swal.fire({icon: 'warning', title: 'Validasi Belum Lengkap', text: 'Pihak penyetuju wajib membubuhkan tanda tangan terlebih dahulu.'});
                return false;
            }
        }
    });
</script>
@endsection