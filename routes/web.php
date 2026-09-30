<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LinktreeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\RuanganController;
use App\Http\Controllers\AdminOrientasiController;
use App\Http\Controllers\PraPenelitianController;
use App\Http\Controllers\PelatihanController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\KonsultasiController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\SuratBalasanController;
use App\Http\Controllers\RoomSequenceController;
use App\Http\Controllers\MouController;
use App\Http\Controllers\PengajuanController;
use App\Http\Controllers\PresentasiController;
use App\Http\Controllers\ProgresController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\RoomScheduleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CorporateInstructorController;
use App\Http\Controllers\OrientasiController;
use App\Http\Controllers\DispensasiController;
use App\Http\Controllers\ShiftScheduleController;
use App\Http\Controllers\CustomFormController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\MasterInstansiController;
use App\Http\Controllers\MasterRuanganController;
use App\Http\Controllers\KegiatanController;
use App\Http\Controllers\RoomShiftController;
use App\Http\Controllers\MasterPelatihanController;
use App\Http\Controllers\RekomendasiPelatihanController;
use App\Http\Controllers\KegiatanPesertaController;
use App\Http\Controllers\PenilaianSettingController;
use App\Http\Controllers\InputNilaiTeoriController;
use App\Http\Controllers\FasilitatorPenilaianController;
use App\Http\Controllers\RekapPenilaianController;

// Route Pendaftaran Pelatihan Eksternal (Publik)
Route::get('/daftar-kegiatan/{id}', [KegiatanPesertaController::class, 'publicDaftarForm'])->name('public.kegiatan.daftar');
Route::post('/daftar-kegiatan/{id}', [KegiatanPesertaController::class, 'submitDaftarPublic'])->name('public.kegiatan.daftar.submit');

// Penilaian Fasilitator (Publik): token -> materi -> peserta -> form
Route::get('/penilaian-fasilitator/{token}', [FasilitatorPenilaianController::class, 'formPenilaian'])
    ->name('fasilitator.penilaian.index');
Route::get('/penilaian-fasilitator/{token}/materi/{materi_id}', [FasilitatorPenilaianController::class, 'daftarPeserta'])
    ->name('fasilitator.penilaian.materi');
Route::get('/penilaian-fasilitator/{token}/materi/{materi_id}/peserta/{peserta_id}', [FasilitatorPenilaianController::class, 'showFormPeserta'])
    ->name('fasilitator.penilaian.form');
Route::post('/penilaian-fasilitator/{token}/materi/{materi_id}/peserta/{peserta_id}', [FasilitatorPenilaianController::class, 'submitPenilaian'])
    ->name('fasilitator.penilaian.submit');

Route::get('/invoice/print/{token}', [InvoiceController::class, 'publicPrint'])->name('public.invoice.print');
Route::get('/pay/{token}', [InvoiceController::class, 'publicPayment'])->name('public.invoice.pay');
Route::post('/pay/{token}', [InvoiceController::class, 'submitPayment'])->name('public.invoice.submit');
Route::get('/v/{slug}', [LinktreeController::class, 'showPublic'])->name('linktree.view');
Route::get('/formst/{slug}/download-bukti/{response_id}', [CustomFormController::class, 'downloadPDF'])->name('forms.public.download_pdf');
Route::get('/forms/{slug}/sukses', [CustomFormController::class, 'showSuccess'])->name('forms.public.success');
Route::get('/forms/{slug}', [CustomFormController::class, 'showPublic'])->name('forms.public.show');
Route::post('/forms/{slug}', [CustomFormController::class, 'submitPublic'])->name('forms.public.submit');
Route::get('/', [PublicController::class, 'landing'])->name('landing');
Route::post('/chatbot', [PublicController::class, 'chatbot'])->name('chatbot.ask');

