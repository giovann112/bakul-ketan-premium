<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('shop.name'))</title>
    <link rel="icon" href="{{ asset('images/logo.png') }}">
    @include('partials.fonts')
    <link rel="stylesheet" href="{{ asset('css/bkp.css') }}">
</head>
@php
    $cartItems = \App\Support\Cart::items();
    $cartCount = \App\Support\Cart::count();
    $cartTotal = $cartCount > 0 ? \App\Support\Cart::total() : 0;
    $activeLoc = $selectedLocation ?? \App\Support\StockPool::selectedLocation();
    $allToppings = \App\Models\Topping::active()->orderBy('id')->get();
@endphp
<body>
<div class="nav-wrap" id="mainNavWrap">
    <header class="bar">
        <a href="{{ route('home') }}" class="brand-link">
            <div class="brand-mark">
                <img src="{{ asset('images/logo.png') }}" alt="{{ config('shop.name') }}">
            </div>
            <div class="brand-text">
                <strong>Bakul Ketan Premium</strong>
                <small>#KETANBISAKRIUK</small>
            </div>
        </a>

        <nav class="nav-pills" id="interactiveNav" aria-label="Menu Utama">
            <span class="nav-glider" id="navGlider" aria-hidden="true"></span>
            <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'on' : '' }}">Beranda</a>
            <a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.*') ? 'on' : '' }}">Menu</a>
            <a href="{{ route('about') }}" class="{{ request()->routeIs('about') ? 'on' : '' }}">Outlet & Foodtruck</a>
            <a href="{{ route('tracking.index') }}" class="{{ request()->routeIs('tracking.*') ? 'on' : '' }}">Lacak</a>
        </nav>

        <div class="nav-actions">
            @auth
                @if (auth()->user()->is_admin)
                    <a href="{{ route('admin.dashboard') }}" class="admin-stealth" title="Panel" aria-label="Panel">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        <span>Panel</span>
                    </a>
                @endif
            @else
                <a href="{{ route('login') }}" class="admin-stealth" title="Staff" aria-label="Staff">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                </a>
            @endauth
        </div>
    </header>
</div>

@include('partials.branch-bar')
@include('partials.flash')

@yield('content')

{{-- Tombol Keranjang Mengambang di Sebelah Kiri --}}
@if (!request()->routeIs('cart.*'))
    <button type="button" class="left-cart-fab js-open-cart-drawer {{ $cartCount > 0 ? 'has-items' : '' }}" aria-label="Buka Keranjang Belanja">
        <span class="left-cart-icon" aria-hidden="true">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
            <span class="left-cart-count">{{ $cartCount }}</span>
        </span>
        <span class="left-cart-text">
            <small>{{ $cartCount > 0 ? $cartCount.' porsi' : 'Keranjang' }}</small>
            <strong>{{ $cartCount > 0 ? 'Rp '.number_format($cartTotal, 0, ',', '.') : 'Kosong' }}</strong>
        </span>
    </button>
@endif

