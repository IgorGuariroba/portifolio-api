<?php

use App\Http\Controllers\ProjectAdminController;
use App\Http\Controllers\ProjectController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

Route::middleware('auth:api')->group(function () {
    Route::post('/admin/projects', [ProjectAdminController::class, 'store']);
    Route::put('/admin/projects/{id}', [ProjectAdminController::class, 'update']);
    Route::delete('/admin/projects/{id}', [ProjectAdminController::class, 'destroy']);
    Route::put('/admin/portfolio', [ProjectAdminController::class, 'updatePortfolio']);
});


Route::get('/projects', [ProjectController::class, 'index']);
Route::get('/projects/{id}', [ProjectController::class, 'show']);
Route::get('/portfolio', [ProjectController::class, 'portfolio']);
