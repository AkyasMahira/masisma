{{-- Sidebar --}}
<div class="sidebar">
    <div>
        <div class="sidebar-header">
            <img class="image-sidebar" src="{{ asset('icon.png') }}" alt="Logo">
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="bi bi-chevron-left"></i>
            </button>
        </div>

        <div class="sidebar-search">
            <div class="search-container">
                <input type="text" class="search-input" placeholder="Cari...">
                <i class="bi bi-search search-icon"></i>
            </div>
        </div>

        {{-- ====================================================== --}}
        {{-- STRUKTUR NAVIGASI BARU (GAYA PILL & GRUP) --}}
        {{-- ====================================================== --}}
        <nav class="nav flex-column sidebar-nav-container">

            {{-- GRUP 1 --}}
            <div class="sidebar-heading">
                <span class="sidebar-text">Menu Utama</span>
            </div>

            <a class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                <i class="bi bi-house-door"></i>
                <span class="sidebar-text">Dashboard</span>
            </a>

            @if (auth()->check() && auth()->user()->role === 'admin')
                {{-- GRUP 2 --}}
                <div class="sidebar-heading">
                    <span class="sidebar-text">Administrasi</span>
                </div>

                {{-- 2. MOU (Dropdown) --}}
                @php
                    $isMouActive = request()->is('mou*');
                @endphp
                <div class="nav-item-dropdown">
                    <a class="nav-link {{ $isMouActive ? 'active-parent' : '' }}" data-bs-toggle="collapse"
                        href="#menuMou" role="button" aria-expanded="{{ $isMouActive ? 'true' : 'false' }}"
                        aria-controls="menuMou">
                        <i class="bi bi-file-earmark-text"></i>
                        <span class="sidebar-text">MOU</span>
                        <i class="bi bi-chevron-down sidebar-arrow"></i>
                    </a>
                    {{-- Sub-menu --}}
                    <div class="collapse sub-menu {{ $isMouActive ? 'show' : '' }}" id="menuMou">
                        {{-- Ganti href="#" dengan route yang sesuai --}}
                        <a class="nav-link {{ request()->is('mou/create*') ? 'active' : '' }}" href="{{ route('mou.create') }}">
                            <span class="sidebar-text">Tambah MOU</span>
                        </a>
                        <a class="nav-link {{ request()->is('mou') ? 'active' : '' }}" href="{{ route('mou.index') }}">
                            <span class="sidebar-text">List MOU</span>
                        </a>
                    </div>
                </div>

                {{-- 3. Pendidikan (Dropdown) --}}
                @php
                    $isPendidikanActive =
                        request()->is('mahasiswa*') || request()->is('ruangan*') || request()->is('absensi*');
                @endphp
                <div class="nav-item-dropdown">
                    <a class="nav-link {{ $isPendidikanActive ? 'active-parent' : '' }}" data-bs-toggle="collapse"
                        href="#menuPendidikan" role="button"
                        aria-expanded="{{ $isPendidikanActive ? 'true' : 'false' }}" aria-controls="menuPendidikan">
                        <i class="bi bi-mortarboard"></i>
                        <span class="sidebar-text">Pendidikan</span>
                        <i class="bi bi-chevron-down sidebar-arrow"></i>
                    </a>
                    {{-- Sub-menu --}}
                    <div class="collapse sub-menu {{ $isPendidikanActive ? 'show' : '' }}" id="menuPendidikan">
                        <a class="nav-link {{ request()->is('mahasiswa*') ? 'active' : '' }}"
                            href="{{ route('mahasiswa.index') }}">
                            <span class="sidebar-text">Mahasiswa</span>
                        </a>
                        <a class="nav-link {{ request()->is('ruangan*') ? 'active' : '' }}"
                            href="{{ route('ruangan.index') }}">
                            <span class="sidebar-text">Ruangan</span>
                        </a>
                        <a class="nav-link {{ request()->is('absensi*') ? 'active' : '' }}"
                            href="{{ route('absensi.index') }}">
                            <span class="sidebar-text">Riwayat Absensi</span>
                        </a>
                    </div>
                </div>

                {{-- 4. Pelatihan (Link Tunggal) --}}
                @php
                    $isPelatihanActive = request()->is('pelatihan*');
                @endphp
                <div class="nav-item-dropdown">
                    <a class="nav-link {{ $isPelatihanActive ? 'active-parent' : '' }}" data-bs-toggle="collapse"
                        href="#menuPelatihan" role="button"
                        aria-expanded="{{ $isPelatihanActive ? 'true' : 'false' }}" aria-controls="menuPelatihan">
                        <i class="bi bi-people"></i>
                        <span class="sidebar-text">Pelatihan</span>
                        <i class="bi bi-chevron-down sidebar-arrow"></i>
                    </a>
                    {{-- Sub-menu --}}
                    <div class="collapse sub-menu {{ $isPelatihanActive ? 'show' : '' }}" id="menuPelatihan">
                        <a class="nav-link {{ request()->is('pelatihan') ? 'active' : '' }}"
                            href="{{ route('pelatihan.index') }}">
                            <span class="sidebar-text">List Pelatihan</span>
                        </a>
                    </div>
                </div>

                {{-- 5. Penelitian (Link Tunggal) --}}
                @php
                    $isPenelitianActive = request()->is('penelitian*');
                @endphp
                <div class="nav-item-dropdown">
                    <a class="nav-link {{ $isPenelitianActive ? 'active-parent' : '' }}" data-bs-toggle="collapse"
                        href="#menuPenelitian" role="button"
                        aria-expanded="{{ $isPenelitianActive ? 'true' : 'false' }}" aria-controls="menuPenelitian">
                        <i class="bi bi-journal-richtext"></i>
                        <span class="sidebar-text">Penelitian</span>
                        <i class="bi bi-chevron-down sidebar-arrow"></i>
                    </a>

                    <!-- Sub-menu -->
                    <div class="collapse sub-menu {{ $isPenelitianActive ? 'show' : '' }}" id="menuPenelitian">
                        <a class="nav-link {{ request()->is('penelitian*') ? 'active' : '' }}"
                            href="#">
                            <span class="sidebar-text">List Penelitian</span>
                        </a>
                    </div>
                </div>
            @endif
        </nav>
        {{-- ====================================================== --}}
        {{-- AKHIR STRUKTUR NAVIGASI BARU --}}
        {{-- ====================================================== --}}
    </div>

    {{-- User Profile di Bawah --}}
    <div class="p-3 sidebar-user-profile">
        <a class="nav-link logout-link" href="#"
            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="bi bi-box-arrow-right"></i>
            <span class="sidebar-text">Logout</span>
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>

        <hr class="logout-divider">

        <div class="d-flex align-items-center">
            @if (auth()->check())
                <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=7c1316&color=fff"
                    class="rounded-circle me-2" width="40" height="40" alt="User">
                <div class="sidebar-text">
                    <div class="fw-bold">{{ auth()->user()->name }}</div>
                    <small>{{ ucfirst(auth()->user()->role ?? 'user') }}</small>
                </div>
            @else
                <img src="https://ui-avatars.com/api/?name=Guest&background=7c1316&color=fff"
                    class="rounded-circle me-2" width="40" height="40" alt="User">
                <div class="sidebar-text">
                    <div class="fw-bold">Guest</div>
                    <small>Visitor</small>
                </div>
            @endif
        </div>
    </div>
