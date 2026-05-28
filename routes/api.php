<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LinkController;
use App\Http\Controllers\PlanController;
use Illuminate\Support\Facades\Route;

// ── Públicas ──────────────────────────────────────────────────
Route::post('auth/login',    [AuthController::class, 'login']);
Route::post('auth/register', [AuthController::class, 'register']);
Route::post('auth/google',   [AuthController::class, 'google']);

Route::get('plans',          [PlanController::class, 'index']);

Route::post('links/click/{id}', [LinkController::class, 'click']);
Route::get('links/url/{id}',    [LinkController::class, 'getUrl']);

// ── Autenticado ───────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('auth/me',     [AuthController::class, 'me']);
    Route::post('auth/logout',[AuthController::class, 'logout']);

    Route::get('links',           [LinkController::class, 'index']);
    Route::post('links',          [LinkController::class, 'store']);
    Route::put('links/{id}',      [LinkController::class, 'update']);
    Route::delete('links/{id}',   [LinkController::class, 'destroy']);

    // ── Admin ─────────────────────────────────────────────────
    Route::middleware('admin')->group(function () {
        Route::get('admin/stats',         [AdminController::class, 'stats']);
        Route::get('admin/users',         [AdminController::class, 'users']);
        Route::get('admin/links',         [AdminController::class, 'links']);
        Route::put('admin/users/{id}',    [AdminController::class, 'updateUser']);
        Route::delete('admin/users/{id}', [AdminController::class, 'destroyUser']);
        Route::delete('admin/links/{id}', [AdminController::class, 'destroyLink']);

        Route::post('plans',          [PlanController::class, 'store']);
        Route::put('plans/{id}',      [PlanController::class, 'update']);
        Route::delete('plans/{id}',   [PlanController::class, 'destroy']);
        Route::post('plans/{id}/toggle', [PlanController::class, 'toggle']);
    });
});
