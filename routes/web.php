<?php

use App\Http\Controllers\CategoryController;
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

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/index',[CategoryController::class,'index'])->name('index');
Route::get('/create',[CategoryController::class,'create'])->name('create');
Route::post('/store',[CategoryController::class,'store'])->name('store');
Route::get('/edit/{id}',[CategoryController::class,'edit'])->name('edit');


