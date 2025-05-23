<?php

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('/user')->group(function() {
    Route::get('', [UserController::class, 'index'])->name('users.get');
    Route::get('{uuid}', [UserController::class, 'show'])->name('user.get');
    Route::post('', [UserController::class, 'store'])->name('user.store');
    Route::put('{uuid}', [UserController::class, 'update'])->name('user.update');
    Route::delete('{uuid}', [UserController::class, 'destroy'])->name('user.delete');
});

Route::prefix('/vacancy')->group(function() {
    Route::get('', [VacancyController::class, 'index'])->name('vacancies.get');
    Route::get('{uuid}', [VacancyController::class, 'show'])->name('vacancy.get');
    Route::post('', [VacancyController::class, 'store'])->name('vacancys.store');
    Route::put('{uuid}', [VacancyController::class, 'update'])->name('vacancys.update');
    Route::delete('{uuid}', [VacancyController::class, 'destroy'])->name('vacancys.delete');
});
