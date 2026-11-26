<?php

namespace App\Providers;

use App\Models\Location;
use App\Support\SchemaGuard;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        SchemaGuard::ensureOrderTables();

        try {
            if (Schema::hasTable('locations') && Location::count() === 0) {
                (new DatabaseSeeder())->run();
            }
        } catch (\Throwable $e) {
            // Abaikan saat perintah artisan migrate pertama kali berjalan
        }
    }
}
