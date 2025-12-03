<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard Interaktif')</title>

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Icon Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    {{-- Choices.js CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />

    {{-- Quill --}}
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

    {{-- Font Awesome --}}
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"
          integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ=="
          crossorigin="anonymous" referrerpolicy="no-referrer" />

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        :root {
            --maroon: #7c1316;
            --maroon-light: #9d2a2e;
            --maroon-dark: #5c0f11;
            --bg-light: #f8f9fa;
            --text-dark: #222;
            --text-muted: #6c757d;
            --transition-speed: 0.3s;
            --border-radius: 8px;
            --shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            --shadow-hover: 0 8px 20px rgba(0, 0, 0, 0.15);
            text-decoration: none !important;
        }

        body {
            background-color: var(--bg-light);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text-dark);
            overflow-x: hidden;
            transition: background-color var(--transition-speed);
            display: flex;
            min-height: 100vh;
        }

        body.dark-mode {
            --bg-light: #1a1a1a;
            --text-dark: #f0f0f0;
            --text-muted: #a0a0a0;
        }

        #notepad-editor {
            height: 300px;
        }

        .content {
            margin-left: 250px;
            padding: 30px;
            min-height: 100vh;
            transition: margin-left var(--transition-speed) ease;
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        /* Saat sidebar collapsed, isi halaman maju ke kiri */
        .content.expanded {
            margin-left: 80px; /* sesuaikan dengan .sidebar.collapsed width */
        }

        footer {
            margin-top: auto;
            padding: 12px 0;
            text-align: center;
            color: var(--text-muted);
            font-size: 0.9rem;
            border-top: 1px solid rgba(0, 0, 0, 0.08);
            backdrop-filter: blur(8px);
            background: rgba(255, 255, 255, 0.6);
            transition: background var(--transition-speed), color var(--transition-speed);
        }

        footer a {
            color: inherit;
            text-decoration: none;
            font-weight: 500;
            transition: color var(--transition-speed), transform 0.2s ease;
        }

        footer a:hover {
            color: var(--maroon);
            transform: translateY(-2px);
        }

        body.dark-mode footer {
            background: rgba(255, 255, 255, 0.04);
            border-top: 1px solid rgba(255, 255, 255, 0.15);
        }

        .content-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #dee2e6;
        }

        a {
            text-decoration: none;
            color: var(--maroon);
        }

        .theme-toggle {
            background: var(--maroon);
            color: white;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: transform var(--transition-speed);
        }

        .theme-toggle:hover {
            transform: rotate(15deg);
        }

        .dashboard-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            transition: transform var(--transition-speed), box-shadow var(--transition-speed);
            border: none;
            height: 100%;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-hover);
        }

        .card-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }

        .card-icon.primary {
            background: rgba(124, 19, 22, 0.1);
            color: var(--maroon);
        }

        .card-icon.success {
            background: rgba(40, 167, 69, 0.1);
            color: #28a745;
        }

        .card-icon.warning {
            background: rgba(255, 193, 7, 0.1);
            color: #ffc107;
        }

        .card-icon.info {
            background: rgba(23, 162, 184, 0.1);
            color: #17a2b8;
        }

        .stat-number {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0.2rem;
        }

        .stat-text {
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .progress-container {
            margin-top: 1rem;
        }

        .progress {
            height: 8px;
            margin-bottom: 0.5rem;
        }

        .progress-label {
            display: flex;
            justify-content: space-between;
            font-size: 0.85rem;
            margin-bottom: 0.2rem;
        }

        .chart-container {
            position: relative;
            height: 300px;
            margin-top: 1rem;
        }

        .notification-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1050;
            max-width: 350px;
        }

        .notification {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 1rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            transform: translateX(400px);
            transition: transform 0.5s ease;
            border-left: 4px solid var(--maroon);
        }

        .notification.show {
            transform: translateX(0);
        }

        .notification-icon {
            margin-right: 1rem;
            font-size: 1.2rem;
            color: var(--maroon);
        }

        .notification-content {
            flex: 1;
        }

        .notification-close {
            background: none;
            border: none;
            font-size: 1.2rem;
            cursor: pointer;
            color: var(--text-muted);
        }

        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-thumb {
            background-color: rgba(124, 19, 22, 0.6);
            border-radius: 8px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background-color: rgba(124, 19, 22, 0.9);
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

        .fade-in {
            animation: fadeIn 0.5s ease forwards;
        }

        body.dark-mode .dashboard-card {
            background: #2d2d2d;
            color: #f0f0f0;
        }

        body.dark-mode .content-header {
            border-bottom-color: #444;
        }

        body.dark-mode footer {
            border-top-color: #444;
        }

        .bg-maroon {
            background-color: var(--maroon) !important;
        }

        .btn-outline-maroon {
            color: var(--maroon);
            border-color: var(--maroon);
        }

        .btn-outline-maroon:hover {
            background-color: var(--maroon);
            color: white;
        }

        @media (max-width: 768px) {
            .content {
                margin-left: 80px;
            }
            .content.expanded {
                margin-left: 0;
            }
        }
    </style>

    @stack('styles')
</head>

<body>
    {{-- Sidebar --}}
    @include('partials.sidebar')

    <div class="content" id="mainContent">
        {{-- Notification Container --}}
        <div class="notification-container" id="notificationContainer"></div>

        <div class="content-header">
            <h1 class="h3 mb-0">@yield('page-title', 'Dashboard Interaktif')</h1>
            <button class="theme-toggle" id="themeToggle">
                <i class="bi bi-moon"></i>
            </button>
        </div>

        @yield('content')

        @include('partials.footer')
    </div>

    {{-- Modal Notepad --}}
    <div class="modal fade" id="notepadModal" tabindex="-1" aria-labelledby="notepadModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="notepadModalLabel">📝 Notepad Pribadi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small">Catatan ini disimpan di browser Anda (LocalStorage) dan tidak disimpan ke server.</p>
                    <div id="notepad-editor"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" id="clear-notepad" class="btn btn-danger">Bersihkan Catatan</button>
                </div>
            </div>
        </div>
    </div>

    {{-- JS libs --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // TOGGLE SIDEBAR – HANYA DI SINI (BIAR GAK DOUBEL)
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.querySelector('.sidebar');
            const mainContent = document.getElementById('mainContent');

            if (sidebarToggle && sidebar && mainContent) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('collapsed');
                    mainContent.classList.toggle('expanded');

                    const icon = sidebarToggle.querySelector('i');
                    if (sidebar.classList.contains('collapsed')) {
                        icon.classList.remove('bi-chevron-left');
                        icon.classList.add('bi-chevron-right');
                    } else {
                        icon.classList.remove('bi-chevron-right');
                        icon.classList.add('bi-chevron-left');
                    }
                });
            }

            // NOTEPAD + QUILL
            const notepadModal = document.getElementById('notepadModal');
            const editorElement = document.getElementById('notepad-editor');
            let quill;
            const storageKey = 'userNotepadContent';

            if (notepadModal && editorElement) {
                notepadModal.addEventListener('shown.bs.modal', function() {
                    if (!quill) {
                        quill = new Quill('#notepad-editor', {
                            theme: 'snow',
                            modules: {
                                toolbar: [
                                    [{ 'header': [1, 2, 3, false] }],
                                    ['bold', 'italic', 'underline', 'strike'],
                                    [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                                    ['link', 'blockquote'],
                                    ['clean']
                                ]
                            }
                        });

                        const savedContent = localStorage.getItem(storageKey);
                        if (savedContent) {
                            try {
                                quill.setContents(JSON.parse(savedContent));
                            } catch (e) {
                                quill.setText(savedContent);
                            }
                        }

                        quill.on('text-change', function(delta, oldDelta, source) {
                            if (source === 'user') {
                                localStorage.setItem(storageKey, JSON.stringify(quill.getContents()));
                            }
                        });
                    }
                });

                const clearButton = document.getElementById('clear-notepad');
                if (clearButton) {
                    clearButton.addEventListener('click', function() {
                        if (confirm('Yakin ingin menghapus semua isi notepad?')) {
                            if (quill) {
                                quill.setContents([]);
                            }
                            localStorage.removeItem(storageKey);
                        }
                    });
                }
            }

            // THEME TOGGLE
            const themeToggle = document.getElementById('themeToggle');
            if (themeToggle) {
                const themeIcon = themeToggle.querySelector('i');

                themeToggle.addEventListener('click', function() {
                    document.body.classList.toggle('dark-mode');

                    if (document.body.classList.contains('dark-mode')) {
                        themeIcon.classList.remove('bi-moon');
                        themeIcon.classList.add('bi-sun');
                    } else {
                        themeIcon.classList.remove('bi-sun');
                        themeIcon.classList.add('bi-moon');
                    }
                });
            }

            // COUNTER ANIMASI
            function animateCounter(elementId, targetValue, duration = 2000) {
                const element = document.getElementById(elementId);
                if (!element) return;

                let startValue = 0;
                const increment = targetValue / (duration / 16);

                function updateCounter() {
                    startValue += increment;
                    if (startValue < targetValue) {
                        element.textContent = Math.floor(startValue).toLocaleString();
                        requestAnimationFrame(updateCounter);
                    } else {
                        element.textContent = targetValue.toLocaleString();
                    }
                }

                updateCounter();
            }

            setTimeout(() => {
                const dd = window.dashboardData || {};
                if (document.getElementById('revenueCount')) animateCounter('revenueCount', dd.totalMahasiswa ?? 12543);
                if (document.getElementById('userCount')) animateCounter('userCount', dd.totalRuangan ?? 324);
                if (document.getElementById('orderCount')) animateCounter('orderCount', dd.totalUsers ?? 567);
                if (document.getElementById('feedbackCount')) animateCounter('feedbackCount', dd.todayAbsensi ?? 89);
            }, 500);

            // CHART LINE
            const revenueCtx = document.getElementById('revenueChart');
            if (revenueCtx) {
                const dd = window.dashboardData || {};
                const labels = dd.months ?? ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'];
                const dataSeries = dd.mahasiswaPerMonth ?? [6500, 7900, 8300, 10500, 12000, 14500, 16800];

                new Chart(revenueCtx.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Mahasiswa',
                            data: dataSeries,
                            borderColor: '#7c1316',
                            backgroundColor: 'rgba(124, 19, 22, 0.1)',
                            borderWidth: 2,
                            tension: 0.4,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    drawBorder: false
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            }

            // CHART DOUGHNUT
            const trafficCtx = document.getElementById('trafficChart');
            if (trafficCtx) {
                const dd = window.dashboardData || {};
                const labels = dd.ruanganLabels && dd.ruanganLabels.length ? dd.ruanganLabels : ['Direct', 'Social', 'Referral', 'Organic'];
                const dataSeries = dd.ruanganData && dd.ruanganData.length ? dd.ruanganData : [35, 25, 20, 20];
                const colors = ['#7c1316', '#9d2a2e', '#c13c41', '#e05257', '#e77a7a', '#f2a3a3'];

                new Chart(trafficCtx.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: dataSeries,
                            backgroundColor: colors.slice(0, labels.length),
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            }

            // Choices.js
            try {
                const choiceElements = document.querySelectorAll('select.js-choices');
                choiceElements.forEach(el => {
                    if (!el._choicesInitialized) {
                        new Choices(el, {
                            searchEnabled: true,
                            itemSelectText: '',
                            shouldSort: false,
                            placeholder: true,
                        });
                        el._choicesInitialized = true;
                    }
                });
            } catch (e) {
                console.warn('Choices.js init failed', e);
            }

            // Hover efek card
            const cards = document.querySelectorAll('.dashboard-card');
            cards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-5px)';
                });

                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });
        });
    </script>

    @include('partials.sweetalert')
    @stack('scripts')
    @yield('scripts')
</body>

</html>