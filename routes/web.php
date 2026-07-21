<?php

use App\Http\Controllers\ProfileController;

// Sekretariat
use App\Http\Controllers\Sekretariat\AnggotaKategorialController;
use App\Http\Controllers\Sekretariat\BaptisController;
use App\Http\Controllers\Sekretariat\DashboardController as SekretariatDashboardController;
use App\Http\Controllers\Sekretariat\KategorialController;
use App\Http\Controllers\Sekretariat\KeanggotaanDppController;
use App\Http\Controllers\Sekretariat\KeluargaController;
use App\Http\Controllers\Sekretariat\KematianController;
use App\Http\Controllers\Sekretariat\KeuskupanController;
use App\Http\Controllers\Sekretariat\KlerusController;
use App\Http\Controllers\Sekretariat\KomuniPertamaController;
use App\Http\Controllers\Sekretariat\KrismaController;
use App\Http\Controllers\Sekretariat\KuasiController;
use App\Http\Controllers\Sekretariat\KubController;
use App\Http\Controllers\Sekretariat\MinyakSuciController;
use App\Http\Controllers\Sekretariat\MutasiAgamaController;
use App\Http\Controllers\Sekretariat\MutasiController;
use App\Http\Controllers\Sekretariat\MutasiKeluargaController;
use App\Http\Controllers\Sekretariat\MutasiUmatController;
use App\Http\Controllers\Sekretariat\ParokiController;
use App\Http\Controllers\Sekretariat\PenggunaController;
use App\Http\Controllers\Sekretariat\PernikahanController;
use App\Http\Controllers\Sekretariat\SakramenController;
use App\Http\Controllers\Sekretariat\StasiController;
use App\Http\Controllers\Sekretariat\UmatController;
use App\Http\Controllers\Sekretariat\ImportController;
use App\Http\Controllers\Sekretariat\WilayahController;

// Portal (Umat / Ketua KUB / Ketua Kategorial)
use App\Http\Controllers\Portal\DashboardController as PortalDashboardController;
use App\Http\Controllers\Portal\KubController as PortalKubController;
use App\Http\Controllers\Portal\KeluargaController as PortalKeluargaController;
use App\Http\Controllers\Portal\UmatController as PortalUmatController;
use App\Http\Controllers\Portal\KategorialController as PortalKategorialController;
use App\Http\Controllers\Portal\MutasiRequestController;
use App\Http\Controllers\Portal\PendaftaranController as PortalPendaftaranController;
use App\Http\Controllers\Portal\SakramenAnggotaController;
use Illuminate\Support\Facades\Route;


Route::get('/', fn() => redirect()->route('login'));

require __DIR__.'/auth.php';

