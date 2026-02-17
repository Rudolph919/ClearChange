<?php

use App\Http\Controllers\ChangeRequestController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('change-requests-pending', [ChangeRequestController::class, 'pendingApproval'])
        ->name('change-requests.pending-approval');
    Route::resource('change-requests', ChangeRequestController::class)->except(['show']);
    Route::get('change-requests/{change_request}/audit', [ChangeRequestController::class, 'audit'])
        ->name('change-requests.audit');
    Route::post('change-requests/{change_request}/submit', [ChangeRequestController::class, 'submit'])
        ->name('change-requests.submit');
    Route::post('change-requests/{change_request}/approve', [ChangeRequestController::class, 'approve'])
        ->name('change-requests.approve');
});

require __DIR__.'/auth.php';
