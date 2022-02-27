<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupActivityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('group_activity', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('group_id')->index();
            $table->integer('activity_id')->index();
            $table->timestamps();

            // $table->foreign('group_id')
            //         ->references('id')
            //         ->on('groups')
            //         ->onDelete('cascade');

            // $table->foreign('activity_id')
            //         ->references('id')
            //         ->on('activities')
            //         ->onDelete('cascade');
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('group_activity');
    }
}