// Profile (semua role yang sudah login)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ─────────────────────────────────────────────────────────────────────────────
// Sekretariat
// ─────────────────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:sekretariat'])
    ->prefix('sekretariat')
    ->name('sekretariat.')
    ->group(function () {
        Route::get('/dashboard', [SekretariatDashboardController::class, 'index'])->name('dashboard');

        Route::resource('keuskupan', KeuskupanController::class);
        Route::resource('klerus', KlerusController::class)->parameter('klerus', 'klerus');
        Route::resource('paroki', ParokiController::class);
        Route::resource('kuasi', KuasiController::class);
        Route::resource('stasi', StasiController::class);
        Route::resource('wilayah', WilayahController::class)->except(['show']);
        Route::resource('kub', KubController::class)->except(['show']);

        // Kelola Keluarga & Umat
        Route::resource('keluarga', KeluargaController::class);
        Route::resource('keluarga.umat', UmatController::class)
            ->shallow()
            ->except(['index', 'show']);

        // Kelola Umat langsung (tanpa melalui keluarga)
        Route::get('umat', [UmatController::class, 'index'])->name('umat.index');
        Route::get('umat/create', [UmatController::class, 'createStandalone'])->name('umat.create');
        Route::post('umat', [UmatController::class, 'storeStandalone'])->name('umat.store');
        Route::get('umat/{umat}', [UmatController::class, 'show'])->name('umat.show');

        // Kematian
        Route::resource('kematian', KematianController::class);

        // Kelola Pengguna
        Route::get('pengguna', [PenggunaController::class, 'index'])->name('pengguna.index');
        Route::get('pengguna/{user}/edit', [PenggunaController::class, 'edit'])->name('pengguna.edit');
        Route::put('pengguna/{user}', [PenggunaController::class, 'update'])->name('pengguna.update');
        Route::post('pengguna/{user}/reset-password', [PenggunaController::class, 'resetPassword'])->name('pengguna.reset-password');

        // Kategorial
        Route::resource('kategorial', KategorialController::class);
        Route::resource('kategorial.anggota-kategorial', AnggotaKategorialController::class)
            ->shallow()
            ->except(['index', 'show']);

        // Mutasi
        Route::resource('mutasi', MutasiController::class)
            ->only(['index', 'destroy']);
        Route::post('mutasi/{mutasi}/approve', [MutasiController::class, 'approve'])
            ->name('mutasi.approve');
        Route::post('mutasi/{mutasi}/reject', [MutasiController::class, 'reject'])
            ->name('mutasi.reject');
        Route::resource('mutasi/umat', MutasiUmatController::class)
            ->names('mutasi.umat')
            ->parameters(['umat' => 'mutasiUmat']);
        Route::resource('mutasi/keluarga', MutasiKeluargaController::class)
            ->names('mutasi.keluarga')
            ->parameters(['keluarga' => 'mutasiKeluarga']);
        Route::resource('mutasi/agama', MutasiAgamaController::class)
            ->names('mutasi.agama')
            ->parameters(['agama' => 'mutasiAgama']);

        // Sakramen
        Route::resource('sakramen', SakramenController::class)
            ->only(['index', 'destroy'])
            ->parameters(['sakramen' => 'sakramen']);
        Route::resource('sakramen/baptis', BaptisController::class)
            ->names('sakramen.baptis')
            ->parameters(['baptis' => 'sakramen']);
        Route::resource('sakramen/komuni-pertama', KomuniPertamaController::class)
            ->names('sakramen.komuni-pertama')
            ->parameters(['komuni-pertama' => 'sakramen']);
        Route::resource('sakramen/krisma', KrismaController::class)
            ->names('sakramen.krisma')
            ->parameters(['krisma' => 'sakramen']);
        Route::resource('sakramen/pernikahan', PernikahanController::class)
            ->names('sakramen.pernikahan')
            ->parameters(['pernikahan' => 'sakramen']);
        Route::resource('sakramen/minyak-suci', MinyakSuciController::class)
            ->names('sakramen.minyak-suci')
            ->parameters(['minyak-suci' => 'sakramen']);

        // Keanggotaan Dpp
        Route::prefix('dpp')->name('dpp.')->group(function () {
            Route::resource('keanggotaan', KeanggotaanDppController::class)
                ->parameters(['keanggotaan' => 'keanggotaan'])
                ->names([
                    'index'   => 'keanggotaan.index',
                    'create'  => 'keanggotaan.create',
                    'store'   => 'keanggotaan.store',
                    'show'    => 'keanggotaan.show',
                    'edit'    => 'keanggotaan.edit',
                    'update'  => 'keanggotaan.update',
                    'destroy' => 'keanggotaan.destroy',
                ]);
        });

        // Import Data
        Route::prefix('import')->name('import.')->group(function () {
            Route::get('/', [ImportController::class, 'index'])->name('index');
            Route::get('/template/{jenis}', [ImportController::class, 'downloadTemplate'])->name('template');
            Route::post('/{jenis}', [ImportController::class, 'import'])->name('proses');
        });

        // Laporan & Cetak PDF (Read-only)
        Route::prefix('laporan')->name('laporan.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Sekretariat\LaporanController::class, 'index'])->name('index');
            Route::get('/sakramen', [\App\Http\Controllers\Sekretariat\LaporanController::class, 'sakramenPdf'])->name('sakramen');
            Route::get('/umat', [\App\Http\Controllers\Sekretariat\LaporanController::class, 'umatPdf'])->name('umat');
            Route::get('/mutasi', [\App\Http\Controllers\Sekretariat\LaporanController::class, 'mutasiPdf'])->name('mutasi');
            Route::get('/organisasi', [\App\Http\Controllers\Sekretariat\LaporanController::class, 'organisasiPdf'])->name('organisasi');
        });
    });


