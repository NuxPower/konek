<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedTinyInteger('year_level')->nullable()->change();
            });

            return;
        }

        $column = DB::selectOne(<<<'SQL'
            SELECT DATA_TYPE AS data_type
            FROM information_schema.columns
            WHERE table_schema = DATABASE()
              AND table_name = 'users'
              AND column_name = 'year_level'
        SQL);

        if (($column->data_type ?? null) === 'tinyint') {
            return;
        }

        DB::statement('ALTER TABLE users MODIFY year_level SMALLINT UNSIGNED NULL');
        DB::statement('UPDATE users SET year_level = year_level - 2000 WHERE year_level BETWEEN 2001 AND 2006');
        DB::statement('UPDATE users SET year_level = NULL WHERE year_level NOT BETWEEN 1 AND 6');
        DB::statement('ALTER TABLE users MODIFY year_level TINYINT UNSIGNED NULL');
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE users MODIFY year_level YEAR NULL');

            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->year('year_level')->nullable()->change();
        });
    }
};