</div>
<script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Temukan elemen input search
            const searchInput = document.querySelector('.sidebar-search .search-input');

            // 2. Tambahkan event listener 'input' (lebih baik dari keyup)
            if (searchInput) {
                searchInput.addEventListener('input', function(e) {
                    filterSidebar(e.target.value);
                });
            }
        });

        function filterSidebar(filterText) {
            const text = filterText.toLowerCase();
            const navContainer = document.querySelector('.sidebar-nav-container');

            // 3. Ambil semua item yang perlu difilter
            const headings = navContainer.querySelectorAll('.sidebar-heading');
            const items = navContainer.querySelectorAll('.sidebar-nav-container > .nav-link, .sidebar-nav-container > .nav-item-dropdown');
            const allSubLinks = navContainer.querySelectorAll('.sub-menu .nav-link');

            // --- 4. RESET STATE (Jika search kosong) ---
            if (text === '') {
                headings.forEach(h => h.style.display = 'block');
                items.forEach(item => item.style.display = 'block');
                allSubLinks.forEach(sub => sub.style.display = ''); // Reset display ke default CSS

                // Tutup semua sub-menu, KECUALI yang sedang aktif
                navContainer.querySelectorAll('.sub-menu').forEach(sub => {
                    const parentLink = sub.closest('.nav-item-dropdown').querySelector('[data-bs-toggle="collapse"]');

                    // Jika parent-nya TIDAK punya class 'active-parent'
                    if (!parentLink.classList.contains('active-parent')) {
                        sub.classList.remove('show');
                        parentLink.setAttribute('aria-expanded', 'false');
                    }
                });
                return; // Selesai
            }

            // --- 5. FILTERING STATE (Jika ada teks di search) ---

            // Sembunyikan semua heading dan sub-link dulu
            headings.forEach(h => h.style.display = 'none');
            allSubLinks.forEach(sub => sub.style.display = 'none'); // Akan kita tampilkan satu per satu jika cocok

            items.forEach(item => {
                let groupHasMatch = false;

                // Cek 1: Apakah teks link utama cocok? (Cth: "Pendidikan")
                const mainLink = item.matches('.nav-link') ? item : item.querySelector('[data-bs-toggle="collapse"]');
                const mainText = mainLink.querySelector('.sidebar-text')?.textContent.toLowerCase() || '';

                if (mainText.includes(text)) {
                    groupHasMatch = true;
                }

                // Cek 2: Apakah ada sub-link yang cocok? (Cth: "Mahasiswa")
                if (item.matches('.nav-item-dropdown')) {
                    const subLinks = item.querySelectorAll('.sub-menu .nav-link');

                    subLinks.forEach(subLink => {
                        const subText = subLink.textContent.toLowerCase();
                        if (subText.includes(text)) {
                            groupHasMatch = true;
                            subLink.style.display = ''; // Tampilkan sub-link yang cocok
                        }
                    });
                }

                // --- 6. TAMPILKAN / SEMBUNYIKAN GRUP ---
                if (groupHasMatch) {
                    item.style.display = 'block'; // Tampilkan grup ini

                    // Jika ini dropdown, paksa buka untuk menunjukkan sub-link yang cocok
                    if (item.matches('.nav-item-dropdown')) {
                        item.querySelector('.sub-menu').classList.add('show');
                        item.querySelector('[data-bs-toggle="collapse"]').setAttribute('aria-expanded', 'true');
                    }

                    // Tampilkan heading yang mendahului item ini
                    let heading = item.previousElementSibling;
                    while (heading) {
                        if (heading.classList.contains('sidebar-heading')) {
                            heading.style.display = 'block';
                            break; // Cukup temukan satu heading di atasnya
                        }
                        heading = heading.previousElementSibling;
                    }

                } else {
                    item.style.display = 'none'; // Sembunyikan grup ini
                }
            });
        }
    </script>
