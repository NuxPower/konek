<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IdentityVerification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class IdentityVerificationController extends Controller
{
    public function index(): View
    {
        $verifications = IdentityVerification::query()
            ->with('user')
            ->where('status', IdentityVerification::STATUS_PENDING)
            ->latest('submitted_at')
            ->paginate(15);

        return view('admin.identity.index', compact('verifications'));
    }

    public function show(IdentityVerification $identityVerification): View
    {
        $identityVerification->load(['user', 'reviewer']);

        return view('admin.identity.show', [
            'verification' => $identityVerification,
        ]);
    }

    public function evidence(IdentityVerification $identityVerification, string $type): StreamedResponse
    {
        abort_unless(in_array($type, ['id', 'selfie'], true), 404);

        $path = $type === 'id'
            ? $identityVerification->id_document_path
            : $identityVerification->selfie_path;

        abort_unless($path && Storage::disk('private')->exists($path), 404);

        return Storage::disk('private')->response($path);
    }

    public function approve(Request $request, IdentityVerification $identityVerification): RedirectResponse
    {
        abort_unless(
            $identityVerification->status === IdentityVerification::STATUS_PENDING,
            409,
            'Only pending identity proofs can be approved.'
        );

        $this->deleteEvidence($identityVerification);

        $identityVerification->forceFill([
            'status' => IdentityVerification::STATUS_VERIFIED,
            'decision_source' => 'admin',
            'rejection_reason' => null,
            'id_document_path' => null,
            'selfie_path' => null,
            'verified_at' => now(),
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ])->save();

        return redirect()->route('admin.identity.show', $identityVerification)->with('success', 'ID proof approved.');
    }

    public function reject(Request $request, IdentityVerification $identityVerification): RedirectResponse
    {
        abort_unless(
            $identityVerification->status === IdentityVerification::STATUS_PENDING,
            409,
            'Only pending identity proofs can be rejected.'
        );

        $data = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:1000'],
        ]);

        $this->deleteEvidence($identityVerification);

        $identityVerification->forceFill([
            'status' => IdentityVerification::STATUS_REJECTED,
            'decision_source' => 'admin',
            'rejection_reason' => $data['rejection_reason'],
            'id_document_path' => null,
            'selfie_path' => null,
            'verified_at' => null,
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ])->save();

        return redirect()->route('admin.identity.show', $identityVerification)->with('success', 'ID proof rejected.');
    }

    private function deleteEvidence(IdentityVerification $verification): void
    {
        Storage::disk('private')->delete(array_filter([
            $verification->id_document_path,
            $verification->selfie_path,
        ]));
    }
}
