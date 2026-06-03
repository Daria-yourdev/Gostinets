<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * GET /admin/products
     */
    public function index(Request $request)
    {
        $query = Product::orderByDesc('id');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                    ->orWhere('slug', 'like', "%{$q}%")
                    ->orWhere('subtitle', 'like', "%{$q}%");
            });
        }

        if ($request->filled('berry')) {
            $query->where('berry_type', $request->berry);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $products = $query->paginate(20)->withQueryString();

        return view('admin.products.index', [
            'products' => $products,
            'berries'  => Product::BERRIES,
            'q'        => $request->q,
            'berry'    => $request->berry,
            'status'   => $request->status,
        ]);
    }

    /**
     * GET /admin/products/create
     */
    public function create()
    {
        return view('admin.products.create', [
            'product' => new Product(),
            'berries' => Product::BERRIES,
            'moods'   => Product::MOODS,
            'badges'  => Product::BADGES,
        ]);
    }

    /**
     * POST /admin/products
     */
    public function store(Request $request)
    {
        $data = $this->validateForm($request);
        $data['slug'] = $this->makeUniqueSlug($data['name']);

        Product::create($data);

        return redirect()->route('admin.products.index')
            ->with('flash', 'Банка добавлена в каталог');
    }

    /**
     * GET /admin/products/{product}/edit
     */
    public function edit(Product $product)
    {
        return view('admin.products.edit', [
            'product' => $product,
            'berries' => Product::BERRIES,
            'moods'   => Product::MOODS,
            'badges'  => Product::BADGES,
        ]);
    }

    /**
     * PATCH /admin/products/{product}
     */
    public function update(Request $request, Product $product)
    {
        $data = $this->validateForm($request, $product->id);

        // Slug обновляем только если имя изменилось
        if ($data['name'] !== $product->name) {
            $data['slug'] = $this->makeUniqueSlug($data['name'], $product->id);
        }

        $product->update($data);

        return redirect()->route('admin.products.edit', $product)
            ->with('flash', 'Изменения сохранены');
    }

    /**
     * DELETE /admin/products/{product}
     */
    public function destroy(Product $product)
    {
        // Если есть позиции в заказах — soft-disable через is_active
        $hasOrders = \App\Models\OrderItem::where('product_id', $product->id)->exists();
        if ($hasOrders) {
            $product->update(['is_active' => false]);
            return redirect()->route('admin.products.index')
                ->with('flash', 'Банка скрыта (есть в истории заказов — удалить нельзя)');
        }

        $product->delete();
        return redirect()->route('admin.products.index')
            ->with('flash', 'Банка убрана из каталога');
    }

    /* ============== HELPERS ============== */

    private function validateForm(Request $r, ?int $ignoreId = null): array
    {
        return $r->validate([
            'name'              => ['required', 'string', 'max:160'],
            'subtitle'          => ['nullable', 'string', 'max:120'],
            'short_description' => ['nullable', 'string', 'max:300'],
            'description'       => ['nullable', 'string', 'max:5000'],
            'berry_type'        => ['required', 'in:' . implode(',', array_keys(Product::BERRIES))],
            'mood'              => ['required', 'in:' . implode(',', array_keys(Product::MOODS))],
            'price' => ['required', 'integer', 'min:1', 'max:99999'],
            'weight'            => ['required', 'integer', 'min:1', 'max:9999'],
            'jam_color'         => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'image_path' => [
                'nullable',
                'string',
                'max:200',
                'regex:/^media\/[a-z0-9_\-\/.]+\.(png|jpg|jpeg|webp)$/i',
            ],
            'badge'             => ['nullable', 'in:' . implode(',', array_keys(Product::BADGES))],
            'is_sugar_free'     => ['nullable', 'boolean'],
            'is_gift'           => ['nullable', 'boolean'],
            'is_active'         => ['nullable', 'boolean'],
            'stock'             => ['required', 'integer', 'min:0', 'max:9999'],
        ], [
            'name.required' => 'Укажите название продукта.',
            'name.string'   => 'Название должно быть текстом.',
            'name.max'      => 'Название не должно превышать 160 символов.',

            'subtitle.string' => 'Подзаголовок должен быть текстом.',
            'subtitle.max'    => 'Подзаголовок не должен превышать 120 символов.',

            'short_description.string' => 'Краткое описание должно быть текстом.',
            'short_description.max'    => 'Краткое описание не должно превышать 300 символов.',

            'description.string' => 'Описание должно быть текстом.',
            'description.max'    => 'Описание не должно превышать 5000 символов.',

            'berry_type.required' => 'Выберите вид ягоды.',
            'berry_type.in'       => 'Выбран недопустимый вид ягоды.',

            'mood.required' => 'Выберите настроение продукта.',
            'mood.in'       => 'Выбрано недопустимое настроение.',

            'price.required' => 'Укажите цену.',
            'price.integer'  => 'Цена должна быть целым числом.',
            'price.min'      => 'Цена должна быть больше нуля.',
            'price.max'      => 'Цена слишком большая.',

            'weight.required' => 'Укажите вес.',
            'weight.integer'  => 'Вес должен быть целым числом.',
            'weight.min'      => 'Вес должен быть больше нуля.',
            'weight.max'      => 'Вес указан некорректно.',

            'jam_color.string' => 'Цвет должен быть строкой.',
            'jam_color.regex'  => 'Цвет должен быть в формате HEX, например #7E1A1A.',

            'image_path.string' => 'Путь к изображению должен быть строкой.',
            'image_path.max'    => 'Путь к изображению слишком длинный.',
            'image_path.regex'  => 'Путь должен быть вида media/catalog/имя.png.',

            'badge.in' => 'Выбран недопустимый бейдж.',

            'is_sugar_free.boolean' => 'Некорректное значение для признака "без сахара".',
            'is_gift.boolean'       => 'Некорректное значение для подарочного товара.',
            'is_active.boolean'     => 'Некорректное значение для активности товара.',

            'stock.required' => 'Укажите остаток на складе.',
            'stock.integer'  => 'Остаток должен быть целым числом.',
            'stock.min'      => 'Остаток не может быть отрицательным.',
            'stock.max'      => 'Остаток слишком большой.',
        ]) + [
            'is_sugar_free' => $r->boolean('is_sugar_free'),
            'is_gift'       => $r->boolean('is_gift'),
            'is_active'     => $r->boolean('is_active'),
        ];
    }

    private function makeUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name, '-', 'ru');
        $slug = $base;
        $i = 1;
        while (Product::where('slug', $slug)->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base . '-' . (++$i);
        }
        return $slug;
    }
}
