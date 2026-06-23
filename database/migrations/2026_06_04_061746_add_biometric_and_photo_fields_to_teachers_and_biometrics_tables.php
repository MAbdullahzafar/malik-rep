<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add the photo field to your main teachers table structure safely
        Schema::table('teachers', function (Blueprint $table) {
            if (!Schema::hasColumn('teachers', 'photo')) {
                $table->string('photo')->nullable()->after('designation');
            }
        });

        // 2. Clear out any old biometric schemas and apply clean WebAuthn structures
        Schema::dropIfExists('teacher_biometrics');
        Schema::create('teacher_biometrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('teachers')->onDelete('cascade');
            $table->text('credential_id'); // Unique ID matching the hardware finger scan register
            $table->text('public_key');    // Verification string for comparison processing
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->dropColumn('photo');
        });
        Schema::dropIfExists('teacher_biometrics');
    }
};
