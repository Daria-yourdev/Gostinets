<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            // Идентификация и базовое описание
            $table->string('slug', 120)->unique();
            $table->string('name', 120);
            $table->string('subtitle', 80)->nullable();
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();

            // Главные оси фильтрации
            // 'vishnya' | 'malina' | 'ezhevika' | 'limon' | 'abrikos' | 'klubnika'
            $table->string('berry_type', 32)->index();

            // 'strast' | 'radost' | 'tayna' | 'yasnost' | 'pokoy' | 'lyubov'
            // Психоделический ярлык — для фильтра «на какое настроение»
            $table->string('mood', 32)->index();

            // Цена и вес
            $table->unsignedInteger('price');            // цена за стандартную банку, ₽
            $table->unsignedSmallInteger('weight')->default(250); // граммы

            // Визуал
            $table->string('jam_color', 9)->default('#7E1A1A'); // hex цвета варенья
            $table->string('image_path', 160)->nullable();

            // Маркеры
            $table->enum('badge', ['HIT', 'NEW', 'SEASON', 'GIFT'])->nullable()->index();
            $table->boolean('is_sugar_free')->default(false)->index();
            $table->boolean('is_gift')->default(false)->index();

            // Склад / видимость
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('stock')->default(50);
            $table->unsignedInteger('sold_count')->default(0); // для сортировки «популярное»

            // SEO
            $table->string('meta_title', 160)->nullable();
            $table->string('meta_description', 300)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
