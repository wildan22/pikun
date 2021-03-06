<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
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


/*
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return redirect('/home');
})->middleware(['auth', 'signed'])->name('verification.verify');
*/

Route::get('email/verify/{id}/{hash}', 'Api\VerificationController@verify')->name('verification.verify');
Route::get('email/resend', 'Api\VerificationController@resend')->name('verification.resend');
Route::post('password/email', 'Api\ForgotPasswordController@forgot');
Route::post('login/google','Api\LoginController@loginWithGoogleToken');
Route::post('register','Api\RegisterController@action');
Route::post('login','Api\LoginController@action');



/* USER ROUTES */
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
//Get All
Route::get('perkiraan','Api\PerkiraanController@getPerkiraanList')->middleware('auth:api');
//Get Limited
Route::get('perkiraan/{id}','Api\PerkiraanController@getPerkiraanBasedRekening')->middleware('auth:api');
Route::post('perkiraan','Api\PerkiraanController@addPerkiraan')->middleware('auth:api');
Route::delete('perkiraan','Api\PerkiraanController@hapusPerkiraan')->middleware('auth:api');


//Mapping Routes
Route::get('mapping/{id}','Api\MappingController@get')->middleware('auth:api');

//Double Query
Route::get('mapping/kredit/{id}','Api\MappingController@getRekeningKredit')->middleware('auth:api');
Route::get('mapping/debit/{id}','Api\MappingController@getRekeningDebit')->middleware('auth:api');






/**JURNAL ROUTES */
Route::post('jurnal/create','Api\JurnalController@addJurnal')->middleware('auth:api');
Route::post('jurnal/lihatjurnal','Api\JurnalController@showJurnalList')->middleware('auth:api');
Route::GET('jurnaldetail/{id}','Api\JurnalController@showSpecificJurnalDetail')->middleware('auth:api');

Route::POST('jurnal/hapus','Api\JurnalController@deleteJurnal')->middleware('auth:api');
Route::POST('jurnal/edit','Api\JurnalController@editJurnal')->middleware('auth:api');


/** PDF REPORTING */
Route::POST('jurnal/pdf','Api\JurnalDetailController@generateJurnalPDF')->middleware('auth:api');
Route::POST('neracasaldo/pdf','Api\JurnalDetailController@generateNeracaSaldoPDF')->middleware('auth:api');
Route::POST('bukubesar/pdf','Api\JurnalDetailController@generateBukuBesarPDF')->middleware('auth:api');
Route::POST('labarugi/pdf','Api\JurnalDetailController@generateLabaRugiPDF')->middleware('auth:api');
Route::POST('neraca/pdf','Api\JurnalDetailController@generateNeracaPDF')->middleware('auth:api');





/**REPORT ROUTES */
Route::POST('report/jurnal/view','Api\LaporanController@showJurnalReportAndroidJson')->middleware('auth:api');
Route::POST('report/bukubesar/view','Api\LaporanController@showBukuBesarJson')->middleware('auth:api');
Route::POST('report/neracasaldo/view','Api\LaporanController@showNeracaSaldo')->middleware('auth:api');
Route::POST('report/labarugi/view','Api\LaporanController@showLabaRugi')->middleware('auth:api');
Route::POST('report/neraca/view','Api\LaporanController@showNeraca')->middleware('auth:api');


/** FUNGSI LAIN */
Route::GET('jurnal/get_tahun_jurnal','Api\JurnalController@tampilkanDataTahun')->middleware('auth:api');


/** TAMBAH JURNAL MANUAL */
Route::POST('jurnal/manual/create','Api\JurnalController@insertManualJurnal')->middleware('auth:api');

