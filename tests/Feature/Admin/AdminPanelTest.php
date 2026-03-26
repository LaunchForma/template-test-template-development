<?php

use App\Shared\Models\User;
use Spatie\Permission\Models\Role;

test('guest is redirected from admin panel', function () {
    $response = $this->get('/admin');
    $response->assertRedirect();
});

test('authenticated user without admin access is blocked from admin panel', function () {
    $user = User::factory()->create();
    $response = $this->actingAs($user)->get('/admin');
    $response->assertStatus(403);
});

test('authenticated user with admin role can access admin panel', function () {
    $role = Role::create(['name' => 'admin']);
    $user = User::factory()->create();
    $user->assignRole($role);
    $response = $this->actingAs($user)->get('/admin');
    $response->assertStatus(200);
});
