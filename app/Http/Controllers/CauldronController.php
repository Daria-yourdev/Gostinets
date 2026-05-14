<?php

namespace App\Http\Controllers;

use App\Models\CustomJam;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * CauldronController — конструктор кастомного варенья.
 *
 * Маршруты:
 *   GET  /cauldron           — index() — страница конструктора (для всех)
 *   POST /cauldron/preview   — preview() — пересчёт цены AJAX (для всех — нужен в UI)
 *   POST /cauldron           — store()   — сохранение/в корзину (middleware:auth)
 *   GET  /cauldron/grimoire  — grimoire() — «гримуар» — мои сохранённые рецепты
 *
 * Доступ к самому конструктору открыт всем, но кнопка «Бросить в котёл»
 * для гостей превращается в призыв войти.
 */
class CauldronController extends Controller
{
    /** Справочник специй с ценами — используется в UI и валидации. */
    public const SPICES = [
        'ginger'     => 'Имбирь',
        'cinnamon'   => 'Корица',
        'vanilla'    => 'Ваниль',
        'cardamom'   => 'Кардамон',
        'mint'       => 'Мята',
        'lemon_zest' => 'Лимонная цедра',
    ];

    /** Справочник подсластителей. */
    public const SWEETENERS = [
        'sugar'  => 'Тростниковый сахар',
        'honey'  => 'Гречишный мёд',
        'stevia' => 'Стевия',
        'none'   => 'Без подсластителя',
    ];

    /* ===========================================================
       Страница конструктора
       =========================================================== */
    public function index(): View
    {
        return view('cauldron', [
            'berries'    => \App\Models\Product::BERRIES,
            'spices'     => self::SPICES,
            'sweeteners' => self::SWEETENERS,

            // Прайс-лист — отдаём в JS как JSON, чтобы пересчёт был мгновенным.
            'priceMap'   => [
                'base'         => CustomJam::BASE_COOK_FEE,
                'extraBerry'   => CustomJam::EXTRA_BERRY_FEE,
                'spice'        => CustomJam::SPICE_FEE,
                'dedication'   => CustomJam::DEDICATION_FEE,
                'berries'      => CustomJam::BERRY_PRICES,
                'sweeteners'   => CustomJam::SWEETENER_FEE,
                'sizes'        => CustomJam::SIZE_MULTIPLIER,
            ],

            // Гостям покажем баннер регистрации, авторизованным — реальную кнопку
            'canCook'    => auth()->check(),
        ]);
    }

    /* ===========================================================
       Пересчёт цены — AJAX
       Возвращает JSON, ничего не сохраняет.
       =========================================================== */
    public function preview(Request $request)
    {
        $data = $this->validatePayload($request, partial: true);
        return response()->json([
            'price' => CustomJam::calculatePrice($data),
        ]);
    }

    /* ===========================================================
       Сохранение в гримуар / отправка в варку
       Только для авторизованных.
       =========================================================== */
    public function store(Request $request)
    {
        // Двойная защита, на случай если middleware пропустит
        if (!auth()->check()) {
            return response()->json([
                'ok' => false,
                'message' => 'Сначала войди в избу — кастом доступен только гостям с грамотой.',
            ], 401);
        }

        $data = $this->validatePayload($request, partial: false);

        $jam = CustomJam::create([
            'user_id'      => auth()->id(),
            'label_name'   => $data['label_name'],
            'berry_main'   => $data['berry_main'],
            'berry_extras' => $data['berry_extras'] ?? [],
            'spices'       => $data['spices'] ?? [],
            'sweetener'    => $data['sweetener'],
            'jar_size'     => $data['jar_size'],
            'dedication'   => $data['dedication'] ?? null,
            'whisper'      => $data['whisper'] ?? null,
            'price'        => CustomJam::calculatePrice($data),
            'status'       => $request->input('action') === 'order' ? 'ordered' : 'draft',
        ]);

        return response()->json([
            'ok'      => true,
            'jam_id'  => $jam->id,
            'price'   => $jam->price,
            'message' => $jam->status === 'ordered'
                ? 'Брошено в котёл. Дарина уже разжигает огонь.'
                : 'Записано в гримуар. Сможешь вернуться и заказать варку позже.',
            'redirect'=> $jam->status === 'ordered' ? url('/orders') : null,
        ]);
    }

    /* ===========================================================
       Гримуар — список сохранённых рецептов текущего пользователя
       =========================================================== */
    public function grimoire(): View
    {
        $jams = CustomJam::where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('cauldron-grimoire', compact('jams'));
    }

    /* ===========================================================
       Валидация payload-а — общая логика для preview() и store()
       =========================================================== */
    private function validatePayload(Request $request, bool $partial): array
    {
        $rules = [
            'label_name'   => ['required', 'string', 'min:2', 'max:60'],
            'berry_main'   => ['required', Rule::in(array_keys(\App\Models\Product::BERRIES))],
            'berry_extras' => ['nullable', 'array', 'max:2'],
            'berry_extras.*' => [Rule::in(array_keys(\App\Models\Product::BERRIES))],
            'spices'       => ['nullable', 'array', 'max:3'],
            'spices.*'     => [Rule::in(array_keys(self::SPICES))],
            'sweetener'    => ['required', Rule::in(array_keys(self::SWEETENERS))],
            'jar_size'     => ['required', Rule::in([250, 500, 750])],
            'dedication'   => ['nullable', 'string', 'max:160'],
            'whisper'      => ['nullable', 'string', 'max:280'],
        ];

        // При preview-расчёте многие поля могут быть пусты — смягчаем required-ы
        if ($partial) {
            $rules['label_name'] = ['nullable', 'string', 'max:60'];
            $rules['berry_main'] = ['nullable', Rule::in(array_keys(\App\Models\Product::BERRIES))];
            $rules['sweetener']  = ['nullable', Rule::in(array_keys(self::SWEETENERS))];
            $rules['jar_size']   = ['nullable', Rule::in([250, 500, 750])];
        }

        return $request->validate($rules, [
            'label_name.required' => 'Не забудь имя для банки.',
            'label_name.min'      => 'Имя слишком короткое.',
            'berry_main.required' => 'Выбери главную ягоду — без неё котёл не варит.',
            'berry_main.in'       => 'Такой ягоды у нас нет.',
            'berry_extras.max'    => 'Не больше двух ягод-компаньонов.',
            'spices.max'          => 'Не больше трёх специй.',
            'sweetener.required'  => 'Выбери, чем сладить.',
            'jar_size.required'   => 'Выбери размер банки.',
            'jar_size.in'         => 'Такой банки у нас нет.',
        ]);
    }
}
