<?php

// database/migrations/xxxx_xx_xx_create_sms_logs_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('sms_logs', function (Blueprint $table) {
            $table->id();
            $table->string('recipient')->nullable();
            $table->text('message');
            $table->string('status')->default('pending');
            $table->text('response')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('sms_logs');
    }
};
