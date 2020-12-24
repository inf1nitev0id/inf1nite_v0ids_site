<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\MahoukaServerRatingController;

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
	return view('auth.reg', ['from' => 'main']);
})->name('reg')->middleware('guest');
Route::post('/register', [LoginController::class, 'register'])
	->name('register')->middleware('guest');
Route::get('/login', function() {
	return view('auth.login', ['from' => 'main']);
})->name('login')->middleware('guest');
Route::post('/auth', [LoginController::class, 'authenticate'])
	->name('auth')->middleware('guest');
Route::get('/logout', function() {
	Auth::logout();
	return redirect()->route('home');
})->name('logout')->middleware('auth');

Route::get('/forum/{id?}', [ForumController::class, 'forum'])->where('id', '[0-9]+')->name('forum');
Route::prefix('forum')->name('forum.')->group(function() {
	Route::post('/add-comment', [ForumController::class, 'addComment'])->name('add-comment');
	Route::post('/delete-comment', [ForumController::class, 'deleteComment'])->name('delete-comment');

	Route::get('/{id}/add-post', [ForumController::class, 'addPostForm'])->name('add-post-form');
	Route::post('/add-post', [ForumController::class, 'addPost'])->name('add-post');
	Route::post('/delete-post', [ForumController::class, 'deletePost'])->name('delete-post');
});

Route::prefix('mahouka')->name('mahouka.')->group(function() {
	Route::get('/', function() {
		return view('mahouka.home');
	})->name('home');

	Route::prefix('top')->name('top.')->group(function() {
		Route::get('/', [MahoukaServerRatingController::class, 'chart'])->name('chart');
		Route::get('/table', [MahoukaServerRatingController::class, 'table'])->name('table');

		Route::middleware('role:admin')->group(function() {
			Route::get('/load', [MahoukaServerRatingController::class, 'loadForm'])->name('load');
			Route::post('/load', [MahoukaServerRatingController::class, 'preload'])->name('preload');
			Route::post('/load_hashes', [MahoukaServerRatingController::class, 'load_hashes'])->name('load-hashes');
			Route::post('/write_rate', [MahoukaServerRatingController::class, 'write_rate'])->name('write-rate');
		});
	});

	Route::get('/reg', function() {
		return view('auth.reg', ['from' => 'mahouka']);
	})->name('reg')->middleware('guest');
	Route::get('/login', function() {
		return view('auth.login', ['from' => 'mahouka']);
	})->name('login')->middleware('guest');
	Route::get('/logout', function() {
		Auth::logout();
		return redirect()->route('mahouka.home');
	})->name('logout')->middleware('auth');
});
