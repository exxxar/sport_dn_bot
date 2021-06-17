<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->default('');
            $table->string('description',1000)->default('');

            $table->string('category')->default('');

            $table->double('mass')->default(0.0);
            $table->double('price')->default(0.0);
            $table->integer('portion_count')->default(0);

            $table->string('image_url',1000)->default('');
            $table->string('site_url',1000)->default('');

            $table->boolean('is_active')->default(true);

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
        Schema::dropIfExists('products');
    }
}
