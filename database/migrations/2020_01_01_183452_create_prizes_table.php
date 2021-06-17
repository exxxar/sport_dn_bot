<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePrizesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prizes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('description',1000)->default('');
            $table->string('image_url',1000);
            $table->integer('position')->default(0);
            $table->boolean('as_default')->default(false);
            $table->tinyInteger('type')->default(0); //0 - real, 1 - virtual
            $table->integer('virtual_amount')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('prizes');
    }
}
