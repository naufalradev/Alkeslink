<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImpactController;
use App\Http\Controllers\AiAssistantController;

/*
|--------------------------------------------------------------------------
| API routes (stateful via Sanctum session cookie)
|--------------------------------------------------------------------------
*/
Route::middleware(['web'])->group(function () {

    // Impact stats for animated landing-page counter
    Route::get('/impact', [ImpactController::class, 'stats'])->name('api.impact');

    // AI chatbot widget
    Route::post('/ai/chat',    [AiAssistantController::class, 'chat'])->name('api.ai.chat');
    Route::get('/ai/history',  [AiAssistantController::class, 'history'])->name('api.ai.history');
});
