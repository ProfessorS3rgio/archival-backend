<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class PrivateDocumentDeletion
{
    public function delete(Model $document): void
    {
        $document->getConnection()->transaction(function () use ($document) {
            $document = $document->newQuery()->lockForUpdate()->findOrFail($document->getKey());
            $hasAttachments = method_exists($document, 'files');
            $files = $hasAttachments ? $document->files()->get() : collect([$document]);
            $paths = $files->pluck('file_url')->filter()->unique()->values()->all();

            // Remove objects before their database references are lost to cascading deletes.
            // Missing objects are safe to delete again when retrying a partial failure.
            if ($paths && ! Storage::disk('private')->delete($paths)) {
                throw new RuntimeException('Private file storage did not confirm deletion.');
            }

            if ($hasAttachments) {
                $document->files()->delete();
            }

            $document->delete();

            if (! in_array(\App\Traits\LogsActivity::class, class_uses_recursive($document), true)) {
                LoggingService::logDelete($document);
            }
        });
    }
}
