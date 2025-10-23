<?php

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

// Display Antrian Umum
Route::get('/display', 'QueueDisplayController@index');
Route::get('/display/payment', 'QueueDisplayController@payment');
Route::get('/display/pharmacy', 'QueueDisplayController@pharmacy');

// Display Antrian Poli
Route::get('/display/poly/{id}', 'Display\QueuePolyController@show')->name('display.poly');

// AJAX
Route::get('/ajax/queue', 'QueueDisplayController@ajaxQueue')->name('ajax.queue');
Route::get('/ajax/queue/poly', 'display\QueuePolyController@ajaxQueue')->name('ajax.poli-queue');

