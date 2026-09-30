<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="theme-color" content="#7c1316">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sindikat')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('icon.png') }}">
    <link rel="shortcut icon" href="{{ asset('icon.png') }}">

    {{-- Bootstrap & Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    {{-- Third Party CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />

    <style>
        :root {
            --maroon: #7c1316;
            --maroon-light: #9d2a2e;
            --maroon-dark: #5c0f11;
            --bg-light: #f8f9fa;
            --sidebar-width: 260px;
            --sidebar-collapsed-width: 80px;
            --transition-speed: 0.3s;
            --border-radius: 12px;
        }

        body {
            background-color: var(--bg-light);
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            overflow-x: hidden;
            min-height: 100vh;
            display: flex;
        }

        /* Dark Mode Vars */
        body.dark-mode {
            --bg-light: #121212;
            --text-dark: #f0f0f0;
            background-color: var(--bg-light);
            color: #f0f0f0;
        }

        /* --- CONTENT AREA --- */
        .content {
            flex: 1;
            margin-left: var(--sidebar-width);
            padding: 25px;
            transition: all var(--transition-speed) cubic-bezier(0.4, 0, 0.2, 1);
            min-width: 0; /* Mencegah overflow pada flex item */
            display: flex;
            flex-direction: column;
        }

        /* Desktop Collapsed */
        .content.expanded {
            margin-left: var(--sidebar-collapsed-width);
        }

        /* Mobile Viewport */
        @media (max-width: 768px) {
            .content, .content.expanded {
                margin-left: 0 !important;
                padding: 15px;
                padding-top: 70px; /* Ruang untuk tombol hamburger mobile */
            }
        }

        /* --- UI COMPONENTS --- */
        .content-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 15px;
        }

        .dashboard-card {
            background: white;
            border-radius: var(--border-radius);
            border: none;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            padding: 1.5rem;
            height: 100%;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        body.dark-mode .dashboard-card {
            background: #1e1e1e;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        }

        .theme-toggle {
            background: var(--maroon);
            color: white;
            border: none;
            border-radius: 10px;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: 0.3s;
        }

        /* Sticky Footer */
        footer {
            margin-top: auto;
            padding: 20px 0;
            text-align: center;
            font-size: 0.85rem;
            color: var(--text-muted);
            border-top: 1px solid rgba(0,0,0,0.05);
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-thumb { background: #ccc; border-radius: 10px; }
        body.dark-mode ::-webkit-scrollbar-thumb { background: #444; }

        #notepad-editor { height: 300px; border-radius: 0 0 8px 8px; }
    </style>

    @stack('styles')
</head>

<body>
    {{-- Sidebar & Overlay (Di-include dari file partials) --}}
    @include('partials.sidebar')

    <div class="content" id="mainContent">
        {{-- Notification Container --}}
        <div class="notification-container" id="notificationContainer"></div>

        <div class="content-header fade-in">
            <!--<div>-->
            <!--    <h1 class="h3 fw-bold mb-1">@yield('page-title', 'Sindikat')</h1>-->
            <!--    <p class="text-muted small mb-0">Selamat datang kembali di Sistem Informasi Pelatihan, Penelitian dan Pendidikan Diklat.</p>-->
            <!--</div>-->
            <div class="d-flex gap-2">
                <!--<button class="btn btn-outline-secondary btn-sm d-none d-md-flex align-items-center" data-bs-toggle="modal" data-bs-target="#notepadModal">-->
                <!--    <i class="bi bi-pencil-square me-2"></i> Notepad-->
                <!--</button>-->
                <!--<button class="theme-toggle" id="themeToggle">-->
                <!--    <i class="bi bi-moon-stars"></i>-->
                <!--</button>-->
            </div>
        </div>

        {{-- Main Yield Content --}}
        <main class="fade-in">
            @yield('content')
        </main>

        @include('partials.footer')
    </div>

    {{-- Modal Notepad --}}
    <div class="modal fade" id="notepadModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-maroon text-white">
                    <h5 class="modal-title"><i class="bi bi-journal-text me-2"></i> Notepad Pribadi</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0">
                    <div id="notepad-editor"></div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" id="clear-notepad" class="btn btn-outline-danger btn-sm">Hapus Semua</button>
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Simpan & Tutup</button>
                </div>
            </div>
        </div>
    </div>

    {{-- JavaScript Libs --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- 1. SYNC SIDEBAR LOGIC ---
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const sidebarToggle = document.getElementById('sidebarToggle'); // Tombol di dalam sidebar
            const mobileHamburger = document.getElementById('mobileHamburger'); // Tombol melayang di mobile

            function updateLayout() {
                if (window.innerWidth > 768) {
                    if (sidebar.classList.contains('collapsed')) {
                        mainContent.classList.add('expanded');
                    } else {
                        mainContent.classList.remove('expanded');
                    }
                } else {
                    mainContent.classList.remove('expanded');
                }
            }

            // Jalankan saat load
            updateLayout();

            // Event listener untuk tombol toggle (Jika diklik, layout konten ikut geser)
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', () => {
                    setTimeout(updateLayout, 10); // beri sedikit delay agar class sidebar berubah dulu
                });
            }

            // --- 2. THEME TOGGLE (DARK MODE) ---
            const themeToggle = document.getElementById('themeToggle');
            if (themeToggle) {
                const icon = themeToggle.querySelector('i');
                
                // Cek storage
                if(localStorage.getItem('theme') === 'dark') {
                    document.body.classList.add('dark-mode');
                    icon.classList.replace('bi-moon-stars', 'bi-sun');
                }

                themeToggle.addEventListener('click', () => {
                    document.body.classList.toggle('dark-mode');
                    const isDark = document.body.classList.contains('dark-mode');
                    
                    icon.classList.toggle('bi-moon-stars', !isDark);
                    icon.classList.toggle('bi-sun', isDark);
                    localStorage.setItem('theme', isDark ? 'dark' : 'light');
                });
            }

            // --- 3. NOTEPAD (QUILL) ---
            const editorElement = document.getElementById('notepad-editor');
            if (editorElement) {
                const quill = new Quill('#notepad-editor', {
                    theme: 'snow',
                    placeholder: 'Tulis catatan penting Anda di sini...'
                });

                const savedContent = localStorage.getItem('userNotepadContent');
                if (savedContent) quill.setContents(JSON.parse(savedContent));

                quill.on('text-change', () => {
                    localStorage.setItem('userNotepadContent', JSON.stringify(quill.getContents()));
                });

                document.getElementById('clear-notepad').addEventListener('click', () => {
                    if(confirm('Hapus semua catatan?')) {
                        quill.setContents([]);
                        localStorage.removeItem('userNotepadContent');
                    }
                });
            }

            // --- 4. CHOICES JS ---
            document.querySelectorAll('.js-choices').forEach(el => {
                new Choices(el, { searchEnabled: true, itemSelectText: '' });
            });
        });
    </script>

    @include('partials.sweetalert')
    @stack('scripts')
    @yield('scripts')
</body>
</html>