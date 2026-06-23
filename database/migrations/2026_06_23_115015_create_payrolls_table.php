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
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            
            // Polymorphic relation fields (Links dynamically to teacher ID or staff member ID)
            $table->unsignedBigInteger('payable_id'); 
            $table->string('payable_type');          // Stores Model reference: App\Models\Teacher or App\Models\StaffMember
            
            $table->string('salary_month');          // e.g., "June-2026"
            $table->decimal('base_amount', 10, 2);
            $table->decimal('deductions', 10, 2)->default(0.00);
            $table->decimal('net_paid', 10, 2);
            $table->enum('status', ['Paid', 'Unpaid', 'Pending'])->default('Unpaid');
            $table->date('payment_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};
