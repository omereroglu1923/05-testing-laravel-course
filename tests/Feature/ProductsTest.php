<?php

use App\Models\Product;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

test('homepage contains empty table', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get('/products')
        ->assertStatus(200)
        ->assertSee(__('No products found'));
});

test('homepage contains non empty table', function () {
    $user = User::factory()->create();
    $product = Product::create([
        'name'  => 'Product 1',
        'price' => 123,
    ]);

    actingAs($user)
        ->get('/products')
        ->assertStatus(200)
        ->assertDontSee(__('No products found'))
        ->assertSee('Product 1')
        ->assertViewHas('products', function (LengthAwarePaginator $collection) use ($product) {
            return $collection->contains($product);
        });
});

test('paginated products table doesnt contain 11th record', function () {
    $user = User::factory()->create();
    $products = Product::factory(11)->create();
    $lastProduct = $products->last();

    actingAs($user)
        ->get('/products')
        ->assertStatus(200)
        ->assertViewHas('products', function (LengthAwarePaginator $collection) use ($lastProduct) {
            return $collection->doesntContain($lastProduct);
        });
});
