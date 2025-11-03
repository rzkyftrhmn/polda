<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
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
        Route::resource('permissions', PermissionController::class);
    }
);
