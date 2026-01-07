<?php

namespace App\Http\Controllers;

use App\Models\Accomplishment;
use App\Services\DocumentFilterService;
use App\Services\LoggingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class AccomplishmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Accomplishment::with(['uploader']);

        // Full-text search
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('file_name', 'like', "%{$searchTerm}%")
                  ->orWhere('extracted_text', 'like', "%{$searchTerm}%");
            });
        }

        $accomplishments = $query->latest('created_at')->get();

        return Inertia::render('Documents/Accomplishments', [
            'accomplishments' => $accomplishments,
            'stats' => [
                'total' => Accomplishment::count(),
                'thisMonth' => Accomplishment::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->count(),
                'thisYear' => Accomplishment::whereYear('created_at', now()->year)->count(),
                'totalFiles' => Accomplishment::whereNotNull('file_name')->count(),
            ],
            'filters' => [
                'years' => Accomplishment::whereNotNull('accomplishment_year')
                    ->distinct()
                    ->pluck('accomplishment_year')
                    ->map(function ($date) {
                        return $date->format('Y');
                    })
                    ->unique()
                    ->sort()
                    ->values()
            ],
            'searchQuery' => $request->search ?? '',
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Forms/AccomplishmentForm');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'accomplishment_year' => 'nullable|date',
            'extracted_text' => 'nullable',
            'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png|max:10240', // 10MB max
        ]);

        $validated['uploaded_by'] = auth()->id();
        $validated['date_uploaded'] = now();

        // Handle extracted_text
        $validated['extracted_text'] = is_string($validated['extracted_text']) ? $validated['extracted_text'] : (is_array($validated['extracted_text']) ? implode('\n\n', array_filter($validated['extracted_text'])) : null);

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('accomplishments', $fileName, 'private');

            $validated['file_name'] = $file->getClientOriginalName();
            $validated['file_url'] = $filePath;
            $validated['file_size'] = $file->getSize();
            $validated['file_type'] = $file->getMimeType();
        }

        $accomplishment = Accomplishment::create($validated);

        // Log the creation
        LoggingService::logCreate($accomplishment, "Created accomplishment: {$accomplishment->title}");

        return redirect()->route('accomplishments.index')
            ->with('success', 'Accomplishment created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Accomplishment $accomplishment)
    {
        // Log the view
        LoggingService::logView($accomplishment, "Viewed accomplishment: {$accomplishment->title}");

        return Inertia::render('Show/AccomplishmentShow', [
            'accomplishment' => $accomplishment->load(['uploader']),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Accomplishment $accomplishment)
    {
        return Inertia::render('Forms/AccomplishmentForm', [
            'accomplishment' => $accomplishment,
            'isEditing' => true,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Accomplishment $accomplishment)
    {
        if (!$this->canEditDocument($accomplishment)) {
            return redirect()->back()->with('error', 'You do not have permission to edit this document.');
        }

        $oldValues = $accomplishment->getAttributes();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'accomplishment_year' => 'nullable|date',
            'extracted_text' => 'nullable',
            'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png|max:10240', // 10MB max
            'is_visible' => 'boolean',
        ]);

        $validated['last_modified_by'] = auth()->id();
        $validated['last_modified_at'] = now();

        // Handle extracted_text
        $validated['extracted_text'] = is_string($validated['extracted_text']) ? $validated['extracted_text'] : (is_array($validated['extracted_text']) ? implode('\n\n', array_filter($validated['extracted_text'])) : null);

        // Handle file upload
        if ($request->hasFile('file')) {
            // Delete old file if exists
            if ($accomplishment->file_url) {
                Storage::disk('private')->delete($accomplishment->file_url);
            }

            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('accomplishments', $fileName, 'private');

            $validated['file_name'] = $file->getClientOriginalName();
            $validated['file_url'] = $filePath;
            $validated['file_size'] = $file->getSize();
            $validated['file_type'] = $file->getMimeType();
            $validated['date_uploaded'] = now();
        }

        $accomplishment->update($validated);

        // Log the update
        LoggingService::logUpdate($accomplishment, $oldValues, "Updated accomplishment: {$accomplishment->title}");

        return redirect()->route('accomplishments.index')
            ->with('success', 'Accomplishment updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Accomplishment $accomplishment)
    {
        if (!$this->canDeleteDocument($accomplishment)) {
            return redirect()->back()->with('error', 'You do not have permission to delete this document.');
        }

        // Delete associated file if exists
        if ($accomplishment->file_url) {
            Storage::disk('private')->delete($accomplishment->file_url);
        }

        // Log the deletion
        LoggingService::logDelete($accomplishment, "Deleted accomplishment: {$accomplishment->title}");

        $accomplishment->delete();

        return redirect()->route('accomplishments.index')
            ->with('success', 'Accomplishment deleted successfully.');
    }

    /**
     * Download the file associated with the accomplishment.
     */
    public function download(Accomplishment $accomplishment)
    {
        if (!$accomplishment->file_url) {
            return redirect()->back()->with('error', 'No file available for download.');
        }

        // Log the download
        LoggingService::logView($accomplishment, "Downloaded accomplishment file: {$accomplishment->title}");

        $filePath = Storage::disk('private')->path($accomplishment->file_url);
        return response()->download($filePath, $accomplishment->file_name);
    }
}