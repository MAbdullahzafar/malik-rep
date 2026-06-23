<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Drops the strict relation block linking it to the enrollments table
            $table->dropForeign('payments_enrollment_id_foreign');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->foreign('enrollment_id')->references('id')->on('enrollments')->onDelete('cascade');
        });
    }
};
