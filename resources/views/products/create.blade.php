<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create Product</title>
    @vite(['resources/css/app.css'])
</head>

<body class="bg-gray-100 p-8">
    <div class="max-w-3xl mx-auto">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">{{ __('Create Product') }}</h2>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <form method="POST" action="{{ route('products.store') }}" class="p-6">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">{{ __('Name') }}</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                        class="mt-1 px-3 py-2 block w-full rounded-md border border-gray-300 focus:border-slate-400 text-sm text-gray-900" />
                    @error('name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-4">
                    <label for="price" class="block text-sm font-medium text-gray-700">{{ __('Price') }}</label>
                    <input id="price" type="text" name="price" value="{{ old('price') }}" required
                        class="mt-1 px-3 py-2 block w-full rounded-md border border-gray-300 focus:border-slate-400 text-sm text-gray-900" />
                    @error('price')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center mt-4">
                    <button type="submit"
                        class="px-4 py-2 text-sm bg-slate-400 hover:bg-slate-400/80 rounded-xl text-gray-900">
                        {{ __('Save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>
