<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 20)->nullable()->after('email');
            $table->string('delivery_city', 80)->nullable();
            $table->string('delivery_zip', 10)->nullable();
            $table->string('delivery_address', 250)->nullable();
            $table->string('delivery_note', 250)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'delivery_city', 'delivery_zip', 'delivery_address', 'delivery_note']);
        });
    }
};