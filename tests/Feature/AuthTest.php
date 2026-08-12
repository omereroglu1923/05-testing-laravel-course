<?php

use function Pest\Laravel\get;
use App\Models\User;
use function Pest\Laravel\post;

test('unauthenticated user cannot access product', function () {
    get('/products')
        ->assertStatus(302)
        ->assertRedirect('login');
});

test('login redirects to dashboard', function () {
    User::factory()->create([
        'email' => 'user@user.com',
        'password' => 'password123',
    ]);

    post('/login', [
        'email' => 'user@user.com',
        'password' => 'password123',
    ])
        ->assertStatus(302)
        ->assertRedirect('dashboard');
});
