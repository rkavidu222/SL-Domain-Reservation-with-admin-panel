<?php



use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDomainOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('domain_orders', function (Blueprint $table) {
            $table->id();
            $table->string('domain_name');
            $table->decimal('price', 10, 2);
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('mobile');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('domain_orders');
    }
}
