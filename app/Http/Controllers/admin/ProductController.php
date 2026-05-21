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
            ->with('flash', 'Банка добавлена в кладовую');
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
            ->with('flash', 'Банка убрана из кладовой');
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
            'price'             => ['required', 'integer', 'min:0', 'max:99999'],
            'weight'            => ['required', 'integer', 'min:1', 'max:9999'],
            'jam_color'         => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'image_path'        => ['nullable', 'string', 'max:200'],
            'badge'             => ['nullable', 'in:' . implode(',', array_keys(Product::BADGES))],
            'is_sugar_free'     => ['nullable', 'boolean'],
            'is_gift'           => ['nullable', 'boolean'],
            'is_active'         => ['nullable', 'boolean'],
            'stock'             => ['required', 'integer', 'min:0', 'max:9999'],
        ], [
            'name.required'       => 'Без имени банка не банка.',
            'berry_type.required' => 'Из чего варено?',
            'mood.required'       => 'Какое у банки настроение?',
            'price.required'      => 'Цена обязательна.',
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
