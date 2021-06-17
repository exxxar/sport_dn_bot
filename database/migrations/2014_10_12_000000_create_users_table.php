<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email')->unique();

            $table->string('telegram_chat_id')->unique()->nullable();

            $table->string('fio_from_telegram')->default('');
            $table->string('phone')->nullable();

            $table->string('birthday')->nullable();

            $table->boolean('is_admin')->default(false);
            $table->boolean('is_working')->default(false);
            $table->boolean('is_vip')->default(false);
            $table->double('cashback_money')->default(0.0);

            $table->unsignedInteger('parent_id')->nullable();

            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
