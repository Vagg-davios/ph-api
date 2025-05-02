<?php

use App\Http\Controllers\Api\PornstarController;
use Illuminate\Support\Facades\Route;

Route::get('pornstars/licenses', [PornstarController::class, 'licenses'])
    ->name('api.pornstars.licenses');

Route::get('pornstars/stats', [PornstarController::class, 'stats'])
    ->name('api.pornstars.stats');

Route::get('/pornstars/search', [PornstarController::class, 'search']);

Route::apiResource('pornstars', PornstarController::class)
    ->only(['index', 'show'])
    ->names('api.pornstars');
