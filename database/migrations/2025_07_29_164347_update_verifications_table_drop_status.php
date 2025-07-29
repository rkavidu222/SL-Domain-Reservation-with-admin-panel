<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateVerificationsTableDropStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
   public function up(): void
{
    Schema::table('verifications', function (Blueprint $table) {
        $table->dropColumn('status');
    });
}

public function down(): void
{
    Schema::table('verifications', function (Blueprint $table) {
        $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
    });
}

}
