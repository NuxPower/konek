<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('job_skill', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained()->cascadeOnDelete();
            $table->foreignId('skill_id')->constrained()->cascadeOnDelete();
            $table->enum('proficiency_required', ['basic', 'intermediate', 'advanced'])->default('basic');
            $table->timestamps();
            $table->unique(['job_id', 'skill_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('job_skill');
    }
};
