<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->string('staff_card_path')->nullable()->after('bio');
            $table->string('staff_card_original_name')->nullable()->after('staff_card_path');
        });
    }

    public function down(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropColumn(['staff_card_path', 'staff_card_original_name']);
        });
    }
};
