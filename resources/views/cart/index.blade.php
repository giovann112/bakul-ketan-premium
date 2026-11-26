@extends('layouts.app')

@section('title', 'Keranjang - '.config('shop.name'))

@section('content')
<div class="w" style="padding-bottom:64px">
    <h1 class="page-title">Keranjang & Pick Up</h1>

    @if ($items->isEmpty())
        <div class="cd">
            <p>Keranjang masih kosong.</p>
            <a class="btn pu" href="{{ route('products.index') }}">Lihat Menu</a>
        </div>
    @else
        <div class="two">
            <div class="cd">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px">
                    <h3>Pesanan Kamu</h3>
                    <span class="pill st-confirmed">{{ $totalPortions }} porsi</span>
                </div>
                @foreach ($items as $item)
                    @php [$bg] = $item->product->panel; @endphp
                    <div class="row">
                        <div class="sw" style="background:{{ $bg }}">
                            @if ($item->product->image_url)<img src="{{ $item->product->image_url }}" alt="">@endif
                        </div>
                        <div>
                            <div style="font-weight:700">{{ $item->product->name }}</div>
                            @if ($item->toppings->isNotEmpty())
                                <div style="font-size:12px;color:var(--pu2);font-weight:700">
                                    + {{ $item->toppings->pluck('name')->implode(', ') }}
                                </div>
                            @endif
                            <div class="muted" style="font-size:13px">Rp {{ number_format($item->unit_price, 0, ',', '.') }} / porsi</div>
                        </div>
                        <div class="qty">
                            <form method="POST" action="{{ route('cart.update', $item->key) }}" class="qty">
                                @csrf @method('PATCH')
                                <input class="in" type="number" name="qty" value="{{ $item->qty }}" min="0" max="99" aria-label="Jumlah {{ $item->product->name }}">
                                <button class="btn line sm">Ubah</button>
                            </form>
                            <form method="POST" action="{{ route('cart.remove', $item->key) }}">
                                @csrf @method('DELETE')
                                <button class="btn sm" style="background:none;color:#C2185B">Hapus</button>
                            </form>
                        </div>
                        <div class="pr" style="font-size:18px">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</div>
                    </div>
                @endforeach
                <div class="total"><span style="font-weight:700">Total Bayar</span><b>Rp {{ number_format($total, 0, ',', '.') }}</b></div>
            </div>

            <form class="cd" method="POST" action="{{ route('checkout.store') }}">
                @csrf
                <h3>Data Pick Up</h3>

                <label class="l" for="location_id">Pilih Lokasi Pick Up</label>
                <select class="in" id="location_id" name="location_id" required>
                    @foreach ($locations as $loc)
                        <option value="{{ $loc->id }}" @selected(old('location_id', $selectedLocation?->id) == $loc->id)>
                            {{ $loc->name }} {{ $loc->today_stock->remaining_portions > 0 ? '(Tersedia)' : '(Habis Hari Ini)' }}
                        </option>
                    @endforeach
                </select>
                @error('location_id')<div class="err">{{ $message }}</div>@enderror

                <label class="l" for="customer_name">Nama Pemesan</label>
                <input class="in" id="customer_name" name="customer_name" value="{{ old('customer_name') }}" placeholder="Nama lengkap" required>
                @error('customer_name')<div class="err">{{ $message }}</div>@enderror

                <label class="l" for="phone">Nomor WhatsApp / HP</label>
                <input class="in" id="phone" name="phone" value="{{ old('phone') }}" placeholder="08xxxxxxxxxx" inputmode="tel" required>
                @error('phone')<div class="err">{{ $message }}</div>@enderror

                <label class="l" for="notes">Catatan / Jam Ambil (opsional)</label>
                <input class="in" id="notes" name="notes" value="{{ old('notes') }}" placeholder="Contoh: Diambil jam 16.30">
                @error('notes')<div class="err">{{ $message }}</div>@enderror

                <button class="btn pu block" style="margin-top:24px;padding:15px">Buat Pesanan Pick Up</button>
            </form>
        </div>
    @endif
</div>
@endsection
