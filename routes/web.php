<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\LessonRequestController;
use App\Http\Controllers\LessonRegisterController;
use App\Http\Controllers\ForWeekLessonListController;
use App\Http\Controllers\ForTodayLessonListController;

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

Route::view('/mockups/lessons/create', 'mockups.lessons.create');

Route::middleware(['auth'])->group(function () {
    Route::get('/lessons/today', ForTodayLessonListController::class)->name('lessons.today');
    Route::get('/lessons/week', ForWeekLessonListController::class)->name('lessons.week');
    Route::get('/lessons/register/create/{lesson}', [LessonRegisterController::class, 'create'])
        ->name('lessons.register.create');
    Route::get('/lessons/{lesson}/requests/create', [LessonRequestController::class, 'create'])->name('lessons.requests.create');
    Route::post('/lessons/{lesson}/requests', [LessonRequestController::class, 'store'])->name('lessons.requests.store');
    Route::get('/lessons/{lesson}', [LessonController::class, 'show'])->name('lessons.show');
    Route::get('/requests/{request}', [LessonRequestController::class, 'show'])->name('requests.show');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';
