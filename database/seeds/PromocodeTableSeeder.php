<?php

use Illuminate\Database\Seeder;

class PromocodeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //

        \App\Promocode::create([
            'code'=>"1234",
            'activated'=>0,
            'user_id'=>null,
        ]);
    }
}
