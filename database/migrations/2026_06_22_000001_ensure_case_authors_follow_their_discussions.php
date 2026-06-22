<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('cases')
            ->select(['id', 'posted_by'])
            ->whereNotNull('posted_by')
            ->orderBy('id')
            ->chunkById(500, function ($cases) {
                $now = now();
                $followers = $cases->map(fn ($case) => [
                    'case_id' => $case->id,
                    'user_id' => $case->posted_by,
                    'created_at' => $now,
                    'updated_at' => $now,
                ])->all();

                DB::table('case_followers')->insertOrIgnore($followers);
            });
    }

    public function down(): void
    {
        // Author follows may also be legitimate user activity, so do not remove them.
    }
};
