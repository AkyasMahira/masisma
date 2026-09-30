<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Presensi - {{ $kegiatan->nama_kegiatan }}</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />

    <style>
        :root {
            --maroon: #7c1316;
            --maroon-dark: #5a0d10;
            --maroon-light: #fef1f2;
            --text-dark: #1e293b;
            --text-gray: #64748b;
            --bg-color: #f8fafc;
            --border-color: #e2e8f0;
        }

        body { 
            background-color: var(--bg-color); 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            color: var(--text-dark);
            -webkit-font-smoothing: antialiased;
            position: relative;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* --- WATERMARK LOGO RAKSASA MIRING --- */
        .watermark-left, .watermark-right {
            position: fixed;
            z-index: -1;
            opacity: 0.06;
            pointer-events: none;
            filter: grayscale(10%);
        }
        .watermark-left {
            top: 10%;
            left: -100px;
            width: 320px;
            transform: rotate(-25deg);
        }
        .watermark-right {
            bottom: 10%;
            right: -80px;
            width: 280px;
            transform: rotate(20deg);
        }

        .wrapper {
            max-width: 500px;
            margin: 0 auto;
            padding: 40px 20px 60px 20px;
            position: relative;
            z-index: 1;
        }

        /* --- HEADER TEKS --- */
        .header-text { text-align: center; margin-bottom: 30px; }
        .header-text h2 { font-size: 1.4rem; font-weight: 800; color: var(--maroon); margin-bottom: 5px; letter-spacing: -0.5px; text-transform: uppercase; }
        .header-text p { font-size: 0.8rem; font-weight: 600; color: var(--text-gray); letter-spacing: 2px; text-transform: uppercase; margin: 0; }

        /* --- KARTU UTAMA GLASSMORPHISM --- */
        .main-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-radius: 24px;
            padding: 35px 25px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.04), 0 1px 3px rgba(255,255,255,0.8) inset;
            border: 1px solid rgba(255, 255, 255, 0.9);
        }

        /* --- INFO KEGIATAN --- */
        .event-info { text-align: center; padding-bottom: 25px; margin-bottom: 25px; border-bottom: 2px dashed var(--border-color); }
        .badge-kegiatan { background-color: var(--maroon-light); color: var(--maroon); font-size: 0.75rem; font-weight: 800; padding: 6px 16px; border-radius: 50px; letter-spacing: 1px; display: inline-block; margin-bottom: 15px; }
        .kegiatan-title { font-size: 1.25rem; font-weight: 800; color: var(--text-dark); line-height: 1.4; margin-bottom: 15px; }
        .kegiatan-meta { display: flex; justify-content: center; flex-wrap: wrap; gap: 15px; font-size: 0.85rem; font-weight: 600; color: var(--text-gray); }
        .kegiatan-meta div { display: flex; align-items: center; gap: 6px; }
        .kegiatan-meta i { color: var(--maroon); font-size: 1.1rem; }

        /* --- FORM STYLING --- */
        .label-custom { font-size: 0.8rem; font-weight: 800; color: var(--text-gray); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 12px; display: block; }

        /* Choices JS */
        .choices__inner { background-color: #fff !important; border: 2px solid var(--border-color) !important; border-radius: 12px !important; padding: 12px 15px !important; font-size: 0.95rem; font-weight: 600; color: var(--text-dark); transition: 0.3s; }
        .choices.is-focused .choices__inner { border-color: var(--maroon) !important; box-shadow: 0 0 0 4px var(--maroon-light) !important; }
        .choices__list--dropdown { border-radius: 12px !important; border: 1px solid var(--border-color) !important; box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important; }
        .choices__item--selectable.is-highlighted { background-color: var(--maroon-light) !important; color: var(--maroon) !important; font-weight: 700; }

        .detail-box { background: rgba(248, 250, 252, 0.8); border-radius: 12px; padding: 15px 20px; margin-top: 15px; display: none; border: 1px solid var(--border-color); animation: fadeIn 0.3s ease; }
        .detail-item { margin-bottom: 10px; }
        .detail-item:last-child { margin-bottom: 0; }
        .detail-label { font-size: 0.7rem; color: var(--text-gray); font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 2px;}
        .detail-value { font-size: 0.95rem; color: var(--text-dark); font-weight: 800; }

        /* RADIO HADIR/IZIN */
        .radio-container { display: flex; gap: 15px; }
        .radio-container > div { flex: 1; }
        .radio-box {
            background: #fff;
            border: 2px solid var(--border-color);
            border-radius: 16px;
            padding: 20px 10px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 10px;
            height: 100%;
            width: 100%;
        }
        .radio-box i { font-size: 2.2rem; color: #cbd5e1; line-height: 1; margin: 0; transition: 0.2s; }
        .radio-box span { font-size: 0.95rem; font-weight: 700; color: var(--text-gray); line-height: 1; margin: 0; transition: 0.2s; }
        
        .btn-check:checked + .radio-box {
            border-color: var(--maroon);
            background-color: var(--maroon-light);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(124, 19, 22, 0.08);
        }
        .btn-check:checked + .radio-box i, .btn-check:checked + .radio-box span { color: var(--maroon); }

        /* Upload Area */
        .upload-area { background: #fff; border: 2px dashed var(--border-color); border-radius: 16px; padding: 30px 20px; text-align: center; position: relative; transition: 0.3s; cursor: pointer; }
        .upload-area:hover { border-color: var(--maroon); background: var(--maroon-light); }
        .upload-area input[type="file"] { position: absolute; width: 100%; height: 100%; top: 0; left: 0; opacity: 0; cursor: pointer; }
        .upload-area i { font-size: 2.2rem; color: #cbd5e1; display: block; margin-bottom: 10px; transition: 0.3s; }
        .upload-area h6 { font-size: 0.95rem; font-weight: 800; color: var(--text-dark); margin-bottom: 5px; }
        .upload-area p { font-size: 0.8rem; color: var(--text-gray); margin: 0; }
        
        .upload-area.uploaded { border-color: #10b981; background: #ecfdf5; border-style: solid; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.08);}
        .upload-area.uploaded i { color: #10b981; }

        /* Button Submit */
        .btn-kirim { background: var(--maroon); color: #fff; border: none; border-radius: 16px; padding: 16px; width: 100%; font-size: 1rem; font-weight: 800; display: flex; justify-content: center; align-items: center; gap: 10px; box-shadow: 0 8px 20px rgba(124, 19, 22, 0.2); transition: all 0.3s; margin-top: 10px; }
        .btn-kirim:hover { background: var(--maroon-dark); transform: translateY(-2px); box-shadow: 0 12px 25px rgba(124, 19, 22, 0.3); color: white;}
        .btn-kirim:disabled { background: #cbd5e1; box-shadow: none; transform: none; cursor: not-allowed; }

        /* Closed State */
        .closed-state { padding: 30px 10px; text-align: center; }
        .closed-state .icon-bg { width: 90px; height: 90px; border-radius: 24px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px auto; }
        .closed-state i { font-size: 3rem; color: #94a3b8; }
        .closed-state h3 { font-size: 1.4rem; font-weight: 800; color: var(--text-dark); margin-bottom: 12px; }
        .closed-state p { font-size: 0.95rem; color: var(--text-gray); font-weight: 500; line-height: 1.6; margin-bottom: 30px; }

        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    </style>
</head>
<body>

    <img src="https://rsudslg.kedirikab.go.id/asset_compro/img/logo/Logo.png" alt="Logo RSUD" class="watermark-left">
    <img src="https://sindikat-rsudslg.kedirikab.go.id/icon.png" alt="Logo Sindikat" class="watermark-right">

    <div class="wrapper">
        <div class="header-text">
            <h2>RSUD Simpang Lima Gumul</h2>
            <p>Sistem Informasi Pendidikan Penelitian dan Pelatihan Diklat RSUD Simpang Lima Gumul</p>
        </div>

        @php
            $sekarang = \Carbon\Carbon::now();$mulai = \Carbon\Carbon::parse($kegiatan->tanggal_mulai);$selesai = \Carbon\Carbon::parse($kegiatan->tanggal_selesai);$batas_pulang = $selesai->copy()->addHour();$isOpen = false;
            $pesanTutup = "";
            $iconTutup = "bi-lock-fill"; 
            $judulTutup = "Presensi Ditutup";

            if ($kegiatan->status_absen == 'buka') {$isOpen = true;
            } elseif ($kegiatan->status_absen == 'tutup') {$isOpen = false;
                $pesanTutup = "Sesi presensi untuk kegiatan ini telah ditutup secara manual oleh Panitia.";
                $iconTutup = "bi-shield-lock-fill";
            } elseif ($kegiatan->status_absen == 'otomatis') {
                if ($sekarang->lessThan($mulai)) {$isOpen = false;
                    $judulTutup = "Belum Dimulai";
                    $pesanTutup = "Sesi presensi otomatis akan terbuka pada jam " . $mulai->format('H:i') . " WIB.";
                    $iconTutup = "bi-clock-fill"; 
                } elseif ($sekarang->greaterThan($batas_pulang)) {$isOpen = false;
                    $judulTutup = "Sesi Berakhir";
                    $pesanTutup = "Batas waktu pengisian presensi telah habis (maksimal 1 jam setelah event berakhir).";
                    $iconTutup = "bi-calendar-x-fill"; 
                } else {
                    $isOpen = true;
                }
            }
        @endphp

        <div class="main-card">
            
            <div class="event-info">
                <div class="badge-kegiatan">{{ strtoupper($kegiatan->jenis_kegiatan) }}</div>
                <h1 class="kegiatan-title">{{ $kegiatan->nama_kegiatan }}</h1>
                <div class="kegiatan-meta">
                  <div><i class="bi bi-calendar-check-fill"></i> {{ $sekarang->format('d M Y') }}</div>
                    <div><i class="bi bi-clock-fill"></i> {{ $mulai->format('H:i') }} WIB</div>
                    <div><i class="bi bi-geo-alt-fill"></i> {{ $kegiatan->platform }}</div>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success rounded-4 mb-4 border-0 d-flex align-items-center" style="background: #ecfdf5; color: #065f46;">
                    <i class="bi bi-check-circle-fill fs-3 me-3"></i> 
                    <div><strong class="d-block mb-1">Sukses!</strong><span style="font-size: 0.85rem;">{{ session('success') }}</span></div>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger rounded-4 mb-4 border-0 d-flex align-items-center" style="background: #fef2f2; color: #991b1b;">
                    <i class="bi bi-exclamation-triangle-fill fs-3 me-3"></i> 
                    <div><strong class="d-block mb-1">Perhatian!</strong><span style="font-size: 0.85rem;">{{ session('error') }}</span></div>
                </div>
            @endif

            @if($isOpen)
                <form action="{{ route('public.kegiatan.absen.submit', $kegiatan->token_absensi) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="label-custom">1. Identitas Peserta <span class="text-danger">*</span></label>
                        <select name="kegiatan_peserta_id" id="peserta_id" required>
                            <option value="">Ketik untuk mencari nama Anda...</option>
                            @foreach($daftarPeserta as$p)
                                @php
                                    $isMasuk = in_array($p->id,$sudahAbsenMasuk ?? []) ? 1 : 0;
                                    $isPulang = in_array($p->id,$sudahAbsenPulang ?? []) ? 1 : 0;
                                    
                                    $statusBadge = '';
                                    if ($kegiatan->tipe_absen == 'masuk_saja' && $isMasuk) {$statusBadge = ' - (Selesai Absen)';
                                    } elseif ($kegiatan->tipe_absen == 'masuk_keluar') {
                                        if ($isPulang) {$statusBadge = ' - (Selesai)';
                                        } elseif ($isMasuk) {$statusBadge = ' - (Belum Pulang)';
                                        }
                                    }
                                @endphp
                                <option value="{{ $p->id }}" 
                                        data-profesi="{{ $p->profesi ?? '-' }}" 
                                        data-instansi="{{ $p->instansi->nama_instansi ?? 'Internal RS' }}"
                                        data-ruangan="{{ $p->ruangan->nama_ruangan ?? '-' }}"
                                        data-is-masuk="{{ $isMasuk }}"
                                        data-is-pulang="{{ $isPulang }}">
                                    {{ strtoupper($p->nama_lengkap_gelar) }}{{$statusBadge }}
                                </option>
                            @endforeach
                        </select>
                        
                        <div class="detail-box" id="detail_peserta">
                            <div class="detail-item">
                                <span class="detail-label">Profesi / Jabatan</span>
                                <span class="detail-value" id="lbl_profesi">-</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">{{ $kegiatan->jenis_kegiatan == 'internal' ? 'Ruangan Unit' : 'Asal Instansi' }}</span>
                                <span class="detail-value" id="lbl_lokasi">-</span>
                            </div>
                            <!-- AREA ALERT DUPLIKAT ABSEN -->
                            <div id="status_absen_info"></div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="label-custom">2. Kehadiran <span class="text-danger">*</span></label>
                        <div class="radio-container">
                            <div>
                                <input type="radio" class="btn-check" name="status_kehadiran" id="hadir" value="hadir" checked>
                                <label class="radio-box" for="hadir">
                                    <i class="bi bi-person-check-fill"></i>
                                    <span>Hadir</span>
                                </label>
                            </div>
                            <div>
                                <input type="radio" class="btn-check" name="status_kehadiran" id="tidak_hadir" value="tidak_hadir">
                                <label class="radio-box" for="tidak_hadir">
                                    <i class="bi bi-person-x-fill"></i>
                                    <span>Izin / Sakit</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4" id="box_foto"> 
                        <label class="label-custom">3. Bukti Foto (Selfie) <span class="text-danger">*</span></label> 
                        <div class="upload-area" id="upload_area"> 
                            <i class="bi bi-camera-fill" id="upload_icon"></i> 
                            <h6 id="upload_title">Buka Kamera</h6> 
                            <p id="file_name">Ketuk area ini untuk mengambil foto</p>                   
                            <input type="file" name="foto_bukti" id="foto_bukti" accept="image/*" capture="environment"  required> 
                        </div> 
                        <div id="foto_error_msg" class="text-danger fw-bold mt-2" style="font-size: 0.8rem; display: none;">
                            <i class="bi bi-exclamation-triangle-fill"></i> Ukuran file terlalu besar (Maksimal 2 MB). Silakan kompres atau kecilkan resolusi kamera Anda terlebih dahulu!
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-kirim">
                        Kirim Presensi <i class="bi bi-arrow-right-circle-fill"></i>
                    </button>
                </form>

            @else
                <div class="closed-state">
                    <div class="icon-bg"><i class="bi {{ $iconTutup }}"></i></div>
                    <h3>{{ $judulTutup }}</h3>
                    <p>{{ $pesanTutup }}</p>
                    
                    <div style="background: rgba(241, 245, 249, 0.8); border-radius: 16px; padding: 20px; border: 1px solid var(--border-color); margin-bottom: 25px;">
                        <span class="label-custom" style="font-size: 0.7rem; margin-bottom: 5px;">Waktu Server Saat Ini</span>
                        <div style="font-weight: 800; color: var(--text-dark); font-size: 1.25rem;">
                            <!-- JAM DIBUAT REALTIME DENGAN ID INI -->
                            <span id="liveClock">{{ $sekarang->format('H:i:s') }}</span> <span style="font-size: 0.9rem; color: var(--text-gray);">WIB</span>
                        </div>
                    </div>

                    <button onclick="window.location.reload()" class="btn-kirim" style="background: #e2e8f0; color: var(--text-dark); box-shadow: none;">
                        <i class="bi bi-arrow-clockwise text-dark"></i> Cek Ulang Status
                    </button>
                </div>
            @endif

        </div>
        
        <div style="text-align: center; margin-top: 30px; font-size: 0.8rem; color: var(--text-gray); font-weight: 700; position: relative; z-index: 2;">
            &copy; {{ date('Y') }} SINDIKAT RSUD SLG
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/browser-image-compression@2.0.2/dist/browser-image-compression.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    
    <script>
        // UPDATE JAM REALTIME (Mencegah salah sangka jam nge-freeze saat halaman dibuka)
        function updateClock() {
            const clockEl = document.getElementById('liveClock');
            if (clockEl) {
                const now = new Date();
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');
                const seconds = String(now.getSeconds()).padStart(2, '0');
                clockEl.innerText = `${hours}:${minutes}:${seconds}`;
            }
        }
        setInterval(updateClock, 1000);

        document.addEventListener('DOMContentLoaded', function() {
            
            // --- 1. INISIALISASI CHOICES.JS ---
            const pesertaDropdown = document.getElementById('peserta_id');
            
            if(pesertaDropdown) {
                new Choices(pesertaDropdown, { 
                    searchEnabled: true, 
                    searchPlaceholderValue: 'Ketik nama Anda...', 
                    itemSelectText: '', 
                    shouldSort: false
                });

                document.querySelector('.choices__input--cloned').addEventListener('input', function(e) {
                    this.value = this.value.toUpperCase();
                });

                pesertaDropdown.addEventListener('change', function(e) {
                    const selectedValue = e.target.value;
                    const detailBox = document.getElementById('detail_peserta');
                    
                    if(!selectedValue) { detailBox.style.display = 'none'; return; }

                    const selectedOption = e.target.options[e.target.options.selectedIndex];
                    document.getElementById('lbl_profesi').innerText = selectedOption.getAttribute('data-profesi').toUpperCase();
                    
                    const lokasi = selectedOption.getAttribute('data-ruangan') !== '-' 
                                   ? selectedOption.getAttribute('data-ruangan') 
                                   : selectedOption.getAttribute('data-instansi');
                    
                    document.getElementById('lbl_lokasi').innerText = lokasi.toUpperCase();
                    detailBox.style.display = 'block';

                    // --- CEK DUPLIKASI ABSENSI DISINI ---
                    const isMasuk = selectedOption.getAttribute('data-is-masuk') === '1';
                    const isPulang = selectedOption.getAttribute('data-is-pulang') === '1';
                    const tipeAbsen = "{{ $kegiatan->tipe_absen }}";
                    
                    let statusHtml = '';
                    let disableSubmit = false;
                    let btnText = 'Kirim Presensi <i class="bi bi-arrow-right-circle-fill"></i>';
                    let btnBg = 'var(--maroon)';

                    if (tipeAbsen === 'masuk_saja') {
                        if (isMasuk) {
                            statusHtml = '<div class="alert alert-warning py-2 mt-3 mb-0" style="font-size: 0.8rem; border:none;"><i class="bi bi-info-circle-fill"></i> Anda sudah menyelesaikan presensi HARI INI.</div>';
                            disableSubmit = true;
                        } else {
                            statusHtml = '<div class="alert alert-success py-2 mt-3 mb-0" style="font-size: 0.8rem; border:none;"><i class="bi bi-check-circle-fill"></i> Anda belum presensi. Silakan isi form.</div>';
                        }
                    } else { // Jika Sistem Masuk & Keluar
                        if (isMasuk && !isPulang) {
                            statusHtml = '<div class="alert alert-warning py-2 mt-3 mb-0" style="font-size: 0.8rem; border:none; background: #fffbeb; color: #b45309;"><i class="bi bi-info-circle-fill"></i> Anda sudah absen MASUK. Silakan lakukan absen PULANG.</div>';
                            btnText = 'Kirim Absen PULANG <i class="bi bi-box-arrow-right"></i>';
                            btnBg = '#d97706'; // Ubah warna tombol jadi Orange (Pulang)
                        } else if (isPulang) {
                            statusHtml = '<div class="alert alert-success py-2 mt-3 mb-0" style="font-size: 0.8rem; border:none;"><i class="bi bi-check-all"></i> Anda sudah menyelesaikan absen masuk & pulang hari ini.</div>';
                            disableSubmit = true;
                        } else {
                            statusHtml = '<div class="alert alert-info py-2 mt-3 mb-0" style="font-size: 0.8rem; border:none;"><i class="bi bi-door-open-fill"></i> Silakan lakukan absen MASUK.</div>';
                        }
                    }

                    // Terapkan ke tampilan dan tombol
                    document.getElementById('status_absen_info').innerHTML = statusHtml;
                    const btnSubmit = document.querySelector('.btn-kirim');
                    btnSubmit.disabled = disableSubmit;
                    btnSubmit.innerHTML = btnText;
                    btnSubmit.style.background = disableSubmit ? '#cbd5e1' : btnBg;
                });
            }

            // --- 2. LOGIKA KOMPRESI GAMBAR OTOMATIS ---
            const fotoInput = document.getElementById('foto_bukti');
            const uploadArea = document.getElementById('upload_area');

            if (fotoInput && uploadArea) {
                fotoInput.addEventListener('change', async function() {
                    if(this.files && this.files.length > 0) {
                        const imageFile = this.files[0];
                        
                        if (!imageFile.type.startsWith('image/')) {
                            document.getElementById('upload_title').innerText = 'Format File Salah!';
                            document.getElementById('file_name').innerHTML = '<span class="text-danger">Harap pilih file gambar/foto.</span>';
                            return;
                        }

                        document.getElementById('upload_title').innerText = 'Mengompres Foto...';
                        document.getElementById('file_name').innerText = 'Mohon tunggu sebentar...';

                        const options = {
                            maxSizeMB: 0.3,          
                            maxWidthOrHeight: 1024,  
                            useWebWorker: false 
                        }

                        try {
                            const compressedFile = await imageCompression(imageFile, options);
                            
                            const dataTransfer = new DataTransfer();
                            dataTransfer.items.add(new File([compressedFile], imageFile.name, {
                                type: imageFile.type
                            }));
                            fotoInput.files = dataTransfer.files;

                            uploadArea.classList.add('uploaded');
                            document.getElementById('upload_icon').className = 'bi bi-check-circle-fill';
                            document.getElementById('upload_title').innerText = 'Foto Berhasil Dikompres';
                            document.getElementById('file_name').innerHTML = `<strong>${imageFile.name} (${(compressedFile.size / 1024).toFixed(1)} KB)</strong>`;
                            
                            document.getElementById('foto_error_msg').style.display = 'none';

                        } catch (error) {
                            console.error("Detail Error:", error);
                            document.getElementById('upload_title').innerText = 'Gagal Kompres Foto';
                            document.getElementById('file_name').innerHTML = `<span class="text-danger">${error.message || 'Terjadi kesalahan sistem.'}</span>`;
                            document.getElementById('upload_icon').className = 'bi bi-exclamation-triangle-fill text-danger';
                        }
                    }
                });
            }
        });
    </script>
</body>
</html>