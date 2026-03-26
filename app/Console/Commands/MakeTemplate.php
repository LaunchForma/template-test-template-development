<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeTemplate extends Command
{
    protected $signature = 'make:template
                            {name : Template name (e.g. coffee-shop, CoffeeShop)}
                            {--pages= : Comma-separated page names to scaffold (e.g. Home,Services,Booking)}';

    protected $description = 'Scaffold a new template with all required directories and files';

    public function handle(): int
    {
        $studly = Str::studly($this->argument('name'));
        $kebab = Str::kebab($studly);

        if ($this->templateAlreadyRegistered($kebab)) {
            $this->error("Template '{$kebab}' is already registered in config/templates.php.");

            return self::FAILURE;
        }

        $created = [];

        $this->createPhpDirectories($studly);
        $this->createFilamentDirectories($studly);
        $this->createDatabaseDirectories($kebab);
        if (! $this->createRoutesFile($kebab, $created)) {
            return self::FAILURE;
        }
        $this->createViewsDirectory($kebab);
        $this->createFrontendDirectories($kebab);
        if (! $this->createTraitFile($studly, $created)) {
            return self::FAILURE;
        }
        if (! $this->createSeeder($studly, $created)) {
            return self::FAILURE;
        }
        if (! $this->createPageStubs($kebab, $created)) {
            return self::FAILURE;
        }
        if (! $this->registerInConfig($studly, $kebab)) {
            return self::FAILURE;
        }

        $this->printSuccess($kebab, $studly, $created);

        return self::SUCCESS;
    }

    protected function templateAlreadyRegistered(string $kebab): bool
    {
        $config = include config_path('templates.php');

        return array_key_exists($kebab, $config['templates'] ?? []);
    }

    protected function createPhpDirectories(string $studly): void
    {
        $dirs = [
            app_path("Templates/{$studly}/Http/Controllers"),
            app_path("Templates/{$studly}/Http/Requests"),
            app_path("Templates/{$studly}/Models"),
            app_path("Templates/{$studly}/Policies"),
            app_path("Templates/{$studly}/Services"),
        ];

        foreach ($dirs as $dir) {
            File::ensureDirectoryExists($dir);
        }
    }

    protected function createFilamentDirectories(string $studly): void
    {
        $dirs = [
            app_path("Filament/Templates/{$studly}/Resources"),
            app_path("Filament/Templates/{$studly}/Pages"),
            app_path("Filament/Templates/{$studly}/Widgets"),
        ];

        foreach ($dirs as $dir) {
            File::ensureDirectoryExists($dir);
            File::put("{$dir}/.gitkeep", '');
        }
    }

    protected function createDatabaseDirectories(string $kebab): void
    {
        $migrationDir = database_path("migrations/templates/{$kebab}");
        File::ensureDirectoryExists($migrationDir);
        File::put("{$migrationDir}/.gitkeep", '');
    }

    protected function createRoutesFile(string $kebab, array &$created): bool
    {
        $stubPath = base_path('stubs/template-route.stub');

        if (! File::exists($stubPath)) {
            $this->error('Stub not found: stubs/template-route.stub');

            return false;
        }

        $path = base_path("routes/templates/{$kebab}.php");
        File::put($path, str_replace('{{ kebab }}', $kebab, File::get($stubPath)));
        $created[] = $path;

        return true;
    }

    protected function createViewsDirectory(string $kebab): void
    {
        $dir = resource_path("views/templates/{$kebab}/emails");
        File::ensureDirectoryExists($dir);
        File::put("{$dir}/.gitkeep", '');
    }

    protected function createFrontendDirectories(string $kebab): void
    {
        $dirs = [
            resource_path("js/templates/{$kebab}/components"),
            resource_path("js/templates/{$kebab}/components/ui"),
            resource_path("js/templates/{$kebab}/layouts"),
            resource_path("js/templates/{$kebab}/pages"),
        ];

        foreach ($dirs as $dir) {
            File::ensureDirectoryExists($dir);
        }
    }

    protected function createTraitFile(string $studly, array &$created): bool
    {
        $stubPath = base_path('stubs/template-trait.stub');

        if (! File::exists($stubPath)) {
            $this->error('Stub not found: stubs/template-trait.stub');

            return false;
        }

        $path = app_path("Templates/{$studly}/Traits/Has{$studly}.php");
        File::ensureDirectoryExists(dirname($path));
        File::put($path, str_replace('{{ studly }}', $studly, File::get($stubPath)));
        $created[] = $path;

        return true;
    }

    protected function createSeeder(string $studly, array &$created): bool
    {
        $exitCode = $this->call('make:template-seeder', ['name' => "{$studly}Seeder"]);

        if ($exitCode !== self::SUCCESS) {
            $this->error("Failed to create seeder for template '{$studly}'.");

            return false;
        }

        $created[] = database_path("seeders/templates/{$studly}Seeder.php");

        return true;
    }

    protected function createPageStubs(string $kebab, array &$created): bool
    {
        if (! $pages = $this->option('pages')) {
            return true;
        }

        $stubPath = base_path('stubs/react-page.stub');

        if (! File::exists($stubPath)) {
            $this->error('Stub not found: stubs/react-page.stub');

            return false;
        }

        $stub = File::get($stubPath);

        foreach (array_map('trim', explode(',', $pages)) as $pageName) {
            $pageKebab = Str::kebab($pageName);
            $pagePascal = Str::studly($pageName);
            $pageTitle = Str::headline($pageName);
            $path = resource_path("js/templates/{$kebab}/pages/{$pageKebab}.tsx");

            $content = str_replace(
                ['{{ PascalName }}', '{{ Title }}'],
                [$pagePascal, $pageTitle],
                $stub,
            );

            File::put($path, $content);
            $created[] = $path;
        }

        return true;
    }

    protected function registerInConfig(string $studly, string $kebab): bool
    {
        $configPath = config_path('templates.php');
        $originalContent = File::get($configPath);
        $displayName = Str::headline($studly);

        $entryBlock = <<<PHP

        '{$kebab}' => [
            'name' => '{$displayName}', // TODO: Update display name if needed
            'user_traits' => ['Has{$studly}'],
            'user_fields' => [
                'fillable' => [
                    // TODO: Add template-specific User fillable fields here
                ],
                'casts' => [
                    // TODO: Add template-specific User casts here
                ],
            ],
            'migrations' => ['{$kebab}'],
            'seeders' => ['{$studly}Seeder'], // TODO: Register additional seeders if needed
        ],
PHP;

        // Inject the new entry before the closing of the templates array (4-space ], + final ];)
        $modified = preg_replace(
            '/(    \],\n\];\n?)$/',
            $entryBlock."\n    ],\n];\n",
            $originalContent,
        );

        if ($modified === null || $modified === $originalContent) {
            $this->error('Failed to update config/templates.php — could not locate injection point. Ensure the file ends with "    ],\n];\n".');

            return false;
        }

        File::put($configPath, $modified);

        try {
            $result = (static function (string $path): mixed {
                return include $path;
            })($configPath);

            if (! is_array($result) || ! isset($result['templates'][$kebab])) {
                throw new \RuntimeException('Config structure invalid after modification.');
            }
        } catch (\Throwable) {
            File::put($configPath, $originalContent);
            $this->error('Failed to update config/templates.php — original content restored.');

            return false;
        }

        return true;
    }

    protected function printSuccess(string $kebab, string $studly, array $created): void
    {
        $this->info("Template \"{$kebab}\" scaffolded successfully.");
        $this->newLine();
        $this->line('  Files created:');

        foreach ($created as $file) {
            $relative = str_replace(base_path().'/', '', $file);
            $this->line("  ✓ {$relative}");
        }

        $this->newLine();
        $this->line('  Next steps:');
        $this->line('  → Review config/templates.php — fill in user fields, casts, and seeders');
        $this->line("  → Add migrations: database/migrations/templates/{$kebab}/");
        $this->line("  → Register Filament resources in app/Filament/Templates/{$studly}/");
        $this->line("  → Run `php artisan make:react MyPage --template={$kebab} --page` to add pages");
    }
}
