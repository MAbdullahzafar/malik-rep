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
        // Drop any pre-existing temporary trials cleanly to avoid database collision exceptions
        Schema::dropIfExists('payment_installments');

        Schema::create('payment_installments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('enrollment_id');
            $table->unsignedBigInteger('student_id');
            $table->integer('installment_number'); // e.g., Milestone 1, 2, 3, 4... up to N
            $table->integer('total_milestones_configured'); // Stores the requested customized split total (3, 4, 6 etc)
            $table->decimal('base_amount', 10, 2);
            $table->decimal('fine_charged', 10, 2)->default(0.00);
            $table->decimal('amount_paid', 10, 2)->default(0.00);
            $table->date('due_date');
            $table->date('paid_date')->nullable();
            $table->enum('status', ['Unpaid', 'Partially Paid', 'Paid'])->default('Unpaid');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_installments');
    }
};
