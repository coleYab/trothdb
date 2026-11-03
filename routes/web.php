<?php

use App\Http\Controllers\ProblemsetController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'dashboard')->name('dashboard');

    Route::get('problemset', [ProblemsetController::class, 'index'])->name('problemset.index');
    Route::get('problems/{problem}', [ProblemsetController::class, 'show'])->name('problems.show');
});

require __DIR__.'/settings.php';