// ─────────────────────────────────────────────────────────────────────────────
// Portal — Umat / Ketua KUB / Ketua Kategorial
// prefix : /portal
// name   : portal.*
// Middleware 'role' membolehkan salah satu dari 3 role ini.
// Sub-group di dalam menggunakan middleware tambahan untuk fitur eksklusif.
// ─────────────────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:umat,ketua_kub,ketua_kategorial'])
    ->prefix('portal')
    ->name('portal.')
    ->group(function () {

        // Dashboard bersama (semua role portal)
        Route::get('dashboard', [PortalDashboardController::class, 'index'])->name('dashboard');

        // Mutasi request (semua role portal)
        Route::prefix('mutasi')->name('mutasi.')->group(function () {
            Route::get('/', [MutasiRequestController::class, 'index'])->name('index');
            Route::get('{mutasi}', [MutasiRequestController::class, 'show'])->name('show');
            Route::get('/umat/ajukan', [MutasiRequestController::class, 'createUmat'])->name('umat.create');
            Route::post('/umat/ajukan', [MutasiRequestController::class, 'storeUmat'])->name('umat.store');
            Route::get('/agama/ajukan', [MutasiRequestController::class, 'createAgama'])->name('agama.create');
            Route::post('/agama/ajukan', [MutasiRequestController::class, 'storeAgama'])->name('agama.store');
            Route::get('/keluarga/ajukan', [MutasiRequestController::class, 'createKeluarga'])->name('keluarga.create');
            Route::post('/keluarga/ajukan', [MutasiRequestController::class, 'storeKeluarga'])->name('keluarga.store');
            // Ketua KUB: ajukan mutasi atas nama umat di KUB-nya
            Route::get('/umat-kub/ajukan', [MutasiRequestController::class, 'createUmatKub'])->name('umat-kub.create');
            Route::post('/umat-kub/ajukan', [MutasiRequestController::class, 'storeUmatKub'])->name('umat-kub.store');
        });

        // Edit data pribadi (semua role portal)
        Route::get('profil/edit', [\App\Http\Controllers\Portal\ProfilController::class, 'edit'])->name('profil.edit');
        Route::put('profil', [\App\Http\Controllers\Portal\ProfilController::class, 'update'])->name('profil.update');

        // Tambah anggota keluarga (khusus kepala keluarga — dicek di controller)
        Route::get('keluarga-saya', [\App\Http\Controllers\Portal\KeluargaSayaController::class, 'show'])->name('keluarga-saya.show');
        Route::get('keluarga-saya/edit', [\App\Http\Controllers\Portal\KeluargaSayaController::class, 'edit'])->name('keluarga-saya.edit');
        Route::put('keluarga-saya', [\App\Http\Controllers\Portal\KeluargaSayaController::class, 'update'])->name('keluarga-saya.update');
        Route::get('keluarga-saya/anggota/tambah', [\App\Http\Controllers\Portal\AnggotaKeluargaController::class, 'create'])->name('keluarga-saya.anggota.create');
        Route::post('keluarga-saya/anggota', [\App\Http\Controllers\Portal\AnggotaKeluargaController::class, 'store'])->name('keluarga-saya.anggota.store');
        Route::get('keluarga-saya/cetak', [\App\Http\Controllers\Portal\KeluargaSayaController::class, 'cetak'])->name('keluarga-saya.cetak');

        // ── Sakramen Saya (semua role portal) ───────────────────────────────
        Route::prefix('sakramen-saya')->name('sakramen-saya.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Portal\SakramenSayaController::class, 'index'])->name('index');
            // Baptis
            Route::get('baptis', [\App\Http\Controllers\Portal\SakramenSayaController::class, 'showBaptis'])->name('baptis');
            Route::post('baptis', [\App\Http\Controllers\Portal\SakramenSayaController::class, 'storeBaptis'])->name('baptis.store');
            Route::get('baptis/edit', [\App\Http\Controllers\Portal\SakramenSayaController::class, 'editBaptis'])->name('baptis.edit');
            Route::put('baptis', [\App\Http\Controllers\Portal\SakramenSayaController::class, 'updateBaptis'])->name('baptis.update');
            // Komuni Pertama
            Route::get('komuni-pertama', [\App\Http\Controllers\Portal\SakramenSayaController::class, 'showKomuni'])->name('komuni');
            Route::post('komuni-pertama', [\App\Http\Controllers\Portal\SakramenSayaController::class, 'storeKomuni'])->name('komuni.store');
            Route::get('komuni-pertama/edit', [\App\Http\Controllers\Portal\SakramenSayaController::class, 'editKomuni'])->name('komuni.edit');
            Route::put('komuni-pertama', [\App\Http\Controllers\Portal\SakramenSayaController::class, 'updateKomuni'])->name('komuni.update');
            // Krisma
            Route::get('krisma', [\App\Http\Controllers\Portal\SakramenSayaController::class, 'showKrisma'])->name('krisma');
            Route::post('krisma', [\App\Http\Controllers\Portal\SakramenSayaController::class, 'storeKrisma'])->name('krisma.store');
            Route::get('krisma/edit', [\App\Http\Controllers\Portal\SakramenSayaController::class, 'editKrisma'])->name('krisma.edit');
            Route::put('krisma', [\App\Http\Controllers\Portal\SakramenSayaController::class, 'updateKrisma'])->name('krisma.update');
            // Pernikahan
            Route::get('pernikahan', [\App\Http\Controllers\Portal\SakramenSayaController::class, 'showPernikahan'])->name('pernikahan');
            Route::post('pernikahan', [\App\Http\Controllers\Portal\SakramenSayaController::class, 'storePernikahan'])->name('pernikahan.store');
            Route::get('pernikahan/edit', [\App\Http\Controllers\Portal\SakramenSayaController::class, 'editPernikahan'])->name('pernikahan.edit');
            Route::put('pernikahan', [\App\Http\Controllers\Portal\SakramenSayaController::class, 'updatePernikahan'])->name('pernikahan.update');
            // Minyak Suci (multiple)
            Route::get('minyak-suci', [\App\Http\Controllers\Portal\SakramenSayaController::class, 'indexMinyakSuci'])->name('minyak-suci');
            Route::post('minyak-suci', [\App\Http\Controllers\Portal\SakramenSayaController::class, 'storeMinyakSuci'])->name('minyak-suci.store');
            Route::get('minyak-suci/{sakramen}/edit', [\App\Http\Controllers\Portal\SakramenSayaController::class, 'editMinyakSuci'])->name('minyak-suci.edit');
            Route::put('minyak-suci/{sakramen}', [\App\Http\Controllers\Portal\SakramenSayaController::class, 'updateMinyakSuci'])->name('minyak-suci.update');
        });

        // ── Sakramen Anggota Keluarga (hanya kepala keluarga — dicek di controller) ──
        Route::prefix('keluarga-saya/anggota/{anggota}/sakramen')->name('sakramen-anggota.')->group(function () {
            Route::get('/', [SakramenAnggotaController::class, 'index'])->name('index');
            // Baptis
            Route::get('baptis', [SakramenAnggotaController::class, 'showBaptis'])->name('baptis');
            Route::post('baptis', [SakramenAnggotaController::class, 'storeBaptis'])->name('baptis.store');
            Route::get('baptis/edit', [SakramenAnggotaController::class, 'editBaptis'])->name('baptis.edit');
            Route::put('baptis', [SakramenAnggotaController::class, 'updateBaptis'])->name('baptis.update');
            // Komuni Pertama
            Route::get('komuni-pertama', [SakramenAnggotaController::class, 'showKomuni'])->name('komuni');
            Route::post('komuni-pertama', [SakramenAnggotaController::class, 'storeKomuni'])->name('komuni.store');
            Route::get('komuni-pertama/edit', [SakramenAnggotaController::class, 'editKomuni'])->name('komuni.edit');
            Route::put('komuni-pertama', [SakramenAnggotaController::class, 'updateKomuni'])->name('komuni.update');
            // Krisma
            Route::get('krisma', [SakramenAnggotaController::class, 'showKrisma'])->name('krisma');
            Route::post('krisma', [SakramenAnggotaController::class, 'storeKrisma'])->name('krisma.store');
            Route::get('krisma/edit', [SakramenAnggotaController::class, 'editKrisma'])->name('krisma.edit');
            Route::put('krisma', [SakramenAnggotaController::class, 'updateKrisma'])->name('krisma.update');
            // Pernikahan
            Route::get('pernikahan', [SakramenAnggotaController::class, 'showPernikahan'])->name('pernikahan');
            Route::post('pernikahan', [SakramenAnggotaController::class, 'storePernikahan'])->name('pernikahan.store');
            Route::get('pernikahan/edit', [SakramenAnggotaController::class, 'editPernikahan'])->name('pernikahan.edit');
            Route::put('pernikahan', [SakramenAnggotaController::class, 'updatePernikahan'])->name('pernikahan.update');
            // Minyak Suci (multiple)
            Route::get('minyak-suci', [SakramenAnggotaController::class, 'indexMinyakSuci'])->name('minyak-suci');
            Route::post('minyak-suci', [SakramenAnggotaController::class, 'storeMinyakSuci'])->name('minyak-suci.store');
            Route::get('minyak-suci/{sakramen}/edit', [SakramenAnggotaController::class, 'editMinyakSuci'])->name('minyak-suci.edit');
            Route::put('minyak-suci/{sakramen}', [SakramenAnggotaController::class, 'updateMinyakSuci'])->name('minyak-suci.update');
        });

        // ── Fitur Ketua KUB ───────────────────────────────────────────────────
        Route::middleware('role:ketua_kub')->group(function () {
            Route::get('kub/show', [PortalKubController::class, 'show'])->name('kub.show');
            Route::get('kub/edit', [PortalKubController::class, 'edit'])->name('kub.edit');
            Route::put('kub', [PortalKubController::class, 'update'])->name('kub.update');
            Route::get('pendaftaran', [PortalPendaftaranController::class, 'index'])->name('pendaftaran.index');
            Route::post('pendaftaran/{user}/approve', [PortalPendaftaranController::class, 'approve'])->name('pendaftaran.approve');
            Route::post('pendaftaran/{user}/reject', [PortalPendaftaranController::class, 'reject'])->name('pendaftaran.reject');
            Route::resource('keluarga', PortalKeluargaController::class);
            Route::resource('umat', PortalUmatController::class);
        });

        // ── Fitur Ketua Kategorial ────────────────────────────────────────────
        Route::middleware('role:ketua_kategorial')->group(function () {
            Route::get('kategorial', [PortalKategorialController::class, 'index'])->name('kategorial.index');
            Route::get('kategorial/{kategorial}', [PortalKategorialController::class, 'show'])->name('kategorial.show');
            Route::get('kategorial/{kategorial}/edit', [PortalKategorialController::class, 'edit'])->name('kategorial.edit');
            Route::put('kategorial/{kategorial}', [PortalKategorialController::class, 'update'])->name('kategorial.update');
            Route::post('kategorial/{kategorial}/anggota', [PortalKategorialController::class, 'storeAnggota'])->name('kategorial.anggota.store');
            Route::get('kategorial/{kategorial}/anggota/{anggota}/edit', [PortalKategorialController::class, 'editAnggota'])->name('kategorial.anggota.edit');
            Route::put('kategorial/{kategorial}/anggota/{anggota}', [PortalKategorialController::class, 'updateAnggota'])->name('kategorial.anggota.update');
            Route::delete('kategorial/{kategorial}/anggota/{anggota}', [PortalKategorialController::class, 'destroyAnggota'])->name('kategorial.anggota.destroy');
        });
    });

