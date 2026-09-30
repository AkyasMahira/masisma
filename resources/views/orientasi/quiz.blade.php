@extends('layouts.app')
@section('page-title', 'Test Orientasi ')
@section('title', 'Test Orientasi ')
@section('content')
<style>
    :root {
        --primary-maroon: #7c1316;
        --light-maroon: #a3191d;
        --bg-soft: #f4f6f9;
        --green-success: #198754;
        --yellow-warning: #ffc107;
        --gray-default: #e9ecef;
    }

    body { background-color: var(--bg-soft); }

    /* LAYOUT & GRID */
    .cbt-wrapper {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        align-items: flex-start;
    }

    /* TIMER BAR (STICKY) */
    .timer-sticky {
        position: sticky;
        top: 70px; 
        z-index: 999;
        background: var(--primary-maroon);
        color: white;
        padding: 10px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    .timer-digits {
        font-family: 'Courier New', Courier, monospace;
        font-weight: 800;
        font-size: 1.5rem;
    }

    /* QUESTION CARD */
    .q-column { flex: 1; min-width: 0; }
    
    .q-card {
        background: white;
        border-radius: 12px;
        border: 1px solid transparent;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        margin-bottom: 20px;
        overflow: hidden;
        transition: all 0.3s ease;
        scroll-margin-top: 150px; /* Jarak aman saat scroll */
    }
    
    /* Efek Fokus saat diklik */
    .q-card:focus-within {
        border-color: var(--primary-maroon);
        box-shadow: 0 0 0 4px rgba(124, 19, 22, 0.1);
    }

    .q-header {
        background: #fff;
        border-bottom: 1px solid #eee;
        padding: 15px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .q-number {
        background: var(--primary-maroon);
        color: white;
        padding: 5px 12px;
        border-radius: 6px;
        font-weight: bold;
        font-size: 0.9rem;
    }

    /* TOMBOL RAGU-RAGU */
    .btn-ragu {
        border: 1px solid #ffc107;
        background: white;
        color: #ffc107;
        border-radius: 20px;
        padding: 5px 15px;
        font-size: 0.85rem;
        font-weight: 600;
        cursor: pointer;
        transition: 0.2s;
    }
    .btn-ragu.active {
        background: #ffc107;
        color: #000;
    }
    .btn-ragu:hover {
        background: #fff3cd;
    }

    /* PILIHAN JAWABAN (RADIO CUSTOM) */
    .option-wrapper {
        position: relative;
        margin-bottom: 10px;
    }
    
    .option-wrapper input[type="radio"] {
        position: absolute;
        opacity: 0;
        cursor: pointer;
        height: 0; width: 0;
    }

    .option-label {
        display: block;
        padding: 12px 15px;
        background: #fff;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s;
        position: relative;
        z-index: 10;
    }

    .option-label:hover {
        background-color: #f8f9fa;
    }

    /* State: CHECKED */
    .option-wrapper input[type="radio"]:checked + .option-label {
        background-color: #fcf0f0; /* Merah muda sangat muda */
        border-color: var(--primary-maroon);
        color: var(--primary-maroon);
        font-weight: bold;
        box-shadow: 0 2px 5px rgba(124, 19, 22, 0.15);
    }

    /* NAVIGASI KANAN (STICKY) */
    .nav-column { width: 300px; }
    
    .nav-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        position: sticky;
        top: 150px;
    }

    .nav-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 8px;
        margin-top: 15px;
    }

    .nav-item {
        aspect-ratio: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        background: var(--gray-default);
        color: #666;
        font-weight: bold;
        cursor: pointer;
        border: 1px solid #ccc;
        transition: 0.2s;
        position: relative;
    }

    .nav-item:hover { transform: scale(1.05); }

    /* STATUS WARNA NAVIGASI - PENTING! */
    .nav-item.status-filled {
        background-color: var(--green-success) !important;
        color: white !important;
        border-color: var(--green-success) !important;
    }

    .nav-item.status-flagged {
        background-color: var(--yellow-warning) !important;
        color: black !important;
        border-color: var(--yellow-warning) !important;
    }
    
    /* Bintang kecil untuk flagged */
    .nav-item.status-flagged::after {
        content: '★';
        position: absolute;
        top: -4px; right: -2px;
        font-size: 10px;
        color: red;
    }

    @media (max-width: 991px) {
        .cbt-wrapper { flex-direction: column; }
        .nav-column { width: 100%; order: -1; } /* Navigasi pindah atas di HP */
        .nav-card { position: relative; top: 0; z-index: 1;}
        .timer-sticky { top: 60px; }
    }
</style>

