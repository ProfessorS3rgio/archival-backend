<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Concerns\ApiResponds;
use App\Models\SangguniangPanlalawiganMember;
use App\Models\SangguniangPanlalawiganTerm;
use App\Services\OfficialProfilePictures;
use Illuminate\Http\JsonResponse;

class SangguniangPanlalawiganApiController extends Controller
{
    use ApiResponds;

    public function __construct(private readonly OfficialProfilePictures $pictures) {}

    public function terms(): JsonResponse
    {
        $terms = SangguniangPanlalawiganTerm::query()
            ->orderByDesc('start_year')
            ->get()
            ->map(fn (SangguniangPanlalawiganTerm $term) => $this->termData($term))
            ->values();

        return $this->ok($terms, 'Sangguniang Panlalawigan terms retrieved successfully');
    }

    public function officials(string $term): JsonResponse
    {
        $termModel = $this->findTerm($term);
        $members = $termModel->members()
            ->with('profilePicture')
            ->orderByRaw("CASE WHEN position = 'Vice Governor' THEN 0 ELSE 1 END")
            ->orderBy('district')
            ->orderBy('full_name')
            ->get()
            ->map(fn (SangguniangPanlalawiganMember $member) => $this->memberData($termModel, $member))
            ->values();

        return $this->ok([
            'term' => $this->termData($termModel),
            'items' => $members,
        ], 'Sangguniang Panlalawigan officials retrieved successfully');
    }

    public function official(string $term, int $member): JsonResponse
    {
        $termModel = $this->findTerm($term);
        $memberModel = $termModel->members()->with('profilePicture')->findOrFail($member);

        return $this->ok($this->memberData($termModel, $memberModel), 'Official retrieved successfully');
    }

    public function picture(string $term, int $member)
    {
        $termModel = $this->findTerm($term);
        $memberModel = $termModel->members()->with('profilePicture')->findOrFail($member);
        abort_unless($memberModel->profilePicture, 404);

        return $this->pictures->response($memberModel->profilePicture);
    }

    private function findTerm(string $term): SangguniangPanlalawiganTerm
    {
        $term = str_replace('sp-', '', $term);

        return SangguniangPanlalawiganTerm::where('term_no', $term)->firstOrFail();
    }

    private function termData(SangguniangPanlalawiganTerm $term): array
    {
        return [
            'id' => $term->term_id,
            'term_no' => $term->term_no,
            'start_year' => $term->start_year?->toDateString(),
            'end_year' => $term->end_year?->toDateString(),
        ];
    }

    private function memberData(SangguniangPanlalawiganTerm $term, SangguniangPanlalawiganMember $member): array
    {
        $picture = $member->profilePicture;
        $pictureUrl = $picture
            ? url("/api/v1/sangguniang-panlalawigan/{$term->term_no}/officials/{$member->member_id}/picture")
            : null;

        return [
            'id' => $member->member_id,
            'term_id' => $member->term_id,
            'district' => $member->district,
            'full_name' => $member->full_name,
            'extension' => $member->extension,
            'position' => $member->position,
            'profile_picture' => $picture ? [
                'filename' => $picture->file_name,
                'mime_type' => $picture->file_type,
                'url' => $pictureUrl,
            ] : null,
        ];
    }
}
