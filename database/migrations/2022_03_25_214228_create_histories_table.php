<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('histories', function (Blueprint $table) {
            $table->id();
            $table->integer('type_id')->nullable(false);
            $table->integer('customer_id')->nullable(false);
            $table->string('fullUrl')->nullable(false);
            $table->string('baseUrl')->nullable(false);
            $table->string('tailUrl')->nullable(false);
            $table->timestamps();

            $table->index('type_id');
            $table->index('customer_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('histories');
    }
}
