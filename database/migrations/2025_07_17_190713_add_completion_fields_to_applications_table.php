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
            $table->text('completion_message')->nullable()->after('status');
            $table->json('completion_attachments')->nullable()->after('completion_message');
            $table->timestamp('completed_at')->nullable()->after('completion_attachments');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn([
                'completion_message',
                'completion_attachments', 
                'completed_at'
            ]);
        });
    }
};