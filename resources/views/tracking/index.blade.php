@extends('layouts.app')

@section('title', 'Lacak pesanan - '.config('shop.name'))

@section('content')
<div class="w" style="padding-bottom:64px;max-width:760px">
    <h1 class="page-title">Lacak pesanan</h1>

    <form class="cd" method="POST" action="{{ route('tracking.search') }}">
        @csrf
        <label class="l" for="order_code" style="margin-top:0">Kode pesanan</label>
        <input class="in" id="order_code" name="order_code" value="{{ old('order_code') }}" placeholder="BKP-20261123-0001" required>
        @error('order_code')<div class="err">{{ $message }}</div>@enderror

        <label class="l" for="phone">Nomor WhatsApp saat memesan</label>
        <input class="in" id="phone" name="phone" value="{{ old('phone') }}" placeholder="08xxxxxxxxxx" inputmode="tel" required>
        @error('phone')<div class="err">{{ $message }}</div>@enderror

        <button class="btn pu" style="margin-top:20px">Cari pesanan</button>
    </form>

    @isset($order)
        @php
            $flow = ['pending', 'confirmed', 'processing', 'ready', 'completed'];
            $at = array_search($order->status, $flow);
        @endphp
        <div class="cd" style="margin-top:32px">
            <div class="eyebrow" style="color:#C2185B;font-size:12px">{{ $order->order_code }} - {{ $order->pickup_location_name }}</div>
            <h3 style="margin:6px 0 4px">{{ $order->status_label }}</h3>

            @if ($order->status === 'cancelled')
                <p class="muted">Pesanan ini dibatalkan.</p>
            @else
                <div class="timeline">
                    @foreach ($flow as $i => $s)
                        <span class="{{ $at !== false && $i <= $at ? 'done' : '' }}">{{ \App\Models\Order::STATUSES[$s] }}</span>
                    @endforeach
                </div>
            @endif

            <table class="tb">
                <tr><th>MENU</th><th>JUMLAH</th><th class="r">SUBTOTAL</th></tr>
                @foreach ($order->items as $item)
                    <tr>
                        <td>
                            {{ $item->product_name }}
                            @if ($item->toppings_summary)<br><small style="color:var(--pu2)">+ {{ $item->toppings_summary }}</small>@endif
                        </td>
                        <td>{{ $item->qty }}</td>
                        <td class="r">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </table>
            <div class="total"><span style="font-weight:700">Total</span><b>{{ $order->total_rp }}</b></div>
        </div>
    @endisset
</div>
@endsection
