<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coordonatori', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->string('address');
            $table->string('phone');
            $table->string('facultatea');
            $table->string('specializare');
            $table->tinyInteger('is_admin');
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
        Schema::dropIfExists('coordonatori');
    }
};
