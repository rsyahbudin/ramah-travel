<?php

use App\Models\User;
use Livewire\Volt\Volt;

test('guests are redirected to the login page', function () {
    $this->get(route('admin.users.index'))
        ->assertRedirect(route('login'));
});

test('non-admin users cannot access user management', function () {
    $user = User::factory()->create(['is_admin' => false]);
    $this->actingAs($user);

    $this->get(route('admin.users.index'))
        ->assertForbidden();
});

test('admin users can view the user list', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $this->actingAs($admin);

    $this->get(route('admin.users.index'))
        ->assertOk()
        ->assertSee($admin->name);
});

test('admin users can create a new user', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $this->actingAs($admin);

    Volt::test('admin.users.form')
        ->set('name', 'New User')
        ->set('email', 'new@example.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->set('is_admin', true)
        ->call('save')
        ->assertRedirect(route('admin.users.index'));

    $this->assertDatabaseHas('users', [
        'name' => 'New User',
        'email' => 'new@example.com',
        'is_admin' => true,
    ]);
});

test('admin users can edit an existing user', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $user = User::factory()->create(['name' => 'Old Name']);
    $this->actingAs($admin);

    Volt::test('admin.users.form', ['user' => $user->id])
        ->set('name', 'Updated Name')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('admin.users.index'));

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'Updated Name',
    ]);
});

test('admin users can delete a user', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $user = User::factory()->create();
    $this->actingAs($admin);

    Volt::test('admin.users.index')
        ->call('delete', $user)
        ->assertOk();

    $this->assertModelMissing($user);
});

test('admin users cannot delete themselves', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $this->actingAs($admin);

    Volt::test('admin.users.index')
        ->call('delete', $admin)
        ->assertOk();

    $this->assertModelExists($admin);
});
