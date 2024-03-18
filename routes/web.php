<?php

use App\Http\Controllers\PivotController;
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


Route::get('/', [PivotController::class, 'index'])->name('pivot.index');
Route::post('/pivot/store', [PivotController::class, 'store'])->name('pivot.store');
Route::get('/export', [PivotController::class, 'export'])->name('pivot.export');
Route::get('/pivot/edit/{id}', [PivotController::class, 'edit'])->name('pivot.edit');
Route::post('/pivot/update/{id}', [PivotController::class, 'update'])->name('pivot.update');
Route::get('/pivot/delete/{id}', [PivotController::class, 'delete'])->name('pivot.delete');

