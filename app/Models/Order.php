<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * Order — заказ варенья.
 *
 * @property int    $id
 * @property string $number
 * @property int|null $user_id
 * @property string $status
 * @property int    $total
 * @property string|null $yookassa_payment_id
 * @property \Carbon\Carbon|null $paid_at
 */
class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'number', 'user_id',
        'contact_name', 'contact_email', 'contact_phone',
        'delivery_method', 'delivery_city', 'delivery_address',
        'delivery_zip', 'delivery_note',
        'subtotal', 'delivery_cost', 'discount', 'total',
        'status',
        'yookassa_payment_id', 'yookassa_status', 'yookassa_payload',
        'paid_at', 'canceled_at',
    ];

    protected $casts = [
        'yookassa_payload' => 'array',
        'subtotal'         => 'integer',
        'delivery_cost'    => 'integer',
        'discount'         => 'integer',
        'total'            => 'integer',
        'paid_at'          => 'datetime',
        'canceled_at'      => 'datetime',
    ];

    /** Способы доставки. */
    public const DELIVERY_METHODS = [
        'cdek'   => ['label' => 'СДЭК', 'cost' => 350, 'eta' => '3–7 дней'],
        'post'   => ['label' => 'Почта России', 'cost' => 250, 'eta' => '7–21 день'],
        'pickup' => ['label' => 'Самовывоз из Казани', 'cost' => 0, 'eta' => 'по договорённости'],
    ];

    /** Человекочитаемые имена статусов. */
    public const STATUS_LABELS = [
        'pending'    => 'Ждёт оплаты',
        'paid'       => 'Оплачен',
        'processing' => 'Варится',
        'shipped'    => 'В пути',
        'delivered'  => 'Доставлен',
        'canceled'   => 'Отменён',
    ];

    /* ===========================================================
       Автогенерация номера заказа при создании
       =========================================================== */
    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            if (empty($order->number)) {
                // Г250512-A1B2 — Г + дата + случайная часть
                $order->number = 'Г' . date('ymd') . '-' . strtoupper(Str::random(4));
            }
        });
    }

    /* ===========================================================
       Связи
       =========================================================== */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /* ===========================================================
       Helpers
       =========================================================== */

    public function statusLabel(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    public function deliveryLabel(): string
    {
        return self::DELIVERY_METHODS[$this->delivery_method]['label'] ?? $this->delivery_method;
    }

    public function totalFormatted(): string
    {
        return number_format($this->total, 0, '.', ' ') . ' ₽';
    }

    public function isPaid(): bool
    {
        return in_array($this->status, ['paid', 'processing', 'shipped', 'delivered'], true);
    }

    public function isCanceled(): bool
    {
        return $this->status === 'canceled';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
}
