<?php

namespace App\Support;

use DateTimeInterface;

final class DocumentActivityYear
{
    public static function fromDates(mixed $approved, mixed $created): ?int
    {
        // An invalid approval date needs correction, not an assumed upload year.
        $date = $approved ?: $created;
        if ($date instanceof DateTimeInterface) {
            $date = $date->format('Y-m-d');
        }

        if (! is_string($date) || ! preg_match('/^(\d{4})-(\d{2})-(\d{2})(?:[T ]|$)/', $date, $parts)) {
            return null;
        }

        $year = (int) $parts[1];
        return $year >= 1000 && checkdate((int) $parts[2], (int) $parts[3], $year) ? $year : null;
    }
}
