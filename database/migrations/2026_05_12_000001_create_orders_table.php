<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            // Номер для пользователя — генерируется в Order::booted()
            $table->string('number', 16)->unique();

            // Привязка к пользователю — nullable, чтобы и гости могли купить
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            // Контакты получателя
            $table->string('contact_name', 100);
            $table->string('contact_email', 160);
            $table->string('contact_phone', 32);

            // Адрес доставки
            $table->string('delivery_method', 16)->default('cdek'); // cdek | post | pickup
            $table->string('delivery_city', 80);
            $table->string('delivery_address', 200);
            $table->string('delivery_zip', 16)->nullable();
            $table->text('delivery_note')->nullable();

            // Денежные поля (всё в копейках было бы строже, но для дипломного ок и в ₽)
            $table->unsignedInteger('subtotal');       // сумма позиций
            $table->unsignedInteger('delivery_cost')->default(0);
            $table->unsignedInteger('discount')->default(0);
            $table->unsignedInteger('total');          // итог к оплате

            // Статусы
            // pending   — создан, ждёт оплаты
            // paid      — оплачен через ЮКассу
            // processing — варится
            // shipped   — отправлен
            // delivered — доставлен
            // canceled  — отменён
            $table->enum('status', [
                'pending', 'paid', 'processing', 'shipped', 'delivered', 'canceled'
            ])->default('pending')->index();

            // Интеграция с ЮКассой
            $table->string('yookassa_payment_id', 64)->nullable()->index();
            $table->string('yookassa_status', 32)->nullable(); // pending|waiting_for_capture|succeeded|canceled
            $table->json('yookassa_payload')->nullable();       // последний webhook payload — для отладки

            $table->timestamp('paid_at')->nullable();
            $table->timestamp('canceled_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
