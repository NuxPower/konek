<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IdentityVerification extends Model
{
    use HasFactory;

    public const STATUS_UNSUBMITTED = 'unsubmitted';

    public const STATUS_PENDING = 'pending';

    public const STATUS_VERIFIED = 'verified';

    public const STATUS_REJECTED = 'rejected';

    public const PHONE_POINTS = 20;

    public const UNIFIED_PROOF_POINTS = 80;

    protected $fillable = [
        'user_id',
        'school_id',
        'extracted_school_id',
        'ocr_confidence',
        'biometric_score',
        'status',
        'decision_source',
        'rejection_reason',
        'id_document_path',
        'selfie_path',
        'analysis_payload',
        'submitted_at',
        'verified_at',
        'reviewed_by',
        'reviewed_at',
        'phone_verified_at',
        'phone_otp_hash',
        'phone_otp_expires_at',
        'phone_otp_sent_at',
        'phone_otp_attempts',
    ];

    protected function casts(): array
    {
        return [
            'analysis_payload' => 'array',
            'submitted_at' => 'datetime',
            'verified_at' => 'datetime',
            'reviewed_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'phone_otp_expires_at' => 'datetime',
            'phone_otp_sent_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function getProofPointsAttribute(): int
    {
        return ($this->phone_verified_at ? self::PHONE_POINTS : 0)
            + ($this->status === self::STATUS_VERIFIED ? self::UNIFIED_PROOF_POINTS : 0);
    }
}
