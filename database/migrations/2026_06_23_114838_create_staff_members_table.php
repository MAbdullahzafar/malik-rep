<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff_members', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // ➔ THIS LINE MUST BE PRESENT HERE
            $table->enum('role', ['Sweeper', 'Guard', 'Peon']);
            $table->string('contact')->nullable();
            $table->decimal('base_salary', 10, 2)->default(0.00);
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_members');
    }
};
