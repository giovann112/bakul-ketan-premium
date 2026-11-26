@extends('layouts.app')

@section('title', 'Pesanan berhasil - '.config('shop.name'))

@section('content')
<div class="w" style="padding-bottom:64px">
    <h1 class="page-title">Pesanan tercatat</h1>

    <div class="banner" style="margin-top:0">
        <div>
            <div class="eyebrow">Kode pesanan - {{ $order->pickup_location_name }}</div>
            <h2>{{ $order->order_code }}</h2>
            <div style="margin-top:4px;font-size:13.5px;color:#E6D4F7">
                Status: <strong style="color:var(--go)">{{ $order->status_label }}</strong>
            </div>
        </div>
        <a href="{{ $waLink }}" target="_blank" rel="noopener" class="btn wa">Konfirmasi via WhatsApp</a>
    </div>

    <div class="cd" style="margin-top:28px">
        <h3>Rincian pesanan</h3>
        <table class="tb">
            <tr><th>MENU & TOPPING</th><th>JUMLAH</th><th class="r">SUBTOTAL</th></tr>
            @foreach ($order->items as $item)
                <tr>
                    <td>
                        <b>{{ $item->product_name }}</b>
                        @if ($item->toppings_summary)
                            <div style="font-size:12px;color:var(--pu2)">+ {{ $item->toppings_summary }}</div>
                        @endif
                    </td>
                    <td>{{ $item->qty }} porsi</td>
                    <td class="r">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </table>
        <div class="total"><span style="font-weight:700">Total</span><b>{{ $order->total_rp }}</b></div>
        <p class="muted" style="margin-top:16px">
            Pemesan: <b>{{ $order->customer_name }}</b> ({{ $order->phone }})<br>
            Cabang: <b>{{ $order->pickup_location_name }}</b>
            @if ($order->location && $order->location->address) - {{ $order->location->address }} @endif
        </p>
        <a class="btn line" href="{{ route('tracking.index') }}">Lacak pesanan</a>
    </div>
</div>
@endsection
