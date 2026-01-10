<?php

namespace App\Http\Controllers;

use App\Models\DraftResolution;
use App\Models\DraftResolutionFile;
use App\Services\DocumentFilterService;
use App\Services\LoggingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class DraftResolutionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $drafts = DraftResolution::with('files')->latest('created_at')->get();

        $stats = [
            'total' => $drafts->count(),
            'inReview' => $drafts->where('status', 'in_review')->count(),
            'pendingApproval' => $drafts->where('status', 'pending_approval')->count(),
            'thisMonth' => $drafts->where('created_at', '>=', now()->startOfMonth())->count(),
        ];

        return Inertia::render('Documents/DraftResolution', [
            'drafts' => $drafts,
            'stats' => $stats,
            'filters' => DocumentFilterService::getAllFilters($drafts, [
                'subject_field' => null, // Draft resolutions don't have subjects
                'date_field' => 'created_at', // Use created_at instead of date_approved
            ]),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Forms/DraftResolutionForm');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'author' => 'nullable|string',
            'session' => 'nullable|string',
            'title' => 'nullable|string',
            'subject' => 'nullable|string',
            'status' => 'nullable|string|in:draft,in_review,pending_approval',
            'added_by' => 'required|exists:users,id',
        ]);

        // Ensure subject is always set to empty string for draft resolutions
        $validated['subject'] = $validated['subject'] ?? '';

        $draft = DraftResolution::create($validated);

        // Handle file uploads if present
        if ($request->hasFile('files')) {
            $files = $request->file('files');
            foreach ($files as $index => $file) {
                $originalName = $file->getClientOriginalName();
                $fileName = time() . '_' . uniqid() . '_' . $originalName;
                $filePath = $file->storeAs('draft-resolutions', $fileName, 'private');

                $rawExtracted = $request->input("extracted_text.{$index}");
                $extractedText = null;
                if (is_string($rawExtracted)) {
                    $normalized = trim($rawExtracted);
                    if ($normalized !== '' && strtolower($normalized) !== 'null') {
                        $extractedText = $rawExtracted;
                    }
                } elseif (!is_null($rawExtracted)) {
                    $extractedText = $rawExtracted;
                }

                DraftResolutionFile::create([
                    'resolution_draft_id' => $draft->resolution_draft_id,
                    'uploaded_by' => $request->added_by,
                    'file_name' => $originalName,
                    'file_url' => $filePath,
                    'file_size' => $file->getSize(),
                    'file_type' => $file->getClientMimeType(),
                    'extracted_text' => $extractedText,
                    'uploaded_at' => now(),
                ]);
            }
        }

        // Log the creation
        LoggingService::logCreate($draft, "Created draft resolution: {$draft->title}");

        return redirect()->back()->with('success', 'Draft resolution created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $draft = DraftResolution::with('files')->findOrFail($id);

        // Provide a signed public URL for Office Online previewing
        $draft->files->transform(function ($file) {
            $file->public_preview_url = URL::temporarySignedRoute(
                'public-serve-file',
                now()->addMinutes(60),
                ['filename' => $file->file_url]
            );
            return $file;
        });

        // Log the view
        LoggingService::logView($draft, "Viewed draft resolution: {$draft->title}");

        if ($request->wantsJson()) {
            $draftData = $draft->toArray();
            unset($draftData['files']);

            return response()->json([
                'draft' => $draftData,
                'files' => $this->mapFilesForResponse($draft->files),
            ]);
        }

        return Inertia::render('Show/DraftResolutionShow', [
            'draft' => $draft,
        ]);
    }

    /**
     * Display the specified resource for dashboard view (no edit button).
     */
    public function dashboardView(string $id)
    {
        $draft = DraftResolution::with('files')->findOrFail($id);

        // Log the view
        LoggingService::logView($draft, "Viewed draft resolution from dashboard: {$draft->title}");

        return Inertia::render('Dashboard/DraftResolutionView', [
            'draft' => $draft,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $draft = DraftResolution::with('files')->findOrFail($id);

        return Inertia::render('Forms/DraftResolutionForm', [
            'draft' => $draft,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $draft = DraftResolution::findOrFail($id);

        if (!$this->canEditDocument($draft)) {
            return redirect()->back()->with('error', 'You do not have permission to edit this document.');
        }

        $oldValues = $draft->getAttributes();

        $validated = $request->validate([
            'author' => 'nullable|string',
            'session' => 'nullable|string',
            'title' => 'nullable|string',
            'subject' => 'nullable|string',
            'status' => 'nullable|string|in:draft,in_review,pending_approval',
        ]);

        // Ensure subject is always set to empty string for draft resolutions
        $validated['subject'] = $validated['subject'] ?? '';

        $draft->update($validated);

        // Handle file uploads if present
        if ($request->hasFile('files')) {
            $files = $request->file('files');
            foreach ($files as $index => $file) {
                $originalName = $file->getClientOriginalName();
                $fileName = time() . '_' . uniqid() . '_' . $originalName;
                $filePath = $file->storeAs('draft-resolutions', $fileName, 'private');

                $rawExtracted = $request->input("extracted_text.{$index}");
                $extractedText = null;
                if (is_string($rawExtracted)) {
                    $normalized = trim($rawExtracted);
                    if ($normalized !== '' && strtolower($normalized) !== 'null') {
                        $extractedText = $rawExtracted;
                    }
                } elseif (!is_null($rawExtracted)) {
                    $extractedText = $rawExtracted;
                }

                DraftResolutionFile::create([
                    'resolution_draft_id' => $draft->resolution_draft_id,
                    'uploaded_by' => $request->added_by,
                    'file_name' => $originalName,
                    'file_url' => $filePath,
                    'file_size' => $file->getSize(),
                    'file_type' => $file->getClientMimeType(),
                    'extracted_text' => $extractedText,
                    'uploaded_at' => now(),
                ]);
            }
        }

        // Handle file removals if present
        if ($request->has('remove_files')) {
            $filesToRemove = $request->input('remove_files');
            foreach ($filesToRemove as $fileId) {
                $file = DraftResolutionFile::find($fileId);
                if ($file) {
                    // Delete file from storage
                    Storage::disk('private')->delete($file->file_url);
                    // Delete file record
                    $file->delete();
                }
            }
        }

        // Log the update
        LoggingService::logUpdate($draft, $oldValues, "Updated draft resolution: {$draft->title}");

        return redirect()->back()->with('success', 'Draft resolution updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $draft = DraftResolution::findOrFail($id);

        if (!$this->canDeleteDocument($draft)) {
            return redirect()->back()->with('error', 'You do not have permission to delete this document.');
        }

        // Log the deletion before actually deleting
        LoggingService::logDelete($draft, "Deleted draft resolution: {$draft->title}");

        // Delete associated files
        foreach ($draft->files as $file) {
            Storage::disk('private')->delete($file->file_url);
            $file->delete();
        }

        $draft->delete();
        return redirect()->route('draft-resolutions.index');
    }

    protected function mapFilesForResponse($files): array
    {
        return $files->map(function ($file) {
            $relativePath = ltrim($file->file_url, '/');
            $downloadUrl = url("/storage/{$relativePath}");

            return [
                'id' => $file->file_id,
                'file_id' => $file->file_id,
                'file_name' => $file->file_name,
                'file_url' => $file->file_url,
                'download_url' => $downloadUrl,
                'preview_url' => url("/serve-file/{$file->file_url}"),
                'file_type' => $file->file_type,
                'file_size' => $file->file_size,
                'uploaded_at' => optional($file->uploaded_at)->toDateTimeString(),
                'created_at' => optional($file->uploaded_at ?? $file->created_at)->toDateTimeString(),
                'updated_at' => optional($file->updated_at)->toDateTimeString(),
            ];
        })->values()->all();
    }
}
