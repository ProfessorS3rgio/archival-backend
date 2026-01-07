<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Concerns\ApiResponds;
use App\Models\DraftResolution;
use App\Models\DraftResolutionFile;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class DraftResolutionApiController extends Controller
{
    use ApiResponds;

    /**
     * Get all draft resolutions with optional filtering
     */
    public function index(Request $request): JsonResponse
    {
        $query = DraftResolution::with('files');

        // Search functionality
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('author', 'like', "%{$searchTerm}%")
                  ->orWhere('subject', 'like', "%{$searchTerm}%")
                  ->orWhere('extracted_text', 'like', "%{$searchTerm}%");
            });
        }

        // Filter by author
        if ($request->has('author') && $request->author) {
            $query->where('author', 'like', "%{$request->author}%");
        }

        // Filter by year
        if ($request->has('year') && $request->year) {
            $query->where('year', $request->year);
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $draftResolutions = $query->paginate($perPage);

        return $this->okPaginated($draftResolutions, function ($draftResolution) {
            return [
                'id' => $draftResolution->resolution_draft_id,
                'title' => $draftResolution->title,
                'author' => $draftResolution->author,
                'subject' => $draftResolution->subject,
                'year' => $draftResolution->year,
                'session' => $draftResolution->session,
                'date_drafted' => $draftResolution->date_drafted,
                'status' => $draftResolution->status,
                'created_at' => $draftResolution->created_at,
                'updated_at' => $draftResolution->updated_at,
                'files' => $draftResolution->files->map(function ($file) use ($draftResolution) {
                    return [
                        'id' => $file->file_id,
                        'filename' => $file->file_name,
                        'original_filename' => $file->file_name,
                        'file_size' => $file->file_size,
                        'mime_type' => $file->file_type,
                        'download_url' => url("/api/v1/Documents/DraftResolution/{$draftResolution->resolution_draft_id}/files/{$file->file_id}/download"),
                        'preview_url' => url("/api/v1/Documents/DraftResolution/{$draftResolution->resolution_draft_id}/files/{$file->file_id}/download?preview=1"),
                    ];
                })->values()->all(),
            ];
        }, 'Draft resolutions retrieved successfully');
    }

    /**
     * Get a specific draft resolution
     */
    public function show(DraftResolution $draftResolution): JsonResponse
    {
        $draftResolution->load('files');

        $data = [
            'id' => $draftResolution->resolution_draft_id,
            'title' => $draftResolution->title,
            'author' => $draftResolution->author,
            'subject' => $draftResolution->subject,
            'year' => $draftResolution->year,
            'session' => $draftResolution->session,
            'date_drafted' => $draftResolution->date_drafted,
            'status' => $draftResolution->status,
            'created_at' => $draftResolution->created_at,
            'updated_at' => $draftResolution->updated_at,
            'files' => $draftResolution->files->map(function ($file) use ($draftResolution) {
                return [
                    'id' => $file->file_id,
                    'filename' => $file->file_name,
                    'original_filename' => $file->file_name,
                    'file_size' => $file->file_size,
                    'mime_type' => $file->file_type,
                    'download_url' => url("/api/v1/Documents/DraftResolution/{$draftResolution->resolution_draft_id}/files/{$file->file_id}/download"),
                    'preview_url' => url("/api/v1/Documents/DraftResolution/{$draftResolution->resolution_draft_id}/files/{$file->file_id}/download?preview=1")
                ];
            })
        ];

        return $this->ok($data, 'Draft resolution retrieved successfully');
    }

    /**
     * Download or preview a specific file from a draft resolution
     */
    public function downloadFile(Request $request, DraftResolution $draftResolution, DraftResolutionFile $file): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        // Ensure the file belongs to the draft resolution
        if ($file->resolution_draft_id !== $draftResolution->resolution_draft_id) {
            abort(404, 'File not found');
        }

        // Check if file exists in storage
        if (!Storage::exists($file->file_url)) {
            abort(404, 'File not found in storage');
        }

        $isPreview = $request->query('preview', false);

        if ($isPreview) {
            // Return file for inline preview in browser
            return Storage::response($file->file_url, $file->file_name, [
                'Content-Type' => $file->file_type ?: 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $file->file_name . '"'
            ]);
        } else {
            return Storage::download($file->file_url, $file->file_name);
        }
    }
}