<?php

namespace App\Http\Controllers;

use App\Models\NationalOfficeCommunicationList;
use App\Models\Ordinance;
use App\Models\Resolution;
use App\Models\SystemLog;
use App\Models\DraftOrdinance;
use App\Models\DraftResolution;
use App\Models\Accomplishment;
use App\Models\Project;
use App\Models\MemorandumOfAgreement;
use App\Models\MunicipalityCommunication;
use App\Models\ProvincialOffice;
use App\Models\CommitteeReport;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $ordinances = Ordinance::all();
        $resolutions = Resolution::all();
        $draftOrdinances = DraftOrdinance::all();
        $draftResolutions = DraftResolution::all();
        $accomplishments = Accomplishment::all();
        $projects = Project::all();
        $moas = MemorandumOfAgreement::all();
        $municipalCommunications = MunicipalityCommunication::all();
        $nationalOfficeCommunications = NationalOfficeCommunicationList::all();
        $provincialOffices = ProvincialOffice::all();
        $committeeReports = CommitteeReport::all();
        $systemLogs = SystemLog::with('user')->latest('created_at')->take(5)->get();
        
        // Count total staff (admin and staff roles)
        $totalStaff = User::whereIn('role', ['admin', 'staff'])->count();
        
        // Ensure user relationships are loaded
        $systemLogs->load('user');

        // Calculate total files and file size
        $totalFiles = DB::table('resolution_files')->whereNotNull('file_size')->count() +
                      DB::table('ordinance_files')->whereNotNull('file_size')->count() +
                      DB::table('resolution_draft_files')->whereNotNull('file_size')->count() +
                      DB::table('ordinance_draft_files')->whereNotNull('file_size')->count() +
                      DB::table('accomplish_lists')->whereNotNull('file_size')->count() +
                      DB::table('project_lists')->whereNotNull('file_size')->count() +
                      DB::table('moa_lists')->whereNotNull('file_size')->count() +
                      DB::table('municipalities_communication_lists')->whereNotNull('file_size')->count() +
                      DB::table('national_offices_list')->whereNotNull('file_size')->count() +
                      DB::table('provincial_offices_list')->whereNotNull('file_size')->count() +
                      DB::table('committee_reports')->whereNotNull('file_size')->count();

        $totalFileSize = DB::table('resolution_files')->whereNotNull('file_size')->sum('file_size') +
                         DB::table('ordinance_files')->whereNotNull('file_size')->sum('file_size') +
                         DB::table('resolution_draft_files')->whereNotNull('file_size')->sum('file_size') +
                         DB::table('ordinance_draft_files')->whereNotNull('file_size')->sum('file_size') +
                         DB::table('accomplish_lists')->whereNotNull('file_size')->sum('file_size') +
                         DB::table('project_lists')->whereNotNull('file_size')->sum('file_size') +
                         DB::table('moa_lists')->whereNotNull('file_size')->sum('file_size') +
                         DB::table('municipalities_communication_lists')->whereNotNull('file_size')->sum('file_size') +
                         DB::table('national_offices_list')->whereNotNull('file_size')->sum('file_size') +
                         DB::table('provincial_offices_list')->whereNotNull('file_size')->sum('file_size') +
                         DB::table('committee_reports')->whereNotNull('file_size')->sum('file_size');

        return Inertia::render('Dashboard', [
            'stats' => [
                'ordinances' => $ordinances->count(),
                'resolutions' => $resolutions->count(),
                'draftOrdinances' => $draftOrdinances->count(),
                'draftResolutions' => $draftResolutions->count(),
                'accomplishments' => $accomplishments->count(),
                'projects' => $projects->count(),
                'moas' => $moas->count(),
                'municipalCommunications' => $municipalCommunications->count(),
                'nationalOfficeCommunications' => $nationalOfficeCommunications->count(),
                'provincialOffices' => $provincialOffices->count(),
                'committeeReports' => $committeeReports->count(),
                'totalFiles' => $totalFiles,
                'totalFileSize' => $totalFileSize,
                'totalStaff' => $totalStaff,
            ],
            'allDocuments' => [
                'ordinances' => $ordinances->map(function ($item) {
                    return [
                        'id' => $item->ordinance_id, 
                        'title' => $item->title, 
                        'type' => 'ordinance', 
                        'author' => $item->author,
                        'date_approved' => $item->date_approved,
                        'created_at' => $item->created_at,
                        'extracted_text' => $item->extracted_text
                    ];
                }),
                'resolutions' => $resolutions->map(function ($item) {
                    return [
                        'id' => $item->resolution_id, 
                        'title' => $item->title, 
                        'type' => 'resolution', 
                        'author' => $item->author,
                        'date_approved' => $item->date_approved,
                        'created_at' => $item->created_at,
                        'extracted_text' => $item->extracted_text
                    ];
                }),
                'draftOrdinances' => $draftOrdinances->map(function ($item) {
                    return [
                        'id' => $item->ordinance_draft_id, 
                        'title' => $item->title, 
                        'type' => 'draft-ordinance', 
                        'author' => $item->author,
                        'created_at' => $item->created_at,
                        'extracted_text' => $item->extracted_text
                    ];
                }),
                'draftResolutions' => $draftResolutions->map(function ($item) {
                    return [
                        'id' => $item->resolution_draft_id, 
                        'title' => $item->title, 
                        'type' => 'draft-resolution', 
                        'author' => $item->author,
                        'created_at' => $item->created_at,
                        'extracted_text' => $item->extracted_text
                    ];
                }),
                'accomplishments' => $accomplishments->map(function ($item) {
                    return ['id' => $item->id, 'title' => $item->title, 'type' => 'accomplishment', 'created_at' => $item->created_at, 'extracted_text' => $item->extracted_text ?? null];
                }),
                'projects' => $projects->map(function ($item) {
                    return ['id' => $item->project_id, 'title' => $item->project_name, 'type' => 'project', 'created_at' => $item->created_at, 'extracted_text' => $item->extracted_text ?? null];
                }),
                'moas' => $moas->map(function ($item) {
                    return ['id' => $item->moa_id, 'title' => $item->title, 'type' => 'moa', 'created_at' => $item->created_at, 'extracted_text' => $item->extracted_text ?? null];
                }),
                'municipalCommunications' => $municipalCommunications->map(function ($item) {
                    return ['id' => $item->municipalitites_communication_id, 'title' => $item->title, 'type' => 'municipal-communication', 'created_at' => $item->created_at, 'extracted_text' => $item->extracted_text ?? null];
                }),
                'nationalOfficeCommunications' => $nationalOfficeCommunications->map(function ($item) {
                    return ['id' => $item->national_offices_id, 'title' => $item->title, 'type' => 'national-office-communication', 'created_at' => $item->created_at, 'extracted_text' => $item->extracted_text ?? null];
                }),
                'provincialOffices' => $provincialOffices->map(function ($item) {
                    return ['id' => $item->provincial_offices_list_id, 'title' => $item->title, 'type' => 'provincial-office', 'created_at' => $item->created_at, 'extracted_text' => $item->extracted_text ?? null];
                }),
                'committeeReports' => $committeeReports->map(function ($item) {
                    return ['id' => $item->report_id, 'title' => $item->title, 'type' => 'committee-report', 'created_at' => $item->created_at, 'extracted_text' => $item->extracted_text ?? null];
                }),
            ],
            'recentOrdinances' => $ordinances->take(5),
            'recentResolutions' => $resolutions->take(5),
            'recentOrdinanceDrafts' => $draftOrdinances->take(5),
            'recentResolutionDrafts' => $draftResolutions->take(5),
            'recentAccomplishments' => $accomplishments->take(5),
            'recentProjects' => $projects->take(5),
            'recentMoas' => $moas->take(5),
            'recentMunicipalCommunications' => $municipalCommunications->take(5),
            'recentNationalOfficeCommunications' => $nationalOfficeCommunications->take(5),
            'recentProvincialOffices' => $provincialOffices->take(5),
            'recentCommitteeReports' => $committeeReports->take(5),
            'recentSystemLogs' => $systemLogs,
            'availableYears' => $this->getAvailableYears($ordinances, $resolutions, $draftOrdinances, $draftResolutions),
            'availableAuthors' => $this->getAvailableAuthors($ordinances, $resolutions, $draftOrdinances, $draftResolutions),
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::attempt(['username' => $request->username, 'password' => $request->password], $request->remember)) {
            $request->session()->regenerate();

            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'username' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    /**
     * Get unique years from all documents using efficient database queries
     */
    private function getAvailableYears($ordinances, $resolutions, $draftOrdinances, $draftResolutions)
    {
        // Use raw SQL UNION queries for maximum performance
        $years = DB::table('ordinance_lists')
            ->selectRaw('DISTINCT YEAR(date_approved) as year')
            ->whereNotNull('date_approved')
            ->union(
                DB::table('resolution_lists')
                    ->selectRaw('DISTINCT YEAR(date_approved) as year')
                    ->whereNotNull('date_approved')
            )
            ->union(
                DB::table('ordinance_draft')
                    ->selectRaw('DISTINCT YEAR(created_at) as year')
                    ->whereNotNull('created_at')
            )
            ->union(
                DB::table('resolution_draft')
                    ->selectRaw('DISTINCT YEAR(created_at) as year')
                    ->whereNotNull('created_at')
            )
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->filter()
            ->toArray();

        return $years;
    }

    /**
     * Get unique authors from all documents using efficient database queries
     */
    private function getAvailableAuthors($ordinances, $resolutions, $draftOrdinances, $draftResolutions)
    {
        // Use raw SQL UNION queries for maximum performance
        $authors = DB::table('ordinance_lists')
            ->select('author')
            ->whereNotNull('author')
            ->where('author', '!=', '')
            ->union(
                DB::table('resolution_lists')
                    ->select('author')
                    ->whereNotNull('author')
                    ->where('author', '!=', '')
            )
            ->union(
                DB::table('ordinance_draft')
                    ->select('author')
                    ->whereNotNull('author')
                    ->where('author', '!=', '')
            )
            ->union(
                DB::table('resolution_draft')
                    ->select('author')
                    ->whereNotNull('author')
                    ->where('author', '!=', '')
            )
            ->distinct()
            ->orderBy('author')
            ->pluck('author')
            ->filter()
            ->toArray();

        // Split semicolon-separated authors and collect unique individual names
        $individualAuthors = [];
        $uniqueAuthors = [];
        
        foreach ($authors as $authorString) {
            // Split by semicolon and trim each author
            $splitAuthors = array_map('trim', explode(';', $authorString));
            
            foreach ($splitAuthors as $individualAuthor) {
                if (!empty($individualAuthor) && !in_array($individualAuthor, $uniqueAuthors)) {
                    $uniqueAuthors[] = $individualAuthor;
                }
            }
        }

        // Sort the unique authors alphabetically
        sort($uniqueAuthors);

        return $uniqueAuthors;
    }
    }
