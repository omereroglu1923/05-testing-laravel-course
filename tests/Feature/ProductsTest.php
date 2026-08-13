<?php

use App\Models\Product;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('homepage contains empty table', function () {
    actingAs($this->user)
        ->get('/products')
        ->assertStatus(200)
        ->assertSee(__('No products found'));
});

test('homepage contains non empty table', function () {
    $product = Product::create([
        'name'  => 'Product 1',
        'price' => 123,
    ]);

    actingAs($this->user)
        ->get('/products')
        ->assertStatus(200)
        ->assertDontSee(__('No products found'))
        ->assertSee('Product 1')
        ->assertViewHas('products', function (LengthAwarePaginator $collection) use ($product) {
            return $collection->contains($product);
        });
});

test('paginated products table doesnt contain 11th record', function () {
    $products = Product::factory(11)->create();
    $lastProduct = $products->last();

    actingAs($this->user)
        ->get('/products')
        ->assertStatus(200)
        ->assertViewHas('products', function (LengthAwarePaginator $collection) use ($lastProduct) {
            return $collection->doesntContain($lastProduct);
        });
});

test('admin can see products create button', function () {
    asAdmin()
        ->get('/products')
        ->assertOk()
        ->assertSee('Add new product');
});

test('non admin cannot see products create button', function () {
    actingAs($this->user)
        ->get('/products')
        ->assertOk()
        ->assertDontSee('Add new product');
});

test('admin can access product create page', function () {
    asAdmin()
        ->get('/products/create')
        ->assertOk();
});

test('non admin cannot access product create page', function () {
    actingAs($this->user)
        ->get('/products/create')
        ->assertForbidden();
});

test('create product successful', function () {
    $product = [
        'name' => 'Product 123',
        'price' => 1234
    ];

    asAdmin()
        ->post('/products', $product)
        ->assertStatus(302)
        ->assertRedirect('products');

    $this->assertDatabaseHas('products', $product);

    $lastProduct = Product::latest()->first();
    expect($product['name'])->toBe($lastProduct->name)
        ->and($product['price'])->toBe($lastProduct->price);
});

test('product edit contains correct values', function () {
    $product = Product::factory()->create();

    asAdmin()->get('products/' . $product->id . '/edit')
        ->assertStatus(200)
        ->assertSee('value="' . $product->name . '"', false)
        ->assertSee('value="' . $product->price . '"', false)
        ->assertViewHas('product', $product);
});

test('product update validation error redirects back to form', function () {
    $product = Product::factory()->create();

    asAdmin()->put('products/' . $product->id, [
        'name' => '',
        'price' => ''
    ])
        ->assertStatus(302)
        ->assertInvalid(['name', 'price'])
        ->assertSessionHasErrors(['name', 'price']);
});

test('product delete successful', function () {
    $product = Product::factory()->create();

    asAdmin()
        ->delete('products/' . $product->id)
        ->assertStatus(302)
        ->assertRedirect('products');

    $this->assertDatabaseMissing('products', $product->toArray());
    $this->assertDatabaseCount('products', 0);

    $this->assertModelMissing($product);
    $this->assertDatabaseEmpty('products');
});

test('api returns products list', function () {
    $product = Product::factory()->create();

    $res = getJson('/api/products')
        ->assertJson([$product->toArray()]);

    expect($res->content())
        ->json()
        ->toHaveCount(1);
});

test('api product store successful', function () {
    $product = [
        'name' => 'Product 1',
        'price' => 123
    ];

    postJson('/api/products', $product)
        ->assertCreated()
        ->assertJson($product);
});

test('api product invalid store returns error', function () {
    $product = [
        'name' => '',
        'price' => 123
    ];

    postJson('/api/products', $product)
        ->assertUnprocessable();
});
