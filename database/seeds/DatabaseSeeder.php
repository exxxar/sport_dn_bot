<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //$this->call(ProductTableSeeder::class);
        $this->call(PrizeTableSeeder::class);
     //   $this->call(PromocodeTableSeeder::class);
        $this->call(IngredientsTableSeeder::class);
    }
}
