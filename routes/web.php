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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

//Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/home', function () {
    return redirect()->route('cards.index');
})->name('home');
Route::view('cards', 'card');

Route::group(['middleware' => 'auth'], function () {
    Route::get('/cards', [\App\Http\Controllers\CardController::class, 'index'])->name('cards.index');
    Route::post('/cards', [\App\Http\Controllers\CardController::class, 'store'])->name('cards.store');
    Route::put('/cards/sync', [\App\Http\Controllers\CardController::class, 'sync'])->name('cards.sync');
    Route::put('/cards/{card}', [\App\Http\Controllers\CardController::class, 'update'])->name('cards.update');
});


Route::group(['middleware' => 'auth'], function () {
    Route::post('/columns', [\App\Http\Controllers\ColumnController::class, 'store'])->name('columns.store');
    Route::delete('/columns', [\App\Http\Controllers\ColumnController::class, 'delete'])->name('columns.update');
});

Route::get('database-dump', [\App\Http\Controllers\ExportDbController::class, 'export']);
