<?php

use App\Http\Controllers\RegisterController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AdminController,
    LessonController,
    CourseController,
    NoviceController,
    CompanyController,
    HolidayController,
    EmployerController,
    InstructorController,
    EvaluationController,
    DisciplineController,
    InvitationController,
    CoordinatorController,
    CourseClassController,
    SubscriptionController,
    LessonRequestController,
    ActivatedUserController,
    LessonRegisterController,
    EvaluationGradeController,
    AdminCoordinatorController,
    ForWeekLessonListController,
    ForTodayLessonListController,
};

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

Route::get('invitations/{code}', [InvitationController::class, 'show'])
    ->name('invitations.show');

Route::post('register', [RegisterController::class, 'store'])
    ->name('register.store');

Route::middleware(['auth', 'active'])->group(function () {
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

    Route::get('companies', [CompanyController::class, 'index'])
        ->name('companies.index');
    Route::get('/companies/create', [CompanyController::class, 'create'])
        ->name('companies.create');
    Route::post('/companies', [CompanyController::class, 'store'])
        ->name('companies.store');
    Route::get('companies/{company}', [CompanyController::class, 'show'])
        ->name('companies.show');

    Route::get('companies/{company}/employers', [
        EmployerController::class, 'index'
    ])->name('companies.employers.index');
    Route::get('companies/{company}/employers/create', [
        EmployerController::class,
        'create'
    ])->name('companies.employers.create');
    Route::post('companies/{company}/employers', [
        EmployerController::class,
        'store'
    ])->name('companies.employers.store');
    Route::get('employers/{registration}', [EmployerController::class, 'show'])
        ->name('employers.show');

    Route::get('companies/{company}/novices', [NoviceController::class, 'index'])
        ->name('companies.novices.index');
    Route::get('companies/{company}/novices/create', [
        NoviceController::class,
        'create'
    ])->name('companies.novices.create');
    Route::post('companies/{company}/novices', [
        NoviceController::class,
        'store'
    ])->name('companies.novices.store');
    Route::get('novices/{registration}', [NoviceController::class, 'show'])
        ->name('novices.show');

    Route::get('instructors', [InstructorController::Class, 'index'])
        ->name('instructors.index');
    Route::get('instructors/create', [InstructorController::Class, 'create'])
        ->name('instructors.create');
    Route::post('instructors/store', [InstructorController::class, 'store'])
        ->name('instructors.store');
    Route::get('instructors/{registration}', [InstructorController::class, 'show'])
        ->name('instructors.show');

    Route::get('classes/{courseClass}/subscriptions/create', [
        SubscriptionController::class, 'create'
    ])->name('classes.subscriptions.create');
    Route::post('subscriptions', [SubscriptionController::class, 'store'])
        ->name('subscriptions.store');

    Route::get('coordinators', [CoordinatorController::class, 'index'])
        ->name('coordinators.index');
    Route::get('coordinators/create', [CoordinatorController::class, 'create'])
        ->name('coordinators.create');
    Route::post('coordinators', [CoordinatorController::class, 'store'])
        ->name('coordinators.store');
    Route::get('coordinators/{registration}', [
        CoordinatorController::class, 'show'
    ])->name('coordinators.show');

    Route::get('admins', [AdminController::class, 'index'])
        ->name('admins.index');
    Route::get('admins/create', [AdminController::class, 'create'])
        ->name('admins.create');
    Route::post('admins', [AdminController::class, 'store'])
        ->name('admins.store');
    Route::get('admins/{registration}', [
        AdminController::class, 'show'
    ])->name('admins.show');

    Route::post('admin-coordinators', [
        AdminCoordinatorController::class,
        'store'
    ])->name('admin-coordinators.store');
    Route::delete('admin-coordinators/{registration}', [
        AdminCoordinatorController::class,
        'delete'
    ])->name('admin-coordinators.destroy');

    Route::post('activated-users', [ActivatedUserController::class, 'store'])
        ->name('activated-users.store');
    Route::delete('activated-users/{user}', [
        ActivatedUserController::class,
        'destroy'
    ])->name('activated-users.destroy');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';
