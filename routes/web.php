<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/',          fn() => view('landing'));
Route::get('/login',     fn() => view('login'));
Route::get('/dashboard', fn() => view('dashboard'));
Route::get('/admin',     fn() => view('admin'));
Route::get('/redirect',  fn() => view('redirect'));

Route::get('/forgot-password',  [AuthController::class, 'forgotPasswordForm']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.email')->middleware('throttle:3,1');
Route::get('/reset-password/{token}', [AuthController::class, 'resetPasswordForm'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update')->middleware('throttle:5,1');
