<?php

namespace App\Services\Identity;

use App\Models\User;

class ManualReviewIdentityAnalyzer implements IdentityAnalyzer
{
    public function analyze(string $idDocumentPath, string $selfiePath, string $schoolId, User $user): IdentityAnalysisResult
    {
        return new IdentityAnalysisResult(
            extractedSchoolId: null,
            ocrConfidence: null,
            idFaceDetected: false,
            selfieFaceDetected: false,
            biometricScore: null,
            warnings: ['No biometric/OCR analyzer is configured. Manual admin review is required.'],
        );
    }
}
