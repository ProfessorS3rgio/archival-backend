<?php

namespace App\Http\Controllers;

use App\Models\Resolution;
use App\Models\ResolutionFile;
use App\Services\DocumentFilterService;
use App\Services\LoggingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Inertia\Inertia;

class ResolutionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Resolution::with('files');

        // Full-text search through extracted text
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('resolution_no', 'like', "%{$searchTerm}%")
                  ->orWhere('author', 'like', "%{$searchTerm}%")
                  ->orWhere('subject', 'like', "%{$searchTerm}%")
                  ->orWhere('extracted_text', 'like', "%{$searchTerm}%")
                  ->orWhereHas('files', function ($fileQuery) use ($searchTerm) {
                      $fileQuery->where('extracted_text', 'like', "%{$searchTerm}%");
                  });
            });
        }

        $resolutions = $query->get();

        return Inertia::render('Documents/Resolution/Resolutions', [
            'resolutions' => $resolutions,
            'stats' => [
                'total' => Resolution::count(),
                'approved' => Resolution::whereNotNull('date_approved')->count(),
                'pending' => Resolution::whereNull('date_approved')->count(),
                'thisMonth' => Resolution::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->count(),
            ],
            'filters' => DocumentFilterService::getAllFilters(Resolution::with('files')->get()),
            'searchQuery' => $request->search ?? '',
        ]);
    }

    /**
     * Display a listing of provincial resolutions only.
     */
    public function provincialIndex(Request $request)
    {
        $query = Resolution::with('files')->where('is_municipal', false);

        // Full-text search through extracted text
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('resolution_no', 'like', "%{$searchTerm}%")
                  ->orWhere('author', 'like', "%{$searchTerm}%")
                  ->orWhere('subject', 'like', "%{$searchTerm}%")
                  ->orWhere('extracted_text', 'like', "%{$searchTerm}%")
                  ->orWhereHas('files', function ($fileQuery) use ($searchTerm) {
                      $fileQuery->where('extracted_text', 'like', "%{$searchTerm}%");
                  });
            });
        }

        $resolutions = $query->get();

        return Inertia::render('Documents/Resolution/Resolutions', [
            'resolutions' => $resolutions,
            'stats' => [
                'total' => Resolution::where('is_municipal', false)->count(),
                'approved' => Resolution::where('is_municipal', false)->whereNotNull('date_approved')->count(),
                'pending' => Resolution::where('is_municipal', false)->whereNull('date_approved')->count(),
                'thisMonth' => Resolution::where('is_municipal', false)->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->count(),
            ],
            'filters' => DocumentFilterService::getAllFilters(Resolution::with('files')->where('is_municipal', false)->get()),
            'searchQuery' => $request->search ?? '',
        ]);
    }

    /**
     * Display a listing of municipal resolutions only.
     */
    public function municipalIndex(Request $request)
    {
        $query = Resolution::with('files')->where('is_municipal', true);

        // Full-text search through extracted text
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('resolution_no', 'like', "%{$searchTerm}%")
                  ->orWhere('author', 'like', "%{$searchTerm}%")
                  ->orWhere('subject', 'like', "%{$searchTerm}%")
                  ->orWhere('extracted_text', 'like', "%{$searchTerm}%")
                  ->orWhereHas('files', function ($fileQuery) use ($searchTerm) {
                      $fileQuery->where('extracted_text', 'like', "%{$searchTerm}%");
                  });
            });
        }

        $resolutions = $query->get();

        return Inertia::render('Documents/Resolution/Resolutions', [
            'resolutions' => $resolutions,
            'stats' => [
                'total' => Resolution::where('is_municipal', true)->count(),
                'approved' => Resolution::where('is_municipal', true)->whereNotNull('date_approved')->count(),
                'pending' => Resolution::where('is_municipal', true)->whereNull('date_approved')->count(),
                'thisMonth' => Resolution::where('is_municipal', true)->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->count(),
            ],
            'filters' => DocumentFilterService::getAllFilters(Resolution::with('files')->where('is_municipal', true)->get()),
            'searchQuery' => $request->search ?? '',
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Forms/ResolutionForm', [
            'autoFillEnabled' => \App\Models\Setting::getAutoFillEnabled(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate(rules: [
            'resolution_no' => 'nullable|string',
            'author' => 'nullable|string',
            'session' => 'nullable|string',
            'title' => 'nullable|string',
            'subject' => 'nullable|string',
            'date_approved' => 'nullable|date',
            'municipality' => 'nullable|string',
            'is_municipal' => 'boolean',
            'added_by' => 'required|exists:users,id',
            'extracted_text' => 'nullable',
            'file_extracted_texts' => 'nullable|array',
            'file_extracted_texts.*' => 'nullable|string',
            'files' => 'nullable|array',
            'files.*' => 'file|mimes:pdf,doc,docx|max:10240', // 10MB max
        ]);

        // Create the resolution
        $resolution = Resolution::create([
            'resolution_no' => $validated['resolution_no'],
            'author' => $validated['author'],
            'session' => $validated['session'],
            'title' => $validated['title'],
            'subject' => $validated['subject'] ?? '',
            'date_approved' => $validated['date_approved'],
            'municipality' => $validated['municipality'],
            'is_municipal' => $validated['is_municipal'] ?? false,
            'added_by' => $validated['added_by'],
            'extracted_text' => is_string($validated['extracted_text']) ? $validated['extracted_text'] : (is_array($validated['extracted_text']) ? implode('\n\n', array_filter($validated['extracted_text'])) : null),
        ]);

        // Handle file uploads
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $index => $file) {
                $originalName = $file->getClientOriginalName();
                $fileName = $originalName;
                
                // Handle filename conflicts by adding a counter if needed
                $baseName = pathinfo($originalName, PATHINFO_FILENAME);
                $extension = pathinfo($originalName, PATHINFO_EXTENSION);
                $counter = 1;
                
                // Determine folder based on is_municipal
                if ($validated['is_municipal']) {
                    // For municipal documents, create subfolder based on municipality name
                    $municipalityName = strtolower(str_replace(' ', '_', $validated['municipality']));
                    $folder = 'municipal/' . $municipalityName;
                } else {
                    $folder = 'provincial';
                }
                $fullPath = 'resolutions/' . $folder . '/' . $fileName;
                
                while (Storage::disk('local')->exists($fullPath)) {
                    $fileName = $baseName . '_' . $counter . '.' . $extension;
                    $fullPath = 'resolutions/' . $folder . '/' . $fileName;
                    $counter++;
                }
                
                $path = $file->storeAs('resolutions/' . $folder, $fileName, 'local');

                // Get per-file extracted text from file_extracted_texts array
                $fileExtractedTexts = $request->input('file_extracted_texts', []);
                $extractedText = null;
                if (isset($fileExtractedTexts[$index]) && is_string($fileExtractedTexts[$index])) {
                    $normalized = trim($fileExtractedTexts[$index]);
                    if ($normalized !== '' && strtolower($normalized) !== 'null') {
                        $extractedText = $fileExtractedTexts[$index];
                    }
                }

                ResolutionFile::create([
                    'resolution_id' => $resolution->resolution_id,
                    'uploaded_by' => $validated['added_by'],
                    'file_name' => $originalName,
                    'file_url' => $path,
                    'file_size' => $file->getSize(),
                    'file_type' => $file->getMimeType(),
                    'extracted_text' => $extractedText,
                ]);
            }
        }

        return redirect('/resolutions')->with('success', 'Resolution created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $resolution = Resolution::with('files')->findOrFail($id);

        // Provide a signed public URL for Office Online previewing
        $resolution->files->transform(function ($file) {
            $file->public_preview_url = URL::temporarySignedRoute(
                'public-serve-file',
                now()->addMinutes(60),
                ['filename' => $file->file_url]
            );
            return $file;
        });

        // Log the view
        LoggingService::logView($resolution, "Viewed resolution: {$resolution->title}");

        if ($request->wantsJson()) {
            $resolutionData = $resolution->toArray();
            unset($resolutionData['files']);

            return response()->json([
                'resolution' => $resolutionData,
                'files' => $this->mapFilesForResponse($resolution->files),
            ]);
        }

        return Inertia::render('Show/ResolutionShow', [
            'resolution' => $resolution,
        ]);
    }

    /**
     * Display the specified resource for dashboard view (no edit button).
     */
    public function dashboardView(string $id)
    {
        $resolution = Resolution::with('files')->findOrFail($id);

        // Provide a signed public URL for Office Online previewing
        $resolution->files->transform(function ($file) {
            $file->public_preview_url = URL::temporarySignedRoute(
                'public-serve-file',
                now()->addMinutes(60),
                ['filename' => $file->file_url]
            );
            return $file;
        });

        // Log the view
        LoggingService::logView($resolution, "Viewed resolution from dashboard: {$resolution->title}");

        return Inertia::render('Dashboard/ResolutionView', [
            'resolution' => $resolution,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $resolution = Resolution::with('files')->findOrFail($id);

        // Provide a signed public URL for Office Online previewing
        $resolution->files->transform(function ($file) {
            $file->public_preview_url = URL::temporarySignedRoute(
                'public-serve-file',
                now()->addMinutes(60),
                ['filename' => $file->file_url]
            );
            return $file;
        });

        return Inertia::render('Forms/ResolutionForm', [
            'resolution' => $resolution,
            'autoFillEnabled' => \App\Models\Setting::getAutoFillEnabled(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $resolution = Resolution::findOrFail($id);
        
        // Check authorization: Staff can only edit their own documents
        if (!$this->canEditDocument($resolution)) {
            return redirect()->back()->with('error', 'You do not have permission to edit this document.');
        }
        
        $oldValues = $resolution->getAttributes();
        
        // Debug logging
        \Log::info('Resolution update request received', [
            'id' => $id,
            'has_files' => $request->hasFile('files'),
            'files_count' => $request->hasFile('files') ? count($request->file('files')) : 0,
            'all_files' => $request->allFiles(),
            'request_data' => $request->all()
        ]);
        
        $validated = $request->validate([
            'resolution_no' => 'nullable|string',
            'author' => 'nullable|string',
            'session' => 'nullable|string',
            'title' => 'nullable|string',
            'subject' => 'nullable|string',
            'date_approved' => 'nullable|date',
            'added_by' => 'nullable|integer',
            'extracted_text' => 'nullable',
            'file_extracted_texts' => 'nullable|array',
            'file_extracted_texts.*' => 'nullable|string',
            'remove_files' => 'nullable|array',
            'remove_files.*' => 'integer|exists:resolution_files,file_id',
                        'files' => 'nullable|array',
            'files.*' => 'file|mimes:pdf,doc,docx|max:10240', // 10MB max
        ]);

        $resolution = Resolution::findOrFail($id);
       $resolution->fill($validated);

        // Ensure subject is always set to empty string for resolutions
        $validated['subject'] = $validated['subject'] ?? '';

        // Handle extracted_text
        $validated['extracted_text'] = is_string($validated['extracted_text']) ? $validated['extracted_text'] : (is_array($validated['extracted_text']) ? implode('\n\n', array_filter($validated['extracted_text'])) : null);

        $resolution->update($validated);

        // Handle file removals
        \Log::info('Resolution update: full request data', [
            'all' => $request->all(),
            'has_files' => $request->hasFile('files'),
            'has_remove_files' => $request->has('remove_files'),
            'content_type' => $request->header('Content-Type')
        ]);
        
        $removeFiles = $request->input('remove_files', []);
        \Log::info('Resolution update: remove_files received', [
            'remove_files' => $removeFiles, 
            'remove_files_type' => gettype($removeFiles)
        ]);
        
        // Handle different input formats that might come from the form
        if (is_null($removeFiles)) {
            $removeFiles = [];
        } else if (!is_array($removeFiles)) {
            $removeFiles = [$removeFiles]; // Convert single value to array
        }
        
        \Log::info('Resolution files to remove (processed)', ['count' => count($removeFiles), 'files' => $removeFiles]);
        
        if (!empty($removeFiles)) {
            foreach ($removeFiles as $fileId) {
                \Log::info('Attempting to remove resolution file', ['file_id' => $fileId]);
                $file = \App\Models\ResolutionFile::find($fileId);
                if ($file && (string)$file->resolution_id === (string)$id) {
                    $exists = Storage::disk('public')->exists($file->file_url);
                    \Log::info('File found for deletion', ['path' => $file->file_url, 'exists' => $exists]);
                    if ($exists) {
                        Storage::disk('public')->delete($file->file_url);
                    }
                    $file->delete();
                    \Log::info('Resolution file record deleted');
                } else {
                    \Log::warning('Resolution file not found or mismatch', ['file_id' => $fileId, 'resolution_id' => $id]);
                }
            }
        }

        // Handle new file uploads
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $index => $file) {
                $originalName = $file->getClientOriginalName();
                $fileName = $originalName;
                
                // Handle filename conflicts by adding a counter if needed
                $baseName = pathinfo($originalName, PATHINFO_FILENAME);
                $extension = pathinfo($originalName, PATHINFO_EXTENSION);
                $counter = 1;
                
                // Determine folder based on is_municipal
                $folder = $resolution->is_municipal ? 'municipal' : 'provincial';
                $fullPath = 'resolutions/' . $folder . '/' . $fileName;
                
                while (Storage::disk('local')->exists($fullPath)) {
                    $fileName = $baseName . '_' . $counter . '.' . $extension;
                    $fullPath = 'resolutions/' . $folder . '/' . $fileName;
                    $counter++;
                }
                
                $path = $file->storeAs('resolutions/' . $folder, $fileName, 'local');

                // Get per-file extracted text from file_extracted_texts array
                $fileExtractedTexts = $request->input('file_extracted_texts', []);
                $extractedText = null;
                if (isset($fileExtractedTexts[$index]) && is_string($fileExtractedTexts[$index])) {
                    $normalized = trim($fileExtractedTexts[$index]);
                    if ($normalized !== '' && strtolower($normalized) !== 'null') {
                        $extractedText = $fileExtractedTexts[$index];
                    }
                }

                ResolutionFile::create([
                    'resolution_id' => $resolution->resolution_id,
                    'uploaded_by' => $validated['added_by'] ?? $resolution->added_by,
                    'file_name' => $originalName,
                    'file_url' => $path,
                    'file_size' => $file->getSize(),
                    'file_type' => $file->getMimeType(),
                    'extracted_text' => $extractedText,
                ]);
            }
        }

        return redirect('/resolutions')->with('success', 'Resolution updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $resolution = Resolution::findOrFail($id);

        $resolution->delete();

        return redirect()->back()->with('success', 'Resolution deleted successfully!');
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
