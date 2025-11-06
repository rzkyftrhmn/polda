<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PermissionController;

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
Route::middleware(['auth'])->group(
    function () {
        // dashboard routes
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');

        // users routes
        Route::resource('users', UserController::class);
        Route::get('datatables/users', [UserController::class, 'datatables'])->name('datatables.users');

        // permission routes
        Route::get('datatables/permissions', [PermissionController::class, 'datatables'])->name('datatables.permissions');
        Route::resource('permissions', PermissionController::class);

        // role routes
        Route::resource('roles', RoleController::class);
        Route::get('datatables/roles', [RoleController::class, 'datatables'])->name('datatables.roles');
    }
);