<div class="container py-4">
    
    <form id="quizForm" action="{{ $type == 'pre' ? route('orientasi.pre.submit') : route('orientasi.post.submit') }}" method="POST">
        @csrf
        {{-- Di dalam form quiz --}}

        {{-- 1. TIMER BAR --}}
        <div class="timer-sticky animate-down">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-stopwatch-fill fs-4"></i>
                <span class="d-none d-md-inline fw-bold">Sisa Waktu:</span>
            </div>
            <div id="timerDisplay" class="timer-digits">15:00</div>
            <div class="badge bg-white text-dark border px-3 py-2">
                {{ $type == 'pre' ? 'PRE-TEST' : 'POST-TEST' }}
            </div>
        </div>

        <div class="cbt-wrapper">
            
            {{-- 2. KOLOM SOAL (KIRI) --}}
            <div class="q-column">
                @foreach($questions as $id => $q)
                    {{-- 
                        FIX: SIMPAN LOOP ITERATION SOAL KE VARIABEL BARU
                        Agar tidak tertimpa oleh loop opsi di bawahnya.
                    --}}
                    @php $qIndex = $loop->iteration; @endphp

                    <div class="q-card animate-up" id="q-card-{{ $qIndex }}" style="animation-delay: {{ $qIndex * 0.05 }}s">
                        
                        <div class="q-header">
                            <div class="d-flex align-items-center gap-3">
                                <span class="q-number">No. {{ $qIndex }}</span>
                            </div>
                            {{-- Tombol Ragu --}}
                            <button type="button" 
                                    class="btn-ragu" 
                                    id="btn-flag-{{ $qIndex }}"
                                    onclick="toggleFlag({{ $qIndex }})">
                                <i class="bi bi-flag-fill"></i> Ragu-ragu
                            </button>
                        </div>

                        <div class="p-4">
                            <p class="fs-5 mb-4 text-dark">{!! nl2br(e($q['q'])) !!}</p>
                            
                            <div class="options-container">
                                @foreach($q['options'] as $key => $val)
                                    <div class="option-wrapper">
                                        {{-- 
                                            FIX ID & FOR: Gunakan $qIndex (bukan $loop->iteration)
                                            agar ID unik per soal: opt-1-a, opt-2-a, dst.
                                        --}}
                                        <input type="radio" 
                                               name="answers[{{ $id }}]" 
                                               value="{{ $key }}" 
                                               id="opt-{{ $qIndex }}-{{ $key }}"
                                               onchange="updateState({{ $qIndex }})"
                                               required>
                                        
                                        <label class="option-label" for="opt-{{ $qIndex }}-{{ $key }}">
                                            <div class="d-flex gap-3">
                                                <span class="fw-bold px-2 bg-light border rounded">{{ strtoupper($key) }}</span>
                                                <span>{{ $val }}</span>
                                            </div>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- 3. KOLOM NAVIGASI (KANAN) --}}
            <div class="nav-column">
                <div class="nav-card">
                    <h6 class="fw-bold mb-3 border-bottom pb-2">Navigasi Soal</h6>
                    
                    {{-- Progress --}}
                    <div class="d-flex justify-content-between small text-muted mb-1">
                        <span>Progress:</span>
                        <span id="progressText">0%</span>
                    </div>
                    <div class="progress mb-3" style="height: 8px;">
                        <div class="progress-bar bg-success" id="progressBar" style="width: 0%"></div>
                    </div>

                    {{-- Grid Nomor --}}
                    <div class="nav-grid">
                        @foreach($questions as $id => $q)
                            {{-- Gunakan $loop->iteration karena ini di loop luar --}}
                            <div class="nav-item" 
                                 id="nav-{{ $loop->iteration }}" 
                                 onclick="scrollToSoal({{ $loop->iteration }})">
                                {{ $loop->iteration }}
                            </div>
                        @endforeach
                    </div>

                    {{-- Legend --}}
                    <div class="mt-4 pt-3 border-top text-center">
                        <div class="d-flex gap-2 justify-content-center mb-3 small flex-wrap">
                            <span class="badge bg-success">Isi</span>
                            <span class="badge bg-warning text-dark">Ragu</span>
                            <span class="badge bg-secondary">Kosong</span>
                        </div>

                        <button type="button" class="btn btn-danger w-100 py-2 fw-bold shadow-sm" onclick="finishExam()">
                            <i class="bi bi-send-fill me-2"></i> KUMPULKAN JAWABAN
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>

