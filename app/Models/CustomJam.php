<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\CustomJam
 *
 * @property int    $id
 * @property int    $user_id
 * @property string $label_name
 * @property string $berry_main
 * @property array  $berry_extras
 * @property array  $spices
 * @property string $sweetener
 * @property int    $jar_size
 * @property string|null $dedication
 * @property string|null $whisper
 * @property int    $price
 * @property string $status
 */
class CustomJam extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'label_name',
        'berry_main',
        'berry_extras',
        'spices',
        'sweetener',
        'jar_size',
        'dedication',
        'whisper',
        'price',
        'status',
    ];

    protected $casts = [
        'berry_extras' => 'array',
        'spices'       => 'array',
        'jar_size'     => 'integer',
        'price'        => 'integer',
    ];

    /* ===========================================================
       ПРАЙС-ЛИСТ ингредиентов и опций.
       Здесь же единая точка правды для расчёта цены — и на сервере,
       и для JS (см. cauldron.js: те же ключи и числа).
       =========================================================== */

    /** Базовая стоимость варки (работа + банка 250 г + крышка + этикетка). */
    public const BASE_COOK_FEE = 250;

    /** Доплата за каждую дополнительную ягоду к основной. */
    public const EXTRA_BERRY_FEE = 80;

    /** Доплата за каждую специю. */
    public const SPICE_FEE = 40;

    /** Цены за ягоду — основа. */
    public const BERRY_PRICES = [
        'vishnya'   => 150,
        'malina'    => 180,
        'ezhevika'  => 200,
        'limon'     => 120,
        'abrikos'   => 140,
        'klubnika'  => 170,
        // Новые:
        'klukva'    => 160,   // клюква — кислая, редкая
        'grusha'    => 130,   // груша — нежная
        'yabloko'   => 120,   // яблоко — доступное
        'shishka'   => 240,   // шишка — штучный ингредиент, дороже
    ];

    /** Доплата за подсластитель. */
    public const SWEETENER_FEE = [
        'sugar'   => 0,
        'honey'   => 70,
        'stevia'  => 50,
        'none'    => 0, // без подсластителя — на воле ягоды
    ];

    /** Множитель цены по размеру банки. */
    public const SIZE_MULTIPLIER = [
        250 => 1.0,
        500 => 1.85,
        750 => 2.6,
    ];

    /** Доплата за персональное посвящение на этикетке. */
    public const DEDICATION_FEE = 60;

    /* ===========================================================
       РАСЧЁТ ЦЕНЫ — статически, чтобы вызывать и из контроллера,
       и для предпросмотра.
       =========================================================== */
    public static function calculatePrice(array $data): int
    {
        $berryMain  = $data['berry_main'] ?? null;
        $extras     = $data['berry_extras'] ?? [];
        $spices     = $data['spices'] ?? [];
        $sweetener  = $data['sweetener'] ?? 'sugar';
        $jarSize    = (int) ($data['jar_size'] ?? 250);
        $dedication = !empty($data['dedication']);

        $price = self::BASE_COOK_FEE;
        $price += self::BERRY_PRICES[$berryMain] ?? 0;
        $price += count($extras) * self::EXTRA_BERRY_FEE;
        $price += count($spices) * self::SPICE_FEE;
        $price += self::SWEETENER_FEE[$sweetener] ?? 0;
        if ($dedication) {
            $price += self::DEDICATION_FEE;
        }

        $multiplier = self::SIZE_MULTIPLIER[$jarSize] ?? 1.0;
        return (int) round($price * $multiplier);
    }

    /* ===========================================================
       Связи
       =========================================================== */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
