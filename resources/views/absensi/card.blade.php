<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sindikat - {{ $mahasiswa->nm_mahasiswa }}</title>
    
    {{-- Fonts & Libraries --}}
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --primary: #8E1616;
            --bg-body: #F3F4F6;
            --surface: #FFFFFF;
            --text-main: #1F2937;
            --text-sub: #6B7280;
            --success: #10B981;
            --danger: #EF4444;
            --shadow-card: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        * { box-sizing: border-box; -webkit-tap-highlight-color: transparent; }
        
        body {
            font-family: 'DM Sans', sans-serif;
            background-color: #e5e5e5;
            margin: 0; padding: 0;
            display: flex; justify-content: center;
            height: 100vh;
            overflow: hidden;
        }

        .mobile-frame {
            width: 100%; max-width: 480px;
            background: var(--bg-body);
            height: 100%;
            display: flex; flex-direction: column;
            position: relative;
            box-shadow: 0 0 50px rgba(0,0,0,0.1);
        }

        /* ========================================= */
        /* 1. BAGIAN ATAS (STICKY / DIAM)            */
        /* ========================================= */
        .pinned-section {
            flex-shrink: 0;
            background: var(--bg-body);
            z-index: 20;
            max-height: 75vh; 
            overflow-y: auto;
            scrollbar-width: none;
        }
        .pinned-section::-webkit-scrollbar { display: none; }

        .top-bar {
            padding: 15px 20px;
            display: flex; justify-content: space-between; align-items: center;
            background: var(--bg-body);
        }
        .btn-icon {
            width: 38px; height: 38px; border-radius: 12px;
            background: white; border: 1px solid #e5e7eb;
            color: var(--text-main); font-size: 1.1rem;
            display: flex; align-items: center; justify-content: center;
            text-decoration: none; box-shadow: 0 2px 5px rgba(0,0,0,0.03);
        }

        /* --- Sapaan & Animasi Awan --- */
        .greeting-wrapper {
            padding: 5px 20px 15px;
            position: relative;
            overflow: hidden;
        }
        .greeting-text {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--text-main);
            margin: 0;
            position: relative;
            z-index: 2;
        }
        .greeting-sub {
            font-size: 0.85rem;
            color: var(--text-sub);
            position: relative;
            z-index: 2;
        }
        
        .cloud-anim {
            position: absolute;
            color: rgba(142, 22, 22, 0.08); /* Awan sewarna merah primary, tapi sangat transparan */
            z-index: 1;
            animation: floatCloud linear infinite;
        }
        .c1 { font-size: 2.5rem; top: -5px; left: -50px; animation-duration: 22s; animation-delay: 0s; }
        .c2 { font-size: 1.8rem; top: 15px; left: -100px; animation-duration: 28s; animation-delay: 5s; }
        .c3 { font-size: 3rem; top: -10px; left: -80px; animation-duration: 25s; animation-delay: 12s; }

        @keyframes floatCloud {
            0% { transform: translateX(0); opacity: 0; }
            10% { opacity: 1; }
            90% { opacity: 1; }
            100% { transform: translateX(500px); opacity: 0; }
        }

        /* ID Card */
        .card-container { padding: 5px 20px 20px; }
        .digital-id {
            background: linear-gradient(135deg, var(--primary), #5a0b0e);
            border-radius: 24px; padding: 20px; color: white;
            position: relative; overflow: hidden; box-shadow: var(--shadow-card);
        }
        .digital-id::before {
            content: ''; position: absolute; top: -50%; left: -50%; width: 200%; height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 60%);
            pointer-events: none;
        }
        .id-header { display: flex; justify-content: space-between; margin-bottom: 15px; position: relative; z-index: 2; }
        .chip {
            background: rgba(255,255,255,0.2); backdrop-filter: blur(4px);
            padding: 5px 10px; border-radius: 20px; font-size: 0.7rem; font-weight: 500;
            display: flex; align-items: center; gap: 5px;
        }
        .id-body { display: flex; gap: 12px; align-items: center; position: relative; z-index: 2; }
        .avatar {
            width: 56px; height: 56px; border-radius: 16px;
            background: white; border: 2px solid rgba(255,255,255,0.3); object-fit: cover;
        }
        .info h2 { font-size: 1.1rem; font-weight: 700; margin: 0 0 2px; }
        .info p { font-size: 0.8rem; margin: 0; opacity: 0.9; }
        
        .id-footer {
            margin-top: 15px; padding-top: 12px; border-top: 1px solid rgba(255,255,255,0.15);
            display: flex; justify-content: space-between; position: relative; z-index: 2;
        }
        .stat-item label { display: block; font-size: 0.65rem; text-transform: uppercase; opacity: 0.7; margin-bottom: 2px; }
        .stat-item span { font-weight: 700; font-size: 0.9rem; }

        /* Action Area (Map & Button) */
        .action-area { padding: 0 20px 20px; }
        .map-card {
            height: 140px; border-radius: 18px; overflow: hidden;
            position: relative; margin-bottom: 12px;
            border: 2px solid #fff; box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .gps-status {
            position: absolute; bottom: 10px; left: 10px; z-index: 400;
            background: rgba(255,255,255,0.95); padding: 5px 10px;
            border-radius: 10px; font-size: 0.7rem; font-weight: 700;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1); display: flex; align-items: center; gap: 5px;
        }
        .btn-swipe {
            width: 100%; border: none; padding: 16px; border-radius: 18px;
            font-size: 1rem; font-weight: 700; color: white; cursor: pointer;
            display: flex; justify-content: center; align-items: center; gap: 8px;
            box-shadow: 0 8px 20px -5px rgba(0,0,0,0.2); transition: 0.2s;
        }
        .btn-swipe:active { transform: scale(0.97); }
        .btn-swipe:disabled { opacity: 0.6; filter: grayscale(1); cursor: not-allowed; }
        .grad-green { background: linear-gradient(135deg, #059669, #10B981); }
        .grad-red { background: linear-gradient(135deg, #B91C1C, #EF4444); }

        /* Alert Box */
        .alert-box {
            margin: 0 20px 15px; padding: 12px; border-radius: 12px; font-size: 0.8rem;
            display: flex; align-items: center; gap: 10px;
        }
        .alert-red { background: #FEF2F2; border: 1px solid #FECACA; color: #991B1B; }
        .alert-yellow { background: #FFFBEB; border: 1px solid #FCD34D; color: #92400E; }

        /* ========================================= */
        /* 2. BAGIAN BAWAH (SCROLLABLE)              */
        /* ========================================= */
        .scrollable-section {
            flex-grow: 1;
            overflow-y: auto;
            background: white;
            border-top-left-radius: 28px;
            border-top-right-radius: 28px;
            box-shadow: 0 -5px 20px rgba(0,0,0,0.03);
            padding: 25px 20px 100px;
            position: relative;
            margin-top: -10px; 
            z-index: 10;
        }

        .section-title { font-size: 0.9rem; font-weight: 700; color: var(--text-main); margin-bottom: 15px; }
        
        .h-item {
            display: flex; align-items: center; gap: 12px;
            padding: 14px 0; border-bottom: 1px solid #F3F4F6;
        }
        .h-item:last-child { border-bottom: none; }
        .h-icon {
            width: 38px; height: 38px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center; font-size: 1.1rem; flex-shrink: 0;
        }
        .bg-in { background: #DCFCE7; color: #166534; }
        .bg-out { background: #FEE2E2; color: #991B1B; }
        .h-title { font-weight: 700; font-size: 0.9rem; }
        .h-sub { font-size: 0.75rem; color: var(--text-sub); }
        .h-time { font-weight: 700; font-size: 0.85rem; color: var(--text-main); }

        /* Bottom Nav */
        .bottom-nav {
            position: absolute; bottom: 20px; left: 50%; transform: translateX(-50%);
            background: rgba(255,255,255,0.9); backdrop-filter: blur(10px);
            padding: 12px 25px; border-radius: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            display: flex; gap: 35px; align-items: center; z-index: 100;
            border: 1px solid rgba(0,0,0,0.05);
        }
        .nav-item { font-size: 1.4rem; color: #9CA3AF; transition: 0.2s; }
        .nav-item.active { color: var(--primary); transform: translateY(-2px); }

        /* Loading Overlay */
        .loader {
            position: absolute; inset: 0; background: var(--bg-body); z-index: 200;
            display: flex; flex-direction: column; align-items: center; justify-content: center;
        }
    </style>
</head>
<body>

<div class="mobile-frame">
    
    {{-- A. BAGIAN STICKY (HEADER + ID + ABSEN) --}}
    <div class="pinned-section">
        
        {{-- Header --}}
        <div class="top-bar">
            <a href="#" onclick="history.back()" class="btn-icon">
                <i class="bi bi-chevron-left"></i>
            </a>
            <div style="font-weight: 700; color: var(--primary);">Absensi {{ ucfirst($mahasiswa->status) }}</div>
            <a href="#" onclick="location.reload()" class="btn-icon">
                <i class="bi bi-arrow-clockwise"></i>
            </a>
        </div>

        {{-- Sapaan Dinamis & Awan Animasi --}}
        <div class="greeting-wrapper">
            <i class="bi bi-cloud-fill cloud-anim c1"></i>
            <i class="bi bi-clouds-fill cloud-anim c2"></i>
            <i class="bi bi-cloud-fill cloud-anim c3"></i>
            
            <h1 class="greeting-text" id="greeting-msg">Halo,</h1>
            <div class="greeting-sub">Sudah siap bertugas hari ini?</div>
        </div>

        {{-- ID Card --}}
        <div class="card-container">
            <div class="digital-id">
                <div class="id-header">
                    <div class="chip">
                        <i class="bi bi-activity"></i> {{ ucfirst($mahasiswa->status) }}
                    </div>
                    <div class="chip">
                        <i class="bi bi-building"></i> {{ $ruangan ? $ruangan->nm_ruangan : '-' }}
                    </div>
                </div>
                <div class="id-body">
                    <img src="{{ $mahasiswa->foto_path ? asset($mahasiswa->foto_path) : 'https://ui-avatars.com/api/?name='.urlencode($mahasiswa->nm_mahasiswa).'&background=fff&color=7c1316' }}" class="avatar">
                    <div class="info">
                        <h2>{{ \Illuminate\Support\Str::limit($mahasiswa->nm_mahasiswa, 18) }}</h2>
                        <p>{{ $mahasiswa->univ_asal }}</p>
                    </div>
                </div>
                <div class="id-footer">
                    <div class="stat-item">
                        <label>Jadwal</label>
                        <span>{{ $scheduleInfo }}</span>
                    </div>
                    <div class="stat-item" style="text-align: right;">
                        <label>Status</label>
                        <span>{{ $absenHariIni && $absenHariIni->type == 'masuk' ? 'Sedang Kerja' : 'Belum Masuk' }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Error / Info Device --}}
        @if($error_state)
            <div class="alert-box alert-red">
                <i class="bi bi-shield-exclamation fs-5"></i>
                <div><strong>Akses Dibatasi</strong><br>{{ $error_state }}</div>
            </div>
        @endif

        {{-- Action UI (Dynamic) --}}
        <div class="action-area">
            
            {{-- 1. Register Device --}}
            <div id="device-register-ui" class="alert-box alert-yellow" style="display: none; flex-direction:column; text-align:center;">
                <i class="bi bi-phone-vibrate fs-1 text-warning"></i>
                <div>
                    <strong>Device Belum Terdaftar</strong><br>
                    Kunci akun Anda di perangkat ini agar aman.
                </div>
                <button onclick="registerCurrentDevice()" style="background:#F59E0B; border:none; color:white; padding:8px 20px; border-radius:8px; width:100%; font-weight:700;">Kunci Perangkat</button>
            </div>

            {{-- 2. Device Mismatch --}}
            <div id="device-mismatch-ui" class="alert-box alert-red" style="display: none;">
                <i class="bi bi-shield-x fs-1"></i>
                <div><strong>Perangkat Salah!</strong><br>Gunakan perangkat yang pertama kali didaftarkan.</div>
            </div>

            {{-- 3. Attendance Ready --}}
            <div id="attendance-ui" style="display: none;">
                @if(!$dispensasiAktif) 
                    
                    {{-- Map --}}
                    <div class="map-card">
                        <div id="location-map" style="width: 100%; height: 100%;"></div>
                        <div class="gps-status" id="gps-status-text">
                            <span class="spinner-border spinner-border-sm text-secondary" style="width:10px;height:10px;"></span>
                            <span style="margin-left:5px;">Cari GPS...</span>
                        </div>
                    </div>

                    {{-- Form Absen --}}
                    <form id="absen-form" action="{{ route('absensi.toggle', $mahasiswa->share_token) }}" method="POST">
                        @csrf
                        <input type="hidden" name="lat" id="geo-lat">
                        <input type="hidden" name="lng" id="geo-lng">
                        <input type="hidden" name="acc" id="geo-acc">
                        <input type="hidden" name="device_id" id="input-device-id">

                        @php 
                            $btnState = ($absenHariIni && $absenHariIni->type === 'masuk') ? 'keluar' : 'masuk'; 
                        @endphp

                        @if($btnState == 'keluar')
                            <button type="submit" class="btn-swipe grad-red" id="btn-submit" disabled>
                                <i class="bi bi-box-arrow-right fs-4"></i> Checkout Pulang
                            </button>
                        @else
                            <button type="submit" class="btn-swipe grad-green" id="btn-submit" disabled>
                                <i class="bi bi-fingerprint fs-4"></i> Absen Masuk
                            </button>
                        @endif
                    </form>

                    {{-- Lupa absen pulang kini lewat: Dashboard Magang > Izin Baru > Dispensasi Lupa Pulang (perlu ACC Karu) --}}

                @else
                    {{-- Tampilan Jika Sedang Izin (Dispensasi) --}}
                    <div style="background: #FFF1F2; border:1px solid #FECACA; border-radius:16px; padding:20px; text-align:center;">
                        <i class="bi bi-calendar2-check fs-1 text-danger"></i>
                        <h4 style="margin:10px 0 5px; color:#991B1B;">Sedang Izin</h4>
                        <p style="margin:0; font-size:0.85rem; color:#7F1D1D;">{{ $dispensasiAktif->keterangan }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- B. BAGIAN BAWAH (SCROLLABLE HISTORY) --}}
    <div class="scrollable-section">
        <div class="section-title">Riwayat Aktivitas</div>
        
        @forelse($riwayat as $log)
            <div class="h-item">
                <div class="h-icon {{ $log->type == 'masuk' ? 'bg-in' : 'bg-out' }}">
                    <i class="bi {{ $log->type == 'masuk' ? 'bi-arrow-down-right' : 'bi-arrow-up-right' }}"></i>
                </div>
                <div class="h-info">
                    <div class="h-title">{{ $log->type == 'masuk' ? 'Absen Masuk' : 'Absen Pulang' }}</div>
                    <div class="h-sub">{{ \Carbon\Carbon::parse($log->created_at)->translatedFormat('l, d F') }}</div>
                </div>
                <div class="h-time">
                    {{ $log->type == 'masuk' ? 
                        ($log->jam_masuk ? \Carbon\Carbon::parse($log->jam_masuk)->format('H:i') : '-') : 
                        ($log->jam_keluar ? \Carbon\Carbon::parse($log->jam_keluar)->format('H:i') : '-') 
                    }}
                </div>
            </div>
        @empty
            <div style="text-align: center; padding: 30px; color: var(--text-sub); font-size: 0.85rem;">
                <i class="bi bi-clock-history fs-1"></i><br>Belum ada riwayat.
            </div>
        @endforelse
    </div>

    {{-- BOTTOM NAV --}}
    <div class="bottom-nav">
        <a href="#" onclick="history.back()" class="nav-item">
            <i class="bi bi-grid-fill"></i>
        </a>
        <a href="#" class="nav-item active">
            <i class="bi bi-qr-code-scan"></i>
        </a>
        <a href="#" onclick="location.reload()" class="nav-item">
            <i class="bi bi-arrow-repeat"></i>
        </a>
    </div>

    {{-- LOADER OVERLAY --}}
    <div id="loader" class="loader">
        <div class="spinner-border text-danger" role="status"></div>
        <p style="margin-top: 15px; font-size: 0.85rem; color: #666;">Memuat...</p>
    </div>

</div>

{{-- SCRIPT --}}
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>

// --- Set Sapaan Dinamis Berdasarkan Jam (Lokal User) ---
function setGreeting() {
    const hour = new Date().getHours();
    let greeting = 'Selamat Malam';
    
    if (hour >= 4 && hour < 11) {
        greeting = 'Selamat Pagi';
    } else if (hour >= 11 && hour < 15) {
        greeting = 'Selamat Siang';
    } else if (hour >= 15 && hour < 18) {
        greeting = 'Selamat Sore';
    }
    
    document.getElementById('greeting-msg').innerText = greeting + ',';
}

// --- Device ID ---
function isInAppBrowser() {
    const ua = navigator.userAgent || navigator.vendor || window.opera;
    return (ua.indexOf("WhatsApp") > -1) || 
           (ua.indexOf("Line") > -1) || 
           (ua.indexOf("Instagram") > -1) || 
           (ua.indexOf("FBAN") > -1) || 
           (ua.indexOf("FBAV") > -1);
}

function setCookie(name, value, days) {
    let expires = "";
    if (days) {
        const date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (value || "")  + expires + "; path=/; secure; samesite=strict";
}

function getCookie(name) {
    const nameEQ = name + "=";
    const ca = document.cookie.split(';');
    for(let i=0;i < ca.length;i++) {
        let c = ca[i];
        while (c.charAt(0) == ' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}

function getDeviceId() {
    const deviceKey = 'sindikat_device_id';
    let id = localStorage.getItem(deviceKey);

    if (!id) {
        id = getCookie(deviceKey);
        if (id) localStorage.setItem(deviceKey, id);
    }

    if (!id) {
        id = 'dev-' + Math.random().toString(36).substr(2, 9) + Date.now().toString(36);
        localStorage.setItem(deviceKey, id);
        setCookie(deviceKey, id, 365);
    }

    return id;
}

document.addEventListener("DOMContentLoaded", function() {
    
    setGreeting(); // Panggil fungsi greeting saat load

    if (isInAppBrowser()) {
        Swal.fire({
            title: 'Buka di Browser Asli!',
            text: 'Untuk menghindari error "Perangkat Salah", mohon klik ikon 3 titik di pojok kanan atas dan pilih "Buka di Chrome/Safari".',
            icon: 'warning',
            confirmButtonColor: '#8E1616', 
            confirmButtonText: 'Mengerti'
        });
    }
});

    const clientDeviceId = getDeviceId();
    const serverDeviceStatus = "{{ $deviceStatus }}"; 
    const dbDeviceId = "{{ $user->device_id ?? '' }}";

    // --- Init ---
    document.addEventListener("DOMContentLoaded", function() {
        const loader = document.getElementById('loader');
        const regUI = document.getElementById('device-register-ui');
        const mismatchUI = document.getElementById('device-mismatch-ui');
        const attendanceUI = document.getElementById('attendance-ui');
        const inputDeviceId = document.getElementById('input-device-id');

        setTimeout(() => {
            loader.style.display = 'none';
            
            if (serverDeviceStatus === 'need_register') {
                regUI.style.display = 'flex';
            } else {
                if (dbDeviceId === clientDeviceId) {
                    attendanceUI.style.display = 'block';
                    if(inputDeviceId) inputDeviceId.value = clientDeviceId;
                    initMap();
                } else {
                    mismatchUI.style.display = 'flex';
                }
            }
        }, 600);
    });

    // --- Register ---
    function registerCurrentDevice() {
        Swal.fire({
            title: 'Kunci Perangkat?',
            text: "Akun ini akan terkunci di perangkat ini selamanya.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#F59E0B',
            confirmButtonText: 'Ya, Kunci!',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                // MODIFIKASI TERAPLIKASI DI SINI: Menyertakan param $mahasiswa->share_token ke url route
                return fetch("{{ route('absensi.register_device', $mahasiswa->share_token) }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ device_id: clientDeviceId })
                }).then(res => res.json()).catch(err => Swal.showValidationMessage(`Gagal: ${err}`));
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire('Berhasil', 'Perangkat berhasil didaftarkan.', 'success').then(() => location.reload());
            }
        });
    }

    // --- Helper: Hitung Jarak (Haversine Formula) ---
    function calculateDistance(lat1, lon1, lat2, lon2) {
        const R = 6371e3; 
        const p1 = lat1 * Math.PI/180;
        const p2 = lat2 * Math.PI/180;
        const dp = (lat2-lat1) * Math.PI/180;
        const dl = (lon2-lon1) * Math.PI/180;

        const a = Math.sin(dp/2) * Math.sin(dp/2) +
                  Math.cos(p1) * Math.cos(p2) *
                  Math.sin(dl/2) * Math.sin(dl/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        return R * c; 
    }

function initMap() {
    if(!document.getElementById('location-map')) return;

    const RSUD_LAT = -7.82159559;
    const RSUD_LNG = 112.05786417;
    const MAX_RADIUS = 350; 

    const map = L.map('location-map', { zoomControl: false, dragging: false }).setView([RSUD_LAT, RSUD_LNG], 16);
    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png').addTo(map);
    L.circle([RSUD_LAT, RSUD_LNG], { radius: MAX_RADIUS, color: 'none', fillColor: '#10B981', fillOpacity: 0.15 }).addTo(map);
    
    const userMarker = L.marker([RSUD_LAT, RSUD_LNG]).addTo(map);
    const btn = document.getElementById('btn-submit');
    const statusTxt = document.getElementById('gps-status-text');

    const geoOptions = {
        enableHighAccuracy: true,
        timeout: 20000,           
        maximumAge: 3000          
    };

    function success(pos) {
        const lat = pos.coords.latitude;
        const lng = pos.coords.longitude;
        const acc = Math.round(pos.coords.accuracy);
        const distance = Math.round(calculateDistance(lat, lng, RSUD_LAT, RSUD_LNG));

        document.getElementById('geo-lat').value = lat;
        document.getElementById('geo-lng').value = lng;
        document.getElementById('geo-acc').value = acc;

        userMarker.setLatLng([lat, lng]);
        map.setView([lat, lng], 17);

        if (acc > 200) { 
            statusTxt.innerHTML = `<span class="text-warning"><i class="bi bi-broadcast"></i> Mencari sinyal stabil (${acc}m)...</span>`;
        } else if (distance <= MAX_RADIUS) {
            statusTxt.innerHTML = `<span class="text-success"><i class="bi bi-geo-alt-fill"></i> Area Terdeteksi (${distance}m)</span>`;
            btn.disabled = false;
        } else {
            statusTxt.innerHTML = `<span class="text-danger"><i class="bi bi-x-octagon-fill"></i> Di Luar Area (${distance}m)</span>`;
            btn.disabled = true;
        }
    }

    function error(err) {
        console.warn(`ERROR(${err.code}): ${err.message}`);
        let errMsg = "Klik 'Izinkan Lokasi' di browser";
        
        if(err.code === 1) errMsg = "Izin lokasi ditolak. Cek setelan browser.";
        if(err.code === 2) errMsg = "Sinyal GPS hilang. Coba ke area terbuka.";
        if(err.code === 3) errMsg = "Gagal mengunci lokasi (Timeout).";

        statusTxt.innerHTML = `<span class="text-danger"><i class="bi bi-shield-x"></i> ${errMsg}</span>`;
        btn.disabled = true;
    }

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(success, error, geoOptions);
        navigator.geolocation.watchPosition(success, error, geoOptions);
    } else {
        statusTxt.innerHTML = `<span class="text-danger">Browser tidak mendukung GPS</span>`;
    }
}

@if(session('success')) 
    Swal.fire({ icon: 'success', title: 'Berhasil', text: '{{ session('success') }}', timer: 2000, showConfirmButton: false }); 
@endif

@if(session('error'))
    (function() {
        const errMsg = "{{ session('error') }}";
        const isLate = errMsg.includes('Terlambat'); // Deteksi pesan error terlambat

        Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: errMsg,
            confirmButtonColor: '#8E1616',
            confirmButtonText: 'OK',
            // Jika terlambat, tampilkan tombol tambahan
            showCancelButton: isLate, 
            cancelButtonText: isLate ? 'Buat Dispen Terlambat' : '',
            cancelButtonColor: '#F59E0B', // Warna kuning (disesuaikan dengan tema UI Anda)
            reverseButtons: true
        }).then((result) => {
            // Jika tombol 'Buat Dispen Terlambat' diklik
            if (result.dismiss === Swal.DismissReason.cancel) {
                window.location.href = "{{ route('mahasiswa.dispensasi.create') }}";
            }
        });
    })();
@endif
</script>

</body>
</html>