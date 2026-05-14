<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Добавляем поле `role` в таблицу users.
     *
     * Возможные значения:
     *   'user'  — обычный гость (по умолчанию)
     *   'admin' — хозяйка котла, имеет доступ к админке
     *
     * При необходимости добавить промежуточные роли (например, 'manager'),
     * расширьте константы в App\Models\User и middleware CheckRole.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 20)->default('user')->after('email')->index();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
