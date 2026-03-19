<?php

use App\Http\Controllers\LinkedInAuthController;
use App\Http\Controllers\LinkedInDashboardController;
use App\Http\Controllers\LinkedInLoginController;
use App\Http\Middleware\LinkedInPanelAuth;
use Illuminate\Support\Facades\Route;

// ── Login (public) ────────────────────────────────────────────────────────────
Route::prefix('linkedin')->middleware(['web'])->group(function () {
    Route::get('/login',  [LinkedInLoginController::class, 'showLogin'])->name('linkedin.login');
    Route::post('/login', [LinkedInLoginController::class, 'login'])->name('linkedin.login.post');
    Route::post('/logout', [LinkedInLoginController::class, 'logout'])->name('linkedin.logout');
});

// ── Protected routes ──────────────────────────────────────────────────────────
Route::prefix('linkedin')->middleware(['web', LinkedInPanelAuth::class])->group(function () {

    // Dashboard
    Route::get('/dashboard',            [LinkedInDashboardController::class, 'index'])->name('linkedin.dashboard');
    Route::post('/toggle-automation',   [LinkedInDashboardController::class, 'toggleAutomation'])->name('linkedin.toggle');
    Route::post('/toggle-lead-magnets', [LinkedInDashboardController::class, 'toggleLeadMagnets'])->name('linkedin.toggle-lead-magnets');
    Route::post('/reset-index',         [LinkedInDashboardController::class, 'resetIndex'])->name('linkedin.reset-index');
    Route::get('/preview-post',         [LinkedInDashboardController::class, 'previewPost'])->name('linkedin.preview');
    Route::post('/publish-now',         [LinkedInDashboardController::class, 'publishNow'])->name('linkedin.publish-now');
    Route::post('/retry-dm/{lead}',     [LinkedInDashboardController::class, 'retryDM'])->name('linkedin.retry-dm');

    // OAuth
    Route::get('/connect',    [LinkedInAuthController::class, 'connect'])->name('linkedin.connect');
    Route::get('/callback',   [LinkedInAuthController::class, 'callback'])->name('linkedin.callback');
    Route::post('/disconnect', [LinkedInAuthController::class, 'disconnect'])->name('linkedin.disconnect');
});