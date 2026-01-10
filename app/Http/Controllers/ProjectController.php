<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Services\DocumentFilterService;
use App\Services\LoggingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Project::with(['addedBy']);

        // Full-text search
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('project_name', 'like', "%{$searchTerm}%")
                  ->orWhere('district', 'like', "%{$searchTerm}%")
                  ->orWhere('location', 'like', "%{$searchTerm}%")
                  ->orWhere('source_fund', 'like', "%{$searchTerm}%")
                  ->orWhere('source_type', 'like', "%{$searchTerm}%")
                  ->orWhere('year', 'like', "%{$searchTerm}%")
                  ->orWhere('extracted_text', 'like', "%{$searchTerm}%");
            });
        }

        $projects = $query->latest('created_at')->get();

        return Inertia::render('Documents/Projects', [
            'projects' => $projects,
            'stats' => [
                'total' => Project::count(),
                'thisMonth' => Project::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->count(),
                'thisYear' => Project::whereYear('created_at', now()->year)->count(),
                'totalAmount' => Project::sum('amount'),
            ],
            'filters' => [
                'districts' => Project::whereNotNull('district')
                    ->distinct()
                    ->pluck('district')
                    ->sort()
                    ->values(),
                'sourceFunds' => Project::whereNotNull('source_fund')
                    ->distinct()
                    ->pluck('source_fund')
                    ->sort()
                    ->values(),
                'sourceTypes' => Project::whereNotNull('source_type')
                    ->distinct()
                    ->pluck('source_type')
                    ->sort()
                    ->values(),
                'years' => Project::whereNotNull('year')
                    ->distinct()
                    ->pluck('year')
                    ->sort()
                    ->values(),
            ],
            'searchQuery' => $request->search ?? '',
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Forms/ProjectForm');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code_no' => 'nullable|integer',
            'district' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'project_name' => 'required|string|max:255',
            'year' => 'nullable|integer|min:1900|max:2100',
            'source_fund' => 'nullable|string|max:255',
            'source_type' => 'nullable|string|max:255',
            'amount' => 'nullable|numeric|min:0',
            'extracted_text' => 'nullable',
            'file' => 'nullable|file|mimes:pdf,doc,docx|max:10240', // 10MB max
        ]);

        $validated['added_by'] = auth()->id();

        // Handle extracted_text
        $validated['extracted_text'] = is_string($validated['extracted_text'] ?? null) 
            ? $validated['extracted_text'] 
            : (is_array($validated['extracted_text'] ?? null) 
                ? implode('\n\n', array_filter($validated['extracted_text'])) 
                : null);

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('projects', $fileName, 'private');

            $validated['file_name'] = $file->getClientOriginalName();
            $validated['file_url'] = $filePath;
            $validated['file_size'] = $file->getSize();
            $validated['file_type'] = $file->getMimeType();
        }

        $project = Project::create($validated);

        // Log the creation
        LoggingService::logCreate($project, "Created project: {$project->project_name}");

        return redirect()->route('projects.index')
            ->with('success', 'Project created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        // Provide a signed public URL for Office Online previewing
        $project->public_preview_url = URL::temporarySignedRoute(
            'public-serve-file',
            now()->addMinutes(60),
            ['filename' => $project->file_url]
        );

        // Log the view
        LoggingService::logView($project, "Viewed project: {$project->project_name}");

        return Inertia::render('Show/ProjectShow', [
            'project' => $project->load(['addedBy']),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        return Inertia::render('Forms/ProjectForm', [
            'project' => $project,
            'isEditing' => true,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        if (!$this->canEditDocument($project)) {
            return redirect()->back()->with('error', 'You do not have permission to edit this document.');
        }

        $oldValues = $project->getAttributes();

        $validated = $request->validate([
            'code_no' => 'nullable|integer',
            'district' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'project_name' => 'required|string|max:255',
            'year' => 'nullable|integer|min:1900|max:2100',
            'source_fund' => 'nullable|string|max:255',
            'source_type' => 'nullable|string|max:255',
            'amount' => 'nullable|numeric|min:0',
            'extracted_text' => 'nullable',
            'file' => 'nullable|file|mimes:pdf,doc,docx|max:10240', // 10MB max
            'is_visible' => 'boolean',
        ]);

        $validated['last_modified_by'] = auth()->id();
        $validated['last_modified_at'] = now();

        // Handle extracted_text
        if (isset($validated['extracted_text'])) {
            $validated['extracted_text'] = is_string($validated['extracted_text']) 
                ? $validated['extracted_text'] 
                : (is_array($validated['extracted_text']) 
                    ? implode('\n\n', array_filter($validated['extracted_text'])) 
                    : null);
        }

        // Handle file upload
        if ($request->hasFile('file')) {
            // Delete old file if exists
            if ($project->file_url) {
                Storage::disk('private')->delete($project->file_url);
            }

            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('projects', $fileName, 'private');

            $validated['file_name'] = $file->getClientOriginalName();
            $validated['file_url'] = $filePath;
            $validated['file_size'] = $file->getSize();
            $validated['file_type'] = $file->getMimeType();
        }

        $project->update($validated);

        // Log the update
        LoggingService::logUpdate($project, $oldValues, "Updated project: {$project->project_name}");

        return redirect()->route('projects.index')
            ->with('success', 'Project updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        if (!$this->canDeleteDocument($project)) {
            return redirect()->back()->with('error', 'You do not have permission to delete this document.');
        }

        // Log the deletion
        LoggingService::logDelete($project, "Deleted project: {$project->project_name}");

        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'Project deleted successfully.');
    }
}