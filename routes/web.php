<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\OrganizationTypeController;
use App\Http\Controllers\OrganizationUnitController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\WargaController;
use App\Http\Controllers\KartuKeluargaController;
use App\Http\Controllers\MutasiWargaController;
use App\Http\Controllers\KeuanganController;
use App\Http\Controllers\InventarisController;
use App\Http\Controllers\PeminjamanInventarisController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will be
| assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect('/login');
});

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Protected routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Profile routes
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'index'])->name('profile.index');

    // User Management routes
    Route::middleware('permission:manage_users')->group(function () {
        Route::resource('users', UserController::class);
    });

    // Data Warga — pendataan warga
    Route::middleware('permission:view_warga')->group(function () {
        Route::get('warga', [WargaController::class, 'index'])->name('warga.index');
        Route::get('warga/{warga}', [WargaController::class, 'show'])->name('warga.show');
    });
    Route::middleware('permission:manage_warga')->group(function () {
        Route::get('warga-create', [WargaController::class, 'create'])->name('warga.create');
        Route::post('warga', [WargaController::class, 'store'])->name('warga.store');
        Route::get('warga/{warga}/edit', [WargaController::class, 'edit'])->name('warga.edit');
        Route::put('warga/{warga}', [WargaController::class, 'update'])->name('warga.update');
        Route::delete('warga/{warga}', [WargaController::class, 'destroy'])->name('warga.destroy');
    });

    // Data Kartu Keluarga — KK & anggota keluarga
    Route::middleware('permission:view_kk')->group(function () {
        Route::get('kartu-keluarga', [KartuKeluargaController::class, 'index'])->name('kartu-keluarga.index');
        Route::get('kartu-keluarga/{kartuKeluarga}', [KartuKeluargaController::class, 'show'])->name('kartu-keluarga.show');
    });
    Route::middleware('permission:manage_kk')->group(function () {
        Route::get('kartu-keluarga-create', [KartuKeluargaController::class, 'create'])->name('kartu-keluarga.create');
        Route::post('kartu-keluarga', [KartuKeluargaController::class, 'store'])->name('kartu-keluarga.store');
        Route::get('kartu-keluarga/{kartuKeluarga}/edit', [KartuKeluargaController::class, 'edit'])->name('kartu-keluarga.edit');
        Route::put('kartu-keluarga/{kartuKeluarga}', [KartuKeluargaController::class, 'update'])->name('kartu-keluarga.update');
        Route::delete('kartu-keluarga/{kartuKeluarga}', [KartuKeluargaController::class, 'destroy'])->name('kartu-keluarga.destroy');
    });

    // Mutasi Warga — masuk / keluar / kelahiran / kematian
    Route::middleware('permission:view_mutasi')->group(function () {
        Route::get('mutasi-warga', [MutasiWargaController::class, 'index'])->name('mutasi-warga.index');
        Route::get('mutasi-warga/{mutasiWarga}', [MutasiWargaController::class, 'show'])->name('mutasi-warga.show');
    });
    Route::middleware('permission:manage_mutasi')->group(function () {
        Route::get('mutasi-warga-create', [MutasiWargaController::class, 'create'])->name('mutasi-warga.create');
        Route::post('mutasi-warga', [MutasiWargaController::class, 'store'])->name('mutasi-warga.store');
        Route::get('mutasi-warga/{mutasiWarga}/edit', [MutasiWargaController::class, 'edit'])->name('mutasi-warga.edit');
        Route::put('mutasi-warga/{mutasiWarga}', [MutasiWargaController::class, 'update'])->name('mutasi-warga.update');
        Route::delete('mutasi-warga/{mutasiWarga}', [MutasiWargaController::class, 'destroy'])->name('mutasi-warga.destroy');
    });

    // Keuangan — Pemasukan / Pengeluaran / Laporan
    Route::middleware('permission:view_keuangan')->group(function () {
        Route::get('keuangan/pemasukan', [KeuanganController::class, 'pemasukan'])->name('keuangan.pemasukan.index');
        Route::get('keuangan/pengeluaran', [KeuanganController::class, 'pengeluaran'])->name('keuangan.pengeluaran.index');
    });
    Route::middleware('permission:view_laporan_keuangan')->group(function () {
        Route::get('keuangan/laporan', [KeuanganController::class, 'laporan'])->name('keuangan.laporan');
    });
    Route::middleware('permission:manage_keuangan')->group(function () {
        Route::get('keuangan/pemasukan/create', [KeuanganController::class, 'createPemasukan'])->name('keuangan.pemasukan.create');
        Route::get('keuangan/pengeluaran/create', [KeuanganController::class, 'createPengeluaran'])->name('keuangan.pengeluaran.create');
        Route::post('keuangan', [KeuanganController::class, 'store'])->name('keuangan.store');
        Route::get('keuangan/{keuangan}/edit', [KeuanganController::class, 'edit'])->name('keuangan.edit');
        Route::put('keuangan/{keuangan}', [KeuanganController::class, 'update'])->name('keuangan.update');
        Route::delete('keuangan/{keuangan}', [KeuanganController::class, 'destroy'])->name('keuangan.destroy');
    });

    // Inventaris — data barang
    Route::middleware('permission:view_inventaris')->group(function () {
        Route::get('inventaris', [InventarisController::class, 'index'])->name('inventaris.index');
        Route::get('inventaris/{inventari}', [InventarisController::class, 'show'])->name('inventaris.show');
    });
    Route::middleware('permission:view_laporan_inventaris')->group(function () {
        Route::get('laporan-inventaris', [InventarisController::class, 'laporan'])->name('inventaris.laporan');
    });
    Route::middleware('permission:manage_inventaris')->group(function () {
        Route::get('inventaris-create', [InventarisController::class, 'create'])->name('inventaris.create');
        Route::post('inventaris', [InventarisController::class, 'store'])->name('inventaris.store');
        Route::get('inventaris/{inventari}/edit', [InventarisController::class, 'edit'])->name('inventaris.edit');
        Route::put('inventaris/{inventari}', [InventarisController::class, 'update'])->name('inventaris.update');
        Route::delete('inventaris/{inventari}', [InventarisController::class, 'destroy'])->name('inventaris.destroy');
    });

    // Peminjaman Inventaris — peminjaman & pengembalian
    Route::middleware('permission:view_peminjaman')->group(function () {
        Route::get('peminjaman-inventaris', [PeminjamanInventarisController::class, 'index'])->name('peminjaman-inventaris.index');
        Route::get('peminjaman-inventaris/{peminjamanInventari}', [PeminjamanInventarisController::class, 'show'])->name('peminjaman-inventaris.show');
    });
    Route::middleware('permission:manage_peminjaman')->group(function () {
        Route::get('peminjaman-inventaris-create', [PeminjamanInventarisController::class, 'create'])->name('peminjaman-inventaris.create');
        Route::post('peminjaman-inventaris', [PeminjamanInventarisController::class, 'store'])->name('peminjaman-inventaris.store');
        Route::get('peminjaman-inventaris/{peminjamanInventari}/edit', [PeminjamanInventarisController::class, 'edit'])->name('peminjaman-inventaris.edit');
        Route::put('peminjaman-inventaris/{peminjamanInventari}', [PeminjamanInventarisController::class, 'update'])->name('peminjaman-inventaris.update');
        Route::delete('peminjaman-inventaris/{peminjamanInventari}', [PeminjamanInventarisController::class, 'destroy'])->name('peminjaman-inventaris.destroy');
        Route::patch('peminjaman-inventaris/{peminjamanInventari}/kembalikan', [PeminjamanInventarisController::class, 'kembalikan'])->name('peminjaman-inventaris.kembalikan');
    });

    // Audit Log routes
    Route::middleware('permission:view_activity_logs')->group(function () {
        Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
        Route::get('activity-logs/{activityLog}', [ActivityLogController::class, 'show'])->name('activity-logs.show');
        Route::get('activity-logs/subject/{type}/{id}', [ActivityLogController::class, 'forSubject'])->name('activity-logs.subject');
    });

    // Role Management routes
    Route::middleware('permission:manage_roles')->group(function () {
        Route::resource('roles', RoleController::class);
    });

    // Permission Management routes
    Route::middleware('permission:manage_permissions')->group(function () {
        Route::resource('permissions', PermissionController::class);
    });

    // Organization Type Management routes
    Route::middleware('permission:manage_organization_types')->group(function () {
        Route::resource('organization-types', OrganizationTypeController::class);
    });

    // Organization Unit Management routes
    Route::middleware('permission:manage_organization_units')->group(function () {
        Route::resource('organization-units', OrganizationUnitController::class);
        
        // Member management routes
        Route::post('organization-units/{organization_unit}/members', [OrganizationUnitController::class, 'addMember'])
            ->name('organization-units.add-member');
        Route::delete('organization-units/{organization_unit}/members/{user}', [OrganizationUnitController::class, 'removeMember'])
            ->name('organization-units.remove-member');
        Route::patch('organization-units/{organization_unit}/head', [OrganizationUnitController::class, 'updateHead'])
            ->name('organization-units.update-head');
    });

});
