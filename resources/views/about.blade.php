@extends('layouts.app')

@section('title', 'Outlet & Foodtruck - '.config('shop.name'))

@section('content')
<div class="w sec">
    <div class="eyebrow" style="color:#C2185B">Lokasi Kami</div>
    <h1 class="page-title" style="margin-top:8px">Outlet & Foodtruck</h1>
    <div class="two">
        <div>
            @foreach ($locations as $loc)
                @php $avail = $loc->today_stock->remaining_portions > 0; @endphp
                <div class="cd flat" style="margin-bottom:14px;background:#FBF8FF">
                    <div style="display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap">
                        <strong style="font-family:'Poppins',sans-serif;font-size:17px;color:var(--pu)">{{ $loc->name }}</strong>
                        <span class="pill {{ $avail ? 'st-completed' : 'st-cancelled' }}">
                            {{ $avail ? 'Buka' : 'Habis Hari Ini' }}
                        </span>
                    </div>
                    <p style="margin:8px 0 4px;font-size:14px"><b>Alamat:</b> {{ $loc->address }}</p>
                    <p style="margin:0;font-size:14px"><b>Jam Operasional:</b> {{ $loc->hours }}</p>
                </div>
            @endforeach
        </div>
        <div style="display:flex;flex-direction:column;gap:18px">
            <div style="border-radius:20px;overflow:hidden;border:2px solid var(--ink);box-shadow:5px 5px 0 var(--ink);position:relative;height:260px">
                <img src="{{ asset('images/booth-bg.jpg') }}" alt="Bakul Ketan Premium" style="width:100%;height:100%;object-fit:cover;object-position:center 55%">
                <div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(27,10,46,.82) 0%,transparent 60%);display:flex;align-items:flex-end;padding:16px;color:#fff">
                    <div>
                        <div style="font-family:'Poppins',sans-serif;font-size:11px;font-weight:700;color:var(--go);letter-spacing:.1em">BAKUL KETAN PREMIUM</div>
                        <div style="font-family:'Montserrat',sans-serif;font-weight:800;font-size:16px">#KetanBisaKriuk</div>
                    </div>
                </div>
            </div>
            <div class="cd">
                <h3>Kontak</h3>
                <p><b>WhatsApp</b><br>0877 7194 4803</p>
                <p><b>Instagram</b><br>@bakulketanpremium</p>
                <a href="https://wa.me/{{ config('shop.wa_number') }}" target="_blank" rel="noopener" class="btn wa block" style="margin-top:16px">Chat via WhatsApp</a>
            </div>
        </div>
    </div>
</div>
@endsection
