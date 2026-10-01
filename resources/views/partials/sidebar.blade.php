<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div class="sidebar" id="sidebar">
    <div class="sidebar-inner">
        <div class="sidebar-header">
            <img class="image-sidebar" src="{{ asset('icon.png') }}" alt="Logo">
            <button class="sidebar-toggle d-none d-md-flex" id="sidebarToggle">
                <i class="bi bi-chevron-left"></i>
            </button>
            <button class="sidebar-close-mobile d-md-none" id="sidebarCloseMobile">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <div class="sidebar-search">
            <div class="search-container">
                <input type="text" class="search-input" placeholder="Cari menu...">
                <i class="bi bi-search search-icon"></i>
            </div>
        </div>

        <nav class="nav flex-column sidebar-nav-container">

            {{-- ========== MENU ADMIN & KASIR ========== --}}
            @if (auth()->check() && in_array(auth()->user()->role, ['admin', 'kasir']))
                <div class="sidebar-heading animate-item">
                    <span class="sidebar-text">Menu Utama</span>
                </div>

                <a class="nav-link animate-item {{ request()->is('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                    <i class="bi bi-house-door"></i>
                    <span class="sidebar-text">Dashboard</span>
                </a>

                {{-- Menu Invoice Muncul untuk Admin & Kasir --}}
                <a class="nav-link animate-item {{ Route::is('admin.invoices.*') ? 'active' : '' }}" href="{{ route('admin.invoices.index') }}">
                    <i class="bi bi-receipt"></i>
                    <span class="sidebar-text">Billing Invoice</span>
                </a>
            @endif

            {{-- ========== MENU KHUSUS ADMIN SAJA ========== --}}
            @if (auth()->check() && auth()->user()->role === 'admin')
                <a class="nav-link animate-item {{ request()->is('users*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                    <i class="bi bi-person-gear"></i>
                    <span class="sidebar-text">Akun User</span>
                </a>

                <a class="nav-link animate-item {{ request()->is('admin/pengajuan*') ? 'active' : '' }}" href="{{ route('admin.pengajuan.index') }}">
                    <i class="bi bi-hourglass-split"></i>
                    <span class="sidebar-text">Pengajuan User</span>
                </a>

                <a class="nav-link animate-item {{ request()->routeIs('admin.ci.*') ? 'active' : '' }}" href="{{ route('admin.ci.index') }}">
                    <i class="bi bi-person-fill-add"></i>
                    <span class="sidebar-text">Clinical Instructur</span>
                </a>
                
                {{-- MoU --}}
                <div class="nav-item-dropdown animate-item">
                    <a class="nav-link {{ request()->is('mou') ? 'active' : '' }}" href="{{ route('mou.index') }}">
                        <i class="bi bi-file-earmark-text"></i>
                        <span class="sidebar-text">MOU</span>
                    </a>
                </div>

                {{-- Booking Ruangan Instansi --}}
                <div class="nav-item-dropdown animate-item">
                    <a class="nav-link {{ request()->is('admin/booking*') ? 'active' : '' }}" href="{{ route('admin.booking.index') }}">
                        <i class="bi bi-calendar-check"></i>
                        <span class="sidebar-text">Booking Instansi</span>
                    </a>
                </div>
                

 
                {{-- Orientasi --}}
                @php $isMateriActive = request()->is('admin.materi*') || request()->is('admin/orientasi*'); @endphp
                <div class="nav-item-dropdown animate-item">
                    <a class="nav-link {{ $isMateriActive ? 'active-parent' : '' }}" data-bs-toggle="collapse" href="#menuMou" role="button">
                        <i class="bi bi-clipboard"></i>
                        <span class="sidebar-text">Orientasi</span>
                        <i class="bi bi-chevron-down sidebar-arrow"></i>
                    </a>
                    <div class="collapse sub-menu {{ $isMateriActive ? 'show' : '' }}" id="menuMou">
                        <a class="nav-link {{ request()->is('admin.materi*') ? 'active' : '' }}" href="{{ route('admin.materi.index') }}">
                            <span class="sidebar-text">Materi</span>
                        </a>
                        <a class="nav-link {{ request()->is('admin/orientasi') ? 'active' : '' }}" href="{{ route('admin.orientasi.index') }}">
                            <span class="sidebar-text">Monitoring Nilai</span>
                        </a>
                        <a class="nav-link {{ request()->is('admin/orientasi-pegawai*') ? 'active' : '' }}" href="{{ route('admin.orientasi_pegawai.index') }}">
                            <span class="sidebar-text">Orientasi Pegawai</span>
                        </a>
                    </div>
                </div>

                {{-- Pendidikan --}}
                @php
                    $isPendidikanActive = request()->is('mahasiswa*') || request()->is('ruangan*') || 
                                          request()->is('room_sequences*') || request()->is('room_schedules*') || 
                                          request()->is('absensi*') || request()->is('admin.dispensasi*');
                @endphp
                <div class="nav-item-dropdown animate-item">
                    <a class="nav-link {{ $isPendidikanActive ? 'active-parent' : '' }}" data-bs-toggle="collapse" href="#menuPendidikan" role="button">
                        <i class="bi bi-mortarboard"></i>
                        <span class="sidebar-text">Pendidikan</span>
                        <i class="bi bi-chevron-down sidebar-arrow"></i>
                    </a>
                    <div class="collapse sub-menu {{ $isPendidikanActive ? 'show' : '' }}" id="menuPendidikan">
                        <a class="nav-link {{ request()->is('mahasiswa*') ? 'active' : '' }}" href="{{ route('mahasiswa.index') }}"><span class="sidebar-text">Mahasiswa</span></a>
                        <a class="nav-link {{ request()->is('ruangan*') ? 'active' : '' }}" href="{{ route('ruangan.index') }}"><span class="sidebar-text">Ruangan</span></a>
                        <a class="nav-link {{ request()->is('admin.dispensasi*') ? 'active' : '' }}" href="{{ route('admin.dispensasi.index') }}"><span class="sidebar-text">Dispensasi</span></a>
                        <a class="nav-link {{ request()->is('absensi*') ? 'active' : '' }}" href="{{ route('absensi.index') }}"><span class="sidebar-text">Riwayat Absensi</span></a>
                          <a class="nav-link {{ request()->is('evaluasi_institusi*') ? 'active' : '' }}" href="{{ route('admin.evaluasi_institusi') }}"><span class="sidebar-text">Evaluasi Institusi</span></a>
                          
                    </div>
                </div>

            {{-- Pelatihan --}}
                @php 
                    // PERBAIKAN: Gunakan '/' bukan '.' untuk request()->is() karena ini membaca path URL
                    $isPelatihanActive = request()->is('pelatihan*') ||
                                         request()->is('diklat*') ||
                                         request()->is('admin/master*') ||
                                         request()->is('admin/evaluasi*') ||
                                         request()->is('admin/kegiatan*') || // <-- Ini akan menjaga menu terbuka saat di dalam rute penilaian
                                         request()->is('admin/forms*') ||
                                         request()->is('admin/linktree*');
                @endphp
                <div class="nav-item-dropdown animate-item">
                    <a class="nav-link {{ $isPelatihanActive ? 'active-parent' : '' }}" data-bs-toggle="collapse" href="#menuPelatihan">
                        <i class="bi bi-people"></i>
                        <span class="sidebar-text">Pelatihan</span>
                        <i class="bi bi-chevron-down sidebar-arrow"></i>
                    </a>
                    <div class="collapse sub-menu {{ $isPelatihanActive ? 'show' : '' }}" id="menuPelatihan">
                        <a class="nav-link {{ request()->is('admin/master_instansi*') ? 'active' : '' }}" href="{{ route('admin.master_instansi.index') }}">Instansi</a>
                        <a class="nav-link {{ request()->is('admin/master_ruangan*') ? 'active' : '' }}" href="{{ route('admin.master_ruangan.index') }}">Ruangan</a>
                        <a class="nav-link {{ request()->is('admin/master_kompetensi*') ? 'active' : '' }}" href="{{ route('admin.master_kompetensi.index') }}">Kompetensi</a>
                        <a class="nav-link {{ request()->is('admin/master-prodi*') ? 'active' : '' }}" href="{{ route('admin.master_prodi.index') }}">Program Studi</a>
                        
                        {{-- Menu Kegiatan Utama --}}
                        <a class="nav-link {{ request()->routeIs('admin.kegiatan.index', 'admin.kegiatan.create', 'admin.kegiatan.edit') ? 'active' : '' }}" href="{{ route('admin.kegiatan.index') }}">Pelatihan</a>

                        {{-- ====== MENU DINAMIS ====== --}}
                        {{-- Hanya muncul jika Admin sedang membuka Setting Penilaian suatu kegiatan --}}
                        @if(request()->routeIs('admin.kegiatan.penilaian.*'))
                            <a class="nav-link active" href="#" style="background: rgba(255,255,255,0.1); border-left: 2px solid #ffde59; padding-left: 15px;">
                                <i class="bi bi-gear-fill me-2" style="font-size: 0.8rem; color: #ffde59;"></i> 
                                <span class="sidebar-text" style="color: #ffde59;">Setting Penilaian</span>
                            </a>
                        @endif
                        {{-- ============================== --}}

                        <a class="nav-link {{ request()->is('pelatihan') ? 'active' : '' }}" href="{{ route('pelatihan.index') }}">Database SDM</a>
                        <a class="nav-link {{ request()->is('diklat*') ? 'active' : '' }}" href="{{ route('diklat.index') }}">Pendaftaran</a>
                        <a class="nav-link {{ request()->is('admin/forms*') ? 'active' : '' }}" href="{{ route('admin.forms.index') }}">Buat Formulir</a>
                        <a class="nav-link {{ request()->is('admin/linktree*') ? 'active' : '' }}" href="{{ route('admin.linktree.index') }}">Paket Link</a>
                        <a class="nav-link {{ request()->is('admin/evaluasi*') ? 'active' : '' }}" href="{{ route('admin.evaluasi.index') }}">Evaluasi &amp; IKM</a>
                        <a class="nav-link {{ request()->is('admin/master-evaluasi*') ? 'active' : '' }}" href="{{ route('admin.master_evaluasi.index') }}">Master Evaluasi</a>
                    </div>
                </div>
         {{-- Penelitian --}}
                @php
                    $isPenelitianActive = request()->is('pra-penelitian*') || request()->is('admin/presentasi*') || request()->is('surat-balasan*');
                @endphp
                <div class="nav-item-dropdown animate-item">
                    <a class="nav-link {{ $isPenelitianActive ? 'active-parent' : '' }}" data-bs-toggle="collapse" href="#menuPenelitian">
                        <i class="bi bi-journal-richtext"></i>
                        <span class="sidebar-text">Penelitian</span>
                        <i class="bi bi-chevron-down sidebar-arrow"></i>
                    </a>
                    <div class="collapse sub-menu {{ $isPenelitianActive ? 'show' : '' }}" id="menuPenelitian">
                        <a class="nav-link {{ request()->is('pra-penelitian*') ? 'active' : '' }}" href="{{ route('pra-penelitian.index') }}">Data Penelitian</a>
                        <a class="nav-link {{ request()->is('surat-balasan*') ? 'active' : '' }}" href="{{ route('surat-balasan.index') }}">Surat Balasan</a>
                        <a class="nav-link {{ request()->is('admin/presentasi*') ? 'active' : '' }}" href="{{ route('admin.presentasi.index') }}">Presentasi</a>
                    </div>
                </div>
                
                  <div class="nav-item-dropdown animate-item">
    {{-- Menentukan apakah parent harus aktif --}}
    @php
        $isOtomasiActive = request()->is('admin/master-pelatihan*', 'admin/rekomendasi-pelatihan*');
    @endphp

    <a class="nav-link {{ $isOtomasiActive ? 'active-parent' : '' }}" data-bs-toggle="collapse" href="#menuOtomasi" role="button" aria-expanded="{{ $isOtomasiActive ? 'true' : 'false' }}">
        <i class="bi bi-robot"></i>
        <span class="sidebar-text">Otomasi</span>
        <i class="bi bi-chevron-down sidebar-arrow"></i>
    </a>

    <div class="collapse sub-menu {{ $isOtomasiActive ? 'show' : '' }}" id="menuOtomasi">
    
        <a class="nav-link {{ request()->is('admin/rekomendasi-pelatihan*') ? 'active' : '' }}" href="{{ route('admin.rekomendasi_pelatihan') }}">
            <span class="sidebar-text">Rekomendasi TNA</span>
        </a>
    </div>
