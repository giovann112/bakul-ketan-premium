@php
    $locs = $locations ?? \App\Support\StockPool::locationsWithTodayStock();
    $sel = $selectedLocation ?? \App\Support\StockPool::selectedLocation();
@endphp
@if ($locs->isNotEmpty())
<div class="loc-dock" role="region" aria-label="Lokasi Ambil">
    <div class="loc-dock-pill">
        @foreach ($locs as $loc)
            @php
                $isAvail = $loc->today_stock->remaining_portions > 0;
                $isSel = $sel && $sel->id === $loc->id;
                $isFoodtruck = strtolower($loc->type ?? '') === 'foodtruck' || str_contains(strtolower($loc->name), 'foodtruck');
            @endphp
            <form method="POST" action="{{ route('location.switch') }}" class="loc-dock-form">
                @csrf
                <input type="hidden" name="location_id" value="{{ $loc->id }}">
                <button type="submit" class="loc-tab {{ $isSel ? 'is-active' : '' }}" title="{{ $loc->address ?: $loc->hours }}">
                    <span class="loc-dot {{ $isAvail ? 'on' : 'off' }}"></span>
                    @if ($isFoodtruck)
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M10 17h4V5H2v12h3"/><path d="M20 17h2v-3.34a4 4 0 0 0-1.17-2.83L19 9h-5"/><path d="M14 17h1"/><circle cx="7.5" cy="17.5" r="2.5"/><circle cx="17.5" cy="17.5" r="2.5"/></svg>
                    @else
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    @endif
                    <span class="loc-tab-name">{{ $loc->name }}</span>
                </button>
            </form>
        @endforeach
    </div>
</div>
@endif
