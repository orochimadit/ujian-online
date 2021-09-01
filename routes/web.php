<?php

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

Route::get('/', function () {
    return view('welcome');
});

Route::post('/tambahAdmin','adminController@tambahAdmin');
Route::post('/loginAdmin','adminController@loginAdmin');
Route::post('/hapusAdmin','adminController@hapusAdmin');
Route::post('/listAdmin','adminController@listAdmin');

Route::post('/tambahKonten','kontenController@tambahKonten');
Route::post('/ubahKonten','kontenController@ubahKonten');
Route::post('/hapusKonten','kontenController@hapusKonten');
Route::post('/listKonten','kontenController@listKonten');

Route::post('/registrasi','pesertaController@registrasi');