<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeReact extends Command
{
    protected $signature = 'make:react
                            {name : Component name (PascalCase or kebab-case)}
                            {--template= : Target template slug (e.g. coffee-shop). Omit for shared.}
                            {--ui : Place in components/ui/}
                            {--page : Place in pages/}
                            {--layout : Place in layouts/}
                            {--hook : Place in hooks/ as a custom hook}';

    protected $description = 'Create a new React component, page, layout, or hook';

    public function handle(): int
    {
        if (! $this->validateFlags()) {
            return self::FAILURE;
        }

        $name = $this->argument('name');
        $template = $this->option('template');

        if ($template && ! $this->validateTemplate($template)) {
            return self::FAILURE;
        }

        $base = $template
            ? resource_path("js/templates/{$template}")
            : resource_path('js/shared');

        [$subdir, $filename, $stub, $componentName] = $this->resolveFileDetails($name);

        $targetPath = "{$base}/{$subdir}/{$filename}";

        if (File::exists($targetPath)) {
            $this->error("File already exists: {$targetPath}");

            return self::FAILURE;
        }

        if (! $this->option('hook') && Str::startsWith(Str::kebab($name), 'use-')) {
            $this->warn("Note: name starts with 'use-' but --hook was not passed. Created as a component. Use --hook for a hook file.");
        }

        $content = $this->buildContent($stub, $componentName, $name);

        if ($content === '') {
            return self::FAILURE;
        }

        File::ensureDirectoryExists(dirname($targetPath));
        File::put($targetPath, $content);

        $relativePath = str_replace(base_path().'/', '', $targetPath);
        $this->info('React component created successfully.');
        $this->newLine();
        $this->line("  ✓ {$relativePath}");
        $this->newLine();
        $this->line('  Import with:');
        $this->line('  '.$this->buildImportStatement($template, $subdir, $filename, $componentName));

        return self::SUCCESS;
    }

    protected function validateFlags(): bool
    {
        $typeFlags = array_filter([
            $this->option('ui'),
            $this->option('page'),
            $this->option('layout'),
            $this->option('hook'),
        ]);

        if (count($typeFlags) > 1) {
            $this->error('Flags --ui, --page, --layout, and --hook are mutually exclusive. Use only one.');

            return false;
        }

        return true;
    }

    protected function validateTemplate(string $template): bool
    {
        if (! File::isDirectory(resource_path("js/templates/{$template}"))) {
            $this->error("Template '{$template}' not found. Run 'php artisan make:template {$template}' first.");

            return false;
        }

        return true;
    }

    /**
     * @return array{string, string, string, string}
     */
    protected function resolveFileDetails(string $name): array
    {
        if ($this->option('hook')) {
            $kebab = Str::kebab($name);

            if (Str::startsWith($kebab, 'use-')) {
                $kebab = substr($kebab, 4);
            }

            $hookName = Str::studly($kebab);

            return ['hooks', "use-{$kebab}.ts", 'react-hook', $hookName];
        }

        $kebab = Str::kebab($name);
        $pascal = Str::studly($name);
        $filename = "{$kebab}.tsx";

        if ($this->option('page')) {
            return ['pages', $filename, 'react-page', $pascal];
        }

        if ($this->option('layout')) {
            return ['layouts', $filename, 'react-layout', $pascal];
        }

        if ($this->option('ui')) {
            return ['components/ui', $filename, 'react-component', $pascal];
        }

        return ['components', $filename, 'react-component', $pascal];
    }

    protected function buildContent(string $stub, string $componentName, string $originalName): string
    {
        $stubPath = base_path("stubs/{$stub}.stub");

        if (! File::exists($stubPath)) {
            $this->error("Stub not found: stubs/{$stub}.stub");

            return '';
        }

        $content = File::get($stubPath);
        $title = Str::headline($originalName);

        return str_replace(
            ['{{ PascalName }}', '{{ HookName }}', '{{ Title }}'],
            [Str::studly($originalName), $componentName, $title],
            $content,
        );
    }

    protected function buildImportStatement(?string $template, string $subdir, string $filename, string $componentName): string
    {
        $alias = $template ? "@templates/{$template}" : '@shared';
        $nameWithoutExt = pathinfo($filename, PATHINFO_FILENAME);

        if ($this->option('hook')) {
            return "import { use{$componentName} } from '{$alias}/{$subdir}/{$nameWithoutExt}';";
        }

        return "import {$componentName} from '{$alias}/{$subdir}/{$nameWithoutExt}';";
    }
}
