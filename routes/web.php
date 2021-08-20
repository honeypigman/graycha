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
use App\Http\Middleware\AuthCheck;
use App\Func\Board;

// Index
Route::get('/', 'Controller@main');

// Api Manager
Route::get('/manager', 'ApiController@main');
Route::get('/api/{api_code}', 'ApiController@setForm');
Route::post('/send', 'ApiController@send');

// Map
Route::get('/map/{api}', 'MapController@index');

// Kids
Route::get('/kids/dtd', 'KidsController@dtd');
Route::post('/kids/dtd/save', 'KidsController@dtdSave');
Route::post('/kids/dtd/sample', 'KidsController@dtdSampleList');

// Blog
Route::get('/blper', 'BlperController@index');
Route::post('/blper/find', 'BlperController@find');
Route::get('/blper/issue', 'BlperController@issue');
Route::get('/blper/keyword', 'BlperController@keyword');
// Route::post('/blper/crawling/{site}', 'BlperController@crawling');