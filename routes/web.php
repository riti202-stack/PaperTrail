<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RequesterController;
use App\Http\Controllers\RunnerDashboardController;
use App\Http\Controllers\Admin\DocumentRequestController;
use App\Http\Controllers\Admin\RunnerController;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return auth()->check() ? redirect('/dashboard') : redirect('/login');
});

Route::get('/dashboard', function () {
    $user = auth()->user();

    return match ($user->role) {
        'admin' => redirect('/admin/requests'),
        'runner' => redirect('/runner/dashboard'),
        default => redirect('/my-requests'),
    };
})->middleware('auth')->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/my-requests', [RequesterController::class, 'index'])->name('requester.index');
    Route::get('/requests/create', [RequesterController::class, 'create'])->name('requester.create');
    Route::post('/requests', [RequesterController::class, 'store'])->name('requester.store');
    Route::get('/requests/{documentRequest}/track', [RequesterController::class, 'track'])->name('requester.track');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/requests', [DocumentRequestController::class, 'index'])->name('requests.index');
    Route::get('/requests/history', [DocumentRequestController::class, 'history'])->name('requests.history');
    Route::post('/requests/{documentRequest}/archive', [DocumentRequestController::class, 'archive'])->name('requests.archive');
    Route::post('/requests/{documentRequest}/approve', [DocumentRequestController::class, 'approve'])->name('requests.approve');
    Route::post('/requests/{documentRequest}/reject', [DocumentRequestController::class, 'reject'])->name('requests.reject');
    Route::get('/requests/{documentRequest}/assign', [DocumentRequestController::class, 'showAssign'])->name('requests.show-assign');
    Route::post('/requests/{documentRequest}/assign', [DocumentRequestController::class, 'assignRunner'])->name('requests.assign');
    Route::get('/requests/{documentRequest}/eligible-runners', [DocumentRequestController::class, 'eligibleRunners'])->name('requests.eligible-runners');

    Route::get('/runners', [RunnerController::class, 'index'])->name('runners.index');
    Route::get('/runners/create', [RunnerController::class, 'create'])->name('runners.create');
    Route::post('/runners', [RunnerController::class, 'store'])->name('runners.store');
});

Route::middleware(['auth', 'runner'])->prefix('runner')->name('runner.')->group(function () {
    Route::get('/dashboard', [RunnerDashboardController::class, 'index'])->name('dashboard');
    Route::get('/requests/{documentRequest}/active', [RunnerDashboardController::class, 'active'])->name('active');
    Route::post('/requests/{documentRequest}/accept', [RunnerDashboardController::class, 'acceptTask'])->name('accept');
    Route::post('/requests/{documentRequest}/decline', [RunnerDashboardController::class, 'declineTask'])->name('decline');
    Route::post('/requests/{documentRequest}/advance', [RunnerDashboardController::class, 'advanceStatus'])->name('advance');
});

require __DIR__.'/auth.php';