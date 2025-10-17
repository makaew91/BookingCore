<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\GuideController;
use App\Http\Controllers\Api\HuntingBookingController;

Route::get('/guides', [GuideController::class, 'index']);
Route::post('/bookings', [HuntingBookingController::class, 'store']);


