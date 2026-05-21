<?php

use Illuminate\Support\Facades\Route;

Route::get('/',          fn() => view('landing'));
Route::get('/login',     fn() => view('login'));
Route::get('/dashboard', fn() => view('dashboard'));
Route::get('/admin',     fn() => view('admin'));
Route::get('/redirect',  fn() => view('redirect'));
