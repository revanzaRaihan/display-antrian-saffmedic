<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Display\DisplaySettingsController;
use App\Http\Controllers\Display\BrandController;

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::get('/ajax/brand', [BrandController::class, 'fetch'])->name('ajax.brand');
Route::post('/display-settings', [DisplaySettingsController::class, 'save']);
Route::get('/display-settings', [DisplaySettingsController::class, 'get']);