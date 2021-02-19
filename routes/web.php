<?php

use App\Http\Controllers\Auth\RegistrationController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\ReactPageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [ReactPageController::class, 'index']);


Route::prefix('/register')->middleware('guest')->group(function () {

    Route::post('/', [RegistrationController::class, 'register']);

    Route::prefix('/verify')->group(function () {

        Route::post('/', [VerificationController::class, 'verify']);
        Route::post('/resend', [VerificationController::class, 'resendEmail']);
    });
});

/*
|--------------------------------------------------------------------------
| Catch-all for React app
|--------------------------------------------------------------------------
*/
Route::get('/{any}', [ReactPageController::class, 'index'])
    ->middleware('auth')
    ->where('any', '.*');