@extends('layouts.app')

@section('title', $product->name.' - '.config('shop.name'))

@section('content')
@php
    [$bg, $fg] = $product->panel;
    $remPortions = $selectedLocation ? $selectedLocation->today_stock->remaining_portions : 0;
    $isAvailable = $remPortions > 0;
@endphp
<div class="w sec" style="padding-top:28px">
    <div style="margin-bottom:20px">
        <a href="{{ route('products.index') }}" class="btn line sm" style="display:inline-flex;align-items:center;gap:8px">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            <span>Kembali ke Semua Menu</span>
        </a>
    </div>

    <div class="thumb reveal">
        <div class="pi" style="background:{{ $bg }};color:{{ $fg }}">
            @if ($product->image_url)
                <img class="pimg" src="{{ $product->image_url }}" alt="{{ $product->name }}">
            @else
                <span class="tg">{{ strtoupper($product->category->name) }}</span><b>{{ $product->flavor }}</b>
            @endif
        </div>
        <div>
            <div class="eyebrow" style="color:#C2185B">{{ $product->category->name }}</div>
            <h1>{{ $product->name }}</h1>
            <span class="pr">{{ $product->price_rp }}</span>
            <p class="stk {{ $isAvailable ? '' : 'out' }}" style="display:inline-block">
                @if ($selectedLocation)
                    {{ $isAvailable ? "Tersedia di {$selectedLocation->name}" : "Habis di {$selectedLocation->name}" }}
                @else
                    Pilih titik pick up
                @endif
            </p>
            <p style="font-size:15.5px;margin-top:14px">{{ $product->description }}</p>

            @if ($isAvailable)
                <form method="POST" action="{{ route('cart.add', $product) }}" style="margin-top:20px" id="showOrderForm">
                    @csrf
                    @if ($toppings->isNotEmpty())
                        <label class="l">Mau pakai topping tambahan?</label>
                        <div class="topping-mode-switch" role="group" aria-label="Pilihan topping">
                            <button type="button" class="mode-btn is-on" data-mode="none">Tanpa Topping (Original)</button>
                            <button type="button" class="mode-btn" data-mode="custom">Pilih Topping Mix</button>
                        </div>

                        <div id="showToppingBox" style="display:none;margin-top:12px">
                            <div class="topping-grid">
                                @foreach ($toppings as $topping)
                                    <label class="top-opt">
                                        <span style="display:flex;align-items:center;gap:8px">
                                            <input type="checkbox" name="toppings[]" value="{{ $topping->id }}">
                                            <span>{{ $topping->name }}</span>
                                        </span>
                                        <small>{{ $topping->price_rp }}</small>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div style="display:flex;gap:12px;align-items:center;margin-top:24px">
                        <input class="in" style="width:88px;border-radius:999px;text-align:center;font-weight:700" type="number" name="qty" value="1" min="1" max="{{ min(20, $remPortions) }}" aria-label="Jumlah porsi">
                        <button class="btn pu" style="flex:1">Masukkan ke Keranjang</button>
                    </div>
                </form>
                <script>
                (function(){
                    const form = document.getElementById('showOrderForm');
                    if(!form) return;
                    const btns = form.querySelectorAll('.mode-btn');
                    const box = document.getElementById('showToppingBox');
                    btns.forEach(btn => {
                        btn.addEventListener('click', () => {
                            btns.forEach(b => b.classList.remove('is-on'));
                            btn.classList.add('is-on');
                            if(btn.dataset.mode === 'custom'){
                                box.style.display = 'block';
                            } else {
                                box.style.display = 'none';
                                box.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
                            }
                        });
                    });
                })();
                </script>
            @else
                <span class="btn off" style="margin-top:24px">Habis di Lokasi Ini Hari Ini</span>
            @endif
        </div>
    </div>
</div>
@endsection
