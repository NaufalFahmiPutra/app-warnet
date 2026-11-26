<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ComputerTypeController;
use App\Http\Controllers\ComputerController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\OperatorController;
use App\Http\Controllers\RentalSessionController;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::resource('members', MemberController::class)->except(['show', 'destroy']);
    Route::get('/sessions', [RentalSessionController::class, 'index'])->name('sessions.index');
    Route::get('/sessions/create', [RentalSessionController::class, 'create'])->name('sessions.create');
    Route::post('/sessions', [RentalSessionController::class, 'store'])->name('sessions.store');
    Route::put('/sessions/{session}/selesai', [RentalSessionController::class, 'selesai'])->name('sessions.selesai');

    Route::middleware('admin')->group(function () {
        Route::resource('computer-types', ComputerTypeController::class)->except(['show']);
        Route::resource('computers', ComputerController::class)->except(['show']);
        Route::delete('/members/{member}', [MemberController::class, 'destroy'])->name('members.destroy');
        Route::resource('operators', OperatorController::class)->except(['show']);

    });

});