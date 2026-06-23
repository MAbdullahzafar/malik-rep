<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations with performance indexing enabled.
     */
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address');
            $table->string('mobile');
            $table->string('contact');
            $table->timestamps();

            // SPEED FIXES: Creates index maps so real-time lookups load in milliseconds
            $table->index('name');
            $table->index('mobile');
            $table->index('contact');
        });
    } 

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
