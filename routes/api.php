<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\StatusController;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/runner/location', [LocationController::class, 'update']);
    Route::get('/requests/{documentRequest}/location', [LocationController::class, 'trackRequest']);
    Route::get('/requests/{documentRequest}/status', [StatusController::class, 'show']);
    Route::get('/runner/new-task-count', [\App\Http\Controllers\RunnerDashboardController::class, 'newTaskCount']);

    Route::middleware('request.chat')->group(function () {
        Route::get('/requests/{documentRequest}/chat', [ChatController::class, 'index']);
        Route::post('/requests/{documentRequest}/chat', [ChatController::class, 'store']);
    });
});