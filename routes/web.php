<?php

use App\Http\Controllers\Auth\PasswordController as AuthPasswordController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TheLynxResultController;
use App\Http\Controllers\SubjectWiseMarksController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\TeachersController;
use App\Http\Controllers\FetchApiController;
use App\Http\Controllers\SectionsController;
use App\Http\Controllers\AssignSubjectController;
use App\Http\Controllers\ClassesController;
use App\Http\Controllers\ClassSectionController;
use App\Http\Controllers\ClassSubjectController;
use Illuminate\Support\Facades\Route;
use App\Models\StudentResult;
use App\Models\Session;
use App\Models\SubjectWiseMarks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

Route::get('/test-role', function () {
    $user = Auth::user();

    if (!$user) {
        return 'Please login first';
    }

    $user->assignRole('admin');

    return [
        'user' => $user->name,
        'roles' => $user->getRoleNames(),
        'permissions' => $user->getPermissionNames(),
        'is_admin' => $user->hasRole('admin'),
    ];
})->middleware('auth');

Route::middleware(['auth', 'role:Admin'])->group(function () {
    Route::get('/teachers', [TeachersController::class, 'index'])->name('teachers.index');
    Route::get('/teachers/create', [TeachersController::class, 'create'])->name('teachers.create');
    Route::post('/teachers/store', [TeachersController::class, 'store'])->name('teachers.store');
    Route::get('/teachers/edit/{id}', [TeachersController::class, 'teacher_edit'])->name('teachers.edit');
    Route::post('/teachers/password/change', [AuthPasswordController::class, 'teacherpassreset'])->name('teacherpass.reset');
});

/*
|--------------------------------------------------------------------------
| AUTH LANDING
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    // return view('results.result_card');
    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| TEACHER SEARCH
|--------------------------------------------------------------------------
*/
Route::get('/teacher/search', [TeachersController::class, 'search_teacher'])
    ->name('teacher.search');

/*
|--------------------------------------------------------------------------
| DASHBOARD (ALREADY ADMIN AWARE ✔)
|--------------------------------------------------------------------------
*/
Route::middleware(['permission:view dashboard'])->get('/dashboard', function (Request $request) {

    $user = Auth::user();
    $isAdmin = $user->hasRole('Admin');

    $totalStudents = $isAdmin
        ? StudentResult::count()
        : StudentResult::where('created_by', $user->id)->count();

    $totalsubjects = $isAdmin
        ? SubjectWiseMarks::count()
        : SubjectWiseMarks::where('created_by', $user->id)->count();

    $currentSession = Session::orderBy('id', 'desc')->first();
    $sessionId = $request->get('session_id') ?? ($currentSession ? $currentSession->id : null);

    $latestResultsQuery = StudentResult::orderByDesc('overall_percentage')
        ->whereRaw("TRIM(UPPER(overall_grade)) NOT IN ('D','E','U','F')")
        ->whereNotNull('overall_percentage');

    if (!$isAdmin) {
        $latestResultsQuery->where('created_by', $user->id);
    }

    $latestResults = $latestResultsQuery->take(3)->get();

    $sessionResults = $sessionId
        ? ($isAdmin
            ? StudentResult::where('session_id', $sessionId)->get()
            : StudentResult::where('session_id', $sessionId)->where('created_by', $user->id)->get()
        )
        : collect();

    $gradesPercentage = [];
    if ($sessionResults->count() > 0) {
        $gradesCount = $sessionResults->groupBy('overall_grade')->map->count();
        $total = $sessionResults->count();

        foreach ($gradesCount as $grade => $count) {
            $gradesPercentage[$grade] = round(($count / $total) * 100, 2);
        }
    }

    return view('dashboard', compact(
        'totalStudents',
        'totalsubjects',
        'currentSession',
        'latestResults',
        'sessionResults',
        'gradesPercentage',
        'sessionId',
        'isAdmin'
    ));
})->middleware(['auth', 'verified'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| ROLE MANAGEMENT
|--------------------------------------------------------------------------
*/
Route::post('/users/{id}/grant', [TeachersController::class, 'grantTeacherRole'])
    ->name('users.grant');

Route::post('/users/{id}/revoke', [TeachersController::class, 'revokeTeacherRole'])
    ->name('users.revoke');

/*
|--------------------------------------------------------------------------
| RESULTS & RELATED (ONLY ADDITION IS ADMIN FILTER SUPPORT)
|--------------------------------------------------------------------------
*/
Route::middleware('auth', 'role:Admin|Teacher')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile/{id}', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/{id}/ picture', [ProfileController::class, 'updateProfilePicture'])->name('profile.picture.update');

    /*
    |--------------------------------------------------------------------------
     | RESULTS (ADMIN = ALL, TEACHER = OWN)
     |--------------------------------------------------------------------------
     */
    Route::get('/results', [TheLynxResultController::class, 'results'])
        ->name('students.result');

    /*
     |--------------------------------------------------------------------------
     | SEARCH (ADMIN CAN FILTER BY USER)
     |--------------------------------------------------------------------------
     */
    Route::get('/results/search', [TheLynxResultController::class, 'search'])
        ->name('results.search');

    Route::get('/results-create', [TheLynxResultController::class, 'result_create'])
        ->name('results.create');

    Route::post('/result-store', [TheLynxResultController::class, 'store'])
        ->name('student_result.store');

    Route::get('/results/{id}', [TheLynxResultController::class, 'show'])
        ->name('results.show');

    Route::delete('/results/{id}', [TheLynxResultController::class, 'destroy'])
        ->name('results.destroy');

    Route::get('/results/{id}/edit', [TheLynxResultController::class, 'edit'])
        ->name('results.edit');

    Route::post('/results/{id}/update', [TheLynxResultController::class, 'update'])
        ->name('results.update');

    Route::get('/subject/search', [SubjectWiseMarksController::class, 'search_subject'])
        ->name('subjects.search');



    Route::get('/subject-total-marks', [SubjectWiseMarksController::class, 'subject_total_marks'])
        ->name('subject-total-marks');

    Route::get('/session-working-days', [SessionController::class, 'session_working_days'])
        ->name('session.working-days');

    Route::get('session/search', [SessionController::class, 'session_search'])
        ->name('session.search');

    Route::resource('subject-marks', SubjectWiseMarksController::class);
    Route::resource('sessions', SessionController::class);

    //fetch student route


});
require __DIR__ . '/auth.php';



