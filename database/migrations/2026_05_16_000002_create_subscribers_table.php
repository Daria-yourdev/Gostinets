<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('email', 160)->unique();
            $table->string('name', 80)->nullable();  // необязательное имя
            $table->string('source', 40)->default('footer'); // откуда подписался
            $table->timestamp('confirmed_at')->nullable();   // на будущее — double opt-in
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscribers');
    }
};
