<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Location;
use App\Models\Product;
use App\Models\Topping;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShopTest extends TestCase
{
    use RefreshDatabase;

    private function setupShop(): array
    {
        $outlet = Location::create([
            'name' => 'Outlet Bakul Ketan', 'slug' => 'outlet', 'type' => 'outlet',
            'weekday_kg' => 7, 'weekend_kg' => 10, 'portions_per_kg' => 15, 'is_active' => true,
        ]);
        $foodtruck = Location::create([
            'name' => 'Foodtruck Bakul Ketan', 'slug' => 'foodtruck', 'type' => 'foodtruck',
            'weekday_kg' => 7, 'weekend_kg' => 10, 'portions_per_kg' => 15, 'is_active' => true,
        ]);
        $category = Category::create(['name' => 'Ketan Gold Cruncmilk', 'slug' => 'gold']);
        $product = Product::create([
            'category_id' => $category->id, 'name' => 'Ketan Durian Premium',
            'slug' => 'ketan-durian', 'price' => 16000, 'is_active' => true,
        ]);
        $topping = Topping::create(['name' => 'Keju', 'price' => 4000, 'is_active' => true]);

        return [$outlet, $foodtruck, $product, $topping];
    }

    public function test_katalog_dan_cabang_tampil(): void
    {
        [$outlet, $foodtruck, $product] = $this->setupShop();

        $this->get('/')->assertOk()->assertSee('Outlet Bakul Ketan')->assertSee('Foodtruck Bakul Ketan');
        $this->get('/produk')->assertOk()->assertSee('Ketan Durian Premium');
    }

    public function test_checkout_dengan_topping_dan_mengurangi_stok_cabang_terpilih(): void
    {
        [$outlet, $foodtruck, $product, $topping] = $this->setupShop();
        $initialOutletPortions = $outlet->todayStock()->remaining_portions;
        $initialFtPortions = $foodtruck->todayStock()->remaining_portions;

        $this->post('/keranjang/tambah/'.$product->id, [
            'qty' => 2,
            'toppings' => [$topping->id],
            'location_id' => $foodtruck->id,
        ])->assertRedirect();

        $this->post('/checkout', [
            'location_id' => $foodtruck->id,
            'customer_name' => 'Siti',
            'phone' => '081234567890',
        ])->assertRedirect();

        $this->assertSame($initialOutletPortions, $outlet->todayStock()->fresh()->remaining_portions);
        $this->assertSame($initialFtPortions - 2, $foodtruck->todayStock()->fresh()->remaining_portions);
        $this->assertDatabaseHas('orders', [
            'customer_name' => 'Siti',
            'pickup_location_name' => 'Foodtruck Bakul Ketan',
            'total' => 40000,
        ]);
    }
}
