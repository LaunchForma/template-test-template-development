<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

if (File::isDirectory(base_path('routes/templates'))) {
    $templateRoutes = File::files(base_path('routes/templates'));

    foreach ($templateRoutes as $routeFile) {
        Route::middleware(['web'])
            ->group($routeFile->getPathname());
    }
}

Route::get('/', function () {
    return Inertia::render('welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');
});

require __DIR__.'/settings.php';
require __DIR__.'/shared.php';

if (app()->environment('local', 'testing')) {
    require __DIR__.'/dev.php';
}
