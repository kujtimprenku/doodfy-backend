<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivityRepeat extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_repeat', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->index()->nullable();
            $table->integer('subcategory_id')->index()->nullable();
            $table->integer('city_id')->index();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->integer('min_persons')->nullable();
            $table->integer('max_persons')->nullable();
            $table->string('location')->nullable();
            $table->string('has_xp')->nullable();
            $table->decimal('lat', 10, 2)->nullable();
            $table->decimal('lon', 10, 2)->nullable();
            $table->text('occurrence')->nullable();
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
        Schema::dropIfExists('activity_repeat');
    }
}
