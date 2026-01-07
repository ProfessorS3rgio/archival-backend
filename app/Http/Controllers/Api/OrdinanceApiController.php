<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Concerns\ApiResponds;
use App\Models\Ordinance;
use App\Models\OrdinanceFile;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class OrdinanceApiController extends Controller
{
    use ApiResponds;

    /**
     * Get all ordinances with optional filtering
     */
    public function index(Request $request): JsonResponse
    {
        $query = Ordinance::with('files');

        // Search functionality
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('ordinance_no', 'like', "%{$searchTerm}%")
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

        // Pagination
        $perPage = $request->get('per_page', 15);
        $ordinances = $query->paginate($perPage);

        return $this->okPaginated($ordinances, function ($ordinance) {
            return [
                'id' => $ordinance->ordinance_id,
                'ordinance_no' => $ordinance->ordinance_no,
                'title' => $ordinance->title,
                'author' => $ordinance->author,
                'subject' => $ordinance->subject,
                'date_approved' => $ordinance->date_approved,
                'created_at' => $ordinance->created_at,
                'updated_at' => $ordinance->updated_at,
                'files' => $ordinance->files->map(function ($file) use ($ordinance) {
                    return [
                        'id' => $file->file_id,
                        'filename' => $file->file_name,
                        'original_filename' => $file->file_name,
                        'file_size' => $file->file_size,
                        'mime_type' => $file->file_type,
                        'download_url' => url("/api/v1/Documents/Ordinance/{$ordinance->ordinance_id}/files/{$file->file_id}/download"),
                        'preview_url' => url("/api/v1/Documents/Ordinance/{$ordinance->ordinance_id}/files/{$file->file_id}/download?preview=1"),
                    ];
                })->values()->all(),
            ];
        }, 'Ordinances retrieved successfully');
    }

    /**
     * Get a specific ordinance
     */
    public function show(Ordinance $ordinance): JsonResponse
    {
        $ordinance->load('files');

        $data = [
            'id' => $ordinance->ordinance_id,
            'ordinance_no' => $ordinance->ordinance_no,
            'title' => $ordinance->title,
            'author' => $ordinance->author,
            'subject' => $ordinance->subject,
            'date_approved' => $ordinance->date_approved,
            'created_at' => $ordinance->created_at,
            'updated_at' => $ordinance->updated_at,
            'files' => $ordinance->files->map(function ($file) use ($ordinance) {
                return [
                    'id' => $file->file_id,
                    'filename' => $file->file_name,
                    'original_filename' => $file->file_name,
                    'file_size' => $file->file_size,
                    'mime_type' => $file->file_type,
                    'download_url' => url("/api/v1/Documents/Ordinance/{$ordinance->ordinance_id}/files/{$file->file_id}/download"),
                    'preview_url' => url("/api/v1/Documents/Ordinance/{$ordinance->ordinance_id}/files/{$file->file_id}/download?preview=1")
                ];
            })
        ];

        return $this->ok($data, 'Ordinance retrieved successfully');
    }

    /**
     * Download or preview a specific file from an ordinance
     */
    public function downloadFile(Request $request, Ordinance $ordinance, OrdinanceFile $file): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        \Log::info("Download/Preview attempt for ordinance {$ordinance->ordinance_id}, file {$file->file_id}");

        // Ensure the file belongs to the ordinance
        if ($file->ordinance_id !== $ordinance->ordinance_id) {
            \Log::warning("File {$file->file_id} does not belong to ordinance {$ordinance->ordinance_id}");
            abort(404, 'File not found');
        }

        // Check if file exists in storage
        if (!Storage::exists($file->file_url)) {
            \Log::warning("File {$file->file_url} not found in storage");
            abort(404, 'File not found in storage');
        }

        $isPreview = $request->query('preview', false);

        if ($isPreview) {
            \Log::info("Previewing file {$file->file_url}");
            // Return file for inline preview in browser
            return Storage::response($file->file_url, $file->file_name, [
                'Content-Type' => $file->file_type ?: 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $file->file_name . '"'
            ]);
        } else {
            \Log::info("Downloading file {$file->file_url}");
            return Storage::download($file->file_url, $file->file_name);
        }
    }
}