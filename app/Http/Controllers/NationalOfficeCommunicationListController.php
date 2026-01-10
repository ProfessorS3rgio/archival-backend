<?php

namespace App\Http\Controllers;

use App\Models\NationalOfficeCommunicationList;
use App\Services\DocumentFilterService;
use App\Services\LoggingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Inertia\Inertia;

class NationalOfficeCommunicationListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = NationalOfficeCommunicationList::with(['addedBy', 'lastModifier']);

        // Full-text search
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('provincial_resolution_no', 'like', "%{$searchTerm}%")
                  ->orWhere('subject', 'like', "%{$searchTerm}%")
                  ->orWhere('file_name', 'like', "%{$searchTerm}%")
                  ->orWhere('extracted_text', 'like', "%{$searchTerm}%");
            });
        }

        // Filter by office/subject
        if ($request->has('office') && $request->office) {
            $query->where('subject', $request->office);
        }

        // Filter by year
        if ($request->has('year') && $request->year) {
            $query->where('year', $request->year);
        }

        $communications = $query->latest('created_at')->get();

        return Inertia::render('Documents/Offices/NationalOffices', [
            'communications' => $communications,
            'stats' => [
                'total' => NationalOfficeCommunicationList::count(),
                'thisMonth' => NationalOfficeCommunicationList::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->count(),
                'thisYear' => NationalOfficeCommunicationList::whereYear('created_at', now()->year)->count(),
            ],
            'filters' => [
                'offices' => NationalOfficeCommunicationList::whereNotNull('subject')
                    ->distinct()
                    ->pluck('subject')
                    ->sort()
                    ->values(),
                'years' => NationalOfficeCommunicationList::whereNotNull('year')
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
        return Inertia::render('Forms/NationalOfficeForm', [
            'subjectOptions' => NationalOfficeCommunicationList::whereNotNull('subject')
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
            'title' => 'nullable|string|max:255',
            'subject' => 'nullable|string|max:255',
            'provincial_resolution_no' => 'nullable|string|max:255',
            'year' => 'nullable|integer|min:1900|max:' . (date('Y') + 10),
            'date_received' => 'nullable|date',
            'files' => 'nullable|array',
            'files.*' => 'file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png|max:10240', // 10MB max
            'extracted_text' => 'nullable|string',
        ]);

        $validated['added_by'] = auth()->id();

        // Handle file upload (take first file since schema only supports one)
        if ($request->hasFile('files') && is_array($request->file('files')) && count($request->file('files')) > 0) {
            $file = $request->file('files')[0]; // Take the first file

            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('national-office-communications', $fileName, 'private');

            if (!$filePath) {
                return back()->withErrors(['file' => 'Failed to store file.']);
            }

            $validated['file_name'] = $file->getClientOriginalName();
            $validated['file_url'] = $filePath;
            $validated['file_size'] = $file->getSize();
            $validated['file_type'] = $file->getMimeType();
            $validated['date_uploaded'] = now();
        }

        $communication = NationalOfficeCommunicationList::create($validated);

        // Log the creation
        LoggingService::logCreate($communication, "Created national office communication: {$communication->title}");

        return redirect()->route('national-office-communications.index')
            ->with('success', 'National office communication created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        // Debug logging before binding
        \Log::info('National Office Show Method - Before Binding', [
            'route_param_id' => $id,
            'request_params' => $request->all(),
        ]);

        $nationalOfficeCommunicationList = NationalOfficeCommunicationList::where('national_offices_id', $id)->first();

        if (!$nationalOfficeCommunicationList) {
            \Log::error('National Office Show Method - Record not found', [
                'searched_id' => $id,
            ]);
            abort(404);
        }

        $communication = $nationalOfficeCommunicationList->load(['addedBy', 'lastModifier']);

        // Provide a signed public URL for Office Online previewing
        $communication->public_preview_url = URL::temporarySignedRoute(
            'public-serve-file',
            now()->addMinutes(60),
            ['filename' => $communication->file_url]
        );

        // Log the view
        LoggingService::logView($communication, "Viewed national office communication: {$communication->title}");

        // Debug logging
        \Log::info('National Office Show Method', [
            'id' => $nationalOfficeCommunicationList->national_offices_id,
            'communication_data' => $communication->toArray(),
            'route_key' => $nationalOfficeCommunicationList->getRouteKey(),
        ]);

        return Inertia::render('Show/NationalOfficeCommunicationShow', [
            'communication' => $communication,
        ]);
    }

    public function edit(Request $request, $id)
    {
        // Debug logging before binding
        \Log::info('National Office Edit Method - Before Binding', [
            'route_param_id' => $id,
            'request_params' => $request->all(),
        ]);

        $nationalOfficeCommunicationList = NationalOfficeCommunicationList::where('national_offices_id', $id)->first();

        if (!$nationalOfficeCommunicationList) {
            \Log::error('National Office Edit Method - Record not found', [
                'searched_id' => $id,
            ]);
            abort(404);
        }

        $loadedData = $nationalOfficeCommunicationList->load(['addedBy', 'lastModifier']);

        // Provide a signed public URL for Office Online previewing
        $loadedData->public_preview_url = URL::temporarySignedRoute(
            'public-serve-file',
            now()->addMinutes(60),
            ['filename' => $loadedData->file_url]
        );

        // Debug logging
        \Log::info('National Office Edit Method', [
            'id' => $nationalOfficeCommunicationList->national_offices_id,
            'loaded_data' => $loadedData->toArray(),
            'route_key' => $nationalOfficeCommunicationList->getRouteKey(),
        ]);

        return Inertia::render('Forms/NationalOfficeForm', [
            'nationalOffice' => $loadedData,
            'isEditing' => true,
            'subjectOptions' => NationalOfficeCommunicationList::whereNotNull('subject')
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
        $nationalOfficeCommunicationList = NationalOfficeCommunicationList::where('national_offices_id', $id)->first();

        if (!$nationalOfficeCommunicationList) {
            abort(404);
        }

        if (!$this->canEditDocument($nationalOfficeCommunicationList)) {
            return redirect()->back()->with('error', 'You do not have permission to edit this document.');
        }

        $oldValues = $nationalOfficeCommunicationList->getAttributes();

        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'subject' => 'nullable|string|max:255',
            'provincial_resolution_no' => 'nullable|string|max:255',
            'year' => 'nullable|integer|min:1900|max:' . (date('Y') + 10),
            'date_received' => 'nullable|date',
            'files' => 'nullable|array',
            'files.*' => 'file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png|max:10240', // 10MB max
            'extracted_text' => 'nullable|string',
            'is_visible' => 'boolean',
            'remove_existing_file' => 'nullable',
        ]);

        // Handle file removal
        $remove_existing_file = $request->input('remove_existing_file');
        if ($remove_existing_file === true || $remove_existing_file === '1' || $remove_existing_file === 'true') {
            if ($nationalOfficeCommunicationList->file_url && Storage::disk('private')->exists($nationalOfficeCommunicationList->file_url)) {
                Storage::disk('private')->delete($nationalOfficeCommunicationList->file_url);
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
            if ($nationalOfficeCommunicationList->file_url && Storage::disk('private')->exists($nationalOfficeCommunicationList->file_url)) {
                Storage::disk('private')->delete($nationalOfficeCommunicationList->file_url);
            }

            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('national-office-communications', $fileName, 'private');

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

        $nationalOfficeCommunicationList->update($validated);

        // Log the update
        LoggingService::logUpdate($nationalOfficeCommunicationList, $oldValues, "Updated national office communication: {$nationalOfficeCommunicationList->title}");

        return redirect()->route('national-office-communications.index')
            ->with('success', 'National office communication updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        $nationalOfficeCommunicationList = NationalOfficeCommunicationList::where('national_offices_id', $id)->first();

        if (!$nationalOfficeCommunicationList) {
            abort(404);
        }

        if (!$this->canDeleteDocument($nationalOfficeCommunicationList)) {
            return redirect()->back()->with('error', 'You do not have permission to delete this document.');
        }

        // Delete file if exists
        if ($nationalOfficeCommunicationList->file_url && Storage::disk('private')->exists($nationalOfficeCommunicationList->file_url)) {
            Storage::disk('private')->delete($nationalOfficeCommunicationList->file_url);
        }

        // Log the deletion
        LoggingService::logDelete($nationalOfficeCommunicationList, "Deleted national office communication: {$nationalOfficeCommunicationList->title}");

        $nationalOfficeCommunicationList->delete();

        return redirect()->route('national-office-communications.index')
            ->with('success', 'National office communication deleted successfully.');
    }
}
