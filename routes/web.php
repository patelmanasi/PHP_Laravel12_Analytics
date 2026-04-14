<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AnalyticsController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/analytics', [AnalyticsController::class, 'index']);
Route::get('/analytics/export', [AnalyticsController::class, 'export']);
