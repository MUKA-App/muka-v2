<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\RegistrationController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Profiles\ProfileAvatarController;
use App\Http\Controllers\Profiles\ProfileController;
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
Route::get('/login', [ReactPageController::class, 'index'])->name('login');

Route::post('/login', [LoginController::class, 'login']);

Route::get('/verify/{token}', [ReactPageController::class, 'index'])->middleware('guest');

Route::prefix('/register')->middleware('guest')->group(function () {
    Route::get('/', [ReactPageController::class, 'index'])->name('register');
    Route::post('/', [RegistrationController::class, 'register']);

    Route::prefix('/verify')->group(function () {

        Route::post('/', [VerificationController::class, 'verify']);
        Route::post('/resend', [VerificationController::class, 'resendEmail']);
    });
});

/*
|--------------------------------------------------------------------------
| Password Routes
|--------------------------------------------------------------------------
*/
Route::get('/password/{any}', [ReactPageController::class, 'index'])
    ->middleware('guest')
    ->where('any', '.*');

Route::prefix('/password')->middleware('guest')->group(function () {
    Route::post('/forgot', [PasswordController::class, 'sendEmail']);
    Route::post('/reset', [PasswordController::class, 'resetPassword']);
});


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
*/
Route::prefix('/api')->middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Profile Routes
    |--------------------------------------------------------------------------
    |
    */

    Route::prefix('/profile')->group(function () {
        Route::get('/', [ProfileController::class, 'mine']);
        Route::post('/', [ProfileController::class, 'create']);
        Route::patch('/', [ProfileController::class, 'edit']);
        Route::put('/avatar', [ProfileAvatarController::class, 'putAvatar']);
        Route::get('/{slug}', [ProfileController::class, 'show']);
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