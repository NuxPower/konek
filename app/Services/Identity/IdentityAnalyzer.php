<?php

namespace App\Services\Identity;

use App\Models\User;

interface IdentityAnalyzer
{
    public function analyze(string $idDocumentPath, string $selfiePath, string $schoolId, User $user): IdentityAnalysisResult;
}
