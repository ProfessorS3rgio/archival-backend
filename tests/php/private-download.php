<?php

// Integration check against the configured database and private disk.
// Database writes made by request logging and sessions are rolled back.
require __DIR__.'/../../vendor/autoload.php';
$app = require __DIR__.'/../../bootstrap/app.php';
$app->instance('request', Illuminate\Http\Request::create('http://localhost'));
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();
set_exception_handler(function (Throwable $error): void {
    fwrite(STDERR, $error->getMessage()."\n");
    exit(1);
});

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

function check(bool $condition, string $message): void
{
    if (! $condition) {
        throw new RuntimeException($message);
    }
    echo "PASS: {$message}\n";
}

DB::beginTransaction();
try {
    Auth::guard('web')->setUser(App\Models\User::firstOrFail());

    foreach (['ordinance' => App\Models\OrdinanceFile::class, 'resolution' => App\Models\ResolutionFile::class] as $type => $model) {
        $file = $model::whereNotNull('file_url')->firstOrFail();
        $response = $kernel->handle(Request::create("/download/{$type}/{$file->getKey()}", 'GET'));
        check($response->getStatusCode() === 200, "{$type} download resolves by record ID");
        check(str_starts_with($response->headers->get('Content-Disposition', ''), 'attachment;'), "{$type} uses attachment disposition");
        check($response->headers->get('Content-Type') === 'application/pdf', "{$type} has PDF content type");
        ob_start();
        $response->sendContent();
        $content = ob_get_clean();
        check(str_starts_with($content, '%PDF'), "{$type} streams actual PDF content from private storage");
    }

    $file = App\Models\OrdinanceFile::firstOrFail();
    $response = $kernel->handle(Request::create("/download/ordinance/{$file->getKey()}/untrusted-filename.pdf", 'GET'));
    check($response->getStatusCode() === 200, 'Legacy filename cannot override the database storage key');

    $record = App\Models\Ordinance::has('files')->firstOrFail();
    $response = $kernel->handle(Request::create("/ordinances/{$record->getKey()}", 'GET', [], [], [], ['HTTP_ACCEPT' => 'application/json']));
    $data = json_decode($response->getContent(), true, flags: JSON_THROW_ON_ERROR);
    check(str_contains($data['files'][0]['download_url'], '/download/ordinance/'), 'Document viewer receives an authenticated download URL');
    check(! str_contains($data['files'][0]['download_url'], '/storage/'), 'Document viewer never receives a local public-storage download URL');

    $response = $kernel->handle(Request::create('/download/unknown/1', 'GET'));
    check($response->getStatusCode() === 404, 'Unknown document types return HTTP 404');
    $response = $kernel->handle(Request::create('/download/ordinance/999999999', 'GET'));
    check($response->getStatusCode() === 404, 'Missing records return HTTP 404');

    Auth::forgetGuards();
    $response = $kernel->handle(Request::create("/download/ordinance/{$file->getKey()}", 'GET'));
    check($response->isRedirect() && str_contains($response->headers->get('Location', ''), '/login'), 'Guests must sign in before downloading');
} finally {
    DB::rollBack();
}
