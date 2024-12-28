<?php

use App\Http\Controllers\Rule\ModuleController;
use App\Http\Controllers\Rule\PermissionController;
use App\Http\Controllers\TenantController;
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

    // Permissões
    Route::controller(PermissionController::class)
        ->prefix('permissions')
        ->group(function () {
            Route::get('/', 'all');
            Route::post('/', 'store');
            Route::get('{permission}', 'find');
            Route::put('{permission}', 'update');
            Route::delete('{permission}', 'destroy');
        });

    // Inquilinos
    Route::controller(TenantController::class)
        ->prefix('tenants')
        ->group(function () {
            Route::get('/', 'all');
            Route::post('/', 'store');
            Route::get('{tenant}', 'find');
            Route::put('{tenant}', 'update');
            Route::delete('{tenant}', 'destroy');
        });
});
