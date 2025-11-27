<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\InstructionsController;
use App\Http\Controllers\Api\EventController as ApiEventController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::group(['middleware' => 'jwt'], function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('profile', [ProfileController::class, 'show']);
        Route::get('petunjuk-dan-arahan', [InstructionsController::class, 'index']);
        Route::post('petunjuk-dan-arahan', [InstructionsController::class, 'store']);
        Route::get('petunjuk-dan-arahan/{report_uuid}', [InstructionsController::class, 'listByReport']);
        Route::get('petunjuk-dan-arahan/users/{report_uuid}', [InstructionsController::class, 'users']);

        Route::post('event', [ApiEventController::class, 'store']);
        Route::get('event', [ApiEventController::class, 'index']);
        Route::get('event/{event_uuid}', [ApiEventController::class, 'show']);
        Route::put('event/{event_uuid}', [ApiEventController::class, 'update']);
        Route::delete('event/{event_uuid}', [ApiEventController::class, 'destroy']);
        Route::get('event/users/{event_uuid}', [ApiEventController::class, 'users']);
    });
});
