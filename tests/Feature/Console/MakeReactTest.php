<?php

use Illuminate\Support\Facades\File;

afterEach(function () {
    $filesToDelete = [
        resource_path('js/shared/components/test-widget.tsx'),
        resource_path('js/shared/components/use-test-widget.tsx'),
        resource_path('js/shared/components/ui/test-widget.tsx'),
        resource_path('js/shared/pages/test-widget.tsx'),
        resource_path('js/shared/layouts/test-widget.tsx'),
        resource_path('js/shared/hooks/use-test-widget.ts'),
        resource_path('js/templates/test-make-react/components/test-widget.tsx'),
        resource_path('js/templates/test-make-react/components/ui/test-widget.tsx'),
        resource_path('js/templates/test-make-react/pages/test-widget.tsx'),
        resource_path('js/templates/test-make-react/hooks/use-test-widget.ts'),
    ];

    foreach ($filesToDelete as $file) {
        if (File::exists($file)) {
            File::delete($file);
        }
    }

    if (File::isDirectory(resource_path('js/templates/test-make-react'))) {
        File::deleteDirectory(resource_path('js/templates/test-make-react'));
    }
});

// --- Path resolution ---

it('creates component in shared/components by default', function () {
    $this->artisan('make:react', ['name' => 'TestWidget'])
        ->assertSuccessful();

    expect(File::exists(resource_path('js/shared/components/test-widget.tsx')))->toBeTrue();
});

it('creates component in shared/components/ui with --ui', function () {
    $this->artisan('make:react', ['name' => 'TestWidget', '--ui' => true])
        ->assertSuccessful();

    expect(File::exists(resource_path('js/shared/components/ui/test-widget.tsx')))->toBeTrue();
});

it('creates component in shared/pages with --page', function () {
    $this->artisan('make:react', ['name' => 'TestWidget', '--page' => true])
        ->assertSuccessful();

    expect(File::exists(resource_path('js/shared/pages/test-widget.tsx')))->toBeTrue();
});

it('creates component in shared/layouts with --layout', function () {
    $this->artisan('make:react', ['name' => 'TestWidget', '--layout' => true])
        ->assertSuccessful();

    expect(File::exists(resource_path('js/shared/layouts/test-widget.tsx')))->toBeTrue();

    $content = File::get(resource_path('js/shared/layouts/test-widget.tsx'));
    expect($content)->toContain('function TestWidget');
    expect($content)->toContain('children: ReactNode');
});

it('creates hook in shared/hooks with --hook', function () {
    $this->artisan('make:react', ['name' => 'TestWidget', '--hook' => true])
        ->assertSuccessful();

    expect(File::exists(resource_path('js/shared/hooks/use-test-widget.ts')))->toBeTrue();
});

it('does not double-prefix use- when name starts with Use', function () {
    $this->artisan('make:react', ['name' => 'UseTestWidget', '--hook' => true])
        ->assertSuccessful();

    expect(File::exists(resource_path('js/shared/hooks/use-test-widget.ts')))->toBeTrue();
    expect(File::exists(resource_path('js/shared/hooks/use-use-test-widget.ts')))->toBeFalse();
});

// --- Template flag ---

it('creates component in template directory with --template', function () {
    File::ensureDirectoryExists(resource_path('js/templates/test-make-react'));

    $this->artisan('make:react', ['name' => 'TestWidget', '--template' => 'test-make-react'])
        ->assertSuccessful();

    expect(File::exists(resource_path('js/templates/test-make-react/components/test-widget.tsx')))->toBeTrue();
});

it('creates page in template directory with --template and --page', function () {
    File::ensureDirectoryExists(resource_path('js/templates/test-make-react'));

    $this->artisan('make:react', ['name' => 'TestWidget', '--template' => 'test-make-react', '--page' => true])
        ->assertSuccessful();

    expect(File::exists(resource_path('js/templates/test-make-react/pages/test-widget.tsx')))->toBeTrue();
});

it('creates hook in template directory with --template and --hook', function () {
    File::ensureDirectoryExists(resource_path('js/templates/test-make-react'));

    $this->artisan('make:react', ['name' => 'TestWidget', '--template' => 'test-make-react', '--hook' => true])
        ->assertSuccessful();

    expect(File::exists(resource_path('js/templates/test-make-react/hooks/use-test-widget.ts')))->toBeTrue();
});

// --- File contents ---

it('component file contains PascalCase component name', function () {
    $this->artisan('make:react', ['name' => 'TestWidget'])
        ->assertSuccessful();

    $content = File::get(resource_path('js/shared/components/test-widget.tsx'));
    expect($content)->toContain('function TestWidget');
    expect($content)->toContain('TestWidgetProps');
});

it('hook file contains correct function name', function () {
    $this->artisan('make:react', ['name' => 'TestWidget', '--hook' => true])
        ->assertSuccessful();

    $content = File::get(resource_path('js/shared/hooks/use-test-widget.ts'));
    expect($content)->toContain('function useTestWidget');
});

it('page file contains Head import and title', function () {
    $this->artisan('make:react', ['name' => 'TestWidget', '--page' => true])
        ->assertSuccessful();

    $content = File::get(resource_path('js/shared/pages/test-widget.tsx'));
    expect($content)->toContain("from '@inertiajs/react'");
    expect($content)->toContain('function TestWidget');
    expect($content)->toContain('<Head title="Test Widget"');
});

// --- Guards ---

it('fails when target file already exists', function () {
    File::ensureDirectoryExists(resource_path('js/shared/components'));
    File::put(resource_path('js/shared/components/test-widget.tsx'), 'existing content');

    $this->artisan('make:react', ['name' => 'TestWidget'])
        ->assertFailed();
});

it('fails when --template directory does not exist', function () {
    $this->artisan('make:react', ['name' => 'TestWidget', '--template' => 'non-existent-template'])
        ->expectsOutputToContain("Run 'php artisan make:template non-existent-template' first")
        ->assertFailed();
});

it('fails when multiple type flags are combined', function () {
    $this->artisan('make:react', ['name' => 'TestWidget', '--page' => true, '--ui' => true])
        ->assertFailed();
});

it('warns when name starts with use- but --hook is not passed', function () {
    $this->artisan('make:react', ['name' => 'UseTestWidget'])
        ->expectsOutputToContain('--hook was not passed')
        ->assertSuccessful();

    // Created as a component, not a hook
    expect(File::exists(resource_path('js/shared/components/use-test-widget.tsx')))->toBeTrue();
    expect(File::exists(resource_path('js/shared/hooks/use-test-widget.ts')))->toBeFalse();
});
