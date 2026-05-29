<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RecordController;

// When the frontend asks for /api/records, run the logic in our Controller
Route::get('/records', [RecordController::class, 'index']);
Route::post('/records', [RecordController::class, 'store']);

// When the frontend sends an Update (PUT) or Delete request for a specific ID
Route::put('/records/{id}', [RecordController::class, 'update']);
Route::delete('/records/{id}', [RecordController::class, 'destroy']);