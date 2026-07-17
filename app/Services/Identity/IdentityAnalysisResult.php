<?php

namespace App\Services\Identity;

class IdentityAnalysisResult
{
    public function __construct(
        public readonly ?string $extractedSchoolId,
        public readonly ?int $ocrConfidence,
        public readonly bool $idFaceDetected,
        public readonly bool $selfieFaceDetected,
        public readonly ?int $biometricScore,
        public readonly array $warnings = [],
    ) {}

    public function toArray(): array
    {
        return [
            'extracted_school_id' => $this->extractedSchoolId,
            'ocr_confidence' => $this->ocrConfidence,
            'id_face_detected' => $this->idFaceDetected,
            'selfie_face_detected' => $this->selfieFaceDetected,
            'biometric_score' => $this->biometricScore,
            'warnings' => $this->warnings,
        ];
    }
}
