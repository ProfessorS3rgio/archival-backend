<?php

namespace App\Services;

use Illuminate\Support\Collection;

class DocumentFilterService
{
    /**
     * Get unique authors from a collection of documents
     *
     * @param Collection $documents
     * @param string $authorField
     * @return array
     */
    public static function getAvailableAuthors(Collection $documents, string $authorField = 'author'): array
    {
        return $documents->pluck($authorField)
            ->filter()
            ->flatMap(function ($author) {
                return array_map('trim', explode(';', $author));
            })
            ->unique()
            ->sort()
            ->values()
            ->toArray();
    }

    /**
     * Get unique subjects from a collection of documents
     *
     * @param Collection $documents
     * @param string $subjectField
     * @return array
     */
    public static function getAvailableSubjects(Collection $documents, string $subjectField = 'subject'): array
    {
        return $documents->pluck($subjectField)
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->toArray();
    }

    /**
     * Get unique years from a collection of documents
     *
     * @param Collection $documents
     * @param string $dateField
     * @return array
     */
    public static function getAvailableYears(Collection $documents, string $dateField = 'date_approved'): array
    {
        return $documents->pluck($dateField)
            ->filter()
            ->map(function ($date) {
                return \Carbon\Carbon::parse($date)->year;
            })
            ->unique()
            ->sortDesc()
            ->values()
            ->toArray();
    }

    /**
     * Get all filter options for a collection of documents
     *
     * @param Collection $documents
     * @param array $config Configuration for field names
     * @return array
     */
    public static function getAllFilters(Collection $documents, array $config = []): array
    {
        $defaults = [
            'author_field' => 'author',
            'subject_field' => 'subject',
            'date_field' => 'date_approved',
        ];

        $config = array_merge($defaults, $config);

        $filters = [
            'authors' => self::getAvailableAuthors($documents, $config['author_field']),
            'years' => self::getAvailableYears($documents, $config['date_field']),
        ];

        // Only include subjects if subject_field is not null
        if ($config['subject_field'] !== null) {
            $filters['subjects'] = self::getAvailableSubjects($documents, $config['subject_field']);
        }

        return $filters;
    }

    /**
     * Get filter options for multiple document types
     *
     * @param array $documentCollections Array of ['type' => Collection, 'config' => [...]]
     * @return array
     */
    public static function getCombinedFilters(array $documentCollections): array
    {
        $allAuthors = collect();
        $allSubjects = collect();
        $allYears = collect();

        foreach ($documentCollections as $collectionData) {
            $documents = $collectionData['documents'];
            $config = $collectionData['config'] ?? [];

            $filters = self::getAllFilters($documents, $config);

            $allAuthors = $allAuthors->merge($filters['authors']);
            $allSubjects = $allSubjects->merge($filters['subjects']);
            $allYears = $allYears->merge($filters['years']);
        }

        return [
            'authors' => $allAuthors->unique()->sort()->values()->toArray(),
            'subjects' => $allSubjects->unique()->sort()->values()->toArray(),
            'years' => $allYears->unique()->sortDesc()->values()->toArray(),
        ];
    }
}