@component('mail::message')
    # New Product Created

    **{{ $product->name }}** was added at ${{ number_format($product->price, 2) }}.

    Thanks,<br>
    {{ config('app.name') }}
@endcomponent
