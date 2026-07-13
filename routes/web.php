<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RequesterController;
use App\Http\Controllers\RunnerDashboardController;
use App\Http\Controllers\Admin\DocumentRequestController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\RunnerController;

Route::get('/', function () {
    return auth()->check() ? redirect('/my-requests') : redirect('/login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/my-requests', [RequesterController::class, 'index'])->name('requester.index');
    Route::get('/requests/create', [RequesterController::class, 'create'])->name('requester.create');
    Route::post('/requests', [RequesterController::class, 'store'])->name('requester.store');
    Route::get('/requests/{documentRequest}/track', [RequesterController::class, 'track'])->name('requester.track');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/requests', [DocumentRequestController::class, 'index'])->name('requests.index');
    Route::post('/requests/{documentRequest}/approve', [DocumentRequestController::class, 'approve'])->name('requests.approve');
    Route::post('/requests/{documentRequest}/reject', [DocumentRequestController::class, 'reject'])->name('requests.reject');
    Route::post('/requests/{documentRequest}/assign', [DocumentRequestController::class, 'assignRunner'])->name('requests.assign');
    Route::get('/requests/{documentRequest}/eligible-runners', [DocumentRequestController::class, 'eligibleRunners'])->name('requests.eligible-runners');
});

Route::middleware(['auth', 'runner'])->prefix('runner')->name('runner.')->group(function () {
    Route::get('/dashboard', [RunnerDashboardController::class, 'index'])->name('dashboard');
    Route::get('/requests/{documentRequest}/active', [RunnerDashboardController::class, 'active'])->name('active');
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

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // ...existing request routes...

    Route::get('/runners', [RunnerController::class, 'index'])->name('runners.index');
    Route::get('/runners/create', [RunnerController::class, 'create'])->name('runners.create');
    Route::post('/runners', [RunnerController::class, 'store'])->name('runners.store');
});

require __DIR__.'/auth.php';