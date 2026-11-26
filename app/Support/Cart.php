<?php

namespace App\Support;

use App\Models\Product;
use App\Models\Topping;
use Illuminate\Support\Collection;

class Cart
{
    public static function raw(): array
    {
        return session('cart', []);
    }

    public static function count(): int
    {
        return (int) collect(self::raw())->sum('qty');
    }

    public static function items(): Collection
    {
        $cart = self::raw();
        if (empty($cart)) {
            return collect();
        }

        $productIds = collect($cart)->pluck('product_id')->unique()->all();
        $toppingIds = collect($cart)->pluck('topping_ids')->flatten()->unique()->all();

        $products = Product::whereIn('id', $productIds)->active()->get()->keyBy('id');
        $toppings = Topping::whereIn('id', $toppingIds)->active()->get()->keyBy('id');

        return collect($cart)->map(function ($row, $key) use ($products, $toppings) {
            $product = $products->get($row['product_id']);
            if (! $product) {
                return null;
            }

            $selectedToppings = collect($row['topping_ids'] ?? [])
                ->map(fn ($tid) => $toppings->get($tid))
                ->filter()
                ->values();

            $toppingTotal = (int) $selectedToppings->sum('price');
            $unitPrice = $product->price + $toppingTotal;
            $qty = (int) $row['qty'];

            return (object) [
                'key'        => $key,
                'product'    => $product,
                'toppings'   => $selectedToppings,
                'unit_price' => $unitPrice,
                'qty'        => $qty,
                'subtotal'   => $unitPrice * $qty,
            ];
        })->filter()->values();
    }

    public static function total(): int
    {
        return (int) self::items()->sum('subtotal');
    }

    public static function makeKey(int $productId, array $toppingIds): string
    {
        $sorted = array_values(array_unique(array_map('intval', $toppingIds)));
        sort($sorted);

        return $productId.':'.implode(',', $sorted);
    }

    public static function add(Product $product, int $qty, array $toppingIds = [], ?int $locationId = null): ?string
    {
        if (! $product->is_active) {
            return "{$product->name} sedang tidak tersedia.";
        }

        $validToppings = Topping::whereIn('id', $toppingIds)->active()->pluck('id')->all();
        $key = self::makeKey($product->id, $validToppings);
        $cart = self::raw();

        $currentTotalPortions = self::count();
        $available = StockPool::remainingPortionsFor($locationId);

        if ($available <= 0) {
            return 'Maaf, pesanan di lokasi ini sudah penuh untuk hari ini. Silakan pilih lokasi pick up lain.';
        }

        if ($currentTotalPortions + $qty > $available) {
            return 'Jumlah pesanan melebihi ketersediaan porsi di lokasi ini hari ini.';
        }

        if (isset($cart[$key])) {
            $cart[$key]['qty'] += $qty;
        } else {
            $cart[$key] = [
                'product_id'  => $product->id,
                'topping_ids' => $validToppings,
                'qty'         => $qty,
            ];
        }

        session(['cart' => $cart]);

        return null;
    }

    public static function setQty(string $key, int $qty, ?int $locationId = null): ?string
    {
        $cart = self::raw();
        if (! isset($cart[$key])) {
            return null;
        }

        if ($qty < 1) {
            unset($cart[$key]);
            session(['cart' => $cart]);

            return null;
        }

        $otherPortions = collect($cart)->except($key)->sum('qty');
        $available = StockPool::remainingPortionsFor($locationId);

        if ($otherPortions + $qty > $available) {
            return 'Jumlah pesanan melebihi ketersediaan porsi di lokasi ini hari ini.';
        }

        $cart[$key]['qty'] = $qty;
        session(['cart' => $cart]);

        return null;
    }

    public static function remove(string $key): void
    {
        $cart = self::raw();
        unset($cart[$key]);
        session(['cart' => $cart]);
    }

    public static function clear(): void
    {
        session()->forget('cart');
    }
}
