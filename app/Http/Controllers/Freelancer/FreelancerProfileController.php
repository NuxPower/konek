<?php

namespace App\Http\Controllers\Freelancer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FreelancerProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user()->load('skills');

        return view('members.profile.show', [
            'user' => $user,
            'viewer' => $request->user(),
            'isOwner' => true,
        ]);
    }

    public function publicShow(Request $request, User $user)
    {
        $user->load('skills');

        abort_unless($user->canBeViewedBy($request->user()), 404);

        return view('members.profile.show', [
            'user' => $user,
            'viewer' => $request->user(),
            'isOwner' => $request->user()?->is($user) ?? false,
        ]);
    }

    public function edit(Request $request)
    {
        $user = $request->user()->load('skills');
        $skills = Skill::with('category')->where('is_active', true)->orderBy('name')->get();

        return view('freelancer.profile.edit', compact('user', 'skills'));
    }

    public function update(UpdateProfileRequest $request)
    {
        $user = $request->user();
        $data = $request->validated();

        $user->fill(collect($data)->except(['skills', 'profile_photo', 'remove_profile_photo', 'resume'])->all());
        $user->forceFill([
            'is_profile_public' => $request->boolean('is_profile_public'),
            'show_email' => $request->boolean('show_email'),
            'show_phone' => $request->boolean('show_phone'),
            'show_links' => $request->boolean('show_links'),
        ]);
        $phoneChanged = $user->isDirty('phone');

        if (! $user->username) {
            $user->username = User::generateUniqueUsername($user->name, $user->id);
        }

        if ($request->boolean('remove_profile_photo') && $user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
            $user->profile_photo_path = null;
        }

        if ($request->hasFile('profile_photo')) {
            $this->replaceProfilePhoto($user, $request->file('profile_photo'));
        }

        if ($request->hasFile('resume')) {
            $this->replaceResume($user, $request->file('resume'));
        }

        DB::transaction(function () use ($phoneChanged, $request, $user) {
            $user->save();
            $user->skills()->sync($request->input('skills', []));

            if ($phoneChanged) {
                $user->identityVerification()->update([
                    'phone_verified_at' => null,
                    'phone_otp_hash' => null,
                    'phone_otp_expires_at' => null,
                    'phone_otp_sent_at' => null,
                    'phone_otp_attempts' => 0,
                ]);
            }
        });

        return redirect()->route('member.profile.show')->with('success', 'Profile updated successfully.');
    }

    public function uploadResume(Request $request)
    {
        $request->validate([
            'resume' => 'required|file|mimes:pdf|max:4096',
        ]);

        $user = $request->user();
        $this->replaceResume($user, $request->file('resume'));
        $user->save();

        return back()->with('success', 'Resume uploaded successfully.');
    }

    public function downloadOwnResume(Request $request): StreamedResponse
    {
        return $this->downloadResume($request, $request->user());
    }

    public function downloadResume(Request $request, User $user): StreamedResponse
    {
        abort_unless($request->user(), 403);
        abort_unless($user->canBeViewedBy($request->user()), 404);
        abort_unless($user->resume_path && Storage::disk('local')->exists($user->resume_path), 404);

        $name = Str::slug($user->name) ?: 'member';

        return Storage::disk('local')->download($user->resume_path, "{$name}-resume.pdf");
    }

    private function replaceResume(User $user, UploadedFile $file): void
    {
        $this->assertSafePdf($file);

        if ($user->resume_path) {
            Storage::disk('local')->delete($user->resume_path);
        }

        $user->resume_path = $file->storeAs('resumes', Str::uuid().'.pdf', 'local');
    }

    private function replaceProfilePhoto(User $user, UploadedFile $file): void
    {
        $encoded = $this->encodeSafeProfilePhoto($file);

        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }

        $path = 'profile-photos/'.Str::uuid().'.jpg';
        Storage::disk('public')->put($path, $encoded);
        $user->profile_photo_path = $path;
    }

    private function assertSafePdf(UploadedFile $file): void
    {
        $contents = file_get_contents($file->getRealPath());
        abort_if($contents === false || ! str_starts_with($contents, '%PDF-'), 422, 'The resume must be a valid PDF file.');

        $lowerSample = strtolower(substr($contents, 0, 1048576));
        abort_if(
            str_contains($lowerSample, '<?php') ||
            str_contains($lowerSample, '<script') ||
            str_contains($lowerSample, '/javascript') ||
            str_contains($lowerSample, '/js'),
            422,
            'The resume PDF contains unsafe content.'
        );
    }

    private function encodeSafeProfilePhoto(UploadedFile $file): string
    {
        $image = match ($file->getMimeType()) {
            'image/jpeg' => imagecreatefromjpeg($file->getRealPath()),
            'image/png' => imagecreatefrompng($file->getRealPath()),
            'image/webp' => function_exists('imagecreatefromwebp') ? imagecreatefromwebp($file->getRealPath()) : false,
            default => false,
        };

        abort_if(! $image, 422, 'Upload a valid profile photo.');

        $width = imagesx($image);
        $height = imagesy($image);
        $max = 900;
        $scale = min(1, $max / max($width, $height));
        $targetWidth = max(1, (int) floor($width * $scale));
        $targetHeight = max(1, (int) floor($height * $scale));

        $canvas = imagecreatetruecolor($targetWidth, $targetHeight);
        imagefill($canvas, 0, 0, imagecolorallocate($canvas, 255, 255, 255));
        imagecopyresampled($canvas, $image, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);

        ob_start();
        imagejpeg($canvas, null, 85);
        $encoded = ob_get_clean();

        imagedestroy($image);
        imagedestroy($canvas);

        abort_if(! $encoded, 422, 'The profile photo could not be processed.');

        return $encoded;
    }
}
