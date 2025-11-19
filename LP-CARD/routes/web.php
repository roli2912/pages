<?php

use App\Http\Controllers\ContestController;
use App\Http\Controllers\ParticipantsViewController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ContestController::class, 'index'])->name('index');
Route::post('/flip-card', [ContestController::class, 'flipCard'])->name('flip-card');
Route::post('/reset', [ContestController::class, 'resetGame'])->name('reset');
Route::post('/register', [ContestController::class, 'register'])->name('register');
Route::get('/clear', function() {
    session()->forget('contest_game');
    return redirect('/')->with('success', 'Session cleared!');
});
Route::get('/view-participants', [ParticipantsViewController::class, 'index']);
