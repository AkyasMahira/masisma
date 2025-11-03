@extends('layouts.public') {{-- Layout tanpa sidebar --}}

@section('title', 'Absensi')

@section('content')
    <style>
        :root {
            --maroon: #7c1316;
            --maroon-light: #9d2a2e;
            --maroon-dark: #5c0f11;
            --bg-light: #f8f9fa;
            --text-dark: #222;
            --text-muted: #6c757d;
        }

        body {
            background: linear-gradient(135deg, var(--maroon-dark), var(--maroon));
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            font-family: "Inter", sans-serif;
            color: #fff;
        }

        .absen-card {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(14px) saturate(160%);
            -webkit-backdrop-filter: blur(14px) saturate(160%);
            border-radius: 18px;
            padding: 25px;
            width: 100%;
            max-width: 420px;
            color: #fff;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.25);
            text-align: center;
            animation: fadeIn 0.4s ease-in-out;
            border: 1px solid rgba(255, 255, 255, 0.28);
        }

        .absen-card h4 {
            font-weight: 700;
            margin-bottom: 4px;
        }

        .absen-card p {
            margin: 0;
            font-size: 14px;
            opacity: 0.9;
        }

        .absen-btn {
            margin-top: 22px;
            width: 100%;
            border-radius: 12px;
            padding: 14px;
            font-weight: 600;
            font-size: 17px;
            border: none;
            background: #fff;
            color: var(--maroon-dark);
            transition: 0.25s ease;
        }

        .absen-btn:hover {
            transform: translateY(-2px) scale(1.03);
            background: var(--bg-light);
            color: var(--maroon);
        }

        .history-card {
            margin-top: 18px;
            display: flex;
            justify-content: space-between;
            gap: 12px;
        }

        .history-box {
            flex: 1;
            background: rgba(255, 255, 255, 0.18);
            border-radius: 14px;
            padding: 12px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.25);
        }

        .history-box h6 {
            margin: 0;
            font-size: 13px;
            opacity: 0.85;
            font-weight: 500;
        }

        .history-box .time {
            font-size: 20px;
            font-weight: 700;
            margin-top: 6px;
            color: #fff;
        }

        .absen-card {
            transform-style: preserve-3d;
            transform: perspective(900px);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .absen-card.hover-active {
            box-shadow: 0 10px 30px rgba(255, 255, 255, 0.35);
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
    </style>


    <div class="absen-card">
        <h4>{{ $mahasiswa->nm_mahasiswa }}</h4>
        <p>{{ $mahasiswa->univ_asal }} — {{ $mahasiswa->prodi }}</p>
        <p><strong>Ruangan:</strong> {{ $mahasiswa->ruangan->nm_ruangan ?? $mahasiswa->nm_ruangan }}</p>
        <p><strong>Status:</strong> {{ $mahasiswa->status }}</p>

        {{-- FORM ABSEN (1 tombol auto masuk/keluar) --}}
        <form action="{{ route('absensi.toggle', $mahasiswa->share_token) }}" method="POST">
            @csrf
            <button class="absen-btn">Absen</button>
        </form>


        {{-- Riwayat Hari Ini --}}
        <div class="history-card">
            <div class="history-box">
                <h6>Masuk</h6>
                <div class="time">{{ $absenHariIni->jam_masuk ?? '-' }}</div>
            </div>
            <div class="history-box">
                <h6>Keluar</h6>
                <div class="time">{{ $absenHariIni->jam_keluar ?? '-' }}</div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            const card = document.querySelector('.absen-card');

            card.addEventListener('mousemove', (e) => {
                const rect = card.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;

                const centerX = rect.width / 2;
                const centerY = rect.height / 2;

                const rotateX = ((y - centerY) / 18) * -1;
                const rotateY = (x - centerX) / 18;

                card.style.transform = `perspective(900px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale(1.03)`;
                card.classList.add('hover-active');
            });

            card.addEventListener('mouseleave', () => {
                card.style.transform = "perspective(900px) rotateX(0deg) rotateY(0deg) scale(1)";
                card.classList.remove('hover-active');
            });
        </script>
    @endpush

@endsection
