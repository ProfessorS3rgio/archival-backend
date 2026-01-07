<?php

namespace App\Http\Controllers;

use App\Models\SangguniangPanlalawiganTerm;
use App\Services\LoggingService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SpTermController extends Controller
{
    /**
     * Display a listing of SP terms
     */
    public function index()
    {
        $terms = SangguniangPanlalawiganTerm::orderBy('start_year')->get();

        return Inertia::render('Settings/SpTerms', [
            'terms' => $terms,
        ]);
    }

    /**
     * Store a newly created SP term
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'term_no' => 'required|string|max:10|unique:sangguniang_panlalawigan_terms,term_no',
            'start_year' => 'required|date',
            'end_year' => 'required|date|after:start_year',
        ]);

        $term = SangguniangPanlalawiganTerm::create($validated);

        // Log the creation
        LoggingService::logCreate($term, "Created SP Term: {$term->term_no}");

        return redirect()->route('sp-terms.index')
            ->with('success', 'SP Term added successfully');
    }

    /**
     * Update the specified SP term
     */
    public function update(Request $request, $termId)
    {
        $term = SangguniangPanlalawiganTerm::findOrFail($termId);

        // Store old values for logging
        $oldValues = $term->getOriginal();

        $validated = $request->validate([
            'term_no' => 'required|string|max:10|unique:sangguniang_panlalawigan_terms,term_no,' . $termId . ',term_id',
            'start_year' => 'required|date',
            'end_year' => 'required|date|after:start_year',
        ]);

        $term->update($validated);

        // Log the update
        LoggingService::logUpdate($term, $oldValues, "Updated SP Term: {$term->term_no}");

        return redirect()->route('sp-terms.index')
            ->with('success', 'SP Term updated successfully');
    }

    /**
     * Remove the specified SP term
     */
    public function destroy($termId)
    {
        $term = SangguniangPanlalawiganTerm::findOrFail($termId);
        
        // Check if term has members
        if ($term->members()->count() > 0) {
            return back()->with('error', 'Cannot delete term with existing members');
        }

        // Store term number before deletion for logging
        $termNo = $term->term_no;

        $term->delete();

        // Log the deletion
        LoggingService::logDelete($term, "Deleted SP Term: {$termNo}");

        return redirect()->route('sp-terms.index')
            ->with('success', 'SP Term deleted successfully');
    }
}
