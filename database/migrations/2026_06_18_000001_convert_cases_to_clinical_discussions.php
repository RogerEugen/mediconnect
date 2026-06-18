<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cases', function (Blueprint $table) {
            $table->foreignId('patient_id')->nullable()->change();
            $table->foreignId('hospital_id')->nullable()->change();
            $table->string('patient_age_group', 30)->nullable()->after('specialization_id');
            $table->string('patient_sex', 20)->nullable()->after('patient_age_group');
            $table->text('private_reference')->nullable()->after('patient_sex');
            $table->text('clinical_history')->nullable()->after('description');
            $table->text('investigation_results')->nullable()->after('symptoms');
            $table->text('discussion_question')->nullable()->after('prior_treatments');
            $table->boolean('author_anonymous')->default(false)->after('discussion_question');
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->string('url')->nullable()->after('type');
        });

        Schema::create('case_followers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_id')->constrained('cases')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['case_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('case_followers');

        Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn('url');
        });

        Schema::table('cases', function (Blueprint $table) {
            $table->dropColumn([
                'patient_age_group',
                'patient_sex',
                'private_reference',
                'clinical_history',
                'investigation_results',
                'discussion_question',
                'author_anonymous',
            ]);
        });
    }
};
