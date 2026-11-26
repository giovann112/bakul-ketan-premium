@extends('layouts.admin')

@section('title', 'Menu Ketan')

@section('content')
<div class="top">
    <div><div class="eyebrow">Kelola</div><h1>Menu Ketan</h1></div>
    <div style="display:flex;gap:12px;flex-wrap:wrap">
        <form method="GET" style="display:flex;gap:8px;flex-wrap:wrap">
            <select class="in" style="border-radius:999px;width:210px" name="category_id" onchange="this.form.submit()">
                <option value="">Semua Kategori</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}" @selected(request('category_id') == $cat->id)>{{ $cat->name }}</option>
                @endforeach
            </select>
            <input class="in" style="border-radius:999px;width:220px" name="q" value="{{ request('q') }}" placeholder="Cari nama menu">
        </form>
        <a href="{{ route('admin.products.create') }}" class="btn pu">+ Tambah Menu</a>
    </div>
</div>

<div class="cd flat">
    <table class="tb">
        <tr><th>FOTO</th><th>NAMA MENU</th><th>KATEGORI</th><th>HARGA</th><th>STATUS</th><th class="r">AKSI</th></tr>
        @forelse ($products as $product)
            @php [$bg] = $product->panel; @endphp
            <tr>
                <td>
                    @if ($product->image_url)<img class="thumb-sm" src="{{ $product->image_url }}" alt="">
                    @else<span class="thumb-sm" style="background:{{ $bg }}"></span>@endif
                </td>
                <td style="font-weight:700">{{ $product->name }}</td>
                <td>{{ $product->category->name }}</td>
                <td style="font-weight:700;color:var(--pu2)">{{ $product->price_rp }}</td>
                <td><span class="pill {{ $product->is_active ? 'st-completed' : 'st-processing' }}">{{ $product->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                <td class="r">
                    <a class="btn line sm" href="{{ route('admin.products.edit', $product) }}">Ubah Harga / Foto</a>
                    <form method="POST" action="{{ route('admin.products.destroy', $product) }}" style="display:inline" onsubmit="return confirm('Hapus menu ini?')">
                        @csrf @method('DELETE')
                        <button class="btn red sm">Hapus</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="muted">Belum ada menu.</td></tr>
        @endforelse
    </table>
    {{ $products->links('vendor.pagination.bkp') }}
</div>
@endsection
