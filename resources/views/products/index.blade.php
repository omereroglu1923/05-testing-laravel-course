<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Products</title>
    @vite(['resources/css/app.css'])
</head>

<body class="bg-gray-100 p-8">
    <div class="max-w-3xl mx-auto">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">{{ __('Products') }}</h2>

        @if (auth()->user()->is_admin)
            <a href="{{ route('products.create') }}"
                class="block w-max px-4 py-2 text-sm bg-slate-400 hover:bg-slate-400/80 rounded-xl text-gray-900 mb-4">
                Add new product
            </a>
        @endif

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price
                            (EUR)</th>
                        @if (auth()->user()->is_admin)
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            </th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($products as $product)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $product->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">${{ number_format($product->price, 2) }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $product->price_eur }}</td>
                            @if (auth()->user()->is_admin)
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('products.edit', $product) }}"
                                            class="px-3 py-1 text-sm bg-slate-400 hover:bg-slate-400/80 rounded-sm text-gray-900">
                                            Edit
                                        </a>
                                        <form action="{{ route('products.destroy', $product) }}" method="POST"
                                            class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('Are you sure?')"
                                                class="px-3 py-1 text-sm bg-red-600 hover:bg-red-600/80 rounded-sm text-white">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="px-6 py-4 text-sm text-gray-500">{{ __('No products found') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>
