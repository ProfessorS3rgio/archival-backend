<?php

// Isolated API contract check: SQLite in memory and a fake private disk.
require __DIR__.'/../../vendor/autoload.php';
$app = require __DIR__.'/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
config([
    'database.default' => 'sqlite',
    'database.connections.sqlite.database' => ':memory:',
    'database.connections.sqlite.url' => null,
    'session.driver' => 'array',
    'cache.default' => 'array',
    'logging.default' => 'null',
    'filesystems.default' => 'private',
]);

use App\Models\ApiClient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

function assertApi(bool $condition, string $message): void
{
    if (! $condition) {
        throw new RuntimeException($message);
    }
    echo "PASS: {$message}\n";
}

foreach (glob(__DIR__.'/../../database/migrations/*create*.php') as $file) {
    (require $file)->up();
}

$user = User::create(['name' => 'Creator', 'email' => 'creator@example.test', 'username' => 'creator', 'role' => 'admin', 'password' => 'Test!Password123']);
Artisan::call('archival:issue-api-token', ['email' => 'sp@example.test', '--name' => 'SP integration']);
$client = ApiClient::where('email', 'sp@example.test')->firstOrFail();
$issued = $client->tokens()->firstOrFail();
assertApi($issued->abilities === ['documents:read'] && $issued->expires_at->isFuture(), 'Issuer creates a limited expiring API client token');
// The command prints a secret once. Do not print its output in this test.
$token = $client->createToken('integration-test', ['documents:read'], now()->addDays(90))->plainTextToken;
$wrongToken = $client->createToken('wrong-ability', ['other:read'])->plainTextToken;
$expiredToken = $client->createToken('expired', ['documents:read'], now()->subMinute())->plainTextToken;
Storage::fake('private');
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

function apiGet(string $path, ?string $token = null): Symfony\Component\HttpFoundation\Response
{
    global $kernel;
    Auth::forgetGuards();
    Auth::shouldUse('web');
    $request = Request::create($path, 'GET', [], [], [], [
        'HTTP_ACCEPT' => 'application/json',
        'HTTP_AUTHORIZATION' => $token ? 'Bearer '.$token : '',
    ]);
    return $kernel->handle($request);
}

$types = [
    ['resolutions', 'Resolution', App\Models\Resolution::class, 'title'],
    ['ordinances', 'Ordinance', App\Models\Ordinance::class, 'title'],
    ['accomplishments', 'Accomplishment', App\Models\Accomplishment::class, 'title'],
    ['projects', 'Project', App\Models\Project::class, 'project_name'],
    ['moas', 'MemorandumOfAgreement', App\Models\MemorandumOfAgreement::class, 'title'],
    ['committee-reports', 'CommitteeReport', App\Models\CommitteeReport::class, 'title'],
    ['municipal-communications', 'MunicipalCommunication', App\Models\MunicipalitiesCommunicationList::class, 'title'],
    ['national-offices', 'NationalOfficeCommunication', App\Models\NationalOfficeCommunicationList::class, 'title'],
    ['provincial-offices', 'ProvincialOffice', App\Models\ProvincialOffice::class, 'title'],
];

assertApi(apiGet('/api/v1/documents/types')->getStatusCode() === 401, 'Anonymous client cannot list document types');
assertApi(apiGet('/api/v1/documents/types', $wrongToken)->getStatusCode() === 403, 'Token without documents:read is rejected');
assertApi(apiGet('/api/v1/documents/types', $expiredToken)->getStatusCode() === 401, 'Expired integration token is rejected');
$available = json_decode(apiGet('/api/v1/documents/types', $token)->getContent(), true);
assertApi(! isset($available['data']['draft-resolutions']) && ! isset($available['data']['draft-ordinances']), 'Integration client type list omits drafts');

foreach ($types as [$slug, $singular, $class, $titleColumn]) {
    $model = new $class;
    $table = $model->getTable();
    $ownerColumn = $class === App\Models\Accomplishment::class ? 'uploaded_by' : 'added_by';
    $visibleId = DB::table($table)->insertGetId([$ownerColumn => $user->id, $titleColumn => 'public-'.$slug, 'is_visible' => true, 'extracted_text' => 'OCR-secret-'.$slug]);
    $hiddenId = DB::table($table)->insertGetId([$ownerColumn => $user->id, $titleColumn => 'private-'.$slug, 'is_visible' => false, 'extracted_text' => 'hidden-OCR-'.$slug]);
    $fileIds = [];
    foreach ([$visibleId, $hiddenId] as $id) {
        if (Illuminate\Support\Facades\Schema::hasColumn($table, 'file_url')) {
            DB::table($table)->where($model->getKeyName(), $id)->update(['file_url' => $slug.'/'.$id.'.pdf', 'file_name' => 'test.pdf']);
            Storage::disk('private')->put($slug.'/'.$id.'.pdf', '%PDF-1.4 test');
        } else {
            $fileIds[$id] = $model->newQueryWithoutScopes()->findOrFail($id)->files()->create(['file_url' => $slug.'/'.$id.'.pdf', 'file_name' => 'test.pdf', 'uploaded_by' => $user->id])->getKey();
            Storage::disk('private')->put($slug.'/'.$id.'.pdf', '%PDF-1.4 test');
        }
    }

    foreach (["/api/v1/documents/{$slug}", "/api/v1/Documents/{$singular}"] as $path) {
        $response = apiGet($path, $token);
        assertApi($response->getStatusCode() === 200 && str_contains($response->getContent(), 'public-'.$slug) && ! str_contains($response->getContent(), 'private-'.$slug) && ! str_contains($response->getContent(), 'OCR-secret-'), "{$slug} lists visible records without extracted text");
    }
    $detail = apiGet("/api/v1/Documents/{$singular}/{$visibleId}", $token);
    assertApi($detail->getStatusCode() === 200 && ! str_contains($detail->getContent(), 'OCR-secret-'), "{$slug} visible detail omits extracted text");
    assertApi(apiGet("/api/v1/Documents/{$singular}/{$hiddenId}", $token)->getStatusCode() === 404, "{$slug} hidden detail is blocked");
    $visible = json_decode(apiGet("/api/v1/Documents/{$singular}/{$visibleId}", $token)->getContent(), true);
    $visibleUrl = $visible['data']['files'][0]['download_url'] ?? null;
    assertApi((bool) $visibleUrl && apiGet(parse_url($visibleUrl, PHP_URL_PATH), $token)->getStatusCode() === 200, "{$slug} visible file downloads");
    $hiddenPath = "/api/v1/Documents/{$singular}/{$hiddenId}";
    $hiddenPath .= isset($fileIds[$hiddenId]) ? "/files/{$fileIds[$hiddenId]}/download" : '/download';
    assertApi(apiGet($hiddenPath, $token)->getStatusCode() === 404, "{$slug} hidden file is blocked");
}

assertApi(apiGet('/api/v1/Documents/DraftResolution', $token)->getStatusCode() === 404, 'Integration client cannot access draft resolutions');
assertApi(apiGet('/api/v1/documents/draft-ordinances', $token)->getStatusCode() === 404, 'Integration client cannot access draft ordinances');
assertApi(apiGet('/api/v1/Documents/Resolution?per_page=101', $token)->getStatusCode() === 422, 'Oversized pages are rejected');
assertApi(apiGet('/api/v1/Documents/Resolution?per_page=0', $token)->getStatusCode() === 422, 'Invalid page size is rejected');
assertApi(apiGet('/api/v1/Documents/Resolution?per_page=1', $token)->getStatusCode() === 200, 'Valid page size is accepted');