// Evaluasi Publik (kritik, saran & IKM diklat) - dari landing
Route::get('/evaluasi', [\App\Http\Controllers\EvaluasiController::class, 'publicForm'])->name('evaluasi.public.form');
Route::get('/evaluasi/terima-kasih', [\App\Http\Controllers\EvaluasiController::class, 'terimaKasih'])->name('evaluasi.public.terimakasih');
Route::post('/evaluasi', [\App\Http\Controllers\EvaluasiController::class, 'publicStore'])->name('evaluasi.public.store');
Route::get('/forgot-password', [AuthController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
Route::get('/cek-data-pelatihan', [PelatihanController::class, 'publicIndex'])->name('public.pelatihan.index');
Route::get('/input-data-pelatihan/{pelatihan}/edit', [PelatihanController::class, 'publicEdit'])->name('public.pelatihan.edit');
Route::put('/input-data-pelatihan/{pelatihan}', [PelatihanController::class, 'publicUpdate'])->name('public.pelatihan.update');
Route::get('/mou-publik', [MouController::class, 'publicIndex'])->name('mou.public_index');
Route::get('/input-data-mou/create', [MouController::class, 'publicCreate'])->name('public.mou.create');
Route::post('/input-data-mou', [MouController::class, 'publicStore'])->name('public.mou.store');
Route::get('/penilaian/{token}', [PresentasiController::class, 'formPenilaian'])->name('ci.penilaian');
Route::post('/penilaian/{token}', [PresentasiController::class, 'submitPenilaian'])->name('ci.submit-penilaian');
Route::get('/presentasi/{id}/sertifikat/{nama_anggota}', [PresentasiController::class, 'downloadSertifikatAnggota'])->name('presentasi.download-sertifikat');
Route::get('/presentasi/{id}/surat-selesai/{nama_anggota}', [PresentasiController::class, 'downloadSuratSelesaiAnggota'])->name('presentasi.download-surat-selesai');
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/sertifikat/download/{token}', [AbsensiController::class, 'generateSertifikatPublik'])->name('sertifikat.download');
Route::get('/validasi-sertifikat/{token}', [AbsensiController::class, 'validasiSertifikat'])->name('sertifikat.validasi');
Route::get('/kegiatan/absen/{token}', [KegiatanController::class, 'publicFormAbsen'])->name('public.kegiatan.absen');
Route::post('/kegiatan/absen/{token}', [KegiatanController::class, 'submitAbsen'])->name('public.kegiatan.absen.submit');
Route::get('/daftar-diklat/{public_link}', [\App\Http\Controllers\DiklatPesertaController::class, 'publicForm'])->name('diklat.public.form');
Route::post('/daftar-diklat/{public_link}', [\App\Http\Controllers\DiklatPesertaController::class, 'register'])->name('diklat.public.register');
Route::get('/daftar-diklat/{public_link}/sukses', [\App\Http\Controllers\DiklatPesertaController::class, 'publicSuccess'])->name('diklat.public.success');
Route::get('/sertifikat/peserta/{id}/{hash}', [KegiatanPesertaController::class, 'publicSertifikat'])->name('public.sertifikat.peserta');

// ROUTE ABSENSI (PUBLIK - TANPA LOGIN)
Route::get('/absensi/{token}', [AbsensiController::class, 'card'])->name('absensi.card');
Route::post('/absensi/{token}/toggle', [AbsensiController::class, 'toggle'])->name('absensi.toggle');
Route::post('/absensi/{token}/register-device', [AbsensiController::class, 'registerDevice'])->name('absensi.register_device');

// =========================================================================
// 1. ROUTES UNTUK USER LOGIN (UMUM)
// =========================================================================
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/mahasiswa/{id}/rolling', [MahasiswaController::class, 'editRolling'])->name('mahasiswa.rolling.edit');
    Route::put('/mahasiswa/{id}/rolling', [MahasiswaController::class, 'updateRolling'])->name('mahasiswa.rolling.update');
    Route::get('/room-sequences/{id}/manage-shift', [ShiftScheduleController::class, 'manage'])->name('shift_schedule.manage');
    Route::post('/room-sequences/{id}/manage-shift', [ShiftScheduleController::class, 'store'])->name('shift_schedule.store');
    Route::prefix('pengajuan')->name('pengajuan.')->group(function () {
        Route::get('/', [PengajuanController::class, 'index'])->name('index');
        Route::get('/detail/{jenis}', [PengajuanController::class, 'detail'])->name('detail');
        Route::post('/pra', [PengajuanController::class, 'ajukanPra'])->name('pra');
        Route::post('/magang', [PengajuanController::class, 'ajukanMagang'])->name('magang');
        Route::post('/{pengajuan}/upload-bukti', [PengajuanController::class, 'uploadBuktiPembayaran'])->name('upload-bukti');
        Route::delete('/{pengajuan}', [PengajuanController::class, 'destroy'])->name('destroy');
    });
    Route::prefix('/room_sequences')->name('room_sequences.')->group(function () {
        Route::get('/', [RoomSequenceController::class, 'index'])->name('index');
        Route::get('/create', [RoomSequenceController::class, 'create'])->name('create');
        Route::post('/', [RoomSequenceController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [RoomSequenceController::class, 'edit'])->name('edit');
        Route::put('/{id}', [RoomSequenceController::class, 'update'])->name('update');
        Route::delete('/{id}', [RoomSequenceController::class, 'destroy'])->name('destroy');
    });
    Route::middleware(['magang'])->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
        Route::get('/dashboard', [MahasiswaController::class, 'dashboard'])->name('dashboard');
        Route::get('/ruangan-info/{id}', [MahasiswaController::class, 'getRuanganInfo'])->name('ruangan.info');
        Route::get('/search/universitas', [MahasiswaController::class, 'searchUniversitas'])->name('search.universitas');
        Route::get('/{mahasiswa}/edit', [MahasiswaController::class, 'edit'])->name('edit');
        Route::put('/{mahasiswa}', [MahasiswaController::class, 'update'])->name('update');
        Route::get('/create', [MahasiswaController::class, 'create'])->name('create');
        Route::post('/', [MahasiswaController::class, 'store'])->name('store');
        Route::patch('/{id}/approve-id-card', [MahasiswaController::class, 'approveIdCard'])->name('approve_id_card');
        Route::get('/dispensasi/template', [DispensasiController::class, 'downloadTemplate'])->name('dispensasi.template');
        Route::get('/dispensasi', [DispensasiController::class, 'index'])->name('dispensasi.index');
        Route::get('/dispensasi/create', [DispensasiController::class, 'create'])->name('dispensasi.create');
        Route::post('/dispensasi', [DispensasiController::class, 'store'])->name('dispensasi.store');
        Route::get('/dispensasi/{id}', [DispensasiController::class, 'show'])->name('dispensasi.show');
    });
    Route::middleware(['pra'])->prefix('pra-penelitian')->name('pra-penelitian.')->group(function () {
        Route::get('/create', [PraPenelitianController::class, 'create'])->name('create');
        Route::post('/', [PraPenelitianController::class, 'store'])->name('store');
    });
    Route::prefix('konsultasi')->name('konsultasi.')->group(function () {
        Route::get('/', [KonsultasiController::class, 'index'])->name('index');
        Route::post('/', [KonsultasiController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [KonsultasiController::class, 'edit'])->name('edit');
        Route::put('/{id}', [KonsultasiController::class, 'update'])->name('update');
        Route::delete('/{id}', [KonsultasiController::class, 'destroy'])->name('destroy');
    });
    Route::prefix('presentasi')->name('presentasi.')->group(function () {
        Route::get('/', [PresentasiController::class, 'show'])->name('show');
        Route::post('/{id}/upload-ppt', [PresentasiController::class, 'uploadPpt'])->name('upload-ppt');
        Route::post('/{id}/upload-laporan', [PresentasiController::class, 'uploadLaporan'])->name('upload-laporan');
        Route::get('/{id}/sertifikat/{nama_anggota}', [PresentasiController::class, 'downloadSertifikatAnggota'])->name('download-sertifikat');
        Route::get('/{id}/surat-selesai/{nama_anggota}', [PresentasiController::class, 'downloadSuratSelesaiAnggota'])->name('download-surat-selesai');
    });
    Route::get('/orientasi', [OrientasiController::class, 'index'])->name('orientasi.index');
    Route::post('/orientasi/start', [OrientasiController::class, 'startPreTest'])->name('orientasi.start');
    Route::get('/orientasi/pre-test', [OrientasiController::class, 'showPreTest'])->name('orientasi.pre');
    Route::post('/orientasi/pre-test', [OrientasiController::class, 'submitPreTest'])->name('orientasi.pre.submit');
    Route::get('/orientasi/post-test', [OrientasiController::class, 'showPostTest'])->name('orientasi.post');
    Route::post('/orientasi/post-test', [OrientasiController::class, 'submitPostTest'])->name('orientasi.post.submit');
    Route::get('/orientasi/sertifikat', [OrientasiController::class, 'sertifikat'])->name('orientasi.sertifikat');
    Route::post('/orientasi/complete/{id}', [OrientasiController::class, 'completeMaterial'])->name('orientasi.complete');
    Route::get('/orientasi/materi/{id}', [OrientasiController::class, 'showMaterial'])->name('orientasi.materi.show');
    Route::post('/orientasi/materi/{id}/complete', [OrientasiController::class, 'completeMaterial'])->name('orientasi.materi.complete');
});

