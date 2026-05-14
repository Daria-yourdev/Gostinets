<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custom_jams', function (Blueprint $table) {
            $table->id();

            // Привязка к пользователю — кастом доступен только зарегистрированным
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Имя кастома (на этикетке)
            $table->string('label_name', 60);

            // Главная ягода (одна) — slug из BERRIES
            $table->string('berry_main', 32);

            // Дополнительные ягоды (0–2) — JSON-массив slug'ов
            $table->json('berry_extras')->nullable();

            // Специи (0–3) — JSON-массив slug'ов: ginger, cinnamon, vanilla, cardamom, mint, lemon_zest
            $table->json('spices')->nullable();

            // Подсластитель: 'sugar' | 'honey' | 'stevia' | 'none'
            $table->string('sweetener', 16)->default('sugar');

            // Размер банки в граммах: 250 | 500 | 750
            $table->unsignedSmallInteger('jar_size')->default(250);

            // Персонализация
            $table->string('dedication', 160)->nullable();  // короткое посвящение на этикетке
            $table->string('whisper', 280)->nullable();     // «шепнуть котлу» — внутреннее пожелание мастеру

            // Расчётная цена в момент сохранения
            $table->unsignedInteger('price');

            // Статус заказа варки
            // 'draft' — черновик (в корзине / сохранён в гримуар)
            // 'ordered' — заказ оформлен
            // 'cooking' — варится
            // 'ready' — готов к выдаче / отправке
            // 'delivered' — доставлен
            $table->enum('status', ['draft', 'ordered', 'cooking', 'ready', 'delivered'])
                  ->default('draft')->index();

            $table->timestamps();

            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_jams');
    }
};
