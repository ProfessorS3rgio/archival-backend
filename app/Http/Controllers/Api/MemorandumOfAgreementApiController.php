<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Concerns\ApiResponds;
use App\Models\MemorandumOfAgreement;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class MemorandumOfAgreementApiController extends Controller
{
    use ApiResponds;

    /**
     * Get all memorandums of agreement with optional filtering
     */
    public function index(Request $request): JsonResponse
    {
        $query = MemorandumOfAgreement::query();

        // Search functionality
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('resolution_no', 'like', "%{$searchTerm}%")
                  ->orWhere('extracted_text', 'like', "%{$searchTerm}%");
            });
        }

        // Filter by year
        if ($request->has('year') && $request->year) {
            $query->where('year', $request->year);
        }

        // Filter by resolution number
        if ($request->has('resolution_no') && $request->resolution_no) {
            $query->where('resolution_no', 'like', "%{$request->resolution_no}%");
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $moas = $query->paginate($perPage);

        return $this->okPaginated($moas, function ($moa) {
            $files = [];
            if ($moa->file_name && $moa->file_url) {
                $files[] = [
                    'id' => $moa->moa_id,
                    'filename' => $moa->file_name,
                    'original_filename' => $moa->file_name,
                    'file_size' => $moa->file_size,
                    'mime_type' => $moa->file_type,
                    'download_url' => url("/api/v1/Documents/MemorandumOfAgreement/{$moa->moa_id}/download"),
                    'preview_url' => url("/api/v1/Documents/MemorandumOfAgreement/{$moa->moa_id}/download?preview=1"),
                ];
            }

            return [
                'id' => $moa->moa_id,
                'resolution_no' => $moa->resolution_no,
                'title' => $moa->title,
                'year' => $moa->year,
                'date_received' => $moa->date_received,
                'date_uploaded' => $moa->date_uploaded,
                'created_at' => $moa->created_at,
                'updated_at' => $moa->updated_at,
                'files' => $files,
            ];
        }, 'Memorandums of agreement retrieved successfully');
    }

    /**
     * Get a specific memorandum of agreement
     */
    public function show(MemorandumOfAgreement $moa): JsonResponse
    {
        $files = [];
        if ($moa->file_name && $moa->file_url) {
            $files[] = [
                'id' => $moa->moa_id,
                'filename' => $moa->file_name,
                'original_filename' => $moa->file_name,
                'file_size' => $moa->file_size,
                'mime_type' => $moa->file_type,
                'download_url' => url("/api/v1/Documents/MemorandumOfAgreement/{$moa->moa_id}/download"),
                'preview_url' => url("/api/v1/Documents/MemorandumOfAgreement/{$moa->moa_id}/download?preview=1")
            ];
        }

        $data = [
            'id' => $moa->moa_id,
            'resolution_no' => $moa->resolution_no,
            'title' => $moa->title,
            'year' => $moa->year,
            'date_received' => $moa->date_received,
            'date_uploaded' => $moa->date_uploaded,
            'created_at' => $moa->created_at,
            'updated_at' => $moa->updated_at,
            'files' => $files
        ];

        return $this->ok($data, 'Memorandum of agreement retrieved successfully');
    }

    /**
     * Download or preview a specific memorandum of agreement file
     */
    public function downloadFile(Request $request, MemorandumOfAgreement $moa): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        if (!$moa->file_name || !$moa->file_url) {
            abort(404, 'File not found');
        }

        // Check if file exists in storage
        if (!Storage::exists($moa->file_url)) {
            abort(404, 'File not found in storage');
        }

        $isPreview = $request->query('preview', false);

        if ($isPreview) {
            // Return file for inline preview in browser
            return Storage::response($moa->file_url, $moa->file_name, [
                'Content-Type' => $moa->file_type ?: 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $moa->file_name . '"'
            ]);
        } else {
            return Storage::download($moa->file_url, $moa->file_name);
        }
    }
}