<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penilaian Presentasi - {{ $presentasi->user->name }}</title>

    {{-- Fonts & Icons --}}
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

        /* --- Navbar --- */
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

        /* --- Cards --- */
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

        /* --- Info Typography --- */
        .info-label {
            font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted);
            font-weight: 600; margin-bottom: 0.2rem; letter-spacing: 0.5px;
        }
        .info-value {
            font-size: 0.95rem; color: var(--text-dark); font-weight: 600; margin-bottom: 1rem;
        }

        /* --- File Download --- */
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

        /* --- Grade Selection (Radio Cards) --- */
        .grade-selector { display: none; }

        .grade-card {
            display: block;
            cursor: pointer;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 1.2rem;
            text-align: center;
            transition: var(--transition);
            background: #fff;
            position: relative;
            overflow: hidden;
        }

        .grade-title { font-size: 2rem; font-weight: 800; line-height: 1; margin-bottom: 0.25rem; }
        .grade-desc { font-size: 0.75rem; font-weight: 600; text-transform: uppercase; }

        /* Colors per Grade */
        .grade-A .grade-title { color: #16a34a; }
        .grade-B .grade-title { color: #16a34a; }
        .grade-C .grade-title { color: #ca8a04; }
        .grade-D .grade-title { color: #dc2626; }

        /* Active States */
        .grade-selector:checked + .grade-card { border-width: 2px; transform: translateY(-3px); box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); }

        .grade-selector[value="A"]:checked + .grade-card { border-color: #16a34a; background-color: #f0fdf4; }
        .grade-selector[value="B"]:checked + .grade-card { border-color: #16a34a; background-color: #f0fdf4; }
        .grade-selector[value="C"]:checked + .grade-card { border-color: #ca8a04; background-color: #fefce8; }
        .grade-selector[value="D"]:checked + .grade-card { border-color: #dc2626; background-color: #fef2f2; }

        /* --- Assessment Items --- */
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

        /* --- Buttons --- */
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

    {{-- Navbar Sederhana --}}
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

            {{-- KOLOM KIRI: Detail Mahasiswa --}}
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

                {{-- Card File PPT --}}
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

            {{-- KOLOM KANAN: Form Penilaian --}}
            <div class="col-lg-8">
                @if ($presentasi->status_penilaian == 'pending')
                    <form action="{{ route('ci.submit-penilaian', $presentasi->id) }}" method="POST" id="formPenilaian">
                        @csrf

                        {{-- Card 1: Nilai Akhir --}}
                        <div class="custom-card">
                            <div class="card-header-custom">
                                <i class="bi bi-trophy fs-5 text-warning"></i> Keputusan Penilaian
                            </div>
                            <div class="card-body-custom">
                                <p class="text-muted small mb-3">Silakan pilih hasil akhir presentasi berdasarkan performa mahasiswa.</p>

                                <div class="row g-3">
                                    <div class="col-6 col-md-3">
                                        <input type="radio" class="grade-selector" name="nilai" id="nilaiA" value="A" required>
                                        <label class="grade-card grade-A" for="nilaiA">
                                            <div class="grade-title">A</div>
                                            <div class="grade-desc text-success">Sangat Baik</div>
                                        </label>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <input type="radio" class="grade-selector" name="nilai" id="nilaiB" value="B">
                                        <label class="grade-card grade-B" for="nilaiB">
                                            <div class="grade-title">B</div>
                                            <div class="grade-desc text-success">Baik</div>
                                        </label>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <input type="radio" class="grade-selector" name="nilai" id="nilaiC" value="C">
                                        <label class="grade-card grade-C" for="nilaiC">
                                            <div class="grade-title">C</div>
                                            <div class="grade-desc text-warning">Cukup / Revisi</div>
                                        </label>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <input type="radio" class="grade-selector" name="nilai" id="nilaiD" value="D">
                                        <label class="grade-card grade-D" for="nilaiD">
                                            <div class="grade-title">D</div>
                                            <div class="grade-desc text-danger">Kurang / Tolak</div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Card 2: Detail Penilaian --}}
                        <div class="custom-card">
                            <div class="card-header-custom">
                                <i class="bi bi-list-check fs-5 text-info"></i> Catatan & Feedback
                            </div>
                            <div class="card-body-custom">
                                <div id="penilaianContainer">
                                    {{-- Item Pertama (Wajib) --}}
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
                                <div class="d-flex align-items-start gap-3">
                                    <i class="bi bi-info-circle-fill text-muted fs-5 mt-1"></i>
                                    <small class="text-muted">
                                        Pastikan data yang diinput sudah benar. Penilaian yang sudah dikirim <strong>tidak dapat diubah kembali</strong>.
                                    </small>
                                </div>
                                <hr class="my-3 border-light">
                                <button type="submit" class="btn btn-maroon btn-lg">
                                    <i class="bi bi-send-fill me-2"></i> Kirim Penilaian
                                </button>
                            </div>
                        </div>

                    </form>
                @else
                    {{-- Tampilan Jika Sudah Dinilai --}}
                    <div class="custom-card text-center py-5">
                        <div class="mb-3">
                            <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                        </div>
                        <h2 class="fw-bold text-dark mb-2">Terima Kasih!</h2>
                        <p class="text-muted">Anda telah menyelesaikan penilaian untuk mahasiswa ini.</p>

                        <div class="d-inline-block bg-light px-4 py-2 rounded-3 border mt-3">
                            <span class="text-muted small text-uppercase fw-bold d-block">Nilai Yang Diberikan</span>
                            <span class="fs-1 fw-bold {{ $presentasi->nilai == 'C' ? 'text-warning' : ($presentasi->nilai == 'D' ? 'text-danger' : 'text-success') }}">
                                {{ $presentasi->nilai }}
                            </span>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
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
                    <input type="text" name="penilaian[${penilaianCount}][judul]" class="form-control" placeholder="Contoh: Kemampuan Presentasi" required>
                </div>
                <div>
                    <label class="form-label small text-muted fw-bold">Komentar / Saran</label>
                    <textarea name="penilaian[${penilaianCount}][keterangan]" rows="2" class="form-control" placeholder="Berikan masukan..." required></textarea>
                </div>
            `;
            container.appendChild(div);
            penilaianCount++;
        }

        // SweetAlert Confirmation
        document.getElementById('formPenilaian')?.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Kirim Penilaian?',
                text: "Data yang dikirim tidak dapat diubah lagi.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#7c1316',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Kirim!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });

        // Alert Success/Error jika ada flash session
        @if(session('success'))
            Swal.fire('Berhasil', '{{ session("success") }}', 'success');
        @endif
        @if(session('error'))
            Swal.fire('Gagal', '{{ session("error") }}', 'error');
        @endif
    </script>
</body>
</html>
