<?php

// All database and storage operations are isolated from the configured services.
require __DIR__.'/../../vendor/autoload.php';
$app = require __DIR__.'/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$app->instance('env', 'testing');
$root = sys_get_temp_dir().'/archival-municipal-test-'.bin2hex(random_bytes(8));
config([
    'database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'database.connections.sqlite.url' => null,
    'filesystems.disks.private' => ['driver' => 'local', 'root' => $root, 'throw' => true],
    'session.driver' => 'array', 'cache.default' => 'array', 'logging.default' => 'null', 'hashing.bcrypt.rounds' => 4,
]);

use App\Models\MunicipalitiesCommunicationList;
use App\Models\SystemLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

set_exception_handler(function (Throwable $error): void { fwrite(STDERR, $error->getMessage()."\n"); exit(1); });
foreach (['2025_10_15_100000_create_users_table_custom', '2025_10_19_200000_create_municipalities_communication_lists_table', '2025_10_22_000000_create_system_logs_table', '2025_10_30_063921_create_sangguniang_panlalawigan_terms_table'] as $file) {
    (require __DIR__.'/../../database/migrations/'.$file.'.php')->up();
}
$admin = User::create(['name' => 'Test Admin', 'email' => 'admin@example.test', 'username' => 'admin', 'password' => 'password', 'role' => 'admin']);
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$payload = ['title' => 'Municipal test', 'subject' => 'Ipil', 'year' => '2026', 'date_received' => '', 'extracted_text' => 'Original search text'];

function check(bool $condition, string $message): void
{
    if (! $condition) throw new RuntimeException($message);
    echo "PASS: {$message}\n";
}
function send(string $uri, array $data = [], array $files = [], string $method = 'POST')
{
    global $app, $admin, $kernel;
    $app['session']->driver()->flush();
    Auth::guard('web')->setUser($admin->fresh());
    return $kernel->handle(Request::create($uri, $method, $data, [], $files, ['HTTP_ACCEPT' => 'application/json']));
}
function pdf(string $name = 'letter.pdf', int $size = 100): UploadedFile
{
    return UploadedFile::fake()->createWithContent($name, "%PDF-1.4\n".str_repeat(' ', $size)."\n%%EOF");
}

try {
    $response = send('/municipal-communications', $payload, ['files' => [pdf(str_repeat('a', 260).'.pdf', 3 * 1024 * 1024)]]);
    $record = MunicipalitiesCommunicationList::firstOrFail();
    check($response->isSuccessful() && $record->file_size > 2 * 1024 * 1024 && Storage::disk('private')->exists($record->file_url), 'A valid attachment above 2 MB saves successfully within the 10 MB application limit');
    check(strlen($record->file_url) < 100 && mb_strlen($record->file_name) <= 255, 'Long client filenames cannot overflow the database path columns');
    $url = '/municipal-communications/'.$record->getKey();
    $oldPath = $record->file_url;
    $metadata = $payload; unset($metadata['extracted_text']);
    $response = send($url, [...$metadata, '_method' => 'PUT']);
    check($response->isSuccessful() && $record->fresh()->extracted_text === 'Original search text' && $record->fresh()->file_url === $oldPath, 'Metadata-only edits preserve the attachment and its search text');

    $disk = Mockery::mock(Illuminate\Filesystem\FilesystemAdapter::class);
    $disk->shouldReceive('putFileAs')->andThrow(new RuntimeException('Simulated R2 outage'));
    Storage::set('private', $disk);
    $response = send($url, [...$payload, '_method' => 'PUT'], ['files' => [pdf()]]);
    Storage::forgetDisk('private');
    check($response->getStatusCode() === 422 && str_contains(json_decode($response->getContent(), true)['errors']['files.0'][0] ?? '', 'could not be saved') && Storage::disk('private')->exists($oldPath), 'Storage failures return attachment errors without deleting the original');

    $beforeFiles = Storage::disk('private')->allFiles();
    SystemLog::creating(function () { throw new RuntimeException('Simulated database failure'); });
    $response = send($url, [...$payload, '_method' => 'PUT'], ['files' => [pdf()]]);
    check($response->getStatusCode() === 500 && $record->fresh()->file_url === $oldPath && Storage::disk('private')->allFiles() === $beforeFiles, 'Failed database saves preserve the old file and clean up the replacement');
    SystemLog::flushEventListeners();

    $response = send($url, [...$payload, '_method' => 'PUT', 'extracted_text' => 'New text'], ['files' => [pdf('replacement.pdf')]]);
    $record->refresh();
    check($response->isSuccessful() && $record->file_url !== $oldPath && ! Storage::disk('private')->exists($oldPath) && $record->extracted_text === 'New text', 'Successful replacement switches the file and then removes the original');
    $oldPath = $record->file_url;

    $response = send('/municipal-communications', $payload, ['files' => [pdf('a.pdf'), pdf('b.pdf')]]);
    check($response->getStatusCode() === 422 && isset(json_decode($response->getContent(), true)['errors']['files']), 'Multiple attachments are rejected rather than silently discarded');
    $response = send('/municipal-communications', $payload, ['files' => [pdf('large.pdf', 10 * 1024 * 1024 + 1)]]);
    check($response->getStatusCode() === 422, 'Attachments above 10 MB receive validation errors');

    $response = send('/api/preview-upload', [], ['file' => pdf('preview.pdf')]);
    $preview = json_decode($response->getContent(), true);
    check($response->isSuccessful() && isset($preview['url']) && Storage::disk('private')->exists($preview['path']), 'Temporary preview uploads produce a signed private-file URL');
    $response = send($url, [...$metadata, '_method' => 'PUT', 'remove_existing_file' => '1']);
    check($response->isSuccessful() && $record->fresh()->file_url === null && ! Storage::disk('private')->exists($oldPath), 'Explicit attachment removal clears the saved reference and file');
    check(send($url.'/edit', method: 'GET')->isSuccessful(), 'Editing a communication without an attachment does not generate an invalid signed URL');
} finally {
    Storage::forgetDisk('private');
    (new Illuminate\Filesystem\Filesystem)->deleteDirectory($root);
    Mockery::close();
}
