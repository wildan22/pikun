<?php

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

/* AUTHENTICATION ROUTES */
Route::post('register','Api\RegisterController@action');
Route::post('login','Api\LoginController@action');
Route::get('me','Api\UserController@me')->middleware('auth:api');
Route::post('updateinfo','Api\UserController@updateInfo')->middleware('auth:api');
Route::delete('closeaccount','Api\UserController@closeAccount')->middleware('auth:api');

//Perusahaan Routes
Route::post('tambahperusahaan','Api\UserController@addPerusahaan')->middleware('auth:api');
Route::post('ubahdataperusahaan','Api\UserController@editDataPerusahaan')->middleware('auth:api');
Route::post('hapusperusahaan','Api\UserController@hapusPerusahaan')->middleware('auth:api');
Route::get('daftarperusahaan','Api\UserController@getDaftarPerusahaan')->middleware('auth:api');

//JenisTransaksiRoute
Route::get('jenistransaksi','JenisTransaksiController@getAllJenisTransaksi')->middleware('auth:api');

//Rekening Routes
Route::get('rekening','RekeningController@get')->middleware('auth:api');

//Perkiraan Routes
Route::get('perkiraan','Api\PerkiraanController@get')->middleware('auth:api');
Route::post('perkiraan','Api\PerkiraanController@addPerkiraan')->middleware('auth:api');
Route::delete('perkiraan','Api\PerkiraanController@hapusPerkiraan')->middleware('auth:api');

//Mapping Routes
Route::get('mapping','Api\MappingController@get')->middleware('auth:api');


/**JURNAL ROUTES */
Route::post('jurnal/create','Api\JurnalController@addJurnal')->middleware('auth:api');

/*
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
*/