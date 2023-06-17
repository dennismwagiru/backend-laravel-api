<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\CategoryController;
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


Route::prefix('auth')->name('auth.')->group(function () {
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('register', [AuthController::class, 'register'])->name('register');

    Route::prefix('password')->name('password.')->group(function () {
        Route::post('forgot', [AuthController::class, 'forgotPassword'])->name('forgot');
        Route::post('reset', [AuthController::class, 'resetPassword'])->name('reset');
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::put('/user/preferences', [AuthController::class, 'updatePreferences']);
});

Route::apiResource('authors', AuthorController::class)->only(['index', 'show']);
Route::apiResource('articles', ArticleController::class)->only(['index', 'show']);
Route::apiResource('categories', CategoryController::class)->only(['index', 'show']);
