<?php

namespace App\Http\Controllers;

use App\Models\MemorandumOfAgreement;
use App\Services\DocumentFilterService;
use App\Services\LoggingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Inertia\Inertia;

class MemorandumOfAgreementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = MemorandumOfAgreement::with(['addedBy']);

        // Full-text search
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('resolution_no', 'like', "%{$searchTerm}%")
                  ->orWhere('extracted_text', 'like', "%{$searchTerm}%");
            });
        }

        $memorandumOfAgreements = $query->latest('created_at')->get();

        return Inertia::render('Documents/MemorandumOfAgreements', [
            'memorandumOfAgreements' => $memorandumOfAgreements,
            'stats' => [
                'total' => MemorandumOfAgreement::count(),
                'thisMonth' => MemorandumOfAgreement::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->count(),
                'thisYear' => MemorandumOfAgreement::whereYear('created_at', now()->year)->count(),
            ],
            'filters' => [
                'years' => MemorandumOfAgreement::whereNotNull('year')
                    ->distinct()
                    ->pluck('year')
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
        return Inertia::render('Forms/MemorandumOfAgreementForm');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'resolution_no' => 'nullable|string|max:255',
            'date_received' => 'nullable|date',
            'title' => 'required|string|max:255',
            'year' => 'nullable|integer|min:1900|max:' . (date('Y') + 10),
            'extracted_text' => 'nullable',
            'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png|max:10240', // 10MB max
        ]);

        $validated['added_by'] = auth()->id();
        $validated['date_uploaded'] = now();

        // Handle extracted_text
        $validated['extracted_text'] = is_string($validated['extracted_text']) ? $validated['extracted_text'] : (is_array($validated['extracted_text']) ? implode('\n\n', array_filter($validated['extracted_text'])) : null);

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('memorandum-of-agreements', $fileName, 'private');

            $validated['file_name'] = $file->getClientOriginalName();
            $validated['file_url'] = $filePath;
            $validated['file_size'] = $file->getSize();
            $validated['file_type'] = $file->getMimeType();
        }

        $memorandumOfAgreement = MemorandumOfAgreement::create($validated);

        // Log the creation
        LoggingService::logCreate($memorandumOfAgreement, "Created MOA: {$memorandumOfAgreement->title}");

        return redirect()->route('memorandum-of-agreements.index')
            ->with('success', 'Memorandum of Agreement created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MemorandumOfAgreement $memorandumOfAgreement)
    {
        // Provide a signed public URL for Office Online previewing
        $memorandumOfAgreement->public_preview_url = URL::temporarySignedRoute(
            'public-serve-file',
            now()->addMinutes(60),
            ['filename' => $memorandumOfAgreement->file_url]
        );

        // Log the view
        LoggingService::logView($memorandumOfAgreement, "Viewed MOA: {$memorandumOfAgreement->title}");

        return Inertia::render('Show/MemorandumOfAgreementShow', [
            'memorandumOfAgreement' => $memorandumOfAgreement->load(['addedBy']),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MemorandumOfAgreement $memorandumOfAgreement)
    {
        // Provide a signed public URL for Office Online previewing
        $memorandumOfAgreement->public_preview_url = URL::temporarySignedRoute(
            'public-serve-file',
            now()->addMinutes(60),
            ['filename' => $memorandumOfAgreement->file_url]
        );

        return Inertia::render('Forms/MemorandumOfAgreementForm', [
            'memorandumOfAgreement' => $memorandumOfAgreement,
            'isEditing' => true,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MemorandumOfAgreement $memorandumOfAgreement)
    {
        if (!$this->canEditDocument($memorandumOfAgreement)) {
            return redirect()->back()->with('error', 'You do not have permission to edit this document.');
        }

        $oldValues = $memorandumOfAgreement->getAttributes();

        $validated = $request->validate([
            'resolution_no' => 'nullable|string|max:255',
            'date_received' => 'nullable|date',
            'title' => 'required|string|max:255',
            'year' => 'nullable|integer|min:1900|max:' . (date('Y') + 10),
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
            if ($memorandumOfAgreement->file_url) {
                Storage::disk('private')->delete($memorandumOfAgreement->file_url);
            }

            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('memorandum-of-agreements', $fileName, 'private');

            $validated['file_name'] = $file->getClientOriginalName();
            $validated['file_url'] = $filePath;
            $validated['file_size'] = $file->getSize();
            $validated['file_type'] = $file->getMimeType();
            $validated['date_uploaded'] = now();
        }

        $memorandumOfAgreement->update($validated);

        // Log the update
        LoggingService::logUpdate($memorandumOfAgreement, $oldValues, "Updated MOA: {$memorandumOfAgreement->title}");

        return redirect()->route('memorandum-of-agreements.index')
            ->with('success', 'Memorandum of Agreement updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MemorandumOfAgreement $memorandumOfAgreement)
    {
        if (!$this->canDeleteDocument($memorandumOfAgreement)) {
            return redirect()->back()->with('error', 'You do not have permission to delete this document.');
        }

        // Delete associated file if exists
        if ($memorandumOfAgreement->file_url) {
            Storage::disk('private')->delete($memorandumOfAgreement->file_url);
        }

        // Log the deletion
        LoggingService::logDelete($memorandumOfAgreement, "Deleted MOA: {$memorandumOfAgreement->title}");

        $memorandumOfAgreement->delete();

        return redirect()->route('memorandum-of-agreements.index')
            ->with('success', 'Memorandum of Agreement deleted successfully.');
    }

    /**
     * Download the file associated with the memorandum of agreement.
     */
    public function download(MemorandumOfAgreement $memorandumOfAgreement)
    {
        if (!$memorandumOfAgreement->file_url) {
            abort(404, 'File not found');
        }

        // Log the download
        LoggingService::logDownload($memorandumOfAgreement, "Downloaded MOA file: {$memorandumOfAgreement->file_name}");

        return Storage::disk('private')->download($memorandumOfAgreement->file_url, $memorandumOfAgreement->file_name);
    }
}