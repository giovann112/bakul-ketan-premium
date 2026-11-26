<?php

namespace App\Http\Controllers;

use App\Models\DailyStock;
use App\Models\Location;
use App\Models\Order;
use App\Models\Product;
use App\Models\Topping;
use App\Support\Cart;
use App\Support\SchemaGuard;
use App\Support\StockPool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    public function store(Request $request)
    {
        SchemaGuard::ensureOrderTables();

        $request->merge(['phone' => Order::normalizePhone((string) $request->input('phone', ''))]);

        $data = $request->validate([
            'location_id'   => 'required|exists:locations,id',
            'customer_name' => 'required|string|max:100',
            'phone'         => ['required', 'regex:/^08[0-9]{8,12}$/'],
            'notes'         => 'nullable|string|max:500',
        ], [
            'phone.regex'          => 'Nomor WhatsApp harus diawali 08 atau 628 dan terdiri dari 10 sampai 14 digit.',
            'location_id.required' => 'Pilih lokasi pick up.',
        ]);

        $cart = Cart::raw();
        if (empty($cart)) {
            return redirect()->route('products.index')->with('error', 'Keranjang masih kosong.');
        }

        StockPool::selectLocation((int) $data['location_id']);

        $order = DB::transaction(function () use ($data, $cart) {
            $location = Location::active()->whereKey($data['location_id'])->firstOrFail();

            $todayStock = $location->todayStock();
            $lockedStock = DailyStock::whereKey($todayStock->id)->lockForUpdate()->firstOrFail();

            $productIds = collect($cart)->pluck('product_id')->unique()->all();
            $toppingIds = collect($cart)->pluck('topping_ids')->flatten()->unique()->all();

            $products = Product::whereIn('id', $productIds)->get()->keyBy('id');
            $toppings = Topping::whereIn('id', $toppingIds)->active()->get()->keyBy('id');

            $lines = [];
            $total = 0;
            $totalPortions = 0;

            foreach ($cart as $row) {
                $product = $products->get($row['product_id']);
                $qty = (int) $row['qty'];

                if (! $product || ! $product->is_active || $qty < 1) {
                    throw ValidationException::withMessages([
                        'cart' => 'Salah satu menu di keranjang sudah tidak tersedia.',
                    ]);
                }

                $selectedToppings = collect($row['topping_ids'] ?? [])
                    ->map(fn ($tid) => $toppings->get($tid))
                    ->filter()
                    ->map(fn (Topping $t) => ['id' => $t->id, 'name' => $t->name, 'price' => $t->price])
                    ->values()
                    ->all();

                $toppingSum = array_sum(array_column($selectedToppings, 'price'));
                $unitPrice = $product->price + $toppingSum;
                $subtotal = $unitPrice * $qty;

                $lines[] = [$product, $selectedToppings, $unitPrice, $qty, $subtotal];
                $total += $subtotal;
                $totalPortions += $qty;
            }

            if ($lockedStock->remaining_portions < $totalPortions) {
                throw ValidationException::withMessages([
                    'cart' => "Maaf, stok di {$location->name} hari ini tidak mencukupi jumlah pesanan Anda.",
                ]);
            }

            $lockedStock->decrement('remaining_portions', $totalPortions);

            $order = Order::create([
                'order_code'           => $this->makeCode(),
                'location_id'          => $location->id,
                'pickup_location_name' => $location->name,
                'customer_name'        => $data['customer_name'],
                'phone'                => $data['phone'],
                'notes'                => $data['notes'] ?? null,
                'total'                => $total,
                'status'               => 'pending',
            ]);

            $hasLegacyPrice = Schema::hasColumn('order_items', 'price');

            foreach ($lines as [$product, $selectedToppings, $unitPrice, $qty, $subtotal]) {
                $itemData = [
                    'product_id'   => $product->id,
                    'product_name' => $product->name,
                    'base_price'   => $product->price,
                    'toppings'     => $selectedToppings,
                    'unit_price'   => $unitPrice,
                    'qty'          => $qty,
                    'subtotal'     => $subtotal,
                ];
                if ($hasLegacyPrice) {
                    $itemData['price'] = $unitPrice;
                }
                $order->items()->create($itemData);
            }

            return $order;
        });

        Cart::clear();
        session(['last_order' => $order->order_code]);

        return redirect()->route('checkout.success', $order->order_code);
    }

    public function success(string $code)
    {
        abort_unless(session('last_order') === $code, 404);

        $order = Order::with(['items', 'location'])->where('order_code', $code)->firstOrFail();

        return view('checkout.success', [
            'order'  => $order,
            'waLink' => $this->waLink($order),
        ]);
    }

    private function makeCode(): string
    {
        $prefix = 'BKP-'.now()->format('Ymd').'-';
        $seq = Order::where('order_code', 'like', $prefix.'%')->count() + 1;

        do {
            $code = $prefix.str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
            $seq++;
        } while (Order::where('order_code', $code)->exists());

        return $code;
    }

    private function waLink(Order $order): string
    {
        $lines = [
            'Halo '.config('shop.name').', saya ingin mengonfirmasi pesanan.',
            '',
            'Kode Pesanan: '.$order->order_code,
            'Nama: '.$order->customer_name,
            'Lokasi Pick Up: '.$order->pickup_location_name,
            '',
            'Pesanan:',
        ];

        foreach ($order->items as $idx => $item) {
            $no = $idx + 1;
            $top = $item->toppings_summary ? " (+ {$item->toppings_summary})" : '';
            $lines[] = "{$no}. {$item->product_name}{$top} x{$item->qty} = Rp ".number_format($item->subtotal, 0, ',', '.');
        }

        $lines[] = '';
        $lines[] = 'Subtotal: Rp '.number_format($order->total, 0, ',', '.');
        if ($order->notes) {
            $lines[] = 'Catatan: '.$order->notes;
        }

        $waNumber = preg_replace('/\D+/', '', (string) config('shop.wa_number', '6287771944803'));
        if (str_starts_with($waNumber, '0')) {
            $waNumber = '62'.substr($waNumber, 1);
        }

        return 'https://wa.me/'.$waNumber.'?text='.rawurlencode(implode("\n", $lines));
    }
}
