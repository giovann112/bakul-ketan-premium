@if (session('success') || session('error') || $errors->has('cart'))
    <div class="flash">
        @if (session('success'))<div class="ok">{{ session('success') }}</div>@endif
        @if (session('error'))<div class="bad">{{ session('error') }}</div>@endif
        @error('cart')<div class="bad">{{ $message }}</div>@enderror
    </div>
@endif
