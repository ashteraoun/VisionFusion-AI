<?php

use App\Http\Controllers\ImageAnalysisController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ImageAnalysisController::class, 'index'])->name('images.index');
Route::post('/analyze-image', [ImageAnalysisController::class, 'analyze'])->name('images.analyze');
Route::get('/health', [ImageAnalysisController::class, 'health'])->name('health');
