<?php

use App\Http\Controllers\Api\CliAuthController;
use App\Http\Controllers\Api\CliSubmissionController;
use App\Http\Controllers\Api\LearnController;
use App\Http\Controllers\Api\ProblemController;
use App\Http\Controllers\Api\SubmissionController;
use Illuminate\Support\Facades\Route;

// Problems & Submissions
Route::get('problems', [ProblemController::class, 'index'])->name('api.problems.index');
Route::post('problems', [ProblemController::class, 'store'])->name('api.problems.store');
Route::get('problems/{problem}/bundle', [ProblemController::class, 'bundle'])->name('api.problems.bundle');
Route::get('problems/{problem}', [ProblemController::class, 'show'])->name('api.problems.show');
Route::get('problems/{problem}/submissions', [SubmissionController::class, 'index'])->name('api.problems.submissions.index');
Route::post('problems/{problem}/submissions', [SubmissionController::class, 'store'])->name('api.problems.submissions.store');

// CLI & LocalJudge Submissions (Score out of 100 + Source Code File Upload)
Route::post('judge/problems/{problem}/submit', [CliSubmissionController::class, 'submit'])->name('api.judge.problems.submit');
Route::post('judge/submit', [CliSubmissionController::class, 'submit'])->name('api.judge.submit');
Route::post('cli/problems/{problem}/submit', [CliSubmissionController::class, 'submit'])->name('api.cli.problems.submit');
Route::post('cli/submit', [CliSubmissionController::class, 'submit'])->name('api.cli.submit');

// Learn tracks & lessons
Route::get('learn/tracks', [LearnController::class, 'tracks'])->name('api.learn.tracks');
Route::get('learn/tracks/{track}', [LearnController::class, 'track'])->name('api.learn.track');
Route::get('learn/tracks/{track}/lessons/{lesson}', [LearnController::class, 'lesson'])->name('api.learn.lesson');
Route::post('learn/tracks/{track}/lessons/{lesson}/complete', [LearnController::class, 'toggleComplete'])->name('api.learn.lesson.complete');

// LocalJudge & CLI Device Login Authorization
Route::post('judge/auth/request', [CliAuthController::class, 'request'])->name('api.judge.auth.request');
Route::post('judge/auth/poll', [CliAuthController::class, 'poll'])->name('api.judge.auth.poll');
Route::get('judge/whoami', [CliAuthController::class, 'whoami'])->name('api.judge.whoami');

Route::post('cli/auth/device-code', [CliAuthController::class, 'request'])->name('api.cli.auth.request');
Route::post('cli/auth/poll', [CliAuthController::class, 'poll'])->name('api.cli.auth.poll');
