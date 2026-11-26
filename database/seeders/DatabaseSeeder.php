<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Location;
use App\Models\Product;
use App\Models\Topping;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@bakulketan.test')],
            [
                'name'     => 'Admin',
                'password' => env('ADMIN_PASSWORD', 'password123'),
                'is_admin' => true,
            ]
        );

        $locations = [
            [
                'slug'            => 'outlet-utama',
                'name'            => 'Jl. Veteran, Lumajang',
                'type'            => 'outlet',
                'address'         => 'Jl. Veteran, Lumajang',
                'hours'           => 'Setiap hari, 06.00 - 22.00 WIB',
                'weekday_kg'      => 7.00,
                'weekend_kg'      => 10.00,
                'portions_per_kg' => 15,
                'is_active'       => true,
            ],
            [
                'slug'            => 'foodtruck-ketan',
                'name'            => 'Embong Kembar, Lumajang',
                'type'            => 'foodtruck',
                'address'         => 'Embong Kembar, Lumajang',
                'hours'           => 'Setiap hari, 17.00 - 23.00 WIB',
                'weekday_kg'      => 7.00,
                'weekend_kg'      => 10.00,
                'portions_per_kg' => 15,
                'is_active'       => true,
            ],
        ];

        foreach ($locations as $loc) {
            Location::updateOrCreate(['slug' => $loc['slug']], $loc);
        }

        // Kategori & Menu sesuai daftar menu Bakul Ketan Premium
        $menuGroups = [
            'Ketan Gold Cruncmilk' => [
                ['Ketan Durian Premium', 16000, 'Ketan pulen hangat dengan saus durian pilihan yang legit dan lumer.'],
                ['Ketan Ice Cream', 12000, 'Perpaduan ketan hangat lembut dengan scoop es krim dingin yang manis.'],
                ['Ketan Ovamaltine', 13000, 'Olesan Ovomaltine cokelat malt renyah melimpah di atas ketan.'],
                ['Ketan Lotus', 13000, 'Selai karamel rempah Lotus Biscoff dengan remahan biskuit renyah.'],
                ['Ketan Nutella', 13000, 'Selai cokelat hazelnut Nutella premium yang creamy dan manis.'],
                ['Ketan Kuah Ori', 8000, 'Ketan klasik disiram kuah santan manis gurih khas Bakul Ketan.'],
                ['Ketan Chococrunchy', 9000, 'Cokelat lumer dengan sereal krispi renyah di setiap gigitan.'],
                ['Ketan Matcha Crunchy', 9000, 'Glaze matcha teh hijau Jepang dengan sensasi crunchy manis.'],
                ['Ketan Tiramisu Crunchy', 9000, 'Saus tiramisu kopi krim lembut bertabur crumble renyah.'],
                ['Ketan Taro Milk', 8000, 'Saus susu talas ungu (taro) yang manis harum dan lembut.'],
                ['Ketan Vanilla Milk', 8000, 'Siraman susu vanila creamy lembut berpadu ketan pulen.'],
                ['Ketan Strawberry Milk', 8000, 'Saus susu stroberi segar manis asam yang pas di lidah.'],
                ['Ketan Cheese Crunchy', 9000, 'Saus keju gurih manis dengan taburan renyah kekinian.'],
                ['Ketan Hazelnut Crunchy', 9000, 'Pasta kacang hazelnut panggang dengan tekstur kriuk nikmat.'],
            ],
            'Ketan Soft Crumble' => [
                ['Ketan Susu Keju', 9000, 'Ketan susu kental manis dengan parutan keju melimpah.'],
                ['Ketan Milo', 8000, 'Taburan bubuk cokelat malt Milo tebal di atas ketan susu.'],
                ['Ketan Redvelvet', 9000, 'Remahan red velvet lembut manis dengan siraman susu creamy.'],
                ['Ketan Oreo', 9000, 'Remahan biskuit Oreo hitam manis berpadu susu kental manis.'],
                ['Ketan Regal', 9000, 'Biskuit Marie Regal klasik yang lumer bersama susu hangat.'],
                ['Ketan Misis Coklat', 8000, 'Taburan meses cokelat klasik favorit sepanjang masa.'],
                ['Ketan Chocochips', 8000, 'Butiran chocochips cokelat manis di atas ketan pulen.'],
                ['Ketan Dancow', 9000, 'Taburan susu bubuk Dancow putih yang gurih dan creamy.'],
                ['Ketan Susu Kacang', 7000, 'Ketan susu dengan taburan kacang tanah sangrai gurih renyah.'],
                ['Ketan Caramel Milk', 7000, 'Siraman saus karamel manis wangi dengan susu kental manis.'],
            ],
            'Ketan Klasik' => [
                ['Ketan Bubuk Ori', 6000, 'Ketan tradisional dengan taburan bubuk kedelai gurih manis.'],
                ['Ketan Susu Ori', 5000, 'Ketan pulen hangat disiram susu kental manis sederhana nan nikmat.'],
                ['Ketan Polosan Ori', 4000, 'Ketan kukus pulen gurih original tanpa topping.'],
            ],
        ];

        foreach ($menuGroups as $catName => $items) {
            $category = Category::firstOrCreate(
                ['slug' => Str::slug($catName)],
                ['name' => $catName]
            );

            foreach ($items as [$name, $price, $desc]) {
                Product::firstOrCreate(
                    ['slug' => Str::slug($name)],
                    [
                        'category_id' => $category->id,
                        'name'        => $name,
                        'description' => $desc,
                        'price'       => $price,
                        'is_active'   => true,
                    ]
                );
            }
        }

        // Topping Mix (Tambahan Topping) sesuai menu gambar
        $toppings = [
            ['Keju', 4000],
            ['Milo', 4000],
            ['Redvelvet Crumble', 4000],
            ['Oreo Crumble', 4000],
            ['Regal Crumble', 4000],
            ['Kacang', 3000],
            ['Chococips', 3000],
            ['Hazelnut Crunchy', 4000],
            ['Dancow', 4000],
            ['Kuah', 4000],
            ['Ice Cream', 4000],
            ['Double Bubuk', 3000],
        ];

        foreach ($toppings as [$name, $price]) {
            Topping::firstOrCreate(
                ['name' => $name],
                ['price' => $price, 'is_active' => true]
            );
        }
    }
}
