@extends('layouts.admin')

@section('title', $product->exists ? 'Ubah Menu' : 'Tambah Menu')

@section('content')
<div class="top">
    <div><h1>{{ $product->exists ? 'Ubah Menu' : 'Tambah Menu' }}</h1></div>
</div>

<form class="cd flat" method="POST" enctype="multipart/form-data"
      action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}">
    @csrf
    @if ($product->exists) @method('PUT') @endif

    <div class="formgrid">
        <div>
            <label class="l" for="category_id" style="margin-top:0">Kategori Menu</label>
            <select class="in" id="category_id" name="category_id" required>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
            @error('category_id')<div class="err">{{ $message }}</div>@enderror
        </div>
        <div>
            <label class="l" for="name" style="margin-top:0">Nama Menu</label>
            <input class="in" id="name" name="name" value="{{ old('name', $product->name) }}" required>
            @error('name')<div class="err">{{ $message }}</div>@enderror
        </div>
        <div class="full">
            <label class="l" for="price">Harga Dasar (Rupiah)</label>
            <input class="in" id="price" type="number" min="0" step="500" name="price" value="{{ old('price', $product->price) }}" required>
            @error('price')<div class="err">{{ $message }}</div>@enderror
        </div>
        <div class="full">
            <label class="l" for="description">Deskripsi Menu</label>
            <textarea class="in" id="description" name="description" rows="3">{{ old('description', $product->description) }}</textarea>
            @error('description')<div class="err">{{ $message }}</div>@enderror
        </div>
        <div class="full">
            <label class="l" for="image">Foto Menu</label>
            @if ($product->image_url)
                <div style="display:flex;align-items:center;gap:14px;margin-bottom:10px">
                    <img class="thumb-sm" style="width:96px;height:96px" src="{{ $product->image_url }}" alt="Foto saat ini">
                    <label style="font-size:13px;color:var(--mg);font-weight:700">
                        <input type="checkbox" name="remove_image" value="1"> Hapus foto saat ini
                    </label>
                </div>
            @endif
            <input class="in" id="image" type="file" name="image" accept="image/*">
            @error('image')<div class="err">{{ $message }}</div>@enderror
        </div>
        <div class="full">
            <label style="display:flex;gap:10px;align-items:center;font-weight:700;margin-top:18px">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->is_active))>
                Tampilkan di katalog
            </label>
        </div>
    </div>

    <div style="margin-top:24px;display:flex;gap:12px">
        <button class="btn pu">Simpan</button>
        <a class="btn line" href="{{ route('admin.products.index') }}">Batal</a>
    </div>
</form>
@endsection
