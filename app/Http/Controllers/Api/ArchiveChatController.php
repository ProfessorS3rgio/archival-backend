<?php

namespace App\Http\Controllers\Api;

use App\Services\ArchiveAssistant;
use App\Services\ArchiveChat;
use App\Services\ArchiveChatSource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use RuntimeException;

class ArchiveChatController
{
    public function assistant(Request $request, ArchiveAssistant $assistant): JsonResponse
    {
        $validator = Validator::make($request->json()->all(), [
            'intent' => 'required|string|in:recent_ordinances,recent_resolutions,ordinance_by_number,resolution_by_number,ordinance_search,resolution_search,records_by_year',
            'input' => 'nullable|array',
        ]);
        if ($validator->fails()) {
            return new JsonResponse(['message' => $validator->errors()->first()], 422);
        }

        try {
            $data = $validator->validated();

            return new JsonResponse($assistant->run($data['intent'], $data['input'] ?? []));
        } catch (RuntimeException $error) {
            return new JsonResponse(['message' => $error->getMessage()], 422);
        }
    }

    public function ask(Request $request, ArchiveChat $chat): JsonResponse
    {
        $validator = Validator::make($request->json()->all(), ['question' => 'required|string|max:500']);
        if ($validator->fails()) {
            return new JsonResponse(['message' => $validator->errors()->first()], 422);
        }

        try {
            return new JsonResponse($chat->answer(trim($validator->validated()['question'])));
        } catch (RuntimeException $error) {
            return new JsonResponse(['message' => $error->getMessage()], 503);
        }
    }

    public function pdf(string $type, int $id, int $file, ArchiveChatSource $source): Response
    {
        try {
            if (! is_file($source->indexPath()."/{$type}-{$id}.json")) {
                return new Response('Source not found.', 404);
            }

            $record = $source->record($type, $id);
            if (! $record || ! in_array($file, array_column($record['files'] ?? [], 'id'))) {
                return new Response('Source not found.', 404);
            }

            $pdf = $source->pdf($type, $id, $file);
        } catch (RuntimeException $error) {
            return new Response($error->getMessage(), 503);
        }

        return new Response($pdf, 200, ['Content-Type' => 'application/pdf', 'Content-Disposition' => 'inline']);
    }
}
