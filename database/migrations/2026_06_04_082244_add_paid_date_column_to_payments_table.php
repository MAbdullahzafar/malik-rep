<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations to physically force insert the column into MySQL.
     */
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Checks if the column is missing, then adds it natively right below enrollment_id
            if (!Schema::hasColumn('payments', 'paid_date')) {
                $table->date('paid_date')->nullable()->after('enrollment_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'paid_date')) {
                $table->dropColumn('paid_date');
            }
        });
    }
};
