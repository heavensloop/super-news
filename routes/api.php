<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\ArticlesController;
use App\Http\Controllers\FiltersController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/register', [RegisteredUserController::class, 'store']);
        Route::post('/login', [LoginController::class, 'login']);
        Route::get('/verify-email', [VerifyEmailController::class, '__invoke'])->name('auth.email.verify');
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [LogoutController::class, 'logout'])->middleware('auth:sanctum');
        Route::get('/me', function (Request $request) {
            return $request->user();
        })->middleware('auth:sanctum');
    });

    Route::get('/articles/featured', [ArticlesController::class, 'featured']);
    Route::get('/articles/{article_id}', [ArticlesController::class, 'show'])
        ->where('article_id', '[0-9]+\-(\w+\-?)+');

    // Setup protected routes for articles
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/articles/filtered', [ArticlesController::class, 'filtered']);
        Route::put('/user/filters', [FiltersController::class, 'save']);
    });
});
