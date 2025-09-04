<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\MAController;
use App\Http\Controllers\HODController;
use App\Http\Controllers\DeanController;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;
use App\Http\Controllers\VCController;

// Login routes
// Set the default login page to MA page
Route::get('/', [MAController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

// URL-based login by employee ID: /leaves/{id}
Route::get('/leaves/{id}', [LoginController::class, 'loginById'])->name('login.byid');

// MA routes (no authentication required)
Route::get('/MApage', [MAController::class, 'index'])->name('ma.index');
Route::get('/MAPage', [MAController::class, 'index'])->name('ma.index.alt'); // Alternative route
Route::get('/MApage/{id}', [MAController::class, 'show'])->name('ma.show');
Route::post('/MApage/{id}/approve', [MAController::class, 'approve'])->name('ma.approve');
Route::post('/MApage/{id}/return', [MAController::class, 'return'])->name('ma.return');

// HOD routes (no authentication required)
Route::get('/HODpage', [HODController::class, 'index'])->name('hod.index');
Route::get('/HODpage/{id}', [HODController::class, 'show'])->name('hod.show');
Route::post('/HODpage/{id}/approve', [HODController::class, 'approve'])->name('hod.approve');
Route::post('/HODpage/{id}/return', [HODController::class, 'return'])->name('hod.return');

// Dean/Registrar routes (no authentication required)
Route::get('/Deanpage', [DeanController::class, 'index'])->name('dean.index');
Route::get('/Deanpage/{id}', [DeanController::class, 'show'])->name('dean.show');
Route::post('/Deanpage/{id}/recommend', [DeanController::class, 'recommend'])->name('dean.recommend');

// VC routes (no authentication required)
Route::get('/VCpage', [VCController::class, 'index'])->name('vc.index');
Route::get('/VCpage/{id}', [VCController::class, 'show'])->name('vc.show');
Route::post('/VCpage/{id}/recommend', [VCController::class, 'recommend'])->name('vc.recommend');

// Protected routes (user must be logged in)
Route::middleware('checklogin')->group(function () {
    
    Route::get('/firstPage', [LeaveController::class, 'index'])->name('leaves.index'); // Landing page
    Route::get('/leave/create', [LeaveController::class, 'create'])->name('leaves.create'); // Create new leave or open draft/returned
    Route::get('/leave/show/{id}', [LeaveController::class, 'show'])->name('leaves.show'); // View submitted leave application
    Route::post('/leave/store', [LeaveController::class, 'store'])->name('leaves.store'); // Save (submit or draft)
    Route::delete('/leave/delete/{id}', [LeaveController::class, 'destroy'])->name('leaves.destroy'); // Delete draft

    // AJAX endpoints for file upload/delete
    Route::post('/leave/upload-file', [LeaveController::class, 'uploadFile'])->name('leaves.uploadFile');
    Route::post('/leave/delete-file', [LeaveController::class, 'deleteFile'])->name('leaves.deleteFile');

    // AJAX endpoints for travel document upload/delete
    Route::post('/leave/upload-travel-document', [LeaveController::class, 'uploadTravelDocument'])->name('leaves.uploadTravelDocument');
    Route::post('/leave/remove-travel-document', [LeaveController::class, 'removeTravelDocument'])->name('leaves.removeTravelDocument');

    // AJAX endpoint for temporary file upload
    Route::post('/leave/upload-temp-file', [LeaveController::class, 'uploadTempFile'])->name('leaves.uploadTempFile');
    Route::post('/leave/delete-temp-file', [LeaveController::class, 'deleteTempFile'])->name('leaves.deleteTempFile');

    // AJAX endpoint for saving travel details
    Route::post('/leave/save-travel-detail', [LeaveController::class, 'saveTravelDetail'])->name('leaves.saveTravelDetail');
    Route::delete('/leave/delete-travel-detail/{id}', [LeaveController::class, 'deleteTravelDetail'])->name('leaves.deleteTravelDetail');

    // Test endpoint for debugging
    Route::post('/leave/test-upload', [LeaveController::class, 'testUpload'])->name('leaves.testUpload');

    Route::get('/leave/draft/create', [LeaveController::class, 'createDraft'])->name('leaves.draft.create');
    Route::get('/leave/new', [LeaveController::class, 'create'])->name('leaves.new'); // New application without database record
});

Route::get('/dashboard', [MAController::class, 'dashboard'])->name('ma.dashboard');
Route::get('/dashboard/vc-approved', [MAController::class, 'dashboardVcApproved'])->name('ma.dashboard.vcapproved');
Route::get('/dashboard/status', [MAController::class, 'statusPage'])->name('ma.status');
Route::get('/MApage/{id}/hod', [MAController::class, 'showHod'])->name('ma.show.hod');
Route::get('/MApage/{id}/dean', [MAController::class, 'showDean'])->name('ma.show.dean');
Route::get('/MApage/{id}/vc', [MAController::class, 'showVc'])->name('ma.show.vc');
