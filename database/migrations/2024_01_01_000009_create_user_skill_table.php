<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_skill', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('skill_id')->constrained()->cascadeOnDelete();
            $table->enum('proficiency_level', ['basic', 'intermediate', 'advanced'])->default('basic');
            $table->integer('years_experience')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'skill_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_skill');
    }
}; 