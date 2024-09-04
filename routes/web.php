<?php

use App\Http\Controllers\WebProxyController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Proxy routes to other web services
Route::any('/web/{service}/{path}', [WebProxyController::class, 'proxy'])->where('path', '.*');


// Google
Route::get('auth/redirect/google');
Route::get('auth/callback/google');

// GitHub
Route::get('auth/redirect/github');
Route::get('auth/callback/github');

// LinkedIn
Route::get('auth/redirect/linkedin');
Route::get('auth/callback/linkedin');
