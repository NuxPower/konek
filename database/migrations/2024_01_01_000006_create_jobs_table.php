<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->text('requirements');
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->constrained('users')->cascadeOnDelete();
            $table->enum('type', ['full-time', 'part-time', 'contract', 'internship']);
            $table->enum('experience_level', ['entry', 'intermediate', 'expert']);
            $table->decimal('budget_min', 10, 2)->nullable();
            $table->decimal('budget_max', 10, 2)->nullable();
            $table->enum('budget_type', ['hourly', 'fixed', 'negotiable']);
            $table->enum('status', ['draft', 'published', 'paused', 'closed', 'cancelled'])->default('draft');
            $table->timestamp('deadline')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->integer('max_applications')->nullable();
            $table->integer('applications_count')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->json('attachments')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('jobs');
    }
};
