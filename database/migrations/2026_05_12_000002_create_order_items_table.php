<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();

            // Снепшоты — на случай если товар изменится / удалится из каталога
            $table->string('product_name', 160);
            $table->string('product_subtitle', 120)->nullable();
            $table->string('product_image', 200)->nullable();

            $table->unsignedInteger('price');   // цена за единицу в момент заказа
            $table->unsignedSmallInteger('qty');
            $table->unsignedInteger('subtotal'); // price * qty

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
