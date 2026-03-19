<?php

use App\Http\Controllers\LinkedInAuthController;
use App\Http\Controllers\LinkedInDashboardController;
use Illuminate\Support\Facades\Route;
// ============================================================
// ADD THESE TO: routes/web.php
// ============================================================



Route::prefix('linkedin')->middleware(['web'])->group(function () {

    // ── Dashboard ─────────────────────────────────────────────────────────
    Route::get('/dashboard',           [LinkedInDashboardController::class, 'index'])->name('linkedin.dashboard');
    Route::post('/toggle-automation',  [LinkedInDashboardController::class, 'toggleAutomation'])->name('linkedin.toggle');
    Route::post('/reset-index',        [LinkedInDashboardController::class, 'resetIndex'])->name('linkedin.reset-index');
    Route::get('/preview-post',        [LinkedInDashboardController::class, 'previewPost'])->name('linkedin.preview');
    Route::post('/publish-now',        [LinkedInDashboardController::class, 'publishNow'])->name('linkedin.publish-now');
    Route::post('/retry-dm/{lead}',    [LinkedInDashboardController::class, 'retryDM'])->name('linkedin.retry-dm');

    // ── OAuth ─────────────────────────────────────────────────────────────
    Route::get('/connect',    [LinkedInAuthController::class, 'connect'])->name('linkedin.connect');
    Route::get('/callback',   [LinkedInAuthController::class, 'callback'])->name('linkedin.callback');
    Route::post('/disconnect', [LinkedInAuthController::class, 'disconnect'])->name('linkedin.disconnect');
});