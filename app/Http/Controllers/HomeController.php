<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Данные для мини-каталога (имитация)
        $products = [
            ['id' => 1, 'name' => 'Смартфон X100', 'price' => 49990, 'old_price' => 59990, 'image' => '/images/product1.jpg', 'rating' => 4.5],
            ['id' => 2, 'name' => 'Ноутбук Pro 15', 'price' => 89990, 'old_price' => null, 'image' => '/images/product2.jpg', 'rating' => 4.8],
            ['id' => 3, 'name' => 'Наушники AirBuds', 'price' => 4990, 'old_price' => 7990, 'image' => '/images/product3.jpg', 'rating' => 4.3],
            ['id' => 4, 'name' => 'Умные часы Watch 5', 'price' => 12990, 'old_price' => null, 'image' => '/images/product4.jpg', 'rating' => 4.6],
        ];

        // Данные для FAQ
        $faqs = [
            ['question' => 'Как оформить заказ?', 'answer' => 'Выберите товар, добавьте в корзину и перейдите к оформлению. Заполните данные и подтвердите заказ.'],
            ['question' => 'Какие способы оплаты?', 'answer' => 'Мы принимаем банковские карты, электронные деньги и наложенный платеж.'],
            ['question' => 'Сколько идет доставка?', 'answer' => 'Доставка по городу 1-2 дня, по регионам 3-7 дней.'],
            ['question' => 'Можно ли вернуть товар?', 'answer' => 'Да, в течение 14 дней с момента получения при сохранении товарного вида.'],
        ];

        // Преимущества
        $advantages = [
            ['icon' => '🚚', 'title' => 'Бесплатная доставка', 'desc' => 'При заказе от 3000 ₽'],
            ['icon' => '🔄', 'title' => 'Возврат 14 дней', 'desc' => 'Без проблем и заморочек'],
            ['icon' => '🔒', 'title' => 'Безопасная оплата', 'desc' => 'Защита ваших данных'],
            ['icon' => '⏰', 'title' => 'Поддержка 24/7', 'desc' => 'Оперативная помощь'],
        ];

        return view('home', compact('products', 'faqs', 'advantages'));
    }
}