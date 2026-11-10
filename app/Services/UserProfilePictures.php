<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class UserProfilePictures
{
    public function upload(UploadedFile $file): string
    {
        try {
            $path = $file->store('user-profiles', 'private');
            if (! $path) {
                throw new \RuntimeException('Profile picture upload failed.');
            }

            return $path;
        } catch (\Throwable $error) {
            report($error);
            throw ValidationException::withMessages(['profile_picture' => 'The picture could not be uploaded. Please try again. Your existing picture has been kept.']);
        }
    }

    public function delete(?string $path): void
    {
        if (! $path) {
            return;
        }

        try {
            if (! Storage::disk('private')->delete($path)) {
                throw new \RuntimeException('Could not clean up an unused profile picture.');
            }
        } catch (\Throwable $error) {
            report($error);
        }
    }
}