</div>
       

            @endif

            {{-- ========== MENU KEPALA RUANGAN ========== --}}
            @if (auth()->check() && in_array(auth()->user()->role, ['ruangan', 'kepala_ruangan']))
                <div class="sidebar-heading animate-item">
                    <span class="sidebar-text">Menu Ruangan</span>
                </div>
                
                <a class="nav-link animate-item {{ request()->routeIs('kepala_ruangan.dashboard') ? 'active' : '' }}" href="{{ route('kepala_ruangan.dashboard') }}">
                    <i class="bi bi-house-door"></i>
                    <span class="sidebar-text">Dashboard Ruangan</span>
                </a>
            @endif

       {{-- ========== MENU USER (MAHASISWA) ========== --}}
            @if (auth()->check() && auth()->user()->role === 'user')
                <div class="sidebar-heading animate-item">
                    <span class="sidebar-text">Menu Utama</span>
                </div>
                <a class="nav-link animate-item {{ request()->is('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                    <i class="bi bi-house-door"></i>
                    <span class="sidebar-text">Dashboard</span>
                </a>

                <div class="sidebar-heading mt-2 animate-item"><span class="sidebar-text">Wajib</span></div>
                <a class="nav-link animate-item {{ request()->is('orientasi*') ? 'active' : '' }}" href="{{ route('orientasi.index') }}">
                    <i class="bi bi-journal-check"></i> <span class="sidebar-text">Orientasi Awal</span>
                </a>
