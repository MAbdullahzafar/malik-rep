<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_attendance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('teachers')->onDelete('cascade');
            $table->date('log_date'); 
            $table->string('log_day'); // Automatically saves Day Name (e.g., Thursday)
            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();
            $table->enum('status', ['Present', 'Absent', 'Late', 'Half-Day'])->default('Present');
            $table->timestamps();

            // Restricts logging to one clean, unified log tracking row per day
            $table->unique(['teacher_id', 'log_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_attendance_logs');
    }
};
