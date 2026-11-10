<?php

// HTTP regression checks use SQLite in memory and never touch the configured database.
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

use App\Models\SystemLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

set_exception_handler(function (Throwable $error): void {
    fwrite(STDERR, $error->getMessage()."\n");
    exit(1);
});

foreach (['2025_10_15_100000_create_users_table_custom', '2025_10_15_100001_create_resolution_lists_table', '2025_10_15_200000_create_ordinance_lists_table', '2025_10_16_100000_create_resolution_draft_table', '2025_10_16_200000_create_ordinance_draft_table', '2025_10_22_000000_create_system_logs_table', '2025_10_30_063921_create_sangguniang_panlalawigan_terms_table'] as $file) {
    (require __DIR__.'/../../database/migrations/'.$file.'.php')->up();
}

$admin = User::create(['name' => 'Test Admin', 'username' => 'admin', 'email' => 'admin@example.test', 'role' => 'admin', 'password' => Hash::make('Original!Password')]);
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

function check(bool $condition, string $message): void
{
    if (! $condition) {
        throw new RuntimeException($message);
    }
    echo "PASS: {$message}\n";
}

function submit(string $uri, array $data, string $method = 'POST', bool $inertia = false): Symfony\Component\HttpFoundation\Response
{
    global $admin, $app, $kernel;
    $app['session']->driver()->flush();
    Auth::guard('web')->setUser($admin->fresh());
    $request = Request::create($uri, $method, $data, [], [], [
        'HTTP_ACCEPT' => $inertia ? 'text/html, application/xhtml+xml' : 'application/json',
        'HTTP_X_INERTIA' => $inertia ? 'true' : '',
        'HTTP_REFERER' => 'http://localhost/settings/add-accounts',
    ]);
    return $kernel->handle($request);
}

function invalid(array $overrides, string $field): void
{
    global $payload;
    $response = submit('/settings/add-accounts', array_replace($payload, $overrides));
    $json = json_decode($response->getContent(), true);
    check($response->getStatusCode() === 422 && isset($json['errors'][$field]), "Account rejects invalid {$field} with a field error");
}

$payload = ['name' => 'José O’Connor-De la Cruz', 'email' => 'jose@example.test', 'username' => 'josé.001', 'role' => 'legislator', 'password' => 'Good!Password123', 'password_confirmation' => 'Good!Password123'];

invalid(['username' => 'bad username<script>'], 'username');
invalid(['username' => ['unexpected']], 'username');
invalid(['email' => 'not-an-email'], 'email');
invalid(['email' => $admin->email, 'username' => $admin->username], 'email');
invalid(['name' => ['unexpected']], 'name');
invalid(['name' => str_repeat('a', 256)], 'name');
invalid(['role' => ['admin']], 'role');
invalid(['role' => 'owner'], 'role');
invalid(['password' => ['unexpected']], 'password');
invalid(['password' => 'short', 'password_confirmation' => 'short'], 'password');
invalid(['password_confirmation' => 'Different!Password'], 'password');
invalid(['password' => "Good\0Password", 'password_confirmation' => "Good\0Password"], 'password');
invalid(['password' => str_repeat('a', 73), 'password_confirmation' => str_repeat('a', 73)], 'password');
invalid(['password' => str_repeat('é', 37), 'password_confirmation' => str_repeat('é', 37)], 'password');
invalid(['name' => "Invalid\x01Name"], 'name');
invalid(['name' => "Invalid\xFFName"], 'name');
check(User::count() === 1, 'Rejected account submissions do not create users');

$response = submit('/settings/add-accounts', $payload);
$created = User::where('username', $payload['username'])->firstOrFail();
check($response->isSuccessful() && $created->name === $payload['name'] && $created->role === 'legislator', 'Unicode names, punctuation, and legislator accounts save successfully');

$edit = $payload;
$edit['password'] = '';
$edit['password_confirmation'] = '';
$oldHash = $created->password;
$response = submit('/settings/manage-accounts/'.$created->id, $edit, 'PUT');
check($response->isSuccessful() && $created->fresh()->password === $oldHash, 'Editing an account with a blank password preserves its password');
$response = submit('/settings/manage-accounts/'.$created->id, array_replace($edit, ['username' => 'invalid space']), 'PUT', true);
check($response->isRedirect() && session('errors')->has('username'), 'Inertia edit errors remain in the form error bag');