<script>
    // Konfigurasi Awal
    const totalSoal = {{ count($questions) }};
    let timeLeft = 15 * 60; // 30 Menit dalam detik

    // 1. JALANKAN SAAT LOAD (Agar warna hijau muncul walau di-refresh)
    document.addEventListener("DOMContentLoaded", function() {
        // Scan semua soal, cek apakah ada yg checked
        for (let i = 1; i <= totalSoal; i++) {
            checkInitialState(i);
        }
        startTimer();
    });

    // 2. FUNGSI CEK STATUS (Dipanggil saat Load & Change)
    function updateState(num) {
        // Logic utama pewarnaan
        const navItem = document.getElementById(`nav-${num}`);
        const btnFlag = document.getElementById(`btn-flag-${num}`);
        
        // Cek status checkbox di dalam card soal tersebut
        const card = document.getElementById(`q-card-${num}`);
        
        // FIX: Cari input yang checked di dalam card spesifik
        const isChecked = card.querySelector('input[type="radio"]:checked') !== null;
        const isFlagged = btnFlag.classList.contains('active');

        // Reset kelas warna dulu
        navItem.classList.remove('status-filled', 'status-flagged');

        // Prioritas Warna: Kuning (Flag) > Hijau (Isi) > Abu (Kosong)
        if (isFlagged) {
            navItem.classList.add('status-flagged');
        } else if (isChecked) {
            navItem.classList.add('status-filled');
        }

        updateProgressBar();
    }

    // Fungsi khusus saat load agar tidak menimpa flag (jika nanti ada fitur simpan flag ke DB)
    function checkInitialState(num) {
        updateState(num); 
    }

    // 3. FUNGSI RAGU-RAGU
    function toggleFlag(num) {
        const btn = document.getElementById(`btn-flag-${num}`);
        
        if (btn.classList.contains('active')) {
            btn.classList.remove('active');
            btn.innerHTML = '<i class="bi bi-flag-fill"></i> Ragu-ragu';
        } else {
            btn.classList.add('active');
            btn.innerHTML = '<i class="bi bi-flag-fill"></i> Ditandai';
        }
        
        // Update warna grid setelah flag berubah
        updateState(num);
    }

    // 4. PROGRESS BAR
    function updateProgressBar() {
        // Hitung total radio yang checked di seluruh halaman
        const filled = document.querySelectorAll('input[type="radio"]:checked').length;
        const percent = Math.round((filled / totalSoal) * 100);
        
        document.getElementById('progressBar').style.width = percent + '%';
        document.getElementById('progressText').innerText = percent + '%';
    }

    // 5. SCROLL KE SOAL
    function scrollToSoal(num) {
        const el = document.getElementById(`q-card-${num}`);
        const offset = 140; // Kompensasi tinggi header sticky
        const topPos = el.getBoundingClientRect().top + window.scrollY - offset;
        
        window.scrollTo({ top: topPos, behavior: 'smooth' });
    }

    // 6. TIMER MUNDUR
    function startTimer() {
        const display = document.getElementById('timerDisplay');
        
        const timer = setInterval(() => {
            let m = Math.floor(timeLeft / 60);
            let s = timeLeft % 60;
            
            // Format 2 digit (05:09)
            m = m < 10 ? '0' + m : m;
            s = s < 10 ? '0' + s : s;
            
            display.innerText = `${m}:${s}`;
            
            // Warna Merah Kritis
            if (timeLeft < 60) {
                display.style.color = '#dc3545';
                // Animasi kedip manual via JS style kalau mau
                display.style.opacity = (display.style.opacity == '0.5' ? '1' : '0.5');
            }

            if (timeLeft <= 0) {
                clearInterval(timer);
                alert("Waktu Habis! Jawaban tersimpan otomatis.");
                document.getElementById('quizForm').submit();
            }
            
            timeLeft--;
        }, 1000);
    }

    // 7. SUBMIT FORM
    function finishExam() {
        const filled = document.querySelectorAll('input[type="radio"]:checked').length;
        const flagged = document.querySelectorAll('.status-flagged').length;
        
        let msg = `Anda sudah mengisi ${filled} dari ${totalSoal} soal.\n`;
        
        if (flagged > 0) {
            msg += `⚠️ Ada ${flagged} soal yang ditandai ragu-ragu.\n`;
        }
        if (filled < totalSoal) {
            msg += `⚠️ Masih ada soal yang kosong.\n`;
        }
        msg += `\nYakin ingin mengumpulkan sekarang?`;

        if (confirm(msg)) {
            document.getElementById('quizForm').submit();
        }
    }
    
    // Cegah enter submit tidak sengaja
    document.addEventListener('keydown', function(event) {
        if (event.key === "Enter") {
            event.preventDefault();
        }
    });
</script>
@endsection