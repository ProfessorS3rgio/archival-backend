<?php

namespace App\Console\Commands;

use App\Models\SpProfilePicture;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MigrateOfficialPictures extends Command
{
    protected $signature = 'officials:migrate-pictures {--dry-run : List local pictures without copying them}';

    protected $description = 'Copy local officials pictures to the private disk and verify their contents before switching records';

    public function handle(): int
    {
        $failures = 0;
        foreach (SpProfilePicture::where('disk', 'public')->lazyById(100, 'sp_profile_pictures_id') as $picture) {
            $path = null;
            try {
                if (! Storage::disk('public')->exists($picture->file_url)) {
                    throw new \RuntimeException('The local source picture is missing.');
                }
                if ($this->option('dry-run')) {
                    $this->line("Would copy picture {$picture->getKey()} to the private disk.");
                    continue;
                }

                $contents = Storage::disk('public')->get($picture->file_url);
                $extension = match ($picture->file_type) {
                    'image/jpeg' => 'jpg',
                    'image/png' => 'png',
                    default => throw new \RuntimeException('Unsupported profile picture type.'),
                };
                $path = 'sp-profiles/'.Str::uuid().'.'.$extension;
                if (! Storage::disk('private')->put($path, $contents, ['ContentType' => $picture->file_type])) {
                    throw new \RuntimeException('The picture could not be copied.');
                }
                if (hash('sha256', $contents) !== hash('sha256', Storage::disk('private')->get($path))) {
                    throw new \RuntimeException('The copied picture failed verification.');
                }

                $updated = SpProfilePicture::whereKey($picture->getKey())->where('disk', 'public')
                    ->where('file_url', $picture->file_url)->update(['disk' => 'private', 'file_url' => $path]);
                if (! $updated) {
                    throw new \RuntimeException('The picture changed during copying. Run the command again.');
                }
                // Retain the original local file as a backup; never remove it during migration.
                $this->info("Migrated and verified picture {$picture->getKey()}.");
                $path = null;
            } catch (\Throwable $error) {
                if ($path) {
                    app(\App\Services\OfficialProfilePictures::class)->deleteFile(['disk' => 'private', 'file_url' => $path]);
                }
                report($error);
                $this->error("Picture {$picture->getKey()}: {$error->getMessage()}");
                $failures++;
            }
        }

        return $failures ? self::FAILURE : self::SUCCESS;
    }
}
