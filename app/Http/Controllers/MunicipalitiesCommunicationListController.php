<?php

namespace App\Http\Controllers;

use App\Models\MunicipalitiesCommunicationList;
use App\Services\DocumentFilterService;
use App\Services\LoggingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\URL;
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
        return Inertia::render('Forms/MunicipalCommunicationForm', [
            'autoFillEnabled' => \App\Models\Setting::getAutoFillEnabled(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $this->validateInput($request);
        $this->saveCommunication($request, $validated);

        return redirect()->route('municipal-communications.index')
            ->with('success', 'Municipal communication created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MunicipalitiesCommunicationList $municipalCommunication)
    {
        // Provide a signed public URL for Office Online previewing
        $municipalCommunication->public_preview_url = $municipalCommunication->file_url ? URL::temporarySignedRoute(
            'public-serve-file',
            now()->addMinutes(60),
            ['filename' => $municipalCommunication->file_url]
        ) : null;

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
        // Provide a signed public URL for Office Online previewing
        $municipalCommunication->public_preview_url = $municipalCommunication->file_url ? URL::temporarySignedRoute(
            'public-serve-file',
            now()->addMinutes(60),
            ['filename' => $municipalCommunication->file_url]
        ) : null;

        return Inertia::render('Forms/MunicipalCommunicationForm', [
            'municipalCommunication' => $municipalCommunication,
            'autoFillEnabled' => \App\Models\Setting::getAutoFillEnabled(),
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

        $validated = $this->validateInput($request);
        $this->saveCommunication($request, $validated, $municipalCommunication);

        return redirect()->route('municipal-communications.index')
            ->with('success', 'Municipal communication updated successfully.');
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

        return Storage::disk('private')->download($municipalCommunication->file_url, $municipalCommunication->file_name);
    }

    private function validateInput(Request $request): array
    {
        return $request->validate([
            'title' => 'required|string|max:255',
            'subject' => 'nullable|string|max:255',
            'year' => 'nullable|integer|min:1900|max:'.(date('Y') + 10),
            'date_received' => 'nullable|date|after_or_equal:1000-01-01|before_or_equal:9999-12-31',
            'files' => 'nullable|array|max:1',
            'files.*' => 'bail|required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png|max:10240',
            'extracted_text' => 'nullable|string',
            'remove_existing_file' => 'nullable|boolean',
        ], [
            'files.max' => 'Choose one attachment per municipal communication.',
            'files.*.max' => 'The attachment must be no larger than 10 MB.',
            'files.*.uploaded' => 'The file did not upload successfully. Please choose it again and retry.',
        ]);
    }

    private function saveCommunication(Request $request, array $validated, ?MunicipalitiesCommunicationList $communication = null): void
    {
        unset($validated['files'], $validated['remove_existing_file']);
        $file = array_values($request->file('files', []))[0] ?? null;
        $path = null;
        $oldPath = $communication?->file_url;
        if ($file) {
            try {
                $path = $file->store('municipal-communications', 'private');
                if (! $path) {
                    throw new \RuntimeException('The attachment could not be stored.');
                }
            } catch (\Throwable $error) {
                report($error);
                throw ValidationException::withMessages(['files.0' => 'The attachment could not be saved. Please try again. Your existing attachment has been kept.']);
            }
            $validated = [...$validated,
                'file_name' => mb_substr($file->getClientOriginalName(), 0, 255),
                'file_url' => $path,
                'file_size' => $file->getSize(),
                'file_type' => $file->getMimeType(),
                'date_uploaded' => now(),
                'extracted_text' => $validated['extracted_text'] ?? '',
            ];
        } elseif ($request->boolean('remove_existing_file')) {
            foreach (['file_name', 'file_url', 'file_size', 'file_type', 'date_uploaded', 'extracted_text'] as $field) {
                $validated[$field] = null;
            }
        }

        try {
            DB::transaction(function () use ($communication, $validated, $oldPath, $path, $request) {
                if ($communication) {
                    $oldValues = $communication->getAttributes();
                    $communication->update([...$validated, 'last_modified_by' => auth()->id(), 'last_modified_at' => now()]);
                    LoggingService::logUpdate($communication, $oldValues, "Updated municipal communication: {$communication->title}");
                } else {
                    $communication = MunicipalitiesCommunicationList::create([...$validated, 'added_by' => auth()->id()]);
                    LoggingService::logCreate($communication, "Created municipal communication: {$communication->title}");
                }
                if ($oldPath && ($path || $request->boolean('remove_existing_file'))) {
                    DB::afterCommit(fn () => $this->deleteUnusedFile($oldPath));
                }
            });
        } catch (\Throwable $error) {
            if ($path) {
                $this->deleteUnusedFile($path);
            }
            throw $error;
        }
    }

    private function deleteUnusedFile(string $path): void
    {
        try {
            if (! Storage::disk('private')->delete($path)) {
                throw new \RuntimeException('Could not remove an unused municipal communication attachment.');
            }
        } catch (\Throwable $error) {
            report($error);
        }
    }

}
