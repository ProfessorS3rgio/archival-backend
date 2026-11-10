<?php

namespace App\Services;

use App\Models\SpProfilePicture;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class OfficialProfilePictures
{
    public function upload(UploadedFile $file): array
    {
        // Hash-based names avoid unsafe characters and database path-length limits.
        try {
            $path = $file->store('sp-profiles', 'private');
        } catch (\Throwable $error) {
            report($error);
            throw ValidationException::withMessages(['profile_picture' => 'The picture could not be uploaded. Please try again. Your existing picture has been kept.']);
        }
        if (! $path) {
            throw ValidationException::withMessages(['profile_picture' => 'The picture could not be uploaded. Please try again.']);
        }

        return [
            'disk' => 'private',
            'file_url' => $path,
            'file_name' => mb_substr($file->getClientOriginalName(), 0, 255),
            'file_type' => $file->getMimeType(),
        ];
    }

    public function deleteFile(array $attributes): void
    {
        try {
            $disk = $attributes['disk'] ?? 'public';
            if (! in_array($disk, ['public', 'private'], true)) {
                throw new \RuntimeException('Unsupported officials picture storage disk.');
            }
            if (! Storage::disk($disk)->delete($attributes['file_url'])) {
                throw new \RuntimeException('Could not remove an unused officials profile picture.');
            }
        } catch (\Throwable $error) {
            // A cleanup failure must not undo or misreport an already committed save.
            report($error);
        }
    }

    public function response(SpProfilePicture $picture)
    {
        abort_unless(in_array($picture->disk, ['public', 'private'], true), 404);
        abort_unless(in_array($picture->file_type, ['image/jpeg', 'image/png'], true), 415);
        $disk = Storage::disk($picture->disk);
        abort_unless($picture->file_url && $disk->exists($picture->file_url), 404);

        return $disk->response($picture->file_url, 'official-profile', [
            'Content-Type' => $picture->file_type,
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, max-age=3600',
        ], 'inline');
    }
}
