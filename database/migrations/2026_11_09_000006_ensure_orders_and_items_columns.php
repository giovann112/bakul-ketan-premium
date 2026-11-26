<?php

use App\Support\SchemaGuard;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        SchemaGuard::ensureOrderTables();
    }

    public function down(): void
    {
        //
    }
};
