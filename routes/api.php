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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// K-AirSpec
//  - /req/kairspec/[operation]/[bas64_string_data]
//Route::get('/req/{api}/{cmd}/{bas64_string_data}', 'ApiController@req')->name('api.req');
Route::get('/list/{type}', 'ApiController@index')->name('api.index');
Route::get('/find/station/{dmX}/{dmY}', 'ApiController@findStation');
Route::get('/find/station/timeflow/{date}/{city}/{station}', 'ApiController@findStationTimeflow');
