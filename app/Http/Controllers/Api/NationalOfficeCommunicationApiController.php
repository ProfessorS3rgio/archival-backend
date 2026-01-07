<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Concerns\ApiResponds;
use App\Models\NationalOfficeCommunicationList;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class NationalOfficeCommunicationApiController extends Controller
{
    use ApiResponds;

    /**
     * Get all national office communications with optional filtering
     */
    public function index(Request $request): JsonResponse
    {
        $query = NationalOfficeCommunicationList::query();

        // Search functionality
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('provincial_resolution_no', 'like', "%{$searchTerm}%")
                  ->orWhere('title', 'like', "%{$searchTerm}%")
                  ->orWhere('subject', 'like', "%{$searchTerm}%")
                  ->orWhere('extracted_text', 'like', "%{$searchTerm}%");
            });
        }

        // Filter by year
        if ($request->has('year') && $request->year) {
            $query->where('year', $request->year);
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $communications = $query->paginate($perPage);

        return $this->okPaginated($communications, function ($communication) {
            $files = [];
            if ($communication->file_name && $communication->file_url) {
                $files[] = [
                    'id' => $communication->national_offices_id,
                    'filename' => $communication->file_name,
                    'original_filename' => $communication->file_name,
                    'file_size' => $communication->file_size,
                    'mime_type' => $communication->file_type,
                    'download_url' => url("/api/v1/Documents/NationalOfficeCommunication/{$communication->national_offices_id}/download"),
                    'preview_url' => url("/api/v1/Documents/NationalOfficeCommunication/{$communication->national_offices_id}/download?preview=1"),
                ];
            }

            return [
                'id' => $communication->national_offices_id,
                'provincial_resolution_no' => $communication->provincial_resolution_no,
                'title' => $communication->title,
                'subject' => $communication->subject,
                'year' => $communication->year,
                'date_received' => $communication->date_received,
                'created_at' => $communication->created_at,
                'updated_at' => $communication->updated_at,
                'files' => $files,
            ];
        }, 'National office communications retrieved successfully');
    }

    /**
     * Get a specific national office communication
     */
    public function show(NationalOfficeCommunicationList $communication): JsonResponse
    {
        $files = [];
        if ($communication->file_name && $communication->file_url) {
            $files[] = [
                'id' => $communication->national_offices_id,
                'filename' => $communication->file_name,
                'original_filename' => $communication->file_name,
                'file_size' => $communication->file_size,
                'mime_type' => $communication->file_type,
                'download_url' => url("/api/v1/Documents/NationalOfficeCommunication/{$communication->national_offices_id}/download"),
                'preview_url' => url("/api/v1/Documents/NationalOfficeCommunication/{$communication->national_offices_id}/download?preview=1")
            ];
        }

        $data = [
            'id' => $communication->national_offices_id,
            'provincial_resolution_no' => $communication->provincial_resolution_no,
            'title' => $communication->title,
            'subject' => $communication->subject,
            'year' => $communication->year,
            'date_received' => $communication->date_received,
            'created_at' => $communication->created_at,
            'updated_at' => $communication->updated_at,
            'files' => $files
        ];

        return $this->ok($data, 'National office communication retrieved successfully');
    }

    /**
     * Download or preview a specific national office communication file
     */
    public function downloadFile(Request $request, NationalOfficeCommunicationList $communication): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        if (!$communication->file_name || !$communication->file_url) {
            abort(404, 'File not found');
        }

        // Check if file exists in storage
        if (!Storage::exists($communication->file_url)) {
            abort(404, 'File not found in storage');
        }

        $isPreview = $request->query('preview', false);

        if ($isPreview) {
            // Return file for inline preview in browser
            return Storage::response($communication->file_url, $communication->file_name, [
                'Content-Type' => $communication->file_type ?: 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $communication->file_name . '"'
            ]);
        } else {
            return Storage::download($communication->file_url, $communication->file_name);
        }
    }
}