@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="top">
    <div>
        <h1>Dashboard</h1>
    </div>
    <div style="display:flex;gap:10px;flex-wrap:wrap">
        <a href="{{ route('admin.stocks.index') }}" class="btn go">Atur Stok Cabang</a>
        <a href="{{ route('admin.products.create') }}" class="btn pu">+ Tambah Menu</a>
    </div>
</div>

<div class="stats">
    <div class="stat p money"><small>PENDAPATAN BULAN INI</small><b>Rp {{ number_format($stats['revenue_month'], 0, ',', '.') }}</b></div>
    <div class="stat g"><small>MENUNGGU KONFIRMASI</small><b>{{ $stats['orders_pending'] }}</b></div>
    <div class="stat"><small>TOTAL PESANAN</small><b>{{ $stats['orders_total'] }}</b></div>
    <div class="stat m"><small>SISA STOK HARI INI</small><b>{{ $stats['remaining_portions'] }} <span style="font-size:16px">porsi</span></b></div>
</div>

<div class="split" style="grid-template-columns:1fr 1fr">
    @foreach ($locations as $loc)
        @php
            $st = $loc->today_stock;
            $pct = $st->total_portions > 0 ? round($st->remaining_portions / $st->total_portions * 100) : 0;
        @endphp
        <div class="cd flat">
            <div style="display:flex;justify-content:space-between;align-items:center">
                <div>
                    <div class="eyebrow" style="color:var(--pu2);font-size:11px">{{ strtoupper($loc->type) }}</div>
                    <h3 style="font-size:22px">{{ $loc->name }}</h3>
                </div>
                <a href="{{ route('admin.stocks.index') }}" class="btn line sm">Kelola Stok</a>
            </div>
            <div style="margin:14px 0 8px;display:flex;justify-content:space-between;font-weight:700;font-size:14px">
                <span>Sisa: {{ $st->remaining_portions }} / {{ $st->total_portions }} porsi ({{ $st->remaining_kg }} kg)</span>
                <span>{{ $pct }}%</span>
            </div>
            <div class="bar2" style="height:12px"><i style="width:{{ $pct }}%;height:12px;background:{{ $pct <= 20 ? '#C2185B' : '#6A1B9A' }}"></i></div>
        </div>
    @endforeach
</div>

<div class="split">
    <div class="cd flat">
        <div style="font-weight:700;margin-bottom:18px">Pendapatan 30 hari terakhir</div>
        <div class="chart">
            @foreach ($series as $day)
                <i class="{{ $day['revenue'] > 0 && $day['revenue'] === $maxRevenue ? 'hi' : '' }}"
                   style="height:{{ max(1, round($day['revenue'] / $maxRevenue * 100)) }}%"
                   title="{{ $day['date']->format('d/m') }}: Rp {{ number_format($day['revenue'], 0, ',', '.') }}"></i>
            @endforeach
        </div>
        <div class="axis">
            <span>{{ $series->first()['date']->format('d/m/Y') }}</span>
            <span>{{ $series->last()['date']->format('d/m/Y') }}</span>
        </div>
    </div>

    <div class="cd flat">
        <div style="font-weight:700;margin-bottom:18px">Menu terlaris</div>
        @php $maxQty = max(1, (int) $topProducts->max('total_qty')); @endphp
        @forelse ($topProducts as $i => $row)
            <div class="top5">
                <span class="n">{{ $i + 1 }}</span>
                <div>
                    <div style="font-weight:700;font-size:14px">{{ $row->product_name }}</div>
                    <div class="bar2"><i style="width:{{ round($row->total_qty / $maxQty * 100) }}%"></i></div>
                </div>
                <b>{{ $row->total_qty }}</b>
            </div>
        @empty
            <p class="muted">Belum ada penjualan.</p>
        @endforelse
    </div>
</div>

<div class="cd flat" style="margin-top:20px">
    <div style="font-weight:700;margin-bottom:6px">Pesanan terbaru</div>
    <table class="tb">
        <tr><th>KODE</th><th>CABANG</th><th>PEMESAN</th><th>TOTAL</th><th class="r">STATUS</th></tr>
        @forelse ($recent as $order)
            <tr>
                <td><a href="{{ route('admin.orders.show', $order) }}" style="font-weight:700">{{ $order->order_code }}</a></td>
                <td>{{ $order->pickup_location_name }}</td>
                <td>{{ $order->customer_name }}</td>
                <td>{{ $order->total_rp }}</td>
                <td class="r"><span class="pill st-{{ $order->status }}">{{ $order->status_label }}</span></td>
            </tr>
        @empty
            <tr><td colspan="5" class="muted">Belum ada pesanan.</td></tr>
        @endforelse
    </table>
</div>
@endsection
