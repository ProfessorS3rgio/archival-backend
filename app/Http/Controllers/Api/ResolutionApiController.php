<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Concerns\ApiResponds;
use App\Models\Resolution;
use App\Models\ResolutionFile;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class ResolutionApiController extends Controller
{
    use ApiResponds;

    /**
     * Get all resolutions with optional filtering
     */
    public function index(Request $request): JsonResponse
    {
        $query = Resolution::with('files');

        // Search functionality
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('resolution_no', 'like', "%{$searchTerm}%")
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
            $query->whereYear('date_approved', $request->year);
        }

        // Filter by status (approved/pending)
        if ($request->has('status')) {
            if ($request->status === 'approved') {
                $query->whereNotNull('date_approved');
            } elseif ($request->status === 'pending') {
                $query->whereNull('date_approved');
            }
        }

        // Filter by municipal/provincial
        if ($request->has('type')) {
            if ($request->type === 'municipal') {
                $query->where('is_municipal', true);
            } elseif ($request->type === 'provincial') {
                $query->where('is_municipal', false);
            }
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $resolutions = $query->paginate($perPage);

        return $this->okPaginated($resolutions, function ($resolution) {
            return [
                'id' => $resolution->resolution_id,
                'resolution_no' => $resolution->resolution_no,
                'title' => $resolution->title,
                'author' => $resolution->author,
                'subject' => $resolution->subject,
                'date_approved' => $resolution->date_approved,
                'session' => $resolution->session,
                'municipal' => $resolution->municipal,
                'is_municipal' => $resolution->is_municipal,
                'created_at' => $resolution->created_at,
                'updated_at' => $resolution->updated_at,
                'files' => $resolution->files->map(function ($file) use ($resolution) {
                    return [
                        'id' => $file->file_id,
                        'filename' => $file->file_name,
                        'original_filename' => $file->file_name,
                        'file_size' => $file->file_size,
                        'mime_type' => $file->file_type,
                        'download_url' => url("/api/v1/Documents/Resolution/{$resolution->resolution_id}/files/{$file->file_id}/download"),
                        'preview_url' => url("/api/v1/Documents/Resolution/{$resolution->resolution_id}/files/{$file->file_id}/download?preview=1"),
                    ];
                })->values()->all(),
            ];
        }, 'Resolutions retrieved successfully');
    }

    /**
     * Get a specific resolution
     */
    public function show(Resolution $resolution): JsonResponse
    {
        $resolution->load('files');

        $data = [
            'id' => $resolution->resolution_id,
            'resolution_no' => $resolution->resolution_no,
            'title' => $resolution->title,
            'author' => $resolution->author,
            'subject' => $resolution->subject,
            'date_approved' => $resolution->date_approved,
            'session' => $resolution->session,
            'municipal' => $resolution->municipal,
            'is_municipal' => $resolution->is_municipal,
            'created_at' => $resolution->created_at,
            'updated_at' => $resolution->updated_at,
            'files' => $resolution->files->map(function ($file) use ($resolution) {
                return [
                    'id' => $file->file_id,
                    'filename' => $file->file_name,
                    'original_filename' => $file->file_name,
                    'file_size' => $file->file_size,
                    'mime_type' => $file->file_type,
                    'download_url' => url("/api/v1/Documents/Resolution/{$resolution->resolution_id}/files/{$file->file_id}/download"),
                    'preview_url' => url("/api/v1/Documents/Resolution/{$resolution->resolution_id}/files/{$file->file_id}/download?preview=1")
                ];
            })
        ];

        return $this->ok($data, 'Resolution retrieved successfully');
    }

    /**
     * Download or preview a specific file from a resolution
     */
    public function downloadFile(Request $request, Resolution $resolution, ResolutionFile $file): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        // Ensure the file belongs to the resolution
        if ($file->resolution_id !== $resolution->resolution_id) {
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