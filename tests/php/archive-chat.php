<?php

require __DIR__.'/../../vendor/autoload.php';
$_ENV['APP_CONFIG_CACHE'] = sys_get_temp_dir().'/archive-chat-config-test-'.bin2hex(random_bytes(6)).'.php';
$_ENV['CACHE_STORE'] = 'array';
$app = require __DIR__.'/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

function checkChat(bool $condition, string $message): void
{
    if (! $condition) {
        throw new RuntimeException($message);
    }
    echo "PASS: {$message}\n";
}

function samplePdf(string $text): string
{
    $stream = "BT /F1 12 Tf 50 700 Td ({$text}) Tj ET";
    $objects = [
        '<< /Type /Catalog /Pages 2 0 R >>',
        '<< /Type /Pages /Kids [3 0 R] /Count 1 >>',
        '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> >>',
        '<< /Length '.strlen($stream)." >>\nstream\n{$stream}\nendstream",
        '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>',
    ];
    $pdf = "%PDF-1.4\n";
    $xref = "0000000000 65535 f \n";
    foreach ($objects as $index => $object) {
        $xref .= sprintf("%010d 00000 n \n", strlen($pdf));
        $pdf .= ($index + 1)." 0 obj\n{$object}\nendobj\n";
    }

    return $pdf."xref\n0 6\n{$xref}trailer\n<< /Size 6 /Root 1 0 R >>\nstartxref\n".strlen($pdf)."\n%%EOF\n";
}

$path = sys_get_temp_dir().'/archive-chat-test-'.bin2hex(random_bytes(6));
config([
    'archive-chat.enabled' => true,
    'archive-chat.source' => 'api',
    'archive-chat.source_url' => 'https://archive.example.test/api/v1',
    'archive-chat.source_token' => 'fake-read-token',
    'archive-chat.index_path' => $path,
    'database.default' => 'intentionally-unavailable',
    'session.driver' => 'database',
    'cache.default' => 'database',
]);

$quote = 'Residents must separate recyclable and biodegradable waste.';
$pdf = samplePdf($quote);
$hidden = false;
$validate = true;
$liveOllama = in_array('--live-ollama', $argv, true);
Http::fake(function (ClientRequest $request) use ($pdf, $quote, &$hidden, &$validate, $liveOllama) {
    $url = $request->url();
    if (str_starts_with($url, 'https://archive.example.test/')) {
        checkChat($request->method() === 'GET' && $request->hasHeader('Authorization', 'Bearer fake-read-token'), 'Archive source only receives authenticated GET requests');

        return match ($url) {
            'https://archive.example.test/api/v1/Documents/Ordinance/42' => $hidden
                ? Http::response([], 404)
                : Http::response(['data' => ['id' => 42, 'title' => 'Waste ordinance', 'date_approved' => '2026-01-01', 'updated_at' => '2026-01-01T00:00:00Z', 'files' => [['id' => 7, 'filename' => 'waste.pdf']]]]),
            'https://archive.example.test/api/v1/Documents/Ordinance/42/files/7/download' => Http::response($pdf),
            default => throw new RuntimeException('Unexpected archive URL'),
        };
    }

    checkChat(! $request->hasHeader('Authorization'), 'The archive token stays out of Ollama requests');
    if ($liveOllama) {
        return null;
    }

    return match ($url) {
        'http://127.0.0.1:11434/api/embed' => Http::response(['embeddings' => array_fill(0, count($request['input']), array_fill(0, 768, 0.1))]),
        'http://127.0.0.1:11434/api/chat' => Http::response(['message' => ['content' => json_encode(
            str_starts_with($request['messages'][0]['content'], 'Check whether')
                ? ['supported' => $validate]
                : ['answer' => $quote, 'source' => 'S1', 'quote' => $quote]
        )]]),
        default => throw new RuntimeException('Unexpected Ollama URL'),
    };
});
if (! $liveOllama) {
    Http::preventStrayRequests();
}

$kernel = $app->make(Kernel::class);
$request = fn ($url, $method = 'GET', $headers = []) => $kernel->handle(Request::create($url, $method, server: $headers + [
    'REMOTE_ADDR' => '127.0.0.1', 'CONTENT_TYPE' => 'application/json', 'HTTP_ACCEPT' => 'application/json',
], content: $method === 'POST' ? '{"question":"What must residents separate?"}' : null));

try {
    $base = 'http://127.0.0.1/api/archive-chat';
    checkChat($request($base)->getStatusCode() === 200, 'Local chat page renders without the default database');
    checkChat($request($base, headers: ['REMOTE_ADDR' => '192.0.2.10'])->getStatusCode() === 404, 'Remote clients cannot open the prototype');
    checkChat($request($base.'/ask', 'POST', ['HTTP_ORIGIN' => 'https://other.example'])->getStatusCode() === 403, 'Cross-origin requests are blocked');
    checkChat(Artisan::call('archival:chat-index', ['type' => 'ordinances', 'id' => 42]) === 0, 'Approved API PDF is indexed');
    $response = $request($base.'/ask', 'POST');
    $result = json_decode($response->getContent(), true);
    checkChat($response->getStatusCode() === 200 && $result['status'] === 'answered', 'Chat returns a verified answer');
    checkChat($result['sources'][0]['url'] === '/api/archive-chat/sources/ordinances/42/7#page=1', 'Citation links to the exact PDF page');
    checkChat($request($base.'/sources/ordinances/42/7')->getContent() === $pdf, 'Cited PDF opens through the local proxy');
    if (! $liveOllama) {
        $validate = false;
        $result = json_decode($request($base.'/ask', 'POST')->getContent(), true);
        checkChat($result['status'] === 'sources_only', 'Validator rejection returns excerpts');
    }
    $hidden = true;
    $result = json_decode($request($base.'/ask', 'POST')->getContent(), true);
    checkChat($result['status'] === 'no_match' && $result['sources'] === [], 'Withdrawn records cannot appear in answers');
    checkChat($request($base.'/sources/ordinances/42/7')->getStatusCode() === 404, 'Withdrawn PDFs cannot be opened');
} finally {
    File::deleteDirectory($path);
}
