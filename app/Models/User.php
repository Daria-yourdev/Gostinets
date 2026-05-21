<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * App\Models\User
 *
 * @property int    $id
 * @property string $name
 * @property string $email
 * @property string $role
 * @property string $password
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /** Возможные значения роли — выводим в константах, чтобы не плодить строки. */
    public const ROLE_USER  = 'user';
    public const ROLE_ADMIN = 'admin';

    /** Список всех допустимых ролей — пригодится для валидации в админке. */
    public const ROLES = [
        self::ROLE_USER,
        self::ROLE_ADMIN,
    ];

    /**
     * Поля, которые можно массово назначать.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * Поля, скрываемые при сериализации.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Касты атрибутов. password => 'hashed' автоматически хеширует
     * пароль при присвоении (Laravel 10+).
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
    ];

    /* ===========================================================
       РОЛИ
       =========================================================== */

    /** Хозяйка котла — полный доступ. */
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /** Обычный гость. */
    public function isUser(): bool
    {
        return $this->role === self::ROLE_USER;
    }

    /** Универсальная проверка по списку ролей. */
    public function hasRole(string ...$roles): bool
    {
        return in_array($this->role, $roles, true);
    }

    /* ===========================================================
       СВЯЗИ
       =========================================================== */

    public function orders(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\Order::class);
    }

    public function customJams(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\CustomJam::class);
    }
}