<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentStatusToDomainOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('domain_orders', function (Blueprint $table) {
            $table->string('payment_status')->default('pending')->after('price');
        });
    }

    public function down()
    {
        Schema::table('domain_orders', function (Blueprint $table) {
            $table->dropColumn('payment_status');
        });
    }

}
