<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDomainPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('domain_prices', function (Blueprint $table) {
            $table->id();
            $table->string('category'); // CAT1, CAT2, CAT3, SUGGESTED
            $table->decimal('old_price', 10, 2);
            $table->decimal('new_price', 10, 2);
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
        Schema::dropIfExists('domain_prices');
    }
}
