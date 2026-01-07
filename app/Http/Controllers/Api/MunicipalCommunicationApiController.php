<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Concerns\ApiResponds;
use App\Models\MunicipalitiesCommunicationList;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class MunicipalCommunicationApiController extends Controller
{
    use ApiResponds;

    /**
     * Get all municipal communications with optional filtering
     */
    public function index(Request $request): JsonResponse
    {
        $query = MunicipalitiesCommunicationList::where('is_visible', true);

        // Search functionality
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('subject', 'like', "%{$searchTerm}%")
                  ->orWhere('extracted_text', 'like', "%{$searchTerm}%");
            });
        }

        // Filter by year
        if ($request->has('year') && $request->year) {
            $query->where('year', $request->year);
        }

        // Filter by municipality
        if ($request->has('municipality') && $request->municipality) {
            $query->where('Municipality', 'like', "%{$request->municipality}%");
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $communications = $query->paginate($perPage);

        return $this->okPaginated($communications, function ($communication) {
            $files = [];
            if ($communication->file_name && $communication->file_url) {
                $files[] = [
                    'id' => $communication->municipalitites_communication_id,
                    'filename' => $communication->file_name,
                    'original_filename' => $communication->file_name,
                    'file_size' => $communication->file_size,
                    'mime_type' => $communication->file_type,
                    'download_url' => url("/api/v1/Documents/MunicipalCommunication/{$communication->municipalitites_communication_id}/download"),
                    'preview_url' => url("/api/v1/Documents/MunicipalCommunication/{$communication->municipalitites_communication_id}/download?preview=1"),
                ];
            }

            return [
                'id' => $communication->municipalitites_communication_id,
                'title' => $communication->title,
                'subject' => $communication->subject,
                'municipality' => $communication->Municipality,
                'year' => $communication->year,
                'date_received' => $communication->date_received,
                'date_uploaded' => $communication->date_uploaded,
                'created_at' => $communication->created_at,
                'updated_at' => $communication->updated_at,
                'files' => $files,
            ];
        }, 'Municipal communications retrieved successfully');
    }

    /**
     * Get a specific municipal communication
     */
    public function show(MunicipalitiesCommunicationList $communication): JsonResponse
    {
        if (!$communication->is_visible) {
            abort(404, 'Communication not found');
        }

        $files = [];
        if ($communication->file_name && $communication->file_url) {
            $files[] = [
                'id' => $communication->municipalitites_communication_id,
                'filename' => $communication->file_name,
                'original_filename' => $communication->file_name,
                'file_size' => $communication->file_size,
                'mime_type' => $communication->file_type,
                'download_url' => url("/api/v1/Documents/MunicipalCommunication/{$communication->municipalitites_communication_id}/download"),
                'preview_url' => url("/api/v1/Documents/MunicipalCommunication/{$communication->municipalitites_communication_id}/download?preview=1")
            ];
        }

        $data = [
            'id' => $communication->municipalitites_communication_id,
            'title' => $communication->title,
            'subject' => $communication->subject,
            'municipality' => $communication->Municipality,
            'year' => $communication->year,
            'date_received' => $communication->date_received,
            'date_uploaded' => $communication->date_uploaded,
            'created_at' => $communication->created_at,
            'updated_at' => $communication->updated_at,
            'files' => $files
        ];

        return $this->ok($data, 'Municipal communication retrieved successfully');
    }

    /**
     * Download or preview a specific municipal communication file
     */
    public function downloadFile(Request $request, MunicipalitiesCommunicationList $communication): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        if (!$communication->is_visible) {
            abort(404, 'Communication not found');
        }

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