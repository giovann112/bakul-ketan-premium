<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SchemaGuard
{
    public static function ensureOrderTables(): void
    {
        try {
            if (Schema::hasTable('locations')) {
                DB::table('locations')->where('slug', 'outlet-utama')->where('address', 'like', '%Raya Utama%')->update([
                    'name'    => 'Jl. Veteran, Lumajang',
                    'address' => 'Jl. Veteran, Lumajang',
                    'hours'   => 'Setiap hari, 06.00 - 22.00 WIB',
                ]);
                DB::table('locations')->where('slug', 'foodtruck-ketan')->where('address', 'like', '%Alun-Alun%')->update([
                    'name'    => 'Embong Kembar, Lumajang',
                    'address' => 'Embong Kembar, Lumajang',
                    'hours'   => 'Setiap hari, 17.00 - 23.00 WIB',
                ]);
            }
        } catch (\Throwable $e) {
            // Abaikan
        }

        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        try {
            if (Schema::hasTable('orders')) {
                $cols = array_map('strtolower', Schema::getColumnListing('orders'));

                $alters = [
                    'order_code'           => "ALTER TABLE `orders` ADD COLUMN `order_code` VARCHAR(50) NULL",
                    'location_id'          => "ALTER TABLE `orders` ADD COLUMN `location_id` BIGINT UNSIGNED NULL",
                    'pickup_location_name' => "ALTER TABLE `orders` ADD COLUMN `pickup_location_name` VARCHAR(100) NOT NULL DEFAULT 'Jl. Veteran, Lumajang'",
                    'customer_name'        => "ALTER TABLE `orders` ADD COLUMN `customer_name` VARCHAR(100) NOT NULL DEFAULT ''",
                    'phone'                => "ALTER TABLE `orders` ADD COLUMN `phone` VARCHAR(20) NOT NULL DEFAULT ''",
                    'notes'                => "ALTER TABLE `orders` ADD COLUMN `notes` TEXT NULL",
                    'total'                => "ALTER TABLE `orders` ADD COLUMN `total` INT UNSIGNED NOT NULL DEFAULT 0",
                    'status'               => "ALTER TABLE `orders` ADD COLUMN `status` VARCHAR(30) NOT NULL DEFAULT 'pending'",
                ];

                foreach ($alters as $col => $sql) {
                    if (! in_array($col, $cols, true)) {
                        try {
                            DB::statement($sql);
                        } catch (\Throwable $e) {
                        }
                    }
                }

                self::relaxLegacyColumns('orders', [
                    'id', 'order_code', 'location_id', 'pickup_location_name',
                    'customer_name', 'phone', 'notes', 'total', 'status',
                    'created_at', 'updated_at',
                ]);
            }

            if (Schema::hasTable('order_items')) {
                $itemCols = array_map('strtolower', Schema::getColumnListing('order_items'));

                $itemAlters = [
                    'product_id'   => "ALTER TABLE `order_items` ADD COLUMN `product_id` BIGINT UNSIGNED NULL",
                    'product_name' => "ALTER TABLE `order_items` ADD COLUMN `product_name` VARCHAR(255) NOT NULL DEFAULT ''",
                    'base_price'   => "ALTER TABLE `order_items` ADD COLUMN `base_price` INT UNSIGNED NOT NULL DEFAULT 0",
                    'toppings'     => "ALTER TABLE `order_items` ADD COLUMN `toppings` JSON NULL",
                    'unit_price'   => "ALTER TABLE `order_items` ADD COLUMN `unit_price` INT UNSIGNED NOT NULL DEFAULT 0",
                    'qty'          => "ALTER TABLE `order_items` ADD COLUMN `qty` INT UNSIGNED NOT NULL DEFAULT 1",
                    'subtotal'     => "ALTER TABLE `order_items` ADD COLUMN `subtotal` INT UNSIGNED NOT NULL DEFAULT 0",
                ];

                foreach ($itemAlters as $col => $sql) {
                    if (! in_array($col, $itemCols, true)) {
                        try {
                            DB::statement($sql);
                        } catch (\Throwable $e) {
                        }
                    }
                }

                self::relaxLegacyColumns('order_items', [
                    'id', 'order_id', 'product_id', 'product_name',
                    'base_price', 'toppings', 'unit_price', 'qty', 'subtotal',
                    'created_at', 'updated_at',
                ]);
            }
        } catch (\Throwable $e) {
            // Abaikan
        }
    }

    private static function relaxLegacyColumns(string $table, array $knownColumns): void
    {
        try {
            $columns = DB::select("SHOW COLUMNS FROM `{$table}`");
            foreach ($columns as $col) {
                $field = strtolower((string) $col->Field);
                if (in_array($field, $knownColumns, true)) {
                    continue;
                }
                if (strtoupper((string) $col->Null) === 'NO' && $col->Default === null) {
                    $type = strtolower((string) $col->Type);
                    if (preg_match('/int|decimal|float|double/', $type)) {
                        DB::statement("ALTER TABLE `{$table}` MODIFY COLUMN `{$col->Field}` {$col->Type} NOT NULL DEFAULT 0");
                    } else {
                        DB::statement("ALTER TABLE `{$table}` MODIFY COLUMN `{$col->Field}` {$col->Type} NULL DEFAULT NULL");
                    }
                }
            }
        } catch (\Throwable $e) {
        }
    }
}
