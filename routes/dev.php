<?php

use App\Shared\Http\Controllers\Dev\DevDashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->prefix('dev')->group(function () {
    Route::get('/', function () {
        return redirect()->route('dev.templates');
    })->name('dev.index');

    Route::get('/templates', [DevDashboardController::class, 'templates'])->name('dev.templates');
});
