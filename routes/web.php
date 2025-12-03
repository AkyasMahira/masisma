<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\RuanganController;
use App\Http\Controllers\PraPenelitianController;
use App\Http\Controllers\PelatihanController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\KonsultasiController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\SuratBalasanController;
use App\Http\Controllers\MouController;
use App\Http\Controllers\PengajuanController;
use App\Http\Controllers\PresentasiController;
use App\Http\Controllers\ProgresController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [PublicController::class, 'landing'])->name('landing');

Route::post('/chatbot', [PublicController::class, 'chatbot'])->name('chatbot.ask');

// --- PUBLIC ROUTES (Pelatihan & Auth) ---
Route::get('/cek-data-pelatihan', [PelatihanController::class, 'publicIndex'])->name('public.pelatihan.index');
Route::get('/input-data-pelatihan/{pelatihan}/edit', [PelatihanController::class, 'publicEdit'])->name('public.pelatihan.edit');
Route::put('/input-data-pelatihan/{pelatihan}', [PelatihanController::class, 'publicUpdate'])->name('public.pelatihan.update');

// --- PUBLIC ROUTES FOR MOU (CREATE-ONLY, NO SIDEBAR) ---
Route::get('/input-data-mou/create', [MouController::class, 'publicCreate'])->name('public.mou.create');
Route::post('/input-data-mou', [MouController::class, 'publicStore'])->name('public.mou.store');
Route::get('/penilaian/{token}', [PresentasiController::class, 'formPenilaian'])->name('ci.penilaian');
Route::post('/penilaian/{token}', [PresentasiController::class, 'submitPenilaian'])->name('ci.submit-penilaian');

// Download Sertifikat & Surat Selesai (Public - untuk semua user)
Route::get('/presentasi/{id}/sertifikat/{nama_anggota}', [PresentasiController::class, 'downloadSertifikatAnggota'])->name('presentasi.download-sertifikat');
Route::get('/presentasi/{id}/surat-selesai/{nama_anggota}', [PresentasiController::class, 'downloadSuratSelesaiAnggota'])->name('presentasi.download-surat-selesai');


