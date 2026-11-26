<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk Admin - {{ config('shop.name') }}</title>
    <link rel="icon" href="{{ asset('images/logo.png') }}">
    @include('partials.fonts')
    <link rel="stylesheet" href="{{ asset('css/bkp.css') }}">
</head>
<body>
<div class="login">
    <form class="cd" method="POST" action="{{ route('login.attempt') }}">
        @csrf
        <img src="{{ asset('images/logo.png') }}" alt="Logo {{ config('shop.name') }}">
        <h3 style="text-align:center">Masuk admin</h3>

        @if (session('error'))<div class="err" style="margin-top:12px">{{ session('error') }}</div>@endif

        <label class="l" for="email">Email</label>
        <input class="in" id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
        @error('email')<div class="err">{{ $message }}</div>@enderror

        <label class="l" for="password">Kata sandi</label>
        <input class="in" id="password" type="password" name="password" required>
        @error('password')<div class="err">{{ $message }}</div>@enderror

        <button class="btn pu block" style="margin-top:24px">Masuk</button>
    </form>
</div>
</body>
</html>
