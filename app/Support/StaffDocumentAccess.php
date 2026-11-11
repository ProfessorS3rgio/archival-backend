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
        }
        foreach (self::ATTACHMENTS as $model => $parentRelation) {
        }
    }

    public static function canReadPath(string $path): bool
    {
        // Staff may read and download the full archive. Ownership is enforced
        // separately by edit and delete actions.
        return true;
    }
}
