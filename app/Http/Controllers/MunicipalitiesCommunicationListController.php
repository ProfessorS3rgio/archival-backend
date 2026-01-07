<?php

namespace App\Http\Controllers;

use App\Models\MunicipalitiesCommunicationList;
use App\Services\DocumentFilterService;
use App\Services\LoggingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class MunicipalitiesCommunicationListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = MunicipalitiesCommunicationList::with(['adder', 'lastModifier']);

        // Full-text search
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('subject', 'like', "%{$searchTerm}%")
                  ->orWhere('file_name', 'like', "%{$searchTerm}%")
                  ->orWhere('extracted_text', 'like', "%{$searchTerm}%");
            });
        }

        // Apply subject filter
        if ($request->has('subject') && $request->subject) {
            $query->where('subject', $request->subject);
        }

        // Apply year filter
        if ($request->has('year') && $request->year) {
            $query->where('year', $request->year);
        }

        $communications = $query->latest('created_at')->get();

        return Inertia::render('Documents/Offices/MunicipalCommunications', [
            'communications' => $communications,
            'stats' => [
                'total' => MunicipalitiesCommunicationList::count(),
                'thisMonth' => MunicipalitiesCommunicationList::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->count(),
                'thisYear' => MunicipalitiesCommunicationList::whereYear('created_at', now()->year)->count(),
            ],
            'filters' => [
                'years' => MunicipalitiesCommunicationList::whereNotNull('year')
                    ->distinct()
                    ->pluck('year')
                    ->sort()
                    ->values(),
                'subjects' => MunicipalitiesCommunicationList::whereNotNull('subject')
                    ->distinct()
                    ->pluck('subject')
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
        return Inertia::render('Forms/MunicipalCommunicationForm');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subject' => 'nullable|string|max:255',
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

            // Validate file attributes
            $fileName = $file->getClientOriginalName();
            $fileSize = $file->getSize();
            $fileType = $file->getMimeType();

            // Ensure file attributes are valid
            if (empty($fileName) || $fileSize <= 0 || empty($fileType)) {
                return back()->withErrors(['file' => 'Invalid file uploaded.']);
            }

            $fileName = time() . '_' . $fileName;
            $filePath = $file->storeAs('municipal-communications', $fileName, 'private');

            // Validate that file was stored successfully
            if (!$filePath) {
                return back()->withErrors(['file' => 'Failed to store file.']);
            }

            $validated['file_name'] = $file->getClientOriginalName();
            $validated['file_url'] = $filePath;
            $validated['file_size'] = $fileSize;
            $validated['file_type'] = $fileType;
            $validated['date_uploaded'] = now();
        }

        $communication = MunicipalitiesCommunicationList::create($validated);

        // Log the creation
        LoggingService::logCreate($communication, "Created municipal communication: {$communication->title}");

        return redirect()->route('municipal-communications.index')
            ->with('success', 'Municipal communication created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MunicipalitiesCommunicationList $municipalCommunication)
    {
        // Log the view
        LoggingService::logView($municipalCommunication, "Viewed municipal communication: {$municipalCommunication->title}");

        return Inertia::render('Show/MunicipalCommunicationShow', [
            'municipalCommunication' => $municipalCommunication->load(['adder', 'lastModifier']),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MunicipalitiesCommunicationList $municipalCommunication)
    {
        return Inertia::render('Forms/MunicipalCommunicationForm', [
            'municipalCommunication' => $municipalCommunication,
            'isEditing' => true,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MunicipalitiesCommunicationList $municipalCommunication)
    {
        if (!$this->canEditDocument($municipalCommunication)) {
            return redirect()->back()->with('error', 'You do not have permission to edit this document.');
        }

        $oldValues = $municipalCommunication->getAttributes();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subject' => 'nullable|string|max:255',
            'year' => 'nullable|integer|min:1900|max:' . (date('Y') + 10),
            'date_received' => 'nullable|date',
            'files' => 'nullable|array',
            'files.*' => 'file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png|max:10240', // 10MB max
            'extracted_text' => 'nullable|string',
            'remove_existing_file' => 'nullable|boolean',
        ]);

        $validated['last_modified_by'] = auth()->id();
        $validated['last_modified_at'] = now();

        // Debug: Log the request data
        \Log::info('Municipal communication update request', [
            'remove_existing_file_raw' => $request->input('remove_existing_file'),
            'remove_existing_file_boolean' => $request->boolean('remove_existing_file'),
            'file_url' => $municipalCommunication->file_url,
        ]);

        // Handle existing file removal - check both string and boolean representations
        $removeFile = $request->boolean('remove_existing_file') || $request->input('remove_existing_file') === 'true' || $request->input('remove_existing_file') === '1';
        if ($removeFile && $municipalCommunication->file_url) {
            \Log::info('Removing file:', ['path' => $municipalCommunication->file_url]);
            Storage::disk('private')->delete($municipalCommunication->file_url);
            $validated['file_name'] = null;
            $validated['file_url'] = null;
            $validated['file_size'] = null;
            $validated['file_type'] = null;
            $validated['date_uploaded'] = null;
            $validated['extracted_text'] = null;
        }

        // Handle file upload (take first file since schema only supports one)
        if ($request->hasFile('files') && is_array($request->file('files')) && count($request->file('files')) > 0) {
            // Delete old file if exists
            if ($municipalCommunication->file_url) {
                Storage::disk('private')->delete($municipalCommunication->file_url);
            }

            $file = $request->file('files')[0]; // Take the first file

            // Validate file attributes
            $fileName = $file->getClientOriginalName();
            $fileSize = $file->getSize();
            $fileType = $file->getMimeType();

            // Ensure file attributes are valid
            if (empty($fileName) || $fileSize <= 0 || empty($fileType)) {
                return back()->withErrors(['file' => 'Invalid file uploaded.']);
            }

            $fileName = time() . '_' . $fileName;
            $filePath = $file->storeAs('municipal-communications', $fileName, 'private');

            // Validate that file was stored successfully
            if (!$filePath) {
                return back()->withErrors(['file' => 'Failed to store file.']);
            }

            $validated['file_name'] = $file->getClientOriginalName();
            $validated['file_url'] = $filePath;
            $validated['file_size'] = $fileSize;
            $validated['file_type'] = $fileType;
            $validated['date_uploaded'] = now();
        }

        $municipalCommunication->update($validated);

        // Log the update
        LoggingService::logUpdate($municipalCommunication, $oldValues, "Updated municipal communication: {$municipalCommunication->title}");

        return back()->with('success', 'Municipal communication updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MunicipalitiesCommunicationList $municipalCommunication)
    {
        if (!$this->canDeleteDocument($municipalCommunication)) {
            return redirect()->back()->with('error', 'You do not have permission to delete this document.');
        }

        // Delete associated file if exists
        if ($municipalCommunication->file_url) {
            Storage::disk('private')->delete($municipalCommunication->file_url);
        }

        // Log the deletion
        LoggingService::logDelete($municipalCommunication, "Deleted municipal communication: {$municipalCommunication->title}");

        $municipalCommunication->delete();

        return redirect()->route('municipal-communications.index')
            ->with('success', 'Municipal communication deleted successfully.');
    }

    /**
     * Download the file associated with the municipal communication.
     */
    public function download(MunicipalitiesCommunicationList $municipalCommunication)
    {
        if (!$municipalCommunication->file_url) {
            return redirect()->back()->with('error', 'No file available for download.');
        }

        // Log the download
        LoggingService::logView($municipalCommunication, "Downloaded municipal communication file: {$municipalCommunication->title}");

        $filePath = Storage::disk('private')->path($municipalCommunication->file_url);
        return response()->download($filePath, $municipalCommunication->file_name);
    }
}
