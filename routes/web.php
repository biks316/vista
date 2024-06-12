<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CompilerController;
use App\Http\Controllers\LaravelProjectCreator;

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


Route::get('/test', function () {
    return view('universal');
});

Route::get('/theme1', function () {
    return view('theme1');
});

Route::get('/c', function () {
    return view('c_code');
});


Route::get('/php', function () {
    return view('compiler');
});

Route::post('/unknown_execute', [CompilerController::class, 'compileAndExecute'])->name('unknown_execute');

Route::post('/php_execute', [CompilerController::class, 'php_execute'])->name('php_execute');

Route::post('/c_execute', [CompilerController::class, 'compileAndExecute'])->name('c_execute');

Route::get('/laravel', [LaravelProjectCreator::class, 'createProject'])->name('laravel');
