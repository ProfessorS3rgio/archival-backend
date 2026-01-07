<?php

namespace App\Http\Controllers;

use App\Models\ProvincialOffice;
use App\Services\DocumentFilterService;
use App\Services\LoggingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ProvincialOfficeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ProvincialOffice::with(['addedBy']);

        // Full-text search
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('subject', 'like', "%{$searchTerm}%")
                  ->orWhere('extracted_text', 'like', "%{$searchTerm}%")
                    ->orWhere('year', 'like', "%{$searchTerm}%");
            });
        }

        $provincialOffices = $query->latest('created_at')->get();

        return Inertia::render('Documents/Offices/ProvincialOffices', [
            'provincialOffices' => $provincialOffices,
            'stats' => [
                'total' => ProvincialOffice::count(),
                'thisMonth' => ProvincialOffice::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->count(),
                'thisYear' => ProvincialOffice::whereYear('created_at', now()->year)->count(),
            ],
            'filters' => [
                'provincialOffices' => ProvincialOffice::whereNotNull('subject')
                    ->distinct()
                    ->pluck('subject')
                    ->sort()
                    ->values(),
                'years' => ProvincialOffice::whereNotNull('year')
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
        return Inertia::render('Forms/ProvincialOfficeForm', [
            'provincialOfficeOptions' => ProvincialOffice::whereNotNull('subject')
                ->distinct()
                ->pluck('subject')
                ->sort()
                ->values(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'year' => 'nullable|integer|min:1900|max:' . (date('Y') + 10),
            'subject' => 'nullable|string|max:255',
            'title' => 'nullable|string|max:255',
            'Date_received' => 'nullable|date',
            'files' => 'nullable|array',
            'files.*' => 'file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png|max:10240', // 10MB max
            'extracted_text' => 'nullable|string',
            'is_visible' => 'boolean',
        ]);

        // Handle file upload
        if ($request->hasFile('files') && is_array($request->file('files')) && count($request->file('files')) > 0) {
            $file = $request->file('files')[0]; // Take the first file

            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('provincial-offices', $fileName, 'private');

            if (!$filePath) {
                return back()->withErrors(['file' => 'Failed to store file.']);
            }

            $validated['file_name'] = $file->getClientOriginalName();
            $validated['file_url'] = $filePath;
            $validated['file_size'] = $file->getSize();
            $validated['file_type'] = $file->getMimeType();
            $validated['date_uploaded'] = now();
        }

        $validated['added_by'] = auth()->id();

        $provincialOffice = ProvincialOffice::create($validated);

        // Log the creation
        LoggingService::logCreate($provincialOffice, "Created provincial office communication: {$provincialOffice->title}");

        return redirect()->route('provincial-offices.index')
            ->with('success', 'Provincial office communication created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        $provincialOffice = ProvincialOffice::where('provincial_offices_list_id', $id)->first();

        if (!$provincialOffice) {
            abort(404);
        }

        $communication = $provincialOffice->load(['addedBy', 'lastModifier']);

        // Log the view
        LoggingService::logView($communication, "Viewed provincial office communication: {$communication->title}");

        return Inertia::render('Show/ProvincialOfficeShow', [
            'communication' => $communication,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, $id)
    {
        $provincialOffice = ProvincialOffice::where('provincial_offices_list_id', $id)->first();

        if (!$provincialOffice) {
            abort(404);
        }

        $loadedData = $provincialOffice->load(['addedBy', 'lastModifier']);

        return Inertia::render('Forms/ProvincialOfficeForm', [
            'provincialOffice' => $loadedData,
            'isEditing' => true,
            'provincialOfficeOptions' => ProvincialOffice::whereNotNull('subject')
                ->distinct()
                ->pluck('subject')
                ->sort()
                ->values(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $provincialOffice = ProvincialOffice::where('provincial_offices_list_id', $id)->first();

        if (!$provincialOffice) {
            abort(404);
        }

        if (!$this->canEditDocument($provincialOffice)) {
            return redirect()->back()->with('error', 'You do not have permission to edit this document.');
        }

        $oldValues = $provincialOffice->getAttributes();

        $validated = $request->validate([
            'year' => 'nullable|integer|min:1900|max:' . (date('Y') + 10),
            'subject' => 'nullable|string|max:255',
            'title' => 'nullable|string|max:255',
            'Date_received' => 'nullable|date',
            'files' => 'nullable|array',
            'files.*' => 'file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png|max:10240', // 10MB max
            'extracted_text' => 'nullable|string',
            'is_visible' => 'boolean',
            'remove_existing_file' => 'nullable',
        ]);

        // Handle file removal
        $remove_existing_file = $request->input('remove_existing_file');
        if ($remove_existing_file === true || $remove_existing_file === '1' || $remove_existing_file === 'true') {
            if ($provincialOffice->file_url && Storage::disk('private')->exists($provincialOffice->file_url)) {
                Storage::disk('private')->delete($provincialOffice->file_url);
            }
            $validated['file_name'] = null;
            $validated['file_url'] = null;
            $validated['file_size'] = null;
            $validated['file_type'] = null;
            $validated['extracted_text'] = null;
            $validated['date_uploaded'] = null;
        }

        // Handle file upload
        if ($request->hasFile('files') && is_array($request->file('files')) && count($request->file('files')) > 0) {
            $file = $request->file('files')[0]; // Take the first file

            // Delete old file if exists
            if ($provincialOffice->file_url && Storage::disk('private')->exists($provincialOffice->file_url)) {
                Storage::disk('private')->delete($provincialOffice->file_url);
            }

            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('provincial-offices', $fileName, 'private');

            if (!$filePath) {
                return back()->withErrors(['file' => 'Failed to store file.']);
            }

            $validated['file_name'] = $file->getClientOriginalName();
            $validated['file_url'] = $filePath;
            $validated['file_size'] = $file->getSize();
            $validated['file_type'] = $file->getMimeType();
            $validated['date_uploaded'] = now();
        }

        $validated['last_modified_by'] = auth()->id();
        $validated['last_modified_at'] = now();

        $provincialOffice->update($validated);

        // Log the update
        LoggingService::logUpdate($provincialOffice, $oldValues, "Updated provincial office communication: {$provincialOffice->title}");

        return back()->with('success', 'Provincial office communication updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        $provincialOffice = ProvincialOffice::where('provincial_offices_list_id', $id)->first();

        if (!$provincialOffice) {
            abort(404);
        }

        if (!$this->canDeleteDocument($provincialOffice)) {
            return redirect()->back()->with('error', 'You do not have permission to delete this document.');
        }

        // Delete file if exists
        if ($provincialOffice->file_url && Storage::disk('private')->exists($provincialOffice->file_url)) {
            Storage::disk('private')->delete($provincialOffice->file_url);
        }

        // Log the deletion
        LoggingService::logDelete($provincialOffice, "Deleted provincial office communication: {$provincialOffice->title}");

        $provincialOffice->delete();

        return redirect()->route('provincial-offices.index')
            ->with('success', 'Provincial office communication deleted successfully.');
    }
}