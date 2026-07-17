<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('identity_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('school_id')->nullable();
            $table->string('extracted_school_id')->nullable();
            $table->unsignedTinyInteger('ocr_confidence')->nullable();
            $table->unsignedTinyInteger('biometric_score')->nullable();
            $table->string('status')->default('unsubmitted');
            $table->string('decision_source')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->string('id_document_path')->nullable();
            $table->string('selfie_path')->nullable();
            $table->json('analysis_payload')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('phone_verified_at')->nullable();
            $table->string('phone_otp_hash')->nullable();
            $table->timestamp('phone_otp_expires_at')->nullable();
            $table->timestamp('phone_otp_sent_at')->nullable();
            $table->unsignedTinyInteger('phone_otp_attempts')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('identity_verifications');
    }
};
