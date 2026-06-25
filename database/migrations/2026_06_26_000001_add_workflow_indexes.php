<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->index(['role', 'is_active'], 'users_role_active_index');
            $table->index('created_at');
        });

        Schema::table('jobs', function (Blueprint $table) {
            $table->index(['status', 'deadline'], 'jobs_status_deadline_index');
            $table->index(['client_id', 'status'], 'jobs_client_status_index');
            $table->index('created_at');
        });

        Schema::table('applications', function (Blueprint $table) {
            $table->index(['status', 'created_at'], 'applications_status_created_index');
            $table->index(['freelancer_id', 'status'], 'applications_freelancer_status_index');
            $table->index(['job_id', 'status'], 'applications_job_status_index');
        });

        Schema::table('activity_log', function (Blueprint $table) {
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_role_active_index');
            $table->dropIndex(['created_at']);
        });

        Schema::table('jobs', function (Blueprint $table) {
            $table->dropIndex('jobs_status_deadline_index');
            $table->dropIndex('jobs_client_status_index');
            $table->dropIndex(['created_at']);
        });

        Schema::table('applications', function (Blueprint $table) {
            $table->dropIndex('applications_status_created_index');
            $table->dropIndex('applications_freelancer_status_index');
            $table->dropIndex('applications_job_status_index');
        });

        Schema::table('activity_log', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
        });
    }
};
