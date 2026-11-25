<?php

namespace App\Services;

use Illuminate\Database\Connection;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Fast, deterministic menu/API queries for the public archive assistant.
 * This service deliberately does not call an LLM or an external API.
 */
class ArchiveAssistant
{
    private const CONNECTION = 'archive_chat_mysql';

    public function run(string $intent, array $input = []): array
    {
        return match ($intent) {
            'recent_ordinances' => $this->recent('ordinance', $input),
            'recent_resolutions' => $this->recent('resolution', $input),
            'ordinance_by_number' => $this->byNumber('ordinance', $input),
            'resolution_by_number' => $this->byNumber('resolution', $input),
            'ordinance_search' => $this->search('ordinance', $input),
            'resolution_search' => $this->search('resolution', $input),
            'records_by_year' => $this->byYear($input),
            default => throw new RuntimeException('Unsupported archive assistant action.'),
        };
    }

    private function recent(string $kind, array $input): array
    {
        $limit = min(max((int) ($input['limit'] ?? 5), 1), 10);
        $rows = $this->base($kind)->orderByDesc('date_approved')->limit($limit)->get();

        return $this->response("Recent {$kind}s", $rows, $kind);
    }

    private function byNumber(string $kind, array $input): array
    {
        $number = trim((string) ($input['number'] ?? ''));
        if ($number === '') {
            throw new RuntimeException('Please provide an ordinance or resolution number.');
        }

        $rows = $this->base($kind)->where($kind.'_no', $number)->limit(10)->get();

        return $this->response("{$kind} number {$number}", $rows, $kind);
    }

    private function search(string $kind, array $input): array
    {
        $query = trim((string) ($input['query'] ?? ''));
        if (mb_strlen($query) < 2) {
            throw new RuntimeException('Please provide at least two characters to search.');
        }

        $builder = $this->base($kind);
        try {
            // The migration indexes the document number together with the text
            // fields; MySQL requires MATCH() to use that same column list.
            $builder->whereFullText([$kind.'_no', 'title', 'subject', 'author', 'extracted_text'], $query);
            $columns = implode(',', [$kind.'_no', 'title', 'subject', 'author', 'extracted_text']);
            $rows = $builder
                ->orderByRaw("MATCH ({$columns}) AGAINST (? IN NATURAL LANGUAGE MODE) DESC", [$query])
                ->orderByDesc('date_approved')->limit(10)->get();
        } catch (QueryException) {
            $like = '%'.$query.'%';
            $builder->where(function (Builder $where) use ($like) {
                $where->where('title', 'like', $like)
                    ->orWhere('subject', 'like', $like)
                    ->orWhere('author', 'like', $like)
                    ->orWhere('extracted_text', 'like', $like);
            });
            $rows = $builder->orderByDesc('date_approved')->limit(10)->get();
        }

        return $this->response("Search results for {$query}", $rows, $kind);
    }

    private function byYear(array $input): array
    {
        $year = (int) ($input['year'] ?? 0);
        if ($year < 1900 || $year > 2200) {
            throw new RuntimeException('Please provide a valid year.');
        }

        $rows = collect(['ordinance', 'resolution'])->flatMap(function (string $kind) use ($year) {
            return $this->base($kind)->whereYear('date_approved', $year)->orderByDesc('date_approved')->limit(10)->get()
                ->map(fn ($row) => $this->format($row, $kind));
        })->sortByDesc('date_approved')->values();

        return [
            'intent' => 'records_by_year',
            'message' => "Approved records from {$year}",
            'items' => $rows,
        ];
    }

    private function base(string $kind): Builder
    {
        return $this->connection()->table($kind.'_lists')
            ->where('is_visible', true)
            ->whereNotNull('date_approved')
            ->select([
                $kind.'_id as id', $kind.'_no as number', 'title', 'subject', 'author', 'date_approved',
            ]);
    }

    private function response(string $message, $rows, string $kind): array
    {
        return [
            'intent' => $kind,
            'message' => $rows->isEmpty() ? 'No published records matched your request.' : $message,
            'items' => $rows->map(fn ($row) => $this->format($row, $kind))->values(),
        ];
    }

    private function format(object $row, string $kind): array
    {
        return [
            'type' => $kind,
            'id' => (int) $row->id,
            'number' => $row->number,
            'title' => $row->title,
            'subject' => $row->subject,
            'author' => $row->author,
            'date_approved' => $row->date_approved,
            'url' => "/{$kind}s/{$row->id}",
        ];
    }

    private function connection(): Connection
    {
        $config = config('database.connections.'.self::CONNECTION);
        if (($config['host'] ?? null) !== '127.0.0.1' || ! empty($config['url']) || ! empty($config['read']) || ! empty($config['write']) || ! empty($config['unix_socket'])) {
            throw new RuntimeException('The benchmark connection must use MySQL on 127.0.0.1.');
        }

        return DB::connection(self::CONNECTION);
    }
}
