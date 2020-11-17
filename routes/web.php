<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\LessonRegisterController;

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

Route::get('/lessons/{lesson}', [LessonController::class, 'show'])->name('lessons.show');


Route::view('/mockups/lessons/create', 'mockups.lessons.create');

Route::middleware(['auth'])->group(function () {
    Route::get('/lessons/register/create/{lesson}', [LessonRegisterController::class, 'create'])
        ->name('lessons.register.create');
});

Route::get('login', function () {
    dump('login page');
})->name('login');
