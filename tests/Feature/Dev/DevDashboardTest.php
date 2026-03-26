<?php

use App\Shared\Models\User;
use Illuminate\Support\Facades\Route;

test('dev index redirects to dev templates', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/dev');

    $response->assertRedirect(route('dev.templates'));
});

test('dev templates page can be rendered', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('dev.templates'));

    $response->assertStatus(200);
});

test('dev templates requires authentication', function () {
    $response = $this->get('/dev/templates');

    $response->assertRedirect(route('login'));
});

test('dev templates returns template metadata as inertia props', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('dev.templates'));

    $response->assertInertia(fn ($page) => $page
        ->component('dev/templates')
        ->has('templates')
        ->has('templates.0', fn ($template) => $template
            ->has('name')
            ->has('slug')
            ->has('entryRoute')
            ->has('entryUrl')
            ->has('routeCount')
            ->has('migrationCount')
            ->has('seederCount')
            ->has('traitCount')
            ->has('hasFilamentResources')
        )
    );
});

test('dev templates returns accurate metadata for test template', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('dev.templates'));

    $response->assertInertia(fn ($page) => $page
        ->has('templates.0', fn ($template) => $template
            ->where('name', 'Test Template Development')
            ->where('slug', 'test-template-development')
            ->where('entryRoute', 'test-template-development.home')
            ->where('routeCount', 1)
            ->where('migrationCount', 0)
            ->where('traitCount', 1)
            ->where('seederCount', 1)
            ->where('hasFilamentResources', false)
            ->etc()
        )
    );
});

test('dev templates handles missing entry_route gracefully', function () {
    $user = User::factory()->create();

    config(['templates.templates.no-route-template' => [
        'name' => 'No Route Template',
        'user_traits' => [],
        'user_fields' => ['fillable' => [], 'casts' => []],
        'migrations' => [],
        'seeders' => [],
    ]]);

    $response = $this->actingAs($user)->get(route('dev.templates'));

    $response->assertInertia(fn ($page) => $page
        ->has('templates', 2)
        ->has('templates.1', fn ($template) => $template
            ->where('name', 'No Route Template')
            ->where('entryRoute', null)
            ->where('entryUrl', null)
            ->etc()
        )
    );
});

test('dev routes are named correctly', function () {
    expect(Route::has('dev.index'))->toBeTrue();
    expect(Route::has('dev.templates'))->toBeTrue();
});

test('dev routes are not accessible in production environment', function () {
    // The dev routes are loaded during testing env (for test purposes),
    // but in production they would not be loaded at all.
    // We verify the conditional guard in web.php by checking that
    // the environment condition is correctly configured.
    $webRoutes = file_get_contents(base_path('routes/web.php'));

    expect($webRoutes)->toContain("app()->environment('local', 'testing')");
    expect($webRoutes)->toContain("require __DIR__.'/dev.php'");
});

test('isDev shared prop is present in authenticated inertia responses', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('dev.templates'));

    $response->assertInertia(fn ($page) => $page
        ->has('isDev')
        ->where('isDev', false)
    );
});

test('fortify home config has local env conditional', function () {
    $fortifyConfig = file_get_contents(base_path('config/fortify.php'));

    expect($fortifyConfig)->toContain("env('APP_ENV') === 'local' ? '/dev' : '/dashboard'");
});
