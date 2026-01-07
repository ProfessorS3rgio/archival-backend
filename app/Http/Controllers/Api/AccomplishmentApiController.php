<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Concerns\ApiResponds;
use App\Models\Accomplishment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class AccomplishmentApiController extends Controller
{
    use ApiResponds;

    /**
     * Get all accomplishments with optional filtering
     */
    public function index(Request $request): JsonResponse
    {
        $query = Accomplishment::query();

        // Search functionality
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('extracted_text', 'like', "%{$searchTerm}%");
            });
        }

        // Filter by year
        if ($request->has('year') && $request->year) {
            $query->whereYear('accomplishment_year', $request->year);
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $accomplishments = $query->paginate($perPage);

        return $this->okPaginated($accomplishments, function ($accomplishment) {
            $files = [];
            if ($accomplishment->file_name && $accomplishment->file_url) {
                $files[] = [
                    'id' => $accomplishment->accomplish_id,
                    'filename' => $accomplishment->file_name,
                    'original_filename' => $accomplishment->file_name,
                    'file_size' => $accomplishment->file_size,
                    'mime_type' => $accomplishment->file_type,
                    'download_url' => url("/api/v1/Documents/Accomplishment/{$accomplishment->accomplish_id}/download"),
                    'preview_url' => url("/api/v1/Documents/Accomplishment/{$accomplishment->accomplish_id}/download?preview=1"),
                ];
            }

            return [
                'id' => $accomplishment->accomplish_id,
                'title' => $accomplishment->title,
                'accomplishment_year' => $accomplishment->accomplishment_year,
                'date_uploaded' => $accomplishment->date_uploaded,
                'created_at' => $accomplishment->created_at,
                'updated_at' => $accomplishment->updated_at,
                'files' => $files,
            ];
        }, 'Accomplishments retrieved successfully');
    }

    /**
     * Get a specific accomplishment
     */
    public function show(Accomplishment $accomplishment): JsonResponse
    {
        $files = [];
        if ($accomplishment->file_name && $accomplishment->file_url) {
            $files[] = [
                'id' => $accomplishment->accomplish_id,
                'filename' => $accomplishment->file_name,
                'original_filename' => $accomplishment->file_name,
                'file_size' => $accomplishment->file_size,
                'mime_type' => $accomplishment->file_type,
                'download_url' => url("/api/v1/Documents/Accomplishment/{$accomplishment->accomplish_id}/download"),
                'preview_url' => url("/api/v1/Documents/Accomplishment/{$accomplishment->accomplish_id}/download?preview=1")
            ];
        }

        $data = [
            'id' => $accomplishment->accomplish_id,
            'title' => $accomplishment->title,
            'accomplishment_year' => $accomplishment->accomplishment_year,
            'date_uploaded' => $accomplishment->date_uploaded,
            'created_at' => $accomplishment->created_at,
            'updated_at' => $accomplishment->updated_at,
            'files' => $files
        ];

        return $this->ok($data, 'Accomplishment retrieved successfully');
    }

    /**
     * Download or preview a specific accomplishment file
     */
    public function downloadFile(Request $request, Accomplishment $accomplishment): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        if (!$accomplishment->file_name || !$accomplishment->file_url) {
            abort(404, 'File not found');
        }

        // Check if file exists in storage
        if (!Storage::exists($accomplishment->file_url)) {
            abort(404, 'File not found in storage');
        }

        $isPreview = $request->query('preview', false);

        if ($isPreview) {
            // Return file for inline preview in browser
            return Storage::response($accomplishment->file_url, $accomplishment->file_name, [
                'Content-Type' => $accomplishment->file_type ?: 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $accomplishment->file_name . '"'
            ]);
        } else {
            return Storage::download($accomplishment->file_url, $accomplishment->file_name);
        }
    }
}