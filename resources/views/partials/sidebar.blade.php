<div class="sidebar">
    <div class="sidebar-inner">
        <div class="sidebar-header">
            <img class="image-sidebar" src="{{ asset('icon.png') }}" alt="Logo">
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="bi bi-chevron-left"></i>
            </button>
        </div>

        <div class="sidebar-search">
            <div class="search-container">
                <input type="text" class="search-input" placeholder="Cari menu...">
                <i class="bi bi-search search-icon"></i>
            </div>
        </div>

        <nav class="nav flex-column sidebar-nav-container">

            {{-- ========== MENU ADMIN ========== --}}
            @if (auth()->check() && auth()->user()->role === 'admin')

                <div class="sidebar-heading">
                    <span class="sidebar-text">Menu Utama</span>
                </div>

                <a class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                    <i class="bi bi-house-door"></i>
                    <span class="sidebar-text">Dashboard</span>
                </a>

                <a class="nav-link {{ request()->is('users*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                    <i class="bi bi-person-gear"></i>
                    <span class="sidebar-text">Manajemen User</span>
                </a>

                <a class="nav-link {{ request()->is('admin/pengajuan*') ? 'active' : '' }}"
                   href="{{ route('admin.pengajuan.index') }}">
                    <i class="bi bi-hourglass-split"></i>
                    <span class="sidebar-text">Approval Pengajuan</span>
                </a>

                <a class="nav-link {{ request()->is('surat-balasan*') ? 'active' : '' }}"
                   href="{{ route('surat-balasan.index') }}">
                    <i class="bi bi-envelope-paper"></i>
                    <span class="sidebar-text">Surat Balasan</span>
                </a>

                {{-- MoU --}}
                @php $isMouActive = request()->is('mou*'); @endphp
                <div class="nav-item-dropdown">
                    <a class="nav-link {{ $isMouActive ? 'active-parent' : '' }}"
                       data-bs-toggle="collapse" href="#menuMou" role="button"
                       aria-expanded="{{ $isMouActive ? 'true' : 'false' }}">
                        <i class="bi bi-file-earmark-text"></i>
                        <span class="sidebar-text">MoU</span>
                        <i class="bi bi-chevron-down sidebar-arrow"></i>
                    </a>
                    <div class="collapse sub-menu {{ $isMouActive ? 'show' : '' }}" id="menuMou">
                        <a class="nav-link {{ request()->is('mou') ? 'active' : '' }}"
                           href="{{ route('mou.index') }}">
                            <span class="sidebar-text">List MOU</span>
                        </a>
                    </div>
                </div>

                {{-- Pendidikan --}}
                @php
                    $isPendidikanActive = request()->is('mahasiswa*') ||
                                          request()->is('ruangan*') ||
                                          request()->is('room_sequences*') ||
                                          request()->is('room_schedules*') ||
                                          request()->is('absensi*');
                @endphp
                <div class="nav-item-dropdown">
                    <a class="nav-link {{ $isPendidikanActive ? 'active-parent' : '' }}"
                       data-bs-toggle="collapse" href="#menuPendidikan" role="button"
                       aria-expanded="{{ $isPendidikanActive ? 'true' : 'false' }}">
                        <i class="bi bi-mortarboard"></i>
                        <span class="sidebar-text">Pendidikan</span>
                        <i class="bi bi-chevron-down sidebar-arrow"></i>
                    </a>
                    <div class="collapse sub-menu {{ $isPendidikanActive ? 'show' : '' }}" id="menuPendidikan">
                        <a class="nav-link {{ request()->is('mahasiswa*') ? 'active' : '' }}"
                           href="{{ route('mahasiswa.index') }}">
                            <span class="sidebar-text">Mahasiswa</span>
                        </a>
                        <a class="nav-link {{ request()->is('ruangan*') ? 'active' : '' }}"
                           href="{{ route('ruangan.index') }}">
                            <span class="sidebar-text">Ruangan</span>
                        </a>
                        <a class="nav-link {{ request()->is('room_sequences*') ? 'active' : '' }}"
                           href="{{ route('room_sequences.index') }}">
                            <span class="sidebar-text">Jadwal Rolling Ruangan</span>
                        </a>
                        <a class="nav-link {{ request()->is('room_schedules*') ? 'active' : '' }}"
                           href="{{ route('room_schedules.index') }}">
                            <span class="sidebar-text">Jadwal Penghuni Aktual</span>
                        </a>
                        <a class="nav-link {{ request()->is('absensi*') ? 'active' : '' }}"
                           href="{{ route('absensi.index') }}">
                            <span class="sidebar-text">Riwayat Absensi</span>
                        </a>
                    </div>
                </div>

                {{-- Pelatihan --}}
                @php $isPelatihanActive = request()->is('pelatihan*'); @endphp
                <div class="nav-item-dropdown">
                    <a class="nav-link {{ $isPelatihanActive ? 'active-parent' : '' }}"
                       data-bs-toggle="collapse" href="#menuPelatihan" role="button"
                       aria-expanded="{{ $isPelatihanActive ? 'true' : 'false' }}">
                        <i class="bi bi-people"></i>
                        <span class="sidebar-text">Pelatihan</span>
                        <i class="bi bi-chevron-down sidebar-arrow"></i>
                    </a>
                    <div class="collapse sub-menu {{ $isPelatihanActive ? 'show' : '' }}" id="menuPelatihan">
                        <a class="nav-link {{ request()->is('pelatihan') ? 'active' : '' }}"
                           href="{{ route('pelatihan.index') }}">
                            <span class="sidebar-text">List Pelatihan</span>
                        </a>
                        <a class="nav-link" href="{{ route('public.pelatihan.index') }}">
                            <span class="sidebar-text">Search Pelatihan</span>
                        </a>
                    </div>
                </div>

                    {{-- Kelola Diklat --}}
                    <a class="nav-link {{ request()->is('diklat*') ? 'active' : '' }}" href="{{ route('diklat.index') }}">
                        <i class="bi bi-clipboard-check"></i>
                        <span class="sidebar-text">Kelola Diklat</span>
                    </a>

                {{-- Penelitian --}}
                @php
                    $isPenelitianActive = request()->is('pra-penelitian*') ||
                                          request()->is('admin/presentasi*');
                @endphp
                <div class="nav-item-dropdown">
                    <a class="nav-link {{ $isPenelitianActive ? 'active-parent' : '' }}"
                       data-bs-toggle="collapse" href="#menuPenelitian" role="button"
                       aria-expanded="{{ $isPenelitianActive ? 'true' : 'false' }}">
                        <i class="bi bi-journal-richtext"></i>
                        <span class="sidebar-text">Penelitian</span>
                        <i class="bi bi-chevron-down sidebar-arrow"></i>
                    </a>
                    <div class="collapse sub-menu {{ $isPenelitianActive ? 'show' : '' }}" id="menuPenelitian">
                        <a class="nav-link {{ request()->is('pra-penelitian*') ? 'active' : '' }}"
                           href="{{ route('pra-penelitian.index') }}">
                            <span class="sidebar-text">Pra-Penelitian</span>
                        </a>
                        <a class="nav-link {{ request()->is('admin/presentasi*') ? 'active' : '' }}"
                           href="{{ route('admin.presentasi.index') }}">
                            <span class="sidebar-text">Presentasi</span>
                        </a>
                    </div>
                </div>
            @endif

            {{-- ========== MENU USER ========== --}}
            @if (auth()->check() && auth()->user()->role === 'user')

                <div class="sidebar-heading">
                    <span class="sidebar-text">Menu Utama</span>
                </div>

                <a class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                    <i class="bi bi-house-door"></i>
                    <span class="sidebar-text">Dashboard</span>
                </a>

                @php
                    $userId       = auth()->id();
                    $pra          = \App\Models\Pengajuan::where('user_id', $userId)->where('jenis', 'pra_penelitian')->latest()->first();
                    $magang       = \App\Models\Pengajuan::where('user_id', $userId)->where('jenis', 'magang')->latest()->first();
                    $hasPraAccess = $pra && $pra->status === 'approved';
                    $hasMagangAccess = $magang && $magang->status === 'approved';
                    $hasCIAccess  = $hasPraAccess && $pra->ci_nama;
                    $presentasi   = $hasCIAccess ? \App\Models\Presentasi::where('user_id', $userId)->first() : null;
                @endphp

                <div class="sidebar-heading mt-2">
                    <span class="sidebar-text">Layanan</span>
                </div>

                <a class="nav-link {{ request()->is('pengajuan') && !request()->is('pengajuan/detail*') ? 'active' : '' }}"
                   href="{{ route('pengajuan.index') }}">
                    <i class="bi bi-grid-1x2"></i>
                    <span class="sidebar-text">Pengajuan & Status</span>
                </a>

                @if ($hasMagangAccess)
                    <div class="sidebar-heading mt-2">
                        <span class="sidebar-text">Aktivitas Magang</span>
                    </div>
                    <a class="nav-link {{ request()->routeIs('mahasiswa.dashboard') ? 'active' : '' }}"
                       href="{{ route('mahasiswa.dashboard') }}">
                        <i class="bi bi-briefcase"></i>
                        <span class="sidebar-text">Dashboard Magang</span>
                    </a>
                @endif

                @if ($hasPraAccess)
                    <div class="sidebar-heading mt-2">
                        <span class="sidebar-text">Aktivitas Penelitian</span>
                    </div>

                    <a class="nav-link {{ request()->is('pengajuan/detail/pra_penelitian') ? 'active' : '' }}"
                       href="{{ route('pengajuan.detail', 'pra_penelitian') }}">
                        <i class="bi bi-bar-chart-line"></i>
                        <span class="sidebar-text">Detail Penelitian</span>
                    </a>

                    @if ($hasCIAccess)
                        <a class="nav-link {{ request()->is('konsultasi*') ? 'active' : '' }}"
                           href="{{ route('konsultasi.index') }}">
                            <i class="bi bi-chat-dots"></i>
                            <span class="sidebar-text">Konsultasi</span>
                        </a>
                    @endif

                    @if ($presentasi)
                        <a class="nav-link {{ request()->is('presentasi*') ? 'active' : '' }}"
                           href="{{ route('presentasi.show') }}">
                            <i class="bi bi-easel"></i>
                            <span class="sidebar-text">Presentasi</span>
                        </a>
                    @endif
                @endif
            @endif
        </nav>
    </div>

    <div class="sidebar-footer">
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
                    @php
                        $user = auth()->user();
                        $mahasiswa = null;
                        if ($user->role === 'user') {
                            $mahasiswa = \App\Models\Mahasiswa::where('user_id', $user->id)->first();
                        }
                        $hasFoto = $mahasiswa && !empty($mahasiswa->foto_path) && file_exists(public_path($mahasiswa->foto_path));
                    @endphp

                    @if ($hasFoto)
                        <img src="{{ asset($mahasiswa->foto_path) }}"
                             class="rounded-circle me-2 object-fit-cover"
                             width="40" height="40" alt="User">
                    @else
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=7c1316&color=fff"
                             class="rounded-circle me-2" width="40" height="40" alt="User">
                    @endif

                    <div class="sidebar-text">
                        <div class="fw-bold text-truncate" style="max-width: 140px;">{{ $user->name }}</div>
                        <small>{{ ucfirst($user->role ?? 'user') }}</small>
                    </div>
                @else
                    <img src="https://ui-avatars.com/api/?name=Guest&background=7c1316&color=fff"
                         class="rounded-circle me-2" width="40" height="40" alt="Guest">
                    <div class="sidebar-text">
                        <div class="fw-bold">Guest</div>
                        <small>Visitor</small>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    // HANYA SEARCH / FILTER SIDEBAR DI SINI
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.querySelector('.sidebar-search .search-input');
        if (searchInput) {
            searchInput.addEventListener('input', function(e) {
                filterSidebar(e.target.value);
            });
        }
    });

    function filterSidebar(filterText) {
        const text = filterText.toLowerCase();
        const navContainer = document.querySelector('.sidebar-nav-container');
        if (!navContainer) return;

        const headings = navContainer.querySelectorAll('.sidebar-heading');
        const items = navContainer.querySelectorAll(
            '.sidebar-nav-container > .nav-link, .sidebar-nav-container > .nav-item-dropdown'
        );
        const allSubLinks = navContainer.querySelectorAll('.sub-menu .nav-link');

        if (text === '') {
            headings.forEach(h => h.style.display = 'block');
            items.forEach(item => item.style.display = 'block');
            allSubLinks.forEach(sub => sub.style.display = '');

            navContainer.querySelectorAll('.sub-menu').forEach(sub => {
                const parentLink = sub.closest('.nav-item-dropdown').querySelector('[data-bs-toggle="collapse"]');
                if (!parentLink.classList.contains('active-parent')) {
                    sub.classList.remove('show');
                    parentLink.setAttribute('aria-expanded', 'false');
                }
            });
            return;
        }

        headings.forEach(h => h.style.display = 'none');
        allSubLinks.forEach(sub => sub.style.display = 'none');

        items.forEach(item => {
            let groupHasMatch = false;
            const mainLink = item.matches('.nav-link') ? item : item.querySelector('[data-bs-toggle="collapse"]');
            const mainText = mainLink.querySelector('.sidebar-text')?.textContent.toLowerCase() || '';

            if (mainText.includes(text)) {
                groupHasMatch = true;
            }

            if (item.matches('.nav-item-dropdown')) {
                const subLinks = item.querySelectorAll('.sub-menu .nav-link');
                subLinks.forEach(subLink => {
                    const subText = subLink.textContent.toLowerCase();
                    if (subText.includes(text)) {
                        groupHasMatch = true;
                        subLink.style.display = '';
                    }
                });
            }

            if (groupHasMatch) {
                item.style.display = 'block';
                if (item.matches('.nav-item-dropdown')) {
                    item.querySelector('.sub-menu').classList.add('show');
                    item.querySelector('[data-bs-toggle="collapse"]').setAttribute('aria-expanded', 'true');
                }
                let heading = item.previousElementSibling;
                while (heading) {
                    if (heading.classList.contains('sidebar-heading')) {
                        heading.style.display = 'block';
                        break;
                    }
                    heading = heading.previousElementSibling;
                }
            } else {
                item.style.display = 'none';
            }
        });
    }
