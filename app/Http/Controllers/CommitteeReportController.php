<?php

namespace App\Http\Controllers;

use App\Models\CommitteeReport;
use App\Models\Committee;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CommitteeReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = CommitteeReport::with(['addedBy']);

        // Search functionality
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('committee_report_no', 'like', "%{$searchTerm}%")
                  ->orWhere('subject', 'like', "%{$searchTerm}%")
                  ->orWhere('extracted_text', 'like', "%{$searchTerm}%");
            });
        }

        $reports = $query->orderBy('created_at', 'desc')->get();

        // Get unique subjects and years for filters
        $subjects = CommitteeReport::whereNotNull('subject')
            ->distinct()
            ->pluck('subject')
            ->sort()
            ->values();

        $years = CommitteeReport::whereNotNull('year')
            ->distinct()
            ->pluck('year')
            ->sort()
            ->reverse()
            ->values();

        return Inertia::render('Documents/CommitteeReport/CommitteeReports', [
            'reports' => $reports,
            'stats' => [
                'total' => CommitteeReport::count(),
                'thisYear' => CommitteeReport::where('year', now()->year)->count(),
                'thisMonth' => CommitteeReport::whereMonth('created_at', now()->month)->count(),
            ],
            'filters' => [
                'subjects' => $subjects,
                'years' => $years,
            ],
            'searchQuery' => $request->search ?? '',
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Forms/CommitteeReportForm', [
            'subjectOptions' => CommitteeReport::whereNotNull('subject')
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
            'committee_report_no' => 'nullable|string',
            'subject' => 'required|string',
            'title' => 'required|string',
            'year' => 'nullable|integer|min:1900|max:' . (now()->year + 1),
            'files' => 'nullable|array',
            'files.*' => 'file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png|max:10240', // 10MB max
            'extracted_text' => 'nullable|string',
        ]);

        $validated['added_by'] = auth()->id();

        // Handle file upload (take first file since schema only supports one)
        if ($request->hasFile('files') && is_array($request->file('files')) && count($request->file('files')) > 0) {
            $file = $request->file('files')[0]; // Take the first file

            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('committee-reports', $fileName, 'private');

            if (!$filePath) {
                return back()->withErrors(['file' => 'Failed to store file.']);
            }

            $validated['file_name'] = $file->getClientOriginalName();
            $validated['file_url'] = $filePath;
            $validated['file_size'] = $file->getSize();
            $validated['file_type'] = $file->getMimeType();
        }

        CommitteeReport::create($validated);

        return redirect()->route('committee-reports.index')->with('success', 'Committee report created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $report = CommitteeReport::with(['addedBy'])->findOrFail($id);

        // Provide a signed public URL for Office Online previewing
        $report->public_preview_url = URL::temporarySignedRoute(
            'public-serve-file',
            now()->addMinutes(60),
            ['filename' => $report->file_url]
        );

        return Inertia::render('Show/CommitteeReportShow', [
            'report' => $report,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $report = CommitteeReport::findOrFail($id);

        return Inertia::render('Forms/CommitteeReportForm', [
            'report' => $report,
            'subjectOptions' => CommitteeReport::whereNotNull('subject')
                ->distinct()
                ->pluck('subject')
                ->sort()
                ->values(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $report = CommitteeReport::findOrFail($id);

        if (!$this->canEditDocument($report)) {
            return redirect()->back()->with('error', 'You do not have permission to edit this document.');
        }

        $validated = $request->validate([
            'committee_report_no' => 'nullable|string',
            'subject' => 'required|string',
            'title' => 'required|string',
            'year' => 'nullable|integer|min:1900|max:' . (now()->year + 1),
            'files' => 'nullable|array',
            'files.*' => 'file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png|max:10240', // 10MB max
            'extracted_text' => 'nullable|string',
        ]);

        // Handle file upload (take first file since schema only supports one)
        if ($request->hasFile('files') && is_array($request->file('files')) && count($request->file('files')) > 0) {
            $file = $request->file('files')[0]; // Take the first file

            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('committee-reports', $fileName, 'private');

            if (!$filePath) {
                return back()->withErrors(['file' => 'Failed to store file.']);
            }

            $validated['file_name'] = $file->getClientOriginalName();
            $validated['file_url'] = $filePath;
            $validated['file_size'] = $file->getSize();
            $validated['file_type'] = $file->getMimeType();
        }

        $report->update($validated);

        return redirect()->route('committee-reports.index')->with('success', 'Committee report updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $report = CommitteeReport::findOrFail($id);

        if (!$this->canDeleteDocument($report)) {
            return redirect()->back()->with('error', 'You do not have permission to delete this document.');
        }

        $report->delete();

        return redirect()->route('committee-reports.index')->with('success', 'Committee report deleted successfully.');
    }
}