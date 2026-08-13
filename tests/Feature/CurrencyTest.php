<?php

use App\Services\CurrencyService;
use Illuminate\Support\Facades\Http;

test('convert usd to eur successful', function () {
    Http::fake([
        'api.exchangerate-fake.test/*' => Http::response(['rate' => 0.98], 200),
    ]);

    $convertedCurrency = (new CurrencyService())->convert(100, 'usd', 'eur');

    expect($convertedCurrency)
        ->toBeFloat()
        ->toEqual(98.0);
});

test('convert usd to gbp returns zero when rate unavailable', function () {
    Http::fake([
        'api.exchangerate-fake.test/*' => Http::response(['rate' => null], 200),
    ]);

    $convertedCurrency = (new CurrencyService())->convert(100, 'usd', 'gbp');

    expect($convertedCurrency)
        ->toBeFloat()
        ->toEqual(0.0);
});
