<?php

use App\Http\Controllers\ChildController;
use App\Http\Controllers\StarController;
use App\Http\Controllers\RewardController;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;

// Health check endpoint
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toISOString(),
        'service' => 'Star Savings API'
    ]);
});

// Children routes
Route::apiResource('children', ChildController::class);

// Star operations
Route::get('stars/recent', [StarController::class, 'recent']);
Route::post('children/{child}/stars/add', [StarController::class, 'add']);
Route::post('children/{child}/stars/subtract', [StarController::class, 'subtract']);

// Rewards routes
Route::apiResource('rewards', RewardController::class);
Route::post('rewards/{reward}/redeem', [RewardController::class, 'redeem']);

// Settings routes
Route::get('settings', [SettingController::class, 'index']);
Route::get('settings/{key}', [SettingController::class, 'show']);
Route::put('settings/{key}', [SettingController::class, 'update']);
