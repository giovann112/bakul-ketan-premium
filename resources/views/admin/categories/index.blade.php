@extends('layouts.admin')

@section('title', 'Kategori')

@section('content')
<div class="top"><div><div class="eyebrow">Kelola</div><h1>Kategori</h1></div></div>

<div class="split" style="grid-template-columns:1.4fr 1fr">
    <div class="cd flat">
        <table class="tb">
            <tr><th>NAMA</th><th>PRODUK</th><th class="r">AKSI</th></tr>
            @forelse ($categories as $category)
                <tr>
                    <td>
                        <form method="POST" action="{{ route('admin.categories.update', $category) }}" style="display:flex;gap:8px">
                            @csrf @method('PUT')
                            <input class="in" style="padding:8px 12px" name="name" value="{{ $category->name }}" aria-label="Nama kategori" required>
                            <button class="btn line sm">Simpan</button>
                        </form>
                    </td>
                    <td>{{ $category->products_count }}</td>
                    <td class="r">
                        <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('Hapus kategori ini?')">
                            @csrf @method('DELETE')
                            <button class="btn red sm">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="3" class="muted">Belum ada kategori.</td></tr>
            @endforelse
        </table>
    </div>

    <form class="cd flat" method="POST" action="{{ route('admin.categories.store') }}">
        @csrf
        <h3>Tambah kategori</h3>
        <label class="l" for="name">Nama kategori</label>
        <input class="in" id="name" name="name" value="{{ old('name') }}" required>
        @error('name')<div class="err">{{ $message }}</div>@enderror
        <button class="btn pu" style="margin-top:20px">Tambah</button>
    </form>
</div>
@endsection
