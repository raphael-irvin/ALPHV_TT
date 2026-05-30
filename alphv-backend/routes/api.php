<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RecordController;
use App\Http\Controllers\AuthController;

// === PUBLIC ROUTES ===
// Anyone can view the grid, and anyone can try to log in
Route::get('/records', [RecordController::class, 'index']);
Route::post('/login', [AuthController::class, 'login']);

// === PROTECTED ROUTES ===
// You MUST have a valid Sanctum Token to access these
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/records', [RecordController::class, 'store']);
    Route::put('/records/{id}', [RecordController::class, 'update']);
    Route::delete('/records/{id}', [RecordController::class, 'destroy']);
    Route::post('/logout', [AuthController::class, 'logout']);
});