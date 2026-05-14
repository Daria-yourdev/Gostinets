<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * AdminSeeder — создаёт стартового админа.
 *
 * Запуск:
 *   php artisan db:seed --class=AdminSeeder
 *
 * Или зарегистрируйте вызов в DatabaseSeeder::run():
 *   $this->call(AdminSeeder::class);
 */
class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // updateOrCreate, чтобы можно было переcидить без дублей
        User::updateOrCreate(
            ['email' => 'admin@gostinec.ru'],
            [
                'name'     => 'Дарина',
                'password' => 'admin123', // автоматически хешируется (cast 'hashed')
                'role'     => User::ROLE_ADMIN,
            ]
        );

        // Тестовый обычный пользователь — удобно для проверки разграничения
        User::updateOrCreate(
            ['email' => 'guest@gostinec.ru'],
            [
                'name'     => 'Иван Гость',
                'password' => 'guest123',
                'role'     => User::ROLE_USER,
            ]
        );
    }
}
