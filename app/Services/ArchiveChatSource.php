<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class ArchiveChatSource
{
    public const TYPES = ['ordinances' => 'Ordinance', 'resolutions' => 'Resolution'];

    public function __construct(private LocalArchiveChatSource $local) {}

    public function indexPath(): string
    {
        $path = config('archive-chat.index_path');

        return $this->usesLocalMysql() ? $path.'/mysql-'.$this->local->indexKey() : $path;
    }

    public function record(string $type, int $id): ?array
    {
        if ($this->usesLocalMysql()) {
            return $this->local->record($type, $id);
        }

        $response = $this->get($type, $id);
        if ($response->status() === 404) {
            return null;
        }

        $record = $response->json('data');
        if (! $response->successful() || ! is_array($record) || (int) ($record['id'] ?? 0) !== $id) {
            throw new RuntimeException('The archive API could not read this record. Check the source URL and read token.');
        }

        return empty($record['date_approved']) ? null : $record;
    }

    public function pdf(string $type, int $id, int $file): string
    {
        if ($this->usesLocalMysql()) {
            return $this->local->pdf($type, $id, $file);
        }

        $response = $this->get($type, $id, "/files/{$file}/download");
        $body = $response->body();
        if (! $response->successful() || ! str_starts_with($body, '%PDF-')) {
            throw new RuntimeException('The archive API could not download this PDF.');
        }

        return $body;
    }

    private function usesLocalMysql(): bool
    {
        return match (config('archive-chat.source')) {
            'mysql' => true,
            'api' => false,
            default => throw new RuntimeException('Set ARCHIVE_CHAT_SOURCE to api or mysql.'),
        };
    }

    private function get(string $type, int $id, string $suffix = ''): Response
    {
        $kind = self::TYPES[$type];

        try {
            return $this->client()->get("/Documents/{$kind}/{$id}{$suffix}");
        } catch (ConnectionException) {
            throw new RuntimeException('The archive API could not be reached.');
        }
    }

    private function client(): PendingRequest
    {
        $url = rtrim((string) config('archive-chat.source_url'), '/');
        $token = (string) config('archive-chat.source_token');
        $parts = parse_url($url);
        if (($parts['scheme'] ?? '') !== 'https' || empty($parts['host']) || isset($parts['user']) || isset($parts['pass']) || isset($parts['query']) || isset($parts['fragment']) || $token === '') {
            throw new RuntimeException('Set ARCHIVE_CHAT_SOURCE_URL to the HTTPS archive API v1 base URL and provide its read token.');
        }

        return Http::baseUrl($url)->withToken($token)->acceptJson()->withoutRedirecting()->connectTimeout(5)->timeout(25);
    }
}
