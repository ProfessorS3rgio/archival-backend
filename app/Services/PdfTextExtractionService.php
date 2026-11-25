<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Smalot\PdfParser\Parser;

class PdfTextExtractionService
{
    public function extract(string $pdfPath): string
    {
        $text = trim((new Parser())->parseFile($pdfPath)->getText());

        if ($text !== '') {
            return $text;
        }

        return $this->extractScannedPdf($pdfPath);
    }

    private function extractScannedPdf(string $pdfPath): string
    {
        $tesseract = $this->resolveCommand('tesseract');
        $pdftoppm = $this->resolveCommand('pdftoppm');

        if (!$tesseract || !$pdftoppm) {
            Log::warning('Scanned PDF has no text layer and OCR is unavailable', [
                'pdf' => basename($pdfPath),
                'required_commands' => ['tesseract', 'pdftoppm'],
            ]);

            return '';
        }

        $directory = dirname($pdfPath);
        $prefix = $directory . DIRECTORY_SEPARATOR . pathinfo($pdfPath, PATHINFO_FILENAME) . '_ocr_' . uniqid();
        $images = [];

        try {
            $render = Process::timeout(180)->run([
                $pdftoppm, '-png', '-r', '200', $pdfPath, $prefix,
            ]);

            if (!$render->successful()) {
                Log::warning('Could not render scanned PDF for OCR', [
                    'pdf' => basename($pdfPath),
                    'error' => $render->errorOutput(),
                ]);

                return '';
            }

            $images = glob($prefix . '-*.png') ?: [];
            sort($images, SORT_NATURAL);
            $pages = [];

            foreach ($images as $image) {
                $ocr = Process::timeout(180)->run([
                    $tesseract, $image, 'stdout', '-l', 'eng', '--psm', '6',
                ]);

                if ($ocr->successful() && trim($ocr->output()) !== '') {
                    $pages[] = trim($ocr->output());
                }
            }

            return trim(implode("\n\n", $pages));
        } catch (\Throwable $exception) {
            Log::warning('Scanned PDF OCR failed', [
                'pdf' => basename($pdfPath),
                'error' => $exception->getMessage(),
            ]);

            return '';
        } finally {
            foreach ($images as $image) {
                if (is_file($image)) {
                    @unlink($image);
                }
            }
        }
    }

    private function resolveCommand(string $command): ?string
    {
        $configuredPath = getenv($command === 'tesseract' ? 'PDF_TESSERACT_PATH' : 'PDF_PDFTOPPM_PATH');
        if ($configuredPath && is_file($configuredPath)) {
            return $configuredPath;
        }

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $windowsPaths = [
                'tesseract' => ['C:\\Program Files\\Tesseract-OCR\\tesseract.exe', 'C:\\Program Files (x86)\\Tesseract-OCR\\tesseract.exe'],
                'pdftoppm' => ['C:\\Program Files\\poppler\\Library\\bin\\pdftoppm.exe', 'C:\\Program Files\\poppler\\bin\\pdftoppm.exe'],
            ];

            if ($command === 'pdftoppm' && getenv('LOCALAPPDATA')) {
                $windowsPaths['pdftoppm'] = array_merge(
                    $windowsPaths['pdftoppm'],
                    glob(getenv('LOCALAPPDATA') . '\\Microsoft\\WinGet\\Packages\\*Poppler*\\poppler-*\\Library\\bin\\pdftoppm.exe') ?: [],
                );
            }

            foreach ($windowsPaths[$command] ?? [] as $path) {
                if (is_file($path)) {
                    return $path;
                }
            }

            $probe = ['where.exe', $command];
        } else {
            $linuxPaths = [
                'tesseract' => ['/usr/bin/tesseract', '/usr/local/bin/tesseract'],
                'pdftoppm' => ['/usr/bin/pdftoppm', '/usr/local/bin/pdftoppm'],
            ];

            foreach ($linuxPaths[$command] ?? [] as $path) {
                if (is_file($path) && is_executable($path)) {
                    return $path;
                }
            }

            $probe = ['which', $command];
        }

        try {
            $result = Process::timeout(10)->run($probe);

            return $result->successful() ? trim(strtok($result->output(), PHP_EOL)) : null;
        } catch (\Throwable) {
            return null;
        }
    }
}
