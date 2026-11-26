@php
    [$bg, $fg] = $product->panel;
    $isAvailable = \App\Support\StockPool::remainingPortionsFor() > 0;
@endphp
<div class="pc reveal">
    <a href="{{ route('products.show', $product->slug) }}" class="pi" style="background:{{ $bg }};color:{{ $fg }}">
        @if ($product->image_url)
            <img class="pimg" src="{{ $product->image_url }}" alt="{{ $product->name }}">
        @else
            <span class="tg">{{ strtoupper($product->category->name ?? 'KETAN PREMIUM') }}</span>
            <b>{{ $product->flavor }}</b>
        @endif
    </a>
    <div class="pf">
        <a class="pname" href="{{ route('products.show', $product->slug) }}">{{ $product->name }}</a>
        @if ($product->description)
            <div class="pdesc">{{ \Illuminate\Support\Str::limit($product->description, 72) }}</div>
        @endif
        <div class="prow">
            <span class="pr">{{ $product->price_rp }}</span>
            <span class="stk {{ $isAvailable ? '' : 'out' }}">
                {{ $isAvailable ? 'Tersedia' : 'Habis' }}
            </span>
        </div>
        <div class="card-actions">
            @if ($isAvailable)
                <button type="button"
                        class="btn pu sm block js-open-order-modal"
                        data-action="{{ route('cart.add', $product) }}"
                        data-name="{{ $product->name }}"
                        data-price="{{ $product->price }}"
                        data-price-rp="{{ $product->price_rp }}"
                        data-category="{{ $product->category->name ?? 'Ketan Premium' }}"
                        data-desc="{{ $product->description }}"
                        data-img="{{ $product->image_url }}"
                        data-bg="{{ $bg }}"
                        data-fg="{{ $fg }}">
                    Pesan Menu
                </button>
            @else
                <span class="btn off sm block">Habis Hari Ini</span>
            @endif
        </div>
    </div>
</div>