Route::middleware(['auth', 'admin.kasir'])->prefix('admin/invoices')->name('admin.invoices.')->group(function () {
    Route::get('/', [InvoiceController::class, 'index'])->name('index');
    Route::get('/create', [InvoiceController::class, 'create'])->name('create');
    Route::post('/', [InvoiceController::class, 'store'])->name('store');
    Route::get('/{invoice}/edit', [InvoiceController::class, 'edit'])->name('edit');
    Route::put('/{invoice}', [InvoiceController::class, 'update'])->name('update');
    Route::delete('/{invoice}', [InvoiceController::class, 'destroy'])->name('destroy');
    Route::get('/report', [InvoiceController::class, 'report'])->name('report');
    Route::get('/{invoice}/print', [InvoiceController::class, 'print'])->name('print');
    Route::patch('/{invoice}/update-status', [InvoiceController::class, 'updateStatus'])->name('updateStatus');
    Route::patch('/{invoice}/verify', [InvoiceController::class, 'verify'])->name('verify');
});

// =========================================================================
// 2. ROUTES KHUSUS ADMIN
// =========================================================================
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/evaluasi-institusi', [DashboardController::class, 'evaluasiInstitusi'])->name('admin.evaluasi_institusi');
    Route::delete('/admin/hapus-nilai-magang/{mahasiswa_id}/{ruangan_id}', [DashboardController::class, 'hapusNilai'])->name('admin.hapus_nilai_magang');

    Route::prefix('admin/master-pelatihan')->name('admin.master_pelatihan.')->group(function () {
        Route::get('/', [MasterPelatihanController::class, 'index'])->name('index');
        Route::get('/create', [MasterPelatihanController::class, 'create'])->name('create');
        Route::post('/', [MasterPelatihanController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [MasterPelatihanController::class, 'edit'])->name('edit');
        Route::put('/{id}', [MasterPelatihanController::class, 'update'])->name('update');
        Route::delete('/{id}', [MasterPelatihanController::class, 'destroy'])->name('destroy');
        Route::post('/import', [MasterPelatihanController::class, 'importCsv'])->name('import');
    });

    Route::get('/admin/rekomendasi-pelatihan', [RekomendasiPelatihanController::class, 'generateRekomendasi'])->name('admin.rekomendasi_pelatihan');
    Route::post('/admin/mahasiswa/{id}/update-nilai-ruangan', [MahasiswaController::class, 'updateNilaiRuangan'])->name('admin.mahasiswa.update_nilai_ruangan');
    Route::get('/admin/orientasi/sertifikat-user/{user_id}', [OrientasiController::class, 'cetakSertifikat'])->name('admin.orientasi.sertifikat_user');

    Route::prefix('admin/master-instansi')->name('admin.master_instansi.')->group(function () {
        Route::get('/', [MasterInstansiController::class, 'index'])->name('index');
        Route::get('/create', [MasterInstansiController::class, 'create'])->name('create');
        Route::post('/', [MasterInstansiController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [MasterInstansiController::class, 'edit'])->name('edit');
        Route::put('/{id}', [MasterInstansiController::class, 'update'])->name('update');
        Route::delete('/{id}', [MasterInstansiController::class, 'destroy'])->name('destroy');
        Route::post('/import', [MasterInstansiController::class, 'import'])->name('import');
    });

    Route::prefix('admin/master-ruangan')->name('admin.master_ruangan.')->group(function () {
        Route::get('/', [MasterRuanganController::class, 'index'])->name('index');
        Route::get('/create', [MasterRuanganController::class, 'create'])->name('create');
        Route::post('/', [MasterRuanganController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [MasterRuanganController::class, 'edit'])->name('edit');
        Route::put('/{id}', [MasterRuanganController::class, 'update'])->name('update');
        Route::delete('/{id}', [MasterRuanganController::class, 'destroy'])->name('destroy');
        Route::post('/import', [MasterRuanganController::class, 'import'])->name('import');
    });

    Route::prefix('admin/api/export')->name('admin.api.export.')->group(function () {
        Route::get('/presensi-kampus', [AbsensiController::class, 'exportPresensiKampusJson'])->name('presensi');
        Route::get('/nilai-akhir', [AbsensiController::class, 'exportNilaiAkhirJson'])->name('nilai');
    });

    Route::prefix('admin/master-kompetensi')->name('admin.master_kompetensi.')->group(function () {
        Route::get('/', [\App\Http\Controllers\MasterKompetensiController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\MasterKompetensiController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\MasterKompetensiController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [\App\Http\Controllers\MasterKompetensiController::class, 'edit'])->name('edit');
        Route::put('/{id}', [\App\Http\Controllers\MasterKompetensiController::class, 'update'])->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\MasterKompetensiController::class, 'destroy'])->name('destroy');
        Route::post('/import', [\App\Http\Controllers\MasterKompetensiController::class, 'import'])->name('import');
    });

    // ---------------------------------------------------------------------
    // EVALUASI DIKLAT + IKM
    // ---------------------------------------------------------------------
    Route::prefix('admin/evaluasi')->name('admin.evaluasi.')->group(function () {
        Route::get('/', [\App\Http\Controllers\EvaluasiController::class, 'index'])->name('index');
        Route::get('/export/csv', [\App\Http\Controllers\EvaluasiController::class, 'exportCsv'])->name('export.csv');
        Route::get('/export/pdf', [\App\Http\Controllers\EvaluasiController::class, 'exportPdf'])->name('export.pdf');
        Route::get('/{id}', [\App\Http\Controllers\EvaluasiController::class, 'show'])->name('show');
        Route::delete('/{id}', [\App\Http\Controllers\EvaluasiController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('admin/master-evaluasi')->name('admin.master_evaluasi.')->group(function () {
        Route::get('/', [\App\Http\Controllers\MasterEvaluasiController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\MasterEvaluasiController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\MasterEvaluasiController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [\App\Http\Controllers\MasterEvaluasiController::class, 'edit'])->name('edit');
        Route::put('/{id}', [\App\Http\Controllers\MasterEvaluasiController::class, 'update'])->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\MasterEvaluasiController::class, 'destroy'])->name('destroy');
    });

    // ---------------------------------------------------------------------
    // KEGIATAN + PESERTA
    // ---------------------------------------------------------------------
    Route::prefix('admin/kegiatan')->name('admin.kegiatan.')->group(function () {
        Route::get('/', [KegiatanController::class, 'index'])->name('index');
        Route::get('/create', [KegiatanController::class, 'create'])->name('create');
        Route::post('/', [KegiatanController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [KegiatanController::class, 'edit'])->name('edit');
        Route::put('/{id}', [KegiatanController::class, 'update'])->name('update');
        Route::delete('/{id}', [KegiatanController::class, 'destroy'])->name('destroy');
        Route::post('/import', [KegiatanController::class, 'import'])->name('import');
        Route::patch('/{id}/update-status', [KegiatanController::class, 'updateStatus'])->name('update_status');

        // Routing Kegiatan Peserta
        Route::get('/{kegiatan_id}/peserta', [KegiatanPesertaController::class, 'index'])->name('peserta.index');
        Route::post('/{kegiatan_id}/peserta', [KegiatanPesertaController::class, 'store'])->name('peserta.store');

        // Approve / Batal Approve Peserta
        Route::post('/{kegiatan_id}/peserta/{id}/approve', [KegiatanPesertaController::class, 'approve'])->name('peserta.approve');
        Route::post('/{kegiatan_id}/peserta/{id}/batal-approve', [KegiatanPesertaController::class, 'batalApprove'])->name('peserta.batal_approve');

        Route::delete('/{kegiatan_id}/peserta/{id}', [KegiatanPesertaController::class, 'destroy'])->name('peserta.destroy');
        Route::post('/{kegiatan_id}/peserta/import', [KegiatanPesertaController::class, 'import'])->name('peserta.import');
        Route::post('/{kegiatan_id}/toggle-absen', [KegiatanPesertaController::class, 'toggleAbsen'])->name('toggle_absen');
        Route::put('/{kegiatan_id}/peserta/{id}', [KegiatanPesertaController::class, 'update'])->name('peserta.update');
        Route::get('/{kegiatan_id}/peserta-export', [KegiatanPesertaController::class, 'export'])->name('peserta.export');
        Route::delete('/{kegiatan_id}/peserta/{id}/reset-absen', [KegiatanPesertaController::class, 'resetAbsen'])->name('peserta.reset_absen');
        Route::get('/{id}/rekap', [KegiatanController::class, 'rekap'])->name('rekap');
    });

    // ---------------------------------------------------------------------
    // PENILAIAN PELATIHAN (Kegiatan -> Materi -> Item; Fasilitator <-> Materi)
    // ---------------------------------------------------------------------
    Route::prefix('admin/kegiatan/{kegiatan_id}/penilaian')->name('admin.kegiatan.penilaian.')->group(function () {
        // Cetak lembar observasi: per peserta per materi
        Route::get('/rekap/{peserta_id}/cetak-pdf/{materi_id}', [RekapPenilaianController::class, 'cetakPdf'])->name('cetak_pdf');

        Route::get('/setting', [PenilaianSettingController::class, 'index'])->name('setting');
        Route::post('/setting', [PenilaianSettingController::class, 'storeSetting'])->name('setting.store');

        // Materi
        Route::post('/materi', [PenilaianSettingController::class, 'storeMateri'])->name('materi.store');
        Route::put('/materi/{materi}', [PenilaianSettingController::class, 'updateMateri'])->name('materi.update');
        Route::delete('/materi/{materi}', [PenilaianSettingController::class, 'destroyMateri'])->name('materi.destroy');

        // Item checklist: tambah lewat materi
        Route::post('/materi/{materi}/item', [PenilaianSettingController::class, 'storeItem'])->name('item.store');
        Route::put('/item/{item}', [PenilaianSettingController::class, 'updateItem'])->name('item.update');
        Route::delete('/item/{item}', [PenilaianSettingController::class, 'destroyItem'])->name('item.destroy');

        // Fasilitator
        Route::post('/fasilitator', [PenilaianSettingController::class, 'storeFasilitator'])->name('fasilitator.store');
        Route::delete('/fasilitator/{fasilitator}', [PenilaianSettingController::class, 'destroyFasilitator'])->name('fasilitator.destroy');

        // Nilai teori (Pre/Post-Test)
        Route::get('/nilai-teori', [InputNilaiTeoriController::class, 'index'])->name('teori.index');
        Route::post('/nilai-teori/{peserta_id}', [InputNilaiTeoriController::class, 'updateNilai'])->name('teori.update');

        // Rekap & kelulusan
        Route::get('/rekap', [RekapPenilaianController::class, 'index'])->name('rekap');
    });

    Route::get('/admin/sync-perpustakaan', [PresentasiController::class, 'apiLaporan'])->name('admin.sync.perpustakaan');
    Route::post('/ruangan/generate-users', [RuanganController::class, 'generateUsers'])->name('ruangan.generate_users');

    Route::prefix('admin/dispensasi')->name('admin.dispensasi.')->group(function () {
        Route::get('/', [DispensasiController::class, 'adminIndex'])->name('index');
        Route::get('/{id}/edit', [DispensasiController::class, 'adminEdit'])->name('edit');
        Route::put('/{id}', [DispensasiController::class, 'adminUpdate'])->name('update');
        Route::delete('/{id}', [DispensasiController::class, 'adminDestroy'])->name('destroy');
        Route::post('/{id}/approve', [DispensasiController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [DispensasiController::class, 'reject'])->name('reject');
    });

    Route::prefix('admin/link')->name('admin.linktree.')->group(function () {
        Route::get('/', [LinktreeController::class, 'index'])->name('index');
        Route::post('/', [LinktreeController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [LinktreeController::class, 'edit'])->name('edit');
        Route::put('/{id}', [LinktreeController::class, 'update'])->name('update');
        Route::delete('/{id}', [LinktreeController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/toggle', [LinktreeController::class, 'toggleStatus'])->name('toggle');
        Route::post('/item/{packageId}', [LinktreeController::class, 'storeItem'])->name('item.store');
        Route::delete('/item/{id}', [LinktreeController::class, 'destroyItem'])->name('item.destroy');
        Route::post('/content/{itemId}', [LinktreeController::class, 'storeContent'])->name('content.store');
        Route::delete('/content/{id}', [LinktreeController::class, 'destroyContent'])->name('content.destroy');
    });

    Route::prefix('admin/forms')->name('admin.forms.')->group(function () {
        Route::get('/', [CustomFormController::class, 'index'])->name('index');
        Route::post('/{id}/duplicate', [CustomFormController::class, 'duplicate'])->name('duplicate');
        Route::get('/create', [CustomFormController::class, 'create'])->name('create');
        Route::post('/', [CustomFormController::class, 'store'])->name('store');
        Route::delete('/response/{id}', [CustomFormController::class, 'destroyResponse'])->name('responses.destroy');
        Route::get('/{id}/edit', [CustomFormController::class, 'edit'])->name('edit');
        Route::put('/{id}', [CustomFormController::class, 'update'])->name('update');
        Route::delete('/{id}', [CustomFormController::class, 'destroy'])->name('destroy');
        Route::get('/{id}/responses', [CustomFormController::class, 'responses'])->name('responses');
        Route::get('/{id}/export-pdf', [CustomFormController::class, 'exportResponsesPDF'])->name('export_pdf');
    });
    Route::patch('/admin/forms/{id}/toggle', [CustomFormController::class, 'toggleStatus'])->name('admin.forms.toggle');

    Route::get('/admin/orientasi', [AdminOrientasiController::class, 'index'])->name('admin.orientasi.index');
    Route::get('admin/orientasi/api-data', [AdminOrientasiController::class, 'apiData'])->name('admin.orientasi.api');
    Route::delete('/admin/orientasi/{id}', [AdminOrientasiController::class, 'destroy'])->name('admin.orientasi.destroy');

    Route::prefix('admin/pengajuan')->name('admin.pengajuan.')->group(function () {
        Route::get('/', [PengajuanController::class, 'adminIndex'])->name('index');
        Route::post('/{pengajuan}/approve', [PengajuanController::class, 'approve'])->name('approve');
        Route::post('/{pengajuan}/reject', [PengajuanController::class, 'reject'])->name('reject');
        Route::post('/{pengajuan}/kirim-galasan', [PengajuanController::class, 'kirimGalasan'])->name('kirim-galasan');
        Route::post('/{pengajuan}/approve-pembayaran', [PengajuanController::class, 'approvePembayaran'])->name('approve-pembayaran');
        Route::get('/{pengajuan}', [PengajuanController::class, 'show'])->name('show');
        Route::delete('/{pengajuan}', [PengajuanController::class, 'destroy'])->name('destroy');
        Route::post('/bulk-action', [PengajuanController::class, 'bulkAction'])->name('bulk_action');
        Route::post('/{pengajuan}/update-ci2', [PengajuanController::class, 'updateCi2'])->name('update-ci2');
        Route::get('/{pengajuan}/download-surat-otomatis', [PengajuanController::class, 'downloadSuratOtomatis'])->name('download-surat-otomatis');
        Route::post('/{pengajuan}/cancel', [PengajuanController::class, 'cancel'])->name('cancel');
    });

    Route::get('/mahasiswa/export-data', [MahasiswaController::class, 'exportData'])->name('mahasiswa.export_all');
    Route::prefix('mahasiswa')->name('mahasiswa.')->group(function () {
        Route::get('/', [MahasiswaController::class, 'index'])->name('index');
        Route::post('/import-excel', [MahasiswaController::class, 'importExcel'])->name('import_excel');
        Route::get('/export', [MahasiswaController::class, 'export'])->name('export');
        Route::get('/links', [MahasiswaController::class, 'copyLinks'])->name('links');
        Route::get('/{id}/sertifikat/summary', [MahasiswaController::class, 'showSertifikatSummary'])->name('sertifikat.summary');
        Route::get('/{mahasiswa}', [MahasiswaController::class, 'show'])->name('show');
        Route::delete('/{mahasiswa}', [MahasiswaController::class, 'destroy'])->name('destroy');
        Route::get('/{id}/sertifikat', [AbsensiController::class, 'generateSertifikatPublik'])->name('sertifikat');
        Route::post('/{id}/reset-device', [MahasiswaController::class, 'resetDevice'])->name('reset-device');
        Route::post('/{id}/reset-edit', [MahasiswaController::class, 'resetEdit'])->name('reset-edit');
    });

    Route::prefix('pra-penelitian')->name('pra-penelitian.')->group(function () {
        Route::get('/', [PraPenelitianController::class, 'index'])->name('index');
        Route::get('/{pra_penelitian}', [PraPenelitianController::class, 'show'])->name('show');
        Route::get('/{pra_penelitian}/edit', [PraPenelitianController::class, 'edit'])->name('edit');
        Route::put('/{pra_penelitian}', [PraPenelitianController::class, 'update'])->name('update');
        Route::delete('/{pra_penelitian}', [PraPenelitianController::class, 'destroy'])->name('destroy');
        Route::patch('/{pra_penelitian}/batal', [PraPenelitianController::class, 'batal'])->name('batal');
        Route::post('/{praPenelitian}/approve-form', [PraPenelitianController::class, 'approveForm'])->name('approve');
        Route::post('/{praPenelitian}/reject-form', [PraPenelitianController::class, 'rejectForm'])->name('reject');
        Route::patch('/{id}/update-jenis', [PraPenelitianController::class, 'updateJenisMahasiswa'])->name('update-jenis');
    });

    Route::prefix('ruangan')->name('ruangan.')->group(function () {
        Route::get('/', [RuanganController::class, 'index'])->name('index');
        Route::get('/create', [RuanganController::class, 'create'])->name('create');
        Route::post('/', [RuanganController::class, 'store'])->name('store');
        Route::get('/{ruangan}', [RuanganController::class, 'show'])->name('show');
        Route::get('/{ruangan}/edit', [RuanganController::class, 'edit'])->name('edit');
        Route::put('/{ruangan}', [RuanganController::class, 'update'])->name('update');
        Route::delete('/{ruangan}', [RuanganController::class, 'destroy'])->name('destroy');
        Route::get('/{id}/shifts', [RoomShiftController::class, 'index'])->name('shifts.index');
        Route::post('/{id}/shifts', [RoomShiftController::class, 'store'])->name('shifts.store');
        Route::delete('/shifts/{shift_id}', [RoomShiftController::class, 'destroy'])->name('shifts.destroy');
    });

    Route::get('pelatihan', [PelatihanController::class, 'index'])->name('pelatihan.index');
    Route::get('pelatihan/export-data', [PelatihanController::class, 'exportData'])->name('pelatihan.exportData');
    Route::post('pelatihan/{id}', [PelatihanController::class, 'storePelatihan'])->name('pelatihan.storeAPI');
    Route::put('pelatihan/{id}/{index}', [PelatihanController::class, 'updatePelatihan'])->name('pelatihan.updateAPI');
    Route::delete('pelatihan/{id}/{index}', [PelatihanController::class, 'destroyPelatihan'])->name('pelatihan.destroyAPI');
    Route::get('pelatihan/{id}/{index}/detail', [PelatihanController::class, 'showPelatihanAPI'])->name('pelatihan.showAPI');

    Route::get('pelatihan/{id}', [PelatihanController::class, 'show'])->name('pelatihan.show');

    Route::prefix('surat-balasan')->name('surat-balasan.')->group(function () {
        Route::get('/', [SuratBalasanController::class, 'index'])->name('index');
        Route::get('/create', [SuratBalasanController::class, 'create'])->name('create');
        Route::post('/store', [SuratBalasanController::class, 'store'])->name('store');
        Route::get('/edit/{suratBalasan}', [SuratBalasanController::class, 'edit'])->name('edit');
        Route::put('/update/{suratBalasan}', [SuratBalasanController::class, 'update'])->name('update');
        Route::delete('/delete/{suratBalasan}', [SuratBalasanController::class, 'destroy'])->name('destroy');
        Route::get('/pdf/{suratBalasan}', [SuratBalasanController::class, 'generatePdf'])->name('pdf');
    });

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
        Route::post('/{id}/finalisasi', [PresentasiController::class, 'terimaSemuaNilai'])->name('finalisasi');
    });

    Route::get('/notes', [NoteController::class, 'index'])->name('notes.index');
    Route::post('/notes', [NoteController::class, 'store'])->name('notes.store');
    Route::delete('/notes/{id}', [NoteController::class, 'destroy'])->name('notes.destroy');

    Route::prefix('admin/ci')->name('admin.ci.')->group(function () {
        Route::get('/', [CorporateInstructorController::class, 'index'])->name('index');
        Route::get('/create', [CorporateInstructorController::class, 'create'])->name('create');
        Route::post('/', [CorporateInstructorController::class, 'store'])->name('store');
        Route::get('/{ci}/edit', [CorporateInstructorController::class, 'edit'])->name('edit');
        Route::put('/{ci}', [CorporateInstructorController::class, 'update'])->name('update');
        Route::delete('/{ci}', [CorporateInstructorController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('room_schedules')->name('room_schedules.')->group(function () {
        Route::get('/', [RoomScheduleController::class, 'index'])->name('index');
        Route::delete('/{id}', [RoomScheduleController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('admin/materi')->name('admin.materi.')->group(function () {
        Route::get('/', [MaterialController::class, 'index'])->name('index');
        Route::get('/create', [MaterialController::class, 'create'])->name('create');
        Route::post('/', [MaterialController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [MaterialController::class, 'edit'])->name('edit');
        Route::put('/{id}', [MaterialController::class, 'update'])->name('update');
        Route::delete('/{id}', [MaterialController::class, 'destroy'])->name('destroy');
        Route::get('/{id}/files', [MaterialController::class, 'manageFiles'])->name('files');
        Route::post('/{id}/files', [MaterialController::class, 'storeFile'])->name('files.store');
        Route::delete('/file/{id}', [MaterialController::class, 'destroyFile'])->name('files.destroy');
    });

    Route::get('/absensi', [AbsensiController::class, 'index'])->name('absensi.index');
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users/{id}/approve', [UserController::class, 'approve'])->name('users.approve');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
});

Route::middleware(['auth', 'admin'])->prefix('diklat')->name('diklat.')->group(function () {
    Route::get('/', [\App\Http\Controllers\DiklatFormController::class, 'index'])->name('index');
    Route::get('/create', [\App\Http\Controllers\DiklatFormController::class, 'create'])->name('create');
    Route::post('/', [\App\Http\Controllers\DiklatFormController::class, 'store'])->name('store');
    Route::get('/{id}', [\App\Http\Controllers\DiklatFormController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [\App\Http\Controllers\DiklatFormController::class, 'edit'])->name('edit');
    Route::put('/{id}', [\App\Http\Controllers\DiklatFormController::class, 'update'])->name('update');
    Route::delete('/{id}', [\App\Http\Controllers\DiklatFormController::class, 'destroy'])->name('destroy');
    Route::get('/{id}/rekap', [\App\Http\Controllers\DiklatPesertaController::class, 'rekap'])->name('rekap');
    Route::delete('/peserta/{id}', [\App\Http\Controllers\DiklatFormController::class, 'destroyPeserta'])->name('peserta.destroy');
});

// =========================================================================
// 3. ROUTES KHUSUS KARU
// =========================================================================
Route::middleware(['auth'])->prefix('kepala-ruangan')->name('kepala_ruangan.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\KepalaRuanganController::class, 'dashboard'])->name('dashboard');
    Route::post('/ruangan/mahasiswa/{id}/nilai', [\App\Http\Controllers\KepalaRuanganController::class, 'simpanNilai'])->name('simpan_nilai');

    // Kepala ruangan boleh ACC / tolak dispensasi mahasiswa di ruangannya
    // (otorisasi kepemilikan ruangan dicek di DispensasiController::bolehKelola)
    Route::post('/dispensasi/{id}/approve', [\App\Http\Controllers\DispensasiController::class, 'approve'])->name('dispensasi.approve');
    Route::post('/dispensasi/{id}/reject', [\App\Http\Controllers\DispensasiController::class, 'reject'])->name('dispensasi.reject');
});