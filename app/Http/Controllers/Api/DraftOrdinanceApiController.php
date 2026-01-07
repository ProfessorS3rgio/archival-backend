<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Concerns\ApiResponds;
use App\Models\DraftOrdinance;
use App\Models\DraftOrdinanceFile;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class DraftOrdinanceApiController extends Controller
{
    use ApiResponds;

    /**
     * Get all draft ordinances with optional filtering
     */
    public function index(Request $request): JsonResponse
    {
        $query = DraftOrdinance::with('files');

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
        $draftOrdinances = $query->paginate($perPage);

        return $this->okPaginated($draftOrdinances, function ($draftOrdinance) {
            return [
                'id' => $draftOrdinance->ordinance_draft_id,
                'title' => $draftOrdinance->title,
                'author' => $draftOrdinance->author,
                'subject' => $draftOrdinance->subject,
                'year' => $draftOrdinance->year,
                'session' => $draftOrdinance->session,
                'date_drafted' => $draftOrdinance->date_drafted,
                'status' => $draftOrdinance->status,
                'created_at' => $draftOrdinance->created_at,
                'updated_at' => $draftOrdinance->updated_at,
                'files' => $draftOrdinance->files->map(function ($file) use ($draftOrdinance) {
                    return [
                        'id' => $file->file_id,
                        'filename' => $file->file_name,
                        'original_filename' => $file->file_name,
                        'file_size' => $file->file_size,
                        'mime_type' => $file->file_type,
                        'download_url' => url("/api/v1/Documents/DraftOrdinance/{$draftOrdinance->ordinance_draft_id}/files/{$file->file_id}/download"),
                        'preview_url' => url("/api/v1/Documents/DraftOrdinance/{$draftOrdinance->ordinance_draft_id}/files/{$file->file_id}/download?preview=1"),
                    ];
                })->values()->all(),
            ];
        }, 'Draft ordinances retrieved successfully');
    }

    /**
     * Get a specific draft ordinance
     */
    public function show(DraftOrdinance $draftOrdinance): JsonResponse
    {
        $draftOrdinance->load('files');

        $data = [
            'id' => $draftOrdinance->ordinance_draft_id,
            'title' => $draftOrdinance->title,
            'author' => $draftOrdinance->author,
            'subject' => $draftOrdinance->subject,
            'year' => $draftOrdinance->year,
            'session' => $draftOrdinance->session,
            'date_drafted' => $draftOrdinance->date_drafted,
            'status' => $draftOrdinance->status,
            'created_at' => $draftOrdinance->created_at,
            'updated_at' => $draftOrdinance->updated_at,
            'files' => $draftOrdinance->files->map(function ($file) use ($draftOrdinance) {
                return [
                    'id' => $file->file_id,
                    'filename' => $file->file_name,
                    'original_filename' => $file->file_name,
                    'file_size' => $file->file_size,
                    'mime_type' => $file->file_type,
                    'download_url' => url("/api/v1/Documents/DraftOrdinance/{$draftOrdinance->ordinance_draft_id}/files/{$file->file_id}/download"),
                    'preview_url' => url("/api/v1/Documents/DraftOrdinance/{$draftOrdinance->ordinance_draft_id}/files/{$file->file_id}/download?preview=1")
                ];
            })
        ];

        return $this->ok($data, 'Draft ordinance retrieved successfully');
    }

    /**
     * Download or preview a specific file from a draft ordinance
     */
    public function downloadFile(Request $request, DraftOrdinance $draftOrdinance, DraftOrdinanceFile $file): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        // Ensure the file belongs to the draft ordinance
        if ($file->ordinance_draft_id !== $draftOrdinance->ordinance_draft_id) {
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