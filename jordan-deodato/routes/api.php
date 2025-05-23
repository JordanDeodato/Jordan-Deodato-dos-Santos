<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VacancyController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::prefix('/user')->group(function () {
        Route::get('', [UserController::class, 'index'])->name('users.get');
        Route::get('{uuid}', [UserController::class, 'show'])->name('user.get');
        Route::post('', [UserController::class, 'store'])->name('user.store');
        Route::put('{uuid}', [UserController::class, 'update'])->name('user.update');
        Route::delete('{uuid}', [UserController::class, 'destroy'])->name('user.delete');
    });

    Route::prefix('/vacancy')->group(function () {
        Route::get('', [VacancyController::class, 'index'])->name('vacancies.get');
        Route::get('{uuid}', [VacancyController::class, 'show'])->name('vacancy.get');
        Route::post('', [VacancyController::class, 'store'])->name('vacancy.store');
        Route::put('{uuid}', [VacancyController::class, 'update'])->name('vacancy.update');
        Route::delete('{uuid}', [VacancyController::class, 'destroy'])->name('vacancy.delete');
        Route::put('close/{uuid}', [VacancyController::class, 'closeVacancy'])->name('vacancy.close');
    });

    Route::prefix('/application')->group(function () {
        Route::get('', [ApplicationController::class, 'index'])->name('applications.get');
        Route::get('{uuid}', [ApplicationController::class, 'show'])->name('application.get');
        Route::post('', [ApplicationController::class, 'store'])->name('application.store');
        Route::put('{uuid}', [ApplicationController::class, 'update'])->name('application.update');
        Route::delete('{uuid}', [ApplicationController::class, 'destroy'])->name('application.delete');
    });

    Route::prefix('/candidate')->group(function () {
        Route::get('', [CandidateController::class, 'index'])->name('candidates.get');
        Route::get('{uuid}', [CandidateController::class, 'show'])->name('candidate.get');
        Route::post('', [CandidateController::class, 'store'])->name('candidate.store');
        Route::put('{uuid}', [CandidateController::class, 'update'])->name('candidate.update');
        Route::delete('{uuid}', [CandidateController::class, 'destroy'])->name('candidate.delete');
    });

});

