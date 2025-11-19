<?php

use App\Http\Controllers\CrosswordController;

Route::get('/', [CrosswordController::class, 'show'])->name('crossword.show');

Route::post('/crossword/submit', [CrosswordController::class, 'submit'])->name('crossword.submit');

Route::get('/entries', [CrosswordController::class, 'index'])->name('crossword.index');
