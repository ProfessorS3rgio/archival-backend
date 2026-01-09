<?php

use App\Http\Controllers\Api\AccomplishmentApiController;
use App\Http\Controllers\Api\CommitteeReportApiController;
use App\Http\Controllers\Api\DocumentApiController;
use App\Http\Controllers\Api\NationalOfficeCommunicationApiController;
use App\Http\Controllers\Api\ProvincialOfficeApiController;
use App\Http\Controllers\Api\DraftOrdinanceApiController;
use App\Http\Controllers\Api\DraftResolutionApiController;
use App\Http\Controllers\Api\MemorandumOfAgreementApiController;
use App\Http\Controllers\Api\MunicipalCommunicationApiController;
use App\Http\Controllers\Api\OrdinanceApiController;
use App\Http\Controllers\Api\ProjectApiController;
use App\Http\Controllers\Api\ResolutionApiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DraftOrdinanceController;
use App\Http\Controllers\DraftResolutionController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\OrdinanceController;
use App\Http\Controllers\ResolutionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::post('/convert-file', [FileController::class, 'convertFile']);
Route::post('/preview-upload', [FileController::class, 'previewUpload']);

// Public API endpoints (authentication disabled for development)
Route::prefix('v1')->group(function () {

    // Clean RESTful endpoints
    Route::prefix('Documents')->group(function () {
        Route::get('/Resolution', [ResolutionApiController::class, 'index']);
        Route::get('/Resolution/{resolution}', [ResolutionApiController::class, 'show']);
        Route::get('/Resolution/{resolution}/files/{file}/download', [ResolutionApiController::class, 'downloadFile'])
            ->name('api.resolutions.files.download');

        Route::get('/Ordinance', [OrdinanceApiController::class, 'index']);
        Route::get('/Ordinance/{ordinance}', [OrdinanceApiController::class, 'show']);
        Route::get('/Ordinance/{ordinance}/files/{file}/download', [OrdinanceApiController::class, 'downloadFile'])
            ->name('api.ordinances.files.download');

        Route::get('/MunicipalCommunication', [MunicipalCommunicationApiController::class, 'index']);
        Route::get('/MunicipalCommunication/{communication}', [MunicipalCommunicationApiController::class, 'show']);
        Route::get('/MunicipalCommunication/{communication}/download', [MunicipalCommunicationApiController::class, 'downloadFile'])
            ->name('api.municipal-communications.download');

        Route::get('/DraftResolution', [DraftResolutionApiController::class, 'index']);
        Route::get('/DraftResolution/{draftResolution}', [DraftResolutionApiController::class, 'show']);
        Route::get('/DraftResolution/{draftResolution}/files/{file}/download', [DraftResolutionApiController::class, 'downloadFile'])
            ->name('api.draft-resolutions.files.download');

        Route::get('/DraftOrdinance', [DraftOrdinanceApiController::class, 'index']);
        Route::get('/DraftOrdinance/{draftOrdinance}', [DraftOrdinanceApiController::class, 'show']);
        Route::get('/DraftOrdinance/{draftOrdinance}/files/{file}/download', [DraftOrdinanceApiController::class, 'downloadFile'])
            ->name('api.draft-ordinances.files.download');

        Route::get('/Accomplishment', [AccomplishmentApiController::class, 'index']);
        Route::get('/Accomplishment/{accomplishment}', [AccomplishmentApiController::class, 'show']);
        Route::get('/Accomplishment/{accomplishment}/download', [AccomplishmentApiController::class, 'downloadFile'])
            ->name('api.accomplishments.download');

        Route::get('/Project', [ProjectApiController::class, 'index']);
        Route::get('/Project/{project}', [ProjectApiController::class, 'show']);
        Route::get('/Project/{project}/download', [ProjectApiController::class, 'downloadFile'])
            ->name('api.projects.download');

        Route::get('/MemorandumOfAgreement', [MemorandumOfAgreementApiController::class, 'index']);
        Route::get('/MemorandumOfAgreement/{moa}', [MemorandumOfAgreementApiController::class, 'show']);
        Route::get('/MemorandumOfAgreement/{moa}/download', [MemorandumOfAgreementApiController::class, 'downloadFile'])
            ->name('api.moas.download');

        Route::get('/CommitteeReport', [CommitteeReportApiController::class, 'index']);
        Route::get('/CommitteeReport/{report}', [CommitteeReportApiController::class, 'show']);
        Route::get('/CommitteeReport/{report}/download', [CommitteeReportApiController::class, 'downloadFile'])
            ->name('api.committee-reports.download');

        Route::get('/NationalOfficeCommunication', [NationalOfficeCommunicationApiController::class, 'index']);
        Route::get('/NationalOfficeCommunication/{communication}', [NationalOfficeCommunicationApiController::class, 'show']);
        Route::get('/NationalOfficeCommunication/{communication}/download', [NationalOfficeCommunicationApiController::class, 'downloadFile'])
            ->name('api.national-office-communications.download');

        Route::get('/ProvincialOffice', [ProvincialOfficeApiController::class, 'index']);
        Route::get('/ProvincialOffice/{communication}', [ProvincialOfficeApiController::class, 'show']);
        Route::get('/ProvincialOffice/{communication}/download', [ProvincialOfficeApiController::class, 'downloadFile'])
            ->name('api.provincial-offices.download');
        Route::get('/{type}', [DocumentApiController::class, 'index']);
        Route::get('/{type}/{document}/files/{file}/download', [DocumentApiController::class, 'downloadFile'])
            ->name('api.documents.files.download');
    });

    // Legacy endpoints (keeping for backward compatibility)
    Route::get('/documents/types', [DocumentApiController::class, 'types']);
    Route::get('/documents/{type}', [DocumentApiController::class, 'index']);
    Route::get('/resolutions', [ResolutionApiController::class, 'index']);
    Route::get('/resolutions/{resolution}', [ResolutionApiController::class, 'show']);
    Route::get('/ordinances', [OrdinanceApiController::class, 'index']);
    Route::get('/ordinances/{ordinance}', [OrdinanceApiController::class, 'show']);
});

Route::middleware('web')->group(function () {

    Route::apiResource('ordinances', OrdinanceController::class);
    Route::apiResource('resolutions', ResolutionController::class);
    Route::apiResource('draft-ordinances', DraftOrdinanceController::class);
    Route::apiResource('draft-resolutions', DraftResolutionController::class);
});