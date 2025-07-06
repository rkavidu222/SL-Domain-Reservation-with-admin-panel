<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCategoryToDomainOrdersTable extends Migration
{
    public function up()
    {
        Schema::table('domain_orders', function (Blueprint $table) {
            $table->string('category', 50)->after('price')->nullable(false);
        });
    }

    public function down()
    {
        Schema::table('domain_orders', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }
}
