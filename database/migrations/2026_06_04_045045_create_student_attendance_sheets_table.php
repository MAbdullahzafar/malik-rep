<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_attendance_sheets', function (Blueprint $table) {
            $table->id();
            // Connects to your existing courses/classes table
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            // Tracks which teacher took attendance (nullable if admin takes it)
            $table->foreignId('teacher_id')->nullable()->constrained('teachers')->onDelete('set null');
            $table->date('attendance_date');
            $table->timestamps();

            // Prevents making two sheets for the same course on the same day
            $table->unique(['course_id', 'attendance_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_attendance_sheets');
    }
};
