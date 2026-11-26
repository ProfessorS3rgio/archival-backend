<?php

namespace App\Jobs;

use App\Models\DraftOrdinanceFile;
use App\Models\DraftResolutionFile;
use App\Models\OrdinanceFile;
use App\Models\ResolutionFile;
use App\Services\PdfTextExtractionService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class ExtractStoredPdfText implements ShouldQueue
{
    use Queueable;

    public int $timeout = 900;
    public int $tries = 3;

    public function __construct(public string $modelClass, public int $recordId, public string $path)
    {
    }

    public function handle(PdfTextExtractionService $extractor): void
    {
        $record = $this->modelClass::find($this->recordId);
        if (! $record || $record->file_url !== $this->path) {
            return;
        }

        $stream = Storage::disk('private')->readStream($this->path);
        if ($stream === false) {
            throw new RuntimeException('Cannot read stored PDF: '.$this->path);
        }

        $localPath = tempnam(sys_get_temp_dir(), 'archival-pdf-');
        if ($localPath === false) {
            fclose($stream);
            throw new RuntimeException('Cannot create a temporary PDF file.');
        }

        try {
            $local = fopen($localPath, 'wb');
            if ($local === false) {
                throw new RuntimeException('Cannot open the temporary PDF file.');
            }
            try {
                if (stream_copy_to_stream($stream, $local) === false) {
                    throw new RuntimeException('Cannot copy the stored PDF.');
                }
            } finally {
                fclose($local);
            }
            $text = $extractor->extract($localPath);
        } finally {
            fclose($stream);
            @unlink($localPath);
        }

        DB::transaction(function () use ($text) {
            $record = $this->modelClass::find($this->recordId);
            if (! $record || $record->file_url !== $this->path) {
                return;
            }

            $parents = [
                OrdinanceFile::class => 'ordinance',
                ResolutionFile::class => 'resolution',
                DraftOrdinanceFile::class => 'draftOrdinance',
                DraftResolutionFile::class => 'draftResolution',
            ];
            if (! isset($parents[$this->modelClass])) {
                $record->extracted_text = $text;
                $record->saveQuietly();
                return;
            }

            $relation = $parents[$this->modelClass];
            $parent = $record->$relation()->lockForUpdate()->first();
            if (! $parent) {
                return;
            }
            $record->extracted_text = $text;
            $record->saveQuietly();
            $parent->extracted_text = $parent->files()->orderBy('file_id')->pluck('extracted_text')
                ->filter(fn ($value) => is_string($value) && trim($value) !== '')->implode("\n\n");
            $parent->saveQuietly();
        });
    }
}
