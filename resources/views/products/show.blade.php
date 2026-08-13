<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $product->name }}</title>
    @vite(['resources/css/app.css'])
</head>

<body class="bg-gray-100 p-8">
    <div class="max-w-3xl mx-auto">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">{{ $product->name }}</h2>
        <p class="text-gray-700">Price: ${{ number_format($product->price, 2) }}</p>
    </div>
</body>

</html>
