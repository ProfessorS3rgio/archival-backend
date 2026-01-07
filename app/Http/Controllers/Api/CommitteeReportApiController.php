<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Concerns\ApiResponds;
use App\Models\CommitteeReport;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class CommitteeReportApiController extends Controller
{
    use ApiResponds;

    /**
     * Get all committee reports with optional filtering
     */
    public function index(Request $request): JsonResponse
    {
        $query = CommitteeReport::query();

        // Search functionality
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('committee_report_no', 'like', "%{$searchTerm}%")
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
        $reports = $query->paginate($perPage);

        return $this->okPaginated($reports, function ($report) {
            $files = [];
            if ($report->file_name && $report->file_url) {
                $files[] = [
                    'id' => $report->report_id,
                    'filename' => $report->file_name,
                    'original_filename' => $report->file_name,
                    'file_size' => $report->file_size,
                    'mime_type' => $report->file_type,
                    'download_url' => url("/api/v1/Documents/CommitteeReport/{$report->report_id}/download"),
                    'preview_url' => url("/api/v1/Documents/CommitteeReport/{$report->report_id}/download?preview=1"),
                ];
            }

            return [
                'id' => $report->report_id,
                'committee_report_no' => $report->committee_report_no,
                'title' => $report->title,
                'subject' => $report->subject,
                'year' => $report->year,
                'created_at' => $report->created_at,
                'updated_at' => $report->updated_at,
                'files' => $files,
            ];
        }, 'Committee reports retrieved successfully');
    }

    /**
     * Get a specific committee report
     */
    public function show(CommitteeReport $report): JsonResponse
    {
        $files = [];
        if ($report->file_name && $report->file_url) {
            $files[] = [
                'id' => $report->report_id,
                'filename' => $report->file_name,
                'original_filename' => $report->file_name,
                'file_size' => $report->file_size,
                'mime_type' => $report->file_type,
                'download_url' => url("/api/v1/Documents/CommitteeReport/{$report->report_id}/download"),
                'preview_url' => url("/api/v1/Documents/CommitteeReport/{$report->report_id}/download?preview=1")
            ];
        }

        $data = [
            'id' => $report->report_id,
            'committee_report_no' => $report->committee_report_no,
            'title' => $report->title,
            'subject' => $report->subject,
            'year' => $report->year,
            'created_at' => $report->created_at,
            'updated_at' => $report->updated_at,
            'files' => $files
        ];

        return $this->ok($data, 'Committee report retrieved successfully');
    }

    /**
     * Download or preview a specific committee report file
     */
    public function downloadFile(Request $request, CommitteeReport $report): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        if (!$report->file_name || !$report->file_url) {
            abort(404, 'File not found');
        }

        // Check if file exists in storage
        if (!Storage::exists($report->file_url)) {
            abort(404, 'File not found in storage');
        }

        $isPreview = $request->query('preview', false);

        if ($isPreview) {
            // Return file for inline preview in browser
            return Storage::response($report->file_url, $report->file_name, [
                'Content-Type' => $report->file_type ?: 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $report->file_name . '"'
            ]);
        } else {
            return Storage::download($report->file_url, $report->file_name);
        }
    }
}