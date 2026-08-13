<?php

use App\Models\Product;

dataset('products', [
    [['name' => 'Product 1', 'price' => 123]],
    [['name' => 'Product 2', 'price' => 453]],
]);

dataset('create product', [
    fn() => Product::factory()->create(),
]);
