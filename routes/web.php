<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InstitutionController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubDivisionController;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\ReportJourneyController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\PelaporanController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Auth::routes();

Route::middleware(['auth'])->group(function () {
        // dashboard routes
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');

        // users routes
        Route::resource('users', UserController::class);
        Route::get('datatables/users', [UserController::class, 'datatables'])->name('datatables.users');

        // permission routes
        Route::get('datatables/permissions', [PermissionController::class, 'datatables'])->name('datatables.permissions');
        Route::resource('permissions', PermissionController::class);
        // profile routes
        Route::get('profile', [ProfileController::class, 'show'])->name('profile.show');
        Route::get('profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
        Route::delete('profile/photo', [ProfileController::class, 'deletePhoto'])->name('profile.photo.delete');
        Route::get('profile/photo/{path}', [ProfileController::class, 'photo'])
            ->where('path', '.*')
            ->name('profile.photo.show');

        //intitution routes
        Route::get('institutions/datatables', [InstitutionController::class, 'datatables'])->name('institutions.datatables');
        Route::resource('institutions', InstitutionController::class);
        
        //division and sub duvision routes
        Route::resource('unit', SubDivisionController::class)->names('subdivisions');
        Route::get('subdivisions/datatables', [SubDivisionController::class, 'datatables'])
            ->name('subdivisions.datatables');
        Route::resource('subdivisions', SubDivisionController::class)->except('show');

        // role routes
        Route::resource('roles', RoleController::class);
        Route::get('datatables/roles', [RoleController::class, 'datatables'])->name('datatables.roles');

        // divisi routes
        Route::resource('sub-bagian', DivisionController::class)->names('divisions');

        Route::get('datatables/sub-bagian', [DivisionController::class, 'datatables'])
            ->name('datatables.division');

        //pelaporan route
        Route::resource('pelaporan', PelaporanController::class);
        Route::get('datatables/pelaporan', [PelaporanController::class, 'datatables'])->name('datatables.pelaporan');
        Route::get('get-cities/{provinceId}', [PelaporanController::class, 'getCitiesByProvince']);
        Route::get('get-districts/{cityId}', [PelaporanController::class, 'getDistrictsByCity']);
        
        //pemanggilan data dashboard
        Route::get('/dashboard/status-summary', [DashboardController::class, 'statusSummary'])->name('dashboard.statusSummary');
        Route::get('/dashboard/top-categories', [DashboardController::class, 'topCategories'])->name('dashboard.topCategories');
        Route::get('/dashboard/trend-reports', [DashboardController::class, 'getTrendReports'])->name('dashboard.trendReports');
        Route::get('/dashboard/total-laporan', [DashboardController::class, 'getTotalReports']);
        Route::get('/dashboard/top-category-active', [DashboardController::class, 'getTopCategoryAktif']);
        Route::get('/dashboard/laporan-aktif', [DashboardController::class, 'getLaporanAktif']);
        Route::get('/dashboard/completion-rate', [DashboardController::class, 'getPersentasiLaporanSelesai']);

        Route::post('/reports/{report}/journeys', [ReportJourneyController::class, 'store'])
            ->name('reports.journeys.store');

    }
);
