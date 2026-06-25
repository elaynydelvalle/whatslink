<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\LinkController;
use App\Http\Controllers\PlanController;
use Illuminate\Support\Facades\Route;

// ── Públicas ──────────────────────────────────────────────────
Route::post('auth/login',    [AuthController::class, 'login'])->middleware('throttle:5,1');
Route::post('auth/register', [AuthController::class, 'register'])->middleware('throttle:5,1');
// auth/google desativada: o backend confiava em email/nome enviados pelo cliente sem
// verificar a assinatura do token do Google, permitindo sequestro de qualquer conta.
// Reativar somente após validar o ID token no servidor (ex.: endpoint tokeninfo do Google).

Route::get('plans',          [PlanController::class, 'index']);

Route::post('links/click/{id}', [LinkController::class, 'click']);
Route::get('links/url/{id}',    [LinkController::class, 'getUrl']);

Route::post('webhooks/asaas', [BillingController::class, 'webhook'])->middleware('throttle:60,1');

// ── Autenticado ───────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('auth/me',     [AuthController::class, 'me']);
    Route::post('auth/logout',[AuthController::class, 'logout']);

    Route::get('links',           [LinkController::class, 'index']);
    Route::post('links',          [LinkController::class, 'store']);
    Route::put('links/{id}',      [LinkController::class, 'update']);
    Route::delete('links/{id}',   [LinkController::class, 'destroy']);

    Route::post('plans/{id}/subscribe', [BillingController::class, 'subscribe'])->middleware('throttle:10,1');

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