// ─────────────────────────────────────────────────────────────────────────────
// Pastor
// ─────────────────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:pastor'])
    ->prefix('pastor')
    ->name('pastor.')
    ->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Pastor\DashboardController::class, 'index'])->name('dashboard');

        // Direktori Umat & Keluarga (Read-only)
        Route::get('/umat', [\App\Http\Controllers\Pastor\UmatController::class, 'index'])->name('umat.index');
        Route::get('/umat/{umat}', [\App\Http\Controllers\Pastor\UmatController::class, 'show'])->name('umat.show');
        Route::get('/keluarga', [\App\Http\Controllers\Pastor\KeluargaController::class, 'index'])->name('keluarga.index');
        Route::get('/keluarga/{keluarga}', [\App\Http\Controllers\Pastor\KeluargaController::class, 'show'])->name('keluarga.show');

        // Sakramen (Read-only logs)
        Route::get('/sakramen', [\App\Http\Controllers\Pastor\SakramenController::class, 'index'])->name('sakramen.index');

        // Mutasi (Read-only logs)
        Route::get('/mutasi', [\App\Http\Controllers\Pastor\MutasiController::class, 'index'])->name('mutasi.index');

        // Klerus (View peers)
        Route::get('/klerus', [\App\Http\Controllers\Pastor\KlerusController::class, 'index'])->name('klerus.index');

        // DPP & Kategorial (Read-only)
        Route::get('/dpp', [\App\Http\Controllers\Pastor\DppController::class, 'index'])->name('dpp.index');
        Route::get('/kategorial', [\App\Http\Controllers\Pastor\KategorialController::class, 'index'])->name('kategorial.index');

        // Laporan & Cetak PDF (Read-only)
        Route::prefix('laporan')->name('laporan.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Pastor\LaporanController::class, 'index'])->name('index');
            Route::get('/sakramen', [\App\Http\Controllers\Pastor\LaporanController::class, 'sakramenPdf'])->name('sakramen');
            Route::get('/umat', [\App\Http\Controllers\Pastor\LaporanController::class, 'umatPdf'])->name('umat');
            Route::get('/mutasi', [\App\Http\Controllers\Pastor\LaporanController::class, 'mutasiPdf'])->name('mutasi');
        });
    });

// ─────────────────────────────────────────────────────────────────────────────
// Dewan Pastoral Paroki (DPP)
// ─────────────────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:dewan_pastoral'])
    ->prefix('dpp')
    ->name('dewan_pastoral.')
    ->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\DewanPastoral\DashboardController::class, 'index'])->name('dashboard');

        Route::resource('keanggotaan', \App\Http\Controllers\DewanPastoral\KeanggotaanDppController::class)
            ->parameters(['keanggotaan' => 'keanggotaan'])
            ->names('keanggotaan');
    });
