@extends('layouts.app')

@section('content')
<section class="hero hero-photo" style="--hero-bg:url('{{ asset('images/booth-bg.jpg') }}')">
    <div class="hero-in">
        <div class="reveal">
            <div class="eyebrow">#KETANBISAKRIUK</div>
            <h1>Ketan premium, <span>pesan tanpa ribet.</span></h1>
            <p>Gold Cruncmilk, Soft Crumble, atau Klasik dengan ekstra topping favoritmu. Pesan online, ambil di Outlet atau Foodtruck.</p>
            <div class="hero-cta">
                <a href="{{ route('products.index') }}" class="btn go">Lihat Menu</a>
                <a href="https://wa.me/{{ config('shop.wa_number') }}" target="_blank" rel="noopener" class="btn ol">Chat WhatsApp</a>
            </div>
        </div>
        <div class="hero-visual">
            <div class="booth-frame">
                <img src="{{ asset('images/booth-bg.jpg') }}" alt="Bakul Ketan Premium" class="booth-photo">
                <div class="booth-logo-badge">
                    <img src="{{ asset('images/logo.png') }}" alt="Bakul Ketan Premium">
                </div>
            </div>
        </div>
    </div>
</section>

<div class="strip">
    <span>#KETANBISAKRIUK</span><span>BAKUL KETAN PREMIUM</span><span>OUTLET & FOODTRUCK</span><span>CUSTOM TOPPING MIX</span>
</div>

<section class="sec">
    <div class="w">
        <div class="sec-head">
            <div>
                <div class="eyebrow" style="color:var(--mg)">Menu Favorit</div>
                <h2>Pilihan Terlaris</h2>
            </div>
            <a href="{{ route('products.index') }}">Lihat semua menu</a>
        </div>
        <div class="grid3">
            @forelse ($featured as $product)
                @include('partials.product-card', ['product' => $product])
            @empty
                <p class="muted">Belum ada produk.</p>
            @endforelse
        </div>
    </div>
</section>

<section class="steps">
    <div class="steps-in">
        <h2>Cara Memesan</h2>
        <div class="steps-grid">
            <div><div class="num">01</div><h3>Pilih Lokasi</h3><p>Pilih ambil di <b>Outlet</b> atau <b>Foodtruck</b>.</p></div>
            <div><div class="num">02</div><h3>Pilih Menu & Topping</h3><p>Pilih menu ketan favorit dan tambahkan ekstra Topping Mix.</p></div>
            <div><div class="num">03</div><h3>Konfirmasi WhatsApp</h3><p>Kirim rincian pesanan ke WhatsApp kami lalu ambil pesananmu.</p></div>
        </div>
    </div>
</section>
@endsection
