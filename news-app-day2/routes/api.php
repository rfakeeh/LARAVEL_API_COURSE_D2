<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\ImageController;

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

//Route::resource('category', CategoryController::class);

Route::get('category', [CategoryController::class, 'index'])->name('category.index');
Route::get('category/{id}', [CategoryController::class, 'show'])->name('category.show');
Route::post('category', [CategoryController::class, 'store'])->name('category.store');
Route::put('category/{id}', [CategoryController::class, 'update'])->name('category.update');
Route::delete('category/{id}', [CategoryController::class, 'destroy'])->name('category.destroy');

Route::get('open_document', [CategoryController::class, 'open_document'])->name('category.open_document');

///////

Route::get('news', [NewsController::class, 'index'])->name('news.index');
Route::get('news/public', [NewsController::class, 'public_index'])->name('news.public_index');
Route::post('news', [NewsController::class, 'store'])->name('news.store');
Route::get('news/{id}', [NewsController::class, 'show'])->name('news.show');
Route::get('news/public/{id}', [NewsController::class, 'public_show'])->name('news.public_show');
Route::put('news/{id}', [NewsController::class, 'update'])->name('news.update');
Route::delete('news/{id}', [NewsController::class, 'destroy'])->name('news.destroy');

Route::post('album', [ImageController::class, 'store'])->name('album.store');
Route::delete('album/{id}', [ImageController::class, 'destroy'])->name('album.destroy');

/*
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
*/
