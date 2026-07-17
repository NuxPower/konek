<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'bio')) {
                $table->text('bio')->nullable();
            }

            if (! Schema::hasColumn('users', 'availability')) {
                $table->string('availability')->nullable();
            }

            if (! Schema::hasColumn('users', 'phone')) {
                $table->string('phone', 50)->nullable();
            }

            if (! Schema::hasColumn('users', 'department')) {
                $table->string('department')->nullable();
            }

            if (! Schema::hasColumn('users', 'year_level')) {
                $table->unsignedTinyInteger('year_level')->nullable();
            }
        });
    }

    public function down(): void
    {
        //
    }
};
