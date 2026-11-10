<?php

namespace App\Http\Controllers;

use App\Models\SangguniangPanlalawiganTerm;
use App\Models\SangguniangPanlalawiganMember;
use App\Models\SpProfilePicture;
use App\Services\LoggingService;
use App\Services\OfficialProfilePictures;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SangguniangPanlalawiganController extends Controller
{
    public function __construct(private OfficialProfilePictures $pictures) {}

    /**
     * Display officials for a specific SP term
     */
    public function officials($termSlug)
    {
        // Extract term number from slug (e.g., "sp-1st" -> "1st")
        $termNumber = str_replace('sp-', '', $termSlug);
        
        // Find the term
        $term = SangguniangPanlalawiganTerm::where('term_no', $termNumber)->first();
        
        if (!$term) {
            return redirect()->route('dashboard')->with('error', 'Term not found');
        }

        // Get all members with their profile pictures
        $members = SangguniangPanlalawiganMember::with('profilePicture')
            ->where('term_id', $term->term_id)
            ->get();

        return Inertia::render('SangguniangPanlalawigan/Officials', [
            'term' => $term,
            'members' => $members,
            'termSlug' => $termSlug,
        ]);
    }

    /**
     * Show form to add new official
     */
    public function create($termSlug)
    {
        // Extract term number from slug
        $termNumber = str_replace('sp-', '', $termSlug);
        
        // Find the term
        $term = SangguniangPanlalawiganTerm::where('term_no', $termNumber)->first();
        
        if (!$term) {
            return redirect()->route('dashboard')->with('error', 'Term not found');
        }

        // Get all members with their profile pictures for validation
        $members = SangguniangPanlalawiganMember::with('profilePicture')
            ->where('term_id', $term->term_id)
            ->get();

        return Inertia::render('SangguniangPanlalawigan/AddOfficial', [
            'term' => $term,
            'termSlug' => $termSlug,
            'members' => $members,
        ]);
    }

    /**
     * Store new official
     */
    public function store(Request $request, $termSlug)
    {
        $validated = $request->validate([
            'term_id' => 'required|exists:sangguniang_panlalawigan_terms,term_id',
            'district' => 'nullable|in:1st District,2nd District,Ex-Officio',
            'full_name' => 'required|string|max:255',
            'extension' => 'required|in:Hon,Atty,Doc',
            'position' => 'required|string|max:255',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Check position-specific limits
        if ($validated['position'] === 'Vice Governor') {
            // Check if Vice Governor already exists for this term
            $existingViceGovernor = SangguniangPanlalawiganMember::where('term_id', $validated['term_id'])
                ->where('position', 'Vice Governor')
                ->exists();

            if ($existingViceGovernor) {
                return back()->withErrors([
                    'position' => 'A Vice Governor already exists for this term.'
                ])->withInput();
            }
        } elseif ($validated['position'] === 'Board Member') {
            // Check district limits for Board Members
            if (in_array($validated['district'] ?? null, ['1st District', '2nd District'])) {
                $existingCount = SangguniangPanlalawiganMember::where('term_id', $validated['term_id'])
                    ->where('district', $validated['district'])
                    ->count();

                if ($existingCount >= 5) {
                    return back()->withErrors([
                        'district' => "Maximum 5 board members allowed per district. {$validated['district']} already has {$existingCount} members."
                    ])->withInput();
                }
            }
        } elseif ($validated['position'] === 'Board Member Ex-Officio') {
            // Check Ex-Officio limits
            $existingCount = SangguniangPanlalawiganMember::where('term_id', $validated['term_id'])
                ->where('district', 'Ex-Officio')
                ->count();

            if ($existingCount >= 4) {
                return back()->withErrors([
                    'district' => "Maximum 4 ex-officio members allowed. Ex-Officio already has {$existingCount} members."
                ])->withInput();
            }
        }

        $this->saveMember($request, $validated);

        return redirect()->route('sp.officials', ['termSlug' => $termSlug])
            ->with('success', 'Official added successfully');
    }

    /**
     * Update an existing official
     */
    public function update(Request $request, $termSlug, $memberId)
    {
        $member = SangguniangPanlalawiganMember::findOrFail($memberId);

        $validated = $request->validate([
            'term_id' => 'required|exists:sangguniang_panlalawigan_terms,term_id',
            'district' => 'nullable|in:1st District,2nd District,Ex-Officio',
            'full_name' => 'required|string|max:255',
            'extension' => 'required|in:Hon,Atty,Doc',
            'position' => 'required|string|max:255',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Check position-specific limits (exclude current member from count)
        if ($validated['position'] === 'Vice Governor') {
            // Check if Vice Governor already exists for this term (excluding current member)
            $existingViceGovernor = SangguniangPanlalawiganMember::where('term_id', $validated['term_id'])
                ->where('position', 'Vice Governor')
                ->where('member_id', '!=', $memberId)
                ->exists();

            if ($existingViceGovernor) {
                return back()->withErrors([
                    'position' => 'A Vice Governor already exists for this term.'
                ])->withInput();
            }
        } elseif ($validated['position'] === 'Board Member') {
            // Check district limits for Board Members
            if (in_array($validated['district'] ?? null, ['1st District', '2nd District'])) {
                $existingCount = SangguniangPanlalawiganMember::where('term_id', $validated['term_id'])
                    ->where('district', $validated['district'])
                    ->where('member_id', '!=', $memberId)
                    ->count();

                if ($existingCount >= 5) {
                    return back()->withErrors([
                        'district' => "Maximum 5 board members allowed per district. {$validated['district']} already has {$existingCount} members."
                    ])->withInput();
                }
            }
        } elseif ($validated['position'] === 'Board Member Ex-Officio') {
            // Check Ex-Officio limits
            $existingCount = SangguniangPanlalawiganMember::where('term_id', $validated['term_id'])
                ->where('district', 'Ex-Officio')
                ->where('member_id', '!=', $memberId)
                ->count();

            if ($existingCount >= 4) {
                return back()->withErrors([
                    'district' => "Maximum 4 ex-officio members allowed. Ex-Officio already has {$existingCount} members."
                ])->withInput();
            }
        }

        $this->saveMember($request, $validated, $member);

        return redirect()->route('sp.officials', ['termSlug' => $termSlug])
            ->with('success', 'Official updated successfully');
    }

    /**
     * Delete an official
     */
    public function destroy($termSlug, $memberId)
    {
        $member = SangguniangPanlalawiganMember::findOrFail($memberId);
        
        DB::transaction(function () use ($member) {
            if ($picture = $member->profilePicture) {
                LoggingService::logDelete($picture, "Deleted profile picture for SP official: {$member->full_name}");
                $picture->delete();
                DB::afterCommit(fn () => $this->pictures->deleteFile($picture->getAttributes()));
            }
            LoggingService::logDelete($member, "Deleted SP official: {$member->full_name} ({$member->position})");
            $member->delete();
        });

        return redirect()->route('sp.officials', ['termSlug' => $termSlug])
            ->with('success', 'Official deleted successfully');
    }

    private function saveMember(Request $request, array $validated, ?SangguniangPanlalawiganMember $member = null): void
    {
        // Keep the old image until both the replacement upload and database save succeed.
        $uploaded = $request->hasFile('profile_picture')
            ? $this->pictures->upload($request->file('profile_picture'))
            : null;

        try {
            DB::transaction(function () use ($validated, $member, $uploaded) {
                $attributes = [
                    'term_id' => $validated['term_id'],
                    'district' => $validated['district'] ?? null,
                    'full_name' => $validated['full_name'],
                    'extension' => $validated['extension'],
                    'position' => $validated['position'],
                ];
                if ($member) {
                    $oldValues = $member->getOriginal();
                    $member->update($attributes);
                    LoggingService::logUpdate($member, $oldValues, "Updated SP official: {$member->full_name}");
                } else {
                    $member = SangguniangPanlalawiganMember::create($attributes);
                    LoggingService::logCreate($member, "Added SP official: {$member->full_name} as {$member->position}");
                }

                if ($uploaded) {
                    $oldPicture = $member->profilePicture;
                    if ($oldPicture) {
                        LoggingService::logDelete($oldPicture, "Deleted old profile picture for SP official: {$member->full_name}");
                        $oldPicture->delete();
                    }
                    $picture = SpProfilePicture::create([
                        ...$uploaded,
                        'sp_member_id' => $member->member_id,
                        'added_by' => auth()->id(),
                    ]);
                    LoggingService::logCreate($picture, "Uploaded profile picture for SP official: {$member->full_name}");
                    if ($oldPicture) {
                        DB::afterCommit(fn () => $this->pictures->deleteFile($oldPicture->getAttributes()));
                    }
                }
            });
        } catch (\Throwable $error) {
            if ($uploaded) {
                $this->pictures->deleteFile($uploaded);
            }
            throw $error;
        }
    }

}
