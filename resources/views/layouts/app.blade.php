<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard Interaktif')</title>

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Icon Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    {{-- Chart.js untuk visualisasi data --}}
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
        }

        body {
            background-color: var(--bg-light);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text-dark);
            overflow-x: hidden;
            transition: background-color var(--transition-speed);
        }

        body.dark-mode {
            --bg-light: #1a1a1a;
            --text-dark: #f0f0f0;
            --text-muted: #a0a0a0;
        }

        /* Content */
        .content {
            margin-left: 250px;
            padding: 30px;
            min-height: 100vh;
            transition: margin-left var(--transition-speed) ease;
        }

        .content.expanded {
            margin-left: 70px;
        }

        /* Header Content */
        .content-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #dee2e6;
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

        /* Dashboard Cards */
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

        /* Progress Bars */
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

        /* Charts */
        .chart-container {
            position: relative;
            height: 300px;
            margin-top: 1rem;
        }

        /* Notifications */
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

        /* Scrollbar styling */
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

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .fade-in {
            animation: fadeIn 0.5s ease forwards;
        }

        /* Dark mode adjustments */
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
    </style>
</head>

<body>
    {{-- Sidebar --}}
    @include('partials.sidebar')

    <div class="content" id="mainContent">
        {{-- Notification Container --}}
        <div class="notification-container" id="notificationContainer"></div>

        {{-- Header Content --}}
        <div class="content-header">
            <h1 class="h3 mb-0">@yield('page-title', 'Dashboard Interaktif')</h1>
            <button class="theme-toggle" id="themeToggle">
                <i class="bi bi-moon"></i>
            </button>
        </div>

        {{-- Isi Halaman --}}
        @yield('content')

        {{-- Footer --}}
        @include('partials.footer')
    </div>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sidebar Toggle
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.querySelector('.sidebar');
            const mainContent = document.getElementById('mainContent');

            if (sidebarToggle && sidebar && mainContent) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('collapsed');
                    mainContent.classList.toggle('expanded');

                    // Change icon based on state
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

            // Theme Toggle
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

            // Animated counters
            function animateCounter(elementId, targetValue, duration = 2000) {
                const element = document.getElementById(elementId);
                if (!element) return;

                let startValue = 0;
                const increment = targetValue / (duration / 16); // 60fps

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

            // Initialize counters if elements exist
            setTimeout(() => {
                if (document.getElementById('revenueCount')) animateCounter('revenueCount', 12543);
                if (document.getElementById('userCount')) animateCounter('userCount', 324);
                if (document.getElementById('orderCount')) animateCounter('orderCount', 567);
                if (document.getElementById('feedbackCount')) animateCounter('feedbackCount', 89);
            }, 500);

            // Charts initialization with existence check
            const revenueCtx = document.getElementById('revenueChart');
            if (revenueCtx) {
                const revenueChart = new Chart(revenueCtx.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
                        datasets: [{
                            label: 'Revenue',
                            data: [6500, 7900, 8300, 10500, 12000, 14500, 16800],
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

            const trafficCtx = document.getElementById('trafficChart');
            if (trafficCtx) {
                const trafficChart = new Chart(trafficCtx.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Direct', 'Social', 'Referral', 'Organic'],
                        datasets: [{
                            data: [35, 25, 20, 20],
                            backgroundColor: [
                                '#7c1316',
                                '#9d2a2e',
                                '#c13c41',
                                '#e05257'
                            ],
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

            // Search functionality
            const searchInput = document.querySelector('.search-input');
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    const navLinks = document.querySelectorAll('.nav-link');

                    navLinks.forEach(link => {
                        const text = link.textContent.toLowerCase();
                        if (text.includes(searchTerm)) {
                            link.style.display = 'flex';
                        } else {
                            link.style.display = 'none';
                        }
                    });
                });
            }

            // Add hover effects to cards
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

    @yield('scripts')
</body>
</html>
