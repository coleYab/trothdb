<?php

use App\Http\Controllers\CliAuthorizeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LearnController;
use App\Http\Controllers\PracticeController;
use App\Http\Controllers\ProblemsetController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'welcome')->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    Route::get('problemset', [ProblemsetController::class, 'index'])->name('problemset.index');
    Route::get('problems/create', [ProblemsetController::class, 'create'])->name('problems.create');
    Route::post('problems', [ProblemsetController::class, 'store'])->name('problems.store');
    Route::get('problems/{problem}', [ProblemsetController::class, 'show'])->name('problems.show');

    // Learning
    Route::get('learn', [LearnController::class, 'index'])->name('learn.index');
    Route::get('learn/{track}', [LearnController::class, 'show'])->name('learn.show');
    Route::get('learn/{track}/{lesson}', [LearnController::class, 'lesson'])->name('learn.lesson');

    // Practice
    Route::get('setup', [PracticeController::class, 'setup'])->name('setup.index');

    // LocalJudge & CLI Device Login Authorization
    Route::get('cli/authorize', [CliAuthorizeController::class, 'show'])->name('cli.authorize');
    Route::post('cli/authorize/approve', [CliAuthorizeController::class, 'approve'])->name('cli.authorize.approve');
    Route::post('cli/authorize/deny', [CliAuthorizeController::class, 'deny'])->name('cli.authorize.deny');
});

require __DIR__.'/settings.php';
