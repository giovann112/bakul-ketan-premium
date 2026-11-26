@extends('layouts.app')

@section('title', 'Menu - '.config('shop.name'))

@section('content')
<section class="band">
    <div class="band-in">
        <div>
            <div class="eyebrow">#JagonyaKetan</div>
            <h1>Semua <span>menu.</span></h1>
        </div>
        <form method="GET" action="{{ route('products.index') }}" style="width:100%;max-width:340px">
            @if (request('kategori'))<input type="hidden" name="kategori" value="{{ request('kategori') }}">@endif
            <input class="search" name="q" value="{{ request('q') }}" placeholder="Cari ketan durian, oreo, matcha..." aria-label="Cari produk">
        </form>
    </div>
</section>

<div class="w" style="padding-top:32px;padding-bottom:64px">
    <div class="chips">
        <a class="btn {{ request('kategori') ? 'line' : 'ink' }}" href="{{ route('products.index', array_filter(['q' => request('q')])) }}">Semua</a>
        @foreach ($categories as $category)
            <a class="btn {{ request('kategori') === $category->slug ? 'ink' : 'line' }}"
               href="{{ route('products.index', array_filter(['kategori' => $category->slug, 'q' => request('q')])) }}">{{ $category->name }}</a>
        @endforeach
        <span class="count">{{ $products->total() }} menu</span>
    </div>

    <div class="grid3">
        @forelse ($products as $product)
            @include('partials.product-card', ['product' => $product])
        @empty
            <p class="muted">Menu tidak ditemukan.</p>
        @endforelse
    </div>

    {{ $products->links('vendor.pagination.bkp') }}
</div>
@endsection
