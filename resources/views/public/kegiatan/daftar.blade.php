<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir Registrasi Pelatihan - Sindikat RSUD SLG</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --sindikat-maroon: #7c1316; /* Warna utama dari tombol/logo */
            --sindikat-maroon-dark: #5c0d10;
            --sindikat-bg: #fdfaf6; /* Warna background hangat dari referensi gambar */
            --sindikat-surface: #ffffff;
            --sindikat-text: #1a1a1a;
            --sindikat-text-muted: #6b7280;
            --sindikat-border: #f3f4f6;
            --sindikat-badge-bg: #fce8e8;
            --sindikat-input-bg: #f9fafb;
        }

        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: linear-gradient(135deg, #fff2eb 0%, #fdfaf6 50%, #f3f4f6 100%);
            color: var(--sindikat-text); 
            min-height: 100vh;
        }

        /* Navbar / Header */
        .brand-nav {
            padding: 24px 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .brand-nav img {
            height: 36px;
            border-radius: 8px;
        }
        .brand-nav .brand-text {
            font-weight: 800;
            font-size: 1.25rem;
            color: var(--sindikat-maroon);
            letter-spacing: -0.5px;
        }

        /* Hero Section */
        .hero-section {
            padding: 40px 0 40px;
        }
        .badge-kegiatan {
            background-color: var(--sindikat-badge-bg);
            color: var(--sindikat-maroon);
            padding: 8px 20px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 700;
            display: inline-block;
            margin-bottom: 24px;
        }
        .hero-title {
            font-size: 2.8rem;
            font-weight: 800;
            line-height: 1.15;
            letter-spacing: -1px;
            margin-bottom: 16px;
            color: #111827;
        }

        /* Form Card & Detail Pelatihan Info */
        .form-card { 
            background: var(--sindikat-surface); 
            border-radius: 24px; 
            box-shadow: 0 4px 24px rgba(0,0,0,0.04); 
            padding: 48px; 
            border: 1px solid #f0f0f0; 
            margin-bottom: 60px;
        }
        .info-kegiatan-box {
            background-color: var(--sindikat-input-bg);
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 32px;
        }
        .info-label {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--sindikat-text-muted);
            font-weight: 700;
            margin-bottom: 4px;
        }
        .info-value {
            font-weight: 600;
            font-size: 1rem;
            color: #111827;
        }
        .info-value i {
            color: var(--sindikat-maroon);
            margin-right: 6px;
        }

        /* Typography dalam Form */
        .section-title { 
            font-weight: 800; 
            color: #111827; 
            font-size: 1.15rem;
            margin-top: 32px; 
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            padding-bottom: 12px;
            border-bottom: 1px solid var(--sindikat-border);
        }
        .section-title i {
            color: var(--sindikat-maroon);
            margin-right: 10px;
        }
        
        /* Inputs */
        .form-label { 
            font-weight: 600; 
            font-size: 0.9rem; 
            color: #374151; 
            margin-bottom: 8px;
        }
        .form-control, .form-select { 
            border-radius: 12px; 
            padding: 14px 16px; 
            background: var(--sindikat-input-bg); 
            border: 1px solid #e5e7eb; 
            font-size: 0.95rem;
            transition: all 0.2s;
        }
        .form-control:focus, .form-select:focus { 
            background: #ffffff;
            border-color: var(--sindikat-maroon); 
            box-shadow: 0 0 0 4px rgba(124, 19, 22, 0.1); 
        }

        /* Commitment Box */
        .commitment-box {
            background: #fdfaf6; 
            border: 1px solid #f0e6dd;
            border-radius: 16px;
            padding: 24px;
        }
        .commitment-box h6 {
            color: var(--sindikat-maroon);
            font-weight: 800;
        }

        /* Submit Button */
        .btn-submit { 
            background-color: var(--sindikat-maroon); 
            color: white; 
            border-radius: 50px; 
            padding: 16px 32px; 
            font-weight: 700; 
            font-size: 1.1rem; 
            border: none; 
            transition: 0.2s; 
            width: 100%; 
        }
        .btn-submit:hover { 
            background-color: var(--sindikat-maroon-dark); 
            color: white; 
            transform: translateY(-2px); 
        }

        /* Responsiveness */
        @media (max-width: 768px) {
            .hero-title { font-size: 2.2rem; }
            .form-card { padding: 24px; border-radius: 20px; }
            .info-kegiatan-box { padding: 16px; }
        }
    </style>
