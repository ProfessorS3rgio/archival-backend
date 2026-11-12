<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Process;
use Illuminate\Validation\ValidationException;

class PdfMerger
{
    /**
     * Merge uploaded PDFs in the supplied order and return a local temp path.
     * The caller owns cleanup of the returned file.
     *
     * @param  list<UploadedFile>  $files
     */
    public function merge(array $files): string
    {
        $script = base_path('scripts/merge_pdfs.py');
        if (! is_file($script)) {
            throw ValidationException::withMessages(['files' => 'PDF merging is not available on this server.']);
        }

        $directory = storage_path('app/private/temp-merges');
        if (! is_dir($directory) && ! mkdir($directory, 0750, true) && ! is_dir($directory)) {
            throw ValidationException::withMessages(['files' => 'The temporary merge directory could not be created.']);
        }

        $output = tempnam($directory, 'merged-');
        if (! $output) {
            throw ValidationException::withMessages(['files' => 'A temporary merge file could not be created.']);
        }
        $output .= '.pdf';

        $python = $this->pythonInterpreter();
        $paths = array_map(static fn (UploadedFile $file): string => $file->getRealPath(), $files);
        $result = Process::timeout(120)->run(array_merge([$python, $script, $output], $paths));

        if (! $result->successful() || ! is_file($output) || filesize($output) < 1) {
            @unlink($output);
            $details = trim($result->errorOutput() ?: $result->output());
            report(new \RuntimeException('PDF merge failed using '.$python.': '.$details));
            throw ValidationException::withMessages(['files' => 'The PDFs could not be merged. Ensure the server Python environment has pypdf installed, and check that each file is a valid, unlocked PDF.']);
        }

        return $output;
    }

    private function pythonInterpreter(): string
    {
        $configured = trim((string) env('PDF_MERGE_PYTHON', ''));
        if ($configured !== '') {
            return $configured;
        }

        $candidates = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN'
            ? [base_path('scripts/.venv/Scripts/python.exe'), 'python']
            : [base_path('scripts/.venv/bin/python'), 'python3'];

        foreach ($candidates as $candidate) {
            if (! str_contains($candidate, DIRECTORY_SEPARATOR) || is_file($candidate)) {
                return $candidate;
            }
        }

        return $candidates[array_key_last($candidates)];
    }
}