// ── FetchApi routes ──────────────────────────────────────────
Route::get('get-students',                [FetchApiController::class, 'getstudents'])->name('getstudents');
Route::get('get-classes',                 [FetchApiController::class, 'getclasses'])->name('getclasses');
Route::get('get-branches',                [FetchApiController::class, 'getbranches'])->name('getbranches');
Route::get('get-branchemployee',          [FetchApiController::class, 'getbranchemployee'])->name('getbranchemployee');
Route::get('api/branches',                [FetchApiController::class, 'getbranches'])->name('api.branches');
Route::get('api/employees',               [FetchApiController::class, 'getbranchemployee'])->name('api.employees');
Route::get('api/employees/{employeeId}',  [FetchApiController::class, 'getemployeedetails'])->name('api.employee.details');


// Assign Subjects CRUD
Route::prefix('assign-subjects')->name('assign-subjects.')->group(function () {

    // Teacher list (only Teachers)
    Route::get('/',                 [AssignSubjectController::class, 'index'])->name('index');

    // Show the assignment form for a specific teacher
    Route::get('/{teacher}/create', [AssignSubjectController::class, 'create'])->name('create');

    // Store a new assignment
    Route::post('/store',           [AssignSubjectController::class, 'store'])->name('store');

    // Delete an assignment
    Route::delete('/{assignment}',  [AssignSubjectController::class, 'destroy'])->name('destroy');

    // ERP API proxies (called via AJAX from the form)
    Route::get('/api/branches',     [AssignSubjectController::class, 'apiBranches'])->name('api.branches');
    Route::get('/api/classes',      [AssignSubjectController::class, 'apiClasses'])->name('api.classes');
    Route::get('/api/sections',     [AssignSubjectController::class, 'apiSections'])->name('api.sections');
    Route::get('/api/subjects',     [AssignSubjectController::class, 'apiSubjects'])->name('api.subjects');
});

Route::middleware(['auth', 'role:Admin'])->group(function () {
    Route::get('/classes',        [ClassesController::class, 'index'])->name('classes.index');
    Route::post('/classes/sync',  [ClassesController::class, 'sync'])->name('classes.sync');
    Route::post('/classes/resync', [ClassesController::class, 'resync'])->name('classes.resync');
});

Route::middleware(['auth', 'role:Admin'])->group(function () {
    Route::get('/sections',         [SectionsController::class, 'index'])->name('sections.index');
    Route::post('/sections/sync',   [SectionsController::class, 'sync'])->name('sections.sync');
    Route::post('/sections/resync', [SectionsController::class, 'resync'])->name('sections.resync');
});

// ── Class Sections ────────────────────────────────────────────────────────
// Add this block to web.php alongside the existing classes / sections groups.

Route::middleware(['auth', 'role:Admin'])->group(function () {
    Route::get('/class-sections',         [ClassSectionController::class, 'index'])->name('class-sections.index');
    Route::post('/class-sections/sync',   [ClassSectionController::class, 'sync'])->name('class-sections.sync');
    Route::post('/class-sections/resync', [ClassSectionController::class, 'resync'])->name('class-sections.resync');
    Route::get('/classsubject', [ClassSubjectController::class, 'index'])->name('class-subjects.index');

    Route::post('/class-subjects/store', [ClassSubjectController::class, 'store'])
        ->name('class-subjects.store');
});



// Also add this import at the top of web.php with the other use statements:
// use App\Http\Controllers\ClassSectionController;
