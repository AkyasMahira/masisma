@extends('layouts.public')

@section('title', 'Absensi')

@section('content')
    <style>
        :root {
            --maroon: #7c1316;
            --maroon-soft: #b83236;
            --maroon-soft2: #e05959;
            --ink: #111827;
            --muted: #6b7280;
            --border-soft: #e5e7eb;
            --bg-soft: #f9fafb;
        }

        body{
            min-height:100vh;
            margin:0;
            padding:24px 16px;
            font-family:"Inter",system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;
            background:
                radial-gradient(900px 520px at top, rgba(124,19,22,0.12), transparent 70%),
                radial-gradient(720px 420px at bottom, rgba(220,38,38,0.08), transparent 70%),
                #ffffff;
            display:flex;
            align-items:center;
            justify-content:center;
            position:relative;
        }

        .absen-wrapper{width:100%;max-width:460px;position:relative;z-index:1;}

        .rsud-logo-page{
            position:fixed;right:-60px;bottom:-40px;width:260px;
            opacity:0.12;pointer-events:none;user-select:none;z-index:0;
        }
        .rsud-logo-page img{width:100%;height:auto;object-fit:contain;}

        .absen-shell{
            padding:1.4px;border-radius:22px;
            background:linear-gradient(135deg,
                rgba(124,19,22,0.12),
                rgba(248,113,113,0.18),
                rgba(148,163,184,0.16));
            box-shadow:0 18px 40px rgba(15,23,42,0.14);
            animation:fadeIn .4s ease-out;
        }

        .absen-card{
            position:relative;overflow:hidden;border-radius:20px;
            background:linear-gradient(180deg,#ffffff 0%,#fff5f5 50%,#ffffff 100%);
            padding:22px 18px 18px;color:var(--ink);
            box-shadow:0 10px 30px rgba(15,23,42,0.12),0 0 0 1px rgba(229,231,235,0.9);
        }
        .absen-content{position:relative;z-index:1;}

        .avatar-wrap{
            display:flex;flex-direction:column;align-items:center;gap:6px;
            margin-bottom:12px;margin-top:-4px;
        }
        .avatar-wrap img{
            width:147px;height:auto;object-fit:contain;
            filter:drop-shadow(0 12px 22px rgba(15,23,42,0.22));
        }
        .avatar-caption{
            font-size:.78rem;text-transform:uppercase;letter-spacing:.12em;color:var(--muted);
        }

        .info-main{text-align:center;margin-bottom:10px;}
        .info-main h4{margin:0;font-size:1.15rem;font-weight:700;color:var(--ink);}
        .info-main p{margin:2px 0 0;font-size:.82rem;color:var(--muted);}
        .info-main p i{font-size:.95rem;color:var(--maroon-soft);margin-right:4px;}

        .chip-row{
            display:flex;flex-wrap:wrap;justify-content:center;gap:6px;margin-top:12px;
        }
        .chip{
            font-size:.78rem;padding:6px 10px;border-radius:999px;
            background:#f9fafb;color:var(--muted);
            display:inline-flex;align-items:center;gap:6px;
            border:1px solid rgba(148,163,184,0.6);
        }
        .chip i{font-size:.9rem;color:var(--maroon-soft);}
        .chip-status-aktif{
            background:linear-gradient(135deg,#ecfdf3,#dcfce7);
            color:#166534;border-color:rgba(22,101,52,0.35);
        }
        .chip-status-aktif i{color:#16a34a;}
        .chip-status-nonaktif{
            background:linear-gradient(135deg,#f9fafb,#e5e7eb);
            color:#4b5563;border-color:rgba(148,163,184,0.7);
        }

        .absen-divider{
            margin:16px 0 12px;height:1px;
            background:linear-gradient(90deg,
                rgba(148,163,184,0),
                rgba(148,163,184,0.8),
                rgba(148,163,184,0));
        }

        /* LOKASI + MAP */
        .location-info{margin-bottom:14px;}
        .location-title{
            font-size:.78rem;font-weight:600;color:var(--muted);
            margin-bottom:4px;letter-spacing:.06em;text-transform:uppercase;
        }
        .location-ref{
            font-size:.7rem;color:var(--muted);margin-bottom:6px;
        }
        .location-map{
            height:220px;border-radius:14px;overflow:hidden;
            border:1px solid rgba(148,163,184,0.7);margin-bottom:6px;
        }
        .location-status-text{
            font-size:.75rem;color:var(--muted);
        }
        .location-status-text .badge-ok{color:#166534;font-weight:600;}
        .location-status-text .badge-no{color:#b91c1c;font-weight:600;}
        .location-status-text .badge-warn{color:#b45309;font-weight:600;}

        .absen-btn{
            width:100%;border-radius:14px;padding:13px 14px;
            font-weight:700;font-size:.98rem;border:none;
            background:linear-gradient(135deg,var(--maroon),var(--maroon-soft2));
            color:#fef2f2;letter-spacing:.02em;
            box-shadow:0 14px 30px rgba(124,19,22,0.5),0 0 0 1px rgba(248,113,113,0.5);
            display:inline-flex;align-items:center;justify-content:center;gap:8px;
            cursor:pointer;transition:transform .18s ease,box-shadow .18s ease,filter .18s ease;
        }
        .absen-btn i{font-size:1.1rem;}
        .absen-btn:hover{
            transform:translateY(-1px) scale(1.02);filter:brightness(1.04);
            box-shadow:0 16px 34px rgba(124,19,22,0.6),0 0 0 1px rgba(248,113,113,0.7);
        }
        .absen-btn:active,.absen-btn.touch-active{
            transform:translateY(0) scale(.97);
            box-shadow:0 8px 20px rgba(124,19,22,0.52),0 0 0 1px rgba(248,113,113,0.75);
        }

        .history-card{margin-top:16px;display:flex;justify-content:space-between;gap:10px;}
        .history-box{
            flex:1;border-radius:14px;padding:9px 10px 10px;text-align:center;
            border:1px solid rgba(148,163,184,0.7);background:#f9fafb;
            position:relative;overflow:hidden;
        }
        .history-box::before{
            content:"";position:absolute;inset:0;
            background:radial-gradient(circle at 0 0,rgba(248,113,113,0.16),transparent 60%);
            opacity:.85;pointer-events:none;
        }
        .history-box-inner{position:relative;z-index:1;}
        .history-box h6{
            margin:0;font-size:.78rem;font-weight:500;color:var(--muted);
            display:flex;align-items:center;justify-content:space-between;gap:4px;
        }
        .history-box h6 i{font-size:.9rem;color:var(--maroon-soft);}
        .history-box .time{font-size:1.15rem;font-weight:700;margin-top:5px;color:var(--ink);}
        .history-box.empty .time{color:rgba(148,163,184,0.9);}

        .absen-footer{
            margin-top:14px;display:flex;justify-content:space-between;align-items:center;gap:10px;
        }
        .footer-caption{font-size:.72rem;color:var(--muted);line-height:1.2;}
        .footer-mini{font-size:.7rem;color:rgba(148,163,184,0.95);text-align:right;}

        @keyframes fadeIn{from{opacity:0;transform:translateY(10px);}to{opacity:1;transform:translateY(0);}}

        @media(max-width:480px){
            body{padding:18px 10px;}
            .absen-card{padding:20px 14px 16px;}
            .info-main h4{font-size:1.05rem;}
            .history-box .time{font-size:1.05rem;}
            .footer-caption{font-size:.68rem;}
            .footer-mini{font-size:.66rem;}
            .location-map{height:200px;}
        }
    </style>

    {{-- Leaflet CSS --}}
    <link rel="stylesheet"
          href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
          crossorigin=""/>

    <div class="rsud-logo-page">
        <img src="https://rsudslg.kedirikab.go.id/asset_compro/img/logo/Logo.png" alt="Logo RSUD SLG">
    </div>

    <div class="absen-wrapper">
        <div class="absen-shell">
            <div class="absen-card">
                <div class="absen-content">
                    <div class="avatar-wrap">
                        <img src="{{ asset('icon.png') }}" alt="Maskot Rakun">
                        <div class="avatar-caption">ABSENSI MAHASISWA PRAKTIK</div>
                    </div>

                    <div class="info-main">
                        <h4>{{ $mahasiswa->nm_mahasiswa }}</h4>
                        <p>
                            <i class="bi bi-mortarboard-fill"></i>
                            {{ $mahasiswa->univ_asal }} • {{ $mahasiswa->prodi }}
                        </p>
                    </div>

                    @php $isAktif = strtolower($mahasiswa->status) === 'aktif'; @endphp
                    <div class="chip-row">
                        <div class="chip">
                            <i class="bi bi-door-open"></i>
                            <span>Ruangan: {{ $mahasiswa->ruangan->nm_ruangan ?? $mahasiswa->nm_ruangan }}</span>
                        </div>
                        <div class="chip {{ $isAktif ? 'chip-status-aktif' : 'chip-status-nonaktif' }}">
                            <i class="bi {{ $isAktif ? 'bi-activity' : 'bi-pause-circle' }}"></i>
                            <span>Status: {{ $mahasiswa->status }}</span>
                        </div>
                    </div>

                    <div class="absen-divider"></div>

                    {{-- STATUS LOKASI + MAP --}}
                    <div class="location-info">
                        <div class="location-title">Status Lokasi</div>
                        <div class="location-ref">
                            Titik referensi RSUD SLG: <strong>-7.8215986, 112.0578523</strong>
                        </div>
                        <div id="location-map" class="location-map"></div>
                        <div id="loc-status-text" class="location-status-text">
                            Mengambil lokasi... izinkan akses GPS di browser untuk menampilkan posisi kamu terhadap RSUD Simpang Lima Gumul.
                        </div>
                    </div>

                    {{-- FORM ABSEN --}}
                    <form id="absen-form" action="{{ route('absensi.toggle', $mahasiswa->share_token) }}" method="POST">
                        @csrf
                        <input type="hidden" name="lat" id="geo-lat">
                        <input type="hidden" name="lng" id="geo-lng">
                        <input type="hidden" name="acc" id="geo-acc">

                        <button type="submit" class="absen-btn btn-press">
                            <i class="bi bi-fingerprint"></i>
                            <span>Absen Hari Ini</span>
                        </button>
                    </form>

                    {{-- Riwayat Hari Ini --}}
                    <div class="history-card">
                        <div class="history-box {{ $absenHariIni && $absenHariIni->jam_masuk ? '' : 'empty' }}">
                            <div class="history-box-inner">
                                <h6><span>Masuk</span><i class="bi bi-box-arrow-in-right"></i></h6>
                                <div class="time">
                                    {{ $absenHariIni && $absenHariIni->jam_masuk ? $absenHariIni->jam_masuk->format('H:i') : '-' }}
                                </div>
                            </div>
                        </div>
                        <div class="history-box {{ $absenHariIni && $absenHariIni->jam_keluar ? '' : 'empty' }}">
                            <div class="history-box-inner">
                                <h6><span>Keluar</span><i class="bi bi-box-arrow-right"></i></h6>
                                <div class="time">
                                    {{ $absenHariIni && $absenHariIni->jam_keluar ? $absenHariIni->jam_keluar->format('H:i') : '-' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="absen-footer">
                        <div class="footer-caption">
                            <div>RSUD Simpang Lima Gumul</div>
                            <div>Kabupaten Kediri</div>
                        </div>
                        <div class="footer-mini">
                            Dibuat untuk kemudahan absensi<br>mahasiswa praktik
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- @push('scripts') --}}
        {{-- Leaflet JS --}}
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
                {{-- integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" --}}
                crossorigin=""></script>

        {{-- SweetAlert2 --}}
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const form = document.getElementById('absen-form');
                const btn = form ? form.querySelector('button[type="submit"]') : null;
                const statusEl = document.getElementById('loc-status-text');

                // Titik referensi RSUD SLG (real)
                const RSUD_LAT = -7.8215986;
                const RSUD_LNG = 112.0578523;
                const RADIUS_ALLOWED = 200;
                const MAX_ACC = 150;

                let map = null;
                let rsudMarker = null;
                let radiusCircle = null;
                let userMarker = null;

                function haversine(lat1, lon1, lat2, lon2) {
                    const R = 6371000;
                    const toRad = d => d * Math.PI / 180;
                    const dLat = toRad(lat2 - lat1);
                    const dLon = toRad(lon2 - lon1);
                    const a = Math.sin(dLat/2)**2 +
                        Math.cos(toRad(lat1))*Math.cos(toRad(lat2))*Math.sin(dLon/2)**2;
                    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
                    return R * c;
                }

                function initMapBase() {
                    const mapDiv = document.getElementById('location-map');
                    if (!mapDiv) return;
                    if (map) return;

                    map = L.map('location-map').setView([RSUD_LAT, RSUD_LNG], 18);

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '&copy; OpenStreetMap contributors'
                    }).addTo(map);

                    rsudMarker = L.marker([RSUD_LAT, RSUD_LNG]).addTo(map)
                        .bindPopup('RSUD Simpang Lima Gumul');

                    radiusCircle = L.circle([RSUD_LAT, RSUD_LNG], {
                        radius: RADIUS_ALLOWED,
                        color: '#7c1316',
                        weight: 1,
                        fillColor: '#f97373',
                        fillOpacity: 0.18
                    }).addTo(map);
                }

                function updateUserOnMap(lat, lng) {
                    initMapBase();
                    if (!map) return;

                    if (!userMarker) {
                        userMarker = L.marker([lat, lng]).addTo(map)
                            .bindPopup('Lokasi Kamu');
                    } else {
                        userMarker.setLatLng([lat, lng]);
                    }

                    const bounds = L.latLngBounds([
                        [RSUD_LAT, RSUD_LNG],
                        [lat, lng]
                    ]);
                    map.fitBounds(bounds, {padding:[20,20]});
                }

                function setHiddenLocation(lat, lng, acc) {
                    document.getElementById('geo-lat').value = lat;
                    document.getElementById('geo-lng').value = lng;
                    document.getElementById('geo-acc').value = Math.round(acc);
                }

                function handlePosition(position, forSubmit = false) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    const acc = position.coords.accuracy;

                    const dist = haversine(lat, lng, RSUD_LAT, RSUD_LNG);
                    const inRadius = dist <= RADIUS_ALLOWED;

                    updateUserOnMap(lat, lng);
                    setHiddenLocation(lat, lng, acc);

                    if (statusEl) {
                        if (acc > MAX_ACC) {
                            statusEl.innerHTML =
                                'Lokasi terdeteksi namun kurang akurat. <span class="badge-warn">Perbaiki sinyal GPS dan internet, lalu coba lagi.</span>';
                        } else if (inRadius) {
                            statusEl.innerHTML =
                                '<span class="badge-ok">Kamu berada di area RSUD Simpang Lima Gumul (radius 200 meter).</span>';
                        } else {
                            statusEl.innerHTML =
                                '<span class="badge-no">Kamu berada di luar radius 200 meter RSUD Simpang Lima Gumul.</span>';
                        }
                    }

                    if (forSubmit) {
                        if (acc > MAX_ACC) {
                            if (btn) { btn.disabled = false; btn.classList.remove('touch-active'); }
                            Swal.fire({
                                icon:'warning',
                                title:'Lokasi Kurang Akurat',
                                text:'Perbaiki sinyal GPS dan internet, lalu coba lagi di dekat area RSUD SLG.'
                            });
                            return;
                        }

                        if (!inRadius) {
                            if (btn) { btn.disabled = false; btn.classList.remove('touch-active'); }
                            Swal.fire({
                                icon:'error',
                                title:'Di Luar Area RSUD SLG',
                                text:'Absen harus dilakukan di area RSUD Simpang Lima Gumul (radius 200 meter).'
                            });
                            return;
                        }

                        form.submit();
                    }
                }

                function handleLocationError(error, context) {
                    let msg = 'Lokasi tidak tersedia. Aktifkan GPS dan izinkan akses lokasi di browser.';
                    if (error.code === error.PERMISSION_DENIED) {
                        msg = 'Izin lokasi ditolak. Aktifkan GPS dan izinkan akses lokasi untuk dapat melakukan absensi.';
                    } else if (error.code === error.POSITION_UNAVAILABLE) {
                        msg = 'Informasi lokasi tidak tersedia. Coba pindah ke area yang lebih terbuka atau cek sinyal.';
                    } else if (error.code === error.TIMEOUT) {
                        msg = 'Pengambilan lokasi terlalu lama. Pastikan GPS dan internet aktif, lalu coba lagi.';
                    }

                    if (statusEl) {
                        statusEl.innerHTML =
                            '<span class="badge-warn">Lokasi tidak tersedia. Aktifkan GPS & izinkan akses lokasi di browser.</span>';
                    }

                    if (context === 'submit') {
                        if (btn) { btn.disabled = false; btn.classList.remove('touch-active'); }
                        Swal.fire({
                            icon:'error',
                            title:'Lokasi Tidak Tersedia',
                            text:msg
                        });
                    }
                }

                // Map dasar selalu muncul di titik RSUD
                initMapBase();

                // Preview lokasi awal (auto minta GPS)
                if ('geolocation' in navigator) {
                    navigator.geolocation.getCurrentPosition(
                        pos => handlePosition(pos, false),
                        err => handleLocationError(err, 'preview'),
                        {enableHighAccuracy:true,timeout:10000,maximumAge:0}
                    );
                } else {
                    if (statusEl) {
                        statusEl.innerHTML =
                            '<span class="badge-warn">Perangkat tidak mendukung fitur lokasi. Absensi membutuhkan GPS aktif.</span>';
                    }
                }

                // Submit absensi
                if (form && btn) {
                    form.addEventListener('submit', function (e) {
                        e.preventDefault();

                        if (!('geolocation' in navigator)) {
                            Swal.fire({
                                icon:'error',
                                title:'Lokasi Tidak Didukung',
                                text:'Perangkat / browser tidak mendukung fitur lokasi. Absensi membutuhkan GPS aktif.'
                            });
                            return;
                        }

                        btn.disabled = true;
                        btn.classList.add('touch-active');

                        navigator.geolocation.getCurrentPosition(
                            pos => handlePosition(pos, true),
                            err => handleLocationError(err, 'submit'),
                            {enableHighAccuracy:true,timeout:10000,maximumAge:0}
                        );
                    });
                }

                // Pesan dari backend
                @if(session('success'))
                    Swal.fire({
                        icon:'success',
                        title:'Berhasil!',
                        text:'{{ session('success') }}',
                        showConfirmButton:false,
                        timer:2500
                    });
                @endif

                @if(session('error'))
                    Swal.fire({
                        icon:'warning',
                        title:'Perhatian!',
                        text:'{{ session('error') }}',
                        showConfirmButton:true
                    });
                @endif
            });
        </script>
    {{-- @endpush --}}
@endsection
