@extends('layouts.public') {{-- Layout tanpa sidebar --}}

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

        /* ===== Latar belakang halaman ===== */
        body {
            min-height: 100vh;
            margin: 0;
            padding: 24px 16px;
            font-family: "Inter", system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background:
                radial-gradient(900px 520px at top, rgba(124, 19, 22, 0.12), transparent 70%),
                radial-gradient(720px 420px at bottom, rgba(220, 38, 38, 0.08), transparent 70%),
                #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .absen-wrapper {
            width: 100%;
            max-width: 460px;
            position: relative;
            z-index: 1;
        }

        /* ===== Logo RSUD pojok kanan bawah, separo aja yang kelihatan ===== */
        .rsud-logo-page {
            position: fixed;
            right: -60px;
            /* geser keluar layar biar cuma separo kelihatan */
            bottom: -40px;
            /* sedikit turun ke bawah */
            width: 260px;
            /* agak lebih besar */
            opacity: 0.12;
            /* transparan tipis */
            pointer-events: none;
            user-select: none;
            z-index: 0;
        }

        .rsud-logo-page img {
            width: 100%;
            height: auto;
            object-fit: contain;
        }

        /* ===== Shell tipis gradasi merah di border ===== */
        .absen-shell {
            padding: 1.4px;
            border-radius: 22px;
            background:
                linear-gradient(135deg,
                    rgba(124, 19, 22, 0.12),
                    rgba(248, 113, 113, 0.18),
                    rgba(148, 163, 184, 0.16));
            box-shadow:
                0 18px 40px rgba(15, 23, 42, 0.14);
            animation: fadeIn 0.4s ease-out;
        }

        /* ===== Kartu utama (putih + gradasi merah tipis dari atas ke bawah) ===== */
        .absen-card {
            position: relative;
            overflow: hidden;
            border-radius: 20px;
            background: linear-gradient(180deg,
                    #ffffff 0%,
                    #fff5f5 50%,
                    #ffffff 100%);
            padding: 22px 18px 18px;
            color: var(--ink);
            box-shadow:
                0 10px 30px rgba(15, 23, 42, 0.12),
                0 0 0 1px rgba(229, 231, 235, 0.9);
            transform-style: preserve-3d;
            transform: perspective(900px);
            transition: transform 0.25s ease, box-shadow 0.25s ease, background 0.25s ease;
        }

        .absen-card.hover-active {
            box-shadow:
                0 18px 40px rgba(15, 23, 42, 0.2),
                0 0 0 1px rgba(220, 38, 38, 0.25);
        }

        /* ===== Konten di atas ===== */
        .absen-content {
            position: relative;
            z-index: 1;
        }

        /* ===== Gambar rakun langsung (tanpa avatar circle) ===== */
        .avatar-wrap {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            margin-bottom: 12px;
            margin-top: -4px;
        }

        .avatar-wrap img {
            width: 147px;
            height: auto;
            object-fit: contain;
            filter: drop-shadow(0 12px 22px rgba(15, 23, 42, 0.22));
        }

        .avatar-caption {
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: var(--muted);
        }

        /* ===== Info utama mahasiswa ===== */
        .info-main {
            text-align: center;
            margin-bottom: 10px;
        }

        .info-main h4 {
            margin: 0;
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--ink);
        }

        .info-main p {
            margin: 2px 0 0;
            font-size: 0.82rem;
            color: var(--muted);
        }

        .info-main p i {
            font-size: 0.95rem;
            color: var(--maroon-soft);
            margin-right: 4px;
        }

        /* ===== Chips ===== */
        .chip-row {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 6px;
            margin-top: 12px;
        }

        .chip {
            font-size: 0.78rem;
            padding: 6px 10px;
            border-radius: 999px;
            background: #f9fafb;
            color: var(--muted);
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: 1px solid rgba(148, 163, 184, 0.6);
        }

        .chip i {
            font-size: 0.9rem;
            color: var(--maroon-soft);
        }

        .chip-status-aktif {
            background: linear-gradient(135deg, #ecfdf3, #dcfce7);
            color: #166534;
            border-color: rgba(22, 101, 52, 0.35);
        }

        .chip-status-aktif i {
            color: #16a34a;
        }

        .chip-status-nonaktif {
            background: linear-gradient(135deg, #f9fafb, #e5e7eb);
            color: #4b5563;
            border-color: rgba(148, 163, 184, 0.7);
        }

        /* ===== Garis pemisah tipis ===== */
        .absen-divider {
            margin: 16px 0 12px;
            height: 1px;
            background: linear-gradient(90deg,
                    rgba(148, 163, 184, 0),
                    rgba(148, 163, 184, 0.8),
                    rgba(148, 163, 184, 0));
        }

        /* ===== Tombol absen ===== */
        .absen-btn {
            width: 100%;
            border-radius: 14px;
            padding: 13px 14px;
            font-weight: 700;
            font-size: 0.98rem;
            border: none;
            background: linear-gradient(135deg, var(--maroon), var(--maroon-soft2));
            color: #fef2f2;
            letter-spacing: 0.02em;
            box-shadow:
                0 14px 30px rgba(124, 19, 22, 0.5),
                0 0 0 1px rgba(248, 113, 113, 0.5);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            cursor: pointer;
            transition: transform 0.18s ease, box-shadow 0.18s ease, filter 0.18s ease;
        }

        .absen-btn i {
            font-size: 1.1rem;
        }

        .absen-btn:hover {
            transform: translateY(-1px) scale(1.02);
            filter: brightness(1.04);
            box-shadow:
                0 16px 34px rgba(124, 19, 22, 0.6),
                0 0 0 1px rgba(248, 113, 113, 0.7);
        }

        .absen-btn:active,
        .absen-btn.touch-active {
            transform: translateY(0) scale(0.97);
            box-shadow:
                0 8px 20px rgba(124, 19, 22, 0.52),
                0 0 0 1px rgba(248, 113, 113, 0.75);
        }

        /* ===== History hari ini ===== */
        .history-card {
            margin-top: 16px;
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }

        .history-box {
            flex: 1;
            border-radius: 14px;
            padding: 9px 10px 10px;
            text-align: center;
            border: 1px solid rgba(148, 163, 184, 0.7);
            background: #f9fafb;
            position: relative;
            overflow: hidden;
        }

        .history-box::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at 0 0, rgba(248, 113, 113, 0.16), transparent 60%);
            opacity: 0.85;
            pointer-events: none;
        }

        .history-box-inner {
            position: relative;
            z-index: 1;
        }

        .history-box h6 {
            margin: 0;
            font-size: 0.78rem;
            font-weight: 500;
            color: var(--muted);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
        }

        .history-box h6 i {
            font-size: 0.9rem;
            color: var(--maroon-soft);
        }

        .history-box .time {
            font-size: 1.15rem;
            font-weight: 700;
            margin-top: 5px;
            color: var(--ink);
        }

        .history-box.empty .time {
            color: rgba(148, 163, 184, 0.9);
        }

        /* ===== Footer kecil ===== */
        .absen-footer {
            margin-top: 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
        }

        .footer-caption {
            font-size: 0.72rem;
            color: var(--muted);
            line-height: 1.2;
        }

        .footer-mini {
            font-size: 0.7rem;
            color: rgba(148, 163, 184, 0.95);
            text-align: right;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 18px 10px;
            }

            .absen-card {
                padding: 20px 14px 16px;
            }

            .info-main h4 {
                font-size: 1.05rem;
            }

            .history-box .time {
                font-size: 1.05rem;
            }

            .footer-caption {
                font-size: 0.68rem;
            }

            .footer-mini {
                font-size: 0.66rem;
            }
        }
    </style>

    {{-- Logo RSUD pojok kanan bawah, separo kelihatan --}}
    <div class="rsud-logo-page">
        <img src="https://rsudslg.kedirikab.go.id/asset_compro/img/logo/Logo.png" alt="Logo RSUD SLG">
    </div>

    <div class="absen-wrapper">
        <div class="absen-shell">
            <div class="absen-card card-tilt">
                <div class="absen-content">
                    {{-- Gambar rakun langsung --}}
                    <div class="avatar-wrap">
                        <img src="{{ asset('icon.png') }}" alt="Maskot Rakun">
                        <div class="avatar-caption">
                            ABSENSI MAHASISWA PRAKTIK
                        </div>
                    </div>

                    {{-- Info utama mahasiswa --}}
                    <div class="info-main">
                        <h4>{{ $mahasiswa->nm_mahasiswa }}</h4>
                        <p>
                            <i class="bi bi-mortarboard-fill"></i>
                            {{ $mahasiswa->univ_asal }} • {{ $mahasiswa->prodi }}
                        </p>
                    </div>

                    {{-- Chips --}}
                    @php
                        $isAktif = strtolower($mahasiswa->status) === 'aktif';
                    @endphp

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

                    {{-- FORM ABSEN (1 tombol auto masuk/keluar) --}}
                    <form action="{{ route('absensi.toggle', $mahasiswa->share_token) }}" method="POST">
                        @csrf
                        <button type="submit" class="absen-btn btn-press">
                            <i class="bi bi-fingerprint"></i>
                            <span>Absen Hari Ini</span>
                        </button>
                    </form>

                    {{-- Riwayat Hari Ini --}}
                    <div class="history-card">
                        <div class="history-box {{ $absenHariIni && $absenHariIni->jam_masuk ? '' : 'empty' }}">
                            <div class="history-box-inner">
                                <h6>
                                    <i class="bi bi-box-arrow-in-right"></i>
                                    Masuk
                                </h6>
                                <div class="time">{{ $absenHariIni->jam_masuk ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="history-box {{ $absenHariIni && $absenHariIni->jam_keluar ? '' : 'empty' }}">
                            <div class="history-box-inner">
                                <h6>
                                    <i class="bi bi-box-arrow-right"></i>
                                    Keluar
                                </h6>
                                <div class="time">{{ $absenHariIni->jam_keluar ?? '-' }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- Footer kecil --}}
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

    @push('scripts')
        <script>
            (function() {
                const card = document.querySelector('.card-tilt');
                const btn = document.querySelector('.btn-press');

                if (!card) return;

                function setTilt(e) {
                    const rect = card.getBoundingClientRect();
                    const point = e.touches ? e.touches[0] : e;
                    const x = point.clientX - rect.left;
                    const y = point.clientY - rect.top;

                    const centerX = rect.width / 2;
                    const centerY = rect.height / 2;

                    const rotateX = ((y - centerY) / 20) * -1;
                    const rotateY = (x - centerX) / 20;

                    card.style.transform =
                        `perspective(900px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale(1.02)`;
                    card.classList.add('hover-active');
                }

                function resetTilt() {
                    card.style.transform = "perspective(900px) rotateX(0deg) rotateY(0deg) scale(1)";
                    card.classList.remove('hover-active');
                }

                // Desktop hover
                card.addEventListener('mousemove', setTilt);
                card.addEventListener('mouseleave', resetTilt);

                // Mobile/touch: efek hover singkat
                card.addEventListener('touchstart', (e) => {
                    setTilt(e);
                    if (btn) btn.classList.add('touch-active');
                    setTimeout(() => {
                        resetTilt();
                        if (btn) btn.classList.remove('touch-active');
                    }, 180);
                }, {
                    passive: true
                });
            })();
        </script>
    @endpush
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 2500
                })
            @endif

            @if (session('error'))
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian!',
                    text: '{{ session('error') }}',
                    showConfirmButton: true
                })
            @endif
        </script>
    @endpush

@endsection