</script>

<style>
    :root {
        --maroon: #7c1316;
        --maroon-light: #a3191d;
        --sidebar-bg: var(--maroon);
        --sidebar-text-color: #e0e0e0;
        --sidebar-text-active: #ffffff;
        --sidebar-pill-hover: rgba(255, 255, 255, 0.1);
        --sidebar-pill-active: var(--maroon-light);
        --sidebar-heading-color: rgba(255, 255, 255, 0.5);
        --transition-speed: 0.25s;
        text-decoration: none !important;
    }

    .sidebar {
        width: 250px;
        height: 100vh;
        background: var(--sidebar-bg);
        color: var(--sidebar-text-color);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        position: fixed;
        top: 0;
        left: 0;
        display: flex;
        flex-direction: column;
        z-index: 1000;
        transition: width var(--transition-speed) ease;
        overflow: hidden;
    }

    .sidebar.collapsed {
        width: 80px;
    }

    .sidebar-inner {
        flex: 1;
        overflow-y: auto;
        overflow-x: hidden;
        display: flex;
        flex-direction: column;
        scroll-behavior: smooth;
    }

    .sidebar-inner::-webkit-scrollbar {
        width: 6px;
    }

    .sidebar-inner::-webkit-scrollbar-track {
        background: rgba(0, 0, 0, 0.2);
        border-radius: 10px;
    }

    .sidebar-inner::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.3);
        border-radius: 10px;
        transition: background 0.3s;
    }

    .sidebar-inner::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.5);
    }

    .sidebar-inner {
        scrollbar-width: thin;
        scrollbar-color: rgba(255, 255, 255, 0.3) rgba(0, 0, 0, 0.2);
    }

    .sidebar-header {
        text-align: center;
        padding: 1.5rem 1rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        position: relative;
        height: 121px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
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
        top: 50%;
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
    }

    .sidebar-toggle:hover {
        background: var(--maroon);
        color: white;
        border-color: var(--maroon);
        transform: translateY(-50%) scale(1.1);
    }

    .sidebar-search {
        padding: 1rem;
        flex-shrink: 0;
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

    .sidebar.collapsed .sidebar-search {
        display: none;
    }

    .sidebar-nav-container {
        flex-grow: 1;
        padding: 0 1rem 1rem 1rem;
        overflow-y: visible;
    }

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

    .sidebar.collapsed .sidebar-heading .sidebar-text {
        display: none;
    }

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
        text-decoration: none;
        animation: fadeIn 0.3s ease;
    }

    .nav-link i {
        margin-right: 12px;
        font-size: 1.2rem;
        min-width: 24px;
        text-align: center;
        transition: all var(--transition-speed) ease;
    }

    .nav-link:hover {
        background: var(--sidebar-pill-hover);
        color: var(--sidebar-text-active);
        text-decoration: none;
    }

    .nav-link.active {
        background: var(--sidebar-pill-active);
        color: var(--sidebar-text-active);
        font-weight: 500;
    }

    .nav-link.active-parent {
        color: var(--sidebar-text-active);
    }

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

    .sidebar-arrow {
        font-size: 0.8rem;
        margin-left: auto;
        transition: transform var(--transition-speed) ease;
    }

    .nav-link[aria-expanded="true"] .sidebar-arrow {
        transform: rotate(180deg);
    }

    .sub-menu {
        position: relative;
        padding-left: 2.1rem;
        margin-left: 0.8rem;
    }

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
    }

    .sub-menu .nav-link::before {
        content: '';
        position: absolute;
        left: -1.3rem;
        top: 50%;
        transform: translateY(-50%);
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.4);
        transition: all var(--transition-speed);
    }

    .sub-menu .nav-link:hover::before {
        background: var(--sidebar-text-active);
    }

    .sub-menu .nav-link.active::before {
        background: var(--sidebar-text-active);
        transform: translateY(-50%) scale(1.3);
    }

    .sidebar-footer {
        flex-shrink: 0;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        background: var(--sidebar-bg);
    }

    .sidebar-user-profile {
        padding: 1rem;
    }

    .sidebar.collapsed .sidebar-user-profile .sidebar-text,
    .sidebar.collapsed .sidebar-user-profile .logout-divider {
        display: none;
    }

    .sidebar.collapsed .sidebar-user-profile .logout-link {
        justify-content: center;
    }

    .logout-link {
        padding: 0.5rem;
        color: var(--sidebar-text-color);
        font-weight: 500;
        margin-bottom: 0.5rem;
        border-radius: 8px;
        transition: all var(--transition-speed);
        text-decoration: none;
        display: flex;
        align-items: center;
    }

    .logout-link i {
        margin-right: 12px;
        font-size: 1.2rem;
    }

    .sidebar.collapsed .logout-link i {
        margin-right: 0;
    }

    .logout-link:hover {
        background: var(--sidebar-pill-hover);
        color: var(--sidebar-text-active);
        text-decoration: none;
    }

    .logout-divider {
        border-color: rgba(255, 255, 255, 0.1);
        margin: 0.5rem 0 1rem;
    }

    @media (max-width: 768px) {
        .sidebar {
            width: 80px;
        }

        .sidebar.collapsed {
            width: 0;
            transform: translateX(-100%);
        }

        .sidebar-toggle {
            display: none;
        }
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
