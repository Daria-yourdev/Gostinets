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
        $adminPassword = env('ADMIN_SEED_PASSWORD', \Illuminate\Support\Str::random(16));

        User::updateOrCreate(
            ['email' => 'admin@gostinec.ru'],
            [
                'name'     => 'Хозяйка',
                'password' => $adminPassword,
                'role'     => 'admin',
            ]
        );

        if (app()->environment('local')) {
            $this->command->info("Admin: admin@gostinec.ru / {$adminPassword}");
        }

        // Тестовый гость — только в local
        if (app()->environment('local')) {
            User::updateOrCreate(
                ['email' => 'guest@gostinec.ru'],
                ['name' => 'Гость', 'password' => 'guest123', 'role' => 'user']
            );
        }
    }
}
