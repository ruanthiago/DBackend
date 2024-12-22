<?php

use App\Http\Controllers\Rule\ModuleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['isAdmin'])->group(function () {
    // Módulos
    Route::controller(ModuleController::class)
        ->prefix('modules')
        ->group(function () {
            Route::get('/', 'all');
            Route::post('/', 'store');
            Route::get('{module}', 'find');
            Route::put('{module}', 'update');
            Route::delete('{module}', 'destroy');
        });
});
