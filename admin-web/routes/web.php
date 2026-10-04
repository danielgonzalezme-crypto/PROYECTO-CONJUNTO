<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\ResidenteController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard.index'));
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
Route::resource('empleados', EmpleadoController::class)->except('show');
Route::get('/residentes', [ResidenteController::class, 'index'])->name('residentes.index');