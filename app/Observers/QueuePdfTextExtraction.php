<?php

namespace App\Observers;

use App\Jobs\ExtractStoredPdfText;
use Illuminate\Database\Eloquent\Model;

class QueuePdfTextExtraction
{
    public function saved(Model $record): void
    {
        $path = $record->file_url;

        if (! $path || ! preg_match('/\.pdf$/i', $path)) {
            return;
        }

        if (! $record->wasRecentlyCreated && ! $record->wasChanged('file_url')) {
            return;
        }

        ExtractStoredPdfText::dispatch(get_class($record), $record->getKey(), $path)
            ->onConnection('database')->onQueue('pdf-text')->afterCommit();
    }
}