@php
                    $userId = auth()->id();
                    
                    // ==========================================
                    // 1. DATA MAGANG
                    // ==========================================
                    $magang = \App\Models\Pengajuan::where('user_id', $userId)->where('jenis', 'magang')->latest()->first();
                    $mahasiswaTerakhir = \App\Models\Mahasiswa::where('user_id', $userId)->latest()->first();
                    
                    $showMenuMagang = false;
                    
                    if ($mahasiswaTerakhir) {
                        $showMenuMagang = true; // Default: Tampilkan agar bisa lihat riwayat/sertifikat lama
                        
                        // Sembunyikan menu JIKA pengajuan terbarunya sudah di-ACC TAPI biodata barunya belum diisi
                        // (Terdeteksi jika data mahasiswa terakhir lebih jadul dari tanggal pengajuan terbaru)
                        if ($magang && $magang->status === 'approved' && $mahasiswaTerakhir->created_at < $magang->created_at) {
                            $showMenuMagang = false; 
                        }
                    }

                    // ==========================================
                    // 2. DATA PENELITIAN
                    // ==========================================
                    $pra = \App\Models\Pengajuan::where('user_id', $userId)->where('jenis', 'pra_penelitian')->latest()->first();
                    $praTerakhir = \App\Models\PraPenelitian::where('user_id', $userId)->latest()->first();
                    
                    $showMenuPenelitian = false;
                    
                    if ($praTerakhir) {
                        $showMenuPenelitian = true; // Default: Tampilkan agar bisa lihat laporan lama
                        
                        if ($pra && $pra->status === 'approved' && $praTerakhir->created_at < $pra->created_at) {
                            $showMenuPenelitian = false;
                        }
                    }
                @endphp

                <div class="sidebar-heading mt-2 animate-item"><span class="sidebar-text">Layanan</span></div>
                <a class="nav-link animate-item {{ request()->is('pengajuan') ? 'active' : '' }}" href="{{ route('pengajuan.index') }}">
                    <i class="bi bi-grid-1x2"></i> <span class="sidebar-text">Pengajuan</span>
                </a>

                {{-- Tampilkan Header "Aktivitas" jika salah satu menu aktif --}}
                @if ($showMenuMagang || $showMenuPenelitian)
                    <div class="sidebar-heading mt-2 animate-item"><span class="sidebar-text">Aktivitas Saya</span></div>
                @endif

                {{-- Menu Dashboard Magang --}}
                @if ($showMenuMagang)
                    <a class="nav-link animate-item {{ request()->routeIs('mahasiswa.dashboard') ? 'active' : '' }}" href="{{ route('mahasiswa.dashboard') }}">
                        <i class="bi bi-briefcase"></i> <span class="sidebar-text">Dashboard Magang</span>
                    </a>
                @endif

                {{-- Menu Dashboard Penelitian (Tambahan Baru) --}}
             {{-- Menu Dashboard Penelitian --}}
                @if ($showMenuPenelitian)
                    <a class="nav-link animate-item {{ request()->is('pengajuan/detail/pra_penelitian') ? 'active' : '' }}" href="{{ route('pengajuan.detail', ['jenis' => 'pra_penelitian']) }}">
                        <i class="bi bi-journal-richtext"></i> <span class="sidebar-text">Dashboard Penelitian</span>
                    </a>
                @endif
            @endif

            {{-- Portal Instansi Mitra --}}
            @if (auth()->check() && auth()->user()->role === 'instansi')
                <div class="sidebar-heading animate-item"><span class="sidebar-text">Portal Instansi</span></div>
                <a class="nav-link animate-item {{ request()->is('instansi/dashboard') ? 'active' : '' }}" href="{{ route('instansi.dashboard') }}">
                    <i class="bi bi-buildings"></i>
                    <span class="sidebar-text">Dashboard</span>
                </a>
                <a class="nav-link animate-item {{ request()->is('instansi/booking*') ? 'active' : '' }}" href="{{ route('instansi.booking.create') }}">
                    <i class="bi bi-calendar-plus"></i>
                    <span class="sidebar-text">Booking Ruangan</span>
                </a>
                <a class="nav-link animate-item {{ request()->is('instansi/rekap') ? 'active' : '' }}" href="{{ route('instansi.rekap') }}">
                    <i class="bi bi-clipboard2-data"></i>
                    <span class="sidebar-text">Rekap & Laporan</span>
                </a>
            @endif

            {{-- Evaluasi Diklat: tersedia untuk semua akun --}}
            @auth
                <a class="nav-link animate-item {{ request()->is('evaluasi*') ? 'active' : '' }}" href="{{ route('evaluasi.public.form') }}" target="_blank">
                    <i class="bi bi-clipboard2-check"></i>
                    <span class="sidebar-text">Evaluasi Diklat</span>
                </a>
            @endauth
        </nav>
    </div>

    <div class="sidebar-footer">
        <div class="p-3 sidebar-user-profile">
            <a class="nav-link logout-link" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="bi bi-box-arrow-right"></i>
                <span class="sidebar-text">Logout</span>
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>
            <hr class="logout-divider">
            <div class="d-flex align-items-center user-info-box">
                @php
                    $user = auth()->user();
                    $mahasiswa = ($user && $user->role === 'user') ? \App\Models\Mahasiswa::where('user_id', $user->id)->first() : null;
                    $foto = ($mahasiswa && $mahasiswa->foto_path) ? asset($mahasiswa->foto_path) : "https://ui-avatars.com/api/?name=".urlencode($user->name ?? 'Guest')."&background=7c1316&color=fff";
                @endphp
                <img src="{{ $foto }}" class="rounded-circle me-2 object-fit-cover user-avatar" width="40" height="40" alt="User">
                <div class="sidebar-text">
                    <div class="fw-bold text-truncate" style="max-width: 130px;">{{ $user->name ?? 'Guest' }}</div>
                    <small class="role-badge">{{ ucfirst($user->role ?? 'Visitor') }}</small>
                </div>
            </div>
        </div>
    </div>
