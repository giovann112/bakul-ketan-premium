@extends('layouts.admin')

@section('title', 'Stok Ketan Cabang')

@section('content')
<div class="top">
    <div>
        <h1>Stok Ketan Cabang</h1>
    </div>
</div>

<div class="split" style="grid-template-columns:1fr 1fr;margin-top:0">
    @foreach ($locations as $loc)
        @php $st = $loc->today_stock; @endphp
        <div class="cd flat">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
                <div>
                    <span class="pill st-confirmed">{{ strtoupper($loc->type) }}</span>
                    <h3 style="margin-top:6px">{{ $loc->name }}</h3>
                </div>
                <form method="POST" action="{{ route('admin.stocks.reset', $loc) }}">
                    @csrf
                    <button class="btn line sm">Reset Hari Ini</button>
                </form>
            </div>

            <form method="POST" action="{{ route('admin.stocks.today', $loc) }}" style="background:#FBF8FF;padding:16px;border-radius:14px;border:1.5px solid var(--line);margin-bottom:20px">
                @csrf @method('PUT')
                <div style="font-weight:700;color:var(--pu);margin-bottom:8px">Stok Hari Ini ({{ now()->format('d M Y') }})</div>
                <div class="formgrid">
                    <div>
                        <label class="l" style="margin-top:4px">Alokasi Ketan (kg)</label>
                        <input class="in" type="number" step="0.5" min="0" name="kg_allocated" value="{{ $st->kg_allocated }}" required>
                    </div>
                    <div>
                        <label class="l" style="margin-top:4px">Sisa Porsi Tersedia</label>
                        <input class="in" type="number" min="0" name="remaining_portions" value="{{ $st->remaining_portions }}" required>
                    </div>
                </div>
                <button class="btn pu sm" style="margin-top:14px">Simpan Stok Hari Ini</button>
            </form>

            <form method="POST" action="{{ route('admin.stocks.location', $loc) }}">
                @csrf @method('PUT')
                <div style="font-weight:700;margin-bottom:6px">Pengaturan Cabang</div>
                <label class="l">Nama Cabang</label>
                <input class="in" name="name" value="{{ $loc->name }}" required>

                <label class="l">Alamat / Lokasi</label>
                <input class="in" name="address" value="{{ $loc->address }}">

                <label class="l">Jam Operasional</label>
                <input class="in" name="hours" value="{{ $loc->hours }}">

                <div class="formgrid">
                    <div>
                        <label class="l">Weekdays (kg)</label>
                        <input class="in" type="number" step="0.5" min="0.5" name="weekday_kg" value="{{ $loc->weekday_kg }}" required>
                    </div>
                    <div>
                        <label class="l">Weekend (kg)</label>
                        <input class="in" type="number" step="0.5" min="0.5" name="weekend_kg" value="{{ $loc->weekend_kg }}" required>
                    </div>
                </div>

                <label class="l">Porsi per 1 kg Ketan</label>
                <input class="in" type="number" min="1" name="portions_per_kg" value="{{ $loc->portions_per_kg }}" required>

                <label style="display:flex;gap:8px;align-items:center;font-weight:700;margin-top:14px">
                    <input type="checkbox" name="is_active" value="1" @checked($loc->is_active)>
                    Cabang aktif
                </label>

                <button class="btn go sm" style="margin-top:16px">Simpan Pengaturan Cabang</button>
            </form>
        </div>
    @endforeach
</div>
@endsection
