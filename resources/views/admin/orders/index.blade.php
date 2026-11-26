@extends('layouts.admin')

@section('title', 'Pesanan')

@section('content')
<div class="top"><div><div class="eyebrow">Kelola</div><h1>Pesanan</h1></div></div>

<div class="filters">
    <a href="{{ route('admin.orders.index') }}" class="{{ !request('status') && !request('location_id') ? 'on' : '' }}">Semua</a>
    @foreach ($locations as $loc)
        <a href="{{ route('admin.orders.index', ['location_id' => $loc->id]) }}" class="{{ request('location_id') == $loc->id ? 'on' : '' }}">{{ $loc->name }}</a>
    @endforeach
    @foreach (\App\Models\Order::STATUSES as $key => $label)
        <a href="{{ route('admin.orders.index', ['status' => $key]) }}" class="{{ request('status') === $key ? 'on' : '' }}">{{ $label }}</a>
    @endforeach
</div>

<div class="cd flat">
    <table class="tb">
        <tr><th>KODE</th><th>TITIK PICK UP</th><th>PEMESAN</th><th>PORSI</th><th>TOTAL</th><th class="r">STATUS</th></tr>
        @forelse ($orders as $order)
            <tr>
                <td><a href="{{ route('admin.orders.show', $order) }}" style="font-weight:700">{{ $order->order_code }}</a></td>
                <td><b>{{ $order->pickup_location_name }}</b></td>
                <td>{{ $order->customer_name }}<br><small class="muted">{{ $order->created_at->format('d/m H:i') }}</small></td>
                <td>{{ $order->items_count }} item</td>
                <td>{{ $order->total_rp }}</td>
                <td class="r"><span class="pill st-{{ $order->status }}">{{ $order->status_label }}</span></td>
            </tr>
        @empty
            <tr><td colspan="6" class="muted">Belum ada pesanan.</td></tr>
        @endforelse
    </table>
    {{ $orders->links('vendor.pagination.bkp') }}
</div>
@endsection