</div>

<button class="mobile-hamburger-btn d-md-none" id="mobileHamburger">
    <i class="bi bi-list"></i>
</button>

<style>
    :root {
        --maroon: #7c1316;
        --maroon-light: #a3191d;
        --sidebar-bg: var(--maroon);
        --sidebar-text-color: #e0e0e0;
        --sidebar-text-active: #ffffff;
        --sidebar-pill-hover: rgba(255, 255, 255, 0.15);
        --sidebar-pill-active: linear-gradient(135deg, var(--maroon-light), #d12a30);
        --sidebar-heading-color: rgba(255, 255, 255, 0.6);
        --transition-speed: 0.4s;
    }

    /* Base Sidebar */
    .sidebar {
        width: 270px;
        height: 100vh;
        background: var(--sidebar-bg);
        color: var(--sidebar-text-color);
        position: fixed;
        top: 0;
        left: 0;
        z-index: 1050;
        display: flex;
        flex-direction: column;
        transition: all var(--transition-speed) cubic-bezier(0.25, 1, 0.5, 1);
        box-shadow: 6px 0 25px rgba(0,0,0,0.15);
    }

    .sidebar.collapsed { width: 85px; }

    @media (max-width: 768px) {
        .sidebar { transform: translateX(-100%); width: 280px; }
        .sidebar.mobile-show { transform: translateX(0); }
        .sidebar.collapsed { width: 280px; }
    }

    .sidebar-overlay {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.6); backdrop-filter: blur(4px);
        display: none; z-index: 1040; opacity: 0; transition: opacity var(--transition-speed);
    }
    .sidebar-overlay.active { display: block; opacity: 1; }

    /* ====== ANIMATED SCROLLBAR (BEAUTIFUL UX) ====== */
    .sidebar-inner { 
        flex: 1; 
        overflow-y: auto; 
        overflow-x: hidden; 
        padding-bottom: 20px;
    }
    .sidebar-inner::-webkit-scrollbar { 
        width: 6px; 
    }
    .sidebar-inner::-webkit-scrollbar-track { 
        background: transparent; 
    }
    .sidebar-inner::-webkit-scrollbar-thumb { 
        background: rgba(255,255,255,0.1); 
        border-radius: 10px; 
    }
    .sidebar-inner:hover::-webkit-scrollbar-thumb { 
        background: rgba(255,255,255,0.3); 
    }
    .sidebar-inner::-webkit-scrollbar-thumb:hover { 
        background: rgba(255,255,255,0.5); 
    }

    .sidebar-header {
        height: 100px; padding: 1rem; display: flex; align-items: center;
        justify-content: space-between; border-bottom: 1px solid rgba(255,255,255,0.05);
    }

    .image-sidebar { 
        height: 200px; padding: 30px; max-width: 200px; 
        object-fit: contain; transition: opacity 0.3s; 
        filter: drop-shadow(0px 4px 6px rgba(0,0,0,0.3));
    }
    .sidebar.collapsed .image-sidebar { opacity: 0; pointer-events: none; width: 0; padding: 0; }

    .sidebar-toggle, .sidebar-close-mobile {
        background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); 
        color: white; width: 34px; height: 34px; border-radius: 50%; display: flex;
        align-items: center; justify-content: center; cursor: pointer;
        transition: all 0.3s cubic-bezier(0.25, 1, 0.5, 1);
    }
    .sidebar-toggle:hover {
        background: white; color: var(--maroon);
        transform: scale(1.1); box-shadow: 0 0 15px rgba(255,255,255,0.4);
    }
    .sidebar.collapsed .sidebar-toggle i { transform: rotate(180deg); }

    /* ====== NAVIGATION LINKS & ANIMATIONS ====== */
    .nav-link {
        color: var(--sidebar-text-color) !important;
        padding: 0.8rem 1.2rem;
        display: flex;
        align-items: center;
        border-radius: 10px;
        margin: 4px 12px;
        text-decoration: none;
        transition: all 0.3s cubic-bezier(0.25, 1, 0.5, 1);
        white-space: nowrap;
        font-weight: 500;
        letter-spacing: 0.3px;
        position: relative;
        overflow: hidden;
    }
    
    .nav-link i { 
        font-size: 1.3rem; 
        min-width: 35px; 
        transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1); /* Bouncing effect */
    }

    .nav-link:hover {
        background: var(--sidebar-pill-hover);
        color: var(--sidebar-text-active) !important;
        transform: translateX(6px);
    }
    
    .nav-link:hover i {
        transform: scale(1.2) rotate(5deg);
    }

    .nav-link.active {
        background: var(--sidebar-pill-active);
        color: var(--sidebar-text-active) !important;
        box-shadow: 0 4px 15px rgba(163, 25, 29, 0.4);
        font-weight: 600;
    }
    
    .nav-link.active i {
        transform: scale(1.1);
        animation: pulseIcon 2s infinite;
    }

    @keyframes pulseIcon {
        0% { transform: scale(1.1); }
        50% { transform: scale(1.25); }
        100% { transform: scale(1.1); }
    }

    .sidebar.collapsed .nav-link:hover { transform: scale(1.1); }
    
    .sidebar.collapsed .sidebar-text,
    .sidebar.collapsed .sidebar-arrow,
    .sidebar.collapsed .sidebar-heading { opacity: 0; display: none; }

    .sidebar.collapsed .nav-link { justify-content: center; margin: 6px; padding: 0.8rem 0; }
    .sidebar.collapsed .nav-link i { min-width: unset; margin: 0; }

    /* ====== SUBMENU ====== */
    .sub-menu { 
        padding-left: 1rem; 
        background: rgba(0,0,0,0.15); 
        border-radius: 0 0 10px 10px; 
        margin: 0 12px; 
    }
    .sub-menu .nav-link {
        font-size: 0.9rem;
        padding: 0.6rem 1rem;
        margin: 2px 5px;
    }
    .sub-menu .nav-link::before {
        content: "•";
        color: rgba(255,255,255,0.3);
        margin-right: 8px;
        transition: color 0.3s;
    }
    .sub-menu .nav-link:hover::before, .sub-menu .nav-link.active::before {
        color: white;
    }

    .sidebar-heading {
        padding: 1.5rem 1.5rem 0.5rem;
        font-size: 0.75rem; text-transform: uppercase;
        color: var(--sidebar-heading-color); letter-spacing: 1.5px;
        font-weight: 700;
    }
