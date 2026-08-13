<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class CurrencyService
{
    public function convert(float $amount, string $currencyFrom, string $currencyTo): float
    {
        try {
            $response = Http::timeout(3)->get('https://api.exchangerate-fake.test/rates', [
                'from' => $currencyFrom,
                'to' => $currencyTo,
            ]);
            $rate = $response->json('rate') ?? 0;
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            $rate = 0;
        }

        return round($amount * $rate, 2);
    }
}