</head>
<body>

    <div class="container">
        <!-- Brand Nav -->
        <div class="brand-nav">
            <img src="https://sindikat-rsudslg.kedirikab.go.id/icon.png" alt="Logo Sindikat" class="shadow-sm">
            <span class="brand-text">Sindikat</span>
        </div>

        <!-- Hero Section -->
        <div class="row hero-section justify-content-center">
            <div class="col-lg-9 text-center text-lg-start">
                <div class="badge-kegiatan">
                    Program Pendidikan dan Pelatihan
                </div>
                <h1 class="hero-title">Formulir Pendaftaran<br>Peserta Pelatihan.</h1>
                <p class="fs-5 text-muted">Silakan lengkapi data diri Anda di bawah ini untuk mengikuti kegiatan.</p>
            </div>
        </div>

        <!-- Form Section -->
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <div class="form-card">
                    
                    <!-- ALERT MESSAGES -->
                    @if(session('success'))
                        <div class="alert alert-success border-0 bg-success text-white rounded-4 shadow-sm p-4 text-center mb-4">
                            <i class="bi bi-check-circle-fill fs-1 d-block mb-2"></i>
                            <h4 class="fw-bold">Pendaftaran Sukses!</h4>
                            <p class="mb-0">{{ session('success') }}</p>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger rounded-4 mb-4 border-0 bg-danger text-white">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- DETAIL PELATIHAN -->
                    <div class="info-kegiatan-box shadow-sm">
                        <h5 class="fw-bold text-dark mb-4"><i class="bi bi-info-square-fill me-2 text-maroon" style="color: var(--sindikat-maroon);"></i> Detail Pelatihan</h5>
                        <div class="row g-4">
                            <div class="col-md-12">
                                <div class="info-label">Nama Kegiatan</div>
                                <div class="info-value fs-5">{{ $kegiatan->nama_kegiatan }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-label">Jadwal Pelaksanaan</div>
                                <div class="info-value">
                                    <i class="bi bi-calendar-range"></i> 
                                    @if(\Carbon\Carbon::parse($kegiatan->tanggal_mulai)->format('Y-m-d') == \Carbon\Carbon::parse($kegiatan->tanggal_selesai)->format('Y-m-d'))
                                        {{ \Carbon\Carbon::parse($kegiatan->tanggal_mulai)->translatedFormat('d F Y') }}
                                    @else
                                        {{ \Carbon\Carbon::parse($kegiatan->tanggal_mulai)->translatedFormat('d F Y') }} <span class="fw-normal text-muted mx-1">s/d</span> {{ \Carbon\Carbon::parse($kegiatan->tanggal_selesai)->translatedFormat('d F Y') }}
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-label">Platform / Tempat</div>
                                <div class="info-value"><i class="bi bi-geo-alt-fill"></i> {{ $kegiatan->platform ?? '-' }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-label">Jenis Kegiatan</div>
                                <div class="info-value"><i class="bi bi-tags-fill"></i> {{ $kegiatan->jenis_kegiatan ?? '-' }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-label">Jam Pelajaran (JPL)</div>
                                <div class="info-value"><i class="bi bi-clock-history"></i> {{ $kegiatan->jpl ?? '0' }} JPL</div>
                            </div>
                            <div class="col-12 mt-4 pt-3 border-top">
                                <div class="info-label mb-2">Deskripsi Pelatihan</div>
                                <p class="text-muted small mb-0 lh-lg">{{ $kegiatan->deskripsi ?? 'Tidak ada deskripsi tambahan.' }}</p>
                            </div>
                            
                            @if(!empty($kegiatan->keahlian) && is_array($kegiatan->keahlian) && count($kegiatan->keahlian) > 0)
                            <div class="col-12 mt-3">
                                <div class="info-label mb-2">Keahlian yang Diperoleh</div>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($kegiatan->keahlian as $skill)
                                        <span class="badge bg-white text-dark border py-2 px-3 shadow-sm">{{ $skill }}</span>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <form action="{{ route('public.kegiatan.daftar.submit', $kegiatan->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- BAGIAN 1: INSTANSI -->
                        <div class="section-title"><i class="bi bi-building"></i> Data Instansi / Penanggung Jawab</div>
                        <div class="row g-4">
                            <div class="col-md-12">
                                <label class="form-label">Instansi Bekerja (Nama RS/Dinas) <span class="text-danger">*</span></label>
                                <input type="text" name="nama_instansi" class="form-control" value="{{ old('nama_instansi') }}" required placeholder="Contoh: RSUD Simpang Lima Gumul">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email Penanggung Jawab <span class="text-danger">*</span></label>
                                <input type="email" name="email_pj" class="form-control" value="{{ old('email_pj') }}" required placeholder="email@instansi.com">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">No. HP Kontak Person <span class="text-danger">*</span></label>
                                <input type="number" name="no_hp_pj" class="form-control" value="{{ old('no_hp_pj') }}" required placeholder="08xxxxxxxxxx">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Alamat Lengkap Instansi <span class="text-danger">*</span></label>
                                <textarea name="alamat_instansi" class="form-control" rows="2" required placeholder="Jln. Raya Kediri - Pare...">{{ old('alamat_instansi') }}</textarea>
                            </div>
                        </div>

                        <!-- BAGIAN 2: DATA PESERTA -->
                        <div class="section-title"><i class="bi bi-person-lines-fill"></i> Data Pribadi Peserta</div>
                        <div class="row g-4">
                            <div class="col-md-12">
                                <label class="form-label">Nama Lengkap & Gelar <span class="text-danger">*</span></label>
<!-- Input Nama Lengkap -->
<input type="text" name="nama_lengkap_gelar" class="form-control" 
       value="{{ old('nama_lengkap_gelar', request('nama')) }}" 
       required placeholder="Contoh: dr. Fulan, Sp.A">

                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tempat Lahir <span class="text-danger">*</span></label>
                                <input type="text" name="tempat_lahir" class="form-control" value="{{ old('tempat_lahir') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_lahir" class="form-control" value="{{ old('tanggal_lahir') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">NIK (KTP) <span class="text-danger">*</span></label>
                                <input type="number" name="nik" class="form-control" value="{{ old('nik') }}" required placeholder="16 Digit NIK">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Jabatan <span class="text-danger">*</span></label>
                                <input type="text" name="jabatan" class="form-control" value="{{ old('jabatan') }}" required placeholder="Contoh: Kepala Ruangan">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Profesi <span class="text-danger">*</span></label>
                              
<!-- Input Profesi -->
<input type="text" name="profesi" class="form-control" 
       value="{{ old('profesi', request('profesi')) }}" 
       required placeholder="Contoh: Perawat / Bidan">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">No. HP (WhatsApp) <span class="text-danger">*</span></label>
                                <input type="number" name="no_hp_peserta" class="form-control" value="{{ old('no_hp_peserta') }}" required placeholder="08xxxxxxxxxx">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email Plataran Sehat Kemenkes <span class="text-danger">*</span></label>
                                <input type="email" name="email_plataran_sehat" class="form-control" value="{{ old('email_plataran_sehat') }}" required placeholder="email@kemkes.go.id">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Pendidikan Terakhir <span class="text-danger">*</span></label>
                                <select name="pendidikan_terakhir" class="form-select" required>
                                    <option value="">- Pilih -</option>
                                    <option value="D3">D3</option>
                                    <option value="D4/S1">D4 / S1</option>
                                    <option value="Profesi">Profesi</option>
                                    <option value="S2/Spesialis">S2 / Spesialis</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status Pegawai <span class="text-danger">*</span></label>
                                <select name="status_pegawai" class="form-select" required>
                                    <option value="">- Pilih -</option>
                                    <option value="PNS">PNS</option>
                                    <option value="PPPK">PPPK</option>
                                    <option value="Non-ASN">Non-ASN / Honorer</option>
                                    <option value="Swasta">Swasta</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">NIP (Opsional)</label>
                                <input type="number" name="nip" class="form-control" value="{{ old('nip') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Pangkat/Golongan (Opsional)</label>
                                <input type="text" name="pangkat_golongan" class="form-control" value="{{ old('pangkat_golongan') }}" placeholder="Cth: Penata Muda / III a">
                            </div>
                        </div>

                        <!-- BAGIAN 3: PREFERENSI & ADMINISTRASI -->
                        <div class="section-title"><i class="bi bi-gear-wide-connected"></i> Teknis & Administrasi</div>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label">Pilih Tempat / Metode <span class="text-danger">*</span></label>
                                <select name="metode_pelatihan" class="form-select" required>
                                    <option value="">- Pilih -</option>
                                    <option value="Daring (Zoom)">Daring (Zoom Meeting)</option>
                                    <option value="Blended (RSUD SLG)">Blended Learning (RSUD SLG Kediri)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Ukuran Kaos <span class="text-danger">*</span></label>
                                <select name="ukuran_kaos" class="form-select" required>
                                    <option value="">- Pilih -</option>
                                    <option value="S">S</option><option value="M">M</option><option value="L">L</option>
                                    <option value="XL">XL</option><option value="XXL">XXL</option><option value="XXXL">XXXL</option>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label d-block mb-3">Sudah Memiliki Akun LMS / Plataran Sehat Kemenkes? <span class="text-danger">*</span></label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="punya_akun_lms" id="lmsYa" value="ya" required>
                                    <label class="form-check-label" for="lmsYa">Ya</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="punya_akun_lms" id="lmsTidak" value="tidak" required>
                                    <label class="form-check-label" for="lmsTidak">Tidak (Silakan buat di lms.kemkes.go.id)</label>
                                </div>
                            </div>
                            
                            <div class="col-md-12">
                                <label class="form-label">Upload Bukti Transfer / Pembayaran <span class="text-danger">*</span></label>
                                <input type="file" name="bukti_bayar" class="form-control bg-white" accept="image/jpeg,image/png,image/jpg" required>
                                <small class="text-muted mt-2 d-block">Format: JPG/PNG/JPEG. Maks 5MB.</small>
                            </div>

                            <div class="col-md-12 mt-4">
                                <div class="commitment-box">
                                    <h6 class="mb-3">Komitmen Peserta Pelatihan</h6>
                                    <ol class="small text-muted mb-4 ps-3">
                                        <li class="mb-2">Bersedia mengikuti seluruh rangkaian kegiatan pelatihan sesuai jadwal yang ditentukan baik secara daring atau luring.</li>
                                        <li class="mb-2">Komitmen mengikuti pelatihan dengan sungguh sungguh dan mematuhi peraturan serta tata tertib yang berlaku.</li>
                                        <li class="mb-2">Memberikan informasi yang benar dan jujur dalam formulir ini.</li>
                                        <li>Menyadari bahwa pelatihan ini bertujuan untuk meningkatkan kompetensi sebagai tenaga kesehatan.</li>
                                    </ol>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="komitmen" value="ya" id="setuju" required>
                                        <label class="form-check-label fw-bold text-dark" for="setuju">
                                            Ya, saya menyetujui komitmen ini <span class="text-danger">*</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-5 pt-4 border-top">
                            <button type="submit" class="btn-submit">
                                Kirim Formulir Registrasi <i class="bi bi-arrow-right ms-2"></i>
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>