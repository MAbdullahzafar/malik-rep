<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // Adds the course link field right after the mobile/contact number
            $table->unsignedBigInteger('course_id')->nullable()->after('mobile');
            
            // Set up a foreign key safety constraint referencing your courses table
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['course_id']);
            $table->dropColumn('course_id');
        });
    }
};