/* =========================================
   CUSTOM SCROLLBAR PREMIUM
========================================= */

/* Mengaktifkan animasi scroll yang halus (smooth scrolling) untuk seluruh halaman */
html {
    scroll-behavior: smooth;
}

/* Mengatur lebar scrollbar vertikal dan tinggi scrollbar horizontal */
::-webkit-scrollbar {
    width: 10px;
    height: 10px;
}

/* Track (Jalur tempat scrollbar bergerak) */
::-webkit-scrollbar-track {
    background: #f8f9fa; /* Warna latar sangat terang agar bersih */
    border-radius: 10px;
    box-shadow: inset 0 0 5px rgba(0, 0, 0, 0.05); /* Sedikit bayangan di dalam */
}

/* Thumb (Pegangan scrollbar yang bisa ditarik) */
::-webkit-scrollbar-thumb {
    /* Gradasi warna maroon sesuai tema */
    background: linear-gradient(180deg, #a3191d, #7c1316); 
    border-radius: 10px;
    /* Memberikan efek border transparan agar terlihat lebih ramping dari jalurnya */
    border: 2px solid #f8f9fa; 
    /* Transisi untuk animasi saat hover */
    transition: background-color 0.3s ease, transform 0.3s ease;
}

/* Animasi & Efek saat kursor (hover) diarahkan ke scrollbar */
::-webkit-scrollbar-thumb:hover {
    /* Warna gradasi berubah lebih terang / menyala saat di-hover */
    background: linear-gradient(180deg, #d12a30, #a3191d);
}

/* Efek saat scrollbar sedang diklik / ditahan (active) */
::-webkit-scrollbar-thumb:active {
    background: #5a0e10; /* Warna menjadi lebih gelap saat ditarik */
}
/* Terapkan class ini ke wadah/div yang menampung konten panjang */
.area-scroll {
    overflow-y: auto;
    overflow-x: hidden;
}

/* 1. Ukuran Lebar & Terlihat Jelas */
.area-scroll::-webkit-scrollbar {
    width: 16px; /* Lebih lebar dari standar, sangat mudah diklik */
}

/* 2. Jalur Scroll (Track) */
.area-scroll::-webkit-scrollbar-track {
    background: #f8f9fa;
    border-left: 3px solid #1a1a1a; /* Garis outline hitam tegas */
}

/* 3. Bilah Gulir (Thumb) - Tampilan Solid & Berani */
.area-scroll::-webkit-scrollbar-thumb {
    background-color: #7c1316; 
    border: 3px solid #1a1a1a; /* Outline hitam tebal memberikan struktur visual yang kuat */
    border-radius: 6px;
    /* Efek bayangan ke dalam (inset) untuk ilusi 3D retro */
    box-shadow: inset -3px -3px 0px rgba(0, 0, 0, 0.3);
}

/* 4. Saat Kursor Diarahkan (Hover) - Menyala Terang */
.area-scroll::-webkit-scrollbar-thumb:hover {
    background-color: #ff3338; /* Berubah jadi warna yang sangat cerah/mencolok */
}

/* 5. Saat Ditekan/Ditarik (Active) - Animasi Masuk/Dipencet */
.area-scroll::-webkit-scrollbar-thumb:active {
    background-color: #a3191d;
    /* Arah bayangan dibalik untuk memberikan efek bilah sedang "tertekan" */
    box-shadow: inset 3px 3px 0px rgba(0, 0, 0, 0.5); 
}
/* =========================================
   SCROLLBAR "ZIPPER / SLIDER" - SANGAT JELAS
========================================= */

/* 1. Lebar rel diperbesar biar gampang ditangkap mouse */
.sidebar-inner::-webkit-scrollbar {
    width: 16px; 
}

/* 2. JALUR (Rel tempat resleting ditarik) */
.sidebar-inner::-webkit-scrollbar-track {
    background: rgba(0, 0, 0, 0.15); /* Gelap transparan */
    border-left: 2px solid rgba(0, 0, 0, 0.4); /* Garis tegas pembatas rel */
    box-shadow: inset 0 0 5px rgba(0,0,0,0.3); /* Rel kerasa masuk ke dalam */
}

/* 3. PEGANGAN (Kepala Resleting) */
.sidebar-inner::-webkit-scrollbar-thumb {
    background-color: #f8f9fa; /* Putih terang biar super kontras dengan background maroon */
    border: 2px solid #000000; /* Border hitam tegas biar bentuknya nyata */
    border-radius: 6px; 
    
    /* INI KUNCI EFEK ZIPPER: Tekstur garis-garis (grip) di tengah bilah */
    background-image: repeating-linear-gradient(
        180deg,
        transparent,
        transparent 4px,
        #000000 4px,
        #000000 6px
    );
    background-size: 8px 100%; /* Garis tekstur difokuskan di tengah aja */
    background-position: center;
    background-repeat: no-repeat;
    
    /* Efek bayangan solid (ala retro/neo-brutalism) biar seolah ngambang dari rel */
    box-shadow: -2px 3px 0px rgba(0, 0, 0, 0.4); 
}

/* 4. SAAT KURSOR MENDEKAT (Hover) */
.sidebar-inner::-webkit-scrollbar-thumb:hover {
    background-color: #ffde59; /* Berubah kuning menyala biar makin jelas posisinya! */
}

/* 5. SAAT DITARIK / DIKLIK (Active) */
.sidebar-inner::-webkit-scrollbar-thumb:active {
    background-color: #d12a30; /* Berubah merah pas ditarik */
    /* Bayangan luar hilang, ganti bayangan dalam biar kerasa "ditekan" masuk */
    box-shadow: inset 2px 2px 6px rgba(0, 0, 0, 0.7); 
}
    /* ====== SEARCH BAR ====== */
    .sidebar-search { padding: 1rem 12px; margin-bottom: 0.5rem; }
    .search-container {
        position: relative; background: rgba(0, 0, 0, 0.2);
        border-radius: 12px; padding: 2px; transition: all 0.3s ease;
        border: 1px solid rgba(255, 255, 255, 0.05);
    }
    .search-container:focus-within {
        background: rgba(0, 0, 0, 0.4); border-color: rgba(255, 255, 255, 0.3);
        box-shadow: 0 0 10px rgba(255, 255, 255, 0.1);
        transform: translateY(-2px);
    }
    .search-input {
        width: 100%; background: transparent; border: none; color: white;
        padding: 0.7rem 0.8rem 0.7rem 2.5rem; font-size: 0.85rem; outline: none;
    }
    .search-input::placeholder { color: rgba(255, 255, 255, 0.4); }
    .search-icon { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: rgba(255, 255, 255, 0.6); transition: color 0.3s; pointer-events: none; }
    .search-container:focus-within .search-icon { color: white; }
    .sidebar.collapsed .sidebar-search { display: none; }

    /* ====== DROPDOWN ARROW ====== */
    .active-parent { background: rgba(0, 0, 0, 0.2); color: white !important; border-radius: 10px 10px 0 0; }
    .sidebar-arrow { margin-left: auto; transition: transform 0.4s cubic-bezier(0.25, 1, 0.5, 1); font-size: 0.8rem; }
    [aria-expanded="true"] .sidebar-arrow { transform: rotate(180deg); color: white; }

    /* ====== USER PROFILE ====== */
    .sidebar-footer { border-top: 1px solid rgba(255,255,255,0.05); background: rgba(0,0,0,0.1); }
    .sidebar-user-profile { 
        background: rgba(255, 255, 255, 0.05); margin: 12px; 
        border-radius: 15px; border: 1px solid rgba(255, 255, 255, 0.05);
        transition: all 0.3s ease;
    }
    .sidebar-user-profile:hover {
        background: rgba(255, 255, 255, 0.1);
        border-color: rgba(255, 255, 255, 0.2);
    }
    .user-avatar { border: 2px solid rgba(255,255,255,0.2); padding: 2px; transition: transform 0.3s ease; }
    .sidebar-user-profile:hover .user-avatar { transform: scale(1.1); border-color: white; }
    
    .role-badge { 
        background: rgba(0,0,0,0.3); padding: 2px 8px; 
        border-radius: 20px; font-size: 0.7rem; color: rgba(255,255,255,0.8); 
    }

    .logout-link { 
        color: #ffb3b3 !important; font-size: 0.85rem; margin: 0 0 10px 0; padding: 0.5rem; 
        border-radius: 8px; transition: all 0.3s;
    }
    .logout-link:hover { background: rgba(255, 0, 0, 0.2); color: white !important; }
    .logout-divider { border-color: rgba(255,255,255,0.1); margin: 10px 0; }
    
    .mobile-hamburger-btn {
        position: fixed; top: 15px; left: 15px; z-index: 1000;
        background: var(--maroon); color: white; border: none;
        padding: 8px 12px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.3);
        transition: transform 0.2s;
    }
    .mobile-hamburger-btn:active { transform: scale(0.9); }

    /* ====== STAGGERED FADE-IN ANIMATION ON LOAD ====== */
    .animate-item {
        opacity: 0;
        transform: translateY(15px);
        animation: fadeInUp 0.5s cubic-bezier(0.25, 1, 0.5, 1) forwards;
    }
    @keyframes fadeInUp {
        to { opacity: 1; transform: translateY(0); }
    }
    /* Add slight delays for each menu item to create a cascading effect */
    .sidebar-nav-container > *:nth-child(1) { animation-delay: 0.1s; }
    .sidebar-nav-container > *:nth-child(2) { animation-delay: 0.15s; }
    .sidebar-nav-container > *:nth-child(3) { animation-delay: 0.2s; }
    .sidebar-nav-container > *:nth-child(4) { animation-delay: 0.25s; }
    .sidebar-nav-container > *:nth-child(5) { animation-delay: 0.3s; }
    .sidebar-nav-container > *:nth-child(6) { animation-delay: 0.35s; }
    .sidebar-nav-container > *:nth-child(7) { animation-delay: 0.4s; }
    .sidebar-nav-container > *:nth-child(8) { animation-delay: 0.45s; }
    .sidebar-nav-container > *:nth-child(9) { animation-delay: 0.5s; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const mobileHamburger = document.getElementById('mobileHamburger');
    const sidebarCloseMobile = document.getElementById('sidebarCloseMobile');
    const overlay = document.getElementById('sidebarOverlay');
    const searchInput = document.querySelector('.search-input');

    function handleToggle() {
        if (window.innerWidth <= 768) {
            sidebar.classList.toggle('mobile-show');
            overlay.classList.toggle('active');
        } else {
            sidebar.classList.toggle('collapsed');
            localStorage.setItem('sidebarState', sidebar.classList.contains('collapsed') ? 'collapsed' : 'expanded');
        }
    }

    if (sidebarToggle) sidebarToggle.addEventListener('click', handleToggle);
    if (mobileHamburger) mobileHamburger.addEventListener('click', handleToggle);
    if (sidebarCloseMobile) sidebarCloseMobile.addEventListener('click', handleToggle);
    if (overlay) overlay.addEventListener('click', handleToggle);

    const allLinks = document.querySelectorAll('.nav-link:not([data-bs-toggle="collapse"])');
    allLinks.forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth <= 768) {
                sidebar.classList.remove('mobile-show');
                overlay.classList.remove('active');
            }
        });
    });

    if (window.innerWidth > 768) {
        const savedState = localStorage.getItem('sidebarState');
        if (savedState === 'collapsed') sidebar.classList.add('collapsed');
    }

    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const text = e.target.value.toLowerCase();
            const items = document.querySelectorAll('.sidebar-nav-container > .nav-link, .sidebar-nav-container > .nav-item-dropdown');
            const headings = document.querySelectorAll('.sidebar-heading');

            if(text === "") {
                items.forEach(i => i.style.display = 'block');
                headings.forEach(h => h.style.display = 'block');
                return;
            }

            items.forEach(item => {
                const content = item.textContent.toLowerCase();
                const isMatch = content.includes(text);
                item.style.display = isMatch ? 'block' : 'none';
                
                if (item.classList.contains('nav-item-dropdown') && isMatch) {
                    const collapseEl = item.querySelector('.collapse');
                    if (collapseEl) {
                        const bsCollapse = bootstrap.Collapse.getInstance(collapseEl) || new bootstrap.Collapse(collapseEl);
                        bsCollapse.show();
                    }
                }
            });

            headings.forEach(h => h.style.display = 'none');
        });
    }
});
</script>