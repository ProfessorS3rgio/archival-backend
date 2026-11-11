<?php

namespace App\Support;

use App\Models;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class StaffDocumentAccess
{
    public const DOCUMENTS = [
        Models\Ordinance::class => 'added_by',
        Models\Resolution::class => 'added_by',
        Models\DraftOrdinance::class => 'added_by',
        Models\DraftResolution::class => 'added_by',
        Models\Accomplishment::class => 'uploaded_by',
        Models\Project::class => 'added_by',
        Models\MemorandumOfAgreement::class => 'added_by',
        Models\MunicipalitiesCommunicationList::class => 'added_by',
        Models\MunicipalityCommunication::class => 'added_by',
        Models\NationalOfficeCommunicationList::class => 'added_by',
        Models\ProvincialOffice::class => 'added_by',
        Models\CommitteeReport::class => 'added_by',
    ];

    public const ATTACHMENTS = [
        Models\OrdinanceFile::class => 'ordinance',
        Models\ResolutionFile::class => 'resolution',
        Models\DraftOrdinanceFile::class => 'draftOrdinance',
        Models\DraftResolutionFile::class => 'draftResolution',
    ];

    public static function registerScopes(): void
    {
        foreach (self::DOCUMENTS as $model => $ownerField) {
            $model::addGlobalScope('api-visible', function (Builder $query) {
                if (request()->is('api/v1/*') && ! in_array(get_class($query->getModel()), [Models\DraftOrdinance::class, Models\DraftResolution::class], true)) {
                    $query->where($query->getModel()->qualifyColumn('is_visible'), true);
                }
            });
            $model::addGlobalScope('staff-owner', function (Builder $query) use ($ownerField) {
                if (Auth::user()?->role === 'staff') {
                    $query->where($query->getModel()->qualifyColumn($ownerField), Auth::id());
                }
            });
        }
        foreach (self::ATTACHMENTS as $model => $parentRelation) {
            $model::addGlobalScope('staff-owner', function (Builder $query) use ($parentRelation) {
                if (Auth::user()?->role === 'staff') {
                    $query->whereHas($parentRelation);
                }
            });
        }
    }

    public static function canReadPath(string $path): bool
    {
        if (Auth::user()?->role !== 'staff') {
            return true;
        }
        if ($path === Auth::user()->profile_picture_path) {
            return true;
        }
        // Scoped queries also protect direct file URLs, including attachments
        // uploaded by someone else to a record owned by this staff member.
        foreach (array_merge(array_keys(self::DOCUMENTS), array_keys(self::ATTACHMENTS)) as $model) {
            if (in_array($model, [Models\Ordinance::class, Models\Resolution::class, Models\DraftOrdinance::class, Models\DraftResolution::class], true)) {
                continue;
            }
            if ($model::where('file_url', $path)->exists()) {
                return true;
            }
        }
        return false;
    }
}