{{-- Pop Up Drawer Keranjang di Sebelah Kiri --}}
<div class="cart-drawer-backdrop" id="cartDrawerBackdrop" aria-hidden="true"></div>
<aside class="cart-drawer" id="cartDrawer" aria-label="Keranjang Pesanan" aria-hidden="true">
    <div class="cart-drawer-head">
        <div>
            <span class="eyebrow" style="color:var(--go);font-size:10.5px">PICK UP: {{ strtoupper($activeLoc?->name ?? 'OUTLET / FOODTRUCK') }}</span>
            <h3>Keranjang Kamu</h3>
        </div>
        <button type="button" class="drawer-close js-close-cart-drawer" aria-label="Tutup Keranjang">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
    </div>

    <div class="cart-drawer-body">
        @if ($cartItems->isEmpty())
            <div class="drawer-empty">
                <p style="font-weight:700;font-size:16px;margin:0 0 6px">Keranjang masih kosong</p>
                <p class="muted" style="font-size:13.5px;margin:0 0 18px">Pilih menu ketan favoritmu, lalu atur topping sesuai selera.</p>
                <a href="{{ route('products.index') }}" class="btn pu sm">Pilih Menu Sekarang</a>
            </div>
        @else
            @foreach ($cartItems as $item)
                @php [$bg] = $item->product->panel; @endphp
                <div class="drawer-item">
                    <div class="drawer-thumb" style="background:{{ $bg }}">
                        @if ($item->product->image_url)
                            <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}">
                        @endif
                    </div>
                    <div class="drawer-item-info">
                        <div class="drawer-item-title">{{ $item->product->name }}</div>
                        @if ($item->toppings->isNotEmpty())
                            <div class="drawer-item-top">+ {{ $item->toppings->pluck('name')->implode(', ') }}</div>
                        @else
                            <div class="drawer-item-top muted">Tanpa topping tambahan</div>
                        @endif
                        <div class="drawer-item-price">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</div>

                        <div class="drawer-item-actions">
                            <form method="POST" action="{{ route('cart.update', $item->key) }}" class="drawer-qty-form">
                                @csrf @method('PATCH')
                                <input type="hidden" name="qty" value="{{ max(0, $item->qty - 1) }}">
                                <button type="submit" class="qty-step" aria-label="Kurangi">-</button>
                            </form>
                            <span class="qty-val">{{ $item->qty }}</span>
                            <form method="POST" action="{{ route('cart.update', $item->key) }}" class="drawer-qty-form">
                                @csrf @method('PATCH')
                                <input type="hidden" name="qty" value="{{ $item->qty + 1 }}">
                                <button type="submit" class="qty-step" aria-label="Tambah">+</button>
                            </form>
                            <form method="POST" action="{{ route('cart.remove', $item->key) }}" style="margin-left:auto">
                                @csrf @method('DELETE')
                                <button type="submit" class="drawer-del">Hapus</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    @if ($cartItems->isNotEmpty())
        <div class="cart-drawer-foot">
            <div class="drawer-total-row">
                <span>Total ({{ $cartCount }} porsi)</span>
                <strong>Rp {{ number_format($cartTotal, 0, ',', '.') }}</strong>
            </div>
            <a href="{{ route('cart.index') }}" class="btn pu block">Lanjut Checkout Pick Up</a>
        </div>
    @endif
</aside>

{{-- Pop Up Modal Pilihan Topping Saat Klik Pesan Menu --}}
<div class="order-modal-backdrop" id="orderModalBackdrop" aria-hidden="true">
    <div class="order-modal" role="dialog" aria-modal="true" aria-labelledby="orderModalTitle">
        <div class="order-modal-head">
            <div>
                <span class="eyebrow" id="orderModalCat" style="color:var(--mg);font-size:11px">MENU KETAN</span>
                <h3 id="orderModalTitle">Nama Menu</h3>
                <div class="order-modal-base-price" id="orderModalBasePrice">Rp 0</div>
            </div>
            <button type="button" class="drawer-close" id="closeOrderModal" aria-label="Tutup">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>

        <form method="POST" action="" id="orderModalForm" class="order-modal-form">
            @csrf
            <div class="order-modal-body">
                <p class="muted" id="orderModalDesc" style="font-size:13.5px;margin:0 0 14px"></p>

                <label class="l" style="margin-top:0">Pilihan Topping</label>
                <div class="topping-mode-switch" role="group" aria-label="Pilihan topping">
                    <button type="button" class="mode-btn is-on" data-mode="none">Tanpa Topping</button>
                    <button type="button" class="mode-btn" data-mode="custom">Tambah Topping Mix</button>
                </div>

                <div id="modalToppingList" style="display:none;margin-top:12px">
                    @if ($allToppings->isNotEmpty())
                        <div class="topping-grid">
                            @foreach ($allToppings as $topping)
                                <label class="top-opt">
                                    <span style="display:flex;align-items:center;gap:8px">
                                        <input type="checkbox" name="toppings[]" value="{{ $topping->id }}" data-price="{{ $topping->price }}">
                                        <span>{{ $topping->name }}</span>
                                    </span>
                                    <small>{{ $topping->price_rp }}</small>
                                </label>
                            @endforeach
                        </div>
                    @else
                        <p class="muted" style="font-size:13px">Belum ada pilihan topping.</p>
                    @endif
                </div>

                <div class="modal-qty-row">
                    <span style="font-family:'Poppins',sans-serif;font-weight:700;font-size:13.5px">Jumlah Porsi</span>
                    <div class="modal-qty-ctrl">
                        <button type="button" class="qty-step" id="modalQtyMinus" aria-label="Kurangi porsi">-</button>
                        <input type="number" name="qty" id="modalQtyInput" value="1" min="1" max="20" readonly>
                        <button type="button" class="qty-step" id="modalQtyPlus" aria-label="Tambah porsi">+</button>
                    </div>
                </div>
            </div>

            <div class="order-modal-foot">
                <button type="submit" class="btn pu block" id="orderModalSubmit">
                    Masuk Keranjang - Rp 0
                </button>
            </div>
        </form>
    </div>
