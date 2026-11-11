<?php

namespace App\Http\Controllers\Api\Concerns;

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

trait ApiResponds
{
    protected function perPage(Request $request): int
    {
        $request->validate(['per_page' => ['sometimes', 'integer', 'min:1', 'max:100']]);

        return (int) $request->input('per_page', 15);
    }

    protected function ok(mixed $data, string $message = 'OK', int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => $message,
        ], $status);
    }

    /**
     * @param callable|null $mapItem function(mixed $item): array
     */
    protected function okPaginated(
        LengthAwarePaginator $paginator,
        ?callable $mapItem = null,
        string $message = 'OK',
        int $status = 200
    ): JsonResponse {
        $collection = $paginator->getCollection();
        if ($mapItem) {
            $collection = $collection->map($mapItem);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'items' => $collection->values()->all(),
                'meta' => [
                    'current_page' => $paginator->currentPage(),
                    'per_page' => $paginator->perPage(),
                    'total' => $paginator->total(),
                    'last_page' => $paginator->lastPage(),
                    'from' => $paginator->firstItem(),
                    'to' => $paginator->lastItem(),
                ],
                'links' => [
                    'first' => $paginator->url(1),
                    'last' => $paginator->url($paginator->lastPage()),
                    'prev' => $paginator->previousPageUrl(),
                    'next' => $paginator->nextPageUrl(),
                ],
            ],
            'message' => $message,
        ], $status);
    }
}
