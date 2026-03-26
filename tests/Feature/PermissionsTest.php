<?php

use App\Shared\Models\User;
use Filament\Facades\Filament;
use Spatie\Permission\Models\Role;

it('can assign a role to a user', function () {
    $role = Role::create(['name' => 'admin']);
    $user = User::factory()->create();
    $user->assignRole($role);
    expect($user->hasRole('admin'))->toBeTrue();
});

it('admin user can access filament panel', function () {
    $role = Role::create(['name' => 'admin']);
    $user = User::factory()->create();
    $user->assignRole($role);
    $panel = Filament::getPanel('admin');
    expect($user->canAccessPanel($panel))->toBeTrue();
});

it('super_admin user can access filament panel', function () {
    $role = Role::create(['name' => 'super_admin']);
    $user = User::factory()->create();
    $user->assignRole($role);
    $panel = Filament::getPanel('admin');
    expect($user->canAccessPanel($panel))->toBeTrue();
});

it('user without roles cannot access filament panel', function () {
    $user = User::factory()->create();
    $panel = Filament::getPanel('admin');
    expect($user->canAccessPanel($panel))->toBeFalse();
});
