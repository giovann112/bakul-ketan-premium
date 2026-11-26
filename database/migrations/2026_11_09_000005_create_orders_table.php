<?php

use App\Support\SchemaGuard;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('orders')) {
            Schema::create('orders', function (Blueprint $table) {
                $table->id();
                $table->string('order_code')->unique();
                $table->foreignId('location_id')->nullable()->constrained()->nullOnDelete();
                $table->string('pickup_location_name', 100);
                $table->string('customer_name', 100);
                $table->string('phone', 20);
                $table->text('notes')->nullable();
                $table->unsignedInteger('total')->default(0);
                $table->string('status')->default('pending')->index();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('order_items')) {
            Schema::create('order_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained()->cascadeOnDelete();
                $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
                $table->string('product_name');
                $table->unsignedInteger('base_price');
                $table->json('toppings')->nullable();
                $table->unsignedInteger('unit_price');
                $table->unsignedInteger('qty');
                $table->unsignedInteger('subtotal');
                $table->timestamps();
            });
        }

        SchemaGuard::ensureOrderTables();
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
