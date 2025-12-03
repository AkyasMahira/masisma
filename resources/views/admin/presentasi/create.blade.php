@extends('layouts.app')

@section('title', 'Set Jadwal Presentasi')
@section('page-title', 'Set Jadwal Presentasi')

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

        .card-header-info {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 1.2rem 1.5rem;
            border-bottom: 1px solid #dee2e6;
            color: var(--text-dark);
            font-weight: 700;
            display: flex; align-items: center; gap: 10px;
        }

        .card-header-form {
            background: var(--custom-maroon);
            color: white;
            padding: 1.2rem 1.5rem;
            font-weight: 700;
            display: flex; align-items: center; gap: 10px;
        }

        .card-body-custom { padding: 1.5rem; }

        /* --- Labels & Values --- */
        .info-group { margin-bottom: 1rem; }
        .info-label {
            font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted);
            font-weight: 700; margin-bottom: 0.2rem; letter-spacing: 0.5px;
        }
        .info-value {
            font-size: 0.95rem; color: var(--text-dark); font-weight: 500;
        }

        /* --- Form Elements --- */
        .form-label { font-weight: 600; font-size: 0.9rem; color: var(--text-dark); }
        .input-group-text { background-color: #f8f9fa; color: var(--custom-maroon); border-right: none; }
        .form-control { border-left: none; padding: 0.6rem 1rem; border-color: #ced4da; }
        .form-control:focus { border-color: var(--custom-maroon-light); box-shadow: 0 0 0 0.2rem rgba(124, 19, 22, 0.1); }

        /* --- Buttons --- */
        .btn-outline-custom {
            border: 1px solid #e2e8f0; color: var(--text-dark); background: white;
            border-radius: 8px; padding: 0.5rem 1.2rem; font-weight: 500; transition: var(--transition);
        }
        .btn-outline-custom:hover { background: #f8f9fa; border-color: #cbd5e1; }

        .btn-maroon {
            background-color: var(--custom-maroon); color: #fff; border: none;
            border-radius: 8px; padding: 0.7rem 1.5rem; font-weight: 600; width: 100%;
            transition: var(--transition); text-transform: uppercase; letter-spacing: 0.5px;
        }
        .btn-maroon:hover { background-color: var(--custom-maroon-light); transform: translateY(-2px); box-shadow: 0 5px 15px rgba(124, 19, 22, 0.2); color: white; }

        /* Animation */
        .animate-up { animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards; opacity: 0; transform: translateY(20px); }
        @keyframes fadeInUp { to { opacity: 1; transform: translateY(0); } }
    </style>

    <div class="container py-4">
        
        {{-- Header --}}
        <div class="page-header-wrapper animate-up mb-4">
            <div>
                <h4 class="fw-bold mb-1" style="color: var(--custom-maroon);">Penjadwalan Presentasi</h4>
                <small class="text-muted">Atur waktu dan tempat presentasi untuk mahasiswa.</small>
            </div>
            <div>
                <a href="{{ route('admin.pengajuan.show', $pengajuan->id) }}" class="btn btn-outline-custom shadow-sm">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </div>

        {{-- Alerts (Ditangani SweetAlert, ini fallback) --}}
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show animate-up shadow-sm border-0" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row g-4">
            
            {{-- KOLOM KIRI: Info Detail --}}
            <div class="col-lg-5 animate-up" style="animation-delay: 0.1s;">
                <div class="custom-card h-100">
                    <div class="card-header-info">
                        <i class="bi bi-person-vcard-fill text-secondary fs-5"></i> Informasi Mahasiswa
                    </div>
                    <div class="card-body-custom">
                        
                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <div class="info-group">
                                    <div class="info-label">Nama Mahasiswa</div>
                                    <div class="info-value fw-bold">{{ $pengajuan->user->name }}</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="info-group">
                                    <div class="info-label">Email</div>
                                    <div class="info-value text-truncate" title="{{ $pengajuan->user->email }}">{{ $pengajuan->user->email }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="info-group">
                            <div class="info-label">Judul Penelitian</div>
                            <div class="info-value fst-italic">"{{ $praPenelitian->judul ?? '-' }}"</div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <div class="info-group">
                                    <div class="info-label">Jenis</div>
                                    <div class="info-value"><span class="badge bg-light text-dark border">{{ $praPenelitian->jenis_penelitian ?? '-' }}</span></div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="info-group">
                                    <div class="info-label">Program Studi</div>
                                    <div class="info-value">{{ $praPenelitian->prodi ?? '-' }}</div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-3 border-light">

                        <div class="info-group">
                            <div class="info-label mb-2">Anggota Tim</div>
                            @if ($praPenelitian->anggotas && $praPenelitian->anggotas->count() > 0)
                                <div class="table-responsive border rounded-3">
                                    <table class="table table-sm table-striped mb-0 small">
                                        <thead class="bg-light">
                                            <tr>
                                                <th class="px-2">Nama</th>
                                                <th class="px-2">Jenjang</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($praPenelitian->anggotas as $anggota)
                                                <tr>
                                                    <td class="px-2">{{ $anggota->nama }}</td>
                                                    <td class="px-2">{{ $anggota->jenjang }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <span class="text-muted small fst-italic">Tidak ada anggota tim.</span>
                            @endif
                        </div>

                        {{-- Update Bagian Dosen + Nomor HP --}}
                        <div class="info-group mt-3">
                            <div class="info-label mb-2">Dosen Pembimbing</div>
                            <ul class="list-group list-group-flush border rounded-3">
                                {{-- Dosen 1 --}}
                                <li class="list-group-item">
                                    <div class="fw-bold text-dark mb-1">
                                        <i class="bi bi-person-badge me-2 text-muted"></i> {{ $praPenelitian->dosen1_nama }}
                                    </div>
                                    @if(!empty($praPenelitian->dosen1_hp))
                                        <div class="small ms-4">
                                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $praPenelitian->dosen1_hp) }}" target="_blank" class="text-decoration-none text-muted">
                                                <i class="bi bi-whatsapp text-success me-1"></i> {{ $praPenelitian->dosen1_hp }}
                                            </a>
                                        </div>
                                    @else
                                        <div class="small ms-4 text-muted fst-italic">No. HP tidak tersedia</div>
                                    @endif
                                </li>

                                {{-- Dosen 2 (Jika Ada) --}}
                                @if ($praPenelitian->dosen2_nama)
                                    <li class="list-group-item">
                                        <div class="fw-bold text-dark mb-1">
                                            <i class="bi bi-person-badge me-2 text-muted"></i> {{ $praPenelitian->dosen2_nama }}
                                        </div>
                                        @if(!empty($praPenelitian->dosen2_hp))
                                            <div class="small ms-4">
                                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $praPenelitian->dosen2_hp) }}" target="_blank" class="text-decoration-none text-muted">
                                                    <i class="bi bi-whatsapp text-success me-1"></i> {{ $praPenelitian->dosen2_hp }}
                                                </a>
                                            </div>
                                        @else
                                            <div class="small ms-4 text-muted fst-italic">No. HP tidak tersedia</div>
                                        @endif
                                    </li>
                                @endif
                            </ul>
                        </div>

                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN: Form Input --}}
            <div class="col-lg-7 animate-up" style="animation-delay: 0.2s;">
                <div class="custom-card">
                    <div class="card-header-form">
                        <i class="bi bi-calendar-event-fill"></i> Form Penjadwalan
                    </div>
                    <div class="card-body-custom">
                        
                        <form action="{{ route('admin.presentasi.store', $pengajuan->id) }}" method="POST" id="scheduleForm">
                            @csrf

                            <div class="mb-4">
                                <label class="form-label">Tanggal Presentasi <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-calendar-date"></i></span>
                                    <input type="date" name="tanggal_presentasi" class="form-control @error('tanggal_presentasi') is-invalid @enderror" 
                                           value="{{ old('tanggal_presentasi') }}" required>
                                </div>
                                @error('tanggal_presentasi') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Waktu Mulai <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-clock"></i></span>
                                        <input type="time" name="waktu_mulai" class="form-control @error('waktu_mulai') is-invalid @enderror" 
                                               value="{{ old('waktu_mulai', '09:00') }}" required>
                                    </div>
                                    @error('waktu_mulai') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Waktu Selesai <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-clock-history"></i></span>
                                        <input type="time" name="waktu_selesai" class="form-control @error('waktu_selesai') is-invalid @enderror" 
                                               value="{{ old('waktu_selesai', '11:00') }}" required>
                                    </div>
                                    @error('waktu_selesai') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Tempat / Ruangan <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-geo-alt-fill"></i></span>
                                    <input type="text" name="tempat" class="form-control @error('tempat') is-invalid @enderror" 
                                           value="{{ old('tempat', $pengajuan->ruangan ?? '') }}" placeholder="Contoh: Ruang Rapat Lt. 2" required>
                                </div>
                                @error('tempat') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Keterangan Tambahan</label>
                                <textarea name="keterangan_admin" rows="3" class="form-control" 
                                          placeholder="Catatan untuk mahasiswa/dosen (Opsional)...">{{ old('keterangan_admin') }}</textarea>
                            </div>

                            <div class="alert alert-info border-0 d-flex align-items-start mb-4 bg-opacity-10" style="background-color: #e0f2fe; color: #0369a1;">
                                <i class="bi bi-info-circle-fill me-2 mt-1"></i>
                                <div class="small">
                                    <strong>Catatan:</strong> Setelah disimpan, link penilaian akan otomatis dibuat dan kirimkan ke CI.
                                </div>
                            </div>

                            <button type="submit" class="btn btn-maroon" id="btnSubmit">
                                <i class="bi bi-send-fill me-2"></i> Kirim Jadwal Presentasi
                            </button>
                        </form>

                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            
            // SweetAlert untuk Notifikasi Session
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session("success") }}',
                    timer: 2000,
                    showConfirmButton: false
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: '{{ session("error") }}'
                });
            @endif

            // SweetAlert Konfirmasi Submit
            const form = document.getElementById('scheduleForm');
            const btnSubmit = document.getElementById('btnSubmit');

            form.addEventListener('submit', function(e) {
                e.preventDefault(); // Stop submit asli

                Swal.fire({
                    title: 'Kirim Jadwal?',
                    text: "Pastikan tanggal dan waktu sudah benar. Notifikasi akan dikirim ke mahasiswa.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#7c1316',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Kirim!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit(); // Lanjutkan submit
                    }
                });
            });
        });
    </script>
@endsection