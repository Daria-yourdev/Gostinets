<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * CatalogController — каталог варенья, фильтры, поиск, страница товара.
 *
 * Маршруты (см. routes/web.php):
 *   GET /catalog          — index()
 *   GET /catalog/{slug}   — show()
 *   GET /search           — search() (поиск из шапки → редиректит в /catalog?q=…)
 */
class CatalogController extends Controller
{
    /* ===========================================================
       Список каталога с фильтрами
       =========================================================== */
    public function index(Request $request): View
    {
        // --- разбираем GET-параметры -----------------------------------------
        $term     = trim((string) $request->query('q', ''));
        $berries  = $this->asArray($request->query('berry'));
        $moods    = $this->asArray($request->query('mood'));
        $badges   = $this->asArray($request->query('badge'));
        $priceMin = $this->asInt($request->query('price_min'));
        $priceMax = $this->asInt($request->query('price_max'));
        $sugarFree = $request->boolean('sugar_free');
        $giftOnly  = $request->boolean('gift');
        $sort     = (string) $request->query('sort', 'season');

        // --- собираем запрос через scope-ы модели ----------------------------
        $query = Product::query()
            ->active()
            ->search($term)
            ->berries($berries)
            ->moods($moods)
            ->badges($badges)
            ->priceBetween($priceMin, $priceMax)
            ->sugarFree($sugarFree)
            ->giftOnly($giftOnly)
            ->sorted($sort);

        $products = $query->paginate(12)->withQueryString();

        // --- метрики для UI (счётчики на чипах) ------------------------------
        $totalActive = Product::active()->count();

        // Диапазон цен для слайдера — берём min/max по всему каталогу
        $priceRange = [
            'min' => (int) (Product::active()->min('price') ?? 0),
            'max' => (int) (Product::active()->max('price') ?? 1000),
        ];

        return view('catalog', [
            'products'     => $products,
            'totalActive'  => $totalActive,
            'priceRange'   => $priceRange,

            // Эхо-значения, чтобы фильтры рендерились в нужном состоянии
            'filters' => [
                'q'         => $term,
                'berries'   => $berries,
                'moods'     => $moods,
                'badges'    => $badges,
                'price_min' => $priceMin,
                'price_max' => $priceMax,
                'sugar_free'=> $sugarFree,
                'gift'      => $giftOnly,
                'sort'      => $sort,
            ],

            // Справочники для рендера чипов
            'berries' => Product::BERRIES,
            'moods'   => Product::MOODS,
            'badges'  => Product::BADGES,
        ]);
    }

    /* ===========================================================
       Карточка одного товара
       =========================================================== */
    public function show(string $slug): View
    {
        $product = Product::active()->where('slug', $slug)->firstOrFail();

        // Похожие — та же ягода или то же настроение, исключая текущий
        $related = Product::active()
            ->where('id', '!=', $product->id)
            ->where(function ($q) use ($product) {
                $q->where('berry_type', $product->berry_type)
                  ->orWhere('mood', $product->mood);
            })
            ->orderByDesc('sold_count')
            ->limit(4)
            ->get();

        return view('product', compact('product', 'related'));
    }

    /* ===========================================================
       Поиск из шапки — простой редирект в каталог с параметром q
       =========================================================== */
    public function search(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        return redirect()->route('catalog')->withInput(['q' => $q])
            ->with('search_term', $q)
            ->setStatusCode(302)
            ->setTargetUrl(route('catalog', $q !== '' ? ['q' => $q] : []));
    }

    /* ===========================================================
       Helpers
       =========================================================== */

    /** Превращает значение GET-параметра в массив строк (даже если пришла одна строка). */
    private function asArray($value): array
    {
        if ($value === null || $value === '') return [];
        if (is_array($value)) return array_values(array_filter($value, fn($v) => $v !== ''));
        return [(string) $value];
    }

    /** Парсит число или null. */
    private function asInt($value): ?int
    {
        if ($value === null || $value === '') return null;
        if (is_numeric($value)) return (int) $value;
        return null;
    }
}
