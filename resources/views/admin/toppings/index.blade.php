@extends('layouts.admin')

@section('title', 'Topping Mix')

@section('content')
<div class="top"><div><div class="eyebrow">Kelola</div><h1>Topping Mix</h1></div></div>

<div class="split" style="grid-template-columns:1.5fr 1fr">
    <div class="cd flat">
        <table class="tb">
            <tr><th>NAMA TOPPING</th><th>HARGA TAMBAHAN</th><th>AKTIF</th><th class="r">AKSI</th></tr>
            @forelse ($toppings as $topping)
                <tr>
                    <td colspan="3">
                        <form method="POST" action="{{ route('admin.toppings.update', $topping) }}" style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
                            @csrf @method('PUT')
                            <input class="in" style="padding:8px 12px;flex:1;min-width:140px" name="name" value="{{ $topping->name }}" required>
                            <input class="in" style="padding:8px 12px;width:110px" type="number" min="0" step="500" name="price" value="{{ $topping->price }}" required>
                            <label style="display:flex;align-items:center;gap:4px;font-size:13px;font-weight:700">
                                <input type="checkbox" name="is_active" value="1" @checked($topping->is_active)> Aktif
                            </label>
                            <button class="btn line sm">Simpan</button>
                        </form>
                    </td>
                    <td class="r">
                        <form method="POST" action="{{ route('admin.toppings.destroy', $topping) }}" onsubmit="return confirm('Hapus topping ini?')">
                            @csrf @method('DELETE')
                            <button class="btn red sm">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="muted">Belum ada topping.</td></tr>
            @endforelse
        </table>
    </div>

    <form class="cd flat" method="POST" action="{{ route('admin.toppings.store') }}">
        @csrf
        <h3>Tambah Topping</h3>
        <label class="l" for="tname">Nama Topping</label>
        <input class="in" id="tname" name="name" placeholder="Contoh: Keju Mozzarella" required>
        @error('name')<div class="err">{{ $message }}</div>@enderror

        <label class="l" for="tprice">Harga Tambahan (Rp)</label>
        <input class="in" id="tprice" type="number" min="0" step="500" name="price" value="4000" required>
        @error('price')<div class="err">{{ $message }}</div>@enderror

        <button class="btn pu" style="margin-top:20px">Tambah Topping</button>
    </form>
</div>
@endsection
