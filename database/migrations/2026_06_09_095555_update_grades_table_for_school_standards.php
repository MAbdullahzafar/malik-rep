<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Safely drop the basic table if it was blankly migrated to rebuild it with professional specs
        Schema::dropIfExists('grades');

        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->enum('exam_type', ['Daily Test', 'Midterm', 'Final Term']); 
            $table->date('evaluation_date');
            $table->decimal('marks_obtained', 5, 2);
            $table->decimal('total_marks', 5, 2)->default(50.00); // Defaults to 50 for Daily Tests
            $table->string('grade_letter', 2);
            $table->string('status'); // Pass or Fail
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
