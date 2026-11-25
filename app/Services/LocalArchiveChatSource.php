<?php

namespace App\Services;

use Illuminate\Database\Connection;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class LocalArchiveChatSource
{
    private const CONNECTION = 'archive_chat_mysql';

    public function indexKey(): string
    {
        $config = config('database.connections.'.self::CONNECTION);

        return hash('sha256', $config['port'].'/'.$config['database']);
    }

    public function record(string $type, int $id): ?array
    {
        $kind = $type === 'ordinances' ? 'ordinance' : 'resolution';
        $key = $kind.'_id';
        $db = $this->connection();

        try {
            $record = $db->table($kind.'_lists')
                ->where($key, $id)->where('is_visible', true)->whereNotNull('date_approved')
                ->first([$key.' as id', $kind.'_no', 'title', 'date_approved', 'updated_at']);
            if (! $record) {
                return null;
            }

            $files = $db->table($kind.'_files')->where($key, $id)->orderBy('file_id')
                ->get(['file_id as id', 'file_name as filename', 'file_type as mime_type', 'file_url', 'updated_at', 'file_size']);
        } catch (QueryException) {
            throw new RuntimeException('The local benchmark database could not be read. Check its credentials and schema.');
        }

        $data = (array) $record;
        $data['files'] = $files->map(fn ($file) => (array) $file)->all();
        $data['updated_at'] = hash('sha256', json_encode($data, JSON_THROW_ON_ERROR));

        return $data;
    }

    public function pdf(string $type, int $id, int $file): string
    {
        $kind = $type === 'ordinances' ? 'ordinance' : 'resolution';
        $key = $kind.'_id';
        $db = $this->connection();

        try {
            $path = $db->table($kind.'_files as file')
                ->join($kind.'_lists as record', 'file.'.$key, '=', 'record.'.$key)
                ->where('record.'.$key, $id)->where('record.is_visible', true)->whereNotNull('record.date_approved')
                ->where('file.file_id', $file)->value('file.file_url');
        } catch (QueryException) {
            throw new RuntimeException('The local benchmark database could not be read. Check its credentials and schema.');
        }

        $disk = Storage::disk('local');
        $body = filled($path) ? $disk->get($path) : null;

        if (! is_string($body) || ! str_starts_with($body, '%PDF-')) {
            throw new RuntimeException('The benchmark PDF is missing from storage/app/private or is unreadable.');
        }

        return $body;
    }

    private function connection(): Connection
    {
        $config = config('database.connections.'.self::CONNECTION);
        if (($config['host'] ?? null) !== '127.0.0.1' || ! empty($config['url']) || ! empty($config['read']) || ! empty($config['write']) || ! empty($config['unix_socket'])) {
            throw new RuntimeException('The benchmark connection must use MySQL on 127.0.0.1.');
        }
        if (blank($config['database'] ?? null) || blank($config['username'] ?? null)) {
            throw new RuntimeException('Configure the existing DB_DATABASE and DB_USERNAME settings for local MySQL.');
        }

        return DB::connection(self::CONNECTION);
    }
}
