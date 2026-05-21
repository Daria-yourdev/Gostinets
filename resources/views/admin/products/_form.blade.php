{{-- Общая форма товара — используется и в create, и в edit --}}

<div class="admin-form-grid">

    {{-- Левая колонка — основное --}}
    <div class="admin-form-main">

        <section class="admin-block">
            <header class="admin-block__head"><h2>Описание</h2></header>

            <label class="admin-field">
                <span class="admin-field__label">Название <em>*</em></span>
                <input type="text" name="name" maxlength="160" required
                       value="{{ old('name', $product->name) }}" placeholder="Вишнёвое варенье">
                @error('name')<span class="admin-field__err">{{ $message }}</span>@enderror
            </label>

            <label class="admin-field">
                <span class="admin-field__label">Подзаголовок (рукопись)</span>
                <input type="text" name="subtitle" maxlength="120"
                       value="{{ old('subtitle', $product->subtitle) }}" placeholder="с косточкой">
                @error('subtitle')<span class="admin-field__err">{{ $message }}</span>@enderror
            </label>

            <label class="admin-field">
                <span class="admin-field__label">Короткое описание</span>
                <textarea name="short_description" maxlength="300" rows="2"
                          placeholder="Одна-две фразы для карточки в каталоге">{{ old('short_description', $product->short_description) }}</textarea>
                @error('short_description')<span class="admin-field__err">{{ $message }}</span>@enderror
            </label>

            <label class="admin-field">
                <span class="admin-field__label">Полное описание (сказ)</span>
                <textarea name="description" maxlength="5000" rows="6"
                          placeholder="История банки — её показывают на странице товара">{{ old('description', $product->description) }}</textarea>
                @error('description')<span class="admin-field__err">{{ $message }}</span>@enderror
            </label>
        </section>

        <section class="admin-block">
            <header class="admin-block__head"><h2>Происхождение</h2></header>

            <div class="admin-form-row">
                <label class="admin-field">
                    <span class="admin-field__label">Ягода <em>*</em></span>
                    <select name="berry_type" required>
                        @foreach($berries as $key => $label)
                            <option value="{{ $key }}" {{ old('berry_type', $product->berry_type) === $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('berry_type')<span class="admin-field__err">{{ $message }}</span>@enderror
                </label>

                <label class="admin-field">
                    <span class="admin-field__label">Настроение <em>*</em></span>
                    <select name="mood" required>
                        @foreach($moods as $key => $label)
                            <option value="{{ $key }}" {{ old('mood', $product->mood) === $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('mood')<span class="admin-field__err">{{ $message }}</span>@enderror
                </label>
            </div>

            <div class="admin-form-row">
                <label class="admin-field">
                    <span class="admin-field__label">Метка</span>
                    <select name="badge">
                        <option value="">— нет —</option>
                        @foreach($badges as $key => $label)
                            <option value="{{ $key }}" {{ old('badge', $product->badge) === $key ? 'selected' : '' }}>
                                {{ $key }} ({{ $label }})
                            </option>
                        @endforeach
                    </select>
                </label>

                <label class="admin-field">
                    <span class="admin-field__label">Цвет варенья (hex)</span>
                    <input type="text" name="jam_color" maxlength="7" pattern="^#[0-9A-Fa-f]{6}$"
                           value="{{ old('jam_color', $product->jam_color) }}" placeholder="#7E1A1A">
                    @error('jam_color')<span class="admin-field__err">{{ $message }}</span>@enderror
                </label>
            </div>
        </section>

        <section class="admin-block">
            <header class="admin-block__head"><h2>Изображение</h2></header>
            <label class="admin-field">
                <span class="admin-field__label">Путь к файлу</span>
                <input type="text" name="image_path" maxlength="200"
                       value="{{ old('image_path', $product->image_path) }}"
                       placeholder="media/catalog/vishnya.png">
                <span class="admin-field__hint">
                    Положи картинку в <code>public/media/catalog/</code> и пропиши путь от <code>public/</code>.
                </span>
                @error('image_path')<span class="admin-field__err">{{ $message }}</span>@enderror
            </label>

            @if($product->image_path)
                <div class="admin-form-preview">
                    <img src="{{ asset($product->image_path) }}" alt="">
                </div>
            @endif
        </section>
    </div>

    {{-- Правая колонка — коммерция --}}
    <aside class="admin-form-side">

        <section class="admin-block">
            <header class="admin-block__head"><h2>Цена и остаток</h2></header>

            <label class="admin-field">
                <span class="admin-field__label">Цена, ₽ <em>*</em></span>
                <input type="number" name="price" min="0" max="99999" required
                       value="{{ old('price', $product->price ?: 0) }}">
                @error('price')<span class="admin-field__err">{{ $message }}</span>@enderror
            </label>

            <label class="admin-field">
                <span class="admin-field__label">Вес, г <em>*</em></span>
                <input type="number" name="weight" min="1" max="9999" required
                       value="{{ old('weight', $product->weight ?: 250) }}">
                @error('weight')<span class="admin-field__err">{{ $message }}</span>@enderror
            </label>

            <label class="admin-field">
                <span class="admin-field__label">Остаток на складе</span>
                <input type="number" name="stock" min="0" max="9999"
                       value="{{ old('stock', $product->stock ?: 0) }}">
                @error('stock')<span class="admin-field__err">{{ $message }}</span>@enderror
            </label>
        </section>

        <section class="admin-block">
            <header class="admin-block__head"><h2>Свойства</h2></header>

            <label class="admin-check">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1"
                       {{ old('is_active', $product->id ? $product->is_active : true) ? 'checked' : '' }}>
                <span>В кладовой (видна покупателям)</span>
            </label>

            <label class="admin-check">
                <input type="hidden" name="is_sugar_free" value="0">
                <input type="checkbox" name="is_sugar_free" value="1"
                       {{ old('is_sugar_free', $product->is_sugar_free) ? 'checked' : '' }}>
                <span>Без сахара</span>
            </label>

            <label class="admin-check">
                <input type="hidden" name="is_gift" value="0">
                <input type="checkbox" name="is_gift" value="1"
                       {{ old('is_gift', $product->is_gift) ? 'checked' : '' }}>
                <span>В подарок</span>
            </label>
        </section>

        <button type="submit" class="admin-btn admin-btn--primary admin-btn--wide">
            {{ $product->exists ? 'Сохранить' : 'Создать' }}
        </button>

        @if($product->exists)
            <a href="{{ route('product', $product->slug) }}" class="admin-btn admin-btn--ghost admin-btn--wide" target="_blank">
                Смотреть на сайте →
            </a>
        @endif
    </aside>
</div>