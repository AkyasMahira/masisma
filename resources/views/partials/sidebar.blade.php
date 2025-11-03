{{-- Sidebar --}}
<div class="sidebar">
    <div>
        <div class="sidebar-header">
            <h4 class="sidebar-text">Masisma</h4>
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

        <nav class="nav flex-column">
            <a class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                <i class="bi bi-house-door"></i>
                <span class="sidebar-text">Dashboard</span>
            </a>
            @if (auth()->check() && auth()->user()->role === 'admin')
                <a class="nav-link {{ request()->is('ruangan') ? 'active' : '' }}" href="{{ route('ruangan.index') }}">
                    <i class="bi bi-bar-chart"></i>
                    <span class="sidebar-text">Ruangan</span>
                </a>
                <a class="nav-link {{ request()->is('mahasiswa') ? 'active' : '' }}"
                    href="{{ route('mahasiswa.index') }}">
                    <i class="bi bi-people"></i>
                    <span class="sidebar-text">Mahasiswa</span>
                </a>
                <a class="nav-link {{ request()->is('absensi') ? 'active' : '' }}" href="{{ route('absensi.index') }}">
                    <i class="bi bi-clock-history"></i>
                    <span class="sidebar-text">Riwayat Absensi</span>
                </a>
            @endif
            <hr>
            {{-- Logout: use POST form to call the named logout route --}}
            <a class="nav-link" href="#"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="bi bi-box-arrow-right"></i>
                <span class="sidebar-text">Logout</span>
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </nav>
    </div>

    <div class="p-3">
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

<style>
    /* Sidebar Styles */
    .sidebar {
        width: 250px;
        min-height: 100vh;
        background: var(--maroon);
        color: #fff;
        box-shadow: var(--shadow);
        position: fixed;
        top: 0;
        left: 0;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        z-index: 1000;
        transition: all var(--transition-speed) ease;
    }

    .sidebar.collapsed {
        width: 70px;
    }

    .sidebar.collapsed .sidebar-text {
        display: none;
    }

    .sidebar.collapsed .nav-link {
        justify-content: center;
        padding: 0.8rem 0.5rem;
    }

    .sidebar.collapsed .nav-link i {
        margin-right: 0;
    }

    .sidebar-header {
        text-align: center;
        padding: 1.5rem 1rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        position: relative;
    }

    .sidebar-header h4 {
        font-weight: 600;
        letter-spacing: 0.5px;
        transition: opacity var(--transition-speed);
    }

    .sidebar-toggle {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(255, 255, 255, 0.1);
        border: none;
        color: white;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: background var(--transition-speed);
    }

    .sidebar-toggle:hover {
        background: rgba(255, 255, 255, 0.2);
    }

    .nav-link {
        color: #fff;
        padding: 0.8rem 1.25rem;
        display: flex;
        align-items: center;
        border-radius: 6px;
        margin: 0.2rem 0.5rem;
        transition: all var(--transition-speed) ease;
        position: relative;
        overflow: hidden;
    }

    .nav-link i {
        margin-right: 8px;
        font-size: 1.1rem;
        transition: margin-right var(--transition-speed);
    }

    .nav-link.active,
    .nav-link:hover {
        background: var(--maroon-light);
        transform: translateX(4px);
        color: white;
    }

    .nav-link::after {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        width: 4px;
        background: white;
        transform: scaleY(0);
        transition: transform var(--transition-speed) ease;
    }

    .nav-link.active::after {
        transform: scaleY(1);
    }

    .badge-notification {
        position: absolute;
        right: 10px;
        background: #ff4757;
        color: white;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.1);
        }

        100% {
            transform: scale(1);
        }
    }

    /* Search Bar */
    .sidebar-search {
        padding: 0.5rem 1rem;
        margin-bottom: 1rem;
    }

    .search-container {
        position: relative;
    }

    .search-input {
        width: 100%;
        padding: 0.5rem 2rem 0.5rem 0.8rem;
        border-radius: 20px;
        border: none;
        background: rgba(255, 255, 255, 0.1);
        color: white;
        transition: background var(--transition-speed);
    }

    .search-input::placeholder {
        color: rgba(255, 255, 255, 0.7);
    }

    .search-input:focus {
        outline: none;
        background: rgba(255, 255, 255, 0.2);
    }

    .search-icon {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        color: rgba(255, 255, 255, 0.7);
    }
</style>
