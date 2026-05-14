<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Product
 *
 * @property int    $id
 * @property string $slug
 * @property string $name
 * @property string $subtitle
 * @property string $berry_type
 * @property string $mood
 * @property int    $price
 * @property int    $weight
 * @property string $jam_color
 * @property string|null $badge
 * @property bool   $is_sugar_free
 * @property bool   $is_gift
 * @property bool   $is_active
 * @property int    $stock
 * @property int    $sold_count
 */
class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug', 'name', 'subtitle', 'short_description', 'description',
        'berry_type', 'mood', 'price', 'weight', 'jam_color', 'image_path',
        'badge', 'is_sugar_free', 'is_gift', 'is_active',
        'stock', 'sold_count', 'meta_title', 'meta_description',
    ];

    protected $casts = [
        'is_sugar_free' => 'boolean',
        'is_gift'       => 'boolean',
        'is_active'     => 'boolean',
        'price'         => 'integer',
        'weight'        => 'integer',
        'stock'         => 'integer',
        'sold_count'    => 'integer',
    ];

    /* ===========================================================
       СПРАВОЧНИКИ — единая точка правды для UI/валидации/фильтров.
       =========================================================== */

    /** Виды ягод: slug => читаемое имя. */
    public const BERRIES = [
        'vishnya'   => 'Вишня',
        'malina'    => 'Малина',
        'ezhevika'  => 'Ежевика',
        'limon'     => 'Лимон',
        'abrikos'   => 'Абрикос',
        'klubnika'  => 'Клубника',
        // Расширенный список:
        'klukva'    => 'Клюква',
        'grusha'    => 'Груша',
        'yabloko'   => 'Яблоко',
        'shishka'   => 'Шишка',   // сосновая/кедровая — психоделический ингредиент
    ];

    /** «Настроения» — психоделический фильтр. */
    public const MOODS = [
        'strast'   => 'Страсть',
        'radost'   => 'Радость',
        'tayna'    => 'Тайна',
        'yasnost'  => 'Ясность',
        'pokoy'    => 'Покой',
        'lyubov'   => 'Любовь',
    ];

    /** Возможные значки на карточке (badge). */
    public const BADGES = [
        'HIT'    => 'хит',
        'NEW'    => 'новинка',
        'SEASON' => 'сезон',
        'GIFT'   => 'в подарок',
    ];

    /**
     * Предсказания ягоды — появляются на странице товара.
     * Вдохновлены oracle-блоком на главной.
     */
    public const MOOD_PREDICTIONS = [
        'strast'  => 'Жжёт воспоминания. Будит что-то давно спящее в груди.',
        'radost'  => 'Ты уже думаешь о лете, пока ещё зима за окном.',
        'tayna'   => 'Тёмный лес, полнолуние, тихий шёпот листьев.',
        'yasnost' => 'Всё встанет на своё место. Открой банку и подожди.',
        'pokoy'   => 'Тихий двор, медная крыша, солнце ровно в полдень.',
        'lyubov'  => 'Кто-то думает о тебе прямо сейчас.',
    ];

    /**
     * Лор ягоды — краткая история про каждый вид.
     * Отображается на вкладке «О ягоде» страницы товара.
     */
    public const BERRY_LORE = [
        'vishnya'  => 'Тёмная, с косточкой, которая хранит миндальный дух. Вишнёвое варенье называли «варкой памяти» — его открывали зимой, чтобы почувствовать лето. Мы берём только зрелую вишню с черешком, варим в медном тазу на медленном огне.',
        'malina'   => 'Лесная малина собирается ранним утром, пока роса не высохла. Говорят, она помогает при первой простуде и первой тоске. Короткая варка сохраняет кислинку, которой не добиться никакими добавками.',
        'ezhevika' => 'Самая мистическая ягода. Ежевика колется, прячется в тёмных углах сада — и даёт самое тёмное, насыщенное варенье. Варится ночью, собирается на закате. Её сок окрашивает руки на несколько дней.',
        'limon'    => 'Лимон не из местных садов, но в медном котле становится своим. Ясный, острый, весенний. Мы варим его целым, с цедрой и косточками — именно они дают нужную горчинку.',
        'abrikos'  => 'Ягода покоя и тихого южного утра. Золотая, нежная, с лёгкой кислинкой у кожицы. Абрикосовое варенье варится в два приёма — чтобы ягода сохранила форму и не превратилась в повидло.',
        'klubnika' => 'Первый сбор в июне. Берём только средние ягоды с зелёным черешком — они ароматнее и крепче больших. Варка — короткая: 15 минут, чтобы клубника осталась живой.',
        'klukva'   => 'Болотная ягода, кислая как честность. Клюква не принимает фальши — в варенье она сохраняет характер. Горьковатая нота уходит на третий день после варки, когда банка «настаивается».',
        'grusha'   => 'Груша варится медленно, долго держит форму. Мягкая снаружи, с твёрдым характером внутри. К ней хорошо идёт кардамон или ваниль — они подчёркивают сладость без назойливости.',
        'yabloko'  => 'Яблоко — самая смиренная ягода. Берёт всё, что даёшь: корицу, ваниль, имбирь — и соединяет в тёплом, зимнем варенье. Мы берём антоновку или семеринку — их кислота не даёт варенью быть приторным.',
        'shishka'  => 'Сосновую шишку собирают молодой, в начале июня, пока она ещё зелёная и мягкая. В варенье она отдаёт смолистую ноту и дух хвойного леса. Один из старейших рецептов — его знали ещё травники.',
    ];

    /* ===========================================================
       SCOPES — собирают цепочку для фильтрации в каталоге.
       Используется в CatalogController.
       =========================================================== */

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('is_active', true);
    }

    public function scopeSearch(Builder $q, ?string $term): Builder
    {
        if (!$term) return $q;
        $like = '%' . trim($term) . '%';
        return $q->where(function (Builder $q) use ($like) {
            $q->where('name', 'like', $like)
              ->orWhere('subtitle', 'like', $like)
              ->orWhere('short_description', 'like', $like)
              ->orWhere('description', 'like', $like);
        });
    }

    public function scopeBerries(Builder $q, array $berries): Builder
    {
        return empty($berries) ? $q : $q->whereIn('berry_type', $berries);
    }

    public function scopeMoods(Builder $q, array $moods): Builder
    {
        return empty($moods) ? $q : $q->whereIn('mood', $moods);
    }

    public function scopeBadges(Builder $q, array $badges): Builder
    {
        return empty($badges) ? $q : $q->whereIn('badge', $badges);
    }

    public function scopePriceBetween(Builder $q, ?int $min, ?int $max): Builder
    {
        if ($min !== null) $q->where('price', '>=', $min);
        if ($max !== null) $q->where('price', '<=', $max);
        return $q;
    }

    public function scopeSugarFree(Builder $q, bool $only): Builder
    {
        return $only ? $q->where('is_sugar_free', true) : $q;
    }

    public function scopeGiftOnly(Builder $q, bool $only): Builder
    {
        return $only ? $q->where('is_gift', true) : $q;
    }

    public function scopeSorted(Builder $q, string $sort = 'popular'): Builder
    {
        return match ($sort) {
            'cheap'   => $q->orderBy('price', 'asc'),
            'expensive' => $q->orderBy('price', 'desc'),
            'new'     => $q->orderBy('created_at', 'desc'),
            default   => $q->orderBy('sold_count', 'desc')->orderBy('created_at', 'desc'),
        };
    }

    /* ===========================================================
       HELPERS для отображения
       =========================================================== */

    /** Человекочитаемое имя ягоды («Вишня»). */
    public function berryLabel(): string
    {
        return self::BERRIES[$this->berry_type] ?? $this->berry_type;
    }

    /** Человекочитаемое настроение («Страсть»). */
    public function moodLabel(): string
    {
        return self::MOODS[$this->mood] ?? $this->mood;
    }

    /** Цена с пробелом и знаком ₽: 320 ₽. */
    public function priceFormatted(): string
    {
        return number_format($this->price, 0, '.', ' ') . ' ₽';
    }

    /** На складе? */
    public function inStock(): bool
    {
        return $this->is_active && $this->stock > 0;
    }

    /** Предсказание для текущего настроения. */
    public function moodPrediction(): string
    {
        return self::MOOD_PREDICTIONS[$this->mood] ?? '';
    }

    /** Лор ягоды (история). */
    public function berryLore(): string
    {
        return self::BERRY_LORE[$this->berry_type] ?? '';
    }

    /** Hex-цвет варенья с фоллбэком. */
    public function jamColor(): string
    {
        return $this->jam_color ?: '#7E1A1A';
    }
}