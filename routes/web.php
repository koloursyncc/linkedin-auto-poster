<?php

use Illuminate\Support\Facades\Route;

require __DIR__ . '/linkedin.php';
Route::get('/', function () {
    return view('welcome');
});