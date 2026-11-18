<?php

namespace App\Console\Commands;

use App\Services\ArchiveChat;
use App\Services\ArchiveChatSource;
use Illuminate\Console\Command;
use RuntimeException;

class IndexArchiveChatRecord extends Command
{
    protected $signature = 'archival:chat-index {type : ordinances or resolutions} {id : Archive record ID}';

    protected $description = 'Index the published PDFs of one archive record for local chat';

    public function handle(ArchiveChat $chat): int
    {
        $type = $this->argument('type');
        $id = filter_var($this->argument('id'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if (! config('archive-chat.enabled') || ! isset(ArchiveChatSource::TYPES[$type]) || $id === false) {
            $this->error('Enable local archive chat and provide ordinances or resolutions with a positive record ID.');

            return self::FAILURE;
        }

        try {
            $result = $chat->index($type, $id);
        } catch (RuntimeException $error) {
            $this->error($error->getMessage());

            return self::FAILURE;
        }

        $this->info("Indexed {$result['chunks']} passages for {$type}/{$id}.");
        if ($result['empty_pages'] > 0) {
            $this->warn("Skipped {$result['empty_pages']} pages without readable text.");
        }

        return self::SUCCESS;
    }
}
