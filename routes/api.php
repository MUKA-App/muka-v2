<?php

use App\Http\Controllers\Profiles\ProfilesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


/*
|--------------------------------------------------------------------------
| API Profile Routes
|--------------------------------------------------------------------------
|
*/
Route::prefix('/profile')->middleware('auth')->group(function () {
    Route::get('/', [ProfilesController::class, 'mine']);
    Route::post('/', [ProfilesController::class, 'create']);
    Route::patch('/', [ProfilesController::class, 'edit']);
    Route::get('/{slug}', [ProfilesController::class, 'show']);
});