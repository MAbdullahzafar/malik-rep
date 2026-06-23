<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_attendance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_sheet_id')->constrained('student_attendance_sheets')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->enum('status', ['Present', 'Absent', 'Late', 'Excused'])->default('Present');
            $table->string('remarks')->nullable(); // For reasons like "Medical leave"
            $table->timestamps();

            // Ensures a student cannot have two status rows in the same class session
            $table->unique(['attendance_sheet_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_attendance_records');
    }
};
