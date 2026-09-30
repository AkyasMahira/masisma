@extends('layouts.public')

@section('title', 'Pembaruan Data Pelatihan - RSUD SLG')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

<style>
    :root {
        --primary-maroon: #7c1316;
        --accent-maroon: #a3191d;
        --soft-maroon: #fdf2f2;
        --glass: rgba(255, 255, 255, 0.95);
        --text-main: #1e293b;
        --text-light: #64748b;
        --border-radius: 20px;
        --shadow-sm: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
        --shadow-lg: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    }

    body {
        background: radial-gradient(circle at top right, #fdf2f2, #f8fafc);
        color: var(--text-main);
        font-family: 'Inter', sans-serif;
    }

    /* --- Hero Header --- */
    .hero-bg {
        background: white;
        padding: 4rem 0 6rem 0;
        text-align: center;
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }

    .hero-logo { height: 90px; filter: drop-shadow(0 5px 15px rgba(0,0,0,0.1)); margin-bottom: 1.5rem; }
    
    .hero-title {
        font-weight: 800;
        letter-spacing: -1px;
        color: var(--primary-maroon);
        margin-bottom: 0.5rem;
    }

    /* --- Stats Dashboard --- */
    .jpl-dashboard {
        margin-top: -4rem;
        background: var(--glass);
        backdrop-filter: blur(10px);
        border-radius: var(--border-radius);
        padding: 2rem;
        border: 1px solid white;
        box-shadow: var(--shadow-lg);
    }

    .jpl-progress-circle {
        width: 80px;
        height: 80px;
        background: var(--soft-maroon);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 4px solid white;
        box-shadow: var(--shadow-sm);
    }

    .progress-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-weight: 700;
        color: var(--text-light);
    }

    /* --- Form Cards --- */
    .section-card {
        background: white;
        border-radius: var(--border-radius);
        padding: 2.5rem;
        margin-bottom: 2rem;
        border: 1px solid rgba(226, 232, 240, 0.8);
        box-shadow: var(--shadow-sm);
    }

    .section-header {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 2rem;
    }

    .icon-box {
        width: 45px;
        height: 45px;
        background: var(--primary-maroon);
        color: white;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .form-label {
        font-weight: 600;
        font-size: 0.8rem;
        color: var(--text-light);
        margin-bottom: 8px;
    }

    .form-control, .form-select {
        border-radius: 12px;
        border: 1.5px solid #f1f5f9;
        background: #f8fafc;
        padding: 12px 16px;
        font-size: 0.95rem;
        transition: 0.3s all;
    }

    .form-control:focus {
        background: white;
        border-color: var(--primary-maroon);
        box-shadow: 0 0 0 4px rgba(124, 19, 22, 0.08);
    }

    /* --- Training Item Rows --- */
    .item-row {
        background: #ffffff;
        border-radius: 18px;
        padding: 25px;
        margin-bottom: 1.5rem;
        border: 1px solid #f1f5f9;
        position: relative;
        transition: 0.3s;
    }

    .item-row:hover {
        box-shadow: var(--shadow-sm);
        border-color: var(--primary-maroon);
    }

    .btn-delete {
        position: absolute;
        top: -10px;
        right: -10px;
        background: white;
        color: #ef4444;
        border: 1px solid #fee2e2;
        width: 35px;
        height: 35px;
        border-radius: 50%;
        box-shadow: var(--shadow-sm);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10;
    }

    .btn-delete:hover { background: #ef4444; color: white; }

    .btn-add {
        width: 100%;
        background: white;
        color: var(--primary-maroon);
        border: 2px dashed #e2e8f0;
        padding: 15px;
        border-radius: var(--border-radius);
        font-weight: 700;
        margin-top: 0.5rem;
    }

    .btn-add:hover { border-color: var(--primary-maroon); background: var(--soft-maroon); }

    /* --- Sticky Bar --- */
    .action-bar {
        position: fixed;
        bottom: 30px;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(30, 41, 59, 0.9);
        backdrop-filter: blur(10px);
        padding: 12px 40px;
        border-radius: 100px;
        display: flex;
        gap: 20px;
        z-index: 1000;
        box-shadow: 0 20px 50px rgba(0,0,0,0.2);
    }

    .btn-save {
        background: var(--primary-maroon);
        color: white;
        border: none;
        padding: 10px 30px;
        border-radius: 100px;
        font-weight: 700;
        transition: 0.3s;
    }

    .btn-save:hover { background: var(--accent-maroon); transform: scale(1.05); }

    .file-link {
        background: #f0fdf4;
        color: #166534;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 0.75rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        margin-top: 10px;
        font-weight: 600;
    }
</style>

<div class="hero-bg">
    <div class="container animate__animated animate__fadeIn">
        <img src="/23.png" alt="Logo" class="hero-logo">
        <h2 class="hero-title">PORTAL DATA MANDIRI</h2>
        <p class="text-muted">Lengkapi profil dan riwayat kompetensi Anda</p>
    </div>
</div>

<div class="container form-container">
    <div class="row justify-content-center">
        <div class="col-lg-11">

            {{-- 1. DASHBOARD RINGKASAN JPL --}}
            @php
                $currentYear = date('Y');
                $totalJpl = $pelatihan->getTotalJplByYear($currentYear);
                $percent = min(($totalJpl / 20) * 100, 100);
            @endphp
            <div class="jpl-dashboard animate__animated animate__slideInUp mb-5">
                <div class="row align-items-center">
                    <div class="col-md-auto text-center">
                        <div class="jpl-progress-circle">
                            <h3 class="mb-0 fw-bold" style="color: var(--primary-maroon)">{{ $totalJpl }}</h3>
                        </div>
                    </div>
                    <div class="col">
                        <div class="d-flex justify-content-between mb-2">
                            <div>
                                <h5 class="mb-0 fw-800">Capaian JPL Tahun {{ $currentYear }}</h5>
                                <span class="text-muted small">Target Minimal: 20 Jam Pelajaran</span>
                            </div>
                            <span class="fw-bold">{{ $totalJpl }}/20 JPL</span>
                        </div>
                        <div class="progress" style="height: 12px; border-radius: 10px; background: #e2e8f0;">
                            <div class="progress-bar" role="progressbar" 
                                 style="width: {{ $percent }}%; background: var(--primary-maroon); border-radius: 10px;">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-auto mt-3 mt-md-0">
                        @if($totalJpl >= 20)
                            <div class="px-4 py-2 rounded-pill bg-success text-white small fw-bold">
                                <i class="fas fa-check-double me-2"></i>TARGET TERPENUHI
                            </div>
                        @else
                            <div class="px-4 py-2 rounded-pill bg-warning text-dark small fw-bold">
                                <i class="fas fa-clock me-2"></i>KURANG {{ 20 - $totalJpl }} JPL
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <form action="{{ route('public.pelatihan.update', $pelatihan->id) }}" method="POST" enctype="multipart/form-data" id="updateForm">
                @csrf
                @method('PUT')

                {{-- SECTION PROFIL --}}
                <div class="section-card">
                    <div class="section-header">
                        <div class="icon-box"><i class="fas fa-user-check"></i></div>
                        <h4 class="mb-0 fw-bold">Data Identitas Pegawai</h4>
                    </div>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label">Nama Lengkap & Gelar</label>
                            <input type="text" name="nama" class="form-control" value="{{ $pelatihan->nama }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nomor Induk Kependudukan (NIK)</label>
                            <input type="text" name="nik" class="form-control" value="{{ $pelatihan->nik }}" placeholder="16 Digit NIK">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Status</label>
                            <input type="text" class="form-control" value="{{ $pelatihan->status_pegawai }}" disabled>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">NIP / NIRP</label>
                            <input type="text" class="form-control" value="{{ $pelatihan->nip ?? $pelatihan->nirp }}" disabled>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Akun LMS (Kemenkes)</label>
                            <select name="lms_status" id="lms_status" class="form-select" onchange="toggleLms(this.value)">
                                <option value="Tidak" {{ $pelatihan->lms_status == 'Tidak' ? 'selected' : '' }}>Belum Ada</option>
                                <option value="Ada" {{ $pelatihan->lms_status == 'Ada' ? 'selected' : '' }}>Sudah Ada</option>
                            </select>
                        </div>
                        <div class="col-md-12" id="lms_email_wrapper" style="display: {{ $pelatihan->lms_status == 'Ada' ? 'block' : 'none' }};">
                            <label class="form-label">Email LMS (Pastikan aktif untuk sinkronisasi data)</label>
                            <input type="email" name="lms_email" class="form-control" value="{{ $pelatihan->lms_email }}" placeholder="contoh@mail.com">
                        </div>
                    </div>
                </div>

                {{-- SECTION PELATIHAN --}}
                @php
                    $categories = [
                        ['title' => 'Riwayat Pelatihan Dasar', 'id' => 'dasar', 'icon' => 'fa-certificate'],
                        ['title' => 'Peningkatan Kompetensi', 'id' => 'komp', 'icon' => 'fa-medal']
                    ];
                @endphp

                @foreach($categories as $cat)
                <div class="section-card">
                    <div class="section-header">
                        <div class="icon-box"><i class="fas {{ $cat['icon'] }}"></i></div>
                        <h4 class="mb-0 fw-bold">{{ $cat['title'] }}</h4>
                    </div>
                    
                    <div id="wrapper-{{ $cat['id'] }}">
                        @php $currentData = $cat['id'] == 'dasar' ? $pelatihan->pelatihan_dasar : $pelatihan->pelatihan_peningkatan_kompetensi; @endphp
                        
                        @foreach($currentData ?? [] as $val)
                        <div class="item-row animate__animated animate__fadeIn">
                            <button type="button" class="btn-delete" onclick="this.closest('.item-row').remove()">
                                <i class="fas fa-times"></i>
                            </button>
                            <div class="row g-3">
                                <div class="col-lg-6">
                                    <label class="form-label">Nama Pelatihan / Diklat</label>
                                    <input type="text" name="pelatihan_{{ $cat['id'] }}[]" class="form-control" value="{{ $val['nama'] }}" required>
                                </div>
                                <div class="col-lg-2 col-6">
                                    <label class="form-label">Tahun</label>
                                    <input type="number" name="pelatihan_tahun_{{ $cat['id'] }}[]" class="form-control" value="{{ $val['tahun'] }}">
                                </div>
                                <div class="col-lg-1 col-6">
                                    <label class="form-label">JPL</label>
                                    <input type="number" name="pelatihan_jpl_{{ $cat['id'] }}[]" class="form-control" value="{{ $val['jpl'] ?? 0 }}">
                                </div>
                                <div class="col-lg-3">
                                    <label class="form-label">Update Sertifikat (PDF)</label>
                                    <input type="file" name="pelatihan_file_{{ $cat['id'] }}[]" class="form-control input-file" accept=".pdf">
                                    <input type="hidden" name="pelatihan_existing_file_{{ $cat['id'] }}[]" value="{{ $val['file'] }}">
                                    @if($val['file'])
                                        <a href="{{ asset('storage/'.$val['file']) }}" target="_blank" class="file-link">
                                            <i class="fas fa-check-circle"></i> File Tersimpan
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <button type="button" class="btn-add" onclick="tambahRow('wrapper-{{ $cat['id'] }}', '{{ $cat['id'] }}')">
                        <i class="fas fa-plus me-2"></i>Tambah Baris Pelatihan
                    </button>
                </div>
                @endforeach

                {{-- Action Footer --}}
                <div class="mb-5 pb-5"></div>
                <div class="action-bar">
                    <a href="{{ route('public.pelatihan.index') }}" class="btn text-white text-decoration-none px-2 fw-bold small opacity-75">Batal</a>
                    <button type="submit" class="btn-save shadow">
                        <i class="fas fa-cloud-upload-alt me-2"></i>Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function toggleLms(val) {
        const wrapper = document.getElementById('lms_email_wrapper');
        if(val === 'Ada') {
            wrapper.style.display = 'block';
            wrapper.classList.add('animate__animated', 'animate__fadeInDown');
        } else {
            wrapper.style.display = 'none';
        }
    }

    function tambahRow(containerId, type) {
        const container = document.getElementById(containerId);
        const html = `
            <div class="item-row animate__animated animate__fadeInUp">
                <button type="button" class="btn-delete" onclick="this.closest('.item-row').remove()">
                    <i class="fas fa-times"></i>
                </button>
                <div class="row g-3">
                    <div class="col-lg-6">
                        <label class="form-label">Nama Pelatihan / Diklat</label>
                        <input type="text" name="pelatihan_${type}[]" class="form-control" placeholder="..." required>
                    </div>
                    <div class="col-lg-2 col-6">
                        <label class="form-label">Tahun</label>
                        <input type="number" name="pelatihan_tahun_${type}[]" class="form-control" value="${new Date().getFullYear()}">
                    </div>
                    <div class="col-lg-1 col-6">
                        <label class="form-label">JPL</label>
                        <input type="number" name="pelatihan_jpl_${type}[]" class="form-control" value="0">
                    </div>
                    <div class="col-lg-3">
                        <label class="form-label">Sertifikat (PDF)</label>
                        <input type="file" name="pelatihan_file_${type}[]" class="form-control input-file" accept=".pdf">
                        <input type="hidden" name="pelatihan_existing_file_${type}[]" value="">
                    </div>
                </div>
            </div>`;
        container.insertAdjacentHTML('beforeend', html);
    }

    document.getElementById('updateForm').onsubmit = function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Kirim Pembaruan?',
            text: "Data akan diperbarui secara otomatis di database rumah sakit.",
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#7c1316',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Update Data!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({ title: 'Memproses...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
                this.submit();
            }
        });
    };
</script>
@endsection