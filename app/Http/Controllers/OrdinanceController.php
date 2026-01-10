<?php

namespace App\Http\Controllers;

use App\Models\Ordinance;
use App\Models\OrdinanceFile;
use App\Services\DocumentFilterService;
use App\Services\LoggingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Inertia\Inertia;

class OrdinanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Ordinance::with('files');

        // Full-text search through extracted text
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('ordinance_no', 'like', "%{$searchTerm}%")
                  ->orWhere('author', 'like', "%{$searchTerm}%")
                  ->orWhere('subject', 'like', "%{$searchTerm}%")
                  ->orWhere('extracted_text', 'like', "%{$searchTerm}%")
                  ->orWhereHas('files', function ($fileQuery) use ($searchTerm) {
                      $fileQuery->where('extracted_text', 'like', "%{$searchTerm}%");
                  });
            });
        }

        $ordinances = $query->get();

        return Inertia::render('Documents/Ordinance/Ordinances', [
            'ordinances' => $ordinances,
            'stats' => [
                'total' => Ordinance::count(),
                'approved' => Ordinance::whereNotNull('date_approved')->count(),
                'pending' => Ordinance::whereNull('date_approved')->count(),
                'thisMonth' => Ordinance::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->count(),
            ],
            'filters' => DocumentFilterService::getAllFilters(Ordinance::with('files')->get()),
            'searchQuery' => $request->search ?? '',
        ]);
    }

    /**
     * Display a listing of provincial ordinances only.
     */
    public function provincialIndex(Request $request)
    {
        $query = Ordinance::with('files')->where('is_municipal', false);

        // Full-text search through extracted text
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('ordinance_no', 'like', "%{$searchTerm}%")
                  ->orWhere('author', 'like', "%{$searchTerm}%")
                  ->orWhere('subject', 'like', "%{$searchTerm}%")
                  ->orWhere('extracted_text', 'like', "%{$searchTerm}%")
                  ->orWhereHas('files', function ($fileQuery) use ($searchTerm) {
                      $fileQuery->where('extracted_text', 'like', "%{$searchTerm}%");
                  });
            });
        }

        $ordinances = $query->get();

        return Inertia::render('Documents/Ordinance/Ordinances', [
            'ordinances' => $ordinances,
            'stats' => [
                'total' => Ordinance::where('is_municipal', false)->count(),
                'approved' => Ordinance::where('is_municipal', false)->whereNotNull('date_approved')->count(),
                'pending' => Ordinance::where('is_municipal', false)->whereNull('date_approved')->count(),
                'thisMonth' => Ordinance::where('is_municipal', false)->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->count(),
            ],
            'filters' => DocumentFilterService::getAllFilters(Ordinance::with('files')->where('is_municipal', false)->get()),
            'searchQuery' => $request->search ?? '',
        ]);
    }

    /**
     * Display a listing of municipal ordinances only.
     */
    public function municipalIndex(Request $request)
    {
        $query = Ordinance::with('files')->where('is_municipal', true);

        // Full-text search through extracted text
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('ordinance_no', 'like', "%{$searchTerm}%")
                  ->orWhere('author', 'like', "%{$searchTerm}%")
                  ->orWhere('subject', 'like', "%{$searchTerm}%")
                  ->orWhere('extracted_text', 'like', "%{$searchTerm}%")
                  ->orWhereHas('files', function ($fileQuery) use ($searchTerm) {
                      $fileQuery->where('extracted_text', 'like', "%{$searchTerm}%");
                  });
            });
        }

        $ordinances = $query->get();

        return Inertia::render('Documents/Ordinance/Ordinances', [
            'ordinances' => $ordinances,
            'stats' => [
                'total' => Ordinance::where('is_municipal', true)->count(),
                'approved' => Ordinance::where('is_municipal', true)->whereNotNull('date_approved')->count(),
                'pending' => Ordinance::where('is_municipal', true)->whereNull('date_approved')->count(),
                'thisMonth' => Ordinance::where('is_municipal', true)->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->count(),
            ],
            'filters' => DocumentFilterService::getAllFilters(Ordinance::with('files')->where('is_municipal', true)->get()),
            'searchQuery' => $request->search ?? '',
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Forms/OrdinanceForm', [
            'autoFillEnabled' => \App\Models\Setting::getAutoFillEnabled(),
            'subjectOptions' => $this->getSubjectOptions(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ordinance_no' => 'nullable|string',
            'author' => 'nullable|string',
            'session' => 'nullable|string',
            'title' => 'nullable|string',
            'subject' => 'nullable|string',
            'date_approved' => 'nullable|date',
            'municipality' => 'nullable|string',
            'is_municipal' => 'boolean',
            'added_by' => 'required|integer',
            'extracted_text' => 'nullable',
            'file_extracted_texts' => 'nullable|array',
            'file_extracted_texts.*' => 'nullable|string',
            'files' => 'nullable|array',
            'files.*' => 'file|mimes:pdf,doc,docx|max:10240', // 10MB max
        ]);

        // Process extracted_text - ensure it's a string
        $extractedText = $validated['extracted_text'] ?? null;
        if (is_array($extractedText)) {
            $extractedText = implode("\n\n", array_filter($extractedText));
        }

        // Create the ordinance
        $ordinance = Ordinance::create([
            'ordinance_no' => $validated['ordinance_no'],
            'author' => $validated['author'],
            'session' => $validated['session'],
            'title' => $validated['title'],
            'subject' => $validated['subject'],
            'date_approved' => $validated['date_approved'],
            'municipality' => $validated['municipality'],
            'is_municipal' => $validated['is_municipal'] ?? false,
            'added_by' => $validated['added_by'],
            'extracted_text' => $extractedText,
        ]);

        // Handle file uploads
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $index => $file) {
                $originalName = $file->getClientOriginalName();
                $fileName = $originalName;
                
                // Handle filename conflicts by adding a counter if needed
                $counter = 1;
                $baseName = pathinfo($originalName, PATHINFO_FILENAME);
                $extension = pathinfo($originalName, PATHINFO_EXTENSION);
                
                // Determine folder based on is_municipal
                if ($validated['is_municipal']) {
                    // For municipal documents, create subfolder based on municipality name
                    $municipalityName = strtolower(str_replace(' ', '_', $validated['municipality']));
                    $folder = 'municipal/' . $municipalityName;
                } else {
                    $folder = 'provincial';
                }
                $fullPath = 'ordinances/' . $folder . '/' . $fileName;
                
                while (Storage::disk('local')->exists($fullPath)) {
                    $fileName = $baseName . '_' . $counter . '.' . $extension;
                    $fullPath = 'ordinances/' . $folder . '/' . $fileName;
                    $counter++;
                }
                
                $path = $file->storeAs('ordinances/' . $folder, $fileName, 'local');

                // Get per-file extracted text from file_extracted_texts array
                $fileExtractedTexts = $request->input('file_extracted_texts', []);
                $extractedText = null;
                if (isset($fileExtractedTexts[$index]) && is_string($fileExtractedTexts[$index])) {
                    $normalized = trim($fileExtractedTexts[$index]);
                    if ($normalized !== '' && strtolower($normalized) !== 'null') {
                        $extractedText = $fileExtractedTexts[$index];
                    }
                }

                OrdinanceFile::create([
                    'ordinance_id' => $ordinance->ordinance_id,
                    'uploaded_by' => $validated['added_by'],
                    'file_name' => $originalName,
                    'file_url' => $path,
                    'file_size' => $file->getSize(),
                    'file_type' => $file->getMimeType(),
                    'extracted_text' => $extractedText,
                ]);
            }
        }

        return redirect('/ordinances')->with('success', 'Ordinance created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $ordinance = Ordinance::with('files')->findOrFail($id);

        // Log the view
        LoggingService::logView($ordinance, "Viewed ordinance: {$ordinance->title}");

        if ($request->wantsJson()) {
            $ordinanceData = $ordinance->toArray();
            unset($ordinanceData['files']);

            return response()->json([
                'ordinance' => $ordinanceData,
                'files' => $this->mapFilesForResponse($ordinance->files),
            ]);
        }

        return Inertia::render('Show/OrdinanceShow', [
            'ordinance' => $ordinance,
        ]);
    }

    /**
     * Display the specified resource for dashboard view (no edit button).
     */
    public function dashboardView(string $id)
    {
        $ordinance = Ordinance::with('files')->findOrFail($id);

        // Log the view
        LoggingService::logView($ordinance, "Viewed ordinance from dashboard: {$ordinance->title}");

        return Inertia::render('Dashboard/OrdinanceView', [
            'ordinance' => $ordinance,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $ordinance = Ordinance::with('files')->findOrFail($id);

        // Provide a signed public URL for Office Online previewing
        $ordinance->files->transform(function ($file) {
            $file->public_preview_url = URL::temporarySignedRoute(
                'public-serve-file',
                now()->addMinutes(60),
                ['filename' => $file->file_url]
            );
            return $file;
        });

        return Inertia::render('Forms/OrdinanceForm', [
            'ordinance' => $ordinance,
            'autoFillEnabled' => \App\Models\Setting::getAutoFillEnabled(),
            'subjectOptions' => $this->getSubjectOptions(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $ordinance = Ordinance::findOrFail($id);
        
        // Check authorization: Staff can only edit their own documents
        if (!$this->canEditDocument($ordinance)) {
            return redirect()->back()->with('error', 'You do not have permission to edit this document.');
        }
        
        $oldValues = $ordinance->getAttributes();
        
        // Debug: Check if files are received
        \Log::info('Update request data', $request->all());
        \Log::info('Has files: ' . ($request->hasFile('files') ? 'true' : 'false'));
        \Log::info('Files data', $request->file('files') ?? []);
        
        $validated = $request->validate([
            'ordinance_no' => 'nullable|string',
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
            'remove_files.*' => 'integer|exists:ordinance_files,file_id',
            'files' => 'nullable|array',
            'files.*' => 'file|mimes:pdf,doc,docx|max:10240', // 10MB max
        ]);

        // Handle extracted_text
        $validated['extracted_text'] = is_string($validated['extracted_text']) ? $validated['extracted_text'] : (is_array($validated['extracted_text']) ? implode('\n\n', array_filter($validated['extracted_text'])) : null);

        $ordinance->update($validated);

        // Handle file removals
        $removeFiles = $request->input('remove_files', []);
        \Log::info('Ordinance update: remove_files received', [
            'raw_input' => $request->all(), 
            'remove_files' => $removeFiles, 
            'remove_files_type' => gettype($removeFiles)
        ]);
        
        // Handle different input formats that might come from the form
        if (is_null($removeFiles)) {
            $removeFiles = [];
        } else if (!is_array($removeFiles)) {
            $removeFiles = [$removeFiles]; // Convert single value to array
        }
        
        \Log::info('Ordinance files to remove (processed)', ['count' => count($removeFiles), 'files' => $removeFiles]);
        
        if (!empty($removeFiles)) {
            foreach ($removeFiles as $fileId) {
                \Log::info('Attempting to remove ordinance file', ['file_id' => $fileId]);
                $file = \App\Models\OrdinanceFile::find($fileId);
                if ($file && (string)$file->ordinance_id === (string)$id) {
                    $exists = Storage::disk('public')->exists($file->file_url);
                    \Log::info('File found for deletion', ['path' => $file->file_url, 'exists' => $exists]);
                    if ($exists) {
                        Storage::disk('public')->delete($file->file_url);
                    }
                    $file->delete();
                    \Log::info('Ordinance file record deleted');
                } else {
                    \Log::warning('Ordinance file not found or mismatch', ['file_id' => $fileId, 'ordinance_id' => $id]);
                }
            }
        }

        // Handle new file uploads
        if ($request->hasFile('files')) {
            \Log::info('Processing file uploads');
            $files = $request->file('files');
            \Log::info('Files array', $files ?: []);
            foreach ($files as $index => $file) {
                \Log::info('Processing file: ' . ($file ? $file->getClientOriginalName() : 'null'));
                if ($file) {
                $originalName = $file->getClientOriginalName();
                $fileName = $originalName;
                
                // Handle filename conflicts by adding a counter if needed
                $baseName = pathinfo($originalName, PATHINFO_FILENAME);
                $extension = pathinfo($originalName, PATHINFO_EXTENSION);
                $counter = 1;
                
                // Determine folder based on is_municipal
                if ($ordinance->is_municipal) {
                    // For municipal documents, create subfolder based on municipality name
                    $municipalityName = strtolower(str_replace(' ', '_', $ordinance->municipality));
                    $folder = 'municipal/' . $municipalityName;
                } else {
                    $folder = 'provincial';
                }
                $fullPath = 'ordinances/' . $folder . '/' . $fileName;
                
                while (Storage::disk('local')->exists($fullPath)) {
                    $fileName = $baseName . '_' . $counter . '.' . $extension;
                    $fullPath = 'ordinances/' . $folder . '/' . $fileName;
                    $counter++;
                }
                
                $path = $file->storeAs('ordinances/' . $folder, $fileName, 'local');

                    // Get per-file extracted text from file_extracted_texts array
                    $fileExtractedTexts = $request->input('file_extracted_texts', []);
                    $extractedText = null;
                    if (isset($fileExtractedTexts[$index]) && is_string($fileExtractedTexts[$index])) {
                        $normalized = trim($fileExtractedTexts[$index]);
                        if ($normalized !== '' && strtolower($normalized) !== 'null') {
                            $extractedText = $fileExtractedTexts[$index];
                        }
                    }

                    OrdinanceFile::create([
                        'ordinance_id' => $ordinance->ordinance_id,
                        'uploaded_by' => $validated['added_by'] ?? $ordinance->added_by,
                        'file_name' => $originalName,
                        'file_url' => $path,
                        'file_size' => $file->getSize(),
                        'file_type' => $file->getMimeType(),
                        'extracted_text' => $extractedText,
                    ]);
                }
            }
        } else {
            \Log::info('No files to upload - hasFile check failed');
        }

        return redirect('/ordinances')->with('success', 'Ordinance updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $ordinance = Ordinance::findOrFail($id);

        // Check authorization: Staff can only delete their own documents
        if (!$this->canDeleteDocument($ordinance)) {
            return redirect()->back()->with('error', 'You do not have permission to delete this document.');
        }

        $ordinance->delete();

        return redirect()->back()->with('success', 'Ordinance deleted successfully!');
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

    /**
     * Get unique authors from ordinances
     */
    private function getAvailableAuthors($ordinances)
    {
        return DocumentFilterService::getAvailableAuthors($ordinances);
    }

    /**
     * Get unique subjects from ordinances
     */
    private function getAvailableSubjects($ordinances)
    {
        return DocumentFilterService::getAvailableSubjects($ordinances);
    }

    /**
     * Get unique years from ordinances
     */
    private function getAvailableYears($ordinances)
    {
        return DocumentFilterService::getAvailableYears($ordinances);
    }

    /**
     * Get subject options for the form dropdown (default subjects + custom ones from database)
     */
    private function getSubjectOptions()
    {
        // Default subject options
        $defaultSubjects = [
            'Agriculture and Fishery',
            'Annual Investment Program',
            'Appropriation/ Local Governance',
            'Barangay Affairs',
            'Building/Housing and Land Regulations',
            'Business Establishment and Professional Regulations',
            'Ecology and Environmental Protection',
            'Economic Enterprises',
            'Education, Arts and Culture',
            'Games and Amusement',
            'Health and Sanitation',
            'Information Technology,Media and Science',
            'Internal Rules of Procedure',
            'Market Code',
            'Parks and Playgrounds',
            'Private and Public Sector',
            'Public and Private Partnership',
            'Public Morals,Youth, Minors and Social Welfare Services',
            'Public Order and Safety',
            'Public Property',
            'Public Works and Infrastructure',
            'Spanish Ordinance',
            'Taxes, Fees and Charges',
            'Tourism Development',
            'Traffic Management,Parking, and Motor Vehicles',
            'Transportation',
        ];

        // Get custom subjects from database (subjects that are not in the default list)
        $customSubjects = Ordinance::whereNotNull('subject')
            ->where('subject', '!=', '')
            ->distinct()
            ->pluck('subject')
            ->filter(function ($subject) use ($defaultSubjects) {
                return !in_array($subject, $defaultSubjects);
            })
            ->values()
            ->toArray();

        // Merge and sort all subjects
        $allSubjects = array_merge($defaultSubjects, $customSubjects);
        sort($allSubjects);

        return $allSubjects;
    }
}
