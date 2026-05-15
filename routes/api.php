<?php

use App\Http\Controllers\ResultApiController;
use Illuminate\Support\Facades\Route;

Route::get('get-students', [ResultApiController::class, 'getStudents']);
Route::get('get-student/{id}', [ResultApiController::class, 'getStudent']);
