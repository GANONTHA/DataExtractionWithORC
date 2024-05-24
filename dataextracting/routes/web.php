<?php

use App\Http\Controllers\OCRController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/data', [OCRController::class, 'processImage'])->name('processImage');
