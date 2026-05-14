<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id', 'product_id',
        'product_name', 'product_subtitle', 'product_image',
        'price', 'qty', 'subtotal',
    ];

    protected $casts = [
        'price'    => 'integer',
        'qty'      => 'integer',
        'subtotal' => 'integer',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function priceFormatted(): string
    {
        return number_format($this->price, 0, '.', ' ') . ' ₽';
    }

    public function subtotalFormatted(): string
    {
        return number_format($this->subtotal, 0, '.', ' ') . ' ₽';
    }
}
