<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Mail\ProductCreatedMail;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Notifications\ProductDeletedNotification;
use App\Jobs\SyncProductToExternalCatalog;

class ProductController extends Controller
{
    public function index(): View
    {
        $products = Product::paginate(10);

        return view('products.index', compact('products'));
    }

    public function create(): View
    {
        return view('products.create');
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $product = Product::create($request->validated());

        Mail::to(Auth::user()->email)->send(new ProductCreatedMail($product));

        SyncProductToExternalCatalog::dispatch($product);

        return redirect()->route('products.index');
    }

    public function edit(Product $product): View
    {
        return view('products.edit', compact('product'));
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $product->update($request->validated());

        return redirect()->route('products.index');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $productName = $product->name;
        $product->delete();

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->notify(new ProductDeletedNotification($productName));

        return redirect()->route('products.index');
    }
}
