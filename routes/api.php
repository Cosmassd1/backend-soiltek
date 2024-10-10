<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\userController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\SensorController;
use App\Http\Controllers\MeasurementsController;


Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('/user-details', [UserController::class, 'userDetails']);

Route::post('/users', [UserController::class, 'store']);

Route::get('/location', [LocationController::class, 'index']);

Route::post('/location', [LocationController::class, 'store']);

Route::get('/sensors', [SensorController::class, 'index']);


Route::post('/readings', [MeasurementsController::class, 'store']);

Route::get('/users-location', [UserController::class, 'getUsersWithLocation']);

Route::get('/user-soil-data', [MeasurementsController::class, 'getUserSoilData']);

Route::get('/user/{user_id}/alert', [MeasurementsController::class, 'getAlert']);





