<?php

namespace App\Providers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->environment('local')) {
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerMigrationPaths();
        $this->registerViewNamespaces();

        Gate::define('viewPulse', function ($user) {
            return $user->email === config('pulse.admin_email');
        });
    }

    protected function registerMigrationPaths(): void
    {
        $this->loadMigrationsFrom(database_path('migrations/shared'));

        collect(File::directories(database_path('migrations/templates')))
            ->each(fn ($path) => $this->loadMigrationsFrom($path));
    }

    protected function registerViewNamespaces(): void
    {
        if (File::isDirectory(resource_path('views/templates'))) {
            $templateViewPaths = File::directories(resource_path('views/templates'));

            foreach ($templateViewPaths as $path) {
                $templateName = basename($path);
                view()->addNamespace($templateName, $path);
            }
        }
    }
}
