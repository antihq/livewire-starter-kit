<?php

use App\Http\Controllers\Api\TaskCallbackController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::name('api.')->group(function () {
    Route::post('tasks/{task}/callback', [TaskCallbackController::class, 'handle'])
        ->name('tasks.callback');
});

Route::get('user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
