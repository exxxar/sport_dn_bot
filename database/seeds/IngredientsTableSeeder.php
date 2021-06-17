<?php

use App\Ingredient;
use Illuminate\Database\Seeder;

class IngredientsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //

        Ingredient::truncate();

        $ingredients = [
            [
                "name" => "Лосось", "price" => 100, "type" => \App\Enums\UseIngredientType::Coating
            ],
            [
                "name" => "Тунец", "price" => 110, "type" => \App\Enums\UseIngredientType::Coating
            ],
            [
                "name" => "Угорь", "price" => 130, "type" => \App\Enums\UseIngredientType::Coating
            ],
            [
                "name" => "Креветка Тигровая", "price" => 140, "type" => \App\Enums\UseIngredientType::Coating
            ],
            [
                "name" => "Окунь", "price" => 60, "type" => \App\Enums\UseIngredientType::Coating
            ],
            [
                "name" => "Кунжут Белый", "price" => 20, "type" => \App\Enums\UseIngredientType::Coating
            ],
            [
                "name" => "Кунжут Черный", "price" => 20, "type" => \App\Enums\UseIngredientType::Coating
            ],
            [
                "name" => "Кунжут ч/б", "price" => 30, "type" => \App\Enums\UseIngredientType::Coating
            ],
            [
                "name" => "Икра Тобико Красная", "price" => 50, "type" => \App\Enums\UseIngredientType::Coating
            ],
            [
                "name" => "Икра Тобико Зелёная", "price" => 50, "type" => \App\Enums\UseIngredientType::Coating
            ],
            [
                "name" => "Икра Тобико Черная", "price" => 50, "type" => \App\Enums\UseIngredientType::Coating
            ],
            [
                "name" => "Старушка Тунца", "price" => 30, "type" => \App\Enums\UseIngredientType::Coating
            ],
            [
                "name" => "Огурец", "price" => 20, "type" => \App\Enums\UseIngredientType::Coating
            ],
            [
                "name" => "Авакадо", "price" => 100, "type" => \App\Enums\UseIngredientType::Coating
            ],
            [
                "name" => "Укроп", "price" => 10, "type" => \App\Enums\UseIngredientType::Coating
            ],
            [
                "name" => "Нори", "price" => 15, "type" => \App\Enums\UseIngredientType::Coating
            ],
            [
                "name" => "Лосось", "price" => 50, "type" => \App\Enums\UseIngredientType::Filling
            ],
            [
                "name" => "Тунец", "price" => 50, "type" => \App\Enums\UseIngredientType::Filling
            ],
            [
                "name" => "Угорь", "price" => 65, "type" => \App\Enums\UseIngredientType::Filling
            ],
            [
                "name" => "Креветка Тигровая", "price" => 70, "type" => \App\Enums\UseIngredientType::Filling
            ],
            [
                "name" => "Окунь", "price" => 30, "type" => \App\Enums\UseIngredientType::Filling
            ],
            [
                "name" => "Снежный Краб", "price" => 30, "type" => \App\Enums\UseIngredientType::Filling
            ],
            [
                "name" => "Креветка Салатная", "price" => 40, "type" => \App\Enums\UseIngredientType::Filling
            ],
            [
                "name" => "Мидии", "price" => 30, "type" => \App\Enums\UseIngredientType::Filling
            ],
            [
                "name" => "Кальмар", "price" => 30, "type" => \App\Enums\UseIngredientType::Filling
            ],
            [
                "name" => "Курица Жареная", "price" => 30, "type" => \App\Enums\UseIngredientType::Filling
            ],
            [
                "name" => "Лосось Жаренный", "price" => 55, "type" => \App\Enums\UseIngredientType::Filling
            ],
            [
                "name" => "Окунь Жаренный", "price" => 35, "type" => \App\Enums\UseIngredientType::Filling
            ],
            [
                "name" => "Сливочный Сыр", "price" => 30, "type" => \App\Enums\UseIngredientType::Filling
            ],
            [
                "name" => "Плаванный сыр", "price" => 25, "type" => \App\Enums\UseIngredientType::Filling
            ],
            [
                "name" => "Икра Тобико Красная", "price" => 50, "type" => \App\Enums\UseIngredientType::Filling
            ],
            [
                "name" => "Икра Лосося", "price" => 100, "type" => \App\Enums\UseIngredientType::Filling
            ],
            [
                "name" => "Огурец", "price" => 10, "type" => \App\Enums\UseIngredientType::Filling
            ],
            [
                "name" => "Авакадо", "price" => 50, "type" => \App\Enums\UseIngredientType::Filling
            ],
            [
                "name" => "Чука", "price" => 15, "type" => \App\Enums\UseIngredientType::Filling
            ],
            [
                "name" => "Помидор", "price" => 15, "type" => \App\Enums\UseIngredientType::Filling
            ],
            [
                "name" => "Сладкиц Перец ", "price" => 15, "type" => \App\Enums\UseIngredientType::Filling
            ],
            [
                "name" => "Айсберг", "price" => 15, "type" => \App\Enums\UseIngredientType::Filling
            ],
            [
                "name" => "Лук зелёный", "price" => 10, "type" => \App\Enums\UseIngredientType::Filling
            ],
            [
                "name" => "Соус спайси", "price" => 15, "type" => \App\Enums\UseIngredientType::Filling
            ],
            [
                "name" => "Майонез", "price" => 10, "type" => \App\Enums\UseIngredientType::Filling
            ],
        ];


        foreach ($ingredients as $ingredient)
            \App\Ingredient::create([
                'title' => $ingredient["name"],
                'mass' => "30",
                'quantity' => "1",
                'price' => $ingredient["price"],
                'use_type' => $ingredient["type"],
            ]);


    }
}
