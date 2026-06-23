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
        Schema::create('academic_histories', function (Blueprint $table) {
            $table->id();
            // Automatically links to your existing students table row footprint
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->string('from_class');     // e.g., "Class 9"
            $table->string('to_class');       // e.g., "Class 10 / Matric"
            $table->string('academic_year');   // e.g., "2026-2027"
            $table->date('promotion_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_histories');
    }
};
