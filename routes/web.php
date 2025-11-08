<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;

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
        Route::resource('institutions', \App\Http\Controllers\InstitutionController::class);
        Route::get('institutions/datatables', [\App\Http\Controllers\InstitutionController::class, 'datatables'])->name('institutions.datatables');
    }
);
