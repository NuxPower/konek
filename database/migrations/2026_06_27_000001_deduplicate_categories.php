<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $duplicateNames = DB::table('categories')
            ->select('name')
            ->groupBy('name')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('name');

        foreach ($duplicateNames as $name) {
            $rows = DB::table('categories')
                ->where('name', $name)
                ->orderBy('id')
                ->pluck('id');

            $keepId = $rows->first();
            $ids = $rows->slice(1)->values();

            if ($ids->isEmpty()) {
                continue;
            }

            DB::table('jobs')
                ->whereIn('category_id', $ids)
                ->update(['category_id' => $keepId]);

            DB::table('skills')
                ->whereIn('category_id', $ids)
                ->update(['category_id' => $keepId]);

            DB::table('categories')
                ->whereIn('id', $ids)
                ->delete();
        }
    }

    public function down(): void
    {
        // Category duplicates cannot be safely restored after jobs/skills are repointed.
    }
};
