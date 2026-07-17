<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\IdentityVerification;
use App\Services\Identity\IdentityAnalyzer;
use App\Services\Sms\SmsSender;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Throwable;

class IdentityVerificationController extends Controller
{
    public function edit(Request $request): View
    {
        $verification = $request->user()->identityVerification()->firstOrCreate([]);

        return view('members.identity.edit', [
            'user' => $request->user(),
            'verification' => $verification,
        ]);
    }

    public function submit(Request $request, IdentityAnalyzer $analyzer): RedirectResponse
    {
        $data = $request->validate([
            'school_id' => ['required', 'string', 'max:50'],
            'id_document' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'selfie' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
        ]);

        $user = $request->user();
        $verification = $user->identityVerification()->firstOrCreate([]);

        $this->deleteEvidence($verification);

        $idPath = $request->file('id_document')->store("identity/{$user->id}", 'private');
        $selfiePath = $request->file('selfie')->store("identity/{$user->id}", 'private');
        $analysis = $analyzer->analyze($idPath, $selfiePath, $data['school_id'], $user);

        $idMatches = $analysis->extractedSchoolId !== null
            && hash_equals($this->normalizeSchoolId($data['school_id']), $this->normalizeSchoolId($analysis->extractedSchoolId));

        $autoApproved = $idMatches
            && ($analysis->ocrConfidence ?? 0) >= (int) config('identity.auto_approve.ocr_confidence')
            && $analysis->idFaceDetected
            && $analysis->selfieFaceDetected
            && ($analysis->biometricScore ?? 0) >= (int) config('identity.auto_approve.biometric_score');

        $verification->fill([
            'school_id' => $data['school_id'],
            'extracted_school_id' => $analysis->extractedSchoolId,
            'ocr_confidence' => $analysis->ocrConfidence,
            'biometric_score' => $analysis->biometricScore,
            'status' => $autoApproved ? IdentityVerification::STATUS_VERIFIED : IdentityVerification::STATUS_PENDING,
            'decision_source' => $autoApproved ? 'auto' : null,
            'rejection_reason' => null,
            'id_document_path' => $autoApproved ? null : $idPath,
            'selfie_path' => $autoApproved ? null : $selfiePath,
            'analysis_payload' => $analysis->toArray(),
            'submitted_at' => now(),
            'verified_at' => $autoApproved ? now() : null,
            'reviewed_by' => null,
            'reviewed_at' => null,
        ])->save();

        if ($autoApproved) {
            Storage::disk('private')->delete([$idPath, $selfiePath]);
        }

        return redirect()
            ->route('member.identity.edit')
            ->with('success', $autoApproved
                ? 'Your ID proof was verified automatically.'
                : 'Your ID proof was submitted for admin review.');
    }

    public function sendPhoneCode(Request $request, SmsSender $sms): RedirectResponse
    {
        $data = $request->validate([
            'phone' => ['required', 'string', 'max:20'],
        ]);

        $user = $request->user();
        $verification = $user->identityVerification()->firstOrCreate([]);

        if ($verification->phone_otp_sent_at && $verification->phone_otp_sent_at->diffInSeconds(now()) < (int) config('identity.phone_otp.resend_seconds')) {
            return back()->withErrors(['phone' => 'Please wait before requesting another verification code.']);
        }

        $phoneChanged = $user->phone !== $data['phone'];

        $code = (string) random_int(100000, 999999);
        try {
            $sms->send($data['phone'], "Your KONEK verification code is {$code}.");
        } catch (Throwable $exception) {
            report($exception);

            return back()->withErrors(['phone' => 'Could not send a verification code right now. Please try again later.']);
        }

        DB::transaction(function () use ($code, $data, $phoneChanged, $user, $verification) {
            if ($phoneChanged) {
                $user->forceFill(['phone' => $data['phone']])->save();
            }

            $verification->forceFill([
                ...($phoneChanged ? ['phone_verified_at' => null] : []),
                'phone_otp_hash' => Hash::make($code),
                'phone_otp_expires_at' => now()->addMinutes((int) config('identity.phone_otp.ttl_minutes')),
                'phone_otp_sent_at' => now(),
                'phone_otp_attempts' => 0,
            ])->save();
        });

        return redirect()
            ->route('member.identity.edit')
            ->with('success', 'A verification code was sent to your phone.');
    }

    public function verifyPhoneCode(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $verification = $request->user()->identityVerification()->firstOrCreate([]);

        if (! $verification->phone_otp_hash || ! $verification->phone_otp_expires_at || $verification->phone_otp_expires_at->isPast()) {
            return back()->withErrors(['code' => 'The verification code has expired.']);
        }

        if ($verification->phone_otp_attempts >= (int) config('identity.phone_otp.max_attempts')) {
            return back()->withErrors(['code' => 'Too many incorrect attempts. Please request a new code.']);
        }

        if (! Hash::check($data['code'], $verification->phone_otp_hash)) {
            $verification->increment('phone_otp_attempts');

            return back()->withErrors(['code' => 'The verification code is incorrect.']);
        }

        $verification->forceFill([
            'phone_verified_at' => now(),
            'phone_otp_hash' => null,
            'phone_otp_expires_at' => null,
            'phone_otp_attempts' => 0,
        ])->save();

        return redirect()
            ->route('member.identity.edit')
            ->with('success', 'Your phone number has been verified.');
    }

    public function reset(Request $request): RedirectResponse
    {
        $verification = $request->user()->identityVerification()->firstOrCreate([]);
        $this->deleteEvidence($verification);

        $verification->forceFill([
            'school_id' => null,
            'extracted_school_id' => null,
            'ocr_confidence' => null,
            'biometric_score' => null,
            'status' => IdentityVerification::STATUS_UNSUBMITTED,
            'decision_source' => null,
            'rejection_reason' => null,
            'id_document_path' => null,
            'selfie_path' => null,
            'analysis_payload' => null,
            'submitted_at' => null,
            'verified_at' => null,
            'reviewed_by' => null,
            'reviewed_at' => null,
        ])->save();

        return redirect()->route('member.identity.edit')->with('success', 'Your ID proof submission was cleared.');
    }

    private function deleteEvidence(IdentityVerification $verification): void
    {
        Storage::disk('private')->delete(array_filter([
            $verification->id_document_path,
            $verification->selfie_path,
        ]));
    }

    private function normalizeSchoolId(string $schoolId): string
    {
        return strtoupper(preg_replace('/[^A-Za-z0-9-]/', '', $schoolId) ?? '');
    }
}
