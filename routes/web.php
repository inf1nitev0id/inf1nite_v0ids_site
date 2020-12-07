<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;

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
  return view('home');
})->name('home');
Route::get('/about', function () {
  return view('about');
})->name('about');

Route::get('/reg', function() {
  return view('reg');
})->name('reg')->middleware('guest');
Route::post('/register', [LoginController::class, 'register'])
  ->name('register')->middleware('guest');
Route::get('/login', function() {
  return view('login');
})->name('login')->middleware('guest');
Route::post('/auth', [LoginController::class, 'authenticate'])
  ->name('auth')->middleware('guest');
Route::get('/logout', function() {
  Auth::logout();
  return back();
})->name('logout')->middleware('auth');