</div>

<footer class="foot">
    <div class="brand">
        <img class="lg" src="{{ asset('images/logo.png') }}" alt="Logo {{ config('shop.name') }}">
        <div>
            <strong>Bakul Ketan Premium</strong>
            <div style="font-size:13px;color:var(--go)">#KetanBisaKriuk - Outlet & Foodtruck</div>
        </div>
    </div>
    <div class="info">
        {{ config('shop.address') }}<br>
        {{ config('shop.hours') }} | WhatsApp 0877-7194-4803 |
        <a href="{{ auth()->check() && auth()->user()->is_admin ? route('admin.dashboard') : route('login') }}" style="opacity:.45;font-size:12px">Staff</a>
    </div>
</footer>

<script>
(function () {
    // 1. Left-Side Cart Drawer Popup
    const drawer = document.getElementById('cartDrawer');
    const drawerBackdrop = document.getElementById('cartDrawerBackdrop');
    const openDrawerBtns = document.querySelectorAll('.js-open-cart-drawer');
    const closeDrawerBtns = document.querySelectorAll('.js-close-cart-drawer');

    function openDrawer() {
        if (!drawer) return;
        drawer.classList.add('is-open');
        drawerBackdrop.classList.add('is-open');
        drawer.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    }
    function closeDrawer() {
        if (!drawer) return;
        drawer.classList.remove('is-open');
        drawerBackdrop.classList.remove('is-open');
        drawer.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }

    openDrawerBtns.forEach(btn => btn.addEventListener('click', openDrawer));
    closeDrawerBtns.forEach(btn => btn.addEventListener('click', closeDrawer));
    if (drawerBackdrop) drawerBackdrop.addEventListener('click', closeDrawer);

    // 2. Order & Topping Modal Popup
    const modalBackdrop = document.getElementById('orderModalBackdrop');
    const modalForm = document.getElementById('orderModalForm');
    const modalTitle = document.getElementById('orderModalTitle');
    const modalCat = document.getElementById('orderModalCat');
    const modalBasePrice = document.getElementById('orderModalBasePrice');
    const modalDesc = document.getElementById('orderModalDesc');
    const modalSubmit = document.getElementById('orderModalSubmit');
    const modalToppingList = document.getElementById('modalToppingList');
    const qtyInput = document.getElementById('modalQtyInput');
    const modeBtns = modalBackdrop ? modalBackdrop.querySelectorAll('.mode-btn') : [];
    const closeModalBtn = document.getElementById('closeOrderModal');

    let basePrice = 0;

    function formatRp(num) {
        return 'Rp ' + Number(num || 0).toLocaleString('id-ID');
    }

    function recalcModalTotal() {
        let toppingSum = 0;
        modalForm.querySelectorAll('input[name="toppings[]"]:checked').forEach(cb => {
            toppingSum += parseInt(cb.dataset.price || '0', 10);
        });
        const qty = Math.max(1, parseInt(qtyInput.value || '1', 10));
        const total = (basePrice + toppingSum) * qty;
        modalSubmit.textContent = 'Masuk Keranjang - ' + formatRp(total);
    }

    function setToppingMode(mode) {
        modeBtns.forEach(b => b.classList.toggle('is-on', b.dataset.mode === mode));
        if (mode === 'custom') {
            modalToppingList.style.display = 'block';
        } else {
            modalToppingList.style.display = 'none';
            modalForm.querySelectorAll('input[name="toppings[]"]').forEach(cb => cb.checked = false);
        }
        recalcModalTotal();
    }

    document.querySelectorAll('.js-open-order-modal').forEach(btn => {
        btn.addEventListener('click', () => {
            basePrice = parseInt(btn.dataset.price || '0', 10);
            modalForm.action = btn.dataset.action;
            modalTitle.textContent = btn.dataset.name || 'Menu Ketan';
            modalCat.textContent = (btn.dataset.category || 'MENU KETAN').toUpperCase();
            modalBasePrice.textContent = btn.dataset.priceRp || formatRp(basePrice);
            modalDesc.textContent = btn.dataset.desc || '';
            modalDesc.style.display = btn.dataset.desc ? 'block' : 'none';
            qtyInput.value = 1;
            setToppingMode('none');

            modalBackdrop.classList.add('is-open');
            modalBackdrop.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
        });
    });

    function closeOrderModal() {
        if (!modalBackdrop) return;
        modalBackdrop.classList.remove('is-open');
        modalBackdrop.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }

    if (closeModalBtn) closeModalBtn.addEventListener('click', closeOrderModal);
    if (modalBackdrop) {
        modalBackdrop.addEventListener('click', (e) => {
            if (e.target === modalBackdrop) closeOrderModal();
        });
    }

    modeBtns.forEach(btn => {
        btn.addEventListener('click', () => setToppingMode(btn.dataset.mode));
    });

    if (modalForm) {
        modalForm.querySelectorAll('input[name="toppings[]"]').forEach(cb => {
            cb.addEventListener('change', recalcModalTotal);
        });
    }

    const minusBtn = document.getElementById('modalQtyMinus');
    const plusBtn = document.getElementById('modalQtyPlus');
    if (minusBtn) {
        minusBtn.addEventListener('click', () => {
            const v = Math.max(1, parseInt(qtyInput.value || '1', 10) - 1);
            qtyInput.value = v;
            recalcModalTotal();
        });
    }
    if (plusBtn) {
        plusBtn.addEventListener('click', () => {
            const v = Math.min(20, parseInt(qtyInput.value || '1', 10) + 1);
            qtyInput.value = v;
            recalcModalTotal();
        });
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeDrawer();
            closeOrderModal();
        }
    });

    // 3. Interactive Navbar Sliding Pill & Scroll Glass Effect
    const navWrap = document.getElementById('mainNavWrap');
    const navEl = document.getElementById('interactiveNav');
    const glider = document.getElementById('navGlider');

    function moveGlider(target) {
        if (!navEl || !glider || !target) return;
        const navRect = navEl.getBoundingClientRect();
        const itemRect = target.getBoundingClientRect();
        glider.style.width = itemRect.width + 'px';
        glider.style.transform = 'translateX(' + (itemRect.left - navRect.left - 5) + 'px)';
        glider.style.opacity = '1';
    }

    if (navEl && glider) {
        const activeLink = navEl.querySelector('a.on');
        if (activeLink) {
            requestAnimationFrame(() => moveGlider(activeLink));
            window.addEventListener('resize', () => moveGlider(activeLink));
        }
        navEl.querySelectorAll('a').forEach(link => {
            link.addEventListener('mouseenter', () => moveGlider(link));
        });
        navEl.addEventListener('mouseleave', () => {
            const current = navEl.querySelector('a.on');
            if (current) moveGlider(current);
            else glider.style.opacity = '0';
        });
    }

    if (navWrap) {
        window.addEventListener('scroll', () => {
            navWrap.classList.toggle('is-scrolled', window.scrollY > 18);
        }, { passive: true });
    }
})();
</script>
</body>
</html>
