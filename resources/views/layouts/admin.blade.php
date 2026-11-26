<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin') - {{ config('shop.name') }}</title>
    <link rel="icon" href="{{ asset('images/logo.png') }}">
    @include('partials.fonts')
    <link rel="stylesheet" href="{{ asset('css/bkp.css') }}">
</head>
<body>
<div class="adm">
    <aside class="side">
        <div class="who">
            <img class="lg" src="{{ asset('images/logo.png') }}" alt="Logo {{ config('shop.name') }}">
            <div>
                <strong>Admin Panel</strong>
                <div style="font-size:12px;color:var(--go)">{{ auth()->user()->name }}</div>
            </div>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'on' : '' }}">Dashboard</a>
        <a href="{{ route('admin.stocks.index') }}" class="{{ request()->routeIs('admin.stocks.*') ? 'on' : '' }}">Stok Ketan (2 Cabang)</a>
        <a href="{{ route('admin.products.index') }}" class="{{ request()->routeIs('admin.products.*') ? 'on' : '' }}">Menu Ketan</a>
        <a href="{{ route('admin.toppings.index') }}" class="{{ request()->routeIs('admin.toppings.*') ? 'on' : '' }}">Topping Mix</a>
        <a href="{{ route('admin.categories.index') }}" class="{{ request()->routeIs('admin.categories.*') ? 'on' : '' }}">Kategori</a>
        <a href="{{ route('admin.orders.index') }}" class="{{ request()->routeIs('admin.orders.*') ? 'on' : '' }}">Pesanan</a>
        <a href="{{ route('home') }}" style="margin-top:12px;border:1.5px solid rgba(253,184,19,.45);color:var(--go)">Ke Website Publik</a>
        <form method="POST" action="{{ route('logout') }}" class="out">
            @csrf
            <button type="submit">Keluar</button>
        </form>
    </aside>

    <main class="main">
        @if (session('success') || session('error'))
            <div class="flash" style="padding:0;margin:0 0 20px">
                @if (session('success'))<div class="ok">{{ session('success') }}</div>@endif
                @if (session('error'))<div class="bad">{{ session('error') }}</div>@endif
            </div>
        @endif
        @yield('content')
    </main>
</div>
</body>
</html>
