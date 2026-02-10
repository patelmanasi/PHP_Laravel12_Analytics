<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AnalyticsController;

Route::get('/analytics', [AnalyticsController::class, 'index']);

Route::get('/', function () {
    return view('welcome');
});