$change = ['current_password' => 'Original!Password', 'password' => 'New!Password123', 'password_confirmation' => 'New!Password123'];
foreach ([['current_password' => 'wrong'], ['current_password' => ['unexpected']], ['password_confirmation' => 'mismatch'], ['password' => str_repeat('a', 73)], ['password' => "New\0Password"]] as $override) {
    $response = submit('/settings/change-password', array_replace($change, $override));
    check($response->getStatusCode() === 422 && Hash::check('Original!Password', $admin->fresh()->password), 'Invalid password change returns validation errors and preserves the password');
}

// Force a storage failure after the user write to verify atomic saves and safe error responses.
SystemLog::creating(function () { throw new RuntimeException('private diagnostic: SQL/password must never be shown'); });
$response = submit('/settings/add-accounts', array_replace($payload, ['email' => 'atomic@example.test', 'username' => 'atomic']), 'POST', true);
check($response->getStatusCode() === 303 && session('errors')->has('general') && ! User::where('username', 'atomic')->exists(), 'Failed account logging rolls back the account and returns a safe Inertia form error');
check(! session()->hasOldInput('password'), 'Failed submissions never flash passwords into session input');
$response = submit('/settings/change-password', $change);
check($response->getStatusCode() === 500 && ! str_contains($response->getContent(), 'private diagnostic') && Hash::check('Original!Password', $admin->fresh()->password), 'Unexpected JSON errors hide diagnostics and roll back password changes');
SystemLog::flushEventListeners();

$change['password'] = $change['password_confirmation'] = str_repeat('é', 36);
$response = submit('/settings/change-password', $change);
check($response->isSuccessful() && Hash::check($change['password'], $admin->fresh()->password), 'A valid password at the 72-byte boundary saves successfully');

foreach ([
    ['/ordinances', ['title' => str_repeat('x', 256), 'added_by' => $admin->id], 'title'],
    ['/resolutions', ['date_approved' => '0005-01-01', 'added_by' => $admin->id], 'date_approved'],
    ['/projects', ['project_name' => 'Test', 'amount' => '10000000000'], 'amount'],
    ['/projects', ['project_name' => 'Test', 'code_no' => '2147483648'], 'code_no'],
    ['/projects', ['project_name' => 'Test', 'extracted_text' => [['nested']]], 'extracted_text'],
    ['/draft-ordinances', ['added_by' => $admin->id, 'title' => str_repeat('x', 256)], 'title'],
    ['/draft-resolutions', ['added_by' => $admin->id, 'files' => 'invalid'], 'files'],
    ['/committee-reports', ['title' => 'Test', 'subject' => str_repeat('x', 256)], 'subject'],
    ['/draft-resolutions', ['added_by' => $admin->id, 'file_extracted_texts' => [['nested']]], 'file_extracted_texts.0'],
] as [$uri, $data, $field]) {
    $response = submit($uri, $data);
    check($response->getStatusCode() === 422 && isset(json_decode($response->getContent(), true)['errors'][$field]), "{$uri} rejects invalid {$field} before writing to the database");
}

foreach (['/draft-ordinances', '/draft-resolutions'] as $uri) {
    $response = submit($uri, ['title' => 'Valid draft', 'added_by' => $admin->id, 'extracted_text' => 'Text from the current upload form.']);
    check($response->isSuccessful() || $response->isRedirect(), "{$uri} accepts the current form's string extracted text");
}
foreach (['/ordinances', '/resolutions'] as $uri) {
    $response = submit($uri, ['title' => 'Valid document', 'added_by' => $admin->id]);
    check($response->isSuccessful() || $response->isRedirect(), "{$uri} handles omitted optional fields without a server error");
}

Route::middleware('web')->post('/test-form-error/{status}', fn (string $status) => abort((int) $status));
Route::middleware('web')->post('/test-upload-storage-error', fn () => back()->withErrors(['file' => 'Failed to store file.']));
$response = submit('/test-upload-storage-error', []);
check($response->getStatusCode() === 422 && isset(json_decode($response->getContent(), true)['errors']['file']), 'A redirected upload failure returns JSON field errors instead of a false success');
foreach ([403, 404, 413, 419, 429, 500, 503] as $status) {
    $response = submit('/test-form-error/'.$status, [], 'POST', true);
    check($response->getStatusCode() === 303 && session('errors')->has('general'), "HTTP {$status} returns an actionable Inertia form error");
    $response = submit('/test-form-error/'.$status, []);
    check($response->getStatusCode() === $status && isset(json_decode($response->getContent(), true)['errors']['general']), "HTTP {$status} preserves JSON status and a safe error message");
}
