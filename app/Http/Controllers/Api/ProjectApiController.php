<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Concerns\ApiResponds;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class ProjectApiController extends Controller
{
    use ApiResponds;

    /**
     * Get all projects with optional filtering
     */
    public function index(Request $request): JsonResponse
    {
        $query = Project::query();

        // Search functionality
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('project_name', 'like', "%{$searchTerm}%")
                  ->orWhere('code_no', 'like', "%{$searchTerm}%")
                  ->orWhere('location', 'like', "%{$searchTerm}%")
                  ->orWhere('district', 'like', "%{$searchTerm}%")
                  ->orWhere('extracted_text', 'like', "%{$searchTerm}%");
            });
        }

        // Filter by year
        if ($request->has('year') && $request->year) {
            $query->where('year', $request->year);
        }

        // Filter by district
        if ($request->has('district') && $request->district) {
            $query->where('district', 'like', "%{$request->district}%");
        }

        // Filter by source fund
        if ($request->has('source_fund') && $request->source_fund) {
            $query->where('source_fund', 'like', "%{$request->source_fund}%");
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $projects = $query->paginate($perPage);

        return $this->okPaginated($projects, function ($project) {
            $files = [];
            if ($project->file_name && $project->file_url) {
                $files[] = [
                    'id' => $project->project_id,
                    'filename' => $project->file_name,
                    'original_filename' => $project->file_name,
                    'file_size' => $project->file_size,
                    'mime_type' => $project->file_type,
                    'download_url' => url("/api/v1/Documents/Project/{$project->project_id}/download"),
                    'preview_url' => url("/api/v1/Documents/Project/{$project->project_id}/download?preview=1"),
                ];
            }

            return [
                'id' => $project->project_id,
                'code_no' => $project->code_no,
                'project_name' => $project->project_name,
                'district' => $project->district,
                'location' => $project->location,
                'year' => $project->year,
                'source_fund' => $project->source_fund,
                'source_type' => $project->source_type,
                'amount' => $project->amount,
                'created_at' => $project->created_at,
                'updated_at' => $project->updated_at,
                'files' => $files,
            ];
        }, 'Projects retrieved successfully');
    }

    /**
     * Get a specific project
     */
    public function show(Project $project): JsonResponse
    {
        $files = [];
        if ($project->file_name && $project->file_url) {
            $files[] = [
                'id' => $project->project_id,
                'filename' => $project->file_name,
                'original_filename' => $project->file_name,
                'file_size' => $project->file_size,
                'mime_type' => $project->file_type,
                'download_url' => url("/api/v1/Documents/Project/{$project->project_id}/download"),
                'preview_url' => url("/api/v1/Documents/Project/{$project->project_id}/download?preview=1")
            ];
        }

        $data = [
            'id' => $project->project_id,
            'code_no' => $project->code_no,
            'project_name' => $project->project_name,
            'district' => $project->district,
            'location' => $project->location,
            'year' => $project->year,
            'source_fund' => $project->source_fund,
            'source_type' => $project->source_type,
            'amount' => $project->amount,
            'created_at' => $project->created_at,
            'updated_at' => $project->updated_at,
            'files' => $files
        ];

        return $this->ok($data, 'Project retrieved successfully');
    }

    /**
     * Download or preview a specific project file
     */
    public function downloadFile(Request $request, Project $project): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        if (!$project->file_name || !$project->file_url) {
            abort(404, 'File not found');
        }

        // Check if file exists in storage
        if (!Storage::exists($project->file_url)) {
            abort(404, 'File not found in storage');
        }

        $isPreview = $request->query('preview', false);

        if ($isPreview) {
            // Return file for inline preview in browser
            return Storage::response($project->file_url, $project->file_name, [
                'Content-Type' => $project->file_type ?: 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $project->file_name . '"'
            ]);
        } else {
            return Storage::download($project->file_url, $project->file_name);
        }
    }
}