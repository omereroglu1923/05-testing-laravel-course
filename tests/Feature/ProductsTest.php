<?php

use App\Models\Product;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use App\Mail\ProductCreatedMail;
use Illuminate\Support\Facades\Mail;
use App\Notifications\ProductDeletedNotification;
use Illuminate\Support\Facades\Notification;
use App\Jobs\SyncProductToExternalCatalog;
use Illuminate\Support\Facades\Queue;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

pest()->group('products');

beforeEach(function () {
    /** @var \Tests\TestCase $this */
    $this->user = User::factory()->create();
});

test('homepage contains empty table', function () {
    actingAs($this->user)
        ->get('/products')
        ->assertStatus(200)
        ->assertSee(__('No products found'));
});

test('homepage contains non empty table', function () {
    Http::fake([
        'api.exchangerate-fake.test/*' => Http::response(['rate' => 0.98], 200),
    ]);

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
    Http::fake([
        'api.exchangerate-fake.test/*' => Http::response(['rate' => 0.98], 200),
    ]);

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

test('create product successful', function ($product) {
    asAdmin()
        ->post('/products', $product)
        ->assertStatus(302)
        ->assertRedirect('products');

    $this->assertDatabaseHas('products', $product);

    $lastProduct = Product::latest()->first();
    expect($product['name'])->toBe($lastProduct->name)
        ->and($product['price'])->toBe($lastProduct->price);
})->with('products');

test('product edit contains correct values', function ($product) {
    asAdmin()->get('products/' . $product->id . '/edit')
        ->assertStatus(200)
        ->assertSee('value="' . $product->name . '"', false)
        ->assertSee('value="' . $product->price . '"', false)
        ->assertViewHas('product', $product);
})->with('create product');

test('product update validation error redirects back to form', function ($product) {
    asAdmin()->put('products/' . $product->id, [
        'name' => '',
        'price' => ''
    ])
        ->assertStatus(302)
        ->assertInvalid(['name', 'price'])
        ->assertSessionHasErrors(['name', 'price']);
})->with('create product');

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

test('product created mail is sent to admin', function () {
    Mail::fake();

    $product = [
        'name' => 'Mailed Product',
        'price' => 555
    ];

    asAdmin()
        ->post('/products', $product)
        ->assertStatus(302);

    Mail::assertQueued(ProductCreatedMail::class, function ($mail) use ($product) {
        return $mail->product->name === $product['name'];
    });
});

test('product deleted notification is sent to admin', function () {
    Notification::fake();

    $product = Product::factory()->create();
    $admin = User::factory()->create(['is_admin' => true]);

    actingAs($admin)
        ->delete('products/' . $product->id)
        ->assertStatus(302);

    Notification::assertSentTo($admin, ProductDeletedNotification::class, function ($notification) use ($product) {
        return $notification->productName === $product->name;
    });
});

test('product creation dispatches sync job', function () {
    Queue::fake();

    $product = [
        'name' => 'Queued Product',
        'price' => 777
    ];

    asAdmin()
        ->post('/products', $product)
        ->assertStatus(302);

    Queue::assertPushed(SyncProductToExternalCatalog::class, function ($job) use ($product) {
        return $job->product->name === $product['name'];
    });
});

test('product image is stored on upload', function () {
    Storage::fake('public');

    $file = UploadedFile::fake()->image('product.jpg');

    $product = [
        'name' => 'Product With Image',
        'price' => 999,
        'image' => $file,
    ];

    asAdmin()
        ->post('/products', $product)
        ->assertStatus(302);

    $lastProduct = Product::latest()->first();

    $this->assertTrue(Storage::disk('public')->exists($lastProduct->image_path));
});

test('non admin cannot access product edit page', function () {
    $product = Product::factory()->create();

    actingAs($this->user)
        ->get('products/' . $product->id . '/edit')
        ->assertForbidden();
});

test('non admin cannot update product', function () {
    $product = Product::factory()->create();

    actingAs($this->user)
        ->put('products/' . $product->id, [
            'name' => 'Hacked Name',
            'price' => 1,
        ])
        ->assertForbidden();

    $this->assertDatabaseMissing('products', ['name' => 'Hacked Name']);
});

test('non admin cannot delete product', function () {
    $product = Product::factory()->create();

    actingAs($this->user)
        ->delete('products/' . $product->id)
        ->assertForbidden();

    $this->assertDatabaseHas('products', ['id' => $product->id]);
});
