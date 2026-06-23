<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            // Flags the 4 backup teachers (true = substitute pool, false = regular class teacher)
            $table->boolean('is_substitute')->default(false)->after('name'); 
            $table->string('specialized_subject')->nullable()->after('is_substitute');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->dropColumn(['is_substitute', 'specialized_subject']);
        });
    }
};
