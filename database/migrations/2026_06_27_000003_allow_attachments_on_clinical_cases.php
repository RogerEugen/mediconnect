<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attachments', function (Blueprint $table) {
            $table->unsignedBigInteger('medical_record_id')->nullable()->change();
            $table->foreignId('case_id')->nullable()->after('medical_record_id')
                ->constrained('cases')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('attachments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('case_id');
            $table->unsignedBigInteger('medical_record_id')->nullable(false)->change();
        });
    }
};
