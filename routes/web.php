<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MonitoringController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\ProfileController; // TAMBAHKAN
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect('/login');
});

Auth::routes();

// =========================
// Dashboard (semua role)
// =========================
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard')
    ->middleware('auth');

Route::get('/about', function() {
    return view('about');
})->name('about')->middleware('auth');

// =========================
// Admin Only
// =========================
Route::middleware(['auth', 'role:admin'])->group(function () {

    // CRUD sekolah (admin saja)
    Route::resource('schools', SchoolController::class)->except(['show']);

    // CRUD kategori
    Route::resource('categories', CategoryController::class);

    // ===== MANAJEMEN USER (CRUD) =====
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    
    // ===== RESET PASSWORD (TAMBAHKAN) =====
    Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
    
    // ===== IMPORT USER =====
    Route::get('/users/import', [UserController::class, 'showImportForm'])->name('users.import.form');
    Route::post('/users/import', [UserController::class, 'import'])->name('users.import');
    Route::get('/users/import/template', [UserController::class, 'downloadTemplate'])->name('users.import.template');

    // Update koordinat sekolah dari peta (khusus admin)
    Route::post('/schools/{school}/coordinates', [SchoolController::class, 'updateCoordinates'])
        ->name('schools.coordinates.update');

    // Import data sekolah
    Route::get('/schools/import', [ImportController::class, 'showImportForm'])
        ->name('schools.import.form');
    Route::post('/schools/import', [ImportController::class, 'import'])
        ->name('schools.import');
    Route::get('/schools/import/template', [ImportController::class, 'downloadTemplate'])
        ->name('schools.import.template');
});

// =========================
// Operator
// =========================
Route::middleware(['auth', 'role:operator'])->group(function () {

    // Sekolah milik operator
    Route::get('/my-school', [SchoolController::class, 'mySchool'])->name('my-school');

    // Program sekolah operator
    Route::get('/my-programs', [ProgramController::class, 'myPrograms'])->name('my-programs');

    // CRUD program (create, edit, update, delete)
    Route::get('/programs/create', [ProgramController::class, 'create'])->name('programs.create');
    Route::post('/programs', [ProgramController::class, 'store'])->name('programs.store');
    Route::get('/programs/{program}/edit', [ProgramController::class, 'edit'])->name('programs.edit');
    Route::put('/programs/{program}', [ProgramController::class, 'update'])->name('programs.update');
    Route::delete('/programs/{program}', [ProgramController::class, 'destroy'])->name('programs.destroy');
    
    // Export untuk operator
    Route::get('/operator/programs/export-excel', [ProgramController::class, 'exportOperatorProgramsExcel'])
        ->name('operator.export.excel');
    Route::get('/operator/programs/export-pdf', [ProgramController::class, 'exportOperatorProgramsPdf'])
        ->name('operator.export.pdf');
    Route::get('/operator/school/export-excel', [ProgramController::class, 'exportOperatorSchoolExcel'])
        ->name('operator.export.school.excel');
    Route::get('/operator/school/export-pdf', [ProgramController::class, 'exportOperatorSchoolPdf'])
        ->name('operator.export.school.pdf');
});

// =========================
// Admin & Pimpinan
// =========================
Route::middleware(['auth', 'role:admin,pimpinan'])->group(function () {

    // Lihat semua program
    Route::get('/programs', [ProgramController::class, 'index'])->name('programs.index');

    // Report
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export-excel', [ReportController::class, 'exportExcel'])
        ->name('reports.export.excel');
    Route::get('/reports/export-pdf', [ReportController::class, 'exportPdf'])
        ->name('reports.export.pdf');

    // Monitoring
    Route::get('/monitoring', [MonitoringController::class, 'index'])->name('monitoring.index');

    // Route untuk peta Leaflet
    Route::get('/peta-slb', [SchoolController::class, 'leafletMap'])
        ->name('schools.peta');
});

// =========================
// Semua user login bisa akses (dikontrol policy)
// =========================
Route::middleware(['auth'])->group(function () {
    Route::get('/schools/{school}', [SchoolController::class, 'show'])->name('schools.show');
    
    // Detail program untuk semua role (admin, pimpinan, operator)
    Route::get('/programs/{program}', [ProgramController::class, 'show'])->name('programs.show');
    
    // ===== ROUTE UNTUK KATEGORI LOKAL (OPERATOR) =====
    // Route ini harus diakses oleh operator yang sudah login
    Route::post('/categories/store-local', [CategoryController::class, 'storeLocal'])
        ->name('categories.store-local');
        
    // Route untuk mendapatkan daftar kategori (API)
    Route::get('/categories/get-for-operator', [CategoryController::class, 'getCategoriesForOperator'])
        ->name('categories.get-for-operator');

    Route::post('/profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.change-password');

});