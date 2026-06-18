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
        Schema::create('cases', function (Blueprint $table) {
            $table->id();
            $table->string('case_number', 20)->unique()->comment('e.g. CASE-2024-001');
            $table->foreignId('patient_id')->constrained('patients')->restrictOnDelete();
            $table->foreignId('medical_record_id')->nullable()->constrained('medical_records')->nullOnDelete();
            $table->foreignId('posted_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('hospital_id')->constrained('hospitals')->restrictOnDelete();
            $table->foreignId('specialization_id')->nullable()->constrained('specializations')->nullOnDelete();
            $table->string('title', 255);
            $table->text('description');
            $table->text('symptoms');
            $table->text('prior_treatments')->nullable();
            $table->enum('urgency', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->enum('status', ['open', 'assigned', 'in_discussion', 'resolved', 'closed'])->default('open');
            $table->text('resolution_notes')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cases');
    }
};
