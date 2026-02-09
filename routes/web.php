<?php

use App\Http\Controllers\Auth\PasswordController as AuthPasswordController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TheLynxResultController;
use App\Http\Controllers\SubjectWiseMarksController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\TeachersController;
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
    Route::get('/teahers',[TeachersController::class, 'index'])->name('teachers.index');
    Route::get('/teahers/create',[TeachersController::class, 'create'])->name('teachers.create');
    Route::post('/teahers/create',[TeachersController::class, 'store'])->name('teachers.store');
    Route::post('/teachers/edit/{id}',[TeachersController::class, 'teacher_edit'])->name('teachers.edit');
    Route::post('/teachers/password/change',[AuthPasswordController::class, 'teacherpassreset'])->name('teacherpass.reset');
});

/*
|--------------------------------------------------------------------------
| AUTH LANDING
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
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
Route::middleware('auth','role:Admin|Teacher')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile/{id}', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/{id}/
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    picture', [ProfileController::class, 'updateProfilePicture'])->name('profile.picture.update');

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

    Route::get('session/search',[SessionController::class, 'session_search'])
        ->name('session.search');

    Route::resource('subject-marks', SubjectWiseMarksController::class);
    Route::resource('sessions', SessionController::class);
});

require __DIR__ . '/auth.php';
