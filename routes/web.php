<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\MonthlyTargetController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/clients-overview', [DashboardController::class, 'overview'])->name('clients.overview');
    Route::resource('clients', ClientController::class);
    Route::resource('contents', ContentController::class);
    Route::post('monthly-targets/bulk', [MonthlyTargetController::class, 'bulkStore'])->name('monthly-targets.bulk');
    Route::resource('monthly-targets', MonthlyTargetController::class);
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
