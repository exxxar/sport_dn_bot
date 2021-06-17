<?php

use App\Prize;
use Illuminate\Database\Seeder;

class PrizeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //

        Prize::truncate();

        for ($i = 0; $i < 7; $i++)
            Prize::create([
                'title' => "Бесплатная доставка",
                'description' => "",
                'image_url' => "https://sun9-71.userapi.com/c855720/v855720573/19103f/aAMxvd5BHv4.jpg",
                'position' => 0,
                'as_default' => false,
            ]);

        for ($i = 0; $i < 7; $i++)
            Prize::create([
                'title' => "Скидка 5%",
                'description' => "",
                'image_url' => "https://sun9-71.userapi.com/c855720/v855720573/19103f/aAMxvd5BHv4.jpg",
                'position' => 0,
                'as_default' => false,
            ]);

        for ($i = 0; $i < 5; $i++)
            Prize::create([
                'title' => "Скидка 10%",
                'description' => "",
                'image_url' => "https://sun9-71.userapi.com/c855720/v855720573/19103f/aAMxvd5BHv4.jpg",
                'position' => 0,
                'as_default' => false,
            ]);

        for ($i = 0; $i < 3; $i++)
            Prize::create([
                'title' => "Скидка 15%",
                'description' => "",
                'image_url' => "https://sun9-71.userapi.com/c855720/v855720573/19103f/aAMxvd5BHv4.jpg",
                'position' => 0,
                'as_default' => false,
            ]);

        Prize::create([
            'title' => "Сяке Маки",
            'description' => "",
            'image_url' => "https://sun9-71.userapi.com/c855720/v855720573/19103f/aAMxvd5BHv4.jpg",
            'position' => 0,
            'as_default' => false,
        ]);

        Prize::create([
            'title' => "Магуро Маки",
            'description' => "",
            'image_url' => "https://sun9-71.userapi.com/c855720/v855720573/19103f/aAMxvd5BHv4.jpg",
            'position' => 0,
            'as_default' => false,
        ]);

        Prize::create([
            'title' => "Маки Вакамэ",
            'description' => "",
            'image_url' => "https://sun9-71.userapi.com/c855720/v855720573/19103f/aAMxvd5BHv4.jpg",
            'position' => 0,
            'as_default' => false,
        ]);

        Prize::create([
            'title' => "Крылышки Тереяки",
            'description' => "",
            'image_url' => "https://sun9-71.userapi.com/c855720/v855720573/19103f/aAMxvd5BHv4.jpg",
            'position' => 0,
            'as_default' => false,
        ]);

        Prize::create([
            'title' => "Лапша WOK с Курицей и овощами",
            'description' => "",
            'image_url' => "https://sun9-71.userapi.com/c855720/v855720573/19103f/aAMxvd5BHv4.jpg",
            'position' => 0,
            'as_default' => false,
        ]);

        Prize::create([
            'title' => "Ролл Радуга",
            'description' => "",
            'image_url' => "https://sun9-71.userapi.com/c855720/v855720573/19103f/aAMxvd5BHv4.jpg",
            'position' => 0,
            'as_default' => false,
        ]);

        Prize::create([
            'title' => "Калифорния Black",
            'description' => "",
            'image_url' => "https://sun9-71.userapi.com/c855720/v855720573/19103f/aAMxvd5BHv4.jpg",
            'position' => 0,
            'as_default' => false,
        ]);

        Prize::create([
            'title' => "Сэт Isushi",
            'description' => "",
            'image_url' => "https://sun9-71.userapi.com/c855720/v855720573/19103f/aAMxvd5BHv4.jpg",
            'position' => 0,
            'as_default' => false,
        ]);


    }
}