// Authentication
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// =========================================================================
// 1. ROUTES UNTUK USER LOGIN (UMUM)
// =========================================================================
Route::middleware(['auth'])->group(function () {

    // Dashboard Utama (Redirect based on role usually happens here)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Pengajuan (Pra-Penelitian & Magang)
    Route::prefix('pengajuan')->name('pengajuan.')->group(function () {
        Route::get('/', [PengajuanController::class, 'index'])->name('index');
        Route::get('/detail/{jenis}', [PengajuanController::class, 'detail'])->name('detail');
        Route::post('/pra', [PengajuanController::class, 'ajukanPra'])->name('pra');
        Route::post('/magang', [PengajuanController::class, 'ajukanMagang'])->name('magang');
        Route::post('/{pengajuan}/upload-bukti', [PengajuanController::class, 'uploadBuktiPembayaran'])->name('upload-bukti');
        // Allow user to delete their pengajuan (cleanup and allow re-apply)
        Route::delete('/{pengajuan}', [PengajuanController::class, 'destroy'])->name('destroy');
    });

    // Sertifikat & Absensi (User View)
    Route::get('/sertifikat/download/{token}', [AbsensiController::class, 'generateSertifikatPublik'])->name('sertifikat.download');
    Route::get('/absensi/{token}', [AbsensiController::class, 'card'])->name('absensi.card');
    Route::post('/absensi/{token}/toggle', [AbsensiController::class, 'toggle'])->name('absensi.toggle');

    // --- AKSES MAHASISWA / MAGANG (User Biasa) ---
    // Middleware 'magang' diasumsikan untuk user yang sudah diterima/aktif
    Route::middleware(['magang'])->prefix('mahasiswa')->name('mahasiswa.')->group(function () {

        // 1. Dashboard Khusus Mahasiswa
        Route::get('/dashboard', [MahasiswaController::class, 'dashboard'])->name('dashboard');

        // 2. Helper (AJAX/Info)
        Route::get('/ruangan-info/{id}', [MahasiswaController::class, 'getRuanganInfo'])->name('ruangan.info');
        Route::get('/search/universitas', [MahasiswaController::class, 'searchUniversitas'])->name('search.universitas');

        // 3. Edit & Update Profil (User edit diri sendiri & Admin edit user)
        // Ditaruh di sini agar User bisa akses. Admin juga bisa akses karena Admin biasanya lolos middleware auth.
        // Pastikan Controller membatasi User A tidak bisa edit User B.
        Route::get('/{mahasiswa}/edit', [MahasiswaController::class, 'edit'])->name('edit');
        Route::put('/{mahasiswa}', [MahasiswaController::class, 'update'])->name('update');

        // Create/Store (Jika pendaftaran dilakukan mandiri setelah login)
        Route::get('/create', [MahasiswaController::class, 'create'])->name('create');
        Route::post('/', [MahasiswaController::class, 'store'])->name('store');
    });

    // --- AKSES PRA-PENELITIAN ---
    Route::middleware(['pra'])->prefix('pra-penelitian')->name('pra-penelitian.')->group(function () {
        Route::get('/create', [PraPenelitianController::class, 'create'])->name('create');
        Route::post('/', [PraPenelitianController::class, 'store'])->name('store');
    });

    // --- AKSES KONSULTASI ---
    // Konsultasi (hanya untuk yang sudah dapat CI)
    Route::prefix('konsultasi')->name('konsultasi.')->group(function () {
        Route::get('/', [KonsultasiController::class, 'index'])->name('index');
        Route::post('/', [KonsultasiController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [KonsultasiController::class, 'edit'])->name('edit');
        Route::put('/{id}', [KonsultasiController::class, 'update'])->name('update');
        Route::delete('/{id}', [KonsultasiController::class, 'destroy'])->name('destroy');
    });

    // Akses Presentasi
        Route::prefix('presentasi')->name('presentasi.')->group(function () {
        Route::get('/', [PresentasiController::class, 'show'])->name('show');
        Route::post('/{id}/upload-ppt', [PresentasiController::class, 'uploadPpt'])->name('upload-ppt');
        Route::post('/{id}/upload-laporan', [PresentasiController::class, 'uploadLaporan'])->name('upload-laporan');
        Route::get('/{id}/sertifikat/{nama_anggota}', [PresentasiController::class, 'downloadSertifikatAnggota'])->name('download-sertifikat');
        Route::get('/{id}/surat-selesai/{nama_anggota}', [PresentasiController::class, 'downloadSuratSelesaiAnggota'])->name('download-surat-selesai');
    });
});


// =========================================================================
// 2. ROUTES KHUSUS ADMIN (FULL CONTROL)
// =========================================================================
Route::middleware(['auth', 'admin'])->group(function () {

    // 1. Pengajuan (Approval)
    Route::prefix('admin/pengajuan')->name('admin.pengajuan.')->group(function () {
        Route::get('/', [PengajuanController::class, 'adminIndex'])->name('index');
        Route::post('/{pengajuan}/approve', [PengajuanController::class, 'approve'])->name('approve');
        Route::post('/{pengajuan}/reject', [PengajuanController::class, 'reject'])->name('reject');
        Route::post('/{pengajuan}/kirim-galasan', [PengajuanController::class, 'kirimGalasan'])->name('kirim-galasan');
        Route::post('/{pengajuan}/approve-pembayaran', [PengajuanController::class, 'approvePembayaran'])->name('approve-pembayaran');
        Route::get('/{pengajuan}', [PengajuanController::class, 'show'])->name('show');
        // Admin can also delete pengajuan
        Route::delete('/{pengajuan}', [PengajuanController::class, 'destroy'])->name('destroy');
    });

    // 2. Manajemen Mahasiswa (Admin View)
    // Note: Edit & Update ada di group atas (shared), Index & Delete khusus Admin
    Route::prefix('mahasiswa')->name('mahasiswa.')->group(function () {
        Route::get('/', [MahasiswaController::class, 'index'])->name('index'); // LIST SEMUA
        Route::post('/import-excel', [MahasiswaController::class, 'importExcel'])->name('import_excel');
        Route::get('/export', [MahasiswaController::class, 'export'])->name('export');
        Route::get('/links', [MahasiswaController::class, 'copyLinks'])->name('links');
        Route::get('/{id}/sertifikat/summary', [MahasiswaController::class, 'showSertifikatSummary'])->name('sertifikat.summary');
        Route::get('/{mahasiswa}', [MahasiswaController::class, 'show'])->name('show');
        Route::delete('/{mahasiswa}', [MahasiswaController::class, 'destroy'])->name('destroy');
        // Admin Generate Sertifikat
        Route::get('/{id}/sertifikat', [MahasiswaController::class, 'generateSertifikat'])->name('sertifikat');
    });

    // 3. Manajemen Pra-Penelitian
    Route::prefix('pra-penelitian')->name('pra-penelitian.')->group(function () {
        Route::get('/', [PraPenelitianController::class, 'index'])->name('index');
        Route::get('/{pra_penelitian}', [PraPenelitianController::class, 'show'])->name('show');
        Route::get('/{pra_penelitian}/edit', [PraPenelitianController::class, 'edit'])->name('edit');
        Route::put('/{pra_penelitian}', [PraPenelitianController::class, 'update'])->name('update');
        Route::delete('/{pra_penelitian}', [PraPenelitianController::class, 'destroy'])->name('destroy');
        Route::patch('/{pra_penelitian}/batal', [PraPenelitianController::class, 'batal'])->name('batal');
        Route::post('/{praPenelitian}/approve-form', [PraPenelitianController::class, 'approveForm'])->name('approve');
        Route::post('/{praPenelitian}/reject-form', [PraPenelitianController::class, 'rejectForm'])->name('reject');
    });

    // 4. Manajemen Ruangan
    Route::prefix('ruangan')->name('ruangan.')->group(function () {
        Route::get('/', [RuanganController::class, 'index'])->name('index');
        Route::get('/create', [RuanganController::class, 'create'])->name('create');
        Route::post('/', [RuanganController::class, 'store'])->name('store');
        Route::get('/{ruangan}', [RuanganController::class, 'show'])->name('show');
        Route::get('/{ruangan}/edit', [RuanganController::class, 'edit'])->name('edit');
        Route::put('/{ruangan}', [RuanganController::class, 'update'])->name('update');
        Route::delete('/{ruangan}', [RuanganController::class, 'destroy'])->name('destroy');
    });

    // 5. Manajemen Pelatihan
    Route::prefix('pelatihan')->name('pelatihan.')->group(function () {
        Route::get('/', [PelatihanController::class, 'index'])->name('index');
        Route::get('/create', [PelatihanController::class, 'create'])->name('create');
        Route::get('/export', [PelatihanController::class, 'export'])->name('export');
        Route::post('/import-excel', [PelatihanController::class, 'import_excel'])->name('import_excel');
        Route::post('/', [PelatihanController::class, 'store'])->name('store');
        Route::get('/{pelatihan}', [PelatihanController::class, 'show'])->name('show');
        Route::get('/{pelatihan}/edit', [PelatihanController::class, 'edit'])->name('edit');
        Route::put('/{pelatihan}', [PelatihanController::class, 'update'])->name('update');
        Route::delete('/{pelatihan}', [PelatihanController::class, 'destroy'])->name('destroy');
    });

    // 6. Manajemen Surat Balasan
    Route::prefix('surat-balasan')->name('surat-balasan.')->group(function () {
        Route::resource('/', 'SuratBalasanController')->except(['show']);
        Route::get('/{suratBalasan}/pdf', 'SuratBalasanController@generatePdf')->name('pdf');
    });

    // 7. Manajemen MOU
    Route::prefix('mou')->name('mou.')->group(function () {
        Route::get('/', [MouController::class, 'index'])->name('index');
        Route::get('/create', [MouController::class, 'create'])->name('create');
        Route::post('/', [MouController::class, 'store'])->name('store');
        Route::post('/import', [MouController::class, 'importExcel'])->name('import_excel');
        Route::get('/{mou}', [MouController::class, 'show'])->name('show');
        Route::get('/{mou}/edit', [MouController::class, 'edit'])->name('edit');
        Route::put('/{mou}', [MouController::class, 'update'])->name('update');
        Route::delete('/{mou}', [MouController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('admin/presentasi')->name('admin.presentasi.')->group(function () {
        Route::get('/', [PresentasiController::class, 'adminIndex'])->name('index');
        Route::get('/create/{pengajuan}', [PresentasiController::class, 'create'])->name('create');
        Route::post('/{pengajuan}', [PresentasiController::class, 'store'])->name('store');
        Route::get('/detail/{id}', [PresentasiController::class, 'detail'])->name('detail');
        Route::post('/{id}/review-laporan', [PresentasiController::class, 'reviewLaporan'])->name('review-laporan');
    });

    // 8. Admin Utilities (Notes, Absensi Rekap, Users)
    Route::get('/notes', [NoteController::class, 'index'])->name('notes.index');
    Route::post('/notes', [NoteController::class, 'store'])->name('notes.store');
    Route::delete('/notes/{id}', [NoteController::class, 'destroy'])->name('notes.destroy');

    Route::get('/absensi', [AbsensiController::class, 'index'])->name('absensi.index');

    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users/{id}/approve', [UserController::class, 'approve'])->name('users.approve');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

    // 9. Manajemen Jadwal Rolling Ruangan Mahasiswa
    Route::prefix('room-schedules')->name('room_schedules.')->group(function () {
        Route::get('/', [\App\Http\Controllers\RoomScheduleController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\RoomScheduleController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\RoomScheduleController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [\App\Http\Controllers\RoomScheduleController::class, 'edit'])->name('edit');
        Route::put('/{id}', [\App\Http\Controllers\RoomScheduleController::class, 'update'])->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\RoomScheduleController::class, 'destroy'])->name('destroy');
    });
});