<style>
    /* ========================================= */
    /* --- STYLING SIDEBAR BARU (PILL UI/UX) --- */
    /* ========================================= */
    :root {
        /* Sesuaikan variabel ini dengan tema Anda jika perlu */
        --maroon: #7c1316;
        --maroon-light: #a3191d;
        --sidebar-bg: var(--maroon);
        --sidebar-text-color: #e0e0e0;
        --sidebar-text-active: #ffffff;
        --sidebar-pill-hover: rgba(255, 255, 255, 0.1);
        --sidebar-pill-active: var(--maroon-light);
        --sidebar-heading-color: rgba(255, 255, 255, 0.5);
        --transition-speed: 0.25s;
    }

    .sidebar {
        width: 250px;
        min-height: 100vh;
        background: var(--sidebar-bg);
        color: var(--sidebar-text-color);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        position: fixed;
        top: 0;
        left: 0;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        z-index: 1000;
        transition: width var(--transition-speed) ease;
    }

    .sidebar.collapsed {
        width: 80px;
        /* Sedikit lebih lebar untuk pill ikon */
    }

    /* --- Header & Logo --- */
    .sidebar-header {
        text-align: center;
        padding: 1.5rem 1rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        position: relative;
        height: 121px;
        /* Samakan tinggi dengan logo */
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .image-sidebar {
        width: 77%;
        height: 90px;
        object-fit: cover;
        border-radius: 8px;
        transition: opacity 0.1s;
    }

    .sidebar.collapsed .image-sidebar {
        opacity: 0;
        display: none;
    }

    .sidebar-toggle {
        position: absolute;
        right: -15px;
        /* Taruh di luar sidebar */
        top: 30px;
        transform: translateY(-50%);
        background: #fff;
        border: 1px solid #e0e0e0;
        color: var(--maroon);
        border-radius: 50%;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all var(--transition-speed);
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        z-index: 1001;
        /* Di atas segalanya */
    }

    .sidebar.collapsed .sidebar-toggle {
        /* Posisikan ulang saat collapsed */
        right: -15px;
    }

    .sidebar-toggle:hover {
        background: var(--maroon);
        color: white;
        border-color: var(--maroon);
        transform: scale(1.1);
    }

    .sidebar-toggle i {
        transition: transform var(--transition-speed) ease;
    }

    .sidebar.collapsed .sidebar-toggle i {
        transform: rotate(180deg);
    }

    /* --- Search Bar --- */
    .sidebar-search {
        padding: 1rem;
    }

    .search-container {
        position: relative;
    }

    .search-input {
        width: 100%;
        padding: 0.6rem 2.2rem 0.6rem 1rem;
        border-radius: 8px;
        border: none;
        background: rgba(0, 0, 0, 0.15);
        color: white;
        transition: all var(--transition-speed);
        font-size: 0.9rem;
    }

    .search-input::placeholder {
        color: rgba(255, 255, 255, 0.6);
    }

    .search-input:focus {
        outline: none;
        background: rgba(0, 0, 0, 0.3);
        box-shadow: 0 0 0 2px var(--maroon-light);
    }

    .search-icon {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: rgba(255, 255, 255, 0.6);
    }

    /* Sembunyikan search saat collapsed */
    .sidebar.collapsed .sidebar-search {
        display: none;
    }

    /* --- Navigasi Container --- */
    .sidebar-nav-container {
        flex-grow: 1;
        overflow-y: auto;
        overflow-x: hidden;
        padding: 0 1rem;
        /* Beri padding untuk "pill" */
    }

    /* Judul Grup (MENU UTAMA, etc) */
    .sidebar-heading {
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--sidebar-heading-color);
        padding: 1.5rem 0.5rem 0.5rem;
        white-space: nowrap;
        overflow: hidden;
        transition: opacity var(--transition-speed);
    }

    .sidebar.collapsed .sidebar-heading {
        /* Saat collapsed, sembunyikan teks tapi sisakan margin */
        padding-top: 1rem;
        padding-bottom: 0;
    }

    .sidebar.collapsed .sidebar-heading .sidebar-text {
        display: none;
    }


    /* --- Link Navigasi (Pill Style) --- */
    .nav-link {
        color: var(--sidebar-text-color);
        padding: 0.7rem 0.8rem;
        display: flex;
        align-items: center;
        border-radius: 8px;
        margin: 0.15rem 0;
        transition: all var(--transition-speed) ease;
        position: relative;
        white-space: nowrap;
        overflow: hidden;
    }

    .nav-link i {
        margin-right: 12px;
        font-size: 1.2rem;
        min-width: 24px;
        /* Jaga agar teks rapi */
        text-align: center;
        transition: all var(--transition-speed) ease;
    }

    /* State Hover (Link manapun) */
    .nav-link:hover {
        background: var(--sidebar-pill-hover);
        color: var(--sidebar-text-active);
    }

    /* State Active (Link tunggal atau link child) */
    .nav-link.active {
        background: var(--sidebar-pill-active);
        color: var(--sidebar-text-active);
        font-weight: 500;
    }

    /* State Active (Hanya Parent dari dropdown yg terbuka) */
    .nav-link.active-parent {
        color: var(--sidebar-text-active);
    }

    /* --- Style Saat Collapsed --- */
    .sidebar.collapsed .nav-link {
        padding: 0.7rem 0;
        justify-content: center;
    }

    .sidebar.collapsed .nav-link i {
        margin-right: 0;
        font-size: 1.3rem;
    }

    .sidebar.collapsed .sidebar-text {
        display: none;
    }

    .sidebar.collapsed .sidebar-arrow {
        display: none;
    }

    .sidebar.collapsed .sub-menu {
        display: none !important;
    }


    /* --- Dropdown Arrow --- */
    .sidebar-arrow {
        font-size: 0.8rem;
        margin-left: auto;
        transition: transform var(--transition-speed) ease;
    }

    .nav-link[aria-expanded="true"] .sidebar-arrow {
        transform: rotate(180deg);
    }

    /* --- Sub-Menu (Line-and-Dot Style) --- */
    .sub-menu {
        position: relative;
        padding-left: 2.1rem;
        /* Ruang untuk garis dan dot */
        margin-left: 0.8rem;
        /* Sejajarkan dengan ikon parent */
    }

    /* Garis Vertikal */
    .sub-menu::before {
        content: '';
        position: absolute;
        left: 0;
        top: 10px;
        bottom: 10px;
        width: 2px;
        background: rgba(255, 255, 255, 0.15);
        border-radius: 2px;
    }

    .sub-menu .nav-link {
        padding: 0.5rem 0.5rem;
        font-size: 0.9rem;
        position: relative;
        background: transparent !important;
        /* Hapus pill style */
    }

    /* Dot Indikator */
    .sub-menu .nav-link::before {
        content: '';
        position: absolute;
        left: -1.3rem;
        /* Posisikan di atas garis */
        top: 50%;
        transform: translateY(-50%);
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.4);
        transition: all var(--transition-speed);
    }

    /* State Hover (Sub-menu) */
    .sub-menu .nav-link:hover {
        color: var(--sidebar-text-active);
    }

    .sub-menu .nav-link:hover::before {
        background: var(--sidebar-text-active);
    }

    /* State Active (Sub-menu) */
    .sub-menu .nav-link.active {
        color: var(--sidebar-text-active);
        font-weight: 500;
        background: transparent !important;
        /* Pastikan transparan */
    }

    .sub-menu .nav-link.active::before {
        background: var(--sidebar-text-active);
        transform: translateY(-50%) scale(1.3);
        /* Besarkan dot aktif */
    }

    /* --- User Profile & Logout --- */
    .sidebar-user-profile {
        padding: 1rem;
        background: rgba(0, 0, 0, 0.1);
    }

    .sidebar.collapsed .sidebar-user-profile {
        padding: 0.5rem;
    }

    .sidebar.collapsed .sidebar-user-profile .sidebar-text,
    .sidebar.collapsed .sidebar-user-profile .logout-divider {
        display: none;
    }

    .sidebar.collapsed .sidebar-user-profile .logout-link {
        justify-content: center;
    }

    .sidebar.collapsed .sidebar-user-profile .logout-link .sidebar-text {
        display: none;
    }

    .logout-link {
        padding: 0.5rem;
        color: var(--sidebar-text-color);
        font-weight: 500;
        margin-bottom: 0.5rem;
    }

    .logout-link:hover {
        background: var(--sidebar-pill-hover);
        color: var(--sidebar-text-active);
    }

    .logout-divider {
        border-color: rgba(255, 255, 255, 0.1);
        margin: 0.5rem 0 1rem;
    }
</style>
