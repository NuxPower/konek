<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained()->cascadeOnDelete();
            $table->foreignId('freelancer_id')->constrained('users')->cascadeOnDelete();
            $table->text('cover_letter');
            $table->decimal('proposed_rate', 10, 2)->nullable();
            $table->enum('rate_type', ['hourly', 'fixed'])->nullable();
            $table->integer('estimated_hours')->nullable();
            $table->text('portfolio_links')->nullable();
            $table->json('attachments')->nullable();
            $table->enum('status', ['pending', 'reviewing', 'shortlisted', 'rejected', 'accepted', 'withdrawn'])->default('pending');
            $table->text('client_notes')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamps();
            $table->unique(['job_id', 'freelancer_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('applications');
    }
}; 