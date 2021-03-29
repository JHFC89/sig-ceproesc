<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\DisciplineController;
use App\Http\Controllers\CourseClassController;
use App\Http\Controllers\LessonRequestController;
use App\Http\Controllers\LessonRegisterController;
use App\Http\Controllers\EvaluationGradeController;
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

Route::middleware(['auth'])->group(function () {
    Route::get('/lessons/today', ForTodayLessonListController::class)->name('lessons.today');
    Route::get('/lessons/week', ForWeekLessonListController::class)->name('lessons.week');
    Route::get('/lessons/{lesson}', [LessonController::class, 'show'])->name('lessons.show');

    Route::get('/lessons/{lesson}/registers/create', [LessonRegisterController::class, 'create'])
        ->name('lessons.registers.create');

    Route::get('/lessons/{lesson}/requests/create', [LessonRequestController::class, 'create'])->name('lessons.requests.create');
    Route::post('/lessons/{lesson}/requests', [LessonRequestController::class, 'store'])->name('lessons.requests.store');
    Route::get('/requests/{request}', [LessonRequestController::class, 'show'])->name('requests.show');
    Route::patch('/requests/{request}', [LessonRequestController::class, 'update'])->name('requests.update');
   
    Route::post('/evaluations/{evaluation}/grades', [EvaluationGradeController::class, 'store'])->name('evaluations.grades.store');
    Route::get('evaluations/{evaluation}', [EvaluationController::class, 'show'])->name('evaluations.show');
    Route::post('/lessons/{lesson}/evaluations', [EvaluationController::class, 'store'])->name('lessons.evaluations.store');
    Route::get('/lessons/{lesson}/evaluations/create', [EvaluationController::class, 'create'])->name('lessons.evaluations.create');

    Route::get('disciplines', [DisciplineController::class, 'index'])
        ->name('disciplines.index');
    Route::get('disciplines/create', [DisciplineController::class, 'create'])
        ->name('disciplines.create');
    Route::post('disciplines', [DisciplineController::class, 'store'])
        ->name('disciplines.store');
    Route::get('disciplines/{discipline}/edit', [
        DisciplineController::class, 
        'edit'
    ])->name('disciplines.edit');
    Route::patch('disciplines/{discipline}', [
        DisciplineController::class, 'update'
    ])->name('disciplines.update');
    Route::get('disciplines/{discipline}', [DisciplineController::class, 'show'])
        ->name('disciplines.show');

    Route::get('courses', [CourseController::class, 'index'])
        ->name('courses.index');
    Route::get('courses/create', [CourseController::class, 'create'])
        ->name('courses.create');
    Route::get('courses/{course}', [CourseController::class, 'show'])
        ->name('courses.show');
    Route::post('courses', [CourseController::class, 'store'])
        ->name('courses.store');

    Route::get('classes', [CourseClassController::class, 'index'])
        ->name('classes.index');
    Route::get('classes/create', [CourseClassController::class, 'create'])
        ->name('classes.create');
    Route::get('classes/{courseClass}', [CourseClassController::class, 'show'])
        ->name('classes.show');
    Route::get('classes/{courseClass}/lessons', [LessonController::class, 'index'])
        ->name('classes.lessons.index');
    Route::get('classes/{courseClass}/lessons/create', [LessonController::class, 'create'])
        ->name('classes.lessons.create');

    Route::get('holidays', [HolidayController::class, 'index'])
        ->name('holidays.index');
    Route::get('holidays/create', [HolidayController::class, 'create'])
        ->name('holidays.create');
    Route::post('holidays', [HolidayController::class, 'store'])
        ->name('holidays.store');

});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';
