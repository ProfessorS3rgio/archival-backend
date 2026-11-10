<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use App\Services\LoggingService;
use App\Services\UserProfilePictures;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ProfileController extends Controller
{
    public function edit()
    {
        return Inertia::render('Settings/Profile');
    }

    public function picture(Request $request)
    {
        $path = $request->user()->profile_picture_path;
        $mime = match (strtolower(pathinfo($path ?? '', PATHINFO_EXTENSION))) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'webp' => 'image/webp',
            default => null,
        };
        abort_unless($path && $mime && Storage::disk('private')->exists($path), 404);

        return Storage::disk('private')->response($path, 'profile-picture', [
            'Content-Type' => $mime,
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store',
        ], 'inline');
    }

    public function update(ProfileRequest $request, UserProfilePictures $pictures)
    {
        $user = $request->user();
        $oldPath = $user->profile_picture_path;
        $newPath = $request->hasFile('profile_picture') ? $pictures->upload($request->file('profile_picture')) : null;

        try {
            DB::transaction(function () use ($request, $user, $newPath) {
                $oldValues = $user->getAttributes();
                $user->fill($request->safe()->only(['name', 'email', 'username']));
                if ($newPath || $request->boolean('remove_profile_picture')) {
                    $user->profile_picture_path = $newPath;
                }
                $user->save();
                LoggingService::logUpdate($user, $oldValues, "Updated own profile: {$user->username}");
            });
        } catch (\Throwable $error) {
            $pictures->delete($newPath);
            throw $error;
        }

        if ($oldPath !== $user->profile_picture_path) {
            $pictures->delete($oldPath);
        }

        return redirect()->route('profile.edit')->with('success', 'Profile updated successfully.');
    }
}
