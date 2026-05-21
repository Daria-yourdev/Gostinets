<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Добавляем custom_jam_id — работает на всех СУБД
        Schema::table('order_items', function (Blueprint $table) {
            $table->foreignId('custom_jam_id')
                ->nullable()
                ->after('product_id')
                ->constrained('custom_jams')
                ->nullOnDelete();
        });

        // 2. Делаем product_id nullable — по-разному в разных СУБД
        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE order_items DROP FOREIGN KEY order_items_product_id_foreign');
            DB::statement('ALTER TABLE order_items MODIFY product_id BIGINT UNSIGNED NULL');
            DB::statement('ALTER TABLE order_items ADD CONSTRAINT order_items_product_id_foreign FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT');
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE order_items ALTER COLUMN product_id DROP NOT NULL');
        }
        // SQLite — пропускаем, он не enforce'ит NOT NULL для FK по умолчанию
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['custom_jam_id']);
            $table->dropColumn('custom_jam_id');
        });

        $driver = DB::connection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE order_items DROP FOREIGN KEY order_items_product_id_foreign');
            DB::statement('ALTER TABLE order_items MODIFY product_id BIGINT UNSIGNED NOT NULL');
            DB::statement('ALTER TABLE order_items ADD CONSTRAINT order_items_product_id_foreign FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT');
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE order_items ALTER COLUMN product_id SET NOT NULL');
        }
    }
};