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
        Schema::create('medical_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('hospital_id')->constrained('hospitals')->restrictOnDelete();
            $table->date('visit_date');
            $table->enum('visit_type', ['outpatient', 'inpatient', 'emergency', 'follow_up']);
            $table->text('symptoms');
            $table->text('diagnosis');
            $table->text('treatment_plan');
            $table->text('prescription')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['active', 'resolved', 'pending'])->default('active');
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_records');
    }
};
