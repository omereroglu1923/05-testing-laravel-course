<?php

use App\Models\Product;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

pest()->group('products');

test('api returns products list', function () {
    Sanctum::actingAs(User::factory()->create());

    $product = Product::factory()->create();

    $res = getJson('/api/products')
        ->assertJson([$product->toArray()]);

    expect($res->content())
        ->json()
        ->toHaveCount(1);
});

test('api product store successful', function () {
    Sanctum::actingAs(User::factory()->create());

    $product = [
        'name' => 'Product 1',
        'price' => 123
    ];

    postJson('/api/products', $product)
        ->assertCreated()
        ->assertJson($product);
});

test('api product invalid store returns error', function () {
    Sanctum::actingAs(User::factory()->create());

    $product = [
        'name' => '',
        'price' => 123
    ];

    postJson('/api/products', $product)
        ->assertUnprocessable();
});

test('api product show route does not exist', function () {
    $product = Product::factory()->create();

    getJson('/api/products/' . $product->id)
        ->assertStatus(404);
});

test('api product store requires authentication', function () {
    postJson('/api/products', ['name' => 'Product 1', 'price' => 123])
        ->assertUnauthorized();
});
