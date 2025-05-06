<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\PredictAnxietyController;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    Route::get('/anxiety-prediction', function () {
        return view('anxiety-prediction');
    })->name('anxiety-prediction');
});


Route::post('/prediction-result', [PredictAnxietyController::class, 'predictAnxiety'])->name('predictAnxiety');

Route::get('/prediction-result', function () {
    return view('prediction-result');
})->name('prediction-result');

