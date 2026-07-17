<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $duplicateGroups = DB::table('categories')
            ->select('name')
            ->selectRaw('MIN(id) as keep_id')
            ->selectRaw('GROUP_CONCAT(id ORDER BY id) as ids')
            ->groupBy('name')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicateGroups as $group) {
            $ids = collect(explode(',', $group->ids))
                ->map(fn (string $id) => (int) $id)
                ->filter(fn (int $id) => $id !== (int) $group->keep_id)
                ->values();

            if ($ids->isEmpty()) {
                continue;
            }

            DB::table('jobs')
                ->whereIn('category_id', $ids)
                ->update(['category_id' => $group->keep_id]);

            DB::table('skills')
                ->whereIn('category_id', $ids)
                ->update(['category_id' => $group->keep_id]);

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
