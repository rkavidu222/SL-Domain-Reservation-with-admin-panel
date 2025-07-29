<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('verifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('domain_order_id');

            $table->string('reference_number')->unique();
            $table->text('description')->nullable();
            $table->string('receipt_path'); // stores image/pdf path
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');

            $table->timestamps();

            $table->foreign('domain_order_id')
                  ->references('id')
                  ->on('domain_orders')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verifications');
    }
};
