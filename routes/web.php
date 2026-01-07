<?php

use App\Http\Controllers\OrdinanceController;
use App\Http\Controllers\ResolutionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\DraftOrdinanceController;
use App\Http\Controllers\DraftResolutionController;
use App\Http\Controllers\SangguniangPanlalawiganController;
use App\Http\Controllers\MunicipalitiesCommunicationListController;
use App\Http\Controllers\NationalOfficeCommunicationListController;
use App\Http\Controllers\ProvincialOfficeController;
use App\Http\Controllers\AccomplishmentController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\MemorandumOfAgreementController;
use App\Http\Controllers\CommitteeReportController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

Route::get('/', function () {
    return Auth::check() ? redirect('/dashboard') : redirect('/login');
});

Route::get('/login', function () {
    return Inertia::render('Auth/Login', [
        'errors' => session('errors') ? session('errors')->getBag('default')->getMessages() : []
    ]);
})->name('login');

Route::post('/login', [DashboardController::class, 'login'])->name('login.post');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Dashboard view routes (no edit button, back to home)
    Route::get('/dashboard/ordinances/{id}', [OrdinanceController::class, 'dashboardView'])->name('dashboard.ordinances.view');
    Route::get('/dashboard/resolutions/{id}', [ResolutionController::class, 'dashboardView'])->name('dashboard.resolutions.view');
    Route::get('/dashboard/draft-ordinances/{id}', [DraftOrdinanceController::class, 'dashboardView'])->name('dashboard.draft-ordinances.view');
    Route::get('/dashboard/draft-resolutions/{id}', [DraftResolutionController::class, 'dashboardView'])->name('dashboard.draft-resolutions.view');
    
    Route::get('/debug-user', function () {
        return response()->json([
            'user' => auth()->user(),
            'user_id' => auth()->id(),
            'all_users' => \App\Models\User::all()->toArray()
        ]);
    });
    Route::get('/ordinances/create', function () {
        return Inertia::render('Forms/OrdinanceForm');
    })->name('ordinances.create');


    Route::get('/resolutions/create', function () {
        return Inertia::render('Forms/ResolutionForm');
    })->name('resolutions.create');


    Route::get('/municipal-ordinances/create', function () {
        return Inertia::render('Forms/OrdinanceForm');
    })->name('municipal-ordinances.create');


    Route::get('/municipal-resolutions/create', function () {
        return Inertia::render('Forms/ResolutionForm');
    })->name('municipal-resolutions.create');

    Route::get('/ordinances/provinces', [OrdinanceController::class, 'provincialIndex'])->name('ordinances.provincial');
    Route::resource('ordinances', OrdinanceController::class);
    Route::get('/resolutions/provinces', [ResolutionController::class, 'provincialIndex'])->name('resolutions.provincial');
    Route::resource('resolutions', ResolutionController::class);
    Route::get('/municipal-ordinances', [OrdinanceController::class, 'municipalIndex'])->name('municipal-ordinances.index');
    Route::get('/municipal-resolutions', [ResolutionController::class, 'municipalIndex'])->name('municipal-resolutions.index');
    Route::resource('draft-ordinances', DraftOrdinanceController::class);
    Route::resource('draft-resolutions', DraftResolutionController::class);
    Route::resource('municipal-communications', MunicipalitiesCommunicationListController::class);
    Route::get('municipal-communications/{municipalCommunication}/download', [MunicipalitiesCommunicationListController::class, 'download'])->name('municipal-communications.download');
    Route::resource('national-office-communications', NationalOfficeCommunicationListController::class);
    Route::resource('provincial-offices', ProvincialOfficeController::class);
    Route::resource('accomplishments', AccomplishmentController::class);
    Route::get('accomplishments/{accomplishment}/download', [AccomplishmentController::class, 'download'])->name('accomplishments.download');
    Route::resource('projects', ProjectController::class);
    Route::resource('memorandum-of-agreements', MemorandumOfAgreementController::class);
    Route::get('memorandum-of-agreements/{memorandumOfAgreement}/download', [MemorandumOfAgreementController::class, 'download'])->name('memorandum-of-agreements.download');
    Route::resource('committee-reports', CommitteeReportController::class);

    // Sangguniang Panlalawigan routes
    Route::get('/sp-{termSlug}/officials', [SangguniangPanlalawiganController::class, 'officials'])
        ->name('sp.officials');
    Route::get('/sp-{termSlug}/add-officials', [SangguniangPanlalawiganController::class, 'create'])
        ->name('sp.add-officials');
    Route::post('/sp-{termSlug}/officials', [SangguniangPanlalawiganController::class, 'store'])
        ->name('sp.store');
    Route::match(['put', 'post'], '/sp-{termSlug}/officials/{memberId}', [SangguniangPanlalawiganController::class, 'update'])
        ->name('sp.update');
    Route::delete('/sp-{termSlug}/officials/{memberId}', [SangguniangPanlalawiganController::class, 'destroy'])
        ->name('sp.destroy');
    Route::post('/logout', [DashboardController::class, 'logout'])->name('logout');
    
    // Settings routes
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::patch('/settings/auto-fill', [SettingsController::class, 'updateAutoFill'])->name('settings.update-auto-fill');
    
    // Change Password route (accessible to all authenticated users)
    Route::get('/settings/change-password', [App\Http\Controllers\AccountController::class, 'changePassword'])->name('settings.change-password');
    Route::post('/settings/change-password', [App\Http\Controllers\AccountController::class, 'updatePassword'])->name('settings.update-password');
    
    // Account Management routes (admin only)
    Route::middleware([\App\Http\Middleware\AdminMiddleware::class])->group(function() {
        Route::get('/settings/manage-accounts', [App\Http\Controllers\AccountController::class, 'index'])->name('settings.manage-accounts');
        Route::get('/settings/add-accounts', [App\Http\Controllers\AccountController::class, 'create'])->name('settings.add-accounts');
        Route::post('/settings/add-accounts', [App\Http\Controllers\AccountController::class, 'store'])->name('settings.add-accounts.store');
        Route::put('/settings/manage-accounts/{id}', [App\Http\Controllers\AccountController::class, 'update'])->name('settings.manage-accounts.update');
        Route::delete('/settings/manage-accounts/{id}', [App\Http\Controllers\AccountController::class, 'destroy'])->name('settings.manage-accounts.destroy');
    });

    // System Logs routes (admin only)
    Route::middleware([\App\Http\Middleware\AdminMiddleware::class])->group(function() {
        Route::get('/system-logs', [App\Http\Controllers\SystemLogController::class, 'index'])->name('system-logs.index');
        Route::get('/system-logs/{log}', [App\Http\Controllers\SystemLogController::class, 'show'])->name('system-logs.show');
        Route::post('/system-logs/{log}/rollback', [App\Http\Controllers\SystemLogController::class, 'rollback'])->name('system-logs.rollback');
    });

    // SP Terms Management routes (admin only)
    Route::middleware([\App\Http\Middleware\AdminMiddleware::class])->group(function() {
        Route::get('/sp-terms', [App\Http\Controllers\SpTermController::class, 'index'])->name('sp-terms.index');
        Route::post('/sp-terms', [App\Http\Controllers\SpTermController::class, 'store'])->name('sp-terms.store');
        Route::put('/sp-terms/{termId}', [App\Http\Controllers\SpTermController::class, 'update'])->name('sp-terms.update');
        Route::delete('/sp-terms/{termId}', [App\Http\Controllers\SpTermController::class, 'destroy'])->name('sp-terms.destroy');
    });

    // Serve files securely
    Route::get('/serve-file/{filename}', function ($filename) {
        $path = storage_path('app/private/' . $filename);

        if (!file_exists($path)) {
            return Inertia::render('Errors/404');
        }

        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $mime = mime_content_type($path);

        // Override mime for common types if detection fails
        if ($extension === 'pdf') {
            $mime = 'application/pdf';
        } elseif ($extension === 'docx') {
            $mime = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
        } elseif ($extension === 'doc') {
            $mime = 'application/msword';
        }

        $headers = ['Content-Type' => $mime];

        // For PDFs, use inline disposition to display in browser
        if ($mime === 'application/pdf') {
            $headers['Content-Disposition'] = 'inline';
        }

        return response()->file($path, $headers);
    })->where('filename', '.*')->name('serve-file');

    // Download files with logging
    Route::get('/download/{type}/{id}/{filename}', function ($type, $id, $filename) {
        // URL decode the filename parameter
        $filename = urldecode($filename);
        
        // Map type to model
        $modelMap = [
            'ordinance' => \App\Models\OrdinanceFile::class,
            'resolution' => \App\Models\ResolutionFile::class,
            'draft-ordinance' => \App\Models\DraftOrdinanceFile::class,
            'draft-resolution' => \App\Models\DraftResolutionFile::class,
            'accomplishment' => \App\Models\Accomplishment::class,
            'project' => \App\Models\Project::class,
            'memorandum-of-agreement' => \App\Models\MemorandumOfAgreement::class,
            'committee-report' => \App\Models\CommitteeReport::class,
        ];

        if (!isset($modelMap[$type])) {
            return Inertia::render('Errors/404');
        }

        $modelClass = $modelMap[$type];
        $file = $modelClass::find($id);

        if (!$file) {
            return Inertia::render('Errors/404');
        }

        // Log the download
        \App\Services\LoggingService::logDownload(
            $file,
            "Downloaded file: {$file->file_name}"
        );

        $path = storage_path('app/private/' . $filename);

        if (!file_exists($path)) {
            return Inertia::render('Errors/404');
        }

        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $mime = mime_content_type($path);

        // Override mime for common types if detection fails
        if ($extension === 'pdf') {
            $mime = 'application/pdf';
        } elseif ($extension === 'docx') {
            $mime = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
        } elseif ($extension === 'doc') {
            $mime = 'application/msword';
        }

        $headers = [
            'Content-Type' => $mime,
            'Content-Disposition' => 'attachment; filename="' . $file->file_name . '"'
        ];

        return response()->file($path, $headers);
    })->where('filename', '.*')->name('download');
});

// Catch-all route for 404 errors
Route::fallback(function () {
    return Inertia::render('Errors/404');
});
