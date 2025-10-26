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

// Home
Route::get('/', function () {
    return view('home');
});


// Display Antrian Umum
Route::get('/display', 'Display\QueueController@index');
Route::get('/display/payment', 'Display\QueueController@payment');
Route::get('/display/pharmacy', 'Display\QueueController@pharmacy');
Route::get('/', 'Display\QueuePolyController@index');

// Display Antrian Poli
Route::get('/display/poly/{id}', 'Display\QueuePolyController@show')->name('display.poly');

// AJAX
Route::get('/ajax/queue', 'Display\QueueController@ajaxQueue')->name('ajax.queue');
Route::get('/ajax/queue/poly', 'Display\QueuePolyController@ajaxQueue')->name('ajax.poli-queue');