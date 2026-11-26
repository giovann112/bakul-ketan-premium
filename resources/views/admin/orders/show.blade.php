@extends('layouts.admin')

@section('title', $order->order_code)

@section('content')
<div class="top">
    <div><div class="eyebrow">{{ $order->pickup_location_name }}</div><h1>{{ $order->order_code }}</h1></div>
    <span class="pill st-{{ $order->status }}" style="font-size:14px">{{ $order->status_label }}</span>
</div>

<div class="split" style="grid-template-columns:1.4fr 1fr">
    <div class="cd flat">
        <h3>Rincian</h3>
        <table class="tb">
            <tr><th>MENU & TOPPING</th><th>HARGA</th><th>PORSI</th><th class="r">SUBTOTAL</th></tr>
            @foreach ($order->items as $item)
                <tr>
                    <td>
                        <b>{{ $item->product_name }}</b>
                        @if ($item->toppings_summary)
                            <div style="font-size:12px;color:var(--pu2)">+ {{ $item->toppings_summary }}</div>
                        @endif
                    </td>
                    <td>Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                    <td>{{ $item->qty }}</td>
                    <td class="r">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </table>
        <div class="total"><span style="font-weight:700">Total</span><b>{{ $order->total_rp }}</b></div>
    </div>

    <div>
        <div class="cd flat">
            <h3>Pemesan</h3>
            <p style="margin:6px 0"><b>{{ $order->customer_name }}</b></p>
            <p style="margin:6px 0">{{ $order->phone }}</p>
            <p style="margin:6px 0">Cabang: <b>{{ $order->pickup_location_name }}</b></p>
            @if ($order->notes)<p class="muted" style="margin:6px 0">Catatan: {{ $order->notes }}</p>@endif
            @php
                $waAdminText = rawurlencode(
                    "Halo Kak {$order->customer_name}, update pesanan Anda di " . config('shop.name') . ":\n\n"
                    . "Kode Pesanan: {$order->order_code}\n"
                    . "Cabang Pick Up: {$order->pickup_location_name}\n"
                    . "Total: {$order->total_rp}\n"
                    . "Status Saat Ini: *{$order->status_label}*\n\n"
                    . "Cek detail pesanan di: " . route('tracking.index')
                );
            @endphp
            <a class="btn wa sm" style="margin-top:8px" target="_blank" rel="noopener" href="https://wa.me/{{ $order->wa_phone }}?text={{ $waAdminText }}">Kirim Update Status via WhatsApp</a>
        </div>

        <form class="cd flat" style="margin-top:20px" method="POST" action="{{ route('admin.orders.status', $order) }}">
            @csrf @method('PATCH')
            <h3>Ubah status</h3>
            @if ($order->status === 'cancelled')
                <p class="muted">Pesanan dibatalkan.</p>
            @else
                <select class="in" name="status" aria-label="Status pesanan">
                    @foreach (\App\Models\Order::STATUSES as $key => $label)
                        <option value="{{ $key }}" @selected($order->status === $key)>{{ $label }}</option>
                    @endforeach
                </select>
                <button class="btn pu" style="margin-top:14px">Simpan status</button>
            @endif
        </form>
    </div>
</div>
<p style="margin-top:20px"><a class="btn line sm" href="{{ route('admin.orders.index') }}">Kembali</a></p>
@endsection
