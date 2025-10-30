<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\FuelRequestController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth', 'admin'])->group(function () {
    Route::resource('users', UserController::class);
    Route::resource('batches', BatchController::class);
    Route::get('/batches-export', [BatchController::class, 'export'])->name('batches.export');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/fuel-requests', [FuelRequestController::class, 'index'])->name('fuel-requests.index');
    Route::get('/fuel-requests/create', [FuelRequestController::class, 'create'])->name('fuel-requests.create');
    Route::post('/fuel-requests', [FuelRequestController::class, 'store'])->name('fuel-requests.store');
    Route::get('/fuel-requests/{fuelRequest}', [FuelRequestController::class, 'show'])->name('fuel-requests.show');
    Route::delete('/fuel-requests/{fuelRequest}', [FuelRequestController::class, 'destroy'])->name('fuel-requests.destroy');
    
    Route::get('/batches/view', [BatchController::class, 'index'])->name('batches.index');
    Route::get('/batches/view/{batch}', [BatchController::class, 'show'])->name('batches.show');
    Route::get('/batches-export', [BatchController::class, 'export'])->name('batches.export');
    
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
require __DIR__ . '/auth.php';