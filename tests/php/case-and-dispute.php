<?php

// Uses only an isolated in-memory database; never migrates the configured database.
require __DIR__.'/../../vendor/autoload.php';
$app = require __DIR__.'/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$app->instance('env', 'testing');
config([
    'database.default' => 'sqlite',
    'database.connections.sqlite.database' => ':memory:',
    'database.connections.sqlite.url' => null,
    'session.driver' => 'array',
    'cache.default' => 'array',
    'logging.default' => 'null',
    'hashing.bcrypt.rounds' => 4,
]);

use App\Http\Controllers\Api\AccomplishmentApiController;
use App\Http\Controllers\Api\DocumentApiController;
use App\Http\Controllers\Api\ProjectApiController;
use App\Models\Accomplishment;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

set_exception_handler(function (Throwable $error): void {
    fwrite(STDERR, $error->getMessage()."\n".$error->getTraceAsString()."\n");
    exit(1);
});

function check(bool $condition, string $message): void
{
    if (! $condition) {
        throw new RuntimeException($message);
    }
    echo "PASS: {$message}\n";
}

foreach (['2025_10_15_100000_create_users_table_custom', '2025_10_17_100000_create_accomplish_lists_table', '2025_10_17_200000_create_project_lists_table', '2025_10_22_000000_create_system_logs_table', '2025_10_30_063921_create_sangguniang_panlalawigan_terms_table'] as $file) {
    (require __DIR__.'/../../database/migrations/'.$file.'.php')->up();
}
$migration = require __DIR__.'/../../database/migrations/2026_11_11_000000_add_parties_to_administrative_cases_and_boundary_disputes.php';
$migration->up();
foreach (['accomplish_lists', 'project_lists'] as $table) {
    check(Schema::hasColumns($table, ['respondent', 'complainant']), "Fresh {$table} supports both parties and the upgrade migration");
}

$admin = User::create(['name' => 'Admin', 'username' => 'case-admin', 'email' => 'case-admin@example.test', 'role' => 'admin', 'password' => bcrypt('Test!Password123')]);
$legacyCase = Accomplishment::create(['title' => 'Legacy record', 'uploaded_by' => $admin->id, 'file_url' => 'accomplishments/existing.pdf']);
$legacyDispute = Project::create(['project_name' => 'Legacy dispute', 'added_by' => $admin->id, 'amount' => 1234.56, 'file_url' => 'projects/existing.pdf']);

// Reproduce the schema on a deployed installation without touching existing records.
$migration->down();
// Also exercise a partial upgrade with one column already present.
Schema::table('accomplish_lists', fn (Blueprint $table) => $table->string('respondent')->nullable());
DB::table('accomplish_lists')->update(['respondent' => 'Previously entered respondent']);
$migration->up();
$migration->up();
check($legacyCase->fresh()->respondent === 'Previously entered respondent' && $legacyCase->fresh()->complainant === null, 'Upgrade preserves existing parties and initializes missing parties as null');
check($legacyCase->fresh()->file_url === 'accomplishments/existing.pdf' && $legacyDispute->fresh()->file_url === 'projects/existing.pdf' && $legacyDispute->fresh()->amount === '1234.56', 'Upgrade preserves legacy files, IDs, and metadata');

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
function submit(string $uri, array $data, string $method = 'POST'): Symfony\Component\HttpFoundation\Response
{
    global $app, $admin, $kernel;
    $app['session']->driver()->flush();
    Auth::guard('web')->setUser($admin->fresh());
    return $kernel->handle(Request::create($uri, $method, $data, [], [], ['HTTP_ACCEPT' => 'application/json']));
}

foreach ([['/accomplishments', Accomplishment::class, 'title', AccomplishmentApiController::class, 'accomplishments'], ['/projects', Project::class, 'project_name', ProjectApiController::class, 'projects']] as [$uri, $model, $title, $apiClass, $type]) {
    $payload = [$title => 'New record', 'respondent' => 'Municipality Alpha', 'complainant' => 'Barangay Beta'];
    $response = submit($uri, $payload);
    check($response->isSuccessful(), "{$type} creates successfully");
    $record = $model::latest($model === Project::class ? 'project_id' : 'accomplish_id')->first();
    check($record->respondent === 'Municipality Alpha' && $record->complainant === 'Barangay Beta', "{$type} persists both parties");
    $payload['respondent'] = 'Municipality Updated';
    check(submit($uri.'/'.$record->getKey(), $payload, 'PUT')->isSuccessful() && $record->fresh()->respondent === 'Municipality Updated', "{$type} updates parties");
    foreach (['respondent' => str_repeat('x', 256), 'complainant' => ['invalid']] as $field => $badValue) {
        $response = submit($uri, array_replace($payload, [$field => $badValue]));
        $json = json_decode($response->getContent(), true);
        check($response->getStatusCode() === 422 && isset($json['errors'][$field]), "{$type} validates {$field}");
    }
    $api = app($apiClass);
    $json = $api->show($record->fresh())->getData(true);
    check(str_contains(json_encode($json), 'Municipality Updated') && str_contains(json_encode($json), 'Barangay Beta'), "{$type} API detail includes both parties");
    foreach (['Municipality Updated', 'Barangay Beta'] as $search) {
        $request = Request::create('/', 'GET', ['search' => $search]);
        $json = $api->index($request)->getData(true);
        check(str_contains(json_encode($json), 'New record') && ! str_contains(json_encode($json), 'Legacy'), "{$type} API searches {$search}");
        $json = app(DocumentApiController::class)->index($request, $type)->getData(true);
        check(str_contains(json_encode($json), 'New record') && str_contains(json_encode($json), 'Barangay Beta'), "Generic {$type} API searches and returns parties");
        $response = submit($uri, ['search' => $search], 'GET');
        check($response->isSuccessful() && str_contains($response->getContent(), 'New record') && ! str_contains($response->getContent(), 'Legacy record'), "{$type} web listing searches {$search}");
    }
    $payload['respondent'] = '';
    $payload['complainant'] = '';
    check(submit($uri.'/'.$record->getKey(), $payload, 'PUT')->isSuccessful() && $record->fresh()->respondent === null && $record->fresh()->complainant === null, "{$type} allows clearing optional parties");
}

$migration->down();
$migration->up();
check(Accomplishment::count() === 2 && Project::count() === 2, 'Migration rollback and reapply preserve document records');

// Verify these fresh-install migrations through Artisan, including the upgrade no-op.
// The unrelated SP extension migration uses MySQL-specific ENUM syntax.
Schema::dropAllTables();
Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true, '--path' => [
    'database/migrations/2025_10_15_100000_create_users_table_custom.php',
    'database/migrations/2025_10_17_100000_create_accomplish_lists_table.php',
    'database/migrations/2025_10_17_200000_create_project_lists_table.php',
    'database/migrations/2026_11_11_000000_add_parties_to_administrative_cases_and_boundary_disputes.php',
]]);
check(Schema::hasColumns('accomplish_lists', ['respondent', 'complainant']) && Schema::hasColumns('project_lists', ['respondent', 'complainant']), 'Fresh case/dispute migrations succeed through Artisan');
