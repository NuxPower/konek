<?php

namespace App\Services\Identity;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Throwable;

class HttpIdentityAnalyzer implements IdentityAnalyzer
{
    public function analyze(string $idDocumentPath, string $selfiePath, string $schoolId, User $user): IdentityAnalysisResult
    {
        try {
            $request = Http::timeout((int) config('identity.analyzer.timeout', 60))
                ->attach('id_document', fopen(Storage::disk('private')->path($idDocumentPath), 'r'), basename($idDocumentPath))
                ->attach('selfie', fopen(Storage::disk('private')->path($selfiePath), 'r'), basename($selfiePath));

            if ($token = config('identity.analyzer.token')) {
                $request = $request->withHeaders(['x-analyzer-token' => $token]);
            }

            $response = $request->post((string) config('identity.analyzer.url'), [
                'school_id' => $schoolId,
            ]);

            if (! $response->successful()) {
                return $this->manualReview("Identity analyzer returned HTTP {$response->status()}.");
            }

            $payload = $response->json();

            if (! is_array($payload)) {
                return $this->manualReview('Identity analyzer returned an invalid response.');
            }

            return new IdentityAnalysisResult(
                extractedSchoolId: $payload['extracted_school_id'] ?? null,
                ocrConfidence: isset($payload['ocr_confidence']) ? (int) $payload['ocr_confidence'] : null,
                idFaceDetected: (bool) ($payload['id_face_detected'] ?? false),
                selfieFaceDetected: (bool) ($payload['selfie_face_detected'] ?? false),
                biometricScore: isset($payload['biometric_score']) ? (int) $payload['biometric_score'] : null,
                warnings: array_values(array_filter((array) ($payload['warnings'] ?? []))),
            );
        } catch (Throwable $exception) {
            report($exception);

            return $this->manualReview('Identity analyzer is unavailable. Manual admin review is required.');
        }
    }

    private function manualReview(string $warning): IdentityAnalysisResult
    {
        return new IdentityAnalysisResult(
            extractedSchoolId: null,
            ocrConfidence: null,
            idFaceDetected: false,
            selfieFaceDetected: false,
            biometricScore: null,
            warnings: [$warning],
        );
    }
}
