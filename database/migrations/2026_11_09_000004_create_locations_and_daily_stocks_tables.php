<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('locations')) {
            Schema::create('locations', function (Blueprint $table) {
                $table->id();
                $table->string('name', 100);
                $table->string('slug')->unique();
                $table->string('type', 30);
                $table->string('address')->nullable();
                $table->string('hours')->nullable();
                $table->decimal('weekday_kg', 5, 2)->default(7.00);
                $table->decimal('weekend_kg', 5, 2)->default(10.00);
                $table->unsignedSmallInteger('portions_per_kg')->default(15);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('daily_stocks')) {
            Schema::create('daily_stocks', function (Blueprint $table) {
                $table->id();
                $table->foreignId('location_id')->constrained()->cascadeOnDelete();
                $table->date('stock_date');
                $table->decimal('kg_allocated', 5, 2);
                $table->unsignedInteger('total_portions');
                $table->unsignedInteger('remaining_portions');
                $table->timestamps();

                $table->unique(['location_id', 'stock_date']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_stocks');
        Schema::dropIfExists('locations');
    }
};
