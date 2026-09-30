<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penilaian Presentasi - {{ $presentasi->user->name }}</title>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        :root {
            --maroon: #7c1316;
            --maroon-light: #a3191d;
            --maroon-subtle: #fcf0f1;
            --text-dark: #1e293b;
            --text-muted: #64748b;
            --card-radius: 16px;
            --transition: 0.3s ease;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            color: var(--text-dark);
            padding-bottom: 40px;
        }

        .navbar-custom {
            background: #fff;
            box-shadow: 0 2px 15px rgba(0,0,0,0.04);
            padding: 1rem 0;
            margin-bottom: 2rem;
        }
        .navbar-brand {
            font-weight: 700;
            color: var(--maroon);
            font-size: 1.25rem;
            display: flex; align-items: center; gap: 10px;
        }

        .custom-card {
            background: #fff;
            border: none;
            border-radius: var(--card-radius);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            margin-bottom: 1.5rem;
            overflow: hidden;
        }

        .card-header-custom {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #f1f5f9;
            font-weight: 700;
            display: flex; align-items: center; gap: 0.5rem;
            background: #fff;
            color: var(--text-dark);
        }

        .card-body-custom { padding: 1.5rem; }

        .info-label {
            font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted);
            font-weight: 600; margin-bottom: 0.2rem; letter-spacing: 0.5px;
        }
        .info-value {
            font-size: 0.95rem; color: var(--text-dark); font-weight: 600; margin-bottom: 1rem;
        }

        .file-card {
            display: flex; align-items: center; padding: 1rem;
            background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px;
            text-decoration: none; color: var(--text-dark); transition: var(--transition);
        }
        .file-card:hover {
            background: #fff; border-color: var(--maroon); box-shadow: 0 4px 12px rgba(124, 19, 22, 0.08); transform: translateY(-2px);
        }
        .file-icon {
            width: 42px; height: 42px; background: #fee2e2; color: #dc2626;
            border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; margin-right: 1rem;
        }

        .assessment-item {
            background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.25rem;
            position: relative; margin-bottom: 1rem; animation: fadeIn 0.3s ease-out;
        }

        .btn-remove-item {
            position: absolute; top: 10px; right: 10px;
            width: 30px; height: 30px; border-radius: 50%; background: #fee2e2; color: #dc2626;
            border: none; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: 0.2s;
        }
        .btn-remove-item:hover { background: #dc2626; color: white; }

        .form-control {
            border-radius: 8px; padding: 0.6rem 1rem; border-color: #cbd5e1;
        }
        .form-control:focus {
            border-color: var(--maroon); box-shadow: 0 0 0 3px rgba(124, 19, 22, 0.1);
        }

        .btn-maroon {
            background-color: var(--maroon); color: white; border: none;
            border-radius: 50px; padding: 0.8rem 2rem; font-weight: 700; width: 100%;
            transition: var(--transition); font-size: 1rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .btn-maroon:hover { background-color: var(--maroon-light); transform: translateY(-2px); }

        .btn-outline-dashed {
            border: 2px dashed #cbd5e1; background: transparent; color: var(--text-muted);
            border-radius: 12px; width: 100%; padding: 0.8rem; font-weight: 600; transition: 0.2s;
        }
        .btn-outline-dashed:hover { border-color: var(--maroon); color: var(--maroon); background: var(--maroon-subtle); }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>

<body>
    <nav class="navbar navbar-custom sticky-top">
        <div class="container">
            <span class="navbar-brand">
                <img src="{{ asset('icon.png') }}" alt="Logo" width="35" height="35" class="rounded">
                E-Presentasi
            </span>
        </div>
    </nav>

    <div class="container">
        <div class="row g-4">
            {{-- KOLOM KIRI --}}
            <div class="col-lg-4">
                <div class="custom-card">
                    <div class="card-header-custom">
                        <i class="bi bi-person-vcard fs-5 text-primary"></i> Informasi Mahasiswa
                    </div>
                    <div class="card-body-custom">
                        <div class="info-label">Nama Lengkap</div>
                        <div class="info-value">{{ $presentasi->user->name }}</div>

                        <div class="info-label">Universitas & Prodi</div>
                        <div class="info-value">
                            {{ $praPenelitian->mou ? ($praPenelitian->mou->nama_instansi ?? $praPenelitian->mou->nama_universitas) : '-' }} <br>
                            <span class="fw-normal text-muted">{{ $praPenelitian->prodi }}</span>
                        </div>

                        <div class="info-label">Judul Penelitian</div>
                        <div class="info-value">{{ $presentasi->praPenelitian->judul }}</div>

                        <hr class="my-3 border-light">

                        <div class="info-label">Tim Peneliti</div>
                        @if ($praPenelitian->anggotas->count() > 0)
                            <ul class="list-unstyled mb-0">
                                @foreach ($praPenelitian->anggotas as $anggota)
                                    <li class="d-flex justify-content-between mb-1">
                                        <span class="text-dark small fw-semibold">{{ $anggota->nama }}</span>
                                        <span class="text-muted small badge bg-light text-dark border">{{ $anggota->jenjang }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                           <div class="text-muted small fst-italic">- Tidak ada anggota tambahan -</div>
                        @endif
                    </div>
                </div>

                <div class="custom-card">
                    <div class="card-header-custom">
                        <i class="bi bi-file-earmark-slides fs-5 text-danger"></i> Materi Presentasi
                    </div>
                    <div class="card-body-custom">
                        @if($presentasi->file_ppt)
                            <a href="{{ Storage::url($presentasi->file_ppt) }}" target="_blank" class="file-card">
                                <div class="file-icon"><i class="bi bi-file-ppt-fill"></i></div>
                                <div class="flex-grow-1 overflow-hidden">
                                    <div class="fw-bold text-dark text-truncate">File Presentasi</div>
                                    <div class="small text-muted">Klik untuk download</div>
                                </div>
                                <i class="bi bi-download text-secondary"></i>
                            </a>
                        @else
                            <div class="alert alert-warning d-flex align-items-center m-0 border-0">
                                <i class="bi bi-exclamation-circle me-2"></i>
                                <div class="small">Mahasiswa belum mengupload file.</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN --}}
            <div class="col-lg-8" id="mainAssessmentContainer">
                @if ($presentasi->status_penilaian == 'pending')
                    <form action="{{ route('ci.submit-penilaian', $presentasi->id) }}" method="POST" id="formPenilaian">
                        @csrf
                        <div class="custom-card">
                            <div class="card-header-custom">
                                <i class="bi bi-trophy fs-5 text-warning"></i> Keputusan Penilaian
                            </div>
                            <div class="card-body-custom">
                                <div class="mb-4">
                                    <label class="info-label">Nama Pemberi Nilai (CI)</label>
                                    <input type="text" name="nama_ci" class="form-control" placeholder="Masukkan Nama Anda" required>
                                </div>

                                <div class="info-label">Input Skor Angka (0-100)</div>
                                <div class="mt-2">
                                    <input type="number" name="skor_angka" class="form-control form-control-lg text-center" 
                                           style="font-size: 2rem; font-weight: bold;" placeholder="0" min="0" max="100" required>
                                    <div class="text-center mt-3 d-flex justify-content-center gap-2 flex-wrap">
                                        <span class="badge bg-info">86-100: A</span>
                                        <span class="badge bg-success">71-85: B</span>
                                        <span class="badge bg-warning text-dark">50-70: C</span>
                                        <span class="badge bg-danger">0-49: D</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="custom-card">
                            <div class="card-header-custom">
                                <i class="bi bi-list-check fs-5 text-info"></i> Catatan & Feedback
                            </div>
                            <div class="card-body-custom">
                                <div id="penilaianContainer">
                                    <div class="assessment-item">
                                        <div class="mb-3">
                                            <label class="form-label small text-muted fw-bold">Aspek Penilaian / Judul</label>
                                            <input type="text" name="penilaian[0][judul]" class="form-control" placeholder="Contoh: Penguasaan Materi" required>
                                        </div>
                                        <div>
                                            <label class="form-label small text-muted fw-bold">Komentar / Saran</label>
                                            <textarea name="penilaian[0][keterangan]" rows="2" class="form-control" placeholder="Berikan masukan..." required></textarea>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="btn-outline-dashed mt-2" onclick="tambahPenilaian()">
                                    <i class="bi bi-plus-circle me-1"></i> Tambah Poin Penilaian
                                </button>
                            </div>
                        </div>

                        <div class="custom-card">
                            <div class="card-body-custom bg-light">
                                <div class="d-flex align-items-start gap-3 mb-3">
                                    <i class="bi bi-info-circle-fill text-muted fs-5 mt-1"></i>
                                    <small class="text-muted">
                                        Pastikan data yang diinput sudah benar. Penilaian ini akan dikalkulasi secara kolektif oleh sistem.
                                    </small>
                                </div>
                                <button type="submit" class="btn btn-maroon btn-lg">
                                    <i class="bi bi-send-fill me-2"></i> Kirim Penilaian
                                </button>
                            </div>
                        </div>
                    </form>
                @else
                    <div class="custom-card text-center py-5">
                        <div class="mb-3">
                            <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                        </div>
                        <h2 class="fw-bold text-dark mb-2">Penilaian Selesai</h2>
                        <p class="text-muted">Admin telah melakukan finalisasi penilaian untuk mahasiswa ini.</p>
                        <div class="d-inline-block bg-light px-4 py-2 rounded-3 border mt-3">
                            <span class="text-muted small text-uppercase fw-bold d-block">Hasil Akhir</span>
                            <span class="fs-1 fw-bold text-maroon">{{ $presentasi->nilai }}</span>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // --- 1. HANDLING LOCAL STORAGE & UI STATE ---
        document.addEventListener("DOMContentLoaded", function() {
            const hasSubmitted = localStorage.getItem('submitted_penilaian_{{ $presentasi->id }}');
            if (hasSubmitted && "{{ $presentasi->status_penilaian }}" === 'pending') {
                const data = JSON.parse(hasSubmitted);
                showSuccessState(data.nama, data.nilai);
            }
        });

        function showSuccessState(nama, nilai) {
            const container = document.getElementById('mainAssessmentContainer');
            container.innerHTML = `
                <div class="custom-card text-center py-5">
                    <div class="mb-3">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                    </div>
                    <h2 class="fw-bold text-dark mb-2">Terima Kasih, ${nama}!</h2>
                    <p class="text-muted">Anda telah mengirimkan nilai untuk mahasiswa ini.</p>
                    <div class="d-inline-block bg-light px-4 py-2 rounded-3 border mt-3">
                        <span class="text-muted small text-uppercase fw-bold d-block">Skor Yang Anda Berikan</span>
                        <span class="fs-1 fw-bold text-maroon">${nilai}</span>
                    </div>
                    <div class="mt-4">
                        <small class="text-muted italic">Menunggu finalisasi kalkulasi dari Admin.</small>
                    </div>
                </div>
            `;
        }

        // --- 2. HANDLING DYNAMIC FORM ---
        let penilaianCount = 1;
        function tambahPenilaian() {
            const container = document.getElementById('penilaianContainer');
            const div = document.createElement('div');
            div.className = 'assessment-item';
            div.innerHTML = `
                <button type="button" class="btn-remove-item" onclick="this.parentElement.remove()">
                    <i class="bi bi-x-lg"></i>
                </button>
                <div class="mb-3">
                    <label class="form-label small text-muted fw-bold">Aspek Penilaian / Judul</label>
                    <input type="text" name="penilaian[${penilaianCount}][judul]" class="form-control" placeholder="Contoh: Kemampuan Komunikasi" required>
                </div>
                <div>
                    <label class="form-label small text-muted fw-bold">Komentar / Saran</label>
                    <textarea name="penilaian[${penilaianCount}][keterangan]" rows="2" class="form-control" placeholder="Berikan masukan..." required></textarea>
                </div>
            `;
            container.appendChild(div);
            penilaianCount++;
        }

        // --- 3. FORM SUBMISSION WITH SWEETALERT ---
        document.getElementById('formPenilaian')?.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const namaCi = this.nama_ci.value;
            const skor = this.skor_angka.value;

            Swal.fire({
                title: 'Kirim Penilaian?',
                text: `Anda akan memberikan skor ${skor} untuk mahasiswa ini.`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#7c1316',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Kirim Sekarang!',
                cancelButtonText: 'Cek Kembali'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Simpan identitas ke LocalStorage sebelum submit
                    const storageData = {
                        nama: namaCi,
                        nilai: skor,
                        date: new Date().toISOString()
                    };
                    localStorage.setItem('submitted_penilaian_{{ $presentasi->id }}', JSON.stringify(storageData));
                    
                    this.submit();
                }
            });
        });

        // --- 4. FLASH SESSION ALERTS ---
        @if(session('success'))
            Swal.fire({ icon: 'success', title: 'Berhasil', text: '{{ session("success") }}', confirmButtonColor: '#7c1316' });
        @endif
        @if(session('error'))
            Swal.fire({ icon: 'error', title: 'Gagal', text: '{{ session("error") }}', confirmButtonColor: '#7c1316' });
        @endif
    </script>
</body>
</html>