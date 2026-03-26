<?php

use App\Shared\Models\User;

it('pulse config file exists', function () {
    expect(config_path('pulse.php'))->toBeFile();
});

it('pulse migration is in shared directory', function () {
    $migrations = glob(database_path('migrations/shared/*pulse*'));
    expect($migrations)->not->toBeEmpty();
});

it('unauthenticated user cannot access pulse dashboard', function () {
    $response = $this->get('/pulse');
    // Pulse uses its own Authorize middleware (not auth), so unauthenticated
    // users get a 403 directly rather than a redirect to login.
    $response->assertStatus(403);
});

it('authenticated user without matching email cannot access pulse dashboard', function () {
    $user = User::factory()->create(['email' => 'notadmin@example.com']);
    config(['pulse.admin_email' => 'admin@example.com']);
    $response = $this->actingAs($user)->get('/pulse');
    $response->assertStatus(403);
});

it('authenticated admin user with matching email can access pulse dashboard', function () {
    $user = User::factory()->create(['email' => 'admin@example.com']);
    config(['pulse.admin_email' => 'admin@example.com']);
    $response = $this->actingAs($user)->get('/pulse');
    $response->assertStatus(200);
});
