<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\QueueController;
use App\Http\Controllers\StaffController;
use Illuminate\Support\Facades\Route;

Route::get('/', [QueueController::class, 'index'])->name('public.windows');
Route::get('/windows/{window}/terms', [QueueController::class, 'terms'])
    ->name('public.queue.terms');
Route::post('/windows/{window}/queue', [QueueController::class, 'issue'])
    ->name('public.queue.issue');
Route::get('/queue/{token}', [QueueController::class, 'show'])
    ->name('public.queue.show');
Route::get('/queue/{token}/status', [QueueController::class, 'status'])
    ->name('public.queue.status');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [LoginController::class, 'showLogin'])
        ->name('login');

    Route::post('/login', [LoginController::class, 'login'])
        ->name('login.submit');
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware(['auth', 'staff'])->prefix('staff')->name('staff.')->group(function (): void {
    Route::get('/', [StaffController::class, 'index'])->name('dashboard');
    Route::post('/call-next', [StaffController::class, 'callNext'])->name('call-next');
    Route::post('/queue/{queueRecord}/present', [StaffController::class, 'markPresent'])
        ->name('present');
    Route::post('/queue/{queueRecord}/complete', [StaffController::class, 'complete'])
        ->name('complete');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/windows', [AdminController::class, 'windows'])->name('windows.index');
    Route::post('/windows', [AdminController::class, 'storeWindow'])->name('windows.store');
    Route::patch('/windows/{window}', [AdminController::class, 'updateWindow'])
        ->name('windows.update');
    Route::delete('/windows/{window}', [AdminController::class, 'deleteWindow'])
        ->name('windows.delete');
    Route::get('/staff', [AdminController::class, 'staff'])->name('staff.index');
    Route::post('/staff', [AdminController::class, 'storeStaff'])->name('staff.store');
    Route::patch('/staff/{staffUser}', [AdminController::class, 'updateStaff'])
        ->name('staff.update');
    Route::post('/staff/{staffUser}/password', [AdminController::class, 'resetStaffPassword'])
        ->name('staff.password');
    Route::delete('/staff/{staffUser}', [AdminController::class, 'deleteStaff'])
        ->name('staff.delete');
    Route::get('/queues', [AdminController::class, 'queues'])->name('queues.index');
    Route::get('/missed-calls', [AdminController::class, 'missedCalls'])
        ->name('missed-calls.index');
    Route::get('/analytics', [AdminController::class, 'analytics'])->name('analytics.index');
});
