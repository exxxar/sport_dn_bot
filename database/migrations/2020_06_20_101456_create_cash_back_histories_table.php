<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCashBackHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cash_back_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->double('amount')->default(0);
            $table->string('bill_number',255)->default('');
            $table->double('money_in_bill',255)->default(0.0);
            $table->unsignedInteger('employee_id')->nullable();
            $table->unsignedInteger('user_id');
            $table->tinyInteger('type');//0 - up, 1 - down
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
        Schema::dropIfExists('cash_back_histories');
    }
}
