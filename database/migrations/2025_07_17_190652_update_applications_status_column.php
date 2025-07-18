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
        Schema::table('applications', function (Blueprint $table) {
            // First, change the column type to string temporarily
            $table->string('status', 50)->change();
        });
        
        // Then update it to enum with new values
        Schema::table('applications', function (Blueprint $table) {
            $table->enum('status', [
                'pending',
                'accepted', 
                'rejected',
                'completed',
                'pending_completion',
                'cancelled'
            ])->default('pending')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            // Revert back to original enum values
            $table->enum('status', [
                'pending',
                'accepted',
                'rejected'
            ])->default('pending')->change();
        });
    }
};