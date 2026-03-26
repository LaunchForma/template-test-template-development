<?php

namespace App\Shared\Http\Controllers\Dev;

use App\Shared\Http\Controllers\Controller;
use Illuminate\Routing\Route as RouteInstance;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class DevDashboardController extends Controller
{
    public function templates(): Response
    {
        $templates = collect(config('templates.templates', []))
            ->map(fn (array $config, string $slug) => $this->gatherTemplateMetadata($slug, $config))
            ->values()
            ->all();

        return Inertia::render('dev/templates', [
            'templates' => $templates,
        ]);
    }

    /**
     * @param  array<string, mixed>  $config
     * @return array<string, mixed>
     */
    private function gatherTemplateMetadata(string $slug, array $config): array
    {
        $entryRoute = $config['entry_route'] ?? null;
        $entryUrl = null;

        if ($entryRoute && Route::has($entryRoute)) {
            $entryUrl = route($entryRoute);
        }

        return [
            'name' => $config['name'] ?? Str::headline($slug),
            'slug' => $slug,
            'entryRoute' => $entryRoute,
            'entryUrl' => $entryUrl,
            'routeCount' => $this->countTemplateRoutes($slug),
            'migrationCount' => $this->countMigrationFiles($slug),
            'seederCount' => count($config['seeders'] ?? []),
            'traitCount' => count($config['user_traits'] ?? []),
            'hasFilamentResources' => $this->hasFilamentResources($slug),
        ];
    }

    private function countTemplateRoutes(string $slug): int
    {
        $prefix = $slug . '.';

        return collect(Route::getRoutes()->getRoutes())
            ->filter(fn (RouteInstance $route) => str_starts_with($route->getName() ?? '', $prefix))
            ->count();
    }

    private function countMigrationFiles(string $slug): int
    {
        $path = database_path("migrations/templates/{$slug}");

        if (! File::isDirectory($path)) {
            return 0;
        }

        return count(File::files($path));
    }

    private function hasFilamentResources(string $slug): bool
    {
        $studlyName = Str::studly($slug);
        $path = app_path("Filament/Templates/{$studlyName}/Resources");

        if (! File::isDirectory($path)) {
            return false;
        }

        $files = File::files($path);

        return collect($files)->contains(fn ($file) => $file->getFilename() !== '.gitkeep');
    }
}
