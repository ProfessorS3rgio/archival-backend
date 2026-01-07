<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Concerns\ApiResponds;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class DocumentApiController extends Controller
{
    use ApiResponds;

    /**
     * Get documents by type with filtering
     */
    public function index(Request $request, string $type): JsonResponse
    {
        $validTypes = [
            'resolutions' => ['model' => 'App\Models\Resolution', 'table' => 'resolution_lists', 'id_field' => 'resolution_id'],
            'ordinances' => ['model' => 'App\Models\Ordinance', 'table' => 'ordinance_lists', 'id_field' => 'ordinance_id'],
            'draft-resolutions' => ['model' => 'App\Models\DraftResolution', 'table' => 'resolution_draft', 'id_field' => 'resolution_draft_id'],
            'draft-ordinances' => ['model' => 'App\Models\DraftOrdinance', 'table' => 'ordinance_draft', 'id_field' => 'ordinance_draft_id'],
            'accomplishments' => ['model' => 'App\Models\Accomplishment', 'table' => 'accomplish_lists', 'id_field' => 'accomplish_id'],
            'projects' => ['model' => 'App\Models\Project', 'table' => 'project_lists', 'id_field' => 'project_id'],
            'moas' => ['model' => 'App\Models\MemorandumOfAgreement', 'table' => 'moa_lists', 'id_field' => 'moa_id'],
            'municipal-communications' => ['model' => 'App\Models\MunicipalitiesCommunicationList', 'table' => 'municipalities_communication_lists', 'id_field' => 'municipalitites_communication_id'],
            'national-offices' => ['model' => 'App\Models\NationalOfficeCommunicationList', 'table' => 'national_offices_list', 'id_field' => 'national_offices_id'],
            'provincial-offices' => ['model' => 'App\Models\ProvincialOffice', 'table' => 'provincial_offices_list', 'id_field' => 'provincial_offices_list_id'],
            'committee-reports' => ['model' => 'App\Models\CommitteeReport', 'table' => 'committee_reports', 'id_field' => 'report_id'],
        ];

        if (!isset($validTypes[$type])) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Invalid document type',
            ], 400);
        }

        $config = $validTypes[$type];
        $model = app($config['model']);
        $query = $model->query();

        $table = $model->getTable();
        $hasColumn = function (string $column) use ($table): bool {
            return Schema::hasColumn($table, $column);
        };

        // Search functionality
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $searchableFields = [];

            foreach (['title', 'author', 'subject'] as $field) {
                if ($hasColumn($field)) {
                    $searchableFields[] = $field;
                }
            }

            // Add type-specific fields
            switch ($type) {
                case 'resolutions':
                case 'draft-resolutions':
                    if ($hasColumn('resolution_no')) {
                        $searchableFields[] = 'resolution_no';
                    }
                    break;
                case 'ordinances':
                case 'draft-ordinances':
                    if ($hasColumn('ordinance_no')) {
                        $searchableFields[] = 'ordinance_no';
                    }
                    break;
            }

            $hasExtractedText = $hasColumn('extracted_text');
            if (!empty($searchableFields) || $hasExtractedText) {
                $query->where(function ($q) use ($searchTerm, $searchableFields, $hasExtractedText) {
                    foreach ($searchableFields as $field) {
                        $q->orWhere($field, 'like', "%{$searchTerm}%");
                    }

                    if ($hasExtractedText) {
                        $q->orWhere('extracted_text', 'like', "%{$searchTerm}%");
                    }
                });
            }
        }

        // Filter by author
        if ($request->has('author') && $request->author) {
            if ($hasColumn('author')) {
                $query->where('author', 'like', "%{$request->author}%");
            }
        }

        // Filter by year
        if ($request->has('year') && $request->year) {
            if ($hasColumn('year')) {
                $query->where('year', $request->year);
            } elseif (in_array($type, ['draft-resolutions', 'draft-ordinances'], true) && $hasColumn('created_at')) {
                $query->whereYear('created_at', $request->year);
            } elseif ($hasColumn('date_approved')) {
                $query->whereYear('date_approved', $request->year);
            } elseif ($hasColumn('date_received')) {
                $query->whereYear('date_received', $request->year);
            }
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $documents = $query->paginate($perPage);

        $message = ucfirst(str_replace('-', ' ', $type)) . ' retrieved successfully';

        return $this->okPaginated($documents, function ($document) use ($config, $type) {
            $documentId = $document->{$config['id_field']};

            $baseData = [
                'id' => $documentId,
                'title' => $document->title ?? null,
                'author' => $document->author ?? null,
                'created_at' => $document->created_at ?? null,
                'updated_at' => $document->updated_at ?? null,
            ];

            // Add type-specific fields
            switch ($type) {
                case 'resolutions':
                    $baseData = array_merge($baseData, [
                        'resolution_no' => $document->resolution_no ?? null,
                        'subject' => $document->subject ?? null,
                        'date_approved' => $document->date_approved ?? null,
                        'session' => $document->session ?? null,
                        'municipal' => $document->municipal ?? null,
                        'is_municipal' => $document->is_municipal ?? null,
                    ]);
                    break;
                case 'ordinances':
                    $baseData = array_merge($baseData, [
                        'ordinance_no' => $document->ordinance_no ?? null,
                        'subject' => $document->subject ?? null,
                        'date_approved' => $document->date_approved ?? null,
                    ]);
                    break;
                case 'draft-resolutions':
                    $baseData = array_merge($baseData, [
                        'resolution_no' => $document->resolution_no ?? null,
                        'subject' => $document->subject ?? null,
                        'date_drafted' => $document->date_drafted ?? null,
                        'session' => $document->session ?? null,
                        'status' => $document->status ?? null,
                    ]);
                    break;
                case 'draft-ordinances':
                    $baseData = array_merge($baseData, [
                        'ordinance_no' => $document->ordinance_no ?? null,
                        'subject' => $document->subject ?? null,
                        'date_drafted' => $document->date_drafted ?? null,
                        'status' => $document->status ?? null,
                    ]);
                    break;
            }

            // Add files if they exist
            if (method_exists($document, 'files')) {
                $document->load('files');
                $baseData['files'] = $document->files->map(function ($file) use ($type, $documentId) {
                    $fileId = $file->file_id ?? ($file->id ?? null);

                    return [
                        'id' => $fileId,
                        'filename' => $file->file_name ?? null,
                        'original_filename' => $file->file_name ?? null,
                        'file_size' => $file->file_size ?? null,
                        'mime_type' => $file->file_type ?? null,
                        'download_url' => url("/api/v1/Documents/{$type}/{$documentId}/files/{$fileId}/download"),
                        'preview_url' => url("/api/v1/Documents/{$type}/{$documentId}/files/{$fileId}/download?preview=1"),
                    ];
                });
            }

            return $baseData;
        }, $message);
    }

    /**
     * Get available document types
     */
    public function types(): JsonResponse
    {
        $types = [
            'resolutions' => 'Resolutions',
            'ordinances' => 'Ordinances',
            'draft-resolutions' => 'Draft Resolutions',
            'draft-ordinances' => 'Draft Ordinances',
            'accomplishments' => 'Accomplishments',
            'projects' => 'Projects',
            'moas' => 'Memorandum of Agreements',
            'municipal-communications' => 'Municipal Communications',
            'national-offices' => 'National Office Communications',
            'provincial-offices' => 'Provincial Office Communications',
            'committee-reports' => 'Committee Reports',
        ];

        return $this->ok($types, 'Document types retrieved successfully');
    }
    public function downloadFile(Request $request, string $type, string $documentId, string $fileId): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $validTypes = [
            'resolutions' => ['model' => 'App\Models\Resolution', 'file_model' => 'App\Models\ResolutionFile', 'foreign_key' => 'resolution_id'],
            'ordinances' => ['model' => 'App\Models\Ordinance', 'file_model' => 'App\Models\OrdinanceFile', 'foreign_key' => 'ordinance_id'],
            'draft-resolutions' => ['model' => 'App\Models\DraftResolution', 'file_model' => 'App\Models\DraftResolutionFile', 'foreign_key' => 'resolution_draft_id'],
            'draft-ordinances' => ['model' => 'App\Models\DraftOrdinance', 'file_model' => 'App\Models\DraftOrdinanceFile', 'foreign_key' => 'ordinance_draft_id'],
        ];

        if (!isset($validTypes[$type])) {
            abort(404, 'Invalid document type');
        }

        $config = $validTypes[$type];
        $model = app($config['model']);
        $fileModel = app($config['file_model']);

        $documentIdField = $this->getIdField($type);

        // Find the document
        $document = $model->where($documentIdField, $documentId)->firstOrFail();

        // Find the file
        $file = $fileModel->where($fileModel->getKeyName(), $fileId)
            ->where($config['foreign_key'], $document->{$documentIdField})
            ->firstOrFail();

        // Check if file exists in storage
        if (!Storage::exists($file->file_url)) {
            abort(404, 'File not found in storage');
        }

        $isPreview = $request->query('preview', false);
        if ($isPreview) {
            return Storage::response($file->file_url, $file->file_name, [
                'Content-Type' => $file->file_type ?: 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $file->file_name . '"',
            ]);
        }

        return Storage::download($file->file_url, $file->file_name);
    }

    /**
     * Get the ID field name for a document type
     */
    private function getIdField(string $type): string
    {
        $idFields = [
            'resolutions' => 'resolution_id',
            'ordinances' => 'ordinance_id',
            'draft-resolutions' => 'resolution_draft_id',
            'draft-ordinances' => 'ordinance_draft_id',
            'accomplishments' => 'accomplish_id',
            'projects' => 'project_id',
            'moas' => 'moa_id',
            'municipal-communications' => 'municipalitites_communication_id',
            'national-offices' => 'national_offices_id',
            'provincial-offices' => 'provincial_offices_list_id',
            'committee-reports' => 'report_id',
        ];

        return $idFields[$type] ?? 'id';
    }
}