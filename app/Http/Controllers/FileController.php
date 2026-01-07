<?php

namespace App\Http\Controllers;

use App\Services\LoggingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser;

class FileController extends Controller
{
    public function convertFile(Request $request)
    {
        \Log::info('Convert file request received', [
            'has_file' => $request->hasFile('file'),
            'file_info' => $request->file('file') ? [
                'name' => $request->file('file')->getClientOriginalName(),
                'size' => $request->file('file')->getSize(),
                'mime' => $request->file('file') ? $request->file('file')->getMimeType() : 'N/A',
            ] : null,
            'php_upload_limits' => [
                'upload_max_filesize' => ini_get('upload_max_filesize'),
                'post_max_size' => ini_get('post_max_size'),
                'max_file_uploads' => ini_get('max_file_uploads'),
            ],
        ]);

        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx|max:10240', // 10MB - PDF, DOC, and DOCX supported
        ]);

        $file = $request->file('file');

        // Debug file upload
        if (!$file) {
            \Log::error('File upload failed', [
                'has_file' => $request->hasFile('file'),
                'file_key_exists' => $request->has('file'),
                'all_files' => $request->allFiles(),
                'upload_errors' => $_FILES ?? 'No $_FILES',
            ]);
            throw new \Exception('File upload failed. Please check file size limits and permissions.');
        }

        if (!$file->isValid()) {
            \Log::error('Invalid file upload', [
                'error' => $file->getError(),
                'error_message' => $file->getErrorMessage(),
            ]);
            throw new \Exception('Invalid file upload: ' . $file->getErrorMessage());
        }

        $originalName = $file->getClientOriginalName();
        $extension = strtolower($file->getClientOriginalExtension());

        // Create temp directory
        $tempDir = storage_path('app/temp');
        if (!is_dir($tempDir)) {
            if (!mkdir($tempDir, 0755, true)) {
                \Log::error('Failed to create temp directory', ['temp_dir' => $tempDir]);
                throw new \Exception('Failed to create temporary directory. Please check permissions.');
            }
        }

        // Check if temp directory is writable
        if (!is_writable($tempDir)) {
            \Log::error('Temp directory not writable', ['temp_dir' => $tempDir]);
            throw new \Exception('Temporary directory is not writable. Please check permissions.');
        }

        $inputPath = $tempDir . '/' . uniqid() . '_' . $originalName;

        // Move uploaded file to temp directory
        try {
            $file->move($tempDir, basename($inputPath));
        } catch (\Exception $e) {
            \Log::error('Failed to move uploaded file', [
                'error' => $e->getMessage(),
                'temp_dir' => $tempDir,
                'input_path' => $inputPath,
            ]);
            throw new \Exception('Failed to save uploaded file. Please check permissions.');
        }

        // Verify file was moved successfully
        if (!file_exists($inputPath)) {
            \Log::error('File not found after move', ['input_path' => $inputPath]);
            throw new \Exception('File upload failed. Please try again.');
        }

        try {
            if ($extension === 'pdf') {
                // Extract text directly from PDF
                $parser = new Parser();
                $pdf = $parser->parseFile($inputPath);
                $text = $pdf->getText();
                $pdfContent = file_get_contents($inputPath);
            } else if (in_array($extension, ['doc', 'docx'])) {
                // Convert DOC/DOCX to PDF using Python script with comtypes (Windows) or LibreOffice (Linux)
                $outputDir = $tempDir;
                $pdfPath = $tempDir . '/' . pathinfo($inputPath, PATHINFO_FILENAME) . '.pdf';

                \Log::info('Converting DOC/DOCX to PDF using Python with comtypes/LibreOffice', [
                    'input' => $inputPath,
                    'output' => $pdfPath,
                ]);

                $conversionSuccessful = false;

                // Use Python script for conversion
                try {
                    $pythonScript = base_path('scripts/docx_to_pdf.py');

                    // Check if Python script exists
                    if (!file_exists($pythonScript)) {
                        \Log::error('Python script not found', ['script_path' => $pythonScript]);
                        throw new \Exception('Python conversion script not found.');
                    }

                    $pythonCommand = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? 'python' : 'python3';

                    \Log::info('Python execution details', [
                        'script' => $pythonScript,
                        'command' => $pythonCommand,
                        'input' => $inputPath,
                        'output' => $pdfPath,
                        'php_os' => PHP_OS,
                    ]);

                    // Ensure PATH environment variable is available for Python subprocess
                    $env = $_SERVER;
                    if (!isset($env['PATH']) && isset($_SERVER['PATH'])) {
                        $env['PATH'] = $_SERVER['PATH'];
                    }
                    // On Windows, also ensure other required environment variables
                    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                        $env = array_merge($env, [
                            'PATH' => getenv('PATH'),
                            'SYSTEMROOT' => getenv('SYSTEMROOT') ?: 'C:\Windows',
                            'WINDIR' => getenv('WINDIR') ?: 'C:\Windows',
                            'TEMP' => getenv('TEMP'),
                            'TMP' => getenv('TMP'),
                            'LOCALAPPDATA' => getenv('LOCALAPPDATA'),
                            'APPDATA' => getenv('APPDATA'),
                        ]);
                    }

                    $process = Process::timeout(120)->env($env)->run([
                        $pythonCommand,
                        $pythonScript,
                        $inputPath,
                        $pdfPath
                    ]);

                    if ($process->successful() && file_exists($pdfPath) && filesize($pdfPath) > 0) {
                        $conversionSuccessful = true;
                        \Log::info('Python conversion successful');
                    } else {
                        \Log::warning('Python conversion failed', [
                            'output' => $process->output(),
                            'error' => $process->errorOutput()
                        ]);
                    }
                } catch (\Exception $e) {
                    \Log::warning('Python conversion exception', ['error' => $e->getMessage()]);
                }

                if (!$conversionSuccessful) {
                    throw new \Exception('DOC/DOCX conversion failed. Please ensure Python and comtypes library are installed, and that Microsoft Word (Windows) or LibreOffice (Linux/macOS) is available.');
                }

                // Extract text from PDF using Smalot PDF Parser
                $parser = new Parser();
                $pdf = $parser->parseFile($pdfPath);
                $text = $pdf->getText();
                $pdfContent = file_get_contents($pdfPath);

                // Clean up PDF
                unlink($pdfPath);
            } else {
                throw new \Exception('Unsupported file type. Only PDF, DOC, and DOCX files are supported.');
            }

            // Clean up input file
            unlink($inputPath);

            // Log successful file conversion
            LoggingService::logCustom(
                'file_convert',
                'files',
                null,
                null,
                null,
                "Successfully converted file: {$originalName} ({$extension} to PDF)"
            );

            return response()->json(['text' => $text, 'pdf' => base64_encode($pdfContent)]);

        } catch (\Exception $e) {
            // Clean up files
            if (file_exists($inputPath)) unlink($inputPath);
            $pdfPath = $tempDir . '/' . pathinfo($inputPath, PATHINFO_FILENAME) . '.pdf';
            if (file_exists($pdfPath)) unlink($pdfPath);

            // Log failed file conversion
            LoggingService::logCustom(
                'file_convert_failed',
                'files',
                null,
                null,
                null,
                "Failed to convert file: {$originalName} - Error: {$e->getMessage()}"
            );

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
