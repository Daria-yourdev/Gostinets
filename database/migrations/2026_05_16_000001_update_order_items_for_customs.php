<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            // Кастомное варенье из котла
            $table->foreignId('custom_jam_id')
                ->nullable()
                ->after('product_id')
                ->constrained('custom_jams')
                ->nullOnDelete();
        });

        // Делаем product_id nullable — кастом не имеет product
        // Через raw SQL (doctrine/dbal не нужен)
        DB::statement('ALTER TABLE order_items MODIFY product_id BIGINT UNSIGNED NULL');
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['custom_jam_id']);
            $table->dropColumn('custom_jam_id');
        });

        DB::statement('ALTER TABLE order_items MODIFY product_id BIGINT UNSIGNED NOT NULL');
    }
};
