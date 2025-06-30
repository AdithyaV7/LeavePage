<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\MAController;
use App\Http\Controllers\HODController;

// Login routes
Route::get('/', [LoginController::class, 'showLogin'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

// MA routes (no authentication required)
Route::get('/MApage', [MAController::class, 'index'])->name('ma.index');
Route::get('/MApage/{id}', [MAController::class, 'show'])->name('ma.show');
Route::post('/MApage/{id}/approve', [MAController::class, 'approve'])->name('ma.approve');
Route::post('/MApage/{id}/return', [MAController::class, 'return'])->name('ma.return');

// HOD routes (no authentication required)
Route::get('/HODpage', [HODController::class, 'index'])->name('hod.index');
Route::get('/HODpage/{id}', [HODController::class, 'show'])->name('hod.show');
Route::post('/HODpage/{id}/approve', [HODController::class, 'approve'])->name('hod.approve');
Route::post('/HODpage/{id}/return', [HODController::class, 'return'])->name('hod.return');

// Protected routes (user must be logged in)
Route::middleware('checklogin')->group(function () {
    
    Route::get('/firstPage', [LeaveController::class, 'index'])->name('leaves.index'); // Landing page
    Route::get('/leave/create', [LeaveController::class, 'create'])->name('leaves.create'); // Create new leave or open draft/returned
    Route::post('/leave/store', [LeaveController::class, 'store'])->name('leaves.store'); // Save (submit or draft)
    Route::delete('/leave/delete/{id}', [LeaveController::class, 'destroy'])->name('leaves.destroy'); // Delete draft
});
