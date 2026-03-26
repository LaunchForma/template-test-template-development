<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

if (app()->environment('local', 'testing')) {
    Route::get('/test', function () {
        return Inertia::render('test');
    })->name('test-template-development.home');
}
