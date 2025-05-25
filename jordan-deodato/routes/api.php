<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\ImportedDataCsvController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserTypeController;
use App\Http\Controllers\VacancyController;
use App\Http\Controllers\VacancyTypeController;
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
        Route::get('', [UserController::class, 'index'])->name('users.index');
        Route::get('{uuid}', [UserController::class, 'show'])->name('user.show');
        Route::post('', [UserController::class, 'store'])->name('user.store');
        Route::put('{uuid}', [UserController::class, 'update'])->name('user.update');
        Route::delete('delete-all', [UserController::class, 'deleteAll'])->name('user.deleteAll');
        Route::delete('delete-by-uuid', [UserController::class, 'deleteByUuid'])->name('user.deleteByUuid');
        Route::delete('{uuid}', [UserController::class, 'destroy'])->name('user.destroy');
    });

    Route::prefix('/user-type')->group(function () {
        Route::get('', [UserTypeController::class, 'index'])->name('user_type.index');
        Route::get('{uuid}', [UserTypeController::class, 'show'])->name('user_type.show');
    });

    Route::prefix('/vacancy')->group(function () {
        Route::get('', [VacancyController::class, 'index'])->name('vacancies.index');
        Route::get('{uuid}', [VacancyController::class, 'show'])->name('vacancy.show');
        Route::post('', [VacancyController::class, 'store'])->name('vacancy.store');
        Route::put('{uuid}', [VacancyController::class, 'update'])->name('vacancy.update');
        Route::delete('delete-all', [VacancyController::class, 'deleteAll'])->name('vacancy.deleteAll');
        Route::delete('delete-by-uuid', [VacancyController::class, 'deleteByUuid'])->name('vacancy.deleteByUuid');
        Route::delete('{uuid}', [VacancyController::class, 'destroy'])->name('vacancy.destroy');
        Route::put('close/{uuid}', [VacancyController::class, 'closeVacancy'])->name('vacancy.close');
    });

    Route::prefix('/vacancy-type')->group(function () {
        Route::get('', [VacancyTypeController::class, 'index'])->name('vacancy_type.index');
        Route::get('{uuid}', [VacancyTypeController::class, 'show'])->name('vacancy_type.show');
    });

    Route::prefix('/application')->group(function () {
        Route::get('', [ApplicationController::class, 'index'])->name('applications.index');
        Route::get('{uuid}', [ApplicationController::class, 'show'])->name('application.show');
        Route::post('', [ApplicationController::class, 'store'])->name('application.store');
        Route::put('{uuid}', [ApplicationController::class, 'update'])->name('application.update');
        Route::delete('delete-all', [ApplicationController::class, 'deleteAll'])->name('application.deleteAll');
        Route::delete('delete-by-uuid', [ApplicationController::class, 'deleteByUuid'])->name('application.deleteByUuid');
        Route::delete('{uuid}', [ApplicationController::class, 'destroy'])->name('application.destroy');
    });

    Route::prefix('/candidate')->group(function () {
        Route::get('', [CandidateController::class, 'index'])->name('candidates.index');
        Route::get('{uuid}', [CandidateController::class, 'show'])->name('candidate.show');
        Route::post('', [CandidateController::class, 'store'])->name('candidate.store');
        Route::put('{uuid}', [CandidateController::class, 'update'])->name('candidate.update');
        Route::delete('delete-all', [CandidateController::class, 'deleteAll'])->name('candidate.deleteAll');
        Route::delete('delete-by-uuid', [CandidateController::class, 'deleteByUuid'])->name('candidate.deleteByUuid');
        Route::delete('{uuid}', [CandidateController::class, 'destroy'])->name('candidate.destroy');
    });

    Route::prefix('/csv')->group(function () {
        Route::get('/analyze', [ImportedDataCsvController::class, 'analyze'])->name('csv.analyze');
    });

});

